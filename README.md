SQL CODE TO CREATE THE DDBB SCHEMA AND TABLE
--------------------------------------------

CREATE DATABASE IF NOT EXISTS workshop;

USE workshop;

CREATE TABLE IF NOT EXISTS reparation (
    id INT AUTO_INCREMENT PRIMARY KEY,
    uuid CHAR(36) NOT NULL,
	 idWorkshop INT(4) NOT NULL,
    name VARCHAR(12) NOT NULL,
    registerDate DATE NOT NULL,
    licensePlate VARCHAR(8) NOT NULL,
    image LONGBLOB,
    UNIQUE (uuid)
);
