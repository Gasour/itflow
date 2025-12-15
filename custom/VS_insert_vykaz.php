<?php require_once "includes/inc_all_custom.php";
?>

<?php
$table_name = "Custom_DS_vykazy";

if (isset($_POST['add_vykaz'])) {


    $client_id = $_POST['client_id'];
    $client_name = $_POST['client_name'];
    $datum = $_POST['datum'];
    $cas = $_POST['cas'];  
    $PopisPrace = $_POST['PopisPrace']; 
    $rok = date("Y", strtotime($datum));
    $mesic = date("m", strtotime($datum));

    $sql_insert = "INSERT INTO " . $table_name . " (client_id, client_name, technik_id, technik_name, datum, cas, popis_prace, rok, mesic) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = mysqli_prepare($mysqli, $sql_insert);

    // Navážeme proměnné na parametry dotazu
    // 'issssssi' znamená: i=integer, s=string. Rok je také integer.
    mysqli_stmt_bind_param($stmt, "issssssii", $client_id, $client_name, $session_user_id, $session_name, $datum, $cas, $PopisPrace, $rok, $mesic);

        // Spustíme připravený dotaz
        if (mysqli_stmt_execute($stmt)) {
             echo "Nový záznam byl úspěšně vložen.";
                } else {
                     echo "Chyba: " . mysqli_error($mysqli);
                        }
    // Uzavřeme prepared statement
    mysqli_stmt_close($stmt);

    // Uzavřeme spojení s databází
    mysqli_close($mysqli);

}

?>
