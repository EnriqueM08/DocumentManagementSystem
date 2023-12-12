<?php
    include("functions.php");
    include("reportFunctions.php");
    echo "<h1> Complete Loan Report Information for 11/1/2023 12 AM to 11/19/2023 11:59 PM</h1>";
    $dblink = connectToDatabase();
    $dblink -> select_db("docstorage");
    $incompleteLoanList = getIncompleteLoans($dblink);
    echo "<h2> Incomplete loans: </h2>";
    while($curLoan=$incompleteLoanList->fetch_array(MYSQLI_ASSOC)) {
	echo "Loan: ".$curLoan['client_id']. "<br> Missing types: ";
        $missingFileTypes = getMissingFileTypes($dblink, $curLoan['client_id']);
	while($curFileType = $missingFileTypes->fetch_array(MYSQLI_ASSOC)) {
	    echo $curFileType['fileType']. " ";
        }
	echo "<br><br>";
    }
    echo "<h2> Complete loans: </h2>";
    $completeLoanList = getCompleteLoans($dblink);
    while($curLoan=$completeLoanList->fetch_array(MYSQLI_ASSOC)) {
        echo "Loan: ".$curLoan['client_id']. " is complete!<br>";
    }
    echo "<h2> Empty loans: </h2>";
    $emptyLoanList = getEmptyLoans($dblink);
    if(!$data=$emptyLoanList->fetch_array(MYSQLI_ASSOC))
    {
	echo "No empty loans in the time range!";
    }
    else
    {
        echo "Loan: ".$data['client_id']." is empty!";
	while ($data=$emptyLoanList->fetch_array(MYSQLI_ASSOC))
            echo "Loan: ".$data['client_id']." is empty!";
    } 
?>
