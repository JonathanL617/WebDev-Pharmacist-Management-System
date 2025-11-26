# Database Migration Guide - Fix Login Issues

## ⚠️ IMPORTANT: Backup First!
Before running any migration, **backup your database** in phpMyAdmin:
1. Go to phpMyAdmin
2. Select `pharmacy_management_system` database
3. Click "Export" tab
4. Click "Go" to download backup

---

## Step-by-Step Migration Process

### Step 1: Run SQL Migration
1. Open **phpMyAdmin**
2. Select `pharmacy_management_system` database
3. Click **SQL** tab
4. Open the file: `include/migration_add_email.sql`
5. Copy all the SQL code
6. Paste into phpMyAdmin SQL tab
7. Click **Go**

**Expected Result:** You should see "1 row affected" for the UPDATE statement.

---

### Step 2: Run Password Migration Script
1. Open your browser
2. Navigate to: `http://localhost/WebDev-Pharmacist-Management-System/include/migrate_passwords.php`
3. Wait for the script to complete
4. You should see: "✓ All passwords migrated successfully!"

**What this does:** Converts all plain text passwords to secure hashed passwords.

---

### Step 3: Test Login
Try logging in with these credentials:

#### Superadmin
- **Email:** `superadmin@mail.com`
- **Password:** `password123`

#### Admin
- **Email:** `admin1@mail.com`
- **Password:** `adminpass`

#### Doctor (D001)
- **Email:** `lim@mail.com`
- **Password:** `doc123`

#### Pharmacist (P001)
- **Email:** `lee@mail.com`
- **Password:** `pharm001`

---

## What Was Fixed?

### 1. Added Email Column
- `super_admin` table now has `super_admin_email` column
- Superadmin can now login with email

### 2. Hashed Passwords
- All passwords are now securely hashed using `PASSWORD_ARGON2I`
- Password verification now works correctly

### 3. Status Validation
- Login now checks if account status is 'active'
- Inactive/blocked accounts cannot login

---

## Troubleshooting

### "Column 'super_admin_email' not found"
- Step 1 (SQL migration) failed or wasn't run
- Re-run `migration_add_email.sql`

### "Invalid email or password"
- Step 2 (password migration) failed or wasn't run
- Re-run `migrate_passwords.php`

### "Your account is inactive"
- Check database: account status must be 'active' (lowercase)
- Update via phpMyAdmin if needed

---

## After Migration

Once login works, you can:
1. Delete `include/migrate_passwords.php` (security)
2. Keep `migration_add_email.sql` for reference
3. Change passwords via the application's password reset feature
