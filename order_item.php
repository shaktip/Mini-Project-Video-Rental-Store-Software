<?php
require('inc/dbcontroller.php');
require('inc/functions.php');
require('include/page_header.php');
require('inc/config.php');
$db_handle = new DBController();

if($_SESSION['role'] == 'S' ) {
    echo "<br/><br/><h3 style='color:red'>Unauthorized access</h3>";
    exit();
}

if ($_SESSION['role'] == 'member')
    $member_id = $_SESSION['logged_in_id'];
elseif (isset($_GET['id']))
    $member_id = $_GET['id'];

if (!isset($member_id)) {
    echo '<div class="alert fade in" style="font-size: 20px; width:400px; color:red">';
    echo "Some error occurred... Try again... <br/><br/>";
    if ($_SESSION['role'] == 'member')
        echo "<a class='btn btn-primary' href='main_member.php'> OK </a>";
    else
        echo "<a class='btn btn-primary' href='main_emp.php'> OK </a>";
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
unset($_SESSION["eligible_for"]);

$sql = search_items($conn, 'O');
if($sql)
    $product_array = $db_handle->runQuery($sql);

?>
<table width="100%">
    <tr>
        <td width="200px">
            <h2 class="form-signin-heading">Order an Item</h2>
        </td>
        <td align="right">

            <table class='table-bordered'>

                <tr bgcolor="#f0ffff">
                    <td rowspan="2" align="center">&nbsp;&nbsp;<b>Mobile Number&nbsp;&nbsp;</b></td>
                    <td rowspan="2" align="center">&nbsp;&nbsp;<b>Delivery Address&nbsp;&nbsp;</b></td>
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
                echo $member_row[4] . "&nbsp; &nbsp;</td>";
                echo "<td align='center' valign='top'>&nbsp; &nbsp; <input class='input-sm' type='text' id='del_address' name='del_address' value='" . $member_row[3] . "' onchange='fn_set_del_address()'> &nbsp; &nbsp;</td>";
                echo "<td align='center'>&nbsp; &nbsp; " . ($_SESSION["eligible_for"][0][0] - $_SESSION["eligible_for"][0][1]) . " &nbsp; &nbsp;</td><td align='center'>&nbsp; &nbsp; " . $_SESSION["eligible_for"][0][1] . " &nbsp; &nbsp;</td><td align='center'>&nbsp; &nbsp; " . ($_SESSION["eligible_for"][1][0] - $_SESSION["eligible_for"][1][1]) . " &nbsp; &nbsp;</td><td align='center'>&nbsp; &nbsp; " . $_SESSION["eligible_for"][1][1];
                ?>
                &nbsp; &nbsp;</td></tr></table>
        </td>
    </tr>
</table>
<?php
if (!isset($_POST['item_language']) && empty($product_array)) {
    echo "No items matching the CD / DVDs you are eligible to place order for";
    unset($_SESSION['error']);
} else {
    ?>
    <br/>
    <form action="<?php $_PHP_SELF ?>" method="post" name="search_prod" id="search_prod" autocomplete="off">

        <table class="table table-striped " width="100%">
            <tr>
                <td>
                    <label for="item_language" >Language</label>
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
                    <input class='input-sm'  value="<?php echo isset($_POST['item_title']) ? $_POST['item_title'] : '' ?>"
                           type="text" name="item_title" id="item_title" autofocus>
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
        function fn_set_del_address(frm) {
            addr = document.getElementById('del_address').value;
            if(addr == ''){
                alert("Enter delivery address for the order");
                return false;
            }
            if(!confirm("Courier charges of Rs. 100 will be applicable for each order. \nAre you sure you want to proceed and place order for \"" + frm.title.value + "\"?"))
                return false;
            frm.action = "order_item2.php?del_address="+addr;
            return true;
        }
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

        echo '';
        echo '<div id="product-grid">';
        echo '<div class="txt-heading"><table width="100%"><tr><td>Search Result</td><td align="right"><span style="font-size:small; color: lightcyan; font-style: italic; " > Courier charges of Rs. 100 will be applicable for each order. </span></td></tr></table></div>';
        foreach ($product_array as $key => $value) {
            ?>

            <div class="product-item">
                <form method="post" onsubmit="fn_set_del_address(this)" name="order_item" id="order_item">
                    <div class="product-image"><img class="img_vrs"
                                                    src="<?php echo $product_array[$key]["cover_image"]; ?>">
                    </div>
                    <div ><strong><?php echo $product_array[$key]["title"]; ?></strong></div>
                    <div class="product-price"><?php echo "Rs." . $product_array[$key]["daily_rent"]; ?></div>
                    <div><input type="submit" name="order_item" value="Order" class="btnAddAction"/></div>
                    <input type="hidden" name="member_id" id="member_id" value="<?php echo $member_id ?>"/>
                    <input type="hidden" name="item_id" id="item_id"
                           value="<?php echo $product_array[$key]["item_id"] ?>"/>
                    <input type="hidden" id="title" name="title" value="<?php echo $product_array[$key]["title"]; ?>"/>
                </form>
            </div>
            <?php
        }
    }
    ?>
    </div>
    <?php
}


require('include/page_footer.php');
?>