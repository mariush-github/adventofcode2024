<?php

define('CR',chr(0x0D));
define('LF',chr(0x0A));


$test = 1;
$input_file = ($test==0) ? __DIR__.'/inputs/20.txt' : __DIR__ .'/inputs/20_test.txt';
$size = ($test==0) ? 141 : 15;

$input = file_get_contents($input_file);
$map = str_replace([CR,LF],['',''],$input);
$scores = array();
$maxScore = 0;
$start = [0,0];
$stop  = [0,0];
$path = '';

function findMap($token) {
	global $map,$size;
	$pos = strpos($map,$token);
	if ($pos!==FALSE) {
		$y = intdiv($pos,$size);
		$x = $pos % $size;
		return [$x,$y];
	} else {
		return [-1,-1];
	}
}

function getMap($x,$y) {
	global $map,$size;
	if (($x<0) || ($y<0) ||($x>=$size) || ($y>=$size)) return '#';
	$c = substr($map,$y*$size+$x,1);
	if (($c=='S')||($c=='E')) $c = '.';
	return $c;
}

function setMap($x,$y,$c='.') {
	global $map,$size;
	if (($x<0) || ($y<0) ||($x>=$size) || ($y>=$size)) return ;
	$map[$y*$size+$x]=$c;
}



function displayMap() {
	global $map,$size,$path;
	echo '<table>';
	for ($j=0;$j<$size;$j++) {
		echo '<tr>';
		for ($i=0;$i<$size;$i++) {
			$m = getMap($i,$j);
			$score = ($m=='#') ? 9998 : getScore($i,$j);
			if ($score==9998) echo '<td>#</td>';
			if ($score==9999) echo '<td>.</td>';
			if ($score<9998) {
				//if (strpos($path,'['.$i.','.$j.']')!=FALSE) {
				if (onPath($i,$j)!=-1) {	
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
	global $scores,$maxScore;
	if (isset($scores[$y*1000+$x])==true) return false;
	$scores[$y*1000+$x]=$score;
	if ($maxScore<$score) $maxScore = $score;
	return true;
}
function getScore($x,$y) { 
	global $scores;
	return (isset($scores[$y*1000+$x])==false) ? 9999 : $scores[$y*1000+$x];
}



function calculateScores() {
	global $scores,$maxScore,$start,$stop;
	$scores = array();
	$scores[$start[1]*1000+$start[0]]=0;
	$maxScore = 0;
	$modified = true;
	$level = 0;
	while ($modified==true) {
		$modified = false;
		foreach ($scores as $sc_coord => $score ) {
			if ($score==$level) {
				$y = intdiv($sc_coord,1000);
				$x = $sc_coord % 1000;
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
function onPath($x,$y){ 
	global $path;
	if (($x<0) || ($y<0)) return -1;
	$code = '-'.str_pad($x,3,' ',STR_PAD_LEFT).str_pad($y,3,' ',STR_PAD_LEFT);
	$pos = strpos($path,$code);
	return ($pos===FALSE) ? -1 : $pos;
	
}

function buildPath(){
	global $scores,$path,$start,$stop;
	$scStop = getScore($stop[0],$stop[1]);
	$x = $stop[0];
	$y = $stop[1];
	if ($scStop==9999) return '';
	
	$path = '-'.str_pad($stop[0],3,' ',STR_PAD_LEFT).str_pad($stop[1],3,' ',STR_PAD_LEFT);
	$dir = [[0,-1],[0,1],[-1,0],[1,0]];
	$sc_prev = $scStop;
	$sc = $scStop;
	while ($sc!=0) {
		$sc_prev = $sc;
		$continue=true;
		for ($i=0; $i<4;$i++) { 
			if ($continue==true){
				$nx=$x+$dir[$i][0];
				$ny=$y+$dir[$i][1];
				$sc = getScore($nx,$ny);
				if ($sc==($sc_prev-1)) {
					$continue=false;
					$path = '-'.str_pad($nx,3,' ',STR_PAD_LEFT).str_pad($ny,3,' ',STR_PAD_LEFT).$path;
					$x=$nx;$y=$ny;
				}
			}
		}
	}
}
//displayMap();

//  use php.exe 18.php >out.html to see nice table with the path bolded.

echo '<html><head></head><body><pre>';
$scores = array();
$start = findMap('S');
$stop = findMap('E');
calculateScores();
buildPath();
displayMap();

echo "start position:";var_dump($start);
echo "stop position:";var_dump($stop);
echo "path = ".$path;
echo "<br/>\n";

$scoreStop = getScore($stop[0],$stop[1]);
echo "Path score is: ".$scoreStop."\n";

$cheats = array();
$dir = [[-1,0], [1,0],[0,-1],[0,1]];
 
for ($i=0;$i<$scoreStop;$i++) {
	$code = substr($path,$i*7,7);
	$x = intval(trim(substr($code,1,3)));
	$y = intval(trim(substr($code,4,3)));
	for ($j=0;$j<4;$j++) {
		if (getMap($x+$dir[$j][0],$y+$dir[$j][1])=='#') {
			$nx = $x + 2 * $dir[$j][0];
			$ny = $y + 2 * $dir[$j][1];
			if (onPath($nx,$ny)!=-1) {
				$nScore = getScore($nx,$ny);
				if ($nScore>$i) {
					$diff = $nScore - $i - 2;
					//echo "x=$x y=$y score=$i dirX=".$dir[$j][0]." dirY=".$dir[$j][1]." hits score $nScore, diff=$diff <br/>\n";
					if (isset($cheats[$diff])==false) $cheats[$diff] = 0;
					$cheats[$diff]++;
				}
			}
		}
	}
}
ksort($cheats);
$total = 0;
foreach ($cheats as $saved=>$qty) {
	if ($saved>=100) $total = $total+$qty;
}
echo "Part 1 solution = $total <br/>\n";

//var_dump($cheats);


$cheats = array();

 
for ($i=0;$i<$scoreStop;$i++) {
	$code = substr($path,$i*7,7);
	$x = intval(trim(substr($code,1,3)));
	$y = intval(trim(substr($code,4,3)));
	
	for ($n=$y-20;$n<=$y+20;$n++) {
		for ($m=$x-20;$m<=$x+20;$m++) {
			$length = abs($m-$x) + abs($n-$y);
			if (($length>=2) && ($length<=20)) {
				if (onPath($m,$n)!=-1) {
					$nScore = getScore($m,$n);
					if ($nScore>$i) {
						$diff = $nScore - $i - $length;
						//echo "x=$x y=$y score=$i m=$m n=$n hits score $nScore, diff=$diff <br/>\n";
						if (isset($cheats[$diff])==false) $cheats[$diff] = 0;
						$cheats[$diff]++;
					}
				}
			}
		}
	}
}
	
ksort($cheats);
$total = 0;
foreach ($cheats as $saved=>$qty) {
	if ($saved>=100) $total = $total+$qty;
}
echo "Part 2 solution = $total <br/>\n";

//var_dump($cheats);

?>