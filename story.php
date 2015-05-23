<?php
error_reporting(0);
ini_set('display_errors', 0);

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

//Get weather data for location

$request = new HTTP_Request2('http://api.openweathermap.org/data/2.5/weather',
                             HTTP_Request2::METHOD_GET, array('use_brackets' => true));

$request->setConfig(array(
    'ssl_verify_peer'   => FALSE,
    'ssl_verify_host'   => FALSE
));

$url = $request->getUrl();
$url->setQueryVariables(array(

    "APPID" => $openweathermap,
    "lat" => $location["latitude"],
    "lon" => $location["longitude"]

));

$weather = $request->send()->getBody();

$weather = json_decode($weather);

$sunrise = $weather->sys->sunrise;
$sunset = $weather->sys->sunset;

$static["sunrisehour"] = date("H",$sunrise);

$static["sunsethour"] = date("H",$sunset);

$current = new DateTime();
$current->setTimestamp($time);

$sunrisetime = new DateTime();
$sunrisetime->setTimestamp($sunrise);

$sunsettime = new DateTime();
$sunsettime->setTimestamp($sunset);

  $static["hourstosunrise"] = date_diff($current,$sunrisetime)->format('%r%H');

if($static["hourstosunrise"] < 0){
  
  $static["hourstosunrise"] += 24;
    
}

$static["hourstosunrise"] = ltrim($static["hourstosunrise"],'0');

  $static["hourstosunset"] = date_diff($current,$sunsettime)->format('%r%H');

if($static["hourstosunset"] < 0){
  
  $static["hourstosunset"] += 24;
    
}

$static["hourstosunset"] = ltrim($static["hourstosunset"],'0');

$weathercode = $weather->weather[0]->id;

//Translate weather codes to useable variables

$forecast = array(

"stormy" => [200,201,202,210,211,212,221,230,231,232,960,961,962,900,901,902],
"rainy" => [300,301,302,310,311,312,313,314,321,500,501,502,503,504,511,520,521,522,531],
"snowy" => [600,601,602,611,612,615,616,620,621,622,906],
"clear" => [800,801],
"cloudy" => [802,803,804],
"calm" => [951,952,953],
"windy" => [954,955,956,957,958,959,960,905],
"scorching" => [904],
"freezing" => [906]
  
);


$static["country"] = $countries[$weather->sys->country];

foreach ($forecast as $name => $condition){
  
  if(in_array($weathercode,$condition)){

  $static["weather"] = $name;

  }
  
}

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

//Catch errors

function shutdown() {
  
  global $output;
  
    $isError = false;

    if ($error = error_get_last()){
    $isError = true;
    }

    if ($isError){
      
      print ("<b><small><br/><p>Something went wrong in the parsing of this story. If it looks OK it could be something minor. If not, the error was: ".$error['message'].". Does that help?</p></small></b>");
    }
}

register_shutdown_function('shutdown');

//Finally print the output

print $output;

?>