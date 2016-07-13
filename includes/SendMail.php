<?php  require_once("initialize.php");

	$basicProfile=BasicProfile::find_by_DOA();
	if(!empty($basicProfile)){
		foreach($basicProfile as $BasicProfile){
		$docid=$BasicProfile->docid;
		echo $docid;
		$doc=Doctor::find_by_docid($docid);
		$user=User::sendmail($doc->emailid,"Happy Aniversary","Aniversary Notification");
		}
	}

		$basicProfile1=BasicProfile::find_by_DOB();
		if(!empty($basicProfile1)){
		
		foreach($basicProfile1 as $BasicProfile1){
		$docid=$BasicProfile1->docid;
		$doc=Doctor::find_by_docid($docid);
		$user=User::sendmail($doc->emailid,"Happy Birthday","Birthday Notification");
		}
	}
	//$user=User::sendmail();
?>