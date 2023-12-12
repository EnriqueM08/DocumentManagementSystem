<?php
    include("functions.php");
    include("reportFunctions.php");
    echo "<h1> Report of unique loans for 11/1/2023 12 AM to 11/19/2023 11:59 PM</h1>";
    $dblink = connectToDatabase();
    $dblink -> select_db("docstorage");
    $uniqueIDS = getTotalUniqueLoans($dblink);
    if($uniqueIDS === "0") {
        echo '<p>NO FILES DURING THIS TIME PERIOD!</p>';
    }
    else {
	echo '<h2>TOTAL UNIQUE LOANS: '.$uniqueIDS.'</h2>';
        echo '<h2>Loan Numbers</h2>';
    	$result = getListOfUniqueLoans($dblink); 
    	if($result->num_rows<=0) { 
            echo '<h3>No Loans in the system!<h3>';
    	}
    	else {
            while($data=$result->fetch_array(MYSQLI_ASSOC))
            {
		echo "[".$data['client_id']."]  ";
            }
    	}
    } 
?>
