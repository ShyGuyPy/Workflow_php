<?php

//multi dimensional array
$test_array = array(1,11,111);

$test_array2 = array(
  array("test1",1,11,111),
  array("test2",2,22,222),
  array("test3",3,33,333)
);

$test_array3 = array(
  "array1" =>array(
    "name"=>1,"age"=>11, "number"=>111,
  ),
  "array2" =>array(
    "name"=>2,"age"=>22, "number"=>222,
  ),
  "array3" =>array(
    "name"=>3,"age"=>33, "number"=>333,
  )
);

echo"index item 2 is:".$test_array[0][2];



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
echo "<br>";

$random_test_var2 = " of a concat assignment";
echo $random_test_var.=$random_test_var2;

$run = 1;

if($run){
  echo "<br>";
  echo "This should not print unless run is 1";
}


?>
