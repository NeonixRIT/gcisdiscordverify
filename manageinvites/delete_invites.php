<?php
include_once '../data/utils.php';
$config = require_once '../data/config.php';

function main() {
    global $config;
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        die("Invalid request method.");
    }

    if (!isset($_POST['invite_ids']) || empty($_POST['invite_ids'])) {
        die("No invite ID(s) provided.");
    }

    $invite_ids = $_POST['invite_ids'];

    $args = [];
    foreach ($invite_ids as $invite_id) {
        array_push($args, $invite_id);
    }

    $cmd = "{$config['python_path']} {$config['project_root']}/runnable/delete_invites.py";
    $args_string = encode_args_b64($args);
    $cmd_and_args = "{$cmd} {$args_string}";
    exec($cmd_and_args);
}

main();
?>