<?php
require_once "connection.php";

// Hitung statistik untuk dashboard
$total_albums = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM albums"))['count'];
$total_artists = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM artists WHERE artist_type='member'"))['count'];
$total_tracks = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM tracks"))['count'];
$total_events = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM events"))['count'];
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>BLACKPINK Admin - Dashboard</title>
  
  <!-- Google Font: Source Sans Pro -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
</head>
<body class="hold-transition sidebar-mini">
<div class="wrapper">

  <!-- Navbar -->
  <nav class="main-header navbar navbar-expand navbar-white navbar-light">
    <ul class="navbar-nav">
      <li class="nav-item">
        <a class="nav-link" data-widget="pushmenu" href="#"><i class="fas fa-bars"></i></a>
      </li>
    </ul>
    <ul class="navbar-nav ml-auto">
      <li class="nav-item">
        <a class="nav-link" href="#"><i class="fas fa-user"></i> Admin</a>
      </li>
    </ul>
  </nav>
  <!-- /.navbar -->

  <!-- Main Sidebar Container -->
  <aside class="main-sidebar sidebar-dark-primary elevation-4">
    <a href="index.php" class="brand-link">
      <span class="brand-text font-weight-light">BLACKPINK Admin</span>
    </a>

    <div class="sidebar">
      <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu">
          <li class="nav-item">
            <a href="index.php" class="nav-link active">
              <i class="nav-icon fas fa-tachometer-alt"></i>
              <p>Dashboard</p>
            </a>
          </li>
          <li class="nav-item">
            <a href="albums/view.php" class="nav-link">
              <i class="nav-icon fas fa-compact-disc"></i>
              <p>Albums</p>
            </a>
          </li>
          <li class="nav-item">
            <a href="artists/view.php" class="nav-link">
              <i class="nav-icon fas fa-users"></i>
              <p>Artists</p>
            </a>
          </li>
          <li class="nav-item">
            <a href="tracks/view.php" class="nav-link">
              <i class="nav-icon fas fa-music"></i>
              <p>Tracks</p>
            </a>
          </li>
          <li class="nav-item">
            <a href="events/view.php" class="nav-link">
              <i class="nav-icon fas fa-calendar-alt"></i>
              <p>Events</p>
            </a>
          </li>
          <li class="nav-item">
            <a href="tours/view.php" class="nav-link">
              <i class="nav-icon fas fa-bus"></i>
              <p>Tours</p>
            </a>
          </li>
          <li class="nav-item">
            <a href="gallery/view.php" class="nav-link">
              <i class="nav-icon fas fa-images"></i>
              <p>Gallery</p>
            </a>
          </li>
          <li class="nav-item">
            <a href="member_endorsements/view.php" class="nav-link">
              <i class="nav-icon fas fa-handshake"></i>
              <p>Endorsements</p>
            </a>
          </li>
        </ul>
      </nav>
    </div>
  </aside>

  <!-- Content Wrapper -->
  <div class="content-wrapper">
    <div class="content-header">
      <div class="container-fluid">
        <h1 class="m-0">Dashboard</h1>
      </div>
    </div>

    <section class="content">
      <div class="container-fluid">
        <!-- Info boxes -->
        <div class="row">
          <div class="col-12 col-sm-6 col-md-3">
            <div class="info-box">
              <span class="info-box-icon bg-info elevation-1"><i class="fas fa-compact-disc"></i></span>
              <div class="info-box-content">
                <span class="info-box-text">Total Albums</span>
                <span class="info-box-number"><?php echo $total_albums; ?></span>
              </div>
            </div>
          </div>
          
          <div class="col-12 col-sm-6 col-md-3">
            <div class="info-box mb-3">
              <span class="info-box-icon bg-success elevation-1"><i class="fas fa-users"></i></span>
              <div class="info-box-content">
                <span class="info-box-text">Members</span>
                <span class="info-box-number"><?php echo $total_artists; ?></span>
              </div>
            </div>
          </div>

          <div class="col-12 col-sm-6 col-md-3">
            <div class="info-box mb-3">
              <span class="info-box-icon bg-warning elevation-1"><i class="fas fa-music"></i></span>
              <div class="info-box-content">
                <span class="info-box-text">Total Tracks</span>
                <span class="info-box-number"><?php echo $total_tracks; ?></span>
              </div>
            </div>
          </div>

          <div class="col-12 col-sm-6 col-md-3">
            <div class="info-box mb-3">
              <span class="info-box-icon bg-danger elevation-1"><i class="fas fa-calendar-alt"></i></span>
              <div class="info-box-content">
                <span class="info-box-text">Upcoming Events</span>
                <span class="info-box-number"><?php echo $total_events; ?></span>
              </div>
            </div>
          </div>
        </div>

        <!-- Main row -->
        <div class="row">
          <div class="col-md-12">
            <div class="card">
              <div class="card-header bg-warning">
                <h3 class="card-title"><i class="fas fa-info-circle mr-2"></i>BLACKPINK Admin Panel</h3>
              </div>
              <div class="card-body">
                <p>Selamat datang di sistem manajemen konten BLACKPINK. Gunakan menu di sidebar untuk mengelola:</p>
                <ul>
                  <li><strong>Albums</strong> - Kelola data album (Group, Solo, Japanese, Collaboration)</li>
                  <li><strong>Artists</strong> - Kelola data member dan guest artists</li>
                  <li><strong>Tracks</strong> - Kelola daftar lagu per album</li>
                  <li><strong>Events</strong> - Kelola jadwal konser dan tur</li>
                  <li><strong>Tours</strong> - Kelola informasi tour</li>
                  <li><strong>Gallery</strong> - Kelola foto-foto dokumentasi</li>
                  <li><strong>Endorsements</strong> - Kelola brand endorsement member</li>
                </ul>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>
  </div>

  <footer class="main-footer">
    <strong>BLACKPINK Admin Panel</strong> 
    &copy; 2026 All rights reserved.
  </footer>

</div>

<!-- jQuery -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<!-- AdminLTE App -->
<script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/js/adminlte.min.js"></script>
</body>
</html>