# FTP Deployment Template Setup Guide

This template provides a production-ready GitHub Action workflow for deploying applications via FTP with automatic versioning support.

## Quick Start

1. Copy `ftp-deployment-template.yml` to your project's `.github/workflows/` directory
2. Configure GitHub Secrets
3. Customize the workflow for your project
4. Push and create a release

## Required GitHub Secrets

Add these secrets to your repository (Settings → Secrets and variables → Actions):

### FTP Credentials
```
FTP_SERVER          = your-ftp-server.com
FTP_USERNAME        = your-ftp-username
FTP_PASSWORD        = your-ftp-password
```

### Email Notifications (Optional but Recommended)
```
EMAIL_USERNAME          = your-email@gmail.com
EMAIL_PASSWORD          = your-app-specific-password
NOTIFICATION_EMAILS     = dev1@company.com, dev2@company.com, manager@company.com
```

## Customization Options

### 1. Change Deployment Trigger

**Deploy on any release:**
```yaml
on:
  release:
    types: [published]
```

**Deploy on specific tag patterns:**
```yaml
on:
  push:
    tags:
      - "v*.*.*"              # v1.0.0, v2.1.3
      - "v*.*.*-beta*"        # v1.0.0-beta1
      - "v*.*.*-alpha*"       # v1.0.0-alpha2
```

**Deploy on specific branches:**
```yaml
on:
  push:
    branches:
      - main
      - production
```

### 2. Configure Server Path

Specify the remote directory on your FTP server:

```yaml
- name: 📂 Sync files to production server
  uses: SamKirkland/FTP-Deploy-Action@v4.3.5
  with:
    server: ${{ secrets.FTP_SERVER }}
    username: ${{ secrets.FTP_USERNAME }}
    password: ${{ secrets.FTP_PASSWORD }}
    server-dir: /public_html/                    # Change this
    # or for subdirectory:
    # server-dir: /public_html/myapp/
```

### 3. Exclude Files from Upload

Prevent unnecessary files from being uploaded:

```yaml
- name: 📂 Sync files to production server
  uses: SamKirkland/FTP-Deploy-Action@v4.3.5
  with:
    server: ${{ secrets.FTP_SERVER }}
    username: ${{ secrets.FTP_USERNAME }}
    password: ${{ secrets.FTP_PASSWORD }}
    exclude: |
      **/.git*
      **/.git*/**
      **/node_modules/**
      **/tests/**
      **/vendor/**
      **/.env
      **/.env.example
      **/phpunit.xml
      **/composer.json
      **/composer.lock
      **/package.json
      **/package-lock.json
      **/*.md
      .github/**
```

### 4. Add Build Steps

For applications that need compilation (e.g., Laravel, React):

```yaml
steps:
  - name: 🚚 Get latest code
    uses: actions/checkout@v4

  # Add these steps before version file creation:

  - name: Setup PHP
    uses: shivammathur/setup-php@v2
    with:
      php-version: 8.2
      extensions: mbstring, xml, ctype, iconv, intl, pdo_mysql

  - name: Install Composer dependencies
    run: composer install --no-dev --optimize-autoloader

  # - name: Setup Node.js
  #   uses: actions/setup-node@v4
  #   with:
  #     node-version: '20'

  # - name: Install npm dependencies and build
  #   run: |
  #     npm ci
  #     npm run build

  - name: 📝 Create version files
    run: |
      echo "${{ github.ref_name }}" > version.txt
      git rev-parse --short HEAD > commit.txt

  # Continue with FTP upload...
```

### 5. Environment-Specific Deployments

Deploy to staging and production:

```yaml
name: Multi-Environment Deployment

on:
  push:
    branches:
      - staging    # → Staging
      - main       # → Production

jobs:
  deploy-staging:
    if: github.ref == 'refs/heads/staging'
    name: 🎉 Deploy to Staging
    runs-on: ubuntu-latest
    steps:
      # ... same steps but use STAGING secrets
      - name: 📂 Sync to staging server
        uses: SamKirkland/FTP-Deploy-Action@v4.3.5
        with:
          server: ${{ secrets.FTP_STAGING_SERVER }}
          username: ${{ secrets.FTP_STAGING_USERNAME }}
          password: ${{ secrets.FTP_STAGING_PASSWORD }}

  deploy-production:
    if: github.ref == 'refs/heads/main'
    name: 🎉 Deploy to Production
    runs-on: ubuntu-latest
    steps:
      # ... same steps but use PRODUCTION secrets
      - name: 📂 Sync to production server
        uses: SamKirkland/FTP-Deploy-Action@v4.3.5
        with:
          server: ${{ secrets.FTP_PRODUCTION_SERVER }}
          username: ${{ secrets.FTP_PRODUCTION_USERNAME }}
          password: ${{ secrets.FTP_PRODUCTION_PASSWORD }}
```

### 6. Customize Email Recipients

**Single recipient:**
```yaml
to: developer@company.com
```

