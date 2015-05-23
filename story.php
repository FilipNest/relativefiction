<?php
error_reporting(0);
ini_set('display_errors', 0);

include "errors.php";

require_once 'HTTP/Request2.php';

//Include API keys

include "secrets.php";

//Include country list

include "countrycodes.php";

//Include foursquare venue categories
include "venuecategories.php";

$foursquare['venuecategories'] = getvenuecategories();

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

//Static variables first

$static = array();

//Create date elements

$static["dayofweek"] = date("l",$time);
$static["monthofyear"] = date("F",$time);
$static["year"] = date("Y", $time);
$static["hours12"] = ltrim(date("h", $time), '0');
$static["hours24"] = date("H", $time);
$static["hoursampm"] = date("a", $time);
$static["minutes"] = date("i", $time);
$static['longitude'] = $location["longitude"];
$static['latitude'] = $location["latitude"];

//Get weather data;

weather();

//Swap out static variables

foreach($static as $name => $value){
 
  $output = str_replace("[".$name."]", $value, $output);
  
}

//Get list of dynamic variables in the text
  
preg_match_all("/\[([^\]]*)\]/", $output, $matches);

//Store matches in array

$variables = $matches[1];

$categoryids = array();

$placevariables = array();

foreach($variables as $variable){
 
  $name = explode("|",$variable)[0];
  
  if(count(explode("|",$variable)) > 0){
  $id = explode("|",$variable)[1];
  }
  
  if(count(explode("|",$variable)) > 2){
  $extra = strtolower(explode("|",$variable)[2]);
  } else {
  $extra = null; 
  }
  
  $name = strtolower($name);
  
  if(isset($foursquare['venuecategories']->$name)){
    
    $placevariables[] = array(
      "tag" => $variable,
      "category" => $foursquare['venuecategories']->$name,
      "id" => $id,
      "extra" => $extra
    );
    
    //Get category id
    
    $categoryids[] = $foursquare['venuecategories']->$name;
    
  }
  
}

//Request all Foursquare categories in text

parselocations();
  
//Onto conditionals!

conditionals();

//Finally print the output

print $output;

?>