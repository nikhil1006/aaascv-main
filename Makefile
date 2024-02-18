.PHONY: build github-ssh start stop

IMG_NAME=aaascv
VERSION=latest

build:
	docker build -t $(IMG_NAME):$(VERSION) -f Dockerfile .

stop:
	scripts/run.sh "stop" "$(IMG_NAME)" "$(VERSION)"

start: stop
	scripts/run.sh "start" "$(IMG_NAME)" "$(VERSION)"

mirror-dev: build
	scripts/run.sh "mirror-dev" "$(IMG_NAME)" "$(VERSION)"

mirror-prod: build
	scripts/run.sh "mirror-prod" "$(IMG_NAME)" "$(VERSION)"

github-ssh:
	eval "$(ssh-agent -s)"
	ssh-add -K ~/.ssh/aaascv
