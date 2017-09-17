<?php
require('inc/config.php');
require('inc/functions.php');
require('include/page_header.php');
if($_SESSION['role'] == 'member' || $_SESSION['role'] == 'S') {
    echo "<br/><br/><h3 style='color:red'>Unauthorized access</h3>";
}
elseif(!empty($_POST) && isset($_POST['issue_item'])) {
    $member_id = safe_input($conn, $_POST['member_id']);
    $item_id = safe_input($conn, $_POST['item_id']);
    $condition = "item_id='" . $item_id . "' AND availability = 1 AND store_id='" . $_SESSION['store_id'] . "' AND status='A'";
    $sql = "SELECT min(copy_id) FROM item_details WHERE " . $condition;

    $res = $conn->query($sql);
    $copy_row = $res->fetch_array();
    if (isset($_SESSION['member_row'])) {
        $member_row = $_SESSION['member_row'];
        unset($_SESSION['member_row']);

        $sql = "SELECT * FROM item_master WHERE item_id = " . $item_id;
        $res = $conn->query($sql);
        $item_row = $res->fetch_array();

        if (issue_item($conn, $copy_row[0], $member_id)) {

            ?>

            <h2 class="form-signin-heading" style="text-align:center; width:700px;">Item issued</h2>
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
            <h4>Issued item information </h4>
            <table class="table ">
                <tr>
                    <td width="200px">
                        <?php echo '<img class="single_img_vrs"  src="' . $item_row['cover_image'] . '">';
                        ?>
                    </td>

                    <td valign="top">
                        <table class="fa-table">

                            <?php
                            echo "<tr class=''><th width='120px'>Item ID: </th><td>" . $item_row[0] . "</td>";
                            echo "<tr><th>Copy ID: </th><td>" . $copy_row[0] . " </td>";
                            echo "<tr><th>Title:</th><td> " . $item_row[1] . " </td>";
                            echo "<tr><th>Rent Per Day:</th><td> Rs." . $item_row[7];
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
                    <td>Issue Date: &nbsp; &nbsp;&nbsp;&nbsp;<?php echo date("Y-m-d H:i:s"); ?> </td>
                    <td>Issued By: &nbsp; &nbsp;&nbsp;&nbsp;<?php echo $_SESSION['logged_in_user']; ?> </td>
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

        echo "<a class='btn btn-primary' href='issue_item.php'> OK </a>";
        echo '</div>';
    }
}
else {
    echo '<div class="alert fade in" style="font-size: 20px; width:400px; color:red">';
    echo "Some error occurred... Try again... <br/><br/>";

    echo "<a class='btn btn-primary' href='issue_item.php'> OK </a>";
    echo '</div>';
}
require('include/page_footer.php');
?>