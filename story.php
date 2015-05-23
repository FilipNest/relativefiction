<?php
error_reporting(0);
ini_set('display_errors', 0);

require_once 'HTTP/Request2.php';

//Include API keys

include "secrets.php";

//Include country list

include "countrycodes.php";

include "venuecategories.php";

$foursquare['venuecategories'] = getvenuecategories();

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

//Make bulk request for all Foursquare categories in text

$extraplaces = array();

function cmp($a, $b)
  {
      if ($a->location->distance == $b->location->distance) {
          return 0;
      }
      return ($a->location->distance < $b->location->distance) ? -1 : 1;
  }

function parselocations(){

  global $extraplaces;
  global $foursquare;
  global $location;
  global $output;
  global $categoryids;
  global $placevariables;
  
  $request = new HTTP_Request2('https://api.foursquare.com/v2/venues/search',
                               HTTP_Request2::METHOD_GET, array('use_brackets' => true));

  $request->setConfig(array(
      'ssl_verify_peer'   => FALSE,
      'ssl_verify_host'   => FALSE
  ));

  $url = $request->getUrl();
  $url->setQueryVariables(array(

      "client_id" => $foursquare['id'],
      "client_secret" => $foursquare['secret'],
    "limit" => 50,
      "v" => "20150516",
      "ll" => $location["latitude"].",".$location["longitude"],
    "categoryId" => implode(",",$categoryids)

  ));

  $places = $request->send()->getBody();

  $places = json_decode($places)->response->venues;

  //Sort into array

  $fetchedvenues = array();

  foreach ($places as $place){

    foreach ($place->categories as $category){

    if(!isset($fetchedvenues[$category->id])){

      $fetchedvenues[$category->id] = array();

    }

       $fetchedvenues[$category->id][] = $place;

    }

  };

  //Loop over variables to swap in places

  foreach($placevariables as $place){

    $tag = $place["tag"];
    $category = $place["category"];
    $id = $place["id"] - 1;
    $extra = $place["extra"];

    if(isset($fetchedvenues[$place["category"]]) && isset($fetchedvenues[$place["category"]][$id])){

      usort($fetchedvenues[$place["category"]], "cmp");

      $venue = $fetchedvenues[$place["category"]][$id]->name;

      //Rewrite to extra if needed

      if($extra == "distance"){

        $venue = $fetchedvenues[$place["category"]][$id]->location->distance;

      }

      if($extra == "street"){

        if(isset($fetchedvenues[$place["category"]][$id]->location->address)){

        $venue = $fetchedvenues[$place["category"]][$id]->location->address;

        } else {

          $venue = "the street";

        }

        //Strip out words containing numbers
         preg_match_all("/(^[\D]+\s|\s[\D]+\s|\s[\D]+$|^[\D]+$)+/",$venue,$result);
  $venue = implode('',$result[0]);

        //Trim

        $venue = trim($venue);

      }

      if($extra == "city"){

        if(isset($fetchedvenues[$place["category"]][$id]->location->city)){

        $venue = $fetchedvenues[$place["category"]][$id]->location->city;

        } else {

          $venue = "the city";

        }

      }

      $output = str_replace("[".$tag."]", $venue, $output);

    } else {
      
      //Slot in extra places.
     
      $extraplaces[] = $place;
      
    }
 }
  
  if(isset($extraplaces) && count($extraplaces) > 0){
            
  $placevariables = $extraplaces;
  $extraplaces = array();
  $categoryids = array();
  
  foreach($placevariables as $place){
   
    if (in_array($place['category'], $categoryids) == false) {
      $categoryids[] = $place['category'];

    }
    
  }
  
  parselocations();
  
}

}

parselocations();

  
//Onto conditionals!

preg_match_all("/\{if([^\]]*)\}/", $output, $conditionals);

$conditionals = $conditionals[1];

if(count($conditionals) > 0){
 
  foreach($conditionals as $conditional){
       
    $variable = '{if'.$conditional.'}';
    $yestext = explode("|",$conditional)[1];
  
    if(count(explode("|",$conditional)) > 2){
    $notext = explode("|",$conditional)[2];  
    } else {
    
      $notext = null;
      
    }
    
    //Strip out whitespace
    
    $logic = explode("|",$conditional)[0];
    $logic = preg_replace('/\s+/', '', $logic);
    $logic = str_replace("if","",$logic);
    
    $logic = explode(",",$logic);
    
    $rules = array();
    
    //Split rules into truth values
    
    foreach($logic as $rule){
        
      $rules[] = array(
      
        "truth" => $rule[0],
        "rule" => substr($rule, 1),
      
      );
      
    };
    
    $pass = false;
    
    foreach($rules as $rule){
      
      $rule['rule'] = str_replace("&gt;",">",$rule['rule']);
            $rule['rule'] = str_replace("&lt;","<",$rule['rule']);
     
      //Check
            
      if (strpos($rule['rule'],'==') !== false) {
                
        $left = explode("==",$rule['rule'])[0];
        $right = explode("==",$rule['rule'])[1];
          
        if($rule['truth'] == "+"){
          $pass = ($left == $right);
        } else {
          $pass = ($left != $right); 
        }
                
      }
      
      if (strpos($rule['rule'],'>') !== false) {
        
        $left = explode(">",$rule['rule'])[0];
        $right = explode(">",$rule['rule'])[1];

        if($rule['truth'] == "+"){
          $pass = ($left > $right);
        } else {
          $pass = ($left < $right); 
        }

        
      }
      
      if (strpos($rule['rule'],'<') !== false) {
        
        $left = explode("<",$rule['rule'])[0];
        $right = explode("<",$rule['rule'])[1];

        if($rule['truth'] == "+"){
          $pass = ($left < $right);
        } else {
          $pass = ($left > $right); 
        }

        
      }
            
      if($pass == false){
       
        break;
        
      }
      
    }
    
    if($pass == false){
      
       $output = str_replace($variable, $notext, $output);
      
    } else {
      
       $output = str_replace($variable, $yestext, $output);
      
    };
    
  }
  
}

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