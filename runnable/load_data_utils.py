from pathlib import Path

PARENT_PATH = Path(f'{__file__}/../../').resolve()

def load_config():
    config_path = Path(PARENT_PATH) / 'data' / 'config.php'

    config = {}
    with open(config_path, 'r') as config_fd:
        config_lines = config_fd.readlines()
        for line in config_lines:
            line = line.strip()
            if not line.startswith('"'):
                continue
            key, value = line.split('=>')
            key = key.strip().strip('"')
            value = value.strip().strip(',').strip('"')
            if value.endswith('/'):
                value = value[:-1]
            config[key] = value
    return config


def load_secrets():
    secrets_path = Path(PARENT_PATH) / 'data' / 'secrets.php'

    secrets = {}
    with open(secrets_path, 'r') as secrets_fd:
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
            secrets[key] = value
    return secrets
