FROM phpdockerio/php74-fpm:latest
WORKDIR "/application"

# Install selected extensions and other stuff
RUN apt-get update \
    && apt-get -y --no-install-recommends install php7.4-mysql php7.4-bz2 php7.4-gd php7.4-mbstring php7.4-odbc php7.4-soap php7.4-bcmath php7.4-xml php7.4-zip \
    && apt-get clean; rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/* /usr/share/doc/*

RUN curl --silent --show-error  https://deb.nodesource.com/setup_10.x | bash - \
    && apt-get update \
    && apt-get install -y nodejs
    
RUN apt-get update && \
    apt-get install -y --no-install-recommends composer zip
    RUN curl --silent --show-error https://getcomposer.org/installer | php
