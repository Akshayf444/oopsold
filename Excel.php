<?php
//include the following 2 files

//require 'Classes/PHPExcel.php';

//require_once 'Classes/PHPExcel/IOFactory.php';
require_once './includes/initialize.php';
//require_once 'config.php';
global $conn;
$path = "Book1.xlsx";

//$objPHPExcel = PHPExcel_IOFactory::load($path);
//
//foreach ($objPHPExcel->getWorksheetIterator() as $worksheet) {
//
//    $worksheetTitle = $worksheet->getTitle();
//
//    $highestRow = $worksheet->getHighestRow(); // e.g. 10
//
//    $highestColumn = $worksheet->getHighestColumn(); // e.g 'F'
//
//    $highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn);
//
//    $nrColumns = ord($highestColumn) - 64;
//
//    echo "<br>The worksheet " . $worksheetTitle . " has ";
//
//    echo $nrColumns . ' columns (A-' . $highestColumn . ') ';
//
//    echo ' and ' . $highestRow . ' row.';
//
//    echo '<br>Data: <table><tr>';
//
//    for ($row = 1; $row <= $highestRow; ++$row) {
//
//        echo '<tr>';
//
//        for ($col = 0; $col < $highestColumnIndex; ++$col) {
//
//            $cell = $worksheet->getCellByColumnAndRow($col, $row);
//
//            $val = $cell->getValue();
//
//            $dataType = PHPExcel_Cell_DataType::dataTypeForValue($val);
//
//            echo '<td>' . $val . '<br>(Typ ' . $dataType . ')</td>';
//        }
//
//        echo '</tr>';
//    }
//
//    echo '</table>';
//}
if (isset($_POST['submit'])) {
    $sql = "TRUNCATE " . $_POST['page'];
    global $database;
    $result = $database->query($sql);
    //var_dump($result);
    
}


for ($row = 2; $row <= $highestRow; ++$row) {

    $val = array();

    for ($col = 0; $col < $highestColumnIndex; ++$col) {

        $cell = $worksheet->getCellByColumnAndRow($col, $row);

        $val[] = $cell->getValue();
    }


//
// $sql="INSERT INTO excel(empid, name)
//
//VALUES ('".$val[0] . "','" . $val[1] . "')";
//
//	echo $sql."\n";
//
//    mysqli_query($conn,$sql);
}
?>
<form action="" method="post">
    <input type="text" name="page" >
    <input type="submit" name="submit" value="click">
</form>