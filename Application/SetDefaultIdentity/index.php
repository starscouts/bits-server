<?php

require_once $_SERVER['DOCUMENT_ROOT'] . "/Private/SessionManager.php"; global $_PROFILE;
header("Content-Type: application/json");

$identities = json_decode(file_get_contents($_SERVER['DOCUMENT_ROOT'] . "/Private/Data/Identities.json"), true);

$identities[$_PROFILE["id"]]["name"] = $_PROFILE["name"];
$identities[$_PROFILE["id"]]["picture"] = "https://privateauth.equestria.dev/hub/api/rest/avatar/" . $_PROFILE['id'] . "?dpr=2&size=64";

file_put_contents($_SERVER['DOCUMENT_ROOT'] . "/Private/Data/Identities.json", json_encode($identities, JSON_PRETTY_PRINT));

die();