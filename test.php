
<?php

function test_function($test_para){
echo"This script is $test_para";
}

$testing = "working";

$run=1;
  if($run){
    $testing = "for testing";
  }

test_function($testing)
//echo"This script is $testing";
?>
