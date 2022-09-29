drop table Uses; 
drop table Facilities;
drop table Orders_RoomService;  
drop table Makes; 
drop table Reserves; 
drop table Requests; 
drop table Cleans; 
drop table Receptionist; 
drop table HouseKeeper;
drop table ParksAt_ParkingSpot;  
drop table Employee_WorksAt; 
drop table Books_Reservation;
drop table has_Room2; 
drop table has_Room1; 
drop table Branch2; 
drop table Branch1; 
drop table Guest; 

CREATE TABLE Facilities(
	TypeName char(20) PRIMARY KEY, 
	Cost int
);

grant select on Facilities to public; 

CREATE TABLE Guest(
	CustomerID int PRIMARY KEY,
	GuestAddress char(40) NOT NULL,
	FirstName char(40) NOT NULL,
	LastName char(40) NOT NULL,
	Email char(40) UNIQUE
);

grant select on Guest to public; 

CREATE TABLE Uses(
	FacilityTypeName char(20), 
	UsesCustomerID int NOT NULL, 
	PRIMARY KEY (FacilityTypeName, UsesCustomerID), 
    FOREIGN KEY (FacilityTypeName) REFERENCES Facilities(TypeName), 
	FOREIGN KEY (UsesCustomerID) REFERENCES Guest(CustomerID)
	ON DELETE CASCADE
);

grant select on Uses to public; 

CREATE TABLE Orders_RoomService(
	OrderNumber int PRIMARY KEY,  
	Cost int, 
	DeliveryTime char(15), 
	OrdersCustomerID int NOT NULL,
	FOREIGN KEY (OrdersCustomerID) REFERENCES Guest(CustomerID)
); 

grant select on Orders_RoomService to public; 

CREATE TABLE Branch1 (
	BranchNumber int PRIMARY KEY, 
	BranchAddress char(50)
); 

grant select on Branch1 to public;

CREATE TABLE Branch2 (
	BranchName char(40) PRIMARY KEY, 
	BranchNumber int,
	FOREIGN KEY (BranchNumber) REFERENCES Branch1(BranchNumber)
); 

grant select on Branch2 to public;

CREATE TABLE Has_Room1(
	RoomType char(40),
	Cost int,
	PRIMARY KEY (RoomType)
); 

grant select on Has_Room1 to public; 

CREATE TABLE Has_Room2(
	RoomNumber int,
	RoomBranchName char(40), 
	RoomType char(40),
	Cleaned char(20),
	PRIMARY KEY (RoomNumber, RoomBranchName), 
	FOREIGN KEY (RoomBranchName) REFERENCES Branch2(BranchName)
	ON DELETE CASCADE,
	FOREIGN KEY (RoomType) REFERENCES Has_Room1(RoomType)  
); 

grant select on Has_Room2 to public; 

CREATE TABLE Requests( 
	RequestRoomNumber int,
	RequestBranchName char(40), 
    RequestsCustomerID int,  
	PRIMARY KEY(RequestRoomNumber, RequestBranchName, RequestsCustomerID), 
    FOREIGN KEY (RequestRoomNumber, RequestBranchName) references Has_Room2(RoomNumber, RoomBranchName) ON DELETE CASCADE,
    FOREIGN KEY (RequestsCustomerID) references Guest(CustomerID) ON DELETE CASCADE
); 

grant select on Requests to public; 

CREATE TABLE Books_Reservation (
	ConfirmationNumber int PRIMARY KEY, 
	BooksCustomerID int NOT NULL, 
	StartDate char(20), 
	EndDate char(20), 
	FOREIGN KEY (BooksCustomerID) REFERENCES Guest(CustomerID)
 	ON DELETE CASCADE 
); 

grant select on Books_Reservation to public; 

CREATE TABLE Employee_WorksAt (
	EmployeeID int PRIMARY KEY, 
	WorksBranchName char (40) NOT NULL, 
	SINNumber int UNIQUE, 
	EmployeeName char(20) NOT NULL, 
	EmploymentYears int, 
	FOREIGN KEY (WorksBranchName) REFERENCES Branch2(BranchName) 
	ON DELETE CASCADE
);

grant select on Employee_WorksAt to public; 

CREATE TABLE Receptionist (
	EmployeeID int PRIMARY KEY,
    FOREIGN KEY (EmployeeID) REFERENCES Employee_WorksAt(EmployeeID) ON DELETE CASCADE
); 

grant select on Receptionist to public; 

CREATE TABLE Housekeeper ( 
	EmployeeID int PRIMARY KEY,
	FOREIGN KEY (EmployeeID) REFERENCES Employee_WorksAt(EmployeeID) ON DELETE CASCADE
); 

grant select on HouseKeeper to public; 

CREATE TABLE Makes ( 
	MakesConfirmationNumber int, 
	MakesEmployeeID int, 
	PRIMARY KEY(MakesConfirmationNumber, MakesEmployeeID), 
	FOREIGN KEY (MakesConfirmationNumber) REFERENCES Books_Reservation(ConfirmationNumber) 
	ON DELETE CASCADE, 
	FOREIGN KEY(MakesEmployeeID) REFERENCES Receptionist(EmployeeID)
); 

