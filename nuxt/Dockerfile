# ---------------------------------------------------------------------------
# Build Stage
# ---------------------------------------------------------------------------
FROM szainmehdi/node:13 as build

WORKDIR /opt

COPY . .

ARG ALGOLIA_APP_ID
ARG ALGOLIA_SEARCH_KEY
ARG ALGOLIA_INDEX_PREFIX
ARG API_DOMAIN

ENV VUE_APP_ALGOLIA_APP_ID $ALGOLIA_APP_ID
ENV VUE_APP_ALGOLIA_SEARCH_KEY $ALGOLIA_SEARCH_KEY
ENV VUE_APP_ALGOLIA_INDEX_PREFIX $ALGOLIA_INDEX_PREFIX
ENV VUE_APP_API_DOMAIN $API_DOMAIN

RUN yarn install \
  --prefer-offline \
  --frozen-lockfile \
  --non-interactive \
  --production=false

RUN yarn build

RUN rm -rf node_modules && \
  NODE_ENV=production yarn install \
  --prefer-offline \
  --pure-lockfile \
  --non-interactive \
  --production=true

# ---------------------------------------------------------------------------
# Production Image
# ---------------------------------------------------------------------------
FROM node:13-alpine

WORKDIR /opt

COPY --from=build /opt  .

ENV HOST 0.0.0.0
EXPOSE 3000

CMD [ "yarn", "start" ]
