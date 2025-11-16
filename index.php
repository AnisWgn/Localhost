<?php
// --- PARTIE LOGIQUE PHP ---

// Fonction pour formater la taille des fichiers de manière lisible.
function formatSizeUnits($bytes)
{
    if ($bytes >= 1073741824) {
        $bytes = number_format($bytes / 1073741824, 2) . ' GB';
    } elseif ($bytes >= 1048576) {
        $bytes = number_format($bytes / 1048576, 2) . ' MB';
    } elseif ($bytes >= 1024) {
        $bytes = number_format($bytes / 1024, 2) . ' KB';
    } elseif ($bytes > 1) {
        $bytes = $bytes . ' bytes';
    } elseif ($bytes == 1) {
        $bytes = $bytes . ' byte';
    } else {
        $bytes = '0 bytes';
    }
    return $bytes;
}

// Définir le répertoire courant.
$current_dir = '.';
$items = scandir($current_dir);

$dirs = [];
$files = [];

// Trier les éléments.
foreach ($items as $item) {
    if ($item === '.' || $item === basename(__FILE__)) continue;
    if ($item === '..') continue;

    if (is_dir($item)) {
        $dirs[] = $item;
    } else {
        $files[] = $item;
    }
}

sort($dirs);
sort($files);

// Construction de la sortie texte.
$output = '';

