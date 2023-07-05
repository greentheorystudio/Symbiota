# Green Theory Studio's Symbiota

The [Green Theory Studio](https://greentheorystudio.com/) fork of Symbiota is a cleaned and updated version of the original, offering many of the same tools 
for generating, managing, and disseminating biodiversity data, with the addition of many new features that allow for much easier portal management, image processing, and incredible spatial analysis capabilities. It is compatible with the newest version of MySQL and is more 
secure than the original, requiring fewer third-party services to configure.

[You can explore this version through our demo portal.](https://greentheorystudio.net/symbiota-demo/) If you would like 
login access to the demo portal to explore the administrative features, 
[please send us a messagage through our contact page.](https://greentheorystudio.com/contact/)
## Requirements

This software can be installed on any Linux, Windows, or Mac operating systems, but requires an additional server application
([Apache](http://httpd.apache.org/), [NGINX](https://www.nginx.com/), etc.) to be installed and configured. In addition, 
it requires the following applications to be installed and configured with the server application:

- [PHP 7.3 or greater](http://php.net/manual/en/install.php)
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

## Recommended server configurations
These configurations are recommended for the php and MySQL/MariaDB installations on the server in addition to the defaults.

### php configurations (made in the php.ini file)

- `expose_php = Off`
- `max_execution_time = 600`
- `max_input_time = 1000`
- `max_input_vars = 3000`
- `memory_limit = 256M`
- `post_max_size = 150M`
- `upload_max_filesize = 150M`

### MySQL/MariaDB configurations (usually made in the mysqld.cnf file)

- `character-set-server=utf8` - replace utf8 with your desired character set
- `collation-server=utf8_general_ci` - replace utf8_general_ci with your desired collation
- `skip-character-set-client-handshake`
- `sql_mode=IGNORE_SPACE,ALLOW_INVALID_DATES,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION`
- `key_buffer_size=100M`
- `max_allowed_packet=100M`
- `thread_cache_size=38`

## Installation

### Install the software

- Navigate into the directory in which you wish to install your portal.
- Clone this repository using the following command:
    
    `git clone https://github.com/greentheorystudio/Symbiota.git .`

- Complete the installation using Composer through the following command:
    
    `composer install`

### Setup the database

- Create a new database for the portal using the utf8 character set and utf8_general_ci collation.
- Execute the `config/schema-1.0/utf8/db_schema-1.2.sql` file within your new installation on your database to setup 
  the initial schema.
- Create a new user in your database server and grant them DELETE, EXECUTE, INSERT, SELECT, and UPDATE 
  privileges on the new database. 

### Configure your installation

- Edit the `config/dbconnection.php` file within your new installation to reflect the database and database 
  user that was created in the previous section.

### Customize your installation

- Edit the `css/main.css` file within your new installation to customize the css styling in your portal.
- Edit the `footer.php`, `header.php`, and `index.php` files within your new installation to customize the layout, 
  top menu bar, and homepage of your portal.
- Edit the `misc/usagepolicy.php` file within your new installation to customize the usage policy for your portal.
- An initial admin user has been installed with the login: `admin` and the password: `admin`. Use this initial user account to 
  create new admin users and then delete the initial user account.

[Go to the Documentation site](https://greentheorystudio.github.io/Symbiota/)
