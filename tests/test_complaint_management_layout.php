<?php
declare(strict_types=1);
// Verify the management panel cannot close the outer employee layout.
$source=file_get_contents(__DIR__.'/../employee/complaint-view.php');
$start=strpos($source,'<section class="card content-card mt-4" id="complaint-update-panel"');
$end=strpos($source,"<?php include __DIR__ . '/../includes/employee-footer.php'; ?>");
if ($start===false || $end===false || $end<=$start) throw new RuntimeException('Management panel wrapper or footer missing');
$panel=substr($source,$start,$end-$start);
$html='';
foreach(token_get_all('<?php ?>'.$panel) as $token) {
    if(is_array($token) && $token[0]===T_INLINE_HTML) $html.=$token[1];
}
preg_match_all('~</?(?:div|section)\\b[^>]*>~i',$html,$matches);
$stack=[];
foreach($matches[0] as $tag) {
    preg_match('~^</?([a-z]+)~i',$tag,$m);
    if(str_starts_with($tag,'</')) {
        if(array_pop($stack)!==$m[1]) throw new RuntimeException('Unbalanced management panel containers');
    } else { $stack[]=$m[1]; }
}
if($stack) throw new RuntimeException('Management panel leaves containers open');
echo "Complaint management panel structure passed.\n";
