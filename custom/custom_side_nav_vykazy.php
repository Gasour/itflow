<!-- Main Sidebar Container -->
<aside class="main-sidebar sidebar-dark-primary d-print-none">

    <a class="pb-1 mt-1 brand-link" href="../<?php echo $config_start_page ?>">
        <p class="h5"><i class="nav-icon fas fa-arrow-left ml-3 mr-2"></i>
            <span class="brand-text ">Back | <strong>KOPOSIT</strong>
        </p>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">

        <!-- Sidebar Menu -->
        <nav>

            <ul class="nav nav-pills nav-sidebar flex-column mt-2" data-widget="treeview" data-accordion="false">

                <li class="nav-header">VZDÁLENÁ SPRÁVA</li>
         
                <li class="nav-item">
                    <a href="VS_addvykaz.php" class="nav-link <?php if (basename($_SERVER["PHP_SELF"]) == "VS_addvykaz.php") { echo "active"; } ?>">
                        <i class="far fa-circle nav-icon"></i>
                        <p>Vložit</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="VS_prehled.php" class="nav-link <?php if (basename($_SERVER["PHP_SELF"]) == "VS_prehled.php") { echo "active"; } ?>">
                        <i class="far fa-circle nav-icon"></i>
                        <p>Přehled</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="VS_admin.php" class="nav-link <?php if (basename($_SERVER["PHP_SELF"]) == "VS_admin.php") { echo "active"; } ?>">
                        <i class="far fa-circle nav-icon"></i>
                        <p>Admin</p>
                    </a>
                </li>                

        
            </ul>

        </nav>
    </div>

</aside>







