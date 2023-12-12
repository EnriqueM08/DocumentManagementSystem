<?php
    include("functions.php");
    include("reportFunctions.php");
    echo "<h1> Number of Files Per Type for 11/1/2023 12 AM to 11/19/2023 11:59 PM</h1>";
    $dblink = connectToDatabase();
    $dblink -> select_db("docstorage");
    $fileTypeList = getFileTypes($dblink); 
    while($curFileType=$fileTypeList->fetch_array(MYSQLI_ASSOC)) {
        $countOfFiles = getNumOfFilesByType($dblink, $curFileType['fileType']);
        echo "There are ".$countOfFiles. " files of the type ".$curFileType['fileType'];
        echo "<br>";
    }
