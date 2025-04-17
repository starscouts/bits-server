<?php

require_once $_SERVER['DOCUMENT_ROOT'] . "/Private/SessionManager.php"; global $_PROFILE;
header("Content-Type: application/json");

$a = [
    "name" => $_PROFILE["name"],
    "id" => $_PROFILE['id'],
    "system" => json_decode(file_get_contents($_SERVER['DOCUMENT_ROOT'] . "/Private/Data/Systems.json"), true)[$_PROFILE['id']],
];

die(json_encode($a));