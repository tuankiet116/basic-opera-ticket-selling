<?php
if (!defined("PAGINATE_NUMBER")) define("PAGINATE_NUMBER", 20);

if (!defined("HTTP_CODE")) define("HTTP_CODE", [
    "INTERNAL_SERVER" => 500,
    "BAD_REQUEST" => 400
]);

if (!defined("CONFIG_HALL_1")) define("CONFIG_HALL_1", [
    ["row" => "A", "start" => 1, "end" => 21],
    ["row" => "B", "start" => 1, "end" => 28],
    ["row" => "C", "start" => 1, "end" => 29],
    ["row" => "D", "start" => 1, "end" => 30],
    ["row" => "E", "start" => 1, "end" => 31],
    ["row" => "F", "start" => 1, "end" => 30],
    ["row" => "G", "start" => 1, "end" => 31],
    ["row" => "H", "start" => 1, "end" => 28],
    ["row" => "I", "start" => 1, "end" => 27],
    ["row" => "K", "start" => 1, "end" => 28],
    ["row" => "L", "start" => 1, "end" => 27],
    ["row" => "M", "start" => 1, "end" => 28],
    ["row" => "N", "start" => 1, "end" => 27],
    ["row" => "P", "start" => 1, "end" => 28],
    ["row" => "Q", "start" => 1, "end" => 27],
    ["row" => "R", "start" => 1, "end" => 22],
    ["row" => "S", "start" => 1, "end" => 22],
    ["row" => "T", "start" => 1, "end" => 20],
    ["row" => "U", "start" => 1, "end" => 8],
]);

if (!defined("CONFIG_HALL_2")) define("CONFIG_HALL_2", [
    ["row" => "LG", "start" => 1, "end" => 10],
    ["row" => "AA", "start" => 1, "end" => 10],
    ["row" => "BB", "start" => 1, "end" => 12],
    ["row" => "CC", "start" => 1, "end" => 12],
    ["row" => "A", "start" => 1, "end" => 32],
    ["row" => "B", "start" => 1, "end" => 33],
    ["row" => "C", "start" => 1, "end" => 32],
    ["row" => "D", "start" => 1, "end" => 31],
    ["row" => "E", "start" => 1, "end" => 32],
    ["row" => "F", "start" => 1, "end" => 31],
    ["row" => "G", "start" => 1, "end" => 12],
    ["row" => "H", "start" => 1, "end" => 10],
    ["row" => "I", "start" => 12, "end" => 17],
]);

if (!defined("CHUNK_SIZE_BROADCAST")) define("CHUNK_SIZE_BROADCAST", 5);

if (!defined("PRICE_DISCOUNT_TYPE")) define("PRICE_DISCOUNT_TYPE", "price-discount");
if (!defined("PERCENTAGE_DISCOUNT_TYPE")) define("PERCENTAGE_DISCOUNT_TYPE", "percentage-discount");