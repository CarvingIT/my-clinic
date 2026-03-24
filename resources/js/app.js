import "./bootstrap";

// jQuery (required by Select2)
import $ from "jquery";
window.$ = window.jQuery = $;

// Alpine.js
import Alpine from "alpinejs";
window.Alpine = Alpine;

// Utilities
import flatpickr from "flatpickr";
import "flatpickr/dist/flatpickr.css";
import axios from "axios";
window.axios = axios;

// Chart.js with plugin
import Chart from "chart.js/auto";
import ChartDataLabels from "chartjs-plugin-datalabels";
Chart.register(ChartDataLabels);
window.Chart = Chart;

// Select2
import select2 from "select2";
import "select2/dist/css/select2.css";
window.select2 = select2;

// Bootstrap (import as namespace, not default)
import * as bootstrap from "bootstrap";
window.bootstrap = bootstrap;

// Sortable.js
import Sortable from "sortablejs";
window.Sortable = Sortable;

// html2canvas
import html2canvas from "html2canvas";
window.html2canvas = html2canvas;

// jsPDF
import { jsPDF } from "jspdf";
window.jsPDF = jsPDF;

// Animate CSS Grid
import "animate-css-grid";
// Dashboard Enhancements
import './dashboard-enhancements';
// Start Alpine
Alpine.start();

// document.addEventListener("DOMContentLoaded", () => {
//     flatpickr(".datetime-picker", {
//         enableTime: true,
//         dateFormat: "Y-m-d H:i",
//     });
// });
