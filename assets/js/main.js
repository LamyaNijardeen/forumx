// assets/js/main.js
document.addEventListener('DOMContentLoaded', function () {
  var btn = document.getElementById('menuToggle');
  var nav = document.getElementById('primaryNav');
  if (!btn || !nav) return;

  btn.addEventListener('click', function () {
    nav.classList.toggle('show');
  });

  // close nav when clicking outside on small screens
  document.addEventListener('click', function (e) {
    if (!nav.classList.contains('show')) return;
    if (e.target === btn || nav.contains(e.target)) return;
    nav.classList.remove('show');
  });
});
