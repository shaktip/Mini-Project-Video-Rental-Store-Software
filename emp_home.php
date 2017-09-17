<?php
if(!session_id()) session_start();
require('inc/config.php');
require('inc/functions.php');
require('include/page_header.php');
if($_SESSION['role'] == 'member' ) {
    echo "<br/><br/><h3 style='color:red'>Unauthorized access</h3>";
    require('include/page_footer.php');
    exit();
}

$emp_id = $_SESSION['logged_in_id'];
$res = get_rows($conn, "employee", "*", "empid=$emp_id");
if(!$res){
    echo "<br/><br/><h3 style='color:red'>Some error occurred</h3>";
    require('include/page_footer.php');
    exit();
}
$emp_row = $res->fetch_array();
?>

<table width="100%">
    <tr>
        <td>
            <h2 class="form-signin-heading" style="text-align:center; width:700px;">My Profile</h2>
        </td>
        <td align="right">
            <br/>
            <a href="edit_emp.php" class="btn btn-info">Update Profile</a>
        </td>
    </tr>
</table>
<br/>
<?php

echo "<table class='table table-striped'>";
echo "<tr class='info'><th>Emp ID</th><th> Name</th><th>Mobile number</th><th>Address</th><th>Role</th></tr>";

echo "<tr class='success'><td>";
echo $emp_row[0] . "</td><td>  " . $emp_row[1] . " </td><td> " . $emp_row[2] . " </td><td> " . $emp_row[3] .
    " </td><td> " . ($emp_row[5]=='M'?'Manager':($emp_row[5]=='S'?'Super Admin':'Clerk Staff'));
echo "</td></tr></table>";

require('include/page_footer.php');
?>