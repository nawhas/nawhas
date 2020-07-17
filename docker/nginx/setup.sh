HOSTNAME=$1

if [ -z "$HOSTNAME" ]
then
  echo "Usage: $0 {domain}"
  exit 1;
fi

IP="127.0.0.1"
HOSTS_LINE="$IP\t$HOSTNAME"
ETC_HOSTS=/etc/hosts

if [ -n "$(grep "$HOSTNAME" /etc/hosts)" ]
  then
    echo "$HOSTNAME already exists in your hosts file: $(grep $HOSTNAME $ETC_HOSTS)"
  else
    echo "Adding $HOSTNAME to your $ETC_HOSTS";
    sudo -- sh -c -e "echo '$HOSTS_LINE' >> /etc/hosts";

    if [ -n "$(grep $HOSTNAME /etc/hosts)" ]
      then
        printf "%s was added successfully \n%s" "$HOSTNAME" "$(grep $HOSTNAME /etc/hosts)";
      else
        echo "Failed to add $HOSTNAME. Try adding it manually.";
    fi
fi

# https://example.test Cert
CERTS_PATH="./certs";
CERT_FILE_NAME="$CERTS_PATH/example.test.crt";

if [ -f $CERT_FILE_NAME ]
then
  exit 0;
fi

cat > openssl.cnf <<EOF
  [req]
  distinguished_name = req_distinguished_name
  x509_extensions = v3_req
  prompt = no
  [req_distinguished_name]
  CN = $HOSTNAME
  [v3_req]
  keyUsage = nonRepudiation, digitalSignature, keyEncipherment
  extendedKeyUsage = serverAuth
  subjectAltName = @alt_names
  [alt_names]
  DNS.1 = $HOSTNAME
  DNS.2 = *.$HOSTNAME
  DNS.3 = localhost
EOF

cat openssl.cnf

openssl req \
  -new \
  -newkey rsa:2048 \
  -sha1 \
  -days 3650 \
  -nodes \
  -x509 \
  -keyout "$CERTS_PATH/$HOSTNAME.key" \
  -out "$CERTS_PATH/$HOSTNAME.crt" \
  -config openssl.cnf

rm openssl.cnf

sudo security add-trusted-cert -d -r trustRoot -k /Library/Keychains/System.keychain "$CERTS_PATH/$HOSTNAME.crt"

openssl x509 -in "$CERTS_PATH/$HOSTNAME.crt" -out "$CERTS_PATH/$HOSTNAME.pem" -outform PEM
