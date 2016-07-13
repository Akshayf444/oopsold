<?php $ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'http://instacom.in/TAPAS/Not_ResponderZotideTM.aspx');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, FALSE);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
//curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1); // On dev server only!
$result = curl_exec($ch);

?>