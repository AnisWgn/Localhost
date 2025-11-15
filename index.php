<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LocalHost // Terminal_</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Share+Tech+Mono&family=Orbitron:wght@400;700;900&display=swap');
        
        :root {
            --green-primary: #00ff41;
            --green-dark: #00cc33;
            --green-glow: #00ff4133;
            --black-deep: #0a0a0a;
            --black-soft: #1a1a1a;
        }

        * {
            cursor: none;
        }

        body {
            background: var(--black-deep);
            font-family: 'Share Tech Mono', monospace;
            overflow-x: hidden;
            cursor: none;
        }

        .cursor-dot {
            width: 8px;
            height: 8px;
            background: var(--green-primary);
            position: fixed;
            border-radius: 50%;
            pointer-events: none;
            z-index: 9999;
            mix-blend-mode: screen;
            transition: transform 0.15s ease-out;
        }

        .cursor-outline {
            width: 32px;
            height: 32px;
            border: 2px solid var(--green-primary);
            position: fixed;
            border-radius: 50%;
            pointer-events: none;
            z-index: 9998;
            transition: all 0.08s ease-out;
            opacity: 0.5;
        }

        .scanline {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            height: 2px;
            background: linear-gradient(90deg, transparent, var(--green-primary), transparent);
            opacity: 0.8;
            animation: scan 8.3s linear infinite;
            pointer-events: none;
            z-index: 1;
        }

        @keyframes scan {
            0% { transform: translateY(0vh); }
            100% { transform: translateY(100vh); }
        }

        .glitch {
            position: relative;
            color: var(--green-primary);
            font-size: clamp(2rem, 5vw, 4.5rem);
            font-weight: 900;
            text-transform: uppercase;
            font-family: 'Orbitron', sans-serif;
            letter-spacing: 0.03em;
            animation: textGlow 2.7s ease-in-out infinite alternate;
        }

        @keyframes textGlow {
            from { text-shadow: 0 0 10px var(--green-glow), 0 0 20px var(--green-glow); }
            to { text-shadow: 0 0 15px var(--green-glow), 0 0 25px var(--green-glow), 0 0 35px var(--green-glow); }
        }

        .glitch::before,
        .glitch::after {
            content: attr(data-text);
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
        }

        .glitch::before {
            animation: glitch1 0.6s infinite;
            color: var(--green-primary);
            z-index: -1;
            opacity: 0.8;
        }

        .glitch::after {
            animation: glitch2 0.6s infinite;
            color: var(--green-dark);
            z-index: -2;
            opacity: 0.8;
        }

        @keyframes glitch1 {
            0%, 100% { clip-path: inset(0 0 100% 0); transform: translate(0); }
            20% { clip-path: inset(33% 0 30% 0); transform: translate(-2px, 1px); }
            40% { clip-path: inset(70% 0 10% 0); transform: translate(1px, -1px); }
            60% { clip-path: inset(10% 0 85% 0); transform: translate(2px, 1px); }
            80% { clip-path: inset(50% 0 40% 0); transform: translate(-1px, 2px); }
        }

        @keyframes glitch2 {
            0%, 100% { clip-path: inset(0 0 100% 0); transform: translate(0); }
            20% { clip-path: inset(60% 0 20% 0); transform: translate(1px, -1px); }
            40% { clip-path: inset(20% 0 60% 0); transform: translate(-2px, 1px); }
            60% { clip-path: inset(85% 0 5% 0); transform: translate(1px, 2px); }
            80% { clip-path: inset(40% 0 45% 0); transform: translate(2px, -1px); }
        }

        .terminal-box {
            background: linear-gradient(135deg, var(--black-soft) 0%, transparent 100%);
            border: 1px solid var(--green-primary);
            border-radius: 3px;
            padding: 1.2rem;
            position: relative;
            overflow: hidden;
            backdrop-filter: blur(10px);
            box-shadow: 0 0 40px var(--green-glow), inset 0 0 20px var(--green-glow);
            transition: all 0.3s ease;
        }

        .terminal-box:hover {
            transform: translateY(-3px) scale(1.02);
            box-shadow: 0 0 60px var(--green-glow), inset 0 0 30px var(--green-glow);
        }

        .terminal-box::before {
            content: '';
            position: absolute;
            top: -2px;
            left: -2px;
            right: -2px;
            bottom: -2px;
            background: linear-gradient(45deg, var(--green-primary), transparent, var(--green-dark));
            z-index: -1;
            opacity: 0;
            transition: opacity 0.3s ease;
            animation: borderRotate 3s linear infinite;
        }

        .terminal-box:hover::before {
            opacity: 0.5;
        }

        @keyframes borderRotate {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        .typing-text {
            border-right: 3px solid var(--green-primary);
            animation: blink 0.9s step-end infinite;
            padding-right: 5px;
        }

        @keyframes blink {
            0%, 50% { border-color: var(--green-primary); }
            51%, 100% { border-color: transparent; }
        }

        .matrix-bg {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            pointer-events: none;
            opacity: 0.03;
            z-index: 0;
        }

        .nav-link {
            position: relative;
            color: #00ff41;
            text-decoration: none;
            padding: 0.5rem 1rem;
            transition: all 0.3s ease;
            display: inline-block;
        }

        .nav-link::before {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 0;
            height: 2px;
            background: var(--green-primary);
            transition: width 0.3s ease;
        }

        .nav-link:hover::before {
            width: 100%;
        }

        .nav-link:hover {
            text-shadow: 0 0 10px var(--green-primary);
            transform: translateY(-2px);
        }

        .code-block {
            background: rgba(0, 0, 0, 0.8);
            border-left: 3px solid var(--green-primary);
            padding: 1rem;
            margin: 1rem 0;
            font-family: 'Share Tech Mono', monospace;
            position: relative;
            overflow-x: auto;
        }

        .code-block::before {
            content: '>';
            position: absolute;
            left: -15px;
            color: var(--green-primary);
            animation: pulse 1.5s infinite;
        }

        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.5; }
        }

        .grid-pattern {
            background-image: 
                linear-gradient(var(--green-primary) 1px, transparent 1px),
                linear-gradient(90deg, var(--green-primary) 1px, transparent 1px);
            background-size: 50px 50px;
            opacity: 0.02;
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            pointer-events: none;
        }

        .flicker {
            animation: flicker 0.15s infinite alternate;
        }

        @keyframes flicker {
            0% { opacity: 1; }
            100% { opacity: 0.97; }
        }

        .asymmetric-layout {
            display: grid;
            grid-template-columns: 1fr 1.3fr 0.8fr;
            gap: 2rem;
            margin-top: 3rem;
        }

        @media (max-width: 768px) {
            .asymmetric-layout {
                grid-template-columns: 1fr;
            }
        }

        .status-indicator {
            width: 8px;
            height: 8px;
            background: var(--green-primary);
            border-radius: 50%;
            display: inline-block;
            animation: statusPulse 2s infinite;
            margin-right: 8px;
        }

        @keyframes statusPulse {
            0%, 100% { transform: scale(1); opacity: 1; }
            50% { transform: scale(1.5); opacity: 0.5; }
        }
    </style>
