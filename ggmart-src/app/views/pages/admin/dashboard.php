<div class="p-4 space-y-6">

  <!-- TITLE -->
  <h1 class="text-2xl font-bold">Dashboard GGMart</h1>

  <!-- SUMMARY CARDS -->
  <div class="grid grid-cols-2 md:grid-cols-4 gap-4" x-data="dashboardPage()">

    <!-- TOTAL PRODUK -->
    <div class="bg-white p-4 rounded shadow">
      <div class="text-sm text-gray-500">Total Produk</div>
      <div class="text-2xl font-bold" x-text="summary.total_produk"></div>
    </div>

    <!-- PENJUALAN HARI INI -->
    <div class="bg-white p-4 rounded shadow">
      <div class="text-sm text-gray-500">Penjualan Hari Ini</div>
      <div class="text-2xl font-bold" x-text="utils.formatRupiah(summary.penjualan_hari_ini)"></div>
      <div class="text-sm"
        :class="summary.growth.penjualan >= 0 ? 'text-green-500' : 'text-red-500'"
        x-text="summary.growth.penjualan + '%'"></div>
    </div>

    <!-- LABA HARI INI -->
    <div class="bg-white p-4 rounded shadow">
      <div class="text-sm text-gray-500">Laba Hari Ini</div>
      <div class="text-2xl font-bold" x-text="utils.formatRupiah(summary.laba_hari_ini)"></div>
      <div class="text-sm"
        :class="summary.growth.laba >= 0 ? 'text-green-500' : 'text-red-500'"
        x-text="summary.growth.laba + '%'"></div>
    </div>

    <!-- JUMLAH TRANSAKSI -->
    <div class="bg-white p-4 rounded shadow">
      <div class="text-sm text-gray-500">Transaksi Hari Ini</div>
      <div class="text-2xl font-bold" x-text="summary.transaksi_hari_ini"></div>
      <div class="text-sm"
        :class="summary.growth.transaksi >= 0 ? 'text-green-500' : 'text-red-500'"
        x-text="summary.growth.transaksi + '%'"></div>
    </div>

  </div>

  <div x-data="analyticsPage()" class="space-y-8">

    <h2 class="text-xl font-bold">Analytics Dashboard</h2>

    <!-- 💰 OMZET & LABA BULAN -->
    <div class="grid grid-cols-2 gap-4">
      <!-- OMZET -->
      <div class="bg-white p-4 rounded shadow">
        <div class="text-sm text-gray-500">Omzet Bulan Ini</div>

        <div class="text-xl font-bold"
          x-text="utils.formatRupiah(data.monthly.omzet_bulan_ini)">
        </div>

        <div class="text-sm"
          :class="data.monthly.omzet_growth >= 0 ? 'text-green-600' : 'text-red-600'">

          <span x-text="data.monthly.omzet_growth + '%'"></span>
        </div>
      </div>

      <!-- LABA -->
      <div class="bg-white p-4 rounded shadow">
        <div class="text-sm text-gray-500">Laba Bulan Ini</div>

        <div class="text-xl font-bold"
          x-text="utils.formatRupiah(data.monthly.laba_bulan_ini)">
        </div>

        <div class="text-sm"
          :class="data.monthly.laba_growth >= 0 ? 'text-green-600' : 'text-red-600'">

          <span x-text="data.monthly.laba_growth + '%'"></span>
        </div>
      </div>

    </div>

    <!-- 📈 CHART TREND -->
    <div class="bg-white p-4 rounded shadow">
      <h3 class="font-semibold mb-2">Trend Penjualan 7 Hari</h3>
      <canvas id="trendChartCanvas" height="100"></canvas>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2">
      <!-- 🏆 TOP PRODUK TERLARIS -->
      <div class="bg-white p-4 rounded shadow">
        <h3 class="font-semibold">Top Produk Terlaris</h3>
        <template x-for="p in data.top_produk">
          <div class="flex justify-between border-b py-1">
            <span x-text="p.nama_produk"></span>
            <span x-text="'Terjual ' + p.total_terjual"></span>
          </div>
        </template>
      </div>
      <!-- 🏆 TOP PROFIT PRODUK-->
      <div class="bg-white p-4 rounded shadow">
        <h3 class="font-semibold">Top Profit Produk</h3>
        <template x-for="p in data.profit_produk">
          <div class="flex justify-between border-b py-1">
            <span x-text="p.nama_produk"></span>
            <span x-text="'Profit ' + utils.formatRupiah(p.profit)"></span>
          </div>
        </template>
      </div>
    </div>
    <!-- ⚠️ STOK -->
    <div class="bg-white p-4 rounded shadow">
      <h3 class="font-semibold">Produk Hampir Habis</h3>
      <template x-for="p in data.low_stock">
        <div class="flex justify-between text-red-600">
          <span x-text="p.nama_produk"></span>
          <span x-text="p.stok"></span>
        </div>
      </template>
    </div>


    <div class="grid grid-cols-1 md:grid-cols-2">
      <!-- 🕒 JAM RAMAI -->
      <div class="bg-white p-4 rounded shadow">
        <h3 class="font-semibold">Jam Ramai</h3>
        <template x-for="j in data.jam_ramai">
          <div class="flex justify-between">
            <span x-text="j.jam + ':00'"></span>
            <span x-text="j.total"></span>
          </div>
        </template>
      </div>
      <!-- 👥 TOP USER -->
      <div class="bg-white p-4 shadow rounded">
        <h3 class="font-semibold">Top Customer</h3>
        <template x-for="u in data.top_user">
          <div class="flex justify-between">
            <span x-text="u.nama"></span>
            <span x-text="utils.formatRupiah(u.total_belanja)"></span>
          </div>
        </template>
      </div>
    </div>

  </div>

