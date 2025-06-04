import './bootstrap';
import './super-admin/admin-dropdown.js';

// import Alpine from 'alpinejs'

// window.Alpine = Alpine

// Alpine.start()
import WebViewer from '@pdftron/webviewer';

document.addEventListener('DOMContentLoaded', function() {
    const viewerElement = document.getElementById('viewer');
    
    if (viewerElement) {
        WebViewer({
            path: '/webviewer',
            licenseKey: 'demo:1748938649155:61cd8324030000000018cc849f3e69b5137f91f119797e667e61f32ee7',
            initialDoc: null, // Don't set an initial document by default
        }, viewerElement).then(instance => {
            // Store the WebViewer instance for later use
            window.webviewerInstance = instance;

            // You can customize the viewer here
            const { docViewer, annotManager } = instance;
            
            // Example: listen for document loaded event
            docViewer.on('documentLoaded', () => {
                console.log('Document loaded');
            });
        });
    }
});