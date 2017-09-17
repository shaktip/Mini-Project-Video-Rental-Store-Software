<?php
require('inc/config.php');
require('inc/functions.php');
require('include/page_header.php');

if($_SESSION['role'] == 'member') {
	echo "Unauthorized access";
}
else {
    $condition = "status='A'";
    // if ($_SESSION['role'] != 'S')
    // $condition = $condition . " AND store_id = ". $_SESSION['store_id'];
    if ($emp_rows = get_rows($conn, "store", "store_id, address, city, pin_code, contact_number", $condition)) {

        ?>
        <h2 class="form-signin-heading" align="center"> Store Details</h2>
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
                <?php if ($_SESSION['role'] == 'S') { ?>
                    <td style=" text-align:right; align:right;">
                        <a href="add_edit_store.php?action=a" class="btn btn-primary">&nbsp; Add &nbsp;</a>
                        <button class="btn btn-primary" onclick="call_store_edit();">&nbsp; Edit &nbsp;</button>
                        <button class="btn btn-primary" onclick="delete_record('store');">Close Store</button>
                    </td>
                <?php } ?>
            </tr>
        </table>
        <br/>
        <table class="table table-striped table-bordered table-hover">
            <thead>
            <tr>
                <?php if ($_SESSION['role'] == 'S') echo "<th></th>" ?>
                <th>Store ID</th>
                <th>Address</th>
                <th>City</th>
                <th>Pin Code</th>
                <th>Contact Number</th>

            </tr>
            </thead>
            <input type="hidden" name="sel_radio" id="sel_radio" value=""/>

            <?php
            $field_count = $emp_rows->field_count;
            while ($row = $emp_rows->fetch_array()) {
                echo "<tr>";
                if ($_SESSION['role'] == 'S')
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
            function call_store_edit() {
                var sel_id = document.getElementById("sel_radio").value;
                if (sel_id == '') {
                    alert("No row selected");
                    return;
                }
                window.location = "add_edit_store.php?action=e&id=" + sel_id;
            }
        </script>
        <?php
    } else {
        echo "<br/><br/><h3 style='width:700px; text-align:center; color: blue'> Store information is not present </h3>";
        echo '<table width="100%">';
        echo '<tr width="100%">';

        echo '<td style=" text-align:right; align:right;">';
        echo '	<a href="add_edit_store.php?action=a" class="btn btn-primary">&nbsp; Add &nbsp;</a>';
        echo '</td>';
        echo '</tr>';
        echo '</table>';
    }
}
 require('include/page_footer.php');
?>