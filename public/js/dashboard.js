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
        // High Contrast Palette
        // Consistent Color Mapping
        const CATEGORY_COLORS = {
            'elektronik': '#ff4d4d',   // Primary (Red-Orange)
            'aksesoris': '#5e72e4',    // Info (Royal Blue)
            'edukasi': '#ffd600',      // Warning (Yellow)
            'atk': '#00cfe8',          // Danger (Cyan)
            'tidak diketahui': '#2dce89' // Success (Vibrant Green)
        };
        const DEFAULT_COLOR = '#8592a3';

        function getColorForCategory(kategori) {
            if (!kategori) return DEFAULT_COLOR;
            const key = kategori.toString().toLowerCase().trim();
            for (const [cat, color] of Object.entries(CATEGORY_COLORS)) {
                if (key.includes(cat)) return color;
            }
            return DEFAULT_COLOR;
        }

        Chart.defaults.font.family = "'Inter', sans-serif";
        Chart.defaults.color = '#566a7f';

        // ===== 1. Line Chart – Tren Mingguan (With Axis Details) =====
        const trenData = window.dashboardData.trenMingguan;
        if (trenData && trenData.length > 0) {
            const labelsTren = trenData.map(d => d.label);
            const totalTren  = trenData.map(d => parseFloat(d.total_penjualan));
            const canvasTren = document.getElementById('chartTrenMingguan');
            if (canvasTren) {
                new Chart(canvasTren, {
                    type: 'line',
                    data: {
                        labels: labelsTren,
                        datasets: [{
                            label: 'Total Penjualan',
                            data: totalTren,
                            borderColor: '#ff4d4d',
                            backgroundColor: 'rgba(255, 77, 77, 0.08)',
                            borderWidth: 3,
                            fill: true,
                            tension: 0.4,
                            pointRadius: 4,
                            pointHoverRadius: 7,
                            pointBackgroundColor: '#ff4d4d',
                            pointBorderColor: '#fff',
                            pointBorderWidth: 2,
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { display: false },
                            tooltip: { 
                                callbacks: { label: ctx => formatRupiah(ctx.parsed.y) }, 
                                backgroundColor: '#fff', titleColor: '#566a7f', bodyColor: '#566a7f', borderColor: '#d9dee3', borderWidth: 1, padding: 10, displayColors: false
                            }
                        },
                        scales: {
                            x: { 
                                grid: { display: false, drawBorder: false }, 
                                ticks: { font: { size: 10, weight: '500' }, color: '#a1acb8', maxRotation: 0 } 
                            },
                            y: { 
                                grid: { color: '#eceef1', borderDash: [5, 5], drawBorder: false }, 
                                ticks: { 
                                    font: { size: 10 }, 
                                    color: '#a1acb8',
                                    callback: v => v >= 1000000 ? (v/1000000) + 'Jt' : v
                                } 
                            }
                        }
                    }
                });
            }
        }

        // ===== 2. Regular Pie Chart – Distribusi Kategori =====
        const kategoriData = window.dashboardData.distribusiKategori;
        const canvasKategori = document.getElementById('chartKategori');
        if (canvasKategori && kategoriData && kategoriData.length > 0) {
            new Chart(canvasKategori, {
                type: 'pie',
                data: {
                    labels: kategoriData.map(d => d.kategori),
                    datasets: [{
                        data: kategoriData.map(d => parseFloat(d.total_kategori)),
                        backgroundColor: kategoriData.map(d => getColorForCategory(d.kategori)),
                        borderWidth: 2,
                        borderColor: '#ffffff',
                        hoverOffset: 8,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { 
                            position: 'bottom', 
                            labels: { 
                                usePointStyle: true, boxWidth: 10, padding: 20, font: { size: 12, weight: '500' }, color: '#566a7f'
                            } 
                        },
                        tooltip: { 
                            callbacks: { label: ctx => ` ${formatRupiah(ctx.parsed)}` }, 
                            backgroundColor: '#fff', titleColor: '#566a7f', bodyColor: '#566a7f', borderColor: '#d9dee3', borderWidth: 1, padding: 12
                        }
                    }
                }
            });
        }

        // ===== 3. Bar Chart – Total Penjualan per Produk =====
        const produkData = window.dashboardData.penjualanPerProduk;
        const canvasProduk = document.getElementById('chartProduk');
        if (canvasProduk && produkData && produkData.length > 0) {
            new Chart(canvasProduk, {
                type: 'bar',
                data: {
                    labels: produkData.map(d => d.produk.length > 15 ? d.produk.substring(0, 15) + '...' : d.produk),
                    datasets: [{
                        label: 'Total Penjualan',
                        data: produkData.map(d => parseFloat(d.total_penjualan)),
                        backgroundColor: '#ff4d4d',
                        borderRadius: 5,
                        barThickness: 15,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: { 
                            callbacks: { label: ctx => formatRupiah(ctx.parsed.y) }, 
                            backgroundColor: '#fff', titleColor: '#566a7f', bodyColor: '#566a7f', borderColor: '#d9dee3', borderWidth: 1, padding: 10
                        }
                    },
                    scales: {
                        x: { grid: { display: false, drawBorder: false }, ticks: { font: { size: 11, weight: '500' }, color: '#566a7f' } },
                        y: { grid: { color: '#eceef1', borderDash: [5, 5], drawBorder: false }, ticks: { font: { size: 11 }, color: '#a1acb8', callback: v => v >= 1000000 ? (v/1000000) + 'Jt' : v } }
                    }
                }
            });
        }

        // ===== 4. Bar Chart – Kategori per Bulan =====
        const kbData   = window.dashboardData.kategoriPerBulan;
        const bulanNames = ['','Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'];
        const canvasKB = document.getElementById('chartKategoriBulan');
        
        if (canvasKB && kbData && kbData.length > 0) {
            const bulanSet = [...new Set(kbData.map(d => `${bulanNames[d.bulan]} ${d.tahun}`))];
            const kategoriSet = [...new Set(kbData.map(d => d.kategori))];
            const kbDatasets  = kategoriSet.map((kat, i) => ({
                label: kat,
                data: bulanSet.map(bl => {
                    const found = kbData.find(d => `${bulanNames[d.bulan]} ${d.tahun}` === bl && d.kategori === kat);
                    return found ? parseFloat(found.total_kategori) : 0;
                }),
                backgroundColor: getColorForCategory(kat),
                borderRadius: 5,
                barThickness: 10,
            }));

            new Chart(canvasKB, {
                type: 'bar',
                data: { labels: bulanSet, datasets: kbDatasets },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { position: 'top', align: 'end', labels: { usePointStyle: true, boxWidth: 10, padding: 15, font: { size: 11, weight: '500' }, color: '#566a7f' } },
                        tooltip: { callbacks: { label: ctx => `${ctx.dataset.label}: ${formatRupiah(ctx.parsed.y)}` }, backgroundColor: '#fff', titleColor: '#566a7f', bodyColor: '#566a7f', borderColor: '#d9dee3', borderWidth: 1, padding: 10 }
                    },
                    scales: {
                        x: { grid: { display: false, drawBorder: false }, ticks: { font: { weight: '500' }, color: '#566a7f' } },
                        y: { grid: { color: '#eceef1', borderDash: [5, 5], drawBorder: false }, ticks: { font: { size: 11 }, color: '#a1acb8', callback: v => v >= 1000000 ? (v/1000000) + 'Jt' : v } }
                    }
                }
            });
        }
    }
});
