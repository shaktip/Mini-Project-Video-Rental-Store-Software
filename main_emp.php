<?php
if(!session_id()) session_start();

?>
    <!DOCTYPE html>
    <html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <title>VRS</title>

        <!-- BOOTSTRAP STYLES-->

        <link href="assets/css/bootstrap.css" rel="stylesheet" />
        <!-- FONTAWESOME STYLES-->
        <link href="assets/css/font-awesome.css" rel="stylesheet" />
        <!-- CUSTOM STYLES-->
        <link href="assets/css/custom.css" rel="stylesheet" />
        <link href="assets/css/style.css" rel="stylesheet" />

        <!-- GOOGLE FONTS-->
        <link href='http://fonts.googleapis.com/css?family=Open+Sans' rel='stylesheet' type='text/css' />
        <!-- JQUERY SCRIPTS -->
        <script src="assets/js/jquery-1.10.2.js"></script>
        <!-- BOOTSTRAP SCRIPTS -->
        <script src="assets/js/bootstrap.min.js"></script>
        <!-- CUSTOM SCRIPTS -->
        <script src="assets/js/custom.js"></script>
    </head>
<body>

    <div id="wrapper">
        <div class="navbar navbar-inverse navbar-fixed-top">

            <table width="100%" >
                <tr>
                    <td valign="">
                        <?php
                        if(empty($_SESSION['role'])){
                            $link = "index.php";
                        }
                        elseif($_SESSION['role'] == 'member'){
                            $link = "member_home.php";
                        }
                        else{
                            $link = "member_emp.php";
                        }

                        ?>
                        <a class="navbar-brand" href="<?php echo $link ?>">
                            <img src="assets/img/logo.jpg" width="150px" height="50px"/>
                        </a>
                    </td>
                    <td width="60%">
                        <!--                        <marquee>-->
                        <span style="font-size:xx-large; color:#d9edf7">
                                 Video Rental Store

                            </span>
                        <!--                        </marquee>-->
                    </td>
                    <td>&nbsp;</td>

                </tr>
            </table>
        </div>
    </div>



<div id="page-inner" style="width:98%">
    <br/>


    <div class="container" style="width:300px">
	<form action = "submit.php" method="post" name="login_form1" id="login_form1" autocomplete="off">
		<h2 class="form-signin-heading">Employee Login</h2>

        <label for="Empid" class="sr-only">Employee ID</label>
        <input type="text" name="Empid" id="Empid" class="form-control" placeholder="Employee ID" required autofocus>

		<label for="Password" class="sr-only">Password</label>
        <input type="password" name="Password" id="Password" class="form-control" placeholder="Password" required pattern=".{3,20}" title="3 to 20 characters.">
		<div class="alert fade in" style="color:red;">
			<?php
				if(isset($_SESSION['error'])){
					echo $_SESSION['error'];
					unset($_SESSION['error']);
				}
			?>
		</div><!-- Display Error Container -->
		<input type = "Submit" name="EmpSubmit" value="Submit" class="btn btn-lg btn-primary btn-block" />
	</form>
</div>

</div>
</body>
</html>