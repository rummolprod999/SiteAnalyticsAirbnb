<?php
require_once 'Controller.php';

class DefaultController extends Controller
{
    function index_page()
    {
        echo $this->template('templates/default.php', ["title" => "Главная страница"]);
    }
}