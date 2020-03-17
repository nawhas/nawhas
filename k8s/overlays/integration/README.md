### Deploying Integration Environments

```shell script
BRANCH="dark-mode-integration"
BRANCH_PREFIX=$(echo "$BRANCH" | sed "s/-integration//")
NS=$(echo $BRANCH | md5 | cut -c 1-8)
# => 5261d622
API_IMAGE="nawhas/api"
WEB_IMAGE="nawhas/web"
GITHUB_SHA=$(git rev-parse --short=8 HEAD)
GITHUB_REF=$BRANCH
BRANCH=$(git rev-parse --abbrev-ref HEAD)
NS_EXISTS=$(kubectl get namespace | grep "intg-$NS")

[ ! -z "$NS_EXISTS" ] || kubectl create namespace "intg-$NS"
kustomize edit set namespace "intg-$NS"

docker build --file ../../../api/Dockerfile \
    -t "$API_IMAGE:intg-$NS-$GITHUB_SHA" \
    --build-arg GITHUB_SHA="$GITHUB_SHA" \
    --build-arg GITHUB_REF="$GITHUB_REF" \
    ../../../api
docker push "$API_IMAGE:intg-$NS-$GITHUB_SHA"
docker build --file ../../../web/Dockerfile \
    -t "$WEB_IMAGE:intg-$NS-$GITHUB_SHA" \
    --build-arg GITHUB_SHA="$GITHUB_SHA" \
    --build-arg GITHUB_REF="$GITHUB_REF" \
    --build-arg ALGOLIA_APP_ID=DX4PV1656L \
    --build-arg ALGOLIA_SEARCH_KEY=6f81668dbb1a08ace47cdaf2c3506483 \
    --build-arg API_DOMAIN="https://api.$BRANCH_PREFIX.intg.k8s.nawhas.com" \
    ../../../web
docker push "$WEB_IMAGE:intg-$NS-$GITHUB_SHA"

kustomize edit set image "IMAGE:TAG=$API_IMAGE:intg-$NS-$GITHUB_SHA"
kustomize edit set image "WEB:TAG=$WEB_IMAGE:intg-$NS-$GITHUB_SHA"
kustomize build . | kubectl apply -f -
```
