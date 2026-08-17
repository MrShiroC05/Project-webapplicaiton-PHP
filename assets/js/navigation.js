// File role: Inject sticky navigation bar into all pages from a single source.

function initializeNavigation() {
    // Base URL for the application - use absolute path
    const baseUrl = '/Webbapplications';

    // Define navigation links in one central place
    const navLinks = [
        {
            label: 'Home',
            href: baseUrl + '/index.php',
            id: 'nav-home'
        },
        {
            label: 'List Region',
            href: baseUrl + '/Region/listRegion.php',
            id: 'nav-list-region'
        }
    ];

    // Build the nav HTML from the configuration
    const navHtml = `
        <nav class="top-nav">
            <div class="nav-inner">
                <div class="nav-brand">LOL Lore</div>
                <div class="nav-links">
                    ${navLinks.map(link => `<a class="nav-link" href="${link.href}" id="${link.id}">${link.label}</a>`).join('')}
                </div>
            </div>
        </nav>
    `;

    // Insert the nav at the top of the page
    const navContainer = document.getElementById('app-nav');
    if (navContainer) {
        navContainer.insertAdjacentHTML('beforeend', navHtml);
    }

    // Set active state based on the real page route
    const currentPath = window.location.pathname.toLowerCase();
    const isHomePage = currentPath === '/'
        || currentPath.endsWith('/webbapplications/index.php');
    const isListRegionPage = currentPath.includes('/region/');

    const homeLink = document.getElementById('nav-home');
    const listRegionLink = document.getElementById('nav-list-region');

    if (homeLink) homeLink.classList.remove('active');
    if (listRegionLink) listRegionLink.classList.remove('active');

    if (isHomePage && homeLink) {
        homeLink.classList.add('active');
    } else if (isListRegionPage && listRegionLink) {
        listRegionLink.classList.add('active');
    }
}

// Run when DOM is ready
document.addEventListener('DOMContentLoaded', initializeNavigation);
