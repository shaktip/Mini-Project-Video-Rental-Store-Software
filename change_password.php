<?php
require('inc/config.php');
require('inc/functions.php');
require('include/page_header.php');

if(change_password($conn)) {
    echo '<h4 class="form-signin-heading" style="text-align:center; width:500px;">Password updated successfully</h4>';
    echo "<h4>Please re-login with your new password and continue</h4>";
    if (isset($_SESSION['role'])) {
        if ($_SESSION['role'] == "member") {
            session_destroy(); /* Destroy started session */
            echo "<a href='main_member.php' class='btn btn-info'> OK </a>";
        } else {
            session_destroy(); /* Destroy started session */
            echo "<a href='main_emp.php' class='btn btn-info'> OK </a>";
        }
    }
    else{
        echo "<a href='index.php' class='btn btn-info'> OK </a>";
    }
    $_POST['current_password'] = '';
    $_POST['new_password'] = '';
    $_POST['confirm_password'] = '';
}
else {
    ?>


    <!-- HTML Form -->
    <form action="<?php $_PHP_SELF ?>" method="post" name="change_password" id="change_password" autocomplete="on">


        <h2 class="form-signin-heading" style="text-align:center; width:500px;">Change Password</h2>

        <!-- Modal Body -->
        <div class="modal-body">
            <div class="form-group">
                <label for="current_password" class="reg_label">Current password</label>
                <input value="<?php echo isset($_POST['current_password']) ? $_POST['current_password'] : '' ?>"
                       class="input-sm" type="password" name="current_password" id="current_password" required pattern=".{3,20}"
                       title="3 to 20 characters.">
            </div>
            <div class="form-group">
                <label for="new_password" class="reg_label">New password</label>
                <input value="<?php echo isset($_POST['new_password']) ? $_POST['new_password'] : '' ?>"
                       class="input-sm" type="password" name="new_password" id="new_password" required pattern=".{3,20}"
                       title="3 to 20 characters.">
            </div>
            <div class="form-group">
                <label for="confirm_password" class="reg_label">Confirm password</label>
                <input value="<?php echo isset($_POST['confirm_password']) ? $_POST['confirm_password'] : '' ?>"
                       class="input-sm" type="password" onblur="check_pass()" name="confirm_password" id="confirm_password" required
                       pattern=".{3,20}" title="3 to 20 characters.">
                <span id="message" style="color:red;"></span>
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
                <input type="submit" name="change_password" class="btn btn-lg btn-success" value="Change Password"/>
                &nbsp;&nbsp;&nbsp;&nbsp;<?php
                if($_SESSION["role"] == "member"){
                    echo '<a href="member_home.php" class="btn  btn-lg btn-default">Cancel</a>';
                }
                else{
                    echo '<a href="emp_home.php" class="btn  btn-lg btn-default">Cancel</a>';
                }
                ?>

                <div class="form-group">
                </div>
    </form>


    <script>

    </script>
    <?php
}
require('include/page_footer.php');
?>

