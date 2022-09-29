<!doctype html>
<html>

<head>
  <title>Rooms</title>
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

  <h2>Rooms</h2>

  <hr />

  <h2>Update Room</h2>

  Enter the room's identification information.<br>
  <form method="POST" action="rooms.php">
    <input type="hidden" id="updateQueryRequest" name="updateQueryRequest">

    Room Number: <input type="text" name="UpdateRoomNumber"> <br /><br />
    Branch Name: <input type="text" name="UpdateRoomBranchName"> <br /><br />

    Select <em>new</em> status of room (yes if cleaned, no otherwise).<br>
    <!cleaned Boolean Selection>
      <!-- <input type="radio" id="updateQueryRequest" name="updateQueryRequest">
      <label for="yes">yes</label><br>
      <input type="radio" id="updateQueryRequest" name="updateQueryRequest">
      <label for="no">no</label><br> -->
      <input type="radio" id="UpdateYesCleaned" name="UpdateYesCleaned">
      <label for="yes">yes</label><br>
      <input type="radio" id="UpdateNoCleaned" name="UpdateNoCleaned">
      <label for="no">no</label><br>

      <input type="submit" value="Update" name="updateSubmit">

  </form>

  <hr />
  <h2>View Room</h2>

  Select desired room using the unique combination of branch name and room number.<br>
  <form method="POST" action="rooms.php">
    <input type="hidden" id="viewQueryRequest" name="viewQueryRequest">
    Room Number: <input type="text" name="ViewRoomNumber"> <br /><br />
    Branch Name: <input type="text" name="ViewRoomBranchName"> <br /><br />
    <input type="submit" value="View" name="viewSubmit">
  </form>

  <hr />
  <h2>Count Rooms by Cost</h2>

  Returns the number of rooms with cost greater than your input.<br>
  <form method="POST" action="rooms.php">
    <input type="hidden" id="countQueryRequest" name="countQueryRequest">
    Cost: <input type="number" name="CountRoomsCost"> <br /><br />
    <input type="submit" value="Count" name="countSubmit">
  </form>

  <hr />
  <h2>View Highest Priced Room by Branch</h2>

  Returns the rooms with the highest cost from each branch<br>
  <form method="POST" action="rooms.php">
    <input type="hidden" id="viewAllQueryRequest" name="viewAllQueryRequest">
    <!-- Cost: <input type="number" name="CountRoomsCost2"> <br /><br /> -->
    <input type="submit" value="View" name="view2Submit">
  </form>

  <hr />

</body>

<?php
$db_conn = NULL;

function printResult($result)
{
  echo "<br>Retrieved data from table Has_Room2:<br>";
  echo "<table>";
  echo "<tr><th>Room Number</th><th>Branch Name</th><th>Room Type</th><th>Cleaned</th></tr>";

  while ($row = OCI_Fetch_Array($result, OCI_BOTH)) {
    echo "<tr><td>" . $row["ROOMNUMBER"] . "</td><td>" . $row["ROOMBRANCHNAME"] . "</td><td>" . $row["ROOMTYPE"] . "</td><td>" . $row["CLEANED"] . "</td><tr>";
  }

  echo "</table>";
}

function handleViewRequest()
{
  global $db_conn;

  $ViewRoomNumber = filter_var($_POST["ViewRoomNumber"], FILTER_VALIDATE_INT);
  $ViewRoomBranchName = $_POST["ViewRoomBranchName"];

  $result = executePlainSQL("SELECT * FROM  Has_Room2 WHERE RoomNumber='" . $ViewRoomNumber . "' AND RoomBranchName='" . $ViewRoomBranchName . "'");

  printResult($result);
  OCICommit($db_conn);
}

function handleCountRequest()
{
  global $db_conn;

  $MinCost = filter_var($_POST["CountRoomsCost"], FILTER_VALIDATE_INT);

  //Aggregation by having: count number of rooms with cost > x 
  $result = executePlainSQL("SELECT * FROM Has_Room1 WHERE Cost > $MinCost GROUP BY Cost HAVING Cost > $MinCost");

  if (($row = oci_fetch_row($result)) != false) {
    echo "There are " . $row[0] . " rooms with a cost greater than $" . $MinCost;
  }

  OCICommit($db_conn);
}


function handleViewAllRequest()
{
  global $db_conn;

  $MinCost = filter_var($_POST["CountRoomsCost2"], FILTER_VALIDATE_INT);

  //Aggregation group by: display rooms with cost > x 
  $result = executePlainSQL("SELECT  t.RoomBranchName, MAX(Cost) FROM 
            Has_Room1 h, Has_Room2 t WHERE h.RoomType = t.RoomType GROUP BY t.RoomBranchName");
  echo "Viewing highest priced rooms: " .$MinCost;
  echo "<table>";
  echo "<tr><th>Branch</th><th>Cost</th></tr>";

  while ($row = OCI_Fetch_Array($result, OCI_BOTH)) {
    echo "<tr><td>" . $row[0] . "</td><td>" . $row[1] . "</td><tr>";
  }

  echo "</table>";

  OCICommit($db_conn);
}



function handleUpdateRequest()
{
  global $db_conn;

  $RoomNumber     = filter_var($_POST["UpdateRoomNumber"], FILTER_VALIDATE_INT);
  $RoomBranchName = $_POST["UpdateRoomBranchName"];

  if (isset($_POST['UpdateYesCleaned'])) { //if 'yes' is pressed 
    executePlainSQL("UPDATE Has_Room2 SET Cleaned='yes' WHERE RoomNumber='" . $RoomNumber . "' AND RoomBranchName='" . $RoomBranchName . "'");
  } else if (isset($_POST['UpdateNoCleaned'])) { //if 'no' is pressed
    executePlainSQL("UPDATE Has_Room2 SET Cleaned='no' WHERE RoomNumber='" . $RoomNumber . "' AND RoomBranchName='" . $RoomBranchName . "'");
  } else {
    echo "No changes made";
  }

  OCICommit($db_conn);
  echo "Updated cleaned status of room " . $RoomNumber . " at " . $RoomBranchName;
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

if (isset($_POST['deleteSubmit']) || isset($_POST['viewSubmit']) || isset($_POST['updateSubmit']) || isset($_POST['view2Submit']) || isset($_POST['countSubmit']) || isset($_POST['insertSubmit'])) {
  handlePOSTRequest();
} else if (isset($_GET['countTupleRequest'])) {
  handleGETRequest();
}

function handlePOSTRequest()
{

  if (connectToDB()) {
    if (array_key_exists('insertQueryRequest', $_POST)) {
      handleInsertRequest();
    } else if (array_key_exists('updateQueryRequest', $_POST)) {
      handleUpdateRequest();
    } else if (array_key_exists('deleteQueryRequest', $_POST)) {
      handleDeleteRequest();
    } else if (array_key_exists('viewQueryRequest', $_POST)) {
      handleViewRequest();
    }else if (array_key_exists('viewAllQueryRequest', $_POST)) {
      handleViewAllRequest();
    } else if (array_key_exists('countQueryRequest', $_POST)) {
      handleCountRequest();
    }
  } else {
    disconnectFromDB();
  }
}
?>

</html>