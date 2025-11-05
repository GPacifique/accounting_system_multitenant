<x-mail::message>
# 🎉 Welcome to {{ $appName }}, {{ $user->name }}!

We're absolutely **thrilled** to have you join our community! Thank you for choosing {{ $appName }} to help manage your financial operations and project tracking.

Your journey with us begins now, and we're here to support you every step of the way.

## 🔐 Your Account Details

- **Name:** {{ $user->name }}
- **Email:** {{ $user->email }}
- **Account Type:** {{ ucfirst($user->role) }} User
- **Joined:** {{ $user->created_at->format('F j, Y') }}

## 🚀 What You Can Do Now

As a **{{ ucfirst($user->role) }}** user, you have access to powerful features including:

✅ **Project Overview** - Track and monitor project progress  
✅ **Financial Reports** - View comprehensive financial summaries  
✅ **Dashboard Access** - Your personalized control center  
✅ **Profile Management** - Update your information anytime  

<x-mail::button :url="$dashboardUrl">
🏠 Go to Your Dashboard
</x-mail::button>

## 💡 Getting Started Tips

**First time here?** Here's how to make the most of your experience:

1. **Explore your dashboard** - Get familiar with the interface
2. **Check available reports** - See what insights are available to you
3. **Update your profile** - Add any additional information
4. **Contact support** - We're here if you need any help!

## 🤝 Need Assistance?

Our team is dedicated to your success. If you have questions about:
- **Features and functionality** 
- **Account permissions**
- **Technical support**
- **Getting started guidance**

Don't hesitate to reach out - we're here to help!

<x-mail::panel>
💡 **Pro Tip:** Bookmark your dashboard for quick access. If you need elevated permissions for additional features, please contact your administrator.
</x-mail::panel>

---

**Thank you once again** for choosing {{ $appName }}. We're committed to providing you with the best experience possible and look forward to supporting your success.

Welcome aboard! 🎊

Warm regards,  
**The {{ $appName }} Team**

<x-mail::subcopy>
Having trouble with the button? Copy and paste this URL into your browser: {{ $dashboardUrl }}
</x-mail::subcopy>
</x-mail::message>
