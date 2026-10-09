"""Download /public_html into ./ftp-snapshot, skipping secrets and runtime data."""

import os
import sys
from ftplib import FTP, error_perm
from pathlib import Path

REMOTE_ROOT = "/public_html"
LOCAL_ROOT = Path("ftp-snapshot")

SKIP_EXACT = {
    "data/dbconfig.php",
    "data/dbconfig.local.php",
    "data/icrm.config.php",
    "config.mail.php",
    ".env",
    "cebu-church-deploy-v1",
}


def should_skip(rel):
    rel = rel.strip("/")
    name = rel.rsplit("/", 1)[-1]
    parts = rel.split("/")
    if rel in SKIP_EXACT or name in SKIP_EXACT:
        return True
    if name.startswith(".env") or name.startswith("._") or name == ".DS_Store":
        return True
    if name.endswith((".log", ".sql")) or "dbconfig" in name.lower():
        return True
    if parts[0] in {".git", ".github", "node_modules", "_BUILDER_INPUT"}:
        return True
    if "_backup_transfer_" in rel:
        return True
    if len(parts) >= 2 and parts[0] == "data" and parts[1] in {"cache", "session", "file", "log", "tmp"}:
        return True
    return False


def parse_list_line(line):
    if not line or line.endswith(" .") or line.endswith(" .."):
        return None
    upper = line.upper()
    if "<DIR>" in upper:
        name = line.split("<DIR>", 1)[-1].strip()
        if name in (".", ".."):
            return None
        return name, True
    parts = line.split(maxsplit=8)
    if len(parts) < 9:
        tokens = line.split()
        if len(tokens) >= 4 and tokens[-2].isdigit():
            name = tokens[-1]
            if name in (".", ".."):
                return None
            return name, False
        return None
    name = parts[8]
    if name in (".", "..") or " -> " in name:
        return None
    if line.startswith("l"):
        return None
    return name, line.startswith("d")


def connect():
    ftp = FTP()
    ftp.connect(os.environ["FTP_SERVER"], int(os.environ["FTP_PORT"]), timeout=60)
    ftp.login(os.environ["FTP_USERNAME"], os.environ["FTP_PASSWORD"])
    try:
        ftp.voidcmd("OPTS UTF8 ON")
    except error_perm:
        pass
    ftp.encoding = "utf-8"
    ftp.set_pasv(True)
    return ftp


def main():
    ftp = connect()
    try:
        ftp.cwd(REMOTE_ROOT)
    except error_perm as exc:
        print(f"cannot open {REMOTE_ROOT}: {exc}", file=sys.stderr)
        try:
            ftp.cwd("/")
            print("top-level names:", ftp.nlst())
        except error_perm as list_exc:
            print(f"cannot list /: {list_exc}", file=sys.stderr)
        return 1

    saved = 0
    skipped = 0
    total_bytes = 0

    def walk(remote_dir, rel):
        nonlocal saved, skipped, total_bytes
        ftp.cwd(remote_dir)
        lines = []
        ftp.retrlines("LIST", lines.append)
        for line in lines:
            parsed = parse_list_line(line)
            if not parsed:
                continue
            name, is_dir = parsed
            child_rel = f"{rel}/{name}" if rel else name
            if should_skip(child_rel):
                skipped += 1
                print(f"skip {child_rel}")
                continue
            if is_dir:
                walk(f"{remote_dir}/{name}", child_rel)
                ftp.cwd(remote_dir)
                continue
            dest = LOCAL_ROOT / child_rel
            dest.parent.mkdir(parents=True, exist_ok=True)
            with dest.open("wb") as handle:
                ftp.retrbinary(f"RETR {name}", handle.write)
            size = dest.stat().st_size
            total_bytes += size
            saved += 1
            print(f"get {child_rel} ({size})")

    walk(REMOTE_ROOT, "")
    print(f"done files={saved} skipped={skipped} bytes={total_bytes}")
    ftp.quit()
    return 0


if __name__ == "__main__":
    sys.exit(main())
