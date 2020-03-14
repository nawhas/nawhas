if [ -f "./api.env" ]; then
  echo "k8s/secrets/api.env file does not exist yet.";
  exit 1;
fi;

kubectl create secret generic api.env --from-env-file k8s/secrets/api.env --dry-run -o yaml | kubectl apply -f -
kubectl create secret generic cloudsql --from-file k8s/secrets/cloudsql --dry-run -o yaml | kubectl apply -f -
