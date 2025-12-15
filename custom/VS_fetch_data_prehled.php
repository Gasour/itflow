<?php
require_once "../../config.php";
require_once "../../functions.php";
require_once "../../includes/check_login.php";



$selected_year = isset($_GET['year']) ? (int)$_GET['year'] : date("Y");
$selected_month = isset($_GET['month']) ? (int)$_GET['month'] : date("m");

$safe_year = (int)$selected_year;
$safe_month = (int)$selected_month;

$table_name = "Custom_DS_vykazy";

$sql_select = "SELECT * FROM " . $table_name . " 
               WHERE rok = " . $safe_year . " AND mesic = " . $safe_month . 
               " ORDER BY client_name"; 

$result = mysqli_query($mysqli, $sql_select);
$data_by_client = [];
$technician_totals = []; 

// ******************************************************
// POMOCNÉ FUNKCE PRO SČÍTÁNÍ ČASU
// ******************************************************

/**
 * Převod času HH:MM na celkový počet sekund
 */
function time_to_seconds($time) {
    if (strpos($time, ':') === false) {
        $parts = explode(':', $time);
        if (count($parts) === 2) {
             return ((int)$parts[0] * 3600) + ((int)$parts[1] * 60);
        }
        return 0; 
    }
    list($hours, $minutes) = explode(':', $time);
    return ((int)$hours * 3600) + ((int)$minutes * 60);
}

/**
 * Převod celkového počtu sekund zpět na formát HH:MM
 */
function seconds_to_time($seconds) {
    if ($seconds < 0) return '00:00';
    $hours = floor($seconds / 3600);
    $minutes = floor(($seconds % 3600) / 60);
    return sprintf('%02d:%02d', $hours, $minutes);
}


if ($result === false) {
    echo "<p style='color: red;'>Chyba v databázovém dotazu: " . mysqli_error($mysqli) . "</p>";
} elseif (mysqli_num_rows($result) > 0) {
    while($row = mysqli_fetch_assoc($result)) {
        $client_name = $row['client_name'];
        
        // ******************************************************
        // SČÍTÁNÍ ČASU (POUŽÍVÁ 'technik_name' namísto 'technik')
        // ******************************************************
        if (isset($row['cas']) && isset($row['technik_name']) && !empty($row['cas'])) {
            $current_time = substr($row['cas'], 0, 5); // Získá HH:MM
            $current_seconds = time_to_seconds($current_time);
            
            // ZDE JE OPRAVA: Používáme technik_name
            $technician = $row['technik_name']; 
            
            $technician_totals[$client_name][$technician] = 
                ($technician_totals[$client_name][$technician] ?? 0) + $current_seconds;
        }


        // ******************************************************
        // FORMÁTOVÁNÍ A ODEBRÁNÍ SLOUPCŮ
        // ******************************************************
        
        // Formátování data
        if (isset($row['datum']) && !empty($row['datum'])) {
            $timestamp = strtotime($row['datum']);
            $row['datum'] = date("d.m.Y", $timestamp);
        }
        
        // Formátování času
        if (isset($row['cas']) && !empty($row['cas'])) {
            $row['cas'] = substr($row['cas'], 0, 5); // HH:MM
        }
        
        // ODEBRÁNÍ NEPOTŘEBNÝCH SLOUPCŮ
        unset($row['client_name']);
        unset($row['rok']);    
        unset($row['mesic']);
        unset($row['id']);
        unset($row['client_id']);
        unset($row['technik_id']);
        // Tuto proměnnou (technik_name) necháváme, aby se zobrazila v tabulce!
        
        $data_by_client[$client_name][] = $row;
    }
} else {
    echo "<p style='text-align: center; padding: 10px; background-color: #ffe0e0; border: 1px solid #ff9999;'>
          V tabulce '$table_name' nebyly pro měsíc $safe_month a rok $safe_year nalezeny žádné záznamy.
          </p>";
}

if (isset($result)) {
    mysqli_free_result($result);
}

// Generování CSS pro šířku a patičku
echo "<style>
    .data-table th, .data-table td {
        padding: 8px;
        text-align: left;
        /* Odeberte pevnou šířku, necháme ji flexibilní */
    }
    .data-table th.header-popis, .data-table td.cell-popis {
        width: 40%; /* Ponecháme, aby sloupec s popisem byl širší */
    }
    .data-table tfoot td { 
        font-weight: bold; 
        background-color: #e0e0e0;
    }
</style>";

// Projdeme seskupená data a vytvoříme pro každého klienta vlastní tabulku
foreach ($data_by_client as $client_name => $rows) {
    echo "<br>";
    
    // ******************************************************
    // ZMĚNA 1: Odstraněno 'text-align: center' z h2, čímž se zarovná vlevo
    echo "<h2 style='color: #333;'>" . htmlspecialchars($client_name) . "</h2>";
    // ******************************************************
    
    // ZJIŠTĚNÍ INDEXU
    $column_names = array_keys($rows[0]);
    $column_count = count($column_names);
    $target_column_name = 'cas';
    $target_index = array_search($target_column_name, $column_names); 

    // ******************************************************
    // ZMĚNA 2: Obalíme tabulku do <div> s šířkou 100%, aby se roztáhla, 
    // a tím pádem se ztratí dojem centrování.
    echo '<div style="width: 100%;">';
    // ******************************************************
    
    echo "<table border='1' class='data-table' style='border-collapse: collapse; width: 100%;'>";
    
    // Vytvoření záhlaví tabulky
    echo "<tr style='background-color: #f2f2f2;'>";
    foreach ($column_names as $column_name) {
        $header = str_replace('_', ' ', $column_name);
        $is_popis = (stripos($column_name, 'popis') !== false || stripos($column_name, 'poznamky') !== false) ? 'header-popis' : '';
        echo "<th class='{$is_popis}'>" . ucwords($header) . "</th>";
    }
    echo "</tr>";
    
    // Procházení řádků
    foreach ($rows as $row) {
        echo "<tr>";
        foreach ($row as $key => $value) {
             $is_popis = (stripos($key, 'popis') !== false || stripos($key, 'poznamky') !== false) ? 'cell-popis' : '';
            echo "<td class='{$is_popis}'>" . htmlspecialchars($value) . "</td>";
        }
        echo "</tr>";
    }

    // ******************************************************
    // VÝPIS SOUČTU ČASU V ZÁPATÍ TABULKY (FOOTER)
    // ******************************************************
    
    if (isset($technician_totals[$client_name]) && $target_index !== false) {
        echo "<tfoot>";
        foreach ($technician_totals[$client_name] as $technician => $total_seconds) {
            $total_time = seconds_to_time($total_seconds);
            
            echo "<tr>";
            // 1. Vytvoření prázdných buněk (levá část)
            for ($i = 0; $i < $target_index; $i++) {
                echo "<td></td>"; 
            }
            
            // 2. Buňka s celkovým časem
            echo "<td>" . $total_time . "</td>";
            
            // 3. Buňka s popiskem (zbylá šířka)
            $colspan = $column_count - $target_index - 1; 
            
            if ($colspan >= 1) {
                 echo "<td colspan='{$colspan}'>Celkový čas pro technika: " . htmlspecialchars($technician) . "</td>";
            } 
            
            echo "</tr>";
        }
        echo "</tfoot>";
    }
    
    echo "</table>";
    
    // ******************************************************
    // ZMĚNA 2: Uzavření obalujícího <div>
    echo '</div>';
    // ******************************************************
}

mysqli_close($mysqli); 
?>
