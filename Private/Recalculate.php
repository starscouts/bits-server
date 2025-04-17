<?php

$rate = (float)trim(file_get_contents("./ExchangeRate.txt"));

$goal = json_decode(file_get_contents("./Data/Goal.json"), true);
$goal["amount"]["gbp"] = $goal["amount"]["eur"] * $rate;
file_put_contents("./Data/Goal.json", json_encode($goal, JSON_PRETTY_PRINT));

foreach (array_filter(scandir("./Data/Transactions"), function ($i) { return !str_starts_with($i, "."); }) as $file) {
    $transaction = json_decode(file_get_contents("./Data/Transactions/$file"), true);

    if ($transaction["amount"]["original"] === "eur") {
        $transaction["amount"]["gbp"] = $transaction["amount"]["eur"] * $rate;
    } else {
        $transaction["amount"]["eur"] = $transaction["amount"]["gbp"] * (1 / $rate);
    }

    file_put_contents("./Data/Transactions/$file", json_encode($transaction, JSON_PRETTY_PRINT));
}

$expenses = json_decode(file_get_contents("./Data/Expenses.json"), true);

foreach ($expenses as $index => $expense) {
    if ($expense["original"] === "eur") {
        $expenses[$index]["gbp"] = $expense["eur"] * $rate;
    } else {
        $expenses[$index]["eur"] = $expense["gbp"] * (1 / $rate);
    }
}

file_put_contents("./Data/Expenses.json", json_encode($expenses, JSON_PRETTY_PRINT));