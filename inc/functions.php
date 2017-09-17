<?php

function send_update_email($to_email, $address, $mobile_number)
{
    $subject = "VRS account updated";

    $headers = 'MIME-Version: 1.0' . "\r\n";
    $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
    $from = "vrs.iit.sb@gmail.com";
    // Create email headers
    $headers .= 'From: ' . $from . "\r\n" .
        'Reply-To: ' . $from . "\r\n" .
        'X-Mailer: PHP/' . phpversion();

    $message = '<html><body>';
    $message .= '<h4>Hi ' . $_SESSION["logged_in_user"] . ',</h4>';

    $message .= 'You have recently updated your details stored with VRS. <br/>';
    $message .= '<br/>Address:  ' . $address;
    $message .= '<br/>Mobile :  ' . $mobile_number;
    $message .= "<br/><br/>Regards, <br/>Team VRS";
    $message .= '</body></html>';

    if (mail($to_email, $subject, $message, $headers)) {
        return True;
    } else {
        return False;
    }
}

function send_email($to_email, $member_id)
{
    $actual_link = "http://$_SERVER[HTTP_HOST]" . dirname($_SERVER['PHP_SELF']) .
        "/activate_member.php?email=" . md5($to_email) . "&id=" . $member_id . "&token=" . md5($member_id);

    $headers = 'MIME-Version: 1.0' . "\r\n";
    $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
    $from = "vrs.iit.sb@gmail.com";
    // Create email headers
    $headers .= 'From: ' . $from . "\r\n" .
        'Reply-To: ' . $from . "\r\n" .
        'X-Mailer: PHP/' . phpversion();

    $subject = "Activate Your VRS Account";
    $content =  "Thank you for registering with VRS! Please click the below link to complete your registration: ".
        " \n\n" . $actual_link . "\n\n\nRegards, \nTeam VRS";


    $message = '<html><body>';
    $message .= '<h1 style="color:darkblue;">Thank you for registering with VRS!</h1>';
    $message .= '<p style="color:mediumvioletred;font-size:18px;">Please click the below link to complete your registration: </p>';
    $message .= '<br/><br/> ' . $actual_link;
    $message .= '</body></html>';


//    echo $content;
    if (mail($to_email, $subject, $content, $headers)) {
        return True;
    }
    else{
        return False;
    }
}

function display_member_info($conn, $member_id, $message="Member Information"){
    $sql = "SELECT m.member_id, m.user_name,contact_number, email,m.membership_type, mi.membership_text, mi.discount_percentage, m.deposit_amount FROM member m JOIN membership_info mi ON m.membership_type=mi.membership_type WHERE member_id = " . $member_id;
    $res = $conn->query($sql);
    $member_row = $res->fetch_array();

    echo '<h4>' .$message . ' </h4>';
    echo "<table class='table table-striped'>";
    echo "<tr class='info'><th>ID</th><th>User Name</th><th>Mobile number</th><th>Email</th><th>Membership</th><th>Deposit Amount</th><th>Discount on Rent</th></tr>";

    echo "<tr class='success'><td>";
    echo $member_row[0] . "</td><td>  " . $member_row[1] . " </td><td> " . $member_row[2] . " </td><td> " . $member_row[3] .
        " </td><td> " . $member_row[5] . " </td><td>Rs. " .$member_row['deposit_amount']. "</td><td> " . $member_row[6] . "%";
    echo "</td></tr></table>";
    return $member_row;
}

function update_member($conn, $member_id){
    if (!empty($_POST) && isset($_POST['udpate_member'])) {
        $email = safe_input($conn, $_POST['email']);

        $address = safe_input($conn, $_POST['address']);
        $mobile_number = safe_input($conn, $_POST['MobileNumber']);

        /* Server side PHP input validation */
        if (filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
            $_SESSION['error'] = "Please enter a valid Email address.";
        }
        /*Update data to member table */
        $sql = "UPDATE member SET address='$address', contact_number='$mobile_number', email='$email' WHERE member_id = $member_id";

        if ($conn->query($sql)) {
            /* Success: Set session variables and redirect to Protected page */
            if(send_update_email($email, $address, $mobile_number))
                return TRUE;
            else{
                $_SESSION['error'] = "Error: Details updated but error in sending confirmation email.";
            }
        } else {
            $_SESSION['error'] = "Error: values could not be updated correctly.";
        }

        return FALSE;
    }
}

function get_last_insert_id($conn)
{
    $sql = "SELECT LAST_INSERT_ID()";
    $res = $conn->query($sql);
    $row = $res->fetch_array();
    return $row[0];
}

function fn_collect_ret_couriers($conn, $selected_items)
{
    $conn->autocommit(FALSE);
    $selected_str = implode(',', $selected_items);

    $sql = "SELECT *, co.status order_status FROM courier_order co JOIN item_master itm ON co.order_id IN ($selected_str) AND co.item_id = itm.item_id ".
        " JOIN member m ON co.placed_by = m.member_id ".
        " JOIN transaction t ON co.trans_id = t.trans_id";

    $res = $conn->query($sql);
    if($res->num_rows == 0)
    {
        $conn->rollback();
        $_SESSION['error'] = 'Some error occurred. Details could not be updated...';
        return FALSE;
    }

    $orders_not_processed = array();
    $flag = 0;

    while($row = $res->fetch_array()){
        if($row['order_status'] != 'PROCESSED'){
            $orders_not_processed[] = $row;
            $conn->rollback();
            continue;
        }

        $processed_by = $_SESSION['logged_in_id'];
        $trans_id = $row["trans_id"];
        $copy_id = $row["copy_id"];

        $sql = "UPDATE transaction SET status = 'R', return_date=NOW(), returned_by=$processed_by, " .
            " return_mode='C' WHERE trans_id = $trans_id";
        // echo "<br/>" . $sql;
        if(!$conn->query($sql))
        {
            $orders_not_processed[] = $row;
            $conn->rollback();
            continue;
        }

        $sql = "UPDATE item_details SET availability=1 WHERE copy_id='$copy_id'";
        if(!$conn->query($sql))
        {
            $orders_not_processed[] = $row;
            $conn->rollback();
            continue;
        }

        $sql = "UPDATE courier_order SET status='CLOSED' WHERE order_id = " . $row["order_id"] ;
        // echo "<br/>" . $sql;
        if(!$conn->query($sql))
        {
            $orders_not_processed[] = $row;
            $conn->rollback();
            continue;
        }
        if(!$flag){
            echo "<h4>Items Returned in Store</h4>";
            echo "<table class='table'>";
            echo "<tr class='info'>";
            echo "<th/><th>Order ID</th><th>Title</th><th>Order Date</th><th>Pick Up Address</th><th>Item Issue Details</th><td align='right'><b>Amount Paid</b></td><td align='right'><b>Order Status</b></td>";
            echo "</tr>";
            $flag = 1;
        }

        echo "<tr class='default'>";
        echo '<td width="150px"><img class="img_vrs" src="' . $row['cover_image'] . '"></td><td>';
        echo $row['order_id'] . " </td><td> " . $row['title'] . " </td><td> " . date('d/m/Y', strtotime($row['order_date']));

        echo "</td><td>" . $row['delivery_address'] ;
        echo "</td><td>Issue Date: " . date('d/m/Y', strtotime($row["loan_date"]));
        echo "<br/> Issued To: " . $row['user_name'];
        echo "<br/>Copy ID: " . $row["copy_id"];
        echo "</td><td align='right'> Rs." . $row["rent_amount"];

        echo "</td><td align='right'> CLOSED";
        echo "</td></tr>";


        $conn->commit();
    }

    echo "</table>";

    if(!empty($orders_not_processed)){
        echo '<div class="txt-heading" ><b>Orders which could not be processed </b></div>';
        echo "<b>Possible causes: got processed by another staff of the store / server error</b>";
        echo "<table class='table'>";
        echo "<tr class='info'>";
        echo "<th/><th>Order ID</th><th>Title</th><th>Order Date</th><th>Pick Up Address</th><th>Item Issue Details</th><td align='right'><b>Amount Paid</b></td><td align='right'><b>Order Status</b></td>";
        echo "</tr>";
        foreach($orders_not_processed as $row){
            echo "<tr class='default'>";
            echo '<td width="150px"><img class="img_vrs" src="' . $row['cover_image'] . '"></td><td>';
            echo $row['order_id'] . " </td><td> " . $row['title'] . " </td><td> " . date('d/m/Y', strtotime($row['order_date']));

            echo "</td><td>" . $row['delivery_address'] ;
            echo "</td><td>Issue Date: " . date('d/m/Y', strtotime($row["loan_date"]));
            echo "<br/> Issued To: " . $row['user_name'];
            echo "<br/>Copy ID: " . $row["copy_id"];
            echo "</td><td align='right'> Rs." . $row["rent_amount"];
            echo "</td><td align='right'> " . $row["order_status"];

            echo "</td></tr>";
        }
        echo "</table>";
    }

    return $flag;

}

