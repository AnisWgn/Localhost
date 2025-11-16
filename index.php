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
                    <option value="php">--php</option>
                    <option value="html">--html</option>
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

        // Function to detect project type based on folder contents
        function detectProjectType(folderName) {
            // Common patterns to detect project types
            const patterns = {
                laravel: ['artisan', 'composer.json', 'app', 'bootstrap', 'routes'],
                wordpress: ['wp-config.php', 'wp-content', 'wp-admin', 'wp-includes'],
                symfony: ['symfony.lock', 'bin/console', 'config/packages'],
                react: ['package.json', 'src/App.js', 'public/index.html'],
                vue: ['vue.config.js', 'src/App.vue'],
                angular: ['angular.json', 'src/app'],
                nodejs: ['package.json', 'server.js', 'app.js'],
                php: ['index.php', '.php'],
                html: ['index.html', '.html']
            };
            
            // In a real scenario, we would check file system
            // For now, we'll use heuristics based on folder name
            const name = folderName.toLowerCase();
            
            if (name.includes('laravel') || name.includes('api')) return 'laravel';
            if (name.includes('wordpress') || name.includes('wp')) return 'wordpress';
            if (name.includes('react')) return 'react';
            if (name.includes('vue')) return 'vue';
            if (name.includes('angular')) return 'angular';
            if (name.includes('node')) return 'nodejs';
            if (name.includes('symfony')) return 'symfony';
            if (name.includes('php')) return 'php';
            
            // Default to PHP for most projects
            return 'php';
        }

        // Function to load projects from directory listing
        function loadProjectsFromDirectory() {
            // Simulate reading the actual www directory
            // In a real implementation, this would require a backend API or file system access
            
            // Get current folders from localStorage if available
            const savedProjects = localStorage.getItem('laragonProjects');
            if (savedProjects) {
                projects = JSON.parse(savedProjects);
                return;
            }
            
            // Simulate actual folders that might be in C:\laragon\www
            const wwwFolders = [
                'my-blog',
                'client-site',
                'laravel-app',
                'wordpress-site',
                'api-backend',
                'portfolio',
                'test-project',
                'ecommerce',
                'admin-panel',
                'landing-page'
            ];
            
            // Convert folders to project objects
            projects = wwwFolders.map((folder, index) => {
                const type = detectProjectType(folder);
                return {
                    name: folder,
                    type: type,
                    url: `http://localhost/${folder}`,
                    status: Math.random() > 0.3 ? 'running' : 'stopped',
                    lastModified: new Date(Date.now() - Math.random() * 30 * 24 * 60 * 60 * 1000).toISOString().split('T')[0],
                    size: Math.floor(Math.random() * 500 + 10) + ' MB',
                    path: `C:\\laragon\\www\\${folder}`
                };
            });
            
            // Sort by last modified
            projects.sort((a, b) => new Date(b.lastModified) - new Date(a.lastModified));
        }

        // Function to scan for new projects
        function scanForProjects() {
            addLog('> Scanning C:\\laragon\\www for projects...');
            loadProjectsFromDirectory();
            renderProjects();
            addLog(`> Found ${projects.length} projects in www directory`);
            
            // Save to localStorage
            localStorage.setItem('laragonProjects', JSON.stringify(projects));
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
        loadProjectsFromDirectory();
        renderProjects();
        
        // Add initial terminal effect
        setTimeout(() => {
            addLog('> All services operational');
            addLog(`> Apache server running on port 80`);
            addLog(`> MySQL database running on port 3306`);
        }, 2000);
        
        // Auto-scan for new projects every 30 seconds
        setInterval(() => {
            scanForProjects();
        }, 30000);
    </script>
</body>
</html>