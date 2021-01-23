<?php
//启动方式：php -S localhost:8000 -t public .htrouter.php

if (!file_exists(__DIR__ . '/' . $_SERVER['REQUEST_URI'])) {
    $_GET['_url'] = $_SERVER['REQUEST_URI'];
}

return false;
