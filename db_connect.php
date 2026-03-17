<?php
$host = "sql203.infinityfree.com";
$user = "if0_41413115";
$pass = "Niko2608";
$db   = "if0_41413115_sklep_drogotu";

// Tworzenie połączenia
$conn = new mysqli($host, $user, $pass, $db);

// Ustawienie kodowania znaków (żeby polskie litery działały)
$conn->set_charset("utf8mb4");

// Sprawdzenie czy nie ma błędów
if ($conn->connect_error) {
    die("Błąd połączenia: " . $conn->connect_error);
}
?>