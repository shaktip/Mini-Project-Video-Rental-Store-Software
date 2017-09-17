<?php
require('inc/config.php');
require('inc/functions.php');
if(!session_id()) session_start();
$member_id = $_GET['id'];
$issued_items = get_issued_items($conn, $member_id);
$output = '';
if(isset($_SESSION['result_exists'])) {
    if ($_SESSION['result_exists'] == 'y') {
        $output = $output . '<div class="txt-heading" ><b>Items already issued to the member</b><a id="btnEmpty" href="issue_item2.php?id=' . $member_id . '" name="issue_item"  >Proceed to issue</a></div>';
        while ($row = $issued_items->fetch_array()) {
            $output .= '<div class="product-item">';
            $output .= '<div class="product-image"><img class="img_vrs" src="' . $row[2] . '"/></div>';
            $output .= '<div><strong>' . $row[1] . '</strong><br/>';
            $output .= 'Copy ID: ' . $row[4] . ' <br/> Issued on: ' . date('d/m/Y', strtotime($row[0]));
            $output .= '<br/><span class="product-price"> Rent/day: Rs. ' . $row[3] . '</span>';
            $output .= '</div>';
            $output .= "</div>";
        }

    } elseif ($_SESSION['result_exists'] == 'n') {
        $output = $output . '<h4 style="color:blue">No items issued to the selected member as of now</h4>';
        $output = $output . '<a href="issue_item2.php?id=' . $member_id . '" name="issue_item" class="btn btn-primary">Proceed to Issue </a>';
        $output = $output . '&nbsp;&nbsp;&nbsp;&nbsp;';
        $output = $output . '<a href="emp_home.php" class="btn btn-default" >Cancel</a>';
    } else {
        $output = $output . '<div class="txt-heading" ><b>No more items can be issued to the selected member</b></div>';
        while ($row = $issued_items->fetch_array()) {
            $output .= '<div class="product-item">';
            $output .= '<div class="product-image"><img class="img_vrs" src="' . $row[2] . '"/></div>';
            $output .= '<div><strong>' . $row[1] . '</strong><br/>';
            $output .= 'Copy ID: ' . $row[4] . ' <br/> Issued on: ' . date('d/m/Y', strtotime($row[0]));
            $output .= '<br/><span class="product-price"> Rent/day: Rs. ' . $row[3] . '</span>';
            $output .= '</div>';
            $output .= "</div>";

        }
    }

    unset($_SESSION['result_exists']);
    echo $output;
    $conn->close();
}
?>