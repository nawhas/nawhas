FROM szainmehdi/node:13

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

COPY package.json package.json
COPY yarn.lock yarn.lock

RUN yarn install \
  --prefer-offline \
  --frozen-lockfile \
  --non-interactive \
  --production=false


ARG APP_VERSION
ARG APP_ENV
# ARG SENTRY_PUBLISH_RELEASE
# ARG SENTRY_AUTH_TOKEN

ENV BUILD_APP_VERSION $APP_VERSION
ENV BUILD_APP_ENV $APP_ENV
# ENV BUILD_SENTRY_PUBLISH_RELEASE $SENTRY_PUBLISH_RELEASE
# ENV BUILD_SENTRY_ORG nawhas
# ENV BUILD_SENTRY_PROJECT web

COPY . .

# RUN printf "[defaults]\norg = $SENTRY_ORG\nproject = $SENTRY_PROJECT\n[auth]\ntoken = $SENTRY_AUTH_TOKEN" > .sentryclirc

RUN yarn build

ENV HOST 0.0.0.0
EXPOSE 3000

# RUN rm .sentryclirc

CMD [ "yarn", "start" ]
