import './bootstrap';

import Alpine from 'alpinejs';
window.Alpine = Alpine;
Alpine.start();

// === Theme Toggle (global, single bind) ===
document.addEventListener('DOMContentLoaded', () => {
  const html = document.documentElement;
  const btn  = document.getElementById('themeToggle');
  if (!btn) return;

  // Guard: cegah double-binding (HMR / render ulang)
  if (btn.dataset.themeBound === '1') return;
  btn.dataset.themeBound = '1';

  const sun  = document.getElementById('icon-sun');
  const moon = document.getElementById('icon-moon');

  const syncIcon = () => {
    const dark = html.classList.contains('dark');
    if (sun && moon) {
      if (dark) { sun.classList.remove('hidden'); moon.classList.add('hidden'); }
      else      { moon.classList.remove('hidden'); sun.classList.add('hidden'); }
    }
  };

  const setTheme = (next /* 'dark' | 'light' */) => {
    html.classList.toggle('dark', next === 'dark');
    try { localStorage.setItem('theme', next); } catch {}
    syncIcon();
    if (window.toast) {
      window.toast({ message: `Tema ${next === 'dark' ? 'Dark' : 'Light'} aktif`, type: 'info', timeout: 1200 });
    }
  };

  // Ikon awal mengikuti kelas <html> dari anti-FOUC script
  syncIcon();

  btn.addEventListener('click', () => {
    const next = html.classList.contains('dark') ? 'light' : 'dark';
    setTheme(next);
    // Debug ringan (opsional): console.log('HTML classes:', html.className);
  });
});
