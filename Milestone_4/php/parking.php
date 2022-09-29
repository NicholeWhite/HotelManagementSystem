<html>


<head>
    <title>Hotel Management System</title>
    <link rel="stylesheet" href="style.css">

</head>

<body>
    <div class="navbar">
        <a href="homePage.php">Home</a>
        <a href="branch.php">Branch</a>
        <a href="employees.php">Employees</a>
        <a href="reservations.php">Reservations</a>
        <a href="guests.php">Guests</a>
        <a href="rooms.php">Rooms</a>
        <a href="facilities.php">Facilities</a>
        <a href="roomService.php">Room Service</a>
        <a href="parking.php">Parking</a>
        <a href="costs.php">Costs</a>
    </div>

    <h2>Parking</h2>
    <hr />


    <h2>View Parking Spot Status</h2>
    <form method="POST" action="parking.php">

        <label for="parkingNumber">Parking Number:</label>
        <select id="viewParkingRequest" name="viewParkingRequest">
            <option value="111234">111234</option>
            <option value="323556">323556</option>
            <option value="125523">125523</option>
            <option value="243356">243356</option>
            <option value="664578">664578</option>
        </select> <br /><br />
        <input type="submit" value="View" name="viewSubmit">

    </form>
    <hr />

    <h2>Update Parking Spot Data</h2>

    <form method="POST" action="parking.php">

        <label for="parkingNumber">Parking Number:</label>
        <select id="updateParkingNumber" name="updateParkingNumber">
            <option value="111234">111234</option>
            <option value="323556">323556</option>
            <option value="125523">125523</option>
            <option value="243356">243356</option>
            <option value="664578">664578</option>
        </select> <br /><br />
        Cost (per day): <input type="number" id = "changeCost" name="SpotCost"> <br /><br />
        Customer ID: <input type="text" name="customerID"><br /><br />
        <input type="radio" id="updateQueryRequest" name="occupied"> Occupied
        <input type="radio" id="updateQueryRequest" name="unoccupied"> Unoccupied <br /><br />

        <input type="submit" value="Update" name="updateSubmit">

    </form>

    <hr />

    <h2>View Guest Information From Parking Number</h2>

    <form method="POST" action="parking.php">

        <label for="parkingNumber">Parking Number:</label>
        <select id="viewGuestRequest" name="viewGuestRequest">
            <option value="111234">111234</option>
            <option value="323556">323556</option>
            <option value="125523">125523</option>
            <option value="243356">243356</option>
            <option value="664578">664578</option>
        </select> <br /><br />
        <input type="submit" value="View" name="viewSubmit2">

    </form>

    <hr />

    <h2>Results</h2>

    <?php
    $db_conn = NULL;
    $success = FALSE;
    function printResult($result)
    { //prints results from a select statement
        echo "<br>Retrieved data from table ParksAt_ParkingSpot:<br>";
        echo "<table>";
        echo "<tr><th>ParkingNumber</th><th>Occupied</th><th>Cost</th><th>CustomerID</th></tr>";

        while ($row = OCI_Fetch_Array($result, OCI_BOTH)) {
            echo "<tr><td>" . $row["PARKINGNUMBER"] . "</td><td>" . $row["OCCUPIED"] . "</td><td>" . $row["COST"] . "</td><td>" . $row["CUSTOMERID"] . "</td><tr>"; //or just use "echo $row[0]"
        }

        echo "</table>";
    }


    function handleViewRequest()
    {
        global $db_conn;

        $ParkingNumber = filter_var($_POST["viewParkingRequest"], FILTER_VALIDATE_INT); //parking number 

        $result = executePlainSQL("SELECT P.* FROM  ParksAt_ParkingSpot P WHERE ParkingNumber='" . $ParkingNumber . "'");
        printResult($result);

        OCICommit($db_conn);
    }

    function handleUpdateRequest()
    { //working!
        global $db_conn;

        $ParkingNumber = filter_var($_POST["updateParkingNumber"], FILTER_VALIDATE_INT); //parking number 
        $CustomerID = $_POST["customerID"];
        $SpotCost = filter_var($_POST["SpotCost"], FILTER_VALIDATE_INT);

        if (isset($_POST['occupied'])) { //if occupied is pressed 
            executePlainSQL("UPDATE ParksAt_ParkingSpot SET Occupied=1, CustomerID = '" . $CustomerID . "'  WHERE ParkingNumber='" . $ParkingNumber . "'");
        } else if (isset($_POST['unoccupied'])) {
            executePlainSQL("UPDATE ParksAt_ParkingSpot SET Occupied=0, CustomerID = NULL WHERE ParkingNumber='" . $ParkingNumber . "'");
        } else {
            echo "no changes made to occupied status \n";
        }

        if ($SpotCost >= 0) {
            echo "Changing cost to $" . $SpotCost . "\n";
            executePlainSQL("UPDATE ParksAt_ParkingSpot SET Cost = '" . $SpotCost . "'  WHERE ParkingNumber='" . $ParkingNumber . "'");
        }

        OCICommit($db_conn);
        echo "Updated parking status of Parking Number " . $ParkingNumber . "\n";
    }

    function handleViewGuestRequest()
    {
        global $db_conn;

        $ParkingNumber = filter_var($_POST["viewGuestRequest"], FILTER_VALIDATE_INT); //parking number 

        $result = executePlainSQL("SELECT G.FirstName, G.LastName, G.Email FROM Guest G, ParksAt_ParkingSpot P WHERE P.ParkingNumber='" . $ParkingNumber . "' AND P.CustomerID = G.CustomerID");
        echo "<br>Retrieved data from table ParksAt_ParkingSpot:<br>";
        echo "<table>";
        echo "<tr><th>First Name</th><th>Last Name</th><th>Email</th></tr>";

        while ($row = OCI_Fetch_Array($result, OCI_BOTH)) {
            echo "<tr><td>" . $row["FIRSTNAME"] . "</td><td>" . $row["LASTNAME"] . "</td><td>" . $row["EMAIL"] . "</td><tr>"; //or just use "echo $row[0]"
        };

        echo "</table>";

        OCICommit($db_conn); //still need to print result!! (TODO)
    }

    function connectToDB()
    {
        global $db_conn;
        $db_conn = OCILogon("ora_khemmi", "a62769344", "dbhost.students.cs.ubc.ca:1522/stu");

        if ($db_conn) {
            return true;
        } else {
            $e = OCI_Error(); // For OCILogon errors pass no handle
            echo htmlentities($e['message']);
            return false;
        }
    }

    function disconnectFromDB()
    {
        global $db_conn;
        

        debugAlertMessage("Disconnect from Database");
        OCILogoff($db_conn);
    }

    function executePlainSQL($cmdstr)
    { //takes a plain (no bound variables) SQL command and executes it
        //echo "<br>running ".$cmdstr."<br>";
        global $db_conn, $success;

        $statement = OCIParse($db_conn, $cmdstr);
        //There are a set of comments at the end of the file that describe some of the OCI specific functions and how they work

        if (!$statement) {
            echo "<br>Cannot parse the following command: " . $cmdstr . "<br>";
            $e = OCI_Error($db_conn); // For OCIParse errors pass the connection handle
            echo htmlentities($e['message']);
            $success = False;
        }

        $r = OCIExecute($statement, OCI_DEFAULT);




        if (!$r) {
            echo "<br>Cannot execute the following command: " . $cmdstr . "<br>";
            $e = oci_error($statement); // For OCIExecute errors pass the statementhandle
            echo htmlentities($e['message']);
            $success = False;
        }

        return $statement;
    }

    function executeBoundSQL($cmdstr, $list)
    {
        /* Sometimes the same statement will be executed several times with different values for the variables involved in the query.
      In this case you don't need to create the statement several times. Bound variables cause a statement to only be
      parsed once and you can reuse the statement. This is also very useful in protecting against SQL injection.
      See the sample code below for how this function is used */

        global $db_conn, $success;
        $statement = OCIParse($db_conn, $cmdstr);

        if (!$statement) {
            echo "<br>Cannot parse the following command: " . $cmdstr . "<br>";
            $e = OCI_Error($db_conn);
            echo htmlentities($e['message']);
            $success = False;
        }

        foreach ($list as $tuple) {
            foreach ($tuple as $bind => $val) {
                //echo $val;
                //echo "<br>".$bind."<br>";
                OCIBindByName($statement, $bind, $val);
                unset($val); //make sure you do not remove this. Otherwise $val will remain in an array object wrapper which will not be recognized by Oracle as a proper datatype
            }

            $r = OCIExecute($statement, OCI_DEFAULT);
            if (!$r) {
                echo "<br>Cannot execute the following command: " . $cmdstr . "<br>";
                $e = OCI_Error($statement); // For OCIExecute errors, pass the statementhandle
                echo htmlentities($e['message']);
                echo "<br>";
                $success = False;
            }
        }
        printResult($statement);
    }

    function handleGETRequest()
    {
        if (connectToDB()) {
            if (array_key_exists('countTuples', $_GET)) {
                handleCountRequest();
            }

            disconnectFromDB();
        }
    }

    if (isset($_POST['viewSubmit']) || isset($_POST['updateSubmit']) || isset($_POST['viewSubmit2'])) {
        handlePOSTRequest();
    } else if (isset($_GET['countTupleRequest'])) {
        handleGETRequest();
    }


    function handlePOSTRequest()
    {

        if (connectToDB()) {
            if (isset($_POST['occupied']) || isset($_POST['unoccupied'])|| array_key_exists('SpotCost', $_POST)) {
                handleUpdateRequest();
            } else if (isset($_POST['viewSubmit2'])) {
                handleViewGuestRequest();
            } else if (array_key_exists('viewParkingRequest', $_POST)) {
                handleViewRequest();
            }
        } else {
            disconnectFromDB();
        }
    }



    ?>

</body>

</html>