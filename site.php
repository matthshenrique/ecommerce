<?php

use \Hcode\Page;

//Rout Tela Principal do Site
$app->get('/', function () {

    $page = new Page();

    $page->setTpl("index");

});
