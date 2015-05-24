<?php

//Get Foursquare categories list once a day of if not set
function getvenuecategories(){
  
  global $foursquare;
  
  if(!file_exists("venuecategories.json") || time() - filemtime("venuecategories.json") >= 86400){

    //Check if venue file exists;

  $request = new HTTP_Request2('https://api.foursquare.com/v2/venues/categories',
                               HTTP_Request2::METHOD_GET, array('use_brackets' => true));

  $request->setConfig(array(
      'ssl_verify_peer'   => FALSE,
      'ssl_verify_host'   => FALSE
  ));

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
  
  return json_decode(file_get_contents("venuecategories.json"));
  
}
?>