<?php
define('CR',chr(0x0D));
define('LF',chr(0x0A));

$test = 1;
$input_file = ($test==0) ? __DIR__.'/inputs/07.txt' : __DIR__ .'/inputs/07_test.txt';

$input = file_get_contents($input_file);
$input = str_replace(CR.LF,LF,$input);
$input = trim($input,LF);

$lines = explode(LF,$input);

function find_solution($total, $values,$base=2) {
	$bits = count($values)-1;
	$max = pow($base,$bits);
	$newTotal = 0;
	for ($i=0;$i<$max;$i++) {
		$newTotal = intval($values[0]);
		$formula = $values[0];
		$bitstring = str_pad( base_convert($i,10,$base),$bits,'0',STR_PAD_LEFT);
		for ($j=0;$j<$bits;$j++) {
			$op = substr($bitstring,$j,1); // not quite bitstring, each char can be 0/1/2
			if ($op == '0') { $newTotal += intval($values[$j+1]); $formula.='+'.$values[$j+1]; }
			if ($op == '1') { $newTotal *= intval($values[$j+1]); $formula.='*'.$values[$j+1]; }
			if ($op == '2') { $newTotal = $newTotal . $values[$j+1]; $formula.='||'.$values[$j+1]; }
		}

		if ($newTotal == $total) {
			echo $formula ." = ".$newTotal."\n"; 
			return true;
		}
	}
	return false;
}

$sum1 = 0;
$sum2 = 0;
foreach ($lines as $line) {
	$parts = explode(':',$line);
	$total = intval($parts[0]);
	$values = explode(' ',trim($parts[1]));
	
	$result = find_solution($total,$values,2);
	if ($result==true) $sum1 += $total;
	$result = find_solution($total,$values,3);
	if ($result==true) $sum2 += $total;
}
echo "part 1 solution = $sum1 \n";
echo "part 1 solution = $sum2 \n";


?>