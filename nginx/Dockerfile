FROM nginx:stable-alpine

ARG FPM_CONTAINER
ENV FPM_CONTAINER $FPM_CONTAINER

COPY site.conf /etc/nginx/conf.d/default.conf.template

RUN echo "Using $FPM_CONTAINER as the FPM_CONTAINER variable."

RUN envsubst '\$FPM_CONTAINER' < /etc/nginx/conf.d/default.conf.template > /etc/nginx/conf.d/default.conf
