# Multi-Tenant Architecture Implementation Guide

## Overview

This document provides a comprehensive guide to the multi-tenant system implementation for our accounting application. The system is designed to serve multiple businesses (tenants) from a single application instance while ensuring complete data isolation and providing business administrator capabilities.

## Architecture Overview

### Multi-Tenancy Strategy

We use a **hybrid multi-tenancy approach** that combines the best of both worlds:

1. **Shared Database with Tenant Scoping** (Primary)
   - Single database with `tenant_id` columns
   - Automatic query scoping via global scopes
   - Cost-effective for smaller tenants

2. **Separate Database per Tenant** (For Large Tenants)
   - Dedicated databases for high-volume tenants
   - Better performance isolation
   - Enhanced security

### Key Components

1. **Tenant Resolution Middleware** - Identifies tenant from request
2. **Database Scoping Middleware** - Enforces tenant data isolation
3. **Security Middleware** - Rate limiting and access control
4. **Business Admin System** - Granular permission management
5. **User Invitation System** - Secure tenant onboarding

## Database Schema

### Core Tables

#### Enhanced `tenants` Table
```sql
-- Contact and Business Information
contact_email VARCHAR(255) NOT NULL
contact_phone VARCHAR(20)
registration_number VARCHAR(100)
description TEXT
logo_path VARCHAR(255)

-- User Management
max_users INT DEFAULT 10
trial_ends_at TIMESTAMP NULL

-- Features and Configuration
features JSON DEFAULT '{}'
timezone VARCHAR(50) DEFAULT 'UTC'
currency CHAR(3) DEFAULT 'USD'
locale CHAR(2) DEFAULT 'en'

-- Security Settings
enforce_2fa BOOLEAN DEFAULT FALSE
session_timeout INT DEFAULT 7200
last_backup_at TIMESTAMP NULL
```

#### User Invitation System
```sql
CREATE TABLE user_invitations (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    tenant_id BIGINT NOT NULL,
    invited_by BIGINT NOT NULL,
    email VARCHAR(255) NOT NULL,
    first_name VARCHAR(255) NOT NULL,
    last_name VARCHAR(255) NOT NULL,
    role VARCHAR(50) NOT NULL,
    department VARCHAR(100),
    token VARCHAR(255) UNIQUE NOT NULL,
    permissions JSON,
    status ENUM('pending', 'accepted', 'expired', 'cancelled') DEFAULT 'pending',
    expires_at TIMESTAMP NOT NULL,
    accepted_at TIMESTAMP NULL,
    accepted_by BIGINT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

#### Business Admin Permissions
```sql
CREATE TABLE business_admin_permissions (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    user_id BIGINT NOT NULL,
    tenant_id BIGINT NOT NULL,
    permission VARCHAR(100) NOT NULL,
    constraints JSON DEFAULT '{}',
    granted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    expires_at TIMESTAMP NULL
);
```

### Available Permissions

The system supports 12 granular business admin permissions:

1. **invite_users** - Send user invitations
2. **manage_users** - Activate/deactivate users
3. **assign_roles** - Change user roles
4. **view_reports** - Access reporting features
5. **manage_settings** - Modify tenant configuration
6. **manage_billing** - Handle billing and subscriptions
7. **export_data** - Export business data
8. **manage_integrations** - Configure third-party integrations
9. **view_audit_logs** - Access security audit logs
10. **manage_permissions** - Grant/revoke admin permissions
11. **backup_data** - Perform data backups
12. **manage_api_keys** - Handle API access tokens

## Implementation Details

### 1. Tenant Resolution

The system resolves tenants using multiple strategies:

```php
// Strategy 1: Subdomain (primary)
acme.siteledger.com → tenant: acme

// Strategy 2: API Header
X-Tenant-ID: 123
X-Tenant-Domain: acme

// Strategy 3: JWT Token Claims
Bearer token with tenant_id claim

// Strategy 4: Query Parameter (development)
?tenant_id=123
```

### 2. Database Scoping

Automatic tenant scoping is applied to all models using the `BelongsToTenant` trait:

```php
// All queries automatically include tenant_id
User::all(); // SELECT * FROM users WHERE tenant_id = ?

// Manual scoping bypass (admin only)
User::withoutGlobalScope('tenant')->get();
```

### 3. Security Features

#### Rate Limiting
- **Default**: 1000 requests/hour
- **Auth**: 10 login attempts/15 minutes
- **API**: 100 calls/hour
- **Admin**: 200 actions/hour

#### Suspicious Activity Detection
- Multiple failed logins
- Rapid-fire requests (>100/minute)
- Cross-tenant access attempts

#### Session Management
- Configurable concurrent session limits
- Automatic session cleanup
- Activity tracking

## API Endpoints

### Tenant Management

```http
POST /api/v1/tenants
GET /api/v1/tenant
PUT /api/v1/tenant
GET /api/v1/tenant/statistics
```

### Business Admin Operations

```http
# User Management
GET /api/v1/admin/users
PUT /api/v1/admin/users/{id}/role
POST /api/v1/admin/users/{id}/deactivate
POST /api/v1/admin/users/{id}/permissions

