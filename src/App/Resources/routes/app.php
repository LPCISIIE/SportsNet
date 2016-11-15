<?php

$app->get('/', 'AppController:home')->setName('home');

$app->get('/epreuve/add', 'EpreuveController:getAddEpreuve')->setName('epreuve.add');
$app->post('/epreuve/add', 'EpreuveController:postAddEpreuve');
