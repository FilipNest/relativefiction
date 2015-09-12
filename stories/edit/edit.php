<?php

include "../../secret.php";

if(isset($_POST["author"]) && isset($_POST["title"]) && isset($_POST["story"]) && isset($_POST["id"]) && isset($_POST["editkey"])){
  
  //Map data in form POST

  $author = $_POST["author"];
  $title = $_POST["title"];
  $text = json_decode($_POST["story"]);
  $id = $_POST["id"];
  $editkey = $_POST["editkey"];
  
  try {

    // variable contains the connection string
    $connection_url = $mongo;

    // create the mongo connection object
    $m = new Mongo($connection_url);

    // use the database we connected to
    $db = $m->selectDB("localstories");

      $collection = $db->selectCollection("stories");
   
   $cursor = $collection->findOne(
        array(
            '_id' => new MongoId($id)
        )
    );
  
  if($cursor['editkey'] != $editkey){
    
    http_response_code (403);
    print "Invalid access key";
    return false;
    
  } 
    
    $data = array(

      "author" => $author,
      "title" => $title,
      "text" => $text,
      "editkey" => $editkey
      
    );
    
    if(isset($_POST["email"])){
     
      $data['email'] = $_POST["email"];
      
    };
    
    $collection->update(array("_id" => new MongoId($id)), $data);
    
    // disconnect from server
    $m->close();
        
    echo $updated;
    
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
