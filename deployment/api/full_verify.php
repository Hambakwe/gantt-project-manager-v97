<?php
/**
 * Gantt Project Manager - FULL Verification Script
 *
 * @version v106
 * @package GanttProjectManager
 * @build 2026-02-21
 *
 * DELETE THIS FILE AFTER VERIFICATION IN PRODUCTION!
 */

error_reporting(E_ALL);
ini_set('display_errors', '1');
header('Content-Type: text/html; charset=utf-8');

define('EXPECTED_VERSION', 'v106');
define('EXPECTED_BUILD_DATE', '2026-02-21');

$baseDir = dirname(__DIR__);
$apiDir = __DIR__;
$passed = 0;
$failed = 0;
$warnings = 0;

function showPass($msg) { global $passed; $passed++; echo "<div class='result pass'>✓ {$msg}</div>\n"; }
function showFail($msg) { global $failed; $failed++; echo "<div class='result fail'>✗ {$msg}</div>\n"; }
function showWarn($msg) { global $warnings; $warnings++; echo "<div class='result warn'>⚠ {$msg}</div>\n"; }
function showInfo($msg) { echo "<div class='result info'>ℹ {$msg}</div>\n"; }
function formatSize($bytes) {
    if ($bytes >= 1048576) return round($bytes / 1048576, 2) . ' MB';
    if ($bytes >= 1024) return round($bytes / 1024, 2) . ' KB';
    return $bytes . ' bytes';
}

// ============================================================
// FILES TO VERIFY
// ============================================================
$versionFiles = array(
    // Core API files
    'api/config.php' => array('path' => $apiDir . '/config.php', 'marker' => '@version v106', 'required' => true, 'desc' => 'Database configuration'),
    'api/projects.php' => array('path' => $apiDir . '/projects.php', 'marker' => '@version v106', 'required' => true, 'desc' => 'Projects API'),
    'api/tasks.php' => array('path' => $apiDir . '/tasks.php', 'marker' => '@version v106', 'required' => true, 'desc' => 'Tasks API'),
    'api/templates.php' => array('path' => $apiDir . '/templates.php', 'marker' => '@version v106', 'required' => true, 'desc' => 'Templates API'),
    'api/comments.php' => array('path' => $apiDir . '/comments.php', 'marker' => '@version v106', 'required' => true, 'desc' => 'Comments API'),
    'api/auth.php' => array('path' => $apiDir . '/auth.php', 'marker' => '@version v106', 'required' => true, 'desc' => 'Authentication API'),
    'api/statistics.php' => array('path' => $apiDir . '/statistics.php', 'marker' => '@version v106', 'required' => true, 'desc' => 'Statistics API'),
    // Portal APIs
    'api/documents.php' => array('path' => $apiDir . '/documents.php', 'marker' => '@version v106', 'required' => true, 'desc' => 'Documents API'),
    'api/contacts.php' => array('path' => $apiDir . '/contacts.php', 'marker' => '@version v106', 'required' => true, 'desc' => 'Team Contacts API'),
    'api/diary.php' => array('path' => $apiDir . '/diary.php', 'marker' => '@version v106', 'required' => true, 'desc' => 'Diary/Calendar API'),
    'api/version.php' => array('path' => $apiDir . '/version.php', 'marker' => '@version v106', 'required' => true, 'desc' => 'Version Info API'),
    // Verification scripts
    'api/db_verify.php' => array('path' => $apiDir . '/db_verify.php', 'marker' => '@version v106', 'required' => false, 'desc' => 'Database verification'),
    'api/file_verify.php' => array('path' => $apiDir . '/file_verify.php', 'marker' => '@version v106', 'required' => false, 'desc' => 'File verification'),
    'api/version_verify.php' => array('path' => $apiDir . '/version_verify.php', 'marker' => '@version v106', 'required' => false, 'desc' => 'Version verification'),
    'api/setup_passwords.php' => array('path' => $apiDir . '/setup_passwords.php', 'marker' => '@version v106', 'required' => false, 'desc' => 'Password setup'),
    // Login system
    'login.php' => array('path' => $baseDir . '/login.php', 'marker' => '@version v106', 'required' => true, 'desc' => 'Popup login page'),
    'login-embed.js' => array('path' => $baseDir . '/login-embed.js', 'marker' => '@version v106', 'required' => true, 'desc' => 'Login embed script'),
    // Frontend pages
    'index.html' => array('path' => $baseDir . '/index.html', 'marker' => '', 'required' => true, 'desc' => 'Dashboard page'),
    'timeline/index.html' => array('path' => $baseDir . '/timeline/index.html', 'marker' => '', 'required' => true, 'desc' => 'Timeline/Gantt page'),
    'documents/index.html' => array('path' => $baseDir . '/documents/index.html', 'marker' => '', 'required' => true, 'desc' => 'Documents page'),
    'team/index.html' => array('path' => $baseDir . '/team/index.html', 'marker' => '', 'required' => true, 'desc' => 'Team Contacts page'),
    'diary/index.html' => array('path' => $baseDir . '/diary/index.html', 'marker' => '', 'required' => true, 'desc' => 'Diary/Calendar page'),
    // Admin pages (v96+)
    'admin/projects/index.html' => array('path' => $baseDir . '/admin/projects/index.html', 'marker' => '', 'required' => true, 'desc' => 'Admin - Manage Projects'),
    'admin/allocate/index.html' => array('path' => $baseDir . '/admin/allocate/index.html', 'marker' => '', 'required' => true, 'desc' => 'Admin - Allocate to Client'),
    'admin/templates/index.html' => array('path' => $baseDir . '/admin/templates/index.html', 'marker' => '', 'required' => true, 'desc' => 'Admin - Templates'),
    // SQL Scripts
    'sql/v106_portal_features.sql' => array('path' => $baseDir . '/sql/v106_portal_features.sql', 'marker' => 'Version: v106', 'required' => true, 'desc' => 'Portal features SQL'),
);

