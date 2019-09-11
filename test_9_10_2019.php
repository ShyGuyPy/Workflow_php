<?php
require_once 'php_practice.php';



define("test_con", 2);

echo"index item 2 is:".$test_array[0][test_con];
echo "<br>";

echo "<br>";

foreach ($test_array as $value) {
  echo "$value <br>";//"item in list <br>";
}

echo "<br>";

foreach ($test_array2 as $value) {
  //echo "$value";
  foreach($value as $sub_value) {
    echo "$sub_value <br>";
  }
}
echo "<br>";

$keys =array_keys($test_array3);
for($i = 0; $i < count($test_array3); $i++) {
  echo $keys[$i] . "{<br>";
  foreach($test_array3[$keys[$i]] as $key => $value) {
    echo $key . " : " . $value . "<br>";
  }
  echo "}<br>";
}


?>
