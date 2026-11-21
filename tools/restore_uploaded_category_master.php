<?php
declare(strict_types=1);

/**
 * Synchronizes ONLY category names, SLA and active flags from the uploaded
 * smartcitytiruppur.sql, preserving all target IDs and historical references.
 *
 * Preview (read-only): php tools/restore_uploaded_category_master.php
 * Apply (LOCAL XAMPP only):
 * php tools/restore_uploaded_category_master.php --apply-local --confirm=RESTORE_UPLOADED_CATEGORIES
 */
if (PHP_SAPI !== 'cli') { http_response_code(403); exit("CLI only\n"); }
$apply = false; $confirm = false;
foreach (array_slice($argv, 1) as $arg) {
    if ($arg === '--apply-local') { $apply = true; continue; }
    if ($arg === '--confirm=RESTORE_UPLOADED_CATEGORIES') { $confirm = true; continue; }
    fwrite(STDERR, "Unknown argument: {$arg}\n"); exit(2);
}
if ($apply && !$confirm) { fwrite(STDERR,"Confirmation required; no changes.\n"); exit(2); }

require_once dirname(__DIR__) . '/includes/bootstrap.php';
if ($apply && (APP_ENV !== 'local' ||
    !in_array(strtolower((string)DB_HOST), ['127.0.0.1','localhost','::1'],true))) {
    fwrite(STDERR,"Apply allowed only with local XAMPP configuration.\n"); exit(2);
}
$pdo = db();
if ((string)$pdo->query('SELECT DATABASE()')->fetchColumn() !== 'smartcitytiruppur') {
    fwrite(STDERR,"SAFETY STOP: expected smartcitytiruppur database. No changes.\n"); exit(2);
}
function masterKey(string $name): string {
    $name = trim(preg_replace('/\s+/u',' ',$name) ?? $name);
    return function_exists('mb_strtolower') ? mb_strtolower($name,'UTF-8') : strtolower($name);
}
function currentCategoryMaster(PDO $db): array {
    return [
        'departments'=>$db->query('SELECT id,department_name,is_active FROM smartcitytiruppur.departments ORDER BY id')->fetchAll(PDO::FETCH_ASSOC),
        'categories'=>$db->query('SELECT id,department_id,category_name,sla_days,is_active FROM smartcitytiruppur.complaint_categories ORDER BY id')->fetchAll(PDO::FETCH_ASSOC)
    ];
}
function categoryChanges(array $source, array $target): array {
    $sourceDepts=[];$targetDepts=[];$targetDeptIds=[];$sourceDeptIds=[];
    foreach($source['departments'] as $d){
        $k=masterKey((string)$d[1]);
        if(isset($sourceDepts[$k]))throw new RuntimeException('Duplicate source department');
        $sourceDepts[$k]=$d;$sourceDeptIds[(int)$d[0]]=$k;
    }
    foreach($target['departments'] as $d){
        $k=masterKey((string)$d['department_name']);
        if(isset($targetDepts[$k]))throw new RuntimeException('Duplicate target department');
        $targetDepts[$k]=$d;$targetDeptIds[(int)$d['id']]=$k;
    }
    foreach($sourceDepts as $k=>$d){
        if(!isset($targetDepts[$k]))throw new RuntimeException('Missing target department: '.$d[1]);
        if((int)$targetDepts[$k]['is_active']!==(int)$d[2])throw new RuntimeException('Department active flag mismatch: '.$d[1]);
    }
    $have=[];
    foreach($target['categories'] as $c){
        $d=$targetDeptIds[(int)$c['department_id']]??null;
        if($d===null)throw new RuntimeException('Unknown target category department');
        $key=$d."\x1F".masterKey((string)$c['category_name']);
        if(isset($have[$key]))throw new RuntimeException('Ambiguous duplicate destination category '.$c['category_name']);
        $have[$key]=$c;
    }
    $seen=[];$insert=[];$update=[];$deactivate=[];$extra=[];$summary=[];$unchanged=0;
    foreach($source['categories'] as $c){
        [$srcId,$srcDeptId,$name,$days,$active]=$c;
        $d=$sourceDeptIds[(int)$srcDeptId]??null;
        if($d===null)throw new RuntimeException('Unknown source category department');
        $key=$d."\x1F".masterKey((string)$name);
        if(isset($seen[$key]))throw new RuntimeException('Duplicate source category '.$name);
        $seen[$key]=true;
        $targetDept=$targetDepts[$d];
        $label=(string)$targetDept['department_name'];
        if(!isset($summary[$label]))$summary[$label]=['total'=>0,'active'=>0];
        $summary[$label]['total']++;
        if((int)$active===1)$summary[$label]['active']++;
        $want=['department_id'=>(int)$targetDept['id'],'category_name'=>(string)$name,
               'sla_days'=>(int)$days,'is_active'=>(int)$active];
        $old=$have[$key]??null;
        if($old===null){$insert[]=$want;continue;}
        if((string)$old['category_name']!==$want['category_name']
           ||(int)$old['sla_days']!==$want['sla_days']
           ||(int)$old['is_active']!==$want['is_active']){
            $update[]=['id'=>(int)$old['id'],'old'=>$old,'new'=>$want];
        }else{$unchanged++;}
    }
    foreach($have as $key=>$c){
        if(!isset($seen[$key])){
            $extra[]=$c;
            if((int)$c['is_active']===1)$deactivate[]=(int)$c['id'];
        }
    }
    return compact('insert','update','deactivate','extra','summary','unchanged');
}
$sourcePath=dirname(__DIR__).'/database/category_master_from_uploaded_sql.json';
try {
    $src=json_decode((string)file_get_contents($sourcePath),true,512,JSON_THROW_ON_ERROR);
    if(count($src['departments']??[])!==14||count($src['categories']??[])!==306){
        throw new RuntimeException('Expected 14 departments and 306 categories in source');
    }
    $hashOptions=JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES|JSON_THROW_ON_ERROR;
    if(hash('sha256',json_encode($src['departments'],$hashOptions))!=='4c2883a0efc5283ed747c36bd5639d825e52b9824858bb28f126c159468c6130'
       ||hash('sha256',json_encode($src['categories'],$hashOptions))!=='3aae6f0ff060200ef1baa894f19ad59a7bd2ff1a6ad808834e0fb1e1863346c5'){
        throw new RuntimeException('Uploaded category source integrity check failed');
    }
}catch(Throwable $error){fwrite(STDERR,"SAFETY STOP: {$error->getMessage()}\n");exit(2);}
$lockName='smartcity_uploaded_category_master';$locked=false;
try {
    if($apply){
        $lock=$pdo->prepare('SELECT GET_LOCK(?,5)');$lock->execute([$lockName]);
        $locked=((int)$lock->fetchColumn()===1);
        if(!$locked)throw new RuntimeException('Could not acquire maintenance lock');
        $engine=$pdo->query("SELECT ENGINE FROM information_schema.TABLES WHERE TABLE_SCHEMA='smartcitytiruppur' AND TABLE_NAME='complaint_categories'")->fetchColumn();
        if(strtoupper((string)$engine)!=='INNODB')throw new RuntimeException('Category table must be InnoDB');
        $triggers=(int)$pdo->query("SELECT COUNT(*) FROM information_schema.TRIGGERS WHERE TRIGGER_SCHEMA='smartcitytiruppur' AND EVENT_OBJECT_TABLE='complaint_categories'")->fetchColumn();
        if($triggers)throw new RuntimeException('Category-table trigger exists; review before changing data');
    }
    $target=currentCategoryMaster($pdo);
    $plan=categoryChanges($src,$target);
    echo 'Uploaded source: 306 categories; main database: '.count($target['categories'])." rows\n";
    printf("Plan: insert=%d update=%d deactivate_extras=%d retain_historical_extras=%d unchanged=%d\n",
        count($plan['insert']),count($plan['update']),count($plan['deactivate']),count($plan['extra']),$plan['unchanged']);
    foreach($plan['summary'] as $label=>$counts){
        printf("%s: %d total, %d active\n",$label,$counts['total'],$counts['active']);
    }
    foreach(array_slice($plan['update'],0,5) as $r){
        printf("Change ID #%d %s: SLA %d -> %d, active %d -> %d\n",$r['id'],$r['new']['category_name'],$r['old']['sla_days'],$r['new']['sla_days'],$r['old']['is_active'],$r['new']['is_active']);
    }
    if(!$apply){echo "READ ONLY. No database records changed.\n";exit(0);}
    $dir=rtrim(sys_get_temp_dir(),'\\/').DIRECTORY_SEPARATOR.'smartcity-category-backups';
    if(!is_dir($dir)&&!mkdir($dir,0700,true)&&!is_dir($dir))throw new RuntimeException('Backup folder not writable');
    $file=$dir.DIRECTORY_SEPARATOR.'pre-category-repair-'.date('Ymd-His').'-'.bin2hex(random_bytes(4)).'.json';
    $backup=json_encode(['created'=>date('c'),'db'=>'smartcitytiruppur','previous'=>$target,'plan'=>$plan],JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE|JSON_INVALID_UTF8_SUBSTITUTE|JSON_THROW_ON_ERROR);
    if(file_put_contents($file,$backup,LOCK_EX)!==strlen($backup))throw new RuntimeException('Failed to save pre-change backup');
    echo "Pre-change backup: {$file}\n";
    if(currentCategoryMaster($pdo)!==$target)throw new RuntimeException('Data changed before transaction');
    $pdo->beginTransaction();
    $rows=$pdo->query('SELECT id,department_id,category_name,sla_days,is_active FROM smartcitytiruppur.complaint_categories ORDER BY id FOR UPDATE')->fetchAll(PDO::FETCH_ASSOC);
    if($rows!==$target['categories'])throw new RuntimeException('Data changed while locking rows');
    $change=$pdo->prepare('UPDATE smartcitytiruppur.complaint_categories SET category_name=?,sla_days=?,is_active=? WHERE id=? AND department_id=?');
    $insert=$pdo->prepare('INSERT INTO smartcitytiruppur.complaint_categories (department_id,category_name,sla_days,is_active) VALUES (?,?,?,?)');
    $disable=$pdo->prepare('UPDATE smartcitytiruppur.complaint_categories SET is_active=0 WHERE id=? AND is_active=1');
    foreach($plan['update'] as $r){
        $n=$r['new'];$change->execute([$n['category_name'],$n['sla_days'],$n['is_active'],$r['id'],$n['department_id']]);
        if($change->rowCount()!==1)throw new RuntimeException('Update failed at category #'.$r['id']);
    }
    foreach($plan['insert'] as $n){
        $insert->execute([$n['department_id'],$n['category_name'],$n['sla_days'],$n['is_active']]);
        if($insert->rowCount()!==1)throw new RuntimeException('Insert failed for '.$n['category_name']);
    }
    foreach($plan['deactivate'] as $id){
        $disable->execute([$id]);
        if($disable->rowCount()!==1)throw new RuntimeException('Deactivate failed for #'.$id);
    }
    $after=categoryChanges($src,currentCategoryMaster($pdo));
    if($after['insert']||$after['update']||$after['deactivate']){
        throw new RuntimeException('Verification failed; rolling back');
    }
    $pdo->commit();
    echo "SUCCESS: uploaded categories synchronized; original category IDs and complaint references preserved.\n";
    echo "Officer approvals and automatic escalation were NOT altered.\n";
}catch(Throwable $error){
    if($pdo->inTransaction())$pdo->rollBack();
    fwrite(STDERR,"SAFETY STOP: {$error->getMessage()}\n");
    exit(3);
}finally{
    if($locked){$unlock=$pdo->prepare('SELECT RELEASE_LOCK(?)');$unlock->execute([$lockName]);}
}
