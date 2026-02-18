# Database Storage for FTP Deployments

When deploying via FTP, the `.git` directory is removed, so you can't access git tags directly. This package offers **three solutions** for handling versioning in FTP deployments.

## Solution Comparison

| Method | Best For | Setup Complexity | Automatic? | Reliability |
|--------|----------|------------------|------------|-------------|
| **Version Files** | Simple deployments | ⭐ Easy | ✅ Yes | ⭐⭐⭐ High |
| **Database (Auto)** | Production apps | ⭐⭐ Medium | ✅ Yes | ⭐⭐⭐ High |
| **Database (Manual)** | Control freaks | ⭐⭐ Medium | ❌ No | ⭐⭐⭐ High |
| **Environment Variable** | Quick fixes | ⭐ Very Easy | ❌ No | ⭐⭐ Medium |

---

## Method 1: Version Files (Recommended for Most Cases)

Create version files during GitHub Actions deployment.

### Setup

Add this step to your GitHub Action **before** FTP upload:

```yaml
- name: 📝 Create version files
  run: |
    echo "${{ github.ref_name }}" > version.txt
    git rev-parse --short HEAD > commit.txt
```

### Usage

No code changes needed! The package automatically reads from these files:

```php
use Williamug\Versioning\Versioning;

echo Versioning::tag();     // v1.0.0 (from version.txt)
echo Versioning::commit();  // abc1234 (from commit.txt)
```

### Complete Example

See [`examples/ftp-deployment-template.yml`](examples/ftp-deployment-template.yml) for a full GitHub Action workflow.

---

## Method 2: Database Storage (Automatic - Recommended)

Store version information in your database with **automatic sync** from version files.

### How It Works

1. GitHub Action creates `version.txt` during deployment
2. Files uploaded via FTP
3. First web request triggers auto-sync to database
4. Future requests read from database (fast!)

### Setup

#### 1. Publish and Run Migration

```bash
php artisan vendor:publish --tag="versioning-migrations"
php artisan migrate
```

#### 2. Enable Auto-Sync (One Line!)

```env
# .env
VERSIONING_USE_DATABASE=true
VERSIONING_AUTO_SYNC=true  # This enables automatic sync
```

Or in config:

```php
// config/versioning.php
return [
    'use_database' => true,
    'auto_sync' => true,  // 🔥 Automatic sync enabled
];
```

#### 3. Create Version Files in GitHub Action

```yaml
- name: 📝 Create version files
  run: |
    echo "${{ github.ref_name }}" > version.txt
    git rev-parse --short HEAD > commit.txt

- name: 📂 FTP Deploy
  uses: SamKirkland/FTP-Deploy-Action@v4.3.5
  # ... FTP config
```

**That's it!** The package automatically syncs `version.txt` to database on first request.

### Usage

```php
use Williamug\Versioning\Versioning;

echo Versioning::tag();     // v1.0.0 (from database, auto-synced from version.txt)
echo Versioning::commit();  // abc1234
```

### Complete GitHub Action Example

```yaml
name: Deploy with Auto-Sync Database Versioning

on:
  release:
    types: [published]

jobs:
  deploy:
    runs-on: ubuntu-latest
    steps:
      - name: 🚚 Checkout code
        uses: actions/checkout@v4
        with:
          fetch-depth: 0

      - name: 📝 Create version files
        run: |
          echo "${{ github.ref_name }}" > version.txt
          git rev-parse --short HEAD > commit.txt

      - name: 📂 FTP Deploy
        uses: SamKirkland/FTP-Deploy-Action@v4.3.5
        with:
          server: ${{ secrets.FTP_SERVER }}
          username: ${{ secrets.FTP_USERNAME }}
          password: ${{ secrets.FTP_PASSWORD }}

      # No manual sync needed! It happens automatically on first request.
```

### How Auto-Sync Works

- Middleware checks for `version.txt` on first web request
- If new version detected, syncs to database automatically
- Uses cache lock to run only once per deployment
- Completely transparent - no manual intervention

---

## Method 3: Database Storage (Manual Control)

If you prefer explicit control over when versions are stored, disable auto-sync.

### Setup

#### 1. Migration (same as automatic)

