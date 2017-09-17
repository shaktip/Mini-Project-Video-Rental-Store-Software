<?php
require_once('inc/dbcontroller.php');
require('inc/functions.php');
require('include/page_header.php');
require('inc/config.php');
$db_handle = new DBController();
if($_SESSION['role'] == 'member' || $_SESSION['role'] == 'S') {
	echo "<br/><br/><h3 style='color:red'>Unauthorized access</h3>";
}
else {
    if (isset($_GET['id']))
        $member_id = $_GET['id'];
    if (!isset($member_id)) {
        echo '<div class="alert fade in" style="font-size: 20px; width:400px; color:red">';
        echo "Some error occurred... Try again... <br/><br/>";
        if ($_SESSION['role'] == 'member')
            echo "<a class='btn btn-primary' href='member_home.php'> OK </a>";
        else
            echo "<a class='btn btn-primary' href='emp_home.php'> OK </a>";
        echo '</div>';
        require('include/page_footer.php');
        exit();
    }
    $sql = "SELECT * FROM member WHERE member_id = " . $member_id;
    $res = $conn->query($sql);
    if($res->num_rows == 0){
        echo '<div class="alert fade in" style="font-size: 20px; width:400px; color:red">';
        echo "Some error occurred... Try again... <br/><br/>";
        if ($_SESSION['role'] == 'member')
            echo "<a class='btn btn-primary' href='member_home.php'> OK </a>";
        else
            echo "<a class='btn btn-primary' href='emp_home.php'> OK </a>";
        echo '</div>';
        require('include/page_footer.php');
        exit();
    }

    $member_row = $res->fetch_array();
    $_SESSION['member_row'] = $member_row;

    $sql = search_items($conn, 'I');

    $product_array = $db_handle->runQuery($sql);

    ?>
    <table width="100%">
        <tr>
            <td width="200px">
                <h2 class="form-signin-heading">Issue Item To</h2>
            </td>
            <td align="right">

                <table class='table-bordered'>

                    <tr bgcolor="#f0ffff">
                        <td rowspan="2" align="center">&nbsp;&nbsp;<b>User Name&nbsp;&nbsp;</b></td>
                        <td rowspan="2" align="center">&nbsp;&nbsp;<b>Mobile Number&nbsp;&nbsp;</b></td>
                        <td rowspan="2" align="center">&nbsp;&nbsp;<b>Membership&nbsp;&nbsp;</b></td>
                        <td colspan="2" align="center">&nbsp;&nbsp;<b># Video CDs&nbsp;&nbsp;</b></td>
                        <td colspan="2" align="center">&nbsp;&nbsp;<b># Music CDs&nbsp;&nbsp;</b></td>
                    </tr>
                    <tr bgcolor="#b0e0e6">
                        <td align="center">&nbsp;&nbsp;<b>Issued&nbsp;&nbsp;</b></td>
                        <td align="center">&nbsp;&nbsp;<b>More Allowed&nbsp;&nbsp;</b></td>
                        <td align="center">&nbsp;&nbsp;<b>Issued&nbsp;&nbsp;</b></td>
                        <td align="center">&nbsp;&nbsp;<b>More Allowed&nbsp;&nbsp;</b></td>
                    </tr>

                    <?php
                    echo "<tr ><td align='center'>&nbsp; &nbsp;";
                    echo $member_row[1] . "&nbsp; &nbsp;</td><td align='center'>&nbsp; &nbsp;  " . $member_row[4] . " &nbsp; &nbsp;</td><td align=\"center\"> " . $member_row[9] . " &nbsp; &nbsp;</td><td align='center'>&nbsp; &nbsp; " . ($_SESSION["eligible_for"][0][0] - $_SESSION["eligible_for"][0][1]) . " &nbsp; &nbsp;</td><td align='center'>&nbsp; &nbsp; " . $_SESSION["eligible_for"][0][1] . " &nbsp; &nbsp;</td><td align='center'>&nbsp; &nbsp; " . ($_SESSION["eligible_for"][1][0] - $_SESSION["eligible_for"][1][1]) . " &nbsp; &nbsp;</td><td align='center'>&nbsp; &nbsp; " . $_SESSION["eligible_for"][1][1];
                    ?>
                    &nbsp; &nbsp;</td></tr></table>
            </td>
        </tr>
    </table>
    <?php
    if (!isset($_POST['item_language']) && empty($product_array)) {
        echo "No items matching the criteria";
    } else {
        ?>
        <br/>
        <form action="<?php $_PHP_SELF ?>" method="post" name="search_prod" id="search_prod" autocomplete="off">

            <table class="table table-striped " width="100%">
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
                        </select>
                    </td>
                    <td>
                        <label for="item_title">Title</label>
                        <input value="<?php echo isset($_POST['item_title']) ? $_POST['item_title'] : '' ?>"
                               class="input-sm" type="text" name="item_title" id="item_title" autofocus>
                    </td>
                    <td>
                        <input type="submit" name="search_prod" class="btn btn-primary" value="Search"/>
                    </td>
                </tr>
            </table>


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

        <?php
        if (isset($_POST['item_language']) && empty($product_array)) {
            echo "No items matching the filter criteria";
        } else {


            echo '<div id="product-grid">';
            echo '<div class="txt-heading">Search Result</div>';
            foreach ($product_array as $key => $value) {
                ?>


                <div class="product-item">
                    <form method="post" onsubmit="return confirm('Are you sure you want to issue this item?');" action="issue_item3.php" name="issue_item" id="issue_item">
                        <div class="product-image"><img class="img_vrs"
                                                        src="<?php echo $product_array[$key]["cover_image"]; ?>"></div>
                        <div><strong><?php echo $product_array[$key]["title"]; ?></strong></div>
                        <div class="product-price"><?php echo "Rs." . $product_array[$key]["daily_rent"]; ?></div>
                        <div><input type="submit" name="issue_item" value="Issue" class="btnAddAction"/></div>
                        <input type="hidden" name="member_id" id="member_id" value="<?php echo $member_id ?>"/>
                        <input type="hidden" name="item_id" id="item_id"
                               value="<?php echo $product_array[$key]["item_id"] ?>"/>
                    </form>
                </div>
                <?php
            }
        }
        ?>
        </div>
        <?php
    }
}
	require('include/page_footer.php');
?>