document.addEventListener('alpine:init', () => {
  Alpine.store('pesananBadge', {
    total: 0,
    interval: null,
    initialized: false,

    async fetch() {
      try {
        const res = await API.get('/transaksi/badge')
        if (res.success) {

          if (this.initialized && res.data.total > this.total) {
            Alpine.store('ui').toast('Pesanan baru masuk!')
          }

          this.total = res.data.total
          this.initialized = true
        }
      } catch (err) {
        console.error('Badge error:', err)
      }
    },

    start() {
      this.fetch()
      // polling ringan (5 detik)
      this.interval = setInterval(() => {
        // hanya polling kalau tab aktif
        if (!document.hidden) {
          this.fetch()
        }
      }, 5000)
    },

    stop() {
      clearInterval(this.interval)
    }
  })

})