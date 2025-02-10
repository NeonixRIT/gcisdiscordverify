import requests
import json

from load_data_utils import load_config, load_secrets

CONFIG = load_config()
SECRETS = load_secrets()

def send_discord_api_request(token, is_bot, endpoint, method="GET", data=None, additional_headers=None):
    """
    Send an authenticated request to Discord's API.
    
    :param token: The authentication token.
    :param is_bot: Boolean flag; if True, the token is treated as a bot token.
    :param endpoint: The API endpoint (e.g., "/users/@me").
    :param method: HTTP method to use (default: "GET").
    :param data: Optional payload (will be JSON encoded if provided).
    :param additional_headers: Optional dictionary of extra headers.
    :return: A dict containing the HTTP status code and the decoded JSON response or error.
    """
    url = CONFIG['discord_api_url'] + endpoint

    # Prepare headers dictionary
    headers = additional_headers.copy() if additional_headers else {}
    
    # If data is provided, add the JSON content type header
    if data is not None:
        headers["Content-Type"] = "application/json"

    # Set the Authorization header depending on whether it's a bot token
    headers["Authorization"] = f"Bot {token}" if is_bot else token
    headers["User-Agent"] = f"{CONFIG['user_agent']}"

    try:
        # If data is provided, send it as a JSON string
        if data is not None:
            response = requests.request(method, url, headers=headers, data=json.dumps(data))
        else:
            response = requests.request(method, url, headers=headers)
    except requests.RequestException as e:
        return {
            "http_code": 0,
            "error": str(e)
        }

    # Attempt to decode the response as JSON
    try:
        response_json = response.json()
    except ValueError:
        response_json = None

    return {
        "http_code": response.status_code,
        "response": response_json
    }


data = []

is_bot = True
guilds_endpoint = '/users/@me/guilds'
bot_token = SECRETS['bot_token']
guilds_result = send_discord_api_request(bot_token, is_bot, guilds_endpoint)['response']
for guild in guilds_result:
    guild_id = guild['id']
    guild_name = guild['name']
    guild_roles_endpoint = f'/guilds/{guild_id}/roles'
    guild_roles = send_discord_api_request(bot_token, is_bot, guild_roles_endpoint)['response']
    bot_role_pos = guild_roles[-1]['position'] # Bot's role is always the last one for /api/v10
    data.append(
        {
            'name': guild_name,
            'id': guild_id,
            'roles': [
                {
                    'name': role['name'],
                    'id': role['id']
                } 
            for role in sorted(guild_roles[:-1], key=lambda x: x['position'], reverse=True)
            if role['name'] != "@everyone" and role['position'] <= bot_role_pos
            ]
        }
    )

with open(f'{CONFIG["project_root"]}/data/guilds.json', 'w') as f:
    json.dump(data, f, indent=4)
