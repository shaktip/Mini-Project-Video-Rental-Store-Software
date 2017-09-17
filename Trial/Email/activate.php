<?php
	require_once("dbcontroller.php");
	$db_handle = new DBController();
	if(!empty($_GET["id"])) {
	$query = "UPDATE registered_users set status = 'active' WHERE id='" . $_GET["id"]. "'";
	$result = $db_handle->updateQuery($query);
		if(!empty($result)) {
			$message = "Your account is activated.";
		} else {
			$message = "Problem in account activation.";
		}
	}
?>
<html>
<head>
<title>PHP User Registration Form</title>
<link href="style.css" type="text/css" rel="stylesheet" />
</head>
<body>
<?php if(isset($message)) { ?>
<div class="message"><?php echo $message; ?></div>
<?php } ?>
</body></html>
		