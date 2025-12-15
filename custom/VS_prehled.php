<?php
require_once "includes/inc_all_custom.php";
// Nastavíme aktuální rok a měsíc pro přednastavení v roletkách
$current_year = date("Y");
$current_month = date("m"); // např. 09
$current_month_n = date("n"); // např. 9 (pro logiku v JS)
?>

    <style>
        /* Jednoduché styly pro lepší vzhled */
        body { font-family: Arial, sans-serif; }
        .controls { margin-bottom: 20px; padding: 15px; border: 1px solid #ccc; background-color: #f9f9f9; }
        .controls label { font-weight: bold; margin-right: 10px; }
        select { padding: 5px; border-radius: 4px; }
    </style>

    <div class="card card-dark">
        <div class="card-header py-2">
            <h3 class="card-title mt-2"><i class="fa fa-tags mr-2"></i>Vzdálená Správa Měsíční přehled</h3>
        </div>
    </div>

<div class="controls">
    <label for="year_select">Vyberte rok:</label>
    <select id="year_select" onchange="fetchData()">
        <?php 
        // Generování ročníků (od aktuálního do 3 roky zpět)
        for ($y = $current_year; $y >= $current_year - 3; $y--): ?>
            <option value="<?php echo $y; ?>" <?php echo ($y == $current_year) ? 'selected' : ''; ?>>
                <?php echo $y; ?>
            </option>
        <?php endfor; ?>
    </select>

    <label for="month_select">Vyberte měsíc:</label>
    <select id="month_select" onchange="fetchData()">
        <?php
        $months = [
            1 => 'Leden', 2 => 'Únor', 3 => 'Březen', 4 => 'Duben',
            5 => 'Květen', 6 => 'Červen', 7 => 'Červenec', 8 => 'Srpen',
            9 => 'Září', 10 => 'Říjen', 11 => 'Listopad', 12 => 'Prosinec'
        ];
        foreach ($months as $num => $name): ?>
            <option value="<?php echo str_pad($num, 2, '0', STR_PAD_LEFT); ?>" 
                    <?php echo ($num == $current_month_n) ? 'selected' : ''; ?>>
                <?php echo $name; ?>
            </option>
        <?php endforeach; ?>
    </select>
</div>

<div id="data_output">
    Načítám data...
</div>

<script>
    document.addEventListener('DOMContentLoaded', (event) => {
        // Načte data hned po načtení stránky s aktuálním rokem a měsícem
        fetchData(); 
    });

    function fetchData() {
        const year = document.getElementById('year_select').value;
        const month = document.getElementById('month_select').value;
        const outputDiv = document.getElementById('data_output');
        
        // Zobrazíme, že se data načítají
        outputDiv.innerHTML = "Načítám data pro " + month + "/" + year + "...";

        // Vytvoření nového AJAX požadavku
        const xhr = new XMLHttpRequest();
        
        // Otevření požadavku na skript fetch_data.php
        xhr.open('GET', 'VS_fetch_data_prehled.php?year=' + year + '&month=' + month, true);

        // Nastavení funkce, která se spustí po dokončení požadavku
        xhr.onload = function() {
            if (xhr.status === 200) {
                // Úspěch: vložíme získaný HTML kód do výstupního elementu
                outputDiv.innerHTML = xhr.responseText;
            } else {
                // Chyba
                outputDiv.innerHTML = 'Nastala chyba při načítání dat. Chyba: ' + xhr.status;
            }
        };

        // Odeslání požadavku
        xhr.send();
    }
</script>


<?php require_once "../../includes/footer.php";?>