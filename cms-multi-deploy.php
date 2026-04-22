<?php
// cms-multi-deploy.php - Multi-file deploy for SOMT CMS
// Access: https://drinksomt.ch/cms-multi-deploy.php?t=somt-cms-2026
$TOKEN = 'somt-cms-2026';
if(!isset($_GET['t']) || $_GET['t'] !== $TOKEN) { http_response_code(401); die('unauthorized'); }
$DOCROOT = '/var/www/vhosts/joerghurschler.com/drinksomt.ch/';
$GITHUB = 'https://raw.githubusercontent.com/joerghurschler-sudo/SOMT-Tee-v2/master/';
$UA = "User-Agent: Mozilla/5.0\r\n";
$CTX = stream_context_create(['http'=>['header'=>$UA,'timeout'=>30]]);
$FILES = [
    'cms-admin.html' => $GITHUB . 'cms-admin.html',
];
$OUT = [];
foreach($FILES as $name => $url) {
    $c = @file_get_contents($url, false, $CTX);
    if($c === false) { $OUT[$name] = "FAIL: could not fetch"; continue; }
    $w = @file_put_contents($DOCROOT.$name, $c);
    $OUT[$name] = $w ? "OK: $w bytes" : "FAIL: write error";
}
echo json_encode(['results'=>$OUT], JSON_PRETTY_PRINT);
unlink(__FILE__);
echo "\n<script>setTimeout(()=>{window.location.href='https://drinksomt.ch/cms-admin.html';},2000);</script>";
