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

// Chart.js
import Chart from "chart.js/auto";
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

const TEXT_SCALE_STORAGE_KEY = "clinic_text_scale";
const TEXT_SCALE_LEVELS = ["100", "110", "120", "130"];
const TEXT_SCALE_DEFAULT = "100";

function normalizeTextScale(scale) {
	const parsed = String(scale || "").trim();
	return TEXT_SCALE_LEVELS.includes(parsed) ? parsed : TEXT_SCALE_DEFAULT;
}

function updateTextScaleControls(activeScale) {
	document.querySelectorAll("[data-text-scale-btn]").forEach((button) => {
		const isActive = button.getAttribute("data-text-scale-btn") === activeScale;
		button.setAttribute("aria-pressed", isActive ? "true" : "false");
	});

	document.querySelectorAll("[data-text-scale-current]").forEach((label) => {
		const value = Math.round((Number(activeScale) / 100) * 100);
		label.textContent = `${value}%`;
	});
}

export function applyAppTextScale(scale, persist = true) {
	const normalizedScale = normalizeTextScale(scale);

	document.documentElement.setAttribute("data-text-scale", normalizedScale);
	document.documentElement.style.setProperty(
		"--app-text-scale-factor",
		String(Number(normalizedScale) / 100)
	);

	if (persist) {
		try {
			localStorage.setItem(TEXT_SCALE_STORAGE_KEY, normalizedScale);
		} catch (error) {
			console.warn("Could not persist text scale:", error);
		}
	}

	updateTextScaleControls(normalizedScale);
	return normalizedScale;
}

window.applyAppTextScale = applyAppTextScale;

document.addEventListener("DOMContentLoaded", () => {
	let storedScale = TEXT_SCALE_DEFAULT;

	try {
		storedScale = localStorage.getItem(TEXT_SCALE_STORAGE_KEY) || TEXT_SCALE_DEFAULT;
	} catch (error) {
		storedScale = TEXT_SCALE_DEFAULT;
	}

	applyAppTextScale(storedScale, false);

	document.querySelectorAll("[data-text-scale-btn]").forEach((button) => {
		button.addEventListener("click", () => {
			const scale = button.getAttribute("data-text-scale-btn") || TEXT_SCALE_DEFAULT;
			applyAppTextScale(scale, true);
		});
	});
});

// Start Alpine
Alpine.start();

// document.addEventListener("DOMContentLoaded", () => {
//     flatpickr(".datetime-picker", {
//         enableTime: true,
//         dateFormat: "Y-m-d H:i",
//     });
// });
