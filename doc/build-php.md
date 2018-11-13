## For OSX

```bash
# install openssl-1.1
$ brew install openssl@1.1

# help tool chain to locate openssl-1.1
$ export PKG_CONFIG_PATH="/usr/local/opt/openssl@1.1/lib/pkgconfig" && phpize

# config
$ ./configure --disable-all \
  --enable-phar \
  --enable-session \
  --enable-short-tags \
  --enable-tokenizer \
  --with-pcre-regex \
  --with-openssl=/usr/local/opt/openssl@1.1 \
  --with-bz2=/usr/local/opt/bzip2 \
  --with-zlib=/usr/local/opt/zlib \
  --with-gmp=/usr/local \
  --enable-dom \
  --enable-libxml \
  --enable-simplexml \
  --enable-xml \
  --enable-xmlreader \
  --enable-xmlwriter \
  --with-xsl \
  --with-libxml-dir=/usr/local/opt/libxml2 \
  --enable-opcache \
  --enable-bcmath \
  --enable-calendar \
  --enable-cli \
  --enable-ctype \
  --enable-dom \
  --enable-fileinfo \
  --enable-filter \
  --enable-shmop \
  --enable-sysvsem \
  --enable-sysvshm \
  --enable-sysvmsg \
  --enable-json \
  --enable-mbregex \
  --enable-mbstring \
  --with-mhash=/usr/local \
  --enable-pcntl \
  --with-pcre-regex \
  --with-pcre-dir=/usr/local \
  --enable-pdo \
  --with-pear \
  --enable-posix \
  --with-readline=/usr/local/opt/readline \
  --enable-sockets \
  --enable-tokenizer \
  --with-curl=/usr \
  --enable-zip

$ make
$ make install
```
