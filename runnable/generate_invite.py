from sys import argv
from json import load, dump
from random import choice, choices
from pathlib import Path
from base64 import b64decode

from load_data_utils import load_config

CONFIG = load_config()
invites_path = Path(f'{CONFIG["project_root"]}/data/invites.json')
if not invites_path.exists():
    with open(invites_path, 'w') as invites_fd:
        invites_fd.write('{}')

invites = None
with open(invites_path, 'r') as invites_fd:
    invites = load(invites_fd)

server_id: str = argv[1]
server_name_b64: str = argv[2]
description_b64: str = argv[3]
roles = argv[4:]

server_name = b64decode(server_name_b64).decode('utf-8')
description = b64decode(description_b64).decode('utf-8')

parsed_roles = []
for i in range(0, len(roles), 2):
    role_id: str = roles[i]
    role_name_b64: str = roles[i + 1]
    role_name: str = b64decode(role_name_b64).decode('utf-8')
    parsed_roles.append((role_id, role_name))


invite_length = 16
invite_characters = 'abcdefghijklmnopqrstuvwxyz'
invite_characters += invite_characters.upper()
invite_characters += '01234567890'
invite_id_first = choice(invite_characters)
invite_id_last = choice(invite_characters)
special_characters = '-_.'
invite_id_second = choice(special_characters)
invite_characters += special_characters
invite_id = (invite_id_first + invite_id_second + ''.join(choices(invite_characters, k=invite_length - 3))) + invite_id_last
while invite_id in invites:
    invite_id = (invite_id_first + invite_id_second + ''.join(choices(invite_characters, k=invite_length - 3))) + invite_id_last
invites[invite_id] = {
    'server_name': server_name,
    'server_id': server_id,
    'description': description,
    'roles': 
    [
        {'id': role_id, 'name': role_name}
        for role_id, role_name in parsed_roles
    ]
}

with open(invites_path, 'w') as invites_fd:
    dump(invites, invites_fd, indent=4)

print(invite_id)
