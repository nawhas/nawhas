#!/usr/bin/env bash
function prepare_environment() {
  API_IMAGE="nawhas/api"
  WEB_IMAGE="nawhas/web"

  pr_num=$(jq --raw-output .pull_request.number "$GITHUB_EVENT_PATH")
  namespace="intg-${pr_num}"
  sha="$GITHUB_SHA"

  api_domain="api.pr-${pr_num}.intg.k8s.nawhas.com"
  app_domain="pr-${pr_num}.intg.k8s.nawhas.com"
  search_host="search.pr-${pr_num}.intg.k8s.nawhas.com"
  mail_host="mail.pr-${pr_num}.intg.k8s.nawhas.com"
  algolia_prefix="intg_${pr_num}_"
  session_cookie_prefix="intg_${pr_num}_"

  app_version="${namespace}-${sha::8}"
  api_image_tag="${API_IMAGE}:${app_version}"
  web_image_tag="${WEB_IMAGE}:${app_version}"

  {
    echo "API_IMAGE=$API_IMAGE"
    echo "WEB_IMAGE=$API_IMAGE"
    echo "NAMESPACE=$namespace"
    echo "PR_NUM=$pr_num"
    echo "APP_DOMAIN=$app_domain"
    echo "API_DOMAIN=$api_domain"
    echo "API_IMAGE_TAG=$api_image_tag"
    echo "WEB_IMAGE_TAG=$web_image_tag"
    echo "APP_VERSION=$app_version"
    echo "SEARCH_HOST=$search_host"
    echo "MAIL_HOST=$mail_host"
    echo "ALGOLIA_PREFIX=$algolia_prefix"
    echo "SESSION_COOKIE_PREFIX=$session_cookie_prefix"
  } >>"$GITHUB_ENV"
}

prepare_environment
