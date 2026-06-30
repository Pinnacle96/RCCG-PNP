/**
 * Admin Panel JavaScript
 */

document.addEventListener('DOMContentLoaded', function() {
    // Mobile menu toggle
    const mobileToggle = document.getElementById('mobile-menu-toggle');
    const mobileMenu = document.getElementById('mobile-menu');
    
    if (mobileToggle && mobileMenu) {
        mobileToggle.addEventListener('click', function() {
            mobileMenu.classList.remove('hidden');
        });
        
        document.getElementById('mobile-menu-close').addEventListener('click', function() {
            mobileMenu.classList.add('hidden');
        });
        
        mobileMenu.addEventListener('click', function(e) {
            if (e.target === mobileMenu) {
                mobileMenu.classList.add('hidden');
            }
        });
    }
    
    // DataTables initialization
    if (typeof DataTable !== 'undefined') {
        document.querySelectorAll('.data-table').forEach(function(table) {
            new DataTable(table, {
                pageLength: 25,
                responsive: true,
                language: {
                    search: 'Search:',
                    lengthMenu: 'Show _MENU_ entries'
                }
            });
        });
    }
    
    // Chart.js initialization
    window.initChart = function(elementId, type, data, options = {}) {
        const ctx = document.getElementById(elementId).getContext('2d');
        return new Chart(ctx, {
            type: type,
            data: data,
            options: options
        });
    };
    
    // Confirm delete
    window.confirmDelete = function(message = 'Are you sure you want to delete this?') {
        return confirm(message);
    };
    
    // Print page
    window.printPage = function() {
        window.print();
    };
});
