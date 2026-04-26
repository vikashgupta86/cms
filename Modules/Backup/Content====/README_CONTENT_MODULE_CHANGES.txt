Content Module Changes (Gov-CMS feature merge)
=============================================

What was changed
----------------
1. Content module aligned to your Menu / MenuItem structure.
2. Replaced broken menu_id flow with menu_item_id everywhere.
3. Added Gov-CMS style admin content manager features:
   - search
   - type/status/menu item filters
   - slug generation
   - file upload handling
   - publish/draft/archive flow
   - SEO fields
   - view button for frontend content route
4. Added frontend CMS routes and controller:
   - /cms/{menuPath}
   - /cms/{menuPath}/{contentSlug}
5. Added helper to work with MenuItem paths and leaf nodes.
6. Rewrote contents migration for a production-ready contents table.
7. Registered Livewire component in ContentServiceProvider.

Important integration assumption
--------------------------------
This module expects your project to already have:
- Modules\Menu\Models\MenuItem model
- menu_items table
- fields like parent_id, slug

Best-supported columns in menu_items
-----------------------------------
The helper works best when menu_items has these columns:
- id
- parent_id
- name (or title)
- slug
- path (preferred) OR full_slug
- sort_order (optional)
- is_active / is_visible / status (optional)

Migration note
--------------
If you already migrated the OLD broken contents table earlier, do one of these:
1. rollback that table and re-run migrate, OR
2. run migrate:fresh in local/dev.

If contents table is not created yet, just run normal migration.

Storage note
------------
For file downloads/uploads, make sure this is done:
php artisan storage:link

