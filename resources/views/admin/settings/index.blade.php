{{-- resources/views/admin/settings/index.blade.php --}}
@extends('layouts.app')

@section('title', 'System Settings - System Administration | SiteLedger')
@section('meta_description', 'Configure global system settings, preferences, and parameters for the SiteLedger multitenant accounting system.')
@section('meta_keywords', 'system settings, configuration, admin settings, global preferences, system administration')

@vite(['resources/css/app.css', 'resources/js/app.js'])

@section('content')
<div class="container mx-auto px-4 py-8">
    {{-- Page Header --}}
    <div class="bg-gradient-to-r from-gray-700 to-gray-900 rounded-xl shadow-lg p-6 mb-8 text-white">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold flex items-center">
                    <div class="theme-aware-bg-card/20 rounded-lg p-2 mr-4">
                        <i class="fas fa-cogs text-2xl"></i>
                    </div>
                    System Settings
                </h1>
                <p class="text-gray-100 mt-2">Configure global system parameters and preferences</p>
            </div>
            <div class="flex space-x-3">
                <button onclick="exportSettings()" class="theme-aware-bg-card/20 text-white px-6 py-3 rounded-lg font-semibold hover:theme-aware-bg-card/30 transition">
                    <i class="fas fa-download mr-2"></i>
                    Export Config
                </button>
                <button onclick="importSettings()" class="theme-aware-bg-card theme-aware-text px-6 py-3 rounded-lg font-semibold hover:theme-aware-bg-secondary transition">
                    <i class="fas fa-upload mr-2"></i>
                    Import Config
                </button>
            </div>
        </div>
    </div>

    {{-- Settings Navigation --}}
    <div class="theme-aware-bg-card rounded-xl shadow-lg mb-8">
        <div class="border-b theme-aware-border">
            <nav class="flex space-x-8 px-6" aria-label="Settings Navigation">
                <button onclick="showSection('general')" 
                        class="settings-nav-btn py-4 px-1 border-b-2 border-transparent theme-aware-text-muted hover:theme-aware-text-secondary hover:theme-aware-border font-medium text-sm active"
                        data-section="general">
                    <i class="fas fa-cog mr-2"></i>
                    General
                </button>
                <button onclick="showSection('security')" 
                        class="settings-nav-btn py-4 px-1 border-b-2 border-transparent theme-aware-text-muted hover:theme-aware-text-secondary hover:theme-aware-border font-medium text-sm"
                        data-section="security">
                    <i class="fas fa-shield-alt mr-2"></i>
                    Security
                </button>
                <button onclick="showSection('email')" 
                        class="settings-nav-btn py-4 px-1 border-b-2 border-transparent theme-aware-text-muted hover:theme-aware-text-secondary hover:theme-aware-border font-medium text-sm"
                        data-section="email">
                    <i class="fas fa-envelope mr-2"></i>
                    Email
                </button>
                <button onclick="showSection('storage')" 
                        class="settings-nav-btn py-4 px-1 border-b-2 border-transparent theme-aware-text-muted hover:theme-aware-text-secondary hover:theme-aware-border font-medium text-sm"
                        data-section="storage">
                    <i class="fas fa-database mr-2"></i>
                    Storage
                </button>
                <button onclick="showSection('api')" 
                        class="settings-nav-btn py-4 px-1 border-b-2 border-transparent theme-aware-text-muted hover:theme-aware-text-secondary hover:theme-aware-border font-medium text-sm"
                        data-section="api">
                    <i class="fas fa-code mr-2"></i>
                    API
                </button>
                <button onclick="showSection('maintenance')" 
                        class="settings-nav-btn py-4 px-1 border-b-2 border-transparent theme-aware-text-muted hover:theme-aware-text-secondary hover:theme-aware-border font-medium text-sm"
                        data-section="maintenance">
                    <i class="fas fa-tools mr-2"></i>
                    Maintenance
                </button>
            </nav>
        </div>
    </div>

    <form action="{{ route('admin.settings.update') }}" method="POST" id="settings-form" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        {{-- General Settings --}}
        <div id="general-section" class="settings-section">
            <div class="theme-aware-bg-card rounded-xl shadow-lg p-6 mb-8">
                <h2 class="text-xl font-bold theme-aware-text mb-6 flex items-center">
                    <i class="fas fa-cog text-blue-600 mr-3"></i>
                    General Settings
                </h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="app_name" class="block text-sm font-medium theme-aware-text-secondary mb-2">
                            Application Name
                        </label>
                        <input type="text" 
                               id="app_name" 
                               name="app_name" 
                               value="{{ old('app_name', $settings['app_name'] ?? 'SiteLedger') }}"
                               class="w-full px-4 py-3 border theme-aware-border rounded-lg focus:ring-2 focus:ring-primary focus:theme-aware-border-focus">
                    </div>

                    <div>
                        <label for="app_domain" class="block text-sm font-medium theme-aware-text-secondary mb-2">
                            Application Domain
                        </label>
                        <input type="text" 
                               id="app_domain" 
                               name="app_domain" 
                               value="{{ old('app_domain', $settings['app_domain'] ?? 'siteledger.com') }}"
                               class="w-full px-4 py-3 border theme-aware-border rounded-lg focus:ring-2 focus:ring-primary focus:theme-aware-border-focus">
                    </div>

                    <div>
                        <label for="default_timezone" class="block text-sm font-medium theme-aware-text-secondary mb-2">
                            Default Timezone
                        </label>
                        <select id="default_timezone" 
                                name="default_timezone" 
                                class="w-full px-4 py-3 border theme-aware-border rounded-lg focus:ring-2 focus:ring-primary focus:theme-aware-border-focus">
                            <option value="UTC" {{ old('default_timezone', $settings['default_timezone'] ?? 'UTC') === 'UTC' ? 'selected' : '' }}>UTC (GMT+0)</option>
                            <option value="America/New_York" {{ old('default_timezone', $settings['default_timezone']) === 'America/New_York' ? 'selected' : '' }}>Eastern Time (GMT-5)</option>
                            <option value="America/Chicago" {{ old('default_timezone', $settings['default_timezone']) === 'America/Chicago' ? 'selected' : '' }}>Central Time (GMT-6)</option>
                            <option value="America/Denver" {{ old('default_timezone', $settings['default_timezone']) === 'America/Denver' ? 'selected' : '' }}>Mountain Time (GMT-7)</option>
                            <option value="America/Los_Angeles" {{ old('default_timezone', $settings['default_timezone']) === 'America/Los_Angeles' ? 'selected' : '' }}>Pacific Time (GMT-8)</option>
                            <option value="Europe/London" {{ old('default_timezone', $settings['default_timezone']) === 'Europe/London' ? 'selected' : '' }}>London (GMT+0)</option>
                            <option value="Europe/Paris" {{ old('default_timezone', $settings['default_timezone']) === 'Europe/Paris' ? 'selected' : '' }}>Paris (GMT+1)</option>
                            <option value="Asia/Tokyo" {{ old('default_timezone', $settings['default_timezone']) === 'Asia/Tokyo' ? 'selected' : '' }}>Tokyo (GMT+9)</option>
                            <option value="Africa/Kigali" {{ old('default_timezone', $settings['default_timezone'] ?? 'Africa/Kigali') === 'Africa/Kigali' ? 'selected' : '' }}>Kigali (GMT+2)</option>
                        </select>
                    </div>

                    <div>
                        <label for="default_currency" class="block text-sm font-medium theme-aware-text-secondary mb-2">
                            Default Currency
                        </label>
                        <select id="default_currency" 
                                name="default_currency" 
                                class="w-full px-4 py-3 border theme-aware-border rounded-lg focus:ring-2 focus:ring-primary focus:theme-aware-border-focus">
                            <option value="RWF" {{ old('default_currency', $settings['default_currency'] ?? 'RWF') === 'RWF' ? 'selected' : '' }}>RWF - Rwandan Franc</option>
                            <option value="USD" {{ old('default_currency', $settings['default_currency']) === 'USD' ? 'selected' : '' }}>USD - US Dollar</option>
                            <option value="EUR" {{ old('default_currency', $settings['default_currency']) === 'EUR' ? 'selected' : '' }}>EUR - Euro</option>
                            <option value="GBP" {{ old('default_currency', $settings['default_currency']) === 'GBP' ? 'selected' : '' }}>GBP - British Pound</option>
                            <option value="CAD" {{ old('default_currency', $settings['default_currency']) === 'CAD' ? 'selected' : '' }}>CAD - Canadian Dollar</option>
                        </select>
                    </div>

                    <div>
                        <label for="default_language" class="block text-sm font-medium theme-aware-text-secondary mb-2">
                            Default Language
                        </label>
                        <select id="default_language" 
                                name="default_language" 
                                class="w-full px-4 py-3 border theme-aware-border rounded-lg focus:ring-2 focus:ring-primary focus:theme-aware-border-focus">
                            <option value="en" {{ old('default_language', $settings['default_language'] ?? 'en') === 'en' ? 'selected' : '' }}>English</option>
                            <option value="fr" {{ old('default_language', $settings['default_language']) === 'fr' ? 'selected' : '' }}>French</option>
                            <option value="rw" {{ old('default_language', $settings['default_language']) === 'rw' ? 'selected' : '' }}>Kinyarwanda</option>
                        </select>
                    </div>

                    <div>
                        <label for="max_tenants" class="block text-sm font-medium theme-aware-text-secondary mb-2">
                            Maximum Tenants
                        </label>
                        <input type="number" 
                               id="max_tenants" 
                               name="max_tenants" 
                               value="{{ old('max_tenants', $settings['max_tenants'] ?? 1000) }}"
                               min="1"
                               class="w-full px-4 py-3 border theme-aware-border rounded-lg focus:ring-2 focus:ring-primary focus:theme-aware-border-focus">
                    </div>

                    <div class="md:col-span-2">
                        <label for="app_logo" class="block text-sm font-medium theme-aware-text-secondary mb-2">
                            Application Logo
                        </label>
                        @if(isset($settings['app_logo']) && $settings['app_logo'])
                            <div class="mb-3 p-3 theme-aware-bg-secondary rounded-lg">
                                <img src="{{ Storage::url($settings['app_logo']) }}" alt="Current Logo" class="h-16 w-auto object-contain">
                                <p class="text-xs theme-aware-text-muted mt-1">Current logo</p>
                            </div>
                        @endif
                        <input type="file" 
                               id="app_logo" 
                               name="app_logo" 
                               accept="image/*"
                               class="w-full px-4 py-3 border theme-aware-border rounded-lg focus:ring-2 focus:ring-primary focus:theme-aware-border-focus">
                        <p class="mt-1 text-xs theme-aware-text-muted">PNG, JPG, or SVG. Max size 2MB.</p>
                    </div>

                    <div class="md:col-span-2">
                        <label class="flex items-center">
                            <input type="checkbox" 
                                   name="maintenance_mode" 
                                   value="1"
                                   {{ old('maintenance_mode', $settings['maintenance_mode'] ?? false) ? 'checked' : '' }}
                                   class="rounded theme-aware-border text-blue-600 focus:ring-primary">
                            <span class="ml-2 text-sm theme-aware-text-secondary">Enable Maintenance Mode</span>
                        </label>
                        <p class="mt-1 text-xs theme-aware-text-muted">When enabled, only administrators can access the system</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Security Settings --}}
        <div id="security-section" class="settings-section hidden">
            <div class="theme-aware-bg-card rounded-xl shadow-lg p-6 mb-8">
                <h2 class="text-xl font-bold theme-aware-text mb-6 flex items-center">
                    <i class="fas fa-shield-alt text-red-600 mr-3"></i>
                    Security Settings
                </h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="session_timeout" class="block text-sm font-medium theme-aware-text-secondary mb-2">
                            Session Timeout (minutes)
                        </label>
                        <input type="number" 
                               id="session_timeout" 
                               name="session_timeout" 
                               value="{{ old('session_timeout', $settings['session_timeout'] ?? 120) }}"
                               min="5"
                               max="1440"
                               class="w-full px-4 py-3 border theme-aware-border rounded-lg focus:ring-2 focus:ring-primary focus:theme-aware-border-focus">
                    </div>

                    <div>
                        <label for="max_login_attempts" class="block text-sm font-medium theme-aware-text-secondary mb-2">
                            Max Login Attempts
                        </label>
                        <input type="number" 
                               id="max_login_attempts" 
                               name="max_login_attempts" 
                               value="{{ old('max_login_attempts', $settings['max_login_attempts'] ?? 5) }}"
                               min="3"
                               max="10"
                               class="w-full px-4 py-3 border theme-aware-border rounded-lg focus:ring-2 focus:ring-primary focus:theme-aware-border-focus">
                    </div>

                    <div>
                        <label for="password_min_length" class="block text-sm font-medium theme-aware-text-secondary mb-2">
                            Minimum Password Length
                        </label>
                        <input type="number" 
                               id="password_min_length" 
                               name="password_min_length" 
                               value="{{ old('password_min_length', $settings['password_min_length'] ?? 8) }}"
                               min="6"
                               max="20"
                               class="w-full px-4 py-3 border theme-aware-border rounded-lg focus:ring-2 focus:ring-primary focus:theme-aware-border-focus">
                    </div>

                    <div>
                        <label for="lockout_duration" class="block text-sm font-medium theme-aware-text-secondary mb-2">
                            Account Lockout Duration (minutes)
                        </label>
                        <input type="number" 
                               id="lockout_duration" 
                               name="lockout_duration" 
                               value="{{ old('lockout_duration', $settings['lockout_duration'] ?? 15) }}"
                               min="5"
                               max="60"
                               class="w-full px-4 py-3 border theme-aware-border rounded-lg focus:ring-2 focus:ring-primary focus:theme-aware-border-focus">
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium theme-aware-text-secondary mb-3">Security Features</label>
                        <div class="space-y-3">
                            <label class="flex items-center">
                                <input type="checkbox" 
                                       name="require_email_verification" 
                                       value="1"
                                       {{ old('require_email_verification', $settings['require_email_verification'] ?? true) ? 'checked' : '' }}
                                       class="rounded theme-aware-border text-blue-600 focus:ring-primary">
                                <span class="ml-2 text-sm theme-aware-text-secondary">Require Email Verification</span>
                            </label>

                            <label class="flex items-center">
                                <input type="checkbox" 
                                       name="enable_2fa" 
                                       value="1"
                                       {{ old('enable_2fa', $settings['enable_2fa'] ?? false) ? 'checked' : '' }}
                                       class="rounded theme-aware-border text-blue-600 focus:ring-primary">
                                <span class="ml-2 text-sm theme-aware-text-secondary">Enable Two-Factor Authentication</span>
                            </label>

                            <label class="flex items-center">
                                <input type="checkbox" 
                                       name="force_https" 
                                       value="1"
                                       {{ old('force_https', $settings['force_https'] ?? true) ? 'checked' : '' }}
                                       class="rounded theme-aware-border text-blue-600 focus:ring-primary">
                                <span class="ml-2 text-sm theme-aware-text-secondary">Force HTTPS</span>
                            </label>

                            <label class="flex items-center">
                                <input type="checkbox" 
                                       name="enable_audit_logging" 
                                       value="1"
                                       {{ old('enable_audit_logging', $settings['enable_audit_logging'] ?? true) ? 'checked' : '' }}
                                       class="rounded theme-aware-border text-blue-600 focus:ring-primary">
                                <span class="ml-2 text-sm theme-aware-text-secondary">Enable Audit Logging</span>
                            </label>

                            <label class="flex items-center">
                                <input type="checkbox" 
                                       name="password_require_special" 
                                       value="1"
                                       {{ old('password_require_special', $settings['password_require_special'] ?? true) ? 'checked' : '' }}
                                       class="rounded theme-aware-border text-blue-600 focus:ring-primary">
                                <span class="ml-2 text-sm theme-aware-text-secondary">Require Special Characters in Passwords</span>
                            </label>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Email Settings --}}
        <div id="email-section" class="settings-section hidden">
            <div class="theme-aware-bg-card rounded-xl shadow-lg p-6 mb-8">
                <h2 class="text-xl font-bold theme-aware-text mb-6 flex items-center">
                    <i class="fas fa-envelope text-green-600 mr-3"></i>
                    Email Settings
                </h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="mail_driver" class="block text-sm font-medium theme-aware-text-secondary mb-2">
                            Mail Driver
                        </label>
                        <select id="mail_driver" 
                                name="mail_driver" 
                                class="w-full px-4 py-3 border theme-aware-border rounded-lg focus:ring-2 focus:ring-primary focus:theme-aware-border-focus">
                            <option value="smtp" {{ old('mail_driver', $settings['mail_driver'] ?? 'smtp') === 'smtp' ? 'selected' : '' }}>SMTP</option>
                            <option value="sendmail" {{ old('mail_driver', $settings['mail_driver']) === 'sendmail' ? 'selected' : '' }}>Sendmail</option>
                            <option value="mailgun" {{ old('mail_driver', $settings['mail_driver']) === 'mailgun' ? 'selected' : '' }}>Mailgun</option>
                            <option value="ses" {{ old('mail_driver', $settings['mail_driver']) === 'ses' ? 'selected' : '' }}>Amazon SES</option>
                        </select>
                    </div>

                    <div>
                        <label for="mail_from_address" class="block text-sm font-medium theme-aware-text-secondary mb-2">
                            From Email Address
                        </label>
                        <input type="email" 
                               id="mail_from_address" 
                               name="mail_from_address" 
                               value="{{ old('mail_from_address', $settings['mail_from_address'] ?? 'noreply@siteledger.com') }}"
                               class="w-full px-4 py-3 border theme-aware-border rounded-lg focus:ring-2 focus:ring-primary focus:theme-aware-border-focus">
                    </div>

                    <div>
                        <label for="mail_from_name" class="block text-sm font-medium theme-aware-text-secondary mb-2">
                            From Name
                        </label>
                        <input type="text" 
                               id="mail_from_name" 
                               name="mail_from_name" 
                               value="{{ old('mail_from_name', $settings['mail_from_name'] ?? 'SiteLedger') }}"
                               class="w-full px-4 py-3 border theme-aware-border rounded-lg focus:ring-2 focus:ring-primary focus:theme-aware-border-focus">
                    </div>

                    <div>
                        <label for="smtp_host" class="block text-sm font-medium theme-aware-text-secondary mb-2">
                            SMTP Host
                        </label>
                        <input type="text" 
                               id="smtp_host" 
                               name="smtp_host" 
                               value="{{ old('smtp_host', $settings['smtp_host'] ?? '') }}"
                               class="w-full px-4 py-3 border theme-aware-border rounded-lg focus:ring-2 focus:ring-primary focus:theme-aware-border-focus">
                    </div>

                    <div>
                        <label for="smtp_port" class="block text-sm font-medium theme-aware-text-secondary mb-2">
                            SMTP Port
                        </label>
                        <input type="number" 
                               id="smtp_port" 
                               name="smtp_port" 
                               value="{{ old('smtp_port', $settings['smtp_port'] ?? 587) }}"
                               class="w-full px-4 py-3 border theme-aware-border rounded-lg focus:ring-2 focus:ring-primary focus:theme-aware-border-focus">
                    </div>

                    <div>
                        <label for="smtp_username" class="block text-sm font-medium theme-aware-text-secondary mb-2">
                            SMTP Username
                        </label>
                        <input type="text" 
                               id="smtp_username" 
                               name="smtp_username" 
                               value="{{ old('smtp_username', $settings['smtp_username'] ?? '') }}"
                               class="w-full px-4 py-3 border theme-aware-border rounded-lg focus:ring-2 focus:ring-primary focus:theme-aware-border-focus">
                    </div>

                    <div>
                        <label for="smtp_password" class="block text-sm font-medium theme-aware-text-secondary mb-2">
                            SMTP Password
                        </label>
                        <input type="password" 
                               id="smtp_password" 
                               name="smtp_password" 
                               value="{{ old('smtp_password', $settings['smtp_password'] ?? '') }}"
                               class="w-full px-4 py-3 border theme-aware-border rounded-lg focus:ring-2 focus:ring-primary focus:theme-aware-border-focus">
                    </div>

                    <div>
                        <label for="smtp_encryption" class="block text-sm font-medium theme-aware-text-secondary mb-2">
                            SMTP Encryption
                        </label>
                        <select id="smtp_encryption" 
                                name="smtp_encryption" 
                                class="w-full px-4 py-3 border theme-aware-border rounded-lg focus:ring-2 focus:ring-primary focus:theme-aware-border-focus">
                            <option value="" {{ old('smtp_encryption', $settings['smtp_encryption'] ?? '') === '' ? 'selected' : '' }}>None</option>
                            <option value="tls" {{ old('smtp_encryption', $settings['smtp_encryption']) === 'tls' ? 'selected' : '' }}>TLS</option>
                            <option value="ssl" {{ old('smtp_encryption', $settings['smtp_encryption']) === 'ssl' ? 'selected' : '' }}>SSL</option>
                        </select>
                    </div>

                    <div class="md:col-span-2">
                        <button type="button" 
                                onclick="testEmailSettings()" 
                                class="bg-blue-600 text-white px-6 py-3 rounded-lg font-semibold hover:bg-blue-700 transition">
                            <i class="fas fa-paper-plane mr-2"></i>
                            Test Email Configuration
                        </button>
                    </div>
                </div>
            </div>
        </div>

        {{-- Storage Settings --}}
        <div id="storage-section" class="settings-section hidden">
            <div class="theme-aware-bg-card rounded-xl shadow-lg p-6 mb-8">
                <h2 class="text-xl font-bold theme-aware-text mb-6 flex items-center">
                    <i class="fas fa-database text-purple-600 mr-3"></i>
                    Storage Settings
                </h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="storage_driver" class="block text-sm font-medium theme-aware-text-secondary mb-2">
                            Default Storage Driver
                        </label>
                        <select id="storage_driver" 
                                name="storage_driver" 
                                class="w-full px-4 py-3 border theme-aware-border rounded-lg focus:ring-2 focus:ring-primary focus:theme-aware-border-focus">
                            <option value="local" {{ old('storage_driver', $settings['storage_driver'] ?? 'local') === 'local' ? 'selected' : '' }}>Local</option>
                            <option value="s3" {{ old('storage_driver', $settings['storage_driver']) === 's3' ? 'selected' : '' }}>Amazon S3</option>
                            <option value="gcs" {{ old('storage_driver', $settings['storage_driver']) === 'gcs' ? 'selected' : '' }}>Google Cloud Storage</option>
                        </select>
                    </div>

                    <div>
                        <label for="max_file_size" class="block text-sm font-medium theme-aware-text-secondary mb-2">
                            Max File Upload Size (MB)
                        </label>
                        <input type="number" 
                               id="max_file_size" 
                               name="max_file_size" 
                               value="{{ old('max_file_size', $settings['max_file_size'] ?? 10) }}"
                               min="1"
                               max="100"
                               class="w-full px-4 py-3 border theme-aware-border rounded-lg focus:ring-2 focus:ring-primary focus:theme-aware-border-focus">
                    </div>

                    <div>
                        <label for="backup_retention_days" class="block text-sm font-medium theme-aware-text-secondary mb-2">
                            Backup Retention (days)
                        </label>
                        <input type="number" 
                               id="backup_retention_days" 
                               name="backup_retention_days" 
                               value="{{ old('backup_retention_days', $settings['backup_retention_days'] ?? 30) }}"
                               min="7"
                               max="365"
                               class="w-full px-4 py-3 border theme-aware-border rounded-lg focus:ring-2 focus:ring-primary focus:theme-aware-border-focus">
                    </div>

                    <div>
                        <label for="auto_backup_frequency" class="block text-sm font-medium theme-aware-text-secondary mb-2">
                            Auto Backup Frequency
                        </label>
                        <select id="auto_backup_frequency" 
                                name="auto_backup_frequency" 
                                class="w-full px-4 py-3 border theme-aware-border rounded-lg focus:ring-2 focus:ring-primary focus:theme-aware-border-focus">
                            <option value="daily" {{ old('auto_backup_frequency', $settings['auto_backup_frequency'] ?? 'daily') === 'daily' ? 'selected' : '' }}>Daily</option>
                            <option value="weekly" {{ old('auto_backup_frequency', $settings['auto_backup_frequency']) === 'weekly' ? 'selected' : '' }}>Weekly</option>
                            <option value="monthly" {{ old('auto_backup_frequency', $settings['auto_backup_frequency']) === 'monthly' ? 'selected' : '' }}>Monthly</option>
                            <option value="disabled" {{ old('auto_backup_frequency', $settings['auto_backup_frequency']) === 'disabled' ? 'selected' : '' }}>Disabled</option>
                        </select>
                    </div>

                    <div class="md:col-span-2">
                        <label for="allowed_file_types" class="block text-sm font-medium theme-aware-text-secondary mb-2">
                            Allowed File Types
                        </label>
                        <input type="text" 
                               id="allowed_file_types" 
                               name="allowed_file_types" 
                               value="{{ old('allowed_file_types', $settings['allowed_file_types'] ?? 'jpg,jpeg,png,gif,pdf,doc,docx,xls,xlsx,csv') }}"
                               class="w-full px-4 py-3 border theme-aware-border rounded-lg focus:ring-2 focus:ring-primary focus:theme-aware-border-focus"
                               placeholder="jpg,jpeg,png,gif,pdf,doc,docx">
                        <p class="mt-1 text-xs theme-aware-text-muted">Comma-separated list of allowed file extensions</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- API Settings --}}
        <div id="api-section" class="settings-section hidden">
            <div class="theme-aware-bg-card rounded-xl shadow-lg p-6 mb-8">
                <h2 class="text-xl font-bold theme-aware-text mb-6 flex items-center">
                    <i class="fas fa-code text-orange-600 mr-3"></i>
                    API Settings
                </h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="api_rate_limit" class="block text-sm font-medium theme-aware-text-secondary mb-2">
                            API Rate Limit (per minute)
                        </label>
                        <input type="number" 
                               id="api_rate_limit" 
                               name="api_rate_limit" 
                               value="{{ old('api_rate_limit', $settings['api_rate_limit'] ?? 60) }}"
                               min="10"
                               max="1000"
                               class="w-full px-4 py-3 border theme-aware-border rounded-lg focus:ring-2 focus:ring-primary focus:theme-aware-border-focus">
                    </div>

                    <div>
                        <label for="api_version" class="block text-sm font-medium theme-aware-text-secondary mb-2">
                            Default API Version
                        </label>
                        <select id="api_version" 
                                name="api_version" 
                                class="w-full px-4 py-3 border theme-aware-border rounded-lg focus:ring-2 focus:ring-primary focus:theme-aware-border-focus">
                            <option value="v1" {{ old('api_version', $settings['api_version'] ?? 'v1') === 'v1' ? 'selected' : '' }}>v1</option>
                            <option value="v2" {{ old('api_version', $settings['api_version']) === 'v2' ? 'selected' : '' }}>v2</option>
                        </select>
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium theme-aware-text-secondary mb-3">API Features</label>
                        <div class="space-y-3">
                            <label class="flex items-center">
                                <input type="checkbox" 
                                       name="enable_api" 
                                       value="1"
                                       {{ old('enable_api', $settings['enable_api'] ?? true) ? 'checked' : '' }}
                                       class="rounded theme-aware-border text-blue-600 focus:ring-primary">
                                <span class="ml-2 text-sm theme-aware-text-secondary">Enable API Access</span>
                            </label>

                            <label class="flex items-center">
                                <input type="checkbox" 
                                       name="require_api_authentication" 
                                       value="1"
                                       {{ old('require_api_authentication', $settings['require_api_authentication'] ?? true) ? 'checked' : '' }}
                                       class="rounded theme-aware-border text-blue-600 focus:ring-primary">
                                <span class="ml-2 text-sm theme-aware-text-secondary">Require API Authentication</span>
                            </label>

                            <label class="flex items-center">
                                <input type="checkbox" 
                                       name="enable_api_documentation" 
                                       value="1"
                                       {{ old('enable_api_documentation', $settings['enable_api_documentation'] ?? true) ? 'checked' : '' }}
                                       class="rounded theme-aware-border text-blue-600 focus:ring-primary">
                                <span class="ml-2 text-sm theme-aware-text-secondary">Enable API Documentation</span>
                            </label>

                            <label class="flex items-center">
                                <input type="checkbox" 
                                       name="log_api_requests" 
                                       value="1"
                                       {{ old('log_api_requests', $settings['log_api_requests'] ?? true) ? 'checked' : '' }}
                                       class="rounded theme-aware-border text-blue-600 focus:ring-primary">
                                <span class="ml-2 text-sm theme-aware-text-secondary">Log API Requests</span>
                            </label>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Maintenance Settings --}}
        <div id="maintenance-section" class="settings-section hidden">
            <div class="theme-aware-bg-card rounded-xl shadow-lg p-6 mb-8">
                <h2 class="text-xl font-bold theme-aware-text mb-6 flex items-center">
                    <i class="fas fa-tools text-yellow-600 mr-3"></i>
                    Maintenance Settings
                </h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="log_retention_days" class="block text-sm font-medium theme-aware-text-secondary mb-2">
                            Log Retention (days)
                        </label>
                        <input type="number" 
                               id="log_retention_days" 
                               name="log_retention_days" 
                               value="{{ old('log_retention_days', $settings['log_retention_days'] ?? 30) }}"
                               min="7"
                               max="365"
                               class="w-full px-4 py-3 border theme-aware-border rounded-lg focus:ring-2 focus:ring-primary focus:theme-aware-border-focus">
                    </div>

                    <div>
                        <label for="cache_driver" class="block text-sm font-medium theme-aware-text-secondary mb-2">
                            Cache Driver
                        </label>
                        <select id="cache_driver" 
                                name="cache_driver" 
                                class="w-full px-4 py-3 border theme-aware-border rounded-lg focus:ring-2 focus:ring-primary focus:theme-aware-border-focus">
                            <option value="file" {{ old('cache_driver', $settings['cache_driver'] ?? 'file') === 'file' ? 'selected' : '' }}>File</option>
                            <option value="redis" {{ old('cache_driver', $settings['cache_driver']) === 'redis' ? 'selected' : '' }}>Redis</option>
                            <option value="memcached" {{ old('cache_driver', $settings['cache_driver']) === 'memcached' ? 'selected' : '' }}>Memcached</option>
                        </select>
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium theme-aware-text-secondary mb-3">Maintenance Features</label>
                        <div class="space-y-3">
                            <label class="flex items-center">
                                <input type="checkbox" 
                                       name="enable_debug_mode" 
                                       value="1"
                                       {{ old('enable_debug_mode', $settings['enable_debug_mode'] ?? false) ? 'checked' : '' }}
                                       class="rounded theme-aware-border text-blue-600 focus:ring-primary">
                                <span class="ml-2 text-sm theme-aware-text-secondary">Enable Debug Mode</span>
                            </label>

                            <label class="flex items-center">
                                <input type="checkbox" 
                                       name="auto_cleanup_logs" 
                                       value="1"
                                       {{ old('auto_cleanup_logs', $settings['auto_cleanup_logs'] ?? true) ? 'checked' : '' }}
                                       class="rounded theme-aware-border text-blue-600 focus:ring-primary">
                                <span class="ml-2 text-sm theme-aware-text-secondary">Auto Cleanup Old Logs</span>
                            </label>

                            <label class="flex items-center">
                                <input type="checkbox" 
                                       name="enable_performance_monitoring" 
                                       value="1"
                                       {{ old('enable_performance_monitoring', $settings['enable_performance_monitoring'] ?? true) ? 'checked' : '' }}
                                       class="rounded theme-aware-border text-blue-600 focus:ring-primary">
                                <span class="ml-2 text-sm theme-aware-text-secondary">Enable Performance Monitoring</span>
                            </label>
                        </div>
                    </div>

                    <div class="md:col-span-2">
                        <h3 class="text-lg font-semibold theme-aware-text mb-4">System Maintenance Actions</h3>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <button type="button" 
                                    onclick="clearCache()" 
                                    class="bg-blue-600 text-white py-2 px-4 rounded-lg font-semibold hover:bg-blue-700 transition">
                                <i class="fas fa-broom mr-2"></i>
                                Clear Cache
                            </button>
                            
                            <button type="button" 
                                    onclick="clearLogs()" 
                                    class="bg-yellow-600 text-white py-2 px-4 rounded-lg font-semibold hover:bg-yellow-700 transition">
                                <i class="fas fa-trash mr-2"></i>
                                Clear Logs
                            </button>
                            
                            <button type="button" 
                                    onclick="optimizeDatabase()" 
                                    class="bg-green-600 text-white py-2 px-4 rounded-lg font-semibold hover:bg-green-700 transition">
                                <i class="fas fa-database mr-2"></i>
                                Optimize DB
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Save Button --}}
        <div class="theme-aware-bg-card rounded-xl shadow-lg p-6">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-semibold theme-aware-text">Save Changes</h3>
                    <p class="text-sm theme-aware-text-secondary">Make sure to save your changes before leaving this page.</p>
                </div>
                <div class="flex space-x-3">
                    <button type="button" 
                            onclick="resetForm()" 
                            class="bg-gray-300 theme-aware-text-secondary px-6 py-3 rounded-lg font-semibold hover:bg-gray-400 transition">
                        Reset
                    </button>
                    <button type="submit" 
                            class="bg-gradient-to-r from-blue-600 to-purple-600 text-white px-8 py-3 rounded-lg font-semibold hover:from-blue-700 hover:to-purple-700 transition">
                        <i class="fas fa-save mr-2"></i>
                        Save Settings
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
function showSection(sectionName) {
    // Hide all sections
    document.querySelectorAll('.settings-section').forEach(section => {
        section.classList.add('hidden');
    });
    
    // Show selected section
    document.getElementById(sectionName + '-section').classList.remove('hidden');
    
    // Update navigation
    document.querySelectorAll('.settings-nav-btn').forEach(btn => {
        btn.classList.remove('active', 'border-blue-500', 'text-blue-600');
        btn.classList.add('border-transparent', 'theme-aware-text-muted');
    });
    
    const activeBtn = document.querySelector(`[data-section="${sectionName}"]`);
    activeBtn.classList.add('active', 'border-blue-500', 'text-blue-600');
    activeBtn.classList.remove('border-transparent', 'theme-aware-text-muted');
}

