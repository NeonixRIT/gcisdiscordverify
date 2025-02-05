<?php
// Retrieve response_code and response from GET parameters.
$response_code = $_GET['response_code'] ?? 'Unknown';
$encoded_response = $_GET['response'] ?? '';

// 4. Decode the URL-encoded string back to JSON, then to an array
$decoded_str = urldecode($encoded_response);
$response_obj = json_decode($decoded_str, true);
if (json_last_error() === JSON_ERROR_NONE) {
    // $response_obj is now your original object/array
    // e.g. $response_obj['error'], $response_obj['details']['code'], etc.
} else {
    $response_obj = [];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Failure - Error Occurred</title>
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
      box-shadow: 0 2px 5px rgba(0,0,0,0.5);
      max-width: 600px;
      margin-left: auto;
      margin-right: auto;
    }
    .btn-custom {
      background-color: #007bff;
      color: #fff;
      border: none;
      margin-top: 20px;
    }
    .btn-custom:hover {
      background-color: #0069d9;
      color: #fff;
    }
    .response-block {
      background-color: #333;
      color: #cfcfcf;
      padding: 15px;
      border-radius: 6px;
      text-align: left;
      white-space: pre-wrap; /* So long JSON lines can wrap if needed */
      word-wrap: break-word;
    }
  </style>
</head>
<body>
  <!-- Optional Navbar -->
  <nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top">
    <a class="navbar-brand" href="../index.php">RIT GCCIS Discord Invite Management</a>
  </nav>
  
  <div class="container">
    <h1 class="mb-4">Failure</h1>
    <p>There was a problem processing your request.</p>
    <p><strong>Response Code:</strong> <?php echo htmlspecialchars($response_code); ?></p>
    <div class="response-block">
      <?php print_r($response_obj); ?>
    </div>
  </div>

  <!-- Bootstrap JS & Dependencies -->
  <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
