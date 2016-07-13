<?php

require_once(dirname(__FILE__) . "/includes/initialize.php");
require_once(dirname(__FILE__) . "/includes/ImageManipulator.php");

/*
 * Initialises the Webservices
 */
OnLoad();

function OnLoad() {
    $method = $_GET['method'];
    $fobj = new api_class();
    if ($method == 'SignIn') {
        $fobj->SignIn();
    } else if ($method == 'GetDoctorProfile') {
        $fobj->GetDoctorProfile();
    } else if ($method == 'EditUser') {
        $fobj->EditUser();
    } else if ($method == 'GetDoctorList') {
        $fobj->GetDoctorList();
    } else if ($method == 'GetUserProfile') {
        $fobj->GetUserProfile();
    } else if ($method == 'ChangePassword') {
        $fobj->ChangePassword();
    } else if ($method == 'GetDailyPlan') {
        $fobj->GetDailyPlan();
    } else if ($method == 'ChangeProfilePhoto') {
        $fobj->ChangeProfilePhoto();
    } else if ($method == 'editBasicProfile') {
        $fobj->editBasicProfile();
    } else if ($method == 'Notifications') {
        $fobj->Notifications();
    } else if ($method == 'GetPost') {
        $fobj->GetPost();
    } else if ($method == 'GetInfoBox') {
        $fobj->GetInfoBox();
    } else if ($method == 'GetProductInfo') {
        $fobj->GetProductInfo();
    } else if ($method == 'PostLike') {
        $fobj->PostLike();
    } else if ($method == 'PostComment') {
        $fobj->PostComment();
    } else if ($method == 'UserLike') {
        $fobj->UserLike();
    } else if ($method == 'UserComment') {
        $fobj->UserComment();
    } else {
        $output = array('status' => 'error', 'message' => "Undefined Method");
        echo json_encode($output);
        exit;
    }
}

class api_class {

    function SignIn() {
        $username = $_REQUEST['empid'];
        $password = $_REQUEST['password'];
        $device_id = $_REQUEST['device_id'];
        $found_tm = Employee::authenticate($username, $password);
        if ($found_tm != false) {
            $updateevice = Employee::updateDevice($found_tm->empid, $device_id);
            $this->content = array();
            $this->content[] = array(
                'empid' => $found_tm->empid,
                'name' => $found_tm->name,
                'emailid' => $found_tm->emailid,
                'bm_empid' => $found_tm->bm_empid
            );
            $output = array('status' => 'success', 'message' => $this->content);
        } else {
            $output = array('status' => 'error', 'message' => "Login Incorrect");
        }
        header('content-type: application/json');
        echo json_encode($output);
    }

    function GetDoctorList() {
        $empid = $_REQUEST['empid'];
        $Doctor = Doctor::find_all($empid);
        $content = array();
        if (!empty($Doctor)) {
            foreach ($Doctor as $item) {
                $content[] = array(
                    'docid' => $item->docid,
                    'Name' => $item->name,
                    'Area' => $item->area,
                    'Speciality' => $item->speciality
                );
            }

            $output = array('status' => 'success', 'message' => $content);
        } else {
            $output = array('status' => 'error', 'message' => "Detail Not Found");
        }

        header('content-type: application/json');
        echo json_encode($output);
    }

