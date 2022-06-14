[![CI Status](https://github.com/demiankatz/Geeby-Deeby/actions/workflows/ci.yaml/badge.svg?branch=dev)](https://github.com/demiankatz/Geeby-Deeby/actions/workflows/ci.yaml)

# Geeby-Deeby


A bibliography and collection management system developed as the foundation for gamebooks.org and dimenovels.org.

# Installation

The following sections run through the installation process; you should follow all of them in the order listed here.

## Install Dependencies

First, after cloning this repository to a directory, use "composer install" to load dependencies (see http://getcomposer.org for details).

## Publish Web Content

The easiest way to get Geeby-Deeby running is to create a symbolic link to /public in your system's web root (often something like `/var/www` or `/var/www/html`). So, for example, if you had installed Geeby-Deeby into `/opt/gbdb`, you could run a command like: `sudo ln -s /opt/gbdb/public /var/www/html/gbdb`.

You may also need to adjust your Apache configuration to allow .htaccess override files within this new symlinked directory; for example, you could edit your default VirtualHost and add:

<pre>
<Directory /var/www/html/gbdb>
    AllowOverride all
</Directory>
</pre>

## Database Configuration

After that, you'll have to set up a database....

First, you need to decide on some details:

- Database name (we'll use gbdb in examples below)
- Database username (we'll use gbdb_user in examples below)
- Database password (we'll use gbdb_pass in examples below)

Next, log in to MySQL as the root user:

<pre>
mysql -uroot -p
</pre>

Once at the MySQL prompt, run these commands, substituting your chosen values where appropriate.

<pre>
create database gbdb character set utf8 collate utf8_general_ci;
create user 'gbdb_user'@'localhost' identified by 'gbdb_pass'
grant all on gbdb.* to 'gbdb_user'@'localhost';
flush privileges;
</pre>

While you are logged in to MySQL, you should also check your SQL mode before leaving the MySQL client:

<pre>
select @@sql_mode;
quit
</pre>

If the mode includes the `ONLY_FULL_GROUP_BY` setting, that will cause compatibility problems. You can disable this by defining a `sql_mode` line in your `my.conf` configuration file (or equivalent); simply copy and paste all of the remaining settings of the SQL mode; for example: `sql_mode=STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION`. You will need to restart MySQL after making this change for the setting to take effect.

Once back at the command line, run this to load the database structure you will need to use the software (again substituting appropriate credentials and database name where appropriate):

<pre>
mysql -ugbdb_user -pgbdb_pass gbdb < data/mysql.sql
</pre>

Finally, unless you decided to use the default of "gbdb" for database name, database username and database password (NOT RECOMMENDED), you will need to configure the software with appropriate credentials.

To do this, create a copy of the `config/autoload/local.php.dist` called simply `config/autoload/local.php`. You can override all of Geeby-Deeby's default configuration settings through this file. To start with, you should edit it to look something like this:

<pre>
return array(
    'geeby-deeby' => array(
        'dbName' => 'gbdb',
        'dbUser' => 'gbdb_user',
        'dbPass' => 'gbdb_pass',
    ),
);
</pre>

## Additional Configuration Options

You can now make some additional configurations as needed; refer to the 'geeby-deeby' array in `module/GeebyDeeby/config/module.config.php` to see all of the settings that may be overridden in your `local.php` file. To start with, you should at least customize the 'siteTitle', 'siteEmail' and 'siteOwner' settings, which will control the name of your site, and the name/email used in site-related contact information. You should adjust the 'emailTransport' setting if you need to
use SMTP to send messages.

## Establishing a Superuser

Finally, you need to establish a superuser account so you can edit the content of the site.

First, sign up for an account through the regular web interface (using the "Sign Up" link in the header).

Next, at the command line, log into MySQL:

<pre>
mysql -ugbdb_user -pgbdb_pass gbdb
</pre>

At the MySQL prompt, you should create an administrator role like this:

<pre>
insert into User_Groups(Group_Name, Content_Editor, User_Editor, Approver, Data_Manager) values ('Superuser', 1, 1, 1, 1);
</pre>

Now you should promote your user to an administrator like this:

<pre>
update Users set User_Group_ID=1, Person_ID=-1 where User_ID=1;
</pre>

(Of course, this assumes that both the Superuser group and the new user you created have IDs of '1' -- if this is a fresh database, that should always be the case, but if you made any mistakes and had to create new rows, please substitute appropriate ID values as needed).

## Accessing the Data Entry Backend

Now, you can add "/edit" to the base URL of your installation (for example, `http://localhost/gbdb/edit`), and log in -- you are ready to populate the database through the web interface.

# Command Line Utilities

Geeby-Deeby includes some utilities that can be run from the command line for data integrity
checking, etc. Simply run `php cli.php` from the Geeby-Deeby directory to get a summary of
commands. You can add the `--help` switch to get more information about a particular command;
for example, `php cli.php check/fulltext --help` will tell you more about the command to check
the integrity of full text links.

# Upgrading

Whenever you pull down changes from the upstream repository, you should be sure to do two things:

1. Run `composer install` to load the latest dependencies.
2. Check the `data/migrations` directory for new SQL scripts added since your last upgrade. These should be run in chronological order to bring your database structure up to date (e.g. `mysql -ugbdb_user -pgbdb_pass gbdb < data/migrations/YYYYMMDD-nnn-name-of-migration.sql`>)
