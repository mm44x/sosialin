// resources/js/theme.js
// document.addEventListener('DOMContentLoaded', () => {
//   const html = document.documentElement;
//   const stored = localStorage.getItem('theme');
//   const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;

//   if (stored === 'dark' || (!stored && prefersDark)) {
//     html.classList.add('dark');
//   } else {
//     html.classList.remove('dark');
//   }

//   const btn = document.getElementById('themeToggle');
//   if (btn) {
//     btn.addEventListener('click', () => {
//       html.classList.toggle('dark');
//       const isDark = html.classList.contains('dark');
//       localStorage.setItem('theme', isDark ? 'dark' : 'light');
//     });
//   }
// })