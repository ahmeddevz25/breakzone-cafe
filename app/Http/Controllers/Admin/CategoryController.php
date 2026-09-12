<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;
use Illuminate\Support\Facades\Log;

class CategoryController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:category management')->only('index');
        $this->middleware('permission:category add')->only('store');
        $this->middleware('permission:category edit')->only('update');
        $this->middleware('permission:category delete')->only('destroy');
    }

    public function index()
    {
        try {
            $categories = Category::with('parent')->orderBy('id', 'asc')->get();
            return view('admin.categories.index', compact('categories'));
        } catch (\Exception $e) {
            Log::error('Category Index Error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Something went wrong while fetching categories.');
        }
    }

    public function store(Request $request)
    {
        $request->validate([
            'category' => 'nullable|string|max:100',
            'name' => 'nullable|string|max:100',
            'parent_id' => 'nullable|exists:categories,id',
            'position' => 'nullable|integer',
            'status' => 'required',
        ]);

        try {
            $data = $request->all();
            $data['category'] = $data['category'] ?? $data['name'] ?? '';
            $data['position'] = $data['position'] ?? 0;
            $status = strtoupper(trim($request->status ?? 'A'));
            $data['status'] = ($status === 'ACTIVE' || $status === '1' || $status === 'A') ? 'A' : 'I';

            Category::create($data);
            return redirect()->route('categories.index')->with('success', 'Category created successfully.');
        } catch (\Exception $e) {
            Log::error('Category Create Error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to create category. Please try again.');
        }
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'category' => 'nullable|string|max:100',
            'name' => 'nullable|string|max:100',
            'parent_id' => 'nullable|exists:categories,id',
            'position' => 'nullable|integer',
            'status' => 'required',
        ]);

        try {
            $category = Category::findOrFail($id);
            // Prevent category from being its own parent
            if ($request->parent_id == $category->id) {
                return redirect()->back()->with('error', 'Category cannot be its own parent.');
            }
            $data = $request->all();
            if (isset($data['name']) && !isset($data['category'])) {
                $data['category'] = $data['name'];
            }
            if (isset($data['status'])) {
                $status = strtoupper(trim($data['status']));
                $data['status'] = ($status === 'ACTIVE' || $status === '1' || $status === 'A') ? 'A' : 'I';
            }
            $category->update($data);
            return redirect()->route('categories.index')->with('success', 'Category updated successfully.');
        } catch (\Exception $e) {
            Log::error('Category Update Error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to update category. Please try again.');
        }
    }

    public function destroy($id)
    {
        try {
            $category = Category::findOrFail($id);
            $category->delete(); // This will cascade delete children if DB constraint is set, or we can handle it here if not. The migration has onDelete cascade.
            return redirect()->route('categories.index')->with('success', 'Category deleted successfully.');
        } catch (\Exception $e) {
            Log::error('Category Delete Error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to delete category. Please try again.');
        }
    }
}
