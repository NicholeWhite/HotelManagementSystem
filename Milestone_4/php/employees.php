<!DOCTYPE html> 

<html> 

	<head>
		<title> Employees </title> 
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

		<h2> Employees </h2>
		
		<hr> 

		<h2> View Employee Information </h2>

		<form method="POST" action="employees.php">
		<input type="hidden" id="viewQueryRequest" name="viewQueryRequest">
            Employee ID: <input type="text" name="ViewEmployeeID"> <br /><br />
  			<input type="submit" value="View" name="viewSubmit">
		</form>

		<hr>

		<h2> Add Employee </h2>

		<form method="POST" action="employees.php">

		<label for="hotels">Branch Name:</label>
		<select id="InsertEmployeeWorks" name="InsertEmployeeWorks">
  			<option value="Hilton">Hilton</option>
  			<option value="Marriott">Marriott</option>
  			<option value="Shelton">Shelton</option>
  			<option value="Motel 8">Motel 8</option>
  			<option value="Best Western">Best Western</option>
		</select>
  		<br><br>

          <input type="hidden" id="insertQueryRequest" name="insertQueryRequest">
            Employee ID: <input type="text" name="InsertEmployeeID"> <br /><br />
            SIN: <input type="text" name="InsertSIN"> <br /><br />
            Employee Name: <input type="text" name="InsertEmployeeName"> <br /><br />

			  <p>Select employee job title(s):</p>
				<input type="checkbox" id="insertQueryRequest" name="HousekeeperInsert">
				<label for="insertQueryRequest"> Housekeeper</label><br>
				<input type="checkbox" id="insertQueryRequest" name="ReceptionistInsert">
				<label for="insertQueryRequest"> Receptionist</label><br><br>
				
  			<input type="submit" value="Add" name="insertSubmit">
		</form>

		<hr>

		<h2> Delete Employee </h2>

		<form method="POST" action="employees.php">
		<input type="hidden" id="deleteQueryRequest" name="deleteQueryRequest">
            Employee ID: <input type="text" name="DeleteEmployeeID"> <br /><br />
  			<input type="submit" value="Delete" name="deleteSubmit">
		</form>

		<hr>

		<h2>Count Employees 1</h2>

		View the total number of employees with employment years > 5 for each branch <br><br> 

		<form method="POST" action="employees.php"> <!start of form>

		<input type="submit" value="view" name="viewSubmit3">
		</form> <!end of form>

		<hr />

		<h2>Count Employees 2</h2>

		View the total number of employees at each branch where each employee has employment years greater than the average employment years across all branches <br><br>

		<form method="POST" action="employees.php"> <!start of form>

		<input type="submit" value="view" name="viewSubmit2">
		</form> <!end of form>

		<hr />

		<h2> Results </h2> 
		
	</body>


	<?php
		$db_conn = NULL; 

		function handleViewRequest() {
			global $db_conn; 

			$ViewEmployeeID = filter_var($_POST["ViewEmployeeID"], FILTER_VALIDATE_INT);
			$result = executePLAINSQL("SELECT * FROM Employee_WorksAt WHERE EmployeeID='" . $ViewEmployeeID . "'");

			echo "<br>Retrieved data from table Employee_WorksAt:<br>";
			echo "<table>";
			echo "<tr><th>Branch</th><th>Employee ID</th><th>SIN</th><th>Name</th><th>Number of Years Worked</th></tr>";
 
		   while ($row = OCI_Fetch_Array($result, OCI_BOTH)) {
			   echo "<tr><td>" . $row["WORKSBRANCHNAME"] . "</td><td>" . $row["EMPLOYEEID"] . "</td><td>" . $row["SINNUMBER"] . "</td><td>" . $row["EMPLOYEENAME"] . "</td><td>" . $row["EMPLOYMENTYEARS"] . "</td><tr>"; //or just use "echo $row[0]"
		   }; 

		   echo "</table>";
	
			OCICommit($db_conn);  
		}

        function handleInsertRequest() { //insert into EmployeeWorksAt, maybe housekeepr || receptionist 
            global $db_conn; 

			$name = $_POST["InsertEmployeeName"]; 

            $tuple = array (
                ":element2" => filter_var($_POST["InsertEmployeeID"], FILTER_VALIDATE_INT), //employeeid
                ":element1" => $_POST["InsertEmployeeWorks"], //branch
                ":element3" => filter_var($_POST["InsertSIN"], FILTER_VALIDATE_INT), //SIN 
                ":element4" => $_POST["InsertEmployeeName"], //employeeName 
				":element5" => '0'
            ); 

            $alltuples = array (
                $tuple
            ); 
            
                executeBoundSQL("insert into Employee_WorksAt values (:element2, :element1, :element3, :element4, :element5)", $alltuples); 
                OCICommit($db_conn); 
				echo "Welcome ". $name . "!"; 

				if (isset($_POST["HousekeeperInsert"])) {
					$tuple = array (
						":housekeeperElement1" => filter_var($_POST["InsertEmployeeID"], FILTER_VALIDATE_INT)
					);
					
					$alltuples = array (
						$tuple
					); 

					executeBoundSQL("insert into Housekeeper values (:housekeeperElement1)", $alltuples);
					OCICommit($db_conn); 
				}; 

				if (isset($_POST["ReceptionistInsert"])) {
					$tuple = array (
						":receptionistElement1" => filter_var($_POST["InsertEmployeeID"], FILTER_VALIDATE_INT)
					);

					$alltuples = array (
						$tuple
					);

					executeBoundSQL("insert into Receptionist values (:receptionistElement1)", $alltuples); 
					OCICommit($db_conn); 
				}; 
        }

		function handleDeleteRequest() {
			global $db_conn; 

			$DeletedEmployeeID = filter_var($_POST["DeleteEmployeeID"], FILTER_VALIDATE_INT);
			
			executePLAINSQL("DELETE FROM Employee_WorksAt WHERE EmployeeID='" . $DeletedEmployeeID . "'");
			OCICommit($db_conn); 
			echo "deleted employee with EmployeeID " . $DeletedEmployeeID . " from system"; 
		}

		function handleCountRequest() {
			global $db_conn; 

			$result = executePlainSQL("SELECT COUNT(*), E.WorksBranchName FROM Employee_WorksAt E GROUP BY E.WorksBranchName HAVING AVG(E.EmploymentYears) > (SELECT AVG(EmploymentYears) FROM Employee_WorksAt)");
			echo "<table>"; 
			echo "<tr><th>Branch Name</th><th>Number of Employees</th><tr>";

			while ($row = OCI_Fetch_Array($result, OCI_BOTH)) {
				echo "<tr><td>" . $row["WORKSBRANCHNAME"]  . "</td><td>" . $row[0] . "</td><tr>";
			}; 
		
			echo "</table>";

			OCICommit($db_conn); 
		}

		function handleCountRequest2() {
			global $db_conn; 

			$result = executePlainSQL("SELECT COUNT(*), E.WorksBranchName FROM Employee_WorksAt E GROUP BY E.WorksBranchName HAVING AVG(E.EmploymentYears) > '5'");
			echo "<table>"; 
			echo "<tr><th>Branch Name</th><th>Number of Employees</th><tr>";

			while ($row = OCI_Fetch_Array($result, OCI_BOTH)) {
				echo "<tr><td>" . $row["WORKSBRANCHNAME"]  . "</td><td>" . $row[0] . "</td><tr>";
			}; 
		
			echo "</table>";

			OCICommit($db_conn); 
		}

		function connectToDB() {
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

		function disconnectFromDB() {
            global $db_conn;

            debugAlertMessage("Disconnect from Database");
            OCILogoff($db_conn);
        }

		function executePlainSQL($cmdstr) { //takes a plain (no bound variables) SQL command and executes it
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

		function executeBoundSQL($cmdstr, $list) {
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
                    unset ($val); //make sure you do not remove this. Otherwise $val will remain in an array object wrapper which will not be recognized by Oracle as a proper datatype
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

		function handleGETRequest() {
            if (connectToDB()) {
                if (array_key_exists('countTuples', $_GET)) {
                    handleCountRequest();
                }

                disconnectFromDB();
            }
        }

		if (isset($_POST['deleteSubmit']) || isset($_POST['viewSubmit']) || isset($_POST['viewSubmit2']) || isset($_POST['insertSubmit']) || isset($_POST['viewSubmit3'])) {
            handlePOSTRequest();
        } else if (isset($_GET['countTupleRequest'])) {
            handleGETRequest();
        }

			
		function handlePOSTRequest() {
			
			if (connectToDB()) {
                if (array_key_exists('insertQueryRequest', $_POST)) {
					handleInsertRequest(); 
				} else if (array_key_exists('deleteQueryRequest', $_POST)) {
					handleDeleteRequest();
				} else if (array_key_exists('viewQueryRequest', $_POST)) {
					handleViewRequest(); 
				} else if (isset($_POST['viewSubmit2'])) {
					handleCountRequest(); 
				} else if (isset($_POST['viewSubmit3'])) {
					handleCountRequest2(); 
				}
			} else {
				disconnectFromDB();
			}
		}


	?>
</html> 