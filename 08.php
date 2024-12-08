<?php
define('CR',chr(0x0D));
define('LF',chr(0x0A));

$test = 0;
$input_file = ($test==0) ? __DIR__.'/inputs/08.txt' : __DIR__ .'/inputs/08_test.txt';
$size = ($test==0) ? 50 : 12;

$input = file_get_contents($input_file);
$input = str_replace(array(CR,LF),array('',''),$input);

// build list of antennas
$antennas = array();
for ($j=0;$j<$size;$j++) {
 for ($i=0;$i<$size;$i++) {
	 $c = substr($input,$i+($j*$size),1);
	 if (($c!='.') && ($c!='#')) array_push($antennas,[$i,$j,$c]);
 }
}
//var_dump($antennas);
$p1 = array();
$p2 = array();
$x = 0;$y = 0;$c = '';
$nx =0;$ny= 0;$nc= '';
// go from top to bottom and find pairs of antennas 
for ($i=0;$i<count($antennas)-1;$i++) {
	list($x,$y,$c) = $antennas[$i];
	for ($j=$i+1;$j<count($antennas);$j++) {
		list($nx,$ny,$nc) = $antennas[$j];
		if (($nc==$c) && ($y!=$ny)) {
			$diffx = $nx-$x;
			$diffy = $ny-$y;
			$valid = true;
			$adiffx = abs($diffx);
			$adiffy = abs($diffy); 
			if (($adiffx==0) || ($adiffy==0)) $valid = false;
			if ($valid==true){ 
				//echo "$x,$y,$c potential pair with $nx,$ny,$nc diff= $diffx , $diffy \n";
				$x1= ($diffx > 0) ? ($x-$adiffx) : ($x+$adiffx); 
				$x2= ($diffx > 0) ? ($nx+$adiffx) : ($nx-$adiffx);
				$y1= $y-$adiffy;
				$y2= $ny+$adiffy;
				//echo "possible points $x1,$y1  and $x2,$y2 \n";
				$p2[$x.'|'.$y] =1;
				$p2[$nx.'|'.$ny] =1;
				
				if (($x1>=0) && ($x1<$size) && ($y1>=0) && ($y1<$size)) { $p1[$x1.'|'.$y1] =1; $p2[$x1.'|'.$y1] =1; }
				if (($x2>=0) && ($x2<$size) && ($y2>=0) && ($y2<$size)) { $p1[$x2.'|'.$y2] =1; $p2[$x2.'|'.$y2] =1; }
				while ( ($x1>=0) && ($x1<$size) && ($y1>=0) && ($y1<$size)) {
					$x1 = ($diffx > 0) ? ($x1-$adiffx) : ($x1+$adiffx); 
					$y1 = $y1-$adiffy;
					if (($x1>=0) && ($x1<$size) && ($y1>=0) && ($y1<$size)) $p2[$x1.'|'.$y1] =1;
				}
				while ( ($x2>=0) && ($x2<$size) && ($y2>=0) && ($y2<$size)) {
					$x2 = ($diffx > 0) ? ($x2+$adiffx) : ($x2-$adiffx); 
					$y2 = $y2+$adiffy;
					if (($x2>=0) && ($x2<$size) && ($y2>=0) && ($y2<$size)) $p2[$x2.'|'.$y2] = 1;
				}
			}
		}
	}
}
// print solution 1 
for ($j=0;$j<$size;$j++) {
 for ($i=0;$i<$size;$i++) {
	 $c = substr($input,$i+($j*$size),1);
	 if (isset($p1[$i.'|'.$j])==true) $c = '#';
	 echo $c;
 }
 echo "\n";
}

echo "Solution 1 = ".count($p1)."\n";

//print solution 2 
for ($j=0;$j<$size;$j++) {
 for ($i=0;$i<$size;$i++) {
	 $c = substr($input,$i+($j*$size),1);
	 if (isset($p2[$i.'|'.$j])==true) $c = '#';
	 echo $c;
 }
 echo "\n";
}


echo "Solution 2 = ".count($p2)."\n";

die();


?>