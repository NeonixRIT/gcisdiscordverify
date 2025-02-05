<?php
$config = require_once '../data/config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die("Invalid request method.");
}

if (!isset($_POST['invite_ids']) || empty($_POST['invite_ids'])) {
    die("No invite ID(s) provided.");
}
$invite_ids = $_POST['invite_ids'];
$cmd = "{$config['python_path']} {$config['project_root']}/runnable/delete_invites.py";
foreach ($invite_ids as $invite_id) {
    $cmd = "{$cmd} {$invite_id}";
}
exec($cmd);
?>