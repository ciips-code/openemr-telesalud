FROM nginx:alpine
RUN apk update \
&& apk upgrade \
&& apk add bash \
&& apk add nano
COPY nginx/default.conf /etc/nginx/conf.d
