
<!DOCTYPE html>
<!--
This is a starter template page. Use this page to start your new project from
scratch. This page gets rid of all links and provides the needed markup only.
-->
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta http-equiv="x-ua-compatible" content="ie=edge">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>AdminLTE 3 | Starter</title>
  <link rel="stylesheet" href="/css/app.css">
  <!-- Google Font: Source Sans Pro -->
  <link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700" rel="stylesheet">
</head>
<body class="hold-transition sidebar-mini">
<div class="wrapper" id="app">

  <!-- Navbar -->
  <nav class="main-header navbar navbar-expand bg-white navbar-light border-bottom">
     <ul class="navbar-nav">
      <li class="nav-item">
        <a class="nav-link" data-widget="pushmenu" href="#"><i class="fa fa-bars"></i></a>
      </li>
    
    </ul>

    <!-- SEARCH FORM -->
      <div class="input-group input-group-sm">
        <input class="form-control form-control-navbar" @keyup="searchit" v-model="search" type="search" placeholder="Search" aria-label="Search">
        <div class="input-group-append">
          <button class="btn btn-navbar" @click="searchit">
            <i class="fa fa-search"></i>
          </button>
        </div>
      </div>


  </nav>
  <!-- /.navbar -->

  <!-- Main Sidebar Container -->
  <aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href="index3.html" class="brand-link">
      <img src="../img/logo.png" alt="Laboran Logo" class="brand-image img-circle elevation-3"
           style="opacity: .8">
      <span class="brand-text font-weight-light">Laboran</span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">
      <!-- Sidebar user panel (optional) -->
      <div class="user-panel mt-3 pb-3 mb-3 d-flex">
        <div class="image">
          <img src="../img/profile.png" class="img-circle elevation-2" alt="User Image">
        </div>
        <div class="info">
          <a href="#" class="d-block">{{Auth::User()->name}}</a>
        </div>
      </div>

      <!-- Sidebar Menu -->
      <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
          <!-- Add icons to the links using the .nav-icon class
               with font-awesome or any other icon font library -->
               <li class="nav-item">
            <router-link to="/dashboard" class="nav-link">
              <i class="nav-icon fas fa-tachometer-alt teal"></i>
              <p>
                Dashboard
              </p>
            </router-link>
          </li>
          <li class="nav-item">
            <router-link to="/tahun-ajaran" class="nav-link">
              <i class="nav-icon fas fa-calendar-alt purple"></i>
              <p>
                Tahun Ajaran
              </p>
            </router-link>
          </li>
          <li class="nav-item">
            <router-link to="/ruangan" class="nav-link">
              <i class="nav-icon fas fa-door-closed indigo"></i>
              <p>
                Ruangan
              </p>
            </router-link>
          </li>
          <li class="nav-item">
            <router-link to="/barang-hilang" class="nav-link">
              <i class="nav-icon fas fa-door-closed red"></i>
              <p>
                Barang Hilang
              </p>
            </router-link>
          </li>
          <li class="nav-item">
            <router-link to="/inventory" class="nav-link">
              <i class="nav-icon fas fa-boxes blue"></i>
              <p>
                Inventory
              </p>
            </router-link>
          </li>
          <li class="nav-item">
            <router-link to="/peminjaman-inventory" class="nav-link">
              <i class="nav-icon fas fa-boxes red"></i>
              <p>
                Peminjaman Inv
              </p>
            </router-link>
          </li>
          <li class="nav-item">
            <router-link to="/permintaan-aplikasi" class="nav-link">
              <i class="nav-icon fab fa-intercom green"></i>
              <p>
                Permintaan App
              </p>
            </router-link>
          </li>
          <li class="nav-item">
            <router-link to="/masalah-lab" class="nav-link">
              <i class="nav-icon fas fa-exclamation-triangle green"></i>
              <p>
                Masalah Lab
              </p>
            </router-link>
          </li>
          <li class="nav-item">
            <router-link to="/uang-kas" class="nav-link">
              <i class="nav-icon fas fa-money-bill-alt teal"></i>
              <p>
                Uang Kas
              </p>
            </router-link>
          </li>
           <li class="nav-item">
            <router-link to="/catatan-beli" class="nav-link">
              <i class="nav-icon fas fa-money-bill-alt orange"></i>
              <p>
                Catatan Beli
              </p>
            </router-link>
          </li>
          <li class="nav-item">
            <router-link to="/koperasi" class="nav-link">
              <i class="nav-icon fas fa-money-bill-alt cyan"></i>
              <p>
                Koperasi
              </p>
            </router-link>
          </li>
          @can('isAdmin')
          <li class="nav-item has-treeview">
            <a href="#" class="nav-link">
              <i class="nav-icon fas fa-cogs blue"></i>
              <p>
                Management
                <i class="right fas fa-angle-left"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
              <li class="nav-item">
                <router-link to="/users" class="nav-link">
                  <i class="fas fa-users nav-icon orange"></i>
                  <p>Users</p>
                </router-link>
              </li>
              <li class="nav-item">
                <router-link to="/profile" class="nav-link">
                  <i class="fas fa-user nav-icon cyan"></i>
                  <p>Profile</p>
                </router-link>
              </li>
            </ul>
          </li>          
          <li class="nav-item">
            <router-link to="/developer" class="nav-link">
              <i class="nav-icon fas fa-cog green"></i>
              <p>
                Developer
              </p>
            </router-link>
          </li>
          @endcan
           <li class="nav-item">         
            <a class="nav-link" href="{{ route('logout') }}"
                onclick="event.preventDefault();
                document.getElementById('logout-form').submit();">
              <i class="nav-icon fa fa-power-off red"></i>
              <p>                
                 {{ __('Logout') }}
              </p>
            </a>

             <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
               @csrf
             </form>

          </li>
        </ul>
      </nav>
      <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
  </aside>

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
        <router-view></router-view>
        
        <vue-progress-bar></vue-progress-bar>
    </div>
    <!-- /.content-header -->

    <!-- Main content -->
    <div class="content">
      <div class="container-fluid">
      </div><!-- /.container-fluid -->
    </div>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->



  <!-- Main Footer -->
  <footer class="main-footer">
    <!-- To the right -->
    <div class="float-right d-none d-sm-inline">
      Anything you want
    </div>
    <!-- Default to the left -->
    <strong>Copyright &copy; 2014-2018 <a href="https://adminlte.io">AdminLTE.io</a>.</strong> All rights reserved.
  </footer>
</div>
<!-- ./wrapper -->

<!-- REQUIRED SCRIPTS -->

@auth
<script>
  window.user = @json(auth()->user())
</script>
@endauth

<script src="/js/app.js"></script>

</body>
</html>
