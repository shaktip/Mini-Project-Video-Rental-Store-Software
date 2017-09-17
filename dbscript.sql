DROP TABLE courier_service;
DROP TABLE courier_order;
DROP TABLE old_items;
DROP TABLE transaction;
DROP TABLE item_details;
DROP TABLE item_master;
DROP TABLE member;
DROP TABLE membership_info;
DROP TABLE employee;
DROP TABLE store;

CREATE TABLE store
(
	store_id SMALLINT PRIMARY KEY auto_increment,
	address VARCHAR(100),
	city VARCHAR(20),
	pin_code INT,
	contact_number CHAR(12),
	status CHAR(1) DEFAULT 'A' CHECK (status IN ('A', 'I')),
	latitude DECIMAL(10,8) ,
	longitude DECIMAL(11,8) 
);


CREATE TABLE employee
(
	empid INT PRIMARY KEY auto_increment,
	emp_name VARCHAR(20),
	contact_number CHAR(12) NOT NULL,
	address VARCHAR(100) NOT NULL,
	password VARCHAR(40) NOT NULL,
	role CHAR(1) CHECK (role IN ('C', 'M', 'S')),	
	store_id SMALLINT REFERENCES store(store_id),
	status CHAR(1) DEFAULT 'A' CHECK (status IN ('A', 'I'))
);

CREATE TABLE membership_info
(
	membership_type CHAR(1) PRIMARY KEY,
	membership_text VARCHAR(20),
	discount_percentage INT,
	no_of_video_cds INT,
	no_of_music_cds INT,
	membership_amount INT
);

INSERT INTO membership_info VALUES('P', 'Platinum', 10, 5, 2, 1500);
INSERT INTO membership_info VALUES('G', 'Gold', 5, 3, 2, 1200);
INSERT INTO membership_info VALUES('S', 'Silver', 0, 2, 1, 1000);

CREATE TABLE member
(
	member_id INT PRIMARY KEY auto_increment,
	user_name VARCHAR(30) UNIQUE,
	password VARCHAR(40) NOT NULL,
	address VARCHAR(100) NOT NULL,
	contact_number CHAR(12) NOT NULL,
	email VARCHAR(50),
	date_of_registration TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
	deposit_amount SMALLINT,
	status CHAR(1) DEFAULT 'A' CHECK (status IN ('A', 'I', 'P')),
	membership_type CHAR(1) REFERENCES membership_info(membership_type)
);

CREATE TABLE item_master
(
	item_id INT PRIMARY KEY auto_increment,
	title VARCHAR(50) NOT NULL,
	language VARCHAR(20) NOT NULL,
	genre VARCHAR(20) NOT NULL,
	date_of_procurement TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
	descr text,
	cover_image VARCHAR(100),
	daily_rent TINYINT NOT NULL CHECK (daily_rent > 0),
	status CHAR(1) DEFAULT 'A' CHECK (status IN ('A', 'I')),
	item_type CHAR(1) DEFAULT 'V' CHECK (item_type IN ('V', 'M'))
);

CREATE TABLE item_details
(
	copy_id INT PRIMARY KEY auto_increment,
	item_id INT REFERENCES item_master(item_id),
	availability BOOLEAN DEFAULT 1,
	store_id SMALLINT REFERENCES store(store_id),
	price INT CHECK (price > 0),
	status CHAR(1) DEFAULT 'A' CHECK (status IN ('I','A')),
	purchased_on TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE transaction
(
	trans_id INT PRIMARY KEY auto_increment,
	copy_id INT REFERENCES item_details(copy_id),
	member_id VARCHAR(20) REFERENCES member(member_id),
	loan_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP NOT NULL,
	return_date TIMESTAMP,
	rent_amount INT,
	status CHAR(1) CHECK (status IN ('I', 'L', 'D', 'R', 'S', 'C', 'P')),
	rented_by INT REFERENCES employee(empid),
	returned_by INT REFERENCES employee(empid),
	rent_mode CHAR(1) CHECK (rent_mode IN ('P', 'C')),
	return_mode CHAR(1) CHECK (return_mode IN ('P', 'C', 'O'))
);

CREATE TABLE old_items
(
	copy_id INT PRIMARY KEY REFERENCES item_details(copy_id),
	reason CHAR(1) CHECK (reason IN ('L', 'D', 'S')),
	transaction_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP NOT NULL,
	amount INT,
	processed_by INT REFERENCES employee(empid)
);

CREATE TABLE courier_order
(
	order_id INT PRIMARY KEY auto_increment,
	placed_by VARCHAR(20) REFERENCES member(member_id),
	item_id INT REFERENCES item_master(item_id),
	order_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP NOT NULL,
	order_for CHAR(6) CHECK (order_for IN ('RENT', 'RETURN')),
	mode CHAR(1) CHECK (mode IN ('O', 'C')),
	call_taken_by INT REFERENCES employee(empid),
	status CHAR(10) CHECK (status IN ('ORDERED', 'DISPATCHED', 'DELIVERED', 'CANCELLED', 'COLLECTED', 'PROCESSED', 'CLOSED')),
	delivery_address VARCHAR(50) NOT NULL,
	trans_id INT REFERENCES transaction(trans_id),
  courier_via INT REFERENCES courier_service(service_id),
  order_processed_by INT REFERENCES employee(empid),
  order_processed_on TIMESTAMP
);

CREATE TABLE courier_service
(
	service_id INT PRIMARY KEY auto_increment,
	service_name VARCHAR(30),
  address VARCHAR(50),
	contact_number CHAR(12),
	email_id VARCHAR(50),
  status CHAR(1) DEFAULT 'A' CHECK (status IN ('I','A'))
);

alter table item_details add purchased_on TIMESTAMP DEFAULT CURRENT_TIMESTAMP;


INSERT INTO store VALUES(0, 'IIT', 'Kharagpur', '123456', '0281545723', 'A', 22.31492740,  87.31053100);
INSERT INTO store VALUES(0, 'Abcd', 'Kolkata', '213455', '0251556677', 'A',  18.89286760, 72.77587290);
INSERT INTO employee VALUES(0,'super','9881545723', 'Kharagpur','30967e36448a9cc4a7b13b48a35ba90a', 'S', NULL, 'A');

INSERT INTO courier_service VALUES(0, 'Professional Couriers', 'abcd', '0202233449', 'professional_courier@gmail.com', 'A');
INSERT INTO courier_service VALUES(0, 'Blue Dart', 'xyz', '0202533449',  'blue_dart@gmail.com', 'A');
INSERT INTO courier_service VALUES(0, 'DTDC', 'aaa', '0202988449', 'dtdc@gmail.com', 'A');
commit;
