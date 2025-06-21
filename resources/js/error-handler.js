// Add to main layout for better error handling
// resources/js/error-handler.js

class ErrorHandler {
    static handle(xhr, defaultMessage = 'An error occurred') {
        let message = defaultMessage;

        if (xhr.responseJSON) {
            if (xhr.responseJSON.message) {
                message = xhr.responseJSON.message;
            } else if (xhr.responseJSON.errors) {
                // Laravel validation errors
                const errors = Object.values(xhr.responseJSON.errors).flat();
                message = errors.join('<br>');
            }
        } else if (xhr.responseText) {
            try {
                const parsed = JSON.parse(xhr.responseText);
                message = parsed.message || defaultMessage;
            } catch (e) {
                message = defaultMessage;
            }
        }

        // Handle specific HTTP status codes
        switch (xhr.status) {
            case 401:
                message = 'You are not authorized. Please login again.';
                setTimeout(() => {
                    window.location.href = '/login';
                }, 2000);
                break;
            case 403:
                message = 'You do not have permission to perform this action.';
                break;
            case 404:
                message = 'The requested resource was not found.';
                break;
            case 419:
                message = 'Your session has expired. Please refresh the page.';
                setTimeout(() => {
                    window.location.reload();
                }, 2000);
                break;
            case 422:
                // Validation errors - message should already be set above
                break;
            case 429:
                message = 'Too many requests. Please try again later.';
                break;
            case 500:
                message = 'Internal server error. Please try again or contact support.';
                break;
            case 503:
                message = 'Service temporarily unavailable. Please try again later.';
                break;
        }

        return message;
    }

    static showError(xhr, defaultMessage = 'An error occurred') {
        const message = this.handle(xhr, defaultMessage);
        showToast('error', message);
    }

    static setupGlobalHandlers() {
        // Setup global AJAX error handler
        $(document).ajaxError(function (event, xhr, settings, thrownError) {
            // Skip handling for specific URLs if needed
            const skipUrls = ['/notifications/count', '/api/'];
            const shouldSkip = skipUrls.some(url => settings.url.includes(url));

            if (!shouldSkip && xhr.status !== 0) {
                ErrorHandler.showError(xhr);
            }
        });

        // Setup global fetch error handler
        const originalFetch = window.fetch;
        window.fetch = function (...args) {
            return originalFetch.apply(this, args)
                .catch(error => {
                    console.error('Fetch error:', error);
                    showToast('error', 'Network error. Please check your connection.');
                    throw error;
                });
        };
    }
}

// Initialize error handlers when DOM is ready
document.addEventListener('DOMContentLoaded', function () {
    ErrorHandler.setupGlobalHandlers();
});