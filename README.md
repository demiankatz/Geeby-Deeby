Geeby-Deeby
===========

A bibliography and collection management system developed as the foundation for gamebooks.org and dimenovels.org.

Installation
============
1. First, use "composer install" to load dependencies (see http://getcomposer.org for details).

2. The easiest way to get Geeby-Deeby running is to create a symbolic link to /public in your system's /var/www.

3. After that, you'll have to create a database using the script found in data/mysql.sql:

Defaults:
>
- Name: gbdb
- User: gbdb // we recommend smart
- Pass: gbdb // choices here
- Char Set: utf8
- Collate: utf8_bin;
