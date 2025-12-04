document.addEventListener('DOMContentLoaded', function() {
    
    // --- 1. LOGIKA PETA INTERAKTIF ---
    const locationSelect = document.getElementById('location-select');
    const mapContainer = document.getElementById('map-container');
    const loadingOverlay = document.getElementById('map-loading');

    if(locationSelect && mapContainer) {
        
        function loadMapData(locationCode) {
            if(loadingOverlay) loadingOverlay.classList.remove('hidden');
            mapContainer.innerHTML = ''; 

            fetch(`/api/block-status?location=${locationCode}`)
                .then(response => response.json())
                .then(data => {
                    renderMap(data);
                })
                .catch(error => {
                    console.error('Error:', error);
                    mapContainer.innerHTML = '<p class="col-span-full text-center py-10">Gagal memuat data.</p>';
                })
                .finally(() => {
                    if(loadingOverlay) loadingOverlay.classList.add('hidden');
                });
        }

        function renderMap(blocks) {
            if(blocks.length === 0) {
                mapContainer.innerHTML = '<p class="col-span-full text-center py-20 text-gray-400">Belum ada data blok.</p>';
                return;
            }

            blocks.forEach(block => {
                const el = document.createElement('div');
                
                // PERBAIKAN TAMPILAN:
                // 1. Aspect Ratio lebih lebar (1.3/1) agar tidak turun
                // 2. Border-2 (Lebih tebal)
                // 3. Flex center
                el.className = `
                    aspect-[1.3/1] flex flex-col items-center justify-content-center 
                    border-2 rounded-t-[50px] rounded-b-md 
                    text-[10px] font-bold cursor-default transition-all duration-300
                    hover:scale-105 hover:shadow-lg relative bg-white
                    flex items-center justify-center
                `;

                // WARNA STATUS (PASTEL SOLID)
                if (block.status === 'available') {
                    el.classList.add('bg-[#ecfdf5]', 'border-[#34d399]', 'text-[#065f46]'); 
                    el.title = "Tersedia";
                } else if (block.status === 'reserved') {
                    el.classList.add('bg-[#fffbeb]', 'border-[#fbbf24]', 'text-[#b45309]');
                    el.title = "Dipesan";
                } else {
                    el.classList.add('bg-[#f1f5f9]', 'border-[#cbd5e1]', 'text-[#64748b]');
                    el.title = "Terisi";
                }

                // ID LENGKAP (Misal: DK-001)
                el.innerText = block.id; 
                
                mapContainer.appendChild(el);
            });
        }

        locationSelect.addEventListener('change', (e) => loadMapData(e.target.value));

        if(locationSelect.value) {
            loadMapData(locationSelect.value);
        }
    }

    // --- 2. SMOOTH SCROLL ---
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            document.querySelector(this.getAttribute('href')).scrollIntoView({
                behavior: 'smooth'
            });
        });
    });
});