/**
 * AJAX Helper Wrapper
 * Handles loading spinners, success toasts, and error toasts automatically
 */

const AJAX = {
    loading: false,
    
    /**
     * Make AJAX request with loading indicator
     */
    request: async function(url, options = {}) {
        this.showLoading();
        
        try {
            const response = await fetch(url, {
                ...options,
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-Token': window.csrfToken(),
                    ...options.headers
                }
            });
            
            const data = await response.json();
            this.hideLoading();
            
            if (data.success) {
                this.showSuccess(data.message || 'Operation completed successfully');
            } else {
                this.showError(data.message || 'An error occurred');
            }
            
            return data;
        } catch (error) {
            this.hideLoading();
            this.showError('Network error. Please try again.');
            console.error('AJAX Error:', error);
            throw error;
        }
    },
    
    /**
     * Show loading indicator
     */
    showLoading: function() {
        if (!this.loading) {
            this.loading = true;
            // Create loading overlay if not exists
            if (!document.getElementById('ajax-loading')) {
                const overlay = document.createElement('div');
                overlay.id = 'ajax-loading';
                overlay.className = 'fixed inset-0 bg-black/50 z-[9999] flex items-center justify-center';
                overlay.innerHTML = `
                    <div class="bg-white p-6 rounded-lg shadow-xl flex items-center gap-3">
                        <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-rccg-red"></div>
                        <span class="text-gray-700 font-medium">Processing...</span>
                    </div>
                `;
                document.body.appendChild(overlay);
            }
        }
    },
    
    /**
     * Hide loading indicator
     */
    hideLoading: function() {
        const loading = document.getElementById('ajax-loading');
        if (loading) {
            loading.remove();
        }
        this.loading = false;
    },
    
    /**
     * Show success toast
     */
    showSuccess: function(message) {
        this.showToast(message, 'success');
    },
    
    /**
     * Show error toast
     */
    showError: function(message) {
        this.showToast(message, 'error');
    },
    
    /**
     * Show generic toast
     */
    showToast: function(message, type = 'info') {
        const toast = document.createElement('div');
        toast.className = `fixed bottom-4 right-4 z-[9999] bg-${type === 'success' ? 'green' : (type === 'error' ? 'red' : 'gray')}-600 text-white px-6 py-4 rounded-lg shadow-xl flex items-center gap-3 animate-slide-up`;
        toast.innerHTML = `
            <i class="fas fa-${type === 'success' ? 'check-circle' : (type === 'error' ? 'exclamation-circle' : 'info-circle')} text-2xl"></i>
            <span class="font-medium">${message}</span>
            <button onclick="this.parentElement.remove()" class="ml-4 text-white/80 hover:text-white">
                <i class="fas fa-times"></i>
            </button>
        `;
        document.body.appendChild(toast);
        
        setTimeout(() => {
            toast.remove();
        }, 5000);
    }
};

// Make AJAX globally available
window.AJAX = AJAX;
