<?php
define('CR',chr(0x0D));
define('LF',chr(0x0A));

$test = 1;
$input_file = ($test==0) ? __DIR__.'/inputs/05.txt' : __DIR__ .'/inputs/05_test.txt';

$input = file_get_contents($input_file);
$input = str_replace(CR.LF,LF,$input);
$input = trim($input,LF);

$parts = explode(LF.LF,$input);
$rules = array();
$rules2= array();
$pages = array();

$lines = explode(LF,$parts[0]);
$pages = explode(LF,$parts[1]);

foreach ($lines as $idx =>$line) {
	$values = explode('|',$line);
	$a = intval($values[0]);
	$b = intval($values[1]);
	array_push($rules,[$a,$b]);
	if (isset($rules2[$a])==FALSE) $rules2[$a] = array();
	array_push($rules2[$a],$b);
}

function valid_pageset($pageline) {
	global $rules;
	$pages = explode(',',$pageline);
	$pe    = array_fill(0,100,100); // page not in pageset = 100, page in = index
	foreach ($pages as $idx => $pagenumber) {
		$pages[$idx] = intval($pagenumber);
		$pe[$pages[$idx]] = $idx;
	}
	//echo $pageline.' = ';
	foreach ($rules as $temp => $rule ) {
		$a = $rule[0];
		$b = $rule[1];
		if (($pe[$a]!=100) && ($pe[$b]!=100)) {
			if ($pe[$a]>=$pe[$b]) return 0;
		}
	}
	$number = $pages[intdiv(count($pages),2)];
	//echo $number."\n";
	return $number;
}

function fix_pageset($pageline) {
	global $rules;
	global $rules2;
	//echo "bad pageset = $pageline\n";
	$pages = explode(',',$pageline);
	$pe    = array_fill(0,100,100); // page not in pageset = 100, page in = index
	foreach ($pages as $idx => $pagenumber) {
		$pages[$idx] = intval($pagenumber);
		$pe[$pages[$idx]] = $idx;
	}
	//echo $pageline.' = ';
	foreach ($rules2 as $pageid => $beforePages ) {
		if ($pe[$pageid]!=100) {
			// the page we have rules for is in the page set
			// make sure the page is before all the other pages in the set
			$origPlace = $pe[$pageid];
			$newPlace = $origPlace;
			for ($i=0;$i<count($beforePages);$i++) {
				$pageid2 = $beforePages[$i];
				if ($pe[$pageid2]!=100) {
					if ($pe[$pageid2]<$newPlace) $newPlace=$pe[$pageid2];
				}
			}
			if ($origPlace!=$newPlace) {
			 //echo "page $pageid moves from offset $origPlace to offset $newPlace \n";
			 foreach ($pe as $idx =>$value) {
				if (($value>=$newPlace) && ($value!=100)) $pe[$idx] = $value+1;
			 }
			 $pe[$pageid]=$newPlace;
			}
		}
	}
	asort($pe);
	$text = '';
	foreach ($pe as $key=>$value) {
		if ($value!=100) $text .= $key.','; 
	}
	$text = trim($text,',');
	
	// echo "new page order is $text."\n";
	$parts = explode(',',$text);
	$nr = $parts[intdiv(count($parts),2)];
	return $nr;
}



$sum1 = 0;
$sum2 = 0;
foreach ($pages as $pageline) {
	 
	$result = valid_pageset($pageline);
	$sum1   += $result;
	if ($result==0) {
		$result2 = fix_pageset($pageline);
		$sum2 += $result2;
	}
}
echo "solution 1 = $sum1 \n";

echo "part 2 solution = $sum2 \n";

?>