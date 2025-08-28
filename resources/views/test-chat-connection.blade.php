@extends('layouts.app')

@section('title', 'Test Chat Connection')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-12">
            <h2>Test Chat Real-time Connection</h2>
            
            <div class="card mb-3">
                <div class="card-header">
                    <h5>Connection Status</h5>
                </div>
                <div class="card-body">
                    <div id="connection-status" class="alert alert-info">
                        Checking connection...
                    </div>
                </div>
            </div>
            
            <div class="card mb-3">
                <div class="card-header">
                    <h5>User Info</h5>
                </div>
                <div class="card-body">
                    <p><strong>User ID:</strong> {{ auth()->id() }}</p>
                    <p><strong>Name:</strong> {{ auth()->user()->name }}</p>
                    <p><strong>Role:</strong> {{ auth()->user()->role }}</p>
                </div>
            </div>
            
            <div class="card mb-3">
                <div class="card-header">
                    <h5>Test Controls</h5>
                </div>
                <div class="card-body">
                    <button id="test-connection" class="btn btn-primary">Test Echo Connection</button>
                    <button id="test-auth" class="btn btn-info">Test Channel Auth</button>
                    <button id="send-test-message" class="btn btn-success">Send Test Message</button>
                </div>
            </div>
            
            <div class="card">
                <div class="card-header">
                    <h5>Debug Messages</h5>
                </div>
                <div class="card-body">
                    <div id="debug-messages" style="height: 300px; overflow-y: scroll; background: #f8f9fa; padding: 10px;">
                        <small class="text-muted">Debug messages will appear here...</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const statusDiv = document.getElementById('connection-status');
    const debugDiv = document.getElementById('debug-messages');
    
    function addDebugMessage(message, type = 'info') {
        const time = new Date().toLocaleTimeString();
        const colorClass = type === 'error' ? 'text-danger' : (type === 'success' ? 'text-success' : 'text-info');
        debugDiv.innerHTML += `<div class="${colorClass}"><small>[${time}] ${message}</small></div>`;
        debugDiv.scrollTop = debugDiv.scrollHeight;
    }
    
    // Check if Echo is loaded
    if (typeof window.Echo !== 'undefined' && window.Echo) {
        statusDiv.innerHTML = '<div class="alert alert-success">✅ Echo loaded successfully</div>';
        addDebugMessage('Echo is loaded and ready', 'success');
        
        // Test connection events
        if (window.Echo.connector && window.Echo.connector.pusher) {
            window.Echo.connector.pusher.connection.bind('connected', () => {
                addDebugMessage('🟢 Connected to Reverb server', 'success');
                statusDiv.innerHTML = '<div class="alert alert-success">✅ Connected to Reverb server</div>';
            });
            
            window.Echo.connector.pusher.connection.bind('disconnected', () => {
                addDebugMessage('🔴 Disconnected from Reverb server', 'error');
                statusDiv.innerHTML = '<div class="alert alert-warning">⚠️ Disconnected from Reverb server</div>';
            });
            
            window.Echo.connector.pusher.connection.bind('error', (error) => {
                addDebugMessage('💥 Connection error: ' + JSON.stringify(error), 'error');
                statusDiv.innerHTML = '<div class="alert alert-danger">❌ Connection error</div>';
            });
        }
    } else {
        statusDiv.innerHTML = '<div class="alert alert-danger">❌ Echo not loaded</div>';
        addDebugMessage('Echo is not loaded - check Vite build', 'error');
    }
    
    // Test connection button
    document.getElementById('test-connection').addEventListener('click', function() {
        addDebugMessage('Testing Echo connection...');
        
        if (window.Echo) {
            try {
                // Try to connect to a test channel
                const channel = window.Echo.private('test-channel');
                addDebugMessage('Attempting to connect to test-channel', 'info');
                
                channel.listen('TestEvent', (e) => {
                    addDebugMessage('Received test event: ' + JSON.stringify(e), 'success');
                });
                
                addDebugMessage('Test channel subscription created', 'success');
            } catch (error) {
                addDebugMessage('Error testing connection: ' + error.message, 'error');
            }
        } else {
            addDebugMessage('Echo not available for testing', 'error');
        }
    });
    
    // Test auth button
    document.getElementById('test-auth').addEventListener('click', function() {
        addDebugMessage('Testing channel authorization...');
        
        fetch('/test-broadcast')
            .then(response => response.json())
            .then(data => {
                addDebugMessage('Auth test response: ' + JSON.stringify(data), 'success');
            })
            .catch(error => {
                addDebugMessage('Auth test error: ' + error.message, 'error');
            });
    });
    
    // Send test message button
    document.getElementById('send-test-message').addEventListener('click', function() {
        addDebugMessage('Sending test message...');
        
        fetch('/test-send-message', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            addDebugMessage('Send message response: ' + JSON.stringify(data), 'success');
        })
        .catch(error => {
            addDebugMessage('Send message error: ' + error.message, 'error');
        });
    });
});
</script>
@endpush
@endsection
