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
	date DATETIME NOT NULL,		/*Changed*/
	/*date DATE NOT NULL,*/		/*Old*/
	category VARCHAR(256) NOT NULL,
	wallet VARCHAR(256) NOT NULL,
	details LONGTEXT NOT NULL
);

/*Wallet Table*/
CREATE TABLE wallet (
	id INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
	walletUsername VARCHAR(256) NOT NULL,
	walletName VARCHAR(256) NOT NULL,
	walletValue INT(11) NOT NULL
);

/*Wallet History*/
CREATE TABLE wallethistory (
	id INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
	walletUsername VARCHAR(256) NOT NULL,
	walletNameFrom VARCHAR(256) NOT NULL,
	walletNameTo VARCHAR(256) NOT NULL,
	walletValue INT(11) NOT NULL,
	walletTransferDate DATETIME NOT NULL,
	type VARCHAR(256) NOT NULL,
	category VARCHAR(256) NOT NULL,
	details LONGTEXT NOT NULL
);

/*Change DATE to DATETIME in date*/
ALTER TABLE 'expenses' CHANGE 'date' 'date' DATETIME NOT NULL; 		/*No need if DATETIME kept in above query*/