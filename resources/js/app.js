// resources/js/app.js

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
  PieController,
  Filler // <-- 1. IMPOR PLUGIN FILLER DI SINI
} from 'chart.js';

// Register the controllers and elements you will use
Chart.register(
    BarController,
    LineController,
    LineElement,
    PointElement,
    CategoryScale,
    LinearScale,
    BarElement,
    Title,
    Tooltip,
    Legend,
    ArcElement,
    PieController,
    Filler // <-- 2. DAFTARKAN PLUGIN FILLER DI SINI
);


window.Alpine = Alpine;
window.Chart = Chart;

Alpine.start();