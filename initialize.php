<?php
date_default_timezone_set("Asia/Kolkata");
$_SESSION['_CURRENT_MONTH'] = date("m");
$_SESSION['CURRRENT_DAY'] = date ("d");
$GLOBALS['site_root'] = 'http://127.0.0.1/oops_new';

require_once('config.php');
require_once('functions.php');
require_once('database.php');
require_once('user.php');
require_once('employee.php');
require_once('doctor.php');
require_once('BasicProfile.php');
require_once('Services.php');
require_once('Academic.php');
require_once('BusinessProfile.php');
require_once('Encryption.php');
require_once('phpMailer/class.phpmailer.php');
require_once('phpMailer/class.smtp.php');
require_once('BMProfile.php');
require_once('SMProfile.php');
require_once('class.QueryWrapper.php');
//require_once('class.ExcelUpload.php');
require_once('class.SentSms.php');
require_once('class.area.php');
require_once('class.planning.php');
require_once('class.competitors.php');
require_once('class.activity.php');
require_once('pagination.php');
require_once('class.QueryWrapper.php');
require_once('class.doctor_visit.php');
require_once('class.product.php');
require_once('class.priority_product.php');
require_once('class.daily_call_planing.php');
require_once('class.brand_business.php');
require_once('class.brand_activity.php');
require_once('class.activity_master.php');

?>