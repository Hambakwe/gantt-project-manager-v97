# Gantt Project Manager - Development Todos

## Completed Tasks

### v106 Release (2026-02-21)
- [x] Fixed admin pages flashing and redirecting to dashboard
- [x] Added proper `authInitialized` state to AuthProvider
- [x] Separated auth loading from projects loading
- [x] Added loading spinner to admin pages while auth initializes
- [x] Used `router.replace` instead of `router.push` for redirects
- [x] Added `useRef` to track user state for async operations
- [x] Updated all 16 PHP API files to @version v106
- [x] Updated config.php GPM_VERSION to v106
- [x] Updated full_verify.php with v106 changelog and version history
- [x] Created v106_portal_features.sql
- [x] Updated schema.sql and templates_schema.sql to v106
- [x] Updated README.md to v106
- [x] Built new Next.js static export with auth fixes
- [x] Created gantt-project-manager-v106-portal.zip (822 KB)

### v105 Release (2026-02-21)
- [x] Updated all 16 PHP API files to @version v105
- [x] Updated config.php GPM_VERSION to v105
- [x] Updated full_verify.php with v105 changelog and version history
- [x] Updated all version markers to v105 in verification files
- [x] Created v105_portal_features.sql
- [x] Updated schema.sql and templates_schema.sql to v105
- [x] Updated README.md to v105
- [x] Created gantt-project-manager-v105-portal.zip (815KB)

### v105 Features & Fixes
- [x] Fixed admin navigation routing (.htaccess subdirectory handling)
- [x] Fixed sidebar navigation links with trailing slashes
- [x] Fixed Create Project Select component error
- [x] Fixed Allocate to Client Select component error
- [x] Version API endpoint (/api/version.php)
- [x] Version display on Dashboard footer
- [x] Version display on Login dialog and login.php

### Previous Versions
- [x] v102 - Version API, version display features
- [x] v100 - Major milestone release
- [x] v97 - Fixed Allocate to Client page error
- [x] v96 - Admin features
- [x] v83 - Client Portal with Documents, Contacts, Diary

## Current Status

Version 106 deployment package is ready:
- **File:** `gantt-project-manager-v106-portal.zip`
- **Size:** 822 KB
- **API Endpoints:** 11 (including version.php)
- **SQL Files:** 8 (including v106_portal_features.sql)

## Database Update Required

Run this SQL to update portal version:
```sql
UPDATE system_settings SET value = 'v106' WHERE `key` = 'portal_version';
```

Or run the full migration:
```bash
mysql -u username -p database_name < sql/v106_portal_features.sql
```
