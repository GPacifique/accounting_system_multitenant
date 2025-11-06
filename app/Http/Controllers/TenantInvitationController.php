<?php

namespace App\Http\Controllers;

use App\Models\Tenant;
use App\Models\TenantInvitation;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class TenantInvitationController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show invitations for a tenant.
     */
    public function index(Tenant $tenant)
    {
        $this->authorize('view', $tenant);

        $invitations = $tenant->invitations()
                             ->with(['invitedBy'])
                             ->orderBy('created_at', 'desc')
                             ->paginate(15);

        return view('admin.tenants.invitations.index', compact('tenant', 'invitations'));
    }

    /**
     * Show the form for creating a new invitation.
     */
    public function create(Tenant $tenant)
    {
        $this->authorize('update', $tenant);

        return view('admin.tenants.invitations.create', compact('tenant'));
    }

    /**
     * Store a newly created invitation.
     */
    public function store(Request $request, Tenant $tenant)
    {
        $this->authorize('update', $tenant);

        $validator = Validator::make($request->all(), [
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('tenant_invitations')->where(function ($query) use ($tenant) {
                    return $query->where('tenant_id', $tenant->id)
                                ->whereIn('status', ['pending', 'accepted']);
                }),
            ],
            'role' => 'required|in:admin,manager,accountant,user',
            'is_admin' => 'boolean',
            'message' => 'nullable|string|max:500',
            'expires_in_days' => 'required|integer|min:1|max:30',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                           ->withErrors($validator)
                           ->withInput();
        }

        // Check if user already exists and is in tenant
        $existingUser = User::where('email', $request->email)->first();
        if ($existingUser && $existingUser->belongsToTenant($tenant->id)) {
            return redirect()->back()
                           ->with('error', 'User is already a member of this tenant.')
                           ->withInput();
        }

        // Create invitation
        $invitation = TenantInvitation::create([
            'tenant_id' => $tenant->id,
            'email' => $request->email,
            'role' => $request->role,
            'is_admin' => $request->boolean('is_admin'),
            'invited_by' => Auth::id(),
            'token' => TenantInvitation::generateToken(),
            'expires_at' => now()->addDays($request->expires_in_days),
            'message' => $request->message,
            'status' => TenantInvitation::STATUS_PENDING,
        ]);

        // Send invitation email (mock for now)
        $this->sendInvitationEmail($invitation);

        return redirect()->route('admin.tenants.invitations.index', $tenant)
                        ->with('success', 'Invitation sent successfully!');
    }

    /**
     * Show the specified invitation.
     */
    public function show($token)
    {
        $invitation = TenantInvitation::where('token', $token)
                                    ->with(['tenant', 'invitedBy'])
                                    ->firstOrFail();

        // Mark as expired if needed
        $invitation->markExpiredIfNeeded();

        if (!$invitation->isPending()) {
            return view('invitations.invalid', compact('invitation'));
        }

        if ($invitation->isExpired()) {
            return view('invitations.expired', compact('invitation'));
        }

        return view('invitations.show', compact('invitation'));
    }

    /**
     * Accept an invitation.
     */
    public function accept(Request $request, $token)
    {
        $invitation = TenantInvitation::where('token', $token)->firstOrFail();
        
        // Mark as expired if needed
        $invitation->markExpiredIfNeeded();

        if (!$invitation->isPending() || $invitation->isExpired()) {
            return redirect()->route('invitations.show', $token)
                           ->with('error', 'This invitation is no longer valid.');
        }

        $user = Auth::user();
        if (!$user) {
            // Redirect to login with return URL
            return redirect()->route('login', ['return' => route('invitations.show', $token)])
                           ->with('message', 'Please log in to accept this invitation.');
        }

        // Check if user's email matches invitation
        if ($user->email !== $invitation->email) {
            return redirect()->route('invitations.show', $token)
                           ->with('error', 'You must be logged in with the invited email address.');
        }

        // Accept the invitation
        if ($invitation->accept($user)) {
            return redirect()->route('tenant.dashboard')
                           ->with('success', "Welcome to {$invitation->tenant->name}!");
        }

        return redirect()->route('invitations.show', $token)
                       ->with('error', 'Failed to accept invitation.');
    }

    /**
     * Decline an invitation.
     */
    public function decline($token)
    {
        $invitation = TenantInvitation::where('token', $token)->firstOrFail();
        
        if ($invitation->isPending()) {
            $invitation->cancel();
        }

        return view('invitations.declined', compact('invitation'));
    }

    /**
     * Cancel an invitation.
     */
    public function cancel(TenantInvitation $invitation)
    {
        $this->authorize('update', $invitation->tenant);

        $invitation->cancel();

        return redirect()->back()
                        ->with('success', 'Invitation cancelled successfully.');
    }

    /**
     * Resend an invitation.
     */
    public function resend(TenantInvitation $invitation)
    {
        $this->authorize('update', $invitation->tenant);

        if (!$invitation->isPending()) {
            return redirect()->back()
                           ->with('error', 'Only pending invitations can be resent.');
        }

        // Extend expiry date
        $invitation->update([
            'expires_at' => now()->addDays(7),
            'token' => TenantInvitation::generateToken(), // New token for security
        ]);

        // Resend email
        $this->sendInvitationEmail($invitation);

        return redirect()->back()
                        ->with('success', 'Invitation resent successfully.');
    }

    /**
     * Send invitation email (mock implementation).
     */
    protected function sendInvitationEmail(TenantInvitation $invitation)
    {
        // Mock email sending - in production, you would use Mail::send()
        // For now, we'll just log the invitation details
        logger('Tenant Invitation Email', [
            'to' => $invitation->email,
            'tenant' => $invitation->tenant->name,
            'role' => $invitation->getRoleLabel(),
            'invited_by' => $invitation->invitedBy->name,
            'expires_at' => $invitation->expires_at->format('M d, Y'),
            'accept_url' => route('invitations.show', $invitation->token),
        ]);

        // In production, you would do something like:
        // Mail::to($invitation->email)->send(new TenantInvitationMail($invitation));
    }
}
