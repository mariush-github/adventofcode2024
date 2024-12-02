<?php
define('CR',chr(0x0D));
define('LF',chr(0x0A));
$input = file_get_contents(__DIR__ .'/inputs/02.txt');
$input = str_replace(CR.LF,LF,$input);
$input = trim($input,LF);
$lines = explode(LF,$input);

function valid_line($line) {
 
 $p = explode(' ',$line);
 
 $diffs = array();
 $a = intval($p[0]);
 for ($i=1;$i<count($p);$i++) {
	$b = intval($p[$i]);
	array_push($diffs,$b-$a);
	$a = $b;
 }
 sort($diffs);
 $min = 0;
 $max = count($diffs)-1;
 if (($diffs[$min]<0) && ($diffs[$max]>=0)) {
	//echo "decrease and increase or 0 diff\n"; 
	return false;
 } 
 for ($i=0;$i<count($diffs);$i++) {
	if ($diffs[$i]==0) { 
		//echo "one diff is 0";
		return false;
	}
	if (abs($diffs[$i])>3) { 
		//echo "one diff > 3\n";
		return false; 
	}
 }
 /*echo "ok\n"; */
 return true;
}

function validate_sublines($line) {
//echo "start subtest $line \n";
$a = explode(' ',$line);
for ($i=0;$i<count($a);$i++) {
	$b = $a;
	$b[$i]='';
	$newline = trim(implode(' ',$b),' ');
	$newline = str_replace('  ', ' ',$newline);
	//echo "subtest $newline \n";
	$result  = valid_line($newline);
	if ($result==true) return true;
}
return false;
	
}

$sum1 = 0;
$sum2 = 0;

foreach ($lines as $idx =>$line) {
 //echo $line."\n";
 
 $result = valid_line($line);
 if ($result == true) $sum1++;
 if ($result==false) {
	 $result = validate_sublines($line);
	 if ($result==true) $sum2++;
 }
}
$sum2 = $sum2+$sum1;
echo "part 1 solution = $sum1\n";
echo "part 2 solution = $sum2\n";


?>