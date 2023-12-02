<?php
/* Day 2 part 2 of Advent of Code.
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
if ($handle) {
    //as long the file has not ended
    while (($line = fgets($handle)) !== false) {
        $answer += findValidGames($line);
    }
    echo $answer;
    fclose($handle);
}

//for every game find the "power" of the minimal amount of coloured cubes
//a.k.a. explode paradise
function findValidGames($string){
    $blue = 0;
    $red = 0;
    $green = 0;
    //get the important data
    $game = explode(": ", $string);
    //separate every "hand"
    $handfuls = explode("; ", $game[1]);
    foreach ($handfuls as $handful){
        //and separate every colour
        $colours = explode(", ", $handful);
        foreach ($colours as $colour){
            //check for the highest number of cubes for every colour and note it down
            if(str_contains($colour, "blue") && $blue < preg_replace('/\D/', '', $colour)){
                $blue = preg_replace('/\D/', '', $colour);
            }
            if(str_contains($colour, "red") && $red < preg_replace('/\D/', '', $colour)){
                $red = preg_replace('/\D/', '', $colour);
            }
            if(str_contains($colour, "green") && $green < preg_replace('/\D/', '', $colour)){
                $green = preg_replace('/\D/', '', $colour);
            }
        }
    }
    //and return the multiplied colours as the puzzle asks for
    return $blue * $red * $green;
}