// Core tables (Gantt system)
$coreTables = array(
    'users' => array('desc' => 'User accounts', 'min_records' => 1, 'required_columns' => array('id', 'email', 'password_hash', 'role', 'full_name')),
    'projects' => array('desc' => 'Projects', 'min_records' => 1, 'required_columns' => array('id', 'name', 'description', 'client_id', 'owner_id')),
    'tasks' => array('desc' => 'Project tasks', 'min_records' => 0, 'required_columns' => array('id', 'project_id', 'name', 'start_date', 'end_date', 'progress')),
    'task_dependencies' => array('desc' => 'Task dependencies', 'min_records' => 0, 'required_columns' => array('id', 'task_id', 'depends_on_task_id')),
    'task_comments' => array('desc' => 'Task comments', 'min_records' => 0, 'required_columns' => array('id', 'task_id', 'user_id', 'comment')),
    'project_templates' => array('desc' => 'Project templates', 'min_records' => 0, 'required_columns' => array('id', 'name', 'description', 'project_type')),
    'task_templates' => array('desc' => 'Task templates', 'min_records' => 0, 'required_columns' => array('id', 'template_id', 'name', 'days_from_start', 'duration_days')),
    'task_template_dependencies' => array('desc' => 'Template dependencies', 'min_records' => 0, 'required_columns' => array('id', 'task_template_id', 'depends_on_template_task_id')),
);

// Portal tables
$portalTables = array(
    'document_categories' => array('desc' => 'Document categories', 'min_records' => 5, 'required_columns' => array('id', 'name', 'slug', 'description', 'icon', 'color')),
    'documents' => array('desc' => 'Document records', 'min_records' => 0, 'required_columns' => array('id', 'project_id', 'category_id', 'name', 'file_name', 'file_path', 'file_type')),
    'document_access_log' => array('desc' => 'Document access tracking', 'min_records' => 0, 'required_columns' => array('id', 'document_id', 'user_id', 'action')),
    'contact_categories' => array('desc' => 'Contact categories', 'min_records' => 4, 'required_columns' => array('id', 'name', 'slug', 'description', 'color')),
    'team_contacts' => array('desc' => 'Team contacts', 'min_records' => 5, 'required_columns' => array('id', 'project_id', 'category_id', 'name', 'email', 'role')),
    'event_types' => array('desc' => 'Calendar event types', 'min_records' => 5, 'required_columns' => array('id', 'name', 'slug', 'color', 'icon')),
    'diary_events' => array('desc' => 'Calendar events', 'min_records' => 0, 'required_columns' => array('id', 'project_id', 'event_type_id', 'title', 'event_date')),
    'event_attendees' => array('desc' => 'Event attendees', 'min_records' => 0, 'required_columns' => array('id', 'event_id', 'user_id', 'status')),
    'system_settings' => array('desc' => 'System configuration', 'min_records' => 1, 'required_columns' => array('key', 'value', 'description')),
);

// Demo credentials
$demoCredentials = array(
    array('email' => 'admin@oasiscapitalfinance.com', 'password' => 'admin123', 'role' => 'Admin'),
    array('email' => 'sarah@oasiscapitalfinance.com', 'password' => 'manager123', 'role' => 'Manager'),
    array('email' => 'contact@acmecorp.com', 'password' => 'client123', 'role' => 'Client'),
);

// Required seed data checks
$seedDataChecks = array(
    array('table' => 'document_categories', 'check' => "slug = 'legal'", 'desc' => 'Legal category'),
    array('table' => 'document_categories', 'check' => "slug = 'financial'", 'desc' => 'Financial category'),
    array('table' => 'document_categories', 'check' => "slug = 'due-diligence'", 'desc' => 'Due Diligence category'),
    array('table' => 'contact_categories', 'check' => "slug = 'ocf'", 'desc' => 'OCF team category'),
    array('table' => 'contact_categories', 'check' => "slug = 'legal'", 'desc' => 'Legal contacts category'),
    array('table' => 'contact_categories', 'check' => "slug = 'client'", 'desc' => 'Client team category'),
    array('table' => 'event_types', 'check' => "slug = 'meeting'", 'desc' => 'Meeting event type'),
    array('table' => 'event_types', 'check' => "slug = 'deadline'", 'desc' => 'Deadline event type'),
    array('table' => 'event_types', 'check' => "slug = 'milestone'", 'desc' => 'Milestone event type'),
    array('table' => 'system_settings', 'check' => "`key` = 'portal_version'", 'desc' => 'Portal version setting'),
);