grant select on Makes to public;

CREATE TABLE Cleans (
	EmployeeID int, 
	RoomNumber int,
	BranchName char(40), 
	PRIMARY KEY (EmployeeID, RoomNumber, BranchName), 
	FOREIGN KEY (EmployeeID) REFERENCES Housekeeper(EmployeeID), 
	FOREIGN KEY (RoomNumber, BranchName) REFERENCES Has_Room2(RoomNumber, RoomBranchName)
); 

grant select on Cleans to public; 

CREATE TABLE Reserves (
	RoomNumber int, 
	BranchName char(40), 
	ConfirmationNumber int, 
	PRIMARY KEY (RoomNumber, BranchName, confirmationNumber),
	FOREIGN KEY (RoomNumber, BranchName) REFERENCES Has_Room2(RoomNumber, RoomBranchName) ON DELETE CASCADE, 
	FOREIGN KEY (ConfirmationNumber) REFERENCES Books_Reservation(ConfirmationNumber)
); 

grant select on Reserves to public; 

CREATE TABLE ParksAt_ParkingSpot(
	ParkingNumber int PRIMARY KEY, 
    Occupied int, --1 = occupied, 0 = unoccupied 
	Cost int, --cost/day (rate)
    CustomerID int, 
    FOREIGN KEY (CustomerID) REFERENCES Guest(CustomerID)
);

grant select on ParksAt_ParkingSpot to public; 

INSERT INTO Guest
VALUES ('12', '7800 Manor Station, Greenville, NC 27834', 'Theia', 'Brown', 'Theia.Brown@gmail.com');

INSERT INTO Guest
VALUES ('34', '5 Smith Avenue Providence, RI 02904', 'Chase', 'Johnson', 'Chase.Johnson@gmail.com');

INSERT INTO Guest
VALUES ('56', '12 Court Circle Ridgecrest, CA 93555', ' Marcus', 'Oneal' , 'Marcus.Oneal@gmail.com');

INSERT INTO Guest
VALUES ('78', '731 Holly Str Ossining, NY 10562' , 'Rubie' , 'Blackmore', 'Rubie.Blackmore@gmail.com');

INSERT INTO Guest
VALUES ('91' ,'9439 Rock Maple St Anchorage, AK 99504', 'Kaitlin', 'Shaw', 'Kaitlin.Shaw@gmail.com');

INSERT INTO Branch1
VALUES ('2', '123-944 Best Street');

INSERT INTO Branch1
VALUES ('33', '0394 Orange Drive');

INSERT INTO Branch1
VALUES ('4', '02937-34 King Street');

INSERT INTO Branch1
VALUES  ('55', '028 Hunter Drive');

INSERT INTO Branch1
VALUES ('3', '0933 Russell Street'); 

INSERT INTO Branch2
VALUES ('Hilton', '2');

INSERT INTO Branch2
VALUES ('Marriott', '33');

INSERT INTO Branch2
VALUES ('Shelton', '4');

INSERT INTO Branch2
VALUES ('Motel 8', '55');

INSERT INTO Branch2
VALUES ('Best Western', '3'); 

INSERT INTO Has_Room1 
VALUES ('Standard Double', '400');

INSERT INTO Has_Room1 
VALUES ('Standard Suite', '800');

INSERT INTO Has_Room1 
VALUES ('Deluxe Double', '400');

INSERT INTO Has_Room1 
VALUES ('Deluxe Suite', '1000');

INSERT INTO Has_Room1 
VALUES ('Presidential Suite', '3000'); 

INSERT INTO Has_Room2 
VALUES ('100', 'Hilton', 'Standard Double', 'Yes');

INSERT INTO Has_Room2 
VALUES ('200', 'Marriott', 'Standard Suite', 'No'); 

INSERT INTO Has_Room2 
VALUES ('300', 'Shelton', 'Deluxe Double', 'Yes'); 

INSERT INTO Has_Room2 
VALUES ('400', 'Motel 8', 'Deluxe Suite', 'Yes');

INSERT INTO Has_Room2 
VALUES ('500', 'Best Western', 'Presidential Suite', 'No'); 

INSERT INTO Books_Reservation
VALUES ('551283', '12', 'Sept 8, 2020', 'Sept 9, 2020');

INSERT INTO Books_Reservation
VALUES ('488291', '34', 'Aug 23, 2020', 'Aug 25 2020');

INSERT INTO Books_Reservation
VALUES ('449103', '56', 'Jan 15, 2020', 'Jan 20, 2020'); 

INSERT INTO Books_Reservation
VALUES ('100482', '78', 'July 3, 2020', 'July 10, 2020'); 

INSERT INTO Books_Reservation
VALUES ('110294', '91', 'Jan 1, 2020', 'Jan 8, 2020');

