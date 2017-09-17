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

$sql = "SELECT * FROM courier_order co JOIN transaction t ON co.trans_id = t.trans_id AND co.status='PROCESSED' AND co.order_for = 'RETURN' ".
    " AND t.copy_id IN (SELECT copy_id FROM item_details WHERE store_id = '" . $_SESSION["store_id"] . "' AND status='A')" .
    " JOIN member m ON co.placed_by = m.member_id ".
    " JOIN item_master itm ON co.item_id = itm.item_id " .
    " JOIN courier_service cs ON cs.service_id = co.courier_via ".
    " ORDER BY order_date";

$res = $conn->query($sql);
if(!$res || $res->num_rows == 0){
    echo "<br/><br/><h3 style='color:blue'>Currently, there are no return orders pending to be collected for the items of this store!</h3>";
    require('include/page_footer.php');
    exit();
}


?>
    <form onsubmit="return validate_chk();" method="post" action="process_order2.php" name="collect_ret_couriers" id="collect_ret_couriers">

        <h2 class="form-signin-heading" align="center"> Return Couriers To Be Collected </h2>

        <table width="100%">
            <tr>
                <td align="right">
                    <input class="btn btn-info" type="submit" name="collect_ret_couriers" value="Couriers Collected"/>
                </td>
            </tr>

        </table>
        <br/>

        <table class="table table-bordered table-hover">
            <thead>
            <tr class="success">
                <th/><th>Title</th><th>Issued Item Details</th><th>Order Details</th><th>Pick Up Address</th><th>Courier Details</th>

            </tr>
            </thead>
            <?php

            while ($row = $res->fetch_array()) {
                // echo $_SESSION['store_address'] . $row['delivery_address'];

                $distance = calculate_distance($_SESSION['store_address'], $row['delivery_address']);

                echo "<tr class='default'>";
                echo '<td width="150px"><img class="img_vrs" src="' . $row['cover_image'] . '"></td>';
                echo '<td><input type="checkbox" name="item_list[]" value="' . $row["order_id"] . '"/> ' . $row['title'];

                echo "</td><td>";
                echo "Issue Date: " . date('d/m/Y', strtotime($row["loan_date"]));
                echo "<br/> Issued To: " . $row['user_name'];
                echo "<br/>Copy ID: " . $row["copy_id"];
                echo " </td><td> Order ID: " . $row['order_id'] . " <br/> Order Date: " . date('d/m/Y', strtotime($row['order_date']));
                echo "<br/> Amount Paid: Rs." . $row["rent_amount"];

                echo "</td><td>" . $row['delivery_address'] . "<br/>(Distance from store: " . $distance. ")";

                echo "</td><td> Courier Service ID: " . $row["courier_via"];
                echo "<br/> Name: " . $row["service_name"];
                echo "<br/> Contact #: " . $row["contact_number"];

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