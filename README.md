# RIT GCIS Discord Invite Management
This is a little project to streamline the process of assigning roles and nicknames to RIT students joining a class managed discord server.

Add the application to the discord server and move its role to the very top of the roles list.

Instructors can then create their own links to send to their section which will automatically add them to the discord server with the proper roles and their real name as a nick name when they visit that link. Invites can also be edited and deleted from the provided `Manage Invites` pages.

# My Instance
I am currently hosting this application at https://people.rit.edu/kjc8084/gcisdiscordverify

Because of my position as a Course Assistant for GCIS-123, Student Employees have access to the create and manage invite pages, otherwise I would not be able to access and test my own code.

# Authentication
Authentication is handled by .htaccess and rit shibboleth via apache.

You can find and modify the respective permissions for each subdirectory/page in that folder's `.htaccess` file.

# Folder/File Descriptions
NOTE: I will be skipping `.htaccess` files. They all serve the same purpose of restricting access to the directory based on RIT account roles. the `data` and `runnable` directories have unique `.htaccess` files to deny any requests to their contents

- `project root`: This is the root directory containing all project files. This absolute path should go in the config file
    - `createinvite`: Webpage restricted to Employees and Student Employees by default for creating invite links
        - `handle_invite_creation.php`: Recieves server data (name, id), description, and roles (Array of [name, id]). Parses and sends that Python for formatting saving the info to a file
        - `index.php`: Main page with form to create an invite link. Automatically populates options using Discord's API with the servers it has access to and their roles
    - `data`: Folder contains configs, secrets, and json for managing invite links
        - `default_config.ph`: a config with mostly empty values. Rename to `config.php` and fill in the values. See the config section for more details
        - `default_secrets.ph`: Similar to `default_config.php` but dedicated to storing secrets need to interact with the Discord API. Rename to `secrets.php` and fill in the values. See the config section for more details
        - `guilds.json`: A json of each server name and id that that application has access to as well as the name and id of each of their roles. Not there by default but is created. Updated every time an `edit` window is opened or `createinvite` page is accessed
        - `invites.json`: Json that keeps track of the current active invites. It store a unique 16 character invite ID that represents an invite, as well as a description, server name/id to join with the link, and roles to grant users when they join with the link. Not there by default but is created
    - `invite`: This is the main endpoint users will use to accept an invite
        - `index.php`: ensures that the provided invite id is valid, then redirects to the applications oauth2 discord authentication endpoint. This is a value in the config. For more information see the config section
    - `join`: This is the page the applications oauth2 discord authentication endpoint redirects to
        - `index.php`: Uses discord's oauth workflow to obtain an access token for the user that accepted the invite link. It then uses that access token and the `bot` access token to join a user to a server and assign them roles and a nickname. The negotiated access token for the user is immediately revoked
        - `failure.php`: page that gets displayed if there was an error somewhere along the way
        - `success.ph`: page that get displayed if the user successfully joined the server or was alread a member of the server
    - `manageinvites`: page for viewing, editting, and deleting active invites
        - `delete_invites.php`: intermediary page for parsing parameters (array of invite ids) and sending them to `delete_invites.py`
        - `edit_invite.php`: intermediary page for parsing parameters (each value of the invite) and sending them to `edit_invite.py`
        - `index.php`: main page to view/edit/or delete invites
        - `update_guilds.php`: helper page to update `guilds.json` whenever an edit window is opened
    - `runnable`: contains python code called by the php to do most of the file handling
        - `delete_invites.py`: Using a list of invite ids it deletes them from `invites.json`
        - `edit_invite.py`: Similar to `generate_invite.php` but doesn't generate ids. Updates the json rather than appends to it.
        - `generate_invite.py`: Generates a unique 16 character invite id, appending the invite and its data to `invites.json`
        - `load_data_utils.py`: For parsing/loading `config.php` and `secrets.php` to be usable in python
        - `update_guilds.py`: Authenticates using the `bot` access token to get the servers the application has access to and their roles. Saves the results in `guilds.json`
    - `index.php`: Main page containing links to add application to a server, create invite links, or modify them

# Data Type Definitions
## Role
```json
{
    {
        "name": string,
        "id": string
    }
}
```

## Server
```json
{
    {
        "name": string,
        "id": string,
        "roles": [role]
    }
}
```

## Invite ID
An invite id is a string of at least length 3 with the following restrictions:
- The first and last character can be is `a-z`, `A-Z`, or `0-9`
- The second character can be `-`, `_`, or `.`
- All other characters can be `a-z`, `A-Z`, `0-9`, `-`, `_`, or `.`

## Invite
```json
{
    invite id: {
        "server_name": string,
        "server_id": string,
        "description": string,
        "nick_prefix": string,
        "nick_suffix": string,
        "roles": [role]
    }
}
```

# Config
## Secrets
- `client_secret`: Available and generated through the Discord developer portal. Go to the application and then `OAuth2` and `Reset Secret`
- `bot_token`: Available and generated through the Discord developer portal. Go to the application and then `Bot` and `Reset Token`

