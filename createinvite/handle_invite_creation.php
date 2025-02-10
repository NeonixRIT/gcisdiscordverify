<?php
$config   = require_once '../data/config.php';
include_once '../data/utils.php';

function main() {
    global $config;

    // Validate request method: must be POST.
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        die("Invalid request method.");
    }
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

    // Extract and encode the description.
    $description = trim($_POST['description']);
    $safeDescription = htmlspecialchars($description, ENT_QUOTES, 'UTF-8');

    // Extract and decode nickname prefix/suffix
    $nick_prefix = !empty($_POST['nick_prefix']) ? $_POST['nick_prefix'] : ' ';
    $nick_suffix = !empty($_POST['nick_suffix']) ? $_POST['nick_suffix'] : ' ';

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

    $args = [$server_id, $server_name, $safeDescription, $nick_prefix, $nick_suffix];
    foreach ($decoded_roles as $role) {
        array_push($args, $role['id']);
        array_push($args, $role['name']);
    }

    $args_string = encode_args_b64($args);
    $cmd = "{$config['python_path']} {$config['project_root']}/runnable/generate_invite.py";
    $cmd_and_args = "{$cmd} {$args_string}";
    exec($cmd_and_args, $invite_id);

    $invite_link = $config['invite_endpoint'] . "?id=" . $invite_id[0];
    echo "Invite link generated: <a href='" . htmlspecialchars($invite_link) . "'>" . htmlspecialchars($invite_link) . "</a>";
}

main();
?>