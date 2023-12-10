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

$map = array();
$startPosition = array();
$rememberRoute = array();
$lowLoopLongitude = 141;
$lowLoopLatitude = 141;
$highLoopLongitude = 0;
$highLoopLatitude = 0;
if($handle){
	$border = "";
	for($i = 0; $i < 142; $i++){
		$border = $border . ".";
	}
	$map[] = str_split($border);
	//as long the file has not ended
	while(($line = fgets($handle)) !== false){
		$line = trim($line);
		$line = "." . $line . ".";
		//if S is found, let's mark it
		if(str_contains($line, "S")){
			$startPosition[] = count($map);
			$startPosition[] = strpos($line, "S");
		}
		$longitude = str_split($line);
		$map[] = $longitude;
	}
	$map[] = str_split($border);
	//do what we need to do
	$map = exchangeStartingPoint($map, $startPosition);
	findRoute($map, $startPosition);
	$nestPossibilities = identifyInsideLoop($map);
	echo $nestPossibilities;
	
	$time = ((hrtime(true) - $start) / 1000000);
	echo "</br>______________________________________</br>";
	echo $time . "ms";
	fclose($handle);
}

//lookup if places are inside the loop
function identifyInsideLoop($map){
	global $lowLoopLongitude;
	global $lowLoopLatitude;
	global $highLoopLongitude;
	global $highLoopLatitude;
	global $rememberRoute;
	$nestPossibilities = 0;
	for($i = $lowLoopLongitude; $i <= $highLoopLongitude; $i++){
		$inside = false;
		$corner = ".";
		for($j = $lowLoopLatitude; $j <= $highLoopLatitude; $j++){
			$type = $map[$i][$j];
			$coordinate = array($i, $j);
			//this costs 100x more time than it should
			$partOfRoute = in_array($coordinate, $rememberRoute);
			if(str_contains("|7FLJ", $type) && $partOfRoute){
				//every time we pass a pipe we decide if we're in or out
				switch($type){
					case "|":
						$inside = !$inside;
						break;
					case "F":
						$corner = "F";
						break;
					case "L":
						$corner = "L";
						break;
					case "7":
						if($corner == "L"){
							$inside = !$inside;
						}
						break;
					case "J":
						if($corner == "F"){
							$inside = !$inside;
						}
						break;
					default;
						break;
				}
			} elseif(!$partOfRoute && $inside){
				$nestPossibilities++;
			}
		}
	}
	return $nestPossibilities;
}

//find the route of the loop
function findRoute($map, $currentPosition){
	global $lowLoopLongitude;
	global $lowLoopLatitude;
	global $highLoopLongitude;
	global $highLoopLatitude;
	global $rememberRoute;
	
	$previous = $currentPosition;
	$rememberRoute[] = $currentPosition;
	//while we did not finish the loop
	do{
		$tempPrevious = $previous;
		$previous = $currentPosition;
		$currentPosition = coordinateManipulator($map, $currentPosition, $tempPrevious);
		$rememberRoute[] = $currentPosition;
		
		//very small optimization for how many things we have to loop through when doing part 2
		if($currentPosition[0] < $lowLoopLongitude){
			$lowLoopLongitude = $currentPosition[0];
		}
		if($currentPosition[0] > $highLoopLongitude){
			$highLoopLongitude = $currentPosition[0];
		}
		if($currentPosition[1] < $lowLoopLatitude){
			$lowLoopLatitude = $currentPosition[1];
		}
		if($currentPosition[1] > $highLoopLatitude){
			$highLoopLatitude = $currentPosition[1];
		}
	} while($currentPosition != $rememberRoute[0]);
}

//check every side if we can connect and did not come from
function coordinateManipulator($map, $current, $previous){
	$northCheck = false;
	$southCheck = false;
	$westCheck = false;
	$eastCheck = false;
	$pipe = $map[$current[0]][$current[1]];
	if($pipe == "|"){
		$northCheck = true;
		$southCheck = true;
	} elseif($pipe == "7"){
		$westCheck = true;
		$southCheck = true;
	} elseif($pipe == "F"){
		$eastCheck = true;
		$southCheck = true;
	} elseif($pipe == "L"){
		$northCheck = true;
		$eastCheck = true;
	} elseif($pipe == "J"){
		$northCheck = true;
		$westCheck = true;
	} elseif($pipe == "-"){
		$westCheck = true;
		$eastCheck = true;
	}
	//check the possible sides and move our pointer
	if($northCheck && ($current[0] - 1) != $previous[0]){
		$current[0] = $current[0] - 1;
		return $current;
	}
	if($southCheck && ($current[0] + 1) != $previous[0]){
		$current[0] = $current[0] + 1;
		return $current;
	}
	if($westCheck && ($current[1] - 1) != $previous[1]){
		$current[1] = $current[1] - 1;
		return $current;
	}
	if($eastCheck && ($current[1] + 1) != $previous[1]){
		$current[1] = $current[1] + 1;
		return $current;
	}
	return $current;
}

//find what character needs to replace S
function exchangeStartingPoint($map, $start){
	$northConnect = "7F|";
	$southConnect = "JL|";
	$westConnect = "FL-";
	$eastConnect = "J7-";
	$northCheck = false;
	$southCheck = false;
	$westCheck = false;
	$eastCheck = false;
	//check the sides and if they're connectable, mark that
	if(str_contains($northConnect, $map[$start[0] - 1][$start[1]])){
		$northCheck = true;
	}
	if(str_contains($southConnect, $map[$start[0] + 1][$start[1]])){
		$southCheck = true;
	}
	if(str_contains($westConnect, $map[$start[0]][$start[1] - 1])){
		$westCheck = true;
	}
	if(str_contains($eastConnect, $map[$start[0]][$start[1] + 1])){
		$eastCheck = true;
	}
	//with every side known we know what character should replace 'S'
	if($northCheck && $southCheck){
		$map[$start[0]][$start[1]] = "|";
	} elseif($northCheck && $westCheck){
		$map[$start[0]][$start[1]] = "J";
	} elseif($northCheck && $eastCheck){
		$map[$start[0]][$start[1]] = "L";
	} elseif($southCheck && $westCheck){
		$map[$start[0]][$start[1]] = "7";
	} elseif($southCheck && $eastCheck){
		$map[$start[0]][$start[1]] = "F";
	} elseif($westCheck && $eastCheck){
		$map[$start[0]][$start[1]] = "-";
	}
	return $map;
}