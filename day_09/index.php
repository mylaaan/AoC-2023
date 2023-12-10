<?php
/* Day 9 part 2 of Advent of Code.
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

$history = array();
if($handle){
	//as long the file has not ended
	while(($line = fgets($handle)) !== false){
		$line = trim($line);
		$history[] = explode(" ", $line);
	}
	echo extrapolate($history);
	
	$time = ((hrtime(true) - $start) / 1000000);
	echo "</br>______________________________________</br>";
	echo $time . "ms";
	fclose($handle);
}


function extrapolate($history){
	$total = 0;
	//for every line of history
	foreach($history as $report){
		$layer = 0;
		$extrapolation = array();
		$extrapolation[] = $report;
		do{
			$difference = false;
			$lastChange = $extrapolation[$layer][1] - $extrapolation[$layer][0];
			//and here we build our lines of extrapolation
			for($i = 0; $i < count($extrapolation[$layer]) - 1; $i++){
				$change = $extrapolation[$layer][$i + 1] - $extrapolation[$layer][$i];
				$extrapolation[$layer + 1][] = $change;
				if($lastChange != $change){
					$difference = true;
				}
				$lastChange = $change;
			}
			$layer++;
			//if they're all the same (not even zeroes) quit
		} while($difference);
		//time to predict the future and more history
		$addedValue = $extrapolation[$layer][0];
		//we can ignore the last one and one-to-last was already done above
		for($j = count($extrapolation) - 2; $j >= 0; $j--){
			//there is not a lot of difference in part 1 & 2
//			$addedValue = end($extrapolation[$j]) + $addedValue; //part 1
			$addedValue = array_shift($extrapolation[$j]) - $addedValue; //part 2
		}
		$total += $addedValue;
	}
	return $total;
}
