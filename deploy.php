<?php
/**
 * ============================================================
 * KAATIB PUBLISHERS - PREMIUM GIT DEPLOYMENT DASHBOARD
 * ============================================================
 * Single-file PHP deployment system with glassmorphism UI
 * Features: Git operations, commit history, terminal logs
 * ============================================================
 */

session_start();

// ============================================================
// CONFIGURATION
// ============================================================
define('ADMIN_PASSWORD', '1234');
define('REPO_PATH', '/home/noorgeec/noorgee.pk/kp');
define('BRANCH', 'main-kp');
define('REPO_URL', 'https://github.com/stayoraguesthouse-code/kaatib-rep.git');
define('BACKUP_DIR', REPO_PATH . '/backups');
define('TIMEZONE', 'Asia/Karachi');
define('SESSION_TIMEOUT', 3600); // 1 hour
define('COMMIT_HISTORY_LIMIT', 50);
define('AUTO_REFRESH_INTERVAL', 3600); // 1 hour in seconds

date_default_timezone_set(TIMEZONE);

// ============================================================
// SESSION MANAGEMENT
// ============================================================
if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: ' . $_SERVER['PHP_SELF']);
    exit;
}

if (!isset($_SESSION['authenticated'])) {
    $_SESSION['authenticated'] = false;
}

if (isset($_POST['password'])) {
    if ($_POST['password'] === ADMIN_PASSWORD) {
        $_SESSION['authenticated'] = true;
        $_SESSION['login_time'] = time();
    } else {
        $login_error = 'Invalid password. Please try again.';
    }
}

// Check session timeout
if ($_SESSION['authenticated'] && time() - $_SESSION['login_time'] > SESSION_TIMEOUT) {
    session_destroy();
    $_SESSION['authenticated'] = false;
}

// ============================================================
// HELPER FUNCTIONS
// ============================================================

function sanitize_output($text) {
    return htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
}

function execute_command($command) {
    $output = [];
    $return_code = 0;
    exec($command . ' 2>&1', $output, $return_code);
    return [
        'output' => implode("\n", $output),
        'code' => $return_code,
        'success' => $return_code === 0
    ];
}

function get_git_status() {
    $status = [
        'branch' => 'Unknown',
        'commit' => 'Unknown',
        'commit_title' => 'Unknown',
        'commit_time' => 'Unknown',
        'modified_files' => 0,
        'connection' => 'Disconnected'
    ];

    $cwd = getcwd();
    chdir(REPO_PATH);

    // Get current branch
    $branch_result = execute_command('git rev-parse --abbrev-ref HEAD');
    if ($branch_result['success']) {
        $status['branch'] = trim($branch_result['output']);
    }

    // Get current commit hash
    $commit_result = execute_command('git rev-parse --short HEAD');
    if ($commit_result['success']) {
        $status['commit'] = trim($commit_result['output']);
    }

    // Get commit title
    $title_result = execute_command('git log -1 --pretty=%B');
    if ($title_result['success']) {
        $lines = explode("\n", trim($title_result['output']));
        $status['commit_title'] = $lines[0];
    }

    // Get commit time
    $time_result = execute_command('git log -1 --pretty=%ci');
    if ($time_result['success']) {
        $timestamp = strtotime(trim($time_result['output']));
        $status['commit_time'] = date('D d-M-Y h:i A', $timestamp);
    }

    // Get modified files count
    $modified_result = execute_command('git status --porcelain');
    if ($modified_result['success']) {
        $status['modified_files'] = count(array_filter(explode("\n", trim($modified_result['output']))));
    }

    // Check git connection
    $connection_result = execute_command('git remote -v');
    $status['connection'] = $connection_result['success'] ? 'Connected' : 'Disconnected';

    chdir($cwd);
    return $status;
}

function get_commit_history() {
    $cwd = getcwd();
    chdir(REPO_PATH);

    $result = execute_command('git log --pretty=format:"%H|%s|%b|%ci" -n ' . COMMIT_HISTORY_LIMIT);
    $commits = [];

    if ($result['success'] && !empty($result['output'])) {
        $lines = explode("\n", trim($result['output']));
        foreach ($lines as $line) {
            if (empty($line)) continue;
            $parts = explode('|', $line, 4);
            if (count($parts) >= 3) {
                $timestamp = strtotime($parts[3]);
                $commits[] = [
                    'hash' => substr($parts[0], 0, 7),
                    'title' => $parts[1],
                    'description' => trim($parts[2]),
                    'time' => date('D d-M-Y h:i A', $timestamp),
                    'relative' => get_relative_time($timestamp)
                ];
            }
        }
    }

    chdir($cwd);
    return $commits;
}

function get_relative_time($timestamp) {
    $diff = time() - $timestamp;
    
    if ($diff < 60) return 'Just now';
    if ($diff < 3600) return floor($diff / 60) . ' minutes ago';
    if ($diff < 86400) return floor($diff / 3600) . ' hours ago';
    if ($diff < 604800) return floor($diff / 86400) . ' days ago';
    if ($diff < 2592000) return floor($diff / 604800) . ' weeks ago';
    return floor($diff / 2592000) . ' months ago';
}

function get_last_deploy_time() {
    if (file_exists(REPO_PATH . '/.last_deploy')) {
        $time = file_get_contents(REPO_PATH . '/.last_deploy');
        $timestamp = (int)$time;
        return date('D d-M-Y h:i A', $timestamp);
    }
    return 'Never';
}

function update_deploy_time() {
    file_put_contents(REPO_PATH . '/.last_deploy', time());
}

