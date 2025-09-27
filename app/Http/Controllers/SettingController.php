<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Setting;

class SettingController extends Controller
{
    public function __construct()
    {
        // Restrict to admin role if using spatie/laravel-permission
        $this->middleware(['auth', 'role:admin']);
    }

    /**
     * Display settings form.
     */
    public function index()
    {
        // Load all settings into key => value array
        $settings = Setting::pluck('value', 'key')->toArray();
        return view('settings.index', compact('settings'));
    }

    /**
     * Update settings in storage.
     */
    public function update(Request $request)
    {
        $validated = $request->validate([
            'app_name'   => 'required|string|max:255',
            'company_email' => 'nullable|email|max:255',
            'tax_rate'  => 'nullable|numeric|min:0',
        ]);

        foreach ($validated as $key => $value) {
            Setting::updateOrCreate(
                ['key' => $key],
                ['value' => $value]
            );
        }

        return redirect()->route('settings.index')->with('success', 'Settings updated successfully.');
    }
}
