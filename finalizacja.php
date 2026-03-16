<?php
session_start();
require_once 'db_connect.php';

if (empty($_SESSION['koszyk'])) {
    header("Location: index.php");
    exit;
}

$show_success = false;
$nr_zamowienia = 0;
$suma_calkowita = 0;

if (isset($_POST['finalizuj'])) {
    $imie = $conn->real_escape_string($_POST['imie']);
    $nazwisko = $conn->real_escape_string($_POST['nazwisko']);
    $email = $conn->real_escape_string($_POST['email']);
    $tel = $conn->real_escape_string($_POST['tel']);
    $miasto = $conn->real_escape_string($_POST['miasto']);
    $ulica = $conn->real_escape_string($_POST['ulica']);
    $nr = $conn->real_escape_string($_POST['nr']);

    $ilosci = array_count_values($_SESSION['koszyk']);
    $ids_string = implode(',', array_keys($ilosci));
    $produkty_query = $conn->query("SELECT * FROM produkty WHERE id IN ($ids_string)");

    $opisy = [];
    while ($p = $produkty_query->fetch_assoc()) {
        $ile = $ilosci[$p['id']];
        $suma_calkowita += ($p['cena'] * $ile);
        $opisy[] = $p['nazwa'] . " (x" . $ile . ")";
    }
    $produkty_tekst = implode(", ", $opisy);

    $conn->begin_transaction();
    try {
        // 1. Klient
        $stmt_k = $conn->prepare("INSERT INTO klienci (imie, nazwisko, nr_telefonu, email, adres_miasto, adres_ulica, adres_nr) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt_k->bind_param("sssssss", $imie, $nazwisko, $tel, $email, $miasto, $ulica, $nr);
        $stmt_k->execute();
        $id_klienta = $conn->insert_id;

        // 2. Zamówienie
        $stmt_z = $conn->prepare("INSERT INTO zamowienia (id_klient, produkty, produkty_id, kwota_zamowienia, status) VALUES (?, ?, ?, ?, 'oczekuje')");
        $stmt_z->bind_param("issd", $id_klienta, $produkty_tekst, $ids_string, $suma_calkowita);
        $stmt_z->execute();
        $nr_zamowienia = $conn->insert_id;

        // 3. Magazyn
        foreach ($ilosci as $id_p => $szt) {
            $conn->query("UPDATE produkty SET dostepne_sztuki = dostepne_sztuki - $szt WHERE id = $id_p");
        }

        $conn->commit();
        $_SESSION['koszyk'] = [];
        $show_success = true;
    } catch (Exception $e) {
        $conn->rollback();
        die("Błąd: " . $e->getMessage());
    }
}
?>
<!DOCTYPE html>
<html lang="pl">

<head>
    <meta charset="UTF-8">
    <title>Podsumowanie - DrogoTu</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://fonts.googleapis.com/css2?family=Work+Sans:wght@300;400;700&display=swap" rel="stylesheet">
    <script src="https://kit.fontawesome.com/f3d5492da4.js" crossorigin="anonymous"></script>
    <link rel="icon" href="logo_DrogoTu.png">
</head>

<body style="background:#f8f9fa; display:flex; align-items:center; justify-content:center; min-height:100vh;">
    <div style="max-width:500px; width:90%; background:white; padding:40px; border-radius:25px; text-align:center; box-shadow:0 10px 30px rgba(0,0,0,0.1);">
        <?php if ($show_success): ?>
            <i class="fa-solid fa-circle-check" style="font-size:70px; color:#2ecc71;"></i>
            <h1 style="margin:20px 0;">Sukces!</h1>
            <p>Zamówienie <b>#<?php echo $nr_zamowienia; ?></b> zostało przyjęte.</p>
            <p>Kwota: <b><?php echo number_format($suma_calkowita, 2, ',', ' '); ?> zł</b></p>
            <a href="index.php" class="btn-koszyk-nav" style="display:inline-block; margin-top:20px; text-decoration:none;">Wróć do sklepu</a>
        <?php else: ?>
            <h1>Coś poszło nie tak...</h1>
            <a href="index.php">Wróć i spróbuj ponownie</a>
        <?php endif; ?>
    </div>
</body>

</html>