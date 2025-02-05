<?php
$config = require_once '../data/config.php';

exec("{$config['python_path']} {$config['project_root']}/runnable/update_guilds.py");
echo json_encode(["success" => true]);
?>