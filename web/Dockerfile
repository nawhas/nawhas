# ---------------------------------------------------------------------------
# Build Stage
# ---------------------------------------------------------------------------
FROM szainmehdi/node:13 AS build

WORKDIR /opt

COPY . .

ARG API_DOMAIN
ARG APP_DOMAIN

ENV VUE_APP_API_DOMAIN $API_DOMAIN
ENV VUE_APP_APP_DOMAIN $APP_DOMAIN

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
