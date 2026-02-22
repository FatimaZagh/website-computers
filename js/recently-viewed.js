/**
 * Recently Viewed Products Management
 * Handles tracking and displaying recently viewed products using localStorage
 */

class RecentlyViewedManager {
    constructor() {
        this.storageKey = 'computer_garage_recently_viewed';
        this.maxItems = 12; // Maximum number of items to store
        this.displayLimit = 6; // Maximum number of items to display
        this.isLoggedIn = this.checkLoginStatus();
        this.userEmail = this.getUserEmail();
        this.init();
    }

    init() {
        // Load and display recently viewed products on page load
        this.displayRecentlyViewed();
    }

    /**
     * Check if user is logged in
     * @returns {boolean} Login status
     */
    checkLoginStatus() {
        // Check if user login status is passed from PHP
        return window.userLoggedIn || false;
    }

    /**
     * Get user email from global variable set by PHP
     * @returns {string|null} User email
     */
    getUserEmail() {
        return window.userEmail || null;
    }

    /**
     * Add a product to recently viewed list (hybrid approach)
     * @param {Object} product - Product object with id, name, image, price, discount
     */
    async addProduct(product) {
        try {
            if (this.isLoggedIn && this.userEmail) {
                // Save to database for logged-in users
                await this.saveToDatabase(product);
            } else {
                // Save to localStorage for guest users
                this.saveToLocalStorage(product);
            }

            // Update display
            this.displayRecentlyViewed();

        } catch (error) {
            console.error('Error adding product to recently viewed:', error);
        }
    }

