# Simulador de torneos de tennis

El proyecto simula un torneo de tennis mediante una llamada a una API. 
Esta hecho en PHP con arquitectura n-capas y API RESTfull.
tambien utiliza PHPUnit y aprovecha el autoload del composer.


## Modo de uso

Para poder hacer pruebas, puede utilizar swagger accediendo [aqui](http://ec2-18-220-167-188.us-east-2.compute.amazonaws.com/swagger)

- primero registrese como usuario utilizando /register
- segundo debera loguearse para obtener el token de autenticacion utilizando /login
- terero copie el valor del campo access_token y utilizelo en Authorize en la esquina superior derecha
- con esos 3 pasos ya esta autenticado y automaticamente se enviarara un header con el bearer token para poder usar las otras llamadas


## Instalacion

Clonar el repositorio:
```
git clone https://github.com/your-username/tennis-tournament-simulator.git
```

Luego debera crear el archivo .env en el root del proyecto:
```
DB_HOST=<host de la base>
DB_NAME=<nombre de la base>
DB_USER=<usuario admin de la base>
DB_PASS=<password del admin de la base>

OAUTH_SECRET_KEY=<cadena de texto aleatoria>
```

Por ultimo debera correr el comando:
```
composer install
```

Luego de configurar su servidor de apache y crear su URL de endpoint,
debera realizar los siguientes ajustes en swagger:
- abrir el archivo swagger/swagger-initializer.js y modificar la url dentro de SwaggerUIBundle
- abrir el archivo swagger/swagger.json y modificar la url dentro de servers


## Pruebas en produccion

Si desea hacer pruebas el enpoint de la API real es
```
http://ec2-18-220-167-188.us-east-2.compute.amazonaws.com
```

## Pruebas unitarias

Para correr las pruebas unitarias dirigase a la consola y 
vaya al directorio raiz del proyecto 
luego puede correr el siguiente comando
```
vendor/bin/phpunit tests/*
```