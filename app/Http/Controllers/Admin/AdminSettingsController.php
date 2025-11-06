<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Mail;

class AdminSettingsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:super-admin');
    }

    /**
     * Display system settings.
     */
    public function index()
    {
        $settings = [
            'app' => [
                'name' => config('app.name'),
                'url' => config('app.url'),
                'env' => config('app.env'),
                'debug' => config('app.debug'),
                'timezone' => config('app.timezone'),
            ],
            'mail' => [
                'driver' => config('mail.default'),
                'host' => config('mail.mailers.smtp.host'),
                'port' => config('mail.mailers.smtp.port'),
                'encryption' => config('mail.mailers.smtp.encryption'),
                'from_address' => config('mail.from.address'),
                'from_name' => config('mail.from.name'),
            ],
            'database' => [
                'connection' => config('database.default'),
                'host' => config('database.connections.mysql.host'),
                'database' => config('database.connections.mysql.database'),
                'username' => config('database.connections.mysql.username'),
            ],
            'cache' => [
                'driver' => config('cache.default'),
                'prefix' => config('cache.prefix'),
            ],
            'session' => [
                'driver' => config('session.driver'),
                'lifetime' => config('session.lifetime'),
                'secure' => config('session.secure'),
            ],
            'queue' => [
                'driver' => config('queue.default'),
                'connection' => config('queue.connections.database.connection') ?? 'default',
            ],
        ];

        $systemInfo = [
            'php_version' => PHP_VERSION,
            'laravel_version' => app()->version(),
            'server_software' => $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown',
            'memory_limit' => ini_get('memory_limit'),
            'max_execution_time' => ini_get('max_execution_time'),
            'upload_max_filesize' => ini_get('upload_max_filesize'),
            'post_max_size' => ini_get('post_max_size'),
        ];

        return view('admin.settings.index', compact('settings', 'systemInfo'));
    }

    /**
     * Update application settings.
     */
    public function updateApp(Request $request)
    {
        $request->validate([
            'app_name' => 'required|string|max:255',
            'app_url' => 'required|url',
            'app_timezone' => 'required|string',
            'app_debug' => 'boolean',
        ]);

        $this->updateEnvFile([
            'APP_NAME' => '"' . $request->app_name . '"',
            'APP_URL' => $request->app_url,
            'APP_TIMEZONE' => $request->app_timezone,
            'APP_DEBUG' => $request->app_debug ? 'true' : 'false',
        ]);

        // Clear config cache
        Artisan::call('config:clear');

        return back()->with('success', 'Application settings updated successfully.');
    }

    /**
     * Update mail settings.
     */
    public function updateMail(Request $request)
    {
        $request->validate([
            'mail_driver' => 'required|string',
            'mail_host' => 'required|string',
            'mail_port' => 'required|integer',
            'mail_username' => 'nullable|string',
            'mail_password' => 'nullable|string',
            'mail_encryption' => 'nullable|string',
            'mail_from_address' => 'required|email',
            'mail_from_name' => 'required|string',
        ]);

        $envUpdates = [
            'MAIL_MAILER' => $request->mail_driver,
            'MAIL_HOST' => $request->mail_host,
            'MAIL_PORT' => $request->mail_port,
            'MAIL_FROM_ADDRESS' => $request->mail_from_address,
            'MAIL_FROM_NAME' => '"' . $request->mail_from_name . '"',
        ];

        if ($request->filled('mail_username')) {
            $envUpdates['MAIL_USERNAME'] = $request->mail_username;
        }

        if ($request->filled('mail_password')) {
            $envUpdates['MAIL_PASSWORD'] = $request->mail_password;
        }

        if ($request->filled('mail_encryption')) {
            $envUpdates['MAIL_ENCRYPTION'] = $request->mail_encryption;
        }

        $this->updateEnvFile($envUpdates);

        // Clear config cache
        Artisan::call('config:clear');

        return back()->with('success', 'Mail settings updated successfully.');
    }

    /**
     * Update cache settings.
     */
    public function updateCache(Request $request)
    {
        $request->validate([
            'cache_driver' => 'required|string',
            'cache_prefix' => 'nullable|string',
        ]);

        $this->updateEnvFile([
            'CACHE_DRIVER' => $request->cache_driver,
            'CACHE_PREFIX' => $request->cache_prefix ?? '',
        ]);

        // Clear all caches
        Artisan::call('cache:clear');
        Artisan::call('config:clear');
        Artisan::call('view:clear');

        return back()->with('success', 'Cache settings updated successfully.');
    }

    /**
     * Test mail configuration.
     */
    public function testMail(Request $request)
    {
        $request->validate([
            'test_email' => 'required|email',
        ]);

        try {
            Mail::raw('This is a test email from your application.', function ($message) use ($request) {
                $message->to($request->test_email)
                        ->subject('Test Email - ' . config('app.name'));
            });

            return back()->with('success', 'Test email sent successfully to ' . $request->test_email);
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to send test email: ' . $e->getMessage());
        }
    }

    /**
     * Clear all caches.
     */
    public function clearCache()
    {
        Artisan::call('cache:clear');
        Artisan::call('config:clear');
        Artisan::call('view:clear');
        Artisan::call('route:clear');

        return back()->with('success', 'All caches cleared successfully.');
    }

    /**
     * Show maintenance mode settings.
     */
    public function maintenance()
    {
        $isDown = app()->isDownForMaintenance();
        
        return view('admin.settings.maintenance', compact('isDown'));
    }

    /**
     * Enable maintenance mode.
     */
    public function enableMaintenance(Request $request)
    {
        $request->validate([
            'message' => 'nullable|string|max:255',
            'allowed_ips' => 'nullable|string',
        ]);

        $options = [];
        
        if ($request->filled('message')) {
            $options['message'] = $request->message;
        }

        if ($request->filled('allowed_ips')) {
            $ips = array_map('trim', explode(',', $request->allowed_ips));
            $options['allow'] = $ips;
        }

        Artisan::call('down', $options);

        return back()->with('success', 'Maintenance mode enabled.');
    }

    /**
     * Disable maintenance mode.
     */
    public function disableMaintenance()
    {
        Artisan::call('up');

        return back()->with('success', 'Maintenance mode disabled.');
    }

    /**
     * Show optimization tools.
     */
    public function optimization()
    {
        return view('admin.settings.optimization');
    }

    /**
     * Optimize application.
     */
    public function optimize()
    {
        Artisan::call('optimize');
        
        return back()->with('success', 'Application optimized successfully.');
    }

    /**
     * Clear optimization.
     */
    public function clearOptimization()
    {
        Artisan::call('optimize:clear');
        
        return back()->with('success', 'Optimization cleared successfully.');
    }

    /**
     * Update environment file.
     */
    protected function updateEnvFile(array $data)
    {
        $envFile = base_path('.env');
        $envContent = file_get_contents($envFile);

        foreach ($data as $key => $value) {
            $pattern = "/^{$key}=.*/m";
            $replacement = "{$key}={$value}";
            
            if (preg_match($pattern, $envContent)) {
                $envContent = preg_replace($pattern, $replacement, $envContent);
            } else {
                $envContent .= "\n{$replacement}";
            }
        }

        file_put_contents($envFile, $envContent);
    }
}