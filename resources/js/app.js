import './bootstrap';

import Alpine from 'alpinejs';

// Import Chart.js and necessary components
import {
  Chart,
  LineController,
  LineElement,
  PointElement,
  LinearScale,
  Title,
  CategoryScale,
  Tooltip,
  Legend,
  BarController, 
  BarElement, 
  ArcElement, 
  PieController
} from 'chart.js';

// Register the controllers and elements you will use
Chart.register(BarController, LineController, LineElement, PointElement,CategoryScale, LinearScale, BarElement, Title, Tooltip, Legend, ArcElement, PieController);


window.Alpine = Alpine;
window.Chart = Chart; // Make Chart.js globally accessible if needed in other scripts or inline

Alpine.start();