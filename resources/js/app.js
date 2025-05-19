import './bootstrap';

import Alpine from 'alpinejs';
import flatpickr from "flatpickr";
import "flatpickr/dist/flatpickr.css";
import axios from 'axios';

import Chart from 'chart.js/auto';
window.Chart = Chart;


window.Alpine = Alpine;

Alpine.start();

// document.addEventListener("DOMContentLoaded", () => {
//     flatpickr(".datetime-picker", {
//         enableTime: true,
//         dateFormat: "Y-m-d H:i",
//     });
// });