INSERT INTO Employee_WorksAt 
VALUES ('1103', 'Hilton', '123456789', 'Rikki Haynes', '1'); 

INSERT INTO Employee_WorksAt 
VALUES ('2774', 'Hilton', '998877665', 'Betty Faulkner', '2'); 

INSERT INTO Employee_WorksAt 
VALUES ('1234', 'Marriott', '334652789', 'Steaphanie Philip', '5'); 

INSERT INTO Employee_WorksAt 
VALUES ('2246', 'Shelton', '620048245', 'Wilfred Dunn', '6');

INSERT INTO Employee_WorksAt 
VALUES ('8736', 'Motel 8', '960334909', 'Billy Herman', '8'); 

INSERT INTO Employee_WorksAt 
VALUES ('5521', 'Best Western', '203498394', 'Ellen Parker', '3'); 

INSERT INTO ParksAt_ParkingSpot
VALUES ('111234', '1', '10', '12');

INSERT INTO ParksAt_ParkingSpot
VALUES ('323556', '1', '15', '34');

INSERT INTO ParksAt_ParkingSpot
VALUES ('125523' , '1', '40', '56');

INSERT INTO ParksAt_ParkingSpot
VALUES ('243356', '0', '20', '78');

INSERT INTO ParksAt_ParkingSpot
VALUES ('664578', '0', '30', '91');

INSERT INTO Housekeeper 
VALUES ('5521');

INSERT INTO Housekeeper 
VALUES('2774'); 

INSERT INTO Housekeeper 
VALUES('1234'); 

INSERT INTO Housekeeper 
VALUES('2246'); 

INSERT INTO Housekeeper 
VALUES('8736'); 

INSERT INTO Receptionist 
VALUES ('1103');

INSERT INTO Receptionist 
VALUES ('2774'); 

INSERT INTO Receptionist 
VALUES ('1234'); 

INSERT INTO Receptionist 
VALUES ('2246'); 

INSERT INTO Receptionist 
VALUES ('8736'); 

INSERT INTO Cleans
VALUES ('5521', '500', 'Best Western'); 

INSERT INTO Cleans
VALUES ('2774', '100', 'Hilton'); 

INSERT INTO Cleans
VALUES ('1234', '200', 'Marriott'); 

INSERT INTO Cleans
VALUES ('2246', '300', 'Shelton'); 

INSERT INTO Cleans
VALUES ('8736', '400', 'Motel 8'); 

INSERT INTO Requests 
VALUES ('100', 'Hilton', '12');

INSERT INTO Requests 
VALUES ('200', 'Marriott', '34');

INSERT INTO Requests 
VALUES ('300', 'Shelton', '56'); 

INSERT INTO Requests 
VALUES ('400', 'Motel 8', '78'); 

INSERT INTO Requests 
VALUES ('500', 'Best Western', '91'); 

INSERT INTO Reserves
VALUES ('100', 'Hilton', '551283');

INSERT INTO Reserves
VALUES ('200', 'Marriott', '488291');

INSERT INTO Reserves
VALUES ('300', 'Shelton', '449103');

INSERT INTO Reserves
VALUES ('400', 'Motel 8', '100482');

INSERT INTO Reserves
VALUES ('500', 'Best Western' , '110294');

INSERT INTO Makes
VALUES ('551283', '1103');

INSERT INTO Makes
VALUES ('488291', '2774');

INSERT INTO Makes
VALUES ('449103', '1234');

INSERT INTO Makes
VALUES ('100482', '2246');

INSERT INTO Makes
VALUES ('110294', '8736');  

INSERT INTO Orders_RoomService 
VALUES ('1442', '20', '4:00 PM', '12');

INSERT INTO Orders_RoomService 
VALUES ('1844', '15', '1:00 PM', '34');

INSERT INTO Orders_RoomService 
VALUES ('8003', '44', '1:30 AM', '56');

INSERT INTO Orders_RoomService 
VALUES ('9553', '102', '2:30 PM', '78');

INSERT INTO Orders_RoomService 
VALUES ('5773', '85', '5:50 PM', '91'); 

INSERT INTO Facilities
VALUES ('Swimming Pool', '0');

INSERT INTO Facilities
VALUES ('Spa', '30'); 

INSERT INTO Facilities
VALUES ('Gym','0'); 

INSERT INTO Facilities
VALUES ('Sauna', '0'); 

INSERT INTO Facilities
VALUES ('Conference Room', '0'); 

INSERT INTO USES 
VALUES ('Conference Room', '34');

INSERT INTO USES 
VALUES ('Spa', '56');

INSERT INTO USES 
VALUES ('Spa', '78');

INSERT INTO USES 
VALUES ('Gym', '91');


-- This is to test the Division in facilities.php
INSERT INTO USES 
VALUES ('Swimming Pool', '12');

INSERT INTO USES 
VALUES ('Conference Room', '12');

INSERT INTO USES 
VALUES ('Spa', '12');

INSERT INTO USES 
VALUES ('Gym', '12');

INSERT INTO USES
VALUES ('Sauna', '12'); 
