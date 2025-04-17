<?php

header("Location: https://privateauth.equestria.dev/hub/api/rest/oauth2/auth?client_id=" . json_decode(file_get_contents($_SERVER['DOCUMENT_ROOT'] . "/Private/Application.json"), true)["id"] . "&response_type=code&redirect_uri=http" . ($_SERVER['HTTPS'] ? "s" : "") . "://" . $_SERVER['HTTP_HOST'] . "/Authentication/Callback&scope=Hub&request_credentials=default&access_type=offline");
die();
