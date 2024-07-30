// resources/js/app.js

// Example: resources/js/app.js
// app.js
import 'bootstrap';

Echo.private(`instance.${instanceId}`)
    .listen('ConsoleOutputUpdated', (e) => {
        console.log(e.output);
        // Append the output to the console display
        const consoleDisplay = document.getElementById('console-display');
        consoleDisplay.innerHTML += `<p>${e.output}</p>`;
    });
