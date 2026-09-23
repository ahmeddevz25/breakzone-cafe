<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Supplier;
use Illuminate\Support\Facades\Log;

class SupplierController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:supplier management')->only('index');
        $this->middleware('permission:supplier add')->only('store');
        $this->middleware('permission:supplier edit')->only('update');
        $this->middleware('permission:supplier delete')->only('destroy');
    }

    public function index()
    {
        try {
            $suppliers = Supplier::orderBy('id', 'asc')->get();
            return view('admin.suppliers.index', compact('suppliers'));
        } catch (\Exception $e) {
            Log::error('Supplier Index Error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Something went wrong while fetching suppliers.');
        }
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'nullable|string|max:200',
            'company' => 'nullable|string|max:200',
            'address' => 'nullable|string|max:250',
            'mobile' => ['required', 'regex:/^[0-9+\-\s()]{7,25}$/'],
            'ntn_no' => ['nullable', 'regex:/^[0-9\-]{5,20}$/'],
            'ntn' => ['nullable', 'regex:/^[0-9\-]{5,20}$/'],
            'email' => 'nullable|email|max:100|unique:cafe_suppliers,email',
            'status' => 'required',
        ], [
            'mobile.required' => 'The mobile number field is required.',
            'mobile.regex' => 'The mobile number format is invalid. Please enter numbers only (e.g., 03001234567).',
            'ntn_no.regex' => 'The NTN # format is invalid. Please enter numbers and hyphens only (e.g., 1234567-8).',
            'ntn.regex' => 'The NTN # format is invalid. Please enter numbers and hyphens only (e.g., 1234567-8).',
        ]);

        try {
            $data = $request->all();
            if (empty($data['ntn_no']) && !empty($data['ntn'])) {
                $data['ntn_no'] = $data['ntn'];
            }
            $status = strtoupper(trim($request->status ?? 'A'));
            $data['status'] = ($status === 'ACTIVE' || $status === '1' || $status === 'A') ? 'A' : 'I';

            Supplier::create($data);
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['status' => true, 'message' => 'Supplier created successfully.']);
            }
            return redirect()->route('suppliers.index')->with('success', 'Supplier created successfully.');
        } catch (\Exception $e) {
            Log::error('Supplier Create Error: ' . $e->getMessage());
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['status' => false, 'message' => 'Failed to create supplier. Please try again.'], 500);
            }
            return redirect()->back()->with('error', 'Failed to create supplier. Please try again.');
        }
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'nullable|string|max:200',
            'company' => 'nullable|string|max:200',
            'address' => 'nullable|string|max:250',
            'mobile' => ['required', 'regex:/^[0-9+\-\s()]{7,25}$/'],
            'ntn_no' => ['nullable', 'regex:/^[0-9\-]{5,20}$/'],
            'ntn' => ['nullable', 'regex:/^[0-9\-]{5,20}$/'],
            'email' => 'nullable|email|max:100|unique:cafe_suppliers,email,' . $id,
            'status' => 'required',
        ], [
            'mobile.required' => 'The mobile number field is required.',
            'mobile.regex' => 'The mobile number format is invalid. Please enter numbers only (e.g., 03001234567).',
            'ntn_no.regex' => 'The NTN # format is invalid. Please enter numbers and hyphens only (e.g., 1234567-8).',
            'ntn.regex' => 'The NTN # format is invalid. Please enter numbers and hyphens only (e.g., 1234567-8).',
        ]);

        try {
            $supplier = Supplier::findOrFail($id);
            $data = $request->all();
            if (isset($data['ntn']) && !isset($data['ntn_no'])) {
                $data['ntn_no'] = $data['ntn'];
            }
            if (isset($data['status'])) {
                $status = strtoupper(trim($data['status']));
                $data['status'] = ($status === 'ACTIVE' || $status === '1' || $status === 'A') ? 'A' : 'I';
            }
            $supplier->update($data);
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['status' => true, 'message' => 'Supplier updated successfully.']);
            }
            return redirect()->route('suppliers.index')->with('success', 'Supplier updated successfully.');
        } catch (\Exception $e) {
            Log::error('Supplier Update Error: ' . $e->getMessage());
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['status' => false, 'message' => 'Failed to update supplier. Please try again.'], 500);
            }
            return redirect()->back()->with('error', 'Failed to update supplier. Please try again.');
        }
    }

    public function destroy($id)
    {
        try {
            $supplier = Supplier::findOrFail($id);
            $supplier->delete();
            return redirect()->route('suppliers.index')->with('success', 'Supplier deleted successfully.');
        } catch (\Exception $e) {
            Log::error('Supplier Delete Error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to delete supplier. Please try again.');
        }
    }
}
