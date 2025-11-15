<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LOCALHOST://SYSTEM_ACCESS</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Share+Tech+Mono&display=swap');
        
        * {
            font-family: 'Share Tech Mono', monospace;
        }
        
        body {
            background: #000;
            overflow: hidden;
        }
        
        .scanline {
            animation: scanline 8s linear infinite;
            background: linear-gradient(to bottom, transparent 50%, rgba(0, 255, 0, 0.03) 50%);
            background-size: 100% 4px;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            pointer-events: none;
            z-index: 1000;
        }
        
        @keyframes scanline {
            0% { transform: translateY(0); }
            100% { transform: translateY(10px); }
        }
        
        .glow {
            text-shadow: 0 0 10px #00ff00, 0 0 20px #00ff00, 0 0 30px #00ff00;
        }
        
        .terminal-cursor {
            animation: blink 1s infinite;
        }
        
        @keyframes blink {
            0%, 50% { opacity: 1; }
            51%, 100% { opacity: 0; }
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
        
        .file-item {
            transition: all 0.3s;
            border: 1px solid transparent;
        }
        
        .file-item:hover {
            background: rgba(0, 255, 0, 0.1);
            border-color: #00ff00;
            box-shadow: 0 0 10px rgba(0, 255, 0, 0.5);
            transform: translateX(10px);
        }
        
        .typing {
            overflow: hidden;
            white-space: nowrap;
            animation: typing 2s steps(40, end);
        }
        
        @keyframes typing {
            from { width: 0; }
            to { width: 100%; }
        }
        
        .glitch {
            animation: glitch 2s infinite;
        }
        
        @keyframes glitch {
            0%, 100% { transform: translate(0); }
            20% { transform: translate(-1px, 1px); }
            40% { transform: translate(-1px, -1px); }
            60% { transform: translate(1px, 1px); }
            80% { transform: translate(1px, -1px); }
        }
        
        .cyber-button {
            position: relative;
            background: transparent;
            color: #00ff00;
            border: 1px solid #00ff00;
            padding: 10px 20px;
            transition: all 0.3s;
            overflow: hidden;
        }
        
        .cyber-button:before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(0, 255, 0, 0.2), transparent);
            transition: left 0.5s;
        }
        
        .cyber-button:hover:before {
            left: 100%;
        }
        
        .cyber-button:hover {
            background: rgba(0, 255, 0, 0.1);
            box-shadow: 0 0 20px rgba(0, 255, 0, 0.5);
            transform: scale(1.05);
        }
    </style>
