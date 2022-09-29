<!DOCTYPE html>
<html>

<head>
       <title>Guests</title>
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

       <h2>Guests</h2>
       Use this page to add guests to the database or view their information.

       <hr />
       <h2>View All Guest Information</h2>

       <form method="POST" action="guests.php">
              <p>Select to view lists of:</p>
              <input type="checkbox" id="viewQueryRequest" name="viewGuestAddress">
              <label for="viewQueryRequest"> Guest Addresses</label><br>
              <input type="checkbox" id="viewQueryRequest" name="viewFirstName">
              <label for="viewQueryRequest"> Guest First Names</label><br>
              <input type="checkbox" id="viewQueryRequest" name="viewLastName">
              <label for="viewQueryRequest"> Guest Last Names</label><br>
              <input type="checkbox" id="viewQueryRequest" name="viewGuestEmail">
              <label for="viewQueryRequest"> Guest Emails</label><br><br>

              <input type="submit" value="view" name="viewSubmit"></p>
       </form>

       <hr />

       <h2>Add Guest</h2>

       Enter the guest's information.<br>

       <form method="POST" action="guests.php">

              <input type="hidden" id="insertQueryRequest" name="insertQueryRequest">
              Customer ID: <input type="text" name="insertCustomerID"> <br />
              First Name: <input type="text" name="insertFirstName"> <br />
              Last Name: <input type="text" name="insertLastName"> <br />
              Address: <input type="text" name="insertAddress"> <br />
              Email: <input type="text" name="insertEmail"> <br /><br>

              <input type="submit" value="Insert" name="insertSubmit">
       </form>

       <hr />

       <h2>Delete Guest</h2>

       Enter the guest's identification information.<br>

       <form method="POST" action="guests.php">
              <!start of form>
                     <!customerID Text Input>
                            <input type="hidden" id="deleteQueryRequest" name="deleteQueryRequest">
                            Customer ID: <input type="text" name="deleteCustomerID"> <br />

                            <input type="submit" value="delete" name="deleteSubmit">
       </form>
       <!end of form>

              <hr />

              <h2>Count Guests</h2>

              Count total number of Guests <br><br>

              <form method="POST" action="guests.php">
                     <!start of form>
                            <!customerID Text Input>

                                   <input type="submit" value="view" name="viewSubmit2">
              </form>
              <!end of form>

                     <hr />

                     <h2> Results </h2>

</body>

<?php
$db_conn = NULL;

function handleViewRequest()
{
       global $db_conn;

       if (isset($_POST["viewGuestAddress"])) {
              $result = executePlainSQL("select GuestAddress from Guest");
              
              echo "<table>";
              echo "<tr><th>Guest Addresses</th></tr>";
              while ($row = OCI_Fetch_Array($result, OCI_BOTH)) {
                     echo "<tr><td>" . $row["GUESTADDRESS"] . "</td><tr>"; //or just use "echo $row[0]"
              };
       };

       if (isset($_POST["viewFirstName"])) {
              $result1 = executePlainSQL("select FirstName from Guest");
              echo "<table>";
              echo "<tr><th>First Names</th></tr>";
              while ($row = OCI_Fetch_Array($result1, OCI_BOTH)) {
                     echo "<tr><td>" . $row["FIRSTNAME"] . "</td><tr>";
              };
       };

       if (isset($_POST["viewLastName"])) {
              $result2 = executePlainSQL("select LastName from Guest");
              echo "<table>";
              echo "<tr><th>Last Names</th></tr>";
              while ($row = OCI_Fetch_Array($result2, OCI_BOTH)) {
                     echo "<tr><td>" . $row["LASTNAME"] . "</td><tr>";
              };
       };

       if (isset($_POST["viewGuestEmail"])) {
              $result3 = executePlainSQL("select Email from Guest");
              echo "<table>";
              echo "<tr><th>Emails</th></tr>";
              while ($row = OCI_Fetch_Array($result3, OCI_BOTH)) {
                     echo "<tr><td>" . $row["EMAIL"] . "</td><tr>";
              };
       };

       OCICommit($db_conn);
}


function handleInsertRequest()
{
       global $db_conn;

       $firstName = $_POST["insertFirstName"];
       $lastName = $_POST["insertLastName"];

       //customerid, address, firstname, lastname, email 
       $tuple = array(
              ":element2" => $_POST["insertCustomerID"],
              ":element1" => $_POST["insertAddress"],
              ":element3" => $_POST["insertFirstName"],
              ":element4" => $_POST["insertLastName"],
              ":element5" => $_POST["insertEmail"]
       );

       $alltuples = array(
              $tuple
       );

       executeBoundSQL("insert into Guest values (:element2, :element1, :element3, :element4, :element5)", $alltuples);
       OCICommit($db_conn);
       echo "Added " . $firstName . " " . $lastName . " to system";
}

function handleDeleteRequest()
{
       global $db_conn;

       $deletedCustomerID = $_POST["deleteCustomerID"];

       executePLAINSQL("DELETE FROM Guest WHERE CustomerID='" . $deletedCustomerID . "'");
       OCICommit($db_conn);
       echo "Deleted customer with customerID " . $deletedCustomerID . " from system";
}

function handleCountRequest()
{
       global $db_conn;

       $result = executePlainSQL("SELECT COUNT(*) FROM Guest");
       echo "<table>";
       echo "<tr><th>Total Number of Guests in the Database is </th><th>";

       while ($row = OCI_Fetch_Array($result, OCI_BOTH)) {
              echo $row[0];
       };

       echo "</table>";

       OCICommit($db_conn);
}


function handleCountRequest2()
{
       global $db_conn;

       $result = executePlainSQL("SELECT COUNT(*), r.CustomerID FROM Orders_RoomService r GROUP BY r.DeliveryTime HAVING AVG(r.Cost) > (SELECT AVG(Cost) FROM Orders_RoomService)");
       echo "<table>";
       echo "<tr><th>Total Number of Guests in the Database is </th><th>";

       while ($row = OCI_Fetch_Array($result, OCI_BOTH)) {
              echo $row[0];
       };

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

if (isset($_POST['viewSubmit']) || isset($_POST['insertSubmit']) || isset($_POST['deleteSubmit']) || isset($_POST['viewSubmit2'])) {
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
              } else if (isset($_POST['viewSubmit'])) {
                     handleViewRequest();
              } else if (isset($_POST['viewSubmit2'])) {
                     handleCountRequest();
              }
       } else {
              disconnectFromDB();
       }
}


?>

</html>