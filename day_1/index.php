<?php
/* Day 1 part 2 of Advent of Code.
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
//what we are looking for
$needles = array("one", "two", "three", "four", "five", "six", "seven",
				 "eight", "nine", 1, 2, 3, 4, 5, 6, 7, 8, 9);
if($handle){
	//as long the file has not ended
	while(($line = fgets($handle)) !== false){
		$answer += findNumbers($line);
	}
	echo $answer;
	fclose($handle);
}

//for every line find the number
function findNumbers($string): int{
	global $needles;
	$neeldePointerArray = array();
	//go through every needle
	foreach($needles as $needle){
		//and check if it's in there at all
		if(str_contains($string, $needle)){
			//and what is the first occurrence?
			$index = stripos($string, $needle);
			//and what is the first occurrence if you look on the other end?
			$indexTwo = strrpos($string, $needle);
			//put it into a key-value array
			$neeldePointerArray[$index] = $needle;
			//don't waste time if the needle only occurred once
			if($index != $indexTwo){
				$neeldePointerArray[$indexTwo] = $needle;
			}
		}
	}
	//because we need the first and last we just sort the array on their keys (index)
	ksort($neeldePointerArray);
	$first = reset($neeldePointerArray);
	$last = end($neeldePointerArray);
	//and if it's not a number we need to change it in one
	if(!is_numeric($first)){
		$first = parseTextToNumber($first);
	}
	if(!is_numeric($last)){
		$last = parseTextToNumber($last);
	}
	//"stick" them together, don't sum them up
	return $first . $last;
}

//ugly big switch to change words to numbers
function parseTextToNumber($text){
	$number = 0;
	switch($text){
		case "one":
			$number = 1;
			break;
		case "two":
			$number = 2;
			break;
		case "three":
			$number = 3;
			break;
		case "four":
			$number = 4;
			break;
		case "five":
			$number = 5;
			break;
		case "six":
			$number = 6;
			break;
		case "seven":
			$number = 7;
			break;
		case "eight":
			$number = 8;
			break;
		case "nine":
			$number = 9;
			break;
	}
	return $number;
}
