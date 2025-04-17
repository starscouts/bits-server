<?php

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

require_once $_SERVER['DOCUMENT_ROOT'] . "/Private/SessionManager.php";
header("Content-Type: application/json");

$users = json_decode(file_get_contents($_SERVER['DOCUMENT_ROOT'] . "/Private/Data/Users.json"), true);
$list = array_reverse(array_filter(scandir($_SERVER['DOCUMENT_ROOT'] . "/Private/Data/Transactions"), function ($i) {
    return !str_starts_with($i, ".") && str_ends_with($i, ".json");
}));
$plist = [];

foreach ($list as $id) {
    if (trim(file_get_contents($_SERVER['DOCUMENT_ROOT'] . "/Private/Data/Transactions/" . $id)) !== "") {
        $item = json_decode(file_get_contents($_SERVER['DOCUMENT_ROOT'] . "/Private/Data/Transactions/" . $id), true);

        $item["author"] = [
            "id" => $item["author"],
            "name" => $item["name"] ?? $users[$item["author"]] ?? $item["author"],
            "avatar" => $item["picture"] ?? "https://privateauth.equestria.dev/hub/api/rest/avatar/" . $item["author"] . "?dpr=2&size=48"
        ];
        $item["date"] = [
            "absolute" => $item["date"],
            "relative" => trim(timeAgo($item["date"]))
        ];
        $plist[] = $item;
    }
}

die(json_encode($plist));