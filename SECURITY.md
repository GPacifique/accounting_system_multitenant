# Security Hardening Guide

This project is a Laravel application. Below are concrete steps and checks to keep it secure in development and production.

## 1) Environment (never commit secrets)
- Do NOT commit `.env` to source control (already ignored in `.gitignore`).
- Production:
  - `APP_ENV=production`
  - `APP_DEBUG=false`
  - `APP_URL=https://your-domain`
  - `SESSION_DRIVER=database`
  - `SESSION_ENCRYPT=true`
  - `SESSION_SECURE_COOKIE=true` (requires HTTPS)
  - `SESSION_HTTP_ONLY=true`
  - `SESSION_SAME_SITE=lax` (or `strict` if you do not embed cross-site)
  - `LOG_LEVEL=warning` (or higher)

## 2) HTTP Security Headers
The app ships with a global middleware `App\Http\Middleware\SecureHeaders` that adds:
- `X-Frame-Options: DENY`
- `X-Content-Type-Options: nosniff`
- `Referrer-Policy: no-referrer`
- `Permissions-Policy: camera=(), microphone=(), geolocation=(), fullscreen=(self), payment=()`
- `Cross-Origin-Opener-Policy: same-origin`
- `Strict-Transport-Security` (HTTPS + production only)
- `Content-Security-Policy-Report-Only` (safe default; adjust and enforce later)

You can tune the CSP in `SecureHeaders.php`. Once verified in the browser console, switch to an enforcing `Content-Security-Policy` header.

## 3) Authentication & Rate Limits
- Login, register, and password reset routes are rate-limited (see `routes/auth.php`).
- Keep password hashing defaults (BCRYPT, `BCRYPT_ROUNDS=12` is set in `.env`).
- Ensure email verification is enabled (already in routes) and use `verified` middleware where needed.

## 4) CSRF, Sessions, and Cookies
- CSRF protection is enabled for the `web` stack (Laravel defaults).
- Use the database session driver and encrypt sessions in production.
- Enable secure cookies (HTTPS) in production via `SESSION_SECURE_COOKIE=true`.

## 5) Dependencies & Updates
- Keep composer and npm dependencies up to date.
- Monitor `laravel/framework` and `spatie/laravel-permission` for security advisories.
- In production, use `php artisan config:cache route:cache view:cache` and keep OPcache enabled.

## 6) Deployment
- Serve over HTTPS behind a trusted proxy (load balancer); ensure proxy forwards `X-Forwarded-*` headers.
- Set file permissions so that `storage/` and `bootstrap/cache/` are writable by the web user only.
- Disable directory indexing on the web server and only expose `public/`.

## 7) Observability
- Set `LOG_LEVEL=warning` (or higher) in production to reduce sensitive info in logs.
- Consider a WAF/CDN (Cloudflare/Fastly) with basic bot mitigation and rate limits at the edge.

## 8) Optional Hardening
- Add 2FA for admin accounts via a package (Laravel Fortify supports this).
- Enforce password policy and account lockout after repeated failures.
- Replace `CSP-Report-Only` with enforcing `Content-Security-Policy` after testing.

## Quick Checklist
- [ ] `.env` not committed
- [ ] APP_DEBUG=false in prod
- [ ] HTTPS everywhere + HSTS
- [ ] Sessions in DB + encrypted + secure cookies
- [ ] CSRF on forms
- [ ] Auth endpoints throttled
- [ ] Security headers present
- [ ] Dependencies up-to-date

