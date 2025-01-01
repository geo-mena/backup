<div align="center">
    <p>
        <h1 align="center">Database Backup Utility </h1>
    </p>
    <p>
    
![Docker Badge](https://shields.io/badge/Docker-20.10.7-blue?logo=docker)
![Laravel-zero Badge](https://shields.io/badge/Laravel%20Zero-11.36.1-blue?logo=laravel)
![PHP Badge](https://shields.io/badge/PHP-8.2-blue?logo=php)
![PostgreSQL Badge](https://shields.io/badge/PostgreSQL-16-blue?logo=postgresql)
![MySQL Badge](https://shields.io/badge/MySQL-8.0-blue?logo=mysql)
![MongoDB Badge](https://shields.io/badge/MongoDB-5.0-blue?logo=mongodb)
</div>

A robust and flexible command-line utility for backing up [MySQL](https://www.mysql.com), [PostgreSQL](https://www.postgresql.org), and [MongoDB](https://www.mongodb.com) databases. Built with simplicity and power in mind, this tool offers a streamlined approach to database backups while providing advanced features for more complex requirements.

---

## ✨ Features

Support for multiple databases:

-   [MySQL](https://www.mysql.com)
-   [PostgreSQL](https://www.postgresql.org)
-   [MongoDB](https://www.mongodb.com)

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