<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BEACONET-mini - Lost & Found</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); min-height: 100vh; display: flex; align-items: center; justify-content: center; }
        .container { max-width: 600px; width: 100%; margin: 20px; }
        .card { background: white; border-radius: 10px; padding: 40px; box-shadow: 0 10px 40px rgba(0,0,0,0.2); }
        h1 { color: #333; margin-bottom: 10px; text-align: center; }
        .subtitle { color: #666; text-align: center; margin-bottom: 30px; }
        .button-group { display: flex; gap: 15px; margin-top: 30px; }
        a { text-decoration: none; padding: 12px 24px; border-radius: 5px; text-align: center; font-weight: 500; }
        .btn-primary { background: #667eea; color: white; flex: 1; }
        .btn-primary:hover { background: #5568d3; }
        .btn-secondary { background: #f0f0f0; color: #333; flex: 1; }
    </style>
</head>
<body>
    <div class="container">
        <div class="card">
            <h1>BEACONET-mini</h1>
            <p class="subtitle">Find what you've lost on the map</p>
            <div class="button-group">
                <a href="/login" class="btn-primary">Login</a>
                <a href="/register" class="btn-secondary">Register</a>
            </div>
        </div>
    </div>
</body>
</html>
