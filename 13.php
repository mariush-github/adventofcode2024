<?php

define('CR',chr(0x0D));
define('LF',chr(0x0A));

$test = 0;
$input_file = ($test==0) ? __DIR__.'/inputs/13.txt' : __DIR__ .'/inputs/13_test.txt';

$input = file_get_contents($input_file);
$input = str_replace(CR,'',$input);
$input = str_replace(['Button A:','Button B:','Prize:','X=','Y=','X+','Y+',' '],['','','','','','','','',''],$input);

$sequences = explode(LF.LF,$input);
$problems = array();
foreach ($sequences as $sequence) {
	$problem = array();
	$values = str_replace(LF,',',$sequence);
	$values = explode(',',$values);
	foreach ($values as $idx=>$value) { $values[$idx]=intval($value); }
	array_push($problem,3,1,$values[0],$values[1],$values[2],$values[3],$values[4],$values[5]);
	array_push($problems,$problem);
}


/*
Button A: X+94, Y+34
Button B: X+22, Y+67
Prize: X=8400, Y=5400
*/

// n x 94 + m x 22 = 8400 
// n x 34 + m x 67 = 5400

// 22 = 2 x 11
// 67 = 67
// 1474 = 2 x 11 x 67

// n x 6298 + m x 1474 = 562,800
// n x 748 + m x 1474 = 118800
// n x 5550 = 444,000 => n = 80


function solveProblem2($p,$modifier=false) {
	global $test;
	list($c1,$c2,$x1,$y1,$x2,$y2,$tx,$ty) = $p;
	if ($modifier==true) {
		$tx += intval(10000000000000);
		$ty += intval(10000000000000);
	}
	if ($test==1) echo "n x $x1 + m x $x2 = $tx\nn x $y1 + m x $y2 = $ty\n\n";
	$a1 = bcmul($x1,$y2); $b1 = bcmul($x2,$y2);
	$a2 = bcmul($x2,$y1); $b2 = bcmul($x2,$y2);
	$t1 = bcmul($tx,$y2);
	$t2 = bcmul($ty,$x2);
	if ($test==1) echo "n x $a1 + m x $b1 = $t1\nn x $a2 + m x $b2 = $t2\n\n";
	
	$order = bccomp($t1,$t2);
	if($order >=0) {
		$a = bcsub($a1,$a2);$t = bcsub($t1,$t2);
	} else {
		$a = bcsub($a2,$a1);$t = bcsub($t2,$t1);
	}
	
	$x = bcdiv($t,$a);
	$r = bcmod($t,$a);

	if ($r!=0) return array();
	
	$rest = bcsub($tx,bcmul($x1,$x));

	$y = bcdiv($rest,$x2);
	$r = bcmod($rest,$x2);
	if ($r!=0) return array();
	$solution = array("$x x $c1 , $y x $c2"=> bcadd( bcmul($x,$c1), bcmul($y,$c2)) );
	return $solution;
}

function SolveProblem($p,$modifier=false) {
	
	$solutions = array();
	list($c1,$c2,$x1,$y1,$x2,$y2,$tx,$ty) = $p;
	
	$max1 = intdiv($ty,$y1);
	while ($max1>=0) {
		$rest = $ty-($max1*$y1);
		$max2 = intdiv($rest,$y2);
		if (($max2*$y2) == $rest) {
			$x = ($max1*$x1) + ($max2*$x2);
			$y = ($max1*$y1) + ($max2*$y2);
			if (($x==$tx) && ($y==$ty) ) $solutions["$max1 x $c1 , $max2 x $c2"] = ($max1*$c1) + ($max2*$c2);
		}
		$max1--;
	}
	asort($solutions);
	//var_dump($solutions);
	return $solutions;
}

$sum1 = 0;
$sum2 = 0;

foreach ($problems as $problem) {
	$solutions = SolveProblem($problem);
	if (count($solutions)>0) {
		$key = array_key_first($solutions);
		$sum1 += $solutions[$key];
	}
	$solutions = SolveProblem2($problem,true);
	if (count($solutions)>0) {
		$key = array_key_first($solutions);
		$sum2 = bcadd($sum2,$solutions[$key]);
	}	
}
echo "Solution 1 = $sum1\n";
echo "Solution 2 = $sum2\n";
?>