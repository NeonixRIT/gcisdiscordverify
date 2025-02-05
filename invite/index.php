<?php
$config = require_once '../data/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (empty($_GET['id'])) {
        die("No invite `id` provided.");
    }
    $invite_id = $_GET['id'];
    $invites = json_decode(file_get_contents("{$config['project_root']}/data/invites.json"));
    if (!property_exists($invites, $invite_id)) {
       die("Invalid invite `id` provided.");
    }

    $invite_id_base64 = base64_encode($invite_id);
    $discord_redirect_url = "{$config['client_oauth_url']}&state={$invite_id}";
    header('Location: '.$discord_redirect_url, true, 301);
    die();
}
?>