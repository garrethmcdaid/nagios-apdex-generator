DROP DATABASE IF EXISTS `nagios`;

CREATE DATABASE nagios;

DROP USER 'nagiosuser'@'localhost';

CREATE USER 'nagiosuser'@'localhost' IDENTIFIED BY 'nagiosuser';

GRANT ALL ON nagios.* TO 'nagiosuser'@'localhost';

FLUSH PRIVILEGES;

USE nagios;

DROP TABLE IF EXISTS `logentries`;

create table logentries(
   id INT NOT NULL AUTO_INCREMENT,
   date DATETIME NOT NULL,
   seconds int(14) NOT NULL,
   service VARCHAR(60) NOT NULL,
   monitor VARCHAR(60) NOT NULL,
   type VARCHAR(10) NOT NULL,
   detail VARCHAR(60),
   PRIMARY KEY ( id )
);
