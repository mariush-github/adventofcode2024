<?php

define('CR',chr(0x0D));
define('LF',chr(0x0A));

$test = 1;
$input_file = ($test==0) ? __DIR__.'/inputs/12.txt' : __DIR__ .'/inputs/12_test.txt';
$size = ($test==0) ? 140 : 10;

$input = file_get_contents($input_file);
$input = str_replace(array(CR,LF),array('',''),$input);

$cellGarden = array_fill(0,$size*$size,0);
$cellEdges = array_fill(0,$size*$size,'');


$gardenCount = 0;
$gardenList = array();

function searchGarden($position, $code,$id) {
	global $input,$size;
	global $cellGarden;
	global $gardenList;
	$y = intdiv($position,$size);
	$x = $position - ($y*$size);
	//echo "searchGarden $position : [$code] : $id x=$x y=$y \n";
	if ($cellGarden[$position]!=0) return;
	$cellGarden[$position]=$id;
	$gardenList[$id]['count']++;
	/* up */ $p = $position-$size; if ($p>=0) { $c=substr($input,$p,1); if ($c==$code) searchGarden($p,$code,$id); }
	/* down */ $p = $position+$size; if ($p<$size*$size) { $c=substr($input,$p,1); if ($c==$code) searchGarden($p,$code,$id); }
	/* left */ $p = $position-1; if ($x>0) { $c=substr($input,$p,1); if ($c==$code) searchGarden($p,$code,$id); }
	/* right */ $p = $position+1; if ($x!=($size-1)) { $c=substr($input,$p,1); if ($c==$code) searchGarden($p,$code,$id); }
}
function getGardenID($p) {
	global $cellGarden;
	if (isset($cellGarden[$p])==false) return 0;
	return $cellGarden[$p];
}
function calculatePerimeter($id){
	global $cellGarden,$gardenList,$size,$cellEdges;
	$total = 0;
	foreach ($cellGarden as $position=>$value) {
		if ($value==$id) {
			/* up */ $p = $position-$size; $cid = getGardenID($p); if ($cid!=$id) { $total+=1; $cellEdges[$position].='u';}
			/* down */ $p = $position+$size;$cid = getGardenID($p); if ($cid!=$id) { $total+=1; $cellEdges[$position].='d';}
			/* left */ $p = $position-1; $cid = getGardenID($p); if ($cid!=$id) { $total+=1; $cellEdges[$position].='l';}
			/* right */ $p = $position+1; $cid = getGardenID($p); if ($cid!=$id) { $total+=1; $cellEdges[$position].='r';}
		}
	}
	$gardenList[$id]['perimeter'] = $total;
}

function calculateSides($id) {
	
	global $cellGarden,$gardenList,$size,$cellEdges;
	$total = 0;
	
	$su = array(); // top edge coordinates of cells that have a top edge with another id
	$sd = array(); // bottom edge
	$sl = array(); // left edge (we'll flip x+(y*size) to y+(x*size) for easy sorting on vertical)
	$sr = array(); // right edge (we'll flip x+(y*size) to y+(x*size) for easy sorting on vertical)
	
	//echo $id."\n";
	foreach ($cellGarden as $position=>$value) {
		if ($value==$id) {
			//echo "$position ".$cellEdges[$position]."\n";
			$y = intdiv($position,$size); $x = $position - ($y*$size);
			$p = ($x*$size)+$y;
			if (strpos($cellEdges[$position],'u')!==FALSE) array_push($su,$position);
			if (strpos($cellEdges[$position],'d')!==FALSE) array_push($sd,$position);
			if (strpos($cellEdges[$position],'l')!==FALSE) array_push($sl,$p);
			if (strpos($cellEdges[$position],'r')!==FALSE) array_push($sr,$p);
		}
	}
	//var_dump($su,$sd,$sl,$sr);

	
	sort($su);
	sort($sd);
	sort($sl);
	sort($sr);
	//var_dump($su,$sd,$sl,$sr);
	
	$total = 0;
	$total+=1; if (count($su)>1) { for ($i=1;$i<count($su);$i++) { if (($su[$i]-$su[$i-1])!=1) $total++; }}; // echo "up   : $total\n";
	$total+=1; if (count($sd)>1) { for ($i=1;$i<count($sd);$i++) { if (($sd[$i]-$sd[$i-1])!=1) $total++; }}; // echo "down : $total\n";
	$total+=1; if (count($sl)>1) { for ($i=1;$i<count($sl);$i++) { if (($sl[$i]-$sl[$i-1])!=1) $total++; }}; // echo "left : $total\n";
	$total+=1; if (count($sr)>1) { for ($i=1;$i<count($sr);$i++) { if (($sr[$i]-$sr[$i-1])!=1) $total++; }}; // echo "right: $total\n";
	$gardenList[$id]['sides'] = $total;
	

}

for ($pos=0;$pos<strlen($input);$pos++) {
	$c = substr($input,$pos,1);
	if ($cellGarden[$pos]==0) {
		$gardenCount++;
		$gardenList[$gardenCount] = array('code'=>$c,'perimeter'=>0,'count'=>0);
		$ret = searchGarden($pos,$c,$gardenCount);
	}
}
$sum = 0;
foreach ($gardenList as $id =>$info) {
	calculatePerimeter($id);
	$sum += $gardenList[$id]['perimeter'] * $gardenList[$id]['count'];
}

echo "Solution 1 = $sum\n";

$sum = 0;
foreach ($gardenList as $id =>$info) {
	calculateSides($id);
	$sum += $gardenList[$id]['sides'] * $gardenList[$id]['count'];
	echo "$id ".$gardenList[$id]['code'].': '.$gardenList[$id]['count'].' x '. $gardenList[$id]['sides']. " , total = $sum\n";

	
}

echo "Solution 2 = $sum\n";
//var_dump($gardenList);
?>