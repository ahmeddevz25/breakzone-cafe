<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Store;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class StoreController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:store management')->only('index');
        $this->middleware('permission:store add')->only('store');
        $this->middleware('permission:store edit')->only('update');
        $this->middleware('permission:store delete')->only('destroy');
    }

    public function index()
    {
        try {
            $stores = Store::orderBy('id', 'asc')->get();
            return view('admin.store.index', compact('stores'));
        } catch (\Exception $e) {
            Log::error('Store Index Error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Something went wrong while fetching stores.');
        }
    }

    public function store(Request $request)
    {
        $request->validate([
            'store' => 'nullable|string|max:100',
            'name' => 'nullable|string|max:100',
            'printer_ip_address' => 'nullable|string|max:20',
            'printer_ip' => 'nullable|string|max:20',
            'printer_port' => 'nullable|string|max:5',
            'opening_balance' => 'nullable|numeric',
            'position' => 'nullable|integer',
            'status' => 'required',
        ]);

        try {
            $data = $request->all();
            $data['store'] = $data['store'] ?? $data['name'] ?? '';
            $data['printer_ip_address'] = $data['printer_ip_address'] ?? $data['printer_ip'] ?? null;
            $data['company_id'] = $data['company_id'] ?? 0;
            $data['opening_balance'] = $data['opening_balance'] ?? 0;
            $data['position'] = $data['position'] ?? 0;

            $status = strtoupper(trim($request->status ?? 'A'));
            $data['status'] = ($status === 'ACTIVE' || $status === '1' || $status === 'A') ? 'A' : 'I';

            Store::create($data);
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['status' => true, 'message' => 'Store created successfully.']);
            }
            return redirect()->route('stores.index')->with('success', 'Store created successfully.');
        } catch (\Exception $e) {
            Log::error('Store Create Error: ' . $e->getMessage());
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['status' => false, 'message' => 'Failed to create store. Please try again.'], 500);
            }
            return redirect()->back()->with('error', 'Failed to create store. Please try again.');
        }
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'store' => 'nullable|string|max:100',
            'name' => 'nullable|string|max:100',
            'printer_ip_address' => 'nullable|string|max:20',
            'printer_ip' => 'nullable|string|max:20',
            'printer_port' => 'nullable|string|max:5',
            'opening_balance' => 'nullable|numeric',
            'position' => 'nullable|integer',
            'status' => 'required',
        ]);

        try {

            $store = Store::findOrFail($id);
            $data = $request->all();
            if (isset($data['name']) && !isset($data['store'])) {
                $data['store'] = $data['name'];
            }
            if (isset($data['printer_ip']) && !isset($data['printer_ip_address'])) {
                $data['printer_ip_address'] = $data['printer_ip'];
            }
            if (isset($data['status'])) {
                $status = strtoupper(trim($data['status']));
                $data['status'] = ($status === 'ACTIVE' || $status === '1' || $status === 'A') ? 'A' : 'I';
            }
            $store->update($data);
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['status' => true, 'message' => 'Store updated successfully.']);
            }
            return redirect()->route('stores.index')->with('success', 'Store updated successfully.');
        } catch (\Exception $e) {
            Log::error('Store Update Error: ' . $e->getMessage());
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['status' => false, 'message' => 'Failed to update store. Please try again.'], 500);
            }
            return redirect()->back()->with('error', 'Failed to update store. Please try again.');
        }
    }

    public function destroy($id)
    {
        try {
            $store = Store::findOrFail($id);
            $store->delete();
            return redirect()->route('stores.index')->with('success', 'Store deleted successfully.');
        } catch (\Exception $e) {
            Log::error('Store Delete Error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to delete store. Please try again.');
        }
    }
}
