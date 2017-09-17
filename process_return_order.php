<?php
if(!session_id()) session_start();
require('inc/config.php');
require('inc/functions.php');
require('include/page_header.php');
if($_SESSION['role'] == 'member' || $_SESSION['role'] == 'S') {
    echo "<br/><br/><h3 style='color:red'>Unauthorized access</h3>";
    require('include/page_footer.php');
    exit();
}

$sql = "SELECT * FROM courier_order co JOIN transaction t ON co.trans_id = t.trans_id AND co.status='ORDERED' AND co.order_for = 'RETURN' ".
    " AND t.copy_id IN (SELECT copy_id FROM item_details WHERE store_id = '" . $_SESSION["store_id"] . "' AND status='A')" .
    " JOIN member m ON co.placed_by = m.member_id ".
    " JOIN item_master itm ON co.item_id = itm.item_id " .
    " ORDER BY order_date";

$res = $conn->query($sql);
if(!$res || $res->num_rows == 0){
    echo "<br/><br/><h3 style='color:blue'>Currently, there are no return orders pending to be processed for the items of this store!</h3>";
    require('include/page_footer.php');
    exit();
}
$courier_service_rows = get_rows($conn, "courier_service", "*", " status='A' ");
if(!$courier_service_rows){
    echo "<h3 style='color:red'> No courier services available with the store to process orders... </h3>";
    if($_SESSION['role'] == 'M')
        echo '<br/>	<a href="manage_courier_services.php" class="btn btn-primary">&nbsp; Add courier service &nbsp;</a>';
    exit();
}

$php_array = array();
while ($row = $courier_service_rows->fetch_array()) {
    $php_array[$row['service_id']] = $row['service_name'] . ",  " . $row['address'] ;
}

?>
    <form onsubmit="return validate_chk();" method="post" action="process_order2.php" name="process_ret_order" id="process_ret_order">

        <h2 class="form-signin-heading" align="center"> Return Orders Placed</h2>
        <br/>
        <table class="table">
            <tr class="info">
                <td valign="middle">

                    <label >Assign Selected Orders To:</label>
                    <select style="width:200px" name="courier_service_id" class='input-sm' id="courier_service_id">
                        <option value="Select">Select</option>
                        <?php

                        foreach ($php_array as $key => $value) {
                            ?>
                            <option value="<?php echo $key ?>"><?php echo $value ?></option>
                        <?php } ?>
                    </select>




                </td>
                <td>
                    <input class="btn btn-info" type="submit" name="process_ret_order" value="Process Orders"/>
                </td>
            </tr>

        </table>

        <table class="table table-bordered table-hover">
            <thead>
            <tr class="success">
                <th/><th>Order ID</th><th>Title</th><th>Order Date</th><th>Pick Up Address</th><th>Item Issue Details</th><td align="right"><b>Amount Paid<br/>(Rent+Courier Charges)</b></td>

            </tr>
            </thead>
            <?php

            while ($row = $res->fetch_array()) {
                // echo $_SESSION['store_address'] . $row['delivery_address'];

                $distance = calculate_distance($_SESSION['store_address'], $row['delivery_address']);

                echo "<tr class='default'>";
                echo '<td width="150px"><img class="img_vrs" src="' . $row['cover_image'] . '"></td>';
                echo '<td><input type="checkbox" name="item_list[]" value="' . $row["order_id"] . '"/> ';
                echo $row['order_id'] . " </td><td> " . $row['title'] . " </td><td> " . date('d/m/Y', strtotime($row['order_date']));

                echo "</td><td>" . $row['delivery_address'] . "<br/>(Distance from store: " . $distance. ")";

                echo "</td><td>";
                echo "Issue Date: " . date('d/m/Y', strtotime($row["loan_date"]));
                echo "<br/> Issued To: " . $row['user_name'];
                echo "<br/>Copy ID: " . $row["copy_id"];
                echo "</td><td align='right'> Rs." . $row["rent_amount"];

                echo "</td></tr>";
            }
            ?>
        </table>

    </form>

    <script>

        function validate_chk() {

            var checkboxes = document.getElementsByName("item_list[]");
            var checkboxesChecked = [];
            // loop over them all
            for (var i = 0; i < checkboxes.length; i++) {
                // And stick the checked ones onto an array...
                if (checkboxes[i].checked) {
                    checkboxesChecked.push(checkboxes[i]);
                }
            }
            if (checkboxesChecked.length == 0) {
                alert("Select at least one order to proceed");
                return false;
            }
            if(document.getElementById("courier_service_id").value == "Select"){
                alert("Select the courier service");
                return false;
            }
            if(!confirm("Are you sure you want to proceed with processing the selected orders?"))
                return false;

            return true;
        }
    </script>
<?php


require('include/page_footer.php');
?>