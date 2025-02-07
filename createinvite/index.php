<?php
$config   = require_once '../data/config.php';

exec("{$config['python_path']} {$config['project_root']}/runnable/update_guilds.py");
$guilds_data = json_decode(file_get_contents("{$config['project_root']}/data/guilds.json"));
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Create Invite</title>
  <!-- Bootstrap CSS -->
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
  <style>
    body {
      background-color: #121212;
      color: #e0e0e0;
      padding-top: 70px;
    }
    .navbar {
      margin-bottom: 20px;
    }
    .form-container {
      background-color: #1e1e1e;
      padding: 30px;
      border-radius: 8px;
      box-shadow: 0 2px 5px rgba(0, 0, 0, 0.5);
    }
    .form-container label {
      color: #e0e0e0;
    }
    .form-control {
      background-color: #2c2c2c;
      color: #e0e0e0;
      border: 1px solid #444;
    }
    .form-control:focus {
      background-color: #2c2c2c;
      color: #e0e0e0;
    }
    .form-check-label {
      color: #e0e0e0;
    }
    .btn-primary {
      background-color: #007bff;
      border-color: #007bff;
    }
    .btn-primary:hover {
      background-color: #0069d9;
      border-color: #0062cc;
    }
    /* Hide additional fields until a server is selected */
    #additional_fields {
      display: none;
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
    
    // Function to show/hide additional fields based on server selection
    function updateAdditionalFieldsVisibility() {
      var serverSelect = document.getElementById('serverSelect');
      var additionalFields = document.getElementById('additional_fields');
      if (serverSelect.value) {
        additionalFields.style.display = 'block';
      } else {
        additionalFields.style.display = 'none';
      }
    }
  </script>
