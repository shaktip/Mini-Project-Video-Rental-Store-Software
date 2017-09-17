<?php
require_once('inc/dbcontroller.php');
require('inc/functions.php');
require('include/page_header.php');
require('inc/config.php');
$db_handle = new DBController();
if($_SESSION['role'] == 'member' || $_SESSION['role'] == 'S' ) {
	echo "<br/><br/><h3 style='color:red'>Unauthorized access</h3>";
}
else {
    $sql = search_for_manage_items($conn);
    // echo $sql;
    $product_array = $db_handle->runQuery($sql);
    ?>
    <h2 class="form-signin-heading" style="text-align:center; width:700px;">Item Details</h2>

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
            <?php if ($_SESSION['role'] == 'M') { ?>
                <td style=" text-align:right; align:right;">
                    <a href="add_new_item.php" class="btn btn-primary">&nbsp; Add New Item &nbsp;</a>
                    <?php if (count($product_array) != 0) { ?>
                        <button class="btn btn-primary" onclick="call_add_more();">Add More Copies</button>
                        <button class="btn btn-primary" onclick="call_edit();">&nbsp; Edit &nbsp;</button>
                        <a href="unused_stock.php" class="btn btn-primary">&nbsp; Unused Stock &nbsp;</a>
                        &nbsp;&nbsp;&nbsp;
                    <?php } ?>
                </td>
            <?php }

            ?>
        </tr>
    </table>

    <?php if (count($product_array) == 0 && !isset($_POST['item_language'])) {
        echo "<h3> No items available in the inventory. </h3>";
    } else {
        ?>
        <form action="<?php $_PHP_SELF ?>" method="post" name="search_prod" id="search_prod" autocomplete="off">
            <div class="modal-body">
                <table class="table table-striped ">
                    <tr>
                        <td>
                            <label for="item_language">Language</label>
                            <select name="item_language" id="item_language" class='input-sm'>
                                <option value="All">All</option>
                                <option value="Hindi">Hindi</option>
                                <option value="English">English</option>
                                <option value="Bengali">Bengali</option>
                                <option value="Tamil">Tamil</option>
                                <option value="Marathi">Marathi</option>
                            </select>
                        </td>

                        <td>
                            <label for="genre">Genre</label>
                            <select name="genre" id="genre" class='input-sm'>
                                <option value="All">All</option>
                                <option value="Romantic">Romantic</option>
                                <option value="Comedy">Comedy</option>
                                <option value="Action">Action</option>
                                <option value="Sci-fi">Sci-Fi</option>
                                <option value="Horror">Horror</option>
                                <option value="Music">Music</option>
                                <option value="Patriotic">Patriotic</option>
                                <option value="Thriller">Thriller</option>
                                <option value="Social">Social</option>
                            </select>
                        </td>
                        <td>
                            <label for="item_title">Title</label>
                            <input value="<?php echo isset($_POST['item_title']) ? $_POST['item_title'] : '' ?>"
                                   class='input-sm' type="text" name="item_title" id="item_title" autofocus>
                        </td>
                        <td>
                            <input type="submit" name="search_prod" class="btn btn-primary" value="Search"/>
                        </td>
                    </tr>
                </table>
            </div>

            <?php
            $val = isset($_POST['item_language']) ? $_POST['item_language'] : '';
            echo "<input type='hidden' id='item_lang' value='" . $val . "'/>";

            $val = isset($_POST['genre']) ? $_POST['genre'] : '';
            echo "<input type='hidden' id='item_gen' value='" . $val . "'/>";
            ?>
        </form>


        <script>
            var item_lang = document.getElementById('item_lang').value;
            var item_gen = document.getElementById('item_gen').value;

            var dd = document.getElementById('item_language');
            for (var i = 0; i < dd.options.length; i++) {
                if (dd.options[i].text === item_lang) {
                    dd.selectedIndex = i;
                    break;
                }
            }

            var gen = document.getElementById('genre');
            for (var i = 0; i < gen.options.length; i++) {
                if (gen.options[i].text === item_gen) {
                    gen.selectedIndex = i;
                    break;
                }
            }
        </script>

        <div id="product-grid">

            <?php
            if (count($product_array) == 0 && isset($_POST['item_language'])) {
                echo "<h3> No items matching the search criteria. </h3>";
            }
            if (!empty($product_array)) {
                echo '<div class="txt-heading">Search Result</div>';
                foreach ($product_array as $key => $value) {
                    echo '<div class="product-item">';
                    if ($_SESSION['role'] == 'M')
                        echo "<input type='radio' onchange='set_sel_radio(this)' name = 'rad_btns' value='" . $product_array[$key]["item_id"] . "'/>";
                    echo '<div class="product-image"><img class="img_vrs" src="' . $product_array[$key]["cover_image"] . '"/></div>';
                    echo '<div><strong>' . $product_array[$key]["title"] . '</strong><br/>';
                    echo '# of copies: ' . $product_array[$key]["num_of_copies"];
                    echo '<br/><span class="product-price"> Rent/day: Rs. ' . $product_array[$key]["daily_rent"] . '</span>';
                    echo '</div>';

                    echo "</div>";
                }

            }
            ?>
            <input type="hidden" name="sel_radio" id="sel_radio" value=""/>
        </div>

        <script>
            function set_sel_radio(rad) {
                document.getElementById("sel_radio").value = rad.value;
            }
            function call_edit() {
                var sel_id = document.getElementById("sel_radio").value;
                if (sel_id == '') {
                    alert("Select the item to be edited");
                    return;
                }
                window.location = "edit_item.php?action=e&id=" + sel_id;
            }
            function call_add_more() {
                var sel_id = document.getElementById("sel_radio").value;
                if (sel_id == '') {
                    alert("Select the item to which more copies have to be added");
                    return;
                }
                window.location = "add_more_copies.php?action=a&id=" + sel_id;
            }
        </script>
        <?php
    }
}
require('include/page_footer.php');
?>