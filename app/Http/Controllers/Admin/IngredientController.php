<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Ingredient;
use App\Models\Store;
use App\Models\Unit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class IngredientController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:ingredient management')->only('index');
        $this->middleware('permission:ingredient add')->only('store');
        $this->middleware('permission:ingredient edit')->only('update');
        $this->middleware('permission:ingredient delete')->only('destroy');
    }

    public function index()
    {
        try {
            $ingredients = Ingredient::with(['store', 'buyingUnit', 'usageUnit'])
                ->orderBy('id', 'desc')
                ->get();

            $stores = Store::where('status', 'A')->orderBy('position', 'asc')->orderBy('id', 'asc')->get();
            // If active stores empty, fetch all
            if ($stores->isEmpty()) {
                $stores = Store::orderBy('position', 'asc')->orderBy('id', 'asc')->get();
            }

            $units = Unit::where('status', 'A')->orderBy('position', 'asc')->orderBy('id', 'asc')->get();
            if ($units->isEmpty()) {
                $units = Unit::orderBy('position', 'asc')->orderBy('id', 'asc')->get();
            }

            return view('admin.ingredients.index', compact('ingredients', 'stores', 'units'));
        } catch (\Exception $e) {
            Log::error('Ingredient Index Error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Something went wrong while fetching ingredients.');
        }
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:150',
            'store_id' => 'required|exists:cafe_stores,id',
            'buying_unit_id' => 'required|exists:cafe_units,id',
            'usage_unit_id' => 'required|exists:cafe_units,id',
            'conversion_value' => 'nullable|numeric|min:0',
            'purchase_price' => 'nullable|numeric|min:0',
            'details' => 'nullable|string',
            'picture' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp,svg|max:2048',
            'status' => 'required',
        ]);

        try {
            $data = $request->except(['picture', 'stock']);
            $data['conversion_value'] = $request->input('conversion_value') ?? 0;
            $data['purchase_price'] = $request->input('purchase_price') ?? 0;
            $data['stock'] = 0;
            $data['details'] = $request->input('details') ?? '';

            $status = strtoupper(trim($request->status ?? 'A'));
            $data['status'] = in_array($status, ['A', 'ACTIVE', '1']) ? 'A' : 'I';

            if ($request->hasFile('picture')) {
                $data['picture'] = $request->file('picture')->store('ingredients', 'public');
            } else {
                $data['picture'] = null;
            }

            $data['created_by'] = auth()->id() ?? 1;
            $data['modified_by'] = auth()->id() ?? 1;
            $data['modified_at'] = now();

            Ingredient::create($data);

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['status' => true, 'message' => 'Ingredient created successfully.']);
            }
            return redirect()->route('ingredients.index')->with('success', 'Ingredient created successfully.');
        } catch (\Exception $e) {
            Log::error('Ingredient Create Error: ' . $e->getMessage());
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['status' => false, 'message' => 'Failed to create ingredient. ' . $e->getMessage()], 500);
            }
            return redirect()->back()->withInput()->with('error', 'Failed to create ingredient. ' . $e->getMessage());
        }
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:150',
            'store_id' => 'required|exists:cafe_stores,id',
            'buying_unit_id' => 'required|exists:cafe_units,id',
            'usage_unit_id' => 'required|exists:cafe_units,id',
            'conversion_value' => 'nullable|numeric|min:0',
            'purchase_price' => 'nullable|numeric|min:0',
            'details' => 'nullable|string',
            'picture' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp,svg|max:2048',
            'status' => 'required',
        ]);

        try {
            $ingredient = Ingredient::findOrFail($id);
            $data = $request->except(['picture', 'stock']);
            $data['conversion_value'] = $request->input('conversion_value') ?? 0;
            $data['purchase_price'] = $request->input('purchase_price') ?? 0;
            $data['details'] = $request->input('details') ?? '';

            $status = strtoupper(trim($request->status ?? 'A'));
            $data['status'] = in_array($status, ['A', 'ACTIVE', '1']) ? 'A' : 'I';

            if ($request->hasFile('picture')) {
                if ($ingredient->picture && Storage::disk('public')->exists($ingredient->picture)) {
                    Storage::disk('public')->delete($ingredient->picture);
                }
                $data['picture'] = $request->file('picture')->store('ingredients', 'public');
            }

            $data['modified_by'] = auth()->id() ?? 1;
            $data['modified_at'] = now();

            $ingredient->update($data);

            // Automatically recalculate recipe costs for all foods using this ingredient
            Ingredient::updateFoodCostPrices($ingredient->id, $data['purchase_price']);

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['status' => true, 'message' => 'Ingredient updated successfully.']);
            }
            return redirect()->route('ingredients.index')->with('success', 'Ingredient updated successfully.');
        } catch (\Exception $e) {
            Log::error('Ingredient Update Error: ' . $e->getMessage());
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['status' => false, 'message' => 'Failed to update ingredient. ' . $e->getMessage()], 500);
            }
            return redirect()->back()->withInput()->with('error', 'Failed to update ingredient. ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
            $ingredient = Ingredient::findOrFail($id);

            if ($ingredient->picture && Storage::disk('public')->exists($ingredient->picture)) {
                Storage::disk('public')->delete($ingredient->picture);
            }

            $ingredient->delete();

            return redirect()->route('ingredients.index')->with('success', 'Ingredient deleted successfully.');
        } catch (\Exception $e) {
            Log::error('Ingredient Delete Error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to delete ingredient.');
        }
    }
}
