<?php
/* Day 7 part 2 of Advent of Code.
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

$totalRanking = array();
$answer = 0;
if($handle){
	//as long the file has not ended
	while(($line = fgets($handle)) !== false){
		$line = trim($line);
		$hand = explode(" ", $line);
		$totalRanking[] = $hand;
	}
	//use standard sort function with custom compare function
	//maybe could have been faster with a custom sort algorithm
	usort($totalRanking, "compare");
	$rankNumber = 1;
	foreach($totalRanking as $rank){
		$answer += ($rankNumber * intval($rank[1]));
		$rankNumber++;
	}
	echo $answer;
	
	echo "</br>______________________________________</br>";
	echo ((hrtime(true) - $start)/1000000) . "ms";
	fclose($handle);
}


function compare($handOne, $handTwo): int{
	$one = str_split($handOne[0]);
	$two = str_split($handTwo[0]);
	//these ranks will not be relative but an absolute number for what kind of hand they have
	$rankOne = determineRank(array_count_values($one));
	$rankTwo = determineRank(array_count_values($two));
	//if they're not the same type we can skip this block
	if( $rankOne == $rankTwo){
		$one = normalizeValues($one);
		$two = normalizeValues($two);
		for($i = 0; $i < count($one); $i++){
			if($one[$i] != $two[$i]){
				return $one[$i] <=> $two[$i];
			}
		}
		//the data set doesn't allow it, but theoretically it can happen that two hands are identical
		return 0;
	}
	return $rankOne <=> $rankTwo;
}

//if the hand was the same type we need compare individual card. So we need to normalize the value
function normalizeValues(array $hand): array{
	$newHand = array();
	foreach($hand as $card){
		if(is_numeric($card)){
			$newHand[] = $card;
		}else{
			if($card == "T"){
				$newHand[] = 10;
			}elseif($card == "J"){ //this is now the joker and not the jack
				$newHand[] = 1;
			}elseif($card == "Q"){
				$newHand[] = 12;
			}elseif($card == "K"){
				$newHand[] = 13;
			}elseif($card == "A"){
				$newHand[] = 14;
			}
		}
	}
	return $newHand;
}

function determineRank(array $hand): int{
	//first look how many jokers are in my hand
	$jokers = 0;
	if(array_key_exists("J", $hand)){
		$jokers = $hand["J"];
	}
	//just keep the values and sort them for easy evaluation
	$handValues = array_values($hand);
	rsort($handValues);
	
	//mega if-else
	//five of a kind = 7
	if($handValues[0] == 5){
		return 7;
	}
	//four of a kind = 6
	if($handValues[0] == 4){
		if($jokers == 1 || $jokers == 4){ //five of a kind
			return 7;
		}
		return 6;
	}
	//full house = 5
	if($handValues[0] == 3 && $handValues[1] == 2){
		if($jokers == 2 || $jokers == 3){ //five of a kind
			return 7;
		}
		return 5;
	}
	//three of a kind = 4
	if($handValues[0] == 3){
		if($jokers == 1 || $jokers == 3){ //four of a kind
			return 6;
		}
		return 4;
	}
	//two pair = 3
	if($handValues[0] == 2 && $handValues[1] == 2){
		if($jokers == 1){ //full house
			return 5;
		}elseif($jokers == 2){ //four of a kind
			return 6;
		}
		return 3;
	}
	//one pair = 2
	if($handValues[0] == 2){
		if($jokers == 1 || $jokers == 2){ //three of a kind
			return 4;
		}
		return 2;
	}
	//high card = 1
	if($jokers == 1){ //one pair
		return 2;
	}
	return 1;
}