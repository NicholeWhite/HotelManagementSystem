<!DOCTYPE html>

<html>

<head>
    <title> Costs </title>
    <link rel="stylesheet" href="style.css">

</head>

<body>

    <div class="navbar">
        <a href="homepage.php">Home</a>
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

    <h2> Costs </h2>

    <hr>

    <h2> Selection </h2>

    <form method="POST" action="costs.php">
        <input type="hidden" id="selectionQueryRequest" name="selectionQueryRequest">

        Select table you would like to query.<br>
        <input type="radio" id="SelectFacilities" name="SelectFacilities">
        <label for="Facilities">Facilities</label><br>
        <input type="radio" id="SelectRooms" name="SelectRooms">
        <label for="Rooms">Rooms</label>
        <br><br>

        <!PROJECTION: choose attribute(s) to view>
            <input type="checkbox" id="CostAttribute" name="CostAttribute" value="CostAttribute">
            <label for="CostAttribute"> Cost</label><br>
            <input type="checkbox" id="NameAttribute" name="NameAttribute" value="NameAttribute">
            <label for="NameAttribute"> Name (or identification information)</label>
            <br><br>
            <!END PROJECTION>


                Select the <em>lower</em> bound of cost range you would like to search.<br>
                The query will take the from lowerBound <= x <=upperBound.<br>
                    <label for="SelectionLowerBound">Lower Bound:</label>
                    <select id="SelectionLowerBound" name="SelectionLowerBound">
                        <option value="0">0</option>
                        <option value="10">10</option>
                        <option value="20">20</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                        <option value="250">250</option>
                        <option value="500">500</option>
                        <option value="1000">1000</option>
                        <option value="1500">1500</option>
                    </select>
                    <br><br>

                    Select the <em>upper</em> bound of cost range you would like to search. <br>
                    <label for="SelectionUpperBound">Upper Bound:</label>
                    <select id="SelectionUpperBound" name="SelectionUpperBound">
                        <option value="0">0</option>
                        <option value="10">10</option>
                        <option value="20">20</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                        <option value="250">250</option>
                        <option value="500">500</option>
                        <option value="1000">1000</option>
                        <option value="1500">1500</option>
                    </select>
                    <br><br>

                    <input type="submit" value="Select" name="selectSubmit">
    </form>

    <hr>

</body>

<?php
$db_conn = NULL;

