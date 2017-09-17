<?php
require('inc/config.php');
require('inc/functions.php');
require('include/page_header.php');
if($_SESSION['role'] == 'member' || $_SESSION['role'] == 'C') {
	echo "<br/><br/><h3 style='color:red'>Unauthorized access</h3>";
}
else {
    if (add_item($conn)) {
        echo "<h4 style='color:blue'>Details are added successfully</h4>";
        echo "<table style='padding:5px'>";
        echo "<tr>";
        echo '<td valign="top" width="250px" align="center" rowspan = 6>';
        echo '<img class="single_img_vrs" src="' . $_SESSION['cover_image'] . '"/>';
        echo '</td>';
        echo "<td style='padding:5px'>Title: </td><td style='padding:5px'>" . $_POST['item_title'] . "</td>";

        echo "<tr><td style='padding:5px'>Description: </td><td style='padding:5px'> " . $_POST['desc'] . "</td>";
        echo "<tr><td style='padding:5px'>Daily Rent: </td><td style='padding:5px'> " . $_POST['daily_rent'] . "</td>";
        echo "<tr><td style='padding:5px'>Language: </td><td style='padding:5px'>" . $_POST['item_language'] . "</td>";
        echo "<tr><td style='padding:5px'>Genre: </td><td style='padding:5px'> " . $_POST['genre'] . "</td>";
        echo "<tr><td style='padding:5px'>No of copies: </td><td style='padding:5px'> " . $_POST['no_of_copies'] . "</td>";


        echo "</table>";
        echo '<br/>	<a href="manage_items.php" class="btn btn-primary" >&nbsp; OK &nbsp;</a>';

        $_POST['item_title'] = '';
        $_POST['desc'] = '';
        $_POST['price'] = '';
        $_POST['daily_rent'] = 0;
        $_POST['no_of_copies'] = 0;
        unset($_SESSION['cover_image']);
    } else {
        ?>
        <!-- HTML Form -->
        <form enctype="multipart/form-data" action="<?php $_PHP_SELF ?>" method="post" name="register_member"
              id="regiser_member" autocomplete="off">


            <h2 class="form-signin-heading" style="text-align:center; width:500px;">Add a new CD / DVD</h2>

            <!-- Modal Body -->
            <div class="modal-body">
                <table>
                    <tr>
                        <td>
                            <div class="form-group">
                                <label for="item_title" class="reg_label">Title</label>
                                <input value="<?php echo isset($_POST['item_title']) ? $_POST['item_title'] : '' ?>"
                                       class="input-sm" type="text" name="item_title" id="item_title" required pattern="^.{3,30}$"
                                       title="min 3, max 30 characters." autofocus>
                            </div>
                        </td>
                        <td>
                            <div class="form-group">
                                <label for="item_language" class="reg_label">Language</label>
                                <select name="item_language" id="item_language" class='input-sm'>
                                    <option value="Hindi">Hindi</option>
                                    <option value="English">English</option>
                                    <option value="Bengali">Bengali</option>
                                    <option value="Tamil">Tamil</option>
                                    <option value="Marathi">Marathi</option>
                                </select>
                            </div>

                        </td>
                    </tr>

                    <tr>
                        <td>
                            <div class="form-group">
                                <label class="reg_label">Type</label>
                                <input type = "radio" name = "ItemType" id = "video" value = "V" checked = "checked" onchange = "document.getElementById('item_type').value=this.value"/>
                                <label for = "video"> Video &nbsp;&nbsp; </label>
                                <input type = "radio" name = "ItemType" id = "music" value = "M" onchange = "document.getElementById('item_type').value=this.value" />
                                <label for = "music"> Music &nbsp;&nbsp; </label>

                                <input type="hidden" id="item_type" name="item_type" value="V"/>
                            </div>

                        </td>
                        <td>
                            <div class="form-group">
                                <label for="genre" class="reg_label">Genre</label>
                                <select name="genre" id="genre" class='input-sm'>
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

                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <div class="form-group">
                                <label for="price" class="reg_label">Price Rs.</label>
                                <input value="<?php echo isset($_POST['price']) ? $_POST['price'] : '' ?>"
                                       class="input-sm" type="text" name="price" id="price" required pattern="^[1-9][0-9]*$" title="only digits">
                            </div>
                        </td>
                        <td>
                            <div class="form-group">
                                <label for="cover_image" class="reg_label">Cover Image</label>
                                <input style="display:inline;"
                                       value="<?php echo isset($_POST['cover_image']) ? $_POST['cover_image'] : '' ?>"
                                       type="file" name="cover_image" id="cover_image">
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <div class="form-group" >
                                <label for="daily_rent" class="reg_label">Daily Rent Rs.</label>
                                <input type="range" name="daily_rent" id="daily_rent"
                                       value="<?php echo isset($_POST['daily_rent']) ? $_POST['daily_rent'] : 10 ?>"
                                       onchange="document.getElementById('rentText').innerHTML = ' &nbsp;&nbsp; <b>' + this.value; + '</b>'"
                                       min="5" max="100" step="5" style="width:140px; display:inline;">
                                <span id="rentText" style="color:blue;"> &nbsp;&nbsp; <b>10</b></span>
                            </div>
                        </td>
                        <td>
                            <div class="form-group">

                                <label for="no_of_copies" class="reg_label">No of copies</label>
                                <input type="range" name="no_of_copies" id="no_of_copies"
                                       value="<?php echo isset($_POST['no_of_copies']) ? $_POST['no_of_copies'] : 1 ?>"
                                       onchange="document.getElementById('copiesText').innerHTML = ' &nbsp;&nbsp; <b>' + this.value; + '</b>'"
                                       min="1" max="50" style="width:140px; display:inline;">
                                <span id="copiesText" style="color:blue;"> &nbsp;&nbsp; <b>1</b></span>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            <div class="form-group">
                                <label for="desc" class="reg_label">Description</label>
                                <textarea name="desc" id="desc" required rows="5" cols="60"><?php echo isset($_POST['desc']) ? $_POST['desc'] : '' ?></textarea>
                            </div>
                        </td>
                    </tr>
                </table>



                <div class="alert fade in" style="text-align:center; width:400px; color:red">
                    <?php
                    if (isset($_SESSION['error'])) {
                        echo $_SESSION['error'];
                        unset($_SESSION['error']);
                    }
                    ?>
                </div>
                <div class="form-group">
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                    <input type="submit" name="add_item" class="btn btn-lg btn-success" value="Add Details"
                           id="add_item"/>
                    &nbsp;&nbsp;&nbsp;&nbsp;
                    <a href="manage_items.php" class="btn  btn-lg btn-default">Cancel</a>
                </div>
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


        <?php
    }
}
 require('include/page_footer.php');
?>

