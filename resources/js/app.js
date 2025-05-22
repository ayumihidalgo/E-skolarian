import './bootstrap';
// import Alpine from 'alpinejs'

// window.Alpine = Alpine

// Alpine.start()
import WebViewer from '@pdftron/webviewer';

document.addEventListener('DOMContentLoaded', function() {
    const viewerElement = document.getElementById('viewer');
    
    if (viewerElement) {
        WebViewer({
            path: '/webviewer',
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