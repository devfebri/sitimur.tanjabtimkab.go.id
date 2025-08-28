import Echo from 'laravel-echo';

window.Echo = new Echo({
    broadcaster: 'reverb',
    key: import.meta.env.VITE_REVERB_APP_KEY,
    wsHost: import.meta.env.VITE_REVERB_HOST,
    wsPort: import.meta.env.VITE_REVERB_PORT ?? 80,
    wssPort: import.meta.env.VITE_REVERB_PORT ?? 443,
    forceTLS: (import.meta.env.VITE_REVERB_SCHEME ?? 'https') === 'https',
    enabledTransports: ['ws', 'wss'],
    authorizer: (channel, options) => {
        return {
            authorize: (socketId, callback) => {
                // Get CSRF token from meta tag
                const token = document.querySelector('meta[name="csrf-token"]');
                const csrfToken = token ? token.getAttribute('content') : '';
                
                console.log('🔐 Authorizing channel:', channel.name, 'with socket:', socketId);
                
                axios.post('/broadcasting/auth', {
                    socket_id: socketId,
                    channel_name: channel.name
                }, {
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    }
                })
                .then(response => {
                    console.log('✅ Channel authorization successful:', response.data);
                    callback(false, response.data);
                })
                .catch(error => {
                    console.error('❌ Echo authorization error:', error.response ? error.response.data : error);
                    callback(true, error);
                });
            }
        };
    },
});

// Debug logging for development
if (import.meta.env.DEV) {
    console.log('🔊 Echo initialized with Reverb config:', {
        broadcaster: 'reverb',
        key: import.meta.env.VITE_REVERB_APP_KEY,
        wsHost: import.meta.env.VITE_REVERB_HOST,
        wsPort: import.meta.env.VITE_REVERB_PORT,
        scheme: import.meta.env.VITE_REVERB_SCHEME
    });
    
    // Log connection events
    window.Echo.connector.pusher.connection.bind('connected', () => {
        console.log('🟢 Echo connected to Reverb server');
    });
    
    window.Echo.connector.pusher.connection.bind('disconnected', () => {
        console.log('🔴 Echo disconnected from Reverb server');
    });
    
    window.Echo.connector.pusher.connection.bind('error', (error) => {
        console.error('💥 Echo connection error:', error);
    });
}
