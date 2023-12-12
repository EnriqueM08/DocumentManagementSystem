<!Doctype html>
<html>
    <head>
        <meta charset="utf-8">
        <title>Upload Main</title>
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
    </head>
    <body>
        <?php
            include ("functions.php");
            $dblink = connectToDatabase();
            $dblink -> select_db("docstorage");
            echo '<div id="page-inner">';
            echo '<h1 class="page-head-line"> Upload an Existing File to DocStorage</h1>';
            echo '<div class="panel-body">';
            if (isset($_REQUEST['msg']) && ($_REQUEST['msg'] == "notPdf"))
            {
                echo '<div class="alert alert-danger alert-dismissable">';
                echo '<button type="button" class="close" data-dismiss="alert" aria-hidden="true">x</button>';
                echo 'The document you attempted to upload is not of type PDF please convert to pdf or try another file!</div>';
            }
            echo '<form method="post" enctype="multipart/form-data" action="">';
            echo '<input type="hidden" name="uploadedby" value="user@test.mail">';
            echo '<input type="hidden" name="MAX_FILE_SIZE" value = "10000000">';
            echo '<div class="form-group">';
            echo '<label for="loanNum" class="control-label">Loan Number</label>';
            echo '<select class="form-control" name="loanNum">';
            $sql="Select client_id from clients where 1";
            $result = $dblink->query($sql);
            if(!$result) {
                $errorfile = fopen("/var/uploadErrors/existingLoanErrors.txt", "w");
                fwrite($errorfile, "Something went wrong with $sql\n".$dblink->error);
                fclose($errorfile);
                die("<p>Something went wrong with $sql<p>".$dblink->error);
            }
            while ($data=$result->fetch_array(MYSQLI_ASSOC))
            {
                echo '<option value="'.$data['client_id'].'">'.$data['client_id'].'</option>';
            }
            echo '</select>';
                echo '</div>';
            echo '<div class="form-group">';
            echo '<label for="docType" class="control-label">Document Type</label>';
            echo '<select class="form-control" name="docType">';
            $sql="Select fileType from documentTypes where 1";
            $result = $dblink->query($sql);
            if(!$result) {
                $errorfile = fopen("/var/uploadErrors/existingLoanErrors.txt", "w");
                fwrite($errorfile, "Something went wrong with $sql\n".$dblink->error);
                fclose($errorfile);
                die("<p>Something went wrong with $sql<p>".$dblink->error);
            }
            while ($data=$result->fetch_array(MYSQLI_ASSOC))
            {
                echo '<option value="'.$data['fileType'].'">'.$data['fileType'].'</option>';
            }
            echo '</select>';
            echo '</div>';
            echo '<div class="form-group">';
            echo '<label class="control-label col-lg-4">File Upload</label>';
            echo '<div class="">';
            echo '<div class="fileupload fileupload-new" data-provides="fileupload">';
                echo '<input type="hidden" value name="userfile">';
                echo '<div class="fileupload-preview thumbnail" style="width: 40%; height: 150px;"></div>';
            echo '<div class="row">';
            echo '<div class="col-md-2">';
            echo '<span class="btn btn-file btn-primary">';
            echo '<span class="fileupload-new">Select File</span>';
            echo '<span class="fileupload-exists">Change</span>';
            echo '<input name="userfile" type="file"></span></div>';
            echo '<div class="col-md-2"><a href="#" class="btn btn-danger fileupload-exists" data-dismiss="fileupload">Remove</a></div>';
            echo '</div>';
            echo '</div>';
            echo '</div>';
            echo '</div>';
            echo '<hr>';
            echo '<button type="submit" name="submit" value="submit" class="btn btn-lg btn-block btn-success">Upload
            File</button>';
            echo '</form>';
            echo '</div>';
            echo '</div>';
            if(isset($_POST['submit']))
            {
                $dblink = connectToDatabase();
                $dblink -> select_db("docstorage");
                date_default_timezone_set('America/Chicago');
                $uploadDName=date("Ymd_H_i_s");
                $loanNum=$_POST['loanNum'];
                $docType=$_POST['docType'];
                $tmpName=$_FILES['userfile']['tmp_name'];
                $fileSize=$_FILES['userfile']['size'];
                $fileType=$_FILES['userfile']['type'];
                if($fileType != 'application/pdf') {
                    redirect("https://ec2-18-222-116-88.us-east-2.compute.amazonaws.com/upload_existing.php?msg=notPdf");
                }
                else{
                    $fileName="$loanNum-$docType-$uploadDName.pdf";
                    $fileLocation = getUploadLocation($fileName, $loanNum);
                    $time_start = microtime(true);
                    move_uploaded_file($_FILES["userfile"]["tmp_name"], $fileLocation);
                    $time_end = microtime(true);
                    $executionTime = ($time_end - $time_start)/60;
                    logUploadedFile($dblink, $loanNum, $fileName, $fileLocation, $fileSize, $executionTime);
                    redirect("https://ec2-18-222-116-88.us-east-2.compute.amazonaws.com/upload.php?msg=success");
                }
            }
        ?>
    </body>
</html>
