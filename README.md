# Online Hospital Appointment System

Patients book appointments with doctors; staff manage schedules; administrators maintain the hospital directory.
Vanilla PHP 8 + MySQL (PDO) + HTML5/CSS3/JS. No frameworks.

## Setup
1. Install PHP 8.1+ and MySQL/MariaDB (XAMPP/WAMP/Laragon works).
2. Import the database:
   ```
   mysql -u root -p < sql/schema.sql
   mysql -u root -p < sql/seed.sql
   ```
   If the database already exists, do not re-import `schema.sql` because it drops existing tables. Apply any required one-time migrations: `sql/migrate_patient_accounts.sql` if needed; `sql/migrate_hospitals.sql` if the hospitals table is missing; `sql/migrate_hospital_building_images.sql` if the hospitals table lacks the photo field; and `sql/migrate_doctor_experience.sql` if that field is missing.
3. Edit DB credentials in `config/config.php` (default: host `127.0.0.1`, db `hospital_db`, user `root`, empty password).
4. Start the server from the project folder:
   ```
   php -S localhost:8000 -t public
   ```
5. Open http://localhost:8000. The Home link opens the public welcome page or logged-in dashboard.

## Demo accounts (password for all: `password`)
| Username | Role |
|---|---|
| admin | admin (everything, incl. changing roles and managing staff/admin accounts) |
| staff1 | staff (CRUD patients, doctors, appointments; edit/delete **patient** user accounts) |
| patient1 | patient (view doctors/appointments, book appointments) |

## Logins
- **Staff & Admin portal:** `index.php?r=auth/staff` - rejects patient accounts.
- **Patient login:** `index.php?r=auth/login` (registration collects patient details and creates a linked patient record).
- **My profile:** `index.php?r=profile/edit` - every logged-in user can edit their own username/email and change their password (current password required). Roles can never be changed here.

## Who can manage user accounts
| Action | admin | staff | patient |
|---|---|---|---|
| List users | yes | yes | no |
| Edit / delete **patient** accounts | yes | yes | no |
| Edit / delete **staff or admin** accounts | yes | no (403) | no |
| Change a user's role | yes | no (403) | no |
| Edit own profile (`profile/edit`) | yes | yes | yes |
| Edit/delete own account via Users page | no (use My profile; self-delete blocked) | no | no |

## Typography
All text (body, headings, inputs, buttons, tables) uses **Bookman Old Style**
(`font-family: "Bookman Old Style", Bookman, "URW Bookman", Georgia, serif`) defined once in `public/assets/css/style.css`.
Bookman Old Style ships with Windows/Microsoft Office; on systems without it the browser falls back to Bookman/URW Bookman, then Georgia.

## Database credentials
Default (`config/config.php`): host `127.0.0.1`, database `hospital_db`, user `root`, password empty. Change them to match your MySQL.

## Structure (MVC)
```
config/config.php          DB credentials
app/core/bootstrap.php     session, PDO, e(), CSRF, auth helpers, view()
app/controllers/*          Auth, Home, Hospital, Patient, Doctor, Appointment, User, Api
app/views/*                templates (all output escaped with e())
public/index.php           front controller + whitelisted routes
public/assets/{css,js}     responsive CSS, validation + Fetch live search
sql/schema.sql, seed.sql   3NF schema (PK/FK) and sample data
```

## Routes (`index.php?r=...`)
| Route | Access |
|---|---|
| auth/login, auth/staff, auth/register, auth/logout(POST) | public / logged in |
| home/index | public welcome page / logged-in dashboard |
| hospitals/index | logged-in users can browse available hospitals |
| hospitals/form, save, delete | admins only; manage hospital name, location, services, category, phone, building photo, and availability |
| doctors/index | any user; shows doctors' years of experience |
| doctors/form, save, delete | staff/admin manage doctor profiles |
| appointments/index, form, save, delete | list+book: any user; edit/delete: staff/admin |
| patients/index, form, save, delete | staff/admin |
| users/index, form, save, delete | admin; staff (patient accounts only) |
| users/role | admin |
| profile/edit, profile/save | any logged-in user (own account) |
| api/search&q=term (JSON) | logged in |

## CRUD coverage
- hospitals, patients, doctors, appointments: full Create/Read/Update/Delete
- users: Register (C), list (R), change role (U), delete (D) - admin

## Security
`password_hash()/password_verify()`, PDO prepared statements everywhere, `htmlspecialchars()` via `e()`,
CSRF token on every POST (and logout), server-side validation, `session_regenerate_id()` on login,
HttpOnly + SameSite cookies, role checks on each action, self-registration is always `patient`.

## Rule: double-booking
A doctor cannot have two non-cancelled appointments at the same date/time (checked in PHP + UNIQUE constraint).

## Test evidence
See `TEST_PLAN.md` (33 cases: happy + failure paths). Put one screenshot per test in `/screenshots`
(e.g. `01-register.png`, `10-double-booking.png`) and tick the Result column.

| Evidence | File |
|---|---|
| Staff & Admin login page | screenshots/01-staff-admin-login.png |
| Admin users page (role dropdowns) | screenshots/02-admin-users.png |
| Staff users page (edit/delete patients only) | screenshots/03-staff-users-edit-delete.png |
| Staff edit user form | screenshots/04-staff-edit-user.png |
| Edit my profile | screenshots/05-edit-my-profile.png |
| CRUD (hospitals, patients, doctors, appointments, users) | screenshots/... |
| Live search (JSON API) | screenshots/... |
| CSRF / 403 / XSS checks | screenshots/... |

## Demo video (2-3 min) - suggested order
Register > login as staff > add patient/doctor > book appointment > double-booking error > live search > admin changes a role > show `schema.sql` and security code.
