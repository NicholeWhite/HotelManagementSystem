<!doctype html>
<html>

<head>
  <title>Reservations</title>
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

  <h2>Reservations</h2>

  <hr />

  <h2>Add Reservation</h2>

  Enter the reservation information.<br>
  <form method="POST" action="reservations.php">
    <input type="hidden" id="insertQueryRequest" name="insertQueryRequest">
    Confirmation Number: <input type="text" name="InsertConfirmationNumber"> <br /><br />
    Customer ID: <input type="text" name="InsertBooksCustomerID"> <br /><br />
    Start Date: <input type="text" name="InsertStartDate"> <br /><br />
    End Date: <input type="text" name="InsertEndDate"> <br /><br />
    <input type="submit" value="Add" name="insertSubmit">
  </form>

  <hr />

  <h2>Delete Reservation</h2>

  Select desired reservation using its confirmation number.<br>
  <form method="POST" action="reservations.php">
    <input type="hidden" id="deleteQueryRequest" name="deleteQueryRequest">
    Confirmation Number: <input type="text" name="DeleteConfirmationNumber"> <br /><br />
    <input type="submit" value="Delete" name="deleteSubmit">
  </form>

</body>

<?php
$db_conn = NULL;

//-->Delete?
function handleViewRequest()
{
  global $db_conn;

  $ViewEmployeeID = filter_var($_POST["ViewEmployeeID"], FILTER_VALIDATE_INT);
  $result = executePLAINSQL("SELECT * FROM Employee_WorksAt WHERE EmployeeID='" . $ViewEmployeeID . "'");

  echo "<br>Retrieved data from table Employee_WorksAt:<br>";
  echo "<table>";
  echo "<tr><th>Branch</th><th>Employee ID</th><th>SIN</th><th>Name</th></tr>";

  while ($row = OCI_Fetch_Array($result, OCI_BOTH)) {
    echo "<tr><td>" . $row["WORKSBRANCHNAME"] . "</td><td>" . $row["EMPLOYEEID"] . "</td><td>" . $row["SINNUMBER"] . "</td><td>" . $row["EMPLOYEENAME"] . "</td><tr>"; //or just use "echo $row[0]"
  };

  echo "</table>";

  OCICommit($db_conn);
}

function handleInsertRequest()
{
  global $db_conn;

  $confirmationNumber = $_POST["InsertConfirmationNumber"];
  $tuple = array(
    ":element1" => $_POST["InsertConfirmationNumber"], //ConfirmationNumber
    ":element2" => $_POST["InsertBooksCustomerID"], //BooksCustomerID
    ":element3" => $_POST["InsertStartDate"], //StartDate
    ":element4" => $_POST["InsertEndDate"] //EndDate 
  );

  $alltuples = array(
    $tuple
  );

  executeBoundSQL("insert into Books_Reservation values (:element1, :element2, :element3, :element4)", $alltuples);

  OCICommit($db_conn);

  echo "A reservation with confirmation number: " . $confirmationNumber . " has been added to the system.";

  //TODO: 
  // if (isset($_POST["HousekeeperInsert"])) {
  // 	$tuple = array (
  // 		":housekeeperElement1" => filter_var($_POST["InsertEmployeeID"], FILTER_VALIDATE_INT)
  // 	);

  // 	$alltuples = array (
  // 		$tuple
  // 	); 

  // 	executeBoundSQL("insert into Housekeeper values (:housekeeperElement1)", $alltuples);
  // 	OCICommit($db_conn); 
  // }; 
}

//GOOD
//handle "Delete Reservation" request
function handleDeleteRequest()
{
  global $db_conn;

  $DeletedConfirmationNumber = filter_var($_POST["DeleteConfirmationNumber"], FILTER_VALIDATE_INT);

  executePLAINSQL("DELETE FROM Books_Reservation WHERE ConfirmationNumber='" . $DeletedConfirmationNumber . "'");
  OCICommit($db_conn);
  echo "Deleted reservation with confirmation number " . $DeletedConfirmationNumber . " from system.";
}

//GOOD
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

//GOOD
function disconnectFromDB()
{
  global $db_conn;

  debugAlertMessage("Disconnect from Database");
  OCILogoff($db_conn);
}

//GOOD
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

//GOOD
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

//GOOD
function handleGETRequest()
{
  if (connectToDB()) {
    if (array_key_exists('countTuples', $_GET)) {
      handleCountRequest();
    }

    disconnectFromDB();
  }
}

//GOOD - WHY NOT IN FN.?
if (isset($_POST['deleteSubmit']) || isset($_POST['viewSubmit']) || isset($_POST['updateSubmit']) || isset($_POST['insertSubmit'])) {
  handlePOSTRequest();
} else if (isset($_GET['countTupleRequest'])) {
  handleGETRequest();
}

//GOOD
function handlePOSTRequest()
{

  if (connectToDB()) {
    if (array_key_exists('insertQueryRequest', $_POST)) {
      handleInsertRequest();
    } else if (array_key_exists('deleteQueryRequest', $_POST)) {
      handleDeleteRequest();
    } else if (array_key_exists('viewQueryRequest', $_POST)) {
      handleViewRequest();
    }
  } else {
    disconnectFromDB();
  }
}
?>

</html>