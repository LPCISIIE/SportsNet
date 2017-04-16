<p align="center"><img style="margin-bottom:3em;" width="150"src="http://www.rogersmedia.com/wp-content/uploads/2013/09/sportsnet.png"> 
<br> <br>
<img src="https://img.shields.io/packagist/l/doctrine/orm.svg?style=flat-square" alt="mit">  
   </p>  <br>

SportsNet is a web application to create and manage sports events.

## Installation
```bash
$ git clone git@github.com:TPCISIIE/SportsNet.git
```

### 2. Download vendors
```bash
$ cd SportsNet
$ composer install
```

### 3. Setup permissions
```bash
chmod -R 777 cache/ public/
```

### 4. Link your database
```bash
$ cd bootstrap
$ cp db.php.dist db.php
```
Fill the array

### 5. Create tables and insert important queries
Execute the following command in a terminal:
```bash
$ php bootstrap/database.php
```
or import `bootstrap/sportnet.sql` in your database manager

## Key files

- `bootstrap/`
    - `dependencies.php`: Register services in application container
    - `controllers.php`: Register controllers in container for easy routing
    - `middleware.php`: Add global middleware to the application
    - `sentinel.php`: Sentinel authentication library configuration
    - `settings.php`: Application configuration
- `src/`
    - `App/`
        - `Controller/`: Apllication controllers
        - `Middleware/`: Application middleware
        - `Model/`: Eloquent models
        - `Service/`: Additional services
        - `TwigExtension/`: Twig extensions
        - `Validation/`: Custom validation rules
        - `Resources/`
            - `routes/`: Routes definition
            - `views/`: Twig templates

## License

SportsNet is open-sourced software licensed under the MIT license.

## Credits 
- Xavier CHOPIN
- Corentin LABROCHE
- David LEBRUN
- Alexis WURTH
