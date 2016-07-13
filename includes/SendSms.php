<?php require_once("initialize.php");
//select all employees
$employees=Employee::find_all();

foreach ($employees as $employee) {
	$smsCount=SMS::count_all();
	$names=array();
	//Selecting Doctors 
	$doctors=Doctor::NextWeekBirthdays($employee->empid);
	foreach ($doctors as $doctor) {
		array_push($names, $doctor->name);
	}

	if(!empty($names)){
		$message ="This Week Birthdays ".implode(", ",$names);
		$sendSms=Employee::sendsms($employee->mobile,$message);
		$smsCount->date =date("Y-m-d h:i:s",time());
		$smsCount->create();
	}else{
		$message = "We dont have any birthday in this Week.";
		$sendSms=Employee::sendsms($employee->mobile,$message);
		$smsCount->date =date("Y-m-d h:i:s",time());
		$smsCount->create();
	}	
}
?>