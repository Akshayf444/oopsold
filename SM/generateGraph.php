<?php
session_start();
if (!isset($_SESSION['SM'])) {
    header("Location:../login.php");
}
require_once("../includes/initialize.php");

if (isset($_POST['search_term']) && isset($_SESSION['doctor'])) {
    $brandName = $_POST['search_term'];
    $doctorName = $_SESSION['doctor'];
    $xAxisData = array();
    $brandSeriesData2 = array();
    $m = 1;
    for ($i = 1; $i <= 12; $i++) {
        $m++;
        $monthData = array();
        $monthname = date('F', mktime(0, 0, 0, $m, 1, date('Y')));
        array_push($xAxisData, $monthname);
        $month = date('m', mktime(0, 0, 0, $m, 1, date('Y')));
        $brand_busi_list = BrandBusiness::find_by_docid($month, $doctorName);

        if (!empty($brand_busi_list)) {
            for ($i = 1; $i < 9; $i++) {
                $data = explode(",", $brand_busi_list->{'brand' . $i});
                if ($data[0] == $brandName) {
                    array_push($monthData, $data[1]);
                }
            }

            array_push($brandSeriesData2, array_sum($monthData));
        } else {
            array_push($brandSeriesData2, 0);
        }
    }

}


if (isset($_POST['doctor_name'])) {
    $xAxisData = array();
    $brandSeriesData = array();
    $doctorName = $_POST['doctor_name'];
    $_SESSION['doctor'] = $doctorName;
    $brand_business = BrandBusiness::entryExist($doctorName);
    if (!empty($brand_business)) {
        for ($i = 1; $i < 9; $i++) {
            $business = explode(",", $brand_business->{'brand' . $i});
            $Product = Product::find_by_id($business[0]);
            array_push($xAxisData, $Product->name);
            array_push($brandSeriesData, $business[1]);
        }
    }

} ?>
