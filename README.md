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
- [git](https://git-scm.com/) (optional, but suggested in order to easily update your installation from this repository)

In addition, the following PHP extensions need to be installed and enabled:

- mysqli
- iconv
- JSON
- Mbstring
- OpenSSL
- PCRE
- PDO
- Session
- SimpleXML
- Tokenizer

[Go to the Documentation site](https://greentheorystudio.github.io/Symbiota/)
