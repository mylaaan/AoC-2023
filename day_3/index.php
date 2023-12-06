<?php
/* Day 3 part 2 of Advent of Code.
 * This solution was created from an original thought
 * as is the fun for these puzzles.
 * This is not clean or thought through code,
 * a fast time is of the essence.
 * I'm not going to clean up afterwards (just added some comments),
 * it just needed to work once and nobody is going to reuse this, right?
 */

//file with data
$handle = fopen("inputfile.txt", "r");
$start = hrtime(true);

$answer = 0;
$matrix = array();
$gearPlot = array();
if($handle){
	//make a top and bottom safety line
	$firstlastLine = "";
	for($j = 0; $j < 144; $j++){
		$firstlastLine .= ".";
	}
	$matrix[0] = str_split($firstlastLine);
	$i = 1;
	//as long the file has not ended
	while(($line = fgets($handle)) !== false){
		//this bugged me the most, an end of line acting as a special character..
		$line = "." . trim($line) . ".";
		//put everything in a matrix
		$matrix[$i] = str_split($line);
		$matrix[$i][count($matrix[$i])] = ".";
		$i++;
	}
	$matrix[$i] = str_split($firstlastLine);
	$answer = matrixCalculator($matrix);
	echo $answer;
	
	echo "</br>_____________________________________</br>";
	echo ((hrtime(true) - $start)/1000000) . "ms";
	fclose($handle);
}

function matrixCalculator($matrix){
	for($y = 0; $y < count($matrix); $y++){
		$x = 0;
		while($x < count($matrix[$y])){
			//check every number's length, so you know where it ends
			if(is_numeric($matrix[$y][$x])){
				if(is_numeric($matrix[$y][$x + 1])){
					if(is_numeric($matrix[$y][$x + 2])){
						loopAround($x, $y, $matrix, 3);
						$x += 2;
					} else{
						loopAround($x, $y, $matrix, 2);
						$x++;
					}
				} else{
					loopAround($x, $y, $matrix, 1);
				}
			}
			$x++;
		}
	}
	return sumUp();
}

//put it all in a 3 dimensional array for easy lookup later
function loopAround($x, $y, $matrix, $length){
	$number = getNumber($x, $y, $matrix);
	$k = ($x - 1);
	//check the top line
	while($k < ($x + $length + 1)){
		if($matrix[$y - 1][$k] === "*"){
			addToPlot($k, $y-1, $number);
		}
		$k++;
	}
	$k = $x - 1;
	//and the bottom
	while($k < ($x + $length + 1)){
		if($matrix[$y + 1][$k] === "*"){
			addToPlot($k, $y+1, $number);
		}
		$k++;
	}
	//and the sides
	if($matrix[$y][$x - 1] === "*"){
		addToPlot($x-1, $y, $number);
	} elseif($matrix[$y][$x + $length] === "*"){
		addToPlot($x + $length, $y, $number);
	}
}

//calculate what power every gear has
function sumUp(){
	global $gearPlot;
	$sum = 0;
	foreach($gearPlot as $gearY){
		foreach($gearY as $gearX){
			//but only if it had more than 1 adjacent number
			if(count($gearX) > 1){
				$gearTorque = 1;
				foreach($gearX as $gear){
					$gearTorque *= $gear;
				}
				$sum += $gearTorque;
			}
		}
	}
	return $sum;
}

//PHP doesn't like a dynamically generated 3 dimensional array. So we need some extra code
function addToPlot($gearX, $gearY, $number){
	global $gearPlot;
	if(!array_key_exists($gearY, $gearPlot)){
		$gearPlot[$gearY] = array();
	}
	if(!array_key_exists($gearX, $gearPlot[$gearY])){
		$gearPlot[$gearY][$gearX] = array();
	}
	array_push($gearPlot[$gearY][$gearX], $number);
}

//we of course also want that number as a whole
function getNumber($x, $y, $matrix): int{
	$number = "";
	while(is_numeric($matrix[$y][$x])){
		$number .= $matrix[$y][$x];
		$x++;
	}
	return $number;
}
