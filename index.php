<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>C:\> Directory Listing</title>
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
            overflow-x: hidden;
            cursor: default;
        }

        /* Canvas pour la pluie numérique en arrière-plan */
        #matrix-canvas {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -1;
            opacity: 0.15;
        }

        /* Conteneur principal */
        #terminal {
            padding: 20px;
            position: relative;
            z-index: 1;
        }

        pre {
            margin: 0;
            white-space: pre;
            text-shadow: 0 0 2px rgba(51, 255, 51, 0.3);
        }

        /* Style des liens */
        a {
            color: #33FF33;
            text-decoration: none;
        }

        a:hover {
            color: #66FF66;
            text-decoration: underline;
        }

        a.dir-link:hover {
            color: #FFFFFF;
        }

        /* Curseur clignotant */
        .cursor {
            display: inline-block;
            background-color: #33FF33;
            animation: blink 1s step-start infinite;
        }

        @keyframes blink {
            0%, 50% {
                opacity: 1;
            }
            50.1%, 100% {
                opacity: 0;
            }
        }

        /* Style pour l'art ASCII */
        .ascii-art {
            color: #00FF00;
            line-height: 1.2;
            margin-bottom: 10px;
        }

        /* Style pour les sections de la liste */
        .file-entry {
            display: block;
        }
    </style>
