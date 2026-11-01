# KFS Portal — Test Accounts

Development / QA accounts for the unified portal (`kfs-portal`).
Every account below is created by the database seeder and can be re-created at any time.

- **Login page:** `http://127.0.0.1:1598/login` (or `/login` on any host)
- **Portal (after login):** `/portal`
- **Seeded by:** `php artisan db:seed --class=UserSeeder --force`
- **Create everything from scratch:** `php artisan migrate:fresh --seed --force`

> **Security:** these accounts exist for local development and internal testing only.
> Remove or change them before deploying to production.

---

## 1. Super administrator

| Field | Value |
| --- | --- |
| Username | `super.admin` |
| Email | `super.admin@kfs.gov.eg` |
| Password | `Demo@12345` |
| Spatie role | `super_admin` |
| Access | Full access to **everything** (bypasses all permission gates, can manage users and roles) |

Alias account with the same permissions:

| Field | Value |
| --- | --- |
| Username | `admin` |
| Email | `admin@kfs.gov.eg` |
| Password | `Admin@12345` |

---

## 2. Portal administration

| Field | Value |
| --- | --- |
| Username | `portal.admin` |
| Email | `portal.admin@kfs.gov.eg` |
| Password | `Demo@12345` |
| Spatie role | `portal_admin` |
| Access | All three systems (`system.archive.access`, `system.meetings.access`, `system.followup.access`) + user/role management + all meeting permissions |
| Expected routes | `/portal`, `/archive`, `/meetings`, `/followup/dashboard` → **200** |

---

## 3. Archive system (الأرشيف الإلكتروني)

| Field | archive.admin | archive.manager | archive.staff |
| --- | --- | --- | --- |
| Email | `archive.admin@kfs.gov.eg` | `archive.manager@kfs.gov.eg` | `archive.staff@kfs.gov.eg` |
| Password | `Demo@12345` | `Demo@12345` | `Demo@12345` |
| Spatie role | `archive_admin` | `archive_manager` | `archive_staff` |
| Legacy role | `admin` | `manager` | `staff` |
| Permissions | `system.archive.access` | `system.archive.access` | `system.archive.access` |
| Allowed routes | `/portal`, `/archive`, `/archive/subjects`, `/archive/workspace` | same | same |
| Blocked routes | `/meetings`, `/followup/dashboard` → **403** | same | same |

---

## 4. Meetings system (إدارة الاجتماعات)

| Field | meetings.admin | meetings.manager | meetings.staff |
| --- | --- | --- | --- |
| Email | `meetings.admin@kfs.gov.eg` | `meetings.manager@kfs.gov.eg` | `meetings.staff@kfs.gov.eg` |
| Password | `Demo@12345` | `Demo@12345` | `Demo@12345` |
| Spatie role | `meetings_admin` | `meetings_manager` | `meetings_staff` |
| Legacy role | `admin` | `manager` | `staff` |
| Permissions | `system.meetings.access` + all `*_meeting`, `*_category`, `*_room`, `*_user`, `*_role` permissions | `system.meetings.access` + view/create/update meetings, view rooms/categories/users | `system.meetings.access` + view/create/update meetings, view rooms |
| Allowed routes | `/portal`, `/meetings`, `/meetings/meetings`, `/meetings/rooms`, `/meetings/users` | same (restricted by policy) | `/portal`, `/meetings`, `/meetings/meetings`, `/meetings/rooms` |
| Blocked routes | `/archive`, `/followup/dashboard` → **403** | same | same |

---

## 5. Follow-up system (التكليفات والمتابعة)

| Field | followup.manager | followup.supervisor | followup.staff |
| --- | --- | --- | --- |
| Email | `followup.manager@kfs.gov.eg` | `followup.supervisor@kfs.gov.eg` | `followup.staff@kfs.gov.eg` |
| Password | `Demo@12345` | `Demo@12345` | `Demo@12345` |
| Spatie role | `followup_manager` | `followup_supervisor` | `followup_staff` |
| Legacy role | `assignment_manager` | `supervisor` | `staff` |
| Permissions | `system.followup.access` + `tasks.view`, `tasks.create`, `tasks.edit`, `tasks.delete` | `system.followup.access` + `tasks.view`, `tasks.create`, `tasks.edit` | `system.followup.access` + `tasks.view`, `tasks.create`, `tasks.edit` |
| Allowed routes | `/portal`, `/followup/dashboard`, `/followup/tasks` | same | same |
| Blocked routes | `/archive`, `/meetings` → **403** | same | same |

---

## 6. Pending (not activated) account

| Field | Value |
| --- | --- |
| Username | `pending.user` |
| Email | `pending.user@kfs.gov.eg` |
| Password | `Demo@12345` |
| Spatie role | `pending_user` (no permissions) |
| Expected behaviour | Login is **rejected** with the message: the account has not been activated yet. Use it to verify that the registration → review → activation flow is enforced. |
| Activation test | Activate it from `/archive/users` (set *Active* = true and assign a role), then log in again |

---

## 7. Legacy demo accounts (still active)

These older accounts were used in earlier development stages and still work:

| Username | Email | Password | Role |
| --- | --- | --- | --- |
| `archive.demo` | `archive.demo@kfs.gov.eg` | `Demo@12345` | `archive_manager` |
| `meetings.demo` | `meetings.demo@kfs.gov.eg` | `Demo@12345` | `meetings_staff` |
| `followup.demo` | `followup.demo@kfs.gov.eg` | `Demo@12345` | `followup_staff` |

They can safely be deleted.

---

## 8. Login identifiers

You can log in with **either** the username **or** the email address:

```
login: super.admin        password: Demo@12345
login: super.admin@kfs.gov.eg  password: Demo@12345
```

---

## 9. Expected HTTP status codes (quick matrix)

| Account | `/portal` | `/archive` | `/meetings` | `/followup/dashboard` |
| --- | --- | --- | --- | --- |
| `super.admin`, `admin`, `portal.admin` | 200 | 200 | 200 | 200 |
| `archive.*` | 200 | 200 | **403** | **403** |
| `meetings.*` | 200 | **403** | 200 | **403** |
| `followup.*` | 200 | **403** | **403** | 200 |
| `pending.user` | **302 → /login** | 302 | 302 | 302 |
| Guest (not logged in) | 302 → /login | 302 | 302 | 302 |

---

## 10. Notes for testers

- **Login throttling:** 8 login attempts per minute and 6 registration attempts per minute (HTTP 429 after the limit).
- **Last login:** every successful login is stored in `users.last_login_at` and shown on the portal page.
- **Password reset:** `/forgot-password` writes the reset link to `storage/logs/laravel.log` when the mail driver is `log`.
- **New registrations:** created through `/register` are always inactive (`is_active = false`) with no system permissions; the user is redirected to `/pending`.
- **Permissions:** defined in `database/seeders/RolesAndPermissionsSeeder.php`; master data (departments, categories, rooms, task sources/entities) in `MasterDataSeeder.php`.
