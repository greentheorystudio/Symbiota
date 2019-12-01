FROM solr:8.3

USER root
WORKDIR /opt/solr/contrib/dataimporthandler/
RUN mkdir lib
WORKDIR /opt/solr/contrib/dataimporthandler/lib/
RUN wget https://dev.mysql.com/get/Downloads/Connector-J/mysql-connector-java-5.1.48.zip
RUN unzip mysql-connector-java-5.1.48.zip
RUN cp mysql-connector-java-5.1.48/mysql-connector-java-5.1.48-bin.jar mysql-connector-java-5.1.48-bin.jar
RUN cp mysql-connector-java-5.1.48/mysql-connector-java-5.1.48.jar mysql-connector-java-5.1.48.jar
RUN rm -rf mysql-connector-java-5.1.48
RUN rm mysql-connector-java-5.1.48.zip
WORKDIR /opt/solr/server/solr-webapp/webapp/WEB-INF/lib/
RUN wget https://github.com/locationtech/jts/releases/download/jts-1.16.1/jts-core-1.16.1.jar

USER solr
WORKDIR /opt/solr/
CMD ["solr-create", "-c", "symbiota"]

ENTRYPOINT ["docker-entrypoint.sh"]
