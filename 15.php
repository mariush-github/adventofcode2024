<?php

define('CR',chr(0x0D));
define('LF',chr(0x0A));

$test = 0;
$input_file = ($test==0) ? __DIR__.'/inputs/15.txt' : __DIR__ .'/inputs/15_test.txt';
$size = ($test==0) ? 50 : 10;

$input = file_get_contents($input_file);
$input = str_replace(CR,'',$input);

// PART 1 
$parts = explode(LF.LF,$input);
$mapstring = str_replace(LF,'',$parts[0]);
$path = str_replace(LF,'',$parts[1]);
$robot = [0,0];
$map = array();
for ($j=0;$j<$size;$j++) {
	$map[$j] = array();
	for ($i=0;$i<$size;$i++) {
		$offset = ($j*$size) + $i;
		$c = substr($mapstring,$offset,1);
		if ($c=='@') {
			$robot = [$i,$j];
			$map[$j][$i]= '.';
		} else {
			$map[$j][$i] = $c;
		}
		
	}
}
function findFirstSpace($offsetX,$offsetY) {
	global $map,$robot; 
	$x = $robot[0]+$offsetX;
	$y = $robot[1]+$offsetY;
	$count = 0;
	while (1==1) {
		$c = $map[$y][$x];
		if ($c=='#') return [-1,-1,-1]; // x,y,jumps
		if ($c=='.') return [$x,$y,$count];
		$x = $x+$offsetX;
		$y = $y+$offsetY;
		$count++;
	}
}

function displayMap(){ 
 global $map,$size,$robot,$sum,$test;
 $sum = 0;
 for ($j=0;$j<$size;$j++) {
	 for ($i=0;$i<$size;$i++) {
		 if (($robot[0]==$i) && ($robot[1]==$j)) {
			 if ($test==1) echo '@';
		 } else {
			 if ($test==1) echo $map[$j][$i];
			 if ($map[$j][$i]=='O') $sum = $sum + $i+$j*100;
		 }
	 }
	 if ($test==1) echo "\n";
 }
 if ($test==1) echo "\n";
}




$sum = 0;
for ($k = 0;$k<strlen($path);$k++) {
	$p = substr($path,$k,1);
	
	$dir = array('^'=> [0,-1] , '>'=>[1,0], 'v' =>[0,1] , '<' => [-1,0] );
	$ox = $dir[$p][0];
	$oy = $dir[$p][1];
	list($x,$y,$count) = findFirstSpace($ox,$oy);
	if (($x!=-1) && ($y!=-1)) { // it's possible to move
		if ($count==0) {		// cell next to the robot is a space
			$robot[0] += $ox;
			$robot[1] += $oy;
		} else {				// some crates between robot and empty space, shift the crates.
			if ($p=='<') { for ($i=$x;$i<$robot[0];$i++) { $map[$y][$i]=$map[$y][$i+1]; $map[$y][$i+1]='.'; } }
			if ($p=='>') { for ($i=$x;$i>$robot[0];$i--) { $map[$y][$i]=$map[$y][$i-1]; $map[$y][$i-1]='.'; } }
			
			if ($p=='^') { for ($i=$y;$i<$robot[1];$i++) { $map[$i][$x]=$map[$i+1][$x]; $map[$i+1][$x]='.'; } }
			if ($p=='v') { for ($i=$y;$i>$robot[1];$i--) { $map[$i][$x]=$map[$i-1][$x]; $map[$i-1][$x]='.'; } }
			$robot[0] += $ox;
			$robot[1] += $oy;
		}
	}
	//echo "Direction = $p.\n";
	
	//echo "Sum = $sum\n";
	//if ($k==5) die();
}
displayMap();
echo "Part 1 solution = $sum\n";



// PART 2 


// crate[0] = robot position , crates start from 1, walls have IDs starting from 1001
$crates = array();
$crateID = 0;
$wallID = 1000;
for ($j=0;$j<$size;$j++) {
	for ($i=0;$i<$size;$i++) {
		$offset = ($j*$size) + $i;
		$c = substr($mapstring,$offset,1);
		if ($c=='@') $crates[0] = [$i*2,$j];
		if ($c=='#') { $wallID++ ; $crates[$wallID]  = [$i*2,$j]; }
		if ($c=='O') { $crateID++; $crates[$crateID] = [$i*2,$j]; }
	}
}
ksort($crates);

