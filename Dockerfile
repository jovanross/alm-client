FROM suchja/wine:latest

MAINTAINER Jovan Ross "jovan.ross@cscglobal.com"

USER root

RUN mkdir /home/xclient/alm-client -p

RUN apt-get -qq update && apt-get -qq -y --no-install-recommends install \
    apt-utils \
    build-essential

RUN apt-get update && apt-get install -y --allow-unauthenticated --force-yes \
    nano \
    curl \
    wget \
    netcat \
    nmap \
    unzip

RUN curl -sL https://deb.nodesource.com/setup_6.x | bash -

RUN apt-get update && apt-get install -y --allow-unauthenticated --force-yes \
    nodejs \
    dnsutils

RUN apt-get update && apt-get install -y --force-yes --allow-unauthenticated \
    cabextract

COPY install/dll/. /home/xclient/alm-client/

RUN chown -R xclient:xusers /home/xclient/alm-client/

RUN winetricks -q vcrun2015 vcrun6sp6 wsh57 msxml6 msvcirt mfc42

WORKDIR /home/xclient/alm-client/

RUN wine regsvr32 /s OTAClient.dll

# RUN wine npm install --g --production windows-build-tools

# RUN wine npm install -g node-gyp

# docker-dev-local.cscinfo.com/alm-client:latest
# docker build . -t docker-dev-local.cscinfo.com/alm-client:latest
# docker run --rm -it --entrypoint /bin/bash docker-dev-local.cscinfo.com/alm-client:latest