    /**
     * Save product to database via API
     * @param {Object} product - Product object
     */
    async saveToDatabase(product) {
        try {
            const response = await fetch('api/save_recently_viewed.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    product_id: product.id,
                    product_name: product.name,
                    product_image: product.image,
                    product_price: product.price,
                    product_discount: product.discount
                })
            });

            const result = await response.json();

            if (!result.success) {
                console.error('Failed to save to database:', result.error);
                // Fallback to localStorage
                this.saveToLocalStorage(product);
            }
        } catch (error) {
            console.error('Database save error:', error);
            // Fallback to localStorage
            this.saveToLocalStorage(product);
        }
    }

    /**
     * Save product to localStorage
     * @param {Object} product - Product object
     */
    saveToLocalStorage(product) {
        try {
            let recentlyViewed = this.getFromLocalStorage();

            // Remove product if it already exists (to avoid duplicates)
            recentlyViewed = recentlyViewed.filter(item => item.id !== product.id);

            // Add product to the beginning of the array
            recentlyViewed.unshift({
                id: product.id,
                name: product.name,
                image: product.image,
                price: product.price,
                discount: product.discount,
                viewedAt: new Date().toISOString()
            });

            // Limit the number of stored items
            if (recentlyViewed.length > this.maxItems) {
                recentlyViewed = recentlyViewed.slice(0, this.maxItems);
            }

            // Save to localStorage
            localStorage.setItem(this.storageKey, JSON.stringify(recentlyViewed));

        } catch (error) {
            console.error('Error saving to localStorage:', error);
        }
    }

    /**
     * Get recently viewed products (hybrid approach)
     * @returns {Array} Array of recently viewed products
     */
    async getRecentlyViewed() {
        try {
            if (this.isLoggedIn && this.userEmail) {
                return await this.getFromDatabase();
            } else {
                return this.getFromLocalStorage();
            }
        } catch (error) {
            console.error('Error getting recently viewed products:', error);
            return this.getFromLocalStorage(); // Fallback to localStorage
        }
    }

    /**
     * Get recently viewed products from database
     * @returns {Array} Array of recently viewed products
     */
    async getFromDatabase() {
        try {
            const response = await fetch('api/get_recently_viewed.php?limit=' + this.displayLimit);
            const result = await response.json();

            if (result.success) {
                return result.products;
            } else {
                console.error('Failed to get from database:', result.error);
                // Fallback to localStorage
                return this.getFromLocalStorage();
            }
        } catch (error) {
            console.error('Database fetch error:', error);
            return this.getFromLocalStorage();
        }
    }

    /**
     * Get recently viewed products from localStorage
     * @returns {Array} Array of recently viewed products
     */
    getFromLocalStorage() {
        try {
            const stored = localStorage.getItem(this.storageKey);
            return stored ? JSON.parse(stored) : [];
        } catch (error) {
            console.error('Error getting from localStorage:', error);
            return [];
        }
    }

    /**
     * Clear all recently viewed products (hybrid approach)
     */
    async clearRecentlyViewed() {
        try {
            if (this.isLoggedIn && this.userEmail) {
                // Clear from database
                await this.clearFromDatabase();
            } else {
                // Clear from localStorage
                localStorage.removeItem(this.storageKey);
            }

            this.displayRecentlyViewed();
        } catch (error) {
            console.error('Error clearing recently viewed products:', error);
        }
    }

    /**
     * Clear all recently viewed products from database
     */
    async clearFromDatabase() {
        try {
            const response = await fetch('api/remove_recently_viewed.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    clear_all: true
                })
            });

            const result = await response.json();

            if (!result.success) {
                console.error('Failed to clear from database:', result.error);
                // Also clear localStorage as fallback
                localStorage.removeItem(this.storageKey);
            }
        } catch (error) {
            console.error('Database clear error:', error);
            // Fallback to localStorage
            localStorage.removeItem(this.storageKey);
        }
    }

    /**
     * Remove a specific product from recently viewed (hybrid approach)
     * @param {string|number} productId - Product ID to remove
     */
    async removeProduct(productId) {
        try {
            if (this.isLoggedIn && this.userEmail) {
                // Remove from database
                await this.removeFromDatabase(productId);
            } else {
                // Remove from localStorage
                let recentlyViewed = this.getFromLocalStorage();
                recentlyViewed = recentlyViewed.filter(item => item.id != productId);
                localStorage.setItem(this.storageKey, JSON.stringify(recentlyViewed));
            }

            this.displayRecentlyViewed();
        } catch (error) {
            console.error('Error removing product from recently viewed:', error);
        }
    }

    /**
     * Remove a specific product from database
     * @param {string|number} productId - Product ID to remove
     */
    async removeFromDatabase(productId) {
        try {
            const response = await fetch('api/remove_recently_viewed.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    product_id: productId
                })
            });

            const result = await response.json();

            if (!result.success) {
                console.error('Failed to remove from database:', result.error);
                // Fallback to localStorage
                let recentlyViewed = this.getFromLocalStorage();
                recentlyViewed = recentlyViewed.filter(item => item.id != productId);
                localStorage.setItem(this.storageKey, JSON.stringify(recentlyViewed));
            }
        } catch (error) {
            console.error('Database remove error:', error);
            // Fallback to localStorage
            let recentlyViewed = this.getFromLocalStorage();
            recentlyViewed = recentlyViewed.filter(item => item.id != productId);
            localStorage.setItem(this.storageKey, JSON.stringify(recentlyViewed));
        }
    }

    /**
     * Check if recently viewed should be displayed on current page
     * @returns {boolean} Whether to show recently viewed section
     */
    shouldDisplayRecentlyViewed() {
        // Get current page filename
        const currentPage = window.location.pathname.split('/').pop().toLowerCase();

        // List of pages where recently viewed should NOT be displayed
        const excludedPages = [
            'about-us.php',
            'contactus.php',
            'contact-us.php'
        ];

        return !excludedPages.includes(currentPage);
    }

    /**
     * Display recently viewed products on the page
     */
    async displayRecentlyViewed() {
        const container = document.getElementById('recently-viewed-products');
        const section = document.getElementById('recently-viewed-section');

        if (!container || !section) {
            return; // Elements not found on this page
        }

        // Check if recently viewed should be displayed on this page
        if (!this.shouldDisplayRecentlyViewed()) {
            section.style.display = 'none';
            return;
        }

        const recentlyViewed = await this.getRecentlyViewed();

        if (recentlyViewed.length === 0) {
            section.style.display = 'none';
            return;
        }

        // Show section
        section.style.display = 'block';

        // Clear existing content
        container.innerHTML = '';

        // Display products (limit to displayLimit)
        const productsToShow = recentlyViewed.slice(0, this.displayLimit);

        productsToShow.forEach(product => {
            const productCard = this.createProductCard(product);
            container.appendChild(productCard);
        });
    }

    /**
     * Create a product card element for horizontal scroll design
     * @param {Object} product - Product object
     * @returns {HTMLElement} Product card element
     */
    createProductCard(product) {
        const cardDiv = document.createElement('div');
        cardDiv.className = 'recently-viewed-card';

        // Calculate discounted price
        const discountAmount = (product.price * product.discount) / 100;
        const finalPrice = product.price - discountAmount;

        // Create the card content
        cardDiv.innerHTML = `
            <button class="recently-viewed-remove-btn"
                    onclick="recentlyViewedManager.removeProduct('${product.id}')"
                    title="Remove from recently viewed">×</button>

            <a href="proddetails.php?pid=${product.id}" style="text-decoration: none; color: inherit;">
                <img src="uploads/${product.image}" alt="${product.name}">
                <div class="product-name">${this.escapeHtml(product.name)}</div>
            </a>

            <div class="product-price">
                ${product.discount > 0 ?
                    `<span class="original-price">₪${product.price}</span>
                     <span class="current-price">₪${finalPrice.toFixed(2)}</span>
                     <div class="discount-badge">${product.discount}% off</div>` :
                    `<span class="current-price">₪${product.price}</span>`
                }
            </div>

            <a href="proddetails.php?pid=${product.id}" class="view-btn">View</a>
        `;

        return cardDiv;
    }

    /**
     * Escape HTML to prevent XSS
     * @param {string} text - Text to escape
     * @returns {string} Escaped text
     */
    escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    /**
     * Format time ago string
     * @param {string} dateString - ISO date string
     * @returns {string} Formatted time ago string
     */
    formatTimeAgo(dateString) {
        const now = new Date();
        const viewed = new Date(dateString);
        const diffMs = now - viewed;
        const diffMins = Math.floor(diffMs / 60000);
        const diffHours = Math.floor(diffMs / 3600000);
        const diffDays = Math.floor(diffMs / 86400000);

        if (diffMins < 1) return 'Just now';
        if (diffMins < 60) return `${diffMins} min ago`;
        if (diffHours < 24) return `${diffHours} hour${diffHours > 1 ? 's' : ''} ago`;
        return `${diffDays} day${diffDays > 1 ? 's' : ''} ago`;
    }
}

// Initialize the recently viewed manager
const recentlyViewedManager = new RecentlyViewedManager();

// Global function to add product (called from product detail pages)
function addToRecentlyViewed(productId, productName, productImage, productPrice, productDiscount = 0) {
    recentlyViewedManager.addProduct({
        id: productId,
        name: productName,
        image: productImage,
        price: parseFloat(productPrice),
        discount: parseInt(productDiscount) || 0
    });
}

// Export for use in other scripts if needed
if (typeof module !== 'undefined' && module.exports) {
    module.exports = RecentlyViewedManager;
}
