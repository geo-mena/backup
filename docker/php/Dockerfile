# docker/php/Dockerfile
FROM php:8.2-cli

# Agregar repositorio oficial de PostgreSQL
RUN apt-get update && apt-get install -y curl gnupg2
RUN curl -fsSL https://www.postgresql.org/media/keys/ACCC4CF8.asc | gpg --dearmor -o /usr/share/keyrings/postgresql-keyring.gpg
RUN echo "deb [signed-by=/usr/share/keyrings/postgresql-keyring.gpg] http://apt.postgresql.org/pub/repos/apt bookworm-pgdg main" > /etc/apt/sources.list.d/postgresql.list

# Instalar dependencias
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libzip-dev \
    libcurl4-openssl-dev \
    pkg-config \
    libssl-dev \
    libpq-dev \
    postgresql-client \
    default-mysql-client \
    && curl -fsSL https://www.mongodb.org/static/pgp/server-6.0.asc | tee /etc/apt/trusted.gpg.d/mongodb.asc \
    && echo "deb [ arch=amd64 ] https://repo.mongodb.org/apt/debian bookworm/mongodb-org/6.0 main" | tee /etc/apt/sources.list.d/mongodb-org-6.0.list \
    && apt-get update \
    && apt-get install -y mongodb-database-tools \
    && docker-php-ext-install zip

# Instalar extensiones de PHP necesarias para bases de datos
RUN docker-php-ext-install pdo pdo_mysql mysqli pdo_pgsql

# Instalar extensión MongoDB
RUN pecl install mongodb \
    && docker-php-ext-enable mongodb

# Instalar Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Directorio de trabajo
WORKDIR /app

# Configurar permisos
RUN mkdir -p storage/backups && \
    chown -R www-data:www-data /app && \
    chmod -R 755 /app/storage