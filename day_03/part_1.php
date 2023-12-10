<?php
/* Day 3 part 1 of Advent of Code.
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
	$sum = 0;
	for($y = 0; $y < count($matrix); $y++){
		$x = 0;
		while($x < count($matrix[$y])){
			//check every number's length, so you know where it ends
			if(is_numeric($matrix[$y][$x])){
				if(is_numeric($matrix[$y][$x + 1])){
					if(is_numeric($matrix[$y][$x + 2])){
						$sum += loopAround($x, $y, $matrix, 3);
						$x += 2;
					} else{
						$sum += loopAround($x, $y, $matrix, 2);
						$x++;
					}
				} else{
					$sum += loopAround($x, $y, $matrix, 1);
				}
			}
			$x++;
		}
	}
	return $sum;
}

function loopAround($x, $y, $matrix, $length): int{
	$number = getNumber($x, $y, $matrix);
	$i = ($x - 1);
	//check the top line
	while($i < ($x + $length + 1)){
		if(!is_numeric($matrix[$y - 1][$i]) && $matrix[$y - 1][$i] !== "."){
			return $number;
		}
		$i++;
	}
	$i = $x - 1;
	//and the bottom
	while($i < ($x + $length + 1)){
		if(!is_numeric($matrix[$y + 1][$i]) && $matrix[$y + 1][$i] !== "."){
			return $number;
		}
		$i++;
	}
	//and the sides
	if((!is_numeric($matrix[$y][$x - 1]) && $matrix[$y][$x - 1] !== ".") ||
		(!is_numeric($matrix[$y][$x + $length]) && $matrix[$y][$x + $length] !== ".")){
		return $number;
	}
	return 0;
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
