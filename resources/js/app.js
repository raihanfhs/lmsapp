import './bootstrap';

import Alpine from 'alpinejs';

// Import Chart.js and necessary components
import { Chart, BarController, CategoryScale, LinearScale, BarElement, Title, Tooltip, Legend } from 'chart.js';

// Register the controllers and elements you will use
Chart.register(BarController, CategoryScale, LinearScale, BarElement, Title, Tooltip, Legend);

window.Alpine = Alpine;
window.Chart = Chart; // Make Chart.js globally accessible if needed in other scripts or inline

Alpine.start();