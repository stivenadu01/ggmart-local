document.addEventListener('alpine:init', () => {

  // 🔐 AUTH STORE
  Alpine.store('auth', {
    user: window.__USER__ ?? null,

    async logout() {
      const ok = await Alpine.store('ui').confirm("Yakin ingin logout?");
      if (!ok) return;
      const res = await API.post('/auth/logout');
      if (res.success) {
        Alpine.store('ui').toast(res.message);
        window.location.href = '/login';
      }
    },

    async refresh() {
      if (!this.user) return;
      const res = await API.get('/auth/me', false);
      if (res.success) {
        this.user = res.data;
      }
    }

  });

  // 🎨 UI STORE
  Alpine.store('ui', {
    loading: false,
    loadingCount: 0,

    startLoading() {
      this.loadingCount++
      setTimeout(() => {
        if (this.loadingCount > 0) this.loading = true
      }, 150)
    },

    stopLoading() {
      this.loadingCount--
      if (this.loadingCount <= 0) {
        this.loading = false
        this.loadingCount = 0
      }
    },

    toastMessage: '',
    toastType: 'success',
    openSearch: false,
    openUserMenu: false,

    // 🔔 TOAST
    toast(msg, type = 'success', duration = 3000) {
      this.toastMessage = msg;
      this.toastType = type;

      setTimeout(() => {
        this.toastMessage = '';
      }, duration);
    },

    // ❓ CONFIRM MODAL SYSTEM
    confirmMessage: '',
    confirmShow: false,
    confirmResolve: null,

    confirm(msg) {
      this.confirmMessage = msg;
      this.confirmShow = true;

      return new Promise((resolve) => {
        this.confirmResolve = resolve;
      });
    },

    confirmYes() {
      this.confirmShow = false;
      if (this.confirmResolve) this.confirmResolve(true);
    },

    confirmNo() {
      this.confirmShow = false;
      if (this.confirmResolve) this.confirmResolve(false);
    },

    flyToCart(imgEl) {
      return new Promise((resolve) => {
        const cart = document.getElementById('cart-icon')
        if (!cart || !imgEl) {
          resolve()
          return
        }

        const imgRect = imgEl.getBoundingClientRect()
        const cartRect = cart.getBoundingClientRect()

        const clone = document.createElement('img')
        clone.src = imgEl.src

        clone.style.position = 'fixed'
        clone.style.top = imgRect.top + 'px'
        clone.style.left = imgRect.left + 'px'
        clone.style.width = imgRect.width + 'px'
        clone.style.height = imgRect.height + 'px'
        clone.style.zIndex = 9999
        clone.style.pointerEvents = 'none'
        clone.style.borderRadius = '12px'
        clone.style.objectFit = 'cover'

        // initial state
        clone.style.opacity = '0'
        clone.style.transform = 'scale(1)'

        document.body.appendChild(clone)

        // ⏱ FRAME 1 → langsung tampil
        requestAnimationFrame(() => {
          clone.style.transition = 'opacity 0.1s ease'
          clone.style.opacity = '1'
        })

        // ⏱ FRAME 2 → semua animasi SEKALIGUS (ini kunci smooth)
        setTimeout(() => {
          const cartCenterX = cartRect.left + (cartRect.width / 2)
          const cartCenterY = cartRect.top + (cartRect.height / 2)

          const cloneWidth = clone.offsetWidth
          const cloneHeight = clone.offsetHeight

          clone.style.transition = `
            transform 0.8s cubic-bezier(.22,1,.36,1),
            top 0.8s cubic-bezier(.22,1,.36,1),
            left 0.8s cubic-bezier(.22,1,.36,1),
            opacity 0.6s ease
          `

          // 🔥 langsung gabung semua:
          clone.style.transform = 'scale(0.2)'
          clone.style.left = (cartCenterX - cloneWidth / 2) + 'px'
          clone.style.top = (cartCenterY - cloneHeight / 2) + 'px'
          clone.style.opacity = '0.2'

        }, 80) // kecil banget delay → terasa instant

        // ⏱ END
        setTimeout(() => {
          clone.remove()
          resolve()
        }, 900)
      })
    }
  });

  // UTILS
  Alpine.store('utils', {

    formatRupiah(angka, prefix = true) {
      const number = Number(angka) || 0;

      const formatted = new Intl.NumberFormat('id-ID', {
        minimumFractionDigits: 0,
        maximumFractionDigits: 0
      }).format(number);

      return prefix ? 'Rp' + formatted : formatted;
    },

    formatDate(dateStr) {
      const date = new Date(dateStr);
      return date.toLocaleDateString('id-ID', {
        weekday: 'long',
        year: 'numeric',
        month: 'long',
        day: 'numeric'
      });
    },

    formatDateTime(dateStr) {
      const date = new Date(dateStr);
      return date.toLocaleString('id-ID', {
        weekday: 'long',
        year: 'numeric',
        month: 'long',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
      });
    },

    storageHelper(key, action, value = null) {
      let data;

      try {
        data = JSON.parse(localStorage.getItem(key)) || [];
      } catch {
        data = [];
      }

      switch (action) {
        case 'load':
          return data;

        case 'save':
          if (!value) return data;
          data.push(value);
          localStorage.setItem(key, JSON.stringify(data));
          return data;

        case 'set':
          if (!Array.isArray(value)) return data;
          localStorage.setItem(key, JSON.stringify(value));
          return value;

        case 'remove':
          data = data.filter((_, i) => i !== value);
          localStorage.setItem(key, JSON.stringify(data));
          return data;

        case 'clear':
          localStorage.removeItem(key);
          return [];

        default:
          console.warn('storageHelper: action tidak dikenal ->', action);
          return data;
      }
    }
  });

  // 🛒 CART
  Alpine.store('cart', {
    items: [],
    async init() {
      if (!Alpine.store('auth').user) {
        this.items = [];
        return;
      }
      await this.load();
    },

    async load() {
      const res = await API.get('/keranjang?u=' + Alpine.store('auth').user.id_user, false);
      if (res.success) {
        this.items = res.data || [];
      }
    },

    async add(item, imgEl = null) {
      if (!Alpine.store('auth').user) {
        const ok = await Alpine.store('ui').confirm('Harus login dulu untuk tambah keranjang');
        if (ok) window.location.href = BASE_URL + '/login';
        return;
      }
      if (item.jumlah > item.stok) {
        Alpine.store('ui').toast(`Stok ${item.nama_produk} tidak cukup`, 'error');
        return;
      }

      try {
        const res = await API.post('/keranjang', {
          ...item,
          id_user: Alpine.store('auth').user.id_user
        }, false);
        if (res.success) {
          if (imgEl) await Alpine.store('ui').flyToCart(imgEl);
          await this.load();
        }
      } catch (err) {
        console.log(err);
      }
    },

    async remove(id_keranjang) {
      try {
        const payload = {
          id_user: Alpine.store('auth').user.id_user,
          id_keranjang: id_keranjang
        }
        await API.delete('/keranjang', payload, false);
        await this.load();
      } finally {
      }
    },

    async update(item) {
      try {
        if (item.jumlah > item.stok) {
          Alpine.store('ui').toast(`Stok ${item.nama_produk} tidak cukup`, 'error');
          return;
        }
        item.id_user = Alpine.store('auth').user.id_user
        await API.put('/keranjang', item, false);
      } finally {
        await this.load();
      }
    },

    async clear() {
      const res = await API.delete('/keranjang/clear', { id_user: Alpine.store('auth').user.id_user }, false);
      if (res.success) {
        this.items = [];
      }
    },

    get totalQty() {
      return this.items.reduce((s, i) => s + (i.jumlah || 1), 0);
    },

    get totalHarga() {
      return this.items.reduce((s, i) => s + (i.harga_jual * (i.jumlah || 1)), 0);
    },

    async checkout() {
      try {
        if (!Alpine.store('auth').user || Alpine.store('auth').user.role == 'admin') {
          Alpine.store('ui').toast('Anda tidak bisa checkout', 'warning')
          return;
        }

        const payload = {
          id_user: Alpine.store('auth').user.id_user,
          total_harga: this.totalHarga,
          detail: this.items.map(i => ({
            kode_produk: i.kode_produk,
            jumlah: i.jumlah,
            harga_satuan: i.harga_jual
          }))
        }
        const res = await API.post('/transaksi/user', payload)
        this.items = []
        this.clear()

        Alpine.store('ui').toast('Checkout berhasil', 'success')
        return setTimeout(() => {
          window.location.href = BASE_URL + '/transaksi'
        }, 1500);
      } catch (err) {
        // error sudah ditangani API.js (toast)
      }
    }

  });
});