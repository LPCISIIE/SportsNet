<?php

require __DIR__ . '/../vendor/autoload.php';

use Illuminate\Database\Capsule\Manager;
use Illuminate\Database\Schema\Blueprint;
use Cartalyst\Sentinel\Native\Facades\Sentinel;
use Cartalyst\Sentinel\Native\SentinelBootstrapper;

$config = require __DIR__ . '/db.php';

$capsule = new Manager();
$capsule->addConnection($config);
$capsule->setAsGlobal();
$capsule->bootEloquent();

$sentinel = (new Sentinel(new SentinelBootstrapper(__DIR__ . '/sentinel.php')))->getSentinel();

Manager::schema()->dropIfExists('activations');
Manager::schema()->dropIfExists('persistences');
Manager::schema()->dropIfExists('reminders');
Manager::schema()->dropIfExists('role_users');
Manager::schema()->dropIfExists('throttle');
Manager::schema()->dropIfExists('roles');
Manager::schema()->dropIfExists('user');

Manager::schema()->create('user', function (Blueprint $table) {
    $table->increments('id');
    $table->string('email')->unique();
    $table->string('password');
    $table->text('permissions');
    $table->timestamp('last_login');
    $table->timestamps();
});

Manager::schema()->create('activations', function (Blueprint $table) {
    $table->increments('id');
    $table->integer('user_id')->unsigned();
    $table->string('code');
    $table->boolean('completed')->default(0);
    $table->timestamp('completed_at')->nullable();
    $table->timestamps();
    $table->foreign('user_id')->references('id')->on('user');
});

Manager::schema()->create('persistences', function (Blueprint $table) {
    $table->increments('id');
    $table->integer('user_id')->unsigned();
    $table->string('code')->unique();
    $table->timestamps();
    $table->foreign('user_id')->references('id')->on('user');
});

Manager::schema()->create('reminders', function (Blueprint $table) {
    $table->increments('id');
    $table->integer('user_id')->unsigned();
    $table->string('code');
    $table->boolean('completed')->default(0);
    $table->timestamp('completed_at')->nullable();
    $table->timestamps();
    $table->foreign('user_id')->references('id')->on('user');
});

Manager::schema()->create('roles', function (Blueprint $table) {
    $table->increments('id');
    $table->string('slug')->unique();
    $table->string('name');
    $table->text('permissions');
    $table->timestamps();
});

Manager::schema()->create('role_users', function (Blueprint $table) {
    $table->integer('user_id')->unsigned();
    $table->integer('role_id')->unsigned();
    $table->timestamps();
    $table->primary(['user_id', 'role_id']);
    $table->foreign('user_id')->references('id')->on('user');
    $table->foreign('role_id')->references('id')->on('roles');
});

Manager::schema()->create('throttle', function (Blueprint $table) {
    $table->increments('id');
    $table->integer('user_id')->unsigned()->nullable();
    $table->string('type');
    $table->string('ip')->nullable();
    $table->timestamps();
    $table->foreign('user_id')->references('id')->on('user');
});

Manager::schema()->create('organisateur', function (Blueprint $table) {
    $table->increments('id');
    $table->string('nom');
    $table->string('prenom');
    $table->string('paypal')->nullable();
    $table->integer('user_id')->unsigned();
    $table->foreign('user_id')->references('id')->on('user');
});

Manager::schema()->create('sportif', function (Blueprint $table) {
    $table->increments('id');
    $table->string('nom');
    $table->string('prenom');
    $table->string('email');
    $table->date('birthday')->nullable();
    $table->integer('user_id')->unsigned();
    $table->foreign('user_id')->references('id')->on('user');
});

Manager::schema()->create('evenement', function (Blueprint $table) {
    $table->increments('id');
    $table->string('nom');
    $table->text('adresse');
    $table->date('date_debut');
    $table->date('date_fin');
    $table->string('telephone', 10);
    $table->string('discipline');
    $table->text('description');
    $table->tinyInteger('etat');
    $table->integer('user_id')->unsigned();
    $table->foreign('user_id')->references('id')->on('user');
});

Manager::schema()->create('epreuve', function (Blueprint $table) {
    $table->increments('id');
    $table->string('nom');
    $table->integer('capacite');
    $table->dateTime('date_debut');
    $table->dateTime('date_fin');
    $table->text('description');
    $table->tinyInteger('etat');
    $table->integer('prix');
    $table->integer('evenement_id')->unsigned();
    $table->foreign('evenement_id')->references('id')->on('evenement');
});

Manager::schema()->create('participe', function (Blueprint $table) {
    $table->integer('sportif_id')->unsigned();
    $table->integer('epreuve_id')->unsigned();
    $table->integer('numero_participant');
    $table->primary(['sportif_id', 'epreuve_id']);
    $table->foreign('sportif_id')->references('id')->on('sportif');
    $table->foreign('epreuve_id')->references('id')->on('epreuve');
});

/* -------------------------------------------------- */

$sentinel->getRoleRepository()->createModel()->create(array(
    'name' => 'Admin',
    'slug' => 'admin',
    'permissions' => array(
        'user.create' => true,
        'user.update' => true,
        'user.delete' => true
    )
));

$sentinel->getRoleRepository()->createModel()->create(array(
    'name' => 'User',
    'slug' => 'user',
    'permissions' => array(
        'user.update' => true
    )
));
