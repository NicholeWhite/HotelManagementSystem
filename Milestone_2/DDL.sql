CREATE TABLE Facilities(
    TypeName char(20) PRIMARY KEY, 
    Cost int
);

INSERT INTO Facilities
VALUES 
(‘Swimming Pool’, ‘0’),
(‘Spa’, ‘30’),
(‘Gym’,’0’), 
(‘Sauna’, ‘0’),
(‘Conference Room’, ‘0’); 


CREATE TABLE Uses(
    TypeName char(20), 
    CustomerID int, 
    PRIMARY KEY (TypeName, CustomerID), 
FOREIGN KEY (TypeName) REFERENCES Facilities(TypeName), 
    FOREIGN KEY (CustomerID) REFERENCES Guest(CustomerID) ON DELETE CASCADE 
);

INSERT INTO USES 
VALUES 
(‘Steam Room’, ‘12’),
(‘Pool locker’, ‘34’),
(‘Massage’, ‘56’),
(‘Facial Massage’, ‘78’),
(‘Equipment Rental’, ‘91’);

CREATE TABLE Orders_RoomService (
    OrderNumber int PRIMARY KEY,  
    Cost int; 
    DeliveryTime char(15)
    CustomerID int,
    FOREIGN KEY (CustomerID) REFERENCES Guest(CustomerID)
); 

INSERT INTO Orders_RoomService 
VALUES 
(‘1442’, ‘20’, ‘4:00 PM’, ‘12’),
(‘1844’, ‘15’, ‘1:00 PM’, ‘34’),
(‘8003’, ‘44’, ‘1:30 AM’, ‘56’), 
(‘9553’, ‘102’, ‘2:30 PM’, ‘78’), 
(‘5773’, ‘85’, ‘5:50 PM’, ‘91’); 


CREATE TABLE Requests( 
    Number int,
    BranchName char(40), 
    CustomerID int,  
    PRIMARY KEY(Number, BranchName, CustomerID), 
    FOREIGN KEY (BranchName) references Branch(BranchName) ON UPDATE CASCADE ON DELETE CASCADE,
    FOREIGN KEY (Number) references Has_Room(Number) ON UPDATE CASCADE ON DELETE CASCADE,
    FOREIGN KEY (CustomerID) references Guest(CustomerID) ON UPDATE CASCADE ON DELETE CASCADE
); 

INSERT INTO Requests 
VALUES 
(‘100’, ‘Hilton’, ‘12’),
(‘300’, ‘Marriott’, ‘34’),
(‘100’, ‘Shelton’, ‘56’), 
(‘500’, ‘Motel 8’, ‘78’), 
(‘300’, ‘Best Western’, ‘91’); 

CREATE TABLE Branch1 (
    BranchNumer int PRIMARY KEY, 
    Address char(50)
); 

INSERT INTO Branch1
VALUES 
(‘2’, ‘123-944 Best Street’),
(‘33’, ‘0394 Orange Drive’), 
(‘4’, ‘02937-34 King Street’), 
(‘55’, ‘028 Hunter Drive’),
(‘3’, ‘0933 Russell Street’); 

CREATE TABLE Branch2 (
    BranchName char(40) PRIMARY KEY, 
    BranchNumber int
); 

INSERT INTO Branch2
VALUES 
(‘Hilton’, ‘2’),
(‘Marriott’, ‘33’), 
(‘Shelton’, ‘4’), 
(‘Motel 8’, ‘55’),
(‘Best Western’, ‘3’); 

CREATE TABLE Books_Reservation (
    ConfirmationNumber int PRIMARY KEY, 
    CustomerID int, 
    StartDate int, 
    EndDate int, 
    FOREIGN KEY (CustomerID) REFERENCES Guest(CustomerID) ON UPDATE CASCADE ON DELETE CASCADE 
); 

INSERT INTO Books_Reservation
VALUES 
(‘551283’, ‘12’, ‘Sept 8, 2020’, ‘Sept 9, 2020’), 
(‘488291’, ‘34’, ‘Aug 23, 2020’, ‘Aug 25 2020’), 
(‘449103’, ‘56’, ‘Jan 15, 2020’, ‘Jan 20, 2020’), 
(‘100482’, ‘78’, ‘July 3, 2020’, ‘July 10, 2020’); 

CREATE TABLE Makes ( 
    ConfirmationNumber int, 
    EmployeeID int, 
    PRIMARY KEY(ConfirmationNumber, EmployeeID), 
    FOREIGN KEY (ConfirmationNumber) REFERENCES Books_Reservation(confirmationNumber) ON UPDATE CASCADE ON DELETE CASCADE, 
    FOREIGN KEY(EmployeeID) REFERENCES Receptionist(EmployeeID)
)

INSERT INTO Makes
VALUES 
(‘551283’, ‘1103’), 
(‘488291’, ‘2774’), 
(‘449103’, ‘1234’), 
(‘100482’, ‘2246’), 
(‘110294’, ‘8736’);  

CREATE TABLE Cleans (
    EmployeeID int, 
    Number int,
    BranchName char(40), 
    PRIMARY KEY (EmployeeID, Number, BranchName), 
    FOREIGN KEY (EmployeeID) REFERENCES Housekeeper(EmployeeID), 
    FOREIGN KEY (Number) REFERENCES Has_Room(Number),
    FOREIGN KEY (BranchName) REFERENCES Branch(BranchName) 
); 

