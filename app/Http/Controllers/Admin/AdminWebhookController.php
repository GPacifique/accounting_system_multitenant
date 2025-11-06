<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class AdminWebhookController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:super-admin');
    }

    /**
     * Display webhook management dashboard.
     */
    public function index()
    {
        $webhooks = $this->getWebhooks();
        $stats = [
            'total_webhooks' => count($webhooks),
            'active_webhooks' => count(array_filter($webhooks, fn($w) => $w['status'] === 'active')),
            'deliveries_today' => $this->getDeliveriesToday(),
            'failed_deliveries' => $this->getFailedDeliveries(),
        ];

        $recentDeliveries = $this->getRecentDeliveries();

        return view('admin.webhooks.index', compact('webhooks', 'stats', 'recentDeliveries'));
    }

    /**
     * Create new webhook.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'url' => 'required|url',
            'events' => 'required|array|min:1',
            'secret' => 'nullable|string|max:255',
            'active' => 'boolean',
        ]);

        $webhook = [
            'id' => Str::uuid(),
            'name' => $request->name,
            'url' => $request->url,
            'events' => $request->events,
            'secret' => $request->secret ?? Str::random(32),
            'status' => $request->boolean('active') ? 'active' : 'inactive',
            'created_at' => now(),
            'last_delivery_at' => null,
            'failure_count' => 0,
        ];

        $this->storeWebhook($webhook);

        return back()->with('success', 'Webhook created successfully.');
    }

    /**
     * Update webhook.
     */
    public function update(Request $request, $webhookId)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'url' => 'required|url',
            'events' => 'required|array|min:1',
            'secret' => 'nullable|string|max:255',
            'active' => 'boolean',
        ]);

        $webhooks = $this->getWebhooks();
        $index = array_search($webhookId, array_column($webhooks, 'id'));

        if ($index === false) {
            return back()->with('error', 'Webhook not found.');
        }

        $webhooks[$index] = array_merge($webhooks[$index], [
            'name' => $request->name,
            'url' => $request->url,
            'events' => $request->events,
            'secret' => $request->secret ?? $webhooks[$index]['secret'],
            'status' => $request->boolean('active') ? 'active' : 'inactive',
            'updated_at' => now(),
        ]);

        $this->storeWebhooks($webhooks);

        return back()->with('success', 'Webhook updated successfully.');
    }

    /**
     * Delete webhook.
     */
    public function destroy($webhookId)
    {
        $webhooks = $this->getWebhooks();
        $index = array_search($webhookId, array_column($webhooks, 'id'));

        if ($index === false) {
            return back()->with('error', 'Webhook not found.');
        }

        array_splice($webhooks, $index, 1);
        $this->storeWebhooks($webhooks);

        return back()->with('success', 'Webhook deleted successfully.');
    }

    /**
     * Test webhook delivery.
     */
    public function test($webhookId)
    {
        $webhooks = $this->getWebhooks();
        $webhook = collect($webhooks)->firstWhere('id', $webhookId);

        if (!$webhook) {
            return back()->with('error', 'Webhook not found.');
        }

        try {
            $this->deliverWebhook($webhook, [
                'event' => 'webhook.test',
                'data' => ['message' => 'This is a test webhook delivery'],
                'timestamp' => now()->toISOString(),
            ]);

            return back()->with('success', 'Test webhook delivered successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Webhook delivery failed: ' . $e->getMessage());
        }
    }

    /**
     * Show webhook delivery logs.
     */
    public function logs($webhookId)
    {
        $webhook = collect($this->getWebhooks())->firstWhere('id', $webhookId);
        
        if (!$webhook) {
            abort(404, 'Webhook not found');
        }

        $logs = $this->getWebhookLogs($webhookId);

        return view('admin.webhooks.logs', compact('webhook', 'logs'));
    }

    /**
     * Get available webhook events.
     */
    public function events()
    {
        $events = [
            'tenant.created' => 'Tenant Created',
            'tenant.updated' => 'Tenant Updated',
            'tenant.deleted' => 'Tenant Deleted',
            'user.created' => 'User Created',
            'user.updated' => 'User Updated',
            'user.deleted' => 'User Deleted',
            'subscription.created' => 'Subscription Created',
            'subscription.updated' => 'Subscription Updated',
            'subscription.cancelled' => 'Subscription Cancelled',
            'payment.successful' => 'Payment Successful',
            'payment.failed' => 'Payment Failed',
        ];

        return response()->json($events);
    }

    /**
     * Get stored webhooks.
     */
    protected function getWebhooks()
    {
        return Cache::get('webhooks', []);
    }

    /**
     * Store single webhook.
     */
    protected function storeWebhook($webhook)
    {
        $webhooks = $this->getWebhooks();
        $webhooks[] = $webhook;
        $this->storeWebhooks($webhooks);
    }

    /**
     * Store webhooks array.
     */
    protected function storeWebhooks($webhooks)
    {
        Cache::put('webhooks', $webhooks, now()->addDays(30));
    }

    /**
     * Get deliveries made today.
     */
    protected function getDeliveriesToday()
    {
        return Cache::get('webhook_deliveries_today', 0);
    }

    /**
     * Get failed deliveries.
     */
    protected function getFailedDeliveries()
    {
        return Cache::get('webhook_failed_deliveries', 0);
    }

    /**
     * Get recent webhook deliveries.
     */
    protected function getRecentDeliveries()
    {
        return Cache::get('recent_webhook_deliveries', []);
    }

    /**
     * Get webhook delivery logs.
     */
    protected function getWebhookLogs($webhookId)
    {
        return Cache::get("webhook_logs_{$webhookId}", []);
    }

    /**
     * Deliver webhook (simplified version).
     */
    protected function deliverWebhook($webhook, $payload)
    {
        $headers = [
            'Content-Type: application/json',
            'User-Agent: AccountingSystem-Webhook/1.0',
        ];

        if (!empty($webhook['secret'])) {
            $signature = hash_hmac('sha256', json_encode($payload), $webhook['secret']);
            $headers[] = 'X-Webhook-Signature: sha256=' . $signature;
        }

        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => $webhook['url'],
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => json_encode($payload),
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_FOLLOWLOCATION => true,
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode < 200 || $httpCode >= 300) {
            throw new \Exception("HTTP {$httpCode}: {$response}");
        }

        // Log successful delivery
        $this->logWebhookDelivery($webhook['id'], $payload, $httpCode, $response);

        return $response;
    }

    /**
     * Log webhook delivery.
     */
    protected function logWebhookDelivery($webhookId, $payload, $httpCode, $response)
    {
        $logs = $this->getWebhookLogs($webhookId);
        
        $logs[] = [
            'id' => Str::uuid(),
            'delivered_at' => now(),
            'payload' => $payload,
            'http_code' => $httpCode,
            'response' => $response,
            'success' => $httpCode >= 200 && $httpCode < 300,
        ];

        // Keep only last 100 logs
        if (count($logs) > 100) {
            $logs = array_slice($logs, -100);
        }

        Cache::put("webhook_logs_{$webhookId}", $logs, now()->addDays(7));
    }
}