function fn_process_ret_orders($conn, $selected_items, $courier_service_id)
{
    $conn->autocommit(FALSE);
    $selected_str = implode(',', $selected_items);

    $sql = "SELECT *, co.status order_status FROM courier_order co JOIN item_master itm ON co.order_id IN ($selected_str) AND co.item_id = itm.item_id ".
        " JOIN member m ON co.placed_by = m.member_id ".
        " JOIN transaction t ON co.trans_id = t.trans_id";

    $res = $conn->query($sql);
    if($res->num_rows == 0)
    {
        $conn->rollback();
        $_SESSION['error'] = 'Some error occurred. Details could not be updated...';
        return FALSE;
    }

    $orders_not_processed = array();
    $flag = FALSE;

    while($row = $res->fetch_array()){
        if($row['order_status'] != 'ORDERED'){
            $orders_not_processed[] = $row;
            $conn->rollback();
            continue;
        }

        $processed_by = $_SESSION['logged_in_id'];
        $trans_id = $row["trans_id"];

        $sql = "UPDATE transaction SET status = 'P', return_date=NOW(), returned_by=$processed_by, " .
            " return_mode='C' WHERE trans_id = $trans_id";
        // echo "<br/>" . $sql;
        if(!$conn->query($sql))
        {
            $orders_not_processed[] = $row;
            $conn->rollback();
            continue;
        }

//        $sql = "UPDATE item_details SET availability=0 WHERE copy_id='$copy_id'";
//        if(!$conn->query($sql))
//        {
//            $orders_not_processed[] = $row;
//            $conn->rollback();
//            continue;
//        }

        $sql = "UPDATE courier_order SET order_processed_on=NOW(), order_processed_by = '$processed_by', courier_via='$courier_service_id', status='PROCESSED' WHERE order_id = " . $row["order_id"] ;
        // echo "<br/>" . $sql;
        if(!$conn->query($sql))
        {
            $orders_not_processed[] = $row;
            $conn->rollback();
            continue;
        }
        if(!$flag){
            echo "<h4>Items Will Be Collected Soon</h4>";
            echo "<table class='table'>";
            echo "<tr class='info'>";
            echo "<th/><th>Order ID</th><th>Title</th><th>Order Date</th><th>Pick Up Address</th><th>Item Issue Details</th><td align='right'><b>Amount Paid</b></td><td align='right'><b>Order Status</b></td>";
            echo "</tr>";
            $flag = TRUE;
        }

        echo "<tr class='default'>";
        echo '<td width="150px"><img class="img_vrs" src="' . $row['cover_image'] . '"></td><td>';
        echo $row['order_id'] . " </td><td> " . $row['title'] . " </td><td> " . date('d/m/Y', strtotime($row['order_date']));

        echo "</td><td>" . $row['delivery_address'] ;
        echo "</td><td>Issue Date: " . date('d/m/Y', strtotime($row["loan_date"]));
        echo "<br/> Issued To: " . $row['user_name'];
        echo "<br/>Copy ID: " . $row["copy_id"];
        echo "</td><td align='right'> Rs." . $row["rent_amount"];

        echo "</td><td align='right'> PROCESSED";
        echo "</td></tr>";


        $conn->commit();
    }

    echo "</table>";

    if(!empty($orders_not_processed)){
        echo '<div class="txt-heading" ><b>Orders not processed </b></div>';
        echo "<b>Possible causes: got processed by another staff of the store</b>";
        echo "<table class='table'>";
        echo "<tr class='info'>";
        echo "<th/><th>Order ID</th><th>Title</th><th>Order Date</th><th>Pick Up Address</th><th>Item Issue Details</th><td align='right'><b>Amount Paid</b></td><td align='right'><b>Order Status</b></td>";
        echo "</tr>";
        foreach($orders_not_processed as $row){
            echo "<tr class='default'>";
            echo '<td width="150px"><img class="img_vrs" src="' . $row['cover_image'] . '"></td><td>';
            echo $row['order_id'] . " </td><td> " . $row['title'] . " </td><td> " . date('d/m/Y', strtotime($row['order_date']));

            echo "</td><td>" . $row['delivery_address'] ;
            echo "</td><td>Issue Date: " . date('d/m/Y', strtotime($row["loan_date"]));
            echo "<br/> Issued To: " . $row['user_name'];
            echo "<br/>Copy ID: " . $row["copy_id"];
            echo "</td><td align='right'> Rs." . $row["rent_amount"];
            echo "</td><td align='right'> " . $row["order_status"];

            echo "</td></tr>";
        }
        echo "</table>";
    }

    return $flag;

}

