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
            echo '<h1 class="page-head-line">Displaying All Files</h1>';
        echo '<div class="panel-body">';
                $dblink = connectToDatabase();
		$dblink -> select_db("docstorage");
		echo '<h2>All Files</h2>';	
                echo '<table class = "table">';
                echo '<thead><tr><td>File Name</td><td>File Type</td><td>File Upload Date</td><td>Action</td></tr></thead>';
                $sql=("SELECT * from files");
                $result = $dblink->query($sql);
                if(!$result) {
                        $errorfile = fopen("/var/searchErrors/searchErrors.txt", "w");
                        fwrite($errorfile, "Something went wrong with $sql\n".$dblink->error);
                        fclose($errorfile);
                        die("<p>Something went wrong with $sql<p>".$dblink->error);
		}
		if($result->num_rows<=0) {
		    echo '</table>';
		    echo '<h3>No Files in the system!<h3>';
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
        echo '</div>';
        echo '</div>';
        ?>
    </body>
</html>
