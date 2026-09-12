<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CafeSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CafeSettingController extends Controller
{
    public function index()
    {
        $setting = CafeSetting::first();
        return view('admin.cafe-settings.index', compact('setting'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'cafe_name' => 'required|string|max:255',
            'cafe_address' => 'required|string|max:500',
            'phone_number' => 'required|string|max:20',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $setting = CafeSetting::first() ?? new CafeSetting();

        if ($request->hasFile('logo')) {
            // Delete old logo if exists
            if ($setting->logo && Storage::disk('public')->exists($setting->logo)) {
                Storage::disk('public')->delete($setting->logo);
            }
            $logoPath = $request->file('logo')->store('logos', 'public');
            $setting->logo = $logoPath;
        }

        $setting->cafe_name = $request->cafe_name;
        $setting->cafe_address = $request->cafe_address;
        $setting->phone_number = $request->phone_number;
        $setting->save();

        // Clear the cache so that the updated logo and other settings reflect immediately
        \Illuminate\Support\Facades\Cache::forget('cafe_setting');

        return redirect()->back()->with('success', 'Cafe settings updated successfully.');
    }
}
