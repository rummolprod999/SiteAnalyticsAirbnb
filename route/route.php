<?php
require_once 'RouterLite.php';
if(AuthController::$is_login || AuthController::check_auth()){
    RouterLite::addRoute('/', 'DefaultController/index_page');
    RouterLite::addRoute('/stat/:num', 'StatController/index_page/$1');

} else{
    RouterLite::addRoute('/', 'AuthController');
}
RouterLite::dispatch();
