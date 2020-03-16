### Deploying Integration Environments

```shell script
BRANCH="dark-mode-integration"
BRANCH_PREFIX=$(echo "$BRANCH" | sed "s/-integration//")
NS=$(echo $BRANCH | md5 | cut -c 1-8)
# => 5261d622

kubectl create namespace "intg-$NS"
kustomize edit set namespace "intg-$NS"
```