function testEmailSettings() {
    const button = event.target;
    button.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Testing...';
    button.disabled = true;
    
    const formData = new FormData(document.getElementById('settings-form'));
    
    fetch('/admin/settings/test-email', {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Test email sent successfully!');
        } else {
            alert('Failed to send test email: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while testing email settings.');
    })
    .finally(() => {
        button.innerHTML = '<i class="fas fa-paper-plane mr-2"></i>Test Email Configuration';
        button.disabled = false;
    });
}

function clearCache() {
    if (confirm('Clear all application cache? This may temporarily slow down the application.')) {
        performMaintenanceAction('clear-cache');
    }
}

function clearLogs() {
    if (confirm('Clear all application logs? This action cannot be undone.')) {
        performMaintenanceAction('clear-logs');
    }
}

function optimizeDatabase() {
    if (confirm('Optimize database tables? This may take a few minutes.')) {
        performMaintenanceAction('optimize-database');
    }
}

function performMaintenanceAction(action) {
    const button = event.target;
    const originalText = button.innerHTML;
    button.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Processing...';
    button.disabled = true;
    
    fetch(`/admin/settings/maintenance/${action}`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(data.message || 'Action completed successfully!');
        } else {
            alert('Action failed: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while performing the maintenance action.');
    })
    .finally(() => {
        button.innerHTML = originalText;
        button.disabled = false;
    });
}

