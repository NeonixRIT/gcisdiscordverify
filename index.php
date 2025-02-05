<?php
$config   = require_once 'data/config.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Main - RIT GCCIS Discord Invite Management</title>
  <!-- Bootstrap CSS -->
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
  <style>
    body {
      background-color: #121212;
      color: #e0e0e0;
      padding-top: 70px; /* Leave space for fixed navbar */
    }
    .navbar {
      margin-bottom: 20px;
    }
    .card {
      background-color: #1e1e1e;
      border: none;
      border-radius: 8px;
      box-shadow: 0 2px 5px rgba(0, 0, 0, 0.5);
    }
    .card h3 {
      margin-bottom: 15px;
    }
    .btn-custom {
      background-color: #007bff;
      color: #fff;
      border: none;
    }
    .btn-custom:hover {
      background-color: #0069d9;
      color: #fff;
    }
    a {
      text-decoration: none;
    }
  </style>
  <script>
    function open_invite() {
      var url = "<?php echo $config['bot_invite_url']; ?>"; 
      var newWindow = window.open(url, '_blank', 'width=500,height=700,top=100,left=100');

      if (newWindow) {
          newWindow.focus();
      } else {
          alert("Popup blocked! Please allow popups for this site.");
      }
      window.location.href = "index.php";
    }
  </script>
</head>
<body>
  <!-- Navbar -->
  <nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top">
    <a class="navbar-brand" href="index.php">Main Menu</a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav"
     aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
     <ul class="navbar-nav ml-auto">
         <li class="nav-item"><a class="nav-link" href="index.php">Main Menu</a></li>
         <li class="nav-item"><a class="nav-link" href="#" onclick="open_invite()">Add to Server</a></li>
         <li class="nav-item"><a class="nav-link" href="createinvite/index.php">Create Invites</a></li>
         <li class="nav-item"><a class="nav-link" href="manageinvites/index.php">Manage Invites</a></li>
        </ul>
      </div>
    </nav>
    
    <!-- Main Content -->
    <div class="container">
      <h1 class="mb-5 text-center">RIT GCCIS Discord Invite Management</h1>
      <div class="row">
        <div class="col-md-4 mb-4">
          <div class="card p-4 text-center">
            <h3>Add to Server</h3>
            <p>Add the application to your discord server so you can create invites for it.</p>
            <a href="#" onclick="open_invite()" class="btn btn-custom btn-block">Add to Server</a>
          </div>
        </div>
        <div class="col-md-4 mb-4">
          <div class="card p-4 text-center">
            <h3>Create Invites</h3>
            <p>Generate new unique invite links for your Discord servers.</p>
            <a href="createinvite/index.php" class="btn btn-custom btn-block">Create Invites</a>
          </div>
        </div>
        <div class="col-md-4 mb-4">
          <div class="card p-4 text-center">
            <h3>Manage Invites</h3>
            <p>View, update, or delete your existing invites.</p>
            <a href="manageinvites/index.php" class="btn btn-custom btn-block">Manage Invites</a>
          </div>
        </div>
      </div>
    </div>

  <!-- Bootstrap JS & Dependencies -->
  <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>