# Test Plan

| # | Type | Test case | Steps | Expected | Result |
|---|---|---|---|---|---|
| 1 | Happy | Register | Fill valid username/email/password (8+) | Account created, redirect to login | |
| 2 | Failure | Register duplicate username | Register `admin` again | Error "already in use" | |
| 3 | Failure | Weak password | Register with 5-char password | Validation error (client and server) | |
| 4 | Happy | Login | Login as staff1 / password | Redirect to appointments, welcome message | |
| 5 | Failure | Wrong password | Login with bad password | "Invalid username or password" | |
| 6 | Happy | Add patient | Staff > Patients > Add, valid data | Patient appears in list | |
| 7 | Failure | Invalid patient | Phone `abc`, future DOB | Error messages, nothing saved | |
| 8 | Happy | Edit/Delete doctor | Staff edits a doctor, then deletes another | List updated | |
| 9 | Happy | Book appointment | Patient1 books future slot | Saved as `pending` | |
| 10 | Failure | Double booking | Book same doctor at same time | "already booked" error | |
| 11 | Failure | Past date | Book appointment in the past | Validation error | |
| 12 | Security | CSRF | Submit a delete form with an altered/missing token | HTTP 419 "Invalid CSRF token" | |
| 13 | Security | Access control | Logged in as patient1, open `?r=patients/index` | HTTP 403 Access denied | |
| 14 | Security | XSS | Save patient name `<script>alert(1)</script>` | Shown as plain text, no script runs | |
| 15 | Async | Live search | Type "Alice" in appointments search | Table filters via JSON API without reload | |
| 16 | Happy | Admin role change | Admin changes patient1 to staff | Role updated | |
| 17 | UI | Font and responsive | Open any page; shrink window to phone width | All text (incl. inputs/buttons) is Bookman Old Style; menu stacks, tables scroll sideways | |
| 18 | Async | Delete from live search | Search, then delete a result as staff | Confirm dialog, then row deleted | |
| 19 | Failure | Patient on staff portal | Log in to `auth/staff` as patient1 | "staff and administrators only", not logged in | PASS (automated) |
| 20 | Happy | Staff edits a patient account | Users > Edit patient1 > change email > Save | "User updated"; no role field shown | PASS (automated) |
| 21 | Happy | Staff deletes a patient account | Users > Delete (confirm) | "User deleted"; row gone | PASS (automated) |
| 22 | Failure | Staff vs admin account | Staff opens `users/form&id=1` or POSTs `users/delete` for admin; POSTs `users/role` | HTTP 403 each time; no Edit/Delete buttons on admin row | PASS (automated) |
| 23 | Happy | Edit my profile | My profile > change email; then change password with correct current password | "Profile updated" | PASS (automated) |
| 24 | Failure | Wrong current password | My profile > new password with wrong current password | "Current password is incorrect" | PASS (automated) |
| 25 | Happy | Admin adds a hospital | Hospitals > Add hospital; enter name, location, category, services and mark available | Hospital appears in the dashboard directory with its location, category, and services | |
| 26 | Failure | Invalid hospital record | Submit hospital form without location or services | Server rejects the record; no hospital is added | |
| 27 | Happy | Browse available hospitals | As a logged-in patient, open Home and Hospitals | Available listings show location, category, services, and contact phone | |
| 28 | Happy | Hide an unavailable hospital | As admin, edit a hospital and clear "Show as currently available" | Hospital no longer appears in dashboard previews and is labelled unavailable in the directory | |
| 29 | Security | Hospital admin-only controls | Sign in as patient and request hospital form/save/delete routes | Each management action returns HTTP 403 | |
| 30 | UI | Users sorted by ID | Open Users as admin or staff | User IDs appear in ascending numeric order | |
| 31 | Happy | Doctor experience | Add or edit a doctor with a whole number from 0 to 70, then open Doctors | Experience years save and display for that doctor | |
| 32 | Happy | Hospital building photo | Admin adds or edits a hospital and uploads a JPG, PNG, or WebP up to 5 MB | Photo preview appears in the form and on hospital cards/dashboard | |
| 33 | Failure | Invalid hospital photo | Upload an executable, unsupported image type, or image larger than 5 MB | Upload is rejected and no unsafe file is displayed | |
