<?php 
$fruits = array('','apple','banana','','guava','','orange');
var_dump($fruits);
var_dump(array_values(array_filter($fruits)));

$f =join(",",array_values(array_filter($fruits)));
echo $f."<br/>";

$f =implode(",",array_values(array_filter($fruits)));
echo $f;
?>