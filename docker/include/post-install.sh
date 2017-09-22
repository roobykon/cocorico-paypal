#!/bin/bash
set -e

FOLDER_PATH="/var/www/html"
CONFIG_PATH="${FOLDER_PATH}/app/config/parameters.yml"

function clear() {
  rm -rf \
    ${FOLDER_PATH}/.[^.]
    ${FOLDER_PATH}/.??*
    ${FOLDER_PATH}/*
}

function install () {
  if [[ -z $(ls -A ${FOLDER_PATH}) ]]; then
    git clone https://github.com/roobykon/cocorico-paypal.git /var/www/html
    cp /var/www/html/web/.htaccess.staging.dist /var/www/html/web/.htaccess
    chown -R www-data:www-data /var/www/html
    chmod -R 755 /var/www/html
  else
    echo "${FOLDER_PATH} not empty"
    echo "user ${0} clear"
    exit 1
  fi
}

function installdeps() {
  if [[ -f ${CONFIG_PATH} ]]; then
    composer install
  else
    echo "${CONFIG_PATH} not exists"
    exit 1
  fi
}

function config() {
  php app/console assetic:dump --env=staging
  php app/console doctrine:schema:create
  php app/console doctrine:fixtures:load
  php app/console cocorico:currency:update
  chown -R www-data:www-data /var/www/html
  chmod -R 755 /var/www/html
}

function help() {
  echo ""
  echo "usege: ${0} [OPT]"
  echo ""
  echo "OPT:"
  echo ""
  echo "clear       - delete all files from ${FOLDER_PATH}"
  echo "install     - git clone cocorico_pp repo to ${FOLDER_PATH}"
  echo "installdeps - compose install"
  echo "config      - php app/console assetic:dump --env=staging"
  echo "              php app/console doctrine:schema:create"
  echo "              php app/console doctrine:fixtures:load"
  echo "              php app/console doctrine:fixtures:load"
  echo "              fix ${FOLDER_PATH} permissions"
}

case ${1} in
  clear) clear;;
  install) install;;
  installdeps) installdeps;;
  config) config;;
  *) help;;
esac