//Selection
function selectionQueryRequest()
{
    global $db_conn;

    $LowerBound = filter_var($_POST["SelectionLowerBound"], FILTER_VALIDATE_INT);
    $UpperBound = filter_var($_POST["SelectionUpperBound"], FILTER_VALIDATE_INT);

    if (isset($_POST['SelectFacilities'])) { //if 'facilities' is pressed 
        $result = executePlainSQL("SELECT * FROM Facilities WHERE Cost>='" . $LowerBound . "' AND Cost<='" . $UpperBound . "'");

        echo "<br>Retrieved data from table Facilities:<br>";
        echo "<table>";
        if (isset($_POST['CostAttribute']) && isset($_POST['NameAttribute'])) { //Display name AND cost
            echo "<tr><th>Name</th><th>Cost</th></tr>";

            while ($row = OCI_Fetch_Array($result, OCI_BOTH)) {
                echo "<tr><td>" . $row["TYPENAME"] . "</td><td>" . $row["COST"] . "</td><tr>";
            };
        } else if (isset($_POST['CostAttribute'])) { //Display cost ONLY
            echo "<tr><th>Cost</th></tr>";

            while ($row = OCI_Fetch_Array($result, OCI_BOTH)) {
                echo "<tr><td>" . $row["COST"] . "</td><tr>";
            };
        } else if (isset($_POST['NameAttribute'])) { //Display name ONLY
            echo "<tr><th>Name</th></tr>";

            while ($row = OCI_Fetch_Array($result, OCI_BOTH)) {
                echo "<tr><td>" . $row["TYPENAME"] . "</td><tr>";
            };
        }
        echo "</table>";

        

        //find the branches that have average emplyment years > 5
        //produce those branch name, the numbe of mepliyees at that branch, and the average
        //employment years for that branch

        //SELECT branchName, count(*), avg(empyears)
        //FROM employment
        //GROUP BY branchName
        //HAVING avg(empyears) > 5








        //(NO PROJECTION) CODE
        // echo "<br>Retrieved data from table Facilities:<br>";
        // echo "<table>";
        // echo "<tr><th>Type Name</th><th>Cost</th></tr>";

        // while ($row = OCI_Fetch_Array($result, OCI_BOTH)) {
        //     echo "<tr><td>" . $row["TYPENAME"] . "</td><td>" . $row["COST"] . "</td><tr>";
        // };
        //echo "</table>";

    } else if (isset($_POST['SelectRooms'])) { //if 'rooms' is pressed
        $result = executePlainSQL("SELECT * FROM Has_Room1 H1, Has_Room2 H2 WHERE Cost>='" . $LowerBound . "' AND Cost<='" . $UpperBound . "' AND H1.RoomType = H2.RoomType");

        echo "<br>Retrieved data from tables Has_Room1 and Has_Room2:<br>";
        echo "<table>";
        if (isset($_POST['CostAttribute']) && isset($_POST['NameAttribute'])) { //Display ID AND cost
            echo "<tr><th>Room Number</th><th>Branch Name</th><th>Cost</th></tr>";

            while ($row = OCI_Fetch_Array($result, OCI_BOTH)) {
                echo "<tr><td>" . $row["ROOMNUMBER"] . "</td><td>" . $row["ROOMBRANCHNAME"] . "</td><td>" . $row["COST"] . "</td><tr>";
            };
        } else if (isset($_POST['CostAttribute'])) { //Display cost ONLY
            echo "<tr><th>Cost</th></tr>";

            while ($row = OCI_Fetch_Array($result, OCI_BOTH)) {
                echo "<tr><td>" . $row["COST"] . "</td><tr>";
            };
        } else if (isset($_POST['NameAttribute'])) { //Display ID ONLY
            echo "<tr><th>Name</th><th>BranchName</th></tr>";

            while ($row = OCI_Fetch_Array($result, OCI_BOTH)) {
                echo "<tr><td>" . $row["ROOMNUMBER"] . "</td><td>" . $row["ROOMBRANCHNAME"] . "</td><tr>";
            };
        }
        echo "</table>";

        // echo "<br>Retrieved data from tables Has_Room1 ans Has_Room2:<br>";
        // echo "<table>";
        // echo "<tr><th>Room Type</th><th>Cost</th><th>Room Number</th><th>Branch Name</th><th>Room Type</th><th>Cleaned</th></tr>";

        // while ($row = OCI_Fetch_Array($result, OCI_BOTH)) {
        //     echo "<tr><td>" . $row["ROOMTYPE"] . "</td><td>" . $row["COST"] . "</td><td>" . $row["ROOMNUMBER"] . "</td><td>" . $row["ROOMBRANCHNAME"] . "</td><td>" . $row["ROOMTYPE"] . "</td><td>" . $row["CLEANED"] . "</td><tr>";
        // };
        // echo "</table>";

    } else {
        echo "No changes made";
    }

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

if (isset($_POST['deleteSubmit']) || isset($_POST['viewSubmit']) || isset($_POST['updateSubmit']) || isset($_POST['selectSubmit'])) {
    handlePOSTRequest();
} else if (isset($_GET['countTupleRequest'])) {
    handleGETRequest();
}


function handlePOSTRequest()
{

    if (connectToDB()) {
        if (array_key_exists('insertQueryRequest', $_POST)) {
            handleInsertRequest();
        } else if (array_key_exists('deleteQueryRequest', $_POST)) {
            handleDeleteRequest();
        } else if (array_key_exists('viewQueryRequest', $_POST)) {
            handleViewRequest();
        } else if (array_key_exists('selectionQueryRequest', $_POST)) {
            selectionQueryRequest();
        }
    } else {
        disconnectFromDB();
    }
}


?>

</html>