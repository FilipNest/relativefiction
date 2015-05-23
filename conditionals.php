<?php

function conditionals(){
  
  global $output;

  preg_match_all("/\{if([^\]]*)\}/", $output, $conditionals);

  $conditionals = $conditionals[1];

  if(count($conditionals) > 0){

    foreach($conditionals as $conditional){

      $variable = '{if'.$conditional.'}';
      $yestext = explode("|",$conditional)[1];

      if(count(explode("|",$conditional)) > 2){
      $notext = explode("|",$conditional)[2];  
      } else {

        $notext = null;

      }

      //Strip out whitespace

      $logic = explode("|",$conditional)[0];
      $logic = preg_replace('/\s+/', '', $logic);
      $logic = str_replace("if","",$logic);

      $logic = explode(",",$logic);

      $rules = array();

      //Split rules into truth values

      foreach($logic as $rule){

        $rules[] = array(

          "truth" => $rule[0],
          "rule" => substr($rule, 1),

        );

      };

      $pass = false;

      foreach($rules as $rule){

        $rule['rule'] = str_replace("&gt;",">",$rule['rule']);
              $rule['rule'] = str_replace("&lt;","<",$rule['rule']);

        //Check

        if (strpos($rule['rule'],'==') !== false) {

          $left = explode("==",$rule['rule'])[0];
          $right = explode("==",$rule['rule'])[1];

          if($rule['truth'] == "+"){
            $pass = ($left == $right);
          } else {
            $pass = ($left != $right); 
          }

        }

        if (strpos($rule['rule'],'>') !== false) {

          $left = explode(">",$rule['rule'])[0];
          $right = explode(">",$rule['rule'])[1];

          if($rule['truth'] == "+"){
            $pass = ($left > $right);
          } else {
            $pass = ($left < $right); 
          }


        }

        if (strpos($rule['rule'],'<') !== false) {

          $left = explode("<",$rule['rule'])[0];
          $right = explode("<",$rule['rule'])[1];

          if($rule['truth'] == "+"){
            $pass = ($left < $right);
          } else {
            $pass = ($left > $right); 
          }


        }

        if($pass == false){

          break;

        }

      }

      if($pass == false){

         $output = str_replace($variable, $notext, $output);

      } else {

         $output = str_replace($variable, $yestext, $output);

      };

    }

  }
}

?>