/**
 * Member Portal JavaScript
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
    
    // Download PDF
    window.downloadPDF = function(url) {
        window.location.href = url;
    };
    
    // View giving history
    window.viewGivingHistory = function() {
        window.location.href = '/portal/giving';
    };
    
    // View attendance history
    window.viewAttendanceHistory = function() {
        window.location.href = '/portal/attendance';
    };
});