function get_modified_files() {
    $cwd = getcwd();
    chdir(REPO_PATH);

    $result = execute_command('git status --porcelain');
    $files = [];

    if ($result['success'] && !empty($result['output'])) {
        $lines = explode("\n", trim($result['output']));
        foreach ($lines as $line) {
            if (!empty($line)) {
                $files[] = trim($line);
            }
        }
    }

    chdir($cwd);
    return $files;
}

function get_disk_usage() {
    $size = disk_free_space(REPO_PATH);
    if ($size === false) return 'Unknown';
    
    $units = ['B', 'KB', 'MB', 'GB', 'TB'];
    $size = max($size, 0);
    $pow = floor(($size ? log($size) : 0) / log(1024));
    $pow = min($pow, count($units) - 1);
    $size /= (1 << (10 * $pow));
    
    return round($size, 2) . ' ' . $units[$pow];
}

function create_backup() {
    if (!is_dir(BACKUP_DIR)) {
        mkdir(BACKUP_DIR, 0755, true);
    }

    $backup_name = 'backup_' . date('Y-m-d_H-i-s') . '.zip';
    $backup_path = BACKUP_DIR . '/' . $backup_name;

    $cwd = getcwd();
    chdir(REPO_PATH);

    $command = 'zip -r ' . escapeshellarg($backup_path) . ' . -x "*.git/*" "backups/*"';
    $result = execute_command($command);

    chdir($cwd);

    return [
        'success' => $result['success'],
        'file' => $backup_name,
        'path' => $backup_path,
        'output' => $result['output']
    ];
}

function get_backups() {
    $backups = [];
    if (is_dir(BACKUP_DIR)) {
        $files = scandir(BACKUP_DIR, SCANDIR_SORT_DESCENDING);
        foreach ($files as $file) {
            if (strpos($file, 'backup_') === 0 && strpos($file, '.zip') !== false) {
                $backups[] = [
                    'name' => $file,
                    'path' => BACKUP_DIR . '/' . $file,
                    'size' => filesize(BACKUP_DIR . '/' . $file),
                    'time' => filemtime(BACKUP_DIR . '/' . $file)
                ];
            }
        }
    }
    return $backups;
}

// ============================================================
// HANDLE AJAX REQUESTS
// ============================================================

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_SESSION['authenticated']) {
    header('Content-Type: application/json');
    $cwd = getcwd();
    chdir(REPO_PATH);

    $response = ['success' => false, 'output' => '', 'message' => ''];

    switch ($_POST['action']) {
        case 'pull':
            $commands = [
                'git fetch origin',
                'git checkout ' . BRANCH,
                'git pull origin ' . BRANCH
            ];
            $output = [];
            foreach ($commands as $cmd) {
                $result = execute_command($cmd);
                $output[] = $result['output'];
            }
            $response['output'] = implode("\n", $output);
            $response['success'] = true;
            update_deploy_time();
            break;

        case 'force_pull':
            $commands = [
                'git fetch origin',
                'git reset --hard origin/' . BRANCH,
                'git clean -fd'
            ];
            $output = [];
            foreach ($commands as $cmd) {
                $result = execute_command($cmd);
                $output[] = $result['output'];
            }
            $response['output'] = implode("\n", $output);
            $response['success'] = true;
            update_deploy_time();
            break;

        case 'push':
            $title = sanitize_output($_POST['title'] ?? '');
            $description = sanitize_output($_POST['description'] ?? '');
            
            if (empty($title)) {
                $response['message'] = 'Commit title is required';
                break;
            }

            $commit_message = $title;
            if (!empty($description)) {
                $commit_message .= "\n\n" . $description;
            }

            $commands = [
                'git add .',
                'git commit -m ' . escapeshellarg($commit_message),
                'git push origin ' . BRANCH
            ];
            $output = [];
            foreach ($commands as $cmd) {
                $result = execute_command($cmd);
                $output[] = $result['output'];
            }
            $response['output'] = implode("\n", $output);
            $response['success'] = true;
            update_deploy_time();
            break;

        case 'undo':
            $result = execute_command('git reset --hard HEAD');
            $response['output'] = $result['output'];
            $response['success'] = $result['success'];
            break;

        case 'revert':
            $result = execute_command('git revert HEAD --no-edit');
            $response['output'] = $result['output'];
            $response['success'] = $result['success'];
            update_deploy_time();
            break;

        case 'restore':
            $commit = $_POST['commit'] ?? '';
            if (empty($commit)) {
                $response['message'] = 'Commit hash is required';
                break;
            }
            $result = execute_command('git reset --hard ' . escapeshellarg($commit));
            $response['output'] = $result['output'];
            $response['success'] = $result['success'];
            update_deploy_time();
            break;

        case 'restore_latest':
            $result = execute_command('git reset --hard origin/' . BRANCH);
            $response['output'] = $result['output'];
            $response['success'] = $result['success'];
            update_deploy_time();
            break;

        case 'backup':
            $backup = create_backup();
            $response['success'] = $backup['success'];
            $response['output'] = $backup['output'];
            $response['message'] = $backup['file'];
            break;

        case 'status':
            $status = get_git_status();
            $response['success'] = true;
            $response['status'] = $status;
            break;

        case 'get_last_commit':
            $commits = get_commit_history();
            if (!empty($commits)) {
                $response['success'] = true;
                $response['title'] = $commits[0]['title'];
                $response['description'] = $commits[0]['description'];
            }
            break;
    }

    chdir($cwd);
    echo json_encode($response);
    exit;
}

// ============================================================
// GET INITIAL DATA
// ============================================================

