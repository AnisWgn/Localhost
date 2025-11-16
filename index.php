<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Terminal - localhost</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            background-color: #000000;
            color: #33FF33;
            font-family: 'Consolas', 'Courier New', 'Lucida Console', monospace;
            font-size: 14px;
            line-height: 1.6;
            overflow-x: auto;
            min-height: 100vh;
            position: relative;
        }

        /* Canvas pour la pluie numérique */
        #matrix {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            opacity: 0.05;
            z-index: 0;
            pointer-events: none;
        }

        /* Conteneur principal */
        #terminal {
            position: relative;
            z-index: 1;
            padding: 20px;
            white-space: pre-wrap;
            word-wrap: break-word;
        }

        /* Effet de lueur subtile CRT */
        #terminal-content {
            text-shadow: 0 0 1px #33FF33;
        }

        /* Liens */
        a {
            color: #33FF33;
            text-decoration: none;
        }

        a:hover {
            color: #66FF66;
            text-decoration: underline;
        }

        /* Curseur clignotant */
        .cursor {
            display: inline-block;
            width: 10px;
            height: 18px;
            background-color: #33FF33;
            animation: blink 1s infinite;
            vertical-align: text-bottom;
        }

        @keyframes blink {
            0%, 49% { opacity: 1; }
            50%, 100% { opacity: 0; }
        }

        /* Animation de frappe */
        .hidden {
            display: none;
        }

        /* ASCII Art */
        .ascii-art {
            color: #00FF00;
            margin-bottom: 20px;
            font-size: 12px;
            line-height: 1.2;
        }

        /* Header de commande */
        .command-line {
            color: #FFFFFF;
            margin-bottom: 10px;
        }

        /* Colonnes alignées */
        .file-entry {
            display: inline-block;
            width: 100%;
        }
    </style>
