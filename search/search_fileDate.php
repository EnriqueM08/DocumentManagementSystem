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
        if (isset($_REQUEST['msg']) && ($_REQUEST['msg'] == "NoFilesFound"))
        {
            echo '<div class="alert alert-danger alert-dismissable">';
            echo '<button type="button" class="close" data-dismiss="alert" aria-hidden="true">x</button>';
	    $startDate = $_REQUEST['startDate'];
	    $endDate = $_REQUEST['endDate'];
            echo "No Files found within date range: ".$startDate." - ". $endDate." were found!</div>";
        }
        if(isset($_REQUEST['foundStartDate']) && isset($_REQUEST['foundEndDate'])) {
                $dblink = connectToDatabase();
                $dblink -> select_db("docstorage");
		$startDate = $_REQUEST['foundStartDate'];
		$endDate = $_REQUEST['foundEndDate'];
                echo '<h2>Results for Date Range: '.$startDate.' - '.$endDate.'</h2>';
                echo '<table class = "table">';
                echo '<thead><tr><td>File Name</td><td>File Type</td><td>File Upload Date</td><td>Action</td></tr></thead>';
                $sql=("Select * from files where file_datetime BETWEEN'".$startDate."' AND '".$endDate."' ORDER BY files.file_datetime ASC");
		$result = $dblink->query($sql);
		if(!$result) {
                	$errorfile = fopen("/var/searchErrors/searchErrors.txt", "w");
                	fwrite($errorfile, "Something went wrong with $sql\n".$dblink->error);
                	fclose($errorfile);
                	die("<p>Something went wrong with $sql<p>".$dblink->error);
            	}
                while($data=$result->fetch_array(MYSQLI_ASSOC))
                {
                    echo '<tr>';
                    echo '<td>'.$data['file_name'].'</td>';
                    echo '<td>'.$data['file_type'].'</td>';
                    echo '<td>'.$data['file_datetime'].'</td>';
                    echo '<td><a href="https://ec2-18-222-116-88.us-east-2.compute.amazonaws.com/view_file.php?fname='.$data['file_name'].'" target="_blank">View File</a></td>';
                }
                echo '</table>';
        }
        else if(!isset($_POST['submit']))
        {
            echo '<form method="post" action="">';
                echo '<div class="form-group">';
	            echo '<label for="date" class="control-label">Select Start Date</label>';
		    date_default_timezone_set('America/Chicago');
                    $curDate = date('Y-m-d');
		    echo '<input type="date" class="form-control" name="date" value = "'.$curDate.'">';
		    echo '<label for="endDate" class="control-label">Select End Date</label>';
                    date_default_timezone_set('America/Chicago');
                    $curDate = date('Y-m-d');
                    echo '<input type="date" class="form-control" name="endDate" value = "'.$curDate.'">';
		echo '</div>';
	        echo '<button name="submit" type="submit" class ="btn btn-primary" value="submit">Search</button>';	
	    echo '</form>';	
        }
        elseif(isset($_POST['submit']) && $_POST['submit']=="submit")
	{
	    $startDate =$_POST['date'];
	    $endDate = $_POST['endDate'];
	    $temp = strtotime($startDate);
	    $convertedStartDate = date('Y/m/d 00:00:00', $temp);
	    $temp = strtotime($endDate);
	    $convertedEndDate = date('Y/m/d 23:59:59', $temp);
            $dblink = connectToDatabase();
            $dblink -> select_db("docstorage");
            $sql= "Select * from files where file_datetime BETWEEN'".$convertedStartDate."' AND '".$convertedEndDate."'";
            $result = $dblink->query($sql);
            if(!$result) {
                $errorfile = fopen("/var/searchErrors/searchErrors.txt", "w");
                fwrite($errorfile, "Something went wrong with $sql\n".$dblink->error);
                fclose($errorfile);
                die("<p>Something went wrong with $sql<p>".$dblink->error);
            }
            if($result->num_rows<=0)
            {
                redirect("https://ec2-18-222-116-88.us-east-2.compute.amazonaws.com/search_fileDate.php?msg=NoFilesFound&startDate=".$startDate."&endDate=".$endDate);
            }
            else
            {
                redirect(redirect("https://ec2-18-222-116-88.us-east-2.compute.amazonaws.com/search_fileDate.php?foundStartDate=".$convertedStartDate."&foundEndDate=".$convertedEndDate));
	    }
        }
        echo '</div>';
        echo '</div>';
        ?>
    </body>
</html>
