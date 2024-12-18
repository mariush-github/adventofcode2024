<?php

define('CR',chr(0x0D));
define('LF',chr(0x0A));

$test = 1;
$input_file = ($test==0) ? __DIR__.'/inputs/18.txt' : __DIR__ .'/inputs/18_test.txt';
$size = ($test==0) ? 70 : 6;
$count = ($test==0) ? 1024 : 12;

$input = file_get_contents($input_file);
$input = str_replace(CR,'',$input);

$lines = explode(LF,$input);


function displayMap() {
	global $map,$size,$path;
	echo '<table>';
	for ($j=0;$j<=$size;$j++) {
		echo '<tr>';
		for ($i=0;$i<=$size;$i++) {
			$score = ($map[$j][$i]=='#') ? 998 : getScore($i,$j);
			if ($score==998) echo '<td>#</td>';
			if ($score==999) echo '<td>.</td>';
			if ($score<998) {
				if (strpos($path,'['.$i.','.$j.']')!=FALSE) {
					echo '<td><b>'.$score.'</b></td>'; //str_pad($score,4,'-',STR_PAD_LEFT);
				} else {
					echo '<td>'.$score.'</td>'; //str_pad($score,4,'-',STR_PAD_LEFT);
				}
			}
		}
		echo "</tr>\n";
	}
	echo '</table>';
	
}



function setScore($x,$y,$score) {
	global $scores,$size,$maxScore;
	if (isset($scores[$y*100+$x])==true) return false;
	$scores[$y*100+$x]=$score;
	if ($maxScore<$score) $maxScore = $score;
	return true;
}
function getScore($x,$y) { 
	global $scores;
	return (isset($scores[$y*100+$x])==false) ? 999 : $scores[$y*100+$x];
}

function getMap($x,$y) {
	global $map,$size;
	if (($x<0) || ($y<0) ||($x>$size) || ($y>$size)) return '#';
	return $map[$y][$x];
}

function calculateScores() {
	global $scores,$maxScore;
	$scores = array();
	$scores[0]=0;
	$maxScore = 0;
	$modified = true;
	$level = 0;
	while ($modified==true) {
		$modified = false;
		foreach ($scores as $sc_coord => $score ) {
			if ($score==$level) {
				$y = intdiv($sc_coord,100);
				$x = $sc_coord % 100;
				//echo $x.':'.$y.':'.$score."\n";
				if (getMap($x-1,$y)!='#') if (setScore($x-1,$y,$score+1)==true) $modified=true;
				if (getMap($x+1,$y)!='#') if (setScore($x+1,$y,$score+1)==true) $modified=true;
				if (getMap($x,$y-1)!='#') if (setScore($x,$y-1,$score+1)==true) $modified=true;
				if (getMap($x,$y+1)!='#') if (setScore($x,$y+1,$score+1)==true) $modified=true;
			}
		}
		if ($modified==true) $level++;
		//displayMap();
	}
}
//displayMap();

//  use php.exe 18.php >out.html to see nice table with the path bolded.

echo '<html><head></head><body><pre>';
$scores = array();
$scores[0]=0;
$maxScore = 0;

$map = array();
for ($i=0;$i<=$size;$i++) {
	$map[$i] = array();
	for ($j=0;$j<=$size;$j++) $map[$i][$j] = '.';
}

for ($i=0;$i<$count;$i++) {
	list($a,$b) = explode(',',$lines[$i]);
	$map[intval($b)][intval($a)] = '#';
}

calculateScores();

echo "Part 1 solution = ".getScore($size,$size)."<br/>\n";

$path = '['.$size.','.$size.']';
$level = $scores[$size*100+$size];
$x = $size;$y=$size;
while ($level!=0) {
	$found = false; 
	if ($found==false) {$a=$x-1;$b=$y; $l=$level-1; if (getScore($a,$b)==$l) {$found=true;$x=$a;$y=$b; $level--;$path = "[$a,$b]".$path;}}
	if ($found==false) {$a=$x+1;$b=$y; $l=$level-1; if (getScore($a,$b)==$l) {$found=true;$x=$a;$y=$b; $level--;$path = "[$a,$b]".$path;}}
	if ($found==false) {$a=$x;$b=$y-1; $l=$level-1; if (getScore($a,$b)==$l) {$found=true;$x=$a;$y=$b; $level--;$path = "[$a,$b]".$path;}}
	if ($found==false) {$a=$x;$b=$y+1; $l=$level-1; if (getScore($a,$b)==$l) {$found=true;$x=$a;$y=$b; $level--;$path = "[$a,$b]".$path;}}
	
}
echo "Part 1 path = ".$path."\n";
displayMap();

for ($i=$count+1;$i<count($lines);$i++) {
	list($a,$b) = explode(',',$lines[$i]);
	$map[$b][$a] = '#';
	calculateScores();
	echo "<p>Line $i adding $a:$b new path length = ".getScore($size,$size)."</p>\n";
	if (getScore($size,$size)==999) {
		echo "Part 2 solution : Pixel at coordinates $a,$b on line $i will block path\n";
		displayMap();
		echo "</pre></body></html>";
		
		die();
	}
	
}


?>