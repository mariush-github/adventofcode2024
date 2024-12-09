<?php
define('CR',chr(0x0D));
define('LF',chr(0x0A));

$test = 0;
$input_file = ($test==0) ? __DIR__.'/inputs/09.txt' : __DIR__ .'/inputs/09_test.txt';
$input = file_get_contents($input_file);
$input = trim($input,CR.LF);
$size = strlen($input);
$fileID = 0;	// small hack, use 0 for free space, start file IDs from 1	
$blockID = -1;	// 

$drive = array();
$usedBlocks = 0;
$lastUsedBlock = 0;
$eSlots = array();
$fSlots = array();
$blockCount = 0;

function loadInput() {
	global $input;
	global $drive;
	global $usedBlocks;
	global $lastUsedBlock;
	global $eSlots;
	global $fSlots;
	global $size;
	global $fileID;
	global $blockCount;
	
	$drive = array();
	$blockID = -1;
	$lastUsedBlock = 0;
	$usedBlocks = 0;
	$fileID = 0;
	$fSlots = array();
	for ($i=0;$i<$size;$i++) {
		$blocks_file = intval(substr($input,$i,1)); $i++;
		$blocks_space = intval(substr($input,$i,1));
		if ($blocks_file!=0) {
			
			$fileID++;
			array_push($fSlots,[$blockID+1,$blocks_file,$fileID]);
			for ($j=0;$j<$blocks_file;$j++) { $blockID++;$drive[$blockID]=$fileID;$lastUsedBlock=$blockID; }
			$usedBlocks = $usedBlocks + $blocks_file;
		}
		if ($blocks_space!=0) {
			
			for ($j=0;$j<$blocks_space;$j++) { $blockID++;$drive[$blockID]=0; }
		}
	}
	$blockCount = $blockID+1;
	$fileCount = $fileID;
	echo "Number of files: $fileCount using $usedBlocks blocks. Last used block : $lastUsedBlock. Total blocks: $blockCount \n";
}

function findEmptyBlock($previousBlock = 0) {
	global $drive;
	global $blockCount;
	if ($drive[$previousBlock]==0) return $previousBlock;
	if ($previousBlock==($blockCount-1)) return -1;
	$i = $previousBlock;
	while ($drive[$i]!=0) {
		if ($i==$blockCount) return -1;
		$i++;
	}
	return $i;
}

function findLastUsedBlock($previousBlock = -1) {
	global $drive;
	global $lastUsedBlock;
	$i = ($previousBlock==-1) ? $lastUsedBlock : $previousBlock;
	if ($drive[$i]!=0) return $i;
	while ($drive[$i]==0) {
		$i--;
		if ($i<0) return -1;
	}
	return $i;
}

function printDrive() {
	global $test;
	if ($test==0) return;
	global $blockCount, $drive;	
	for($i=0;$i<$blockCount;$i++) {
		echo ($drive[$i]==0) ? '.' : ($drive[$i]-1);
	}
	echo "\n";
}

function findEmptyBlockMinSize($len) {
	global $drive;
	global $blockCount;
	$l = 0;
	$estart = -1;
	for ($i=1;$i<$blockCount;$i++) {
		if ($drive[$i]!=0) {
			$l=0;
		} else {
			$l++; 
			if ($l==$len) return ($i+1-$len);
			
		}
	}
	return -1;
}


loadInput();

printDrive();

$e = findEmptyBlock();
$u = findLastUsedBlock();
while ($e<$u) {
	$drive[$e]=$drive[$u];
	$drive[$u]= 0;
	$e = findEmptyBlock($e);
	$u = findLastUsedBlock($u);
}

printDrive();

$sum = 0;
for($i=0;$i<$blockCount;$i++) {
	if ($drive[$i]!=0) { $sum += $i*($drive[$i]-1); }
}
echo "Solution 1 = $sum \n";

loadInput();


printDrive();
$continue = true;

$idx = count($fSlots)-1;
while ($continue == true ) {
	$fOffset = $fSlots[$idx][0];
	$fLen = $fSlots[$idx][1];
	$fID = $fSlots[$idx][2];
	// could definitely be optimized to remember last empty area and not start from beginning every time
	$eOffset = findEmptyBlockMinSize($fLen);
	
	if (($eOffset<$fOffset) && ($eOffset!=-1)) {
		for ($i=0;$i<$fLen;$i++) {
			$drive[$eOffset+$i] = $fID;
			$drive[$fOffset+$i] = 0;
		}
		printDrive();
	}
	$idx--;
	if ($idx<0) $continue=false;
}

$sum = 0;
for($i=0;$i<$blockCount;$i++) {
	if ($drive[$i]!=0) { $sum += $i*($drive[$i]-1); }
}
echo "Solution 2 = $sum \n";

?>