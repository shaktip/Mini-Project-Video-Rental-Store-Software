<?php
if(!session_id()) session_start();
if (!isset($_SESSION['role'])) 
	header("location:index.php");
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
            <div class="adjust-nav">
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
                                $link = "emp_home.php";
                            }

                            ?>
                            <a class="navbar-brand" href="<?php echo $link ?>">
                                <img src="assets/img/logo.jpg" width="150px" height="50px"/>
                            </a>
                        </td>
                        <td width="60%">
<!--                            <marquee>-->
                            <span style="font-size:xx-large; color:#d9edf7; align:center">
                                 Video Rental Store

                            </span>
<!--                            </marquee>-->
                        </td>
                        <td>
                             <span class="logout-spn" style="font-size:large">
                                 <?php echo "Welcome " . $_SESSION['logged_in_user']; ?>
                                 <br/>
                              <a href="logout.php" style="color:#fff; font-size:medium">Logout</a>
                            </span>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
        <!-- /. NAV TOP  -->
        <nav class="navbar-default navbar-side" role="navigation">
            <div class="sidebar-collapse">
                <ul class="nav" id="main-menu">
                    <li>

                        <?php if($_SESSION['role'] == 'member') {
                            ?>

                            <a href="member_home.php"><i class="fa fa-desktop "></i>My profile</a>
                            <?php
                        }
                        else {
                        ?>
                            <a href="emp_home.php"><i class="fa fa-desktop "></i>My profile</a>
                            <?php
                        }
                        ?>
                    </li>

                   

                 	<li>
                        <a href="change_password.php"><i class="fa fa-qrcode "></i>Change Password</a>
                    </li>
                    
                    <?php if($_SESSION['role'] != 'member')
            		{
            		?>
                   
					<li class="dropdown" id="view_links" name="view_links">
					<?php if($_SESSION['role'] == 'S' || $_SESSION['role'] == 'M')
                    	{
  							echo '<a class="dropbtn"><i class="fa fa-qrcode "></i>Manage</a>';
  					 	}
  						elseif($_SESSION['role'] == 'C') {
  							echo '<a class="dropbtn"><i class="fa fa-qrcode "></i>View</a>';
  						}
  					?>
  						<div class="dropdown-content">
  							<a href="manage_emp.php">Employees</a>
  							<?php if($_SESSION['role'] == 'M')
                    		{
                    		?>
    						<a href="manage_items.php">Items</a>
                            <?php } ?>
    						<a href="manage_customers.php">Members</a>

    						<?php if($_SESSION['role'] != 'M')
                    		{
                    		?>
    						<a href="manage_stores.php">Stores</a>
    						<?php } ?>
                            <a href="manage_courier_services.php">Courier Services</a>
  						</div>
					</li>
					<?php } ?>
					
					<?php if($_SESSION['role'] == 'M' )
            		{
            		?>
            		<li>
						<a href="manage_stores.php"><i class="fa fa-bar-chart-o"></i>View Stores</a>
					</li>
					<?php } ?>
					<?php if($_SESSION['role'] != 'S')
            		{
            		?>
                        <li class="dropdown" id="view_links" name="view_links">
                            <?php if($_SESSION['role'] == 'M' || $_SESSION['role'] == 'C')
                            {
                                echo '<a class="dropbtn"><i class="fa fa-qrcode "></i>Item Transaction</a>';
                                echo '<div class="dropdown-content">';
                                echo '<a href="issue_item.php">Issue</a>';
                                echo '<a href = "return_item.php" >Return/Lost/Damaged</a>';
                                echo '<a href="process_order.php">Process Rent Order</a>';
                                echo '<a href="process_return_order.php">Process Return Order</a>';
                                echo '<a href="collect_return_courier.php">Collect Return Courier</a>';
                            }
                            elseif($_SESSION['role'] == 'member') {
                                echo '<a class="dropbtn"><i class="fa fa-qrcode "></i>Orders</a>';
                                echo '<div class="dropdown-content">';
                                echo '<a href="order_item.php">Rent</a>';
                                echo '<a href = "return_order.php" >Return/Lost/Damaged</a>';
                                echo '<a href = "cancel_order.php" >My Orders</a>';
                            }
                            ?>

                            </div>
                        </li>

                    <?php }
                    if($_SESSION['role'] == 'M' ){
                    ?>
                        <li>
                            <a href="store_report.php"><i class="fa fa-bar-chart-o"></i>Store Report</a>
                        </li>
                   
                    <?php }
                        if($_SESSION['role'] == 'S' ){
                        ?>
                        <li>
                            <a href="store_report_for_SA.php"><i class="fa fa-bar-chart-o"></i>Store Report</a>
                        </li>

                    <?php } ?>

                </ul>


        </nav>
    </div>


        <!-- /. NAV SIDE  -->
        <div id="page-wrapper" >
            <div id="page-inner" style="width:98%">
