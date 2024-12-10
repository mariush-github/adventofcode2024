<?php
define('CR',chr(0x0D));
define('LF',chr(0x0A));

$test = 1;
$input_file = ($test==0) ? __DIR__.'/inputs/10.txt' : __DIR__ .'/inputs/10_test.txt';
$size = ($test==0) ? 40 : 8;

$input = file_get_contents($input_file);
$input = str_replace(array(CR,LF),array('',''),$input);

$list = array();
$results = array();
$scores = array();

$p = strpos($input,'0');
while ($p!==FALSE) {
	$scores[$p]='';
	$l = array([$p,0]);
	array_push($list,$l);
	$p = strpos($input,'0',$p+1);
}

$x = 0;
$y = 0;
$z = 0;

function getLevel($x,$y) {
	global $size;
	global $input;
	if (($x<0) || ($x>=$size) || ($y<0) || ($y>=$size)) return -1;
	$c = substr($input,$x + ($y * $size),1);
	if ($c==-1) return -1;
	return intval($c);
}

while (count($list)>0) {
	$l = array_shift($list);
	$lc = count($l)-1;
	$pos = 0;
	$z   = 0;
	
	list($pos,$z) = $l[$lc];
	if ($z == 9) {
		array_push($results,$l);
		$ps = $l[0][0];
		if (strpos($scores[$ps],'-'.$pos.'-')===FALSE) $scores[$ps] .= '-'.$pos.'-'; 
	} else {
		$j = intdiv($pos,$size); $i = $pos - ($j*$size);
		$nz = getLevel($i-1,$j); if ($nz==($z+1)) { $nl = $l; array_push($nl,[$pos-1,$nz]);array_push($list,$nl); }
		$nz = getLevel($i+1,$j); if ($nz==($z+1)) { $nl = $l; array_push($nl,[$pos+1,$nz]);array_push($list,$nl); }
		$nz = getLevel($i,$j-1); if ($nz==($z+1)) { $nl = $l; array_push($nl,[$pos-$size,$nz]);array_push($list,$nl); }
		$nz = getLevel($i,$j+1); if ($nz==($z+1)) { $nl = $l; array_push($nl,[$pos+$size,$nz]);array_push($list,$nl); }
	}
}

$sum = 0;
foreach ($scores as $score) {
	$offsets = explode('--',trim($score,'-'));
	$sum+= count($offsets);
}
echo "solution 1 = $sum \n";
echo "solution 2 = ".count($results)."\n";


?>