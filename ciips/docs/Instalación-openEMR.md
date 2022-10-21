### Instalación de openEMR en Ubuntu versión 22.04

# Actualizamos primeramente las dependencias del servidor
```bash
$ sudo apt update
$ sudo apt upgrade -y
$ sudo apt install git-core lsb-release ca-certificates apt-transport-https software-properties-common -y
```

# Instalación de MySQL 8.0
Podemos ejecutar el siguiente comando para saber que versiones estan dispobibles
```
$ apt-cache search mysql-server
```
Instalamos la versión actual (2022-09-21)
```
$ sudo apt install mysql-server-8.0 -y 
$ sudo systemctl start mysql.service
```

# Procedemos a configurar el MySQL
Ingresamos a la terminal de MySQL y le establecemos una contraseña al usuario root
```
$ sudo mysql
mysql> ALTER USER 'root'@'localhost' IDENTIFIED WITH mysql_native_password BY 'root';
mysql> exit;
```

Ahora intentamos ingresar con el usuario y contraseña que acabos de establecer
```
$ mysql -uroot -p
mysql> exit;
```

# Una vez tenemos el servidor de base de datos instalado procedemos a crear la base de datos y 
# el usuario de OpenEMR
```
$ mysql -uroot -p
mysql> CREATE DATABASE openemr CHARACTER SET utf8 COLLATE utf8_general_ci;
mysql> CREATE USER 'openemr_user'@'localhost' IDENTIFIED BY '0peN3Mr2022*';
mysql> GRANT ALL PRIVILEGES ON openemr.* TO 'openemr_user'@'localhost';
mysql> FLUSH PRIVILEGES;
mysql> exit;
```

# Instalación de PHP 8.1
```
$ sudo apt install -y php8.1-fpm \
php8.1-cli \
php8.1-common \
php8.1-curl \
php8.1-ldap \
php8.1-xml \
php8.1-xsl \
php8.1-gd \
php8.1-zip \
php8.1-soap \
php8.1-mbstring \
php8.1-mysql \
php8.1-xml \
php8.1-redis \
php8.1-intl \
php8.1-bcmath \
php8.1-dev \
php-pear
```

# Para desarrollo instalamos Xdebug
```
sudo pecl channel-update pecl.php.net
sudo pecl install xdebug
```

# Configuramos PHP
Abrir el archivo `/etc/php/8.1/fpm/php.ini`
```
$ sudo nano /etc/php/8.1/fpm/php.ini
max_input_vars = 3000
max_execution_time = 60
max_input_time = -1
post_max_size = 30M
memory_limit = 256M
mysqli.allow_local_infile = On
$ sudo systemctl reload php8.1-fpm.service 
```

# Instalamos Composer
```
$ curl -sS https://getcomposer.org/installer -o /tmp/composer-setup.php
$ HASH=`curl -sS https://composer.github.io/installer.sig`
$ echo $HASH
$ php -r "if (hash_file('SHA384', '/tmp/composer-setup.php') === '$HASH') { echo 'Installer verified'; } else { echo 'Installer corrupt'; unlink('composer-setup.php'); } echo PHP_EOL;"
$ sudo php /tmp/composer-setup.php --install-dir=/usr/local/bin --filename=composer
$ composer
```
# Generamos nuestro propio certificado autofirmado
openssl req -new -key ssl.key -out ssl.csr -passin pass:$password \
    -subj "/C=CR/ST=Heredia/L=San_Francisco/O=ciips/OU=openemr-telesalud/CN=openemr-telesalud.local/emailAddress=user@domain.net"


# Instalación de NGINX
```
$ sudo apt install -y nginx
$ systemctl status nginx
```
Deshabilitamos el virtual host default de nginx
```
$ sudo rm -rf /etc/nginx/sites-enabled/default 
```

Creamos y habilitamos el virtual host para openemr, por convención lo llamaremos openemr.local
```
$ sudo touch /etc/nginx/sites-available/openemr.local
$ sudo cat <<EOT >> /etc/nginx/sites-available/openemr.local
server {
    listen 80;
    server_name server_name openemr-telesalud.local www.openemr-telesalud.local; www.openemr-telesalud.local;
    return 301 https://openemr-telesalud.local$request_uri;
}

server {
    listen 	     443 ssl http2;
    listen  [::]:443 ssl http2;
    server_name openemr-telesalud.local www.openemr-telesalud.local;
    # root /usr/local/nginx/www;   
    root /lxcshared/openemr-telesalud;

    access_log  /var/log/*/openemr-telesalud.local_access_log main;
    error_log   /var/log/*/openemr-telesalud.local_error_log notice;

    # openemr specific SSL settings
    # to customize your configuration: https://mozilla.github.io/server-side-tls/ssl-config-generator/
    # configuration can be placed here, or in a separate file named openemr-ssl.conf
    include openemr-ssl.conf;  

    ssl_certificate         ssl/ssl.cert;
    ssl_certificate_key     ssl/ssl.key;

    # restrict/protect certain files
    include globals.conf;   

    # deny access to writable files/directories
    location ~* ^/sites/*/(documents|edi|era) { 
	    deny all;
        return 404; 
    }

    # deny access to certain directories
    location ~* ^/(contrib|tests) { 
		deny all;
        return 404;
    }
	
    # Uncomment one of the following two blocks, but not both:
    # protect special files from outside openemer login, and restrict them to superAdmins only
    #location ~* ^/(admin|setup|acl_setup|acl_upgrade|sl_convert|sql_upgrade|gacl/setup|ippf_upgrade|sql_patch)\.php {
	#	auth_basic 				"Restricted Access"; 
	#	auth_basic_user_file 	/path/to/.htpasswd;
	#	fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name; 
	#	fastcgi_pass php; 
	#	include fastcgi_params; 
    #}

    # If you pick this block, be sure to comment the previous one
    # Alternatively all access to these files can be denied
    #location ~* ^/(admin|setup|acl_setup|acl_upgrade|sl_convert|sql_upgrade|gacl/setup|ippf_upgrade|sql_patch)\.php { 
    #	deny all; 
    #	return 404; 
    #}

    location / {
	# try as file ($uri), as directory ($uri/) if not found, send to index file
	# no php is touched for static content
        try_files $uri $uri/ /index.php;
    }	
	
    # redirect server error pages to the static page /50x.html
    error_page   500 502 503 504  /50x.html;
    location = /50x.html { 
		root   /usr/local/www/nginx-dist; 
    } 

    # rewrite location statement for Zend modules at /interface/modules/zend_modules/public/Installer
    # it is meant to replace Apache's .htaccess file rewrite rule: RewriteRule ^.*$ - [NC,L], RewriteRule ^.*$ index.php [NC,L] 
    
    # pass the PHP scripts to the FastCGI server listening on unix socket, in this case php-fpm
    location ~* \.php$ {
	    include snippets/fastcgi_params;
        try_files $uri =404;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
    	fastcgi_pass unix:/run/php/php8.1-fpm.sock;
    }
    # dynamic stuff goes to php-proxy
    include php-proxy.conf;
}
EOT
```