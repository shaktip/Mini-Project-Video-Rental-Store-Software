<?php
function get_store_details($conn, $store_id){
    if ($store_id != null)
        $condition = " AND store_id = $store_id";
    else
        $condition = " AND store_id IN (SELECT store_id FROM store WHERE status='A') OR store_id IS NULL ";

    $rows = array();
    $res = $conn->query("SELECT role, count(*) FROM employee WHERE status='A' " . $condition . " GROUP BY role");

    while($row = $res->fetch_array()){
        $rows[$row["role"]] = $row;
    }

    return $rows;
}