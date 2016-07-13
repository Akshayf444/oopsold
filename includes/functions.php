<?php

require_once('class.state.php');
require_once ('class.product.php');

function strip_zeros_from_date($marked_string = "") {
    // first remove the marked zeros
    $no_zeros = str_replace('*0', '', $marked_string);
    // then remove any remaining marks
    $cleaned_string = str_replace('*', '', $no_zeros);
    return $cleaned_string;
}

function redirect_to($location = NULL) {
    if ($location != NULL) {
        header("Location: {$location}");
        exit;
    }
}

//converting date to text
function datetime_to_text($datetime = "") {
    $unixdatetime = strtotime($datetime);
    return strftime("%B %d, %Y at %I:%M %p", $unixdatetime);
}

function flashMessage($message, $type) {
    if (!isset($_SESSION)) {
        session_start();
    }

    if (ucfirst($type) == 'Error') {

        $_SESSION['message'] = '<div class = "row"><div class = "col-lg-12 col-md-12"><div class="alert alert-danger"> '
                . '<a href="#" class="close" data-dismiss="alert">&times;</a>'
                . '<strong>' . $message . '</strong></div></div></div>';
    }
    if (ucfirst($type) == 'Success') {
        $_SESSION['message'] = '<div class = "row"><div class = "col-lg-12 col-md-12"><div class="alert alert-success"> '
                . '<a href="#" class="close" data-dismiss="alert">&times;</a>'
                . '<strong>Success!! </strong>' . $message . '</div></div></div>';
    }
}

function sendsms($mobile, $messages) {

    $authKey = "78106A1u8VLmCC054cb666b";
    $mobileNumber = $mobile;
    $senderId = "ZOTIDE";
    $message = $messages;
    $finalmessage = rawurlencode($message);
    $smsUser = 'manish';
    $smsPassword = '123456';

//Define route 
    $route = "4";
//Prepare you post parameters
    $postData = array(
        'authkey' => $authKey,
        'mobiles' => $mobileNumber,
        'message' => $finalmessage,
        'sender' => $senderId,
        'route' => $route
    );

//API URL
    $url = "https://control.msg91.com/sendhttp.php";

// init the resource
    $ch = curl_init();
    curl_setopt_array($ch, array(
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => $postData
//,CURLOPT_FOLLOWLOCATION => true
    ));


//Ignore SSL certificate verification
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);


//get response
    $output = curl_exec($ch);

//Print error if any
    if (curl_errno($ch)) {
        echo 'error:' . curl_error($ch);
    }

    curl_close($ch);

//echo $output;
    countSMS($message);

    return $output;
}

function countSMS($message) {
    $finalMessage = $message;
    $msg_count = 0;
    if (strlen($finalMessage) > 0 && strlen($finalMessage) < 161) {
        $msg_count = 1;
    } elseif (strlen($finalMessage) > 161 && strlen($finalMessage) < 307) {
        $msg_count = 2;
    } elseif (strlen($finalMessage) > 307) {
        $msg_count = 3;
    }

    $smsCount = new SMS();
    $smsCount->msg = $message;
    $smsCount->date = date("Y-m-d h:i:s", time());
    $smsCount->msg_count = $msg_count;
    $smsCount->create();
}

function stateList($id = "") {
    $stateList = '<option value ="" >Select State</option>';
    $States = State::find_all();

    foreach ($States as $State) {
        if ($id === $State->state_name) {
            $stateList .='<option value = "' . $State->state_name . '" selected>' . $State->state_name . '</option>';
        } else {
            $stateList .='<option value = "' . $State->state_name . '">' . $State->state_name . '</option>';
        }
    }

    return $stateList;
}

function areaList($id = "") {
    $empid = $_SESSION['employee'];
    require_once("class.territory.php");
    $Territories = Territory::find_by_empid($empid);
    $territoryList = '<option value ="" >Select Area</option>';
    foreach ($Territories as $Territory) {
        $sub_list = array_filter(array_map('trim', explode(",", $Territory->subterritory)));
        $territoryList .='<option value ="' . $Territory->territory . '" >' . $Territory->territory . '</option>';
        if (!empty($sub_list)) {
            foreach ($sub_list as $value) {
                $territoryList .='<option value ="' . $value . '" >' . $value . '</option>';
            }
        }
    }
    return $territoryList;
}

function ProductList1($id = "") {
    $productList = '';
    $Products = Product::find_all();
    foreach ($Products as $Product) {
        if ($Product->id == $id) {
            $productList = $Product->name;
            break;
        }
    }

    return $productList;
}

function time_passed($timestamp) {
    //type cast, current time, difference in timestamps
    $timestamp = (int) $timestamp;
    $current_time = time();
    $diff = $current_time - $timestamp;

    //intervals in seconds
    $intervals = array(
        'year' => 31556926, 'month' => 2629744, 'week' => 604800, 'day' => 86400, 'hour' => 3600, 'minute' => 60
    );

    //now we just find the difference
    if ($diff == 0) {
        return 'just now';
    }

    if ($diff < 60) {
        return $diff == 1 ? $diff . ' second ago' : $diff . ' seconds ago';
    }

    if ($diff >= 60 && $diff < $intervals['hour']) {
        $diff = floor($diff / $intervals['minute']);
        return $diff == 1 ? $diff . ' minute ago' : $diff . ' minutes ago';
    }

    if ($diff >= $intervals['hour'] && $diff < $intervals['day']) {
        $diff = floor($diff / $intervals['hour']);
        return $diff == 1 ? $diff . ' hour ago' : $diff . ' hours ago';
    }

    if ($diff >= $intervals['day'] && $diff < $intervals['week']) {
        $diff = floor($diff / $intervals['day']);
        return $diff == 1 ? $diff . ' day ago' : $diff . ' days ago';
    }

    if ($diff >= $intervals['week'] && $diff < $intervals['month']) {
        $diff = floor($diff / $intervals['week']);
        return $diff == 1 ? $diff . ' week ago' : $diff . ' weeks ago';
    }

    if ($diff >= $intervals['month'] && $diff < $intervals['year']) {
        $diff = floor($diff / $intervals['month']);
        return $diff == 1 ? $diff . ' month ago' : $diff . ' months ago';
    }

    if ($diff >= $intervals['year']) {
        $diff = floor($diff / $intervals['year']);
        return $diff == 1 ? $diff . ' year ago' : $diff . ' years ago';
    }
}

function pageHeading($heading, $smallHeading = '') {
    return '<div class="row">
                <div class="col-lg-12">
                    <h1 class="page-header">'.$heading.'<small> '.$smallHeading.'</small></h1>
                </div>      <!-- /.col-lg-12 -->
            </div>';
}

?>