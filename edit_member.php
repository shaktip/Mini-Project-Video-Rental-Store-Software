<?php
require('include/page_header.php');
require('inc/config.php');
require('inc/functions.php');

if($_SESSION['role'] != 'member' ) {
    echo "<br/><br/><h3 style='color:red'>Unauthorized access</h3>";
    require('include/page_footer.php');
    exit();
}

$member_id = $_SESSION["logged_in_id"];
if(update_member($conn, $member_id)){
    echo "<br/><br/><h3 style='margin-left: 100px'>Your details are updated successfully. ";
    echo "<br/><br/><a href='member_home.php' class='btn btn-info'> &nbsp;&nbsp;OK &nbsp;&nbsp;</a></h3>";
    require('include/page_footer.php');
    exit();
}

$member_row = ($conn->query("SELECT * FROM member WHERE member_id=$member_id"))->fetch_array();

?>

    <h2 class="form-signin-heading" style="text-align:center; width:500px;">Update My Details</h2>
<br/>
    <!-- HTML Form -->
    <form action="<?php $_PHP_SELF ?>" method="post" name="udpate_member" id="udpate_member" autocomplete="off">

            <div class="form-group">
                <label for="UserName" class="reg_label">User name</label>
                <input value="<?php echo isset($_POST['UserName']) ? $_POST['UserName'] : $member_row["user_name"] ?>"
                       disabled class='input-sm' type="text" name="UserName" id="UserName" required
                       pattern="^.{3,30}$" title="min 3, max 30 characters." autofocus>

            </div>

            <div class="form-group">
                <label for="email" class="reg_label">Email address</label>
                <input value="<?php echo isset($_POST['email']) ? $_POST['email'] : $member_row["email"] ?>"
                       class='input-sm' type="email" name="email" id="Email" required>
            </div>
            <div class="form-group">
                <label for="address" class="reg_label">Address</label>
                <input value="<?php echo isset($_POST['address']) ? $_POST['address'] : $member_row["address"] ?>"
                       class='input-sm' type="text" name="address" id="address" required>
            </div>
            <div class="form-group">
                <label for="MobileNumber" class="reg_label">Mobile Number</label>
                <input value="<?php echo isset($_POST['MobileNumber']) ? $_POST['MobileNumber'] : $member_row["contact_number"] ?>"
                       class='input-sm' type="text" name="MobileNumber" id="MobileNumber" required pattern="^[789][0-9]{9}$"
                       title="10 digits starting with 7/8/9">
            </div>

            <?php
            if(isset($_SESSION['error'])){
                echo '<div class="alert fade in" style="color: red">';
                echo $_SESSION['error'];
                unset($_SESSION['error']);
                echo '</div>';
            }
            ?>
            <br/>

            <div class="form-group" style="margin-left: 100px">
        <input type="submit" name="udpate_member" class="btn btn-lg btn-success" value="Save" id="submit"/>
        <a class="btn btn-lg btn-default" href="member_home.php">
            Cancel</a>
            </div>


    </form>



<?php require('include/page_footer.php');



?>

