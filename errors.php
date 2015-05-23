<?php

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

?>