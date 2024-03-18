@echo off
set HOSTNAME=%1
set OPENSSL_PATH=C:\Program Files\Git\mingw64\bin

if "%HOSTNAME%"=="" (
  echo Usage: %0 {domain}
  exit /b 1
)

call :add_to_hosts
call :add_certificates

exit /b 0

:add_to_hosts
@REM Adding to hosts file
echo Adding to hosts file
set IP=127.0.0.1
set HOSTS_LINE=%IP%    %HOSTNAME%
set ETC_HOSTS=C:\Windows\System32\drivers\etc\hosts

findstr /R /C:"^%IP%[ ]*%HOSTNAME%$" %ETC_HOSTS% >nul
if %errorlevel%==0 (
  echo %HOSTNAME% already exists in your hosts file
) else (
  echo Adding %HOSTNAME% to your %ETC_HOSTS%
  echo %HOSTS_LINE% >> %ETC_HOSTS%
)
exit /b 0

:add_certificates
@REM Adding Certificates
echo Adding Certificates
set CERTS_PATH=.\certs
set CERT_FILE_NAME=%CERTS_PATH%\%HOSTNAME%.crt

if exist %CERT_FILE_NAME% (
  echo %CERT_FILE_NAME% already exists
  exit /b 0
)

call :generate_openssl_config

type openssl.cnf

call :generate_certificate

del openssl.cnf

call :add_certificates_to_trusted_store

:: Add certificate to trusted store (This command needs to be run as admin)
:: Note: Windows does not have a direct equivalent to add-trusted-cert command. 
:: You might need to manually import the certificate or use certutil command.
:: certutil -addstore "Root" "%CERTS_PATH%\%HOSTNAME%.crt"

"%OPENSSL_PATH%\openssl" x509 -in "%CERTS_PATH%\%HOSTNAME%.crt" -out "%CERTS_PATH%\%HOSTNAME%.pem" -outform PEM
exit /b 0

:generate_openssl_config
(
echo [req]
echo distinguished_name = req_distinguished_name
echo x509_extensions = v3_req
echo prompt = no
echo [req_distinguished_name]
echo CN = %HOSTNAME%
echo [v3_req]
echo keyUsage = nonRepudiation, digitalSignature, keyEncipherment
echo extendedKeyUsage = serverAuth
echo subjectAltName = @alt_names
echo [alt_names]
echo DNS.1 = %HOSTNAME%
echo DNS.2 = *.%HOSTNAME%
echo DNS.3 = localhost
) > openssl.cnf
exit /b 0

:generate_certificate
echo Generating certificate
"%OPENSSL_PATH%\openssl" req ^
  -new ^
  -newkey rsa:2048 ^
  -sha1 ^
  -days 3650 ^
  -nodes ^
  -x509 ^
  -keyout "%CERTS_PATH%\%HOSTNAME%.key" ^
  -out "%CERTS_PATH%\%HOSTNAME%.crt" ^
  -config openssl.cnf
exit /b 0

:add_certificates_to_trusted_store
echo Adding certificates to trusted store
certutil -addstore "Root" "%CERTS_PATH%\%HOSTNAME%.crt"
exit /b 0

