from sys import argv
from json import load, dump
from pathlib import Path
from base64 import b64decode

from load_data_utils import load_config, unpack_args

CONFIG = load_config()
invites_path = Path(f'{CONFIG["project_root"]}/data/invites.json')
if not invites_path.exists():
    with open(invites_path, 'w') as invites_fd:
        invites_fd.write('{}')

if len(argv) != 2:
    print('{"message": "Failure"}')
    exit()

args = unpack_args(argv[1])

invite_id = args[0]
server_id: str = args[1]
server_name: str = args[2]
description: str = args[3]
nick_prefix: str = args[4]
nick_suffix: str = args[5]
roles = args[6:]

if nick_prefix.strip() == '':
    nick_prefix = ''

if nick_suffix.strip() == '':
    nick_suffix = ''

parsed_roles = [(roles[i], roles[i + 1]) for i in range(0, len(roles), 2)]

invites = None
with open(invites_path, 'r') as invites_fd:
    invites = load(invites_fd)

invites[invite_id] = {
    'server_name': server_name,
    'server_id': server_id,
    'description': description,
    'nick_prefix': nick_prefix,
    'nick_suffix': nick_suffix,
    'roles': 
    [
        {'id': role_id, 'name': role_name}
        for role_id, role_name in parsed_roles
    ]
}

with open(invites_path, 'w') as invites_fd:
    dump(invites, invites_fd, indent=4)
