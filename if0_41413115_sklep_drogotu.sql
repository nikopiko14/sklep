-- phpMyAdmin SQL Dump
-- version 4.9.0.1
-- https://www.phpmyadmin.net/
--
-- Host: sql203.infinityfree.com
-- Czas generowania: 17 Mar 2026, 09:08
-- Wersja serwera: 11.4.10-MariaDB
-- Wersja PHP: 7.2.22

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Baza danych: `if0_41413115_sklep_drogotu`
--

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `kategorie`
--

CREATE TABLE `kategorie` (
  `id` int(11) NOT NULL,
  `nazwa` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Zrzut danych tabeli `kategorie`
--

INSERT INTO `kategorie` (`id`, `nazwa`) VALUES
(1, 'Elektronika'),
(2, 'Dom i Ogród'),
(3, 'Moda'),
(4, 'Odkurzacze'),
(5, 'Nabiał'),
(6, 'Artykuły spożywcze'),
(7, 'Napoje'),
(8, 'Jedzenie'),
(9, 'Laptopy'),
(10, 'Akcesoria'),
(11, 'Zdrowie'),
(12, 'Sport'),
(13, 'Inne');

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `klienci`
--

CREATE TABLE `klienci` (
  `id` int(11) NOT NULL,
  `imie` varchar(35) NOT NULL,
  `nazwisko` varchar(35) NOT NULL,
  `nr_telefonu` varchar(15) NOT NULL,
  `email` char(70) NOT NULL,
  `adres_miasto` varchar(40) NOT NULL,
  `adres_ulica` varchar(50) NOT NULL,
  `adres_nr` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `produkty`
--

CREATE TABLE `produkty` (
  `id` int(11) NOT NULL,
  `nazwa` varchar(255) NOT NULL,
  `cena` decimal(10,2) NOT NULL,
  `opis` varchar(1000) NOT NULL,
  `dostepne_sztuki` int(11) NOT NULL,
  `zdjecie` varchar(300) DEFAULT 'default.jpg'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Zrzut danych tabeli `produkty`
--

INSERT INTO `produkty` (`id`, `nazwa`, `cena`, `opis`, `dostepne_sztuki`, `zdjecie`) VALUES
(1, 'Wiertarko-wkrętarka BOSCH EasyImpact 18V-40 06039D810C z akumulatorem', '389.90', 'Najczęściej wybierana wiertarko-wkrętarka na rynku, cechująca się duża wytrzymałością i długim czasem działania na baterii!', 21, 'wiertarkowkretarkaBOSCH.jpg'),
(2, 'Lodówka Samsung No Frost 185,3cm', '2099.00', 'Lodówka Samsung RB33B612EBN Pełny No Frost 185,3cm Szuflada z kontrolą wilgotności', 11, 'samsunglodowka.jpg'),
(3, 'Masło Mlekovita', '5.99', 'Najsmaczniejsze masło firmy Mlekovita, idealne na kanapki!', 778, 'maslomlekovita.jpg'),
(4, 'Chleb Wiejski', '8.00', 'Chleb Wiejski - taki jak Twojej babci!', 23, 'chlebwiejski.jpg'),
(5, 'Apple MacBook Pro Laptop z czipem M4 Pro z 14-rdzeniowym CPU i 20-rdzeniowym GPU: wyświetlacz Liquid', '11200.70', 'Laptop Apple MacBook Pro, 16,2\", 20-rdzeniowym GPU i 14-rdzeniowym CPU, 24GB RAM, 512GB pamięci SSD w kolorze Gwiezdna Czerń to świetny wybór dla osób wymagających świetnej jakości pracy w cichych warunkach oraz długiej pracy na baterii.', 25, 'macbook.jpg, macbook2.jpg'),
(6, 'Odkurzacz BOSCH BGL6POW1', '730.00', 'Odkurzacz BOSCH BGL6POW1 zwraca uwagę przede wszystkim piękną, matową obudową, która została wyróżniona prestiżową nagrodą Red Dot Design Award. Wyposażony w turboszczotkę oraz szczotkę do twardych powierzchni poradzi sobie z każdym postawionym przed nim zadaniem. Sterowanie na rączce urządzenia gwarantuje niezwykle wygodną obsługę bez zbędnego schylania się do włącznika. Zintegrowane akcesoria są zawsze pod ręką i pozwolą odkurzać każdy zakamarek, a 12-metrowy zapewnia rzadsze przełączanie między gniazdkami.', 44, 'odkurzaczBOSCH.jpg'),
(8, '\nBosch Professional: odkurzacz do pracy na mokro i na sucho GAS 12-25 PL', '667.67', 'Odkurzacz przemysłowy GAS 12-25 PL to profesjonalne urządzenie przeznaczone do odsysania pyłu z elektronarzędzi, a także zbierania pyłu i mokrych odpadów z różnych powierzchni. Model GAS 12-25 PL cechuje się kompaktową mobilną konstrukcją, dzięki czemu znajdzie zastosowanie w rozmaitych pracach. Dzięki opatentowanej technologii automatycznego oczyszczania filtra odkurzacz utrzymuje stałą moc ssącą.', 67, 'odkurzaczBOSCHprzemyslowy.jpg'),
(11, 'Coca-Cola 500ml', '6.09', 'Najpopularniejszy na świecie napój.', 54, 'cocacola.jpg, cocacola2.jpg'),
(12, 'Woda Muszynianka 1.5L (x6)', '21.50', 'Naturalna woda mineralna Muszynianka jest krystalicznie czysta. Zawiera duże ilości magnezu oraz wapnia a także żelaza, fosforu, potasu oraz manganu, które są składnikami niezbędnymi do prawidłowego funkcjonowania wielu narządów. W związku z tym regularne picie wody Muszynianka reguluje niedobory minerałów, przyspiesza metabolizm i pomaga w wydalaniu szkodliwych substancji z organizmu oraz obniża zawartość cukru we krwi. Jej spożywanie poleca się profilaktycznie a także w w wielu kuracjach, między innymi wzmacniających i regenerujących. Woda mineralna Muszynianka jest delikatna, częściowo odgazowana. Czerpana ze źródła Popradzkiego Parku Krajobrazowego mieszczącego się w Muszynie-Zdroju.', 34, 'muszynianka.jpg, muszynianka2.jpg'),
(13, 'Zestaw Długopisów BIC (x8)', '10.00', 'Długopis jednorazowy round stic to najtańsza propozycja z oferty artykułów piśmienniczych firmy bic. Mimo niskiej ceny pisanie jest przyjemne i bezproblemowo kreślimy czystą i równomierną linię', 88, 'dlugopisybic.jpg'),
(28, 'Odżywka Białkowa Koncentrat Activlab 100% Whey Premium 500g', '52.00', 'Activlab 100% Whey Premiumto idealne białko dla każdego – niezależnie od poziomu zaawansowania. Szukasz sposobu na lepszą regenerację, zwiększenie siły lub uzupełnienie diety? Ten produkt sprawdzi się doskonale zarówno u sportowców, jak i osób dbających o zdrowy tryb życia.\r\n\r\nIdealny do każdej diety\r\nActivlab 100% Whey Premium to szybki i wygodny sposób na dostarczenie wysokiej jakości białka. Doskonale komponuje się z owsianką, koktajlem czy naleśnikami. Niskocukrowa formuła bez zbędnych dodatków sprawia, że pasuje do diety redukcyjnej, masowej i zbilansowanej.\r\n\r\nSmak, który motywuje\r\nDostępny w wielu pysznych smakach, Activlab 100% Whey Premium zamienia codzienną suplementację w przyjemność. Wybierz jakość, której zaufały tysiące – postaw na sprawdzony produkt, który działa. Idealny wybór dla Ciebie!', 27, '1773749436_0.jpg'),
(29, 'Bieżnia Treningowa 7.1 Pro Just7Gym Professional', '17999.00', 'JUST7GYM PROFESSIONAL – Został opracowany z myślą o najbardziej wymagających klientach, aby wspierać ich w dążeniu do stawania się lepszymi. Wykonany z komponentów o najwyższej jakości, idealnie nadaje się do użytku komercyjnego 24h/7.\r\n\r\nProdukt serii PROFESSIONAL wykonany jest z normą PN-EN ISO 20957-1:2014-02; PN-EN ISO 20957-2:2021-11 i przeznaczony jest do użytku w placówkach komercyjnych i obiektach użyteczności publicznej takich jak kluby fitness, ośrodki sportowe, studia fitness czy duże sieci siłowni.', 5, '1773749496_0.jpg'),
(30, 'Laptop MSI Cyborg 15 B2RWFKG-038XPL Core 7 240H / 16 GB / 512 GB / RTX 5060 / 144 Hz', '4299.00', 'Dedykowany układ graficzny: NVIDIA GeForce RTX 5060 Laptop GPU\r\nPamięć RAM (zainstalowana): 16 GB\r\nProcesor: Intel Core 7 240H\r\nPrzekątna ekranu: 15.6\"\r\nSystem operacyjny: Brak\r\n\r\nWkrocz w nową przyszłość z procesorem Intel® Core™ serii 200, z odnowioną hybrydową architekturą rdzeni dla lepszej pracy wielozadaniowej i płynnego uruchamiania wymagających gier. Szybsze prędkości odczytu/zapisu pamięci DDR5 prowadzą do przyspieszonej wydajności we wszystkich aspektach. Dzięki przepustowości do 7,88 GB/s dysk SSD PCIe Gen.4 sprawia, że ładowanie wymagających zadań, od dużych gier po skomplikowane projekty, staje się dziecinnie proste.', 12, '1773749636_0.jpg, 1773749636_1.jpg, 1773749636_2.jpg, 1773749636_3.jpg, 1773749636_4.jpg'),
(31, 'Dieta pudełkowa wysokobiałkowa', '66.20', 'Jesteś na redukcji, ale obawiasz się utraty ciężko wypracowanych mięśni? Ta dieta jest dla Ciebie.\r\n\r\nZapewnia wysoką podaż białka, które pomaga chronić masę mięśniową podczas odchudzania. Jednocześnie jest bogata w błonnik, co gwarantuje długotrwałe uczucie sytości i ułatwia kontrolę nad apetytem.\r\n\r\nPosiłki bazują na pełnowartościowych źródłach białka (chude mięso, ryby, nabiał) oraz węglowodanach złożonych, przy jednoczesnym ograniczeniu cukrów prostych.\r\n\r\nbiałko: 20% | tłuszcz: 40% | węglowodany: 40%', 22, '1773749884_0.jpg'),
(32, 'Oshee Napój Izotoniczny Niegazowany O Smaku Wieloowocowym 0,75 L', '3.77', 'Roztwory węglowodanowo-elektrolitowe zwiększają wchłanianie wody podczas ćwiczeń fizycznych oraz pomagają utrzymać wytrzymałość podczas długotrwałych ćwiczeń wytrzymałościowych.\r\n\r\nOshee zachęca do prowadzenia zdrowego trybu życia i odżywiania się w sposób zrównoważony.\r\n\r\nSposób przechowywania: Przechowywać w temperaturze otoczenia\r\nSposób użycia: Tworzenie się osadów jest zjawiskiem naturalnym. Przed otwarciem delikatnie wstrząsnąć.\r\n\r\nTermin ważności: Przechowywać w suchym i chłodnym miejscu. Chronić przed działaniem promieni słonecznych. Spożyć bezpośrednio po otwarciu.\r\n\r\nSkład produktu: woda. glukoza. kwas: kwas cytrynowy. cytrynian sodu. substancje konserwujące: sorbinian potasu, benzoesan sodu. regulator kwasowości: cytryniany potasu. substancje słodzące: aspartam, acesulfam K. stabilizatory: guma arabska, estry glicerolu i żywicy roślinnej. aromat. witaminy: niacyna, witamina B6, biotyna. barwnik: błękit brylantowy FCF', 34, '1773750036_0.jpg'),
(33, 'Nawóz do borówek z mikroelementami – 1 kg | Target', '17.02', 'Nawóz granulowany do borówek z mikroskładnikami, jest nawozem WE typu NPK, zawierającym podstawowe składniki pokarmowe: azot, fosfor, potas, magnez i siarkę oraz mikroskładniki takie jak: bor, miedź, żelazo, mangan i cynk. Odpowiednia ilość azotu gwarantuje bujne plony. Nawóz stabilizuje kwaśny odczyn gleby warunkujący prawidłowy wzrost roślin.', 24, '1773750285_0.jpg'),
(34, 'Gucci GG NARROW RIMLESS METAL SUNGLASSES UNISEX - Okulary przeciwsłoneczne', '2209.00', 'Kształt okularów: Prostokątne\r\nWzór: Nadruk\r\nKategoria filtra: Mocne przyciemnienie (kategoria 3)\r\nFiltr UV: Tak\r\nEtui do okularów: Twarde etui, woreczek ze sznureczkiem', 4, '1773750405_0.jpg'),
(35, 'Dres Bluza z kapturem z nadrukiem 3D wilka oraz spodnie, z motywami zwierzęcymi', '86.35', 'Materiał: Poliester\r\nWzorzysty: Druk\r\nCzy jest przezroczysty?: Nie\r\nTkanina: Lekkie rozciągnięcie\r\nStyl kołnierzyka: Zakapturzony', 46, '1773750836_0.jpg'),
(36, 'Boxerki Męskie BIIHUDU z Nadrukiem John Pork', '28.54', 'Wzorzysty: Druk\r\nCzy jest przezroczysty?: Nie\r\nTkanina: Średnia rozciągliwość', 1, '1773751077_0.jpg');

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `produkty_kategorie`
--

CREATE TABLE `produkty_kategorie` (
  `id_produktu` int(11) NOT NULL,
  `id_kategorii` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Zrzut danych tabeli `produkty_kategorie`
--

INSERT INTO `produkty_kategorie` (`id_produktu`, `id_kategorii`) VALUES
(1, 1),
(2, 1),
(5, 1),
(6, 1),
(8, 1),
(30, 1),
(1, 2),
(2, 2),
(6, 2),
(8, 2),
(29, 2),
(33, 2),
(34, 3),
(35, 3),
(36, 3),
(6, 4),
(8, 4),
(3, 5),
(3, 6),
(4, 6),
(11, 6),
(12, 6),
(28, 6),
(31, 6),
(32, 6),
(11, 7),
(12, 7),
(32, 7),
(4, 8),
(31, 8),
(5, 9),
(30, 9),
(13, 10),
(31, 11),
(29, 12),
(13, 13);

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `zamowienia`
--

CREATE TABLE `zamowienia` (
  `id` int(11) NOT NULL,
  `id_klient` int(11) NOT NULL,
  `produkty` text NOT NULL,
  `data` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `produkty_id` text NOT NULL,
  `kwota_zamowienia` decimal(10,2) NOT NULL,
  `status` enum('oczekuje','oplacone','anulowane','dostarczone') DEFAULT 'oczekuje'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indeksy dla zrzutów tabel
--

--
-- Indeksy dla tabeli `kategorie`
--
ALTER TABLE `kategorie`
  ADD PRIMARY KEY (`id`);

--
-- Indeksy dla tabeli `klienci`
--
ALTER TABLE `klienci`
  ADD PRIMARY KEY (`id`);

--
-- Indeksy dla tabeli `produkty`
--
ALTER TABLE `produkty`
  ADD PRIMARY KEY (`id`);

--
-- Indeksy dla tabeli `produkty_kategorie`
--
ALTER TABLE `produkty_kategorie`
  ADD PRIMARY KEY (`id_produktu`,`id_kategorii`),
  ADD KEY `id_kategorii` (`id_kategorii`);

--
-- Indeksy dla tabeli `zamowienia`
--
ALTER TABLE `zamowienia`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_klient_zamowienie` (`id_klient`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT dla tabeli `kategorie`
--
ALTER TABLE `kategorie`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT dla tabeli `klienci`
--
ALTER TABLE `klienci`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT dla tabeli `produkty`
--
ALTER TABLE `produkty`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;

--
-- AUTO_INCREMENT dla tabeli `zamowienia`
--
ALTER TABLE `zamowienia`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- Ograniczenia dla zrzutów tabel
--

--
-- Ograniczenia dla tabeli `produkty_kategorie`
--
ALTER TABLE `produkty_kategorie`
  ADD CONSTRAINT `produkty_kategorie_ibfk_1` FOREIGN KEY (`id_produktu`) REFERENCES `produkty` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `produkty_kategorie_ibfk_2` FOREIGN KEY (`id_kategorii`) REFERENCES `kategorie` (`id`) ON DELETE CASCADE;

--
-- Ograniczenia dla tabeli `zamowienia`
--
ALTER TABLE `zamowienia`
  ADD CONSTRAINT `fk_klient_zamowienie` FOREIGN KEY (`id_klient`) REFERENCES `klienci` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
