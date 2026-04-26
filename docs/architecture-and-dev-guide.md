# Architecture And Development Guide

## Purpose
This handbook gives contributors a fixed workflow for building features safely in this codebase.

## Stack Overview
- Backend: Laravel 12, Sanctum, Spatie Permission, Inertia server adapter.
- Frontend: React + TypeScript + Inertia + Vite.
- Extension model: dynamic add-on packages under `packages/workdo/*`.
- Storage: local/public disk by default, optional S3/Wasabi.

## Runtime Entry Points
- App bootstrap and middleware aliases: `bootstrap/app.php`.
- Service providers list: `bootstrap/providers.php`.
- Dynamic package provider loader: `app/Providers/PackageServiceProvider.php`.
- Main routes: `routes/web.php`, `routes/api.php`, `routes/auth.php`.
- Installer and updater routes: `routes/installer.php`, `routes/updater.php`.

## Core Architecture
### 1) Multi-tenant + RBAC
- Users carry `type` (`superadmin`, `company`, staff-like roles, etc).
- Permissions and roles are managed through Spatie Permission.
- Seeder baseline is in `database/seeders/PermissionRoleSeeder.php`.

### 2) Module/Add-on System
- Packages are discovered from `packages/workdo/*`.
- Package metadata comes from each `module.json`.
- Activation state is stored in `add_ons` table and resolved through helpers/module class.
- Menu/settings visibility is gated by:
  - activated package list
  - user permissions

### 3) Inertia Shared Context
- `app/Http/Middleware/HandleInertiaRequests.php` shares:
  - `auth.user`, roles, permissions
  - `activatedPackages`
  - admin/company settings
  - base image URL prefix and global app data

### 4) Plan Enforcement
- `app/Http/Middleware/PlanModuleCheck.php` enforces plan validity and module access.
- Superadmin bypasses plan restrictions.

## Frontend Composition
### 1) App Boot
- `resources/js/app.tsx` resolves:
  - core pages from `resources/js/pages/**`
  - package pages from `packages/workdo/*/src/Resources/js/Pages/**`

### 2) Dynamic Navigation
- `resources/js/utils/menu.ts` merges:
  - core menus
  - package menus based on activated packages
  - custom DB-driven menus
- Final menu is permission-filtered.

### 3) Dynamic Settings Panels
- `resources/js/utils/settings.ts` merges:
  - core setting sections
  - package setting sections
- Final sections are permission-filtered.

## Feature Development Workflow
## 0) Before You Start
- Pull latest branch and run app locally.
- Confirm the feature belongs to:
  - core app
  - existing package
  - new package/add-on

## 1) Backend Work
- Add migration/model updates first.
- Add or reuse FormRequest validation classes.
- Add controller action and route.
- Protect route/action with permission checks.
- If feature is package-specific, keep code inside package boundaries.

## 2) Permission Integration
- Add permission names to relevant seeders (core or package seeder).
- Assign permission to intended roles.
- Verify permission string matches frontend menu/settings entry.

## 3) Frontend Work
- Add/extend page/component in core or package path.
- Wire route links via Ziggy route names.
- If it is a settings section, register in the proper settings file.
- If it is navigation, register menu entry and required permission.

## 4) Data + Validation Rules
- Never trust frontend constraints alone.
- Validate all inputs in backend.
- Keep file upload validation strict (size/type/ownership).

## 5) Manual Verification Checklist
- Role-based visibility works (superadmin/company/staff).
- Permission denial returns safe UX.
- Feature works with active and inactive package states.
- Feature works after cache clear and fresh login.

## Security Guidelines
- Do not expose installer/updater in production without strict access checks.
- Avoid broad `0777` style filesystem permissions where possible.
- Validate and sanitize any package/module installation inputs.
- Keep `APP_DEBUG=false` in production.
- Use HTTPS and secure cookie/session settings in live environments.
- Review third-party remote calls before production use.

## Deployment Workflow (GitHub -> cPanel)
- Build frontend locally:
  - `npm ci`
  - `npm run build`
- Commit/push updated `public/build` assets.
- On server:
  - `git pull`
  - `php artisan optimize:clear`
  - `php artisan config:cache`
  - `php artisan route:cache`
  - `php artisan view:cache`

Important:
- If server cannot run npm, `public/build` must come from local build commits.
- Stale build files cause missing package menus/settings on live.

## Testing Strategy
- Current test coverage is minimal. Add focused feature tests for:
  - permission-gated routes
  - settings save endpoints
  - package activation flows
  - high-risk file/media actions
- Prefer high-value tests over broad low-signal tests.

## Common Pitfalls
- Package enabled in DB but permissions not seeded to role.
- Package code present but frontend `public/build` not rebuilt.
- Settings keys saved but wrong `created_by` context.
- Missing `storage:link` or missing media files in `storage/app/public`.

## Quick Debug Sequence
1. Confirm route exists and middleware passes.
2. Confirm user has required permission.
3. Confirm package is active.
4. Confirm expected setting keys exist in DB.
5. Rebuild frontend and clear caches.
6. Re-test with hard refresh.
