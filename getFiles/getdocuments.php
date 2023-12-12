<?php
    set_time_limit(1200);
    if(empty($_SERVER['REMOTE_ADDR']))
    {
	require_once('functions.php');
        $username="rre165";
        $password="rc7jBd3LntQHZq8K";

        $conn = connectToDatabase();
        $sid = createSession($username, $password, $conn, 0.0);
	$execTime = getDocuments($sid, $username, $conn);
	$execTime += closeSession($sid, $username, $conn);
        calculateSessionTime($sid, $conn, $execTime);
        $conn->close();
    }
?>
