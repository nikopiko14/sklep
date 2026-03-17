<?php
session_start();
require_once 'db_connect.php';

// --- KONFIGURACJA ---
$admin_user = "admin";
$admin_pass = "admin123";

// --- LOGIKA LOGOWANIA ---
if (isset($_POST['login_action'])) {
    if (($_POST['user'] ?? '') === $admin_user && ($_POST['password'] ?? '') === $admin_pass) {
        $_SESSION['admin_logged'] = true;
        $_SESSION['admin_user'] = $_POST['user'];
    } else {
        $error = "Nieprawidłowe dane logowania!";
    }
}

if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: admin.php");
    exit;
}

// --- LOGIKA PANELU ---
if (isset($_SESSION['admin_logged'])) {

    // ZMIANA STATUSU ZAMÓWIENIA - POPRAWIONA LOGIKA
    if (isset($_POST['update_status'])) {
        $id_zam = intval($_POST['zam_id']);
        $nowy_status = $_POST['nowy_status'];

        // Statusy muszą odpowiadać definicji ENUM w Twojej bazie danych SQL
        $allowed = ['oczekuje', 'oplacone', 'anulowane', 'dostarczone'];

        if (in_array($nowy_status, $allowed)) {
            $stmt = $conn->prepare("UPDATE zamowienia SET status = ? WHERE id = ?");
            $stmt->bind_param("si", $nowy_status, $id_zam);
            $stmt->execute();
            $stmt->close();
            // Przekierowanie z powrotem do karty zamówień
            header("Location: admin.php?tab=zamowienia&msg=status_zmieniony");
            exit;
        }
    }

    // USUWANIE ZAMÓWIENIA
    if (isset($_GET['usun_zam'])) {
        $id_zam = intval($_GET['usun_zam']);
        $conn->query("DELETE FROM zamowienia WHERE id = $id_zam");
        header("Location: admin.php?tab=zamowienia&msg=usunieto_zam");
        exit;
    }

    // USUWANIE PRODUKTU
    if (isset($_GET['delete_prod'])) {
        $id_prod = intval($_GET['delete_prod']);
        $conn->query("DELETE FROM produkty WHERE id = $id_prod");
        header("Location: admin.php?tab=produkty");
        exit;
    }

    // DODAWANIE PRODUKTU
    if (isset($_POST['add_product'])) {
        $nazwa = $_POST['p_nazwa'];
        $cena = floatval($_POST['p_cena']);
        $opis = $_POST['p_opis'];
        $sztuki = intval($_POST['p_sztuki']);
        $wybrane_kategorie = isset($_POST['p_kategorie']) ? $_POST['p_kategorie'] : [];

        $uploaded_photos = [];
        if (!empty($_FILES['p_fotos']['name'][0])) {
            if (!is_dir('zdjecia')) {
                mkdir('zdjecia', 0777, true);
            }
            foreach ($_FILES['p_fotos']['tmp_name'] as $key => $tmp_name) {
                if ($_FILES['p_fotos']['error'][$key] == 0) {
                    $ext = pathinfo($_FILES['p_fotos']['name'][$key], PATHINFO_EXTENSION);
                    $new_name = time() . "_" . $key . "." . $ext;
                    if (move_uploaded_file($tmp_name, "zdjecia/" . $new_name)) {
                        $uploaded_photos[] = $new_name;
                    }
                }
            }
        }

        $foto_string = !empty($uploaded_photos) ? implode(", ", $uploaded_photos) : "default.jpg";

        $stmt = $conn->prepare("INSERT INTO produkty (nazwa, cena, opis, dostepne_sztuki, zdjecie) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sdsis", $nazwa, $cena, $opis, $sztuki, $foto_string);

        if ($stmt->execute()) {
            $nowe_id_prod = $conn->insert_id;
            $stmt->close();
            if (!empty($wybrane_kategorie) && is_array($wybrane_kategorie)) {
                $stmt_kat = $conn->prepare("INSERT INTO produkty_kategorie (id_produktu, id_kategorii) VALUES (?, ?)");
                foreach ($wybrane_kategorie as $id_kat) {
                    $id_kat_int = intval($id_kat);
                    $stmt_kat->bind_param("ii", $nowe_id_prod, $id_kat_int);
                    $stmt_kat->execute();
                }
                $stmt_kat->close();
            }
            header("Location: admin.php?tab=produkty&msg=dodano");
            exit;
        }
    }

    // POBIERANIE DANYCH
    $zamowienia = $conn->query("SELECT z.*, k.imie, k.nazwisko, k.email FROM zamowienia z JOIN klienci k ON z.id_klient = k.id ORDER BY z.data DESC");
    $produkty_list = $conn->query("SELECT * FROM produkty ORDER BY id DESC");
    $kategorie_list = $conn->query("SELECT * FROM kategorie ORDER BY nazwa ASC");
}
?>

<!DOCTYPE html>
<html lang="pl">

