<?php
$config   = require_once '../data/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate input: ensure a server was selected.
    if (empty($_POST['server'])) {
        die("No server selected.");
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
    $description = isset($_POST['description']) ? trim($_POST['description']) : '';
    $safeDescription = htmlspecialchars($description, ENT_QUOTES, 'UTF-8');
    $safeDescription_b64 = base64_encode($safeDescription);
    
    $roles = isset($_POST['roles']) ? $_POST['roles'] : [];
    $decoded_roles = [];
    
    // Decode each role JSON string.
    foreach ($roles as $role_json) {
        $role_data = json_decode($role_json, true);
        if (json_last_error() === JSON_ERROR_NONE && is_array($role_data)) {
            $decoded_roles[] = $role_data;
        } else {
            die("Invalid role data provided.");
        }
    }

    $cmd = "{$config['python_path']} {$config['project_root']}/runnable/generate_invite.py {$server_id} {$server_name_b64} {$safeDescription_b64}";
    foreach ($decoded_roles as $role) {
        $role_name_b64 = base64_encode($role['name']);
        $cmd = "{$cmd} {$role['id']} {$role_name_b64}";
    }

    exec($cmd, $invite_id);
    
    $invite_link = $config['invite_endpoint'] . "?id=" . $invite_id[0];
    echo "Invite link generated: <a href='" . htmlspecialchars($invite_link) . "'>" . htmlspecialchars($invite_link) . "</a>";
}
?>