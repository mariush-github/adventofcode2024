<?php
define('CR',chr(0x0D));
define('LF',chr(0x0A));
$input = file_get_contents(__DIR__ .'/inputs/03.txt');
//$input = str_replace(CR.LF,LF,$input);
//$input = trim($input,LF);
$sum = 0;
function calculate($text) {
	$sum = 0;
	$pos = strpos($text,'mul(');
	while ($pos!==FALSE){
		$pos1 = strpos($text,')',$pos+1);
		$pair = substr($text,$pos+4,$pos1-$pos-4);
		$parts = explode(',',$pair);
		if (count($parts)==2) {
			if ((ctype_digit($parts[0])==true) && (ctype_digit($parts[1])==true)){
				$prod = intval($parts[0]) * intval($parts[1]);  
				$sum += $prod;
			}
		}
		$pos = strpos($text,'mul(',$pos+1);
	}
	return $sum;
}
$sum = calculate($input);
echo "part 1 solution = $sum \n";
$sum = 0;
$input .= 'don\'t()';
$chunks = array();
$start = 0;
$finish= 0;
while ($start!==FALSE) {
	$finish = strpos($input,'don\'t()',$start);
	array_push($chunks,array($start,$finish));
	$start = strpos($input,'do()',$finish+1);
}
foreach ($chunks as $idx => $pair) {
	$sum += calculate(substr($input,$pair[0],$pair[1]-$pair[0]));
}
echo "part 2 solution = $sum \n";

?>