<html>


<head>
    <title>Hotel Management System</title>

    <head>
        <title>Hotel Management System</title>
        <link rel="stylesheet" href="style.css">

    </head>

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

    <h2>Facilities</h2>
    <hr />

    <h2>View All Facilities</h2>
    <form class="txt" method="POST" action="facilities.php">
        <!--refresh page when submitted-->
        <input type="hidden" id="viewFacilityRequest" name="viewFacilityRequest">
        <input type="submit" value="View" name="viewSubmit"></p>
    </form>


    <hr />

    <h2>View Customers Who Have Used All Facilities</h2>
    <form class="txt" method="POST" action="facilities.php">
        <!--refresh page when submitted-->
        <input type="hidden" id="viewFacilityCustomerRequest" name="viewFacilityCustomerRequest">
        <input type="submit" value="View" name="viewSubmit2"></p>
    </form>


    <hr />

    <h2>Results</h2>


    <?php
    $db_conn = NULL;
    $success = FALSE;
    function printResult($result)
    { //prints results from a select statement
        echo "<br>Retrieved data from table Facilities:<br>";
        echo "<table>";
        echo "<tr><th>Facility Type</th><th>Cost</th></tr>";

        while ($row = OCI_Fetch_Array($result, OCI_BOTH)) {
            echo "<tr><td>" . $row["TYPENAME"] . "</td><td>" . $row["COST"] . "</td><tr>"; //or just use "echo $row[0]"
        }


        echo "</table>";
    }


    function handleViewRequest()
    {
        global $db_conn;

        $result = executePlainSQL("SELECT * FROM  Facilities");
        printResult($result);

        OCICommit($db_conn);
    }

    function handleViewCustomerRequest()
    {
        global $db_conn;


        $sql = "SELECT CustomerID FROM Guest g WHERE NOT EXISTS ((SELECT f.TypeName FROM Facilities f) 
        MINUS (SELECT u.FacilityTypeName FROM Uses u WHERE u.UsesCustomerID = g.CustomerID))";
        $result = executePlainSQL($sql);

        echo "<br>Retrieved data from User table:<br>";
        echo "<table>";
        echo "<tr><th>CustomerID</th></tr>";

        while ($row = OCI_Fetch_Array($result, OCI_BOTH)) {
            echo "<tr><td>" . $row["CUSTOMERID"] . "</td><tr>"; //or just use "echo $row[0]"
        }

        echo "</table>";

        OCICommit($db_conn);
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



    if (isset($_POST['viewSubmit']) || isset($_POST['viewSubmit2'])) {
        handlePOSTRequest();
    }


    function handlePOSTRequest()
    {

        if (connectToDB()) {
            if (array_key_exists('viewFacilityRequest', $_POST)) {
                handleViewRequest();
            } else if (array_key_exists('viewFacilityCustomerRequest', $_POST)) {
                handleViewCustomerRequest();
            }
        } else {
            disconnectFromDB();
        }
    }



    ?>


</body>

</html>
