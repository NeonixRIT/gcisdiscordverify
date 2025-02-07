<?php
$config = require_once '../data/config.php';
$secrets = require_once '../data/secrets.php';

function exchangeCode($code) {
    global $config, $secrets;
    $data = [
        'grant_type'  => 'authorization_code',
        'code'        => $code,
        'redirect_uri'=> $config['redirect_uri'],
    ];

    $postFields = http_build_query($data);
    $url = "{$config['discord_api_url']}/oauth2/token";

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_USERAGENT, $config['user_agent']);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    
    // Set HTTP Basic Authentication using client id and secret
    curl_setopt($ch, CURLOPT_USERPWD, $config['client_id'] . ':' . $secrets['client_secret']);

    // Set the appropriate headers
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/x-www-form-urlencoded'
    ]);

    $response = curl_exec($ch);

    if (curl_errno($ch)) {
        die('Curl error: ' . curl_error($ch));
    }

    $httpStatus = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    if ($httpStatus >= 400) {
        die("HTTP error code: $httpStatus");
    }

    curl_close($ch);

    return json_decode($response, true);
}

function revokeAccessToken($accessToken) {
    global $config, $secrets;
    $data = [
        'token'           => $accessToken,
        'token_type_hint' => 'access_token'
    ];

    $postFields = http_build_query($data);
    $url = "{$config['discord_api_url']}/oauth2/token/revoke";

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_USERAGENT, $config['user_agent']);
    curl_setopt($ch, CURLOPT_USERPWD, $config['client_id'] . ':' . $secrets['client_secret']);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/x-www-form-urlencoded'
    ]);

    $response = curl_exec($ch);
    curl_close($ch);

    return $response;
}

function getUserData($accessToken) {
    global $config, $secrets;
    $url = "{$config['discord_api_url']}/users/@me";

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_USERAGENT, $config['user_agent']);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Authorization: Bearer $accessToken"
    ]);

    $response = curl_exec($ch);
    curl_close($ch);

    return json_decode($response, true);
}

/**
 * Adds a user to a guild using Discord's Add Guild Member endpoint.
 *
 * @param string $accessToken An OAuth2 access token granted with the guilds.join scope for the user.
 * @param string $guildId     The ID of the guild.
 * @param string $userId      The ID of the user to add.
 * @param string $botToken    The bot token to be used in the Authorization header.
 * @param string|null $nick   (Optional) Nickname to assign to the user.
 * @param array $roles        (Optional) Array of role IDs to assign.
 * @param bool $mute          (Optional) Whether the user should be muted in voice channels.
 * @param bool $deaf          (Optional) Whether the user should be deafened in voice channels.
 *
 * @return array Returns an array containing the HTTP code and decoded response (if any).
 */
function addGuildMember($accessToken, $guildId, $userId, $nick = null, $roles = array(), $mute = false, $deaf = false) {
    global $config, $secrets;
    $url = "{$config['discord_api_url']}/guilds/{$guildId}/members/{$userId}";
    
    // Prepare the data to send.
    $data = array(
        'access_token' => $accessToken
    );
    
    if ($nick !== null) {
        $data['nick'] = $nick;
    }
    
    if (!empty($roles)) {
        $data['roles'] = $roles;
    }
    
    // Even if mute and deaf are false, we include them to be explicit.
    $data['mute'] = $mute;
    $data['deaf'] = $deaf;
    
    // Initialize cURL.
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_USERAGENT, $config['user_agent']);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        "Authorization: Bot {$secrets['bot_token']}",
        "Content-Type: application/json"
    ));
    
    // Execute the request.
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    
    if(curl_errno($ch)) {
        $error = curl_error($ch);
        curl_close($ch);
        return array('error' => $error);
    }
    
    curl_close($ch);
    
    // Decode the JSON response if present.
    $decodedResponse = json_decode($response, true);
    
    return array(
        'http_code' => $httpCode,
        'response'  => $decodedResponse
    );
}

$first_name = $_SERVER['givenName'];
$last_name = $_SERVER['sn'];
$rit_username = $_SERVER['uid'];

$code = $_GET['code'];
$result = exchangeCode($code);
$access_token = $result['access_token'];
$user_data = getUserData($access_token);

$discord_username = $user_data['username'];
$invite_id = $_GET['state'];

$invite_data = json_decode(file_get_contents("{$config['project_root']}/data/invites.json"))->$invite_id;
$server_id = $invite_data->{'server_id'};
$roles = $invite_data->{'roles'};
$role_ids = [];
foreach ($roles as $role_data) {
    $role_id = $role_data->{'id'};
    $role_ids[] = $role_id;
}
$user_id = $user_data['id'];
$nick_prefix = $invite_data->{'nick_prefix'} ?? '';
$nick_suffix = $invite_data->{'nick_suffix'} ?? '';

$nick_name = "{$nick_prefix}{$first_name} {$last_name}{$nick_suffix}";
$res = addGuildMember($access_token, $server_id, $user_id, $nick_name, $role_ids);
$revoke_response = revokeAccessToken($access_token);

if ($res['http_code'] == 201 || $res['http_code'] == 204) {
    echo "<script>window.location = 'success.php?server_id={$server_id}';</script>";
} else {
    $resp_data = urlencode(json_encode($res['response']));
    echo "<script>window.location = 'failure.php?response_code={$res['http_code']}&response={$resp_data}';</script>";
}
?>