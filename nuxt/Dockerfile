# ---------------------------------------------------------------------------
# Build Stage
# ---------------------------------------------------------------------------
FROM szainmehdi/node:13 as build

WORKDIR /opt

COPY . .
COPY .env.docker .env

ARG APP_VERSION
ENV APP_VERSION $APP_VERSION

RUN yarn install \
  --prefer-offline \
  --frozen-lockfile \
  --non-interactive \
  --production=false

RUN yarn build

ENV HOST 0.0.0.0
EXPOSE 3000

CMD [ "yarn", "start" ]
