<?php
// Get the server_id from the query parameter (or another source)
$server_id = $_GET['server_id'] ?? '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Success - Added to Server</title>
  <!-- Bootstrap CSS -->
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
  <style>
    body {
      background-color: #121212;
      color: #e0e0e0;
      padding-top: 70px;
      text-align: center;
    }
    .container {
      background-color: #1e1e1e;
      padding: 30px;
      margin-top: 50px;
      border-radius: 8px;
      box-shadow: 0 2px 5px rgba(0, 0, 0, 0.5);
      max-width: 600px;
      margin-left: auto;
      margin-right: auto;
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
    /* Button container for side-by-side buttons */
    .btn-group {
      display: flex;
      justify-content: center;
      gap: 10px;
      margin-top: 20px;
    }
  </style>
</head>
<body>
  <!-- Optional Navbar -->
  <nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top">
    <a class="navbar-brand" href="../index.php">RIT GCCIS Discord Invite Management</a>
  </nav>
  
  <div class="container">
    <h1 class="mb-4">Success!</h1>
    <p>You have been successfully added to the server.</p>
    <p>You may now close this tab.</p>
    <div class="btn-group">
      <a href="discord://discord.com/channels/<?php echo htmlspecialchars($server_id); ?>" class="btn btn-custom">Open Discord App</a>
    </div>
  </div>

  <!-- Bootstrap JS & Dependencies -->
  <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>