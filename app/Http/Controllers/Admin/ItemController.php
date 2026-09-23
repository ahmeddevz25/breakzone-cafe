<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Item;
use App\Models\Store;
use App\Models\Unit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ItemController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:item management')->only('index');
        $this->middleware('permission:item add')->only('store');
        $this->middleware('permission:item edit')->only('update');
        $this->middleware('permission:item delete')->only('destroy');
    }

    public function index()
    {
        try {
            $items = Item::with(['store', 'category', 'unit', 'brand'])
                ->orderBy('position', 'asc')
                ->orderBy('id', 'desc')
                ->get();

            $stores = Store::orderBy('position', 'asc')->orderBy('id', 'asc')->get();
            $categories = Category::orderBy('position', 'asc')->orderBy('id', 'asc')->get();
            $units = Unit::orderBy('position', 'asc')->orderBy('id', 'asc')->get();
            $brands = Brand::orderBy('position', 'asc')->orderBy('id', 'asc')->get();

            return view('admin.items.index', compact('items', 'stores', 'categories', 'units', 'brands'));
        } catch (\Exception $e) {
            Log::error('Item Index Error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Something went wrong while fetching items.');
        }
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'store_id' => 'required|exists:cafe_stores,id',
            'category_id' => 'required|exists:cafe_categories,id',
            'code' => 'required|string|max:20|unique:cafe_items,code',
            'unit_id' => 'required|exists:cafe_units,id',
            'brand_id' => 'nullable|exists:cafe_brands,id',
            'price' => 'nullable|numeric|min:0',
            'purchase_price' => 'nullable|numeric|min:0',
            'sale_price' => 'required|numeric|min:0',
            'details' => 'nullable|string',
            'picture' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp,svg|max:2048',
            'status' => 'required',
            'position' => 'nullable|integer',
        ]);

        try {
            $data = $request->except(['picture', 'stock']);
            $data['price'] = $request->input('price') ?? $request->input('purchase_price') ?? 0;
            $data['sale_price'] = $request->input('sale_price') ?? 0;
            $data['stock'] = 0;
            $data['position'] = $request->input('position') ?? 0;
            $data['details'] = $request->input('details') ?? '';

            $status = strtoupper(trim($request->status ?? 'A'));
            $data['status'] = in_array($status, ['A', 'ACTIVE', '1']) ? 'A' : 'I';

            if ($request->hasFile('picture')) {
                $data['picture'] = $request->file('picture')->store('items', 'public');
            } else {
                $data['picture'] = '';
            }

            $data['created_by'] = auth()->id() ?? 1;
            $data['modified_by'] = auth()->id() ?? 1;
            $data['modified_at'] = now();

            Item::create($data);

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['status' => true, 'message' => 'Item created successfully.']);
            }
            return redirect()->route('items.index')->with('success', 'Item created successfully.');
        } catch (\Exception $e) {
            Log::error('Item Create Error: ' . $e->getMessage());
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['status' => false, 'message' => 'Failed to create item. ' . $e->getMessage()], 500);
            }
            return redirect()->back()->withInput()->with('error', 'Failed to create item. ' . $e->getMessage());
        }
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'store_id' => 'required|exists:cafe_stores,id',
            'category_id' => 'required|exists:cafe_categories,id',
            'code' => 'required|string|max:20|unique:cafe_items,code,' . $id,
            'unit_id' => 'required|exists:cafe_units,id',
            'brand_id' => 'nullable|exists:cafe_brands,id',
            'price' => 'nullable|numeric|min:0',
            'purchase_price' => 'nullable|numeric|min:0',
            'sale_price' => 'required|numeric|min:0',
            'details' => 'nullable|string',
            'picture' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp,svg|max:2048',
            'status' => 'required',
            'position' => 'nullable|integer',
        ]);

        try {
            $item = Item::findOrFail($id);
            $data = $request->except(['picture', 'stock']);

            $data['price'] = $request->input('price') ?? $request->input('purchase_price') ?? 0;
            $data['sale_price'] = $request->input('sale_price') ?? 0;
            $data['position'] = $request->input('position') ?? 0;
            $data['details'] = $request->input('details') ?? '';

            $status = strtoupper(trim($request->status ?? 'A'));
            $data['status'] = in_array($status, ['A', 'ACTIVE', '1']) ? 'A' : 'I';

            if ($request->hasFile('picture')) {
                // Delete old picture if exists
                if (!empty($item->picture) && Storage::disk('public')->exists($item->picture)) {
                    Storage::disk('public')->delete($item->picture);
                }
                $data['picture'] = $request->file('picture')->store('items', 'public');
            }

            $data['modified_by'] = auth()->id() ?? 1;
            $data['modified_at'] = now();

            $item->update($data);

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['status' => true, 'message' => 'Item updated successfully.']);
            }
            return redirect()->route('items.index')->with('success', 'Item updated successfully.');
        } catch (\Exception $e) {
            Log::error('Item Update Error: ' . $e->getMessage());
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['status' => false, 'message' => 'Failed to update item. ' . $e->getMessage()], 500);
            }
            return redirect()->back()->withInput()->with('error', 'Failed to update item. ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
            $item = Item::findOrFail($id);

            // Delete associated picture
            if (!empty($item->picture) && Storage::disk('public')->exists($item->picture)) {
                Storage::disk('public')->delete($item->picture);
            }

            $item->delete();

            return redirect()->route('items.index')->with('success', 'Item deleted successfully.');
        } catch (\Exception $e) {
            Log::error('Item Delete Error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to delete item. Please try again.');
        }
    }
}
