#!/usr/bin/env bash
set -euo pipefail

ROOT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
cd "$ROOT_DIR"

info() {
  printf '\033[1;34m[INFO]\033[0m %s\n' "$1"
}

warn() {
  printf '\033[1;33m[WARN]\033[0m %s\n' "$1" >&2
}

error() {
  printf '\033[1;31m[ERREUR]\033[0m %s\n' "$1" >&2
  exit 1
}

command_exists() {
  command -v "$1" >/dev/null 2>&1
}

composer_cmd() {
  if command_exists composer; then
    printf 'composer'
    return 0
  fi

  if [[ -f "$ROOT_DIR/composer.phar" ]]; then
    if php "$ROOT_DIR/composer.phar" --version >/dev/null 2>&1; then
      printf 'php %s/composer.phar' "$ROOT_DIR"
      return 0
    fi

    warn "Le fichier composer.phar local est invalide, il sera ignoré."
  fi

  return 1
}

install_dependencies() {
  local composer_cmd_value

  if ! composer_cmd_value="$(composer_cmd)"; then
    warn "Composer n'est pas disponible ou est invalide. Je continue sans installer les dépendances."
    return 1
  fi

  info "Installation des dépendances PHP via Composer..."
  if [[ "$composer_cmd_value" == composer ]]; then
    composer install --no-interaction --no-progress --prefer-dist --no-dev
  else
    # shellcheck disable=SC2086
    $composer_cmd_value install --no-interaction --no-progress --prefer-dist --no-dev
  fi
}

info "Dossier du projet : $ROOT_DIR"

if ! command_exists php; then
  error "PHP n'est pas installé ou n'est pas disponible dans le PATH."
fi

PHP_VERSION="$(php -r 'echo PHP_VERSION;')"
PHP_MAJOR="${PHP_VERSION%%.*}"
PHP_REST="${PHP_VERSION#*.}"
PHP_MINOR="${PHP_REST%%.*}"
if [[ "$PHP_MAJOR" -lt 8 || ( "$PHP_MAJOR" -eq 8 && "$PHP_MINOR" -lt 2 ) ]]; then
  error "PHP 8.2 ou supérieur est requis. Version détectée : $PHP_VERSION"
fi

MISSING_EXTS=()
for ext in intl mbstring sqlite3; do
  if ! php -m | grep -qi "^${ext}$"; then
    MISSING_EXTS+=("$ext")
  fi
done

if (( ${#MISSING_EXTS[@]} > 0 )); then
  warn "Extensions PHP manquantes détectées: ${MISSING_EXTS[*]}"
  warn "Je continue quand même; si l'application échoue au démarrage, installe ces extensions puis relance le script."
fi

if [[ "${INSTALL_DEPS:-0}" == "1" ]]; then
  install_dependencies || warn "L'installation des dépendances a échoué, mais je continue quand même vers le lancement du serveur."
else
  warn "Installation des dépendances ignorée par défaut. Lance avec INSTALL_DEPS=1 pour les installer."
fi

DB_PATH="$ROOT_DIR/writable/database.db"
ENV_FILE="$ROOT_DIR/.env"
BASE_SQL="$ROOT_DIR/base.sql"
SERVER_HOST="${SERVER_HOST:-127.0.0.1}"
BROWSER_HOST="${BROWSER_HOST:-127.0.0.1}"
APP_BASE_URL="http://${BROWSER_HOST}:8080/"

if [[ ! -f "$BASE_SQL" ]]; then
  error "Fichier SQL introuvable : $BASE_SQL"
fi

mkdir -p "$ROOT_DIR/writable"

if [[ -f "$ENV_FILE" ]]; then
  if grep -q '^app\.baseURL=' "$ENV_FILE"; then
    sed -i "s|^app\.baseURL=.*|app.baseURL=$APP_BASE_URL|" "$ENV_FILE"
  else
    printf 'app.baseURL=%s\n' "$APP_BASE_URL" >> "$ENV_FILE"
  fi
  if grep -q '^database\.default\.database=' "$ENV_FILE"; then
    sed -i "s|^database\.default\.database=.*|database.default.database=$DB_PATH|" "$ENV_FILE"
  else
    printf '\ndatabase.default.database=%s\n' "$DB_PATH" >> "$ENV_FILE"
  fi
  if grep -q '^database\.default\.DBDriver=' "$ENV_FILE"; then
    sed -i "s|^database\.default\.DBDriver=.*|database.default.DBDriver=SQLite3|" "$ENV_FILE"
  else
    printf 'database.default.DBDriver=SQLite3\n' >> "$ENV_FILE"
  fi
  if grep -q '^CI_ENVIRONMENT=' "$ENV_FILE"; then
    sed -i 's|^CI_ENVIRONMENT=.*|CI_ENVIRONMENT=development|' "$ENV_FILE"
  else
    printf 'CI_ENVIRONMENT=development\n' >> "$ENV_FILE"
  fi
else
  cat > "$ENV_FILE" <<EOF
CI_ENVIRONMENT=development
app.baseURL=$APP_BASE_URL

database.default.database=$DB_PATH
database.default.DBDriver=SQLite3
EOF
fi

if [[ ! -f "$DB_PATH" ]]; then
  info "Création et initialisation de la base SQLite..."
  php -r '
    $dbPath = $argv[1];
    $sqlFile = $argv[2];
    $db = new SQLite3($dbPath);
    $db->enableExceptions(true);
    $sql = file_get_contents($sqlFile);
    if ($sql === false) {
      fwrite(STDERR, "Impossible de lire le fichier SQL\n");
      exit(1);
    }
    if (!$db->exec($sql)) {
      fwrite(STDERR, "Impossible d initialiser la base SQLite\n");
      exit(1);
    }
  ' "$DB_PATH" "$BASE_SQL"
else
  info "La base SQLite existe déjà : $DB_PATH"
fi

info "Lancement du serveur de développement CodeIgniter..."
info "Ouvre ensuite : http://${BROWSER_HOST}:8080"
info "Adresse d'écoute : http://${SERVER_HOST}:8080"
php spark serve --host "$SERVER_HOST" --port 8080
