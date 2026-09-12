<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Brand;
use Illuminate\Support\Facades\Log;

class BrandController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:brand management')->only('index');
        $this->middleware('permission:brand add')->only('store');
        $this->middleware('permission:brand edit')->only('update');
        $this->middleware('permission:brand delete')->only('destroy');
    }

    public function index()
    {
        try {
            $brands = Brand::orderBy('id', 'asc')->get();
            return view('admin.brands.index', compact('brands'));
        } catch (\Exception $e) {
            Log::error('Brand Index Error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Something went wrong while fetching brands.');
        }
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'position' => 'nullable|integer',
            'status' => 'required',
        ]);

        try {
            $data = $request->all();
            $data['position'] = $data['position'] ?? 0;
            $status = strtoupper(trim($request->status ?? 'A'));
            $data['status'] = ($status === 'ACTIVE' || $status === '1' || $status === 'A') ? 'A' : 'I';

            Brand::create($data);
            return redirect()->route('brands.index')->with('success', 'Brand created successfully.');
        } catch (\Exception $e) {
            Log::error('Brand Create Error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to create brand. Please try again.');
        }
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'position' => 'nullable|integer',
            'status' => 'required',
        ]);

        try {
            $brand = Brand::findOrFail($id);
            $data = $request->all();
            if (isset($data['status'])) {
                $status = strtoupper(trim($data['status']));
                $data['status'] = ($status === 'ACTIVE' || $status === '1' || $status === 'A') ? 'A' : 'I';
            }
            $brand->update($data);
            return redirect()->route('brands.index')->with('success', 'Brand updated successfully.');
        } catch (\Exception $e) {
            Log::error('Brand Update Error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to update brand. Please try again.');
        }
    }

    public function destroy($id)
    {
        try {
            $brand = Brand::findOrFail($id);
            $brand->delete();
            return redirect()->route('brands.index')->with('success', 'Brand deleted successfully.');
        } catch (\Exception $e) {
            Log::error('Brand Delete Error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to delete brand. Please try again.');
        }
    }
}
