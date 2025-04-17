<?php

global $SessionManagerAllowDisallowed;

if (isset($_COOKIE['PEH2_SESSION_TOKEN'])) {
    if (str_contains($_COOKIE['PEH2_SESSION_TOKEN'], ".") || str_contains($_COOKIE['PEH2_SESSION_TOKEN'], "/")) {
        header("Content-Type: application/json"); die("{\n    \"status\": 1\n}");
    }

    if (file_exists($_SERVER['DOCUMENT_ROOT'] . "/includes/tokens/" . str_replace(".", "", str_replace("/", "", $_COOKIE['PEH2_SESSION_TOKEN'])))) {
        $_PROFILE = json_decode(file_get_contents($_SERVER['DOCUMENT_ROOT'] . "/includes/tokens/" . str_replace(".", "", str_replace("/", "", $_COOKIE['PEH2_SESSION_TOKEN']))), true);

        $users = json_decode(file_get_contents($_SERVER['DOCUMENT_ROOT'] . "/Private/Data/Users.json"), true);
        $users[$_PROFILE["id"]] = $_PROFILE["name"];
        file_put_contents($_SERVER['DOCUMENT_ROOT'] . "/Private/Data/Users.json", json_encode($users));
    } else {
        header("Content-Type: application/json"); die("{\n    \"status\": 1\n}");
    }
} else {
    header("Content-Type: application/json"); die("{\n    \"status\": 1\n}");
}