</div>

<script>
  function dashboardPage() {
    return {
      // STATE
      summary: {
        total_produk: 0,
        transaksi_hari_ini: 0,
        penjualan_hari_ini: 0,
        laba_hari_ini: 0,
        growth: {},
      },
      loading: false,

      async init() {
        await this.loadSummary()
      },

      // API CALL
      async loadSummary() {
        try {
          Alpine.store('ui').startLoading()
          const res = await API.get('/dashboard/summary');
          this.summary = res.data;
        } catch (e) {
          console.error('Summary error:', e);
        } finally {
          Alpine.store('ui').stopLoading()
        }
      },

      formatGrowth(val) {
        if (val == null) return '0%';
        return (val > 0 ? '+' : '') + val + '%';
      },

      growthClass(val) {
        if (val > 0) return 'text-green-600 text-sm font-semibold';
        if (val < 0) return 'text-red-600 text-sm font-semibold';
        return 'text-gray-500 text-sm';
      },

      growthIcon(val) {
        if (val > 0) return '⬆️';
        if (val < 0) return '⬇️';
        return '➡️';
      }
    }
  }
</script>
<script>
  function analyticsPage() {
    return {
      data: {
        monthly: {
          laba_bulan_ini: 0,
          laba_growth: 0.0,
          omzet_bulan_ini: 0,
          omzet_growth: 0.0
        },
        trend: [],
        top_produk: [],
        slow_produk: [],
        low_stock: [],
        profit_produk: [],
        jam_ramai: [],
        top_user: []
      },

      chart: null,

      async init() {
        const res = await API.get('/dashboard/analytics');
        this.data = res.data;
        console.log(this.data);

        this.renderChart();
      },

      renderChart() {
        const ctx = document.getElementById('trendChartCanvas');

        const labels = this.data.trend.map(i => i.tanggal);
        const penjualan = this.data.trend.map(i => i.penjualan);
        const laba = this.data.trend.map(i => i.laba);

        if (this.chart) {
          this.chart.destroy();
        }

        this.chart = new Chart(ctx, {
          type: 'line',
          data: {
            labels: labels,
            datasets: [{
                label: 'Penjualan',
                data: penjualan,
                borderColor: 'blue',
                backgroundColor: 'rgba(0,0,255,0.1)',
                tension: 0.3
              },
              {
                label: 'Laba',
                data: laba,
                borderColor: 'green',
                backgroundColor: 'rgba(0,255,0,0.1)',
                tension: 0.3
              }
            ]
          },
          options: {
            responsive: true,
            plugins: {
              legend: {
                position: 'top'
              },
              tooltip: {
                mode: 'index',
                intersect: false
              }
            },
            interaction: {
              mode: 'nearest',
              axis: 'x',
              intersect: false
            },
            scales: {
              y: {
                beginAtZero: true
              }
            }
          }
        });
      },
    }
  }
</script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>