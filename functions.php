<?php
    #Removed Username and Password and altered API endpoints for security purposes.
    $username="";
    $password="";

    function createSession($username, $password, $conn, $execution_time)
    {
        $data="username=$username&password=$password";
        $ch=curl_init('api/create_session');
        curl_setopt($ch, CURLOPT_POST,1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'content-type: application/x-www-form-urlencoded',
            'content-length: ' . strlen($data))
        );
        $time_start = microtime(true);
        $result = curl_exec($ch);
        $time_end = microtime(true);
        $execution_time += ($time_end - $time_start)/60;
        curl_close($ch);
        $cinfo=json_decode($result,true);
        $conn -> select_db("requestLogs");
        if ($cinfo[0]=="Status: OK" && $cinfo[1] == "MSG: Session Created")
        {
            $sid=$cinfo[2];
            $data="sid=$sid&uid=$username";
            date_default_timezone_set('America/Chicago');
            $datetime = date('Y-m-d H:i:s');
            $ip = $_SERVER['REMOTE_ADDR'];
            $sql = "INSERT INTO sessions (sessionID, status, connectionOpened, createExecutionTime, ipAddress)
                    VALUES ('$sid', 'active', '$datetime', '$execution_time', '$ip')";
            echo "\r\nSession Created Successfully!\r\n";
            echo "SID: $sid\r\n";
            echo "Create Session Execution Time: $execution_time\r\n";
            if($conn->query($sql) === TRUE)
            {
                echo "Session Stored in Database!\r\n";
            }
            return $sid;
        }
        else
        {
            $conn -> select_db("errorLogs");
            $sql = "INSERT INTO phpErrors (callType, returnedMessage) VALUES ('Create Session', '$cinfo[1]')";
            $conn->query($sql);
            if($cinfo[1] == "MSG: Previous Session Found")
            {
                $execution_time += clearSession($username, $password, $conn);
                echo "\r\nError clearing session and reattempting\r\n";
                return createSession($username, $password, $conn, $execution_time);
            }
            echo "\r\n ERROR: " . $cinfo[1] . "\r\n";
            return null;
        }
    }

    function closeSession($sessionID, $username, $conn) {
        $data="sid=$sessionID&uid=$username";
        $ch=curl_init('api/close_session');
        curl_setopt($ch, CURLOPT_POST,1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'content-type: application/x-www-form-urlencoded',
            'content-length: ' . strlen($data))
        );
        $time_start = microtime(true);
        $result = curl_exec($ch);
        $time_end = microtime(true);
        $execution_time = ($time_end - $time_start)/60;
        curl_close($ch);
        $cinfo=json_decode($result,true);
        $conn -> select_db("requestLogs");
        if ($cinfo[0]=="Status: OK")
        {
            date_default_timezone_set('America/Chicago');
            $datetime = date('Y-m-d H:i:s');
            $sql = "UPDATE sessions SET status='inactive' , connectionClosed='$datetime', closeExecutionTime = '$execution_time' WHERE sessionID='$sessionID'";
            echo "\r\nSession successfully closed!\r\n";
            echo "SID: $sessionID\r\n";
            echo "Close Session execution time: $execution_time\r\n";
            if($conn->query($sql) === TRUE)
            {
                echo "Session set to inactive in Database!\r\n";
            }
        }
        else
        {
            $conn -> select_db("errorLogs");
            $sql = "INSERT INTO phpErrors (callType, returnedMessage) VALUES ('Close Session', '$cinfo[1]')";
            $conn->query($sql);
            if($cinfo[1] == "MSG: SID cannot be null" || $cinfo[1] == "MSG: SID not found")
            {
                echo "Attempting to clearSession";
                $execution_time += clearSession($username, "rc7jBd3LntQHZq8K", $conn);
            }
            else
            {
                echo "\r\nERROR: " . $cinfo[1] . "\r\n";
                return -1;
            }
        }
        return $execution_time;
    }

    function clearSession($username, $password, $conn)
    {
        $data="username=$username&password=$password";
        $ch=curl_init('api/clear_session');
        curl_setopt($ch, CURLOPT_POST,1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'content-type: application/x-www-form-urlencoded',
            'content-length: ' . strlen($data))
        );
        $time_start = microtime(true);
        $result = curl_exec($ch);
        $time_end = microtime(true);
        $execution_time = ($time_end - $time_start)/60;
        curl_close($ch);
        $cinfo=json_decode($result,true);
        $conn -> select_db("requestLogs");
        if ($cinfo[0]=="Status: OK")
        {
            echo "\r\nSession found and cleared!\r\n";
            date_default_timezone_set('America/Chicago');
            $datetime = date('Y-m-d H:i:s');
            $sql = "UPDATE sessions SET status='inactive' ,connectionClosed='$datetime', closeExecutionTime = '$execution_time' WHERE status='active'";
            if($conn->query($sql) === TRUE)
            {
                echo "Session set to inactive in Database!\r\n";
            }
            echo "Clear Session execution time: $execution_time\r\n";
        }
        else if($cinfo[1]=="MSG: No previous session found")
        {
            $conn -> select_db("errorLogs");
            $sql = "INSERT INTO phpErrors (callType, returnedMessage) VALUES ('Clear Session', '$cinfo[1]')";
            $conn->query($sql);
            echo "\r\nError: No Previous session to clear\r\n";
        }
        else {
            echo "\r\nError: " . $cinfo[1] . "\r\n";
        }
        return $execution_time;
    }

    function queryFiles($username, $sessionID, $conn)
    {
        $data="uid=$username&sid=$sessionID";
        $ch=curl_init('api/query_files');
        curl_setopt($ch, CURLOPT_POST,1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'content-type: application/x-www-form-urlencoded',
            'content-length: ' . strlen($data))
        );
        $time_start = microtime(true);
        $result = curl_exec($ch);
        $time_end = microtime(true);
        $execution_time = ($time_end - $time_start)/60;
        curl_close($ch);
        $cinfo=json_decode($result,true);
        $conn -> select_db("requestLogs");
        if ($cinfo[0]=="Status: OK")
        {
            echo "\r\nQuery Files execution time: $execution_time\r\n";
            $sql = "INSERT INTO queryFiles (sessionID, returnedMessage, executionTime)
                VALUES ('$sessionID', '$cinfo[1]', '$execution_time')";
            if($conn->query($sql) === TRUE)
            {
                echo "QueryFile Message successfully stored!\r\n";
            }
        }
        else
        {
            $conn -> select_db("errorLogs");
            $sql = "INSERT INTO phpErrors (callType, returnedMessage) VALUES ('Query Files', '$cinfo[1]')";
            $conn->query($sql);
            echo "ERROR: " . $cinfo[1] . "\r\n";
        }
        return $cinfo[1];
    }

    function requestFile($sessionID, $username, $fileID, $loanID, $conn)
    {
        $data="sid=$sessionID&uid=$username&fid=$fileID";
        if(!is_dir("/var/pdfFiles/$loanID"))
            mkdir("/var/pdfFiles/$loanID");
        #Need to set file name based on fileID also will need to handle duplicate files
        $dirName = "/var/pdfFiles/$loanID";
        $fileName = "/var/pdfFiles/$loanID/$fileID";
        while(is_file($fileName) && is_dir($dirName)) {
            echo "\r\nDUPLICATE FOUND\r\n";
            $dirName .= "/duplicates";
            if(!is_dir($dirName))
            {
                mkdir($dirName);
                $fileName = "$dirName/$fileID";
            }
        }
        $fp = fopen($fileName, 'w');
        $ch=curl_init('api/request_file');
        curl_setopt($ch, CURLOPT_POST,1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'content-type: application/x-www-form-urlencoded',
            'content-length: ' . strlen($data))
        );
        curl_setopt($ch, CURLOPT_FILE, $fp);
        $time_start = microtime(true);
        $result = curl_exec($ch);
        $time_end = microtime(true);
        $execution_time = ($time_end - $time_start)/60;
        curl_close($ch);
        fclose($fp);
        date_default_timezone_set('America/Chicago');
        $datetime = date('Y-m-d H:i:s');
        $fileSize = filesize($fileName);
        logFile($conn, $loanID, $fileID, $fileName, $fileSize, $username, $datetime, $execution_time);
        echo "Request File execution time: $execution_time\r\n";
        return $execution_time;
    }

    function logFile($conn, $loanID, $fileID, $fileName, $fileSize, $username, $datetime, $executionTime) {
        $conn -> select_db("docstorage");
        $sql = "SELECT * FROM clients WHERE client_id='$loanID'";
        $result = $conn->query($sql);
        if($result->fetch_row() == null) {
            echo "\r\nNeed to create client!\r\n";
            $sql = "INSERT into clients (client_id, client_status, client_type)
                    VALUES ('$loanID', 'active', 'normal')";
            if($conn->query($sql) === TRUE) {
                echo "Successfully created client!\r\n";
            }
        }
        $fileInfo = explode('-', $fileID);
        $dateInfo = substr($fileInfo[2], 0, 4) . "/";
        $dateInfo .= substr($fileInfo[2], 4, 2) . "/";
        $dateInfo .= substr($fileInfo[2], 6, 2) . " ";
        $dateInfo .= substr($fileInfo[2], 9, 2) . ":";
        $dateInfo .= substr($fileInfo[2], 12, 2) . ":";
        $dateInfo .= substr($fileInfo[2], 15, 2);
        $dateNum = strtotime($dateInfo);
        $sqlFormattedDate = date('Y-m-d H:i:s', $dateNum);
        $sql = "INSERT into files (client_id, file_status, file_name, file_type, file_location, file_datetime, file_size, file_permissions, fileUploader, time_uploaded, executionTime)
                VALUES ('$loanID', 'uploaded', '$fileID', '$fileInfo[1]', '$fileName', '$sqlFormattedDate', '$fileSize', 'Anyone', '$username', '$datetime', '$executionTime')";
        if($conn->query($sql) === TRUE) {
            echo "\r\nSuccessfully Inserted file information\r\n";
        }
    }

    function requestAllDocuments($sessionID, $username)
    {
        $data="sid=$sessionID&uid=$username";
        $ch=curl_init('api/request_all_documents');
        curl_setopt($ch, CURLOPT_POST,1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'content-type: application/x-www-form-urlencoded',
            'content-length: ' . strlen($data))
        );
        $time_start = microtime(true);
        $result = curl_exec($ch);
        $time_end = microtime(true);
        $execution_time = ($time_end - $time_start)/60;
        curl_close($ch);
        $cinfo=json_decode($result,true);
        if ($cinfo[0]=="Status: OK")
        {
            echo "\r\nRequest All Documents execution time: $execution_time\r\n";
        }
        else
        {
            echo "\r\n ERROR: " . $cinfo[1] . "\r\n";
        }
        return $cinfo[1];
    }

    function requestAllLoanIDs($sessionID, $username)
    {
        $data="sid=$sessionID&uid=$username";
        $ch=curl_init('api/request_all_loans');
        curl_setopt($ch, CURLOPT_POST,1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'content-type: application/x-www-form-urlencoded',
            'content-length: ' . strlen($data))
        );
        $time_start = microtime(true);
        $result = curl_exec($ch);
        $time_end = microtime(true);
        $execution_time = ($time_end - $time_start)/60;
        curl_close($ch);
        $cinfo=json_decode($result,true);
        if ($cinfo[0]=="Status: OK")
        {
            #Need to loop through and compare to what I have
            echo "<pre>";
            print_r($cinfo);
            echo "</pre>";
            echo "Request All Loans execution time: $execution_time\r\n";
        }
        else if($cinfo[1]=="MSG: No previous session found")
        {
            echo "test";
        }
        else
        {
            echo "<pre>";
            print_r($cinfo);
            echo "</pre>";
        }
        return $cinfo[1];
    }

    function requestFileByLoanNumber($sessionID, $username, $loanID)
    {
        $data="sid=$sessionID&uid=$username&lid=$loanID";
        $ch=curl_init('api/request_file_by_loan');
        curl_setopt($ch, CURLOPT_POST,1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'content-type: application/x-www-form-urlencoded',
            'content-length: ' . strlen($data))
        );
        $time_start = microtime(true);
        $result = curl_exec($ch);
        $time_end = microtime(true);
        $execution_time = ($time_end - $time_start)/60;
        curl_close($ch);
        $cinfo=json_decode($result,true);
        if ($cinfo[0]=="Status: OK")
        {
            #Need to loop through and compare to what I have
            echo "<pre>";
            print_r($cinfo);
            echo "</pre>";
            echo "Request File by Loan Number execution time: $execution_time\r\n";
        }
        else
        {
            echo "<pre>";
            print_r($cinfo);
            echo "</pre>";
        }
    }

    function connectToDatabase()
    {
        $servername = "localhost";
        $username = "";
        $password = "";
        $conn = new mysqli($servername, $username, $password);
        if($conn->connect_error)
        {
            die("Connection failed: " . $conn->connect_error);
        }
        else
        {
            echo "Connection successful";
        }
        return $conn;
    }

    function getDocuments($sid, $username, $conn)
    {
        $filesString = queryFiles($username, $sid, $conn);
        if($filesString == "MSG: SID not found") {
            $sid = createSession($username, $password, $conn);
            $filesString = queryFiles($username, $sid, $conn);
        }
        preg_match_all('/([A-Za-z0-9]+(-[A-Za-z0-9]+)+)_\d\d_\d\d_\d\d\.pdf/', $filesString, $fileIDs);
        $execTime = saveFiles($sid, $username, $fileIDs, $conn);
        return $execTime;
    }

    function saveFiles($sid, $username, $fileIDs, $conn) {
        $count = 0;
        $execTime = 0.0;
        foreach ($fileIDs[0] as $fileID) {
            $fileInfo = explode('-', $fileID);
            $count++;
            $execTime += requestFile($sid, $username, $fileID, $fileInfo[0], $conn);
        }
        $conn -> select_db("requestLogs");
        $sql = "UPDATE queryFiles SET numFilesToAdd='$count' WHERE sessionID='$sid'";
        if($conn->query($sql) === TRUE)
        {
            echo "numFiles stored is $count!";
        }
        return $execTime;
    }

    function calculateSessionTime($sid, $conn, $execTime)
    {
        $conn -> select_db("requestLogs");
        $sql = "SELECT createExecutionTime FROM sessions WHERE sessionID='$sid'";
        $result = $conn->query($sql);
        if($result == TRUE)
        {
            $execTime += $result->fetch_row()[0];
            $sql = "SELECT executionTime FROM queryFiles WHERE sessionID ='$sid'";
            $result = $conn->query($sql);
            if($result == TRUE)
            {
                $execTime += $result->fetch_row()[0];
                $sql = "UPDATE sessions SET totalExecutionTime = '$execTime' WHERE sessionID = '$sid'";
                $conn->query($sql);
            }
        }
    }

    function saveMasterRecord($sid, $username, $fileIDs, $conn) {
        $count = 0;
        $execTime = 0.0;
        $conn -> select_db("weeklyCheck");
        $sql = "DELETE FROM allFiles";
        $conn->query($sql);
        foreach ($fileIDs[0] as $fileID) {
            $fileInfo = explode('-', $fileID);
            $count++;
            $sql = "INSERT INTO allFiles (loanID, fileName)
                   VALUES ('$fileInfo[0]', '$fileID')";
            $conn->query($sql);
        }
        echo "\r\nNum Records: " . $count . "\r\n";
        return $count;
    }

    function findUnsavedFiles($conn, $sid, $username) {
        $conn -> select_db("weeklyCheck");
        $sql = "SELECT * FROM allFiles";
        $result = $conn->query($sql);
        $curFile = $result->fetch_row();
        $conn -> select_db("docstorage");
        $unsavedCount = 0;
        while($curFile != null) {
            $sql = "SELECT * FROM files WHERE file_name='$curFile[1]'";
            $resultTwo = $conn->query($sql);
            if($resultTwo->fetch_row() == null) {
                 requestFile($sid, $username, $curFile[1], $curFile[0], $conn);
                $unsavedCount++;
            }
            $curFile = $result->fetch_row();
        }
        echo "\r\n Count = " . $unsavedCount . "\r\n";
        return $unsavedCount;
    }

    function findDuplicates($conn, $sid, $username) {
        $conn -> select_db("weeklyCheck");
        $sql = "SELECT loanID, fileName, COUNT(*) FROM allFiles GROUP BY loanID, fileName HAVING COUNT(*) > 1;";
        $result = $conn->query($sql);
        $conn -> select_db("docstorage");
        $curFile = $result->fetch_row();
        $dupCount = 0;
        while($curFile != null) {
            $count = $curFile[2];
            $count -= 1;
            $sql = "SELECT file_name, COUNT(file_name) FROM files WHERE file_name = '$curFile[1]' GROUP BY file_name HAVING COUNT(file_name) > $count";
            $resultTwo = $conn->query($sql);
            if($resultTwo->fetch_row() == null) {
                requestFile($sid, $username, $curFile[1], $curFile[0], $conn);
                echo "\r\n Found unadded duplicate and added\r\n";
                $dupCount++;
            }
            $curFile = $result->fetch_row();
        }
        echo "\r\n Num of duplicates added = $dupCount\r\n";
        return $dupCount;
    }

    #function storeAllLoanIDs($conn, $sid, $username) {
#       $loanString = requestAllLoanIDs($sid, $username);
 #       $conn -> select_db("weeklyCheck");
#       preg_match_all('/[A-Za-z0-9]+/', $loanString, $loanIDs);
#       foreach($loanIDs[0] as $loanID) {
#           if($loanID != "MSG")
#           {
 #               $sql = "INSERT INTO allLoanIDs (loanID)
#               VALUES ('$loanID')";
 #               $conn->query($sql);
  #          }
#       }
 #   }
?>
