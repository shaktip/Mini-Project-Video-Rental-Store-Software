<?php
require('inc/config.php');
require('inc/functions.php');
require('include/page_header.php');
if($_SESSION['role'] != 'S') {
	echo "<br/><br/><h3 style='color:red'>Unauthorized access</h3>";
}
else {
    $action = $_GET['action'];
    $msg = 'Add New Store';
    $btn_name = 'add_store';
    if ($action == 'e') {
        $btn_name = 'edit_store';
        $msg = 'Edit Store Details';
        $_SESSION['sel_store_id'] = $store_id = $_GET['id'];
        $ret_value = get_rows($conn, "store", "*", "store_id = " . $store_id);
        $store_row = $ret_value->fetch_array();
    }
    $store_id = add_edit_store($conn);
    if ($store_id != FALSE) {
        if ($store_id == "updated") {
            echo "<h2>Details are updated successfully! </h2>";
        } else {
            echo "<h2>Details are added successfully! </h2>";
        }

        echo "<table style='padding:5px'>";
        echo "<tr><td style='padding:5px'>Address: </td><td style='padding:5px'>" . $_POST['address'] . "</td>";
        echo "<tr><td style='padding:5px'>City: </td><td style='padding:5px'> " . $_POST['city'] . "</td>";
        echo "<tr><td style='padding:5px'>Pin Code: </td><td style='padding:5px'> " . $_POST['pin_code'] . "</td>";
        echo "<tr><td style='padding:5px'>Contact Number: </td><td style='padding:5px'> " . $_POST['contact_number'] . "</td>";
        if ($store_id != "updated")
            echo "<tr><td style='padding:5px'>New Store id: </td><td style='padding:5px'> <b>" . $store_id . "</b></td>";
        echo "</table>";
        echo '<br/>	<a href="manage_stores.php" class="btn btn-primary">&nbsp; OK &nbsp;</a>';
        if ($store_id != "updated")
            echo '&nbsp; &nbsp; &nbsp; <a href="add_edit_emp.php?action=a&store_id=' . $store_id . '" class="btn btn-primary"> Add Staff </a>';
        $_POST['city'] = '';
        $_POST['address'] = '';
        $_POST['pin_code'] = '';
        $_POST['contact_number'] = '';
    } else {
        ?>


        <!-- HTML Form -->
        <form action="<?php $_PHP_SELF ?>" method="post" name="store" id="store" autocomplete="off">


            <h2 class="form-signin-heading" style="text-align:center; width:500px;"><?php echo $msg ?></h2>

            <!-- Modal Body -->
            <div class="modal-body">

                <div class="form-group">
                    <label for="address" class="reg_label">Address</label>
                    <input value="<?php if ($btn_name == 'add_store') echo isset($_POST['address']) ? $_POST['address'] : ''; else echo $store_row['address']; ?>"
                           class='input-sm' type="text" name="address" id="address" required>
                </div>
                <div class="form-group">
                    <label for="city" class="reg_label">City</label>
                    <input value="<?php if ($btn_name == 'add_store') echo isset($_POST['city']) ? $_POST['city'] : ''; else echo $store_row['city']; ?>"
                           class='input-sm' type="text" name="city" id="city" required pattern="^[a-zA-Z ]*$"
                           title="Only alphabets allowed">
                </div>
                <div class="form-group">
                    <label for="pin_code" class="reg_label">Pin Code</label>
                    <input value="<?php if ($btn_name == 'add_store') echo isset($_POST['pin_code']) ? $_POST['pin_code'] : ''; else echo $store_row['pin_code']; ?>"
                           class='input-sm' type="text" name="pin_code" id="pin_code" required pattern="^[1-9][0-9]{5}$"
                           title="6 digits, not starting with 0">
                </div>
                <div class="form-group">
                    <label for="contact_number" class="reg_label">Contact Number</label>
                    <input value="<?php if ($btn_name == 'add_store') echo isset($_POST['contact_number']) ? $_POST['contact_number'] : ''; else echo $store_row['contact_number']; ?>"
                           class='input-sm' type="text" name="contact_number" id="contact_number" required pattern="^0[1-9][0-9]{8}$"
                           title="10 digits starting with 0">
                </div>

                <div class="form-group">
                    <label for="store_latitude" class="reg_label">Latitude</label>
                    <input value="<?php if ($btn_name == 'add_store') echo isset($_POST['latitude']) ? $_POST['latitude'] : ''; else echo $store_row['latitude']; ?>"
                           class='input-sm' type="text" name="store_latitude" id="store_latitude" required
                           title="only numbers allowed">
                </div>

                <div class="form-group">
                    <label for="store_longitude" class="reg_label">Longitude</label>
                    <input value="<?php if ($btn_name == 'add_store') echo isset($_POST['longitude']) ? $_POST['longitude'] : ''; else echo $store_row['longitude']; ?>"
                           class='input-sm' type="text" name="store_longitude" id="store_longitude" required
                           title="only numbers allowed">
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
                    <a href="manage_stores.php" class="btn  btn-lg btn-default">Cancel</a>

                </div>

            </div>
        </form>

        <?php
    }
}
 require('include/page_footer.php');
?>

