<?php

//Get weather data for location

//Include country list

include "countrycodes.php";

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

if($weather->cod !== 200){
  
//  return false;
  
};

$sunrise = $weather->sys->sunrise;
$sunset = $weather->sys->sunset;

register('Sunrisehour', function($variable) use($sunrise){

return date("H",$sunrise); 

});

register('sunsethour', function($variable) use($sunset){

return date("H",$sunset); 

});

$current = new DateTime();
$current->setTimestamp($time);

$sunrisetime = new DateTime();
$sunrisetime->setTimestamp($sunrise);

$sunsettime = new DateTime();
$sunsettime->setTimestamp($sunset);

register('hourstosunrise', function($variable) use($sunrisetime,$current){

$time = date_diff($current,$sunrisetime)->format('%r%H');

  if($time < 0){

  $time += 24;

  }

  return ltrim($time,'0');

});

register('hourstosunset', function($variable) use($sunsettime,$current){

$time = date_diff($current,$sunsettime)->format('%r%H');

  if($time < 0){

  $time += 24;

  }

  return ltrim($time,'0');

});

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



$country = $countries[$weather->sys->country];

register('country', function($variable) use($country){

  return $country;  

});

foreach ($forecast as $name => $condition){

  if(in_array($weathercode,$condition)){

     register('weather', function($variable) use($name){

  return $name;  

  });

  }

}

?>