</head>
<body>
    <canvas id="matrix"></canvas>
    <div id="terminal">
        <pre id="terminal-content" class="hidden"></pre>
        <span class="cursor" id="cursor"></span>
    </div>

    <script>
        // Pluie numérique en arrière-plan
        const canvas = document.getElementById('matrix');
        const ctx = canvas.getContext('2d');

        canvas.width = window.innerWidth;
        canvas.height = window.innerHeight;

        const matrix = "ABCDEFGHIJKLMNOPQRSTUVWXYZ123456789@#$%^&*()*&^%+-/~{[|`]}";
        const matrixArray = matrix.split("");

        const fontSize = 10;
        const columns = canvas.width / fontSize;

        const drops = [];
        for(let x = 0; x < columns; x++) {
            drops[x] = Math.floor(Math.random() * -100);
        }

        function drawMatrix() {
            ctx.fillStyle = 'rgba(0, 0, 0, 0.04)';
            ctx.fillRect(0, 0, canvas.width, canvas.height);

            ctx.fillStyle = '#0F0';
            ctx.font = fontSize + 'px monospace';

            for(let i = 0; i < drops.length; i++) {
                const text = matrixArray[Math.floor(Math.random() * matrixArray.length)];
                ctx.fillText(text, i * fontSize, drops[i] * fontSize);

                if(drops[i] * fontSize > canvas.height && Math.random() > 0.975) {
                    drops[i] = 0;
                }
                drops[i]++;
            }
        }

        setInterval(drawMatrix, 35);

        // Redimensionner le canvas
        window.addEventListener('resize', () => {
            canvas.width = window.innerWidth;
            canvas.height = window.innerHeight;
        });

        // Données simulées des fichiers (remplacer par des données réelles en PHP)
        const fileSystem = {
            currentPath: "C:\\xampp\\htdocs",
            entries: [
                { type: 'dir', name: '..', size: '', date: '01/01/2024  12:00' },
                { type: 'dir', name: 'admin', size: '', date: '15/11/2024  09:30' },
                { type: 'dir', name: 'assets', size: '', date: '20/11/2024  14:15' },
                { type: 'dir', name: 'backup', size: '', date: '10/11/2024  08:45' },
                { type: 'dir', name: 'config', size: '', date: '22/11/2024  16:20' },
                { type: 'dir', name: 'database', size: '', date: '18/11/2024  11:00' },
                { type: 'dir', name: 'includes', size: '', date: '21/11/2024  13:45' },
                { type: 'dir', name: 'logs', size: '', date: '23/11/2024  17:30' },
                { type: 'dir', name: 'modules', size: '', date: '19/11/2024  10:15' },
                { type: 'dir', name: 'public', size: '', date: '24/11/2024  09:00' },
                { type: 'dir', name: 'templates', size: '', date: '16/11/2024  15:30' },
                { type: 'file', name: '.htaccess', size: '1.2 KB', date: '20/11/2024  10:00' },
                { type: 'file', name: 'composer.json', size: '2.5 KB', date: '15/11/2024  14:30' },
                { type: 'file', name: 'config.php', size: '4.8 KB', date: '22/11/2024  09:15' },
                { type: 'file', name: 'database.sql', size: '156.3 KB', date: '18/11/2024  16:45' },
                { type: 'file', name: 'favicon.ico', size: '15.1 KB', date: '10/11/2024  11:20' },
                { type: 'file', name: 'functions.php', size: '23.7 KB', date: '21/11/2024  13:00' },
                { type: 'file', name: 'LICENSE', size: '1.1 KB', date: '01/11/2024  08:00' },
                { type: 'file', name: 'login.php', size: '8.9 KB', date: '23/11/2024  15:15' },
                { type: 'file', name: 'logout.php', size: '2.1 KB', date: '23/11/2024  15:30' },
                { type: 'file', name: 'README.md', size: '5.6 KB', date: '05/11/2024  12:00' },
                { type: 'file', name: 'robots.txt', size: '0.8 KB', date: '08/11/2024  09:45' },
                { type: 'file', name: 'style.css', size: '45.2 KB', date: '24/11/2024  10:30' }
            ]
        };

        // Formater la liste des fichiers
        function formatFileList() {
            let output = '';
            
            // Séparer les dossiers et les fichiers
            const dirs = fileSystem.entries.filter(e => e.type === 'dir');
            const files = fileSystem.entries.filter(e => e.type === 'file');
            
            // Header de la liste
            output += '\n Le volume dans le lecteur C n\'a pas de nom.\n';
            output += ' Le numéro de série du volume est 4E9F-1A2B\n\n';
            output += ' Répertoire de ' + fileSystem.currentPath + '\n\n';
            
            // Afficher les dossiers
            dirs.forEach(dir => {
                const dateStr = dir.date.padEnd(20);
                const typeStr = '<DIR>'.padEnd(15);
                if (dir.name === '..') {
                    output += `${dateStr}${typeStr}<a href="#" onclick="navigateUp(); return false;">${dir.name}</a>\n`;
                } else {
                    output += `${dateStr}${typeStr}<a href="#" onclick="navigateToDir('${dir.name}'); return false;">${dir.name}</a>\n`;
                }
            });
            
            // Afficher les fichiers
            files.forEach(file => {
                const dateStr = file.date.padEnd(20);
                const sizeStr = file.size.padStart(15);
                output += `${dateStr}${sizeStr} <a href="#" onclick="openFile('${file.name}'); return false;">${file.name}</a>\n`;
            });
            
            // Footer avec statistiques
            const totalFiles = files.length;
            const totalDirs = dirs.length - 1; // Exclure '..'
            output += `\n              ${totalFiles} fichier(s)`;
            output += `\n              ${totalDirs} répertoire(s)`;
            output += '\n              2,147,483,648 octets libres\n';
            
            return output;
        }

        // ASCII Art
        const asciiArt = `
╔══════════════════════════════════════════════════════════════════════════╗
║                                                                          ║
║  ████████╗███████╗██████╗ ███╗   ███╗██╗███╗   ██╗ █████╗ ██╗          ║
║  ╚══██╔══╝██╔════╝██╔══██╗████╗ ████║██║████╗  ██║██╔══██╗██║          ║
║     ██║   █████╗  ██████╔╝██╔████╔██║██║██╔██╗ ██║███████║██║          ║
║     ██║   ██╔══╝  ██╔══██╗██║╚██╔╝██║██║██║╚██╗██║██╔══██║██║          ║
║     ██║   ███████╗██║  ██║██║ ╚═╝ ██║██║██║ ╚████║██║  ██║███████╗     ║
║     ╚═╝   ╚══════╝╚═╝  ╚═╝╚═╝     ╚═╝╚═╝╚═╝  ╚═══╝╚═╝  ╚═╝╚══════╝     ║
║                                                                          ║
║                    [ SYSTEM ACCESS GRANTED ]                            ║
║                    [ ROOT PRIVILEGES ACTIVE ]                           ║
║                                                                          ║
╚══════════════════════════════════════════════════════════════════════════╝
`;

        // Contenu complet du terminal
        const fullContent = asciiArt + '\n' + fileSystem.currentPath + '> dir' + formatFileList();

        // Machine à écrire
        function typeWriter() {
            const terminal = document.getElementById('terminal-content');
            const cursor = document.getElementById('cursor');
            terminal.classList.remove('hidden');
            
            let index = 0;
            const speed = 5; // Vitesse de frappe en ms
            
            function type() {
                if (index < fullContent.length) {
                    // Ajouter plusieurs caractères à la fois pour accélérer
                    const chunkSize = 3;
                    const chunk = fullContent.substring(index, index + chunkSize);
                    terminal.innerHTML += chunk;
                    index += chunkSize;
                    
                    // Faire défiler vers le bas
                    window.scrollTo(0, document.body.scrollHeight);
                    
                    setTimeout(type, speed);
                } else {
                    // Afficher le curseur à la fin
                    cursor.style.display = 'inline-block';
                }
            }
            
            type();
        }

        // Fonctions de navigation simulées
        function navigateUp() {
            alert('Navigation vers le répertoire parent...');
        }

        function navigateToDir(dirName) {
            alert('Ouverture du répertoire: ' + dirName);
        }

        function openFile(fileName) {
            alert('Ouverture du fichier: ' + fileName);
        }

        // Lancer l'animation au chargement
        window.addEventListener('load', () => {
            setTimeout(typeWriter, 500);
        });

        // Effet de scanlines CRT (optionnel)
        const style = document.createElement('style');
        style.textContent = `
            body::before {
                content: " ";
                display: block;
                position: fixed;
                top: 0;
                left: 0;
                bottom: 0;
                right: 0;
                background: linear-gradient(rgba(18, 16, 16, 0) 50%, rgba(0, 0, 0, 0.25) 50%), linear-gradient(90deg, rgba(255, 0, 0, 0.06), rgba(0, 255, 0, 0.02), rgba(0, 0, 255, 0.06));
                z-index: 2;
                background-size: 100% 2px, 3px 100%;
                pointer-events: none;
                opacity: 0.2;
            }
        `;
        document.head.appendChild(style);
    </script>
</body>
</html>