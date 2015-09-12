  <script src="intro.js"></script>

  <link rel="stylesheet" href="/library.css" />

  <h1>Library</h1>

  <h2>Wrting created using Local Stories.</h2>

<p>Anyone can contribute so feel free to <a href="/maker.php"><b>head over to the Write section</b></a> to submit your own.</p>

<p>Because the upload process is completely open, please email filip@bluejumpers.com or leave an issue in the GitHub issue queue if you notice something is overly spammy, pretending to be by someone it's not or such. Also, please don't take advantage of the open system by uploading bad content on purpose.</p>

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
      $cursor->sort(array('date' => 0));
      
   echo $cursor->count() . ' stories in library. <br/>';
   
   print "<ul id='stories'>";
   
   foreach($cursor as $doc) {
     
     $date = date("dS \o\\f F Y", $doc['date']);
     
     print "<li><a href='/stories/".(string) $doc['_id']."'>";
     print "<span class='title'>" . $doc['title'] . "</span><br />";
     print "<small> by </small>" . "<span class='author'>" . $doc['author'] . "</a></span>";
     print "<span class='date'>Published on the " . $date . "</span>";
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
