<?php

$app->get('/', 'AppController:home')->setName('home');
$app->get('/search', 'AppController:search')->setName('search');
