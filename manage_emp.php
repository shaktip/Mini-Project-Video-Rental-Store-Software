<?php
require('inc/config.php');
require('inc/functions.php');
require('include/page_header.php');

if($_SESSION['role'] == 'member') {
	echo "<br/><br/><h3 style='color:red'>Unauthorized access</h3>";
}
else {
    $condition = "status='A'";
    if ($_SESSION['role'] != 'S')
        $condition = $condition . " AND store_id = " . $_SESSION['store_id'];
    if ($emp_rows = get_rows($conn, "employee", "empid, emp_name, contact_number, address, role, store_id", $condition)) {

        ?>
        <h2 class="form-signin-heading" style="text-align:center; width:700px;"> Employee Details</h2>
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
                        <a href="add_edit_emp.php?action=a&store_id=null" class="btn btn-primary">&nbsp; Add &nbsp;</a>
                        <button class="btn btn-primary" onclick="call_edit();">&nbsp; Edit &nbsp;</button>
                        <button class="btn btn-primary" onclick="delete_record('emp');">Delete</button>
                    </td>
                <?php } ?>
            </tr>
        </table>
        <br/>
        <table class="table table-striped table-bordered table-hover">
            <thead>
            <tr>
                <?php if ($_SESSION['role'] != 'C') echo "<th></th>" ?>
                <th>Emp ID</th>
                <th>Name</th>
                <th>Mobile number</th>
                <th>Address</th>
                <th>Role</th>
                <th>Store ID</th>
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
        </script>
        <?php
    } else {
        echo "<br/><br/><h3 style='width:700px; text-align:center; color: blue'> Employee information is not present </h3>";
        echo '<table width="100%">';
        echo '<tr width="100%">';

        echo '<td style=" text-align:right; align:right;">';
        echo '	<a href="add_edit_emp.php?action=a&store_id=null" class="btn btn-primary">&nbsp; Add &nbsp;</a>';
        echo '</td>';
        echo '</tr>';
        echo '</table>';
    }
}
 require('include/page_footer.php');
?>