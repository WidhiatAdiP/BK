<?php
if (!isset($_SESSION)) {
  session_start();
}

// Include the database connection file (koneksi.php)
include_once("koneksi.php");

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_pasien_modal'])) {
  $id = $_POST['id'];
  $newNamaPasien = $_POST['new_nama_pasien'];
  $newAlamat = $_POST['new_alamat'];
  $newNoKTP = $_POST['new_no_ktp'];
  $newNoHP = $_POST['new_no_hp'];
  $newNoRM = $_POST['new_no_rm'];

  // Update pasien in the database using prepared statement
  $updateQuery = "UPDATE pasien SET nama=?, alamat=?, no_ktp=?, no_hp=?, no_rm=? WHERE id=?";
  $stmt = $mysqli->prepare($updateQuery);
  $stmt->bind_param("sssssi", $newNamaPasien, $newAlamat, $newNoKTP, $newNoHP, $newNoRM, $id);

  if ($stmt->execute()) {
    // Update successful
    header("Location: pasienAdmin.php");
    exit();
  } else {
    // Update failed, handle error (you may redirect or display an error message)
    echo "Update failed: " . $stmt->error;
  }

  $stmt->close();
  
}
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_pasien'])) {
  $newNamaPasien = $_POST['add_nama_pasien'];
  $newAlamat = $_POST['add_alamat'];
  $newNoKTP = $_POST['add_no_ktp'];
  $newNoHP = $_POST['add_no_hp'];
  $newNoRM = $_POST['add_no_rm'];

  // Insert new pasien into the database using prepared statement
  $insertQuery = "INSERT INTO pasien (nama, alamat, no_ktp, no_hp, no_rm) VALUES (?, ?, ?, ?, ?)";
  $stmt = $mysqli->prepare($insertQuery);
  $stmt->bind_param("sssss", $newNamaPasien, $newAlamat, $newNoKTP, $newNoHP, $newNoRM);

  if ($stmt->execute()) {
    // Insertion successful
    header("Location: pasienAdmin.php");
    exit();
  } else {
    // Insertion failed, handle error (you may redirect or display an error message)
    echo "Insertion failed: " . $stmt->error;
  }

  $stmt->close();
}

// Menangani penghapusan pasien
if (isset($_POST['delete_pasien'])) {
  $id = $_POST['id'];

  // Lanjutkan dengan penghapusan pasien
  $deletePasienQuery = "DELETE FROM pasien WHERE id=?";
  $stmtPasien = $mysqli->prepare($deletePasienQuery);
  $stmtPasien->bind_param("i", $id);

  // Jalankan penghapusan pasien
  if ($stmtPasien->execute()) {
      // Penghapusan pasien berhasil
      // Bersihkan output buffer
      ob_clean();

      // Redirect kembali ke halaman utama atau tampilkan pesan keberhasilan
      header("Location: pasienAdmin.php");
      exit();
  } else {
      // Penghapusan pasien gagal, tangani kesalahan
      echo "Penghapusan pasien gagal: " . $stmtPasien->error;
  }

  // Tutup prepared statement
  $stmtPasien->close();
}

// Fetch data from the 'pasien' table
$pasienQuery = "SELECT * FROM pasien";
$pasienResult = $mysqli->query($pasienQuery);

// Fetch the data as an associative array
$pasienData = $pasienResult->fetch_all(MYSQLI_ASSOC);

$nomorUrut = 1;
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <!-- Add your head section here -->

  <!-- Bootstrap CSS -->
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.6.0/css/bootstrap.min.css">

  <!-- Add other necessary CSS links here -->
</head>

