<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Terminal</title>
    <script src="https://cdn.tailwindcss.com/3.4.3"></script>
    <style>
        body {
            background-color: #000;
            color: #33FF33;
            font-family: 'Courier New', Courier, monospace;
            margin: 0;
            padding: 1rem;
        }
        pre {
            white-space: pre-wrap;
            word-wrap: break-word;
        }
        a {
            color: #33FF33;
            text-decoration: none;
        }
        a:hover {
            color: #99FF99;
            text-decoration: underline;
        }
        #cursor {
            animation: blink 1s step-end infinite;
        }
        @keyframes blink {
            from, to {
                background-color: transparent;
            }
            50% {
                background-color: #33FF33;
            }
        }
    </style>
</head>
<body class="bg-black text-green-400 font-mono p-4">
    <pre id="terminal"></pre>
    <script>
        const terminal = document.getElementById('terminal');
        const cursor = document.createElement('span');
        cursor.id = 'cursor';
        cursor.innerHTML = '&#9608;'; // Block cursor

        const content = [
            '  _______  __   __  _______  _______  __   __  _______  _______ ',
            ' |       ||  | |  ||       ||       ||  | |  ||       ||       |',
            ' |    ___||  |_|  ||    ___||_     _||  |_|  ||    ___||    ___|',
            ' |   |___ |       ||   |___   |   |  |       ||   |___ |   |___ ',
            ' |    ___||       ||    ___|  |   |  |       ||    ___||    ___|',
            ' |   |___ |   _   ||   |___   |   |  |   _   ||   |___ |   |___ ',
            ' |_______||__| |__||_______|  |___|  |__| |__||_______||_______|',
            '                                                               ',
            'System Initializing...',
            ' ',
            'C:\localhost> dir',
            ' ',
            ' Volume in drive C has no label.',
            ' Volume Serial Number is 486A-B56D',
            ' ',
            ' Directory of C:\localhost',
            ' ',
            '01/05/2024  10:15    <DIR>          .',
            '01/05/2024  10:15    <DIR>          <a href="#">..</a>',
            '28/04/2024  14:30    <DIR>          <a href="#">assets</a>',
            '15/03/2024  09:00    <DIR>          <a href="#">includes</a>',
            '22/04/2024  22:10    <DIR>          <a href="#">logs</a>',
            '12/01/2024  18:05           1,245 Ko <a href="#">config.ini</a>',
            '03/04/2024  11:45           8,780 Ko <a href="#">data.json</a>',
            '19/04/2024  16:50             150 Ko <a href="#">README.md</a>',
            '30/04/2024  08:20          12,340 Ko <a href="#">backup.zip</a>',
            '               4 File(s)     22,515 Ko',
            '               5 Dir(s)  142,331,688 Ko free',
            ' ',
            'C:\localhost> '
        ];

        let lineIndex = 0;
        let charIndex = 0;

        function type() {
            if (lineIndex < content.length) {
                const line = content[lineIndex];
                if (charIndex < line.length) {
                    terminal.innerHTML += line.charAt(charIndex);
                    charIndex++;
                    setTimeout(type, 10);
                } else {
                    terminal.innerHTML += '\n';
                    lineIndex++;
                    charIndex = 0;
                    setTimeout(type, 50);
                }
            } else {
                terminal.appendChild(cursor);
            }
        }
        type();
    </script>
</body>
</html>