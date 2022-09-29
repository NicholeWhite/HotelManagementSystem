<!DOCTYPE html>

<html>

<head>
    <title> Branch </title>
    <link rel="stylesheet" href="style.css">
    <?php include 'login.php'; ?>
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

    <h2> Branch </h2>

    <hr>

    <h2> View Branch Information </h2>
    <form method="POST" action="branch.php">

        <label for="hotels">Select a Branch Name:</label>

        <select name="viewBranch" id="viewBranch">
            <option value="Hilton">Hilton</option>
            <option value="Marriott">Marriott</option>
            <option value="Shelton">Shelton</option>
            <option value="Motel 8">Motel 8</option>
            <option value="Best Western">Best Western</option>
        </select>
        <br><br>
        <input type="submit" value="View" name="viewSubmit">
    </form>

    <hr>

    <h2> Results </h2>


    <p>
    </p>

</body>

<?php
$db_conn = NULL;
$success = FALSE;
function printResult($result)
{ //prints results from a select statement
    echo "<br>Retrieved data from table Branch:<br>";
    echo "<table>";
    echo "<tr><th>Branch Name</th><th>Branch Number</th><th>Branch Address</th></tr>";

    while ($row = OCI_Fetch_Array($result, OCI_BOTH)) {
        echo "<tr><td>" . $row["BRANCHNAME"] . "</td><td>" . $row["BRANCHNUMBER"] . "</td><td>" . $row["BRANCHADDRESS"] . "</td><tr>"; //or just use "echo $row[0]"
    }




    echo "</table>";
}


function handleViewRequest()
{
    global $db_conn;

    $BranchName = $_POST["viewBranch"];

    $result = executePlainSQL("SELECT B1.BranchAddress,  B2.BranchName, B2.BranchNumber FROM  Branch1 B1, Branch2 B2 WHERE B2.BranchName='" . $BranchName . "' and B2.BranchNumber = B1.BranchNumber");
    printResult($result);

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

if (isset($_POST['viewSubmit'])) {
    handlePOSTRequest();
} else if (isset($_GET['countTupleRequest'])) {
    handleGETRequest();
}


function handlePOSTRequest()
{

    if (connectToDB()) {
        if (isset($_POST['viewSubmit'])) {
            handleViewRequest();
        }
    } else {
        disconnectFromDB();
    }
}



?>

</html>
