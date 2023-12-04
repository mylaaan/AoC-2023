<?php
/* Day 4 part 1 of Advent of Code.
 * This solution was created from an original thought
 * as is the fun for these puzzles.
 * This is not clean or thought through code,
 * a fast time is of the essence.
 * I'm not going to clean up afterwards (just added some comments),
 * it just needed to work once and nobody is going to reuse this, right?
 */

//file with data
$handle = fopen("inputfile.txt", "r");

$answer = 0;
if($handle){
	//as long the file has not ended
	while(($line = fgets($handle)) !== false){
		$line = trim($line);
		$numbers = explode(": ", $line);
		$splitNumbers = explode(" | ", $numbers[1]);
		$answer += scratchcardCalculator(splitAndClean($splitNumbers[0]), splitAndClean($splitNumbers[1]));
	}
	echo $answer;
	fclose($handle);
}

//check every number against each other
function scratchcardCalculator($winning, $having): int{
	$points = 0.5;
	foreach($winning as $win){
		if(in_array(trim($win), $having)){
			$points *= 2;
		}
	}
	return floor($points);
}

//spaces were ruining comparisons
function splitAndClean($list): array{
	$list = trim($list);
	$cleanList = str_replace("  ", " ", $list);
	return explode(" ", $cleanList);
}