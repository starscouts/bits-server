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

function timeAgo($time): string {
    if (!is_numeric($time)) {
        $time = strtotime($time);
    }

    $periods = ["second", "minute", "hour", "day", "week", "month", "year", "age"];
    $lengths = array("60", "60", "24", "7", "4.35", "12", "100");

    $now = time();

    $difference = $now - $time;
    if ($difference <= 10 && $difference >= 0) {
        return $tense = "now";
    } elseif ($difference > 0) {
        $tense = "ago";
    } else {
        $tense = "later";
    }

    for ($j = 0; $difference >= $lengths[$j] && $j < count($lengths)-1; $j++) {
        $difference /= $lengths[$j];
    }

    $difference = round($difference);

    $period =  $periods[$j] . ($difference >1 ? "s" :'');
    return "{$difference} {$period} {$tense} ";
}

if (!isset($_GET['Transaction'])) {
    die();
}

$list = array_filter(scandir($_SERVER['DOCUMENT_ROOT'] . "/Private/Data/Transactions"), function ($i) {
    return !str_starts_with($i, ".") && str_ends_with($i, ".json");
});
$identities = json_decode(file_get_contents($_SERVER['DOCUMENT_ROOT'] . "/Private/Data/Identities.json"), true);
$users = json_decode(file_get_contents($_SERVER['DOCUMENT_ROOT'] . "/Private/Data/Users.json"), true);

$list = array_filter(array_map(function ($item) {
    $i = json_decode(file_get_contents($_SERVER['DOCUMENT_ROOT'] . "/Private/Data/Transactions/" . $item), true);
    $i["__file_id"] = $item;
    return $i;
}, $list), function ($item) {
    return ($item["date"] === base64url_decode($_GET['Transaction']));
});
sort($list);
var_dump($list);

$item = json_decode(file_get_contents($_SERVER['DOCUMENT_ROOT'] . "/Private/Data/Transactions/" . $list[0]["__file_id"]), true);
$ntfy = json_decode(file_get_contents($_SERVER['DOCUMENT_ROOT'] . "/Private/Application.json"), true)["ntfy"];

@file_get_contents('https://' . $ntfy["server"] . '/' . $ntfy["topic"], false, stream_context_create([
    'http' => [
        'method' => 'POST',
        'header' =>
            "Content-Type: text/plain\r\n" .
            "Title: " . $identities[$_PROFILE["id"]]["name"] . " deleted a transaction\r\n" .
            "Priority: default\r\n" .
            "Tags: bits\r\n" .
            "Authorization: Basic " . base64_encode($ntfy["user"] . ":" . $ntfy["password"]),
        'content' => "Original transaction created by " . ($item["name"] ?? $users[$item["author"]] ?? $item["author"]) . " " . timeAgo($item["date"])
    ]
]));

unlink($_SERVER['DOCUMENT_ROOT'] . "/Private/Data/Transactions/" . $list[0]["__file_id"]);