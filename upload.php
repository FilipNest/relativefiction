<?php

include "secret.php";

if(isset($_POST["author"]) && isset($_POST["title"]) && isset($_POST["story"])){

  //Map data in form POST

$author = $_POST["author"];
$title = $_POST["title"];
$text = json_decode($_POST["story"]);
  
  try {

    $connection_url = $mongo;

    // create the mongo connection object
    $m = new Mongo($connection_url);

    // extract the DB name from the connection path
    $url = parse_url($connection_url);
    $db_name = preg_replace('/\/(.*)/', '$1', $url['path']);

    $collection = $m->selectCollection('localstories', 'stories');
    
    $data = array(

      "author" => $author,
      "title" => $title,
      "text" => $text,
      "editkey" => editkey($text)
      
    );
    
$collection->insert($data);
    
    // disconnect from server
    $m->close();
    
    print (string) $data['_id'];
    
  } catch ( MongoConnectionException $e ) {
    http_response_code (500);
  } catch ( MongoException $e ) {
    http_response_code (500);
  } catch ( Exception $e ) {
    http_response_code (500);
  }  
  
} else {
 
  http_response_code (400);
  print json_encode($_POST);
  
}

?>
