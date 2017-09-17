<?php
require('inc/config.php');
require('inc/functions.php');
require('include/page_header.php');
if($_SESSION['role'] == 'member' || $_SESSION['role']=='S' ) {
	echo "<br/><br/><h3 style='color:red'>Unauthorized access</h3>";
}
elseif(!empty($_POST) && isset($_POST['process_order'])) {

	$selected_items = $_POST['item_list'];
	$courier_service_id = safe_input($conn, $_POST['courier_service_id']);

    echo '<h2 class="form-signin-heading" style="text-align:center; width:700px;">Orders Processed</h2>';

    $res = fn_process_orders($conn, $selected_items, $courier_service_id);

}
elseif(!empty($_POST) && isset($_POST['process_ret_order'])) {

    $selected_items = $_POST['item_list'];
    $courier_service_id = safe_input($conn, $_POST['courier_service_id']);

    echo '<h2 class="form-signin-heading" style="text-align:center; width:700px;">Orders Processed</h2>';

    $res = fn_process_ret_orders($conn, $selected_items, $courier_service_id);


}
elseif(!empty($_POST) && isset($_POST['collect_ret_couriers'])) {

    $selected_items = $_POST['item_list'];

    echo '<h2 class="form-signin-heading" style="text-align:center; width:700px;">Couriers Collected</h2>';

    $res = fn_collect_ret_couriers($conn, $selected_items);


}
            	 
if(isset($_SESSION['error']) && $_SESSION['error'] != ''){
    echo '<div class="alert fade in" style="text-align:center; width:400px; color:red">';
	echo $_SESSION['error']; 
	unset($_SESSION['error']);
    echo '</div>';
}
elseif($res == True) {
    if(!(!empty($_POST) && isset($_POST['collect_ret_couriers']))) {
        $sql = "SELECT * FROM courier_service WHERE service_id=$courier_service_id";
        $res = $conn->query($sql);
        if ($res->num_rows == 0) {
            $conn->rollback();
            $_SESSION['error'] = 'Some error occurred. Details could not be updated...';
            exit();
        }
        $courier_service_row = $res->fetch_array();

        ?>
        <table class="table">
            <tr class="success">
                <td>Date: &nbsp; &nbsp;&nbsp;&nbsp;<?php echo date("Y-m-d H:i:s"); ?> </td>
                <td align="right">Orders Placed Via: <?php echo $courier_service_row['service_name']; ?></td>
            </tr>
            <tr class="success">

                <td>Processed By: &nbsp; &nbsp;&nbsp;&nbsp;<?php echo $_SESSION['logged_in_user']; ?> </td>
                <td align="right">Contact Number: <?php echo $courier_service_row['contact_number']; ?></td>
            </tr>
            <tr>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
            </tr>

        </table>
        <?php
    }
    else{
        ?>
        <table class="table">
            <tr class="success">
                <td>Date: &nbsp; &nbsp;&nbsp;&nbsp;<?php echo date("Y-m-d H:i:s"); ?> </td>
                <td>Processed By: &nbsp; &nbsp;&nbsp;&nbsp;<?php echo $_SESSION['logged_in_user']; ?> </td>
            </tr>

            <tr>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
            </tr>

        </table>
<?php
    }
}
require('include/page_footer.php');
?>

