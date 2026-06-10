import subprocess
import sys

def run_grunt_amd():
    moodle_root = "/Users/hectorteran/Dev/moodle-dev/public"
    print(f"Ejecutando 'npx -p node@22.11.0 npx grunt amd --force' en {moodle_root}...")
    try:
        result = subprocess.run(
            ["npx", "-p", "node@22.11.0", "npx", "grunt", "amd", "--force"],
            cwd=moodle_root,
            check=True,
            text=True,
            stdout=subprocess.PIPE,
            stderr=subprocess.PIPE
        )
        print("Compilación AMD completada con éxito (ignoring lint warnings/errors).")
        print(result.stdout)
    except subprocess.CalledProcessError as e:
        print("Error en compilación AMD:")
        print(e.stderr)
        print(e.stdout)
        sys.exit(1)

if __name__ == "__main__":
    run_grunt_amd()
