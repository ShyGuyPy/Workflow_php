
<?php
function test_function($test_para){
for($i=1;$i <= 5; $i++){
echo"This script is $test_para";
echo"___"; }
}

$testing = "working";

//$run = 0;
$run=1;
  if($run){
    $testing = "for testing";
  }

test_function($testing)
//echo"This script is $testing";

?>
