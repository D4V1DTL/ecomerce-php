# Ecommerce API (Laravel)

## Levantar el proyecto con Docker

1. **Asegúrate de tener Docker Desktop instalado y corriendo.**

2. **Copia el archivo de entorno y genera la clave de la aplicación (solo la primera vez):**

    ```sh
    cp .env.example .env
    ```

3. **Crea el archivo de base de datos para SQLite (si usas SQLite):**

    ```sh
    touch database/database.sqlite
    ```

4. **Construye y levanta los contenedores:**

    ```sh
    docker compose up --build
    ```

5. **Instala las dependencias de Composer dentro del contenedor (solo la primera vez):**

    ```sh
    docker compose exec app bash
    composer install
    php artisan key:generate
    php artisan migrate:fresh --seed
    exit
    ```

6. **Accede a la API en tu navegador o Postman:**
    ```
    http://localhost:8000/api/products
    ```

---

**Notas:**

-   Si cambias el código fuente, Docker sincroniza los archivos automáticamente gracias al volumen.
-   Si necesitas detener los contenedores, usa `docker compose down`.
-   Si usas otra base de datos (MySQL/PostgreSQL), configura tu `.env` y agrega el servicio correspondiente en `docker-compose.yml`.

---

¡Con estos pasos puedes levantar y probar tu API Laravel usando Docker fácilmente!

API RESTful para ecommerce desarrollada en Laravel.

## Requisitos

-   PHP >= 8.1
-   Composer
-   SQLite/MySQL/PostgreSQL (según tu configuración)
-   Node.js y npm (opcional, solo si usas frontend o Mix)

## Instalación

1. **Clona el repositorio**

    ```sh
    git clone https://github.com/D4V1DTL/ecomerce-php.git
    cd ecomerce-php
    ```

2. **Instala dependencias de PHP**

    ```sh
    composer install
    ```

3. **Copia el archivo de entorno y configura tus variables**

    ```sh
    cp .env.example .env
    ```

    Edita `.env` y configura tu base de datos (por ejemplo, para SQLite):

    ```
    DB_CONNECTION=sqlite
    DB_DATABASE=/ruta/absoluta/a/database.sqlite
    ```

    O para MySQL:

    ```
    DB_CONNECTION=mysql
    DB_HOST=127.0.0.1
    DB_PORT=3306
    DB_DATABASE=nombre_db
    DB_USERNAME=usuario
    DB_PASSWORD=contraseña
    ```

4. **Genera la clave de la aplicación**

    ```sh
    php artisan key:generate
    ```

5. **Crea la base de datos si es necesario**

    - Para SQLite:
        ```sh
        touch database/database.sqlite
        ```
    - Para MySQL/PostgreSQL: crea la base de datos desde tu gestor.

6. **Ejecuta migraciones y seeders**

    ```sh
    php artisan migrate:fresh --seed
    ```

    Esto creará todas las tablas y poblará la base de datos con datos de ejemplo.

7. **Levanta el servidor de desarrollo**
    ```sh
    php artisan serve
    ```
    La API estará disponible en [http://localhost:8000](http://localhost:8000).

## Endpoints principales

-   **Autenticación:**  
    `POST /api/register`  
    `POST /api/login`

-   **Productos:**  
    `GET /api/products`  
    `GET /api/products/{id}`

-   **Categorías:**  
    `GET /api/categories`

-   **Carrito:**  
    `GET /api/cart`  
    `POST /api/cart/add`  
    `POST /api/cart/sync`  
    `DELETE /api/cart/clear`  
    `DELETE /api/cart/remove/{productId}`

-   **Pedidos:**  
    `POST /api/orders`  
    `GET /api/orders`  
    `GET /api/orders/{id}`  
    `PATCH /api/orders/{orderId}/status`

## Notas

-   Recuerda agregar tu archivo `.env` al `.gitignore` para no subir credenciales sensibles.
-   Puedes modificar los seeders para agregar más datos de prueba según lo necesites.

---

¡Listo! Ahora puedes empezar a consumir la API desde tu frontend o herramientas como Postman.
