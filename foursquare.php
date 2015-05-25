<?php

//Include foursquare venue categories
include "venuecategories.php";

$foursquare['venuecategories'] = getvenuecategories();

function foursquare($variables){
  
    global $location;
    global $output;
    global $foursquare;

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
  
  $extraplaces = array();
  parselocations($categoryids,$placevariables,$foursquare,$extraplaces);
  
};


/////////////////




//Compare venue distances for sort;

function cmp($a, $b)
  {
      if ($a->location->distance == $b->location->distance) {
          return 0;
      }
      return ($a->location->distance < $b->location->distance) ? -1 : 1;
  }

function parselocations($categoryids,$placevariables,$foursquare,$extraplaces){

  global $location;
  global $output;
  
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
          
      // Strip out any characters after a |
            
      $venue = explode("|",$venue)[0];
      
      //Strip out any text in brackets
      
      $venue = preg_replace('/\s*\([^)]*\)/', '', $venue);
      
      //Trim
      
      $venue = trim($venue);
      
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
  
    parselocations($categoryids,$placevariables,$foursquare,$extraplaces);
  
}

}
?>