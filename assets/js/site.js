document.addEventListener('DOMContentLoaded', function () {
  // Toast utility
  function showToast(message, timeout = 2500) {
    let t = document.getElementById('site-toast');
    if (!t) {
      t = document.createElement('div');
      t.id = 'site-toast';
      t.style.position = 'fixed';
      t.style.right = '20px';
      t.style.top = '20px';
      t.style.background = 'rgba(0,0,0,0.8)';
      t.style.color = '#fff';
      t.style.padding = '10px 14px';
      t.style.borderRadius = '6px';
      t.style.zIndex = 99999;
      t.style.boxShadow = '0 6px 16px rgba(0,0,0,0.2)';
      t.style.fontSize = '14px';
      document.body.appendChild(t);
    }
    t.textContent = message;
    t.style.opacity = '1';

    clearTimeout(t._hideTimer);
    t._hideTimer = setTimeout(() => {
      t.style.opacity = '0';
    }, timeout);
  }

  // Update cart count (animated)
  function updateCartCount(count) {
    const el = document.getElementById('cartCount');
    if (!el) return;
    el.textContent = count;
    el.animate([
      { transform: 'scale(1)' },
      { transform: 'scale(1.3)' },
      { transform: 'scale(1)' }
    ], { duration: 350, easing: 'ease-out' });
  }

  // Add-to-cart AJAX + fly-to-cart animation
  document.querySelectorAll('.menu-item form[action="cart.php"]').forEach(form => {
    form.addEventListener('submit', function (e) {
      e.preventDefault();

      const menuCard = form.closest('.menu-item');
      const img = menuCard.querySelector('img');
      const menuId = form.querySelector('input[name="menu_id"]').value;

      // Build form data
      const fd = new FormData();
      fd.append('action', 'add');
      fd.append('menu_id', menuId);
      fd.append('ajax', '1');

      fetch('cart.php', { method: 'POST', body: fd, credentials: 'same-origin' })
        .then(async resp => {
          // If user was redirected to login (not logged in), follow redirect in top window
          if (resp.redirected && resp.url && resp.url.includes('login.php')) {
            window.location.href = resp.url;
            return;
          }

          const data = await resp.json().catch(() => null);
          if (!data) {
            // Unexpected response — fall back to full submit
            form.submit();
            return;
          }

          if (data.success) {
            // toast
            showToast('Added to cart');
            // update cart count if provided
            if (typeof data.totalItems !== 'undefined') updateCartCount(data.totalItems);

            // flying image animation
            if (img) {
              const imgRect = img.getBoundingClientRect();
              const cartLink = document.querySelector('a[href="cart.php"]');
              const cartRect = cartLink ? cartLink.getBoundingClientRect() : { left: window.innerWidth - 40, top: 20 };

              const clone = img.cloneNode(true);
              clone.style.position = 'fixed';
              clone.style.left = imgRect.left + 'px';
              clone.style.top = imgRect.top + 'px';
              clone.style.width = imgRect.width + 'px';
              clone.style.height = imgRect.height + 'px';
              clone.style.transition = 'transform 700ms cubic-bezier(.2,.8,.2,1), opacity 700ms';
              clone.style.zIndex = 99998;
              document.body.appendChild(clone);

              const dx = cartRect.left + cartRect.width / 2 - (imgRect.left + imgRect.width / 2);
              const dy = cartRect.top + cartRect.height / 2 - (imgRect.top + imgRect.height / 2);

              requestAnimationFrame(() => {
                clone.style.transform = `translate(${dx}px, ${dy}px) scale(0.12)`;
                clone.style.opacity = '0.6';
              });

              setTimeout(() => clone.remove(), 800);
            }
          } else {
            showToast(data.message || 'Could not add to cart');
          }
        })
        .catch(() => {
          showToast('Network error');
        });
    });
  });

  // Client-side search/filter for menu
  const searchInput = document.createElement('input');
  searchInput.type = 'search';
  searchInput.id = 'menuSearch';
  searchInput.placeholder = 'Search menu (name / description)...';
  searchInput.style.width = '100%';
  searchInput.style.maxWidth = '560px';
  searchInput.style.margin = '0 auto 16px';
  searchInput.style.display = 'block';
  searchInput.style.padding = '8px 10px';
  searchInput.style.borderRadius = '6px';
  searchInput.style.border = '1px solid #ddd';

  const menuSection = document.querySelector('.menu');
  if (menuSection) {
    menuSection.insertBefore(searchInput, menuSection.querySelector('.menu-items'));
  }

  let searchTimer = null;
  searchInput.addEventListener('input', function () {
    clearTimeout(searchTimer);
    const q = this.value.trim().toLowerCase();
    searchTimer = setTimeout(() => {
      document.querySelectorAll('.menu-item').forEach(card => {
        const text = (card.textContent || '').toLowerCase();
        const match = q === '' || text.indexOf(q) !== -1;
        card.style.display = match ? '' : 'none';
      });
    }, 150);
  });

  // Initialize cart count element if present (small safety)
  const cartEl = document.getElementById('cartCount');
  if (cartEl && cartEl.textContent.trim() === '') cartEl.textContent = '0';
});