<?php
require('inc/config.php');
require('inc/functions.php');
require('include/header.php');

?>
<div class="container" style="width:300px">
	<form action = "submit.php" method="post" name="login_member" id="login_member" autocomplete="off">
		<h2 class="form-signin-heading">Member Login</h2>

        <label for="UserName" class="sr-only">User Name</label>
        <input type="text" name="UserName" id="UserName" class=" form-control" placeholder="User Name" required autofocus/>

		<label for="Password" class="sr-only">Password</label>
        <input type="password" name="Password" id="Password" class="form-control" placeholder="Password" required pattern=".{3,20}" title="3 to 20 characters." />
		<div class="alert fade in" style='color:red'>
			<?php 
				if(isset($_SESSION['error'])){ 
					echo  $_SESSION['error'];
					unset($_SESSION['error']);
				}				
			?>
		</div><!-- Display Error Container -->
		<input type = "Submit" name="MemberSubmit" id="MemberSubmit" value="Submit" class="btn btn-lg btn-primary btn-block" />
		<a class="btn btn-lg btn-info btn-block" href="register_member.php">New user? Sign up!</a>
		<!-- <button type="button" class="btn btn-lg btn-info btn-block" data-toggle="modal" data-target="#registration_modal">New user? Sign up!</button>
		-->
	</form>
</div>


<?php require('include/footer.php');?>