Geeby-Deeby
===========

A bibliography and collection management system developed as the foundation for gamebooks.org and dimenovels.org.

Installation
============
The easiest way to get Geeby-Deeby running is to create a symbolic link to /public in your system's /var/www.

After that, you'll have to create a database using the script found in data/mysql.sql:

Defaults:
>
- Name: gbdb
- User: gbdb // we recommend smart
- Pass: gbdb // choices here
- Char Set: utf8
- Collate: utf8_bin;
