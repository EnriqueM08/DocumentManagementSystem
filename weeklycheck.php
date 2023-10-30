<?php
    require_once('functions.php');
    set_time_limit(300);

    $username="";
    $password="";

    $conn = connectToDatabase();
    $sid = createSession($username, $password, $conn, 0.0);
    getDocuments($sid, $username, $conn);
    $filesString = requestAllDocuments($sid, $username);
    preg_match_all('/([A-Za-z0-9]+(-[A-Za-z0-9]+)+)_\d\d_\d\d_\d\d\.pdf/', $filesString, $fileIDs);
    $totalCount = saveMasterRecord($sid, $username, $fileIDs, $conn);
    $unsavedCount = findUnsavedFiles($conn, $sid, $username);
    $unsavedCount += findDuplicates($conn, $sid, $username);
    if($unsavedCount == 0) {
        echo "\r\nALL FILES SAVED CORRECTLY!\r\n";
    }
    else {
        echo "\r\nFILES MISSING SHOULD BE ADDED NOW!\r\n";
    }
    #checkAllLoanIDs($conn, "49d7b3c7906657f414733c285fcea4933797a4fe", $username);
    closeSession($sid, $username, $conn);
    $conn->close();
?>
