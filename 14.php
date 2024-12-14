<?php

define('CR',chr(0x0D));
define('LF',chr(0x0A));

$test = 0;
$input_file = ($test==0) ? __DIR__.'/inputs/14.txt' : __DIR__ .'/inputs/14_test.txt';
$width = ($test==1) ? 11 : 101;
$height = ($test==1) ? 7 : 103;

// x,y , m,n start and end coordinates, 
$quadrants = array();
$quadrants[0] = [0,0,intdiv($width,2)-1,intdiv($height,2)-1];
$quadrants[1] = [intdiv($width,2)+1,0,$width-1,intdiv($height,2)-1];
$quadrants[2] = [0,intdiv($height,2)+1,intdiv($width,2)-1,$height-1];
$quadrants[3] = [intdiv($width,2)+1,intdiv($height,2)+1,$width-1,$height-1];

$input = file_get_contents($input_file);
$input = str_replace(CR,'',$input);
$input = str_replace(['p=',' v='],['',','],$input);

function displayRobot() {
	global $robots,$width,$height;
	$data = array_fill(0,$width*$height,'.');
	foreach ($robots as $info) {
		$offset = $info[1] * $width + $info[0];
		$data[$offset] = '*';
	}
	$text = implode('',$data);
	for ($i=0;$i<$height;$i++) {
		echo substr($text, $i*$width,$width)."\n";
	}
	if (strpos($text,'********')!==FALSE) echo "!!potential easter egg!!";
	echo "\n";
}

function updateRobot($info) {
	global $width,$height;
	$x = intval($info[0]);
	$y = intval($info[1]);
	$ox = intval($info[2]);
	$oy = intval($info[3]);
	$x += $ox;
	$y += $oy;
	if (($x<0) || ($x>=$width)) {
		$x = ($x<0) ? ($x+$width) : ($x-$width);
	}
	if (($y<0) || ($y>=$height)) {
		$y = ($y<0) ? ($y+$height) : ($y-$height);
	}
	return [$x,$y,$ox,$oy];
}


$lines = explode(LF,$input);
$robots = array();
foreach ($lines as $line) {
	if (trim($line)!='') array_push($robots,explode(',',$line));
}

for ($i=0;$i<100;$i++) {
	foreach ($robots as $idx =>$info) {
		$robots[$idx] = updateRobot($info);
	}
	//echo "update $i:\n";
	//displayRobot();
}
echo "part 1 solution: \n";
$sum = 1;
foreach ($quadrants as $qid => $quad) {
	list($x,$y,$m,$n) = $quad;
	$total = 0;
	foreach ($robots as $idx =>$robot) {
		if (($robot[0]>=$x) && ($robot[0]<=$m) && ($robot[1]>=$y) && ($robot[1]<=$n)) $total++;
	}
	echo "Quad $qid = $total\n";
	$sum = $sum * $total;
	
}
echo "Total = $sum\n";

// dump output to a text file ex php 14.php >out.txt and search for "potential easter egg" 

for ($i=101;$i<10000;$i++) {
	foreach ($robots as $idx =>$info) {
		$robots[$idx] = updateRobot($info);
	}
	echo "update $i:\n";
	displayRobot();
}


?>