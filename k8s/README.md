# DigitalOcean Kubernetes Setup

## Create Our Resources

```shell script
doctl kubernetes cluster kubeconfig save k8s-nawhas

SHA=$(git rev-parse --short=8 HEAD)
BRANCH=$(git rev-parse --abbrev-ref HEAD)
IMAGE=nawhas/api

# Build the docker image and tag them
docker build --file ../api/Dockerfile \
  -t "$IMAGE:$SHA" \
  -t "$IMAGE:$BRANCH" \
  --build-arg GITHUB_SHA=$SHA \
  ../api

# Push images to docker hub
docker push "$IMAGE:$SHA" && docker push "$IMAGE:$BRANCH"

# Decrypt Secrets
cd secrets
GPG_KEY="GET_GPG_KEY_FROM_MAINTAINERS" ./decrypt.sh

# Apply kustomization to generate manifest.
cd ..
kustomize edit set image "IMAGE:TAG=$IMAGE:$SHA"

# Deploy everything to Kubernetes
# This will create the API service, API deployment, and secrets.
kustomize build . | kubectl apply -f -

# Set up Ingress
# https://www.digitalocean.com/community/tutorials/how-to-set-up-an-nginx-ingress-with-cert-manager-on-digitalocean-kubernetes
kubectl apply -f https://raw.githubusercontent.com/kubernetes/ingress-nginx/controller-v1.4.0/deploy/static/provider/cloud/deploy.yaml
# Verify the status of the deployed Ingress
kubectl wait --namespace ingress-nginx \
  --for=condition=ready pod \
  --selector=app.kubernetes.io/component=controller \
  --timeout=120s

kubectl apply -f ingress-controller.yml

kubectl apply -f ingress.yml

## Set up cert-manager for LetsEncrypt certs
kubectl create namespace cert-manager

kubectl apply -f https://github.com/cert-manager/cert-manager/releases/download/v1.10.0/cert-manager.yaml

## Create the staging cert issuer (staging here refers to LetsEncrypt's stating server
kubectl create -f certs/issuer.stg.yml

# Verify that the certificate was issued propertly
kubectl describe cert
# Should see this line
#   Normal  Issued  73s   cert-manager  Certificate issued successfully

kubectl create -f certs/issuer.yml

# Update ingress.yml: 
# -     cert-manager.io/cluster-issuer: "letsencrypt-staging"
# +     cert-manager.io/cluster-issuer: "letsencrypt-prod"

kubectl apply -f ingress.yml
```
