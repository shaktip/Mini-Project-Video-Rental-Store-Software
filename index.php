<?php
require('include/header.php');
require('inc/config.php');
require('inc/functions.php');
require('inc/dbcontroller.php');

$db_handle = new DBController();

$sql = "SELECT * FROM item_master WHERE status='A'";
if(isset($_POST) && !empty($_POST["search_prod"])){
    $search_message = "All ";
    $item_language = strtoupper(safe_input($conn, $_POST['item_language']));
    $genre = strtoupper(safe_input($conn, $_POST['genre']));
    $item_type = strtoupper(safe_input($conn, $_POST['item_type']));
    $item_title = strtoupper(safe_input($conn, $_POST['item_title']));

    if($_POST["item_language"] != 'All') {
        $search_message .= $_POST["item_language"] . ", ";
        $sql .= " AND upper(language) = '" . $item_language . "'";
    }
    if($_POST["genre"] != 'All') {
        $search_message .= $_POST["genre"] . ", ";
        $sql .= " AND upper(genre) = '" . $genre . "'";
    }
    if($_POST["item_type"] != 'All') {
        $search_message .= $_POST["item_type"] . ", ";

        $sql .= " AND upper(item_type) = '" . $item_type[0] . "'";
    }

    if($search_message != "All ")
        $search_message = substr($search_message, 0, strlen($search_message) - 2);

    $search_message .= ' CD/DVDs';
    if($_POST["item_title"]!= '') {
        $search_message .= ' with title / description containing: \'' . $_POST["item_title"] . "'";
        $sql .= " AND (upper(title) LIKE '%" . $item_title . "%' OR upper(descr) LIKE '%" . $item_title . "%') ";
    }
}
if(isset($_POST) && !empty($_POST["reset_filters"])) {
    unset($_POST);
}
$sql .= " ORDER BY date_of_procurement DESC ";
$product_array = $db_handle->runQuery($sql);

?>

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
                    <label for="item_type" >Type</label>
                    <select name="item_type" id="item_type" class='input-sm'>
                        <option value="All">All</option>
                        <option value="Video">Video</option>
                        <option value="Music">Music</option>

                    </select>
                </td>
                <td>
                    <label for="item_title">Title/Description</label>
                    <input class='input-sm'  value="<?php echo isset($_POST['item_title']) ? $_POST['item_title'] : '' ?>"
                           type="text" name="item_title" id="item_title" autofocus>
                </td>
                <td>
                    <input type="submit" name="search_prod" class="btn btn-primary" value="Search"/>
                    <a name="reset_filters" class="btn btn-primary" href="index.php"> Reset </a>
                </td>
            </tr>
        </table>


        <?php
        $val = isset($_POST['item_language']) ? $_POST['item_language'] : '';
        echo "<input type='hidden' id='item_lang' value='" . $val . "'/>";

        $val = isset($_POST['genre']) ? $_POST['genre'] : '';
        echo "<input type='hidden' id='item_gen' value='" . $val . "'/>";

        $val = isset($_POST['item_type']) ? $_POST['item_type'] : '';
        echo "<input type='hidden' id='item_ty' value='" . $val . "'/>";
        ?>
    </form>


    <script>
        function fn_set_del_address(frm) {
            addr = document.getElementById('del_address').value;
            if(addr == ''){
                alert("Enter delivery address for the order");
                return false;
            }
            frm.action = "order_item2.php?del_address="+addr;
            return true;
        }
        var item_lang = document.getElementById('item_lang').value;
        var item_gen = document.getElementById('item_gen').value;
        var item_ty = document.getElementById('item_ty').value;

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

        var typ = document.getElementById('item_type');
        for (var i = 0; i < typ.options.length; i++) {
            if (typ.options[i].text === item_ty) {
                typ.selectedIndex = i;
                break;
            }
        }
    </script>

    <?php
    if(!empty($_POST)){
        echo '<div class="txt-heading"><table width="100%"><tr><td>'. $search_message;
        if(count($product_array)) {
            echo '</td><td align="right">Showing top ' . count($product_array) . ' recent items';
        }
        echo "</td></tr></table></div>";

    }
    if (isset($_POST['item_language']) && empty($product_array)) {

        echo "No items matching the filter criteria";
    } else {


        echo '<div id="product-grid">';

        foreach ($product_array as $key => $value) {
            ?>


            <div class="product-item">

                    <div class="product-image"><img title="<?php echo $product_array[$key]["descr"]; ?>" class="img_vrs"
                                                    src="<?php echo $product_array[$key]["cover_image"]; ?>">
                    </div>
                    <div><strong><?php echo $product_array[$key]["title"] . "</strong><br/>" ;
                        echo  $product_array[$key]["language"] . ", " . $product_array[$key]["genre"]. " (" . $product_array[$key]["item_type"] . ")";
                    ?>
                            <br/>
                    <span class="product-price">Rent: Rs.<?php echo $product_array[$key]["daily_rent"]; ?> per day
                    <br/>
                    </span>
                    </div>

            </div>
            <?php
        }
    }
    ?>
    </div>
    <?php
}
require('include/footer.php');
?>