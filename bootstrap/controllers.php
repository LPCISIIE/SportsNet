<?php

$container['AppController'] = function ($container) {
    return new App\Controller\AppController($container);
};

$container['AuthController'] = function ($container) {
    return new App\Controller\AuthController($container);
};

$container['EvenementController'] = function ($container) {
    return new App\Controller\EvenementController($container);
};

$container['EpreuveController'] = function ($container) {
    return new App\Controller\EpreuveController($container);

};

$container['UserController'] = function ($container) {
    return new App\Controller\UserController($container);
};
