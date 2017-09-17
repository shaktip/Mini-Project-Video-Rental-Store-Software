<?php
	require('inc/config.php');
	require('inc/functions.php');
    if(!session_id()) session_start();

	$tab_name = $_GET['tab'];
	$sel_id = $_GET['id'];
	$_SESSION['msg'] = '';
	
	if ($tab_name === "emp") {
		if($sel_id == $_SESSION['logged_in_id']){
			$_SESSION['msg'] = 'You cannot delete your own details';
		}
		else {
			$sql = "UPDATE employee SET status='I' WHERE empid = " . $sel_id;
			if ($conn->query($sql)) {
				$_SESSION['msg'] = 'Employee details deleted successfully';			
			}
			else{
				$_SESSION['msg'] = 'Some error occurred!';			
			}
		}
		header ("location: manage_emp.php");
	}
	elseif ($tab_name === "store") {
	    $conn->autocommit(false);
		$sql = "UPDATE store SET status='I' WHERE store_id = " . $sel_id;
		if ($conn->query($sql)) {
		    if($conn->query("UPDATE employee SET status='I' WHERE store_id = $sel_id"))
		        if($conn->query("UPDATE item_details SET status='I' WHERE store_id = $sel_id")) {
                    $_SESSION['msg'] = 'Store and its corresponding staff and item details deleted successfully';
                    $conn->commit();
                    header("location: manage_stores.php");
                    return true;
                }
		}
		$_SESSION['msg'] = 'Some error occurred!';
		$conn->rollback();
		return false;
	}
	elseif ($tab_name === "member") {
        $sql = "UPDATE member SET status='I' WHERE member_id = " . $sel_id;
        if ($conn->query($sql)) {
            $_SESSION['msg'] = 'Customer details deleted successfully';
        } else {
            $_SESSION['msg'] = 'Some error occurred!';
        }
        header("location: manage_customers.php");
    }
    elseif ($tab_name === "courier_service") {
        $sql = "UPDATE courier_service SET status='I' WHERE service_id = " . $sel_id;
        if ($conn->query($sql)) {
            $_SESSION['msg'] = 'Courier Service details deleted successfully';
        } else {
            $_SESSION['msg'] = 'Some error occurred!';
        }
        header("location: manage_courier_services.php");
    }
?>