```bash
php artisan vendor:publish --tag="versioning-migrations"
php artisan migrate
```

#### 2. Enable Database, Disable Auto-Sync

```env
VERSIONING_USE_DATABASE=true
VERSIONING_AUTO_SYNC=false  # Manual control
```

#### 3. Store Version During Deployment

**Option A: Via Artisan Command**

Add to your deployment script or GitHub Action:

```bash
php artisan version:store v1.0.0 --commit=abc1234 --release="Production Release"
```

**Option B: Via HTTP Endpoint**

Create a route in your application:

```php
// routes/api.php
use Illuminate\Http\Request;
use Williamug\Versioning\Models\AppVersion;

Route::post('/deployment/version', function (Request $request) {
    $request->validate([
        'tag' => 'required|string',
        'commit' => 'nullable|string',
        'full' => 'nullable|string',
    ]);

    AppVersion::store(
        tag: $request->tag,
        commit: $request->commit,
        full: $request->full,
        releaseName: $request->release_name,
    );

    return response()->json(['success' => true]);
})->middleware('auth:sanctum'); // Protect with authentication!
```

Then call it from GitHub Actions:

```yaml
- name: 📝 Store version in database
  run: |
    curl -X POST "${{ secrets.APP_URL }}/api/deployment/version" \
      -H "Authorization: Bearer ${{ secrets.API_TOKEN }}" \
      -H "Content-Type: application/json" \
      -d '{
        "tag": "${{ github.ref_name }}",
        "commit": "${{ github.sha }}",
        "release_name": "${{ github.event.release.name }}"
      }'
```

**Option C: Via Code**

```php
use Williamug\Versioning\Models\AppVersion;

AppVersion::store(
    tag: 'v1.0.0',
    commit: 'abc1234',
    full: 'v1.0.0-5-gabc1234',
    releaseName: 'Production Release',
    metadata: ['environment' => 'production']
);
```

### Usage

```php
use Williamug\Versioning\Versioning;

// Package automatically checks database first
echo Versioning::tag();     // v1.0.0 (from database)
echo Versioning::commit();  // abc1234 (from database)
```

### GitHub Action Complete Example

```yaml
name: Deploy with Database Versioning

on:
  release:
    types: [published]

jobs:
  deploy:
    runs-on: ubuntu-latest
    steps:
      - name: 🚚 Checkout code
        uses: actions/checkout@v4

      - name: 📂 FTP Deploy
        uses: SamKirkland/FTP-Deploy-Action@v4.3.5
        with:
          server: ${{ secrets.FTP_SERVER }}
          username: ${{ secrets.FTP_USERNAME }}
          password: ${{ secrets.FTP_PASSWORD }}

      - name: 📝 Store version in database
        run: |
          ssh ${{ secrets.SSH_USER }}@${{ secrets.SSH_HOST }} \
            "cd /path/to/app && php artisan version:store ${{ github.ref_name }} \
            --commit=${{ github.sha }} \
            --release='${{ github.event.release.name }}'"
```

### Version History

Query all past deployments:

```php
use Williamug\Versioning\Models\AppVersion;

// Get all versions
$versions = AppVersion::latest('deployed_at')->get();

// Get current version
$current = AppVersion::current();

// Get specific version details
foreach ($versions as $version) {
    echo "{$version->version_tag} - {$version->deployed_at}\n";
}
```

---

## Method 4: Environment Variable

Simple but requires manual updates.

### Setup

```env
# .env
APP_VERSION=v1.0.0
```

```php
// config/versioning.php
return [
    'fallback_version' => env('APP_VERSION', 'dev'),
];
```

### Usage

```php
echo Versioning::tag();  // v1.0.0 (from .env)
```

**Limitation**: Must manually update `.env` on server after each deployment.

---

## Priority Order

The package checks sources in this order:

1. **Database** (if `use_database` is enabled)
2. **Version files** (`version.txt`, `commit.txt`)
3. **Git repository** (if `.git` exists)
4. **Composer.json** (if version field exists)
5. **Fallback version** (from config or env)

---

## Update Methods Comparison

