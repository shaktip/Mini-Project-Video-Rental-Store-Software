<?php
require('include/header.php');

require('inc/config.php');
require('inc/functions.php');

if(reg_member($conn)){
    echo "<h3 align='center' style='color:blue'>Your details are registered and activation email is sent to your email id. <br/><br/>Check your email and click the activation link to activate your account with VRS. </h3>";
    echo "<br/><br/><br/><div align='center'>If you do not find an email from us in your inbox, please check spam / junk mail folder.</div>";
    require('include/footer.php');
    exit();
}
?>
<div align="center">
    <h2 class="form-signin-heading" >New Member</h2>

        <!-- HTML Form -->
        <form action="<?php $_PHP_SELF ?>" method="post" name="register_member" id="regiser_member" autocomplete="off">

            <div class="container" align="left" style="margin-left: 400px; width:700px; ">

            <div class="form-group">
                <label for="NewUserName" class="reg_label">User name</label>
                <input value="<?php echo isset($_POST['NewUserName']) ? $_POST['NewUserName'] : '' ?>"
                       class='input-sm' type="text" name="NewUserName" id="NewUserName" required pattern="^.{3,30}$" title="min 3, max 30 characters." autofocus>
            </div>
            <div class="form-group">
                <label for="RegPassword" class="reg_label">New password</label>
                <input value="<?php echo isset($_POST['RegPassword']) ? $_POST['RegPassword'] : '' ?>"
                       class='input-sm' type="password" name="RegPassword" id="RegPassword" required pattern="^.{5,20}$" title="5 to 20 characters.">
            </div>
            <div class="form-group">
                <label for="ConfirmPassword" class="reg_label">Confirm password</label>
                <input value="<?php echo isset($_POST['ConfirmPassword']) ? $_POST['ConfirmPassword'] : '' ?>"
                       class='input-sm' type="password" onblur="check_pass()" name="ConfirmPassword" id="ConfirmPassword" required pattern="^.{5,20}$" title="5 to 20 characters.">
                <span id="message" style="color:red;"></span>
            </div>
            <div class="form-group">
                <label for="email" class="reg_label">Email address</label>
                <input value="<?php echo isset($_POST['email']) ? $_POST['email'] : '' ?>"
                       class='input-sm' type="email" name="email" id="Email" required>
            </div>
            <div class="form-group">
                <label for="address" class="reg_label">Address</label>
                <input value="<?php echo isset($_POST['address']) ? $_POST['address'] : '' ?>"
                       class='input-sm' type="text" name="address" id="address" required>
            </div>
            <div class="form-group">
                <label for="MobileNumber" class="reg_label">Mobile Number</label>
                <input value="<?php echo isset($_POST['MobileNumber']) ? $_POST['MobileNumber'] : '' ?>"
                       class='input-sm' type="text" name="MobileNumber" id="MobileNumber" required pattern="^[789][0-9]{9}$" title="10 digits starting with 7/8/9">
            </div>
            <div class="form-group" >
                <label class="reg_label">Membership Type</label>
                <input type = "radio" name = "MembershipType" id = "platinum" value = "Platinum" checked = "checked" onchange = "deposit_amt()"/>
          		<label for = "platinum"> Platinum &nbsp;&nbsp; </label>
          		<input type = "radio" name = "MembershipType" id = "gold" value = "Gold" onchange = "deposit_amt()" />
          		<label for = "gold"> Gold &nbsp;&nbsp; </label>
          		<input type = "radio" name = "MembershipType" id = "silver" value = "Silver" onchange = "deposit_amt()" />
          		<label for = "silver"> Silver &nbsp;&nbsp; </label>
          		<span id="membershipDeposit" style="color:blue;"> &nbsp;&nbsp; Deposit Amount: <b>Rs.1500</b></span>
          		<input type="hidden" id="memType" name="memType" value="P"/>
          		<input type="hidden" id="amt" name="amt" value="1500"/>
            </div>
            

            	<?php 
					if(isset($_SESSION['error'])){
					    echo '<div class="alert fade in">';
						echo $_SESSION['error']; 
						unset($_SESSION['error']);
						echo '</div>';
					}				
				?>

        


            </div>
            <input type="submit" name="RegisterMember" class="btn btn-lg btn-success" value="Submit" id="submit"/>
            <a class="btn btn-lg btn-default" href="main_member.php">
                Cancel</a>



        </form>
</div>

<script>
    function check_pass(){
 	   if (document.getElementById('RegPassword').value===document.getElementById('ConfirmPassword').value){
 			document.getElementById('submit').disabled = false;
 			document.getElementById('message').innerHTML = "";
		}
		else {
 			document.getElementById('submit').disabled = true;
 			document.getElementById('message').innerHTML = " &nbsp;&nbsp;  &nbsp;&nbsp;  &nbsp;&nbsp; Passwords do not match";
 			
		}
    }
    
    function deposit_amt() {
        if (document.getElementById('platinum').checked) {
            document.getElementById('membershipDeposit').innerHTML = " &nbsp;&nbsp; Deposit Amount: <b>Rs.1500</b>";
            document.getElementById('memType').value = "P"
            document.getElementById('amt').value = 1500
        }
        else if (document.getElementById('gold').checked) {
            document.getElementById('membershipDeposit').innerHTML = " &nbsp;&nbsp; Deposit Amount: <b>Rs.1200</b>";
            document.getElementById('memType').value = "G"
            document.getElementById('amt').value = 1200
        }
        else if (document.getElementById('silver').checked) {
            document.getElementById('membershipDeposit').innerHTML = " &nbsp;&nbsp; Deposit Amount: <b>Rs.1000</b>";
            document.getElementById('memType').value = "S"
            document.getElementById('amt').value = 1000
        }
    }
</script>
<?php require('include/footer.php');



?>