INSERT INTO Cleans
VALUES 
(‘1103’, ‘100’, ‘Hilton’), 
(‘5521’, ‘300’, ‘Marriott’), 
(‘7328’, ‘300’, ‘Shelton’), 
(‘5521’, ‘400’, ‘Motel 8’), 
(‘5521’, ‘100’, ‘Best Western); 

CREATE TABLE Employee_WorksAt (
    EmployeeID int PRIMARY KEY, 
    BranchName char (50) NOT NULL, 
    SIN int UNIQUE, 
    Name char(20) NOT NULL, 
    FOREIGN KEY (BranchName) REFERENCES Branch(BranchName) ON UPDATE CASCADE ON DELETE CASCADE
);

INSERT INTO Employee_WorksAt 
VALUES 
(‘1103’, ‘123456789’, ‘Rikki Haynes’), 
(‘2774’, ‘998877665’, ‘Betty Faulkner’), 
(‘1234’, ‘334652789’, ‘Steaphanie Philip’), 
(‘2246’, ‘620048245’, ‘Wilfred Dunn’), 
(‘8736’, ‘960334909’, ‘Billy Herman’); 

CREATE TABLE Receptionist (
    EmployeeID int PRIMARY KEY,
    FOREIGN KEY (EmployeeID) REFERENCES Employee(EmployeeID)
); 

INSERT INTO Receptionist 
VALUES 
(‘1103’), 
(‘2774’), 
(‘1234’), 
(‘2246’), 
(‘8736’); 

CREATE TABLE Housekeeper ( 
    EmployeeID int PRIMARY KEY,
    FOREIGN KEY (EmployeeID) REFERENCES Employee(EmployeeID)
); 

INSERT INTO Housekeeper 
VALUES 
(‘5521’), 
(‘2774’), 
(‘1234’), 
(‘2246’), 
(‘8736’); 

CREATE TABLE Has_Room1(
    Type char(40),
    Cost int,
    PRIMARY KEY (Type)
); 

INSERT INTO Has_Room1 
VALUES 
(‘Standard Double’, ‘400’), 
(‘Standard Suite’, ‘800’), 
(‘Standard Double’, ‘400’,), 
(‘Deluxe Suite’, ‘1000’,), 
(‘Presidential Suite’, ‘3000’); 

CREATE TABLE Has_Room2(
    Number int,
    BranchName char(40), 
    Type char(40),
    Cleaned char(20),
    PRIMARY KEY (Number, BranchName), 
    FOREIGN KEY (BranchName) REFERENCES Branch(BranchName) ON UPDATE CASCADE ON DELETE CASCADE
); 

INSERT INTO Has_Room2 
VALUES 
(‘100’, ‘Hilton’, ‘Yes’), 
(‘200’, ‘Marriott’, ‘No’), 
(‘300’, ‘Shelton’, ‘Yes’), 
(‘400’, ‘Motel 8’, ‘Yes’), 
(‘500’, ‘Best Western’, ‘No’); 

CREATE TABLE Reserves (
    Number int, 
    BranchName char(40), 
    ConfirmationNumber int, 
    PRIMARY KEY (number, branchName, confirmationNumber) 
    FOREIGN KEY (Number) REFERENCES Has_Room(Number), 
    FOREIGN KEY (BranchName) REFERENCES Branch(BranchName) ON DELETE CASCADE ON UPDATE CASCADE, 
    FOREIGN KEY (ConfirmationNumber) REFERENCES Books_Reservation(ConfirmationNumber)
); 

INSERT INTO Reserves
VALUES 
(‘100’, ‘Hilton’, ‘551283’),
(‘300’, ‘Marriott’, ‘488291’),
(’100’, ’Shelton’, ’449103’),
(‘500’, ‘Motel 8’, ‘100482’),
(’300’, ’Best Western’ , ’110294’);

CREATE TABLE ParksAt_ParkingSpot(
    ParkingNumber int PRIMARY KEY,
    Location char(40),
    Cost int, 
    CustomerID int, 
    FOREIGN KEY (CustomerID) REFERENCES Guest(CustomerID)
);

INSERT INTO ParksAt_ParkingSpot
VALUES 
(‘111234’, ‘Hilton Parkade’, ‘5’, ‘12’),
(‘323556’, ‘Marriott Parkade’, ‘3’, ’34’),
(‘125523’ , ’Shelton Parkade’, ‘15’, ‘56’),
(‘243356’, ‘Motel 8 Lot 2’, ‘23’, ‘78’),
(‘664578’, ‘Best Western Lot 4’, ‘4’, ‘91’);

CREATE TABLE Guest(
    CustomerID int PRIMARY KEY
    Address char(40) NOT NULL,
    FirstName char(40) NOT NULL,
    LastName char(40) NOT NULL,
    Email char(40) UNIQUE
);

INSERT INTO Guest
VALUES 
(‘12’, ’7800 Manor Station, Greenville, NC 27834’, ‘Theia’, ‘Brown,Theia.Brown@gmail.com’),
(‘34’, ’5 Smith Avenue Providence, RI 02904’, ‘Chase’, ‘Johnson’, ‘Chase.Johnson@gmail.com’),
(‘56’, ’12 Court Circle Ridgecrest, CA 93555’, ’ Marcus’, ’Oneal’ , ’Marcus.Oneal@gmail.com’),
(‘78’, ’731 Holly Str Ossining, NY 10562’ , ’Rubie’ , ‘Blackmore’, ’Rubie.Blackmore@gmail.com’),
(‘91’ ,’9439 Rock Maple St Anchorage, AK 99504’, ‘Kaitlin’, ‘Shaw’, ’Kaitlin.Shaw@gmail.com’);
