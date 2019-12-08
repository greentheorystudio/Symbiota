FROM jekyll/jekyll:latest

WORKDIR "/srv/jekyll"

COPY docker-entrypoint.sh /usr/bin/
RUN chmod +x /usr/bin/docker-entrypoint.sh

ENTRYPOINT [ "docker-entrypoint.sh" ]
