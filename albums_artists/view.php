<?php
require_once __DIR__ . "/../connection.php";

$sql = "SELECT albums_artists.album_id, albums_artists.artist_id, 
               albums.title AS album_title, artists.name AS artist_name 
        FROM albums_artists 
        JOIN albums ON albums_artists.album_id = albums.album_id 
        JOIN artists ON albums_artists.artist_id = artists.artist_id 
        ORDER BY albums.title, artists.name";
$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Albums Artists - BLACKPINK Admin</title>
  
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
  <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap4.min.css">
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
      <li class="nav-item">
        <a href="../index.php" class="nav-link"><i class="fas fa-home"></i> Home</a>
      </li>
    </ul>
  </nav>

  <!-- Sidebar -->
  <aside class="main-sidebar sidebar-dark-primary elevation-4">
    <a href="../index.php" class="brand-link">
      <span class="brand-text font-weight-light">BLACKPINK Admin</span>
    </a>
    <div class="sidebar">
      <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column">
          <li class="nav-item"><a href="../index.php" class="nav-link"><i class="nav-icon fas fa-tachometer-alt"></i><p>Dashboard</p></a></li>
          <li class="nav-item"><a href="../albums/view.php" class="nav-link"><i class="nav-icon fas fa-compact-disc"></i><p>Albums</p></a></li>
          <li class="nav-item"><a href="../artists/view.php" class="nav-link"><i class="nav-icon fas fa-users"></i><p>Artists</p></a></li>
          <li class="nav-item"><a href="../tracks/view.php" class="nav-link"><i class="nav-icon fas fa-music"></i><p>Tracks</p></a></li>
          <li class="nav-item"><a href="../events/view.php" class="nav-link"><i class="nav-icon fas fa-calendar-alt"></i><p>Events</p></a></li>
          <li class="nav-item"><a href="../tours/view.php" class="nav-link"><i class="nav-icon fas fa-bus"></i><p>Tours</p></a></li>
          <li class="nav-item"><a href="../gallery/view.php" class="nav-link"><i class="nav-icon fas fa-images"></i><p>Gallery</p></a></li>
          <li class="nav-item"><a href="../member_endorsements/view.php" class="nav-link"><i class="nav-icon fas fa-handshake"></i><p>Endorsements</p></a></li>
          <li class="nav-item"><a href="../subscriber_registrations/view.php" class="nav-link"><i class="nav-icon fas fa-envelope-open-text"></i><p>Subscribers</p></a></li>
          <li class="nav-item"><a href="../albums_type/view.php" class="nav-link"><i class="nav-icon fas fa-tags"></i><p>Albums Type</p></a></li>
          <li class="nav-item"><a href="view.php" class="nav-link active"><i class="nav-icon fas fa-link"></i><p>Albums Artists</p></a></li>
        </ul>
      </nav>
    </div>
  </aside>

  <!-- Content Wrapper -->
  <div class="content-wrapper">
    <div class="content-header">
      <div class="container-fluid">
        <h1 class="m-0">Manage Albums Artists</h1>
      </div>
    </div>

    <section class="content">
      <div class="container-fluid">
        <!-- Menggunakan card-secondary untuk tabel relasi/penghubung -->
        <div class="card card-secondary">
          <div class="card-header">
            <h3 class="card-title"><i class="fas fa-link mr-2"></i>Album & Artist Relations</h3>
            <div class="card-tools">
              <a href="insert.php" class="btn btn-success btn-sm">
                <i class="fas fa-plus mr-1"></i> Add New Relation
              </a>
            </div>
          </div>
          <div class="card-body">
            <table id="relationsTable" class="table table-bordered table-striped table-hover">
              <thead>
                <tr>
                  <th>Album ID</th>
                  <th>Album Title</th>
                  <th>Artist ID</th>
                  <th>Artist Name</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody>
                <?php if (mysqli_num_rows($result) > 0): ?>
                  <?php while ($row = mysqli_fetch_assoc($result)): ?>
                    <tr>
                      <td class="text-center">
                        <span class="badge badge-secondary"><?php echo htmlspecialchars($row['album_id']); ?></span>
                      </td>
                      <td>
                        <i class="fas fa-compact-disc text-primary mr-1"></i> 
                        <strong><?php echo htmlspecialchars($row['album_title'] ?? ''); ?></strong>
                      </td>
                      <td class="text-center">
                        <span class="badge badge-secondary"><?php echo htmlspecialchars($row['artist_id']); ?></span>
                      </td>
                      <td>
                        <i class="fas fa-user text-info mr-1"></i> 
                        <strong><?php echo htmlspecialchars($row['artist_name'] ?? ''); ?></strong>
                      </td>
                      <td>
                        <a href="edit.php?album=<?php echo $row['album_id']; ?>&artist=<?php echo $row['artist_id']; ?>" class="btn btn-warning btn-sm">
                          <i class="fas fa-edit"></i> Edit
                        </a>
                        <button onclick="deleteRelation(<?php echo $row['album_id']; ?>, <?php echo $row['artist_id']; ?>)" class="btn btn-danger btn-sm">
                          <i class="fas fa-trash"></i> Delete
                        </button>
                      </td>
                    </tr>
                  <?php endwhile; ?>
                <?php else: ?>
                  <tr><td colspan="5" class="text-center">No relations found</td></tr>
                <?php endif; ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </section>
  </div>

  <!-- Footer -->
  <footer class="main-footer">
    <strong>BLACKPINK Admin</strong> &copy; 2026
  </footer>

</div>

<!-- Scripts -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap4.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/js/adminlte.min.js"></script>
<script>
  $(document).ready(function() {
    $('#relationsTable').DataTable({
      "paging": true,
      "lengthChange": true,
      "searching": true,
      "ordering": true,
      "info": true,
      "autoWidth": false,
      "responsive": true,
    });
  });

  // Fungsi hapus khusus untuk Primary Key Gabungan
  function deleteRelation(albumId, artistId) {
    if (confirm('Are you sure you want to remove this relation?')) {
      const formData = new FormData();
      formData.append('album_id', albumId);
      formData.append('artist_id', artistId);
      
      fetch('delete.php', {
        method: 'POST',
        body: formData
      })
      .then(response => {
        if (response.ok) {
          alert('Relation removed successfully!');
          location.reload();
        } else {
          alert('Failed to remove relation.');
        }
      });
    }
  }
</script>
</body>
</html>