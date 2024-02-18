#!/bin/bash

OP=${1:-stop}
IMG_NAME=${2:-aaascv}
VERSION=${3:-latest}

cleanup() {
	local _img_name="$1" _container_id=""

	_container_id=$(get_container_id "$_img_name")

	if [[ -n "$_container_id" ]]; then
		docker container stop "${_container_id}"
	fi
	docker rm /${_img_name} >/dev/null || exit 0
}

get_container_id() {
	local _img_name="$1" _container_id=""
	_container_id=$(docker container ls -q --filter name="${_img_name}"*)

	echo "$_container_id"
}

current_path="$(pwd)"
case "${OP}" in
	start)
		docker run --name "${IMG_NAME}" \
			-p 8080:80 \
			-v "${current_path}"/site:/usr/share/nginx/html:ro \
			-d "${IMG_NAME}:${VERSION}"
		;;
	stop)
		cleanup "${IMG_NAME}"
		;;
	mirror-dev)
		docker run -it \
			-e FTP_PASS \
			-v "${current_path}"/site:/usr/share/nginx/html:ro \
			"${IMG_NAME}:${VERSION}" \
			/bin/bash /scripts/sync.sh "/dev.autismashramawaaz.org"
		;;
	mirror-prod)
		docker run -it \
			-e FTP_PASS \
			-v "${current_path}"/site:/usr/share/nginx/html:ro \
			"${IMG_NAME}:${VERSION}" \
			/bin/bash /scripts/sync.sh "/public_html"
		;;
	*)
		echo "$0 <start|stop> ${IMG_NAME}"
		;;
esac
