<?php

header("Content-Type: text/plain");
// TODO: handle errors

if (!isset($_GET['code'])) {
    die();
}

$appdata = json_decode(file_get_contents($_SERVER['DOCUMENT_ROOT'] . "/Private/Application.json"), true);

$crl = curl_init('https://privateauth.equestria.dev/hub/api/rest/oauth2/token');
curl_setopt($crl, CURLOPT_RETURNTRANSFER, true);
curl_setopt($crl, CURLINFO_HEADER_OUT, true);
curl_setopt($crl, CURLOPT_POST, true);
curl_setopt($crl, CURLOPT_HTTPHEADER, [
    "Authorization: Basic " . base64_encode($appdata["id"] . ":" . $appdata["secret"]),
    "Content-Type: application/x-www-form-urlencoded",
    "Accept: application/json"
]);
curl_setopt($crl, CURLOPT_POSTFIELDS, "grant_type=authorization_code&redirect_uri=" . urlencode("http" . ($_SERVER['HTTPS'] ? "s" : "") . "://" . $_SERVER['HTTP_HOST'] . "/Authentication/Callback") . "&code=" . $_GET['code']);

$result = curl_exec($crl);
$result = json_decode($result, true);

curl_close($crl);

if (isset($result["access_token"])) {
    $crl = curl_init('https://privateauth.equestria.dev/hub/api/rest/users/me');
    curl_setopt($crl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($crl, CURLINFO_HEADER_OUT, true);
    curl_setopt($crl, CURLOPT_HTTPHEADER, [
        "Authorization: Bearer " . $result["access_token"],
        "Accept: application/json"
    ]);

    $result = curl_exec($crl);
    $result = json_decode($result, true);

    if (!file_exists($_SERVER['DOCUMENT_ROOT'] . "/Private/SessionTokens")) mkdir($_SERVER['DOCUMENT_ROOT'] . "/Private/SessionTokens");

    if (in_array($result["id"], json_decode(file_get_contents($_SERVER['DOCUMENT_ROOT'] . "/Private/AllowedUsers.json"), true))) {
        $token = bin2hex(random_bytes(32));
        file_put_contents($_SERVER['DOCUMENT_ROOT'] . "/Private/SessionTokens/" . $token, json_encode($result));
        header("Set-Cookie: BITS_SESSION_TOKEN=" . $token . "; SameSite=None; Path=/; Secure; HttpOnly");

        header("Location: /Authentication/Success");
    } else {
        header("Location: /Authentication/Disallowed");
    }

    die();
}