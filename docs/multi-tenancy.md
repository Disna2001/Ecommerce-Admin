# Multi-Tenancy

This project uses a single-codebase, single-server, hostname-based tenant architecture for the `Ecommerce+Admin` product.

## How it works

- One Laravel app serves multiple client storefront and admin workspaces.
- The current hostname resolves directly to the matching tenant workspace.
- Tenant-owned models use `tenant_id` and are scoped automatically during web requests.
- Site settings are tenant-specific, so branding, mail settings, storefront content, and similar values can differ per client.
- Queue jobs that send customer notifications now carry tenant context with them.

## Core pieces

- `tenants` table stores the client workspace and allowed hostnames.
- `App\Services\Tenancy\TenantManager` resolves and stores the current tenant.
- `App\Http\Middleware\ResolveTenant` enforces tenant resolution per request.
- `App\Models\Concerns\BelongsToTenant` applies tenant scoping to tenant-owned models.

## Local default tenant

The migration creates a default tenant from `APP_URL` and also allows:

- `localhost`
- `127.0.0.1`

On this machine, the local app URL is:

```text
http://127.0.0.1:9001
```

## Adding another client

Create a new tenant record with its primary hostname and any aliases, then point the domain to this app. Once the hostname resolves here, the tenant workspace will load its own storefront, branding, and admin experience.

## Important note

This foundation keeps the app tenant-aware at the data layer. If you later add new business tables, give them a `tenant_id` and apply the tenant model trait so they stay isolated inside each deployment workspace.