<body class="hold-transition sidebar-mini">
  <div class="wrapper">
    <section class="content">
      <div class="container-fluid">
        <div class="row">
          <div class="col-12">
            <div class="card">
              <div class="card-body">
                <button type="button" class="btn btn-success btn-sm" data-toggle="modal" data-target="#addModal">Tambah Pasien</button>

                <table id="example2" class="table table-bordered table-hover">
                  <thead>
                    <tr>
                      <th>#</th>
                      <th>Nama Pasien</th>
                      <th>Alamat</th>
                      <th>No. KTP</th>
                      <th>No. HP</th>
                      <th>No. RM</th>
                      <th>Aksi</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php
                    foreach ($pasienData as $pasienRow) {
                      echo "<tr>";
                      echo "<td>" . $nomorUrut++ . "</td>"; // Menampilkan nomor urut
                      echo "<td>" . $pasienRow['nama'] . "</td>";
                      echo "<td>" . $pasienRow['alamat'] . "</td>";
                      echo "<td>" . $pasienRow['no_ktp'] . "</td>";
                      echo "<td>" . $pasienRow['no_hp'] . "</td>";
                      echo "<td>" . $pasienRow['no_rm'] . "</td>";
                      echo "<td>
                        <form method='post' action=''>
                            <input type='hidden' name='id' value='" . $pasienRow['id'] . "'>
                            <input type='hidden' name='new_nama_pasien' value='" . $pasienRow['nama'] . "'>
                            <input type='hidden' name='new_alamat' value='" . $pasienRow['alamat'] . "'>
                            <input type='hidden' name='new_no_ktp' value='" . $pasienRow['no_ktp'] . "'>
                            <input type='hidden' name='new_no_hp' value='" . $pasienRow['no_hp'] . "'>
                            <input type='hidden' name='new_no_rm' value='" . $pasienRow['no_rm'] . "'>

                            <button type='button' name='update_pasien' class='btn btn-warning btn-sm update-btn' data-toggle='modal' data-target='#updateModal' 
                            data-id='" . $pasienRow['id'] . "' 
                            data-nama_pasien='" . $pasienRow['nama'] . "' 
                            data-alamat='" . $pasienRow['alamat'] . "'
                            data-no_ktp='" . $pasienRow['no_ktp'] . "'
                            data-no_hp='" . $pasienRow['no_hp'] . "'
                            data-no_rm='" . $pasienRow['no_rm'] . "'>Update</button>
                            
                            <form method='post' action=''>
                                <input type='hidden' name='id' value='" . $pasienRow['id'] . "'>
                                <button type='submit' name='delete_pasien' class='btn btn-danger btn-sm' onclick='return confirm(\"Are you sure?\");'>Delete</button>
                            </form>
                        </form>
                      </td>";
                      echo "</tr>";
                    }
                    ?>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>
  </div>

  <!-- Modal -->
  <div class="modal fade" id="updateModal" tabindex="-1" role="dialog" aria-labelledby="updateModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="updateModalLabel">Perbarui Pasien</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <form method="post" action="pasienAdmin.php">
            <!-- Replace with the actual update PHP file -->
            <input type="hidden" name="id" id="update_id">
            <div class="form-group">
              <label for="update_nama_pasien">Nama Pasien</label>
              <input type="text" class="form-control" id="update_nama_pasien" name="new_nama_pasien" required>
            </div>
            <div class="form-group">
              <label for="update_alamat">Alamat</label>
              <input type="text" class="form-control" id="update_alamat" name="new_alamat" required>
            </div>
            <div class="form-group">
              <label for="update_no_ktp">No. KTP</label>
              <input type="text" class="form-control" id="update_no_ktp" name="new_no_ktp" required>
            </div>
            <div class="form-group">
              <label for="update_no_hp">No. HP</label>
              <input type="text" class="form-control" id="update_no_hp" name="new_no_hp" required>
            </div>
            <div class="form-group">
              <label for="update_no_rm">No. RM</label>
              <input type="text" class="form-control" id="update_no_rm" name="new_no_rm" required>
            </div>
            <button type="submit" name="update_pasien_modal" class="btn btn-primary">Update</button>
          </form>
        </div>
      </div>
    </div>
  </div>

  <!-- Modal for adding pasien -->
  <div class="modal fade" id="addModal" tabindex="-1" role="dialog" aria-labelledby="addModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="addModalLabel">Tambah Pasien</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <form method="post" action="pasienAdmin.php">
            <!-- Replace with the actual add PHP file -->
            <div class="form-group">
              <label for="add_nama_pasien">Nama Pasien</label>
              <input type="text" class="form-control" id="add_nama_pasien" name="add_nama_pasien" required>
            </div>
            <div class="form-group">
              <label for="add_alamat">Alamat</label>
              <input type="text" class="form-control" id="add_alamat" name="add_alamat" required>
            </div>
            <div class="form-group">
              <label for="add_no_ktp">No. KTP</label>
              <input type="text" class="form-control" id="add_no_ktp" name="add_no_ktp" required>
            </div>
            <div class="form-group">
              <label for="add_no_hp">No. HP</label>
              <input type="text" class="form-control" id="add_no_hp" name="add_no_hp" required>
            </div>
            <div class="form-group">
              <label for="add_no_rm">No. RM</label>
              <input type="text" class="form-control" id="add_no_rm" name="add_no_rm" required>
            </div>
            <button type="submit" name="add_pasien" class="btn btn-primary">Tambah</button>
          </form>
        </div>
      </div>
    </div>
  </div>

  <!-- Bootstrap JS and jQuery -->
  <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.6.0/js/bootstrap.min.js"></script>

  <!-- Add other necessary script includes here -->

  <script>
    document.addEventListener('DOMContentLoaded', function() {
      // Add your JavaScript code here
      var updateButtons = document.querySelectorAll('.update-btn');

      updateButtons.forEach(function(button) {
        button.addEventListener('click', function() {
          var id = button.getAttribute('data-id');
          var nama_pasien = button.getAttribute('data-nama_pasien');
          var alamat = button.getAttribute('data-alamat');
          var no_ktp = button.getAttribute('data-no_ktp');
          var no_hp = button.getAttribute('data-no_hp');
          var no_rm = button.getAttribute('data-no_rm');

          document.getElementById('update_id').value = id;
          document.getElementById('update_nama_pasien').value = nama_pasien;
          document.getElementById('update_alamat').value = alamat;
          document.getElementById('update_no_ktp').value = no_ktp;
          document.getElementById('update_no_hp').value = no_hp;
          document.getElementById('update_no_rm').value = no_rm;
        });
      });
    });
  </script>
</body>

</html>