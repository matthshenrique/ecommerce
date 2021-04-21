<?php

use \Hcode\Model\Cart;
use \Hcode\Model\Category;
use \Hcode\Model\Product;
use \Hcode\Page;

//Rout Tela Principal do Site
$app->get('/', function () {

    $products = Product::listAll();

    $page = new Page();

    $page->setTpl("index", [
        "products" => Product::checkList($products),
    ]);

});

$app->get("/categories/:idcategory", function ($idcategory) {

    $page = (isset($_GET["page"])) ? (int) $_GET["page"] : 1;

    $category = new Category();

    $category->get((int) $idcategory);

    $pagination = $category->getProductsPage($page);

    $pages = [];

    for ($i = 1; $i <= $pagination["pages"]; $i++) {
        array_push($pages, [
            "link" => "/categories/" . $category->getidcategory() . "?page=" . $i,
            "page" => $i,
        ]);
    }

    $page = new Page();

    $page->setTpl("category", [
        "category" => $category->getValues(),
        "products" => $pagination["data"],
        "pages" => $pages,
    ]);

});

//Rout Informações do Produto
$app->get("/products/:desurl", function ($desurl) {

    $product = new Product();

    $product->getFromURL($desurl);

    $page = new Page();

    $page->setTpl("product-detail", [
        "product" => $product->getValues(),
        "categories" => $product->getCategories(),
    ]);

});

//Rout Criar Carrinho ou Chamar um existente
$app->get("/cart", function () {

    $cart = Cart::getFromSession();

    $page = new Page();

    $page->setTpl("cart", [
        "cart" => $cart->getvalues(),
        "products" => $cart->getproducts(),
        "error" => Cart::getMsgError(),
    ]);
});

//Rout adicionar produto no Carrinho
$app->get("/cart/:idproduct/add", function ($idproduct) {

    $product = new Product();

    $product->get((int) $idproduct);

    $cart = Cart::getFromSession();

    $qtd = (isset($_GET['qtd'])) ? (int) $_GET['qtd'] : 1;

    for ($i = 0; $i < $qtd; $i++) {

        $cart->addProduct($product);
    }

    header("Location: /cart");
    exit;

});

//Rout Remover apenas um Produto do Carrinho
$app->get("/cart/:idproduct/minus", function ($idproduct) {

    $product = new Product();

    $product->get((int) $idproduct);

    $cart = Cart::getFromSession();

    $cart->removeProduct($product);

    header("Location: /cart");
    exit;

});

//Rout Remover Todos os Produtos do Carrinho
$app->get("/cart/:idproduct/remove", function ($idproduct) {

    $product = new Product();

    $product->get((int) $idproduct);

    $cart = Cart::getFromSession();

    $cart->removeProduct($product, true);

    header("Location: /cart");
    exit;

});

//Rout Calcular Frete
$app->post("/cart/freight", function () {

    $cart = Cart::getFromSession();

    $cart->setFreight($_POST["zipcode"]);

    header("Location: /cart");
    exit;

});
