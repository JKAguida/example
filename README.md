# Init

***Este comando se usa para iniciar el composer en el proyecto***
```bash
composer init
```

***Este comando regenera el mapa de clases del autoload***
```bash
composer dump-autoload
```

***Este comando instala ramsey/uuid***
```bash
composer require ramsey/uuid
```

***Este comando instala firebase/php-jwt***
```bash
composer require firebase/php-jwt
```

***Este comando instala phpmailer/phpmailer***
```bash
composer require phpmailer/phpmailer
```

***Este comando crea la clave RSA .pem privada***
```bash
openssl genpkey -algorithm RSA -pkeyopt rsa_keygen_bits:2048 -out private.pem
```

***Este comando crea la clave RSA .pem publica***
```bash
openssl pkey -in private.pem -pubout -out public.pem
```

***Asignarle los permisos adecuados a cada key***
```bash
chmod 600 private.pem   # solo tú puedes leerla
chmod 644 public.pem    # ésta sí puede ser legible por todos
```