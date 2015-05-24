<?php
//error_reporting(0);
//ini_set('display_errors', 0);

require_once 'HTTP/Request2.php';

//Include API keys

include "secrets.php";

//Map data in form POST

$time = $_POST["time"];
$location = $_POST["location"];
$output = json_decode($_POST["text"]);

//Static variables container

$static = array();

//Create static elements

$static['longitude'] = $location["longitude"];
$static['latitude'] = $location["latitude"];

//Create array of static functions

$translators = array();

function register($name, $action){
  
  global $translators;

  $translators[$name] = function($variable) use ($action){
    
    global $output;
    
    $value = call_user_func($action, $variable);
    
    $output = str_replace("[".$variable."]", $value, $output);
    
  };
  
}

include "includes.php";

//Get weather data and import into $static array;

weather();

//Swap out all static variables in text

foreach($static as $name => $value){
 
  $output = str_replace("[".$name."]", $value, $output);
  
}

//Get list of remaining variables in the text
  
preg_match_all("/\[([^\]]*)\]/", $output, $matches);

//Store matches in an array

$variables = $matches[1];

//Check if there are any functions defined for these results

foreach ($variables as $key => $variable){
  
  $start = explode("|",$variable)[0];
  
  if(isset($translators[$start])){
    
    call_user_func($translators[$start],$variable);
    
    unset($variables[$key]);
    
  };
  
};

//Get Foursquare venue data and replace relevant tags

foursquare($variables);
  
//Worth through any conditional tags and convert them to the calculated value

conditionals();

//Log any errors

include "errors.php";

//Finally return the output

print $output;

?>