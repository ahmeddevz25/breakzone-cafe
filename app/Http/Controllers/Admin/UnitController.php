<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Unit;
use Illuminate\Support\Facades\Log;

class UnitController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:unit management')->only('index');
        $this->middleware('permission:unit add')->only('store');
        $this->middleware('permission:unit edit')->only('update');
        $this->middleware('permission:unit delete')->only('destroy');
    }

    public function index()
    {
        try {
            $units = Unit::orderBy('position', 'asc')->orderBy('id', 'asc')->get();
            return view('admin.units.index', compact('units'));
        } catch (\Exception $e) {
            Log::error('Unit Index Error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Something went wrong while fetching units.');
        }
    }

    public function store(Request $request)
    {
        $request->validate([
            'unit' => 'nullable|string|max:100',
            'name' => 'nullable|string|max:100',
            'quantity' => 'nullable|numeric',
            'position' => 'nullable|integer',
            'status' => 'required',
        ]);

        try {
            $data = $request->all();
            $data['unit'] = $data['unit'] ?? $data['name'] ?? '';
            $data['quantity'] = $data['quantity'] ?? 0;
            $data['position'] = $data['position'] ?? 0;

            $status = strtoupper(trim($request->status ?? 'A'));
            $data['status'] = ($status === 'ACTIVE' || $status === '1' || $status === 'A') ? 'A' : 'I';

            Unit::create($data);
            return redirect()->route('units.index')->with('success', 'Unit created successfully.');
        } catch (\Exception $e) {
            Log::error('Unit Create Error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to create unit. Please try again.');
        }
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'unit' => 'nullable|string|max:100',
            'name' => 'nullable|string|max:100',
            'quantity' => 'nullable|numeric',
            'position' => 'nullable|integer',
            'status' => 'required',
        ]);

        try {
            $unit = Unit::findOrFail($id);
            $data = $request->all();
            if (isset($data['name']) && !isset($data['unit'])) {
                $data['unit'] = $data['name'];
            }
            if (isset($data['status'])) {
                $status = strtoupper(trim($data['status']));
                $data['status'] = ($status === 'ACTIVE' || $status === '1' || $status === 'A') ? 'A' : 'I';
            }
            $unit->update($data);
            return redirect()->route('units.index')->with('success', 'Unit updated successfully.');
        } catch (\Exception $e) {
            Log::error('Unit Update Error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to update unit. Please try again.');
        }
    }

    public function destroy($id)
    {
        try {
            $unit = Unit::findOrFail($id);
            $unit->delete();
            return redirect()->route('units.index')->with('success', 'Unit deleted successfully.');
        } catch (\Exception $e) {
            Log::error('Unit Delete Error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to delete unit. Please try again.');
        }
    }
}
