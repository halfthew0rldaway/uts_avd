/**
 * Dashboard Analitik Penjualan
 * Core Charting Logic
 */

// Format Rupiah helper
function formatRupiah(value) {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0
    }).format(value);
}

document.addEventListener('DOMContentLoaded', function() {
    // 1. Handling form loading state
    const formImport = document.getElementById('form-import-modal');
    if (formImport) {
        formImport.addEventListener('submit', function() {
            const btn = document.getElementById('btn-import-modal');
            btn.disabled = true;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> Sedang Memproses Data...';
        });
    }

    // 2. Auto-open modal if validation errors exist
    if (window.dashboardData && window.dashboardData.errors && window.dashboardData.errors.file) {
        const modalEl = document.getElementById('modalImport');
        if (modalEl) {
            const myModal = new bootstrap.Modal(modalEl);
            myModal.show();
        }
    }

    // 3. Chart Configuration
    if (window.dashboardData) {
        const COLORS = [
            '#696cff', // Primary
            '#71dd37', // Success
            '#03c3ec', // Info
            '#ffab00', // Warning
            '#ff3e1d', // Danger
            '#8592a3', // Secondary
            '#233446', // Dark
        ];

        Chart.defaults.font.family = "'Public Sans', sans-serif";
        Chart.defaults.color = '#a1acb8';

        // ===== 1. Line Chart – Tren Mingguan =====
        const trenData = window.dashboardData.trenMingguan;
        const labelsTren = trenData.map(d => `Minggu ${d.minggu} (${d.tahun})`);
        const totalTren  = trenData.map(d => parseFloat(d.total_penjualan));

        const canvasTren = document.getElementById('chartTrenMingguan');
        if (canvasTren) {
            new Chart(canvasTren, {
                type: 'line',
                data: {
                    labels: labelsTren,
                    datasets: [{
                        label: 'Total Penjualan (Rp)',
                        data: totalTren,
                        borderColor: '#ffab00',
                        backgroundColor: 'rgba(255, 171, 0, 0.1)',
                        borderWidth: 3,
                        fill: true,
                        tension: 0.4,
                        pointRadius: 0,
                        pointHoverRadius: 6,
                        pointBackgroundColor: '#fff',
                        pointBorderColor: '#ffab00',
                        pointBorderWidth: 2,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: { callbacks: { label: ctx => formatRupiah(ctx.parsed.y) }, backgroundColor: '#fff', titleColor: '#566a7f', bodyColor: '#566a7f', borderColor: '#d9dee3', borderWidth: 1 }
                    },
                    scales: {
                        x: { grid: { display: false, drawBorder: false }, ticks: { display: false } },
                        y: { grid: { display: false, drawBorder: false }, ticks: { display: false } }
                    }
                }
            });
        }

        // ===== 2. Pie Chart – Distribusi Kategori =====
        const kategoriData = window.dashboardData.distribusiKategori;
        const canvasKategori = document.getElementById('chartKategori');
        if (canvasKategori) {
            new Chart(canvasKategori, {
                type: 'pie',
                data: {
                    labels: kategoriData.map(d => d.kategori),
                    datasets: [{
                        data: kategoriData.map(d => parseFloat(d.total_kategori)),
                        backgroundColor: COLORS,
                        borderWidth: 0,
                        hoverOffset: 4,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { position: 'bottom', labels: { usePointStyle: true, boxWidth: 8, font: { size: 12 } } },
                        tooltip: { callbacks: { label: ctx => ` ${formatRupiah(ctx.parsed)}` }, backgroundColor: '#fff', titleColor: '#566a7f', bodyColor: '#566a7f', borderColor: '#d9dee3', borderWidth: 1 }
                    }
                }
            });
        }

        // ===== 3. Bar Chart – Total Penjualan per Produk =====
        const produkData = window.dashboardData.penjualanPerProduk;
        const canvasProduk = document.getElementById('chartProduk');
        if (canvasProduk) {
            new Chart(canvasProduk, {
                type: 'bar',
                data: {
                    labels: produkData.map(d => d.produk.length > 15 ? d.produk.substring(0, 15) + '...' : d.produk),
                    datasets: [{
                        label: 'Total Penjualan (Rp)',
                        data: produkData.map(d => parseFloat(d.total_penjualan)),
                        backgroundColor: '#696cff',
                        borderRadius: 4,
                        barThickness: 12,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: { callbacks: { label: ctx => formatRupiah(ctx.parsed.y) }, backgroundColor: '#fff', titleColor: '#566a7f', bodyColor: '#566a7f', borderColor: '#d9dee3', borderWidth: 1 }
                    },
                    scales: {
                        x: { grid: { display: false, drawBorder: false }, ticks: { font: { size: 11 } } },
                        y: { grid: { color: '#eceef1', borderDash: [5, 5], drawBorder: false }, ticks: { callback: v => 'Rp ' + new Intl.NumberFormat('id-ID').format(v) } }
                    }
                }
            });
        }

        // ===== 4. Bar Chart – Kategori per Bulan =====
        const kbData   = window.dashboardData.kategoriPerBulan;
        const bulanNames = ['','Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'];
        const canvasKB = document.getElementById('chartKategoriBulan');
        
        if (canvasKB && kbData.length > 0) {
            const bulanSet = [...new Set(kbData.map(d => `${bulanNames[d.bulan]} ${d.tahun}`))];
            const kategoriSet = [...new Set(kbData.map(d => d.kategori))];
            const kbDatasets  = kategoriSet.map((kat, i) => ({
                label: kat,
                data: bulanSet.map(bl => {
                    const found = kbData.find(d => `${bulanNames[d.bulan]} ${d.tahun}` === bl && d.kategori === kat);
                    return found ? parseFloat(found.total_kategori) : 0;
                }),
                backgroundColor: COLORS[i % COLORS.length],
                borderRadius: 4,
                barThickness: 8,
            }));

            new Chart(canvasKB, {
                type: 'bar',
                data: { labels: bulanSet, datasets: kbDatasets },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { position: 'top', align: 'end', labels: { usePointStyle: true, boxWidth: 8, font: { size: 11 } } },
                        tooltip: { callbacks: { label: ctx => `${ctx.dataset.label}: ${formatRupiah(ctx.parsed.y)}` }, backgroundColor: '#fff', titleColor: '#566a7f', bodyColor: '#566a7f', borderColor: '#d9dee3', borderWidth: 1 }
                    },
                    scales: {
                        x: { grid: { display: false, drawBorder: false } },
                        y: { grid: { color: '#eceef1', borderDash: [5, 5], drawBorder: false }, ticks: { callback: v => 'Rp ' + new Intl.NumberFormat('id-ID').format(v) } }
                    }
                }
            });
        }
    }
});
