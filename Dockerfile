FROM nginx:1.17.1

COPY ./nginx/nginx.conf /etc/nginx/conf.d/default.conf
COPY ./nginx/mime.types /etc/nginx/mime.types