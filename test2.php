<?php
$test_array = array(
  array("test1",1,11,111),
  array("test2",2,22,222),
  array("test3",3,33,333)
);

foreach ($test_array[2] as $value) {
  echo $value."<br>";
}
?>
