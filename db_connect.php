<?php
$host = "localhost";
$user = "vh15473_admin";
$pass = "Niko26082010#";
$db   = "vh15473_sklep_drogotu";

// Tworzenie połączenia
$conn = new mysqli($host, $user, $pass, $db);

// Ustawienie kodowania znaków (żeby polskie litery działały)
$conn->set_charset("utf8mb4");

// Sprawdzenie czy nie ma błędów
if ($conn->connect_error) {
    die("Błąd połączenia: " . $conn->connect_error);
}
?>