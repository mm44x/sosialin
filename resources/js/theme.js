// resources/js/theme.js
document.addEventListener('DOMContentLoaded', () => {
  const html = document.documentElement;
  const stored = localStorage.getItem('theme');
  const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;

  if (stored === 'dark' || (!stored && prefersDark)) {
    html.classList.add('dark');
  } else {
    html.classList.remove('dark');
  }

  const btn = document.getElementById('themeToggle');
  if (btn) {
    btn.addEventListener('click', () => {
      html.classList.toggle('dark');
      const isDark = html.classList.contains('dark');
      localStorage.setItem('theme', isDark ? 'dark' : 'light');
    });
  }
});

// document.addEventListener('DOMContentLoaded', () => {
//   // --- Theme: init berdasar localStorage / sistem (backup kalau anti-FOUC di <head> ketinggalan) ---
//   try {
//     const saved = localStorage.getItem('theme');
//     const systemDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
//     const root = document.documentElement;
//     if (saved === 'dark' || (!saved && systemDark)) {
//       root.classList.add('dark');
//     } else {
//       root.classList.remove('dark');
//     }
//   } catch (e) {}

//   // --- Theme toggles (desktop + mobile) ---
//   const setTheme = (isDark) => {
//     document.documentElement.classList.toggle('dark', isDark);
//     localStorage.setItem('theme', isDark ? 'dark' : 'light');
//   };
//   const togglers = [
//     document.getElementById('theme-toggle'),
//     document.getElementById('theme-toggle-mobile'),
//     // fallback untuk ID lama agar aman kalau masih ada
//     document.getElementById('themeToggle'),
//   ].filter(Boolean);

//   togglers.forEach(btn => {
//     btn.addEventListener('click', () => {
//       const isDark = !document.documentElement.classList.contains('dark');
//       setTheme(isDark);
//     });
//   });

//   // --- Mobile menu ---
//   const mobileBtn = document.getElementById('mobileMenuBtn');
//   const mobileMenu = document.getElementById('mobileMenu');
//   if (mobileBtn && mobileMenu) {
//     mobileBtn.addEventListener('click', () => {
//       const hidden = mobileMenu.classList.toggle('hidden');
//       mobileBtn.setAttribute('aria-expanded', String(!hidden));
//     });
//   }
// });
