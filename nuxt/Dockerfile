# ---------------------------------------------------------------------------
# Build Stage
# ---------------------------------------------------------------------------
FROM szainmehdi/node:13 as build

WORKDIR /opt

RUN apk --no-cache add shadow \
    gcc \
    musl-dev \
    autoconf \
    automake \
    make \
    libtool \
    nasm \
    tiff \
    jpeg \
    zlib \
    zlib-dev \
    file \
    pkgconf

COPY . .
COPY .env.docker .env

ARG APP_VERSION
ARG SENTRY_PUBLISH_RELEASE

ENV APP_VERSION $APP_VERSION
ENV SENTRY_PUBLISH_RELEASE $SENTRY_PUBLISH_RELEASE

RUN yarn install \
  --prefer-offline \
  --frozen-lockfile \
  --non-interactive \
  --production=false

RUN yarn build

ENV HOST 0.0.0.0
EXPOSE 3000

CMD [ "yarn", "start" ]
