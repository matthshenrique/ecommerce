<?php

use \Hcode\Model\Category;
use \Hcode\Model\User;
use \Hcode\Page;
use \Hcode\PageAdmin;

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