function findCrate($x,$y) {
	global $crates;
	foreach ($crates as $idx => $crate) {
		if (($idx!=0) && ($crate[1]==$y)) {
			if (($crate[0]==$x) || ($crate[0]==($x-1))) return $idx;
		}
	}
	return 0;
}

function canMove($crateID,$direction) {
	global $crates;
	if ($crateID>1000) return false;
	// get coordinates of crate
	list($x,$y) = $crates[$crateID];
	//echo "crate $crateID at x=$x y=$y \n";
	$c1 = 0;
	$c2 = 0;
	if ($direction=='<') $c1 = findCrate($x-1,$y);
	if ($direction=='>') $c1 = findCrate($x+2,$y);
	if ($direction=='^') { $c1 = findCrate($x,$y-1); $c2 = findCrate($x+1,$y-1); }
	if ($direction=='v') { $c1 = findCrate($x,$y+1); $c2 = findCrate($x+1,$y+1); }
	//echo "c1 = $c1 c2= $c2 \n";
	if (($direction=='<') || ($direction=='>')) return ($c1==0) ? true : canMove($c1,$direction);
	if (($direction=='^') || ($direction=='v')) {
		if ($c1==$c2) {
			return ($c1==0) ? true : canMove($c1,$direction);
		} else {
			$a = canMove($c1,$direction); $b = canMove($c2,$direction);
			if (($a==false) || ($b==false)) return false;
			return true;
		}
	}
	echo "ret";
	return false;
}

function moveCrate($crateID,$direction) {
	global $crates;
	if ($crateID>1000) return false;
	list($x,$y) = $crates[$crateID];
	$c1 = 0;
	$c2 = 0;
	if ($direction=='<') $c1 = findCrate($x-1,$y);
	if ($direction=='>') $c1 = findCrate($x+2,$y);
	if ($direction=='^') { $c1 = findCrate($x,$y-1); $c2 = findCrate($x+1,$y-1); }
	if ($direction=='v') { $c1 = findCrate($x,$y+1); $c2 = findCrate($x+1,$y+1); }
	if (($direction=='<') || ($direction=='>')) {
		if ($c1!=0) $result = moveCrate($c1,$direction);
	}
	if (($direction=='^') || ($direction=='v')) {
		if ($c1!=0) $result = moveCrate($c1,$direction);
		if (($c2!=0) && ($c2!=$c1)) $result = moveCrate($c2,$direction);
	}
	if ($direction=='<') $crates[$crateID][0] = $crates[$crateID][0]-1;
	if ($direction=='>') $crates[$crateID][0] = $crates[$crateID][0]+1;
	if ($direction=='^') $crates[$crateID][1] = $crates[$crateID][1]-1;
	if ($direction=='v') $crates[$crateID][1] = $crates[$crateID][1]+1;
}

function displayMap2() {
	global $crates,$size,$sum;
	$map = array_fill(0,2*$size*$size,'.');
	$sum = 0;
	foreach ($crates as $idx =>$crate) {
		if ($idx!=0) { 
			list($x,$y) = $crate;
			if ($idx<1000) {
				$map[$x + ($y*$size*2)] = '[';
				$map[$x + 1 + ($y*$size*2)] = ']';
				$sum = $sum + $x+$y*100;
			} else {
				$map[$x + ($y*$size*2)] = '#';
				$map[$x + 1 + ($y*$size*2)] = '#';
			}
		}
	}
	for ($j=0;$j<$size;$j++) {
		for ($i=0;$i<$size*2;$i++) {
			echo $map[$j*$size*2 + $i];
		}
		echo "\n";
	}
	echo "\n";
}

displayMap2();

list($x,$y) = $crates[0];
for ($k = 0;$k<strlen($path);$k++) {
	$p = substr($path,$k,1);
	$moving = false;
	if ($p=='<') $c = findCrate($x-1,$y); 
	if ($p=='>') $c = findCrate($x+1,$y); 
	if ($p=='^') $c = findCrate($x,$y-1); 
	if ($p=='v') $c = findCrate($x,$y+1); 
	
	if ($c==0) $moving=true;
	if ($c!=0) { $moving = canMove($c,$p); if ($moving==true) moveCrate($c,$p);}
		
	if ($moving == true) {
		if ($p=='<') $x = $x-1; 
		if ($p=='>') $x = $x+1; 
		if ($p=='^') $y = $y-1; 
		if ($p=='v') $y = $y+1; 		
	}
}
displayMap2();

echo "Part 2 solution = $sum\n";

//1543780

?>