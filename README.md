<p align="center">
    <h1 align="center">Database Backup Utility </h1>
</p>

<p align="center">
  <a href="https://github.com/laravel-zero/framework/actions"><img src="https://github.com/laravel-zero/laravel-zero/actions/workflows/tests.yml/badge.svg" alt="Build Status" /></a>
  <a href="https://packagist.org/packages/laravel-zero/framework"><img src="https://img.shields.io/packagist/dt/laravel-zero/framework.svg" alt="Total Downloads" /></a>
  <a href="https://packagist.org/packages/laravel-zero/framework"><img src="https://img.shields.io/packagist/v/laravel-zero/framework.svg?label=stable" alt="Latest Stable Version" /></a>
  <a href="https://packagist.org/packages/laravel-zero/framework"><img src="https://img.shields.io/packagist/l/laravel-zero/framework.svg" alt="License" /></a>
</p>

A robust and flexible command-line utility for backing up [MySQL](https://www.mysql.com), [PostgreSQL](https://www.postgresql.org), and [MongoDB](https://www.mongodb.com) databases. Built with simplicity and power in mind, this tool offers a streamlined approach to database backups while providing advanced features for more complex requirements.

---

## ✨ Features

Support for multiple databases:

-   MySQL/MariaDB
-   PostgreSQL
-   MongoDB

Advanced backup options:

-   Automatic compression (.gz)
-   Timestamped backups
-   Generated file validation
-   Detailed process information

## 🚀 Installation

1. Clone the repository:

```bash
git clone https://github.com/geo-mena/backup.git
```

2. Install the dependencies:

```bash
cd backup
```

3. Initialize with Docker:

```bash
./init.sh
```

4. Build the Docker image:

```bash
docker compose build --no-cache
```

5. Start the Docker container:

```bash
docker compose up -d
```

6. Access the container:

```bash
docker compose exec app sh
```

## 📋 Requirements

-   [Docker](https://www.docker.com)
-   [Docker Compose](https://docs.docker.com/compose)
-   [Git](https://git-scm.com)

## 💻 Usage

### PostgreSQL

```bash
docker compose run --rm app php application backup \
    --type=postgres \
    --host=your-host \
    --port=5432 \
    --database=your-db \
    --user=your-user \
    --password=your-password \
    --compress
```

### MySQL

```bash
docker compose run --rm app php application backup \
    --type=mysql \
    --host=your-host \
    --port=3306 \
    --database=your-db \
    --user=root \
    --password=your-password \
    --compress
```

### MongoDB

```bash
docker compose run --rm app php application backup \
    --type=mongodb \
    --host=your-host \
    --port=27017 \
    --database=your-db \
    --user=your-user \
    --password=your-password \
    --compress
```

## 🔧 Available Options

| Option       | Description                                  | Default Value         |
| ------------ | -------------------------------------------- | --------------------- |
| `--type`     | Database type (`mysql`/`postgres`/`mongodb`) | `mysql`               |
| `--host`     | Database host                                | `localhost`           |
| `--port`     | Database port                                | `[depends on type]`   |
| `--database` | Database name                                | **required**          |
| `--user`     | Database user                                | -                     |
| `--password` | Database password                            | -                     |
| `--output`   | Output directory                             | `app/storage/backups` |
| `--compress` | Compress the backup                          | `false`               |

## 📁 File Structure

Backups are stored in the storage/backups/ directory with the following format:

```
{database}_{type}_{date}.{extension}[.gz]
```

Example:

```
mydb_postgres_2024-12-31_18-30-00.sql.gz
```

## 🐳 Container Information

The container includes:

-   PHP 8.2
-   PostgreSQL Client 16
-   MySQL Client
-   MongoDB Tools

## ⚠️ Important Notes

-   Compressed backups use gzip (.gz)
-   Using environment variables for passwords is recommended in production
-   Timestamps are in UTC format

## 🐛 Troubleshooting

### Permission Error

If you encounter a permission error, run the following command:

```bash
chmod -R 755 storage
```

### Connection Error

Verify that the host and port are accessible from the Docker container

## 📝 License

The **Database Backup Utility** is an open-source software licensed under the MIT license.

## ✨ Contributing
Contributions are welcome! Please open an issue first to discuss any changes you'd like to make.