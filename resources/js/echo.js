import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

window.Pusher = Pusher;

window.Echo = new Echo({
    broadcaster: 'reverb',
    key: import.meta.env.VITE_REVERB_APP_KEY || 'nexus_key',
    wsHost: import.meta.env.VITE_REVERB_HOST || window.location.hostname,
    wsPort: import.meta.env.VITE_REVERB_PORT || 8080,
    wssPort: import.meta.env.VITE_REVERB_PORT || 443,
    forceTLS: (import.meta.env.VITE_REVERB_SCHEME || 'http') === 'https',
    enabledTransports: ['ws', 'wss'],
});

// Realtime listeners
if (window.tenantId) {
    window.Echo.private(`tenant.${window.tenantId}`)
        .listen('.task.updated', (e) => {
            console.log('[RealTime] Task Updated:', e);
            if (window.Livewire) {
                window.Livewire.dispatch('refreshTaskBoard');
            }
        });
}
