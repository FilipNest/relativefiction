<?php

//Catch errors

function shutdown() {
  
  global $output;
  
    $isError = false;

    if ($error = error_get_last()){
    $isError = true;
    }

    if ($isError){
      
      $location = pathinfo($error['file']);
      
      print ("<b><small><br/><p>Something went wrong in the parsing of this story. If it looks OK it could be something minor. If not, the error was: ".$error['message']." on line " . $error['line'] ." of ".$location['basename']. ". Please report this error if it keeps coming up.</p></small></b>");
    }
}

register_shutdown_function('shutdown');

?>
