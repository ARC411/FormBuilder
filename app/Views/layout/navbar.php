<style>
  @media all and (min-width: 992px) {
    .hover-dropdown .dropdown-menu {
      display: none;
      margin-top: 0;
      transition: all 0.3s ease;
    }
    .hover-dropdown:hover .dropdown-menu {
      display: block;
    }
  }
  .dropdown-item:hover {
    background-color: #f8f9fa;
  }
</style>
<nav class="navbar navbar-expand-lg bg-light bg-gradient sticky-top shadow-sm mb-3">
  <div class="container-fluid">
    <a class="navbar-brand item-center" href="/">
      Checksheet Management <br> System
    </a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarSupportedContent">
      <ul class="navbar-nav me-auto mb-2 mb-lg-0 align-items-center">
        <?php
          $session = \Config\Services::session();
          if($session->get('empid')){
            echo '
            <li class="nav-item">
              <a class="nav-link" href="'.base_url().'input"><i class="fas fa-plus-circle"></i> Input</a>
            </li>';
          }
        ?>
        <li class="nav-item">
          <a class="nav-link" href="<?php echo base_url();?>history"><i class="fas fa-history"></i> History</a>
        </li>
        <?php
          // Cek jika user sudah login (punya empid)
          if ($session->get('empid')) {
              // Ambil raw string positionid dari session (misal: "14,Foreman (Prod.)" atau "18,Operator")
              $position_string = (string)$session->get('positionid');
              $is_admin = $session->get('isadmin');
              
              // Mengecek apakah ada kata "Operator" di dalam string jabatan (case-insensitive)
              // Jika hasilnya !== false, berarti dia adalah operator.
              $is_operator = (stripos($position_string, 'operator') !== false);

              // Tampilkan menu Approve HANYA JIKA BUKAN Operator ATAU dia adalah Admin
              if ( !$is_operator || $is_admin ) {
                  echo '<li class="nav-item">
                    <a class="nav-link" href="'.base_url().'approve"><i class="fas fa-thumbs-up"></i> Approve</a>
                  </li>';
              }
          } else {
              // Jika belum login, tampilkan tombol Login
              echo '<li class="nav-item">
                <a class="nav-link" href="'.base_url().'login"><i class="fas fa-sign-in-alt"></i> Login</a>
              </li>';
          }
          
          if($session->get('isadmin')){
            echo '<li class="nav-item dropdown hover-dropdown">
              <a class="nav-link dropdown-toggle" href="#" id="navbarDropdownProcess" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="fas fa-layer-group"></i> Operations
              </a>
              <ul class="dropdown-menu shadow-sm border-0" aria-labelledby="navbarDropdownProcess">
                <li><a class="dropdown-item py-2" href="'.base_url().'production"><i class="fas fa-industry text-primary me-2"></i> Production</a></li>
                <li><a class="dropdown-item py-2" href="'.base_url().'startup"><i class="fas fa-rocket text-success me-2"></i> Startup</a></li>
                <li><a class="dropdown-item py-2" href="'.base_url().'foregoing"><i class="fas fa-clipboard-check text-warning me-2"></i> Foregoing</a></li>
              </ul>
            </li>';
            echo '<li class="nav-item">
              <a class="nav-link" href="'.base_url().'log-activity"><i class="fas fa-clipboard-list"></i> Log Activity</a>
            </li>';
            echo '<li class="nav-item">
              <a class="nav-link" href="'.base_url().'process"><i class="fas fa-cogs"></i> Process</a>
            </li>';
            echo '<li class="nav-item">
              <a class="nav-link" href="'.base_url().'device"><i class="fas fa-desktop"></i> Device</a>
            </li>';
            echo '<li class="nav-item">
              <a class="nav-link" href="'.base_url('machine').'"><i class="fas fa-server"></i> Machine</a>
            </li>';
            
            // --- MENU BARU: FORM BUILDER ---
            echo '<li class="nav-item">
              <a class="nav-link text-warning" href="'.base_url('form-builder').'"><i class="fas fa-hammer"></i> Form Builder</a>
            </li>';       
        }

          if($session->get('empid')){
            echo '<li class="nav-item">
              <a href="'.base_url().'logout" class="btn btn-danger">
                <i class="fas fa-sign-out-alt"></i> Log Out
              </a>
            </li>';
        }
        ?>
      </ul>
    </div>
    <?php 
      if($session->get('empid')){
        echo '<div class="float-right me-5">
        <p class="m-0"><i class="fas fa-user"></i> '.$session->get('name').'</p>
        </div>';
      }
    ?>
  </div>
</nav>
