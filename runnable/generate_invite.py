from sys import argv
from json import load, dump
from random import choice, choices
from pathlib import Path
from base64 import b64decode

from load_data_utils import load_config, unpack_args

CONFIG = load_config()
invites_path = Path(f'{CONFIG["project_root"]}/data/invites.json')
if not invites_path.exists():
    with open(invites_path, 'w') as invites_fd:
        invites_fd.write('{}')

invites = None
with open(invites_path, 'r') as invites_fd:
    invites = load(invites_fd)


if len(argv) != 2:
    print('{"message": "Failure"}')

args = unpack_args(argv[1])
server_id = args[0]
server_name = args[1]
description = args[2]
nick_prefix = args[3]
nick_suffix = args[4]
roles = args[5:]

if nick_prefix.strip() == '':
    nick_prefix = ''

if nick_suffix.strip() == '':
    nick_suffix = ''

parsed_roles = [(roles[i], roles[i + 1]) for i in range(0, len(roles), 2)]

invite_length = 8
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

print(invite_id)
