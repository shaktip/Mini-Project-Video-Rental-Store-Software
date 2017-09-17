<?php
if(count($_POST)>0) {
    /* Form Required Field Validation */
    foreach ($_POST as $key => $value) {
        if (empty($_POST[$key])) {
            $message = ucwords($key) . " field is required";
            break;
        }
    }
    /* Password Matching Validation */
    if ($_POST['password'] != $_POST['confirm_password']) {
        $message = 'Passwords should be same<br>';
    }

    /* Email Validation */
    if (!isset($message)) {
        if (!filter_var($_POST["userEmail"], FILTER_VALIDATE_EMAIL)) {
            $message = "Invalid UserEmail";
        }
    }

    /* Validation to check if gender is selected */
    if (!isset($message)) {
        if (!isset($_POST["gender"])) {
            $message = " Gender field is required";
        }
    }

    /* Validation to check if Terms and Conditions are accepted */
    if (!isset($message)) {
        if (!isset($_POST["terms"])) {
            $message = "Accept Terms and conditions before submit";
        }
    }

    if (!isset($message)) {
        require_once("dbcontroller.php");
        $db_handle = new DBController();
        $query = "SELECT * FROM registered_users where email = '" . $_POST["userEmail"] . "'";
        $count = $db_handle->numRows($query);

        if ($count == 0) {
            $query = "INSERT INTO registered_users (user_name, first_name, last_name, password, email, gender) VALUES
			('" . $_POST["userName"] . "', '" . $_POST["firstName"] . "', '" . $_POST["lastName"] . "', '" . md5($_POST["password"]) . "', '" . $_POST["userEmail"] . "', '" . $_POST["gender"] . "')";
            $current_id = $db_handle->insertQuery($query);
            if (!empty($current_id)) {
                $actual_link = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]" . "activate.php?id=" . $current_id;
                $toEmail = $_POST["userEmail"];
                $subject = "User Registration Activation Email";
                $content = "Click this link to activate your account. <a href='" . $actual_link . "'>" . $actual_link . "</a>";
                echo $content;
                $mailHeaders = "From: Admin\r\n";
                if (mail($toEmail, $subject, $content, $mailHeaders)) {
                    $message = "You have registered and the activation mail is sent to your email. Click the activation link to activate you account.";
                }
                unset($_POST);
            } else {
                $message = "Problem in registration. Try Again!";
            }
        } else {
            $message = "User Email is already in use.";
        }
    }
}
?>
<html>
<head>
<title>PHP User Registration Form</title>
<link href="style.css" type="text/css" rel="stylesheet" />
</head>
<body>
<form name="frmRegistration" method="post" action="">
<table border="0" width="500" align="center" class="demo-table">
<?php if(isset($message)) { ?>
<div class="message"><?php echo $message; ?></div>
<?php } ?>
<tr>
<td>Username</td>
<td><input type="text" class="demoInputBox" name="userName" value="<?php if(isset($_POST['userName'])) echo $_POST['userName']; ?>"></td>
</tr>
<tr>
<td>First Name</td>
<td><input type="text" class="demoInputBox" name="firstName" value="<?php if(isset($_POST['firstName'])) echo $_POST['firstName']; ?>"></td>
</tr>
<tr>
<td>Last Name</td>
<td><input type="text" class="demoInputBox" name="lastName" value="<?php if(isset($_POST['lastName'])) echo $_POST['lastName']; ?>"></td>
</tr>
<tr>
<td>Password</td>
<td><input type="password" class="demoInputBox" name="password" value=""></td>
</tr>
<tr>
<td>Confirm Password</td>
<td><input type="password" class="demoInputBox" name="confirm_password" value=""></td>
</tr>
<tr>
<td>Email</td>
<td><input type="text" class="demoInputBox" name="userEmail" value="<?php if(isset($_POST['userEmail'])) echo $_POST['userEmail']; ?>"></td>
</tr>
<tr>
<td>Gender</td>
<td><input type="radio" name="gender" value="Male" <?php if(isset($_POST['gender']) && $_POST['gender']=="Male") { ?>checked<?php  } ?>> Male
<input type="radio" name="gender" value="Female" <?php if(isset($_POST['gender']) && $_POST['gender']=="Female") { ?>checked<?php  } ?>> Female
</td>
</tr>
<tr>
<td></td>
<td><input type="checkbox" name="terms"> I accept Terms and Conditions</td>
</tr>
</table>
<div>
<input type="submit" name="submit" value="Register" class="btnRegister">
</div>
</form>
</body></html>