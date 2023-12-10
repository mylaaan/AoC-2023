<?php
/* Day 8 part 2 of Advent of Code.
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

$loops = 0;
$instructions = array();
$directions = array();
$startingInstructions = array();
if($handle){
	//as long the file has not ended
	while(($line = fgets($handle)) !== false){
		$line = trim($line);
		//let's keep the directions apart
		if($loops == 0){
			$leftRight = str_split($line);
			foreach($leftRight as $direction){
				if($direction == "L"){
					$directions[] = 0;
				} else{
					$directions[] = 1;
				}
			}
			//note down the map
		} elseif($loops > 1){
			$parts = explode(" = (", $line);
			$pureText = str_replace(")", "", $parts[1]);
			$instructions[$parts[0]] = explode(", ", $pureText);
			//we need to know where to start
			if(str_ends_with($parts[0], "A")){
				$startingInstructions[] = array($parts[0], 0);
			}
		}
		$loops++;
	}
	$numbers = escapeWasteland($directions, $instructions, $startingInstructions);
	echo leastCommonMultiple($numbers);
	
	$time = ((hrtime(true) - $start) / 1000000);
	echo "</br>______________________________________</br>";
	echo $time . "ms";
	fclose($handle);
}

//went looping first, calculating is waaaaaaayyyy faster
function leastCommonMultiple($numbers){
	$amount = $numbers[0][1];
	for($i = 0; $i < count($numbers); $i++){
		$one = gmp_init(intval($amount));
		$two = gmp_init($numbers[$i][1]);
		$amount = gmp_lcm($one, $two);
	}
	return $amount;
}

//how long does it take to find the end of the line?
function escapeWasteland($directionList, $instructions, $ghostPaths){
	$maxDirection = count($directionList) - 1;
	for($i = 0; $i < count($ghostPaths); $i++){
		$current = $ghostPaths[$i][0];
		$steps = 0;
		$direction = -1;
		//while not have found the end
		while(!str_ends_with($current, "Z")){
			//no more directions? Start anew
			if($direction < $maxDirection){
				$direction++;
			} else{
				$direction = 0;
			}
			$current = $instructions[$current][$directionList[$direction]];
			$steps++;
		}
		//note down how many steps it took, this will be used for the calculation later
		$ghostPaths[$i][0] = $current;
		$ghostPaths[$i][1] = $steps;
	}
	return $ghostPaths;
}