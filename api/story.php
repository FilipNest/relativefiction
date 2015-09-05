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

//Create array of translating functions

$translations = array();

function register($name, $action){
  
  global $translations;
  
  //Force lowercase (don't shoot me, makes it simpler for the writer).
  
  $name = strtolower($name);

  $translations[$name] = function($variable) use ($action){
    
    global $output;
    
    $value = call_user_func($action, $variable);
    
    $output = str_replace("[".$variable."]", $value, $output);
    
  };
  
}

//Include all translators

include "translators.php";

//Register longitude and latitude variables

register('longitude', function($variable) use($location){
  
  return $location["longitude"];
  
});
 
register('latitude', function($variable) use($location){
  
  return $location["latitude"];
  
});

//Get list of variables in the text
  
preg_match_all("/\[([^\]]*)\]/", $output, $matches);

//Store matches in an array

$variables = $matches[1];

//Check if there are any translation functions registered for these results and use them

foreach ($variables as $key => $variable){
  
  $start = strtolower(explode("|",$variable)[0]);
  
  if(isset($translations[$start])){
    
    call_user_func($translations[$start],$variable);
    
    unset($variables[$key]);
    
  };
  
};

//Get Foursquare venue data and replace relevant tags

foursquare($variables);

//Loop over any remaining variables

foreach ($variables as $variable){
  
  $segments = explode("|",$variable);
  
  if(count($segments)>2 && $segments[2] == "distance"){
   
        $output = str_replace("[".$variable."]", "99999999999999", $output);
    
  } else if (count($segments)>2 && $segments[2] == "street"){
    
    $output = str_replace("[".$variable."]", "Unknown Street", $output);
    
  } else if (count($segments)>2 && $segments[2] == "city"){
    
    $output = str_replace("[".$variable."]", "Unknown City", $output);
    
  } else {
    
     $output = str_replace("[".$variable."]", $segments[0], $output);
    
  }
  
};
  
//Worth through any conditional tags and convert them to the calculated value

include "conditionals.php";

conditionals();

//Report errors

include "errors.php";

//Finally return the output

print $output;

?>