<head>
    <meta charset="UTF-8">
    <title>Admin Panel - DrogoTu</title>
    <link rel="stylesheet" href="style.css">
    <script src="https://kit.fontawesome.com/f3d5492da4.js" crossorigin="anonymous"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="icon" href="logo_DrogoTu.png" type="png">
    <style>
        :root {
            --primary: #4e73df;
            --success: #1cc88a;
            --danger: #e74a3b;
            --dark: #2e3759;
            --light: #f8f9fc;
            --warning: #f6c23e;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: var(--light);
            margin: 0;
            color: #5a5c69;
        }

        .admin-navbar {
            background: white;
            height: 70px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 40px;
            box-shadow: 0 2px 15px rgba(0, 0, 0, 0.1);
            position: sticky;
            top: 0;
            z-index: 1000;
        }

        .admin-brand {
            display: flex;
            align-items: center;
            gap: 12px;
            text-decoration: none;
            color: var(--dark);
            font-weight: 700;
            font-size: 1.3rem;
        }

        .admin-brand img {
            height: 45px;
        }

        .admin-content {
            padding: 40px;
            max-width: 1400px;
            margin: 0 auto;
        }

        .admin-tabs {
            display: flex;
            gap: 10px;
            margin-bottom: 30px;
            border-bottom: 2px solid #e3e6f0;
        }

        .tab-link {
            padding: 12px 25px;
            cursor: pointer;
            border: none;
            background: none;
            font-weight: 600;
            color: #858796;
            border-bottom: 3px solid transparent;
        }

        .tab-link.active {
            color: var(--primary);
            border-bottom-color: var(--primary);
        }

        .admin-card {
            background: white;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        }

        .form-control {
            width: 100%;
            padding: 12px;
            border: 1px solid #d1d3e2;
            border-radius: 8px;
            margin-top: 5px;
            box-sizing: border-box;
        }

        .category-box {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(140px, 1fr));
            gap: 10px;
            background: #f8f9fc;
            padding: 15px;
            border-radius: 8px;
            border: 1px solid #d1d3e2;
            margin-top: 5px;
        }

        .admin-table {
            width: 100%;
            border-collapse: collapse;
        }

        .admin-table th {
            background: #f8f9fc;
            padding: 15px;
            text-align: left;
            font-size: 0.75rem;
            text-transform: uppercase;
            border-bottom: 2px solid #e3e6f0;
        }

        .admin-table td {
            padding: 15px;
            border-bottom: 1px solid #e3e6f0;
            font-size: 0.9rem;
            vertical-align: middle;
        }

        /* POPRAWIONE KOLORY STATUSÓW */
        .status-badge {
            padding: 8px 12px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            border: 1px solid transparent;
        }

        .status-oczekuje {
            background: #fff3cd;
            color: #856404;
            border-color: #ffeeba;
        }

        .status-oplacone {
            background: #d4edda;
            color: #155724;
            border-color: #c3e6cb;
        }

        .status-anulowane {
            background: #f8d7da;
            color: #721c24;
            border-color: #f5c6cb;
        }

        .status-dostarczone {
            background: #d1ecf1;
            color: #0c5460;
            border-color: #bee5eb;
        }

        .btn-submit {
            background: var(--success);
            color: white;
            border: none;
            padding: 15px;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 700;
            width: 100%;
            margin-top: 20px;
        }
    </style>
</head>

