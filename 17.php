<?php

define('CR',chr(0x0D));
define('LF',chr(0x0A));

$test = 1;
$input_file = ($test==0) ? __DIR__.'/inputs/17.txt' : __DIR__ .'/inputs/17_test.txt';

$input = file_get_contents($input_file);
$input = str_replace(CR,'',$input);
$input = str_replace(['Register A: ','Register B: ','Register C: ','Program: '], ['','','',''],$input);


$lines = explode(LF,$input);
$r[0] = $lines[0];
$r[1] = $lines[1];
$r[2] = $lines[2];

function hackyxor($a,$b) {
	$x=str_pad(base_convert($a,10,2),64,'0',STR_PAD_LEFT);
	$y=str_pad(base_convert($b,10,2),64,'0',STR_PAD_LEFT);
	$z='';
	for ($i=0;$i<64;$i++) {
		$pair = substr($x,$i,1).substr($y,$i,1);
		$c='0'; if (($pair=='10') || ($pair=='01')) $c='1';
		$z .= $c;
	}
	return base_convert($z,2,10);
}

function computer($reg,$oc,$part2=false) {
	$r = array(0,0,0);
	$r[0] = $reg[0];
	$r[1] = $reg[1];
	$r[2] = $reg[2];
	$opcodes = explode(',',$oc);
	$offset = 0;
	$continue = true;
	$output = '';
	while ($continue==true) {
		$opcode = $opcodes[$offset];
		$operand_pre = ($offset+1)<count($opcodes) ? intval($opcodes[$offset+1]) : 0;
		$operand = $operand_pre;
		if (($operand>3) &&($operand<7)) $operand = $r[$operand-4];
		//echo "\n A= ".$r[0]." B=".$r[1]." C=".$r[2]." offset=$offset opcode=$opcode operand=$operand [$operand_pre] out=$output \n";
	
		if (($opcode==0) || ($opcode==6) || ($opcode==7)) { // adv , bdv, cdv
			$nr = bcpow(2,$operand);
			$result = bcdiv($r[0],$nr);
			if ($opcode==0) $r[0] = $result;
			if ($opcode==6) $r[1] = $result;
			if ($opcode==7) $r[2] = $result;
			
			$offset = $offset+2;
		}
		if ($opcode==1) { // bxl 
			$r[1] = hackyxor($r[1],$operand_pre);
			$offset = $offset+2;
		}
		if ($opcode==2) { // bst
			$r[1] = bcmod($operand,8);
			$offset = $offset+2;
		}
		if ($opcode==4) { //bxc
			$r[1]  = hackyxor($r[1],$r[2]);
			$offset=$offset+2;
		}
		if ($opcode==5) { //out
			$result = bcmod($operand,8);
			if ($output!='') $output.=',';
			$output .=$result;
			if ($part2==true) {
				if (substr($oc,0,strlen($output))!=$output) return [0,0,0,-1];
			}
			$offset=$offset+2;
		}
		if ($opcode==3) { // jnz
			if ($r[0]!=0) {
				$offset = $operand;
			} else {
				$offset++;
			}
		}
		
		if ($offset>=count($opcodes)) $continue=false;
	}
	//echo "\n EXIT A= ".$r[0]." B=".$r[1]." C=".$r[2]." offset=$offset opcode=$opcode operand=$operand [$operand_pre] out=$output \n";
	return [$r[0],$r[1],$r[2],$output];
}

$result = computer([$lines[0],$lines[1],$lines[2]],$lines[4]);
echo "Part 1 solution = ".$result[3]."\n";

/**

2,4,1,1,7,5,1,5,4,1,5,5,0,3,3,0

2,4  : B = A mod 8

1,1  : A = A div 2

7,5  : C = A div 2^[B]

1,5  : A = A div 2^[B]

4,1  : B = B xor C

5,5  : print contents of B 

.... 


*/

// part 2 not implemented.


?>