</head>
<body class="bg-black text-green-400">
    <!-- Effet de scanline -->
    <div class="scanline"></div>
    
    <!-- Matrix background effect -->
    <canvas class="matrix-bg" id="matrix"></canvas>
    
    <!-- Container principal -->
    <div class="relative z-10 min-h-screen p-4">
        <!-- Header -->
        <header class="mb-8 border-b border-green-400 pb-4">
            <h1 class="text-4xl glow glitch">
                <span class="text-green-500">LOCALHOST://</span>SYSTEM_ACCESS
            </h1>
            <div class="mt-2 text-sm">
                <span class="text-green-300">STATUS:</span> 
                <span class="text-green-400">CONNECTED</span>
                <span class="text-green-300 ml-4">IP:</span> 
                <span class="text-green-400">127.0.0.1</span>
                <span class="text-green-300 ml-4">TIME:</span> 
                <span id="time" class="text-green-400"></span>
            </div>
        </header>
        
        <!-- Terminal -->
        <div class="bg-black border border-green-400 rounded p-4 mb-6 shadow-2xl" style="box-shadow: 0 0 30px rgba(0, 255, 0, 0.3);">
            <div class="flex items-center mb-2">
                <span class="text-green-500 mr-2">root@localhost:~$</span>
                <input type="text" id="terminal-input" class="bg-transparent outline-none flex-1 text-green-400" placeholder="Enter command..." />
                <span class="terminal-cursor text-green-400">_</span>
            </div>
            <div id="terminal-output" class="text-sm text-green-300 max-h-40 overflow-y-auto"></div>
        </div>
        
        <!-- Navigation -->
        <div class="mb-6">
            <div class="flex items-center space-x-2 mb-4">
                <button class="cyber-button" onclick="navigateTo('/')">
                    [ROOT]
                </button>
                <span class="text-green-400">/</span>
                <span id="current-path" class="text-green-300">www</span>
            </div>
        </div>
        
        <!-- File Explorer -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Dossiers -->
            <div class="bg-black border border-green-400 rounded p-4" style="box-shadow: 0 0 20px rgba(0, 255, 0, 0.2);">
                <h2 class="text-xl mb-4 glow">
                    <span class="text-green-500">[</span>DIRECTORIES<span class="text-green-500">]</span>
                </h2>
                <div id="directories" class="space-y-2">
                    <!-- Les dossiers seront ajoutés ici -->
                </div>
            </div>
            
            <!-- Fichiers -->
            <div class="bg-black border border-green-400 rounded p-4" style="box-shadow: 0 0 20px rgba(0, 255, 0, 0.2);">
                <h2 class="text-xl mb-4 glow">
                    <span class="text-green-500">[</span>FILES<span class="text-green-500">]</span>
                </h2>
                <div id="files" class="space-y-2">
                    <!-- Les fichiers seront ajoutés ici -->
                </div>
            </div>
            
            <!-- Informations système -->
            <div class="bg-black border border-green-400 rounded p-4" style="box-shadow: 0 0 20px rgba(0, 255, 0, 0.2);">
                <h2 class="text-xl mb-4 glow">
                    <span class="text-green-500">[</span>SYSTEM_INFO<span class="text-green-500">]</span>
                </h2>
                <div class="space-y-2 text-sm">
                    <div><span class="text-green-300">OS:</span> <span class="text-green-400">Linux localhost 5.4.0</span></div>
                    <div><span class="text-green-300">CPU:</span> <span class="text-green-400">Intel Core i7 @ 3.60GHz</span></div>
                    <div><span class="text-green-300">RAM:</span> <span class="text-green-400">16.0 GB</span></div>
                    <div><span class="text-green-300">DISK:</span> <span class="text-green-400">256 GB SSD</span></div>
                    <div class="mt-4">
                        <div class="text-green-300">CPU USAGE:</div>
                        <div class="bg-green-900 h-2 rounded mt-1">
                            <div class="bg-green-400 h-full rounded" style="width: 35%; box-shadow: 0 0 10px rgba(0, 255, 0, 0.8);"></div>
                        </div>
                    </div>
                    <div class="mt-2">
                        <div class="text-green-300">RAM USAGE:</div>
                        <div class="bg-green-900 h-2 rounded mt-1">
                            <div class="bg-green-400 h-full rounded" style="width: 62%; box-shadow: 0 0 10px rgba(0, 255, 0, 0.8);"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- File Content Viewer -->
        <div id="file-viewer" class="hidden mt-6 bg-black border border-green-400 rounded p-4" style="box-shadow: 0 0 30px rgba(0, 255, 0, 0.3);">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-xl glow">
                    <span class="text-green-500">[</span>FILE_VIEWER<span class="text-green-500">]</span>
                    <span id="file-name" class="ml-2 text-green-300"></span>
                </h3>
                <button onclick="closeFileViewer()" class="cyber-button">
                    [CLOSE]
                </button>
            </div>
            <pre id="file-content" class="text-green-300 text-sm overflow-x-auto"></pre>
        </div>
    </div>
    
    <script>
        // Simulated file system
        const fileSystem = {
            '/': {
                type: 'directory',
                children: {
                    'www': {
                        type: 'directory',
                        children: {
                            'index.html': {
                                type: 'file',
                                size: '4.2KB',
                                content: '<!DOCTYPE html>\n<html>\n<head>\n    <title>Welcome</title>\n</head>\n<body>\n    <h1>Welcome to localhost</h1>\n</body>\n</html>'
                            },
                            'style.css': {
                                type: 'file',
                                size: '2.1KB',
                                content: 'body {\n    margin: 0;\n    padding: 20px;\n    font-family: Arial, sans-serif;\n}\n\nh1 {\n    color: #333;\n}'
                            },
                            'script.js': {
                                type: 'file',
                                size: '1.5KB',
                                content: 'console.log("Script loaded");\n\nfunction init() {\n    console.log("Initializing...");\n}\n\nwindow.onload = init;'
                            },
                            'config': {
                                type: 'directory',
                                children: {
                                    'settings.json': {
                                        type: 'file',
                                        size: '0.8KB',
                                        content: '{\n    "version": "1.0.0",\n    "debug": true,\n    "port": 8080\n}'
                                    },
                                    'database.conf': {
                                        type: 'file',
                                        size: '1.2KB',
                                        content: '[DATABASE]\nhost=localhost\nport=3306\nuser=root\npassword=********'
                                    }
                                }
                            },
                            'assets': {
                                type: 'directory',
                                children: {
                                    'logo.png': {
                                        type: 'file',
                                        size: '15.3KB',
                                        content: '[BINARY IMAGE DATA]'
                                    },
                                    'favicon.ico': {
                                        type: 'file',
                                        size: '4.2KB',
                                        content: '[BINARY ICON DATA]'
                                    }
                                }
                            },
                            'README.md': {
                                type: 'file',
                                size: '3.7KB',
                                content: '# LOCALHOST SERVER\n\n## Installation\n1. Clone the repository\n2. Run npm install\n3. Start the server\n\n## Security Notice\nThis is a simulation. No actual file system access.'
                            }
                        }
                    },
                    'etc': {
                        type: 'directory',
                        children: {
                            'hosts': {
                                type: 'file',
                                size: '0.5KB',
                                content: '127.0.0.1    localhost\n::1          localhost'
                            }
                        }
                    },
                    'var': {
                        type: 'directory',
                        children: {
                            'log': {
                                type: 'directory',
                                children: {
                                    'access.log': {
                                        type: 'file',
                                        size: '12.8KB',
                                        content: '[2024-01-01 00:00:00] GET / 200 OK\n[2024-01-01 00:00:01] GET /style.css 200 OK\n[2024-01-01 00:00:02] GET /script.js 200 OK'
                                    }
                                }
                            }
                        }
                    }
                }
            }
        };
        
        let currentPath = '/www';
        
        // Update time
        function updateTime() {
            const now = new Date();
            document.getElementById('time').textContent = now.toLocaleTimeString('fr-FR');
        }
        setInterval(updateTime, 1000);
        updateTime();
        
        // Matrix effect
        const canvas = document.getElementById('matrix');
        const ctx = canvas.getContext('2d');
        canvas.width = window.innerWidth;
        canvas.height = window.innerHeight;
        
        const matrix = "ABCDEFGHIJKLMNOPQRSTUVWXYZ123456789@#$%^&*()*&^%+-/~{[|`]}";
        const matrixArray = matrix.split("");
        
        const fontSize = 10;
        const columns = canvas.width/fontSize;
        const drops = [];
        for(let x = 0; x < columns; x++) {
            drops[x] = 1;
        }
        
        function drawMatrix() {
            ctx.fillStyle = 'rgba(0, 0, 0, 0.04)';
            ctx.fillRect(0, 0, canvas.width, canvas.height);
            
            ctx.fillStyle = '#00ff00';
            ctx.font = fontSize + 'px monospace';
            
            for(let i = 0; i < drops.length; i++) {
                const text = matrixArray[Math.floor(Math.random()*matrixArray.length)];
                ctx.fillText(text, i*fontSize, drops[i]*fontSize);
                
                if(drops[i]*fontSize > canvas.height && Math.random() > 0.975) {
                    drops[i] = 0;
                }
                drops[i]++;
            }
        }
        setInterval(drawMatrix, 35);
        
        // Navigate file system
        function navigateTo(path) {
            currentPath = path;
            document.getElementById('current-path').textContent = path === '/' ? '/' : path.substring(1);
            displayFiles(path);
        }
        
        function getItemAtPath(path) {
            const parts = path.split('/').filter(p => p);
            let current = fileSystem['/'];
            
            for (const part of parts) {
                if (current.children && current.children[part]) {
                    current = current.children[part];
                } else {
                    return null;
                }
            }
            
            return current;
        }
        
        function displayFiles(path) {
            const item = getItemAtPath(path);
            if (!item || item.type !== 'directory') return;
            
            const directoriesDiv = document.getElementById('directories');
            const filesDiv = document.getElementById('files');
            
            directoriesDiv.innerHTML = '';
            filesDiv.innerHTML = '';
            
            // Add parent directory
            if (path !== '/') {
                const parentPath = path.substring(0, path.lastIndexOf('/')) || '/';
                directoriesDiv.innerHTML += `
                    <div class="file-item p-2 cursor-pointer" onclick="navigateTo('${parentPath}')">
                        <span class="text-green-500">[DIR]</span> <span class="text-green-400">..</span>
                    </div>
                `;
            }
            
            // Add directories and files
            if (item.children) {
                Object.entries(item.children).forEach(([name, child]) => {
                    if (child.type === 'directory') {
                        const fullPath = path === '/' ? '/' + name : path + '/' + name;
                        directoriesDiv.innerHTML += `
                            <div class="file-item p-2 cursor-pointer" onclick="navigateTo('${fullPath}')">
                                <span class="text-green-500">[DIR]</span> <span class="text-green-400">${name}/</span>
                            </div>
                        `;
                    } else {
                        filesDiv.innerHTML += `
                            <div class="file-item p-2 cursor-pointer" onclick="viewFile('${name}', '${path}')">
                                <span class="text-green-500">[FILE]</span> 
                                <span class="text-green-400">${name}</span>
                                <span class="text-green-300 text-xs ml-2">${child.size}</span>
                            </div>
                        `;
                    }
                });
            }
            
            if (filesDiv.innerHTML === '') {
                filesDiv.innerHTML = '<div class="text-green-300 text-sm">No files in this directory</div>';
            }
            if (directoriesDiv.innerHTML === '' || (path === '/' && directoriesDiv.innerHTML === '')) {
                if (path === '/') {
                    Object.entries(fileSystem['/'].children).forEach(([name, child]) => {
                        if (child.type === 'directory') {
                            directoriesDiv.innerHTML += `
                                <div class="file-item p-2 cursor-pointer" onclick="navigateTo('/${name}')">
                                    <span class="text-green-500">[DIR]</span> <span class="text-green-400">${name}/</span>
                                </div>
                            `;
                        }
                    });
                }
            }
        }
        
        function viewFile(fileName, path) {
            const item = getItemAtPath(path);
            if (!item || !item.children || !item.children[fileName]) return;
            
            const file = item.children[fileName];
            document.getElementById('file-name').textContent = fileName;
            document.getElementById('file-content').textContent = file.content;
            document.getElementById('file-viewer').classList.remove('hidden');
        }
        
        function closeFileViewer() {
            document.getElementById('file-viewer').classList.add('hidden');
        }
        
        // Terminal functionality
        const terminalInput = document.getElementById('terminal-input');
        const terminalOutput = document.getElementById('terminal-output');
        
        terminalInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                const command = this.value;
                processCommand(command);
                this.value = '';
            }
        });
        
        function processCommand(command) {
            addToTerminal(`> ${command}`);
            
            const parts = command.split(' ');
            const cmd = parts[0].toLowerCase();
            
            switch(cmd) {
                case 'ls':
                    listCurrentDirectory();
                    break;
                case 'cd':
                    if (parts[1]) {
                        changeDirectory(parts[1]);
                    } else {
                        addToTerminal('Usage: cd <directory>');
                    }
                    break;
                case 'cat':
                    if (parts[1]) {
                        catFile(parts[1]);
                    } else {
                        addToTerminal('Usage: cat <filename>');
                    }
                    break;
                case 'pwd':
                    addToTerminal(currentPath);
                    break;
                case 'clear':
                    terminalOutput.innerHTML = '';
                    break;
                case 'help':
                    addToTerminal('Available commands:');
                    addToTerminal('  ls     - list directory contents');
                    addToTerminal('  cd     - change directory');
                    addToTerminal('  cat    - display file contents');
                    addToTerminal('  pwd    - print working directory');
                    addToTerminal('  clear  - clear terminal');
                    addToTerminal('  help   - show this help');
                    break;
                default:
                    addToTerminal(`Command not found: ${cmd}`);
            }
        }
        
        function addToTerminal(text) {
            terminalOutput.innerHTML += `<div>${text}</div>`;
            terminalOutput.scrollTop = terminalOutput.scrollHeight;
        }
        
        function listCurrentDirectory() {
            const item = getItemAtPath(currentPath);
            if (!item || item.type !== 'directory') return;
            
            if (item.children) {
                Object.entries(item.children).forEach(([name, child]) => {
                    if (child.type === 'directory') {
                        addToTerminal(`<span class="text-blue-400">${name}/</span>`);
                    } else {
                        addToTerminal(`${name} (${child.size})`);
                    }
                });
            }
        }
        
        function changeDirectory(dir) {
            let newPath;
            if (dir === '/') {
                newPath = '/';
            } else if (dir === '..') {
                newPath = currentPath.substring(0, currentPath.lastIndexOf('/')) || '/';
            } else if (dir.startsWith('/')) {
                newPath = dir;
            } else {
                newPath = currentPath === '/' ? '/' + dir : currentPath + '/' + dir;
            }
            
            const item = getItemAtPath(newPath);
            if (item && item.type === 'directory') {
                navigateTo(newPath);
                addToTerminal(`Changed directory to: ${newPath}`);
            } else {
                addToTerminal(`cd: ${dir}: No such directory`);
            }
        }
        
        function catFile(fileName) {
            const item = getItemAtPath(currentPath);
            if (!item || !item.children || !item.children[fileName]) {
                addToTerminal(`cat: ${fileName}: No such file`);
                return;
            }
            
            const file = item.children[fileName];
            if (file.type === 'directory') {
                addToTerminal(`cat: ${fileName}: Is a directory`);
            } else {
                addToTerminal(file.content);
            }
        }
        
        // Initialize
        displayFiles('/www');
        addToTerminal('System initialized. Type "help" for available commands.');
    </script>
</body>
</html>