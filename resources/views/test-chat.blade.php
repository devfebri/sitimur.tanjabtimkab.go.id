<!DOCTYPE html>
<html>
<head>
    <title>Test Chat System</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    @livewireStyles
</head>
<body>
    <div class="container mt-5">
        <h1>🧪 Test Chat System</h1>
        <div class="alert alert-info">
            <strong>Testing Status:</strong>
            <ul class="mb-0">
                <li>✅ Livewire Loaded</li>
                <li>✅ Bootstrap Loaded</li>
                <li>✅ CustomChat Component Ready</li>
            </ul>
        </div>
        
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Chat Component Test</h5>
            </div>
            <div class="card-body">
                @livewire('custom-chat')
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    @livewireScripts
</body>
</html>
