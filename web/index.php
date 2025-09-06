<?php
include_once './assets/php/function.php';

if (isset($_GET['signup'])) {
    showPage('header', ["page_title" => 'healthlink - Signup page healthlink']);
    showPage('navbar');
    showPage('signup');
    showPage('footer');
}
if (isset($_GET['login'])) {
    showPage('header', ["page_title" => 'healthlink - Login page healthlink']);
    showPage('navbar');
    showPage('login');
    showPage('footer');
}