</head>
<body class="bg-black text-green-400 min-h-screen relative">
    <div class="cursor-dot"></div>
    <div class="cursor-outline"></div>
    <div class="scanline"></div>
    <canvas class="matrix-bg" id="matrix"></canvas>
    <div class="grid-pattern"></div>

    <nav class="fixed top-0 w-full z-50 backdrop-blur-md bg-black/70 border-b border-green-400/20">
        <div class="container mx-auto px-6 py-4 flex justify-between items-center">
            <div class="flex items-center space-x-2">
                <span class="status-indicator"></span>
                <span class="text-green-400 font-mono text-sm flicker">SYSTEM_ONLINE</span>
            </div>
            <div class="flex space-x-6">
                <a href="#" class="nav-link">[HOME]</a>
                <a href="#projects" class="nav-link">[PROJECTS]</a>
                <a href="#terminal" class="nav-link">[TERMINAL]</a>
                <a href="#contact" class="nav-link">[CONTACT]</a>
            </div>
        </div>
    </nav>

    <main class="container mx-auto px-6 pt-24 pb-12">
        <section class="min-h-screen flex flex-col justify-center">
            <div class="mb-8">
                <h1 class="glitch" data-text="LOCALHOST_">LOCALHOST_</h1>
                <p class="text-green-400/70 text-lg mt-4 font-mono typing-text" id="subtitle"></p>
            </div>

            <div class="asymmetric-layout">
                <div class="terminal-box" style="margin-top: 20px;">
                    <h3 class="text-green-400 font-bold mb-3">&gt; SYSTEM.INFO</h3>
                    <p class="text-green-400/80 text-sm leading-relaxed">
                        Connexion établie.<br>
                        IP: 127.0.0.1<br>
                        Port: 8080<br>
                        Status: <span class="text-green-400">ACTIF</span>
                    </p>
                </div>

                <div class="terminal-box" style="margin-top: -10px;">
                    <h3 class="text-green-400 font-bold mb-3">&gt; CURRENT_PROJECT</h3>
                    <div class="code-block">
                        <code class="text-green-400/90">
                            const project = {<br>
                            &nbsp;&nbsp;name: "Neural_Interface",<br>
                            &nbsp;&nbsp;version: "2.0.1",<br>
                            &nbsp;&nbsp;status: "in_progress",<br>
                            &nbsp;&nbsp;completion: 73<br>
                            };
                        </code>
                    </div>
                    <div class="mt-4">
                        <div class="bg-black/50 rounded-full h-2 overflow-hidden">
                            <div class="bg-green-400 h-full" style="width: 73%; animation: loadProgress 2s ease-out;"></div>
                        </div>
                    </div>
                </div>

                <div class="terminal-box" style="margin-top: 35px;">
                    <h3 class="text-green-400 font-bold mb-3">&gt; QUICK.ACCESS</h3>
                    <ul class="space-y-2 text-sm">
                        <li class="hover:text-green-300 cursor-pointer transition-colors">→ Database_Manager</li>
                        <li class="hover:text-green-300 cursor-pointer transition-colors">→ API_Endpoints</li>
                        <li class="hover:text-green-300 cursor-pointer transition-colors">→ Log_Viewer</li>
                        <li class="hover:text-green-300 cursor-pointer transition-colors">→ Config_Files</li>
                    </ul>
                </div>
            </div>

            <div class="mt-16 terminal-box max-w-3xl">
                <div class="flex items-center mb-4">
                    <span class="status-indicator"></span>
                    <h3 class="text-green-400 font-bold">&gt; TERMINAL_</h3>
                </div>
                <div class="font-mono text-sm space-y-2" id="terminal-output">
                    <p class="text-green-400/70">[23:47:12] Initialisation du système...</p>
                    <p class="text-green-400/70">[23:47:13] Chargement des modules principaux...</p>
                    <p class="text-green-400/70">[23:47:14] Connexion au serveur local établie</p>
                    <p class="text-green-400">[23:47:15] <span class="flicker">█</span> Prêt pour les commandes...</p>
                </div>
                <input type="text" class="w-full bg-transparent border-none outline-none text-green-400 mt-4 font-mono" placeholder="$ Entrez une commande..." id="terminal-input">
            </div>
        </section>
    </main>

    <script>
        // Cursor custom
        const cursor = document.querySelector('.cursor-dot');
        const cursorOutline = document.querySelector('.cursor-outline');

        document.addEventListener('mousemove', (e) => {
            cursor.style.left = e.clientX + 'px';
            cursor.style.top = e.clientY + 'px';
            cursorOutline.style.left = (e.clientX - 12) + 'px';
            cursorOutline.style.top = (e.clientY - 12) + 'px';
        });

        document.addEventListener('mousedown', () => {
            cursor.style.transform = 'scale(0.8)';
            cursorOutline.style.transform = 'scale(1.3)';
        });

        document.addEventListener('mouseup', () => {
            cursor.style.transform = 'scale(1)';
            cursorOutline.style.transform = 'scale(1)';
        });

        // Typing effect
        const subtitleText = "Développeur Full Stack // Système Local Actif";
        const subtitle = document.getElementById('subtitle');
        let charIndex = 0;

        function typeWriter() {
            if (charIndex < subtitleText.length) {
                subtitle.textContent += subtitleText.charAt(charIndex);
                charIndex++;
                setTimeout(typeWriter, 80 + Math.random() * 40); // Variation humaine
            }
        }

        setTimeout(typeWriter, 500);

        // Matrix effect
        const canvas = document.getElementById('matrix');
        const ctx = canvas.getContext('2d');

        canvas.width = window.innerWidth;
        canvas.height = window.innerHeight;

        const matrix = "ABCDEFGHIJKLMNOPQRSTUVWXYZ123456789@#$%^&*()";
        const matrixArr = matrix.split("");
        const fontSize = 16;
        const columns = canvas.width / fontSize;
        const drops = [];

        for(let x = 0; x < columns; x++) {
            drops[x] = Math.random() * -100;
        }

        function drawMatrix() {
            ctx.fillStyle = 'rgba(0, 0, 0, 0.04)';
            ctx.fillRect(0, 0, canvas.width, canvas.height);
            
            ctx.fillStyle = '#00ff41';
            ctx.font = fontSize + 'px monospace';

            for(let i = 0; i < drops.length; i++) {
                const text = matrixArr[Math.floor(Math.random() * matrixArr.length)];
                ctx.fillText(text, i * fontSize, drops[i] * fontSize);
                
                if(drops[i] * fontSize > canvas.height && Math.random() > 0.975) {
                    drops[i] = 0;
                }
                drops[i]++;
            }
        }

        setInterval(drawMatrix, 35);

        // Terminal interaction
        const terminalInput = document.getElementById('terminal-input');
        const terminalOutput = document.getElementById('terminal-output');

        terminalInput.addEventListener('keypress', (e) => {
            if(e.key === 'Enter') {
                const command = e.target.value;
                const time = new Date().toLocaleTimeString('fr-FR');
                
                const newLine = document.createElement('p');
                newLine.className = 'text-green-400/70';
                newLine.textContent = `[${time}] $ ${command}`;
                terminalOutput.appendChild(newLine);

                // Simulated responses avec délai humain
                setTimeout(() => {
                    const response = document.createElement('p');
                    response.className = 'text-green-400';
                    
                    if(command.toLowerCase().includes('help')) {
                        response.innerHTML = '> Commandes disponibles: status, clear, info, ping';
                    } else if(command.toLowerCase().includes('status')) {
                        response.innerHTML = '> Tous les systèmes sont opérationnels';
                    } else if(command.toLowerCase().includes('clear')) {
                        terminalOutput.innerHTML = '';
                    } else if(command.toLowerCase().includes('ping')) {
                        response.innerHTML = '> PING localhost (127.0.0.1): 0.023ms';
                    } else {
                        response.innerHTML = `> Commande non reconnue: ${command}`;
                    }
                    
                    terminalOutput.appendChild(response);
                    terminalOutput.scrollTop = terminalOutput.scrollHeight;
                }, 300 + Math.random() * 200);

                e.target.value = '';
            }
        });

        // Random glitches
        setInterval(() => {
            if(Math.random() > 0.95) {
                document.body.style.filter = 'brightness(1.1)';
                setTimeout(() => {
                    document.body.style.filter = 'brightness(1)';
                }, 50);
            }
        }, 3000);

        // Window resize
        window.addEventListener('resize', () => {
            canvas.width = window.innerWidth;
            canvas.height = window.innerHeight;
        });
    </script>
</body>
</html>