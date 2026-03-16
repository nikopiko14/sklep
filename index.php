<?php
session_start();
require_once 'db_connect.php';

if (!isset($_SESSION['koszyk'])) $_SESSION['koszyk'] = [];

// --- LOGIKA KOSZYKA ---
if (isset($_GET['akcja'])) {
    $id_prod = isset($_GET['id']) ? intval($_GET['id']) : 0;
    $max_dostepne = 0;
    if ($id_prod > 0) {
        $check = $conn->query("SELECT dostepne_sztuki FROM produkty WHERE id = $id_prod");
        $row_check = $check->fetch_assoc();
        $max_dostepne = $row_check ? $row_check['dostepne_sztuki'] : 0;
    }

    if ($_GET['akcja'] == 'dodaj') {
        $ile_chce_dodac = isset($_GET['ilosc']) ? intval($_GET['ilosc']) : 1;
        $ile_w_koszu = 0;
        foreach ($_SESSION['koszyk'] as $item) if ($item == $id_prod) $ile_w_koszu++;

        if ($max_dostepne >= ($ile_w_koszu + $ile_chce_dodac)) {
            for ($i = 0; $i < $ile_chce_dodac; $i++) $_SESSION['koszyk'][] = $id_prod;
            header("Location: index.php?status=dodano&last_id=$id_prod#prod_$id_prod");
        } else {
            header("Location: index.php?status=brak_sztuk&last_id=$id_prod#prod_$id_prod");
        }
        exit;
    }

    if ($_GET['akcja'] == 'ustaw_ilosc') {
        $nowa_ilosc = intval($_GET['ilosc']);
        if ($nowa_ilosc > $max_dostepne) $nowa_ilosc = $max_dostepne;
        $_SESSION['koszyk'] = array_values(array_filter($_SESSION['koszyk'], fn($v) => $v != $id_prod));
        if ($nowa_ilosc > 0) {
            for ($i = 0; $i < $nowa_ilosc; $i++) $_SESSION['koszyk'][] = $id_prod;
        }
        header("Location: index.php?status=zaktualizowano&last_id=$id_prod#prod_$id_prod");
        exit;
    }

    if ($_GET['akcja'] == 'usun_calosc') {
        $_SESSION['koszyk'] = array_values(array_filter($_SESSION['koszyk'], fn($v) => $v != $id_prod));
        header("Location: index.php?status=usunieto");
        exit;
    }

    if ($_GET['akcja'] == 'wyczysc_wszystko') {
        $_SESSION['koszyk'] = [];
        header("Location: index.php?status=wyczyszczono");
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="pl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <script src="https://kit.fontawesome.com/f3d5492da4.js" crossorigin="anonymous"></script>
    <link href="https://fonts.googleapis.com/css2?family=Work+Sans:wght@300;400;700;800&display=swap" rel="stylesheet">
    <link rel="icon" href="logo_DrogoTu.png">
    <title>Sklep DrogoTu</title>
</head>

<body>

    <div id="toast-container" class="toast-overlay">
        <div class="toast-box" id="toast-content">
            <i id="toast-icon" class="fa-solid"></i>
            <span id="toast-message"></span>
        </div>
    </div>

    <div id="confirm-modal" class="modal-overlay" style="display:none; z-index:11000; background:rgba(0,0,0,0.5); position:fixed; inset:0; align-items:center; justify-content:center;">
        <div class="toast-box" style="flex-direction:column; text-align:center; padding:30px; background:white; border-radius:15px;">
            <p id="confirm-text" style="margin-bottom:20px; font-weight:bold;"></p>
            <div style="display:flex; gap:10px;">
                <button id="confirm-yes" class="btn-kup" style="background:#2ecc71; border:none; padding:10px 20px; color:white; border-radius:5px; cursor:pointer;">Tak</button>
                <button onclick="zamknijConfirm()" class="btn-reset" style="padding:10px 20px; border-radius:5px; cursor:pointer;">Anuluj</button>
            </div>
        </div>
    </div>

    <nav>
        <div class="nav-container">
            <div class="nav-logo">
                <a href="index.php" style="text-decoration:none; color:black; font-weight:bold; font-size:1.2em; display:flex; align-items:center; gap:10px;">
                    <img src="logo_DrogoTu.png" alt="Logo" style="height:35px;"> DrogoTu
                </a>
            </div>
            <div class="nav-links">
                <a href="index.php"><i class="fa-solid fa-house"></i> Główna</a>
                <a href="#produkty"><i class="fa-solid fa-tag"></i> Produkty</a>
                <a href="regulamin.html"><i class="fa-solid fa-file-contract"></i> Regulamin</a>
                <a href="#kontakt"><i class="fa-solid fa-envelope"></i> Kontakt</a>
            </div>
            <div class="nav-cart-wrapper">
                <button onclick="toggleKoszyk()" class="btn-koszyk-nav">
                    <i class="fa-solid fa-basket-shopping"></i> KOSZYK (<?php echo count($_SESSION['koszyk']); ?>)
                </button>
            </div>
        </div>
    </nav>

    <div id="koszyk-okno" class="koszyk-boczny">
        <div id="koszyk-lista">
            <div style="display:flex; justify-content: space-between; align-items: center; padding: 20px; border-bottom:1px solid #eee;">
                <h3 style="margin:0;">Twój Koszyk</h3>
                <button onclick="toggleKoszyk()" style="background:none; border:none; font-size:25px; cursor:pointer;">&times;</button>
            </div>
            <div style="padding: 20px;">
                <?php if (empty($_SESSION['koszyk'])): ?>
                    <p style="text-align:center;">Koszyk jest pusty.</p>
                    <?php else:
                    $ilosci = array_count_values($_SESSION['koszyk']);
                    $ids = implode(',', array_keys($ilosci));
                    $wynik = $conn->query("SELECT * FROM produkty WHERE id IN ($ids)");
                    $suma_calkowita = 0;
                    while ($p = $wynik->fetch_assoc()):
                        $sztuk = $ilosci[$p['id']];
                        $suma_calkowita += ($p['cena'] * $sztuk);
                    ?>
                        <div class="koszyk-item">
                            <img src="zdjecia/<?php echo explode(',', $p['zdjecie'])[0]; ?>" class="koszyk-img">
                            <div style="flex-grow:1;">
                                <div style="display:flex; justify-content:space-between; font-size:13px;">
                                    <strong><?php echo htmlspecialchars($p['nazwa']); ?></strong>
                                    <a href="javascript:void(0)" onclick="potwierdzUsuniecie(<?php echo $p['id']; ?>)" style="color:red;"><i class="fa-solid fa-trash-can"></i></a>
                                </div>
                                <div style="display:flex; align-items:center; margin-top:5px; gap: 5px;">
                                    <div class="qty-pill">
                                        <button class="btn-pill" onclick="zmienInput(<?php echo $p['id']; ?>, -1, <?php echo $p['dostepne_sztuki']; ?>)">-</button>
                                        <input type="number" id="input_qty_<?php echo $p['id']; ?>" value="<?php echo $sztuk; ?>" class="input-pill" readonly>
                                        <button class="btn-pill" onclick="zmienInput(<?php echo $p['id']; ?>, 1, <?php echo $p['dostepne_sztuki']; ?>)">+</button>
                                    </div>
                                    <button onclick="zatwierdzIlosc(<?php echo $p['id']; ?>)" class="btn-confirm-qty"><i class="fa-solid fa-check"></i></button>
                                    <span style="margin-left:auto; font-weight:bold;"><?php echo number_format($p['cena'] * $sztuk, 2, ',', ' '); ?> zł</span>
                                </div>
                            </div>
                        </div>
                    <?php endwhile; ?>
                    <div style="border-top:1px solid #eee; padding-top: 15px;">
                        <h4 style="text-align:right;">Suma: <?php echo number_format($suma_calkowita, 2, ',', ' '); ?> zł</h4>
                        <a href="javascript:void(0)" onclick="potwierdzWyczyszczenie()" style="display:block; text-align:center; color:red; font-size:12px; margin:10px 0; text-decoration:none;">Wyczyść koszyk</a>
                        <button onclick="pokazFormularz()" class="btn-order">DALEJ: DOSTAWA</button>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <div id="koszyk-formularz" style="display:none; padding:20px;">
            <button onclick="pokazListe()" style="border:none; background:none; color:#007bff; cursor:pointer; margin-bottom:20px; font-weight: bold;"><i class="fa-solid fa-arrow-left"></i> Wróć do koszyka</button>
            <form method="POST" action="finalizacja.php">
                <input type="text" name="imie" placeholder="Imię" required class="form-input">
                <input type="text" name="nazwisko" placeholder="Nazwisko" required class="form-input">
                <input type="email" name="email" placeholder="Email" required class="form-input">
                <input type="text" name="tel" placeholder="Nr telefonu" required class="form-input">
                <input type="text" name="miasto" placeholder="Miasto" required class="form-input">
                <div style="display:flex; gap:10px;">
                    <input type="text" name="ulica" placeholder="Ulica" required style="flex:2;" class="form-input">
                    <input type="text" name="nr" placeholder="Nr" required style="flex:1;" class="form-input">
                </div>
                <label style="font-size:13px; cursor:pointer; display: flex; align-items: center; gap: 8px; margin: 15px 0;">
                    <input type="checkbox" id="reg-check" onchange="validateForm()" required>
                    <span>Akceptuję <a href="regulamin.html" target="_blank">regulamin sklepu</a></span>
                </label>
                <button type="submit" name="finalizuj" id="btn-final" disabled class="btn-final-order">ZAMAWIAM I PŁACĘ</button>
            </form>
        </div>
    </div>

    <main>
        <h1 id="produkty" style="text-align:center; margin-top:40px;">NASZE PRODUKTY</h1>
        <hr class="header-hr">

        <div class="filter-wrapper">
            <div class="filter-bar">
                <div class="search-box">
                    <i class="fa-solid fa-magnifying-glass"></i>
                    <input type="text" id="searchInput" placeholder="Czego szukasz?" onkeyup="aplikujWszystkieFiltry()">
                </div>
                <div class="sort-box">
                    <label>Sortowanie</label>
                    <select id="sortSelect" onchange="sortujProdukty()">
                        <option value="default">Polecane</option>
                        <option value="price-asc">Najtańsze</option>
                        <option value="price-desc">Najdroższe</option>
                        <option value="name-asc">Nazwa A-Z</option>
                    </select>
                </div>
            </div>

            <div class="extra-filters">
                <div class="category-filters">
                    <button class="btn-kat active" onclick="ustawKategorie('all', this)">Wszystkie</button>
                    <?php
                    $kats = $conn->query("SELECT nazwa FROM kategorie ORDER BY nazwa ASC");
                    while ($k = $kats->fetch_assoc()):
                    ?>
                        <button class="btn-kat" onclick="ustawKategorie('<?php echo htmlspecialchars($k['nazwa']); ?>', this)">
                            <?php echo htmlspecialchars($k['nazwa']); ?>
                        </button>
                    <?php endwhile; ?>
                </div>
                <div class="price-inputs">
                    <div class="price-field">
                        <span>od</span>
                        <input type="number" id="priceMin" placeholder="zł" min="0" oninput="aplikujWszystkieFiltry()">
                    </div>
                    <div class="price-field">
                        <span>do</span>
                        <input type="number" id="priceMax" placeholder="zł" min="0" oninput="aplikujWszystkieFiltry()">
                    </div>
                    <button class="btn-reset" onclick="resetujFiltry()"><i class="fa-solid fa-rotate-right"></i></button>
                </div>
            </div>
        </div>

        <div class="produkty-grid">
            <?php
            $query = "SELECT p.*, GROUP_CONCAT(k.nazwa SEPARATOR ', ') as kat_list 
                      FROM produkty p 
                      LEFT JOIN produkty_kategorie pk ON p.id = pk.id_produktu 
                      LEFT JOIN kategorie k ON pk.id_kategorii = k.id 
                      GROUP BY p.id";
            $res = $conn->query($query);

            while ($row = $res->fetch_assoc()):
                $ile_k = count(array_keys($_SESSION['koszyk'], $row['id']));
                $fotos = array_map('trim', explode(',', $row['zdjecie']));
                $stan_klasa = ($row['dostepne_sztuki'] < 5) ? 'stan-niski' : 'stan-ok';
                $js_tytul = rawurlencode($row['nazwa']);
                $js_opis = rawurlencode($row['opis']);
                $js_fotos = rawurlencode(json_encode($fotos));
            ?>
                <div class="kafelekprodukt" id="prod_<?php echo $row['id']; ?>" data-category="<?php echo htmlspecialchars($row['kat_list']); ?>" data-price="<?php echo $row['cena']; ?>">
                    <?php if ($ile_k > 0): ?>
                        <div class="badge-koszyk" onclick="potwierdzUsuniecie(<?php echo $row['id']; ?>)">
                            <span><?php echo $ile_k; ?></span><i class="fa-solid fa-trash"></i>
                        </div>
                    <?php endif; ?>

                    <div class="foto-contener">
                        <img src="zdjecia/<?php echo $fotos[0]; ?>" class="galeria-img">
                    </div>

                    <div class="info-contener">
                        <span class="kat-badge"><?php echo htmlspecialchars($row['kat_list']); ?></span>
                        <h3><?php echo htmlspecialchars($row['nazwa']); ?></h3>
                        <div class="stan-magazynowy">
                            <i class="fa-solid fa-warehouse"></i> Magazyn:
                            <span class="<?php echo $stan_klasa; ?>"><?php echo $row['dostepne_sztuki']; ?> szt.</span>
                        </div>
                        <div class="opis-wrapper">
                            <div class="opis-kontener">
                                <p class="opis-tekst"><?php echo htmlspecialchars($row['opis']); ?></p>
                                <div class="gradient-fade"></div>
                            </div>
                            <button class="btn-more" onclick="otworzModalSafe('<?php echo $js_tytul; ?>', '<?php echo $js_opis; ?>', '<?php echo $js_fotos; ?>')">
                                Szczegóły produktu
                            </button>
                        </div>
                    </div>

                    <div class="zakup-sekcja">
                        <p class="cena-main"><?php echo number_format($row['cena'], 2, ',', ' '); ?> zł</p>
                        <div style="display:flex; gap:8px;">
                            <div class="qty-pill">
                                <button class="btn-pill" onclick="zmienMainQty(<?php echo $row['id']; ?>, -1, <?php echo $row['dostepne_sztuki']; ?>)"><i class="fa-solid fa-minus"></i></button>
                                <input type="number" id="ile_<?php echo $row['id']; ?>" value="1" min="1" class="input-pill" readonly>
                                <button class="btn-pill" onclick="zmienMainQty(<?php echo $row['id']; ?>, 1, <?php echo $row['dostepne_sztuki']; ?>)"><i class="fa-solid fa-plus"></i></button>
                            </div>
                            <button onclick="dodajZIloscia(<?php echo $row['id']; ?>)" class="btn-kup" style="flex-grow:1;">Dodaj</button>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    </main>

    <footer class="main-footer">
        <div class="footer-top">
            <div class="footer-container">
                <div class="footer-column about">
                    <div class="footer-logo">
                        <img src="logo_DrogoTu.png" alt="Logo" style="height:40px; margin-right:10px;"> Drogotu<span>Sklep</span>
                    </div>
                    <p>Najlepsze produkty w najniższych cenach. Twój ulubiony sklep internetowy, gdzie jakość spotyka się z oszczędnością.</p>
                    <div class="footer-socials">
                        <a href="#"><i class="fab fa-facebook-f"></i></a>
                        <a href="#"><i class="fab fa-instagram"></i></a>
                        <a href="#"><i class="fab fa-x-twitter"></i></a>
                    </div>
                </div>
                <div class="footer-column links">
                    <h3>Menu</h3>
                    <ul>
                        <li><a href="index.php">Strona główna</a></li>
                        <li><a href="regulamin.html">Regulamin</a></li>
                        <li><a href="regulamin.html">Polityka prywatności</a></li>
                    </ul>
                </div>
                <div class="footer-column contact" id="kontakt">
                    <h3>Kontakt</h3>
                    <p><i class="fa-solid fa-phone"></i> +48 123 456 789</p>
                    <p><i class="fa-solid fa-envelope"></i> kontakt@drogotu.pl</p>
                    <p><i class="fa-solid fa-location-dot"></i> ul. Kościuszki 24, Kutno</p>
                </div>
            </div>
        </div>
        <div class="footer-bottom">
            <div class="footer-container">
                <div class="footer-copy">
                    &copy; <span id="currentYear"></span> <b>Drogotu</b>. Wszelkie prawa zastrzeżone.
                </div>
                <div class="footer-admin-link">
                    <a href="admin.php"><i class="fa-solid fa-lock"></i> Panel Administratora</a>
                </div>
            </div>
        </div>
    </footer>

    <div id="modal-opis" class="modal-overlay" onclick="zamknijModal()">
        <div class="modal-content" onclick="event.stopPropagation()">
            <span onclick="zamknijModal()" class="close-modal-custom">&times;</span>
            <div class="modal-grid">
                <div class="modal-foto-box">
                    <img id="modal-img-main" src="...">
                    <div id="modal-thumbnails">...</div>
                </div>
                <div class="modal-text-box">
                    <h2 id="modal-tytul">...</h2>
                    <p id="modal-tresc">...</p>
                </div>
            </div>
        </div>
    </div>

    <script>
        // --- FILTRY I SORTOWANIE ---
        let aktualnaKat = 'all';

        function aplikujWszystkieFiltry() {
            const szukaj = document.getElementById('searchInput').value.toLowerCase();
            const cenaMin = parseFloat(document.getElementById('priceMin').value) || 0;
            const cenaMax = parseFloat(document.getElementById('priceMax').value) || Infinity;

            document.querySelectorAll('.kafelekprodukt').forEach(p => {
                const nazwa = p.querySelector('h3').innerText.toLowerCase();
                const kat = p.getAttribute('data-category').toLowerCase();
                const cena = parseFloat(p.getAttribute('data-price'));

                const matchesSearch = nazwa.includes(szukaj);
                const matchesPrice = cena >= cenaMin && cena <= cenaMax;
                const matchesKat = (aktualnaKat === 'all' || kat.includes(aktualnaKat.toLowerCase()));

                p.style.display = (matchesSearch && matchesPrice && matchesKat) ? "flex" : "none";
            });
        }

        function ustawKategorie(kat, btn) {
            document.querySelectorAll('.btn-kat').forEach(b => b.classList.remove('active'));
            btn.classList.add('active');
            aktualnaKat = kat;
            aplikujWszystkieFiltry();
        }

        function resetujFiltry() {
            document.getElementById('searchInput').value = "";
            document.getElementById('priceMin').value = "";
            document.getElementById('priceMax').value = "";
            ustawKategorie('all', document.querySelector('.btn-kat'));
        }

        function sortujProdukty() {
            const grid = document.querySelector('.produkty-grid');
            const items = Array.from(grid.querySelectorAll('.kafelekprodukt'));
            const typ = document.getElementById('sortSelect').value;
            items.sort((a, b) => {
                if (typ === 'price-asc') return a.dataset.price - b.dataset.price;
                if (typ === 'price-desc') return b.dataset.price - a.dataset.price;
                if (typ === 'name-asc') return a.querySelector('h3').innerText.localeCompare(b.querySelector('h3').innerText);
                return 0;
            });
            items.forEach(i => grid.appendChild(i));
        }

        // --- KOSZYK I MODALE ---
        function toggleKoszyk() {
            const k = document.getElementById('koszyk-okno');
            k.style.display = (k.style.display === 'block') ? 'none' : 'block';
        }

        function pokazFormularz() {
            document.getElementById('koszyk-lista').style.display = 'none';
            document.getElementById('koszyk-formularz').style.display = 'block';
        }

        function pokazListe() {
            document.getElementById('koszyk-lista').style.display = 'block';
            document.getElementById('koszyk-formularz').style.display = 'none';
        }

        function validateForm() {
            document.getElementById('btn-final').disabled = !document.getElementById('reg-check').checked;
        }

        function zmienInput(id, delta, max) {
            const inp = document.getElementById('input_qty_' + id);
            let v = parseInt(inp.value) + delta;
            if (v < 0) v = 0;
            if (v > max) {
                showToast("Max dostępnych: " + max, "error");
                v = max;
            }
            inp.value = v;
        }

        function zmienMainQty(id, delta, max) {
            const inp = document.getElementById('ile_' + id);
            let v = parseInt(inp.value) + delta;
            if (v < 1) v = 1;
            if (v > max) {
                showToast("Tylko " + max + " szt.", "error");
                v = max;
            }
            inp.value = v;
        }

        function zatwierdzIlosc(id) {
            const ile = document.getElementById('input_qty_' + id).value;
            window.location.href = '?akcja=ustaw_ilosc&id=' + id + '&ilosc=' + ile;
        }

        function dodajZIloscia(id) {
            const ile = document.getElementById('ile_' + id).value;
            window.location.href = '?akcja=dodaj&id=' + id + '&ilosc=' + ile;
        }

        function otworzModalSafe(t, d, f) {
            document.getElementById('modal-tytul').innerText = decodeURIComponent(t);
            document.getElementById('modal-tresc').innerText = decodeURIComponent(d);
            const fotos = JSON.parse(decodeURIComponent(f));
            const mainImg = document.getElementById('modal-img-main');
            const thumbs = document.getElementById('modal-thumbnails');
            mainImg.src = 'zdjecia/' + fotos[0];
            thumbs.innerHTML = '';
            fotos.forEach(foto => {
                const img = document.createElement('img');
                img.src = 'zdjecia/' + foto;
                img.classList.add('thumb-img');
                img.onclick = () => mainImg.src = img.src;
                thumbs.appendChild(img);
            });
            document.getElementById('modal-opis').style.display = 'flex';
        }

        function zamknijModal() {
            document.getElementById('modal-opis').style.display = 'none';
        }

        function zamknijConfirm() {
            document.getElementById('confirm-modal').style.display = 'none';
        }

        function askConfirm(text, onConfirm) {
            const modal = document.getElementById('confirm-modal');
            document.getElementById('confirm-text').innerText = text;
            modal.style.display = 'flex';
            document.getElementById('confirm-yes').onclick = function() {
                onConfirm();
                zamknijConfirm();
            };
        }

        function potwierdzUsuniecie(id) {
            askConfirm("Usunąć produkt?", () => window.location.href = '?akcja=usun_calosc&id=' + id);
        }

        function potwierdzWyczyszczenie() {
            askConfirm("Opróżnić koszyk?", () => window.location.href = '?akcja=wyczysc_wszystko');
        }

        function showToast(text, type = 'info') {
            const container = document.getElementById('toast-container');
            const message = document.getElementById('toast-message');
            const icon = document.getElementById('toast-icon');
            const box = container.querySelector('.toast-box');
            message.innerText = text;
            container.style.display = 'block';
            icon.className = "fa-solid ";
            if (type === 'success') {
                box.style.borderLeft = "6px solid #2ecc71";
                icon.classList.add("fa-circle-check");
                icon.style.color = "#2ecc71";
            } else if (type === 'error') {
                box.style.borderLeft = "6px solid #e74c3c";
                icon.classList.add("fa-circle-xmark");
                icon.style.color = "#e74c3c";
            } else {
                box.style.borderLeft = "6px solid #3498db";
                icon.classList.add("fa-circle-info");
                icon.style.color = "#3498db";
            }
            setTimeout(() => container.style.display = 'none', 3000);
        }

        document.getElementById('currentYear').textContent = new Date().getFullYear();

        window.onload = function() {
            const params = new URLSearchParams(window.location.search);
            if (params.has('status')) {
                const s = params.get('status');
                if (s === 'dodano') showToast("Dodano do koszyka!", "success");
                if (s === 'brak_sztuk') showToast("Brak w magazynie!", "error");
                if (s === 'usunieto') showToast("Usunięto.", "info");
                if (s === 'wyczyszczono') showToast("Koszyk pusty.", "info");
                if (s === 'zaktualizowano') showToast("Zaktualizowano.", "success");
            }
        };
    </script>
</body>

</html>