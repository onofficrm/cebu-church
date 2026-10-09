"""Mirror /public_html into ./ftp-snapshot with lftp, skipping secrets and runtime data."""

import os
import subprocess
import sys
from pathlib import Path

REMOTE_ROOT = "/public_html"
LOCAL_ROOT = "ftp-snapshot"

EXCLUDES = [
    r"(^|/)(\.git|\.github|node_modules|_BUILDER_INPUT)(/|$)",
    r"(^|/)data/(cache|session|file|log|tmp)(/|$)",
    r"(^|/)(data/dbconfig\.php|data/dbconfig\.local\.php|data/icrm\.config\.php|config\.mail\.php)$",
    r"(^|/)\.env($|\.)",
    r"(^|/)cebu-church-deploy-v1$",
    r"dbconfig",
    r"\.(sql|log)$",
    r"_backup_transfer_",
]


def lftp_quote(value):
    escaped = value.replace("\\", "\\\\").replace('"', '\\"')
    return f'"{escaped}"'


def main():
    host = os.environ["FTP_SERVER"]
    user = os.environ["FTP_USERNAME"]
    password = os.environ["FTP_PASSWORD"]
    port = os.environ["FTP_PORT"].strip()
    if not port.isdigit():
        print("FTP_PORT must be numeric", file=sys.stderr)
        return 1
    Path(LOCAL_ROOT).mkdir(parents=True, exist_ok=True)

    commands = [
        "set cmd:fail-exit yes",
        "set ftp:ssl-allow no",
        "set ftp:passive-mode on",
        "set net:timeout 30",
        "set net:max-retries 2",
        "set xfer:clobber on",
        f"open -p {port} -u {lftp_quote(user)},{lftp_quote(password)} {lftp_quote(host)}",
    ]
    mirror = ["mirror", "--verbose", "--parallel=3"]
    for pattern in EXCLUDES:
        mirror.append("--exclude=" + lftp_quote(pattern))
    mirror.extend([REMOTE_ROOT, LOCAL_ROOT])
    commands.append(" ".join(mirror))
    commands.append("bye")

    script_path = Path("ftp-lftp.txt")
    script_path.write_text("\n".join(commands) + "\n", encoding="utf-8")
    try:
        completed = subprocess.run(
            ["lftp", "-f", str(script_path)],
            check=False,
        )
    finally:
        script_path.unlink(missing_ok=True)
    if completed.returncode != 0:
        print(f"lftp failed with exit code {completed.returncode}", file=sys.stderr)
        return completed.returncode

    files = [path for path in Path(LOCAL_ROOT).rglob("*") if path.is_file()]
    total = sum(path.stat().st_size for path in files)
    print(f"done files={len(files)} bytes={total}")
    return 0


if __name__ == "__main__":
    sys.exit(main())
