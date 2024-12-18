<?php
define('CR',chr(0x0D));
define('LF',chr(0x0A));


$test = 1;
$input_file = ($test==0) ? __DIR__.'/inputs/16.txt' : __DIR__ .'/inputs/16_test.txt';
$size = ($test==0) ? 141 : 15;

//$input = file_get_contents($input_file);
$input = file_get_contents($input_file);
$input = str_replace(array(CR,LF),array('',''),$input);


$maze = array_fill(0,$size*$size,0);
$mazeStart = [0,0];
$mazeFinish = [0,0];
$solutions = array();
$paths = array();

function loadMaze() {
	global $maze,$input,$size,$mazeStart, $mazeFinish;
	for ($j=0;$j<$size;$j++) {
		for ($i=0;$i<$size;$i++) {
			$c = substr($input,$j*$size+$i,1);
			if ($c=='S') {$mazeStart = [$i,$j]; $c='.'; }
			if ($c=='E') {$mazeFinish = [$i,$j]; $c='.'; }
			$maze[$j*$size + $i] = $c;
		}
	}
}

$nodes = array(); // points in maze where you can turn 90 degrees and/or continue forward
$paths = array(); // if a path between two nodes is horizontal or vertical ... 0 = horizontal 1 = vertical


function buildPaths() {
	global $maze,$size,$paths; 
	$hLines = array();
	$vLines = array();
	for ($y=0;$y<$size;$y++) {
		$text = ''; for ($x=0;$x<$size;$x++) $text.=$maze[$y*$size+$x]; $hLines[$y]=$text;
	}
	for ($x=0;$x<$size;$x++) {
		$text = ''; for ($y=0;$y<$size;$y++) $text.=$maze[$y*$size+$x]; $vLines[$x]=$text;
	}
	// horizontal paths
	for ($y=1;$y<($size-1);$y++) {
		$prev = $hLines[$y-1];
		$curr = $hLines[$y];
		$next = $hLines[$y+1];
		$points = array();
		$p = strpos($curr,'#..'); while($p!==FALSE) { array_push($points,$p+1); $p = strpos($curr,'#..',$p+1); }
		$p = strpos($curr,'..#'); while($p!==FALSE) { array_push($points,$p+1); $p = strpos($curr,'..#',$p+1); }
		if (count($points)>0) {
			$p = strpos($prev,'#.#'); while($p!==FALSE) { array_push($points,$p+1); $p = strpos($prev,'#.#',$p+1); }
			$p = strpos($next,'#.#'); while($p!==FALSE) { array_push($points,$p+1); $p = strpos($next,'#.#',$p+1); }
		}
		sort($points);
		$points = array_unique($points);
		sort($points);
		if (count($points)>1) {
			for ($i=1;$i<count($points);$i++) {
				$text = substr($hLines[$y],$points[$i-1],$points[$i]-$points[$i-1]+1);
				$textLen = strlen($text)-1;
				if (trim($text,'.')=='') {
					$pStart = $y*1000+$points[$i-1];
					$pStop = $y*1000+$points[$i];
					$paths[$pStart.':'.$pStop] = array('score'=>$textLen, 'direction'=>0);
					$paths[$pStop.':'.$pStart] = array('score'=>$textLen, 'direction'=>0);
					//echo $pStart.'-'.$pStop.':'.$textLen."\n";
				}
			}
		}
	}
	// vertical paths
	for ($x=1;$x<($size-1);$x++) {
		$prev = $vLines[$x-1];
		$curr = $vLines[$x];
		$next = $vLines[$x+1];
		$points = array();
		$p = strpos($curr,'#..'); while($p!==FALSE) { array_push($points,$p+1); $p = strpos($curr,'#..',$p+1); }
		$p = strpos($curr,'..#'); while($p!==FALSE) { array_push($points,$p+1); $p = strpos($curr,'..#',$p+1); }
		if (count($points)>0) {
			$p = strpos($prev,'#.#'); while($p!==FALSE) { array_push($points,$p+1); $p = strpos($prev,'#.#',$p+1); }
			$p = strpos($next,'#.#'); while($p!==FALSE) { array_push($points,$p+1); $p = strpos($next,'#.#',$p+1); }
		}
		sort($points);
		$points = array_unique($points);
		sort($points);
		if (count($points)>1) {
			for ($i=1;$i<count($points);$i++) {
				$text = substr($vLines[$x],$points[$i-1],$points[$i]-$points[$i-1]+1);
				$textLen = strlen($text)-1;
				if (trim($text,'.')=='') {
					$pStart = $points[$i-1]*1000+$x;
					$pStop =  $points[$i]*1000+$x;
					$paths[$pStart.':'.$pStop] = array('score'=>$textLen, 'direction'=>1);
					$paths[$pStop.':'.$pStart] = array('score'=>$textLen, 'direction'=>1);
					//echo $pStart.'-'.$pStop.':'.$textLen."\n";
				}
			}
		}
	}
}

