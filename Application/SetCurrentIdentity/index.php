<?php

require_once $_SERVER['DOCUMENT_ROOT'] . "/Private/SessionManager.php"; global $_PROFILE;
header("Content-Type: application/json");

$identities = json_decode(file_get_contents($_SERVER['DOCUMENT_ROOT'] . "/Private/Data/Identities.json"), true);

if (!isset($_GET['Name']) || !isset($_GET['Picture'])) {
    die();
}

function base64url_decode($data, $strict = false): string {
    $b64 = strtr($data, '-_', '+/');
    return base64_decode($b64, $strict);
}

$identities[$_PROFILE["id"]]["name"] = base64url_decode($_GET['Name']);
$identities[$_PROFILE["id"]]["picture"] = base64url_decode($_GET['Picture']);

file_put_contents($_SERVER['DOCUMENT_ROOT'] . "/Private/Data/Identities.json", json_encode($identities, JSON_PRETTY_PRINT));

die();