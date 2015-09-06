  <script src="intro.js"></script>

  <link rel="stylesheet" href="/library.css" />

  <h1>Library</h1>

  <h2>Wrting created using Local Stories.</h2>

<p>Anyone can contribute so feel free to <a href="/maker.php"><b>head over to the Write section</b></a> to submit your own.</p>

<?php

include "secret.php";

 try {

    // variable contains the connection string
    $connection_url = $mongo;

    // create the mongo connection object
    $m = new Mongo($connection_url);

    // use the database we connected to
    $db = $m->selectDB("localstories");

      $collection = $db->selectCollection("stories");
   
      $cursor = $collection->find();
      $cursor->limit(0);
      
   echo $cursor->count() . ' stories in library. <br/>';
   
   print "<ul id='stories'>";
   
   function getDateTimeFromMongoId(MongoId $mongoId)
{
    $dateTime = new DateTime('@'.$mongoId->getTimestamp());
    $dateTime->setTimezone(new DateTimeZone(date_default_timezone_get()));
    return $dateTime;
}
   
   foreach($cursor as $doc) {
     
$date = getDateTimeFromMongoId($doc['_id']);
     
     print "<li><a href='/stories/".(string) $doc['_id']."'>";
     print $doc['title'];
     print " by " . "<span class='author'>" . $doc['author'] . "</a></span>";
     print "<span class='date'>Published on the " . $date->format('mS \o\f F Y') . "</span>";
     print "</li>"; 
     
      }
   
   print "</ul>";

    // disconnect from server
    $m->close();
  } catch ( MongoConnectionException $e ) {
    die('Error connecting to MongoDB server');
  } catch ( MongoException $e ) {
    die('Mongo Error: ' . $e->getMessage());
  } catch ( Exception $e ) {
    die('Error: ' . $e->getMessage());
  }

?>

<?php include "footer.php"; ?>
