# ---------------------------------------------------------------------------
# Build Stage
# ---------------------------------------------------------------------------
FROM szainmehdi/node:13 as build

WORKDIR /opt

COPY . .

ARG API_DOMAIN
ARG SERVER_API_DOMAIN
ARG APP_DOMAIN
ARG SEARCH_HOST

ENV API_BASE_URL $API_DOMAIN
ENV SERVER_API_BASE_URL $SERVER_API_DOMAIN
ENV APP_DOMAIN $APP_DOMAIN
ENV SEARCH_HOST $SEARCH_HOST

RUN yarn install \
  --prefer-offline \
  --frozen-lockfile \
  --non-interactive \
  --production=false

RUN yarn build

ENV HOST 0.0.0.0
EXPOSE 3000

CMD [ "yarn", "start" ]