# User Invitations
POST /api/v1/admin/invitations
GET /api/v1/admin/invitations
DELETE /api/v1/admin/invitations/{id}
```

### Public Endpoints

```http
POST /api/v1/invitations/{token}/accept
GET /api/invitations/{token}
```

## Middleware Stack

The multi-tenant system uses a layered middleware approach:

```php
Route::middleware([
    'resolve.tenant',      // Identify tenant
    'tenant.scope',        // Apply database scoping
    'tenant.security:api', // Rate limiting & security
    'auth:api'            // Authentication
])->group(function () {
    // Tenant-scoped routes
});
```

## Security Considerations

### Data Isolation

1. **Global Query Scoping** - Automatic tenant_id filtering
2. **Middleware Validation** - User-tenant relationship verification
3. **Database Constraints** - Foreign key enforcement
4. **Audit Logging** - Complete activity tracking

### Cross-Tenant Access Prevention

1. **Tenant Context Validation** - Every request validates tenant access
2. **Model-Level Scoping** - Global scopes prevent data leakage
3. **Permission Boundaries** - Business admin permissions are tenant-scoped
4. **API Security** - Rate limiting and monitoring

### Audit and Compliance

```php
// All significant actions are logged
AuditLog::create([
    'tenant_id' => $tenant->id,
    'user_id' => $user->id,
    'action' => 'user_invited',
    'description' => 'Invited user: user@example.com',
    'metadata' => [...],
    'severity' => 'low|medium|high',
    'ip_address' => $request->ip(),
    'user_agent' => $request->userAgent(),
]);
```

## Business Admin Capabilities

### User Management

Business admins can:
- Send user invitations with role assignment
- Activate/deactivate user accounts
- Modify user roles within their tenant
- View user activity and statistics

### Permission Management

Granular permission system allows:
- Role-based access control
- Constraint-based permissions
- Temporary permission grants
- Permission audit trails

### Tenant Administration

Business admins can manage:
- Tenant settings and configuration
- Billing and subscription information
- Data exports and backups
- Integration configurations

## Testing Strategy

### Unit Tests

1. **Model Tests** - Tenant scoping and relationships
2. **Middleware Tests** - Resolution and security
3. **Permission Tests** - Business admin capabilities
4. **API Tests** - Endpoint functionality

### Integration Tests

1. **Cross-Tenant Isolation** - Ensure no data leakage
2. **User Invitation Flow** - Complete invitation process
3. **Permission Enforcement** - Business admin boundaries
4. **Security Tests** - Rate limiting and access control

### Example Test Cases

```php
// Test tenant isolation
public function test_users_cannot_access_other_tenant_data()
{
    $tenant1 = Tenant::factory()->create();
    $tenant2 = Tenant::factory()->create();
    
    $user1 = User::factory()->create();
    $user1->tenants()->attach($tenant1);
    
    $this->actingAs($user1)
         ->get("/api/v1/users")
         ->assertJsonMissing(['tenant_id' => $tenant2->id]);
}
```

## Performance Optimization

### Database Optimization

1. **Indexing Strategy**
   ```sql
   CREATE INDEX idx_tenant_id ON users (tenant_id);
   CREATE INDEX idx_tenant_user ON tenant_users (tenant_id, user_id);
   ```

2. **Query Optimization**
   - Eager loading tenant relationships
   - Efficient pagination
   - Selective field retrieval

### Caching Strategy

1. **Tenant Resolution Caching** - Cache tenant lookups
2. **Permission Caching** - Cache business admin permissions
3. **Rate Limit Caching** - Redis-based rate limiting
4. **Session Caching** - Efficient session management

## Deployment Considerations

### Environment Configuration

```env
# Multi-tenancy settings
MULTITENANCY_DEFAULT_USER_LIMIT=10
MULTITENANCY_ENABLE_SEPARATE_DATABASES=true
MULTITENANCY_DOMAIN_SUFFIX=.siteledger.com

# Security settings
RATE_LIMIT_ENABLED=true
AUDIT_LOG_RETENTION_DAYS=365
SESSION_TIMEOUT_MINUTES=120
```

### Migration Strategy

1. **Landlord Migrations** - System-wide tables
2. **Tenant Migrations** - Per-tenant schema changes
3. **Data Migration** - Existing data transformation
4. **Rollback Procedures** - Safe deployment practices

## Monitoring and Maintenance

### Key Metrics

1. **Tenant Health**
   - Active tenant count
   - User distribution
   - Storage utilization

2. **Security Metrics**
   - Failed login attempts
   - Rate limit violations
   - Cross-tenant access attempts

3. **Performance Metrics**
   - Response times per tenant
   - Database query performance
   - Cache hit rates

### Maintenance Tasks

1. **Regular Backups** - Per-tenant backup procedures
2. **Log Rotation** - Audit log management
3. **Cache Cleanup** - Session and rate limit cleanup
4. **Security Reviews** - Permission audit procedures

## Troubleshooting Guide

### Common Issues

1. **Tenant Not Found**
   - Check subdomain configuration
   - Verify tenant status
   - Review middleware order

2. **Permission Denied**
   - Validate user-tenant relationship
   - Check business admin permissions
   - Review role assignments

3. **Data Isolation Issues**
   - Verify global scope application
   - Check tenant_id presence
   - Review query logs

### Debug Tools

```php
// Enable query logging
DB::enableQueryLog();

// Check current tenant
$tenant = app('currentTenant');

// Verify user permissions
$user->getBusinessPermissions($tenantId);
```

## Future Enhancements

### Planned Features

1. **Advanced Analytics** - Per-tenant usage analytics
2. **API Gateway** - Centralized API management
3. **Backup Automation** - Automated backup scheduling
4. **SSO Integration** - Single sign-on capabilities
5. **Mobile API** - Dedicated mobile endpoints

### Scalability Improvements

1. **Database Sharding** - Horizontal scaling strategy
2. **Microservices** - Service decomposition
3. **CDN Integration** - Static asset optimization
4. **Load Balancing** - Multi-instance deployment

This implementation provides a robust, secure, and scalable multi-tenant foundation that can grow with your business needs while maintaining strict data isolation and providing comprehensive business administration capabilities.