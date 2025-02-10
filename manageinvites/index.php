<?php
$config = require_once '../data/config.php';

// Path to the invites JSON file
$invites_file = "{$config['project_root']}/data/invites.json";
$invites = [];
if (file_exists($invites_file)) {
    $invites = json_decode(file_get_contents($invites_file), true);
}

// Also load the guilds data for editing purposes
$guilds_file = "{$config['project_root']}/data/guilds.json";
$guilds_data = [];
if (file_exists($guilds_file)) {
    $guilds_data = json_decode(file_get_contents($guilds_file), true);
}

// Determine if there are invites
$has_invites = !empty($invites);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Manage Invites</title>
  <!-- Bootstrap CSS -->
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
  <style>
    body {
      background-color: #121212;
      color: #e0e0e0;
      padding-top: 70px; /* Space for fixed navbar */
    }
    .navbar {
      margin-bottom: 20px;
    }
    .container {
      background-color: #1e1e1e;
      padding: 30px;
      border-radius: 8px;
      box-shadow: 0 2px 5px rgba(0,0,0,0.5);
    }
    table {
      color: #e0e0e0;
    }
    th, td {
      border-color: #444;
    }
    .action_btn {
      margin-right: 5px;
    }
    /* Increase clickable area of checkboxes a bit */
    .select_invite {
      transform: scale(1.2);
      margin: 0;
    }
    .nick_prefix::before,
    .nick_suffix::before {
      content: '"';
    }
    .nick_prefix::after,
    .nick_suffix::after {
      content: '"';
    }
  </style>
  <script>
    function open_invite() {
      var url = "<?php echo $config['bot_invite_url']; ?>"; 
      var new_window = window.open(url, '_blank', 'width=500,height=700,top=100,left=100');
      if (new_window) {
          new_window.focus();
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
    <a class="navbar-brand" href="../index.php">RIT GCCIS Discord Invite Management</a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbar_nav"
     aria-controls="navbar_nav" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbar_nav">
      <ul class="navbar-nav ml-auto">
         <li class="nav-item"><a class="nav-link" href="../index.php">Main Menu</a></li>
         <li class="nav-item"><a class="nav-link" href="#" onclick="open_invite()">Add to Server</a></li>
         <li class="nav-item"><a class="nav-link" href="../createinvite/index.php">Create Invites</a></li>
         <li class="nav-item"><a class="nav-link" href="../manageinvites/index.php">Manage Invites</a></li>
      </ul>
    </div>
  </nav>
  
  <!-- Main Container -->
  <div class="container">
    <h2 class="mb-4">Manage Invites</h2>
    <?php if ($has_invites): ?>
      <!-- Bulk Delete Button -->
      <button id="bulk_delete_btn" class="btn btn-danger mb-3">Delete Selected Invites</button>
    <?php endif; ?>
    
    <?php if (!file_exists($invites_file)): ?>
      <div class="alert alert-danger">Invites file not found.</div>
    <?php elseif ($invites === null): ?>
      <div class="alert alert-danger">Error decoding invites JSON.</div>
    <?php elseif (!$has_invites): ?>
      <div class="alert alert-info" id="no_invites_msg">No invites found.</div>
    <?php else: ?>
      <table class="table table-dark table-striped">
        <thead>
          <tr>
            <th><input type="checkbox" id="select_all"></th>
            <th>Invite ID</th>
            <th>Server Name</th>
            <th>Description</th>
            <th>Nickname Prefix</th>
            <th>Nickname Suffix</th>
            <th>Roles</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody id="invites_table_body">
          <?php foreach ($invites as $invite_id => $data): 
            $invite_link = $config['invite_endpoint'].'?id='.$invite_id;
          ?>
            <tr id="invite-<?php echo htmlspecialchars($invite_id); ?>">
              <td><input type="checkbox" class="select_invite" value="<?php echo htmlspecialchars($invite_id); ?>"></td>
              <td><?php echo htmlspecialchars($invite_id); ?></td>
              <td class="server_name"><?php echo htmlspecialchars($data['server_name'] ?? 'N/A'); ?></td>
              <td class="description"><?php echo htmlspecialchars($data['description'] ?? ''); ?></td>
              <td class="nick_prefix"><?php echo htmlspecialchars($data['nick_prefix'] ?? ''); ?></td>
              <td class="nick_suffix"><?php echo htmlspecialchars($data['nick_suffix'] ?? ''); ?></td>
              <td class="roles">
                <?php 
                  if (isset($data['roles']) && is_array($data['roles'])) {
                      $role_names = array_map(function($role) {
                          return $role['name'];
                      }, $data['roles']);
                      echo htmlspecialchars(implode(", ", $role_names));
                  } else {
                      echo 'N/A';
                  }
                ?>
              </td>
              <td>
                <button class="btn btn-sm btn-info action_btn btn_edit"
                  data-invite_id="<?php echo htmlspecialchars($invite_id); ?>"
                  data-server_id="<?php echo htmlspecialchars($data['server_id'] ?? ''); ?>"
                  data-server_name="<?php echo htmlspecialchars($data['server_name'] ?? ''); ?>"
                  data-description="<?php echo htmlspecialchars($data['description'] ?? ''); ?>"
                  data-nick_prefix="<?php echo htmlspecialchars($data['nick_prefix'] ?? ''); ?>"
                  data-nick_suffix="<?php echo htmlspecialchars($data['nick_suffix'] ?? ''); ?>"
                  data-roles='<?php echo json_encode($data['roles'] ?? []); ?>'>
                  Edit
                </button>
                <button class="btn btn-sm btn-danger action_btn btn_delete"
                  data-invite_id="<?php echo htmlspecialchars($invite_id); ?>">
                  Delete
                </button>
                <button class="btn btn-sm btn-secondary action_btn btn_copy"
                  data-invite_link="<?php echo htmlspecialchars($invite_link); ?>">
                  Copy Link
                </button>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    <?php endif; ?>
  </div>
  
  <!-- Edit Invite Modal -->
  <div class="modal fade" id="edit_invite_modal" tabindex="-1" role="dialog" aria-labelledby="edit_invite_modal_label" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content bg-dark text-light">
        <div class="modal-header">
          <h5 class="modal-title" id="edit_invite_modal_label">Edit Invite</h5>
          <button type="button" class="close text-light" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <form id="edit_invite_form">
          <div class="modal-body">
            <input type="hidden" name="invite_id" id="edit_invite_id">
            <!-- Server Selection as Drop Down (Required) -->
            <div class="form-group">
              <label for="edit_server_select">Select Server <span style="color:red;">*</span></label>
              <select class="form-control" id="edit_server_select" name="server" required>
                <option value="">-- Select a Server --</option>
                <?php foreach ($guilds_data as $guild): ?>
                  <option value='<?php echo json_encode(["id" => $guild['id'], "name" => $guild['name']]); ?>'>
                    <?php echo htmlspecialchars($guild['name']); ?>
                  </option>
                <?php endforeach; ?>
              </select>
              <small class="form-text text-muted">This field is required.</small>
            </div>
            <!-- Description Input (Required) -->
            <div class="form-group">
              <label for="edit_description">Description <span style="color:red;">*</span></label>
              <input type="text" class="form-control" id="edit_description" name="description" required pattern="^(?!\s*$).+" title="Cannot be only whitespace.">
            </div>
            <!-- Nick Prefix and Nick Suffix Fields (Optional) -->
            <div class="row">
              <div class="col-md-6">
                <div class="form-group">
                  <label for="edit_nick_prefix">Nickname Prefix (Optional)</label>
                  <input type="text" class="form-control" id="edit_nick_prefix" name="nick_prefix" placeholder="Enter a nickname prefix" pattern="^(?!\s*$).+" title="Cannot be only whitespace.">
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-group">
                  <label for="edit_nick_suffix">Nickname Suffix (Optional)</label>
                  <input type="text" class="form-control" id="edit_nick_suffix" name="nick_suffix" placeholder="Enter a nickname suffix" pattern="^(?!\s*$).+" title="Cannot be only whitespace.">
                </div>
              </div>
            </div>
            <!-- Roles Container (populated based on server selection) -->
            <div class="form-group">
              <label>Select Roles to Assign (Optional)</label>
              <div id="edit_roles_container">
                <!-- Roles checkboxes will be populated here -->
              </div>
              <small class="form-text text-muted">
                Only roles that the application has permissions to assign are displayed.
              </small>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
            <button type="submit" class="btn btn-primary">Save Changes</button>
          </div>
        </form>
      </div>
    </div>
  </div>
  
  <!-- jQuery and Bootstrap JS & dependencies -->
  <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    // Expose guilds_data to JavaScript (for use in the edit modal)
    var guilds_data = <?php echo json_encode($guilds_data); ?>;
    
    // Function to populate roles checkboxes in the edit modal based on the selected server
    function populate_edit_roles(selected_server_obj, preselected_roles) {
      var container = $('#edit_roles_container');
      container.empty();
      
      if (!selected_server_obj) {
        container.html('<p>Select a server to view roles.</p>');
        return;
      }
      
      // Find the guild object in guilds_data that matches the selected server id
      var selected_guild = guilds_data.find(function(guild) {
        return guild.id === selected_server_obj.id;
      });
      
      if (selected_guild && selected_guild.roles && selected_guild.roles.length > 0) {
        selected_guild.roles.forEach(function(role) {
          var div = $('<div class="form-check"></div>');
          var input = $('<input class="form-check-input" type="checkbox" name="roles[]" />');
          // Set value as JSON string with id and name
          input.val(JSON.stringify({ id: role.id, name: role.name }));
          input.attr('id', 'edit_role_' + role.id);
          
          // If preselected_roles (an array) contains a role with the same id, mark the checkbox as checked
          if (preselected_roles && preselected_roles.some(function(r) { return r.id === role.id; })) {
            input.prop('checked', true);
          }
          
          var label = $('<label class="form-check-label"></label>');
          label.attr('for', 'edit_role_' + role.id);
          label.text(role.name);
          
          div.append(input).append(label);
          container.append(div);
        });
      } else {
        container.html('<p>No roles found for this server.</p>');
      }
    }
    
    $(document).ready(function() {
      // Select All functionality
      $('#select_all').on('change', function() {
        $('.select_invite').prop('checked', this.checked);
      });
      
      $(document).on('change', '.select_invite', function() {
        if (!this.checked) {
          $('#select_all').prop('checked', false);
        } else if ($('.select_invite:checked').length === $('.select_invite').length) {
          $('#select_all').prop('checked', true);
        }
      });
      
      // Bulk Delete functionality (unified endpoint)
      $('#bulk_delete_btn').click(function() {
        var selected_invite_ids = [];
        $('.select_invite:checked').each(function() {
          selected_invite_ids.push($(this).val());
        });
        
        if (selected_invite_ids.length === 0) {
          alert("No invites selected for deletion.");
          return;
        }
        
        if (confirm("Are you sure you want to delete the selected invites?")) {
          $.post('delete_invites.php', { invite_ids: selected_invite_ids }, function(response) {
            selected_invite_ids.forEach(function(invite_id) {
              $('#invite-' + $.escapeSelector(invite_id)).remove();
            });
            // If there are no invite checkboxes left, remove the entire table
            if ($('.select_invite').length === 0) {
              $('#invites_table_body').closest('table').remove();
              $('.container').append('<div class="alert alert-info" id="no_invites_msg">No invites found.</div>');
              $('#bulk_delete_btn').hide();
            }
          }).fail(function() {
            alert("Error deleting selected invites.");
          });
        }
      });
      
      // Individual Delete functionality (using the unified endpoint)
      $('.btn_delete').click(function() {
        var invite_id = $(this).data('invite_id');
        if (confirm("Are you sure you want to delete this invite?")) {
          $.post('delete_invites.php', { invite_ids: [invite_id] }, function(response) {
            $('#invite-' + $.escapeSelector(invite_id)).remove();
            if ($('.select_invite').length === 0) {
              $('#invites_table_body').closest('table').remove();
              $('.container').append('<div class="alert alert-info" id="no_invites_msg">No invites found.</div>');
              $('#bulk_delete_btn').hide();
            }
          }).fail(function() {
            alert("Error deleting invite.");
          });
        }
      });
      
      // Copy Invite Link functionality
      $('.btn_copy').click(function() {
        var $this = $(this);
        var invite_link = $this.data('invite_link');
        if (!invite_link) {
          alert("No invite link found to copy!");
          return;
        }
        navigator.clipboard.writeText(invite_link).then(function() {
          $this.text('Copied!');
          setTimeout(function() {
            $this.text('Copy Link');
          }, 5000);
        }).catch(function(err) {
          alert("Failed to copy invite link.");
        });
      });
      
      // Edit functionality: when an edit button is clicked, update guilds before opening modal
      $('.btn_edit').click(function() {
        $.get('update_guilds.php', function(update_response) {
          var invite_id = $(this).data('invite_id');
          var server_id = $(this).data('server_id');
          var server_name = $(this).data('server_name');
          var description = $(this).data('description');
          var nick_prefix = $(this).data('nick_prefix');
          var nick_suffix = $(this).data('nick_suffix');
          var roles = $(this).data('roles'); // roles may be an array or a JSON string
          if (typeof roles === "string") {
            try {
              roles = JSON.parse(roles);
            } catch (e) {
              roles = [];
            }
          }
          var current_server = { id: server_id, name: server_name };
          $('#edit_server_select').val(JSON.stringify(current_server));
          $('#edit_description').val(description);
          $('#edit_nick_prefix').val(nick_prefix);
          $('#edit_nick_suffix').val(nick_suffix);
          populate_edit_roles(current_server, roles);
          $('#edit_invite_id').val(invite_id);
          $('#edit_invite_modal').modal('show');
        }.bind(this));
      });
      
      // When the server drop down in the edit modal changes, update the roles container
      $('#edit_server_select').on('change', function() {
        var value = $(this).val();
        var selected_server;
        try {
          selected_server = JSON.parse(value);
        } catch (e) {
          selected_server = null;
        }
        populate_edit_roles(selected_server, []);
      });
      
      // Handle submission of the edit invite form
      $('#edit_invite_form').submit(function(event) {
        event.preventDefault();
        var selected_roles = [];
        $('#edit_roles_container input[name="roles[]"]:checked').each(function() {
          selected_roles.push($(this).val());
        });
        var form_data = $(this).serializeArray();
        form_data.push({ name: "roles", value: JSON.stringify(selected_roles) });
        
        $.post('edit_invite.php', form_data, function(response) {
          var invite_id = $('#edit_invite_id').val();
          var new_server_data = JSON.parse($('#edit_server_select').val());
          var new_description = $('#edit_description').val();
          var new_nick_prefix = $('#edit_nick_prefix').val();
          var new_nick_suffix = $('#edit_nick_suffix').val();
          var role_names = [];
          selected_roles.forEach(function(role_json) {
            var role_obj = JSON.parse(role_json);
            role_names.push(role_obj.name);
          });
          
          var row = $('#invite-' + $.escapeSelector(invite_id));
          // Update the visible cells
          row.find('.server_name').text(new_server_data.name);
          row.find('.description').text(new_description);
          row.find('.nick_prefix').text(new_nick_prefix);
          row.find('.nick_suffix').text(new_nick_suffix);
          row.find('.roles').text(role_names.join(", "));
          
          // Update the data attributes on the Edit button so subsequent edits load the new values
          var editBtn = row.find('.btn_edit');
          editBtn.data('server_id', new_server_data.id);
          editBtn.data('server_name', new_server_data.name);
          editBtn.data('description', new_description);
          editBtn.data('nick_prefix', new_nick_prefix);
          editBtn.data('nick_suffix', new_nick_suffix);
          // Save the roles as a JSON string
          editBtn.data('roles', JSON.stringify(selected_roles));
          
          $('#edit_invite_modal').modal('hide');
        }).fail(function() {
          alert("Error modifying invite.");
        });
      });
    });
  </script>
</body>
</html>