function fn_process_orders($conn, $selected_items, $courier_service_id)
{
    $conn->autocommit(FALSE);
    $selected_str = implode(',', $selected_items);

    $sql = "SELECT *, co.status order_status FROM courier_order co JOIN item_master itm ON co.order_id IN ($selected_str) AND co.item_id = itm.item_id ".
        " JOIN member m ON co.placed_by = m.member_id";

    $res = $conn->query($sql);
    if($res->num_rows == 0)
    {
        $conn->rollback();
        $_SESSION['error'] = 'Some error occurred. Details could not be updated...';
        return FALSE;
    }

    $orders_not_processed = array();
    $flag = false;
    while($row = $res->fetch_array()){
        if(!eligible_for_more_items($conn,$row['member_id'])){
            $orders_not_processed[] = $row;
            $conn->rollback();
            continue;
        }
        else{
            // Format: $_SESSION["eligible_for"] = array(array($max_no_of_video_cds, $no_of_more_video),
            // array($max_no_of_music_cds, $no_of_more_music));
            if(($_SESSION["eligible_for"][0][1] <= 0 && $row["item_type"] == 'V') || ($_SESSION["eligible_for"][1][1] <= 0 && $row["item_type"] == 'M')){
                $orders_not_processed[] = $row;
                $conn->rollback();
                continue;
            }
        }

        if($row['order_status'] != 'ORDERED'){
            $orders_not_processed[] = $row;
            $conn->rollback();
            continue;
        }
        $condition = "item_id='" . $row['item_id'] . "' AND availability = 1 AND store_id='" . $_SESSION['store_id'] . "' AND status='A'";
        $copy_res = $conn->query("SELECT min(copy_id) FROM item_details WHERE " . $condition);
        $copy_id = ($copy_res->fetch_array())[0];
        if($copy_id == 0 || $copy_id=='')
        {
            $orders_not_processed[] = $row;
            $conn->rollback();
            continue;
        }

        $member_id = $row['placed_by'];
        $processed_by = $_SESSION['logged_in_id'];
        $sql = "INSERT INTO transaction(trans_id, copy_id, member_id, rented_by, rent_mode, status) " .
            " VALUES(0, '$copy_id', '$member_id' , '$processed_by', 'C', 'I')";
        // echo "<br/>" . $sql;
        if(!$conn->query($sql))
        {
            $orders_not_processed[] = $row;
            $conn->rollback();
            continue;
        }
        $trans_id = get_last_insert_id($conn);

        $sql = "UPDATE item_details SET availability=0 WHERE copy_id='$copy_id'";
        if(!$conn->query($sql))
        {
            $orders_not_processed[] = $row;
            $conn->rollback();
            continue;
        }

        $sql = "UPDATE courier_order SET order_processed_on=NOW(), order_processed_by = '$processed_by', trans_id='$trans_id', courier_via='$courier_service_id', status='DISPATCHED' WHERE order_id = $row[0] ";
        // echo "<br/>" . $sql;
        if(!$conn->query($sql))
        {
            $orders_not_processed[] = $row;
            $conn->rollback();
            continue;
        }

        if(!$flag){
            echo "<h4>Items Dispatched</h4>";
            echo "<table class='table'>";
            echo "<tr class='info'>";
            echo "<th/><th>Order ID</th><th>Title</th><th>Order Date</th><th>Delivery Address</th><th>Member ID</th><th>Daily Rent</th><th>Order Status</th>";
            echo "</tr>";
            $flag = TRUE;
        }

        echo "<tr class='default'>";
        echo '<td width="150px"><img class="img_vrs" src="' . $row['cover_image'] . '"></td><td>';
        echo $row['order_id'] . " </td><td> " . $row['title'] . " </td><td> " . date('d/m/Y', strtotime($row['order_date']));

        echo "</td><td>" . $row['delivery_address'] ;

        echo "</td><td> " . $row['placed_by'];
        echo "</td><td> Rs." . $row['daily_rent'];
        echo "</td><td> DISPATCHED";
        echo "</td></tr>";
        $conn->commit();
    }

    echo "</table>";

    if(!empty($orders_not_processed)){
        echo '<div class="txt-heading" ><b>Orders not processed </b></div>';
        echo "<b>Possible causes: no availability of ordered item currently / got processed by another staff or store / customer not eligible for more items</b>";
        echo "<table class='table'>";
        echo "<tr class='info'>";
        echo "<th/><th>Order ID</th><th>Title</th><th>Order Date</th><th>Delivery Address</th><th>Member ID</th><th>Daily Rent</th><th>Order Status</th>";
        echo "</tr>";

        foreach($orders_not_processed as $row){
            echo "<tr class='default'>";
            echo '<td width="150px"><img class="img_vrs" src="' . $row['cover_image'] . '"></td><td>';
            echo $row['order_id'] . " </td><td> " . $row['title'] . " </td><td> " . date('d/m/Y', strtotime($row['order_date']));

            echo "</td><td>" . $row['delivery_address'] ;

            echo "</td><td> " . $row['placed_by'];
            echo "</td><td> Rs." . $row['daily_rent'];
            echo "</td><td> " . $row['order_status'];
            echo "</td></tr>";
        }
        echo "</table>";
    }

    return $flag;

}

function calculate_distance($from, $to)
{
    $from = urlencode($from);
    $to = urlencode($to);
    $distance = 0;
    $data = file_get_contents("http://maps.googleapis.com/maps/api/distancematrix/json?origins=$from&destinations=$to&language=en-EN&sensor=false");
    if($data) {
        $data = json_decode($data);
        foreach ($data->rows[0]->elements as $road) {
//        $time += $road->duration->value;
//        $distance += $road->distance->value;

//        echo $road->duration->text;
//        echo "<br/>";
            $distance = $road->distance->text;
//        echo "<br/>";
        }
    }
    return $distance;
}


function add_item_details($conn, $item_id, $no_of_copies, $store_id, $price)
{
    for ($i = 1; $i <= $no_of_copies; $i++) {
        $sql = "INSERT INTO item_details VALUES(0, '$item_id', 1, '$store_id', '$price', 'A', NOW()); ";
        if (!$conn->query($sql))
            return FALSE;
    }
    return TRUE;
}


function order_item($conn, $member_id, $item_id, $order_for, $address){
    if($_SESSION['role'] == 'member') {
        $mode = 'O';
        $call_taken_by = 'NULL';
    }
    elseif ($_SESSION['role'] == 'M' || $_SESSION['role'] == 'C') {
        $mode = 'C';
        $call_taken_by = $_SESSION['logged_in_id'];
    }
    else {
        $_SESSION['error'] = "Some error occurred...";
        return FALSE;
    }

    $sql = "INSERT INTO courier_order(order_id , placed_by , item_id , order_date , order_for , mode , call_taken_by , status , delivery_address ) VALUES(0, '$member_id', '$item_id', NOW(), '$order_for', '$mode', $call_taken_by , 'ORDERED', '$address'); ";
    //echo $sql;
    if ($conn->query($sql)) {
        return get_last_insert_id($conn);
    }
    else {
        $_SESSION['error'] = "Error while placing order for the item...";
        return FALSE;
    }
}
function add_more_copies($conn)
{
    if (!empty($_POST) && isset($_POST['add_more_copies'])) {
        $more_no_of_copies = safe_input($conn, $_POST['more_no_of_copies']);
        $price = safe_input($conn, $_POST['price']);
        $_SESSION['error'] = '';
        $item_row = $_SESSION['item_details'];
        $no_of_copies = $_SESSION['no_of_copies'];
        $store_id = $_SESSION['store_id'];

        unset($_SESSION['no_of_copies']);

        if (add_item_details($conn, $item_row[0], $more_no_of_copies, $store_id, $price)) {
            return $no_of_copies + $more_no_of_copies;
        } else {
            $_SESSION['error'] = "error while updating details for the item...";
            return FALSE;
        }
    }
}