### Automatic (Recommended ⭐)
```env
VERSIONING_USE_DATABASE=true
VERSIONING_AUTO_SYNC=true
```
- ✅ Zero manual work after setup
- ✅ Syncs on first request after deployment
- ✅ Version files → Database automatically
- ⚠️ Slight delay on first request (cached after)

### Manual via Command
```bash
ssh server "php artisan version:store v1.0.0"
```
- ✅ Full control over timing
- ✅ Can add custom metadata
- ❌ Requires SSH access or extra deploy step
- ❌ More complex GitHub Action

### Manual via API
```bash
curl -X POST https://app.com/api/version -d "tag=v1.0.0"
```
- ✅ Works without SSH
- ✅ Can trigger from anywhere
- ❌ Requires secure API endpoint
- ❌ More code to maintain

### Version Files Only (No Database)
```yaml
echo "v1.0.0" > version.txt
```
- ✅ Simplest approach
- ✅ No database needed
- ❌ No version history
- ❌ Files could be accidentally deleted

---

## Priority Order

The package checks sources in this order:

1. **Database** (if `use_database` is enabled)
2. **Version files** (`version.txt`, `commit.txt`)
3. **Git repository** (if `.git` exists)
4. **Composer.json** (if version field exists)
5. **Fallback version** (from config or env)

---

## Hybrid Approach (Best of Both Worlds)

Combine database + version files for maximum reliability:

### GitHub Action

```yaml
steps:
  - name: 📝 Create version files
    run: |
      echo "${{ github.ref_name }}" > version.txt
      git rev-parse --short HEAD > commit.txt

  - name: 📂 FTP Deploy
    uses: SamKirkland/FTP-Deploy-Action@v4.3.5
    # ... FTP config

  - name: 📝 Store in database (fallback)
    run: |
      ssh ${{ secrets.SSH_USER }}@${{ secrets.SSH_HOST }} \
        "cd /path/to/app && php artisan version:store ${{ github.ref_name }}"
```

### Configuration

```php
// config/versioning.php
return [
    'use_database' => true,  // Database as primary
    // Version files automatically used as fallback
];
```

### Benefits

- **Database**: Primary source, queryable, historical data
- **Version files**: Backup in case database is unavailable
- **Git**: Works in development
- **Robust**: Multiple fallbacks ensure version is always available

---

## Testing

### Test Version Files

```bash
echo "v1.2.3" > version.txt
echo "abc1234" > commit.txt
php artisan tinker
>>> Versioning::tag()
=> "v1.2.3"
```

### Test Database

```bash
php artisan version:store v1.2.3 --commit=abc1234
php artisan tinker
>>> Versioning::tag()
=> "v1.2.3"
>>> AppVersion::current()
```

### Test Priority

```bash
# Both exist - database takes priority
echo "v1.0.0" > version.txt
php artisan version:store v2.0.0
php artisan tinker
>>> Versioning::tag()
=> "v2.0.0"  # From database
```

---

## Troubleshooting

### Version shows 'dev'

**Database enabled but showing 'dev':**
- Check if migration ran: `php artisan migrate:status`
- Check if version stored: `php artisan tinker` → `AppVersion::current()`
- Check config: `config('versioning.use_database')`

**Version files not working:**
- Verify files exist: `ls -la version.txt commit.txt`
- Check permissions: `chmod 644 version.txt commit.txt`
- Check file content: `cat version.txt`

### Database connection errors

The package gracefully handles DB errors and falls back to version files or git.

### Want to disable database

```env
VERSIONING_USE_DATABASE=false
```

Or remove from config:

```php
'use_database' => false,
```

---

## Migration from File-based to Database

```bash
# 1. Install migration
php artisan vendor:publish --tag="versioning-migrations"
php artisan migrate

# 2. Import current version from file
php artisan version:store $(cat version.txt) --commit=$(cat commit.txt)

# 3. Enable database in config
# config/versioning.php
'use_database' => true,

# 4. Test
php artisan tinker
>>> Versioning::tag()

# 5. Optional: Keep files as backup or remove
rm version.txt commit.txt
```

---

## See Also

- [FTP Deployment Guide](FTP-DEPLOYMENT.md) - Complete FTP deployment documentation
- [GitHub Action Template](examples/ftp-deployment-template.yml) - Ready-to-use workflow
- [README](README.md) - Main package documentation
