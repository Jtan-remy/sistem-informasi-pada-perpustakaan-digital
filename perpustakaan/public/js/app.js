// public/js/app.js

document.addEventListener('DOMContentLoaded', function () {

  // ── Auto-dismiss flash alert after 4s ──────────────────────────────
  const alerts = document.querySelectorAll('.alert.fade.show');
  alerts.forEach(function (el) {
    setTimeout(function () {
      const bsAlert = bootstrap.Alert.getOrCreateInstance(el);
      bsAlert.close();
    }, 4000);
  });

  // ── Active link di sidebar berdasarkan URL ──────────────────────────
  const currentHref = window.location.href;
  document.querySelectorAll('.sidebar .nav-link').forEach(function (link) {
    if (currentHref.includes(link.getAttribute('href'))) {
      link.classList.add('active', 'fw-semibold');
    }
  });

  // ── Konfirmasi sebelum submit form hapus ───────────────────────────
  document.querySelectorAll('[data-confirm]').forEach(function (el) {
    el.addEventListener('click', function (e) {
      if (!confirm(el.dataset.confirm)) e.preventDefault();
    });
  });

  // ── Tooltip Bootstrap ─────────────────────────────────────────────
  document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(function (el) {
    new bootstrap.Tooltip(el);
  });

  // ── Preview gambar sebelum upload (generic) ────────────────────────
  document.querySelectorAll('input[type="file"][data-preview]').forEach(function (input) {
    input.addEventListener('change', function () {
      const target = document.getElementById(input.dataset.preview);
      if (!target || !input.files[0]) return;
      const reader = new FileReader();
      reader.onload = function (e) {
        if (target.tagName === 'IMG') {
          target.src = e.target.result;
        } else {
          target.style.backgroundImage = 'url(' + e.target.result + ')';
        }
      };
      reader.readAsDataURL(input.files[0]);
    });
  });

  // ── Tombol kembali ke atas ─────────────────────────────────────────
  const main = document.querySelector('.main-content');
  if (main) {
    const btn = document.createElement('button');
    btn.className = 'btn btn-primary rounded-circle shadow position-fixed';
    btn.style.cssText = 'bottom:1.5rem;right:1.5rem;width:44px;height:44px;display:none;z-index:999;line-height:1';
    btn.innerHTML = '<i class="bi bi-arrow-up"></i>';
    btn.title = 'Kembali ke atas';
    document.body.appendChild(btn);

    main.addEventListener('scroll', function () {
      btn.style.display = main.scrollTop > 200 ? 'flex' : 'none';
      btn.style.alignItems = 'center';
      btn.style.justifyContent = 'center';
    });
    btn.addEventListener('click', function () {
      main.scrollTo({ top: 0, behavior: 'smooth' });
    });
  }

});
