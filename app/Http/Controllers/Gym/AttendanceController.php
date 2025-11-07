<?php

namespace App\Http\Controllers\Gym;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Attendance;
use App\Models\Member;
use Illuminate\Support\Facades\Auth;

class AttendanceController extends Controller
{
    public function index()
    {
        $attendances = Attendance::with('member', 'creator')
            ->latest()
            ->limit(100)
            ->get();

        return view('gym.attendances.index', compact('attendances'));
    }

    public function create()
    {
        return view('gym.attendances.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'member_id' => 'nullable|string', // accept scanned member_id or numeric id
            'notes' => 'nullable|string',
            'action' => 'nullable|in:checkin,checkout',
        ]);

        // Try to find member by member_id (string) first, otherwise by numeric id
        $member = null;
        if (!empty($data['member_id'])) {
            $key = $data['member_id'];
            $member = Member::where('member_id', $key)->orWhere('id', $key)->first();
        }

        if (!$member) {
            return back()->withErrors(['member_id' => 'Member not found for provided ID'])->withInput();
        }

        $tenant = app('currentTenant') ?? null;

        if (($data['action'] ?? 'checkin') === 'checkout') {
            // Find latest open attendance for member and set checked_out_at
            $attendance = Attendance::where('member_id', $member->id)->whereNull('checked_out_at')->latest()->first();
            if ($attendance) {
                $attendance->update(['checked_out_at' => now(), 'notes' => $data['notes'] ?? $attendance->notes]);
            } else {
                // No open attendance — create one with both timestamps
                $attendance = Attendance::create([
                    'tenant_id' => $tenant ? $tenant->id : null,
                    'member_id' => $member->id,
                    'checked_in_at' => now(),
                    'checked_out_at' => now(),
                    'notes' => $data['notes'] ?? null,
                    'created_by' => Auth::id(),
                ]);
            }
        } else {
            // Check-in
            $attendance = Attendance::create([
                'tenant_id' => $tenant ? $tenant->id : null,
                'member_id' => $member->id,
                'checked_in_at' => now(),
                'notes' => $data['notes'] ?? null,
                'created_by' => Auth::id(),
            ]);
        }

        return redirect()->route('gym.attendances.index')->with('success', 'Attendance recorded.');
    }
}
