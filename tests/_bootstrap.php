<?php
// This is global bootstrap for autoloading
$composerAutoloadFile = './vendor/autoload.php';
if(!is_file($composerAutoloadFile)){
    die("You really should run composer before testing");
}
include $composerAutoloadFile;