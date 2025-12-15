<?php
require_once "includes/inc_all.php"; 

// ******************************************************
// ZMĚNA 1: Technik ID je napevno 4
// ******************************************************
$selected_technik_id = 4; // Toto je pevná hodnota

// Datum se stále načítá z formuláře
$selected_date = isset($_GET['datum']) ? trim($_GET['datum']) : null;

$where_conditions = ["1=1"];

// Vždy aplikujeme filtr na ID technika 4
$where_conditions[] = "technik_id = " . $selected_technik_id;

if (!empty($selected_date)) {
    // Předpoklad, že je datum v DB ve formátu RRRR-MM-DD
    $safe_date = mysqli_real_escape_string($mysqli, $selected_date);
    $where_conditions[] = "datum = '" . $safe_date . "'";
}

$table_name = "Custom_DS_vykazy";
$sql_select = null;

// Dotaz spustíme, pokud je nastaveno jakékoli datum (nebo vždy, pokud nechceme filtrovat jen datum)
// Jelikož ID je pevné, dotaz spustíme, pokud existuje ID (což je vždy 4)
if ($selected_technik_id) {
    $sql_select = "SELECT id, client_name, datum, cas, popis_prace, technik_id FROM " . $table_name . " 
                   WHERE " . implode(' AND ', $where_conditions) . 
                   " ORDER BY datum DESC, client_name ASC"; 

    $result = mysqli_query($mysqli, $sql_select);
}
?>

<div class="card card-dark">
    <div class="card-header py-2">
        <h3 class="card-title mt-2"><i class="fa fa-search mr-2"></i>Vyhledávání a úprava Výkazů pro technika ID <?php echo $selected_technik_id; ?></h3>
    </div>
</div>

<div class="card-body p-2 p-md-3">
    <form class="mb-4" autocomplete="off" method="GET"> 
        <div class="row">
            
            <div class="col-md-3 mb-2">
                <label for="datum">Filtrovat podle data výkazu</label>
                <input type="date" class="form-control" id="datum" name="datum" value="<?php echo htmlspecialchars($selected_date ?? ''); ?>">
            </div>
            
            <div class="col-md-9 mb-2 pt-4">
                <button type="submit" class="btn btn-primary"><i class="fa fa-search mr-1"></i>Najít výkazy</button>
                <?php if (!empty($selected_date)): // Filtr na datum je aplikován ?>
                <a href="<?php echo basename($_SERVER['PHP_SELF']); ?>" class="btn btn-secondary" title="Zrušit vyhledávání"><i class="fa fa-times mr-1"></i>Zrušit filtr data</a>
                <?php endif; ?>
            </div>
        </div>
    </form>
</div>
<hr>

<?php if (isset($result)): ?>
    
    <?php if ($result === false): ?>
        <p class="alert alert-danger">Chyba v databázovém dotazu: <?php echo mysqli_error($mysqli); ?></p>
    <?php elseif (mysqli_num_rows($result) > 0): ?>
        
        <h2>Nalezené výkazy (<?php echo mysqli_num_rows($result); ?>):</h2>

        <div class="table-responsive">
            <table class="table table-striped table-hover table-sm">
                <thead>
                    <tr class="bg-primary text-white">
                        <th>Klient</th>
                        <th>Datum</th>
                        <th>Čas</th>
                        <th>Popis Práce</th>
                        <th style="width: 100px;">Akce</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = mysqli_fetch_assoc($result)): ?>
                        <?php
                            $display_date = date("d.m.Y", strtotime($row['datum']));
                            $display_time = substr($row['cas'] ?? '', 0, 5);
                        ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['client_name'] ?? ''); ?></td>
                            <td><?php echo htmlspecialchars($display_date); ?></td>
                            <td><?php echo htmlspecialchars($display_time); ?></td>
                            <td><?php echo htmlspecialchars(substr($row['popis_prace'] ?? '', 0, 100)); ?>...</td>
                            <td>
                                <button 
                                    class="btn btn-warning btn-sm" 
                                    data-toggle="modal" 
                                    data-target="#editVykazModal"
                                    data-id="<?php echo htmlspecialchars($row['id']); ?>"
                                    data-client="<?php echo htmlspecialchars($row['client_name'] ?? ''); ?>"
                                    data-datum="<?php echo htmlspecialchars($row['datum']); ?>" 
                                    data-cas="<?php echo htmlspecialchars($display_time); ?>"
                                    data-popis="<?php echo htmlspecialchars($row['popis_prace'] ?? ''); ?>"
                                >
                                    Opravit
                                </button>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
        
    <?php else: ?>
        <p class="alert alert-info">Nebyly nalezeny žádné záznamy odpovídající zadaným kritériím (Technik ID 4).</p>
    <?php endif; ?>

<?php 
    if (isset($result)) {
        mysqli_free_result($result);
    }
endif; 
mysqli_close($mysqli);
?>

<div class="modal fade" id="editVykazModal" tabindex="-1" role="dialog" aria-labelledby="editVykazModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header bg-warning text-dark">
        <h5 class="modal-title" id="editVykazModalLabel">Opravit výkaz pro klienta: <span id="modal-client-name"></span></h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      
      <form action="update_vykaz.php" method="post" autocomplete="off">
        <div class="modal-body">         
            <input type="hidden" id="edit-id" name="id"> 
            
            <div class="form-group">
                <label for="edit-datum">Datum</label>
                <input type="date" class="form-control" id="edit-datum" name="datum">
            </div>

            <div class="form-group">
                <label for="edit-cas">Časová náročost (HH:MM)</label>
                <input type="time" class="form-control" id="edit-cas" name="cas">
            </div>
            
            <div class="form-group">
                <label for="edit-popis">Popis Práce</label>
                <textarea class="form-control" id="edit-popis" rows="4" name="popis_prace"></textarea>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Zavřít</button>
            <button type="submit" name="update_vykaz" class="btn btn-warning text-bold"><i class="fa fa-save mr-2"></i>Uložit změny</button>  
         </div>
      </form>
    </div>
  </div>
</div>

<script>
$(document).ready(function() {
    
    // Používáme oficiální metodu Bootstrapu pro plnění dat, která minimalizuje konflikty.
    $('#editVykazModal').on('show.bs.modal', function (event) {
        
        var button = $(event.relatedTarget); 
        
        // Získání dat z atributů data-
        var id = button.data('id');
        var clientName = button.data('client');
        var datum = button.data('datum');
        var cas = button.data('cas');
        var popis = button.data('popis');

        var modal = $(this);

        // Vložení dat do formuláře v modalu
        modal.find('#modal-client-name').text(clientName);
        modal.find('#edit-id').val(id);
        modal.find('#edit-datum').val(datum);
        modal.find('#edit-cas').val(cas);
        modal.find('#edit-popis').val(popis);
    });
});
</script>