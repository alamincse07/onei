FROM python:3.6
ENV PYTHONUNBUFFERED 1
RUN mkdir /code
WORKDIR /code
ADD requirements.txt /code/
RUN pip3 install -r requirements.txt
RUN apt-get update && apt-get install -y mailutils ssmtp jq
RUN apt-get install php7.0 -y
RUN apt-get install php7.0-curl
ADD . /code/
