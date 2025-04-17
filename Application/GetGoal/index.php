<?php

require_once $_SERVER['DOCUMENT_ROOT'] . "/Private/SessionManager.php";
header("Content-Type: application/json");

$goal = json_decode(file_get_contents($_SERVER['DOCUMENT_ROOT'] . "/Private/Data/Goal.json"), true);

die(json_encode($goal));