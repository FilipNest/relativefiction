<?php
error_reporting(0);
ini_set('display_errors', 0);

require_once 'HTTP/Request2.php';

//Include API keys

include "secrets.php";

//Include foursquare venue fetching function

include "foursquarefetch.php";

//Weather parsing

include "weather.php";

//Condtitional parsing

include "conditionals.php";

//Map data in form POST

$time = $_POST["time"];
$location = $_POST["location"];
$output = json_decode($_POST["text"]);

//Static variables container

$static = array();

//Create static elements

$static["dayofweek"] = date("l",$time);
$static["monthofyear"] = date("F",$time);
$static["year"] = date("Y", $time);
$static["hours12"] = ltrim(date("h", $time), '0');
$static["hours24"] = date("H", $time);
$static["hoursampm"] = date("a", $time);
$static["minutes"] = date("i", $time);
$static['longitude'] = $location["longitude"];
$static['latitude'] = $location["latitude"];

//Get weather data and import into $static array;

weather();

//Swap out all static variables in text

foreach($static as $name => $value){
 
  $output = str_replace("[".$name."]", $value, $output);
  
}

//Get list of dynamic variables in the text
  
preg_match_all("/\[([^\]]*)\]/", $output, $matches);

//Store matches in an array

$variables = $matches[1];

//Get Foursquare venue data and replace relevant tags

foursquare($variables);
  
//Worth through any conditional tags and convert them to the calculated value

conditionals();

//Log any errors

include "errors.php";

//Finally return the output

print $output;

?>