[![Build Status](https://travis-ci.org/demiankatz/Geeby-Deeby.svg?branch=master)](https://travis-ci.org/demiankatz/Geeby-Deeby)

Geeby-Deeby
===========

A bibliography and collection management system developed as the foundation for gamebooks.org and dimenovels.org.

Installation
============
1. First, use "composer install" to load dependencies (see http://getcomposer.org for details).

2. The easiest way to get Geeby-Deeby running is to create a symbolic link to /public in your system's web root (often `/var/www`).

3. After that, you'll have to create a database using the script found in data/mysql.sql.

<pre>
# this will require you to log in with your root credentials:
mysql -uroot -p
# this creates the gbdb database and a gbdb user with a password of gbdb;
# it is recommended that you change these defaults. If you use non-default
# username/password settings, change them accordingly in
# module/GeebyDeeby/config/module.config.php
create database gbdb character set utf8 collate utf8_general_ci;
grant all on gbdb.* to 'gbdb'@'localhost' identified by 'gbdb';
# This imports the data (adjust credentials/db name as needed):
mysql -ugbdb -pgbdb gbdb < data/mysql.sql
</pre>
