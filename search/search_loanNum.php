<!Doctype html>
<html>
    <head>
        <meta charset="utf-8">
        <title>Search by Loan Number</title>
        <!-- BOOTSTRAP STYLES-->
        <link href="assets/css/bootstrap.css" rel="stylesheet">
        <!-- FONTAWESOME STYLES-->
        <link href="assets/css/font-awesome.css" rel="stylesheet">
        <!--CUSTOM BASIC STYLES-->
        <link href="assets/css/basic.css" rel="stylesheet">
        <!--CUSTOM MAIN STYLES-->
        <link href="assets/css/custom.css" rel="stylesheet">
        <!-- PAGE LEVEL STYLES -->
        <link href="assets/css/bootstrap-fileupload.min.css" rel="stylesheet">
        <!-- PAGE LEVEL STYLES -->
        <link href="assets/css/prettyPhoto.css" rel="stylesheet">
        <link rel="stylesheet" type="text/css" href="assets/css/print.css" media="print">
        <!--[if lt IE 9]><script src="scripts/flashcanvas.js"></script><![endif]-->
        <!-- JQUERY SCRIPTS -->
        <script src="assets/js/jquery-1.10.2.js"></script>
        <!-- BOOTSTRAP SCRIPTS -->
        <script src="assets/js/bootstrap.js"></script>
        <!-- METISMENU SCRIPTS -->
        <script src="assets/js/jquery.metisMenu.js"></script>
        <!-- CUSTOM SCRIPTS <script src="assets/js/custom.js"></script>-->
        <script src="assets/js/bootstrap-fileupload.js"></script>

        <script src="assets/js/jquery.prettyPhoto.js"></script>
        <script src="assets/js/galleryCustom.js"></script>
        <style>
            .default {background-color:#E1E1E1;}
        </style>
        <script>
            function addFocus(div){
                document.getElementById(div).classList.remove("default");
            }
            function removeFocus(div){
                document.getElementById(div).classList.add("default");
            }
        </script>
    </head>
    <body>
        <?php
        include("functions.php");
	echo '<div id="page-inner">';
	    echo '<h1 class="page-head-line">Select the search criteria</h1>';	
	echo '<div class="panel-body">';
	if (isset($_REQUEST['msg']) && ($_REQUEST['msg'] == "NotFound")) 
        {
            echo '<div class="alert alert-danger alert-dismissable">';
	    echo '<button type="button" class="close" data-dismiss="alert" aria-hidden="true">x</button>';
	    $loanNumNotFound = $_REQUEST['loanNum'];
            echo "Loan Number: ".$loanNumNotFound. " was not found in the system!</div>";
	}
	if(isset($_REQUEST['foundLoanNum'])) {
		$dblink = connectToDatabase();
                $dblink -> select_db("docstorage");
		$loanNum = $_REQUEST['foundLoanNum'];
		echo '<h2>Results for loan number: '.$loanNum.'</h2>';
                echo '<table class = "table">';
                echo '<thead><tr><td>File Name</td><td>File Type</td><td>File Upload Date</td><td>Action</td></tr></thead>';
                $sql=("SELECT * from files where client_id = '$loanNum'");
                $result = $dblink->query($sql);
		if(!$result) {
                	$errorfile = fopen("/var/searchErrors/searchErrors.txt", "w");
                	fwrite($errorfile, "Something went wrong with $sql\n".$dblink->error);
                	fclose($errorfile);
                	die("<p>Something went wrong with $sql<p>".$dblink->error);
		}
		if($result->num_rows<=0) {
                    echo '</table>';
                    echo '<h3>No Files for this loan!<h3>';
		}
		else {
		    while($data=$result->fetch_array(MYSQLI_ASSOC))
                    {
                        echo '<tr>';
                        echo '<td>'.$data['file_name'].'</td>';
                        echo '<td>'.$data['file_type'].'</td>';
                        echo '<td>'.$data['file_datetime'].'</td>';
		        echo '<td><a href="https://ec2-18-222-116-88.us-east-2.compute.amazonaws.com/view_file.php?fname='.$data['file_name'].'" target="_blank">View File</a></td>';
		        echo '</tr>';
                    }
		    echo '</table>';
		}
	}
	else if(!isset($_POST['submit']))
	{
	        echo '<form method="post" action="">';
	            echo '<div class="form-group">';
                        echo '<label for="loanNum" class="control-label">Loan Number</label>';
                        echo '<input type="text" class="form-control" name="loanNum">';
	            echo '</div>';
	            echo '<button name="submit" type="submit" class ="btn btn-primary" value="submit">Search</button>';
	        echo '</form>';
	}
	elseif(isset($_POST['submit']) && $_POST['submit']=="submit")
	{
            $loanNum =$_POST['loanNum'];
            $dblink = connectToDatabase();
            $dblink -> select_db("docstorage");
            $sql= "Select * from clients where client_id = '$loanNum'";
            $result = $dblink->query($sql);
            if(!$result) {
                $errorfile = fopen("/var/searchErrors/searchErrors.txt", "w");
                fwrite($errorfile, "Something went wrong with $sql\n".$dblink->error);
                fclose($errorfile);
                die("<p>Something went wrong with $sql<p>".$dblink->error);
            }
            if($result->num_rows<=0)
            {
                redirect("https://ec2-18-222-116-88.us-east-2.compute.amazonaws.com/search_loanNum.php?msg=NotFound&loanNum=".$loanNum);
            }
	    else
	    {
		redirect(redirect("https://ec2-18-222-116-88.us-east-2.compute.amazonaws.com/search_loanNum.php?foundLoanNum=".$loanNum));
	    }
	} 
	echo '</div>';
	echo '</div>';
	?>
    </body>
</html>
