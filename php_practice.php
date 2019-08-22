<?php

//multi dimensional array
$test_array = array(
  array("test1",1,11,111),
  array("test2",2,22,222),
  array("test3",3,33,333)
);

echo"index item 2 is:".$test_array[0][2];

echo "<br>";
//create on object
class test_ob{
  public $test_var = "testing class properties";

  function show_test_var(){
    echo "func works";
    //return $this->test_var;
  }
}

$instance_test = new test_ob;
$instance_test->show_test_var();

$random_test_var = "this is only a test";

echo "<br>";
echo strlen($random_test_var);
echo "<br>";
echo str_word_count($random_test_var);

?>
