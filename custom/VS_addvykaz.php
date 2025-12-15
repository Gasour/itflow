<?php 
require_once "includes/inc_all_custom.php";

// 1. ZPRACOVÁNÍ VYHLEDÁVACÍHO DOTAZU
$q = isset($_GET['q']) ? trim($_GET['q']) : null;
$search_condition = "";

if (!empty($q)) {
    // Připravíme řetězec pro LIKE dotaz, s ochranným escapováním proti SQL Injection
    $safe_q = mysqli_real_escape_string($mysqli, $q);
    $search_condition = " AND clients.client_name LIKE '%" . $safe_q . "%' ";
}

?>

    <div class="card card-dark">
        <div class="card-header py-2">
            <h3 class="card-title mt-2"><i class="fa fa-fw fa-user-friends mr-2"></i>Vzdálená Správa</h3>
        </div>
    </div>

    <div class="card-body p-2 p-md-3">
        <form class="mb-4" autocomplete="off" method="GET"> 
        <div class="row">
            <div class="col-md-4">
                <div class="input-group mb-3 mb-sm-0">
                    <input type="search" class="form-control" name="q" value="<?php if (isset($q)) { echo htmlspecialchars($q); } ?>" placeholder="Hledat klienta" autofocus>
                        <div class="input-group-append">
                            <button type="submit" class="btn btn-primary"><i class="fa fa-search"></i></button>
                            <?php if (!empty($q)): ?>
                            <a href="<?php echo basename($_SERVER['PHP_SELF']); ?>" class="btn btn-secondary" title="Zrušit vyhledávání"><i class="fa fa-times"></i></a>
                            <?php endif; ?>
                        </div>
                </div>
            </div>
        </div>
        </form>
    </div>
    <hr>

<?php

// ÚPRAVA SQL DOTAZU S FILTREM
$sql_klienti = mysqli_query(
    $mysqli,
    "
    SELECT
        clients.client_id,
        clients.client_name,
        client_tags.tag_id
    FROM
        clients
    JOIN
        client_tags ON clients.client_id = client_tags.client_id
    WHERE
        client_tags.tag_id = 1
        " . $search_condition . " 
    ORDER BY
        clients.client_name ASC
    "
);

// Kontrola, zda dotaz pro klienty proběhl úspěšně
if ($sql_klienti) {
    $total_clients_found = mysqli_num_rows($sql_klienti);
    
    echo "<h1>Seznam Firem:</h1>";
    
    if ($total_clients_found > 0) {
        
        // ******************************************************
        // ZMĚNA ZDE: Nový kontejner pro omezení šířky tabulky
        // max-width: 600px omezí šířku, bez mx-auto zůstane vlevo
        echo '<div style="max-width: 600px;">'; 
        // ******************************************************
        
        echo '<div class="table-responsive">';
        // table-sm pro menší paddingy
        echo '<table class="table table-striped table-hover table-sm">'; 
        echo '<thead>';
        echo '<tr class="bg-primary text-white">';
        echo '<th>Název Klienta</th>'; 
        echo '<th style="width: 100px;">Akce</th>'; 
        echo '</tr>';
        echo '</thead>';
        echo '<tbody>';

        // Smyčka pro procházení výsledků
        while ($client = mysqli_fetch_assoc($sql_klienti)) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($client['client_name']) . "</td>";
            
            // Tlačítko "Přidat" (Stále potřebuje ID klienta pro modal)
            echo '<td><button class="btn btn-sm btn-success open-modal-btn" data-toggle="modal" data-target="#addvykaz" data-client-id="'. htmlspecialchars($client['client_id']) .'" data-client-name="'. htmlspecialchars($client['client_name']) .'">Přidat</button></td>';
            
            echo "</tr>";
        }

        echo '</tbody>';
        echo '</table>';
        echo '</div>'; // Uzavření table-responsive
        
        // ******************************************************
        // UZAVŘENÍ NOVÉHO KONTEJNERU PRO ŠÍŘKU
        echo '</div>'; 
        // ******************************************************


    } else {
        echo "<p class='alert alert-warning'>Nebyly nalezeny žádné firmy, které by odpovídaly filtru " . htmlspecialchars($q) . ".</p>";
    }

} else {
    echo "<p class='alert alert-danger'>Chyba při provádění dotazu: " . mysqli_error($mysqli) . "</p>";
}
// Uzavření spojení s DB
mysqli_close($mysqli); 


?>

<div class="modal fade" id="addvykaz" tabindex="-1" role="dialog" aria-labelledby="addvykaz">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Vzdálená správa</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"></button>
      </div>
      <form action="VS_insert_vykaz.php" method="post" autocomplete="off">
        <div class="modal-body">         
            <input type="hidden" id="client_id" name="client_id">
            <input type="hidden" id="client_name" name="client_name">
              <div class="row">
                    <div class="col">
                        <label for="datum">Datum</label>
                        <input type="date" class="form-control" id="datum" name="datum">
                    </div>
                    <div class="col">
                        <label for="cas">Časová náročost</label>
                        <input type="time" class="form-control" id="cas" name="cas">
                    </div>
              </div><br>
            <div class="form-group">
                <label for="PopisPrace">Popis Práce</label>
                <textarea class="form-control" id="PopisPrace" rows="3" name="PopisPrace"></textarea>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Zavřít</button>
            <button type="submit" name="add_vykaz" class="btn btn-primary text-bold"><i class="fa fa-check mr-2"></i>Create</button>  
         </div>
      </form>
    </div>
  </div>
</div>


<script>
    // Nastavení dnešního data
    const today = new Date();
    const yyyy = today.getFullYear();
    const mm = String(today.getMonth() + 1).padStart(2, '0'); 
    const dd = String(today.getDate()).padStart(2, '0');
    const formattedDate = `${yyyy}-${mm}-${dd}`;
    document.getElementById('datum').value = formattedDate;
</script>

<script>
// Logika pro modal (přenáší ID klienta a jméno)
$(document).ready(function() {
    $('#addvykaz').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget);
        var clientID = button.data('client-id');
        var clientName = button.data('client-name');
        var modal = $(this);
        modal.find('#client_id').val(clientID);
        modal.find('#client_name').val(clientName);
    });
});
</script>

<?php require_once "../../includes/footer.php";?>