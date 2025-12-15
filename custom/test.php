<?php require_once "includes/inc_all_custom.php";
?>

    <div class="card card-dark">
        <div class="card-header py-2">
            <h3 class="card-title mt-2"><i class="fa fa-tags mr-2"></i>Operace s DB</h3>
        </div>
    </div>
    

<div class="d-flex align-items-center">
    
<form action="VS_setup_db.php" method="post" style="margin-right: 15px;">
    <button 
        type="submit" 
        name="create_table" 
        class="btn btn-primary"
        
        onclick="return confirm('Opravdu chcete Vyvořit novou tabulku v databázi?');"
    >
        Vytvořit tabulku DB
    </button>
</form>
<form action="VS_delete_db.php" method="post" style="margin-right: 15px;">
    <button 
        type="submit" 
        name="delete_table" 
        class="btn btn-danger"
        
        onclick="return confirm('POZOR! Opravdu chcete SMAZAT tabulku z databáze? Tato akce je nevratná a dojde ke ztrátě VŠECH dat!');"
    >
        Smazat tabulku DB
    </button>
</form>

</div>
<br>
<hr>
<br>
<?php
    // Název tabulky
$table_name = "tags";

// SQL dotaz pro získání tagů
$sql_tags = mysqli_query(
    $mysqli,
    "
    SELECT
        tag_id,
        tag_name
    FROM
        " . $table_name . "
    ORDER BY
        tag_name ASC
    "
);

?>

    <div class="card card-dark">
        <div class="card-header py-2">
            <h3 class="card-title mt-2"><i class="fa fa-tags mr-2"></i>Seznam Tagů</h3>
        </div>
    </div>


<?php

// Kontrola, zda dotaz pro tagy proběhl úspěšně
if ($sql_tags) {
    $total_tags_found = mysqli_num_rows($sql_tags);
    
    if ($total_tags_found > 0) {
        
        // Kontejner pro omezení šířky (např. na 400px) a zarovnání vlevo
        echo '<div style="max-width: 400px;">'; 
        
        echo '<div class="table-responsive">';
        // table-sm pro menší paddingy
        echo '<table class="table table-striped table-hover table-sm">'; 
        echo '<thead>';
        echo '<tr class="bg-primary text-white">';
        echo '<th style="width: 50px;">ID</th>';       // Zobrazíme ID, protože u tagů je užitečné
        echo '<th>Název Tagu</th>'; 
        echo '</tr>';
        echo '</thead>';
        echo '<tbody>';

        // Smyčka pro procházení výsledků
        while ($tag = mysqli_fetch_assoc($sql_tags)) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($tag['tag_id']) . "</td>";
            echo "<td>" . htmlspecialchars($tag['tag_name']) . "</td>";
            echo "</tr>";
        }

        echo '</tbody>';
        echo '</table>';
        echo '</div>'; // Uzavření table-responsive
        
        echo '</div>'; // Uzavření kontejneru pro šířku


    } else {
        echo "<p class='alert alert-warning'>V tabulce '$table_name' nebyly nalezeny žádné tagy.</p>";
    }

} else {
    echo "<p class='alert alert-danger'>Chyba při provádění dotazu: " . mysqli_error($mysqli) . "</p>";
}

mysqli_close($mysqli); 

?>

    <div class="card card-dark">
        <div class="card-header py-2">
            <h3 class="card-title mt-2"><i class="fa fa-tags mr-2"></i>Oprava výkazu</h3>
        </div>
    </div>

<?php require_once "../includes/footer.php";?>