<!DOCTYPE html>
<html>
<head>
    <title>Chat Broadcasting Debug</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Use same Vite configuration as main app -->
    @vite(['resources/js/app.js'])
</head>
<body>
    <div class="container mt-5">
        <h1>🔧 Chat Broadcasting Debug</h1>
        <p>User: <strong>{{ auth()->user()->name ?? 'Not logged in' }}</strong></p>
        
        <div id="status" class="alert alert-info">
            <strong>Status:</strong> <span id="status-text">Initializing...</span>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5>🎧 Listen for Messages</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label>Conversation ID:</label>
                            <input type="number" id="listen-conversation-id" value="1" class="form-control">
                        </div>
                        <button onclick="subscribeToConversation()" class="btn btn-primary">Subscribe</button>
                        <button onclick="unsubscribeFromConversation()" class="btn btn-secondary">Unsubscribe</button>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5>📤 Send Test Message</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label>Conversation ID:</label>
                            <input type="number" id="conversation-id" value="1" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label>Message:</label>
                            <input type="text" id="test-message" value="Hello from {{ auth()->user()->name ?? 'Test User' }}" class="form-control">
                        </div>
                        <button onclick="sendTestMessage()" class="btn btn-success">Send Message</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="card mt-4">
            <div class="card-header">
                <h5>📋 Debug Log</h5>
            </div>
            <div class="card-body">
                <div id="debug-log" style="height: 300px; overflow-y: scroll; background: #f8f9fa; padding: 10px; font-family: monospace; font-size: 12px;">
                    <div><strong>Debug Log:</strong></div>
                </div>
            </div>
        </div>
    </div>

    <script>
        let currentChannel = null;
        let logs = [];

        function log(message) {
            const timestamp = new Date().toLocaleTimeString();
            const logMessage = `[${timestamp}] ${message}`;
            logs.push(logMessage);
            
            const debugLog = document.getElementById('debug-log');
            debugLog.innerHTML = '<div><strong>Debug Log:</strong></div>' + logs.map(log => `<div>${log}</div>`).join('');
            debugLog.scrollTop = debugLog.scrollHeight;
            
            console.log(logMessage);
        }

        function updateStatus(status, alertClass = 'alert-info') {
            document.getElementById('status-text').textContent = status;
            document.getElementById('status').className = `alert ${alertClass}`;
        }

        // Initialize when DOM is ready
        document.addEventListener('DOMContentLoaded', function() {
            log('🚀 Page loaded, checking Echo...');
            
            setTimeout(() => {
                if (typeof Echo !== 'undefined') {
                    log('✅ Echo is available and loaded');
                    updateStatus('Echo loaded successfully', 'alert-success');
                } else {
                    log('❌ Echo is not available');
                    updateStatus('Echo not loaded - check console for errors', 'alert-danger');
                }
            }, 2000);
        });

        function subscribeToConversation() {
            const conversationId = document.getElementById('listen-conversation-id').value;
            
            if (typeof Echo === 'undefined') {
                log('❌ Cannot subscribe: Echo not available');
                updateStatus('Echo not available', 'alert-danger');
                return;
            }
            
            if (currentChannel) {
                log('🔄 Unsubscribing from previous channel...');
                currentChannel.unsubscribe();
            }
            
            log(`🎧 Subscribing to chat.${conversationId}...`);
            
            try {
                currentChannel = Echo.private(`chat.${conversationId}`)
                    .listen('MessageSent', (e) => {
                        log(`📨 MESSAGE RECEIVED: ${JSON.stringify(e)}`);
                        updateStatus(`Message received on chat.${conversationId}!`, 'alert-success');
                    })
                    .error((error) => {
                        log(`❌ Channel error: ${JSON.stringify(error)}`);
                        updateStatus('Channel subscription error', 'alert-danger');
                    });
                
                log(`✅ Successfully subscribed to chat.${conversationId}`);
                updateStatus(`Listening to chat.${conversationId}`, 'alert-info');
                
            } catch (error) {
                log(`❌ Subscription failed: ${error.message}`);
                updateStatus('Subscription failed', 'alert-danger');
            }
        }

        function unsubscribeFromConversation() {
            if (currentChannel) {
                currentChannel.unsubscribe();
                currentChannel = null;
                log('✅ Unsubscribed from conversation');
                updateStatus('Unsubscribed', 'alert-secondary');
            } else {
                log('⚠️ No active channel to unsubscribe from');
            }
        }

        function sendTestMessage() {
            const conversationId = document.getElementById('conversation-id').value;
            const message = document.getElementById('test-message').value;
            
            log(`📤 Sending test message to conversation ${conversationId}...`);
            
            fetch('/test-send-message', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    conversation_id: conversationId,
                    message: message
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    log('✅ Test message sent successfully and event fired');
                    updateStatus('Message sent successfully', 'alert-success');
                } else {
                    log(`❌ Failed to send test message: ${data.message}`);
                    updateStatus('Failed to send message', 'alert-danger');
                }
            })
            .catch(error => {
                log(`❌ Error sending test message: ${error.message}`);
                updateStatus('Error sending message', 'alert-danger');
            });
        }
    </script>
</body>
</html>
