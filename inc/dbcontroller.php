<?php
class DBController {
	private $host = "localhost";
	private $user = "root";
	private $password = "abcd";
	private $database = "vrs";
	private $conn;
	
	function __construct() {
		$this->conn = $this->connectDB();
	}
	
	function connectDB() {
		$conn = mysqli_connect($this->host,$this->user,$this->password,$this->database);
		return $conn;
	}
	
	function runQuery($query) {
		$result = mysqli_query($this->conn,$query);
		while($row=mysqli_fetch_assoc($result)) {
			$resultset[] = $row;
		}		
		if(!empty($resultset))
			return $resultset;
	}

    function numRows($query) {
        $result  = $this->conn->query($query);
        $rowcount = $result->num_rows;
        return $rowcount;
    }

    function updateQuery($query) {
        $result = $this->conn->query($query);
        if (!$result) {
            die('Invalid query');
        } else {
            return $result;
        }
    }

    function insertQuery($query) {
        $result = $this->conn->query($query);
        if (!$result) {
            die('Invalid query' );
        } else {
            return $this->conn->insert_id;
        }
    }

    function deleteQuery($query) {
        $result = $this->conn->query($query);
        if (!$result) {
            die('Invalid query' );
        } else {
            return $result;
        }
    }
}
?>