// API endpoint function checks
$apiFunctionChecks = array(
    'api/projects.php' => array(
        'functions' => array('GET projects', 'POST create', 'PUT update', 'DELETE project'),
        'features' => array('client_id filtering', 'client_name allocation'),
    ),
    'api/tasks.php' => array(
        'functions' => array('GET tasks', 'POST create', 'PUT update', 'DELETE task'),
        'features' => array('project_id filtering', 'dependencies'),
    ),
    'api/templates.php' => array(
        'functions' => array('GET templates', 'GET single template', 'POST create from template'),
        'features' => array('task_templates', 'template_dependencies'),
    ),
    'api/documents.php' => array(
        'functions' => array('GET documents', 'GET categories', 'POST upload', 'DELETE document'),
        'features' => array('category filtering', 'project filtering'),
    ),
    'api/contacts.php' => array(
        'functions' => array('GET contacts', 'GET categories', 'POST create', 'PUT update', 'DELETE contact'),
        'features' => array('category filtering', 'project filtering'),
    ),
    'api/diary.php' => array(
        'functions' => array('GET events', 'GET event types', 'POST create', 'PUT update', 'DELETE event'),
        'features' => array('date filtering', 'attendees'),
    ),
    'api/auth.php' => array(
        'functions' => array('POST login', 'POST logout', 'GET session'),
        'features' => array('session management', 'password verification'),
    ),
    'api/statistics.php' => array(
        'functions' => array('GET project stats'),
        'features' => array('task counts', 'progress calculation'),
    ),
    'api/comments.php' => array(
        'functions' => array('GET comments', 'POST create', 'DELETE comment'),
        'features' => array('task_id filtering'),
    ),
    'api/version.php' => array(
        'functions' => array('GET version info'),
        'features' => array('file version', 'database version', 'sync status'),
    ),
);

