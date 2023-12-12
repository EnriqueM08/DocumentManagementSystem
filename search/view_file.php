<?php
	include("functions.php");
	$dblink = connectToDatabase();
        $dblink -> select_db("docstorage"); 
	$fname = $_REQUEST['fname'];
	$sql = "Select file_location from files where file_name = '$fname'";
	$result = $dblink->query($sql);
	if(!$result) {
            $errorfile = fopen("/var/searchErrors/searchErrors.txt", "w");
            fwrite($errorfile, "Something went wrong with $sql\n".$dblink->error);
            fclose($errorfile);
            die("<p>Something went wrong with $sql<p>".$dblink->error);
        }
	$data = $result->fetch_array(MYSQLI_ASSOC);
	$fp = fopen($data['file_location'], "r");
	$tmp = fread($fp, filesize($data['file_location']));
	fclose($fp);
	header('Content-Type: application/pdf');
	header('Content-Length: '.strlen($tmp));
	header('Content-Disposition: inline; filename = \'$fname\'');
	echo $tmp;
?>
