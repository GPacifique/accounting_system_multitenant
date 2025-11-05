<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Mail\WelcomeUserMail;
use App\Mail\AdminNewUserNotification;
use App\Notifications\UserRegistered;
use Illuminate\Support\Facades\Mail;

class TestEmailNotifications extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'email:test {type=welcome} {--email=} {--preview}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test email notifications (welcome, admin-notification)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $type = $this->argument('type');
        $email = $this->option('email');
        $preview = $this->option('preview');

        // Get or create a test user
        $testUser = User::first() ?? User::factory()->create([
            'name' => 'Test User',
            'email' => $email ?: 'test@example.com',
            'role' => 'user'
        ]);

        $this->info("🧪 Testing {$type} email notification");
        $this->line("👤 Test user: {$testUser->name} ({$testUser->email})");
        $this->newLine();

        switch ($type) {
            case 'welcome':
                return $this->testWelcomeEmail($testUser, $preview);
                
            case 'admin-notification':
                return $this->testAdminNotification($testUser, $preview);
                
            case 'both':
                $this->testWelcomeEmail($testUser, $preview);
                $this->newLine();
                return $this->testAdminNotification($testUser, $preview);
                
            default:
                $this->error("Unknown email type: {$type}");
                $this->line("Available types: welcome, admin-notification, both");
                return 1;
        }
    }

    private function testWelcomeEmail(User $user, bool $preview = false)
    {
        $this->info("📧 Testing Welcome Email...");
        
        if ($preview) {
            $mailable = new WelcomeUserMail($user);
            $this->line("Subject: " . $mailable->envelope()->subject);
            $this->line("To: {$user->email}");
            $this->info("✅ Welcome email preview generated (check logs)");
        } else {
            try {
                Mail::to($user)->send(new WelcomeUserMail($user));
                $this->info("✅ Welcome email sent successfully!");
            } catch (\Exception $e) {
                $this->error("❌ Failed to send welcome email: " . $e->getMessage());
                return 1;
            }
        }
        
        return 0;
    }

    private function testAdminNotification(User $newUser, bool $preview = false)
    {
        $this->info("👮 Testing Admin Notification...");
        
        // Get admin users or create a test admin
        $adminUsers = User::role('admin')->get();
        if ($adminUsers->isEmpty()) {
            $this->warn("No admin users found. Creating test admin...");
            $adminUser = User::factory()->create([
                'name' => 'Test Admin',
                'email' => 'admin@example.com',
                'role' => 'admin'
            ]);
            $adminUser->assignRole('admin');
            $adminUsers = collect([$adminUser]);
        }

        if ($preview) {
            $mailable = new AdminNewUserNotification($newUser);
            $this->line("Subject: " . $mailable->envelope()->subject);
            $this->line("To: " . $adminUsers->pluck('email')->join(', '));
            $this->info("✅ Admin notification preview generated (check logs)");
        } else {
            try {
                foreach ($adminUsers as $admin) {
                    Mail::to($admin)->send(new AdminNewUserNotification($newUser));
                }
                $this->info("✅ Admin notification sent to {$adminUsers->count()} admin(s)!");
            } catch (\Exception $e) {
                $this->error("❌ Failed to send admin notification: " . $e->getMessage());
                return 1;
            }
        }
        
        return 0;
    }
}
