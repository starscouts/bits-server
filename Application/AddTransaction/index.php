<?php

require_once $_SERVER['DOCUMENT_ROOT'] . "/Private/SessionManager.php"; global $_PROFILE;

/**
 * Encode data to Base64URL
 * @param string $data
 * @return boolean|string
 */
function base64url_encode($data)
{
    // First of all you should encode $data to Base64 string
    $b64 = base64_encode($data);

    // Make sure you get a valid result, otherwise, return FALSE, as the base64_encode() function do
    if ($b64 === false) {
        return false;
    }

    // Convert Base64 to Base64URL by replacing “+” with “-” and “/” with “_”
    $url = strtr($b64, '+/', '-_');

    // Remove padding character from the end of line and return the Base64URL result
    return rtrim($url, '=');
}

/**
 * Decode data from Base64URL
 * @param string $data
 * @param boolean $strict
 * @return boolean|string
 */
function base64url_decode($data, $strict = false)
{
    // Convert Base64URL to Base64 by replacing “-” with “+” and “_” with “/”
    $b64 = strtr($data, '-_', '+/');

    // Decode Base64 string and return the original data
    return base64_decode($b64, $strict);
}

if (!isset($_GET['Currency']) || !isset($_GET['Amount']) || !is_numeric($_GET['Amount']) || !isset($_GET['Description']) || !isset($_GET['Operation'])) {
    die();
}

$transaction = [];
$exchangeRate = (float)file_get_contents($_SERVER['DOCUMENT_ROOT'] . "/Private/ExchangeRate.txt");
$identities = json_decode(file_get_contents($_SERVER['DOCUMENT_ROOT'] . "/Private/Data/Identities.json"), true);

$eurToGbp = $exchangeRate;
$gbpToEur = 1 / $eurToGbp;

if ($_GET['Currency'] === "€") {
    $transaction = [
        'author' => $_PROFILE['id'],
        'type' => $_GET['Operation'] === "+" || $_GET['Operation'] === " " ? "gain" : "pay",
        'amount' => [
            'eur' => (float)$_GET['Amount'],
            'gbp' => (float)$_GET['Amount'] * $eurToGbp,
            'original' => 'eur'
        ],
        'date' => date("c"),
        'description' => base64url_decode($_GET['Description']),
        'name' => $identities[$_PROFILE["id"]]["name"],
        'picture' => $identities[$_PROFILE["id"]]["picture"]
    ];
    $notificationMoney = "£" . number_format((float)$_GET['Amount'] * $eurToGbp, 2) . "/" . number_format((float)$_GET['Amount'], 2) . "€";
} else {
    $transaction = [
        'author' => $_PROFILE['id'],
        'type' => $_GET['Operation'] === "+" || $_GET['Operation'] === " " ? "gain" : "pay",
        'amount' => [
            'eur' => (float)$_GET['Amount'] * $gbpToEur,
            'gbp' => (float)$_GET['Amount'],
            'original' => 'gbp'
        ],
        'date' => date("c"),
        'description' => base64url_decode($_GET['Description']),
        'name' => $identities[$_PROFILE["id"]]["name"],
        'picture' => $identities[$_PROFILE["id"]]["picture"]
    ];
    $notificationMoney = "£" . number_format((float)$_GET['Amount'], 2) . "/" . number_format((float)$_GET['Amount'] * $gbpToEur, 2) . "€";
}

$ntfy = json_decode(file_get_contents($_SERVER['DOCUMENT_ROOT'] . "/Private/Application.json"), true)["ntfy"];

@file_get_contents('https://' . $ntfy["server"] . '/' . $ntfy["topic"], false, stream_context_create([
    'http' => [
        'method' => 'POST',
        'header' =>
            "Content-Type: text/plain\r\n" .
            "Title: " . $identities[$_PROFILE["id"]]["name"] . ($_GET['Operation'] === "+" || $_GET['Operation'] === " " ? " added " : " removed ") . $notificationMoney . "\r\n" .
            "Priority: default\r\n" .
            "Tags: bits\r\n" .
            "Authorization: Basic " . base64_encode($ntfy["user"] . ":" . $ntfy["password"]),
        'content' => trim(base64url_decode($_GET['Description'])) !== "" ? base64url_decode($_GET['Description']) : "(No description)"
    ]
]));

file_put_contents($_SERVER['DOCUMENT_ROOT'] . "/Private/Data/Transactions/" . date('U') . ".json", json_encode($transaction));