$git_status = get_git_status();
$commit_history = get_commit_history();
$modified_files = get_modified_files();
$last_deploy = get_last_deploy_time();
$backups = get_backups();

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kaatib Publishers - Deployment Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        :root {
            --primary: #1a2a6c;
            --secondary: #f2a900;
            --dark: #0f1419;
            --darker: #0a0e13;
            --card: rgba(30, 41, 82, 0.3);
            --border: rgba(242, 169, 0, 0.2);
        }

        body {
            background: linear-gradient(135deg, var(--darker) 0%, #1a2a6c 50%, #0f1419 100%);
            background-attachment: fixed;
            color: #e0e0e0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            overflow-x: hidden;
        }

        /* Animated Background */
        .animated-bg {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -1;
            background: linear-gradient(135deg, var(--darker) 0%, #1a2a6c 50%, #0f1419 100%);
        }

        .particle {
            position: absolute;
            background: radial-gradient(circle, var(--secondary) 0%, transparent 70%);
            border-radius: 50%;
            opacity: 0.1;
            animation: float 20s infinite;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0) translateX(0); }
            50% { transform: translateY(-20px) translateX(10px); }
        }

        /* Glassmorphism */
        .glass {
            background: var(--card);
            backdrop-filter: blur(10px);
            border: 1px solid var(--border);
            border-radius: 12px;
        }

        .glass-dark {
            background: rgba(15, 20, 25, 0.6);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(242, 169, 0, 0.1);
            border-radius: 12px;
        }

        /* Glow Effects */
        .glow {
            box-shadow: 0 0 20px rgba(242, 169, 0, 0.3);
        }

        .glow-hover:hover {
            box-shadow: 0 0 30px rgba(242, 169, 0, 0.5);
            transition: all 0.3s ease;
        }

        /* Buttons */
        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--secondary) 0%, #e69500 100%);
            color: #000;
            box-shadow: 0 0 15px rgba(242, 169, 0, 0.4);
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 0 25px rgba(242, 169, 0, 0.6);
        }

        .btn-secondary {
            background: rgba(26, 42, 108, 0.8);
            color: var(--secondary);
            border: 2px solid var(--secondary);
        }

        .btn-secondary:hover {
            background: var(--secondary);
            color: #000;
        }

        .btn-danger {
            background: rgba(220, 38, 38, 0.8);
            color: #fff;
            border: 2px solid #dc2626;
        }

        .btn-danger:hover {
            background: #dc2626;
            box-shadow: 0 0 20px rgba(220, 38, 38, 0.5);
        }

        .btn-success {
            background: rgba(34, 197, 94, 0.8);
            color: #fff;
            border: 2px solid #22c55e;
        }

        .btn-success:hover {
            background: #22c55e;
            box-shadow: 0 0 20px rgba(34, 197, 94, 0.5);
        }

        .btn-warning {
            background: rgba(234, 179, 8, 0.8);
            color: #000;
            border: 2px solid #eab308;
        }

        .btn-warning:hover {
            background: #eab308;
            box-shadow: 0 0 20px rgba(234, 179, 8, 0.5);
        }

        .btn:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }

        /* Status Badges */
        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .status-success {
            background: rgba(34, 197, 94, 0.2);
            color: #22c55e;
            border: 1px solid #22c55e;
        }

        .status-error {
            background: rgba(220, 38, 38, 0.2);
            color: #ef4444;
            border: 1px solid #ef4444;
        }

        .status-warning {
            background: rgba(234, 179, 8, 0.2);
            color: #eab308;
            border: 1px solid #eab308;
        }

        .status-info {
            background: rgba(59, 130, 246, 0.2);
            color: #3b82f6;
            border: 1px solid #3b82f6;
        }

        .pulse {
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.5; }
        }

        /* Terminal */
        .terminal {
            background: #000;
            color: #0f0;
            font-family: 'Courier New', monospace;
            padding: 15px;
            border-radius: 8px;
            overflow-y: auto;
            max-height: 300px;
            font-size: 12px;
            line-height: 1.6;
            border: 1px solid rgba(15, 255, 0, 0.2);
        }

        .terminal-line {
            margin: 5px 0;
        }

        .terminal-success { color: #22c55e; }
        .terminal-error { color: #ef4444; }
        .terminal-warning { color: #eab308; }
        .terminal-info { color: #3b82f6; }

        /* Forms */
        input, textarea, select {
            background: rgba(30, 41, 82, 0.5);
            border: 1px solid var(--border);
            color: #e0e0e0;
            padding: 10px 15px;
            border-radius: 8px;
            font-family: inherit;
            transition: all 0.3s ease;
        }

        input:focus, textarea:focus, select:focus {
            outline: none;
            background: rgba(30, 41, 82, 0.8);
            border-color: var(--secondary);
            box-shadow: 0 0 10px rgba(242, 169, 0, 0.3);
        }

        textarea {
            resize: vertical;
        }

        /* Scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
        }

        ::-webkit-scrollbar-track {
            background: rgba(30, 41, 82, 0.3);
        }

        ::-webkit-scrollbar-thumb {
            background: var(--secondary);
            border-radius: 4px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: #e69500;
        }

        /* Animations */
        .fade-in {
            animation: fadeIn 0.5s ease-in;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .slide-in {
            animation: slideIn 0.3s ease-out;
        }

        @keyframes slideIn {
            from { opacity: 0; transform: translateX(-20px); }
            to { opacity: 1; transform: translateX(0); }
        }

        /* Responsive */
        @media (max-width: 768px) {
            .btn { padding: 8px 16px; font-size: 12px; }
            .terminal { max-height: 200px; }
            .grid-2 { grid-template-columns: 1fr !important; }
        }

        .grid-2 {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
        }

        .grid-3 {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
        }

        .grid-6 {
            display: grid;
            grid-template-columns: repeat(6, 1fr);
            gap: 15px;
        }

        @media (max-width: 1024px) {
            .grid-3 { grid-template-columns: repeat(2, 1fr); }
            .grid-6 { grid-template-columns: repeat(3, 1fr); }
        }

        @media (max-width: 640px) {
            .grid-3 { grid-template-columns: 1fr; }
            .grid-6 { grid-template-columns: repeat(2, 1fr); }
        }

        .text-glow {
            color: var(--secondary);
            text-shadow: 0 0 10px rgba(242, 169, 0, 0.5);
        }

        .border-glow {
            border: 2px solid var(--secondary);
            box-shadow: 0 0 15px rgba(242, 169, 0, 0.3);
        }

        .loading {
            display: inline-block;
            width: 20px;
            height: 20px;
            border: 3px solid rgba(242, 169, 0, 0.3);
            border-radius: 50%;
            border-top-color: var(--secondary);
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.7);
            backdrop-filter: blur(5px);
        }

        .modal.show {
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .modal-content {
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: 30px;
            max-width: 500px;
            width: 90%;
            animation: slideIn 0.3s ease-out;
        }

        .modal-header {
            font-size: 20px;
            font-weight: 700;
            margin-bottom: 20px;
            color: var(--secondary);
        }

        .modal-body {
            margin-bottom: 20px;
            line-height: 1.6;
        }

        .modal-footer {
            display: flex;
            gap: 10px;
            justify-content: flex-end;
        }

        .commit-item {
            padding: 15px;
            background: rgba(30, 41, 82, 0.4);
            border: 1px solid var(--border);
            border-radius: 8px;
            margin-bottom: 10px;
            transition: all 0.3s ease;
        }

        .commit-item:hover {
            background: rgba(30, 41, 82, 0.6);
            border-color: var(--secondary);
        }

        .commit-hash {
            font-family: 'Courier New', monospace;
            color: var(--secondary);
            font-weight: 600;
        }

        .commit-title {
            font-weight: 600;
            margin: 5px 0;
            color: #fff;
        }

        .commit-desc {
            font-size: 12px;
            color: #b0b0b0;
            margin: 5px 0;
            max-height: 60px;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .commit-meta {
            font-size: 11px;
            color: #888;
            display: flex;
            justify-content: space-between;
            margin-top: 8px;
        }

        .file-item {
            padding: 10px 15px;
            background: rgba(30, 41, 82, 0.3);
            border-left: 3px solid var(--secondary);
            margin-bottom: 8px;
            border-radius: 4px;
            font-family: 'Courier New', monospace;
            font-size: 12px;
        }

        .backup-item {
            padding: 12px;
            background: rgba(30, 41, 82, 0.4);
            border: 1px solid var(--border);
            border-radius: 8px;
            margin-bottom: 8px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .backup-item a {
            color: var(--secondary);
            text-decoration: none;
            font-weight: 600;
        }

        .backup-item a:hover {
            text-decoration: underline;
        }

        .badge {
            display: inline-block;
            padding: 4px 12px;
            background: rgba(242, 169, 0, 0.2);
            color: var(--secondary);
            border-radius: 20px;
            font-size: 11px;
            font-weight: 600;
        }
    </style>
</head>
<body>
    <div class="animated-bg"></div>

    <?php if (!$_SESSION['authenticated']): ?>
    <!-- LOGIN PAGE -->
    <div class="min-h-screen flex items-center justify-center p-4">
        <div class="glass-dark p-12 max-w-md w-full fade-in">
            <div class="text-center mb-8">
                <i class="fas fa-lock text-4xl text-glow mb-4"></i>
                <h1 class="text-3xl font-bold text-glow">Deployment Dashboard</h1>
                <p class="text-gray-400 mt-2">Kaatib Publishers Git Deployment System</p>
            </div>

            <form method="POST" class="space-y-6">
                <div>
                    <label class="block text-sm font-semibold mb-2">Admin Password</label>
                    <input type="password" name="password" required autofocus class="w-full" placeholder="Enter password">
                    <?php if (isset($login_error)): ?>
                        <p class="text-red-500 text-sm mt-2"><i class="fas fa-exclamation-circle"></i> <?php echo $login_error; ?></p>
                    <?php endif; ?>
                </div>

                <button type="submit" class="btn btn-primary w-full">
                    <i class="fas fa-sign-in-alt"></i> Login
                </button>
            </form>

            <div class="mt-8 p-4 bg-blue-500/10 border border-blue-500/20 rounded-8px">
                <p class="text-xs text-gray-400 text-center">
                    <i class="fas fa-info-circle"></i> Default password: <span class="text-yellow-400 font-mono">1234</span>
                </p>
            </div>
        </div>
    </div>

    <?php else: ?>
    <!-- DASHBOARD -->
    <div class="min-h-screen p-4 md:p-8">
        <!-- TOP NAVIGATION -->
        <div class="glass mb-8 p-6 flex justify-between items-center flex-wrap gap-4 glow">
            <div class="flex items-center gap-4">
                <i class="fas fa-rocket text-2xl text-glow"></i>
                <div>
                    <h1 class="text-2xl font-bold text-glow">Kaatib Publishers</h1>
                    <p class="text-gray-400 text-sm">Git Deployment Dashboard</p>
                </div>
            </div>

            <div class="flex items-center gap-4 flex-wrap">
                <span class="badge"><i class="fas fa-code-branch"></i> <?php echo BRANCH; ?></span>
                <span class="badge"><i class="fas fa-server"></i> <?php echo REPO_PATH; ?></span>
                <a href="<?php echo REPO_URL; ?>" target="_blank" class="btn btn-secondary" title="View on GitHub">
                    <i class="fab fa-github"></i>
                </a>
                <a href="?logout" class="btn btn-danger">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </a>
            </div>
        </div>

        <!-- STATUS BOXES -->
        <div class="grid-6 mb-8">
            <div class="glass p-4 text-center glow-hover">
                <i class="fas fa-code-branch text-2xl text-glow mb-2"></i>
                <p class="text-xs text-gray-400 mb-1">Current Branch</p>
                <p class="font-mono text-sm font-bold"><?php echo sanitize_output($git_status['branch']); ?></p>
            </div>

            <div class="glass p-4 text-center glow-hover">
                <i class="fas fa-hashtag text-2xl text-glow mb-2"></i>
                <p class="text-xs text-gray-400 mb-1">Current Commit</p>
                <p class="font-mono text-sm font-bold"><?php echo sanitize_output($git_status['commit']); ?></p>
            </div>

            <div class="glass p-4 text-center glow-hover">
                <i class="fas fa-clock text-2xl text-glow mb-2"></i>
                <p class="text-xs text-gray-400 mb-1">Last Deploy</p>
                <p class="text-sm font-bold"><?php echo $last_deploy; ?></p>
            </div>

            <div class="glass p-4 text-center glow-hover">
                <i class="fas fa-check-circle text-2xl text-glow mb-2"></i>
                <p class="text-xs text-gray-400 mb-1">Deploy Status</p>
                <span class="status-badge status-success">
                    <i class="fas fa-circle pulse"></i> Ready
                </span>
            </div>

            <div class="glass p-4 text-center glow-hover">
                <i class="fas fa-wifi text-2xl text-glow mb-2"></i>
                <p class="text-xs text-gray-400 mb-1">Git Connection</p>
                <span class="status-badge status-success">
                    <i class="fas fa-circle pulse"></i> <?php echo $git_status['connection']; ?>
                </span>
            </div>

            <div class="glass p-4 text-center glow-hover">
                <i class="fas fa-exclamation-triangle text-2xl text-glow mb-2"></i>
                <p class="text-xs text-gray-400 mb-1">Pending Changes</p>
                <p class="text-sm font-bold"><?php echo count($modified_files); ?> files</p>
            </div>
        </div>

        <!-- MAIN CONTENT -->
        <div class="grid-2 mb-8">
            <!-- LEFT COLUMN -->
            <div>
                <!-- DEPLOYMENT ACTIONS -->
                <div class="glass p-6 mb-8 glow">
                    <h2 class="text-xl font-bold text-glow mb-6 flex items-center gap-2">
                        <i class="fas fa-rocket"></i> Deployment Actions
                    </h2>

                    <div class="space-y-4">
                        <button onclick="executePull()" class="btn btn-primary w-full">
                            <i class="fas fa-download"></i> Standard Pull
                        </button>

                        <button onclick="confirmForcePull()" class="btn btn-warning w-full">
                            <i class="fas fa-bolt"></i> Force Pull
                        </button>

                        <button onclick="showPushModal()" class="btn btn-success w-full">
                            <i class="fas fa-upload"></i> Push Changes
                        </button>

                        <button onclick="confirmUndo()" class="btn btn-danger w-full">
                            <i class="fas fa-undo"></i> Undo Local Changes
                        </button>

                        <button onclick="confirmRevert()" class="btn btn-warning w-full">
                            <i class="fas fa-history"></i> Revert Last Commit
                        </button>

                        <button onclick="showRestoreModal()" class="btn btn-secondary w-full">
                            <i class="fas fa-clock"></i> Restore Previous Version
                        </button>

                        <button onclick="executeBackup()" class="btn btn-primary w-full">
                            <i class="fas fa-save"></i> Create Backup
                        </button>
                    </div>

                    <div class="mt-6 p-4 bg-blue-500/10 border border-blue-500/20 rounded-8px text-xs text-gray-400">
                        <p class="font-semibold text-blue-400 mb-2"><i class="fas fa-lightbulb"></i> Project Instructions</p>
                        <p>Every update must include:</p>
                        <p>1. Commit message within 10 words</p>
                        <p>2. Detailed extended description</p>
                    </div>
                </div>

                <!-- PUSH COMMIT FORM -->
                <div class="glass p-6 glow">
                    <h2 class="text-xl font-bold text-glow mb-6 flex items-center gap-2">
                        <i class="fas fa-pen"></i> Commit Details
                    </h2>

                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-semibold mb-2">Commit Title (Max 10 words)</label>
                            <input type="text" id="commitTitle" placeholder="Brief description of changes..." maxlength="100">
                            <p class="text-xs text-gray-500 mt-1">Word count: <span id="wordCount">0</span>/10</p>
                        </div>

                        <div>
                            <label class="block text-sm font-semibold mb-2">Extended Description</label>
                            <textarea id="commitDesc" placeholder="Detailed description of what changed and why..." rows="10"></textarea>
                        </div>
                    </div>
                </div>
            </div>

            <!-- RIGHT COLUMN -->
            <div>
                <!-- COMMIT HISTORY -->
                <div class="glass p-6 mb-8 glow">
                    <h2 class="text-xl font-bold text-glow mb-6 flex items-center gap-2">
                        <i class="fas fa-history"></i> Commit History (Last <?php echo COMMIT_HISTORY_LIMIT; ?>)
                    </h2>

                    <div class="max-h-96 overflow-y-auto">
                        <?php if (empty($commit_history)): ?>
                            <p class="text-gray-400 text-center py-8"><i class="fas fa-inbox"></i> No commits found</p>
                        <?php else: ?>
                            <?php foreach ($commit_history as $commit): ?>
                                <div class="commit-item">
                                    <div class="flex justify-between items-start mb-2">
                                        <span class="commit-hash"><?php echo $commit['hash']; ?></span>
                                        <button onclick="restoreCommit('<?php echo $commit['hash']; ?>')" class="btn btn-secondary text-xs py-1 px-2">
                                            <i class="fas fa-redo"></i> Restore
                                        </button>
                                    </div>
                                    <div class="commit-title"><?php echo sanitize_output($commit['title']); ?></div>
                                    <?php if (!empty($commit['description'])): ?>
                                        <div class="commit-desc"><?php echo sanitize_output($commit['description']); ?></div>
                                    <?php endif; ?>
                                    <div class="commit-meta">
                                        <span><?php echo $commit['time']; ?></span>
                                        <span><?php echo $commit['relative']; ?></span>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- MODIFIED FILES -->
                <div class="glass p-6 glow">
                    <h2 class="text-xl font-bold text-glow mb-6 flex items-center gap-2">
                        <i class="fas fa-file-alt"></i> Modified Files (<?php echo count($modified_files); ?>)
                    </h2>

                    <div class="max-h-48 overflow-y-auto">
                        <?php if (empty($modified_files)): ?>
                            <p class="text-gray-400 text-center py-8"><i class="fas fa-check-circle"></i> No modified files</p>
                        <?php else: ?>
                            <?php foreach ($modified_files as $file): ?>
                                <div class="file-item"><?php echo sanitize_output($file); ?></div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- TERMINAL OUTPUT -->
        <div class="glass p-6 mb-8 glow">
            <h2 class="text-xl font-bold text-glow mb-6 flex items-center gap-2">
                <i class="fas fa-terminal"></i> Terminal Output
            </h2>

            <div class="terminal" id="terminal">
                <div class="terminal-line terminal-info">$ Deployment Dashboard Ready</div>
                <div class="terminal-line terminal-info">$ Waiting for command execution...</div>
            </div>
        </div>

        <!-- BACKUPS & ADVANCED TOOLS -->
        <div class="grid-2 mb-8">
            <!-- BACKUPS -->
            <div class="glass p-6 glow">
                <h2 class="text-xl font-bold text-glow mb-6 flex items-center gap-2">
                    <i class="fas fa-archive"></i> Backups
                </h2>

                <div class="max-h-64 overflow-y-auto">
                    <?php if (empty($backups)): ?>
                        <p class="text-gray-400 text-center py-8"><i class="fas fa-inbox"></i> No backups yet</p>
                    <?php else: ?>
                        <?php foreach ($backups as $backup): ?>
                            <div class="backup-item">
                                <div>
                                    <p class="font-mono text-sm"><?php echo $backup['name']; ?></p>
                                    <p class="text-xs text-gray-500"><?php echo date('D d-M-Y h:i A', $backup['time']); ?></p>
                                </div>
                                <a href="<?php echo $backup['path']; ?>" download class="btn btn-secondary text-xs py-1 px-2">
                                    <i class="fas fa-download"></i> Download
                                </a>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>

            <!-- SYSTEM INFO -->
            <div class="glass p-6 glow">
                <h2 class="text-xl font-bold text-glow mb-6 flex items-center gap-2">
                    <i class="fas fa-info-circle"></i> System Information
                </h2>

                <div class="space-y-4 text-sm">
                    <div class="flex justify-between items-center p-3 bg-blue-500/10 rounded-8px">
                        <span class="text-gray-400"><i class="fas fa-server"></i> PHP Version</span>
                        <span class="font-mono font-bold"><?php echo phpversion(); ?></span>
                    </div>

                    <div class="flex justify-between items-center p-3 bg-blue-500/10 rounded-8px">
                        <span class="text-gray-400"><i class="fas fa-hard-drive"></i> Disk Free</span>
                        <span class="font-mono font-bold"><?php echo get_disk_usage(); ?></span>
                    </div>

                    <div class="flex justify-between items-center p-3 bg-blue-500/10 rounded-8px">
                        <span class="text-gray-400"><i class="fas fa-clock"></i> Server Time</span>
                        <span class="font-mono font-bold" id="serverTime"><?php echo date('D d-M-Y h:i A'); ?></span>
                    </div>

                    <div class="flex justify-between items-center p-3 bg-blue-500/10 rounded-8px">
                        <span class="text-gray-400"><i class="fas fa-code-branch"></i> Repository</span>
                        <span class="font-mono font-bold text-xs"><?php echo REPO_URL; ?></span>
                    </div>

                    <button onclick="refreshStatus()" class="btn btn-primary w-full mt-4">
                        <i class="fas fa-sync-alt"></i> Refresh Status
                    </button>
                </div>
            </div>
        </div>

        <!-- FOOTER -->
        <div class="text-center text-gray-500 text-sm py-8">
            <p><i class="fas fa-shield-alt"></i> Secure Deployment Dashboard | Last Updated: <?php echo date('D d-M-Y h:i A'); ?></p>
            <p class="mt-2">Kaatib Publishers © 2026 | All Rights Reserved</p>
        </div>
    </div>

    <!-- MODALS -->
    <div id="confirmModal" class="modal">
        <div class="modal-content">
            <div class="modal-header"><i class="fas fa-exclamation-triangle"></i> Confirm Action</div>
            <div class="modal-body" id="confirmMessage"></div>
            <div class="modal-footer">
                <button onclick="closeModal()" class="btn btn-secondary">Cancel</button>
                <button onclick="executeConfirmedAction()" class="btn btn-danger">Confirm</button>
            </div>
        </div>
    </div>

    <div id="pushModal" class="modal">
        <div class="modal-content">
            <div class="modal-header"><i class="fas fa-upload"></i> Push Changes</div>
            <div class="modal-body">
                <p class="mb-4">Review your commit details before pushing:</p>
                <div class="p-3 bg-blue-500/10 rounded-8px mb-4">
                    <p class="text-xs text-gray-400 mb-1">Title:</p>
                    <p id="pushTitle" class="font-mono text-sm font-bold"></p>
                </div>
                <div class="p-3 bg-blue-500/10 rounded-8px">
                    <p class="text-xs text-gray-400 mb-1">Description:</p>
                    <p id="pushDesc" class="font-mono text-sm" style="max-height: 100px; overflow-y: auto;"></p>
                </div>
            </div>
            <div class="modal-footer">
                <button onclick="closeModal()" class="btn btn-secondary">Cancel</button>
                <button onclick="executePush()" class="btn btn-success">Push</button>
            </div>
        </div>
    </div>

    <div id="restoreModal" class="modal">
        <div class="modal-content">
            <div class="modal-header"><i class="fas fa-clock"></i> Restore Previous Version</div>
            <div class="modal-body">
                <label class="block text-sm font-semibold mb-2">Select Commit to Restore</label>
                <select id="restoreSelect" class="w-full mb-4">
                    <option value="">-- Select a commit --</option>
                    <?php foreach ($commit_history as $commit): ?>
                        <option value="<?php echo $commit['hash']; ?>">
                            <?php echo sanitize_output($commit['hash'] . ' - ' . substr($commit['title'], 0, 50)); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <button onclick="restoreLatest()" class="btn btn-success w-full">
                    <i class="fas fa-redo"></i> Restore Latest Version
                </button>
            </div>
            <div class="modal-footer">
                <button onclick="closeModal()" class="btn btn-secondary">Cancel</button>
                <button onclick="executeRestore()" class="btn btn-warning">Restore Selected</button>
            </div>
        </div>
    </div>

    <?php endif; ?>

    <script>
        let pendingAction = null;

        function addTerminalLine(text, type = 'info') {
            const terminal = document.getElementById('terminal');
            const line = document.createElement('div');
            line.className = 'terminal-line terminal-' + type;
            line.textContent = '$ ' + text;
            terminal.appendChild(line);
            terminal.scrollTop = terminal.scrollHeight;
        }

        function clearTerminal() {
            document.getElementById('terminal').innerHTML = '';
        }

        function showModal(modalId) {
            document.getElementById(modalId).classList.add('show');
        }

        function closeModal() {
            document.querySelectorAll('.modal').forEach(m => m.classList.remove('show'));
        }

        function confirmAction(message, action) {
            document.getElementById('confirmMessage').textContent = message;
            pendingAction = action;
            showModal('confirmModal');
        }

        function executeConfirmedAction() {
            if (pendingAction) {
                pendingAction();
            }
            closeModal();
        }

        function executePull() {
            clearTerminal();
            addTerminalLine('Executing: git fetch origin', 'info');
            addTerminalLine('Executing: git checkout ' + '<?php echo BRANCH; ?>', 'info');
            addTerminalLine('Executing: git pull origin ' + '<?php echo BRANCH; ?>', 'info');

            fetch(window.location.href, {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: 'action=pull'
            })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    data.output.split('\n').forEach(line => {
                        if (line.includes('error') || line.includes('Error')) {
                            addTerminalLine(line, 'error');
                        } else if (line.includes('Already up to date')) {
                            addTerminalLine(line, 'info');
                        } else if (line.includes('fast-forward') || line.includes('Merge made')) {
                            addTerminalLine(line, 'success');
                        } else if (line) {
                            addTerminalLine(line, 'info');
                        }
                    });
                    addTerminalLine('Pull completed successfully', 'success');
                    setTimeout(refreshStatus, 1000);
                } else {
                    addTerminalLine('Pull failed', 'error');
                }
            });
        }

        function confirmForcePull() {
            confirmAction('⚠️ Force pull will discard all local changes. Continue?', () => {
                clearTerminal();
                addTerminalLine('Executing: git fetch origin', 'info');
                addTerminalLine('Executing: git reset --hard origin/' + '<?php echo BRANCH; ?>', 'warning');
                addTerminalLine('Executing: git clean -fd', 'warning');

                fetch(window.location.href, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: 'action=force_pull'
                })
                .then(r => r.json())
                .then(data => {
                    if (data.success) {
                        data.output.split('\n').forEach(line => {
                            if (line.includes('error') || line.includes('Error')) {
                                addTerminalLine(line, 'error');
                            } else if (line) {
                                addTerminalLine(line, 'success');
                            }
                        });
                        addTerminalLine('Force pull completed', 'success');
                        setTimeout(refreshStatus, 1000);
                    } else {
                        addTerminalLine('Force pull failed', 'error');
                    }
                });
            });
        }

        function showPushModal() {
            const title = document.getElementById('commitTitle').value;
            const desc = document.getElementById('commitDesc').value;

            if (!title) {
                alert('Please enter a commit title');
                return;
            }

            document.getElementById('pushTitle').textContent = title;
            document.getElementById('pushDesc').textContent = desc || '(No description)';
            showModal('pushModal');
        }

        function executePush() {
            const title = document.getElementById('commitTitle').value;
            const desc = document.getElementById('commitDesc').value;

            if (!title) {
                alert('Commit title is required');
                return;
            }

            closeModal();
            clearTerminal();
            addTerminalLine('Executing: git add .', 'info');
            addTerminalLine('Executing: git commit -m "' + title + '"', 'info');
            addTerminalLine('Executing: git push origin ' + '<?php echo BRANCH; ?>', 'info');

            fetch(window.location.href, {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: 'action=push&title=' + encodeURIComponent(title) + '&description=' + encodeURIComponent(desc)
            })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    data.output.split('\n').forEach(line => {
                        if (line.includes('error') || line.includes('Error')) {
                            addTerminalLine(line, 'error');
                        } else if (line.includes('nothing to commit')) {
                            addTerminalLine(line, 'warning');
                        } else if (line) {
                            addTerminalLine(line, 'success');
                        }
                    });
                    addTerminalLine('Push completed successfully', 'success');
                    document.getElementById('commitTitle').value = '';
                    document.getElementById('commitDesc').value = '';
                    setTimeout(refreshStatus, 1000);
                } else {
                    addTerminalLine('Push failed: ' + data.message, 'error');
                }
            });
        }

        function confirmUndo() {
            confirmAction('⚠️ This will discard all uncommitted changes. Continue?', () => {
                clearTerminal();
                addTerminalLine('Executing: git reset --hard HEAD', 'warning');

                fetch(window.location.href, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: 'action=undo'
                })
                .then(r => r.json())
                .then(data => {
                    if (data.success) {
                        addTerminalLine(data.output, 'success');
                        addTerminalLine('Local changes undone', 'success');
                        setTimeout(refreshStatus, 1000);
                    } else {
                        addTerminalLine('Undo failed', 'error');
                    }
                });
            });
        }

        function confirmRevert() {
            confirmAction('Revert the last commit?', () => {
                clearTerminal();
                addTerminalLine('Executing: git revert HEAD --no-edit', 'info');

                fetch(window.location.href, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: 'action=revert'
                })
                .then(r => r.json())
                .then(data => {
                    if (data.success) {
                        data.output.split('\n').forEach(line => {
                            if (line) addTerminalLine(line, 'success');
                        });
                        addTerminalLine('Revert completed', 'success');
                        setTimeout(refreshStatus, 1000);
                    } else {
                        addTerminalLine('Revert failed', 'error');
                    }
                });
            });
        }

        function showRestoreModal() {
            showModal('restoreModal');
        }

        function restoreCommit(hash) {
            confirmAction('Restore commit ' + hash + '?', () => {
                clearTerminal();
                addTerminalLine('Executing: git reset --hard ' + hash, 'warning');

                fetch(window.location.href, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: 'action=restore&commit=' + hash
                })
                .then(r => r.json())
                .then(data => {
                    if (data.success) {
                        addTerminalLine(data.output, 'success');
                        addTerminalLine('Restored to commit ' + hash, 'success');
                        setTimeout(refreshStatus, 1000);
                    } else {
                        addTerminalLine('Restore failed', 'error');
                    }
                });
            });
        }

        function executeRestore() {
            const commit = document.getElementById('restoreSelect').value;
            if (!commit) {
                alert('Please select a commit');
                return;
            }
            closeModal();
            restoreCommit(commit);
        }

        function restoreLatest() {
            confirmAction('Restore to latest version?', () => {
                closeModal();
                clearTerminal();
                addTerminalLine('Executing: git reset --hard origin/' + '<?php echo BRANCH; ?>', 'info');

                fetch(window.location.href, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: 'action=restore_latest'
                })
                .then(r => r.json())
                .then(data => {
                    if (data.success) {
                        addTerminalLine(data.output, 'success');
                        addTerminalLine('Restored to latest version', 'success');
                        setTimeout(refreshStatus, 1000);
                    } else {
                        addTerminalLine('Restore failed', 'error');
                    }
                });
            });
        }

        function executeBackup() {
            clearTerminal();
            addTerminalLine('Creating backup...', 'info');

            fetch(window.location.href, {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: 'action=backup'
            })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    addTerminalLine('Backup created: ' + data.message, 'success');
                    addTerminalLine(data.output, 'info');
                    setTimeout(() => location.reload(), 2000);
                } else {
                    addTerminalLine('Backup failed', 'error');
                }
            });
        }

        function refreshStatus() {
            fetch(window.location.href, {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: 'action=status'
            })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    addTerminalLine('Status refreshed', 'success');
                    setTimeout(() => location.reload(), 1000);
                }
            });
        }

        // Update word count
        document.getElementById('commitTitle')?.addEventListener('input', function() {
            const words = this.value.trim().split(/\s+/).filter(w => w.length > 0).length;
            document.getElementById('wordCount').textContent = Math.min(words, 10);
        });

        // Update server time
        setInterval(() => {
            const now = new Date();
            const options = { weekday: 'short', year: 'numeric', month: 'short', day: 'numeric', hour: '2-digit', minute: '2-digit', second: '2-digit' };
            document.getElementById('serverTime').textContent = now.toLocaleDateString('en-US', options);
        }, 1000);

        // Auto-refresh every hour
        setInterval(refreshStatus, <?php echo AUTO_REFRESH_INTERVAL * 1000; ?>);
    </script>
</body>
</html>