function update_item($conn, $item_id)
{
    if (!empty($_POST) && isset($_POST['edit_item'])) {
        $item_title = safe_input($conn, $_POST['item_title']);
        $item_language = safe_input($conn, $_POST['item_language']);
        $genre = safe_input($conn, $_POST['genre']);
        $desc = safe_input($conn, $_POST['desc']);
        $daily_rent = safe_input($conn, $_POST['daily_rent']);
        $_SESSION['error'] = '';
        $file_update = '';
        if ($_FILES["cover_image"]["name"] != '') {
            $sql = "SELECT cover_image FROM item_master WHERE item_id=$item_id";
            $old_file = (($conn->query($sql))->fetch_array())[0];

            unlink($old_file);
            $target_file = "uploads/" . $item_id . basename($_FILES["cover_image"]["name"]);
            if (!upload_file($target_file)) {
                return FALSE;
            }
            $file_update = ", cover_image = '$target_file'";
            $_SESSION['cover_image'] = $target_file;
        }
        /*Insert registration data to member table */
        $sql = "UPDATE item_master SET title='$item_title', language='$item_language' , " .
            " genre='$genre', descr='$desc', daily_rent='$daily_rent' " . $file_update .
            " WHERE item_id=$item_id";

        if ($conn->query($sql)) {
            return TRUE;
        }

        $_SESSION['error'] = "error in updating details...";
        return FALSE;
    }
}

function fn_lost_damaged_item_member($conn, $lost_or_damaged, $selected_items)
{
    $conn->autocommit(FALSE);
    if(isset($_SESSION['update_member_sql'])){
        if (!$conn->query($_SESSION['update_member_sql'])) {
            $conn->rollback();
            $_SESSION['error'] = 'Some error occurred. Details could not be updated...';
            unset($_SESSION['update_member_sql']);
            return FALSE;
        }
        unset($_SESSION['update_member_sql']);
    }
    $total_amount = 0;


    $selected_trans_str = implode(',', $selected_items);

    $sql = "UPDATE transaction SET return_date=NOW(), return_mode='O', status='$lost_or_damaged' WHERE trans_id IN ($selected_trans_str); ";

    $selected_items_str = '';
    foreach ($selected_items as $trans_id) {
        $row = $_SESSION['issued_items'][$trans_id];
        $price = $row['price'];
        $copy_id = $row['copy_id'];
        $selected_items_str .= $copy_id . ",";

        $by_courier = 'N';
        $courier_charges = 0;
        $check_courier = $conn->query("SELECT * FROM courier_order WHERE trans_id=$trans_id AND order_processed_by IS NOT NULL");
        if($check_courier->num_rows > 0){
            $by_courier = 'Y';
            while($courier_row = $check_courier->fetch_array()){
                $courier_charges += 100;
            }
        }

        $total_amount += $price + $courier_charges;

        echo "<tr class='default'>";
        echo '<td width="150px"><img class="img_vrs" src="' . $row['cover_image'] . '"></td>';
        echo '<td>' . $copy_id . " </td><td> " . $row[1] . " </td><td> " . $row[3] . "</td><td>  " . date('d/m/Y', strtotime($row[0]));
        echo "</td><td align=right> Rs." . $price;
        echo "</td><td align=right> Rs." . $courier_charges;
        echo "</td><td>" . $by_courier . "</td></tr>";
        $sql .= " INSERT INTO old_items VALUES ($copy_id, '$lost_or_damaged', NOW(), $price+$courier_charges, NULL); ";

    }
    echo '<tr class="info"><td colspan="7" align=right><strong>Total:</strong> Rs. ' . $total_amount . '</td><td/></tr>';
    echo "</table>";

    $selected_items_str = rtrim($selected_items_str, ", ");

    $sql .= " UPDATE item_details SET status='I'  WHERE copy_id IN ($selected_items_str)";

    unset($_SESSION['issued_items']);
    $_SESSION['sql'] = $sql;
    return $total_amount;
}

function fn_lost_damaged_item($conn, $lost_or_damaged, $selected_items)
{

    $conn->autocommit(FALSE);
    if(isset($_SESSION['update_member_sql'])){
        if (!$conn->query($_SESSION['update_member_sql'])) {
            $conn->rollback();
            $_SESSION['error'] = 'Some error occurred. Details could not be updated!';
            unset($_SESSION['update_member_sql']);
            return FALSE;
        }
        unset($_SESSION['update_member_sql']);
    }
    if(!empty($selected_items)) {
        $total_amount = 0;
        $returned_by = $_SESSION['logged_in_id'];

        $selected_trans_str = implode(',', $selected_items);

        $sql = "UPDATE transaction SET return_date=NOW(), returned_by = $returned_by, return_mode='P', status='$lost_or_damaged' WHERE trans_id IN ($selected_trans_str); ";
        if (!$conn->query($sql)) {
            $conn->rollback();
            $_SESSION['error'] = 'Some error occurred. Details could not be updated...';
            return FALSE;
        }
        $selected_items_str = '';
        foreach ($selected_items as $trans_id) {
            $row = $_SESSION['issued_items'][$trans_id];
            $price = $row['price'];
            $copy_id = $row['copy_id'];
            $selected_items_str .= $copy_id . ",";

            $by_courier = 'No';
            $courier_charges = 0;
            $check_courier = $conn->query("SELECT * FROM courier_order WHERE trans_id=$trans_id AND order_processed_by IS NOT NULL");
            if ($check_courier->num_rows > 0) {
                $by_courier = 'Yes';
                while ($courier_row = $check_courier->fetch_array()) {
                    $courier_charges += 100;
                }
            }

            $total_amount += $price + $courier_charges;

            echo "<tr class='default'>";
            echo '<td width="150px"><img class="img_vrs" src="' . $row['cover_image'] . '"></td>';
            echo '<td>' . $copy_id . " </td><td> " . $row[1] . " </td><td> " . $row[3] . "</td><td>  " . date('d/m/Y', strtotime($row[0]));
            echo "</td><td align='right'> Rs." . $price;
            echo "</td><td align='center'>" . $by_courier;
            echo "</td><td align='right'> Rs." . $courier_charges;
            echo "</td></tr>";
            $sql = " INSERT INTO old_items VALUES ($copy_id, '$lost_or_damaged', NOW(), $price+$courier_charges, $returned_by); ";
            if (!$conn->query($sql)) {
                $conn->rollback();
                $_SESSION['error'] = 'Some error occurred. Details could not be updated...!';
                return FALSE;
            }
        }
        echo '<tr class="info"><td colspan="8" align=right><strong>Total:</strong> Rs. ' . $total_amount . '</td><td/></tr>';
        echo "</table>";

        $selected_items_str = rtrim($selected_items_str, ", ");

        $sql = " UPDATE item_details SET status='I'  WHERE copy_id IN ($selected_items_str)";
        if (!$conn->query($sql)) {
            $conn->rollback();
            $_SESSION['error'] = 'Some error occurred. Details could not be updated.....';
            return FALSE;
        }
    }
    unset($_SESSION['issued_items']);
    $conn->commit();
    return TRUE;
}
function get_mysql_timestamp($conn){
    $res = $conn->query('SELECT NOW()');
    $now = ($res->fetch_array())[0];
    return $now;
}

