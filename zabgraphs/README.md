# ZabGraphs
> Based on [Graphtrees](https://share.zabbix.com/zabbix-tools-and-utilities/web-addons/graphtree-for-zabbix) for Zabbix, permits easy access to all graphs of a host, hosts or host group.

![](https://repository-images.githubusercontent.com/189693207/0b4ecb00-85ec-11e9-8c44-8aa5c1fb7d94)

- View all graphs of host
- View all graphs os hosts group
- Use the Item field for show specific items graphs like CPU, Disk, network traffic, ping, etc, for one or multiple hosts.
- Use search bar to filter hosts.


![](https://user-images.githubusercontent.com/7674445/58826320-74dfd100-860e-11e9-90e2-ca2c09816038.png)


## Installation

1 - Copy zabgraphs folder to Zabbix folder (/usr/share/zabbix);

2 - Copy config.php.sample to config.php;

3 - Edit config.php with your server settings;

4 - Access URL http://your_zabbix_server/zabbix/zabgraphs;




#### To add a menu item for ZabGraphs on Zabbix:

- Make a copy of /usr/share/zabbix/include/menu.inc.php file.

- Copy the file "menu/menu.inc.php" for your zabbix include folder (/usr/share/zabbix/include).

![](https://user-images.githubusercontent.com/7674445/58826988-fa17b580-860f-11e9-83ab-af72303cb9fa.png)




#### Zabbix API Needs php-posix:

apt-get install php-common - ubuntu/debian

yum install php-process - redhat/centos

zypper install php-posix - OpenSuse


