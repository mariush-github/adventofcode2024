<?php
define('CR',chr(0x0D));
define('LF',chr(0x0A));


$test = 1;
$input_file = ($test==0) ? __DIR__.'/inputs/19.txt' : __DIR__ .'/inputs/19_test.txt';
$size = ($test==0) ? 141 : 15;

//$input = file_get_contents($input_file);
$input = file_get_contents($input_file);
$input = str_replace(array(CR,' '),array('',''),$input);

$lines = explode(LF,$input);
$flags = explode(',',$lines[0]);

$cache = array();
$solutions = array();

function findSolution( $pattern,$combo='',$stopatfirst=true) {
	global $flags,$solutions;
	$combo_trimmed = str_replace(',','',$combo);
	if ($stopatfirst==true) {
		if (count($solutions)>0)return ;
	}
	
	if ($combo_trimmed==$pattern) {
		array_push($solutions,$combo);
		return;
	}
	foreach ($flags as $flag) {
		$s = '';
		if ($combo_trimmed!='') $s = $combo.',';
		$s .= $flag;
		$st = $combo_trimmed.$flag;
		if ($st==substr($pattern,0,strlen($st))) findSolution($pattern,$s,$stopatfirst);
	}
}



if (function_exists('str_starts_with')==false) {
	function str_starts_with($a,$b) {
		if (strlen($b)>strlen($a)) return false;
		if (substr($a,0,strlen($b))==$b) return true;
		return false;
	}
}

function countAllSolutions($pattern,$level=0) {
	global $flags,$cache;
	if ($pattern=='') return 1;
	if (isset($cache[$pattern])==true) return $cache[$pattern];
	$result = 0;
	foreach ($flags as $flag) {
		if (str_starts_with($pattern,$flag)==true) $result += countAllSolutions(substr($pattern,strlen($flag)),$level+1);
	}
	$cache[$pattern]=$result;
	return $result;
}

$total = 0;
$total2 = 0;

function cmp($a,$b) {
	$al = strlen($a);$bl = strlen($b);
	if ($al==$bl) return 0;
	return ($al<$bl) ? 1 : -1;
}
// sort flags so that the towels with most stripes are used first 

usort($flags,'cmp');


$sol_all = array();
for ($i=2;$i<count($lines);$i++) {
	$pattern = $lines[$i];
	$solutions = array();
	findSolution($pattern);

	if (count($solutions)>0) {
		$total = $total+1;
		$sol_all[$pattern] = $solutions[0];
		$total2 = $total2 + countAllSolutions($pattern);
	}
}
echo "part 1 solution = ".$total."\n";
echo "part 2 solution = ".$total2."\n";



?>