</head>
<body>
  <!-- Navbar -->
  <nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top">
    <a class="navbar-brand" href="../index.php">RIT GCCIS Discord Invite Management</a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav"
     aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
     <ul class="navbar-nav ml-auto">
         <li class="nav-item"><a class="nav-link" href="../index.php">Main Menu</a></li>
         <li class="nav-item"><a class="nav-link" href="#" onclick="open_invite()">Add to Server</a></li>
         <li class="nav-item"><a class="nav-link" href="../createinvite/index.php">Create Invites</a></li>
         <li class="nav-item"><a class="nav-link" href="../manageinvites/index.php">Manage Invites</a></li>
     </ul>
    </div>
  </nav>
  
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-md-8 form-container">
        <h2 class="mb-4 text-center">Generate Unique Invite Link</h2>
        <form id="inviteForm" action="handle_invite_creation.php" method="POST">
          <!-- Server Selection (Required) -->
          <div class="form-group">
            <label for="serverSelect">Select Server <span style="color:red;">*</span></label>
            <select class="form-control" id="serverSelect" name="server" required onchange="updateAdditionalFieldsVisibility()">
              <option value="">-- Select a Server --</option>
              <?php foreach ($guilds_data as $guild): ?>
                <option value='<?php echo json_encode(["id" => $guild->id, "name" => $guild->name]); ?>'>
                  <?php echo htmlspecialchars($guild->name); ?>
                </option>
              <?php endforeach; ?>
            </select>
            <small class="form-text text-muted">This field is required.</small>
          </div>
          
          <!-- Additional fields, hidden until a server is selected -->
          <div id="additional_fields">
            <!-- Description Input (Required) -->
            <div class="form-group">
              <label for="inviteDescription">Description <span style="color:red;">*</span></label>
              <input type="text" class="form-control" id="inviteDescription" name="description" placeholder="Enter a description for the invite" required pattern="^(?!\s*$).+" title="Cannot be only whitespace.">
              <small class="form-text text-muted">This field is required.</small>
            </div>
            <!-- Nick Prefix and Nick Suffix Fields (Optional) -->
            <div class="row">
              <div class="col-md-6">
                <div class="form-group">
                  <label for="nickPrefix">Nickname Prefix (Optional)</label>
                  <input type="text" class="form-control" id="nickPrefix" name="nick_prefix" placeholder="Enter a nickname prefix" pattern="^(?!\s*$).+" title="Cannot be only whitespace.">
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-group">
                  <label for="nickSuffix">Nickname Suffix (Optional)</label>
                  <input type="text" class="form-control" id="nickSuffix" name="nick_suffix" placeholder="Enter a nickname suffix" pattern="^(?!\s*$).+" title="Cannot be only whitespace.">
                </div>
              </div>
            </div>
            <!-- Roles Selection (Optional) -->
            <div class="form-group">
              <label>Select Roles to Assign (Optional)</label>
              <div id="rolesContainer">
                <p>Select a server above to view roles.</p>
              </div>
              <small class="form-text text-muted">
                Only roles that the application has permissions to assign are displayed.
              </small>
            </div>
            <button type="submit" class="btn btn-primary btn-block">Generate Invite Link</button>
          </div>
        </form>
        <div id="inviteResult" class="mt-4"></div>
      </div>
    </div>
  </div>
  
  <!-- Pass guilds data to JavaScript -->
  <script>
    var guildsData = <?php echo json_encode($guilds_data); ?>;
    
    document.getElementById('serverSelect').addEventListener('change', function() {
      var rolesContainer = document.getElementById('rolesContainer');
      rolesContainer.innerHTML = '';
      
      if (!this.value) {
        rolesContainer.innerHTML = '<p>Select a server above to view roles.</p>';
        return;
      }
      
      // Parse the JSON value to retrieve both id and name.
      var selectedServer;
      try {
        selectedServer = JSON.parse(this.value);
      } catch (e) {
        console.error('Error parsing server data:', e);
        rolesContainer.innerHTML = '<p>Invalid server data.</p>';
        return;
      }
      
      // Find the full guild object using the server id.
      var selectedGuild = guildsData.find(function(guild) {
        return guild.id === selectedServer.id;
      });
      
      if (selectedGuild && selectedGuild.roles && selectedGuild.roles.length > 0) {
        selectedGuild.roles.forEach(function(role) {
          var div = document.createElement('div');
          div.className = 'form-check';
          
          var input = document.createElement('input');
          input.className = 'form-check-input';
          input.type = 'checkbox';
          input.name = 'roles[]';
          // Pass both role id and role name as a JSON string.
          input.value = JSON.stringify({ id: role.id, name: role.name });
          input.id = 'role_' + role.id;
          
          var label = document.createElement('label');
          label.className = 'form-check-label';
          label.setAttribute('for', input.id);
          label.textContent = role.name;
          
          div.appendChild(input);
          div.appendChild(label);
          rolesContainer.appendChild(div);
        });
      } else {
        rolesContainer.innerHTML = '<p>No roles found for this server.</p>';
      }
    });
  </script>
  
  <!-- jQuery and Bootstrap JS & dependencies -->
  <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    $(document).ready(function() {
      $('#inviteForm').submit(function(event) {
        event.preventDefault();
        $.ajax({
          url: $(this).attr('action'),
          method: $(this).attr('method'),
          data: $(this).serialize(),
          success: function(response) {
            var html = '<div class="alert alert-success" id="inviteAlert">' +
                        response +
                        '<br><button class="btn btn-secondary mt-2" id="copyInviteLinkButton">Copy Invite Link</button>' +
                       '</div>';
            $('#inviteResult').html(html);
          },
          error: function(xhr, status, error) {
            $('#inviteResult').html('<div class="alert alert-danger">An error occurred: ' + error + '</div>');
          }
        });
      });
      
      $(document).on('click', '#copyInviteLinkButton', function(){
        var inviteLink = $('#inviteAlert a').attr('href');
        if (!inviteLink) {
          alert("No invite link found to copy!");
          return;
        }
        navigator.clipboard.writeText(inviteLink).then(function() {
          $('#copyInviteLinkButton').text('Copied!');
          setTimeout(function(){
            $('#copyInviteLinkButton').text('Copy Invite Link');
          }, 5000);
        }, function(err) {
          console.error('Failed to copy text: ', err);
        });
      });
    });
  </script>
</body>
</html>
