# ---------------------------------------------------------------------------
# Build Stage
# ---------------------------------------------------------------------------
FROM szainmehdi/node:13

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
ENV NODE_ENV production

RUN yarn install --frozen-lockfile \
        && yarn lint --no-fix \
        && yarn build

ENV HOST 0.0.0.0
EXPOSE 3000

# Start the server
CMD [ "yarn", "start" ]
