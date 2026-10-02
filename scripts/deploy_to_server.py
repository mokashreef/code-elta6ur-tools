import os
import tarfile
import paramiko
import time

HOST = os.environ.get('DEPLOY_HOST') or input("Enter SSH Host/IP: ")
PORT = int(os.environ.get('DEPLOY_PORT', '22'))
USER = os.environ.get('DEPLOY_USER') or input("Enter SSH Username: ")
PASS = os.environ.get('DEPLOY_PASS', '')

if not PASS:
    try:
        import getpass
        PASS = getpass.getpass(f"Enter SSH Password for {USER}@{HOST}:{PORT}: ")
    except Exception:
        pass
REMOTE_DOMAIN_DIR = os.environ.get('DEPLOY_DIR', f'/home/{USER}')
REMOTE_WEB_DIR = os.environ.get('DEPLOY_WEB_DIR', f'{REMOTE_DOMAIN_DIR}/public_html')
PACKAGE_NAME = 'deploy_package.tar.gz'

LOCAL_DIR = os.path.abspath(os.path.join(os.path.dirname(__file__), '..'))
LOCAL_ARCHIVE = os.path.join(LOCAL_DIR, PACKAGE_NAME)

print("=" * 60)
print("STARTING DEPLOYMENT TO CODE ELTA6UR SERVER")
print("=" * 60)

# 1. Create clean tar.gz package
print(f"Packaging files from: {LOCAL_DIR}")
include_dirs = ['admin', 'api', 'assets', 'auth', 'config', 'database', 'includes', 'tools', 'user']
include_files = ['.htaccess', 'index.php', 'ecosystem.php', 'tool.php']

file_count = 0
with tarfile.open(LOCAL_ARCHIVE, "w:gz") as tar:
    for d in include_dirs:
        dir_path = os.path.join(LOCAL_DIR, d)
        if os.path.exists(dir_path):
            tar.add(dir_path, arcname=d)
            for _, _, files in os.walk(dir_path):
                file_count += len(files)
            print(f"  + Added directory: {d}")

    for f in include_files:
        file_path = os.path.join(LOCAL_DIR, f)
        if os.path.exists(file_path):
            tar.add(file_path, arcname=f)
            file_count += 1
            print(f"  + Added file: {f}")

archive_size_mb = os.path.getsize(LOCAL_ARCHIVE) / (1024 * 1024)
print(f"Package created: {PACKAGE_NAME} ({archive_size_mb:.2f} MB, ~{file_count} files)")

# 2. Connect via SSH
print(f"\nConnecting to {HOST}:{PORT} as {USER}...")
ssh = paramiko.SSHClient()
ssh.set_missing_host_key_policy(paramiko.AutoAddPolicy())
ssh.connect(HOST, port=PORT, username=USER, password=PASS, timeout=30)
print("SSH connection established successfully.")

# 3. SFTP Upload
print(f"\nUploading {PACKAGE_NAME} to remote server...")
sftp = ssh.open_sftp()
remote_package = f"{REMOTE_DOMAIN_DIR}/{PACKAGE_NAME}"

last_reported = 0
def progress_callback(transferred, total):
    global last_reported
    percent = (transferred / total) * 100
    if percent - last_reported >= 20 or transferred == total:
        print(f"  Progress: {percent:.1f}% ({transferred/(1024*1024):.2f} / {total/(1024*1024):.2f} MB)")
        last_reported = percent

sftp.put(LOCAL_ARCHIVE, remote_package, callback=progress_callback)
sftp.close()
print("Upload completed successfully.")

# 4. Extract archive on remote server
print(f"\nExtracting package into {REMOTE_WEB_DIR}...")
extract_cmd = f"tar -xzf {remote_package} -C {REMOTE_WEB_DIR} && rm -f {remote_package}"
stdin, stdout, stderr = ssh.exec_command(extract_cmd)
exit_status = stdout.channel.recv_exit_status()
err = stderr.read().decode('utf-8')

if exit_status != 0:
    print(f"Extraction ERROR (Code {exit_status}): {err}")
    ssh.close()
    exit(1)
print("Extraction completed successfully.")

# 5. Fix permissions
print("\nSetting directory and file permissions...")
perm_cmd = f"find {REMOTE_WEB_DIR} -type d -exec chmod 755 {{}} + && find {REMOTE_WEB_DIR} -type f -exec chmod 644 {{}} +"
stdin, stdout, stderr = ssh.exec_command(perm_cmd)
stdout.channel.recv_exit_status()
print("Permissions normalized (755 dirs / 644 files).")

# 6. Verify remote state
print("\nVerifying remote deployed files...")
check_cmd = f"""
echo "=== PUBLIC_HTML LISTING ==="
ls -la {REMOTE_WEB_DIR} | head -n 25
echo "=== TOOLS COUNT ==="
ls -1 {REMOTE_WEB_DIR}/tools | wc -l
echo "=== ECOSYSTEM TEST ==="
test -f {REMOTE_WEB_DIR}/ecosystem.php && echo "ecosystem.php EXISTS" || echo "ecosystem.php MISSING"
test -f {REMOTE_WEB_DIR}/includes/ecosystem_data.php && echo "ecosystem_data.php EXISTS" || echo "ecosystem_data.php MISSING"
"""
stdin, stdout, stderr = ssh.exec_command(check_cmd)
print(stdout.read().decode('utf-8'))

# 7. Test PHP syntax on remote
print("Running remote PHP syntax check on index.php and ecosystem.php...")
test_php = f"php -l {REMOTE_WEB_DIR}/index.php && php -l {REMOTE_WEB_DIR}/ecosystem.php && php -l {REMOTE_WEB_DIR}/includes/tools_registry.php"
stdin, stdout, stderr = ssh.exec_command(test_php)
print(stdout.read().decode('utf-8'))

# 8. Clean local archive
if os.path.exists(LOCAL_ARCHIVE):
    os.remove(LOCAL_ARCHIVE)
    print(f"Cleaned up local package {PACKAGE_NAME}.")

ssh.close()
print("=" * 60)
print("DEPLOYMENT SUCCESSFULLY COMPLETED!")
print("=" * 60)
