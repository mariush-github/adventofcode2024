<?php
define('CR',chr(0x0D));
define('LF',chr(0x0A));

$test = 0;
$input_file = ($test==0) ? __DIR__.'/inputs/04.txt' : __DIR__ .'/inputs/04_test.txt';
$xmax = ($test==0) ? 140 : 10;
$ymax = $xmax;
 
$input = file_get_contents($input_file);
$input = str_replace(array(CR,LF),array('',''),$input);
//$input = trim($input,LF);

function getc($x,$y,$len=1) {
	global $input;
	global $xmax;
	global $ymax;
	if (($x<0) || ($x>=$xmax) || ($y<0) || ($y>=$ymax)) return ' ';
	return substr($input,($y*$xmax) + $x,$len);
}

$choices = array();
array_push($choices, [ [1,0],[2,0], [3,0] ]);  // horizontal -> 
array_push($choices, [ [-1,0],[-2,0], [-3,0] ]);  // horizontal <- 
array_push($choices, [ [0,1],[0,2], [0,3] ]);  // vertical  v 
array_push($choices, [ [0,-1],[0,-2], [0,-3] ]);  // vertical ^ 
array_push($choices, [ [1,1],[2,2], [3,3] ]);  // diagonal \ ->
array_push($choices, [ [1,-1],[2,-2], [3,-3] ]);  // diagonal / ->
array_push($choices, [ [-1,-1],[-2,-2], [-3,-3] ]);  // diagonal \ <-
array_push($choices, [ [-1,1],[-2,2], [-3,3] ]);  // diagonal / <-

$sum = 0;
$offset = strpos($input,'X');
while ($offset!==FALSE) {
	$j = intdiv($offset,$xmax);
	$i = $offset - ($j * $xmax);
	foreach ($choices as $idx => $ch) {
		$text = 'X' . getc($i+$ch[0][0],$j+$ch[0][1]).getc($i+$ch[1][0],$j+$ch[1][1]).getc($i+$ch[2][0],$j+$ch[2][1]);
		if ($text =='XMAS') $sum++;
	}
	$offset = strpos($input,'X',$offset+1);
}	

echo "part 1 solution = $sum \n";

$sum = 0;
$offset = strpos($input,'A');
while ($offset!==FALSE) {
	$j = intdiv($offset,$xmax);
	$i = $offset - ($j * $xmax);
	
	$text = getc($i-1,$j-1).getc($i+1,$j-1).getc($i-1,$j+1).getc($i+1,$j+1);
		
	// M M  M S  S M  S S   MMSS MSMS SMSM SSMM
	//  A    A    A    A
	// S S  M S  S M  M M
	if (($text =='MMSS') || ($text=='MSMS') ||($text=='SMSM') ||($text=='SSMM')) $sum++;

	$offset = strpos($input,'A',$offset+1);
}	


echo "part 2 solution = $sum \n";

?>