    function GetDoctorProfile() {
        $docid = $_REQUEST['docid'];
        $type = $_REQUEST['type'];
        if (strtolower($type) == 'basic') {
            $Doctor = BasicProfile::find_by_docid($docid);
            if (!empty($Doctor)) {
                $content = array();
                $content[] = array(
                    'docid' => $Doctor->docid,
                    'name' => $Doctor->name,
                    'empid' => $Doctor->empid,
                    'DOB' => $Doctor->DOB,
                    'DOA' => $Doctor->DOA,
                    'class' => $Doctor->class,
                    'clinic_address' => $Doctor->clinic_address,
                    'residential_address' => $Doctor->residential_address,
                    'receive_mailers' => $Doctor->receive_mailers,
                    'receive_sms' => $Doctor->receive_sms,
                    'yrs_of_practice' => $Doctor->yrs_of_practice,
                    'month' => $Doctor->month,
                    'behaviour' => $Doctor->behaviour,
                    'inclination_to_speaker' => $Doctor->inclination_to_speaker,
                    'potential_to_speaker' => $Doctor->potential_to_speaker,
                    'hobbies' => $Doctor->hobbies,
                    'activity_inclination' => $Doctor->activity_inclination,
                    'type' => $Doctor->type,
                    'gen_ophthal' => $Doctor->gen_ophthal,
                    'retina' => $Doctor->retina,
                    'glaucoma' => $Doctor->glaucoma,
                    'cornea' => $Doctor->cornea,
                    'any_other' => $Doctor->any_other,
                    'other' => $Doctor->other,
                    'total' => $Doctor->total,
                    'plot1' => $Doctor->plot1,
                    'street1' => $Doctor->street1,
                    'area1' => $Doctor->area1,
                    'city1' => $Doctor->city1,
                    'state1' => $Doctor->state1,
                    'pincode1' => $Doctor->pincode1,
                    'plot2' => $Doctor->plot2,
                    'street2' => $Doctor->street2,
                    'area2' => $Doctor->area2,
                    'city2' => $Doctor->city2,
                    'state2' => $Doctor->state2,
                    'pincode2' => $Doctor->pincode2,
                    'daily_opd' => $Doctor->daily_opd,
                    'value_per_rx' => $Doctor->value_per_rx,
                    'pharma_potential' => $Doctor->pharma_potential,
                    'msl_code' => $Doctor->msl_code,
                    'clinic_name' => $Doctor->clinic_name
                );

                $output = array('status' => 'success', 'message' => $content);
            } else {
                $output = array('status' => 'error', 'message' => "Detail Not Found");
            }
        } elseif (strtolower($type) == 'academic') {
            $Doctor = AcaProfile::find_by_docid($docid);
            if (!empty($Doctor)) {
                $content = array();
                $content[] = array(
                    'docid' => $Doctor->docid,
                    'name' => $Doctor->name,
                    'empid' => $Doctor->empid,
                    'media' => $Doctor->media,
                    'journal' => $Doctor->journal,
                    'subscription' => $Doctor->subscription,
                    'materials' => $Doctor->materials,
                    'activities' => $Doctor->activities,
                    'local' => $Doctor->local,
                    'intern' => $Doctor->intern,
                );

                $output = array('status' => 'success', 'message' => $content);
            } else {
                $output = array('status' => 'error', 'message' => "Detail Not Found");
            }
        } elseif (strtolower($type) == 'service') {
            $Doctor = Services::find_by_docid($docid);
            if (!empty($Doctor)) {
                $content = array();
                $content[] = array(
                    'docid' => $Doctor->docid,
                    'name' => $Doctor->name,
                    'empid' => $Doctor->empid,
                    'Services provided to Doctor' => $Doctor->aushadh,
                    'Activities' => $Doctor->factor,
                    'High Value Gifts ' => $Doctor->AOIC,
                    'Special Rate' => $Doctor->DOC,
                    'Bulk Sampling' => $Doctor->ESCRS,
                    'Post-op pouches / cards' => $Doctor->WGC,
                    'Journals/Books/Online Subscription' => $Doctor->WOC,
                    'Conferences' => $Doctor->Other,
                );
                $output = array('status' => 'success', 'message' => $content);
            } else {
                $output = array('status' => 'error', 'message' => "Detail Not Found");
            }
        }
        header('content-type: application/json');
        echo json_encode($output);
    }

    function GetUserProfile() {
        $empid = $_REQUEST['empid'];
        $found_tm = Employee::find_by_empid($empid);
        if (!empty($found_tm)) {
            $photo_path = isset($found_tm->profile_photo) && $found_tm->profile_photo != '' ? 'http://techvertica.in/foresight/files/' . $found_tm->profile_photo : 'http://techvertica.in/foresight/files/Default.png';
            $content = array();
            $content[] = array(
                'Name' => $found_tm->name,
                'emailid' => $found_tm->emailid,
                'state' => $found_tm->state,
                'HQ' => $found_tm->HQ,
                'zone' => $found_tm->zone,
                'region' => $found_tm->region,
                'city' => $found_tm->city,
                'photo' => $photo_path,
                'DOB' => $found_tm->DOB,
                'DOA' => $found_tm->doa,
                'mobile' => $found_tm->mobile
            );
            $output = array('status' => 'success', 'message' => $content);
        } else {
            $output = array('status' => 'error', 'message' => "Detail Not Found");
        }

        header('content-type: application/json');
        echo json_encode($output);
    }

