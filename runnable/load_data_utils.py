from pathlib import Path
from base64 import b64decode

PARENT_PATH = Path(f'{__file__}/../../').resolve()


def load_data(php_data_path: str) -> dict:
    data = {}
    with open(php_data_path, 'r') as secrets_fd:
        secrets_lines = secrets_fd.readlines()
        for line in secrets_lines:
            line = line.strip()
            if not line.startswith('"'):
                continue
            key, value = line.split('=>')
            key = key.strip().strip('"')
            value = value.strip().strip(',').strip('"')
            if value.endswith('/'):
                value = value[:-1]
            data[key] = value
    return data


def load_config():
    config_path = Path(PARENT_PATH) / 'data' / 'config.php'
    return load_data(config_path)


def load_secrets():
    secrets_path = Path(PARENT_PATH) / 'data' / 'secrets.php'
    return load_data(secrets_path)


def unpack_args(args_b64):
    '''
    Unpacks base64 encoded arguments
    Each file expects a single base64 encoded string which is
    then multiple base64 encoded strings separated by spaces
    which are then decoded and returned as a list.
    '''
    args_str = b64decode(args_b64).decode('utf-8')
    decoded_args = [b64decode(arg).decode('utf-8') for arg in args_str.split(' ')]
    return decoded_args
