<?php

register('dayofweek', function($variable){
  
  global $time;
  
  //Check if offset is set
  
  $offset = 0;
  
  if(count(explode("|",$variable)) > 1){
   
    $offset = explode("|",$variable)[1];
    
    
    $offset = intval($offset*86400);
    
  }
  
  return date("l",$time + $offset); 
  
});
 
register('minutes', function(){
  
  global $time;
  
  return date("i",$time);
  
});

register('monthofyear', function($variable){
  
  global $time;
  
  return date("F",$time);
  
});

register('year', function($variable){
  
  global $time;
  
  return date("Y",$time);
  
});

register('hours12', function($variable){
  
  global $time;
  
  return ltrim(date("h", $time), '0');
  
});

register('hours24', function($variable){
  
  global $time;
  
  return date("H", $time);
  
});

register('hoursampm', function($variable){
  
  global $time;
  
  return date("a", $time);
  
});

?>