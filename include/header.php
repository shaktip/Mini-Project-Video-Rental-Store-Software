<?php
if(!session_id()) session_start();
error_reporting(0);
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
                    <td>
                             <span class="logout-spn" style="font-size:small">

                              Hello, <a href="main_member.php" style="color:#fff; font-size:medium">Sign in</a>
                                 <br/>
                                 New to VRS? <a href="register_member.php" style="color:#fff; font-size:medium">Sign up!</a>
                            </span>
                    </td>
                </tr>
            </table>
        </div>
</div>



    <div id="page-inner" style="width:98%">
        <br/>

