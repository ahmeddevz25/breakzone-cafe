<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Food;
use App\Models\FoodDetail;
use App\Models\Ingredient;
use App\Models\Item;
use App\Models\Store;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class FoodController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:food management')->only('index');
        $this->middleware('permission:food add')->only('store');
        $this->middleware('permission:food edit')->only(['update', 'getFoodDetails']);
        $this->middleware('permission:food delete')->only('destroy');
    }

    public function index()
    {
        try {
            $foods = Food::with(['store', 'category', 'foodDetails.ingredient.usageUnit', 'foodDetails.ingredient.buyingUnit'])
                ->orderBy('position', 'asc')
                ->orderBy('id', 'desc')
                ->get();

            $stores = Store::where('status', 'A')->orderBy('position', 'asc')->get();
            $categories = Category::where('status', 'A')->orderBy('position', 'asc')->get();
            $ingredients = Ingredient::with(['buyingUnit', 'usageUnit'])
                ->where('status', 'A')
                ->orderBy('name', 'asc')
                ->get();

            return view('admin.foods.index', compact('foods', 'stores', 'categories', 'ingredients'));
        } catch (\Exception $e) {
            Log::error('Food Index Error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Something went wrong while fetching foods.');
        }
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:250',
            'store_id' => 'required|exists:cafe_stores,id',
            'category_id' => 'required|exists:cafe_categories,id',
            'code' => 'required|string|max:50|unique:cafe_foods,code',
            'price' => 'required|numeric|min:0',
            'picture' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp,svg|max:2048',
            'status' => 'required',
            'ingredients' => 'nullable|array',
            'ingredients.*.ingredient_id' => 'nullable|exists:cafe_ingredients,id',
            'ingredients.*.quantity' => 'nullable|numeric|min:0',
            'ingredients.*.unit_price' => 'nullable|numeric|min:0',
        ]);

        try {
            DB::beginTransaction();

            $status = strtoupper(trim($request->status ?? 'A'));
            $statusChar = in_array($status, ['A', 'ACTIVE', '1']) ? 'A' : 'I';

            $picturePath = null;
            if ($request->hasFile('picture')) {
                $picturePath = $request->file('picture')->store('foods', 'public');
            }

            // Calculate total ingredient cost
            $totalCostPrice = 0;
            $ingredientRows = [];

            if ($request->has('ingredients') && is_array($request->ingredients)) {
                foreach ($request->ingredients as $row) {
                    $ingredientId = $row['ingredient_id'] ?? null;
                    $qty = isset($row['quantity']) ? (float)$row['quantity'] : 0;
                    $unitPrice = isset($row['unit_price']) ? (float)$row['unit_price'] : 0;

                    if ($ingredientId && $qty > 0) {
                        $lineTotal = round($qty * $unitPrice, 2);
                        $totalCostPrice += $lineTotal;

                        $ing = Ingredient::find($ingredientId);
                        $usageUnitId = $ing ? $ing->usage_unit_id : null;

                        $ingredientRows[] = [
                            'ingredient_id' => $ingredientId,
                            'usage_unit_id' => $usageUnitId,
                            'quantity' => $qty,
                            'unit_price' => $unitPrice,
                            'total_price' => $lineTotal,
                        ];
                    }
                }
            }

            $food = Food::create([
                'store_id' => $request->store_id,
                'category_id' => $request->category_id,
                'name' => $request->name,
                'code' => $request->code,
                'picture' => $picturePath,
                'price' => (float)$request->price,
                'cost_price' => $totalCostPrice,
                'status' => $statusChar,
                'position' => (int)($request->position ?? 0),
                'created_by' => auth()->id() ?? 1,
                'modified_by' => auth()->id() ?? 1,
                'modified_at' => now(),
            ]);

            foreach ($ingredientRows as $detail) {
                $food->foodDetails()->create($detail);
            }

            DB::commit();

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['status' => true, 'message' => 'Food item and recipe created successfully.']);
            }
            return redirect()->route('foods.index')->with('success', 'Food item and recipe created successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Food Create Error: ' . $e->getMessage());
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['status' => false, 'message' => 'Failed to save food: ' . $e->getMessage()], 500);
            }
            return redirect()->back()->withInput()->with('error', 'Failed to save food: ' . $e->getMessage());
        }
    }

    public function update(Request $request, $id)
    {
        $food = Food::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:250',
            'store_id' => 'required|exists:cafe_stores,id',
            'category_id' => 'required|exists:cafe_categories,id',
            'code' => 'required|string|max:50|unique:cafe_foods,code,' . $id,
            'price' => 'required|numeric|min:0',
            'picture' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp,svg|max:2048',
            'status' => 'required',
            'ingredients' => 'nullable|array',
            'ingredients.*.ingredient_id' => 'nullable|exists:cafe_ingredients,id',
            'ingredients.*.quantity' => 'nullable|numeric|min:0',
            'ingredients.*.unit_price' => 'nullable|numeric|min:0',
        ]);

        try {
            DB::beginTransaction();

            $status = strtoupper(trim($request->status ?? 'A'));
            $statusChar = in_array($status, ['A', 'ACTIVE', '1']) ? 'A' : 'I';

            $picturePath = $food->picture;
            if ($request->hasFile('picture')) {
                if (!empty($food->picture) && Storage::disk('public')->exists($food->picture)) {
                    Storage::disk('public')->delete($food->picture);
                }
                $picturePath = $request->file('picture')->store('foods', 'public');
            }

            // Calculate total ingredient cost
            $totalCostPrice = 0;
            $ingredientRows = [];

            if ($request->has('ingredients') && is_array($request->ingredients)) {
                foreach ($request->ingredients as $row) {
                    $ingredientId = $row['ingredient_id'] ?? null;
                    $qty = isset($row['quantity']) ? (float)$row['quantity'] : 0;
                    $unitPrice = isset($row['unit_price']) ? (float)$row['unit_price'] : 0;

                    if ($ingredientId && $qty > 0) {
                        $lineTotal = round($qty * $unitPrice, 2);
                        $totalCostPrice += $lineTotal;

                        $ing = Ingredient::find($ingredientId);
                        $usageUnitId = $ing ? $ing->usage_unit_id : null;

                        $ingredientRows[] = [
                            'ingredient_id' => $ingredientId,
                            'usage_unit_id' => $usageUnitId,
                            'quantity' => $qty,
                            'unit_price' => $unitPrice,
                            'total_price' => $lineTotal,
                        ];
                    }
                }
            }

            $food->update([
                'store_id' => $request->store_id,
                'category_id' => $request->category_id,
                'name' => $request->name,
                'code' => $request->code,
                'picture' => $picturePath,
                'price' => (float)$request->price,
                'cost_price' => $totalCostPrice,
                'status' => $statusChar,
                'position' => (int)($request->position ?? $food->position),
                'modified_by' => auth()->id() ?? 1,
                'modified_at' => now(),
            ]);

            // Re-create food details
            $food->foodDetails()->delete();
            foreach ($ingredientRows as $detail) {
                $food->foodDetails()->create($detail);
            }

            DB::commit();

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['status' => true, 'message' => 'Food item and recipe updated successfully.']);
            }
            return redirect()->route('foods.index')->with('success', 'Food item and recipe updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Food Update Error: ' . $e->getMessage());
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['status' => false, 'message' => 'Failed to update food: ' . $e->getMessage()], 500);
            }
            return redirect()->back()->withInput()->with('error', 'Failed to update food: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
            $food = Food::findOrFail($id);

            if (!empty($food->picture) && Storage::disk('public')->exists($food->picture)) {
                Storage::disk('public')->delete($food->picture);
            }

            $food->delete();

            return redirect()->route('foods.index')->with('success', 'Food item deleted successfully.');
        } catch (\Exception $e) {
            Log::error('Food Delete Error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to delete food: ' . $e->getMessage());
        }
    }

    /**
     * AJAX endpoint to generate next unique code for a category
     */
    public function generateCode(Request $request)
    {
        $categoryId = $request->query('category_id');
        if (!$categoryId) {
            return response()->json(['status' => false, 'message' => 'Category ID required.'], 400);
        }

        $category = Category::find($categoryId);
        if (!$category) {
            return response()->json(['status' => false, 'message' => 'Category not found.'], 404);
        }

        // Build hierarchical prefix:
        // Level 1: top parent ID (2 digits), Level 2: child ID (2 digits)
        $parentId = $category->parent_id ?: $category->id;
        $childId = $category->id;
        $prefix = str_pad($parentId, 2, '0', STR_PAD_LEFT) . str_pad($childId, 2, '0', STR_PAD_LEFT);

        // Find highest existing code starting with prefix in cafe_foods or cafe_items
        $lastFood = Food::where('code', 'LIKE', "{$prefix}%")
            ->orderBy('code', 'desc')
            ->first();

        $lastItem = Item::where('code', 'LIKE', "{$prefix}%")
            ->orderBy('code', 'desc')
            ->first();

        $lastCode = null;
        if ($lastFood && $lastItem) {
            $lastCode = max($lastFood->code, $lastItem->code);
        } elseif ($lastFood) {
            $lastCode = $lastFood->code;
        } elseif ($lastItem) {
            $lastCode = $lastItem->code;
        }

        if ($lastCode && is_numeric($lastCode)) {
            $nextNumber = (int)$lastCode + 1;
            $code = str_pad((string)$nextNumber, strlen($lastCode), '0', STR_PAD_LEFT);
        } else {
            // Default 8 digit format e.g. 02050101
            $code = $prefix . '0101';
        }

        // Ensure uniqueness
        while (Food::where('code', $code)->exists() || Item::where('code', $code)->exists()) {
            if (is_numeric($code)) {
                $code = str_pad((string)((int)$code + 1), strlen($code), '0', STR_PAD_LEFT);
            } else {
                $code = $code . '_1';
            }
        }

        return response()->json([
            'status' => true,
            'code' => $code,
        ]);
    }

    /**
     * AJAX endpoint to fetch food data and ingredients for editing
     */
    public function getFoodDetails($id)
    {
        $food = Food::with(['foodDetails.ingredient.usageUnit', 'foodDetails.ingredient.buyingUnit'])->find($id);

        if (!$food) {
            return response()->json(['status' => false, 'message' => 'Food not found.'], 404);
        }

        $details = $food->foodDetails->map(function ($detail) {
            $ing = $detail->ingredient;
            $usageUnitName = $ing && $ing->usageUnit ? ($ing->usageUnit->unit ?? $ing->usageUnit->name) : '-';

            // Calculate unit price if not set
            $unitPrice = $detail->unit_price;
            if ($unitPrice <= 0 && $ing) {
                $conv = $ing->conversion_value > 0 ? $ing->conversion_value : 1;
                $unitPrice = $ing->purchase_price / $conv;
            }

            return [
                'ingredient_id' => $detail->ingredient_id,
                'ingredient_name' => $ing ? $ing->name : '-',
                'usage_unit' => $usageUnitName,
                'quantity' => (float)$detail->quantity,
                'unit_price' => (float)$unitPrice,
                'total_price' => (float)$detail->total_price,
            ];
        });

        return response()->json([
            'status' => true,
            'food' => [
                'id' => $food->id,
                'store_id' => $food->store_id,
                'category_id' => $food->category_id,
                'name' => $food->name,
                'code' => $food->code,
                'price' => (float)$food->price,
                'cost_price' => (float)$food->cost_price,
                'stock' => (float)($food->stock ?? 0),
                'status' => $food->status,
                'picture_url' => $food->picture_url,
                'ingredients' => $details,
            ],
        ]);
    }
}
