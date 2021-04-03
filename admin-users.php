<?php

use \Hcode\Model\User;
use \Hcode\PageAdmin;

//Rout Listar Usuario
$app->get("/admin/users", function () {

    $users = User::verifyLogin();

    $users = User::listAll();

    $page = new PageAdmin();

    $page->setTpl("users", array(
        "users" => $users,
    ));
});

//Rout Criar Usuários GET
$app->get("/admin/users/create", function () {

    User::verifyLogin();
    $page = new PageAdmin();
    $page->setTpl("users-create");
});

//Rout Criar Usuário POST
$app->post("/admin/users/create", function () {
    User::verifyLogin();

    $user = new User();

    $_POST["inadmin"] = (isset($_POST["inadmin"])) ? 1 : 0;

    $user->setData($_POST);

    $user->save();

    header("Location: /admin/users");
    exit;
});

//Rout Deletar Usuario GET
$app->get("/admin/users/:iduser/delete", function ($iduser) {
    User::verifyLogin();

    $user = new User();

    $user->get((int) $iduser);

    $user->delete();

    header("Location: /admin/users");
    exit;

});

//Rout Alterar Usuário GET
$app->get("/admin/users/:iduser", function ($iduser) {

    $user = new User();
    $user->get((int) $iduser);
    $page = new PageAdmin();
    $page->setTpl("users-update", array(

        "user" => $user->getValues(),
    ));
});

//Rout Alterar Usuario POST
$app->post("/admin/users/:iduser", function ($iduser) {
    User::verifyLogin();

    $user = new User();

    $_POST["inadmin"] = (isset($_POST["inadmin"])) ? 1 : 0;

    $user->get((int) $iduser);

    $user->setData($_POST);

    $user->update();

    header("Location: /admin/users");
    exit;
});
