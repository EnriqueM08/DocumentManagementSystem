<?php
    function getTotalUniqueLoans($dblink) {
        $sql=("SELECT COUNT(DISTINCT client_id) AS 'UNIQUE_IDS' FROM files WHERE file_datetime > \"2023-11-01 00:00:00\" AND file_datetime < \"2023-11-19 11:59:59\" AND client_id IN (SELECT DISTINCT client_id from files WHERE fileUploader = \"rre165\")");
        $result = $dblink->query($sql);
        if(!$result) {
            $errorfile = fopen("/var/searchErrors/searchErrors.txt", "w");
            fwrite($errorfile, "Something went wrong with $sql\n".$dblink->error);
            fclose($errorfile);
            die("<p>Something went wrong with $sql</p>".$dblink->error. "<br><p>Please refresh and try again!</p>");
        }
        $data = $result->fetch_array(MYSQLI_ASSOC);
        return $data['UNIQUE_IDS'];
    }

    function getListOfUniqueLoans($dblink) {
        $sql=("SELECT DISTINCT client_id FROM files WHERE file_datetime > \"2023-11-01 00:00:00\" AND file_datetime < \"2023-11-19 11:59:59\" AND client_id IN (SELECT DISTINCT client_id from files WHERE fileUploader = \"rre165\")");
        $result = $dblink->query($sql);
        if(!$result) {
            $errorfile = fopen("/var/searchErrors/searchErrors.txt", "w");
            fwrite($errorfile, "Something went wrong with $sql\n".$dblink->error);
            fclose($errorfile);
            die("<p>Something went wrong with $sql</p>".$dblink->error. "<br><p>Please refresh and try again!</p>");
	}
        return $result;
    }

    function getTotalFileSize($dblink) {
	$sql=("SELECT SUM(file_size) AS totalFileSize FROM files WHERE file_datetime > \"2023-11-01 00:00:00\" AND file_datetime < \"2023-11-19 11:59:59\" AND fileUploader = \"rre165\"");
        $result = $dblink->query($sql);
        if(!$result) {
            $errorfile = fopen("/var/searchErrors/searchErrors.txt", "w");
            fwrite($errorfile, "Something went wrong with $sql\n".$dblink->error);
            fclose($errorfile);
            die("<p>Something went wrong with $sql</p>".$dblink->error. "<br><p>Please refresh and try again!</p>");
	}
	$data = $result->fetch_array(MYSQLI_ASSOC);
	return $data['totalFileSize'];
    }

    function getTotalNumOfFiles($dblink) {
        $sql=("SELECT COUNT(*) AS numFiles FROM files WHERE file_datetime > \"2023-11-01 00:00:00\" AND file_datetime < \"2023-11-19 11:59:59\" AND fileUploader = \"rre165\"");
        $result = $dblink->query($sql);
        if(!$result) {
            $errorfile = fopen("/var/searchErrors/searchErrors.txt", "w");
            fwrite($errorfile, "Something went wrong with $sql\n".$dblink->error);
            fclose($errorfile);
            die("<p>Something went wrong with $sql</p>".$dblink->error. "<br><p>Please refresh and try again!</p>");
        }
	$data = $result->fetch_array(MYSQLI_ASSOC);
	return $data['numFiles'];
    }

    function getAverageSizeOfAll($dblink) {
	$sum = getTotalFileSize($dblink);
        $numOfFiles = getTotalNumOfFiles($dblink);
	if($sum === 0 || $numOfFiles === 0)
	    return 0;
        else
	    return round($sum / $numOfFiles);
    }

    function getAverageNumOfFiles($dblink) {
        $totalNumOfFiles = getTotalNumOfFiles($dblink);
	$totalNumOfLoans = getTotalUniqueLoans($dblink);
	if($totalNumOfFiles === 0 || $totalNumOfLoans === 0)
	    return 0;
	else
            return round($totalNumOfFiles / $totalNumOfLoans);
    }

    function getNumLoansForID($dblink, $loanID) {
        $sql=("SELECT COUNT(*) AS numFiles FROM files WHERE file_datetime > \"2023-11-01 00:00:00\" AND file_datetime < \"2023-11-19 11:59:59\" AND fileUploader = \"rre165\" AND client_id = \"$loanID\"");
        $result = $dblink->query($sql);
        if(!$result) {
            $errorfile = fopen("/var/searchErrors/searchErrors.txt", "w");
            fwrite($errorfile, "Something went wrong with $sql\n".$dblink->error);
            fclose($errorfile);
            die("<p>Something went wrong with $sql</p>".$dblink->error. "<br><p>Please refresh and try again!</p>");
        }
        $data = $result->fetch_array(MYSQLI_ASSOC);
        return $data['numFiles'];
    }

    function getSumFileSizeForID($dblink, $loanID) {
        $sql=("SELECT SUM(file_size) AS loanFileSize FROM files WHERE file_datetime > \"2023-11-01 00:00:00\" AND file_datetime < \"2023-11-19 11:59:59\" AND fileUploader = \"rre165\" AND client_id = \"$loanID\"");
        $result = $dblink->query($sql);
        if(!$result) {
            $errorfile = fopen("/var/searchErrors/searchErrors.txt", "w");
            fwrite($errorfile, "Something went wrong with $sql\n".$dblink->error);
            fclose($errorfile);
            die("<p>Something went wrong with $sql</p>".$dblink->error. "<br><p>Please refresh and try again!</p>");
        }
        $data = $result->fetch_array(MYSQLI_ASSOC);
        return $data['loanFileSize'];
    }

    function getAvgFileSizeForID($dblink, $loanID) {
	$fileSizeSum = getSumFileSizeForID($dblink, $loanID);
	$totalFilesForLoan = getNumLoansForID($dblink, $loanID);
	if($fileSizeSum === 0 || $totalFilesForLoan === 0)
	    return 0;
	else
            return round($fileSizeSum / $totalFilesForLoan);
    }

    function getLoansForID($dblink, $loanID) {
        $sql=("SELECT COUNT(*) AS numFiles FROM files WHERE file_datetime > \"2023-11-01 00:00:00\" AND file_datetime < \"2023-11-19 11:59:59\" AND fileUploader = \"rre165\" AND client_id = \"$loanID\"");
        $result = $dblink->query($sql);
        if(!$result) {
            $errorfile = fopen("/var/searchErrors/searchErrors.txt", "w");
            fwrite($errorfile, "Something went wrong with $sql\n".$dblink->error);
            fclose($errorfile);
            die("<p>Something went wrong with $sql</p>".$dblink->error. "<br><p>Please refresh and try again!</p>");
        }
        $data = $result->fetch_array(MYSQLI_ASSOC);
        return $data['numFiles'];
    }

    function getIncompleteLoans($dblink) {
        $sql=("SELECT DISTINCT client_id FROM files WHERE file_datetime > \"2023-11-01 00:00:00\" AND file_datetime < \"2023-11-19 11:59:59\" AND fileUploader = \"rre165\" GROUP BY client_id HAVING COUNT(DISTINCT file_type) != 8");
        $result = $dblink->query($sql);
        if(!$result) {
            $errorfile = fopen("/var/searchErrors/searchErrors.txt", "w");
            fwrite($errorfile, "Something went wrong with $sql\n".$dblink->error);
            fclose($errorfile);
            die("<p>Something went wrong with $sql</p>".$dblink->error. "<br><p>Please refresh and try again!</p>");
        }
        return $result;
    }

    function getMissingFileTypes($dblink, $loanID) {
	$sql=("SELECT fileType FROM documentTypes WHERE fileType NOT IN (SELECT DISTINCT file_type from files WHERE file_datetime > \"2023-11-01 00:00:00\" AND file_datetime < \"2023-11-19 11:59:59\" AND fileUploader = \"rre165\" AND client_id = \"$loanID\")");
        $result = $dblink->query($sql);
        if(!$result) {
            $errorfile = fopen("/var/searchErrors/searchErrors.txt", "w");
            fwrite($errorfile, "Something went wrong with $sql\n".$dblink->error);
            fclose($errorfile);
            die("<p>Something went wrong with $sql</p>".$dblink->error. "<br><p>Please refresh and try again!</p>");
        }
        return $result;
    }

    function getCompleteLoans($dblink) {
        $sql=("SELECT DISTINCT client_id FROM files WHERE file_datetime > \"2023-11-01 00:00:00\" AND file_datetime < \"2023-11-19 11:59:59\" AND fileUploader = \"rre165\" GROUP BY client_id HAVING COUNT(DISTINCT file_type) = 8");
        $result = $dblink->query($sql);
        if(!$result) {
            $errorfile = fopen("/var/searchErrors/searchErrors.txt", "w");
            fwrite($errorfile, "Something went wrong with $sql\n".$dblink->error);
            fclose($errorfile);
            die("<p>Something went wrong with $sql</p>".$dblink->error. "<br><p>Please refresh and try again!</p>");
        }
        return $result;
    }

    function getEmptyLoans($dblink) {
        $sql=("SELECT clients.client_id FROM clients LEFT JOIN files ON clients.client_id = files.client_id WHERE files.client_id IS NULL AND file_datetime > \"2023-11-01 00:00:00\" AND file_datetime < \"2023-11-19 11:59:59\" AND fileUploader = \"rre165\"");
        $result = $dblink->query($sql);
        if(!$result) {
            $errorfile = fopen("/var/searchErrors/searchErrors.txt", "w");
            fwrite($errorfile, "Something went wrong with $sql\n".$dblink->error);
            fclose($errorfile);
            die("<p>Something went wrong with $sql</p>".$dblink->error. "<br><p>Please refresh and try again!</p>");
        }
        return $result;
    }

    function getNumOfFilesByType($dblink, $fileType) {
	$sql=("SELECT COUNT(*) AS count FROM files WHERE file_type = \"$fileType\" AND file_datetime > \"2023-11-01 00:00:00\" AND file_datetime < \"2023-11-19 11:59:59\" AND fileUploader = \"rre165\"");
        $result = $dblink->query($sql);
        if(!$result) {
            $errorfile = fopen("/var/searchErrors/searchErrors.txt", "w");
            fwrite($errorfile, "Something went wrong with $sql\n".$dblink->error);
            fclose($errorfile);
            die("<p>Something went wrong with $sql</p>".$dblink->error. "<br><p>Please refresh and try again!</p>");
	}
	$data = $result->fetch_array(MYSQLI_ASSOC);
        return $data['count'];
    }

    function getFileTypes($dblink) {
        $sql=("SELECT fileType from documentTypes");
        $result = $dblink->query($sql);
        if(!$result) {
            $errorfile = fopen("/var/searchErrors/searchErrors.txt", "w");
            fwrite($errorfile, "Something went wrong with $sql\n".$dblink->error);
            fclose($errorfile);
            die("<p>Something went wrong with $sql</p>".$dblink->error. "<br><p>Please refresh and try again!</p>");
        }
        return $result;
    }
?>
