<?php
/* Day 6 part 2 of Advent of Code.
 * This solution was created from an original thought
 * as is the fun for these puzzles.
 * This is not clean or thought through code,
 * a fast time is of the essence.
 * I'm not going to clean up afterwards (just added some comments),
 * it just needed to work once and nobody is going to reuse this, right?
 */

//file with data
$handle = fopen("inputfile.txt", "r");

//because of the regex function these need to be strings over here
$timelimit = "";
$distance = "";
if($handle){
	//as long the file has not ended
	while(($line = fgets($handle)) !== false){
		$line = trim($line);
		$numbers = explode(": ", $line);
		if(strlen($timelimit) === 0){
			$timelimit = preg_replace('/\s+/', "", $numbers[1]);
		}else{
			$distance = preg_replace('/\s+/', "", $numbers[1]);
		}
	}
	echo intersect($timelimit, $distance);
	fclose($handle);
}

//when do we go faster than the record?
function intersect($timelimit, $distance): int{
	//let get a head start
	for($i = floor($distance / $timelimit); $i <= $timelimit; $i++){
		if(($i * ($timelimit-$i)) > $distance){
			//the crossover is the same amount on both ends so just subtract that
			return ($timelimit - (2*$i)) + 1;
		}
	}
	return 0;
}