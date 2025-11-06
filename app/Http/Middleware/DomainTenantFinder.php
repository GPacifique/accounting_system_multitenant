<?php

namespace App\Http\Middleware;

use Spatie\Multitenancy\TenantFinder\TenantFinder;
use Spatie\Multitenancy\Contracts\IsTenant;
use Illuminate\Http\Request;
use App\Models\Tenant;

class DomainTenantFinder extends TenantFinder
{
    public function findForRequest(Request $request): ?\Spatie\Multitenancy\Contracts\IsTenant
    {
        $host = $request->getHost();
        
        // Extract subdomain from host
        $subdomain = $this->extractSubdomain($host);
        
        if (!$subdomain) {
            return null;
        }

        // Find tenant by domain/subdomain
        return Tenant::where('domain', $subdomain)->first();
    }

    protected function extractSubdomain(string $host): ?string
    {
        // Remove common local development domains
        $localDomains = ['localhost', '127.0.0.1'];
        
        if (in_array($host, $localDomains)) {
            return null;
        }

        // Handle development with port numbers
        if (str_contains($host, ':')) {
            $host = explode(':', $host)[0];
        }

        // For development, you might want to use a different pattern
        // Example: tenant1.yourdomain.test, tenant2.yourdomain.test
        $parts = explode('.', $host);
        
        // If we have at least 3 parts (subdomain.domain.tld), return the first part as subdomain
        if (count($parts) >= 3) {
            return $parts[0];
        }

        // For development, you might want to handle it differently
        // For example, if using domain like "tenant1-yourdomain.test"
        if (count($parts) === 2 && str_contains($parts[0], '-')) {
            $subdomainParts = explode('-', $parts[0]);
            return $subdomainParts[0];
        }

        return null;
    }
}