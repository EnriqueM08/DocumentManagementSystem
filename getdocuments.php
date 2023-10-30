<?php
    require_once('functions.php');
    $username="";
    $password="";

    $conn = connectToDatabase();
    $sid = createSession($username, $password, $conn, 0.0);
    $execTime = getDocuments($sid, $username, $conn);
    $execTime += closeSession($sid, $username, $conn);
    calculateSessionTime($sid, $conn, $execTime);
    $conn->close();
?>
