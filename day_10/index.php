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
		$map[] = str_split($line);
	}
	$map[] = str_split($border);
	//do what we need to do
	$map = exchangeStartingPoint($map, $startPosition);
	$map = findRoute($map, $startPosition);
	$nestPossibilities = identifyInsideLoop($map);
	echo $nestPossibilities;
	
	$time = ((hrtime(true) - $start) / 1000000);
	echo "</br>______________________________________</br>";
	echo $time . "ms";
	fclose($handle);
}

//lookup if places are inside the loop
function identifyInsideLoop($map){
	$nestPossibilities = 0;
	for($i = 0; $i < count($map); $i++){
		$inside = false;
		$corner = ".";
		for($j = 0; $j < count($map[0]); $j++){
			$type = $map[$i][$j];
			$partOfRoute = false;
			//if this is not a character but an array it was marked as part of the loop
			if(is_array($map[$i][$j])){
				$partOfRoute = true;
				$type = $map[$i][$j][0];
			}
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
				$map[$i][$j] = "I";
				$nestPossibilities++;
			}
		}
	}
	return $nestPossibilities;
}

//find the route of the loop
function findRoute($map, $currentPosition){
	global $trueStart;
	//set a starting point so we know when to stop
	$trueStart = $currentPosition;
	$previous = $currentPosition;
	//while we did not finish the loop
	do{
		$pipe = $map[$currentPosition[0]][$currentPosition[1]];
		$map[$currentPosition[0]][$currentPosition[1]] = array($pipe, true);
		$tempPrevious = $previous;
		$previous = $currentPosition;
		$currentPosition = coordinateManipulator($map, $currentPosition, $tempPrevious, $pipe);
		
	} while($currentPosition != $trueStart);
	return $map;
}

//check every side if we can connect and did not come from
function coordinateManipulator($map, $current, $previous, $pipe){
	$northCheck = false;
	$southCheck = false;
	$westCheck = false;
	$eastCheck = false;
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
	//what can connect on all the different sides
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