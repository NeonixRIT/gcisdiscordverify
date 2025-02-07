<?php
$config = require_once '../data/config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die("Invalid request method.");
}

if (!isset($_POST['invite_id'])) {
    die("No invite id provided.");
}

if (!isset($_POST['server'])) {
    die("No invite server info provided.");
}

// Retrieve the posted invite_id.
$invite_id = $_POST['invite_id'] ?? '';

// Retrieve and decode the server data (JSON string) from the drop-down.
$server_json = $_POST['server'] ?? '';
$server_data = json_decode($server_json, true);
if (json_last_error() !== JSON_ERROR_NONE) {
    $server_data = [];
}
$server_id = $server_data['id'] ?? '';
$server_name = $server_data['name'] ?? '';
$server_name_b64 = base64_encode($server_name);

// Retrieve the description.
$description = $_POST['description'] ?? '';
$description_b64 = base64_encode($description);

// Retrieve the nickname prefix and suffix.
$nick_prefix = !empty($_POST['nick_prefix']) ? $_POST['nick_prefix'] : ' ';;
$nick_prefix_b64 = base64_encode($nick_prefix);
$nick_suffix = !empty($_POST['nick_suffix']) ? $_POST['nick_suffix'] : ' ';;
$nick_suffix_b64 = base64_encode($nick_suffix);

// Retrieve and decode the roles data.
// The roles field is sent as a JSON string representing an array of JSON strings.
$roles_json = $_POST['roles'] ?? '';
$selected_roles = json_decode($roles_json, true);
if (json_last_error() !== JSON_ERROR_NONE) {
    $selected_roles = [];
}

// Decode each role JSON string into an associative array.
$decoded_roles = [];
if (is_array($selected_roles)) {
    foreach ($selected_roles as $role_json) {
        $role_data = json_decode($role_json, true);
        if (json_last_error() === JSON_ERROR_NONE) {
            $decoded_roles[] = $role_data;
        }
    }
}

$cmd = "{$config['python_path']} {$config['project_root']}/runnable/edit_invite.py {$invite_id} {$server_id} {$server_name_b64} {$description_b64} {$nick_prefix_b64} {$nick_suffix_b64}";
foreach ($decoded_roles as $role_data) {
    $role_name_b64 = base64_encode($role_data['name']);
    $cmd = "{$cmd} {$role_data['id']} {$role_name_b64}";
}
exec($cmd);
?>
