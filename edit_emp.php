<?php
require('inc/config.php');
require('inc/functions.php');
require('include/page_header.php');
if($_SESSION['role'] == 'member') {
    echo "<br/><br/><h3 style='color:red'>Unauthorized access</h3>";
    require('include/page_footer.php');
    exit();
}


$ret_value = get_rows($conn, "employee", "empid, emp_name, contact_number, address, role, store_id", "empid = " . $_SESSION["logged_in_id"]);
$emp_row = $ret_value->fetch_array();
$store_id = $emp_row["store_id"];

if (edit_employee($conn)) {
    echo "<h2>Details are updated successfully! </h2>";
    echo "<br/>";

    echo "<table style='padding:5px; background-color:lightyellow; margin-left: 50px '>";
    echo "<tr><td style='padding:5px'><b>Employee name: </td><td style='padding:5px'>" . $_POST['emp_name'] . "</td>";
    echo "<tr><td style='padding:5px'><b>Address: </b></td><td style='padding:5px'> " . $_POST['address'] . "</td>";
    echo "<tr><td style='padding:5px'><b>Mobile Number: </b></td><td style='padding:5px'> " . $_POST['mobile_number'] . "</td>";

    echo "</td>";

    echo "</table>";
    echo '<br/>	<a href="emp_home.php" class="btn btn-primary">&nbsp; OK &nbsp;</a>';
    $_POST['emp_name'] = '';
    $_POST['address'] = '';
    $_POST['mobile_number'] = '';
    exit();

}
?>


<!-- HTML Form -->
<form action="<?php $_PHP_SELF ?>" method="post" name="update_emp" id="update_emp" autocomplete="off">


    <h2 class="form-signin-heading" style="text-align:center; width:500px;">Update My Details</h2>

    <!-- Modal Body -->
    <div class="modal-body">
        <div class="form-group">
            <label for="emp_name" class="reg_label">Employee Name</label>
            <input
                   value="<?php echo isset($_POST['emp_name']) ? $_POST['emp_name'] : $emp_row['emp_name']; ?>"
                   class='input-sm' type="text" name="emp_name" id="emp_name" required pattern="^.{3,30}$"
                   title="min 3, max 30 characters." autofocus>
        </div>
        <div class="form-group">
            <label for="address" class="reg_label">Address</label>
            <input
                   value="<?php  echo isset($_POST['address']) ? $_POST['address'] : $emp_row['address']; ?>"
                   class='input-sm' type="text" name="address" id="address" required>
        </div>
        <div class="form-group">
            <label for="mobile_number" class="reg_label">Mobile Number</label>
            <input
                   value="<?php echo isset($_POST['mobile_number']) ? $_POST['mobile_number'] : $emp_row['contact_number']; ?>"
                   class='input-sm' type="text" name="mobile_number" id="mobile_number" required pattern="^[789][0-9]{9}$"
                   title="10 digits starting with 7/8/9">
        </div>


        <div class="alert fade in" style="text-align:center; width:400px; color:red">
            <?php
            if (isset($_SESSION['error'])) {
                echo $_SESSION['error'];
                unset($_SESSION['error']);
            }
            ?>
        </div>
        <div class="form-group">
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            <input type="submit" name="update_emp" class="btn btn-lg btn-success" value="Save"/>
            &nbsp;&nbsp;&nbsp;&nbsp;
            <a href="emp_home.php" class="btn  btn-lg btn-default">Cancel</a>

        </div>

    </div>
</form>


            <?php


     require('include/page_footer.php');
    ?>

