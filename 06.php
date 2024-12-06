<?php
define('CR',chr(0x0D));
define('LF',chr(0x0A));

$test = 0;
$input_file = ($test==0) ? __DIR__.'/inputs/06.txt' : __DIR__ .'/inputs/06_test.txt';
$size = ($test==0) ? 130 : 10;

$input = file_get_contents($input_file);
$input = str_replace(array(CR,LF),array('',''),$input);


function test_path($add_obstacle=false,$obsx=0,$obsy=0) {
	global $input;
	global $size;
	$offset = strpos($input,'^');
	$y = intdiv($offset,$size);
	$x = $offset - ($y*$size);
	$dir = 'u';
	$continue = true;
	$loop = false;
	$path = array();
	$path[$x.'-'.$y] = 1;
	while ($continue==true) {
		if ($dir=='u') { $nx = $x;$ny = $y-1; }
		if ($dir=='d') { $nx = $x;$ny = $y+1; }
		if ($dir=='r') { $nx = $x+1;$ny = $y; }
		if ($dir=='l') { $nx = $x-1;$ny = $y; }
		// if we're out of map, stop
		if (($nx<0) || ($nx==$size) || ($ny<0) || ($ny==$size)) $continue=false;
		// otherwise see if it's obstacle and change direction if needed  
		if ($continue==true) {
			$cell = substr($input,($nx + ($ny*$size)),1);
			if ($add_obstacle==true) { // pretend there's an obstacle if we want to attempt a loop
				if (($nx==$obsx) && ($ny==$obsy)) $cell = '#';
			}
			if ($cell=='#') {
				// change direction 
				$newdir = array('u'=>'r','r'=>'d','d'=>'l','l'=>'u');
				$dir = $newdir[$dir];
			} else {
				// this new cell is a valid location, so update our path and position
				if (isset($path[$nx.'-'.$ny])==false) $path[$nx.'-'.$ny] = 0;
				$path[$nx.'-'.$ny]++;
				// lazy loop check - if we crossed through this 100 times we're probably in a loop
				if ($path[$nx.'-'.$ny]>100) {$loop= true; $continue=false; }
				$x = $nx;
				$y = $ny;
				
			}
		}
	}
	return array( 'path'=>$path, 'loop'=>$loop, 'count'=>count($path));
}

//echo "position is $x,$y \n";;
$data = test_path(false);
echo "solution 1 = ".$data['count']."\n";

// we have original path, so obstacles could only be placed on this path,
// so we put all the points in an array and go backwards placing obstacles
// and see if we get a loop.
$sum = 0;

$offset = strpos($input,'^');
$y = intdiv($offset,$size);
$x = $offset - ($y*$size);


$path = array();
foreach ($data['path'] as $coords =>$counter) {
	$co = explode('-',$coords);
	$co[0] = intval($co[0]); 
	$co[1]=intval($co[1]);
	$no = $co[0] + $co[1]*$size; 
	// don't add the origin point
	if ($offset!=$no) {
		array_push($path,[$co[0],$co[1]]);
	}
}
for ($j=count($path)-1;$j>=0;$j--) {
	$data = test_path(true,$path[$j][0],$path[$j][1]);
	if ($data['loop']=='true') {
		echo "loop detected by adding obstacle at ". json_encode($path[$j])." \n";
		$sum++;
	}
}

echo "solution 2 = ".$sum."\n";

//var_dump($path);
//var_dump($data);
//$data  = test_path(true,3,6);
//var_dump($data);

die();

?>