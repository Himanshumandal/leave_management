<?php

// function loadEnv(string $file): void
// {
//     if (!file_exists($file)) {
//         return;
//     }

//     $lines = file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

//     foreach ($lines as $line) {

//         $line = trim($line);

//         // Ignore comments
//         if ($line === '' || str_starts_with($line, '#')) {
//             continue;
//         }

//         if (!str_contains($line, '=')) {
//             continue;
//         }

//         [$key, $value] = explode('=', $line, 2);

//         $key = trim($key);
//         $value = trim($value);

//         // Remove surrounding quotes
//         $value = trim($value, "\"'");

//         $_ENV[$key] = $value;
//         $_SERVER[$key] = $value;
//     }
// }


function env(string $key, $default = null)
{
    return $_ENV[$key] ?? $default;
}