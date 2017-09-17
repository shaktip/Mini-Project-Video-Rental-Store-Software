<?php
require('inc/config.php');
require('inc/functions.php');
require('include/page_header.php');
if($_SESSION['role'] != 'member' ) {
    echo "<br/><br/><h3 style='color:red'>Unauthorized access</h3>";
    require('include/page_footer.php');
    exit();
}

$member_id = $_SESSION['logged_in_id'];
?>
<table width="100%">
    <tr>
        <td>
            <h2 class="form-signin-heading" style="text-align:center; width:700px;">My Profile</h2>
        </td>
        <td align="right">
            <br/>
            <a href="edit_member.php" class="btn btn-info">Update Profile</a>
        </td>
    </tr>
</table>

<?php

display_member_info($conn, $member_id, "");

$issued_items = get_issued_items($conn, $member_id);
if($issued_items->num_rows == 0){
    echo '<div class="txt-heading" ><b>No items issued as of now </b></div>';
}
else {
    echo '<div class="txt-heading" ><b>Currently Issued Items</b>';
    echo '</div>';

    while ($row = $issued_items->fetch_array()) {

        echo '<div class="product-item">';
        echo '<div class="product-image"><img class="img_vrs" title="' . $row['descr'] . '"  src="' . $row['cover_image'] . '"/></div>';
        echo '<div><strong>' . $row['title'] . '</strong>  (' . (($row['item_type'] == 'V') ? "Video" : "Music") . ')<br/>';
        echo 'Copy ID: ' . $row['copy_id'] . ', Store ID: ' . $row['store_id'] . ' <br/> Issued on: ' . date('d/m/Y', strtotime($row[0]));
        echo '<br/><span class="product-price"> Rent/day: Rs. ' . $row[3] . '<br/> Price: Rs. ' . $row['price'] . '</span>';
        echo '</div>';

        echo "</div>";
    }
}
echo "<div class='txt-heading1'><table width=\"100%\"><tr><td>&nbsp;</td></tr></table></div>";
$ordered_items = get_ordered_items($conn, $member_id);
if($ordered_items->num_rows == 0){
    echo '<div class="txt-heading" ><b>No orders placed as of now </b></div>';
}
else {
    echo '<div class="txt-heading" ><table width="100%"><tr><td width="50%"><b>Currently Placed Orders</b>';
    echo '</td><td align="right">';

    echo '</td></tr></table></div>';

    while ($row = $ordered_items->fetch_array()) {

        echo '<div class="product-item">';
        echo '<div class="product-image"><img class="img_vrs" title="' . $row['descr'] . '"  src="' . $row['cover_image'] . '"/></div>';
        echo '<div><strong>' . $row['title'] . '</strong>  (' . (($row['item_type'] == 'V') ? "Video" : "Music") . ')<br/>';
        echo 'Order for: ' . $row["order_for"] . '<br/>Placed on: ' . date('d/m/Y', strtotime($row[0]));
        echo '<br/><span class="product-price"> Rent/day: Rs. ' . $row[3] . '</span>';
        echo '</div>';

        echo "</div>";

    }
}
require('include/page_footer.php');
?>