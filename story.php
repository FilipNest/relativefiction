<?php

require_once 'HTTP/Request2.php';

//Include API keys

include "secrets.php";

//Get Foursquare categories list once a day or if not set

if(true){

$request = new HTTP_Request2('https://api.foursquare.com/v2/venues/categories',
                             HTTP_Request2::METHOD_GET, array('use_brackets' => true));
$url = $request->getUrl();
$url->setQueryVariables(array(

    "client_id" => $foursquare['id'],
    "client_secret" => $foursquare['secret'],
    "v" => "20150516"
  
));

$response = $request->send()->getBody();

$response = json_decode($response, true)['response']['categories'];

$venues = array();

function traverse($object){
  
  global $venues;
    
  $venues[strtolower($object['name'])] = $object["id"];
  
  if(isset($object["categories"]) && count($object["categories"] > 0)){
   
    foreach($object["categories"] as $subcategory){
     
      traverse($subcategory);
      
    }
    
  }
    
};
  
foreach($response as $category){
  
  traverse($category);
  
}   
  
  $response = json_encode($venues);
    file_put_contents('venuecategories.json', $response);
  
};

$foursquare['venuecategories'] = json_decode(file_get_contents("venuecategories.json"));

//Check all data is present

$time = $_POST["time"];
$location = $_POST["location"];
$output = json_decode($_POST["text"]);

//Static variables first as they're the easiest

//Create date elements

$day = date("l",$time);
$month = date("F",$time);
$year = date("Y", $time);

//Days

$output = str_replace("[day]",$day,$output);

//Months

$output = str_replace("[month]", $month, $output);

//Years

$output = str_replace("[year]", $year, $output);

//Get list of dynamic variables in the text
  
preg_match_all("/\[([^\]]*)\]/", $output, $matches);

//Store matches in array

$variables = $matches[1];

$categoryids = array();

$placevariables = array();

foreach($variables as $variable){
 
  $name = explode("|",$variable)[0];
  $id = explode("|",$variable)[1];
  
  if(isset($foursquare['venuecategories']->$name)){
    
    $placevariables[] = array(
      "tag" => $variable,
      "category" => $foursquare['venuecategories']->$name,
      "id" => $id
    );
    
    //Get category id
    
    $categoryids[] = $foursquare['venuecategories']->$name;
    
  }
  
}

//Make bulk request for all categories in text

$request = new HTTP_Request2('https://api.foursquare.com/v2/venues/search',
                             HTTP_Request2::METHOD_GET, array('use_brackets' => true));
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

////Sort each category by distance
//
//function nearest($a, $b)
//{
//    return strcmp($a->location->distance, $b->location->distance);
//}
//
//foreach($fetchedvenues as $venue){
//  
// usort($venue, "nearest"); 
//  
//};

//Loop over variables to swap in places

foreach($placevariables as $place){
  
  $tag = $place["tag"];
  $category = $place["category"];
  $id = $place["id"] - 1;
   
  if(isset($fetchedvenues[$place["category"]]) && isset($fetchedvenues[$place["category"]][$id])){
   
    $venue = $fetchedvenues[$place["category"]][$id]->name;
    
    $output = str_replace("[".$tag."]", $venue, $output);
    
  }
  
}

//Finally print the output

print $output;

?>