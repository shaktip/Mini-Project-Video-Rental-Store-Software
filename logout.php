
<?php if(!session_id()) session_start(); /* Starts the session */
//echo 1;
if (isset($_SESSION['role']) && $_SESSION['role']== "member"){
//	echo 2;
	$_SESSION['role'] = '';
	session_destroy(); /* Destroy started session */
	header("location:index.php");
}
else {
//	echo 3;
    $_SESSION['role'] = '';
    session_destroy(); /* Destroy started session */
    header("location:main_emp.php");
}
exit;
?>

