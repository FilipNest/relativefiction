<?php

register('dayofweek', function($variable){
  
  global $time;
  
  $date = new DateTime("@$time"); 
  
  //Check if offset is set
  
  if(count(explode("|",$variable)) > 1){
   
    $offset = explode("|",$variable)[1];
    
    intval($offset);
    
    date_add($date, date_interval_create_from_date_string($offset.' days'));
    
  }
  
  return date_format($date, 'l'); 
  
});
 
register('minutes', function($variable){
  
global $time;
  
  $date = new DateTime("@$time"); 
  
  //Check if offset is set
  
  if(count(explode("|",$variable)) > 1){
   
    $offset = explode("|",$variable)[1];
    
    intval($offset);
    
    date_add($date, date_interval_create_from_date_string($offset.' minutes'));
    
  }
  
  return date_format($date, 'i'); 
  
});

register('monthofyear', function($variable){
  
  global $time;
  
  $date = new DateTime("@$time"); 
  
  //Check if offset is set
  
  if(count(explode("|",$variable)) > 1){
   
    $offset = explode("|",$variable)[1];
    
    intval($offset);
    
    date_add($date, date_interval_create_from_date_string($offset.' months'));
    
  }
  
  return date_format($date, 'F'); 
  
});

register('year', function($variable){
  
  global $time;
  
  $date = new DateTime("@$time"); 
  
  //Check if offset is set
  
  if(count(explode("|",$variable)) > 1){
   
    $offset = explode("|",$variable)[1];
    
    intval($offset);
    
    date_add($date, date_interval_create_from_date_string($offset.' years'));
    
  }
  
  return date_format($date, 'Y'); 
  
});

register('hours12', function($variable){
  
  global $time;
  
  $date = new DateTime("@$time"); 
  
  //Check if offset is set
  
  if(count(explode("|",$variable)) > 1){
   
    $offset = explode("|",$variable)[1];
    
    intval($offset);
    
    date_add($date, date_interval_create_from_date_string($offset.' hours'));
    
  }
  
  return ltrim(date_format($date, 'h'), '0');
  
});

register('hours24', function($variable){
  
  global $time;
  
  $date = new DateTime("@$time"); 
  
  //Check if offset is set
  
  if(count(explode("|",$variable)) > 1){
   
    $offset = explode("|",$variable)[1];
    
    intval($offset);
    
    date_add($date, date_interval_create_from_date_string($offset.' hours'));
    
  }
  
    return date_format($date, 'H'); 
  
});

register('hoursampm', function($variable){
  
    global $time;
  
  $date = new DateTime("@$time"); 
  
  //Check if offset is set
  
  if(count(explode("|",$variable)) > 1){
   
    $offset = explode("|",$variable)[1];
    
    intval($offset);
    
    date_add($date, date_interval_create_from_date_string($offset.' hours'));
    
  }
  
    return date_format($date, 'a');
  
});

?>