**Multiple recipients:**
```yaml
to: dev1@company.com, dev2@company.com, manager@company.com
```

**Use GitHub Secret:**
```yaml
to: ${{ secrets.NOTIFICATION_EMAILS }}
```

**Different recipients based on status:**
```yaml
- name: 📧 Send success notification
  if: success()
  uses: dawidd6/action-send-mail@v3
  with:
    to: team@company.com

- name: 📧 Send failure notification
  if: failure()
  uses: dawidd6/action-send-mail@v3
  with:
    to: devops@company.com, manager@company.com
```

## Example Configurations

### Laravel Project

```yaml
- name: Setup PHP
  uses: shivammathur/setup-php@v2
  with:
    php-version: 8.2

- name: Install dependencies
  run: composer install --no-dev --optimize-autoloader

- name: Create version files
  run: |
    echo "${{ github.ref_name }}" > version.txt
    git rev-parse --short HEAD > commit.txt

- name: Sync to server
  uses: SamKirkland/FTP-Deploy-Action@v4.3.5
  with:
    server: ${{ secrets.FTP_SERVER }}
    username: ${{ secrets.FTP_USERNAME }}
    password: ${{ secrets.FTP_PASSWORD }}
    server-dir: /public_html/
    exclude: |
      **/.git*
      **/node_modules/**
      **/tests/**
      **/.env
      **/storage/logs/**
```

### WordPress Plugin

```yaml
- name: Create version files
  run: |
    echo "${{ github.ref_name }}" > version.txt
    git rev-parse --short HEAD > commit.txt

- name: Sync to server
  uses: SamKirkland/FTP-Deploy-Action@v4.3.5
  with:
    server: ${{ secrets.FTP_SERVER }}
    username: ${{ secrets.FTP_USERNAME }}
    password: ${{ secrets.FTP_PASSWORD }}
    server-dir: /wp-content/plugins/my-plugin/
    exclude: |
      **/.git*
      **/node_modules/**
      **/tests/**
      **/.wordpress-org/**
      **/bin/**
```

### Static Website

```yaml
- name: Setup Node.js
  uses: actions/setup-node@v4
  with:
    node-version: '20'

- name: Build site
  run: |
    npm ci
    npm run build

- name: Create version files
  run: |
    echo "${{ github.ref_name }}" > dist/version.txt
    git rev-parse --short HEAD > dist/commit.txt

- name: Sync to server
  uses: SamKirkland/FTP-Deploy-Action@v4.3.5
  with:
    server: ${{ secrets.FTP_SERVER }}
    username: ${{ secrets.FTP_USERNAME }}
    password: ${{ secrets.FTP_PASSWORD }}
    local-dir: ./dist/
    server-dir: /public_html/
```

## Versioning Package Integration

After deployment, your application can access version information:

### Laravel
```php
use Williamug\Versioning\Facades\Versioning;

// In your controller or view
echo Versioning::tag();     // v1.0.0
echo Versioning::commit();  // abc1234

// Blade template
@app_version_tag  {{-- v1.0.0 --}}
```

### Vanilla PHP
```php
require 'vendor/autoload.php';

use Williamug\Versioning\StandaloneVersioning;

StandaloneVersioning::setRepositoryPath(__DIR__);
echo StandaloneVersioning::tag();     // v1.0.0
echo StandaloneVersioning::commit();  // abc1234
```

## Troubleshooting

### Issue: FTP upload fails

**Solution:** Check FTP credentials and server accessibility
```bash
# Test FTP connection locally
ftp your-server.com
# Enter username and password
```

### Issue: Version shows 'dev' on server

**Solution:** Verify version files were created and uploaded
1. Check GitHub Action logs for "Create version files" step
2. SSH/FTP to server and check if `version.txt` exists
3. Check file permissions: `chmod 644 version.txt`


### Issue: Large files timing out

**Solution:** Increase timeout or exclude large directories
```yaml
- name: 📂 Sync files to production server
  uses: SamKirkland/FTP-Deploy-Action@v4.3.5
  timeout-minutes: 60  # Increase from 40
  with:
    exclude: |
      **/storage/logs/**
      **/storage/framework/cache/**
```

## Testing Locally

Test the workflow before deploying:

```bash
# 1. Install act (GitHub Actions local runner)
brew install act  # macOS
# or
curl https://raw.githubusercontent.com/nektos/act/master/install.sh | sudo bash

# 2. Create .secrets file
cat > .secrets << EOF
FTP_SERVER=your-server.com
FTP_USERNAME=your-username
FTP_PASSWORD=your-password
EOF

# 3. Run workflow locally
act -s GITHUB_TOKEN="$(gh auth token)" --secret-file .secrets
```


## Support

For issues specific to:
- **FTP Deploy Action**: https://github.com/SamKirkland/FTP-Deploy-Action
- **Email Action**: https://github.com/dawidd6/action-send-mail
- **Versioning Package**: https://github.com/williamug/versioning

## License

This template is free to use and modify for your projects.
