# Green Theory Studio's Symbiota

The Green Theory Studio fork of Symbiota is a cleaned and updated version of the original, offering all of the same tools 
for generating, managing, and disseminating biodiversity data. It is compatible with the newest version of MySQL and is more 
secure than the original, requiring fewer third-party services to configure.

## Requirements

Symbiota can be installed on any Linux, Windows, or Mac operating systems, but requires an additional server application
([Apache](http://httpd.apache.org/), [NGINX](https://www.nginx.com/), etc.) to be installed and configured. In addition, 
Symbiota requires the following applications to be installed and configured with the server application:

- [PHP 7.2 or greater](http://php.net/manual/en/install.php)
- [MySQL 5.6.2 or greater](https://www.mysql.com/) or [MariaDB 10.2 or greater](https://mariadb.com/)
- [Composer](https://getcomposer.org/doc/00-intro.md)
- [git](https://git-scm.com/)

In addition, the following PHP extensions need to be installed and enabled:

- php-curl
- php-gd
- php-mbstring
- php-mysql
- php-xml
- php-xmlrpc
- php-zip

The optional [PHP Pear](https://pear.php.net/) package [Image_Barcode](https://pear.php.net/package/Image_Barcode) can also
be installed to enable barcode generation on specimen labels.

## Installation

### Install the software

- Navigate into the directory in which you wish to install Symbiota.
- Clone this repository using the following command:
    
    `git clone https://github.com/greentheorystudio/Symbiota.git .`

- Complete the installation using Composer through the following command:
    
    `composer install`

### Setup the database

- Create a new database for your Symbiota portal using the utf8 character set and utf8_general_ci collation.
- Execute the `config/schema-1.0/utf8/db_schema_compiled-1.x.sql` file within your Symbiota installation on your database to setup 
  the initial schema.
- Create a new user in your database server and grant them DELETE, EXECUTE, INSERT, SELECT, and UPDATE 
  privileges on your Symbiota database. 

### Configure your Symbiota installation

- Edit the `config/dbconnection.php` file within your Symbiota installation to reflect the Symbiota database and database 
  user that was created in the previous section.
- Edit the `config/symbini.php` file within your Symbiota installation with your configuration preferences for your Symbiota portal.

### Customize your Symbiota installation

- Edit the `css/main.css` file within your Symbiota installation to customize the css styling in your Symbiota portal.
- Edit the `footer.php`, `header.php`, and `index.php` files within your Symbiota installation to customize the layout, 
  top menu bar, and homepage of your Symbiota portal.
- Edit the `misc/usagepolicy.php` file within your Symbiota portal to customize the usage policy for your portal.
- An initial admin user has been installed with the login: `admin` and the password: `admin`. Use this initial user account to 
  create new admin users and then delete the initial user account.

### Converting a database from the original Symbiota

If you would like to use a database created using the original Symbiota, follow these steps to upgrade the database:
- Ensure that your database is using schema 1.0 or higher. If it isn't, run the necessary database patches in the original 
  Symbiota installation to upgrade to schema version 1.0.
- Execute the `config/schema-1.0/utf8/greentheorystudio_patch.sql` file within your Symbiota installation on your database 
  to make necessary schema adjustments.
- If your database schema is version 1.0, execute the `config/schema-1.0/utf8/db_schema_patch-1.1.sql` file within your Symbiota 
  installation on your database to upgrade the schema version to 1.1.
- Execute the `config/schema-1.0/utf8/db_schema_patch-1.2.sql` file within your Symbiota installation on your database to 
  upgrade the schema version to 1.2.

[Go to the Documentation site](https://greentheorystudio.github.io/Symbiota/)
