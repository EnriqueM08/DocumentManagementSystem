<?php
    include("functions.php");
    include("reportFunctions.php");
    echo "<h1> Report of Document Sizes for 11/1/2023 12 AM to 11/19/2023 11:59 PM</h1>";
    $dblink = connectToDatabase();
    $dblink -> select_db("docstorage");
    $fileSize = getTotalFileSize($dblink);
    if($fileSize===0) {
        echo '<p>NO FILES DURING THIS TIME PERIOD!</p>';
    }
    else {
	echo '<h2>TOTAL SIZE OF ALL FILES: ';
	echo $fileSize. ' Bytes</h2>';
	echo '<h3>AVERAGE SIZE OF DOCUMENT ACROSS ALL LOANS: ';
	echo getAverageSizeOfAll($dblink). ' Bytes<h3>';
    }
?>
