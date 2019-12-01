FROM solr:8.3

USER root
WORKDIR /opt/solr/server/solr-webapp/webapp/WEB-INF/lib/
RUN wget https://github.com/locationtech/jts/releases/download/jts-1.16.1/jts-core-1.16.1.jar

USER solr
WORKDIR /opt/solr/
CMD ["solr-create", "-c", "symbiota"]

USER root
COPY solrconfig.xml /var/solr/data/symbiota/conf/solrconfig.xml
COPY data-config.xml /var/solr/data/symbiota/conf/data-config.xml
COPY schema.xml /var/solr/data/symbiota/conf/schema.xml
RUN chown -R solr:solr /var/solr/data/symbiota

USER solr
ENTRYPOINT ["docker-entrypoint.sh"]
