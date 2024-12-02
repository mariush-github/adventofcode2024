<?php
define('CR',chr(0x0D));
define('LF',chr(0x0A));
$input = file_get_contents(__DIR__ .'/inputs/01.txt');
$input = str_replace(CR.LF,LF,$input);
$input = trim($input,LF);
$pairs = explode(LF,$input);
$a = array();
$b = array();
foreach ($pairs as $value) {
	$parts = explode('   ',$value);
	array_push($a,intval($parts[0]));
	array_push($b,intval($parts[1]));
}

sort($a);
sort($b);
$sum = 0;
foreach ($a as $idx=>$value) {
	$sum+= abs($b[$idx]-$a[$idx]);
}
echo "part 1 solution = $sum\n";
$c = array();
foreach ($a as $idx=>$value) {
	$c[$value] = 0;
	$c[$b[$idx]] = 0;
}
foreach ($b as $idx=>$value) {
	$c[$value]++;
}
$sum = 0;
foreach ($a as $idx=>$value) {
	$sum += $value * $c[$value];
}
echo "part 2 solution = $sum\n";


?>