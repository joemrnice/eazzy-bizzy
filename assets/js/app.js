// EazzyBizzy App JavaScript

const API_BASE = '/api';

// Utility functions
const api = {
  async request(endpoint, options = {}) {
    const response = await fetch(`${API_BASE}${endpoint}`, {
      headers: {
        'Content-Type': 'application/json',
        ...options.headers,
      },
      ...options,
    });
    return response.json();
  },
  
  get(endpoint) {
    return this.request(endpoint);
  },
  
  post(endpoint, data) {
    return this.request(endpoint, {
      method: 'POST',
      body: JSON.stringify(data),
    });
  },
};

// Cart functionality
const cart = {
  async add(productId, quantity = 1, variantId = null) {
    return api.post('/cart.php?action=add', {
      product_id: productId,
      quantity,
      variant_id: variantId,
    });
  },
  
  async get() {
    return api.get('/cart.php?action=get');
  },
  
  async update(id, quantity) {
    return api.post('/cart.php?action=update', { id, quantity });
  },
  
  async remove(id) {
    return api.post('/cart.php?action=remove', { id });
  },
  
  async clear() {
    return api.post('/cart.php?action=clear');
  },
  
  async getCount() {
    return api.get('/cart.php?action=count');
  },
};

// Wishlist functionality
const wishlist = {
  async toggle(productId) {
    const check = await api.get(`/wishlist.php?action=check&product_id=${productId}`);
    if (check.data.in_wishlist) {
      return api.post('/wishlist.php?action=remove', { product_id: productId });
    } else {
      return api.post('/wishlist.php?action=add', { product_id: productId });
    }
  },
};

// Auth functionality
const auth = {
  async login(email, password) {
    return api.post('/auth.php?action=login', { email, password });
  },
  
  async register(data) {
    return api.post('/auth.php?action=register', data);
  },
  
  async logout() {
    return api.post('/auth.php?action=logout');
  },
  
  async getCurrentUser() {
    return api.get('/auth.php?action=current');
  },
};

// Update cart badge on page load
document.addEventListener('DOMContentLoaded', async () => {
  try {
    const result = await cart.getCount();
    const badge = document.querySelector('.cart-count');
    if (badge && result.data) {
      badge.textContent = result.data.count;
    }
  } catch (error) {
    console.error('Failed to load cart count:', error);
  }
});
