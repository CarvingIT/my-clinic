/**
 * Dashboard UI/UX Enhancement JavaScript
 * Provides interactive features and improved user experience
 */

class DashboardEnhancer {
    constructor() {
        this.init();
    }

    init() {
        this.setupAnimations();
        this.setupRealTimeUpdates();
        this.setupAccessibility();
        this.setupPerformanceOptimizations();
        this.setupUserPreferences();
    }

    /**
     * Setup entrance animations and micro-interactions
     */
    setupAnimations() {
        // Staggered card animations
        const cards = document.querySelectorAll('.metric-card, .quick-action-card');
        cards.forEach((card, index) => {
            card.style.animationDelay = `${index * 100}ms`;
            card.classList.add('animate-fade-in-up');
        });

        // Intersection Observer for scroll animations
        if ('IntersectionObserver' in window) {
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('animate-slide-in');
                    }
                });
            }, { threshold: 0.1 });

            document.querySelectorAll('.animate-on-scroll').forEach(el => {
                observer.observe(el);
            });
        }
    }

    /**
     * Setup real-time data updates
     */
    setupRealTimeUpdates() {
        // Auto-refresh dashboard data every 5 minutes
        setInterval(() => {
            this.updateDashboardMetrics();
        }, 300000);

        // Update queue count every 30 seconds
        setInterval(() => {
            this.updateQueueCount();
        }, 30000);

        // Live timestamp updates
        this.updateTimestamps();
        setInterval(() => {
            this.updateTimestamps();
        }, 60000);
    }

    /**
     * Update dashboard metrics via AJAX
     */
    async updateDashboardMetrics() {
        try {
            const response = await fetch('/api/dashboard/metrics');
            if (response.ok) {
                const data = await response.json();
                this.updateMetricCards(data);
            }
        } catch (error) {
            console.warn('Failed to update dashboard metrics:', error);
        }
    }

    /**
     * Update queue count
     */
    async updateQueueCount() {
        try {
            const response = await fetch('/api/queue/count');
            if (response.ok) {
                const data = await response.json();
                const queueElements = document.querySelectorAll('[data-queue-count]');
                queueElements.forEach(el => {
                    this.animateNumberChange(el, data.count);
                });
            }
        } catch (error) {
            console.warn('Failed to update queue count:', error);
        }
    }

    /**
     * Animate number changes with counting effect
     */
    animateNumberChange(element, newValue) {
        const currentValue = parseInt(element.textContent) || 0;
        const duration = 1000;
        const steps = 20;
        const stepValue = (newValue - currentValue) / steps;
        const stepDuration = duration / steps;

        let currentStep = 0;
        const timer = setInterval(() => {
            currentStep++;
            const value = Math.round(currentValue + (stepValue * currentStep));
            element.textContent = value;

            if (currentStep >= steps) {
                clearInterval(timer);
                element.textContent = newValue;
            }
        }, stepDuration);
    }

    /**
     * Update relative timestamps
     */
    updateTimestamps() {
        const timestamps = document.querySelectorAll('[data-timestamp]');
        timestamps.forEach(el => {
            const timestamp = el.getAttribute('data-timestamp');
            if (timestamp) {
                el.textContent = this.getRelativeTime(new Date(timestamp));
            }
        });
    }

    /**
     * Get relative time string (e.g., "5 minutes ago")
     */
    getRelativeTime(date) {
        const now = new Date();
        const diffInMs = now - date;
        const diffInMinutes = Math.floor(diffInMs / (1000 * 60));
        const diffInHours = Math.floor(diffInMs / (1000 * 60 * 60));
        const diffInDays = Math.floor(diffInMs / (1000 * 60 * 60 * 24));

        if (diffInMinutes < 1) return 'Just now';
        if (diffInMinutes < 60) return `${diffInMinutes} minute${diffInMinutes > 1 ? 's' : ''} ago`;
        if (diffInHours < 24) return `${diffInHours} hour${diffInHours > 1 ? 's' : ''} ago`;
        return `${diffInDays} day${diffInDays > 1 ? 's' : ''} ago`;
    }

    /**
     * Setup accessibility enhancements
     */
    setupAccessibility() {
        // Keyboard navigation for cards
        const interactiveCards = document.querySelectorAll('[data-interactive]');
        interactiveCards.forEach(card => {
            card.setAttribute('tabindex', '0');
            card.setAttribute('role', 'button');

            card.addEventListener('keydown', (e) => {
                if (e.key === 'Enter' || e.key === ' ') {
                    e.preventDefault();
                    card.click();
                }
            });
        });

        // Screen reader announcements for dynamic content
        this.setupScreenReaderAnnouncements();

        // High contrast mode detection
        if (window.matchMedia('(prefers-contrast: high)').matches) {
            document.body.classList.add('high-contrast');
        }

        // Reduced motion support
        if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
            document.body.classList.add('reduced-motion');
        }
    }

    /**
     * Setup screen reader announcements
     */
    setupScreenReaderAnnouncements() {
        const announcer = document.createElement('div');
        announcer.setAttribute('aria-live', 'polite');
        announcer.setAttribute('aria-atomic', 'true');
        announcer.className = 'sr-only';
        announcer.id = 'sr-announcer';
        document.body.appendChild(announcer);

        // Announce metric updates
        window.announceToScreenReader = (message) => {
            announcer.textContent = message;
            setTimeout(() => {
                announcer.textContent = '';
            }, 1000);
        };
    }

    /**
     * Setup performance optimizations
     */
    setupPerformanceOptimizations() {
        // Lazy loading for images
        if ('loading' in HTMLImageElement.prototype) {
            const images = document.querySelectorAll('img[data-lazy]');
            images.forEach(img => {
                img.loading = 'lazy';
            });
        } else {
            // Fallback for browsers that don't support native lazy loading
            this.setupIntersectionObserverForImages();
        }

        // Preload critical resources
        this.preloadCriticalResources();

        // Setup service worker for offline functionality
        if ('serviceWorker' in navigator) {
            this.setupServiceWorker();
        }
    }

    /**
     * Setup intersection observer for image lazy loading fallback
     */
    setupIntersectionObserverForImages() {
        const imageObserver = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const img = entry.target;
                    img.src = img.dataset.src;
                    img.classList.remove('lazy');
                    imageObserver.unobserve(img);
                }
            });
        });

        document.querySelectorAll('img[data-lazy]').forEach(img => {
            imageObserver.observe(img);
        });
    }

    /**
     * Preload critical resources
     */
    preloadCriticalResources() {
        const criticalRoutes = [
            '/patients',
            '/queue',
            '/users'
        ];

        criticalRoutes.forEach(route => {
            const link = document.createElement('link');
            link.rel = 'prefetch';
            link.href = route;
            document.head.appendChild(link);
        });
    }

    /**
     * Setup service worker
     */
    async setupServiceWorker() {
        try {
            await navigator.serviceWorker.register('/sw.js');
            console.log('Service Worker registered successfully');
        } catch (error) {
            console.warn('Service Worker registration failed:', error);
        }
    }

    /**
     * Setup user preferences
     */
    setupUserPreferences() {
        // Theme preference
        const savedTheme = localStorage.getItem('dashboard-theme');
        if (savedTheme) {
            document.body.classList.add(`theme-${savedTheme}`);
        }

        // Auto-refresh preference
        const autoRefresh = localStorage.getItem('dashboard-auto-refresh');
        if (autoRefresh === 'false') {
            // Disable auto-refresh if user has opted out
            this.disableAutoRefresh();
        }

        // Notification preferences
        this.setupNotifications();
    }

    /**
     * Setup browser notifications
     */
    async setupNotifications() {
        if ('Notification' in window) {
            const permission = await Notification.requestPermission();
            if (permission === 'granted') {
                // Setup notification for queue updates
                this.enableQueueNotifications();
            }
        }
    }

    /**
     * Enable queue notifications
     */
    enableQueueNotifications() {
        let lastQueueCount = 0;

        setInterval(async () => {
            try {
                const response = await fetch('/api/queue/count');
                const data = await response.json();

                if (data.count > lastQueueCount && lastQueueCount > 0) {
                    new Notification('New Patient in Queue', {
                        body: `${data.count} patient${data.count > 1 ? 's' : ''} waiting`,
                        icon: '/favicon.png',
                        badge: '/favicon.png'
                    });
                }

                lastQueueCount = data.count;
            } catch (error) {
                console.warn('Failed to check queue for notifications:', error);
            }
        }, 60000);
    }

    /**
     * Update metric cards with new data
     */
    updateMetricCards(data) {
        Object.keys(data).forEach(key => {
            const element = document.querySelector(`[data-metric="${key}"]`);
            if (element) {
                this.animateNumberChange(element, data[key]);
                // Announce to screen readers
                if (window.announceToScreenReader) {
                    window.announceToScreenReader(`${key} updated to ${data[key]}`);
                }
            }
        });
    }

    /**
     * Disable auto-refresh
     */
    disableAutoRefresh() {
        // Clear existing intervals
        const intervalIds = [window.metricsInterval, window.queueInterval];
        intervalIds.forEach(id => {
            if (id) clearInterval(id);
        });
    }
}

// Initialize dashboard enhancements when DOM is ready
document.addEventListener('DOMContentLoaded', () => {
    new DashboardEnhancer();
});

// Export for module use
if (typeof module !== 'undefined' && module.exports) {
    module.exports = DashboardEnhancer;
}
