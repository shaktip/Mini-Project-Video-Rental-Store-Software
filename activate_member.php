<?php
	require_once("inc/dbcontroller.php");
    require('include/header.php');
    if(!session_id()) session_start();
	$db_handle = new DBController();
	if(!empty($_POST) && isset($_POST['make_payment'])){
        $query = "UPDATE member set status = 'A' WHERE member_id='" . $_GET["id"] . "'";
        $result = $db_handle->updateQuery($query);
        if (!empty($result) && !empty($_SESSION['amt'])) {
            $message = "<h3>Payment of Rs. " . $_SESSION['amt'] . " is successful!</h3>";
            $message .= "<h4>Your membership is accepted by VRS! <br/><br/>Enjoy watching movies and listening to music with a variety of our collection!</h4><br/>";
            $message .= '<a href="main_member.php">Click here to login</a>';
            unset($_SESSION['amt']);
        } else {
            $message = "Problem in account activation.";
        }
    }
	elseif(!empty($_GET["id"]) && !empty($_GET['email']) && !empty($_GET['token'])) {
        $query = "SELECT email, deposit_amount, status, user_name FROM member WHERE member_id=" . $_GET['id'];
        $result = $db_handle->runQuery($query);
        if (!empty($result)) {
            echo "<h3>Hello, " . $result[0]["user_name"] . "</h3>";
            if ($result[0]["status"] == 'A') {
                $message = "<h3>Your email id is already validated and account is activated</h3>";
            } elseif ($result[0]["status"] == 'I') {
                $message = "<h3>Sorry, your account with VRS is deactivated. <br/> <br/>You may register with us once again and complete all the joining formalities.</h3>";
                $message .= '<br/><a href="register_member.php">Click here to register</a>';
            } else {
                if (md5($result[0]["email"]) == $_GET['email'] && $_GET['token'] == md5($_GET["id"])) {
                    $message = "<form method='post' action='' name='make_payment' id='make_payment'>";
                    $message .= "<h3>Your email ID is validated. Please proceed to pay Rs." . $result[0]["deposit_amount"] . "</h3><br/><br/>";
                    $message .= '<input type="submit" class="btn btn-info" id="make_payment" name="make_payment" value="Pay and Complete Registration" />';
                    $message .= "</form>";
                    $_SESSION['amt'] = $result[0]["deposit_amount"];
                } else {
                    $message = "Problem in email id validation.";
                }
            }
        }
    }
    if(isset($message)) {
	    echo '<br/><br/>';
	    echo '<div class="container" style="color:green;" align="center">';
        echo  $message . "</div>";
        unset($message);
    }

    require('include/footer.php');
?>



