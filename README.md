 Maestro Framework
<p align="center"><img style="margin-bottom:3em;" width="150"src="http://www.rogersmedia.com/wp-content/uploads/2013/09/sportsnet.png"> 
<br> <br>
[![Packagist](https://img.shields.io/packagist/l/doctrine/orm.svg?style=flat-square)]()  
   </p>  <br>

SportsNet is a web application to create and manage sports events.

## Installation
```
$ git@github.com:TPCISIIE/SportsNet.git

```

### 2. Download vendors
```
$ cd SportsNet
$ composer install
```

### 3. Setup permissions
```
chmod -R 777 cache
```

### 4. Link your database
```
$ cd bootstrap
$ cp db.php.dist db.php
Fill the array
```

### 5. Create tables and insert important queries
Execute the following command in a terminal:
```
$ php bootstrap/database.php
```
or import `bootstrap/installation.sql` in your database manager


## License

SportsNet is open-sourced software licensed under the MIT license.

## Credits 
 Xavier CHOPIN
 Corentin LABROCHE
 David LEBRUN
 Alexis WURTH
