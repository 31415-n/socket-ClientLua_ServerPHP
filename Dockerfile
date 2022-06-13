FROM ubuntu:20.04

RUN  sed -i 's|docker.com/linux/debian|docker.com/linux/ubuntu|g' /etc/apt/sources.list

RUN apt-get update && apt-get upgrade -y

ENV MYSQL_PWD root
ENV DEBIAN_FRONTEND=noninteractive 

RUN apt-get install -y zip unzip sudo net-tools lua5.3 screen php php-xdebug php-cli cron php-curl php-mysql php-mysqli fontconfig iputils-ping openssl nano tree curl libssl-dev libcurl4-openssl-dev

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

ENV LOG_STDOUT **Boolean**
ENV LOG_STDERR **Boolean**
ENV LOG_LEVEL warn
ENV ALLOW_OVERRIDE All
ENV DATE_TIMEZONE UTC
ENV TERM dumb

RUN mkdir  /www
RUN chmod 777 /www
WORKDIR /www