## Config
- `python_path`: Absolute path to a python 3 executable on the local system
- `project_root`: Absolute path to the root of the project folder with `data`, `invite`, etc. directories.
- `invite_endpoint`: HTTP URL to page hosting the `invite` directory  
- `redirect_uri`: HTTP URL to the page hosting the `join` directory. This should also match exactly to what is entered in the Discord Developer Portal under `OAuth2` and `Redirects`. It should also be the redirect selected when generating the `client_oauth_url` through the Developer Portal
- `discord_api_url`: Base URL for the discord API (currently /api/v10)
- `client_id`: ID of your Discord Application. Found through the Developer Portal under `OAuth2` and `Client ID`
- `bot_invite_url`: URL to invite your application to servers. Also found in the Developer Portal under `OAuth2` and `OAuth2 URL Generator`. Select the `bot` scope, then the `Administrator` permission, `Guild Install` integration type, and then copy the generated URL
- `client_oauth_url`: URL to retrieve access token for user that `join` will redirect to. Generated in the Developer Portal under `OAuth2` and `OAuth2 URL Generator`. Select `identify` and `guilds.join`, the `redirect_uri` defined above and input under `Redirects`, and then copy the generated URL.
- `user_agent`: User agent to be sent with every request to discord API. This is generally "project_name (where_project_is_hosted, project_version_string)"

## Example Config
Below is the `config.php` used for my instance, to get a sense of what the values should be.
```php
<?php
return [
    "python_path"      => "/usr/bin/python",
    "project_root"     => "/home/kjc8084/www/gcisdiscordverify",
    "invite_endpoint"  => "https://people.rit.edu/kjc8084/gcisdiscordverify/invite",
    "redirect_uri"     => "https://people.rit.edu/kjc8084/gcisdiscordverify/join",
    "discord_api_url"  => "https://discord.com/api/v10",
    "client_id"        => "1336452116679753738",
    "bot_invite_url"   => "https://discord.com/oauth2/authorize?client_id=1336452116679753738&permissions=8&integration_type=0&scope=bot",
    "client_oauth_url" => "https://discord.com/oauth2/authorize?client_id=1336452116679753738&response_type=code&redirect_uri=https%3A%2F%2Fpeople.rit.edu%2Fkjc8084%2Fgcisdiscordverify%2Fjoin&scope=guilds.join+identify",
    "user_agent"       => "RIT Invite Manager (people.rit.edu/kjc8084/gcisdiscordverify, 1.0)",
];
?>
```

# Usage
## Step 0: Setup
- Using the Discord Developer Portal, create an application
  - Ensure "Public Bot" is toggled under the `Bot` section in the Discord Developer Portal if you want others to be able to add the application to their server
- Clone project code and enter appropriate config values
- Adjust .htaccess files as desired
- Host files at `people.rit.edu/your_rit_id` or another rit shibboleth enabled apache server
- Create discord server

## Step 1: Add Application to Discord Server
- Go to the `index.php` page in the project root directory and follow the link to add the application to your server created in step 0
- Go into the server settings and move the application's role above the roles you wish it to be able to apply to users

## Step 2: Create Invite Links
- Go to the `createinvite/index.php` page, select your server and the roles you wish the invite link to apply to users. Add a description to the invite link to remember what is for
- Distribute the invite to section students

## Step 3: Manage Invite Link
- If you made an error in step 2, go to `modifyinvite/index.php` and edit or delete the invite you created

## Step 4: Thats It...
Nothing else :)

# Notes
## Limitations
Developing and testing requires a second Discord user/account as the application does not have permissions to modify the nickname or roles of a server owner.

Authentication to each page is also solely dependant on RIT shibboleth w/ the `.htaccess` files. Without this, it is not currently feasible to reliably use as anyone could create and manage invites.

## User Data Usage and Storage
### Discord
This application uses Discord OAuth2 workflow to authenticate as the user to join a server and retrieve their user id. This data is never saved. The authentication token is immediately revoked after success or failure of accepting the invite link and is also immediately discarded.

### RIT
`peoples.rit.edu` allows unique access to RIT's authentication and user data. This application uses the users RIT `givenName` (first name) and surname, or `sn` (last name) to set the user's nickname to their full name when they join the Discord server. After this is done, this information is immediately discarded and never saved. This data is made available to the application and accessed through the PHP `$_SERVER` variable, as RIT adds this information to it.

## AI Usage
A majority of this project was developed using the ChatGPT o3-mini-high model. It is responsible almost all of the HTML/CSS/JS. It also wrote a large portion of the PHP code (mainly in the use of curl to make requests), as well as some of the Python code for interacting with the Discord API. The entire chat history used in the making of this project is available in the `Resources` section. If any secrets happen to have leaked in the chat log, they have long been changed. Copilot was also used but largely for minor autocompletions.

## TODO
- Fine tune `.htaccess`
- Ensure bot permissions are minimal. Administrator it likely not needed but what does it need?
- Unify naming conventions in all files to use `snake_case`
- Remove most AI line comments and properly comment secions of code and files
- Modularize the HTML/CSS/JS?
- Togglable dark/light modes?
- Modify user if already a member of server?

# Resources
- [Discord Developer Portal](https://discord.com/developers/applications)
- [Discord API Documentation](https://discord.com/developers/docs/intro)
- [Chat GPT Chat History](https://chatgpt.com/share/67a3c9b8-538c-8010-8549-ca501cdb1723)
- [RIT Website Documentation](https://www.rit.edu/webresources/official-rit-website-documentation)
