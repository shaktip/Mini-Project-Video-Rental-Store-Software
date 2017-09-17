<?php
require('inc/config.php');
require('inc/functions.php');
require('include/page_header.php');
if($_SESSION['role'] == 'C' || $_SESSION['role'] == 'member') {
    echo "<br/><br/><h3 style='color:red'>Unauthorized access</h3>";
}
else {
    $action = $_GET['action'];
    $msg = 'Add New Courier Service';
    $btn_name = 'add_service';
    if ($action == 'e') {
        $btn_name = 'edit_service';
        $msg = 'Edit Courier Service Details';
        $_SESSION['sel_service_id'] = $service_id = $_GET['id'];
        $ret_value = get_rows($conn, "courier_service", "*", "service_id = " . $service_id);
        $service_row = $ret_value->fetch_array();
    }
    $service_id = add_edit_service($conn);
    if ($service_id != FALSE) {
        if ($service_id == "updated") {
            echo "<h2>Details are updated successfully! </h2>";
        } else {
            echo "<h2>Details are added successfully! </h2>";
        }

        echo "<table style='padding:5px'>";
        echo "<tr><td style='padding:5px'>Service Name: </td><td style='padding:5px'> " . $_POST['service_name'] . "</td>";
        echo "<tr><td style='padding:5px'>Address: </td><td style='padding:5px'>" . $_POST['address'] . "</td>";
        echo "<tr><td style='padding:5px'>Email: </td><td style='padding:5px'> " . $_POST['email'] . "</td>";
        echo "<tr><td style='padding:5px'>Contact Number: </td><td style='padding:5px'> " . $_POST['contact_number'] . "</td>";
        if ($service_id != "updated")
            echo "<tr><td style='padding:5px'>New Courier Service id: </td><td style='padding:5px'> <b>" . $service_id . "</b></td>";
        echo "</table>";
        echo '<br/>	<a href="manage_courier_services.php" class="btn btn-primary">&nbsp; OK &nbsp;</a>';
        $_POST['city'] = '';
        $_POST['address'] = '';
        $_POST['pin_code'] = '';
        $_POST['contact_number'] = '';
    } else {
        ?>


        <!-- HTML Form -->
        <form action="<?php $_PHP_SELF ?>" method="post" name="courier_service" id="courier_service" autocomplete="off">


            <h2 class="form-signin-heading" style="width: 500px" align="center"><?php echo $msg ?></h2>

            <!-- Modal Body -->
            <div class="modal-body">
                <div class="form-group">
                    <label for="service_name" class="reg_label">Service Name</label>
                    <input value="<?php if ($btn_name == 'add_service') echo isset($_POST['service_name']) ? $_POST['service_name'] : ''; else echo $service_row['service_name']; ?>"
                           class='input-sm' type="text" name="service_name" id="service_name" required
                           >
                </div>

                <div class="form-group">
                    <label for="address" class="reg_label">Address</label>
                    <input value="<?php if ($btn_name == 'add_service') echo isset($_POST['address']) ? $_POST['address'] : ''; else echo $service_row['address']; ?>"
                           class='input-sm' type="text" name="address" id="address" required>
                </div>

                <div class="form-group">
                    <label for="contact_number" class="reg_label">Contact Number</label>
                    <input value="<?php if ($btn_name == 'add_service') echo isset($_POST['contact_number']) ? $_POST['contact_number'] : ''; else echo $service_row['contact_number']; ?>"
                           class='input-sm' type="text" name="contact_number" id="contact_number" required pattern="^0[1-9][0-9]{8}$"
                           title="10 digits starting with 0">
                </div>

                <div class="form-group">
                    <label for="email" class="reg_label">Email address</label>
                    <input value="<?php if ($btn_name == 'add_service') echo isset($_POST['email']) ? $_POST['email'] : ''; else echo $service_row['email_id'];  ?>"
                           class='input-sm' type="email" name="email" id="Email" required>
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
                    <input type="submit" name="<?php echo $btn_name; ?>" class="btn btn-lg btn-success" value="Save"/>
                    &nbsp;&nbsp;&nbsp;&nbsp;
                    <a href="manage_courier_services.php" class="btn  btn-lg btn-default">Cancel</a>

                </div>

            </div>
        </form>

        <?php
    }
}
require('include/page_footer.php');
?>

