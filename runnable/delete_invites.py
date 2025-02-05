from sys import argv
from json import load, dump
from pathlib import Path
from load_data_utils import load_config

CONFIG = load_config()
invites_path = Path(f'{CONFIG["project_root"]}/data/invites.json')
if not invites_path.exists():
    with open(invites_path, 'w') as invites_fd:
        invites_fd.write('{}')

invite_ids = argv[1:]
invites = None
with open(invites_path, 'r') as invites_fd:
    invites = load(invites_fd)

for invite_id in invite_ids:
    if invite_id in invites:
        del invites[invite_id]

with open(invites_path, 'w') as invites_fd:
    invites = dump(invites, invites_fd, indent=4)