function fn_return_item_order($conn, $selected_items, $discount, $member_id, $address ){
    // $conn,
    if($_SESSION['role'] == 'member') {
        $mode = 'O';
        $call_taken_by = 'NULL';
    }
    elseif ($_SESSION['role'] == 'M' || $_SESSION['role'] == 'C') {
        $mode = 'C';
        $call_taken_by = $_SESSION['logged_in_id'];
    }
    else {
        $_SESSION['error'] = "Some error occurred...";
        return FALSE;
    }
    $conn->autocommit(FALSE);
    $total_rent = 0;
    $returned_by = $_SESSION['logged_in_id'];
    $now = strtotime(get_mysql_timestamp($conn));
    $sql = '';

    foreach ($selected_items as $trans_id) {

        $row = $_SESSION['issued_items'][$trans_id];
        $item_id = $row['item_id'];
        $issued_on = strtotime($row[0]);
        $copy_id = $row['copy_id'];

        $number_of_days = ceil(($now - $issued_on) / (60 * 60 * 24));

        $rent_amount = round(($row[3] * $number_of_days) * (1 - ($discount / 100))) + 100;

        $by_courier = 'N';
        $check_courier = $conn->query("SELECT * FROM courier_order WHERE trans_id=$trans_id AND order_processed_by IS NOT NULL");
        if($check_courier->num_rows > 0){
            $by_courier = 'Y';
            while($courier_row = $check_courier->fetch_array()){
                $rent_amount += 100;
            }
        }

        $total_rent += $rent_amount;

        $sql .= " INSERT INTO courier_order(order_id , placed_by , item_id , order_date , order_for , mode , call_taken_by , status , delivery_address, trans_id ) VALUES(0, '$member_id', '$item_id', NOW(), 'RETURN', '$mode', $call_taken_by , 'ORDERED', '$address', $trans_id); ";
        $sql .= " UPDATE transaction SET rent_amount = $total_rent WHERE trans_id = $trans_id; ";

//        if (!$conn->query($sql)) {
//            $conn->rollback();
//            $_SESSION['error'] = 'Some error occurred. Details could not be updated...';
//            return FALSE;
//        }

        echo "<tr class='default'>";
        echo '<td width="150px"><img class="img_vrs" title="' . $row['descr'] . '" src="' . $row['cover_image'] . '"></td>';
        echo '<td>' . $copy_id . " </td><td> " . $row[1] . " </td><td> " . $row[3] . "</td><td>  " . date('d/m/Y', strtotime($row[0]));
        echo "</td><td align=right> Rs." . ($row[3] * $number_of_days);
        echo "</td><td align=right> Rs." . $rent_amount;
        echo "</td><td align='left' ><span style='margin-left: 50px'>Rent: " . $by_courier . "<br/><span style='margin-left: 50px'>Return: Y </span></span>";
        echo "</td><td/></tr>";
    }
    echo '<tr class="info"><td colspan="7" align=right><strong>Total:</strong> Rs. ' . $total_rent . '</td><td/><td/></tr>';
    echo "</table>";

    $_SESSION['sql'] = $sql;
    return $total_rent;
}
function fn_return_item($conn, $discount, $selected_items)
{
    $conn->autocommit(FALSE);
    $total_rent = 0;
    $returned_by = $_SESSION['logged_in_id'];
    $selected_items_str = '';
    $now = strtotime(get_mysql_timestamp($conn));
    $sql = '';
    foreach ($selected_items as $trans_id) {

        $row = $_SESSION['issued_items'][$trans_id];
        $issued_on = strtotime($row[0]);
        $copy_id = $row['copy_id'];
        $selected_items_str .= $copy_id . ",";
        $number_of_days = ceil(($now - $issued_on) / (60 * 60 * 24));

        $rent_amount = round(($row[3] * $number_of_days) * (1 - ($discount / 100)));

        $by_courier = 'N';
        $check_courier = $conn->query("SELECT * FROM courier_order WHERE trans_id=$trans_id");
        if($check_courier->num_rows > 0){
            $by_courier = 'Y';
            while($courier_row = $check_courier->fetch_array()){
                $rent_amount += 100;
            }
        }

        $total_rent += $rent_amount;
        $sql = "UPDATE transaction SET rent_amount = $rent_amount, return_date=NOW(), returned_by = $returned_by, return_mode='P', status='R' WHERE trans_id = $trans_id; ";
        if (!$conn->query($sql)) {
            $conn->rollback();
            $_SESSION['error'] = 'Some error occurred. Details could not be updated...';
            return FALSE;
        }

        echo "<tr class='default'>";
        echo '<td width="150px"><img class="img_vrs" src="' . $row['cover_image'] . '"></td>';
        echo '<td>' . $copy_id . " </td><td> " . $row[1] . " </td><td> " . $row[3] . "</td><td>  " . date('d/m/Y', strtotime($row[0]));
        echo "</td><td align=right> Rs." . ($row[3] * $number_of_days);
        echo "</td><td align=right> Rs." . $rent_amount;
        echo "</td><td align='center'>" . $by_courier;
        echo "</td><td/></tr>";
    }

    echo '<tr class="info"><td colspan="7" align=right><strong>Total:</strong> Rs. ' . $total_rent . '</td><td/><td/></tr>';
    echo "</table>";
    $selected_items_str = rtrim($selected_items_str, ", ");
    // echo $selected_items_str;
    //$selected_items_str = implode(',', $selected_items);

    $sql = "UPDATE item_details SET availability=1 WHERE copy_id IN ($selected_items_str)";
    if (!$conn->query($sql)) {
        $conn->rollback();
        $_SESSION['error'] = 'Some error occurred. Details could not be updated...';
        return FALSE;
    }

    $conn->commit();
    return TRUE;

}

function issue_item($conn, $copy_id, $member_id)
{
    $conn->autocommit(FALSE);
    $rented_by = $_SESSION['logged_in_id'];
    $sql = "INSERT INTO transaction(trans_id, copy_id, member_id, rented_by, rent_mode, status) " .
        " VALUES(0, '$copy_id', '$member_id' , '$rented_by', 'P', 'I')";
    $res1 = $conn->query($sql);

    $sql = "UPDATE item_details SET availability=0 WHERE copy_id='$copy_id'";
    $res2 = $conn->query($sql);

    if ($res1 && $res2) {
        $conn->commit();

        return TRUE;
    } else {
        $conn->rollback();

        $_SESSION['error'] = 'Some error occurred. Item could not be issued.';
        return FALSE;
    }
}

function search_member($conn){
  	if(!empty($_POST) && isset($_POST['search_mem'])){
  		$user_name = strtoupper(safe_input($conn, $_POST['user_name']));
  		// echo $user_name;
  		$mobile_number = safe_input($conn, $_POST['mobile_number']);
  		// echo $mobile_number;
  		$condition = " status = 'A' AND ";
  		if($user_name != '' && $mobile_number == '')
  		    $condition .= " upper(user_name) LIKE '%".$user_name . "%' ";
        elseif($user_name == '' && $mobile_number != '')
            $condition .= " contact_number LIKE '%" . $mobile_number ."%'";
  		else
            $condition .= " (upper(user_name) LIKE '%".$user_name . "%' OR contact_number LIKE '%" . $mobile_number ."%')";
  		if($rows = get_rows($conn, "member", "*", $condition)) {
  			return $rows;
  		}
  		else{
  			$_SESSION['error'] = 'No member found with the given data';
  			return TRUE;
  		}
  	}
  	return FALSE;
}

