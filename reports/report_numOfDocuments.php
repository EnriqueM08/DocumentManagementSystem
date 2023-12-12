<?php
    include("functions.php");
    include("reportFunctions.php");
    echo "<h1> Report of Number of Documents for 11/1/2023 12 AM to 11/19/2023 11:59 PM</h1>";
    $dblink = connectToDatabase();
    $dblink -> select_db("docstorage");
    $totalNumOfFiles = getTotalNumOfFiles($dblink);
    if($totalNumOfFiles === "0") {
        echo '<p>NO FILES DURING THIS TIME PERIOD!</p>';
    }
    else {
        echo '<h2>TOTAL NUMBER OF DOCUMENTS: '.$totalNumOfFiles.'</h2>';
        $totalNumOfLoans = getTotalUniqueLoans($dblink); 
        if($totalNumOfLoans === "0") {
            echo '<p>NO LOANS FOUND DURING THIS TIME PERIOD!</p>';
	}
	else {
	    echo '<h3>AVERAGE DOCUMENTS PER LOAN: ';
	    echo getAverageNumOfFiles($dblink). '</h3>';
	}
    }
?>
