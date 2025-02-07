<?php
$config   = require_once '../data/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate input: ensure a server was selected.
    if (empty($_POST['server'])) {
        die("No server selected.");
    }

    if (empty($_POST['description'])) {
        die("No description provided.");
    }

    $server_data = json_decode($_POST['server'], true);
    if (json_last_error() !== JSON_ERROR_NONE || !is_array($server_data)) {
        die("Invalid server data provided.");
    }
    
    // Extract server id and name.
    $server_id   = $server_data['id'];
    $server_name = $server_data['name'];
    $server_name_b64 = base64_encode($server_name);

    // Extract and encode the description.
    $description = trim($_POST['description']);
    $safeDescription = htmlspecialchars($description, ENT_QUOTES, 'UTF-8');
    $safeDescription_b64 = base64_encode($safeDescription);

    // Extract and decode nickname prefix/suffix
    $nick_prefix = !empty($_POST['nick_prefix']) ? $_POST['nick_prefix'] : ' ';
    $nick_prefix_b64 = base64_encode($nick_prefix);
    $nick_suffix = !empty($_POST['nick_suffix']) ? $_POST['nick_suffix'] : ' ';
    $nick_suffix_b64 = base64_encode($nick_suffix);
    
    // Extract and decode the roles.
    $roles = isset($_POST['roles']) ? $_POST['roles'] : [];
    $decoded_roles = [];
    
    foreach ($roles as $role_json) {
        $role_data = json_decode($role_json, true);
        if (json_last_error() === JSON_ERROR_NONE && is_array($role_data)) {
            $decoded_roles[] = $role_data;
        } else {
            die("Invalid role data provided.");
        }
    }

    $cmd = "{$config['python_path']} {$config['project_root']}/runnable/generate_invite.py {$server_id} {$server_name_b64} {$safeDescription_b64} {$nick_prefix_b64} {$nick_suffix_b64}";
    foreach ($decoded_roles as $role) {
        $role_name_b64 = base64_encode($role['name']);
        $cmd = "{$cmd} {$role['id']} {$role_name_b64}";
    }
    exec($cmd, $invite_id);
    
    $invite_link = $config['invite_endpoint'] . "?id=" . $invite_id[0];
    echo "Invite link generated: <a href='" . htmlspecialchars($invite_link) . "'>" . htmlspecialchars($invite_link) . "</a>";
}
?>