# ---------------------------------------------------------------------------
# Build Stage
# ---------------------------------------------------------------------------
FROM szainmehdi/node:13 AS build

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

RUN yarn install --frozen-lockfile \
        && yarn lint --mode=production --no-fix \
        && yarn build

# ---------------------------------------------------------------------------
# Production Image
# ---------------------------------------------------------------------------
FROM nginx:stable-alpine

WORKDIR /var/www/web/public

COPY --from=build /opt/dist .
COPY docker/nginx.conf /etc/nginx/conf.d/default.conf
