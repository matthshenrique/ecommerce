<?php
session_start();

require_once "vendor/autoload.php";

use Hcode\PageAdmin;
use \Hcode\Model\Category;
use \Hcode\Model\User;
use \Hcode\Page;
use \Slim\Slim;

$app = new Slim();

$app->config('debug', true);

//Rout Tela Principal do Site
$app->get('/', function () {

    $page = new Page();

    $page->setTpl("index");

});

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

//Rout listar Categorias
$app->get("/admin/categories", function () {

    User::verifyLogin();

    $categories = Category::listAll();

    $page = new PageAdmin();

    $page->setTpl("categories", [
        "categories" => $categories,
    ]);

});

//Rout Criar Categorias GET
$app->get("/admin/categories/create", function () {

    User::verifyLogin();

    $page = new PageAdmin();

    $page->setTpl("categories-create");
});

//Rout Criar Categorias POST
$app->post("/admin/categories/create", function () {

    User::verifyLogin();

    $category = new Category();

    $category->setData($_POST);

    $category->save();

    header("Location: /admin/categories");
    exit;
});

//Rout Deletar Categorias
$app->get("/admin/categories/:idcategory/delete", function ($idcategory) {

    User::verifyLogin();

    $category = new Category();

    $category->get((int) $idcategory);

    $category->delete();

    header("Location: /admin/categories");
    exit;
});

//Rout Alterar Categorias GET
$app->get("/admin/categories/:idcategory", function ($idcategory) {

    User::verifyLogin();

    $category = new Category();

    $category->get((int) $idcategory);

    $page = new PageAdmin();

    $page->setTpl("categories-update", [
        "category" => $category->getValues(),
    ]);

});

//Rout Alterar Categorias POST
$app->post("/admin/categories/:idcategory", function ($idcategory) {

    User::verifyLogin();

    $category = new Category();

    $category->get((int) $idcategory);

    $category->setData($_POST);

    $category->save();

    header("Location: /admin/categories");
    exit;

});

$app->get("/categories/:idcategory", function ($idcategory) {

    $category = new Category();

    $category->get((int) $idcategory);

    $page = new Page();

    $page->setTpl("category", [
        "category" => $category->getValues(),
        "products" => [],
    ]);

});

$app->run();