function showMaze($useMaze = NULL) {
	global $maze,$input,$size;
	$m = ($useMaze==NULL) ? $maze: $useMaze;
	echo "\n";
	for ($j=0;$j<$size;$j++) {
		for ($i=0;$i<$size;$i++) {
			echo $m[$j*$size + $i];;
		}
		echo "\n";
	}
	echo "\n";
}


// try to filter dead ends so we don't waste time going in dead ends 
function filterMaze() {
	global $maze,$size,$mazeStart,$mazeFinish;
	$cnt = 1;
	while ($cnt>0) {
		$cnt=0;
		$maze[$mazeStart[1]*$size+$mazeStart[0]]='S';
		$maze[$mazeFinish[1]*$size+$mazeFinish[0]]='E';
		for ($y=1;$y<($size-1);$y++) {
			for ($x=1;$x<($size-1);$x++) {
				$c = $maze[$y*$size+$x];
				if ($c=='#') {
					if ( ($maze[($y-1)*$size+($x-1)]=='#') && ($maze[($y)*$size+($x-1)]=='.') && ($maze[($y+1)*$size+($x-1)]=='#') ) { $maze[$y*$size+$x-1] = '#'; $cnt++; }
					if ( ($maze[($y-1)*$size+($x+1)]=='#') && ($maze[($y)*$size+($x+1)]=='.') && ($maze[($y+1)*$size+($x+1)]=='#') ) { $maze[$y*$size+$x+1] = '#'; $cnt++; }
					if ( ($maze[($y-1)*$size+($x-1)]=='#') && ($maze[($y-1)*$size+($x)]=='.') && ($maze[($y-1)*$size+($x+1)]=='#') ) { $maze[($y-1)*$size+$x] = '#'; $cnt++; }
					if ( ($maze[($y+1)*$size+($x-1)]=='#') && ($maze[($y+1)*$size+($x)]=='.') && ($maze[($y+1)*$size+($x+1)]=='#') ) { $maze[($y+1)*$size+$x] = '#'; $cnt++; }
				}
			}
		}
		$maze[$mazeStart[1]  * $size + $mazeStart[0] ] = '.';
		$maze[$mazeFinish[1] * $size + $mazeFinish[0]] = '.';
		//showMaze();
	}
}

function calculateScoreRoute($route) {
	global $paths; 
	$d = 0;
	$s = 0;
	for ($i=1;$i<count($route);$i++) {
		$code = $route[$i-1].':'.$route[$i];
		$s = $s + $paths[$code]['score'];
		if ($paths[$code]['direction']!=$d) $s = $s + 1000;
		$d = $paths[$code]['direction'];
	}
	return $s;
}


loadMaze();
showMaze();
filterMaze();
showMaze();
buildPaths();

$routes = array();
$solutions = array();

$codeStart = $mazeStart[1] *1000 + $mazeStart[0];
$codeStop  = $mazeFinish[1]*1000 + $mazeFinish[0];
$cache = array();

array_push($routes,[$codeStart]);

while (count($routes)>0) {
	$route = array_shift($routes);
	$lastNode = $route[count($route)-1];
	if ($lastNode==$codeStop) {
		array_push($solutions,$route);
	} else {
		$continue = true;
		if (count($route)>1) {
			$score = calculateScoreRoute($route);
			if (isset($cache[$lastNode])==false) {
				$cache[$lastNode]=$score;
			} else {
				if ($cache[$lastNode]<$score) { $continue=false; /*echo "cache : skipping route, score = $score, already route with score = ".$cache[$lastNode]."\n"; */}
			}
		}
		if ($continue==true) {
			if (count($route)>1) $cache[$lastNode]=$score;
			$connections = array();
			foreach ($paths as $pathCodes => $pathInfo) {
				list($a,$b) = explode(':',$pathCodes);
				if ($a==$lastNode) {
					if (array_search($b,$route)==false) array_push($connections,$b);
				}
			}
			if (count($connections)>0) {
				foreach ($connections as $connection) {
					$newRoute = $route; array_push($newRoute,$connection); array_push($routes,$newRoute);
				}
			}
		}
	}
	
}



$scores = array();
foreach ($solutions as $idx => $sol) {
	$score = calculateScoreRoute($sol);array_push($scores,$score);
	echo $idx .' : '. json_encode($sol).' = '.$score."\n";
}
sort($scores);
echo "Part 1 solution, lowest score = ".$scores[0]."\n";

$desiredScore = $scores[0];


?>