<?php
/**
 * Password Migration Script
 * 
 * This script hashes all plain text passwords in the database.
 * Run this ONCE after running the SQL migration.
 * 
 * WARNING: This will permanently change passwords in the database!
 * Make sure to backup your database first!
 */

// Use absolute paths
$rootPath = dirname(__DIR__);
//require_once $rootPath . '/app/config/conn.php';
//require_once $rootPath . '/app/controller/password_functions.php';

echo "===========================================\n";
echo "Password Migration Script\n";
echo "===========================================\n\n";

// Check if database connection exists
if (!$conn) {
    die("ERROR: Database connection failed!\n");
}

// Password mappings (plain text -> will be hashed)
$passwords = [
    'super_admin' => [
        ['id' => 'SA001', 'plain' => 'password123']
    ],
    'admin' => [
        ['id' => 'A001', 'plain' => 'adminpass']
    ],
    'staff' => [
        ['id' => 'D001', 'plain' => 'doc12345'],
        ['id' => 'D002', 'plain' => 'doctor123'],
        ['id' => 'P001', 'plain' => 'pharm001']
    ]
];

$totalUpdated = 0;
$errors = [];

// Migrate super_admin passwords
echo "Migrating super_admin passwords...\n";
foreach ($passwords['super_admin'] as $user) {
    try {
        $hashedPassword = hashPassword($user['plain']);
        $stmt = $conn->prepare("UPDATE super_admin SET super_admin_password = ? WHERE super_admin_id = ?");
        $stmt->bind_param("ss", $hashedPassword, $user['id']);
        
        if ($stmt->execute()) {
            echo "  ✓ Updated super_admin {$user['id']}\n";
            $totalUpdated++;
        } else {
            $errors[] = "Failed to update super_admin {$user['id']}: " . $stmt->error;
        }
        $stmt->close();
    } catch (Exception $e) {
        $errors[] = "Error hashing password for super_admin {$user['id']}: " . $e->getMessage();
    }
}

// Migrate admin passwords
echo "\nMigrating admin passwords...\n";
foreach ($passwords['admin'] as $user) {
    try {
        $hashedPassword = hashPassword($user['plain']);
        $stmt = $conn->prepare("UPDATE admin SET admin_password = ? WHERE admin_id = ?");
        $stmt->bind_param("ss", $hashedPassword, $user['id']);
        
        if ($stmt->execute()) {
            echo "  ✓ Updated admin {$user['id']}\n";
            $totalUpdated++;
        } else {
            $errors[] = "Failed to update admin {$user['id']}: " . $stmt->error;
        }
        $stmt->close();
    } catch (Exception $e) {
        $errors[] = "Error hashing password for admin {$user['id']}: " . $e->getMessage();
    }
}

// Migrate staff passwords
echo "\nMigrating staff passwords...\n";
foreach ($passwords['staff'] as $user) {
    try {
        $hashedPassword = hashPassword($user['plain']);
        $stmt = $conn->prepare("UPDATE staff SET staff_password = ? WHERE staff_id = ?");
        $stmt->bind_param("ss", $hashedPassword, $user['id']);
        
        if ($stmt->execute()) {
            echo "  ✓ Updated staff {$user['id']}\n";
            $totalUpdated++;
        } else {
            $errors[] = "Failed to update staff {$user['id']}: " . $stmt->error;
        }
        $stmt->close();
    } catch (Exception $e) {
        $errors[] = "Error hashing password for staff {$user['id']}: " . $e->getMessage();
    }
}

// Summary
echo "\n===========================================\n";
echo "Migration Summary\n";
echo "===========================================\n";
echo "Total passwords updated: $totalUpdated\n";

if (!empty($errors)) {
    echo "\nErrors encountered:\n";
    foreach ($errors as $error) {
        echo "  ✗ $error\n";
    }
} else {
    echo "\n✓ All passwords migrated successfully!\n";
}

echo "\n===========================================\n";
echo "IMPORTANT: Login credentials after migration:\n";
echo "===========================================\n";
echo "Superadmin:\n";
echo "  Email: superadmin@mail.com\n";
echo "  Password: password123\n\n";
echo "Admin:\n";
echo "  Email: admin1@mail.com\n";
echo "  Password: adminpass\n\n";
echo "Doctor (D001):\n";
echo "  Email: lim@mail.com\n";
echo "  Password: doc123\n\n";
echo "Doctor (D002):\n";
echo "  Email: wong@mail.com\n";
echo "  Password: doc234\n\n";
echo "Pharmacist (P001):\n";
echo "  Email: lee@mail.com\n";
echo "  Password: pharm001\n";
echo "===========================================\n";

$conn->close();
?>
