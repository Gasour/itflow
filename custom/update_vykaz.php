<?php
require_once "includes/inc_all_custom.php";

if (isset($_POST['update_vykaz'])) {
    
    // Získání dat z POST
    $id = $_POST['id'] ?? null;
    $datum = $_POST['datum'] ?? null;
    $cas = $_POST['cas'] ?? null;
    $popis_prace = $_POST['popis_prace'] ?? null;

    if ($id && $datum && $cas && $popis_prace) {
        
        // Ochrana proti SQL Injection
        $safe_id = (int)$id;
        $safe_datum = mysqli_real_escape_string($mysqli, $datum);
        $safe_cas = mysqli_real_escape_string($mysqli, $cas);
        $safe_popis_prace = mysqli_real_escape_string($mysqli, $popis_prace);

        $table_name = "Custom_DS_vykazy";
        
        // SQL dotaz pro aktualizaci
        $sql_update = "UPDATE " . $table_name . " SET
                       datum = '" . $safe_datum . "',
                       cas = '" . $safe_cas . "',
                       popis_prace = '" . $safe_popis_prace . "'
                       WHERE id = " . $safe_id;
                       
        if (mysqli_query($mysqli, $sql_update)) {
            // Přesměrování zpět, ideálně na stránku s vyhledáváním (např. search_vykazy_by_id.php)
            // Zde by bylo dobré zachovat původní parametry vyhledávání!
            
            // Pro zjednodušení přesměrujeme jen na domovskou stránku, kde se hledání provádí
            header("Location: search_vykazy_by_id.php?success=1");
            exit;
        } else {
            // Chyba aktualizace
            $error = "Chyba při aktualizaci záznamu: " . mysqli_error($mysqli);
        }
        
    } else {
        $error = "Chybí povinné údaje pro aktualizaci.";
    }

} else {
    $error = "Neplatný požadavek.";
}

// Zobrazení chybového hlášení, pokud došlo k chybě
if (isset($error)) {
    echo "<p style='color: red;'>CHYBA: " . htmlspecialchars($error) . "</p>";
}

mysqli_close($mysqli);
?>