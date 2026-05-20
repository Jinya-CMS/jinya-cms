---
title: Apache
---

# Installation in Apache

This guide will walk you through the process of setting up Jinya CMS on a server running Apache and PHP.

## Prerequisites

Before you begin, ensure your server meets the following requirements:

- **PHP**: Version 8.4 or higher
- **PHP Extensions**:
  - `ext-date`
  - `ext-fileinfo`
  - `ext-intl`
  - `ext-json`
  - `ext-pdo`
  - `ext-pdo_mysql`
  - `ext-zip`
  - `ext-zlib`
- **Database**: MySQL or MariaDB
- **Web Server**: Apache with `mod_rewrite` enabled

## Installation Steps

### Download Jinya CMS

Download the latest stable release of Jinya CMS from our release server:

<a class="button button--primary" href="https://releases.jinya.de/cms/stable">Download Jinya CMS Stable</a>

### Extract the Archive

Extract the downloaded ZIP file into your web server's document root or a subdirectory of your choice.

```bash
unzip jinya-cms-stable.zip -d /var/www/jinya-cms
```

### Set Permissions

Ensure that the web server user (usually `www-data`) has write access to the following directories:

- `var/`
- `public/` (for media uploads)

```bash
chown -R www-data:www-data /var/www/jinya-cms
chmod -R 775 /var/www/jinya-cms/var
chmod -R 775 /var/www/jinya-cms/public
```

### Configure Apache

You need to configure an Apache VirtualHost to point to the `public` directory of your Jinya CMS installation. Make sure `AllowOverride All` is set so the `.htaccess` file can be processed.

Example VirtualHost configuration:

```apache
<VirtualHost *:80>
    ServerName yourdomain.com
    DocumentRoot /var/www/jinya-cms/public

    <Directory /var/www/jinya-cms/public>
        AllowOverride All
        Require all granted
    </Directory>

    ErrorLog ${APACHE_LOG_DIR}/jinya-cms-error.log
    CustomLog ${APACHE_LOG_DIR}/jinya-cms-access.log combined
</VirtualHost>
```

Enable the configuration and restart Apache:

```bash
sudo a2ensite jinya-cms.conf
sudo a2enmod rewrite
sudo systemctl restart apache2
```

### Run the Installer

Once your web server is configured, navigate to your domain in your web browser. Jinya CMS includes a web-based installer that will guide you through the remaining steps:

- Database configuration
- Administrative user creation
- Initial site setup

Follow the on-screen instructions to complete your installation.

## Troubleshooting

If you encounter any issues during installation:

- Check the Apache error logs.
- Verify that your PHP version and extensions meet the requirements.
- Ensure the `var/` directory is writable.