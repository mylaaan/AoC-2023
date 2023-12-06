<?php
/* Day 4 part 2 of Advent of Code.
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
$copyCue = array();
if($handle){
	//making it big enough for the entire deck of cards
	for($i = 0; $i < 230; $i++){
		$copyCue[$i] = 0;
	}
	//as long the file has not ended
	while(($line = fgets($handle)) !== false){
		$line = trim($line);
		$numbers = explode(": ", $line);
		$splitNumbers = explode(" | ", $numbers[1]);
		$answer += scratchcardCalculator(splitAndClean($splitNumbers[0]), splitAndClean($splitNumbers[1]));
	}
	echo $answer;
	
	echo "</br>_____________________________________</br>";
	echo ((hrtime(true) - $start)/1000000) . "ms";
	fclose($handle);
}

//check every number against each other
function scratchcardCalculator($winning, $having): int{
	global $copyCue;
	//original + copies
	$repeats = array_shift($copyCue) + 1;
	$futureCards = 0;
	foreach($winning as $win){
		if(in_array(trim($win), $having)){
			$futureCards++;
		}
	}
	//separate loops to load up the cue to take some load off
	if($futureCards > 0){
		for($i = 0; $i < $repeats; $i++){
			for($j = 0;$j < $futureCards; $j++){
				if(count($copyCue) > $j){
					$copyCue[$j]++;
				}
			}
		}
	}
	return $repeats;
}

//spaces were ruining comparisons
function splitAndClean($list): array{
	$list = trim($list);
	$cleanList = str_replace("  ", " ", $list);
	return explode(" ", $cleanList);
}