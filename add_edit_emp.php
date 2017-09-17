    <?php
    require('inc/config.php');
    require('inc/functions.php');
    require('include/page_header.php');
    if($_SESSION['role'] == 'member' || $_SESSION['role'] == 'C') {
        echo "<br/><br/><h3 style='color:red'>Unauthorized access</h3>";
    }
    else {

        $action = $_GET['action'];
        if ($action == 'a')
            $store_id = $_GET['store_id'];
        $msg = 'Add New Employee';
        $btn_name = 'add_emp';
        if ($action == 'e') {
            $btn_name = 'edit_emp';
            $msg = 'Edit Employee Details';
            $_SESSION['sel_empid'] = $empid = $_GET['id'];
            $ret_value = get_rows($conn, "employee", "empid, emp_name, contact_number, address, role, store_id", "empid = " . $empid);
            $emp_row = $ret_value->fetch_array();
            $store_id = $emp_row["store_id"];
        }
        $emp_id = add_edit_employee($conn);
        if ($emp_id != FALSE) {
            if ($emp_id == "updated") {
                echo "<h2>Details are updated successfully! </h2>";
            } else {
                echo "<h2>Details are added successfully! </h2>";
            }
            echo "<table style='padding:5px; background-color:lightyellow; margin-left: 50px '>";
            echo "<tr><td style='padding:5px'><b>Employee name: </td><td style='padding:5px'>" . $_POST['emp_name'] . "</td>";
            echo "<tr><td style='padding:5px'><b>Address: </b></td><td style='padding:5px'> " . $_POST['address'] . "</td>";
            echo "<tr><td style='padding:5px'><b>Mobile Number: </b></td><td style='padding:5px'> " . $_POST['mobile_number'] . "</td>";
            echo "<tr><td style='padding:5px'><b>Store ID: </b></td><td style='padding:5px'> " . $_POST['sel_store_id'] . "</td>";
            echo "<tr><td style='padding:5px'><b>Role: </b></td><td style='padding:5px'> ";
            if($_POST['sel_role']=='M')
                echo 'Manager';
            else
                echo 'Clerk Staff';
            echo "</td>";

            if ($emp_id != "updated") {
                echo "<tr><td style='padding:5px'>New emp id: </td><td style='padding:5px'> <b>" . $emp_id . "</b></td>";
                echo "<tr><td style='padding:5px'>Default password: </td><td style='padding:5px'> <b>vrs</b></td>";
            }
            echo "</table>";
            echo '<br/>	<a href="manage_emp.php" class="btn btn-primary">&nbsp; OK &nbsp;</a>';
            $_POST['emp_name'] = '';
            $_POST['address'] = '';
            $_POST['mobile_number'] = '';

        } else {
            if ($_SESSION['role'] == 'S') {
                if (($btn_name == 'add_emp') || ($btn_name == 'edit_emp' && $emp_row['role'] != 'S')) {

                    $store_rows = get_rows($conn, "store", "*", " status = 'A' ");
                    if ($store_rows->num_rows == 0) {
                        echo "<script> alert('Add store first'); </script>";
                        header("location: add_edit_store.php");
                        exit();
                    } else {
                        $php_array = array();
                        while ($row = $store_rows->fetch_array()) {
                            $php_array[$row['store_id']] = $row['address'] . ",  " . $row['city'] . ",  " . $row['pin_code'];
                        }
                    }
                }
            }
            ?>


            <!-- HTML Form -->
            <form action="<?php $_PHP_SELF ?>" method="post" name="emp" id="emp" autocomplete="off">


                <h2 class="form-signin-heading" style="text-align:center; width:500px;"><?php echo $msg ?></h2>

                <!-- Modal Body -->
                <div class="modal-body">
                    <div class="form-group">
                        <label for="emp_name" class="reg_label">Employee Name</label>
                        <input
                               value="<?php if ($btn_name == 'add_emp') echo isset($_POST['emp_name']) ? $_POST['emp_name'] : ''; else echo $emp_row['emp_name']; ?>"
                               class='input-sm' type="text" name="emp_name" id="emp_name" required pattern="^.{3,20}$"
                               title="min 3, max 20 characters." autofocus>
                    </div>
                    <div class="form-group">
                        <label for="address" class="reg_label">Address</label>
                        <input
                               value="<?php if ($btn_name == 'add_emp') echo isset($_POST['address']) ? $_POST['address'] : ''; else echo $emp_row['address']; ?>"
                               class='input-sm' type="text" name="address" id="address" required>
                    </div>
                    <div class="form-group">
                        <label for="mobile_number" class="reg_label">Mobile Number</label>
                        <input
                               value="<?php if ($btn_name == 'add_emp') echo isset($_POST['mobile_number']) ? $_POST['mobile_number'] : ''; else echo $emp_row['contact_number']; ?>"
                               class='input-sm' type="text" name="mobile_number" id="mobile_number" required pattern="^[789][0-9]{9}$"
                               title="10 digits starting with 7/8/9">
                    </div>
                    <input type="hidden" id="sel_store_id" name="sel_store_id"
                           value="<?php if ($btn_name == 'add_emp') echo $_SESSION['store_id']; else echo $emp_row['store_id']; ?>"/>
                    <input type="hidden" id="sel_role" name="sel_role" value="<?php if ($btn_name == 'add_emp') echo 'C'; else echo $emp_row['role']; ?>"/>
                    <?php
                    if ($_SESSION['role'] == "S") {

                        ?>
                        <div class="form-group">
                            <label class="reg_label">Role</label>
                            <input type="radio" name="role" id="clerk" value="C" checked
                                   onchange="document.getElementById('sel_role').value = 'C'; "/>
                            <label for="clerk"> Clerk Staff &nbsp;&nbsp; </label>
                            <?php
                            if ($btn_name == 'add_emp') {
                                ?>
                            <input type="radio" name="role" id="manager" value="M"
                                   onchange="document.getElementById('sel_role').value = 'M'; "/>
                            <?php
                            }
                            elseif ($btn_name == 'edit_emp'){
                            if ($emp_row['role'] == 'M') {
                            ?>
                            <input type="radio" name="role" id="manager" value="M" checked
                                   onchange="document.getElementById('sel_role').value = 'M'; "/>
                                <script>
                                    document.getElementById("sel_role").value = 'M';
                                </script>
                            <?php }
                            else {
                            ?>
                            <input type="radio" name="role" id="manager" value="M"
                                   onchange="document.getElementById('sel_role').value = 'M'; "/>

                                <?php
                            }
                            }
                            ?>
                            <label for="manager"> Manager &nbsp;&nbsp; </label>

                        </div>

                        <div class="form-group">
                            <label class="reg_label">Store id</label>
                            <select style="width:50px" name="store_id" id="store_id"
                                     onchange="fn_update_values(this)">
                                <?php
                                $ind = -1;
                                $store_index = 0;
                                foreach ($php_array as $key => $value) {
                                    ++$ind;
                                    if ($key == $store_id) $store_index = $ind;
                                    ?>
                                    <option value="<?php echo $value ?>"><?php echo $key ?></option>
                                <?php } ?>
                            </select>
                            <script>
                                var e = document.getElementById("store_id");
                                e.selectedIndex = 0;
                            </script>
                            <?php
                            if ($store_id != 'null') {
                                echo "<script> e.selectedIndex = " . $store_index . "</script>";
                            }
                            ?>

                            <span id="addr" style="color:blue;"> </span>

                            <script>
                                document.getElementById("addr").innerHTML = " &nbsp;&nbsp; <b>" + e.options[e.selectedIndex].value + "</b>";
                                document.getElementById("sel_store_id").value = e.options[e.selectedIndex].text;
                            </script>
                        </div>
                        <?php
                    } elseif ($btn_name == 'edit_emp' && $emp_row['role'] == 'S') {
                        echo "<script> document.getElementById('sel_role').value = 'S'; </script>";

                    }
                    ?>

                </div>

                <div class="alert fade in" style="text-align:center; width:400px; color:red">
                    <?php
                    if (isset($_SESSION['error'])) {
                        echo $_SESSION['error'];
                        unset($_SESSION['error']);
                    }
                    ?>
                </div>
                <div class="form-group">
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                    <input type="submit" name="<?php echo $btn_name; ?>" class="btn btn-lg btn-success" value="Save"/>
                    &nbsp;&nbsp;&nbsp;&nbsp;
                    <a href="manage_emp.php" class="btn  btn-lg btn-default">Cancel</a>

                </div>

                </div>
            </form>

            <script>
                function fn_update_values(sel_store){
                    document.getElementById('addr').innerHTML = ' &nbsp;&nbsp; <b>' + sel_store.value; + '</b>';
                    document.getElementById('sel_store_id').value = sel_store.options[sel_store.selectedIndex].text;


                }
            </script>

            <?php
        }
    }
     require('include/page_footer.php');
    ?>

