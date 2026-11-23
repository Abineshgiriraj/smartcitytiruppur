<?php
declare(strict_types=1);
/** Pure database-local selection checks. Uses in-memory SQLite; does not touch smartcitytiruppur. */
require_once dirname(__DIR__) . '/includes/officer-authority.php';
if (!in_array('sqlite', PDO::getAvailableDrivers(), true)) {
    fwrite(STDERR, "SQLite PDO unavailable: cannot run selection tests.\n");
    exit(2);
}
$pdo = new PDO('sqlite::memory:');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$pdo->exec("CREATE TABLE complaints (id INTEGER PRIMARY KEY, assigned_officer_id INTEGER, is_deleted INTEGER, status TEXT)");
$pdo->exec("INSERT INTO complaints VALUES
 (1,35,0,'Assigned'),(2,35,0,'In Progress'),
 (3,37,0,'Assigned'),(4,109778,0,'Resolved'),
 (5,37,1,'Assigned')");
$candidates = [
 ['id'=>35,'officer_name'=>'Officer A','ward_rank'=>0],
 ['id'=>37,'officer_name'=>'Officer B','ward_rank'=>0],
 ['id'=>109778,'officer_name'=>'Officer C','ward_rank'=>0],
];
$choose = pdf_pick_least_loaded_officer($pdo,$candidates);
if ((int)$choose['id']!==109778 || (int)$choose['open_complaints']!==0) {
    throw new RuntimeException('Least-load selection failed.');
}
$pdo->exec("INSERT INTO complaints VALUES (6,109778,0,'Assigned')");
$choose=pdf_pick_least_loaded_officer($pdo,$candidates);
if ((int)$choose['id']!==37) throw new RuntimeException('Stable ID tie breaker failed.');
$candidates[2]['ward_rank']=1;
$choose=pdf_pick_least_loaded_officer($pdo,$candidates);
if ((int)$choose['id']!==37) throw new RuntimeException('Explicit ward priority failed.');
if (pdf_pick_least_loaded_officer($pdo,[])!==null) throw new RuntimeException('Empty candidate guard failed.');
echo "PASS: least workload, open-only filtering, ward priority, stable tie-break and no-candidate guard.\n";