function exportSettings() {
    window.open('/admin/settings/export', '_blank');
}

function importSettings() {
    const input = document.createElement('input');
    input.type = 'file';
    input.accept = '.json';
    input.onchange = function(e) {
        const file = e.target.files[0];
        if (!file) return;
        
        const formData = new FormData();
        formData.append('settings_file', file);
        
        fetch('/admin/settings/import', {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Settings imported successfully!');
                location.reload();
            } else {
                alert('Failed to import settings: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while importing settings.');
        });
    };
    input.click();
}

function resetForm() {
    if (confirm('Reset all settings to their default values? Unsaved changes will be lost.')) {
        document.getElementById('settings-form').reset();
    }
}

// Form submission handling
document.getElementById('settings-form').addEventListener('submit', function(e) {
    const submitBtn = this.querySelector('button[type="submit"]');
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Saving...';
    submitBtn.disabled = true;
});

// Auto-save functionality
let autoSaveTimer;
document.querySelectorAll('input, select, textarea').forEach(field => {
    field.addEventListener('change', function() {
        clearTimeout(autoSaveTimer);
        autoSaveTimer = setTimeout(() => {
            console.log('Auto-saving settings...');
            // Auto-save functionality could be implemented here
        }, 30000);
    });
});
</script>
@endsection