    function EditUser() {
        $empid = $_REQUEST['empid'];
        $found_tm = Employee::find_by_empid($empid);
        if (!empty($found_tm)) {
            $updateEmployee = new Employee();
            $updateEmployee->empid = $_REQUEST['empid'];
            $updateEmployee->HQ = $_REQUEST['HQ'];
            $updateEmployee->DOB = $_REQUEST['dob'];
            $updateEmployee->name = $_REQUEST['name'];
            $updateEmployee->emailid = $_REQUEST['emailid'];
            $updateEmployee->state = $_REQUEST['state'];
            $updateEmployee->zone = $_REQUEST['zone'];
            $updateEmployee->region = $_REQUEST['region'];
            $updateEmployee->city = $_REQUEST['city'];
            $updateEmployee->mobile = $_REQUEST['mobile'];
            $updateEmployee->doa = $_REQUEST['doa'];
            $updateEmployee->profile_photo = $found_tm->profile_photo;
            $updateEmployee->cipla_empid = $found_tm->cipla_empid;
            $updateEmployee->password = $found_tm->password;
            $updateEmployee->lock_basic = $found_tm->lock_basic;
            $updateEmployee->lock_service = $found_tm->lock_service;
            $updateEmployee->lock_buisness = $found_tm->lock_buisness;
            $updateEmployee->lock_academic = $found_tm->lock_academic;
            $updateEmployee->team = $found_tm->team;
            $updateEmployee->bm_empid = $found_tm->bm_empid;
            $updateEmployee->device_id = $found_tm->device_id;
            $update = $updateEmployee->update($updateEmployee->empid);
            if ($update) {
                $output = array('status' => 'success', 'message' => "Record Updated Successfully");
            }
        } else {
            $output = array('status' => 'error', 'message' => "Detail Not Found");
        }

        header('content-type: application/json');
        echo json_encode($output);
    }

    function ChangeProfilePhoto() {
        ini_set("gd.jpeg_ignore_warning", 1);
        $empid = $_REQUEST['empid'];
        $found_tm = Employee::find_by_empid($empid);
        if (!empty($found_tm)) {
            if (isset($_REQUEST['profile_photo'])) {
                //$image_contents = base64_encode(file_get_contents('files/1431323126.jpg')); 
                $image = $_REQUEST['profile_photo'];
                try {
                    $img_decode = imagecreatefromstring(base64_decode($image));
                } catch (Exception $exc) {
                    echo $exc->getTraceAsString();
                }
                if ($img_decode != false) {
                    $ran = time();
                    $img_upload = "files/" . $ran . ".png";
                    imagepng($img_decode, $img_upload);

                    $manipulator = new ImageManipulator($img_upload);
                    $newImage = $manipulator->resample(150, 150);
                    $manipulator->save($img_upload);
                    $fileName = $ran . ".png";
                    $Employee = Employee::updatepic($empid, $fileName);
                    if ($Employee) {
                        $output = array('status' => 'success', 'message' => "Photo Updated Successfully.");
                    } else {
                        $output = array('status' => 'error', 'message' => "Error Occured.");
                    }
                } else {
                    $output = array('status' => 'error', 'message' => "Error Occured.");
                }
            }
        } else {
            $output = array('status' => 'error', 'message' => "Detail Not Found");
        }

        header('content-type: application/json');
        echo json_encode($output);
    }

    function GetDailyPlan() {
        $plan_date = $_REQUEST['plan_date'];
        $empid = $_REQUEST['empid'];
        $Planning = Planning::find_by_date_empid($plan_date, $empid);
        $sql = "SELECT dp.*,d.name,d.area,d.speciality,dv.id AS dvt_id,dv.docid,db.`msl_code`,p.`remark`,prt.`product1_id`,prt.`product2_id`,prt.`product3_id`  FROM planning p "
                . "LEFT JOIN daily_call_planning dp ON dp.`plan_id` = p.`id` "
                . "INNER JOIN `doctor_visit` dv ON dv.`plan_id`=p.`id` "
                . "LEFT JOIN `priority_product` prt ON dv.`docid` = prt.`docid` "
                . "INNER JOIN doctors d ON d.`docid` = dv.`docid`"
                . "LEFT JOIN `doc_basic_profile` db ON db.`docid` = dv.`docid`  WHERE p.`date`= '$plan_date' AND p.empid = '$empid'";
        $Planning = QueryWrapper::executeQuery($sql);

        if (!empty($Planning)) {
            $content = array();
            foreach ($Planning as $Plan) {
                $Product = Product::find_by_id($Plan->product1_id);
                $product_1 = $Product->name;
                $Product = Product::find_by_id($Plan->product2_id);
                $product_2 = $Product->name;
                $Product = Product::find_by_id($Plan->product3_id);
                $product_3 = $Product->name;
                $finalProduct = $product_1 . ',' . $product_2 . ',' . $product_3;
                $lastMeet = DailyCallPlanning::lastMeet($Plan->docid);
                $meet = !empty($lastMeet) ? date('d-m-Y', strtotime($lastMeet->created)) : '';
                if (!is_null($Plan->id)) {
                    $status = 'met';
                } else {
                    $status = 'Not Met';
                }
                $content[] = array(
                    'docid' => $Plan->docid,
                    'name' => $Plan->name,
                    'area' => $Plan->area,
                    'speciality' => $Plan->speciality,
                    'msl_code' => $Plan->msl_code,
                    'last_meet' => $meet,
                    'priority_product' => $finalProduct,
                    'remark' => $Plan->remark,
                    'status' => $status,
                );
            }
            $output = array('status' => 'success', 'message' => $content);
        } else {
            $output = array('status' => 'error', 'message' => 'Planning Details Not Found.');
        }
        header('content-type: application/json');
        echo json_encode($output);
    }

