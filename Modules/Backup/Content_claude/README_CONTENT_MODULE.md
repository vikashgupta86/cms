# Content Module — Installation & Setup

## What's New / Changed

| File | Status |
|------|--------|
| `database/migrations/2026_04_23_100000_create_contents_table.php` | **NEW** (replaces old migration) |
| `Models/Content.php` | **REPLACED** |
| `Http/Controllers/Backend/ContentsController.php` | **REPLACED** |
| `Http/Controllers/Frontend/CmsController.php` | **REPLACED** |
| `Http/Requests/StoreContentRequest.php` | **NEW** |
| `Http/Requests/UpdateContentRequest.php` | **NEW** |
| `Providers/ContentServiceProvider.php` | **REPLACED** |
| `Providers/RouteServiceProvider.php` | **REPLACED** |
| `Providers/EventServiceProvider.php` | **REPLACED** |
| `routes/web.php` | **REPLACED** |
| `Resources/views/backend/contents/index.blade.php` | **REPLACED** |
| `Resources/views/backend/contents/create.blade.php` | **REPLACED** |
| `Resources/views/backend/contents/edit.blade.php` | **REPLACED** |
| `Resources/views/backend/contents/form.blade.php` | **REPLACED** |
| `Resources/views/frontend/cms/content-list.blade.php` | **NEW** |
| `Resources/views/frontend/cms/content-detail.blade.php` | **NEW** |
| `Resources/views/frontend/cms/child-menu-grid.blade.php` | **NEW** |
| `database/seeders/ContentDatabaseSeeder.php` | **REPLACED** |
| `Config/config.php` | **REPLACED** |
| `lang/en/text.php` | **REPLACED** |
| `module.json` | **REPLACED** |
| `composer.json` | **REPLACED** |

> ⚠️ The Menu module files are **NOT modified**. Zero changes to Menu.

---

## Installation Steps

### 1. Drop old contents table (if it used `menu_item_id`)

```sql
DROP TABLE IF EXISTS contents;
```

Or rollback:
```bash
php artisan migrate:rollback
```

### 2. Copy the module

Replace `Modules/Content/` with this new Content folder.

### 3. Run migrations

```bash
php artisan migrate
```

### 4. Link storage

```bash
php artisan storage:link
```

### 5. Register the module

If using `nwidart/laravel-modules`, the `module.json` handles this automatically.

If **not** using that package, add the service provider to `bootstrap/providers.php`:

```php
\Modules\Content\Providers\ContentServiceProvider::class,
```

### 6. Seed (optional)

```bash
php artisan db:seed --class="Modules\Content\database\seeders\ContentDatabaseSeeder"
```

Or add to your `DatabaseSeeder.php`:

```php
$this->call(\Modules\Content\database\seeders\ContentDatabaseSeeder::class);
```

### 7. Route conflict note

The catch-all CMS routes (`{path}` and `{path}/{slug}`) must be loaded **last**.
The module's `RouteServiceProvider` loads them via `routes/web.php`.
Ensure your app loads module routes **after** all other route files.

In `bootstrap/app.php` (Laravel 11+):

```php
->withRouting(
    web: __DIR__.'/../routes/web.php',
    // module routes are loaded by service providers — they run after
)
```

### 8. Permissions (Spatie)

The seeder creates these permissions:

- `content.index`
- `content.create`
- `content.edit`
- `content.delete`
- `content.publish`

Assign them to your admin role manually or via the seeder (auto-assigns to `super-admin` / `admin` roles).

---

## Key Design Decisions

- **`contents.menu_id`** → FK to `menus.id` (NOT `menu_items.id`).
- Frontend routing uses `/{path}` and `/{path}/{slug}` catch-all patterns.
- `CmsController::show()` checks for child menus (using `menus.full_slug LIKE 'parent/%'`) and renders either a grid or a content list.
- File uploads go to `storage/app/public/content-files/` (accessible at `storage/content-files/` after `storage:link`).
- Max upload size is configurable via `CONTENT_MAX_FILE_SIZE_KB` env var (default 10 MB).
