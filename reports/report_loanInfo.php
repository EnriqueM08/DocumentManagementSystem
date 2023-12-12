<?php
    include("functions.php");
    include("reportFunctions.php");
    echo "<h1> Detailed Loan Report Information for 11/1/2023 12 AM to 11/19/2023 11:59 PM</h1>";
    $dblink = connectToDatabase();
    $dblink -> select_db("docstorage");
    $loanList = getListOfUniqueLoans($dblink);
    if($loanList->num_rows<=0) {
        echo '<h3>No Loans in the system for givenctime period!<h3>';
    }
    else {
	$totalAvg = getAverageNumOfFiles($dblink);
        $totalAvgSize = getAverageSizeOfAll($dblink);
	echo '<h3>Total Average number of files per loan = '.$totalAvg.'</h3>';
	echo '<h3>Total Average size of files per loan = '.$totalAvgSize.'</h3>';
        while($curLoan=$loanList->fetch_array(MYSQLI_ASSOC))
        {
	    echo "Loan ID: ".$curLoan['client_id']."<br>";
            $numFiles = getNumLoansForID($dblink, $curLoan['client_id']);
	    echo "Total Number of Documents Recieved: ".$numFiles."<br>";
	    $avgFileSize = getAvgFileSizeForID($dblink, $curLoan['client_id']);
	    echo "Average Size of File for loan: ".$avgFileSize;
	    
	    if($avgFileSize < $totalAvgSize){
                echo ", Size of files is below average!<br>";
            }
            else if($avgFileSize > $totalAvgSize) {
                echo ", Size of files is above average!<br>";
            }
            else
		echo ", Size of files is average!<br>";

	    if($numFiles < $totalAvg){
	        echo " Num of files is below average!<br>";
	    }
	    else if($numFiles > $totalAvg) {
	        echo " Num of files is above average!<br>";
	    }
	    else
		echo " Num of files is average!<br>";
	    echo "<br>";
	}
    }
?>
