---
title: Docker Compose
---

# Installation with Docker Compose

This guide will walk you through the process of setting up Jinya CMS using Docker Compose. This is the easiest way to get Jinya CMS up and running.

## Prerequisites

- **Docker**: Version 20.10 or higher
- **Docker Compose**: Version 2.0 or higher

## Installation Steps

### Create a `docker-compose.yml`

Create a new directory for your Jinya CMS installation and create a `docker-compose.yml` file with the following content:

```yaml
services:
  database:
    image: mariadb:11
    restart: always
    environment:
      MARIADB_DATABASE: jinya
      MARIADB_USER: jinya
      MARIADB_PASSWORD: jinya_password
      MARIADB_ROOT_PASSWORD: root_password
    volumes:
      - db_data:/var/lib/mysql

  jinya-cms:
    image: jinyacms/jinya-cms:latest
    restart: always
    ports:
      - "8080:80"
    environment:
      MYSQL_HOST: database
      MYSQL_DATABASE: jinya
      MYSQL_USER: jinya
      MYSQL_PASSWORD: jinya_password
    volumes:
      - jinya_data:/var/www/html/var
      - jinya_public:/var/www/html/public
    depends_on:
      - database

volumes:
  db_data:
  jinya_data:
  jinya_public:
```

### Start the Containers

Run the following command in the directory where you created the `docker-compose.yml` file:

```bash
docker compose up -d
```

### Run the Installer

Once the containers are started, navigate to `http://localhost:8080` in your web browser. Jinya CMS includes a web-based installer that will guide you through the remaining steps:

- Database configuration (use `database` as host, and the credentials from your `docker-compose.yml`)
- Administrative user creation
- Initial site setup

Follow the on-screen instructions to complete your installation.

## Environment Variables

The Jinya CMS Docker image can be configured using environment variables. Here are the most important ones:

| Variable         | Description                               |
|------------------|-------------------------------------------|
| `MYSQL_HOST`     | The hostname of your MySQL/MariaDB server |
| `MYSQL_DATABASE` | The name of the database                  |
| `MYSQL_USER`     | The database user                         |
| `MYSQL_PASSWORD` | The database password                     |
| `MYSQL_PORT`     | The database port (default: 3306)         |

## Persistence

The example `docker-compose.yml` uses named volumes to persist your data:

- `db_data`: Persists the database content.
- `jinya_data`: Persists the `var/` directory (logs, cache, etc.).
- `jinya_public`: Persists the `public/` directory (uploaded media).

It is important to keep these volumes to ensure your data is not lost when containers are recreated.
