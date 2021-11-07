/*Create a Database*/
CREATE DATABASE expenseManager;

/*User Table*/
CREATE TABLE users (
	id INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
	username VARCHAR(256) NOT NULL,
	password VARCHAR(256) NOT NULL,
	name VARCHAR(256) NOT NULL,
	email VARCHAR(256) NOT NULL
);

/*Expense Table*/
CREATE TABLE expenses (
	id INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
	username VARCHAR(256) NOT NULL,
	type VARCHAR(256) NOT NULL,
	amount INT(11) NOT NULL,
	date DATE NOT NULL,
	category VARCHAR(256) NOT NULL,
	details LONGTEXT NOT NULL
);