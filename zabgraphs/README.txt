

1 - Copy zabgraphs folder to Zabbix folder (/usr/share/zabbix);

2 - Copy config.php.sample to config.php;

3 - Edit config.php with your server settings;

4 - Access URL http://your_zabbix_server/zabbix/zabgraphs;


To add a menu item for ZabGraphs see README.txt file in menu folder.


Zabbix API Needs php-posix:

In debian/ubuntu is in php-common package.
yum install php-process - redhat/centos
zypper install php-posix - OpenSuse
