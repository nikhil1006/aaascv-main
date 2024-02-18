FROM nginx

RUN apt -y update \
    && apt -y install lftp \
    && apt -y upgrade

RUN mkdir -p /scripts
COPY ./scripts/sync.sh /scripts/sync.sh

