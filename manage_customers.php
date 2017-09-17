<?php
require('inc/config.php');
require('inc/functions.php');
require('include/page_header.php');

if($_SESSION['role'] == 'member') {
	echo "<br/><br/><h3 style='color:red'>Unauthorized access</h3>";
}
else {
    $condition = "status='A'";
    if ($emp_rows = get_rows($conn, "member", "member_id, user_name, contact_number, address, email, membership_type", $condition)) {

        ?>
        <h2 class="form-signin-heading" style="text-align:center;" align="center"> Member Details</h2>
        <table width="100%">
            <tr width="100%">
                <td>
                    <?php
                    if (isset($_SESSION['msg'])) {
                        echo "<b> " . $_SESSION['msg'] . " </b>";
                        $_SESSION['msg'] = '';
                    }
                    ?>
                </td>
                <?php if ($_SESSION['role'] != 'C') { ?>
                    <td style=" text-align:right; align:right;">
                        <button class="btn btn-primary" onclick="delete_cust();">Delete</button>
                    </td>
                <?php } ?>
            </tr>
        </table>
        <br/>
        <table class="table table-striped table-bordered table-hover">
            <thead>
            <tr>
                <?php if ($_SESSION['role'] != 'C') echo "<th></th>" ?>
                <th>ID</th>
                <th>Name</th>
                <th>Mobile number</th>
                <th>Address</th>
                <th>Email ID</th>
                <th>Membership Type</th>
            </tr>
            </thead>
            <input type="hidden" name="sel_radio" id="sel_radio" value=""/>

            <?php
            $field_count = $emp_rows->field_count;
            while ($row = $emp_rows->fetch_array()) {
                echo "<tr>";
                if ($_SESSION['role'] != 'C')
                    echo "<td><input type='radio' onchange='set_sel_radio(this)' name = 'rad_btns' value = '" . $row[0] . "'> </td>";
                for ($i = 0; $i < $field_count; $i++) {
                    echo "<td>" . $row[$i] . "</td>";
                }
                echo "</tr>";
            }
            ?>
        </table>

        <script>
            function set_sel_radio(rad) {
                document.getElementById("sel_radio").value = rad.value;
            }
            function call_edit() {
                var sel_id = document.getElementById("sel_radio").value;
                if (sel_id == '') {
                    alert("No row selected");
                    return;
                }
                window.location = "add_edit_emp.php?action=e&id=" + sel_id;
            }

            function delete_cust(tab_name){
                var sel_id = document.getElementById("sel_radio").value;
                if (sel_id == ''){
                    alert("No row selected");
                    return;
                }
                var ret = confirm("Are you sure you want to delete details?");
                if (ret == true) {
                    window.location = "delete_customer.php?id="+sel_id;
                }
            }
        </script>
        <?php
    } else {
        if (isset($_SESSION['msg'])) {
            echo "<br/><br/> <b>" . $_SESSION['msg'] . " </b>";
            $_SESSION['msg'] = '';
        }
        echo "<br/><br/><h4 style='width:700px; text-align:center; color: blue'> Customer information is not present </h4>";

    }
}
 require('include/page_footer.php');
?>