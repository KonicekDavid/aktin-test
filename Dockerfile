FROM ubuntu:latest
LABEL authors="konas"

ENTRYPOINT ["top", "-b"]