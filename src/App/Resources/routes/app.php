<?php

$app->get('/home', 'AppController:home')->setName('home');
$app->get('/search', 'AppController:search')->setName('search');
