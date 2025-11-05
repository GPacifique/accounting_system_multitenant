<x-mail::message>
# 👤 New User Registration Alert

**Great news!** A new user has successfully registered on **{{ $appName }}**.

## 📋 User Information

<x-mail::table>
| Field | Details |
|:------|:--------|
| **Full Name** | {{ $newUser->name }} |
| **Email Address** | {{ $newUser->email }} |
| **Assigned Role** | {{ ucfirst($newUser->role) }} |
| **Registration Date** | {{ $newUser->created_at->format('F j, Y \a\t g:i A') }} |
| **Email Status** | {{ $newUser->email_verified_at ? '✅ Verified' : '⏳ Pending Verification' }} |
| **Account Status** | 🟢 Active |
</x-mail::table>

## 🎯 Quick Actions

Take immediate action to welcome and manage this new user:

<x-mail::button :url="$userDetailUrl">
👁️ View User Profile
</x-mail::button>

<x-mail::button :url="$usersUrl">
👥 Manage All Users
</x-mail::button>

## 🔧 Recommended Next Steps

**Consider these administrative actions:**

✅ **Review User Profile** - Verify user information and contact details  
✅ **Welcome Outreach** - Send a personal welcome message if appropriate  
✅ **Role Assessment** - Determine if role assignment matches user needs  
✅ **Permission Review** - Grant additional access if required  
✅ **Team Introduction** - Introduce new user to relevant team members  

## 🛡️ Security & Compliance

- **Default Role:** User has been assigned "{{ ucfirst($newUser->role) }}" role with standard permissions
- **Access Level:** Limited to read-only project information and basic reports
- **Next Review:** Consider user needs and potential role upgrades during onboarding

<x-mail::panel>
🔔 **Admin Reminder:** This is an automated notification. The user has already received their welcome email with login instructions and dashboard access.
</x-mail::panel>

## 📊 Current User Statistics

Want to see the bigger picture? Check your admin dashboard for:
- Total registered users
- Recent activity summaries  
- Role distribution analytics
- User engagement metrics

---

**Thank you** for maintaining excellent user management practices. Your prompt attention to new registrations helps ensure a smooth onboarding experience for all users.

Best regards,  
**{{ $appName }} Admin System**

<x-mail::subcopy>
This notification was sent because you have administrator privileges on {{ $appName }}. 
To manage notification preferences, visit your admin settings.
</x-mail::subcopy>
</x-mail::message>