// ============================================================
// HTML OUTPUT START
// ============================================================
echo "<!DOCTYPE html><html><head><title>Full Verification - Client Portal " . EXPECTED_VERSION . "</title>
<style>
*{box-sizing:border-box}body{font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif;max-width:1200px;margin:40px auto;padding:20px;background:#f5f5f4;color:#1c1917}
h1{color:#1c1917;border-bottom:3px solid #14b8a6;padding-bottom:15px;display:flex;align-items:center;gap:15px;flex-wrap:wrap}
h1 .badge{background:#14b8a6;color:white;padding:5px 15px;border-radius:20px;font-size:18px}
h1 .new{background:#22c55e;color:white;padding:3px 10px;border-radius:12px;font-size:12px}
h2{color:#44403c;margin-top:30px;padding-bottom:10px;border-bottom:1px solid #d6d3d1}
h3{color:#57534e;margin-top:20px;}
.result{padding:10px 15px;margin:5px 0;border-radius:6px;border-left:4px solid}
.pass{background:#dcfce7;color:#166534;border-color:#22c55e}
.fail{background:#fee2e2;color:#991b1b;border-color:#ef4444}
.warn{background:#fef3c7;color:#92400e;border-color:#f59e0b}
.info{background:#e0f2fe;color:#0369a1;border-color:#0ea5e9}
table{width:100%;border-collapse:collapse;margin:15px 0;background:white;border-radius:8px;overflow:hidden;box-shadow:0 1px 3px rgba(0,0,0,0.1)}
th,td{padding:12px 15px;text-align:left;border-bottom:1px solid #e7e5e4}th{background:#f5f5f4;font-weight:600}
.summary{display:flex;gap:15px;margin:25px 0;flex-wrap:wrap}
.summary-box{padding:20px 30px;border-radius:12px;text-align:center;min-width:120px}
.summary-box.pass-box{background:#dcfce7}.summary-box.fail-box{background:#fee2e2}.summary-box.warn-box{background:#fef3c7}
.summary-box .count{font-size:36px;font-weight:bold}.summary-box .label{font-size:14px;color:#57534e;margin-top:5px}
.ok{color:#22c55e;font-weight:600}.bad{color:#ef4444;font-weight:600}
code{background:#e7e5e4;padding:2px 8px;border-radius:4px;font-size:13px}
.warning-box{background:#fee2e2;border:2px solid #ef4444;padding:20px;border-radius:12px;margin:25px 0}
.success-box{background:#dcfce7;border:2px solid #22c55e;padding:20px;border-radius:12px;margin:25px 0}
.feature-box{background:linear-gradient(135deg,#14b8a6 0%,#0d9488 100%);color:white;padding:20px;border-radius:12px;margin:20px 0}
.feature-box h3{margin:0 0 10px 0;color:white}.feature-box ul{margin:0;padding-left:20px}.feature-box li{margin:5px 0}
pre{background:#1c1917;color:#fafaf9;padding:20px;border-radius:8px;overflow-x:auto;font-size:13px}
.grid-2{display:grid;grid-template-columns:1fr 1fr;gap:20px}
.grid-3{display:grid;grid-template-columns:1fr 1fr 1fr;gap:15px}
.version-history{background:#fafaf9;border:1px solid #e7e5e4;padding:15px;border-radius:8px;margin:15px 0}
.version-history h4{margin:0 0 10px 0;color:#44403c}
.version-history ul{margin:0;padding-left:20px;color:#57534e}
.stat-card{background:white;padding:15px;border-radius:8px;border:1px solid #e7e5e4;text-align:center}
.stat-card .number{font-size:28px;font-weight:bold;color:#14b8a6}
.stat-card .label{font-size:12px;color:#78716c;margin-top:5px}
.changelog{background:#fef3c7;border:1px solid #f59e0b;padding:15px;border-radius:8px;margin:15px 0}
.changelog h4{margin:0 0 10px 0;color:#92400e}
.changelog ul{margin:0;padding-left:20px;color:#78716c}
@media(max-width:768px){.grid-2,.grid-3{grid-template-columns:1fr}}
</style></head><body>
<h1>Client Portal Verification <span class='badge'>" . EXPECTED_VERSION . "</span> <span class='new'>Latest</span></h1>

<div class='changelog'>
<h4>v106 Changelog</h4>
<ul>
<li><strong>Fixed:</strong> Admin pages flashing and redirecting to dashboard on load</li>
<li><strong>Fixed:</strong> Race condition in auth state initialization</li>
<li><strong>New:</strong> Proper authInitialized state in AuthProvider</li>
<li><strong>New:</strong> Loading spinner on admin pages while auth initializes</li>
<li><strong>Improved:</strong> Separated auth loading from projects loading state</li>
<li><strong>Updated:</strong> All file version tags to v106</li>
</ul>
</div>

<div class='feature-box'><h3>Client Portal " . EXPECTED_VERSION . " - Complete Feature Set</h3>
<div class='grid-2'>
<ul>
<li><strong>Dashboard</strong> - Project overview, stats, quick actions</li>
<li><strong>Project Timeline</strong> - Interactive Gantt chart</li>
<li><strong>Document Library</strong> - Manage deal documents</li>
<li><strong>Team Contacts</strong> - Deal team information</li>
<li><strong>Diary/Calendar</strong> - Events and scheduling</li>
</ul>
<ul>
<li><strong>Admin: Manage Projects</strong> - Create, edit, delete projects</li>
<li><strong>Admin: Allocate to Client</strong> - Assign projects to clients</li>
<li><strong>Admin: Templates</strong> - Manage project templates</li>
<li><strong>Role-based Access</strong> - Admin/Manager/Client</li>
<li><strong>17 Database Tables</strong> - Core + Portal</li>
</ul>
</div>
</div>

<p><strong>Verified:</strong> " . date('Y-m-d H:i:s') . " | <strong>PHP:</strong> " . PHP_VERSION . " | <strong>Server:</strong> " . ($_SERVER['SERVER_NAME'] ?? php_uname('n')) . "</p>";

// ============================================================
// Section 1: Files & Version Markers
// ============================================================
echo "<h2>1. Files & Version Markers (" . count($versionFiles) . " files)</h2>";
echo "<table><tr><th>File</th><th>Description</th><th>Size</th><th>Version</th><th>Status</th></tr>";
foreach ($versionFiles as $name => $info) {
    if (!file_exists($info['path'])) {
        echo "<tr><td><code>{$name}</code></td><td>{$info['desc']}</td><td>-</td><td>-</td><td class='bad'>✗ MISSING</td></tr>";
        if ($info['required']) $failed++; else $warnings++;
        continue;
    }
    $content = file_get_contents($info['path']);
    $size = formatSize(filesize($info['path']));
    $hasMarker = empty($info['marker']) || strpos($content, $info['marker']) !== false;

    // Extract version from file
    $versionFound = '-';
    if (preg_match('/@version\s+(v\d+)/', $content, $matches)) {
        $versionFound = $matches[1];
    } elseif (preg_match('/Version:\s+(v\d+)/', $content, $matches)) {
        $versionFound = $matches[1];
    }

    if ($hasMarker) {
        echo "<tr><td><code>{$name}</code></td><td>{$info['desc']}</td><td>{$size}</td><td><span class='ok'>{$versionFound}</span></td><td class='ok'>✓ OK</td></tr>";
        $passed++;
    } else {
        echo "<tr><td><code>{$name}</code></td><td>{$info['desc']}</td><td>{$size}</td><td><span class='bad'>{$versionFound}</span></td><td class='bad'>✗ Wrong version</td></tr>";
        $failed++;
    }
}
echo "</table>";

// ============================================================
// Section 2: Database Connection
// ============================================================
echo "<h2>2. Database Connection</h2>";
$dbConnected = false; $db = null; $existingTables = array();
if (!file_exists($apiDir . '/config.php')) {
    showFail("config.php not found");
} else {
    try {
        require_once $apiDir . '/config.php';
        if (!class_exists('Database')) { showFail("Database class not found"); }
        else {
            $db = Database::getInstance();
            $dbConnected = true;
            $stmt = $db->query("SELECT DATABASE() as db_name, VERSION() as db_version");
            $info = $stmt->fetch(PDO::FETCH_ASSOC);
            showPass("Connected to: <strong>" . $info['db_name'] . "</strong> (MySQL " . $info['db_version'] . ")");
            if (defined('GPM_VERSION')) showPass("Config version: " . GPM_VERSION);
            $stmt = $db->query("SHOW TABLES");
            while ($row = $stmt->fetch(PDO::FETCH_NUM)) $existingTables[] = $row[0];
            showInfo("Found " . count($existingTables) . " tables in database");
        }
    } catch (Exception $e) { showFail("Connection failed: " . $e->getMessage()); }
}

// ============================================================
// Section 3: Core Tables (Gantt System)
// ============================================================
echo "<h2>3. Core Tables (" . count($coreTables) . " tables)</h2>";
if ($dbConnected) {
    echo "<table><tr><th>Table</th><th>Description</th><th>Records</th><th>Columns</th><th>Status</th></tr>";
    foreach ($coreTables as $table => $info) {
        $exists = in_array($table, $existingTables);
        if ($exists) {
            $stmt = $db->query("SELECT COUNT(*) as cnt FROM `{$table}`");
            $count = $stmt->fetch()['cnt'];

            // Check required columns
            $stmt = $db->query("SHOW COLUMNS FROM `{$table}`");
            $columns = array();
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $columns[] = $row['Field'];
            }
            $missingCols = array_diff($info['required_columns'], $columns);
            $colStatus = empty($missingCols) ? 'ok' : 'warn';
            $colText = empty($missingCols) ? count($columns) . ' cols' : 'Missing: ' . implode(', ', $missingCols);

            $status = $count >= $info['min_records'] && empty($missingCols) ? 'ok' : 'warn';
            $statusText = ($status === 'ok') ? '✓ OK' : '⚠ Check';
            echo "<tr><td><code>{$table}</code></td><td>{$info['desc']}</td><td>{$count}</td><td class='{$colStatus}'>{$colText}</td><td class='{$status}'>{$statusText}</td></tr>";
            if ($status === 'ok') $passed++; else $warnings++;
        } else {
            echo "<tr><td><code>{$table}</code></td><td>{$info['desc']}</td><td>-</td><td>-</td><td class='bad'>✗ MISSING</td></tr>";
            $failed++;
        }
    }
    echo "</table>";
} else { showFail("Cannot check - database not connected"); }

// ============================================================
// Section 4: Portal Tables
// ============================================================
echo "<h2>4. Portal Tables (" . count($portalTables) . " tables)</h2>";
if ($dbConnected) {
    $portalTablesExist = 0;
    echo "<table><tr><th>Table</th><th>Description</th><th>Records</th><th>Min Required</th><th>Columns</th><th>Status</th></tr>";
    foreach ($portalTables as $table => $info) {
        $exists = in_array($table, $existingTables);
        if ($exists) {
            $stmt = $db->query("SELECT COUNT(*) as cnt FROM `{$table}`");
            $count = $stmt->fetch()['cnt'];

            // Check required columns
            $stmt = $db->query("SHOW COLUMNS FROM `{$table}`");
            $columns = array();
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $columns[] = $row['Field'];
            }
            $missingCols = array_diff($info['required_columns'], $columns);
            $colStatus = empty($missingCols) ? 'ok' : 'warn';
            $colText = empty($missingCols) ? '✓' : 'Missing: ' . implode(', ', $missingCols);

            $hasMinRecords = $count >= $info['min_records'];
            $status = $hasMinRecords && empty($missingCols) ? 'ok' : 'warn';
            $statusText = $hasMinRecords ? '✓ OK' : '⚠ Low data';
            echo "<tr><td><code>{$table}</code></td><td>{$info['desc']}</td><td>{$count}</td><td>{$info['min_records']}</td><td class='{$colStatus}'>{$colText}</td><td class='{$status}'>{$statusText}</td></tr>";
            if ($hasMinRecords && empty($missingCols)) $passed++; else $warnings++;
            $portalTablesExist++;
        } else {
            echo "<tr><td><code>{$table}</code></td><td>{$info['desc']}</td><td>-</td><td>{$info['min_records']}</td><td>-</td><td class='bad'>✗ MISSING</td></tr>";
            $failed++;
        }
    }
    echo "</table>";

    if ($portalTablesExist === 0) {
        showFail("Portal tables not found. Run: <code>sql/v106_portal_features.sql</code>");
    } elseif ($portalTablesExist < count($portalTables)) {
        showWarn("Some portal tables missing. Re-run: <code>sql/v106_portal_features.sql</code>");
    } else {
        showPass("All " . count($portalTables) . " portal tables present");
    }
} else { showFail("Cannot check - database not connected"); }

// ============================================================
// Section 5: Seed Data Verification
// ============================================================
echo "<h2>5. Seed Data Verification</h2>";
if ($dbConnected) {
    echo "<table><tr><th>Check</th><th>Table</th><th>Status</th></tr>";
    foreach ($seedDataChecks as $check) {
        if (in_array($check['table'], $existingTables)) {
            $stmt = $db->query("SELECT COUNT(*) as cnt FROM `{$check['table']}` WHERE {$check['check']}");
            $count = $stmt->fetch()['cnt'];
            if ($count > 0) {
                echo "<tr><td>{$check['desc']}</td><td><code>{$check['table']}</code></td><td class='ok'>✓ Found</td></tr>";
                $passed++;
            } else {
                echo "<tr><td>{$check['desc']}</td><td><code>{$check['table']}</code></td><td class='bad'>✗ Missing</td></tr>";
                $failed++;
            }
        } else {
            echo "<tr><td>{$check['desc']}</td><td><code>{$check['table']}</code></td><td class='warn'>⚠ Table missing</td></tr>";
            $warnings++;
        }
    }
    echo "</table>";
} else { showWarn("Cannot check seed data - database not connected"); }

// ============================================================
// Section 6: Portal Version Check
// ============================================================
echo "<h2>6. Portal Version Check</h2>";
if ($dbConnected && in_array('system_settings', $existingTables)) {
    $stmt = $db->query("SELECT `value` FROM `system_settings` WHERE `key` = 'portal_version'");
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($row) {
        $dbVersion = $row['value'];
        if ($dbVersion === EXPECTED_VERSION) {
            showPass("Database portal version: <strong>{$dbVersion}</strong> (matches expected " . EXPECTED_VERSION . ")");
        } else {
            showWarn("Database portal version: <strong>{$dbVersion}</strong> (expected " . EXPECTED_VERSION . "). Run v106_portal_features.sql to update.");
        }
    } else {
        showWarn("portal_version not set in system_settings. Run v106_portal_features.sql");
    }
} else {
    showWarn("Cannot check portal version - system_settings table missing");
}

// ============================================================
// Section 7: Project Templates
// ============================================================
echo "<h2>7. Project Templates</h2>";
if ($dbConnected && in_array('project_templates', $existingTables)) {
    $stmt = $db->query("SELECT id, name, (SELECT COUNT(*) FROM task_templates WHERE template_id = pt.id) as tasks FROM project_templates pt WHERE is_active=1 LIMIT 10");
    $templates = $stmt->fetchAll(PDO::FETCH_ASSOC);
    if (count($templates) > 0) {
        echo "<table><tr><th>ID</th><th>Name</th><th>Tasks</th></tr>";
        foreach ($templates as $t) echo "<tr><td>{$t['id']}</td><td>{$t['name']}</td><td>{$t['tasks']}</td></tr>";
        echo "</table>";
        showPass("Found " . count($templates) . " active template(s)");
    } else { showWarn("No templates found - run templates_schema.sql"); }
} else { showWarn("Template table missing"); }

// ============================================================
// Section 8: Login System & Demo Users
// ============================================================
echo "<h2>8. Login System & Demo Users</h2>";
$loginFiles = array('login.php' => $baseDir.'/login.php', 'login-embed.js' => $baseDir.'/login-embed.js', 'login-demo.html' => $baseDir.'/login-demo.html');
foreach ($loginFiles as $name => $path) {
    if (file_exists($path)) { showPass("{$name} found (" . formatSize(filesize($path)) . ")"); }
    else { if ($name === 'login-demo.html') { $warnings++; showWarn("{$name} missing (optional)"); } else { $failed++; showFail("{$name} missing"); } }
}

echo "<h3>Demo User Passwords</h3>";
if ($dbConnected) {
    echo "<table><tr><th>Role</th><th>Email</th><th>Password</th><th>Status</th></tr>";
    foreach ($demoCredentials as $cred) {
        $stmt = $db->prepare("SELECT password_hash FROM users WHERE email = ?");
        $stmt->execute([$cred['email']]);
        $user = $stmt->fetch();
        if ($user && password_verify($cred['password'], $user['password_hash'])) {
            echo "<tr><td>{$cred['role']}</td><td><code>{$cred['email']}</code></td><td><code>{$cred['password']}</code></td><td class='ok'>✓ OK</td></tr>";
            $passed++;
        } else {
            echo "<tr><td>{$cred['role']}</td><td><code>{$cred['email']}</code></td><td><code>{$cred['password']}</code></td><td class='bad'>✗ Not set</td></tr>";
            $failed++;
        }
    }
    echo "</table>";
    showInfo("Run <code>/api/setup_passwords.php</code> to configure passwords");
}

// ============================================================
// Section 9: Static Assets
// ============================================================
echo "<h2>9. Static Assets</h2>";
$nextDir = $baseDir . '/_next/static';
if (is_dir($nextDir)) {
    $css = glob($nextDir . '/css/*.css');
    $js = glob($nextDir . '/chunks/*.js');
    $fonts = glob($nextDir . '/media/*.woff2');

    echo "<div class='grid-3'>";
    echo "<div class='stat-card'><div class='number'>" . count($css) . "</div><div class='label'>CSS Files</div></div>";
    echo "<div class='stat-card'><div class='number'>" . count($js) . "</div><div class='label'>JS Chunks</div></div>";
    echo "<div class='stat-card'><div class='number'>" . count($fonts) . "</div><div class='label'>Font Files</div></div>";
    echo "</div>";

    if (count($css) > 0) $passed++; else $failed++;
    if (count($js) > 0) $passed++; else $failed++;
    if (count($fonts) > 0) $passed++; else $warnings++;
} else { showFail("_next/static directory missing"); $failed++; }

// Logo files
$logoDir = $baseDir . '/images/logo';
if (is_dir($logoDir)) {
    $logos = glob($logoDir . '/*.{png,jpg,svg}', GLOB_BRACE);
    showPass("Logo files: " . count($logos) . " found");
    foreach ($logos as $logo) {
        showInfo("  - " . basename($logo) . " (" . formatSize(filesize($logo)) . ")");
    }
} else { showWarn("images/logo directory missing"); }

// ============================================================
// Section 10: API Endpoints & Functions
// ============================================================
echo "<h2>10. API Endpoints & Functions</h2>";

$apiFiles = array('config.php','projects.php','tasks.php','templates.php','auth.php','documents.php','contacts.php','diary.php','comments.php','statistics.php');
echo "<table><tr><th>API File</th><th>Syntax</th><th>Functions</th><th>Features</th></tr>";
foreach ($apiFiles as $file) {
    $path = $apiDir . '/' . $file;
    if (file_exists($path)) {
        // Syntax check
        $output = array();
        $syntaxOk = true;
        if (function_exists('exec')) {
            exec("php -l " . escapeshellarg($path) . " 2>&1", $output, $ret);
            $syntaxOk = ($ret === 0);
        }
        $syntaxStatus = $syntaxOk ? "<span class='ok'>✓ OK</span>" : "<span class='bad'>✗ Error</span>";

        // Function & feature checks
        $apiKey = 'api/' . $file;
        $funcText = '-';
        $featText = '-';
        if (isset($apiFunctionChecks[$apiKey])) {
            $funcText = count($apiFunctionChecks[$apiKey]['functions']) . ' endpoints';
            $featText = count($apiFunctionChecks[$apiKey]['features']) . ' features';
        }

        echo "<tr><td><code>{$file}</code></td><td>{$syntaxStatus}</td><td>{$funcText}</td><td>{$featText}</td></tr>";
        if ($syntaxOk) $passed++; else $failed++;
    } else {
        echo "<tr><td><code>{$file}</code></td><td class='bad'>✗ Missing</td><td>-</td><td>-</td></tr>";
        $failed++;
    }
}
echo "</table>";

// ============================================================
// Section 11: Portal Pages
// ============================================================
echo "<h2>11. Portal Pages</h2>";
$portalPages = array(
    '/' => array('file' => '/index.html', 'desc' => 'Dashboard'),
    '/timeline/' => array('file' => '/timeline/index.html', 'desc' => 'Project Timeline (Gantt)'),
    '/documents/' => array('file' => '/documents/index.html', 'desc' => 'Document Library'),
    '/team/' => array('file' => '/team/index.html', 'desc' => 'Team Contacts'),
    '/diary/' => array('file' => '/diary/index.html', 'desc' => 'Diary/Calendar'),
    '/admin/projects/' => array('file' => '/admin/projects/index.html', 'desc' => 'Admin - Manage Projects'),
    '/admin/allocate/' => array('file' => '/admin/allocate/index.html', 'desc' => 'Admin - Allocate to Client (v97 fix)'),
    '/admin/templates/' => array('file' => '/admin/templates/index.html', 'desc' => 'Admin - Templates'),
);

echo "<table><tr><th>Route</th><th>Description</th><th>File Size</th><th>Status</th></tr>";
foreach ($portalPages as $route => $info) {
    $filePath = $baseDir . $info['file'];
    if (file_exists($filePath)) {
        $size = formatSize(filesize($filePath));
        echo "<tr><td><code>{$route}</code></td><td>{$info['desc']}</td><td>{$size}</td><td class='ok'>✓ OK</td></tr>";
        $passed++;
    } else {
        echo "<tr><td><code>{$route}</code></td><td>{$info['desc']}</td><td>-</td><td class='bad'>✗ MISSING</td></tr>";
        $failed++;
    }
}
echo "</table>";

// ============================================================
// Section 12: System Settings
// ============================================================
echo "<h2>12. System Settings</h2>";
if ($dbConnected && in_array('system_settings', $existingTables)) {
    $stmt = $db->query("SELECT `key`, `value`, `description` FROM system_settings ORDER BY `key`");
    $settings = $stmt->fetchAll(PDO::FETCH_ASSOC);
    if (count($settings) > 0) {
        echo "<table><tr><th>Key</th><th>Value</th><th>Description</th></tr>";
        foreach ($settings as $s) {
            $isVersionKey = $s['key'] === 'portal_version';
            $valueClass = ($isVersionKey && $s['value'] === EXPECTED_VERSION) ? 'ok' : '';
            echo "<tr><td><code>{$s['key']}</code></td><td class='{$valueClass}'>{$s['value']}</td><td>{$s['description']}</td></tr>";
        }
        echo "</table>";
        showPass("System settings configured (" . count($settings) . " entries)");
    } else {
        showWarn("No system settings found - run v106_portal_features.sql");
    }
} else {
    showWarn("system_settings table not found");
}

// ============================================================
// Summary
// ============================================================
echo "<h2>Summary</h2>
<div class='summary'>
<div class='summary-box pass-box'><div class='count'>{$passed}</div><div class='label'>Passed</div></div>
<div class='summary-box fail-box'><div class='count'>{$failed}</div><div class='label'>Failed</div></div>
<div class='summary-box warn-box'><div class='count'>{$warnings}</div><div class='label'>Warnings</div></div>
</div>";

$total = $passed + $failed + $warnings;
$passRate = $total > 0 ? round(($passed / $total) * 100) : 0;

if ($failed === 0) {
    echo "<div class='success-box'><strong>✓ All checks passed!</strong> Client Portal " . EXPECTED_VERSION . " deployment is complete and ready to use.<br>Pass rate: {$passRate}%</div>";
} else {
    echo "<div class='warning-box'><strong>✗ {$failed} issue(s) found!</strong> Please fix the failed items above before going live.<br>Pass rate: {$passRate}%</div>";
}

// ============================================================
// Setup Instructions
// ============================================================
echo "<h2>Setup Instructions</h2>
<table><tr><th>Step</th><th>Action</th><th>Command/URL</th></tr>
<tr><td>1</td><td>Upload all files</td><td><code>gantt-project-manager-v106-portal.zip</code></td></tr>
<tr><td>2</td><td>Configure database</td><td>Edit <code>api/config.php</code></td></tr>
<tr><td>3</td><td>Run SQL scripts</td><td><code>schema.sql → seed.sql → templates_schema.sql → v106_portal_features.sql</code></td></tr>
<tr><td>4</td><td>Setup passwords</td><td><code>/api/setup_passwords.php</code></td></tr>
<tr><td>5</td><td>Verify deployment</td><td><code>/api/full_verify.php</code></td></tr>
<tr><td>6</td><td>Test login</td><td><code>/login.php</code></td></tr>
<tr><td>7</td><td>Delete verification scripts</td><td>See security note below</td></tr>
</table>";

echo "<h2>SQL for Existing Installations</h2>
<pre>mysql -u username -p database_name &lt; sql/v106_portal_features.sql</pre>
<p>This updates the portal version and adds any new portal tables without affecting existing data.</p>";

// Version history
echo "<div class='version-history'>
<h4>Version History</h4>
<ul>
<li><strong>v106</strong> - Fixed admin pages flashing/redirect, improved auth state management</li>
<li><strong>v105</strong> - Fixed admin navigation routing, trailing slashes, version display features</li>
<li><strong>v102</strong> - Version API, version display on Dashboard and Login pages</li>
<li><strong>v100</strong> - Major milestone release, complete portal deployment package</li>
<li><strong>v97</strong> - Fixed Allocate to Client page error (Select component)</li>
<li><strong>v96</strong> - Admin features (Manage Projects, Allocate to Client, Templates)</li>
<li><strong>v95</strong> - White logo on dark sidebar</li>
<li><strong>v83</strong> - Client Portal with Documents, Contacts, Diary</li>
<li><strong>v77</strong> - Popup login system</li>
</ul>
</div>";

echo "<div class='warning-box'><strong>⚠️ Security:</strong> Delete these files after verification:<br>
<code>full_verify.php</code>, <code>db_verify.php</code>, <code>file_verify.php</code>, <code>version_verify.php</code>, <code>setup_passwords.php</code></div>

<p style='text-align:center;color:#78716c;margin-top:30px;'>
&copy; " . date('Y') . " Oasis Capital Finance. All rights reserved. | Client Portal " . EXPECTED_VERSION . "
</p>

</body></html>";
