// Lelis Realty Services — Professional JavaScript
const API_URL = 'api';

const SAMPLE_PROPERTIES = [
  { id:1, title:"Modern Luxury Villa", address:"Brgy. San Isidro", city:"Batangas City", price:4500000, bedrooms:5, bathrooms:4, square_feet:4200, status:"available", property_type:"Residential", images:"https://images.unsplash.com/photo-1613490493576-7fde63acd811?w=800&q=80" },
  { id:2, title:"Contemporary Penthouse", address:"Poblacion", city:"Lipa City", price:3200000, bedrooms:3, bathrooms:2, square_feet:2100, status:"available", property_type:"Residential", images:"https://images.unsplash.com/photo-1600596542815-ffad4c1539a9?w=800&q=80" },
  { id:3, title:"Premium Estate Home", address:"P. Gomez St.", city:"Tanauan City", price:5800000, bedrooms:6, bathrooms:5, square_feet:5500, status:"available", property_type:"Residential", images:"https://images.unsplash.com/photo-1600585154340-be6161a56a0c?w=800&q=80" }
];

// ===== UTILITIES =====
function showAlert(message, type = 'success') {
  const alert = document.createElement('div');
  alert.className = `alert alert-${type}`;
  alert.style.cssText = 'position:fixed;top:90px;right:20px;z-index:9999;min-width:300px;animation:fadeInUp 0.3s ease';
  alert.innerHTML = `<span>${message}</span><button onclick="this.parentElement.remove()" style="background:none;border:none;color:inherit;cursor:pointer;font-size:1.2rem;margin-left:auto">&times;</button>`;
  document.body.appendChild(alert);
  setTimeout(() => alert.remove(), 5000);
}

async function fetchAPI(endpoint, options = {}) {
  try {
    let apiPath = API_URL;
    if (window.location.pathname.includes('/admin/')) apiPath = '../' + API_URL;
    const response = await fetch(`${apiPath}${endpoint}`, { ...options, headers: { 'Content-Type':'application/json', ...options.headers } });
    if (!response.ok) { const error = await response.json(); throw new Error(error.error || 'API Error'); }
    return await response.json();
  } catch (error) { console.error('API Error:', error); throw error; }
}

function formatCurrency(value) {
  return '₱' + parseInt(value).toLocaleString();
}

// ===== PROPERTIES =====
async function loadFeaturedProperties() {
  try {
    const response = await fetchAPI('/properties.php?action=featured');
    displayProperties(response.properties, 'featured-properties');
  } catch (error) { displayProperties(SAMPLE_PROPERTIES, 'featured-properties'); }
}

async function loadProperties(page = 1, filters = {}) {
  try {
    let endpoint = `/properties.php?action=list&page=${page}`;
    if (filters.city) endpoint += `&city=${filters.city}`;
    if (filters.type) endpoint += `&type=${filters.type}`;
    const response = await fetchAPI(endpoint);
    displayProperties(response.properties);
    return response;
  } catch (error) { displayProperties(SAMPLE_PROPERTIES); return { properties: SAMPLE_PROPERTIES }; }
}

async function loadPropertyDetail(id) {
  try {
    const response = await fetchAPI(`/properties.php?action=detail&id=${id}`);
    displayPropertyDetail(response.property);
  } catch (error) {
    const property = SAMPLE_PROPERTIES.find(p => p.id == id) || SAMPLE_PROPERTIES[0];
    displayPropertyDetail(property);
  }
}

