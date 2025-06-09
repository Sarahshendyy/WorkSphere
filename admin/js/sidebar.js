// Sidebar Toggle Functionality
document.addEventListener('DOMContentLoaded', function() {
    const sidebar = document.querySelector('.sidebar');
    const mainContent = document.querySelector('.main-content');
    const toggleBtn = document.getElementById('toggleSidebar');
    
    // Check for saved state
    const isSidebarCollapsed = localStorage.getItem('sidebarCollapsed') === 'true';
    if (isSidebarCollapsed) {
        sidebar.classList.add('collapsed');
        mainContent.classList.add('expanded');
        toggleBtn.classList.add('collapsed');
    }

    // Check if we're on mobile
    const isMobile = window.innerWidth <= 768;
    if (isMobile) {
        sidebar.classList.remove('collapsed');
        mainContent.classList.remove('expanded');
    }

    toggleBtn.addEventListener('click', function() {
        if (isMobile) {
            sidebar.classList.toggle('active');
        } else {
            sidebar.classList.toggle('collapsed');
            mainContent.classList.toggle('expanded');
            toggleBtn.classList.toggle('collapsed');
            
            // Save state only for desktop
            localStorage.setItem('sidebarCollapsed', sidebar.classList.contains('collapsed'));
        }
    });

    // Handle window resize
    window.addEventListener('resize', function() {
        const isMobile = window.innerWidth <= 768;
        if (isMobile) {
            sidebar.classList.remove('collapsed');
            mainContent.classList.remove('expanded');
        } else {
            const isCollapsed = localStorage.getItem('sidebarCollapsed') === 'true';
            if (isCollapsed) {
                sidebar.classList.add('collapsed');
                mainContent.classList.add('expanded');
                toggleBtn.classList.add('collapsed');
            }
        }
    });
}); 