function eligible_for_more_items($conn, $member_id)
{
    $membership_res = $conn->query("SELECT mi.* FROM membership_info mi JOIN member m ON m.membership_type = mi.membership_type WHERE m.member_id=$member_id");
    $membership_row = $membership_res->fetch_array();
    $max_no_of_video_cds = $membership_row["no_of_video_cds"];
    $max_no_of_music_cds = $membership_row["no_of_music_cds"];

    $no_of_video_cds_issued = 0;
    $no_of_music_cds_issued = 0;

    $sql = "SELECT itm.item_type, count(*) FROM transaction t JOIN item_details itd ON t.copy_id = itd.copy_id AND t.member_id=" . $member_id . " JOIN item_master itm ON itd.item_id = itm.item_id WHERE t.status = 'I' AND itd.status='A' GROUP BY itm.item_type";
    $res = $conn->query($sql);
    while ($row = $res->fetch_array()) {
        if ($row[0] == 'V')
            $no_of_video_cds_issued = $row[1];
        elseif ($row[0] == 'M')
            $no_of_music_cds_issued = $row[1];
        //echo $row[0] . " " .$no_of_video_cds_issued . $no_of_music_cds_issued;
    }
    $no_of_more_video = ($max_no_of_video_cds - $no_of_video_cds_issued);
    $no_of_more_music = ($max_no_of_music_cds - $no_of_music_cds_issued);
    $_SESSION["eligible_for"] = array(array($max_no_of_video_cds, $no_of_more_video),
        array($max_no_of_music_cds, $no_of_more_music));

    if ($no_of_more_video == 0 && $no_of_more_music == 0)
        return FALSE;


    //echo $_SESSION["eligible_for"][0][1];
    return TRUE;
}

function get_ordered_items($conn, $member_id){
    $sql = "SELECT co.order_date, itm.title, itm.cover_image, itm.daily_rent, itm.item_type, co.order_for, itm.descr FROM courier_order co JOIN item_master itm ON co.item_id = itm.item_id AND co.placed_by=" . $member_id . " AND co.status = 'ORDERED' ";
    $ordered_items = $conn->query($sql);
    return $ordered_items;
}
function get_issued_items($conn, $member_id)
{
    $_SESSION['result_exists'] = '';
    $sql = "SELECT t.loan_date, itm.title, itm.cover_image, itm.daily_rent, t.copy_id, itd.price, t.trans_id, store_id, itd.item_id, itm.item_type, itm.descr FROM transaction t JOIN item_details itd ON t.copy_id = itd.copy_id AND t.member_id=" . $member_id . " JOIN item_master itm ON itd.item_id = itm.item_id WHERE t.status = 'I' and itd.status='A'";

    $issued_items = $conn->query($sql);
    $eligible = eligible_for_more_items($conn, $member_id);
    if ($issued_items->num_rows > 0) {
        if (!$eligible) {
            $_SESSION['result_exists'] = 'f';
        } else
            $_SESSION['result_exists'] = 'y';
    } else {
        $_SESSION['result_exists'] = 'n';
    }
    return $issued_items;
}

function search_items($conn, $for)
{
    if($for != 'O')
        $store_id = $_SESSION['store_id'];
    $sql = "SELECT * FROM item_master im WHERE im.status='A' ";

    if(!isset($_SESSION["eligible_for"])) {

        if ($_SESSION['role'] == 'member') {
            eligible_for_more_items($conn, $_SESSION['logged_in_id']);

        }
        else
            return FALSE;
    }
    if ($_SESSION["eligible_for"][0][1] == 0) {
        if ($_SESSION["eligible_for"][1][1] == 0) {
            if($_SESSION['role'] != 'member' && $_SESSION['role']!='S') {
                header("location: issue_item.php");
            }
            else{
                $_SESSION['error'] = 'You have been already issued with maximum number of items as per your membership type. You are not eligible to order more CD/DVDs';
                return FALSE;
            }

            return;
        }else
            $sql .= " AND im.item_type = 'M'  ";

    } elseif ($_SESSION["eligible_for"][1][1] == 0)
        $sql .= " AND im.item_type = 'V'  ";


    if($for == 'I') {
        $sql .= " AND  EXISTS( SELECT 1 FROM item_details itd " .
            "                   WHERE im.item_id=itd.item_id AND itd.availability=1 AND " .
            "                   itd.store_id = '$store_id' AND itd.status='A'" .
            "                ) ";
    }
    if (!empty($_POST) && isset($_POST['search_prod'])) {
        $item_language = strtoupper(safe_input($conn, $_POST['item_language']));
        $genre = strtoupper(safe_input($conn, $_POST['genre']));
        $item_title = strtoupper(safe_input($conn, $_POST['item_title']));

        if ($item_language !== 'ALL')
            $sql .= " AND upper(language) = '" . $item_language . "'";
        if ($genre !== 'ALL')
            $sql .= " AND upper(genre) = '" . $genre . "'";
        if ($item_title !== '')
            $sql .= " AND upper(title) LIKE '%" . $item_title . "%'";
    }

    return $sql;
}

function search_selected_unused_item($conn, $item_id){
    $store_id = $_SESSION['store_id'];
    $sql = "SELECT copy_id, price " .
        " FROM item_details  WHERE item_id=$item_id " .
        " AND store_id = $store_id AND status='A' AND availability=1 " .
        " AND copy_id NOT IN (SELECT copy_id FROM transaction ";

    $from_date = $_SESSION['from_date'];
    if($from_date == '')
        $sql .= ")";
    else
        $sql .= " WHERE loan_date > '" . date('Y-m-d',strtotime($from_date)) . "')";

    return $sql;
}

function search_unused_items($conn)
{
    $store_id = $_SESSION['store_id'];
    $sql = "SELECT im.item_id, im.cover_image, im.daily_rent, im.title, count(itd.copy_id) as num_of_copies " .
        " FROM item_master im JOIN item_details itd ON im.item_id = itd.item_id " .
        " AND itd.store_id = $store_id AND itd.status='A' " .
        " WHERE im.status = 'A' AND itd.copy_id NOT IN (SELECT copy_id FROM transaction ";

    $_SESSION['from_date'] = '';
    if (!empty($_POST) && isset($_POST['search_unused_prod'])) {
        $item_language = strtoupper(safe_input($conn, $_POST['item_language']));
        $genre = strtoupper(safe_input($conn, $_POST['genre']));
        $item_title = strtoupper(safe_input($conn, $_POST['item_title']));
        $_SESSION['from_date'] = $from_date = safe_input($conn, $_POST['from_date']);

        if($from_date == '')
            $sql .= ")";
        else
            $sql .= " WHERE loan_date > '" . date('Y-m-d',strtotime($from_date)) . "')";
        if ($item_language !== 'ALL')
            $sql .= " AND upper(language) = '" . $item_language . "'";
        if ($genre !== 'ALL')
            $sql .= " AND upper(genre) = '" . $genre . "'";
        if ($item_title !== '')
            $sql .= " AND upper(title) LIKE '%" . $item_title . "%'";
    }
    else
        $sql .= ") ";

    $sql .= " GROUP BY im.item_id, im.cover_image, im.daily_rent, im.title " .
        "  ORDER BY title";

    return $sql;

}