    function AddDailyPlan() {
        
    }

    function ChangePassword() {
        $oldPassword = $_REQUEST['old_password'];
        $newPassword = $_REQUEST['new_password'];
        $empid = $_REQUEST['empid'];
        $found_tm = Employee::find_by_empid($empid);
        if ($found_tm) {
            if ($found_tm->password === $oldPassword) {
                $changePassword = Employee::changePassword($newPassword, $empid);
                if ($changePassword) {
                    $output = array('status' => 'success', 'message' => "Password Changed Successfully.");
                }
            } else {
                $output = array('status' => 'error', 'message' => "Old Password Dosn't Match");
            }
        } else {
            $output = array('status' => 'error', 'message' => "Detail Not Found");
        }

        header('content-type: application/json');
        echo json_encode($output);
    }

    function editBasicProfile() {
        $docid = $_REQUEST['docid'];
        $empid = $_REQUEST['empid'];
        $sql = "SELECT db.* FROM `employees` e "
                . "INNER JOIN `doctors` d ON d.`empid` = e.`empid` "
                . "INNER JOIN `doc_basic_profile` db ON d.`docid` = db.docid WHERE d.`docid` = '$docid' AND e.`empid` = '$empid'";

        $fields = array('clinic_address', 'residential_address',
            'receive_mailers', 'receive_sms', 'yrs_of_practice',
            'inclination_to_speaker', 'potential_to_speaker',
            'other', 'total', 'gen_ophthal', 'retina', 'glaucoma', 'cornea', 'any_other',
            'plot1', 'street1', 'area1', 'state1', 'pincode1', 'plot2', 'street2', 'area2', 'state2', 'pincode2',
            'daily_opd', 'value_per_rx', 'value_per_month', 'pharma_potential', 'msl_code', 'clinic_name', 'class', 'DOB', 'DOA', 'behaviour');

        $postFields = array();
        $entryExist = QueryWrapper::executeQuery($sql);
        if (!empty($entryExist)) {
            $newBasicProfile = new BasicProfile();
            if ($editType == '1') {
                if (isset($_REQUEST['DOB']) && $_REQUEST['DOB'] != '') {
                    $newBasicProfile->DOB = date("Y-m-d", strtotime($_REQUEST['DOB']));
                } else {
                    $newBasicProfile->DOB = '0000-00-00';
                }

                if (isset($_REQUEST['DOA']) && $_REQUEST['DOA'] != '') {
                    $newBasicProfile->DOA = date("Y-m-d", strtotime($_REQUEST['DOA']));
                } else {
                    $newBasicProfile->DOA = '0000-00-00';
                }

                $newBasicProfile->behaviour = $_REQUEST['behaviour'];


                $postFields = array('class', 'DOB', 'DOA', 'behaviour');
                foreach ($postFields as $item) {
                    if (isset($_REQUEST[$item])) {
                        $newBasicProfile->{$item} = trim($_REQUEST[$item]);
                    }
                    break;
                }
            } elseif ($editType == '2') {
                $postFields = array('class', 'DOB', 'DOA', 'behaviour');
                foreach ($postFields as $item) {
                    if (isset($_REQUEST[$item])) {
                        $newBasicProfile->{$item} = trim($_REQUEST[$item]);
                    }
                }
            } elseif ($editType == '3') {
                $postFields = array('class', 'DOB', 'DOA', 'behaviour');
                foreach ($postFields as $item) {
                    if (isset($_REQUEST[$item])) {
                        $newBasicProfile->{$item} = trim($_REQUEST[$item]);
                    }
                }
            } elseif ($editType == '4') {
                $postFields = array('class', 'DOB', 'DOA', 'behaviour');
                foreach ($postFields as $item) {
                    if (isset($_REQUEST[$item])) {
                        $newBasicProfile->{$item} = trim($_REQUEST[$item]);
                    }
                }
            }

            $finalFields = array_diff($postFields, $fields);
            foreach ($fields as $item) {
                $newBasicProfile->{$item} = $entryExist->{$item};
            }

            $newBasicProfile->update($docid);
        } else {
            $output = array('status' => 'error', 'message' => "Detail Not Found");
        }

        header('content-type: application/json');
        echo json_encode($output);
    }

    function editBasicProfile2() {
        
    }

    function editBasicProfile3() {
        
    }

}

?>
