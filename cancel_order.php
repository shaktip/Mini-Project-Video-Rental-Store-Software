<?php
if(!session_id()) session_start();
require('inc/config.php');
require('inc/functions.php');
require('include/page_header.php');
if($_SESSION['role'] != 'member' ) {
    echo "<br/><br/><h3 style='color:red'>Unauthorized access</h3>";
    require('include/page_footer.php');
    exit();
}
$member_id=$_SESSION['logged_in_id'];
if(!empty($_POST) && isset($_POST['cancel_order'])){
    $conn->autocommit(false);
    $order_id = safe_input($conn,$_POST['order_id']);
    $order_res  = get_rows($conn,'courier_order', '*', 'order_id=' .$order_id);
    $order_row = $order_res->fetch_array();

    if($order_row["status"] == "ORDERED") {
        $flag = 1;
        $msg = '';
        $sql1 = "UPDATE courier_order SET status='CANCELLED' WHERE order_id=$order_id; ";
        if($order_row["order_for"] == 'RETURN'){
            $trans_id = $order_row["trans_id"];

            $amount = (($conn->query("SELECT rent_amount FROM transaction WHERE trans_id=$trans_id"))->fetch_array())[0];
            $sql2 = "UPDATE member SET deposit_amount = deposit_amount + $amount WHERE member_id=$member_id; ";
            $sql3 = "UPDATE transaction SET rent_amount = NULL WHERE trans_id=$trans_id";

            if(!($conn->query($sql2) && $conn->query($sql3))){
                echo "<h4 style='color:red'>Some error occurred while cancelling order </h4>";
                $conn->rollback();
                $flag = 0;
            }
            else{
                $msg .= " Your payment of Rs.$amount is added to your deposit amount";

            }
        }
        if ($flag == 1 && $conn->query($sql1)) {
            echo "<h4 style='color:blue'>Order cancelled successfully </h4>" . $msg;
            $conn->commit();
        } elseif($flag != 0) {
            $conn->rollback();
            echo "<h4 style='color:red'>Some error occurred while cancelling order </h4>";
        }
    }
    else{
        echo "<h4 style='color:blue'>Sorry, your order could not be cancelled! It is already processed.</h4>";
    }

}

$sql = "SELECT *, co.status order_status FROM courier_order co JOIN item_master itm ON co.item_id = itm.item_id ".
    " JOIN member m ON co.placed_by = m.member_id AND m.member_id = $member_id ORDER BY order_id DESC";
$res = $conn->query($sql);
if($res->num_rows == 0){
    echo "<br/><br/><h3 style='color:blue'>No orders are currently placed !</h3>";
    require('include/page_footer.php');
    exit();
}

?>


        <h2 class="form-signin-heading" align="center"> My Orders </h2>
        <br/>


        <table class="table table-bordered table-hover">
            <thead>
            <tr class="success">
                <th/><th>Order ID</th><th>Title</th><th>Order Date</th><th>Delivery/Pick Up Address</th><th width="120px">Order For</th><th width="120px">Order Status</th>

            </tr>
            </thead>
            <?php

            while ($row = $res->fetch_array()) {
                // echo $_SESSION['store_address'] . $row['delivery_address'];
                echo '<form onsubmit="return confirm(\'Are you sure you want to cancel the selected order?\');" method="post" action="cancel_order.php" name="cancel_order" id="cancel_order">';


                echo "<tr class='default'>";
                echo '<td width="120px"><img class="img_vrs" title="<?php echo $row[\'descr\']; ?>"  src="' . $row['cover_image'] . '"></td>';

                echo " </td><td> " . $row['order_id'] . " </td><td> " . $row['title'] . "<br/>(" . (($row['item_type']=='V')? "Video" : "Music") .") </td><td> " . date('d/m/Y', strtotime($row['order_date']));
                echo "</td><td>" . $row['delivery_address'];
                echo "</td><td>" . $row['order_for'] . "</td>";
                if($row['order_status'] == "ORDERED") {
                    echo '<td>' . $row['order_status'];
                    echo '<br/><br/><input type="submit" name="cancel_order" value="Cancel"/> ';
                    echo "<input type='hidden' name='order_id' id='order_id' value='". $row["order_id"] . "'/>";
                }
                else
                    echo '<td>' . $row['order_status'];


                echo "</td></tr>";
                echo '</form>';
            }
            ?>
        </table>

    </form>


<?php


require('include/page_footer.php');
?>