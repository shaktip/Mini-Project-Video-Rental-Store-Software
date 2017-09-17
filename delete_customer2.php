<?php
require('inc/config.php');
require('inc/functions.php');
require('include/page_header.php');
if($_SESSION['role'] == 'member' || $_SESSION['role'] == 'C') {
    echo "<br/><br/><h3 style='color:red'>Unauthorized access</h3>";
    require('include/page_footer.php');
    exit();
}
if(empty($_POST) || !isset($_POST['del_member'])){
    header("location: manage_customers.php");
    exit();
}
$member_id = safe_input($conn, $_POST['member_id']);
$member_row = $conn->query("SELECT member_id, user_name, contact_number, email, deposit_amount FROM member WHERE member_id = $member_id")->fetch_array();
$_SESSION['update_member_sql'] = "UPDATE member SET status='I', deposit_amount=0 WHERE member_id = $member_id";
echo '<h2 class="form-signin-heading" align="center">Member Deleted</h2>';
echo '<h4>Member information </h4>';
echo "<table class='table table-striped'>";
echo "<tr class='info'><th>ID</th><th>User Name</th><th>Mobile number</th><th>Email</th><th>Deposit Amount</th></tr>";

echo "<tr class='success'><td>";
echo $member_row["member_id"] . "</td><td>  " . $member_row["user_name"] . " </td><td> " . $member_row["contact_number"] . " </td><td> " . $member_row["email"] .
    " </td><td> Rs." . $member_row["deposit_amount"] ;
echo "</td></tr></table>";
if(!empty($_SESSION['issued_trans_ids'])) {
    echo "<h4>Issued Items Marked as Lost</h4>";
    echo "<table class='table '>";
    echo "<tr class='info'>";
    echo "<th/><th>Copy ID</th><th>Title</th><th>Daily Rent</th><th>Issue Date</th><td align=right><b>Total Price</b></td><td align='center'><b>Rented by Courier</b></td><td align='right'><b>Courier Charges</b></td><th/>";
}
$ret = fn_lost_damaged_item($conn, 'L', $_SESSION['issued_trans_ids']);
if($ret){
    echo "<h4 style='color:blue' align='center'>Details of the items updated and member information deleted successfully</h4>";
}
else{
    echo "<div>" . $_SESSION['error'] . "</div>";
    unset($_SESSION['error']);
}
unset($_SESSION['issued_trans_ids']);

?>