function search_for_manage_items($conn)
{
    $store_id = $_SESSION['store_id'];
    $sql = "SELECT im.item_id, im.cover_image, im.daily_rent, im.title, count(itd.copy_id) as num_of_copies " .
        " FROM item_master im LEFT OUTER JOIN item_details itd ON im.item_id = itd.item_id " .
        " AND itd.store_id = $store_id AND itd.status='A' " .
        " WHERE im.status = 'A'";


    if (!empty($_POST) && isset($_POST['search_prod'])) {
        $item_language = strtoupper(safe_input($conn, $_POST['item_language']));
        $genre = strtoupper(safe_input($conn, $_POST['genre']));
        $item_title = strtoupper(safe_input($conn, $_POST['item_title']));

        if ($item_language !== 'ALL')
            $sql .= " AND upper(language) = '" . $item_language . "'";
        if ($genre !== 'ALL')
            $sql .= " AND upper(genre) = '" . $genre . "'";
        if ($item_title !== '')
            $sql .= " AND upper(title) LIKE '%" . $item_title . "%'";
    }

    $sql .= " GROUP BY im.item_id, im.cover_image, im.daily_rent, im.title " .
        " ORDER BY title";
    return $sql;
}

/*Function to get users data*/
function get_items($con, $store_id)
{
    $sql = "SELECT * FROM item_master im WHERE EXISTS (SELECT 1 FROM item_details itd WHERE itd.status = 'A' AND im.item_id=itd.item_id AND itd.availability=1 AND itd.store_id='$store_id')";
    $res = $con->query($sql);
    if ($res->num_rows > 0) {
        return $res;
    } else {
        return FALSE;
    }
}

function get_rows($conn, $tab_name, $columns, $condition)
{
    $sql = "SELECT " . $columns . " FROM " . $tab_name;
    if ($condition != '')
        $sql = $sql . " WHERE " . $condition;

    $res = $conn->query($sql);
    if ($res->num_rows > 0) {
        return $res;
    } else {
        return FALSE;
    }
}

function safe_input($con, $data)
{
    return htmlspecialchars(mysqli_real_escape_string($con, trim($data)));
}

/*Function to set JSON output*/
function output($Return=array())
{
    /*Set response header*/
    header("Access-Control-Allow-Origin: *");
    header("Content-Type: application/json; charset=UTF-8");
    /*Final JSON response*/
    exit(json_encode($Return));
}



function reg_member($conn)
{
    if (!empty($_POST) && isset($_POST['RegisterMember'])) {
        $user_name = safe_input($conn, $_POST['NewUserName']);
        $password = safe_input($conn, $_POST['RegPassword']);
        $email = safe_input($conn, $_POST['email']);
        $address = safe_input($conn, $_POST['address']);
        $mobile_number = safe_input($conn, $_POST['MobileNumber']);
        $memType = safe_input($conn, $_POST['memType']);
        $_SESSION['amt'] = $amt = safe_input($conn, $_POST['amt']);
        $_SESSION['error'] = '';

        /* Server side PHP input validation */
        if ($user_name === '') {
            $_SESSION['error'] = "Please enter user name.";
        } elseif (filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
            $_SESSION['error'] = "Please enter a valid Email address.";
        } elseif ($password === '') {
            $_SESSION['error'] = "Please enter Password.";
        }


        /* Check user name existence in DB */

        $res = $conn->query("SELECT * FROM member WHERE user_name='$user_name'");
        if ($res->num_rows == 0) {
            /*Insert registration data to member table */
            $sql = "INSERT INTO member (user_name, password, address, contact_number, email, deposit_amount, status, membership_type) values('$user_name', '" . md5($password) . "' , '$address', '$mobile_number', '$email', '$amt', 'P', '$memType' )";

            if ($conn->query($sql)) {
                /* Success: Set session variables and redirect to Protected page */
                if(send_email($email, $conn->insert_id))
                    return TRUE;
                else{
                    $_SESSION['error'] = "Error: Details inserted but error in sending activation email.";
                }
            } else {
                $_SESSION['error'] = "error: values were not inserted correctly.";
            }
        } else {
            /*User name already exists: Set error message */
            $_SESSION['error'] = 'User name already exists. Choose another user name';
        }

        return FALSE;
    }
}

function add_item($conn)
{
    if (!empty($_POST) && isset($_POST['add_item'])) {
        $item_title = safe_input($conn, $_POST['item_title']);
        $item_language = safe_input($conn, $_POST['item_language']);
        $genre = safe_input($conn, $_POST['genre']);
        $desc = safe_input($conn, $_POST['desc']);
        $price = safe_input($conn, $_POST['price']);
        $daily_rent = safe_input($conn, $_POST['daily_rent']);
        $no_of_copies = safe_input($conn, $_POST['no_of_copies']);
        $item_type = safe_input($conn, $_POST['item_type']);
        $_SESSION['error'] = '';

        $conn->autocommit(FALSE);
        /*Insert registration data to member table */
        $sql = "INSERT INTO item_master values(0, '$item_title', '$item_language' , '$genre', NOW(), '$desc', NULL, '$daily_rent', 'A', '$item_type')";

        if ($conn->query($sql)) {
            /* Success */

            $item_id = get_last_insert_id($conn);
            $target_file = "uploads/" . $item_id . basename($_FILES["cover_image"]["name"]);
            if (upload_file($target_file)) {

                $sql = "UPDATE item_master SET cover_image='$target_file' WHERE item_id='$item_id'";

                if ($conn->query($sql)) {
                    $store_id = $_SESSION['store_id'];
                    for ($i = 1; $i <= $no_of_copies; $i++) {
                        $sql = "INSERT INTO item_details VALUES(0, '$item_id', 1, '$store_id', '$price', 'A', NOW())";
                        if (!$conn->query($sql)) break;
                    }
                    if ($i == $no_of_copies + 1) {
                        $conn->commit();
                        $_SESSION['cover_image'] = $target_file;
                        return TRUE;
                    }
                }
            }
        }
        if ($_SESSION['error'] == '')
            $_SESSION['error'] = "error in storing details...";
        $conn->rollback();
        return FALSE;
    }
}

function upload_file($target_file)
{
    $target_dir = "uploads/";

    $uploadOk = 1;
    $imageFileType = pathinfo($target_file, PATHINFO_EXTENSION);
    // Check if image file is a actual image or fake image
    $check = getimagesize($_FILES["cover_image"]["tmp_name"]);
    if ($check === false) {
        $_SESSION['error'] = "File is not an image.";
        return FALSE;
    }

    // Check if file already exists
    if (file_exists($target_file)) {
        $_SESSION['error'] = "Sorry, file already exists.";
        return FALSE;
    }
    // Check file size
    if ($_FILES["cover_image"]["size"] > 500000) {
        $_SESSION['error'] = "Sorry, your file is too large.";
        return FALSE;
    }

    // Allow certain file formats
    if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
        && $imageFileType != "gif"
    ) {
        $_SESSION['error'] = "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
        return FALSE;
    }
    if (move_uploaded_file($_FILES["cover_image"]["tmp_name"], $target_file)) {
        return TRUE;
    } else {
        $_SESSION['error'] = "Sorry, there was an error uploading your file.";
        return FALSE;
    }

}

