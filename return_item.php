<?php
require('inc/config.php');
require('inc/functions.php');
require('include/page_header.php');
if($_SESSION['role'] == 'member' || $_SESSION['role']=='S') {
	echo "<br/><br/><h3 style='color:red'>Unauthorized access</h3>";
}
else {
    $result = search_member($conn);

    ?>

    <!-- HTML Form -->

    <form onsubmit="return fn_search_mem();" action="<?php $_PHP_SELF ?>" method="post" name="search_mem"
          id="search_mem" autocomplete="off">

        <h2 class="form-signin-heading" style="text-align:center; width:700px;">Return Item</h2>

        <!-- Modal Body -->
        <div class="modal-body">
            <table class="table table-striped">
                <tr>
                    <td>
                        <label for="user_name" class="reg_label">Member User Name </label>
                        <input value="<?php echo isset($_POST['user_name']) ? $_POST['user_name'] : '' ?>"
                               type="text" name="user_name" id="user_name" autofocus>
                    </td>
                    <td> /</td>
                    <td>
                        <label for="mobile_number" class="reg_label">Member Mobile Number </label>
                        <input value="<?php echo isset($_POST['mobile_number']) ? $_POST['mobile_number'] : '' ?>"
                               type="text" name="mobile_number" id="mobile_number" pattern="^[0-9]*$"
                               title="only digits" autofocus>
                    </td>
                    <td>
                        <input type="submit" name="search_mem" class="btn btn-primary" value="Search"/>
                    </td>
                </tr>
            </table>
            <input type="hidden" name="sel_radio" id="sel_radio" value=""/>
        </div>
    </form>
    <?php
    if ($result)
        if ((!empty($result) && !isset($_SESSION['error'])) || (isset($_SESSION['error']) && $_SESSION['error'] == '')) {
            echo "<table class='table table-striped'>";

            if ($result->num_rows == 1) {
                echo "<tr class='info'><th>ID</th><th>User Name</th><th>Address</th><th>Mobile number</th><th>Email</th><th>Membership</th></tr>";
                $row = $result->fetch_array();
                echo "<tr class='success'><td>";
                echo $row[0] . "</td><td>  " . $row[1] . " </td><td> " . $row[3] . " </td><td> " . $row[4] . " </td><td> " . $row[5] . " </td><td> " . $row[9];
                echo "</td></tr></table>";
                $issued_items = get_issued_items($conn, $row[0]);
            } else {
                echo "<tr class='info'><th/><th>ID</th><th>User Name</th><th>Address</th><th>Mobile number</th><th>Email</th><th>Membership</th></tr>";
                while ($row = $result->fetch_array()) {
                    echo "<tr class='success'><td>";
                    echo "<input type='radio' onchange='get_selected_members_info(this)' name = 'rad_btns' value = '" . $row[0] . "'> </td><td>";
                    echo $row[0] . "</td><td>  " . $row[1] . " </td><td> " . $row[3] . " </td><td> " . $row[4] . " </td><td> " . $row[5] . " </td><td> " . $row[9];
                    echo "</td></tr>";
                }
                echo "</table>";
                echo "<span id='msg' name='msg'></span>";

            }
        }
    if (isset($_SESSION['error']) && $_SESSION['error'] != '') {
        echo '<div class="alert fade in" style="width:400px; color:red">';
        echo $_SESSION['error'];
        unset($_SESSION['error']);
        echo '</div>';
    } elseif (isset($_SESSION['result_exists'])) {
        if ($_SESSION['result_exists'] != 'n') {
            $_SESSION['issued_items'] = array();
            echo '<form onsubmit="return validate_chk();" method="post" action="return_item2.php?id=' . $row['member_id'] . '" name="return_item" id="return_item">';
            echo '<div class="txt-heading" ><table width="100%"><tr><td width="50%"><b>Items issued to the member </b>';
            echo '</td><td align="right">';
            echo '<input class="btnAddAction" type="submit" name="return_items" value="Return"/> &nbsp; &nbsp;';
            echo '<input class="btnAddAction" type="submit" name="lost_items" value="Lost"/> &nbsp; &nbsp;';
            echo '<input class="btnAddAction" type="submit" name="damaged_items" value="Damaged"/>';

            echo '</td></tr></table></div>';
            echo '<div align="center" style="color:white; background-color: #adadad" >Items issued from different store are disabled for return. Items have to be returned to the same store where they are issued from.</div>';
            while ($row = $issued_items->fetch_array()) {
                $_SESSION['issued_items'][$row['trans_id']] = $row;
                echo '<div class="product-item">';
                if($row['store_id'] == $_SESSION['store_id'])
                    echo '<input type="checkbox" name="item_list[]" value="' . $row["trans_id"] . '"/>';
                else
                    echo '<input type="checkbox" disabled name="item_list[]" value="' . $row["trans_id"] . '"/>';
                echo '<div class="product-image"><img class="img_vrs"  src="' . $row[2] . '"/></div>';
                echo '<div><strong>' . $row[1] . '</strong><br/>';
                echo 'Copy ID: ' . $row[4] . ' <br/> Issued on: ' . date('d/m/Y', strtotime($row[0]));
                echo '<br/><span class="product-price"> Rent/day: Rs. ' . $row[3] . '</span>';
                echo '</div>';

                echo "</div>";

            }
            echo '</form>';

        } else {
            echo '<h4 style="color:blue; width:700px;text-align:center">No items issued to the member from this store</h4>';

        }

        unset($_SESSION['result_exists']);
    }
    ?>


    <script>

        function validate_chk() {

            var checkboxes = document.getElementsByName("item_list[]");
            var checkboxesChecked = [];
            // loop over them all
            for (var i = 0; i < checkboxes.length; i++) {
                // And stick the checked ones onto an array...
                if (checkboxes[i].checked) {
                    checkboxesChecked.push(checkboxes[i]);
                }
            }
            if (checkboxesChecked.length == 0) {
                alert("Select at least one CD / DVD");
                return false;
            }
            return true;
        }

        function get_selected_members_info(rad) {

            if (rad.value == '') {
                document.getElementById('msg').innerHTML = "Element not selected/entered";
                return;
            } else {

                if (window.XMLHttpRequest) {
                    // code for IE7+, Firefox, Chrome, Opera, Safari
                    xmlhttp = new XMLHttpRequest();
                } else {
                    // code for IE6, IE5
                    xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
                }
                xmlhttp.onreadystatechange = function () {

                    if (this.readyState == 4 && this.status == 200) {
                        document.getElementById('msg').innerHTML = this.responseText;
                    }
                };

                xmlhttp.open("GET", "get_issued_items_for_return3.php?id=" + rad.value, true);
                xmlhttp.send();
            }

        }
        function fn_search_mem() {

            var mobile_number = document.getElementById('mobile_number').value;
            var user_name = document.getElementById('user_name').value;
            //myForm.txtarea.value = "Testing";

            if (user_name == '' && mobile_number == '') {
                alert("Enter user name and / or mobile number");
                return false;
            }
        }
    </script>

    <?php

}
	require('include/page_footer.php');
?>