function displayProperties(properties, containerId = 'properties-grid') {
  const container = document.getElementById(containerId);
  if (!container) return;
  container.innerHTML = properties.map((prop, i) => {
    let img = prop.images ? prop.images.split(',')[0] : 'https://images.unsplash.com/photo-1600596542815-ffad4c1539a9?w=800&q=80';
    if (!img.startsWith('http') && window.location.pathname.includes('/admin/')) img = '../' + img;
    return `
      <div class="property-card reveal" style="animation-delay:${i * 0.1}s">
        <div class="img-wrap">
          <img src="${img}" alt="${prop.title}" loading="lazy">
          <span class="property-badge">${prop.status}</span>
        </div>
        <div class="property-content">
          <div class="property-location">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
            ${prop.address}, ${prop.city}
          </div>
          <h3 class="property-title">${prop.title}</h3>
          <div class="property-price">${formatCurrency(prop.price)}</div>
          <div class="property-specs">
            ${prop.bedrooms ? `<span class="spec-item"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 7v11m0-4h18m0 4V8a3 3 0 0 0-3-3H6a3 3 0 0 0-3 3"/><path d="M7 11V7"/><path d="M17 11V7"/></svg> ${prop.bedrooms} Bed</span>` : ''}
            ${prop.bathrooms ? `<span class="spec-item"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 12h16a1 1 0 0 1 1 1v3a4 4 0 0 1-4 4H7a4 4 0 0 1-4-4v-3a1 1 0 0 1 1-1z"/><path d="M6 12V5a2 2 0 0 1 2-2h3v2.25"/></svg> ${prop.bathrooms} Bath</span>` : ''}
            ${prop.square_feet ? `<span class="spec-item"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2"/><path d="M3 9h18M9 3v18"/></svg> ${prop.square_feet.toLocaleString()} sqft</span>` : ''}
          </div>
          <div class="property-actions">
            <a href="${window.location.pathname.includes('/admin/') ? '../' : ''}property-detail.html?id=${prop.id}" class="btn btn-primary btn-small">View Details</a>
          </div>
        </div>
      </div>`;
  }).join('');
  initScrollReveal();
}

function displayPropertyDetail(property) {
  const container = document.getElementById('property-detail');
  if (!container) return;
  const images = typeof property.images === 'string' ? property.images.split(',') : (property.images || []);
  const mainImage = images[0] || 'https://images.unsplash.com/photo-1600596542815-ffad4c1539a9?w=800&q=80';
  container.innerHTML = `
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:2.5rem">
      <div>
        <img src="${mainImage}" alt="${property.title}" style="width:100%;border-radius:var(--radius);margin-bottom:1rem;box-shadow:var(--shadow-md)">
      </div>
      <div>
        <span class="section-label">${property.property_type || 'Property'}</span>
        <h1 style="font-family:var(--font-heading);margin:0.5rem 0 1rem;font-size:2rem">${property.title}</h1>
        <div class="property-price" style="font-size:1.8rem;margin-bottom:1.5rem">${formatCurrency(property.price)}</div>
        <div style="background:var(--light-bg);padding:1.25rem;border-radius:var(--radius);margin-bottom:2rem;border:1px solid var(--border)">
          <div class="property-specs" style="padding:0;border:none;margin:0">
            ${property.bedrooms ? `<span class="spec-item">${property.bedrooms} Bedrooms</span>` : ''}
            ${property.bathrooms ? `<span class="spec-item">${property.bathrooms} Bathrooms</span>` : ''}
            ${property.square_feet ? `<span class="spec-item">${property.square_feet.toLocaleString()} sqft</span>` : ''}
          </div>
        </div>
        <h3 style="margin-bottom:0.5rem;font-size:1rem">About This Property</h3>
        <p style="color:var(--light-text);line-height:1.8;font-size:0.92rem">${property.description || 'Contact us for more details about this premium property listing.'}</p>
        <div style="display:flex;gap:1rem;margin-top:2rem">
          <button onclick="openModal('appointmentModal')" class="btn btn-primary btn-lg" style="flex:1">Book Viewing</button>
          <button onclick="openModal('inquiryModal')" class="btn btn-outline btn-lg" style="flex:1">Send Inquiry</button>
        </div>
      </div>
    </div>`;
}

// ===== AUTH =====
async function checkAuthStatus() {
  try {
    const response = await fetchAPI('/auth.php?action=status');
    const loginBtn = document.querySelector('.login-btn');
    if (response.loggedIn && loginBtn) loginBtn.style.display = 'none';
  } catch (error) { /* not logged in */ }
}

async function logout() {
  await fetchAPI('/auth.php?action=logout', { method:'POST' });
  window.location.href = 'index.html';
}

// ===== MODALS =====
function openModal(id) { document.getElementById(id)?.classList.add('active'); }
function closeModal(id) { document.getElementById(id)?.classList.remove('active'); }

// ===== SCROLL REVEAL =====
function initScrollReveal() {
  const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
      if (entry.isIntersecting) { entry.target.classList.add('visible'); observer.unobserve(entry.target); }
    });
  }, { threshold: 0.1, rootMargin: '0px 0px -40px 0px' });
  document.querySelectorAll('.reveal').forEach(el => observer.observe(el));
}

// ===== ANIMATED COUNTER =====
function animateCounters() {
  document.querySelectorAll('[data-count]').forEach(counter => {
    const target = parseInt(counter.getAttribute('data-count'));
    const suffix = counter.getAttribute('data-suffix') || '';
    let current = 0;
    const step = Math.max(1, Math.floor(target / 50));
    const timer = setInterval(() => {
      current += step;
      if (current >= target) { current = target; clearInterval(timer); }
      counter.textContent = current.toLocaleString() + suffix;
    }, 30);
  });
}

// ===== NAVBAR =====
function initNavbarScroll() {
  const header = document.querySelector('header');
  if (!header) return;
  window.addEventListener('scroll', () => {
    header.classList.toggle('scrolled', window.scrollY > 30);
  });
}

function initMobileMenu() {
  const hamburger = document.querySelector('.hamburger');
  const navMenu = document.querySelector('nav ul');
  if (!hamburger || !navMenu) return;
  hamburger.addEventListener('click', () => {
    hamburger.classList.toggle('active');
    navMenu.classList.toggle('active');
  });
  navMenu.querySelectorAll('a').forEach(link => {
    link.addEventListener('click', () => { hamburger.classList.remove('active'); navMenu.classList.remove('active'); });
  });
}

// ===== INIT =====
document.addEventListener('DOMContentLoaded', () => {
  checkAuthStatus();
  initScrollReveal();
  initNavbarScroll();
  initMobileMenu();
  if (document.getElementById('featured-properties')) loadFeaturedProperties();
  if (document.getElementById('properties-grid')) loadProperties();
  if (document.getElementById('property-detail')) {
    const id = new URLSearchParams(window.location.search).get('id');
    if (id) loadPropertyDetail(id);
  }
  const counterObserver = new IntersectionObserver((entries) => {
    entries.forEach(entry => { if (entry.isIntersecting) { animateCounters(); counterObserver.disconnect(); } });
  });
  const counterEl = document.querySelector('[data-count]');
  if (counterEl) counterObserver.observe(counterEl);
});

document.addEventListener('click', (e) => { if (e.target.classList.contains('modal')) e.target.classList.remove('active'); });