function change_password($conn)
{
    if (!empty($_POST) && isset($_POST['change_password'])) {

        $current_password = safe_input($conn, $_POST['current_password']);
        $new_password = safe_input($conn, $_POST['new_password']);
        $confirm_password = safe_input($conn, $_POST['confirm_password']);
        if ($new_password === $confirm_password) {
            if ($_SESSION['role'] == 'member') {
                $sql = "SELECT * FROM member WHERE member_id=" . $_SESSION['logged_in_id'] . " AND password = '" . md5($current_password) . "'";
                $res = $conn->query($sql);
                if ($res->num_rows == 0) {
                    $_SESSION['error'] = 'Current password is incorrect';
                    return FALSE;
                }
                $sql = "UPDATE member SET password='" . md5($new_password) . "' WHERE member_id=" . $_SESSION['logged_in_id'];
            } else {
                $sql = "SELECT * FROM employee WHERE empid=" . $_SESSION['logged_in_id'] . " AND password = '" . md5($current_password) . "'";
                $res = $conn->query($sql);
                if ($res->num_rows == 0) {
                    $_SESSION['error'] = 'Current password is incorrect';
                    return FALSE;
                }
                $sql = "UPDATE employee SET password='" . md5($new_password) . "' WHERE empid=" . $_SESSION['logged_in_id'];
            }

            if ($conn->query($sql)) {
                return TRUE;
            } else {
                $_SESSION['error'] = 'Operation failed';
            }
        } else {
            $_SESSION['error'] = 'Passwords do not match';
        }
        return FALSE;
    }
}

function edit_employee($conn)
{
    if (!empty($_POST) && isset($_POST['update_emp'])) {
        $emp_name = safe_input($conn, $_POST['emp_name']);
        $address = safe_input($conn, $_POST['address']);
        $mobile_number = safe_input($conn, $_POST['mobile_number']);

        /* Server side PHP input validation */
        if ($emp_name === '' || $address == '' || $mobile_number == '') {
            $_SESSION['error'] = "Please enter all the fields";
            return FALSE;
        }

        $sql = "UPDATE employee SET emp_name = '$emp_name', contact_number = '$mobile_number', address = '$address' WHERE empid = " . $_SESSION['logged_in_id'];

        if ($conn->query($sql)) {
            /* Success */
            $conn->commit();
            return True;
        } else {
            $_SESSION['error'] = "Error: values could not be updated correctly.";
        }
    }
}

function add_edit_employee($conn)
{
    if (!empty($_POST)) {
        if (isset($_POST['add_emp']) || isset($_POST['edit_emp'])) {
            $emp_name = safe_input($conn, $_POST['emp_name']);
            $address = safe_input($conn, $_POST['address']);
            $mobile_number = safe_input($conn, $_POST['mobile_number']);
            $sel_role = safe_input($conn, $_POST['sel_role']);
            $_SESSION['error'] = '';
            if ($_SESSION['role'] == 'S') {
                $store_id = safe_input($conn, $_POST['sel_store_id']);
            } else $store_id = $_SESSION['store_id'];

            /* Server side PHP input validation */
            if ($emp_name === '') {
                $_SESSION['error'] = "Please enter user name.";
                return FALSE;
            }
        }

        if (isset($_POST['add_emp'])) {
            $sql = "INSERT INTO employee VALUES(0, '$emp_name', '$mobile_number', '$address', '" . md5('vrs') . "' , '$sel_role', '$store_id', 'A' )";

            if ($conn->query($sql)) {
                /* Success */
                $last_id = get_last_insert_id($conn);
                $conn->commit();

                return $last_id;
            } else {
                $_SESSION['error'] = "error: values were not inserted correctly.";
            }
        } elseif (isset($_POST['edit_emp'])) {
            if ($store_id == '')
                $sql = "UPDATE employee SET emp_name = '$emp_name', contact_number = '$mobile_number', address = '$address', role = '$sel_role' WHERE empid = " . $_SESSION['sel_empid'];
            else
                $sql = "UPDATE employee SET emp_name = '$emp_name', contact_number = '$mobile_number', address = '$address', role = '$sel_role', store_id = $store_id WHERE empid = " . $_SESSION['sel_empid'];

            if ($conn->query($sql)) {
                /* Success */
                $conn->commit();

                return "updated";
            } else {
                $_SESSION['error'] = "error: values were not updated correctly.";
            }
        }

        return FALSE;
    }
}
function add_edit_service($conn){
    if (!empty($_POST)) {
        if (isset($_POST['add_service']) || isset($_POST['edit_service'])) {

            $address = safe_input($conn, $_POST['address']);
            $service_name = safe_input($conn, $_POST['service_name']);
            $email = safe_input($conn, $_POST['email']);
            $contact_number = safe_input($conn, $_POST['contact_number']);
            $_SESSION['error'] = '';

            /* Server side PHP input validation */
            if ($address === '' || $email === '' || $service_name === '' || $contact_number === '') {
                $_SESSION['error'] = "Please enter all the values";
                return FALSE;
            }
        }
        if (isset($_POST['add_service'])) {
            $sql = "INSERT INTO courier_service VALUES(0, '$service_name', '$address', '$contact_number', '$email', 'A')";

            if ($conn->query($sql)) {
                /* Success */
                $last_id = get_last_insert_id($conn);
                $conn->commit();
                return $last_id;
            } else {
                $_SESSION['error'] = "Error: values were not inserted correctly.";
            }
        } elseif (isset($_POST['edit_service'])) {
            $sql = "UPDATE courier_service SET service_name = '$service_name', contact_number = '$contact_number', address = '$address', email_id = '$email' WHERE service_id = " . $_SESSION['sel_service_id'];

            if ($conn->query($sql)) {
                /* Success */
                $conn->commit();
                return "updated";
            } else {
                $_SESSION['error'] = "Error: values were not updated correctly.";
            }
        }

        return FALSE;
    }
}
function add_edit_store($conn)
{
    if (!empty($_POST)) {
        if (isset($_POST['add_store']) || isset($_POST['edit_store'])) {

            $address = safe_input($conn, $_POST['address']);
            $city = safe_input($conn, $_POST['city']);
            $pin_code = safe_input($conn, $_POST['pin_code']);
            $contact_number = safe_input($conn, $_POST['contact_number']);
            $store_latitude = safe_input($conn, $_POST['store_latitude']);
            $store_longitude = safe_input($conn, $_POST['store_longitude']);
            $_SESSION['error'] = '';

            /* Server side PHP input validation */
            if ($address === '' || $city === '' || $pin_code === '' || $contact_number === '') {
                $_SESSION['error'] = "Please enter all the values";
                return FALSE;
            }
        }
        if (isset($_POST['add_store'])) {
            $sql = "INSERT INTO store VALUES(0, '$address', '$city', '$pin_code', '$contact_number', 'A', '$store_latitude', '$store_longitude')";

            if ($conn->query($sql)) {
                /* Success */
                $last_id = get_last_insert_id($conn);
                $conn->commit();
                return $last_id;
            } else {
                $_SESSION['error'] = "error: values were not inserted correctly.";
            }
        } elseif (isset($_POST['edit_store'])) {
            $sql = "UPDATE store SET latitude='$store_latitude', longitude='$store_longitude', city = '$city', contact_number = '$contact_number', address = '$address', pin_code = '$pin_code' WHERE store_id = " . $_SESSION['sel_store_id'];
//            echo $sql;
            if ($conn->query($sql)) {
                /* Success */
                $conn->commit();
                return "updated";
            } else {
                $_SESSION['error'] = "error: values were not updated correctly.";
            }
        }

        return FALSE;
    }
}
?>
