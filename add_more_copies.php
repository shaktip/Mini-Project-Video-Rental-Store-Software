<?php
require('inc/config.php');
require('inc/functions.php');
require('include/page_header.php');
if($_SESSION['role'] == 'member' || $_SESSION['role'] == 'C') {
	echo "<br/><br/><h3 style='color:red'>Unauthorized access</h3>";
}
else {
    if (!isset($_GET['id']))
        header("location: manage_items.php");
    else {
        $item_id = $_GET['id'];
        if ($new_no_of_copies = add_more_copies($conn)) {
            echo "<h2>Details are updated successfully! </h2><br/>";
            $row = $_SESSION['item_details'];
            unset($_SESSION['item_details']);


            echo "<table class='table' border='0'>";
            echo "<tr>";
            echo '<td valign="top" width="250px" rowspan = 6>';
            echo '<img class="single_img_vrs" src="' . $row['cover_image'] . '"/>';
            echo '</td>';
            echo "<td class='success'><b>Title: </b></td><td class='success' style='padding:5px'>" . $row['title'] . "</td>";

            echo "<tr class='success'><td style='padding:5px' width='200px'><b>Description: </b> </td><td style='padding:5px'> " . $row['descr'] . "</td>";
            echo "<tr class='success'><td style='padding:5px'><b>Daily Rent:</b> </td><td style='padding:5px'> " . $row['daily_rent'] . "</td>";
            echo "<tr class='success'><td style='padding:5px'><b>Language: </b></td><td style='padding:5px'>" . $row['language'] . "</td>";
            echo "<tr class='success'><td style='padding:5px'><b>Genre:</b> </td><td style='padding:5px'> " . $row['genre'] . "</td>";
            echo "<tr class='success'><td style='padding:5px'><b>Total number of copies: </b></td><td style='padding:5px'> " . $new_no_of_copies . "</td>";
            echo "<tr><td>&nbsp;</td><tr/>";
            echo "</table>";
            echo '<br/>	<a href="manage_items.php" class="btn btn-primary" >&nbsp; OK &nbsp;</a>';

            $_POST['item_title'] = '';
            $_POST['desc'] = '';
            $_POST['daily_rent'] = 0;
            $_POST['no_of_copies'] = 0;
            unset($_SESSION['cover_image']);
        } else {
            $res = $conn->query("SELECT * FROM item_master WHERE item_id=$item_id");
            $row = $res->fetch_array();

            $store_id = $_SESSION['store_id'];
            $res = $conn->query("SELECT count(*) FROM item_details WHERE item_id=$item_id AND store_id=$store_id");
            $itd_row = $res->fetch_array();
            $_SESSION['item_details'] = $row;

            $no_of_copies = $itd_row[0];
            $_SESSION['no_of_copies'] = $no_of_copies;

            echo '<h2 class="form-signin-heading" style="text-align:center; width:700px;">Add More Copies</h2>';
            echo '<h4><b>Item: </b></h4>';
            echo "<table class='table table-striped'>";

            echo "<tr class='info'><th>Title</th><th>Language</th><th>Genre</th><th>Daily Rent</th><th>Current # of copies</th><th>Description</th></tr>";

            echo "<tr class='success'><td>";
            echo $row[1] . "</td><td>  " . $row[2] . " </td><td> " . $row[3] . " </td><td> Rs." . $row[7] . " </td><td> " . $no_of_copies . " </td><td width='200px'> " . $row[5];
            echo "</td></tr></table>";

            ?>

            <br/>


            <!-- HTML Form -->
            <form action="<?php $_PHP_SELF ?>" method="post" name="add_more_copies" id="add_more_copies"
                  autocomplete="off">


                <!-- Modal Body -->
                <table>
                    <tr>
                        <td valign="top" width="200px">
                            <?php
                            echo '<img class="single_img_vrs" src="' . $row["cover_image"] . '"/>';
                            ?>
                        </td>
                        <td>
                            <div class="modal-body">


                                <div class="form-group">

                                    <label for="more_no_of_copies" class="reg_label">Copies procured &nbsp;
                                        &nbsp;</label>
                                    <input type="range" name="more_no_of_copies" id="no_of_copies"
                                           value="<?php echo isset($_POST['more_no_of_copies']) ? $_POST['more_no_of_copies'] : 1 ?>"
                                           onchange="document.getElementById('copiesText').innerHTML = ' &nbsp;&nbsp; <b>' + this.value; + '</b>'"
                                           min="1" max="50" style="width:200px; display:inline;">
                                    <span id="copiesText" style="color:blue;"> &nbsp;&nbsp; <b>1</b></span>
                                </div>

                                <div class="form-group">
                                    <label for="price" class="reg_label">Price Rs. &nbsp;&nbsp;</label>
                                    <input value="<?php echo isset($_POST['price']) ? $_POST['price'] : '' ?>"
                                           class="input-sm" type="text" name="price" id="price" required pattern="^[1-9][0-9]*$"
                                           title="only digits">
                                </div>

                                <?php
                                if (isset($_SESSION['error']) && $_SESSION['error'] != '') {
                                    echo '<div class="alert fade in" style="text-align:center; width:400px; color:red">';
                                    echo $_SESSION['error'];
                                    unset($_SESSION['error']);
                                    echo '</div>';
                                }
                                ?>

                                <div class="form-group">
                                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                    <input type="submit" name="add_more_copies" class="btn btn-lg btn-success"
                                           value="Add" id="add_more_copies"/>
                                    &nbsp;&nbsp;&nbsp;&nbsp;
                                    <a href="manage_items.php" class="btn  btn-lg btn-default">Cancel</a>
                                </div>
                            </div>
                        </td>

                </table>
            </form>

            <?php
        }
    }
}
 require('include/page_footer.php');
?>

