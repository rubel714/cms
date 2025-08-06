<?php
 
 	$query = "SELECT UserId,LoginName FROM `t_users` where UserId>8;";
 
	$resultdata = $dbh->query($query);
	foreach($resultdata as $row){
		$UserId = $row["UserId"];
		$LoginName = $row["LoginName"];
		$Password = password_hash($LoginName, PASSWORD_DEFAULT);

		$query1 = "update `t_users` set Password='$Password' where UserId=$UserId;";
		$dbh->query($query1);
		$i++;


	}

 
	 

?>