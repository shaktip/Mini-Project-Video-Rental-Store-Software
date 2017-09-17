<?php
require('inc/config.php');
require('inc/functions.php');
if(!session_id()) session_start();
$member_id = $_GET['id'];
$issued_items = get_issued_items($conn, $member_id);
$output = '';
if(isset($_SESSION['result_exists'])) {
    if ($_SESSION['result_exists'] != 'n') {
        $_SESSION['issued_items'] = array();
        $output .= '<form onsubmit="return validate_chk();" method="post" action="return_item2.php?id=' . $member_id . '" name="return_item" id="return_item">';
        $output .= '<div class="txt-heading" ><table width="100%"><tr><td width="50%"><b>Items issued to the member </b>';
        $output .= '</td><td align="right">';
        $output .= '<input class="btnAddAction" type="submit" name="return_items" value="Return"/> &nbsp; &nbsp;';
        $output .= '<input class="btnAddAction" type="submit" name="lost_items" value="Lost"/> &nbsp; &nbsp;';
        $output .= '<input class="btnAddAction" type="submit" name="damaged_items" value="Damaged"/>';

        $output .= '</td></tr></table></div>';
        $output .= '<div align="center" style="color:white; background-color: #adadad" >Items issued from different store are disabled for return. Items have to be returned to the same store where they are issued from.</div>';
        while ($row = $issued_items->fetch_array()) {
            $_SESSION['issued_items'][$row['trans_id']] = $row;
            $output .= '<div class="product-item">';
            $st_id = $_SESSION['store_id'];
            $row_st_id = $row['store_id'];
            if($row['store_id'] == $_SESSION['store_id'])
                $output .= '<input type="checkbox" name="item_list[]" value="' . $row["trans_id"] . '"/>';
            else
                $output .= '<input type="checkbox" disabled name="item_list[]" value="' . $row["trans_id"] . '"/>';
            $output .= '<div class="product-image"><img class="img_vrs"  src="' . $row[2] . '"/></div>';
            $output .= '<div><strong>' . $row[1] . '</strong><br/>';
            $output .= 'Copy ID: ' . $row[4] . ' <br/> Issued on: ' . date('d/m/Y', strtotime($row[0]));
            $output .= '<br/><span class="product-price"> Rent/day: Rs. ' . $row[3] . '</span>';
            $output .= '</div>';

            $output .= "</div>";

        }
        $output .= '</form>';

    } else {
        $output .= '<h4 style="color:blue; width:700px;text-align:center">No items issued to the member from this store</h4>';

    }

    unset($_SESSION['result_exists']);
    echo $output;
    $conn->close();
}
?>