$output .= "
    __  __ ___ ___ _  _   _   _  _ ___   _   
   |  \/  | __/ __| || | /_\ | \| | __| /_\  
   | |\/| | _| (__| __ |/ _ \| .` | _| / _ \ 
   |_|  |_|___\___|_||_/_/ \_\_|\_|___/_/ \_\\
   \n";
$output .= "SYSTEM BOOT... OK\n";
$output .= "DIRECTORY LISTING INITIALIZED.\n\n";

$display_path = str_replace('\\', '/', getcwd());
$prompt_path = "C:" . htmlspecialchars(substr($display_path, strrpos($display_path, '/') ?: ''));
$output .= $prompt_path . "> dir\n\n";

$output .= " Volume in drive C is LOCALHOST\n";
$output .= " Directory of " . htmlspecialchars($display_path) . "\n\n";

// Lien parent ".."
$mtime_parent = date("d/m/Y  H:i", filemtime('..'));
$output .= $mtime_parent . "    &lt;DIR&gt;          " . "         <a href=\"..\">..</a>\n";

// Dossiers
foreach ($dirs as $dir) {
    $mtime = date("d/m/Y  H:i", filemtime($dir));
    $dir_name = htmlspecialchars($dir);
    $output .= $mtime . "    &lt;DIR&gt;          " . "         <a href=\"$dir_name/\">$dir_name/</a>\n";
}

// Fichiers
foreach ($files as $file) {
    $mtime = date("d/m/Y  H:i", filemtime($file));
    $size = filesize($file);
    $formatted_size = formatSizeUnits($size);
    $file_name = htmlspecialchars($file);
    
    $padded_size = str_pad($formatted_size, 14, ' ', STR_PAD_LEFT);

    $output .= $mtime . "                   " . $padded_size . " <a href=\"$file_name\">$file_name</a>\n";
}

$total_items = count($dirs) + count($files) + 1; // +1 pour ".."
$output .= "\n               " . $total_items . " File(s)\n";
$output .= "\n" . $prompt_path . ">";

// Préparation de la chaîne pour JavaScript, en échappant les caractères spéciaux pour les template literals.
$js_output = str_replace(['\\', '`', '${'], ['\\\\', '\\`', '\\${'], $output);

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Index of <?php echo htmlspecialchars(basename(getcwd())); ?></title>
    
    <style>
        * { box-sizing: border-box; }
        html, body {
            margin: 0; padding: 0; width: 100%; height: 100%;
            background-color: #000;
            color: #33FF33;
            font-family: 'Consolas', 'Lucida Console', 'Courier New', monospace;
        }
        canvas#digital-rain {
            position: fixed; top: 0; left: 0; z-index: 1; opacity: 0.2;
        }
        #terminal {
            position: relative; z-index: 2; font-size: 1rem; line-height: 1.4;
            white-space: pre; /* Utiliser 'pre' pour un alignement strict */
            padding: 15px; text-shadow: 0 0 3px rgba(51, 255, 51, 0.4);
        }
        #terminal a {
            color: #33FF33; text-decoration: none;
        }
        #terminal a:hover {
            background-color: #33FF33; color: #000;
        }
        .cursor {
            display: inline-block; width: 10px; height: 1.2rem;
            background-color: #33FF33;
            animation: blink 1s step-end infinite;
            margin-left: 5px; vertical-align: bottom;
            opacity: 0; /* Caché au début */
        }
        @keyframes blink { 50% { opacity: 0; } }
    </style>
</head>
<body>
    <canvas id="digital-rain"></canvas>
    <pre id="terminal"><span id="output"></span><span class="cursor" id="cursor"></span></pre>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const outputElement = document.getElementById('output');
            const cursorElement = document.getElementById('cursor');
            
            // On utilise le texte préparé par PHP. C'est la correction clé.
            const textToType = `<?php echo $js_output; ?>`;
            
            let index = 0;
            const typingSpeed = 5;

            function type() {
                if (index < textToType.length) {
                    let char = textToType[index];

                    // Si on rencontre une balise HTML ou une entité HTML...
                    if (char === '<' || char === '&') {
                        let endTagIndex;
                        if (char === '<') {
                            endTagIndex = textToType.indexOf('>', index); // Trouve la fin de la balise
                        } else {
                            endTagIndex = textToType.indexOf(';', index); // Trouve la fin de l'entité
                        }

                        if (endTagIndex !== -1) {
                            // On extrait la balise/entité complète
                            const fullTag = textToType.substring(index, endTagIndex + 1);
                            outputElement.innerHTML += fullTag;
                            index = endTagIndex; // On saute à la fin de la balise
                        } else {
                            outputElement.append(char); // Fallback
                        }
                    } else {
                        // Sinon, on ajoute simplement le caractère (via .append pour éviter les problèmes d'interprétation HTML)
                        outputElement.append(char);
                    }
                    
                    index++;
                    window.scrollTo(0, document.body.scrollHeight);
                    setTimeout(type, typingSpeed);
                } else {
                    // L'écriture est terminée, on affiche le curseur
                    cursorElement.style.opacity = '1';
                }
            }
            
            type();

            // --- Animation Pluie Numérique ---
            const canvas = document.getElementById('digital-rain');
            const ctx = canvas.getContext('2d');
            canvas.width = window.innerWidth;
            canvas.height = window.innerHeight;
            const alphabet = 'アァカサタナハマヤャラワガザダバパイィキシチニヒミリヰギジヂビピウゥクスツヌフムユュルグズブヅプエェケセテネヘメレヱゲゼデベペオォコソトノホモヨョロヲゴゾドボポヴッンABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
            const fontSize = 16;
            const columns = canvas.width / fontSize;
            const rainDrops = Array.from({ length: columns }).fill(1);

            function drawDigitalRain() {
                ctx.fillStyle = 'rgba(0, 0, 0, 0.05)';
                ctx.fillRect(0, 0, canvas.width, canvas.height);
                ctx.fillStyle = '#33FF33';
                ctx.font = fontSize + 'px monospace';
                rainDrops.forEach((y, i) => {
                    const text = alphabet.charAt(Math.floor(Math.random() * alphabet.length));
                    ctx.fillText(text, i * fontSize, y * fontSize);
                    if (y * fontSize > canvas.height && Math.random() > 0.975) {
                        rainDrops[i] = 0;
                    }
                    rainDrops[i]++;
                });
            }
            setInterval(drawDigitalRain, 35);
            window.addEventListener('resize', () => {
                canvas.width = window.innerWidth;
                canvas.height = window.innerHeight;
                rainDrops.length = canvas.width / fontSize;
                rainDrops.fill(1);
            });
        });
    </script>
</body>
</html>