<body>

    <nav class="admin-navbar">
        <a href="admin.php" class="admin-brand">
            <img src="logo_DrogoTu.png" alt="Logo"> <span>DrogoTu Admin</span>
        </a>
        <div style="display:flex; gap:20px; align-items:center;">
            <a href="index.php" style="text-decoration:none; color:#858796;"><i class="fa-solid fa-store"></i> Sklep</a>
            <?php if (isset($_SESSION['admin_logged'])): ?>
                <a href="?logout=1" style="color:var(--danger); text-decoration:none; font-weight:700;">Wyloguj</a>
            <?php endif; ?>
        </div>
    </nav>

    <?php if (!isset($_SESSION['admin_logged'])): ?>
        <div style="display:flex; justify-content:center; align-items:center; height:80vh;">
            <div class="admin-card" style="width:360px; text-align:center;">
                <h2>Panel Logowania</h2>
                <?php if (isset($error)) echo "<p style='color:red'>$error</p>"; ?>
                <form method="POST">
                    <input type="text" name="user" class="form-control" placeholder="Użytkownik" required><br><br>
                    <input type="password" name="password" class="form-control" placeholder="Hasło" required><br><br>
                    <button type="submit" name="login_action" class="btn-submit" style="background:var(--primary)">ZALOGUJ SIĘ</button>
                </form>
            </div>
        </div>
    <?php else: ?>

        <div class="admin-content">
            <div class="admin-tabs">
                <button class="tab-link <?php echo (!isset($_GET['tab']) || $_GET['tab'] == 'zamowienia') ? 'active' : ''; ?>" onclick="location.href='?tab=zamowienia'">Zamówienia</button>
                <button class="tab-link <?php echo (isset($_GET['tab']) && $_GET['tab'] == 'produkty') ? 'active' : ''; ?>" onclick="location.href='?tab=produkty'">Produkty</button>
            </div>

            <div id="tab-zamowienia" style="display: <?php echo (!isset($_GET['tab']) || $_GET['tab'] == 'zamowienia') ? 'block' : 'none'; ?>">
                <div class="admin-card" style="padding:0; overflow:hidden;">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Klient</th>
                                <th>Produkty</th>
                                <th>Suma</th>
                                <th>Status</th>
                                <th>Akcje</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($z = $zamowienia->fetch_assoc()): ?>
                                <tr>
                                    <td><strong>#<?php echo $z['id']; ?></strong></td>
                                    <td><strong><?php echo $z['imie'] . " " . $z['nazwisko']; ?></strong><br><small><?php echo $z['email']; ?></small></td>
                                    <td>
                                        <div style="font-size:0.85rem;"><?php echo nl2br($z['produkty']); ?></div>
                                    </td>
                                    <td><strong><?php echo number_format($z['kwota_zamowienia'], 2, ',', ' '); ?> zł</strong></td>
                                    <td>
                                        <form method="POST" action="admin.php?tab=zamowienia">
                                            <input type="hidden" name="zam_id" value="<?php echo $z['id']; ?>">
                                            <input type="hidden" name="update_status" value="1">
                                            <select name="nowy_status" onchange="this.form.submit()"
                                                class="status-badge status-<?php echo $z['status']; ?>"
                                                style="cursor:pointer; outline:none; border:none; font-family:inherit;">
                                                <option value="oczekuje" <?php echo $z['status'] == 'oczekuje' ? 'selected' : ''; ?>>Oczekuje</option>
                                                <option value="oplacone" <?php echo $z['status'] == 'oplacone' ? 'selected' : ''; ?>>Opłacone</option>
                                                <option value="anulowane" <?php echo $z['status'] == 'anulowane' ? 'selected' : ''; ?>>Anulowane</option>
                                                <option value="dostarczone" <?php echo $z['status'] == 'dostarczone' ? 'selected' : ''; ?>>Dostarczone</option>
                                            </select>
                                        </form>
                                    </td>
                                    <td>
                                        <a href="?usun_zam=<?php echo $z['id']; ?>&tab=zamowienia" onclick="return confirm('Usunąć zamówienie?')" style="color:var(--danger)">
                                            <i class="fa-solid fa-trash"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <div id="tab-produkty" style="display: <?php echo (isset($_GET['tab']) && $_GET['tab'] == 'produkty') ? 'block' : 'none'; ?>">
                <div style="display:grid; grid-template-columns: 1fr 2fr; gap:30px; align-items: start;">
                    <div class="admin-card">
                        <h3 style="margin-top:0;">Dodaj produkt</h3>
                        <form method="POST" enctype="multipart/form-data">
                            <label>Nazwa</label>
                            <input type="text" name="p_nazwa" class="form-control" required>
                            <label style="display:block; margin-top:10px;">Cena (zł)</label>
                            <input type="number" step="0.01" name="p_cena" class="form-control" required>
                            <label style="display:block; margin-top:10px;">Ilość</label>
                            <input type="number" name="p_sztuki" class="form-control" required>
                            <label style="display:block; margin-top:10px;">Kategorie</label>
                            <div class="category-box">
                                <?php $kategorie_list->data_seek(0);
                                while ($k = $kategorie_list->fetch_assoc()): ?>
                                    <label style="font-size:0.85rem; cursor:pointer;">
                                        <input type="checkbox" name="p_kategorie[]" value="<?php echo $k['id']; ?>"> <?php echo $k['nazwa']; ?>
                                    </label>
                                <?php endwhile; ?>
                            </div>
                            <label style="display:block; margin-top:10px;">Opis</label>
                            <textarea name="p_opis" class="form-control" rows="3" required></textarea>
                            <label style="display:block; margin-top:10px;">Zdjęcia</label>
                            <input type="file" name="p_fotos[]" class="form-control" multiple accept="image/*">
                            <button type="submit" name="add_product" class="btn-submit">DODAJ DO BAZY</button>
                        </form>
                    </div>

                    <div class="admin-card" style="padding:0; overflow:hidden;">
                        <table class="admin-table">
                            <thead>
                                <tr>
                                    <th>Foto</th>
                                    <th>Produkt</th>
                                    <th>Cena</th>
                                    <th>Stan</th>
                                    <th>Akcje</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($p = $produkty_list->fetch_assoc()):
                                    $img = explode(',', $p['zdjecie'])[0]; ?>
                                    <tr>
                                        <td><img src="zdjecia/<?php echo trim($img); ?>" style="width:50px; height:50px; object-fit:cover; border-radius:6px;"></td>
                                        <td><strong><?php echo $p['nazwa']; ?></strong></td>
                                        <td><?php echo number_format($p['cena'], 2, ',', ' '); ?> zł</td>
                                        <td><?php echo $p['dostepne_sztuki']; ?> szt.</td>
                                        <td><a href="?delete_prod=<?php echo $p['id']; ?>&tab=produkty" onclick="return confirm('Usunąć produkt?')" style="color:var(--danger)"><i class="fa-solid fa-trash"></i></a></td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
</body>

</html>