</head>
<body>
    <!-- Canvas pour l'effet Matrix -->
    <canvas id="matrix-canvas"></canvas>

    <!-- Terminal principal -->
    <div id="terminal">
        <pre id="output"></pre><span class="cursor">█</span>
    </div>

    <script>
        // ============================================
        // EFFET PLUIE NUMÉRIQUE (MATRIX BACKGROUND)
        // ============================================
        const canvas = document.getElementById('matrix-canvas');
        const ctx = canvas.getContext('2d');

        canvas.width = window.innerWidth;
        canvas.height = window.innerHeight;

        const katakana = 'アイウエオカキクケコサシスセソタチツテトナニヌネノハヒフヘホマミムメモヤユヨラリルレロワヲン';
        const latin = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        const alphabet = katakana + latin;

        const fontSize = 14;
        const columns = canvas.width / fontSize;

        const rainDrops = [];
        for (let x = 0; x < columns; x++) {
            rainDrops[x] = Math.random() * canvas.height / fontSize;
        }

        function drawMatrix() {
            ctx.fillStyle = 'rgba(0, 0, 0, 0.05)';
            ctx.fillRect(0, 0, canvas.width, canvas.height);

            ctx.fillStyle = '#0F0';
            ctx.font = fontSize + 'px monospace';

            for (let i = 0; i < rainDrops.length; i++) {
                const text = alphabet.charAt(Math.floor(Math.random() * alphabet.length));
                ctx.fillText(text, i * fontSize, rainDrops[i] * fontSize);

                if (rainDrops[i] * fontSize > canvas.height && Math.random() > 0.975) {
                    rainDrops[i] = 0;
                }
                rainDrops[i]++;
            }
        }

        setInterval(drawMatrix, 50);

        // Redimensionnement du canvas
        window.addEventListener('resize', () => {
            canvas.width = window.innerWidth;
            canvas.height = window.innerHeight;
        });

        // ============================================
        // SIMULATION DU CONTENU DU TERMINAL
        // ============================================
        
        // Art ASCII du logo
        const asciiLogo = `
   ██████╗███╗   ███╗██████╗     ████████╗███████╗██████╗ ███╗   ███╗██╗███╗   ██╗ █████╗ ██╗     
  ██╔════╝████╗ ████║██╔══██╗    ╚══██╔══╝██╔════╝██╔══██╗████╗ ████║██║████╗  ██║██╔══██╗██║     
  ██║     ██╔████╔██║██║  ██║       ██║   █████╗  ██████╔╝██╔████╔██║██║██╔██╗ ██║███████║██║     
  ██║     ██║╚██╔╝██║██║  ██║       ██║   ██╔══╝  ██╔══██╗██║╚██╔╝██║██║██║╚██╗██║██╔══██║██║     
  ╚██████╗██║ ╚═╝ ██║██████╔╝       ██║   ███████╗██║  ██║██║ ╚═╝ ██║██║██║ ╚████║██║  ██║███████╗
   ╚═════╝╚═╝     ╚═╝╚═════╝        ╚═╝   ╚══════╝╚═╝  ╚═╝╚═╝     ╚═╝╚═╝╚═╝  ╚═══╝╚═╝  ╚═╝╚══════╝
`;

        // Simulation du contenu du répertoire
        const terminalContent = `${asciiLogo}
 Volume dans le lecteur C est System
 Le numéro de série du volume est 4E2A-B8F1

 Répertoire de C:\\xampp\\htdocs

12/01/2025  14:32    <DIR>          .
12/01/2025  14:32    <DIR>          <a href="../" class="dir-link">..</a>
15/12/2024  09:15    <DIR>          <a href="assets/" class="dir-link">assets</a>
08/01/2025  16:42    <DIR>          <a href="config/" class="dir-link">config</a>
22/11/2024  11:28    <DIR>          <a href="css/" class="dir-link">css</a>
03/01/2025  13:55    <DIR>          <a href="database/" class="dir-link">database</a>
18/12/2024  10:20    <DIR>          <a href="images/" class="dir-link">images</a>
05/01/2025  17:30    <DIR>          <a href="js/" class="dir-link">js</a>
29/12/2024  08:45    <DIR>          <a href="scripts/" class="dir-link">scripts</a>
11/01/2025  12:10    <DIR>          <a href="uploads/" class="dir-link">uploads</a>
10/01/2025  15:22             4 256 <a href="config.php">config.php</a>
12/01/2025  14:32             8 192 <a href="database.sql">database.sql</a>
08/01/2025  09:47            12 458 <a href="functions.php">functions.php</a>
15/12/2024  16:55             2 048 <a href="header.php">header.php</a>
12/01/2025  11:20            15 872 <a href="index.php">index.php</a>
07/01/2025  13:33             6 144 <a href="login.php">login.php</a>
22/12/2024  10:15             3 584 <a href="logout.php">logout.php</a>
05/01/2025  14:28             9 216 <a href="README.md">README.md</a>
18/12/2024  08:50             5 632 <a href="style.css">style.css</a>
              9 fichier(s)           67 402 octets
             10 Rép(s)  125 847 552 000 octets libres

C:\\xampp\\htdocs> `;

        // ============================================
        // EFFET MACHINE À ÉCRIRE
        // ============================================
        const output = document.getElementById('output');
        let charIndex = 0;
        const typingSpeed = 8; // Millisecondes par caractère

        function typeWriter() {
            if (charIndex < terminalContent.length) {
                const char = terminalContent.charAt(charIndex);
                
                // Insérer le caractère (en préservant le HTML pour les liens)
                if (char === '<') {
                    // Trouver la balise complète
                    const closeTag = terminalContent.indexOf('>', charIndex);
                    const tag = terminalContent.substring(charIndex, closeTag + 1);
                    output.innerHTML += tag;
                    charIndex = closeTag + 1;
                } else {
                    output.innerHTML += char;
                    charIndex++;
                }
                
                // Auto-scroll vers le bas
                window.scrollTo(0, document.body.scrollHeight);
                
                // Vitesse variable pour plus de réalisme
                const speed = char === '\n' ? typingSpeed * 2 : typingSpeed;
                setTimeout(typeWriter, speed);
            }
        }

        // Démarrer l'animation après un court délai
        setTimeout(typeWriter, 500);

        // ============================================
        // EFFETS SONORES (OPTIONNEL - COMMENTÉ)
        // ============================================
        // Si vous voulez ajouter un son de frappe clavier:
        /*
        const keySound = new Audio('data:audio/wav;base64,UklGRhYAAABXQVZFZm10...');
        keySound.volume = 0.1;
        // Jouer le son à chaque caractère dans typeWriter()
        */
    </script>
</body>
</html>