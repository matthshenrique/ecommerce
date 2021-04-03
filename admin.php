<?php

use \Hcode\Model\User;
use \Hcode\PageAdmin;

//Rout Acessar Área do Admin
$app->get('/admin', function () {

    User::verifyLogin();

    $page = new PageAdmin();

    $page->setTpl("index");

});

//Rout Fazer Login no sistema GET
$app->get("/admin/login", function () {

    $page = new PageAdmin([
        "header" => false,
        "footer" => false,
    ]);

    $page->setTpl("login");
});

//Rout Fazer Login no sistema POST
$app->post("/admin/login", function () {

    User::login($_POST["login"], $_POST["password"]);

    header("Location: /admin");

    exit;
});

//Rout Fazer Logout
$app->get('/admin/logout', function () {
    User::logout();
    header("Location: /admin/login");
    exit;
});

//Rout Esqueci Senha GET
$app->get("/admin/forgot", function () {

    $page = new PageAdmin([
        "header" => false,
        "footer" => false,
    ]);

    $page->setTpl("forgot");
});

//Rout Esqueci Senha POST
$app->post("/admin/forgot", function () {

    $user = USER::getForgot($_POST["email"]);

    header("Location: /admin/forgot/sent");
    exit;
});

//Rout Envio Nova Senha
$app->get("/admin/forgot/sent", function () {

    $page = new PageAdmin([
        "header" => false,
        "footer" => false,
    ]);

    $page->setTpl("forgot-sent");
});

//Rout Resetar Senha GET
$app->get("/admin/forgot/reset", function () {

    $user = User::validForgotDecrypt($_GET["code"]);

    $page = new PageAdmin([
        "header" => false,
        "footer" => false,
    ]);

    $page->setTpl("forgot-reset", array(
        "name" => $user["desperson"],
        "code" => $_GET["code"],
    ));
});

//Rout Resetar Senha POST
$app->post("/admin/forgot/reset", function () {

    $forgot = User::validForgotDecrypt($_POST["code"]);

    User::setForgotUsed($forgot["idrecovery"]);

    $user = new User();

    $user->get((int) $forgot["iduser"]);

    $password = password_hash($_POST["password"], PASSWORD_DEFAULT, [
        "cost" => 12,
    ]);

    $user->setPassword($password);

    $page = new PageAdmin([
        "header" => false,
        "footer" => false,
    ]);

    $page->setTpl("forgot-reset-success");
});
