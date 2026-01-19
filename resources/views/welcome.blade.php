<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BEACONET-mini - Lost & Found</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; background: linear-gradient(135deg, #10B981 0%, #059669 100%); min-height: 100vh; display: flex; align-items: center; justify-content: center; transition: background 0.3s; }
        body.dark-mode { background: linear-gradient(135deg, #1a3a3a 0%, #0f2828 100%); }
        .container { max-width: 600px; width: 100%; margin: 20px; }
        .card { background: white; border-radius: 10px; padding: 40px; box-shadow: 0 10px 40px rgba(0,0,0,0.2); transition: background 0.3s, color 0.3s; }
        .dark-mode .card { background: #1f2937; color: white; }
        h1 { color: #059669; margin-bottom: 10px; text-align: center; }
        .dark-mode h1 { color: #10B981; }
        .subtitle { color: #666; text-align: center; margin-bottom: 30px; }
        .dark-mode .subtitle { color: #bbb; }
        .button-group { display: flex; gap: 15px; margin-top: 30px; }
        a { text-decoration: none; padding: 12px 24px; border-radius: 5px; text-align: center; font-weight: 500; transition: background 0.3s; }
        .btn-primary { background: #10B981; color: white; flex: 1; }
        .btn-primary:hover { background: #059669; }
        .btn-secondary { background: #f0f0f0; color: #333; flex: 1; }
        .dark-mode .btn-secondary { background: #374151; color: white; }
        .dark-mode .btn-secondary:hover { background: #4b5563; }
        .theme-toggle { position: absolute; top: 20px; right: 20px; background: #10B981; color: white; border: none; padding: 10px 15px; border-radius: 5px; cursor: pointer; }
        .dark-mode .theme-toggle { background: #4ade80; color: #1f2937; }
        .theme-toggle:hover { transform: scale(1.05); }
    </style>
</head>
<body>
    <button class="theme-toggle" onclick="toggleTheme()"><i class="fas fa-moon"></i></button>
    <div class="container">
        <div class="card">
            <h1><i class="fas fa-map-pin"></i> BEACONET-mini</h1>
            <p class="subtitle">Find what you've lost on the map</p>
            <div class="button-group">
                <a href="/login" class="btn-primary">Login</a>
                <a href="/register" class="btn-secondary">Register</a>
            </div>
        </div>
    </div>
    <script>
        // Initialize dark mode from localStorage
        if (localStorage.getItem('theme') === 'dark') {
            document.body.classList.add('dark-mode');
        }
        
        function toggleTheme() {
            document.body.classList.toggle('dark-mode');
            const theme = document.body.classList.contains('dark-mode') ? 'dark' : 'light';
            localStorage.setItem('theme', theme);
        }
    </script>
</body>
</html>
