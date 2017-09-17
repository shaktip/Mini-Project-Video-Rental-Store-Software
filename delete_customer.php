<?php
require('inc/config.php');
require('inc/functions.php');
require('include/page_header.php');
if($_SESSION['role'] == 'member' || $_SESSION['role'] == 'C') {
    echo "<br/><br/><h3 style='color:red'>Unauthorized access</h3>";
    require('include/page_footer.php');
    exit();
}
if(empty($_GET) || !isset($_GET['id'])){
    header("location: manage_customers.php");
    exit();
}
$member_id = $_GET['id'];
?>
<form method="post" onsubmit="return delete_cust();" name="del_member" action="delete_customer2.php">
<table width="100%">
    <tr>
        <td valign="top">
            <h2 class="form-signin-heading" >Delete Member</h2>
        </td>
        <td align="right" valign="middle">
            <br/>
            <span id="display_info" style="visibility: hidden; color:red">
                <input type="checkbox" id="confirm_del"/> Mark all issued items as <b>"Lost"</b> and recover amount from deposit
            </span>
            <br/>
            <input type="submit" id="del_member" name="del_member" class="btn btn-primary" value="Proceed to Delete" />

        </td>
    </tr>
</table>

<?php
$member_row = display_member_info($conn, $member_id);

$issued_items = get_issued_items($conn, $member_id);

$total_price = 0;
$total_rent = 0;

$discount = $member_row['discount_percentage'];
$now = strtotime(get_mysql_timestamp($conn));
$_SESSION['issued_trans_ids'] = array();
$_SESSION['issued_items'] = array();
if(!($issued_items && $issued_items->num_rows > 0)){
    echo '<div class="txt-heading" ><b>No items issued to the member </b>';
}
else {
    echo '<div class="txt-heading" ><table width="100%"><tr><td width="50%"><b>Items issued to the member </b>';
    echo '</td><td align="right"> Total price of items: Rs. <span id="total_price"></span>';
    echo '</td><td align="right"> Total rent till date: Rs. <span id="total_rent"></span>';

    echo '</td></tr></table></div>';

    while ($row = $issued_items->fetch_array()) {
        $_SESSION['issued_items'][$row['trans_id']] = $row;
        $_SESSION['issued_trans_ids'][] = $row['trans_id'];

        $total_price += $row['price'];
        $number_of_days = ceil(($now - strtotime($row['loan_date'])) / (60 * 60 * 24));

        $rent_amount = round(($row['daily_rent'] * $number_of_days) * (1 - ($discount / 100)));
        $total_rent += $rent_amount;

        echo '<div class="product-item">';
        echo '<div class="product-image"><img class="img_vrs"  src="' . $row['cover_image'] . '"/></div>';
        echo '<div><strong>' . $row['title'] . '</strong>  (' . (($row['item_type'] == 'V') ? "Video" : "Music") . ')<br/>';
        echo 'Copy ID: ' . $row['copy_id'] . ', Store ID: ' . $row['store_id'] . ' <br/> Issued on: ' . date('d/m/Y', strtotime($row[0]));
        echo '<br/><span class="product-price"> Rent/day: Rs. ' . $row[3] . '<br/> Price: Rs. ' . $row['price'] . '</span>';
        echo '</div>';

        echo "</div>";

    }
    $selected_trans_str = implode(',', $_SESSION['issued_trans_ids']);
    $check_courier = $conn->query("SELECT * FROM courier_order WHERE trans_id IN ($selected_trans_str) AND order_processed_by IS NOT NULL");
    $total_price += ($check_courier->num_rows * 100);
    unset($_SESSION['result_exists']);
}

echo "<input type='hidden' name='member_id'  id='member_id' value='" . $member_id . "'/>";
echo "<input type='hidden' name='price' id='price' value='" . $total_price . "'/>";
echo "<input type='hidden' name='rent' id='rent' value='" . $total_rent . "'/>";
echo "<input type='hidden' name='deposit' id='deposit' value='" . $member_row['deposit_amount'] . "'/>";

?>
</form>

<script>
    price = parseFloat(document.getElementById("price").value);
    deposit = parseFloat(document.getElementById("deposit").value);
    document.getElementById("total_price").innerHTML = price;
    document.getElementById("total_rent").innerHTML = document.getElementById("rent").value;
    if( price > deposit) {
        alert("Member has issued more items! Total price > Member's deposit. Contact the member to get remaining amount. Deleting customer is not possible.");
        document.getElementById("del_member").disabled = true;
    }
    else if(price != 0){
        document.getElementById("display_info").style.visibility = "visible";

    }

    function delete_cust(){
        if(price != 0) {
            if (!document.getElementById("confirm_del").checked) {
                alert("Confirm about amount recovery for the issued items");
                return false;
            }
        }
        var ret = confirm("Are you sure you want to delete details?");
        if (ret == true) {

            var ret1 = confirm("I confirm that the remaining amount of Rs." + (deposit-price) + " is handed over to the member.");
            if(ret1 == true)
                return true;
        }
        return false;
    }
</script>

<?php
require('include/page_footer.php');
?>