<?php
// Fonction pour scanner les projets dans le répertoire www
function scanProjects($wwwPath = 'C:\\laragon\\www') {
    $projects = [];
    
    if (!is_dir($wwwPath)) {
        return $projects;
    }
    
    $items = scandir($wwwPath);
    
    foreach ($items as $item) {
        if ($item === '.' || $item === '..' || !is_dir($wwwPath . '\\' . $item)) {
            continue;
        }
        
        $projectPath = $wwwPath . '\\' . $item;
        $projectType = detectProjectType($projectPath);
        
        // Obtenir la date de modification
        $lastModified = date('Y-m-d', filemtime($projectPath));
        
        // Calculer la taille du dossier
        $size = calculateDirectorySize($projectPath);
        
        $projects[] = [
            'name' => $item,
            'type' => $projectType,
            'url' => 'http://localhost/' . $item,
            'status' => 'running',
            'lastModified' => $lastModified,
            'size' => formatBytes($size),
            'path' => $projectPath
        ];
    }
    
    // Trier par date de modification (plus récent en premier)
    usort($projects, function($a, $b) {
        return strtotime($b['lastModified']) - strtotime($a['lastModified']);
    });
    
    return $projects;
}

// Fonction pour détecter le type de projet
function detectProjectType($projectPath) {
    $files = [];
    
    if (is_dir($projectPath)) {
        $items = scandir($projectPath);
        foreach ($items as $item) {
            if ($item !== '.' && $item !== '..') {
                $files[] = strtolower($item);
            }
        }
    }
    
    // Détection Laravel
    if (in_array('artisan', $files) || in_array('composer.json', $files)) {
        $composerPath = $projectPath . '\\composer.json';
        if (file_exists($composerPath)) {
            $composer = json_decode(file_get_contents($composerPath), true);
            if (isset($composer['require']['laravel/framework'])) {
                return 'laravel';
            }
        }
        if (in_array('artisan', $files)) {
            return 'laravel';
        }
    }
    
    // Détection WordPress
    if (in_array('wp-config.php', $files) || in_array('wp-content', $files) || in_array('wp-admin', $files)) {
        return 'wordpress';
    }
    
    // Détection Symfony
    if (in_array('symfony.lock', $files) || in_array('bin', $files)) {
        if (is_dir($projectPath . '\\bin') && file_exists($projectPath . '\\bin\\console')) {
            return 'symfony';
        }
    }
    
    // Détection React
    if (in_array('package.json', $files)) {
        $packagePath = $projectPath . '\\package.json';
        if (file_exists($packagePath)) {
            $package = json_decode(file_get_contents($packagePath), true);
            if (isset($package['dependencies']['react']) || isset($package['dependencies']['react-dom'])) {
                return 'react';
            }
        }
    }
    
    // Détection Vue
    if (in_array('vue.config.js', $files) || in_array('vite.config.js', $files)) {
        $packagePath = $projectPath . '\\package.json';
        if (file_exists($packagePath)) {
            $package = json_decode(file_get_contents($packagePath), true);
            if (isset($package['dependencies']['vue'])) {
                return 'vue';
            }
        }
    }
    
    // Détection Angular
    if (in_array('angular.json', $files)) {
        return 'angular';
    }
    
    // Détection Node.js
    if (in_array('package.json', $files) && (in_array('server.js', $files) || in_array('app.js', $files))) {
        return 'nodejs';
    }
    
    // Détection PHP
    $phpFiles = array_filter($files, function($file) {
        return pathinfo($file, PATHINFO_EXTENSION) === 'php';
    });
    if (!empty($phpFiles) || in_array('index.php', $files)) {
        return 'php';
    }
    
    // Détection HTML
    if (in_array('index.html', $files)) {
        return 'html';
    }
    
    // Par défaut
    return 'other';
}

// Fonction pour calculer la taille d'un répertoire
function calculateDirectorySize($directory) {
    $size = 0;
    if (is_dir($directory)) {
        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($directory, RecursiveDirectoryIterator::SKIP_DOTS),
            RecursiveIteratorIterator::SELF_FIRST
        );
        
        foreach ($iterator as $file) {
            if ($file->isFile()) {
                $size += $file->getSize();
            }
        }
    }
    return $size;
}

// Fonction pour formater les bytes
function formatBytes($bytes, $precision = 2) {
    $units = ['B', 'KB', 'MB', 'GB', 'TB'];
    
    $bytes = max($bytes, 0);
    $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
    $pow = min($pow, count($units) - 1);
    
    $bytes /= pow(1024, $pow);
    
    return round($bytes, $precision) . ' ' . $units[$pow];
}

