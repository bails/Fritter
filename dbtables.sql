#
#  dbtables.sql
#
#  Script for creating mysql tables for fritter app
#
DROP TABLE IF EXISTS users;

CREATE TABLE users (
 username varchar(30) primary key,
 password varchar(32),
 userid varchar(32),
 userlevel tinyint(1) unsigned not null,
 email varchar(50),
 timestamp int(11) unsigned not null
);


#
#  Table structure for active users table
#
DROP TABLE IF EXISTS active_users;

CREATE TABLE active_users (
 username varchar(30) primary key,
 timestamp int(11) unsigned not null
);


#
#  Table structure for active guests table
#
DROP TABLE IF EXISTS active_guests;

CREATE TABLE active_guests (
 ip varchar(15) primary key,
 timestamp int(11) unsigned not null
);


#
#  Table structure for banned users table
#
DROP TABLE IF EXISTS banned_users;

CREATE TABLE banned_users (
 username varchar(30) primary key,
 timestamp int(11) unsigned not null
);


#
# Table for following relationships
#

CREATE TABLE following (
 username varchar(30) NOT NULL,
 followername varchar(30) NOT NULL,
 PRIMARY KEY (username, followername)
);

#
# Table for user posts
#

CREATE TABLE posts (
 id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
 username varchar(30) NOT NULL,
 `body` varchar(140) NOT NULL,
 stamp DATETIME NOT NULL
);

#
# Table for LOLs
#

CREATE TABLE lols (
post_id INT NOT NULL,
lol INT NOT NULL DEFAULT 0
);