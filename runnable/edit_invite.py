from sys import argv
from json import load, dump
from pathlib import Path
from base64 import b64decode

from load_data_utils import load_config

CONFIG = load_config()
invites_path = Path(f'{CONFIG["project_root"]}/data/invites.json')
if not invites_path.exists():
    with open(invites_path, 'w') as invites_fd:
        invites_fd.write('{}')

invite_id = argv[1]
server_id: str = argv[2]
server_name_b64: str = argv[3]
description_b64: str = argv[4]
roles = argv[5:]

server_name = b64decode(server_name_b64).decode('utf-8')
description = b64decode(description_b64).decode('utf-8')

parsed_roles = []
for i in range(0, len(roles), 2):
    role_id: str = roles[i]
    role_name_b64: str = roles[i + 1]
    role_name: str = b64decode(role_name_b64).decode('utf-8')
    parsed_roles.append((role_id, role_name))

invites = None
with open(invites_path, 'r') as invites_fd:
    invites = load(invites_fd)

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
    invites = dump(invites, invites_fd, indent=4)
