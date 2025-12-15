<?php require_once "includes/inc_all_custom.php";
?>

<?php
$table_name = "Custom_DS_vykazy";

 // SQL dotaz pro kontrolu existence tabulky
$check_table_sql = "SHOW TABLES LIKE '$table_name'";
$result = mysqli_query($mysqli, $check_table_sql);

// Kontrola, zda je výsledek dotazu neprázdný
if (mysqli_num_rows($result) > 0) {
    // Tabulka již existuje
    echo "Tabulka '$table_name' již existuje. Vytváření nebylo nutné.";
} else {
    // Tabulka neexistuje, vytvoříme ji
    $create_table_sql = "CREATE TABLE $table_name (
        id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        client_id INT(11),
        client_name VARCHAR(255),
        technik_id INT(11),
        technik_name VARCHAR(255),
        datum DATE,
        cas TIME,
        popis_prace TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
        rok INT(4),
        mesic INT(4)
    )";

    // Spuštění dotazu pro vytvoření tabulky
    if (mysqli_query($mysqli, $create_table_sql)) {
        echo "Tabulka '$table_name' byla úspěšně vytvořena.";
    } else {
        echo "Chyba při vytváření tabulky: " . mysqli_error($mysqli);
    }
}

// Uzavření spojení
mysqli_close($mysqli);

?>