// Si c'est une requête AJAX pour obtenir les projets
if (isset($_GET['action']) && $_GET['action'] === 'getProjects') {
    header('Content-Type: application/json');
    $wwwPath = isset($_GET['path']) ? $_GET['path'] : 'C:\\laragon\\www';
    echo json_encode(scanProjects($wwwPath));
    exit;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laragon Projects Terminal</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@400;700&display=swap');
        
        body {
            font-family: 'JetBrains Mono', 'Courier New', monospace;
            background: #000000;
            color: #00ff00;
            overflow-x: hidden;
        }
        
        .matrix-bg {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            pointer-events: none;
            opacity: 0.1;
            z-index: 1;
        }
        
        .matrix-rain {
            position: absolute;
            top: -100%;
            font-size: 14px;
            animation: fall linear infinite;
        }
        
        @keyframes fall {
            to {
                top: 100%;
            }
        }
        
        .terminal-cursor::after {
            content: '_';
            animation: blink 1s infinite;
            color: #00ff00;
        }
        
        @keyframes blink {
            0%, 50% { opacity: 1; }
            51%, 100% { opacity: 0; }
        }
        
        .glow {
            text-shadow: 0 0 10px #00ff00, 0 0 20px #00ff00, 0 0 30px #00ff00;
        }
        
        .project-card {
            border: 1px solid #00ff00;
            background: rgba(0, 255, 0, 0.05);
            transition: all 0.3s ease;
        }
        
        .project-card:hover {
            background: rgba(0, 255, 0, 0.1);
            box-shadow: 0 0 20px rgba(0, 255, 0, 0.5);
            transform: translateY(-2px);
        }
        
        .typing-effect {
            overflow: hidden;
            white-space: nowrap;
            animation: typing 2s steps(40, end);
        }
        
        @keyframes typing {
            from { width: 0 }
            to { width: 100% }
        }
        
        .scanline {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 2px;
            background: #00ff00;
            opacity: 0.1;
            animation: scan 8s linear infinite;
            pointer-events: none;
            z-index: 10;
        }
        
        @keyframes scan {
            0% { top: 0%; }
            100% { top: 100%; }
        }
        
        input, select {
            background: #000000;
            border: 1px solid #00ff00;
            color: #00ff00;
            outline: none;
        }
        
        input:focus, select:focus {
            box-shadow: 0 0 10px rgba(0, 255, 0, 0.5);
        }
        
        .btn-terminal {
            border: 1px solid #00ff00;
            background: transparent;
            color: #00ff00;
            transition: all 0.3s ease;
        }
        
        .btn-terminal:hover {
            background: #00ff00;
            color: #000000;
            box-shadow: 0 0 15px #00ff00;
        }
    </style>
</head>
<body class="min-h-screen relative">
    <!-- Matrix Rain Background -->
    <div class="matrix-bg" id="matrixBg"></div>
    
    <!-- Scanline Effect -->
    <div class="scanline"></div>
    
    <!-- Main Container -->
    <div class="container mx-auto px-4 py-8 relative z-20">
        <!-- Terminal Header -->
        <div class="mb-8">
            <div class="text-sm mb-2 opacity-70">Microsoft Windows [Version 10.0.19043.1826]</div>
            <div class="text-sm mb-4 opacity-70">(c) Laragon Terminal - Projects Manager</div>
            <div class="typing-effect">
                <h1 class="text-4xl font-bold glow mb-2">LARAGON://LOCALHOST</h1>
            </div>
            <div class="text-lg mt-2">
                <span class="opacity-70">C:\laragon\www></span>
                <span class="terminal-cursor">dir /projects</span>
            </div>
        </div>

        <!-- System Status -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
            <div class="project-card p-4 rounded">
                <div class="text-xs opacity-70 mb-1">[SYSTEM STATUS]</div>
                <div class="text-xl font-bold">ONLINE</div>
                <div class="text-xs mt-2">Apache: <span id="apacheStatus">RUNNING</span></div>
            </div>
            <div class="project-card p-4 rounded">
                <div class="text-xs opacity-70 mb-1">[DATABASE]</div>
                <div class="text-xl font-bold">MySQL</div>
                <div class="text-xs mt-2">Port: 3306 | Status: <span id="mysqlStatus">ACTIVE</span></div>
            </div>
            <div class="project-card p-4 rounded">
                <div class="text-xs opacity-70 mb-1">[PHP VERSION]</div>
                <div class="text-xl font-bold">PHP 8.1.10</div>
                <div class="text-xs mt-2">Memory: <span id="memoryUsage">128MB</span></div>
            </div>
        </div>

        <!-- Search and Filter -->
        <div class="mb-6">
            <div class="flex flex-wrap gap-4">
                <input 
                    type="text" 
                    id="searchInput"
                    placeholder="search_project.exe"
                    class="px-4 py-2 rounded flex-1 min-w-[200px]"
                >
                <select id="filterType" class="px-4 py-2 rounded">
                    <option value="all">--all-projects</option>
                    <option value="laravel">--laravel</option>
                    <option value="wordpress">--wordpress</option>
                    <option value="symfony">--symfony</option>
                    <option value="react">--react</option>
                    <option value="vue">--vue</option>
                    <option value="angular">--angular</option>
                    <option value="nodejs">--nodejs</option>
                    <option value="php">--php</option>
                    <option value="html">--html</option>
                    <option value="other">--other</option>
                </select>
                <button onclick="scanForProjects()" class="btn-terminal px-6 py-2 rounded">
                    ↻ REFRESH
                </button>
                <button onclick="addProject()" class="btn-terminal px-6 py-2 rounded">
                    + NEW_PROJECT
                </button>
            </div>
        </div>

        <!-- Projects Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6" id="projectsGrid">
            <!-- Projects will be dynamically added here -->
        </div>

        <!-- Terminal Log -->
        <div class="mt-8 project-card rounded p-4">
            <div class="text-xs opacity-70 mb-2">[SYSTEM LOG]</div>
            <div id="terminalLog" class="text-xs opacity-70 max-h-32 overflow-y-auto">
                <div>> Initializing Laragon environment...</div>
                <div>> Loading projects from C:\laragon\www...</div>
                <div>> System ready.</div>
            </div>
        </div>
    </div>

    <!-- Add Project Modal -->
    <div id="addProjectModal" class="fixed inset-0 bg-black bg-opacity-90 hidden items-center justify-center z-50">
        <div class="project-card rounded p-6 max-w-md w-full mx-4">
            <h3 class="text-xl font-bold mb-4 glow">CREATE NEW PROJECT</h3>
            <input 
                type="text" 
                id="projectName"
                placeholder="project_name"
                class="w-full px-4 py-2 rounded mb-4"
            >
            <select id="projectType" class="w-full px-4 py-2 rounded mb-4">
                <option value="laravel">Laravel</option>
                <option value="wordpress">WordPress</option>
                <option value="php">PHP</option>
                <option value="html">HTML/CSS/JS</option>
            </select>
            <div class="flex gap-4">
                <button onclick="createProject()" class="btn-terminal px-4 py-2 rounded flex-1">
                    CREATE
                </button>
                <button onclick="closeModal()" class="btn-terminal px-4 py-2 rounded flex-1">
                    CANCEL
                </button>
            </div>
        </div>
    </div>

    <script>
        // Projects will be loaded dynamically from the actual www folder
        let projects = [];

        // Function to load projects from directory listing via PHP
        async function loadProjectsFromDirectory() {
            try {
                addLog('> Scanning C:\\laragon\\www for projects...');
                const response = await fetch('?action=getProjects');
                if (!response.ok) {
                    throw new Error('Failed to fetch projects');
                }
                projects = await response.json();
                addLog(`> Found ${projects.length} projects in www directory`);
            } catch (error) {
                console.error('Error loading projects:', error);
                addLog('> Error: Failed to load projects from directory');
                projects = [];
            }
        }

        // Function to scan for new projects
        async function scanForProjects() {
            await loadProjectsFromDirectory();
            renderProjects();
        }

        // Matrix rain effect
        function createMatrixRain() {
            const container = document.getElementById('matrixBg');
            const chars = '01アイウエオカキクケコサシスセソタチツテトナニヌネノハヒフヘホマミムメモヤユヨラリルレロワヲン';
            
            for (let i = 0; i < 50; i++) {
                const span = document.createElement('span');
                span.className = 'matrix-rain';
                span.style.left = Math.random() * 100 + '%';
                span.style.animationDuration = Math.random() * 20 + 10 + 's';
                span.style.animationDelay = Math.random() * 20 + 's';
                
                let text = '';
                for (let j = 0; j < 20; j++) {
                    text += chars[Math.floor(Math.random() * chars.length)] + '<br>';
                }
                span.innerHTML = text;
                container.appendChild(span);
            }
        }

        // Render projects
        function renderProjects(projectsList = projects) {
            const grid = document.getElementById('projectsGrid');
            grid.innerHTML = '';
            
            projectsList.forEach((project, index) => {
                const card = document.createElement('div');
                card.className = 'project-card rounded p-4';
                card.style.animationDelay = `${index * 0.1}s`;
                
                const statusColor = project.status === 'running' ? 'text-green-400' : 'text-red-400';
                const statusIcon = project.status === 'running' ? '●' : '○';
                
                card.innerHTML = `
                    <div class="flex justify-between items-start mb-2">
                        <div class="text-xs opacity-70">[PROJECT-${String(index + 1).padStart(3, '0')}]</div>
                        <div class="${statusColor} text-xs">${statusIcon} ${project.status.toUpperCase()}</div>
                    </div>
                    <h3 class="text-lg font-bold mb-2">${project.name}</h3>
                    <div class="text-xs opacity-70 mb-3">
                        <div>Type: ${project.type.toUpperCase()}</div>
                        <div>Size: ${project.size || 'N/A'}</div>
                        <div>Modified: ${project.lastModified}</div>
                        <div class="truncate" title="${project.path}">Path: ${project.path}</div>
                    </div>
                    <div class="flex gap-2 mt-4">
                        <a href="${project.url}" target="_blank" class="btn-terminal px-3 py-1 rounded text-xs flex-1 text-center">
                            OPEN
                        </a>
                        <button onclick="toggleProject('${project.name}')" class="btn-terminal px-3 py-1 rounded text-xs flex-1">
                            ${project.status === 'running' ? 'STOP' : 'START'}
                        </button>
                    </div>
                `;
                
                grid.appendChild(card);
            });
            
            addLog(`> Loaded ${projectsList.length} projects`);
        }

        // Search functionality
        document.getElementById('searchInput').addEventListener('input', filterProjects);
        document.getElementById('filterType').addEventListener('change', filterProjects);

        function filterProjects() {
            const searchTerm = document.getElementById('searchInput').value.toLowerCase();
            const filterType = document.getElementById('filterType').value;
            
            let filtered = projects;
            
            if (filterType !== 'all') {
                filtered = filtered.filter(p => p.type === filterType);
            }
            
            if (searchTerm) {
                filtered = filtered.filter(p => p.name.toLowerCase().includes(searchTerm));
            }
            
            renderProjects(filtered);
        }

        // Toggle project status
        function toggleProject(projectName) {
            const project = projects.find(p => p.name === projectName);
            if (project) {
                project.status = project.status === 'running' ? 'stopped' : 'running';
                renderProjects();
                addLog(`> Project "${projectName}" ${project.status === 'running' ? 'started' : 'stopped'}`);
            }
        }

        // Add log entry
        function addLog(message) {
            const log = document.getElementById('terminalLog');
            const entry = document.createElement('div');
            entry.textContent = message;
            log.appendChild(entry);
            log.scrollTop = log.scrollHeight;
        }

        // Modal functions
        function addProject() {
            document.getElementById('addProjectModal').style.display = 'flex';
        }

        function closeModal() {
            document.getElementById('addProjectModal').style.display = 'none';
        }

        function createProject() {
            const name = document.getElementById('projectName').value;
            const type = document.getElementById('projectType').value;
            
            if (name) {
                const newProject = {
                    name: name.toLowerCase().replace(/\s+/g, '-'),
                    type: type,
                    url: `http://${name.toLowerCase().replace(/\s+/g, '-')}.test`,
                    status: 'running',
                    lastModified: new Date().toISOString().split('T')[0]
                };
                
                projects.unshift(newProject);
                renderProjects();
                addLog(`> Created new ${type} project: "${newProject.name}"`);
                closeModal();
                document.getElementById('projectName').value = '';
            }
        }

        // Simulate system status updates
        setInterval(() => {
            document.getElementById('memoryUsage').textContent = 
                Math.floor(Math.random() * 50 + 100) + 'MB';
        }, 3000);

        // Initialize
        createMatrixRain();
        
        // Load projects from directory on startup
        (async function() {
            await loadProjectsFromDirectory();
            renderProjects();
            
            // Add initial terminal effect
            setTimeout(() => {
                addLog('> All services operational');
                addLog(`> Apache server running on port 80`);
                addLog(`> MySQL database running on port 3306`);
            }, 2000);
        })();
        
        // Auto-scan for new projects every 30 seconds
        setInterval(() => {
            scanForProjects();
        }, 30000);
    </script>
</body>
</html>