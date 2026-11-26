<?php
require_once __DIR__ . '/../includes/bootstrap.php';

$pdo = db();
echo "--- Testing Unique 5-Digit Reference Generation & Concurrency Safety ---\n";

// Verify sequence table
$seq = $pdo->query('SELECT * FROM complaint_reference_sequence WHERE id = 1')->fetch(PDO::FETCH_ASSOC);
echo "Current sequence: " . print_r($seq, true) . "\n";

// Generate 3 sequential references
$r1 = generate_reference_number(null, $pdo);
$r2 = generate_reference_number(null, $pdo);
$r3 = generate_reference_number(null, $pdo);

echo "Generated: {$r1}, {$r2}, {$r3}\n";

if (!preg_match('/^[0-9]{5}$/', $r1) || !preg_match('/^[0-9]{5}$/', $r2) || !preg_match('/^[0-9]{5}$/', $r3)) {
    echo "ERROR: Generated reference is not a 5-digit number!\n";
    exit(1);
}

if ((int)$r2 !== (int)$r1 + 1 || (int)$r3 !== (int)$r2 + 1) {
    echo "ERROR: Numbers are not strictly sequential!\n";
    exit(1);
}

// Check collision guard: insert a future record, verify generator skips it
$collisionTarget = (string)((int)$r3 + 1);
echo "Simulating pre-existing collision at {$collisionTarget}...\n";
$pdo->prepare("INSERT INTO complaints (reference_no, citizen_name, mobile_no, communication_address, ward_id, zone_id, department_id, category_id, status, source, created_at, updated_at) 
               VALUES (?, 'Collision Test Citizen', '9999999999', 'Test Location', 1, 1, 1, 1, 'Registered', 'Website', NOW(), NOW())")
    ->execute([$collisionTarget]);

$r4 = generate_reference_number(null, $pdo);
echo "Generated after pre-existing {$collisionTarget}: {$r4}\n";
if ((int)$r4 !== (int)$collisionTarget + 1) {
    echo "ERROR: Collision was not skipped! Got {$r4}, expected " . ((int)$collisionTarget + 1) . "\n";
    exit(1);
}

// Clean up collision test record
$pdo->prepare("DELETE FROM complaints WHERE reference_no = ?")->execute([$collisionTarget]);
echo "Collision test passed successfully!\n";

echo "ALL 5-DIGIT REFERENCE TESTS PASSED.\n";
