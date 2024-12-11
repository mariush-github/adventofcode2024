<?php
define('CR',chr(0x0D));
define('LF',chr(0x0A));


$test = 0;
$input_file = ($test==0) ? __DIR__.'/inputs/11.txt' : __DIR__ .'/inputs/11_test.txt';


$input = file_get_contents($input_file);
$input = str_replace(array(CR,LF),array('',''),$input);


function build_array($text) {
	$list = array();
	$segments = explode(' ',$text);
	foreach ($segments as $segment) {
		if (isset($list[$segment])==FALSE) $list[$segment]=0;
		$list[$segment]++;
	}
	return $list;
}

function blink_array($list) {
	$l = $list;
	$l2 = array();
	foreach ($l as $value => $count) {
		$length = strlen($value);
		$half = intdiv($length,2);
		if ($value=='0') {
			if (isset($l2['1'])==FALSE) $l2['1']=0;
			$l2['1'] += $count;
		} else {
			if ($length==($half*2)) {
				$a = substr($value,0,$half);
				$b = substr($value,$half, $half);
				$b = ltrim($b,'0');
				if ($b=='') $b='0';
				if (isset($l2[$a])==FALSE) $l2[$a] = 0;
				if (isset($l2[$b])==FALSE) $l2[$b] = 0;
				$l2[$a]+=$count;
				$l2[$b]+=$count;
				
			} else {
				$a = bcmul($value,'2024');
				if (isset($l2[$a])==FALSE) $l2[$a]=0;
				$l2[$a]+=$count;
			}
		}
	}
	return $l2;
}

$l  = build_array($input);


for ($i=1;$i<76;$i++) {
	$l = blink_array($l);
	$total = 0;
	foreach ($l as $value => $count) {
		$total = bcadd($total,$count);
	}
	if ($i==25) echo "Solution part 1 after blinking $i times : $total \n";
	if ($i==75) echo "Solution part 2 after blinking $i times : $total \n";
}


?>