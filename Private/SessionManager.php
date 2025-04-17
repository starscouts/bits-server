<?php

if (!file_exists($_SERVER['DOCUMENT_ROOT'] . "/Data")) mkdir($_SERVER['DOCUMENT_ROOT'] . "/Data");
if (!file_exists($_SERVER['DOCUMENT_ROOT'] . "/Data/Transactions")) mkdir($_SERVER['DOCUMENT_ROOT'] . "/Data/Transactions");
if (!file_exists($_SERVER['DOCUMENT_ROOT'] . "/Data/Goal.json")) file_put_contents($_SERVER['DOCUMENT_ROOT'] . "/Data/Goal.json", '{"name": "No goal","amount": {"eur": 0,"gbp": 0}}');
if (!file_exists($_SERVER['DOCUMENT_ROOT'] . "/Data/Users.json")) file_put_contents($_SERVER['DOCUMENT_ROOT'] . "/Data/Users.json", "{}");

global $SessionManagerAllowDisallowed;

if (isset($_COOKIE['PEH2_SESSION_TOKEN'])) {
    if (str_contains($_COOKIE['PEH2_SESSION_TOKEN'], ".") || str_contains($_COOKIE['PEH2_SESSION_TOKEN'], "/")) {
        header("Location: /Authentication/Start");
        die();
    }

    if (file_exists($_SERVER['DOCUMENT_ROOT'] . "/includes/tokens/" . str_replace(".", "", str_replace("/", "", $_COOKIE['PEH2_SESSION_TOKEN'])))) {
        $_PROFILE = json_decode(file_get_contents($_SERVER['DOCUMENT_ROOT'] . "/includes/tokens/" . str_replace(".", "", str_replace("/", "", $_COOKIE['PEH2_SESSION_TOKEN']))), true);

        $users = json_decode(file_get_contents($_SERVER['DOCUMENT_ROOT'] . "/Private/Data/Users.json"), true);
        $users[$_PROFILE["id"]] = $_PROFILE["name"];
        file_put_contents($_SERVER['DOCUMENT_ROOT'] . "/Private/Data/Users.json", json_encode($users));
    } else {
        header("Location: /Authentication/Start");
        die();
    }
} else {
    header("Location: /Authentication/Start");
    die();
}