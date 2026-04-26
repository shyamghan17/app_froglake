# CRM Feature Pre-Merge Checklist

Use this checklist before merging any CRM (`Lead` package) feature.

## 1) Scope and Boundaries
- Change is limited to intended scope (no unrelated refactor).
- CRM-specific logic is in `packages/workdo/Lead/*`.
- Core app changes are only made when truly required.

## 2) Database and Models
- Migration added for schema changes (no direct DB edits).
- New columns/relations are reflected in model `$fillable` and casts if needed.
- Tenant fields (`created_by`, `creator_id`) are set correctly on create.

## 3) Permissions and Roles
- New permission keys are added to CRM permission seeder.
- Controller actions check required permissions (`can(...)`).
- UI visibility is permission-gated (buttons, pages, menu items).
- Role assignment impact is verified for company/staff/client.

## 4) Validation and Security
- All write endpoints use FormRequest or validator rules.
- File endpoints validate type/size and ownership.
- No trust in frontend tenant/user IDs without server-side checks.
- No secrets or credentials are added to code/logs.

## 5) Tenant and Access Safety
- All queries are tenant-scoped (`created_by = creatorId()` or equivalent).
- Own-vs-any permission behavior is correctly implemented.
- Membership checks are used where applicable (`user_leads`, `user_deals`, `client_deals`).

## 6) Frontend Integrity
- Page/component placed in package path under `Resources/js/Pages`.
- Route names are correct and stable (`lead.*` pattern).
- Menu/settings entries (if any) are wired and permission-gated.
- List + kanban behavior still works for leads/deals if touched.

## 7) CRM Workflow Regression Checks
- Lead create/edit/view/delete flows work.
- Deal create/edit/view/delete flows work.
- Stage movement/order update works and persists.
- Lead-to-deal conversion still works.
- Tasks/calls/files/discussions/emails affected flows still work.
- Dashboard/report pages still render correctly.

## 8) API (If Affected)
- API endpoints validate input and return consistent response format.
- Sanctum/auth middleware and permission checks are intact.
- Tenant scoping is applied to API queries as well.

## 9) Data and Event Consistency
- Activity logs/events are added where required.
- Notifications/emails (if used) are not broken by the change.
- Default data/seed assumptions remain valid.

## 10) Deployment Readiness
- Build-related frontend changes are identified.
- For GitHub -> cPanel flow, `public/build` updates are ready when needed.
- Cache refresh commands are documented for rollout notes.
- No build command was run without user approval.

## 11) Git and Review Quality
- Commit message is clear and scoped.
- PR description includes:
  - what changed
  - permissions impacted
  - migrations/seeders impacted
  - rollout steps
  - rollback note

## 12) Final Sign-off
- Manual verification completed for intended roles.
- No obvious regressions in touched CRM areas.
- Feature is ready for merge and deployment.
