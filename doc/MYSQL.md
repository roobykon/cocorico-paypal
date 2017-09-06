# MySQL

> version: 

**5.7.19**

> install

    sudo apt-get install mysql-server

> create schema

```sql
CREATE SCHEMA `auto-guide`;
```

> show schemas

```sql
SHOW SCHEMAS;
```

> create user with privileges on db

```sql
CREATE USER 'auto-guide'@'localhost' IDENTIFIED BY 'auto-guide';
GRANT ALL PRIVILEGES ON `auto-guide` . * TO 'auto-guide'@'localhost';
FLUSH PRIVILEGES;
```
> edit config

    sudo vim /etc/mysql/my.cnf

> add following to config

```ini
[mysqld]
sql_mode = STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION
```

> restart service

    sudo service mysql restart