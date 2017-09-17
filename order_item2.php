<?php
require('inc/config.php');
require('inc/functions.php');
require('include/page_header.php');
if($_SESSION['role'] == 'S') {
    echo "<br/><br/><h3 style='color:red'>Unauthorized access</h3>";
}
elseif(!empty($_POST) && isset($_POST['order_item'])) {
    $member_id = safe_input($conn, $_POST['member_id']);
    $item_id = safe_input($conn, $_POST['item_id']);
    $del_address = $_GET['del_address'];

    $check_order = $conn->query("SELECT * FROM courier_order WHERE placed_by = $member_id AND item_id = $item_id AND status='ORDERED' AND order_for='RENT'");
    if($check_order->num_rows > 0){
        echo "<br/><br/><h3 style='color:blue' align='center'>There is already a rent order placed for the same CD/DVD. <br/><br/> You can place another order once the previous order is processed.</h3>";
        require('include/page_footer.php');
        exit();
    }


    if (isset($_SESSION['member_row'])) {
        $member_row = $_SESSION['member_row'];
        unset($_SESSION['member_row']);

        $sql = "SELECT * FROM item_master WHERE item_id = " . $item_id;
        $res = $conn->query($sql);
        $item_row = $res->fetch_array();

        if ($order_id = order_item($conn, $member_row[0], $item_id, 'RENT', $del_address)) {

            ?>

            <h2 class="form-signin-heading" style="text-align:center; width:700px;">Item ordered</h2>
            <h4>Member information </h4>
            <table class='table table-striped'>
            <tr class='danger'>
                <th>ID</th>
                <th>User Name</th>
                <th>Address</th>
                <th>Mobile number</th>
                <th>Email</th>
                <th>Membership</th>
            </tr>
            <?php
            echo "<tr class='warning'><td>";
            echo $member_row[0] . "</td><td>  " . $member_row[1] . " </td><td> " . $member_row[3] . " </td><td> " . $member_row[4] . " </td><td> " . $member_row[5] . " </td><td> " . $member_row[9];
            echo "</td></tr></table>";
            ?>
            <h4>Ordered item information </h4>
            <table class="table ">
                <tr>
                    <td width="200px">
                        <?php echo '<img class="single_img_vrs"  src="' . $item_row['cover_image'] . '">';
                        ?>
                    </td>

                    <td valign="top">
                        <table class="fa-table">

                            <?php
                            echo "<tr class=''><th width='120px'>Order ID: </th><td>" . $order_id . "</td>";
                            echo "<tr class=''><th width='120px'>Item ID: </th><td>" . $item_row[0] . "</td>";
                            echo "<tr><th>Title:</th><td> " . $item_row[1] . " </td>";
                            echo "<tr><th>Delivery Address:</th><td> " . $del_address . " </td>";
                            echo "<tr><th>Rent Per Day:</th><td> Rs." . $item_row[7];
                            echo "<tr><th>Courier Charges:</th><td> Rs.100" ;
                            ?>

                            <tr>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                            </tr>
                        </table>
                    </td>

                </tr>
            </table>
            <table class="table">
                <tr>
                    <td>Order placed on: &nbsp; &nbsp;&nbsp;&nbsp;<?php echo get_mysql_timestamp($conn); ?> </td>
                    <td>Ordered by: &nbsp; &nbsp;&nbsp;&nbsp;<?php echo $_SESSION['logged_in_user']; ?> </td>
                </tr>
                <tr>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                </tr>

            </table>
            <?php
        } else {
            echo '<div class="alert fade in" style="text-align:center; width:400px; color:red">';

            if (isset($_SESSION['error'])) {
                echo $_SESSION['error'];
                unset($_SESSION['error']);
            }
            echo '</div>';
        }
    } else {
        echo '<div class="alert fade in" style="font-size: 20px; width:400px; color:red">';
        echo "Some error occurred... Try again... <br/><br/>";

        echo ($_SESSION["role"] == 'member') ? "<a class='btn btn-primary' href='order_item.php'> OK </a>" : "<a class='btn btn-primary' href='order_item_by_staff.php'> OK </a>";
        echo '</div>';
    }
}
else {
    echo '<div class="alert fade in" style="font-size: 20px; width:400px; color:red">';
    echo "Some error occurred... Try again... <br/><br/>";

    echo ($_SESSION["role"] == 'member') ? "<a class='btn btn-primary' href='order_item.php'> OK </a>" : "<a class='btn btn-primary' href='order_item_by_staff.php'> OK </a>";
    echo '</div>';
}
require('include/page_footer.php');
?>