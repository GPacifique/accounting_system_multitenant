<?php

namespace App\Http\Controllers\Gym;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Trainer;
use Illuminate\Support\Facades\Auth;

class TrainerController extends Controller
{
    public function index()
    {
        $trainers = Trainer::latest()->paginate(20);
        return view('gym.trainers.index', compact('trainers'));
    }

    public function create()
    {
        return view('gym.trainers.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:50',
            'specializations' => 'nullable|array',
            'experience_years' => 'nullable|numeric|min:0',
            'certifications' => 'nullable|string',
            'hourly_rate' => 'nullable|numeric|min:0',
            'photo' => 'nullable|image|mimes:jpg,jpeg,png|max:5120',
        ]);

        $tenant = app()->bound('currentTenant') ? app('currentTenant') : null;

        $trainerData = [
            'tenant_id' => $tenant ? $tenant->id : null,
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'email' => $data['email'] ?? null,
            'phone' => $data['phone'] ?? null,
            'specializations' => $data['specializations'] ?? null,
            'experience_years' => $data['experience_years'] ?? null,
            'certifications' => $data['certifications'] ?? null,
            'hourly_rate' => $data['hourly_rate'] ?? null,
            'status' => Trainer::STATUS_ACTIVE,
        ];

        // Handle photo upload
        if ($request->hasFile('photo')) {
            $path = $request->file('photo')->store('trainers', 'public');
            $trainerData['profile_image'] = $path;
        }

        $trainer = Trainer::create($trainerData);

        return redirect()->route('gym.trainers.index')->with('success', 'Trainer created successfully.');
    }

    public function show(Trainer $trainer)
    {
        if (view()->exists('gym.trainers.show')) {
            return view('gym.trainers.show', compact('trainer'));
        }

        return redirect()->route('gym.trainers.index');
    }

    public function edit(Trainer $trainer)
    {
        if (view()->exists('gym.trainers.edit')) {
            return view('gym.trainers.edit', compact('trainer'));
        }

        return redirect()->route('gym.trainers.index');
    }

    public function update(Request $request, Trainer $trainer)
    {
        $data = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:50',
            'specializations' => 'nullable|array',
            'experience_years' => 'nullable|numeric|min:0',
            'certifications' => 'nullable|string',
            'hourly_rate' => 'nullable|numeric|min:0',
            'photo' => 'nullable|image|mimes:jpg,jpeg,png|max:5120',
        ]);

        $trainer->fill([
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'email' => $data['email'] ?? null,
            'phone' => $data['phone'] ?? null,
            'specializations' => $data['specializations'] ?? null,
            'experience_years' => $data['experience_years'] ?? null,
            'certifications' => $data['certifications'] ?? null,
            'hourly_rate' => $data['hourly_rate'] ?? null,
        ]);

        if ($request->hasFile('photo')) {
            $path = $request->file('photo')->store('trainers', 'public');
            $trainer->profile_image = $path;
        }

        $trainer->save();

        return redirect()->route('gym.trainers.index')->with('success', 'Trainer updated successfully.');
    }

    public function destroy(Trainer $trainer)
    {
        $trainer->delete();
        return redirect()->route('gym.trainers.index')->with('success', 'Trainer removed.');
    }
}
