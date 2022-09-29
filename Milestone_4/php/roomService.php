<html>


<head>
    <title>Hotel Management System</title>

    <head>
        <title>Hotel Management System</title>
        <link rel="stylesheet" href="style.css">

    </head>

</head>

<body>

    <!-- <form method="POST" action="oracle-test.php"> -->
    <!-- if you want another page to load after the button is clicked, you have to specify that page in the action parameter
            <input type="hidden" id="resetTablesRequest" name="resetTablesRequest">
            <p><input type="submit" value="Reset" name="reset"></p>
        </form> -->
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

    <h2>Room Service</h2>

    <hr />

    <h2>View Current Orders</h2>
    <form class="txt" method="POST" action="roomService.php">
        <!--refresh page when submitted-->
        <input type="hidden" id="viewOrdersRequest" name="viewOrdersRequest">
        <input type="submit" value="View" name="viewSubmit"></p>
    </form>
    <hr />


    <h2>Add Room Service Order To System</h2>
    <form class="txt" method="POST" action="roomService.php">
        <!--refresh page when submitted-->
        <input type="hidden" id="insertOrderRequest" name="insertOrderRequest">
        Order Number: <input type="text" name="orderNo"><br /><br />
        Cost: <input type="number" id="quantity" name="quantity" min="0" max="200" step=".01" value="10"> <br /><br />
        Delivery Time: <input type="time" id="deliveryTime" name="deliveryTime"> <br /><br />
        Customer ID: <input type="text" name="customerID"><br /><br />
        <input type="submit" value="Add" name="insertSubmit"></p>
    </form>

    <hr />

    <h2>Delete Room Service Order</h2>
    <form method="POST" action="roomService.php">
        <!--refresh page when submitted-->
        <input type="hidden" id="deleteQueryRequest" name="deleteQueryRequest">
        <!-- https://stackoverflow.com/questions/8022353/how-to-populate-html-dropdown-list-with-values-from-database -->
        Order Number: <input type="text" name="orderNumber"> <br /><br />
        <input type="submit" value="Delete" name="deleteSubmit"></p>
    </form>

    <hr />

    <h2>Results</h2>

    <?php
    $db_conn = NULL;
    $success = FALSE;
    function printResult($result)
    { //prints results from a select statement
        echo "<br>Retrieved data from table Orders_RoomService:<br>";
        echo "<table>";
        echo "<tr><th>Order Number</th><th>Cost</th><th>Delivery Time</th><th>Customer ID</th></tr>";

        while ($row = OCI_Fetch_Array($result, OCI_BOTH)) {
            echo "<tr><td>" . $row[0] . "</td><td>" . $row[1] . "</td><td>" . $row[2] . "</td><td>" . $row[3] . "</td><tr>"; //or just use "echo $row[0]"
        }

        echo "</table>";
    }


    function handleViewRequest()
    {
        global $db_conn;

        $result = executePlainSQL("SELECT * FROM  Orders_RoomService");
        printResult($result);

        OCICommit($db_conn);
    }

    function handleDeleteRequest()
    { //working!
        global $db_conn;

        $orderNumber = filter_var($_POST["orderNumber"], FILTER_VALIDATE_INT); //parking number 

        if (isset($_POST['orderNumber'])) { //if occupied is pressed 
            executePlainSQL("DELETE FROM Orders_RoomService WHERE orderNumber='" . $orderNumber . "'");
            //   DELETE FROM table_name WHERE condition
        } else {
            echo "no changes made";
        }

        OCICommit($db_conn);
        echo "Deleted order number: " . $orderNumber;
    }

    function handleInsertRequest()
    {
        global $db_conn;

        // <input type="hidden" id="insertOrderRequest" name="insertOrderRequest">
        //     Order Number: <input type="text" name="orderNo"><br /><br />
        //     Cost: <input type="number" id="quantity" name="quantity" min="0" max="200" step=".01" value="10">  <br /><br />
        //     Delivery Time: <input type="time" id="deliveryTime" name="deliveryTime">  <br /><br />
        //     Customer ID: <input type="text" name="customerID"><br /><br />
        //     <input type="submit" value="Add" name="insertSubmit"></p>

        $orderNumber = $_POST["orderNo"];

        $tuple = array(
            ":element2" => filter_var($_POST["orderNo"], FILTER_VALIDATE_INT), //employeeid
            ":element1" => $_POST["quantity"], //branch
            ":element3" => $_POST["deliveryTime"], //SIN 
            ":element4" => $_POST["customerID"] //employeeName 
        );

        $alltuples = array(
            $tuple
        );

        executeBoundSQL("INSERT INTO Orders_RoomService values (:element2, :element1, :element3, :element4)", $alltuples);
        OCICommit($db_conn);
        echo "Added order number: " . $orderNumber;
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


    if (isset($_POST['viewSubmit']) || isset($_POST['deleteSubmit']) || isset($_POST['insertSubmit'])) {
        handlePOSTRequest();
    }


    function handlePOSTRequest()
    {

        if (connectToDB()) {
            if (array_key_exists('deleteQueryRequest', $_POST)) {
                handleDeleteRequest();
            } else if (isset($_POST['insertSubmit'])) {
                handleInsertRequest();
            } else if (array_key_exists('viewOrdersRequest', $_POST)) {
                handleViewRequest();
            }
        } else {
            disconnectFromDB();
        }
    }



    ?>


</body>

</html>
