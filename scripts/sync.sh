#!/bin/bash -e

# The path on the remote host. THIS MUST EXIST!
RCD="${1:-/public_html}"
FTP_PASS="${FTP_PASS:-replaceme}"

FTP_HOST="sv13.byethost13.org"
FTP_USER="autismas"
DELETE="--delete"
# This path is mount on the Docker image
LCD="/usr/share/nginx/html"

fatal_error() {
    local _msg=$1

    echo "$_msg"
    exit 255
}

mirror_ftp() {
    local _ftp_user=$1 _ftp_pass=$2 _ftp_host=$3
    local _ftp_url=""
    
    # set -x
    _ftp_url="ftp://${_ftp_user}:${_ftp_pass}@${_ftp_host}"
    # https://lftp.yar.ru/lftp-man.html
    lftp "${_ftp_url}" -e "set ftp:ssl-allow no; lcd $LCD; cd $RCD; mirror --verbose --reverse ${DELETE} --exclude=.DS_Store $LCD $RCD; quit"
}

if [[ "${FTP_PASS}" = "replaceme" ]]; then
    fatal_error "Set FTP_PASS in your environment i.e. export FTP_PASS=<password>"
fi

mirror_ftp "${FTP_USER}" "${FTP_PASS}" "${FTP_HOST}"
