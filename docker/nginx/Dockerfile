FROM nginx:1.23.3-alpine

RUN apk add --no-cache --virtual .build-deps \
    gcc \
    libc-dev \
    make \
    openssl-dev \
    pcre-dev \
    zlib-dev \
    linux-headers

WORKDIR /usr/src
RUN wget http://nginx.org/download/nginx-1.23.3.tar.gz && \
    tar -xzf nginx-1.23.3.tar.gz

RUN cd nginx-1.23.3 && \
    ./configure \
        --with-stream \
        --with-stream_ssl_module \
        --with-http_ssl_module \
        --with-http_v2_module \
        --with-http_realip_module \
        --with-http_gunzip_module \
        --with-http_gzip_static_module && \
    make && \
    make install

RUN rm -rf /usr/src/* && \
    apk del .build-deps

EXPOSE 80 3001
CMD ["nginx", "-g", "daemon off;"]