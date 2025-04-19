
document.addEventListener('DOMContentLoaded', () => {
    let currentPage = 1;
    const perPageInput = document.getElementById('perPageInput');
    const container = document.getElementById('vehiculos-container');
    const paginationControls = document.getElementById('pagination-controls');
    const paginationInfo = document.getElementById('pagination-info');

    const marcaFiltro = document.getElementById('marcaFiltro');
    const precioMin = document.getElementById('precioMin');
    const precioMax = document.getElementById('precioMax');

    const tipoVehiculoFiltro = document.getElementById('tipoVehiculoFiltro');
    const lugarFiltro = document.getElementById('lugarFiltro');
    const anioFiltroContainer = document.getElementById('anioFiltroContainer');
    const valoracionFiltro = document.getElementById('valoracionFiltro');

    function getCheckedValues(container) {
        return Array.from(container.querySelectorAll('input[type="checkbox"]:checked')).map(cb => cb.value);
    }

    function renderPagination(totalPages) {
        paginationControls.innerHTML = '';

        const prevBtn = document.createElement('button');
        prevBtn.className = 'btn btn-outline-secondary';
        prevBtn.innerHTML = '&laquo;';
        prevBtn.disabled = currentPage === 1;
        prevBtn.addEventListener('click', () => {
            if (currentPage > 1) {
                currentPage--;
                fetchVehiculos();
            }
        });
        paginationControls.appendChild(prevBtn);

        const pageIndicator = document.createElement('span');
        pageIndicator.className = 'btn btn-outline-primary disabled';
        pageIndicator.textContent = `Página ${currentPage}`;
        paginationControls.appendChild(pageIndicator);

        const nextBtn = document.createElement('button');
        nextBtn.className = 'btn btn-outline-secondary';
        nextBtn.innerHTML = '&raquo;';
        nextBtn.disabled = currentPage === totalPages;
        nextBtn.addEventListener('click', () => {
            if (currentPage < totalPages) {
                currentPage++;
                fetchVehiculos();
            }
        });
        paginationControls.appendChild(nextBtn);

        paginationInfo.textContent = `Páginas encontradas: ${totalPages}`;
    }

    function fetchVehiculos({ resetPage = false } = {}) {
        if (resetPage) currentPage = 1;

        const perPage = parseInt(perPageInput.value) || 16;

        const params = new URLSearchParams({
            page: currentPage,
            perPage: perPage,
            marca: marcaFiltro.value.trim(),
            precioMin: precioMin.value,
            precioMax: precioMax.value,
        });

        getCheckedValues(tipoVehiculoFiltro).forEach(tipo => params.append('tipos[]', tipo));
        getCheckedValues(lugarFiltro).forEach(lugar => params.append('lugares[]', lugar));
        getCheckedValues(anioFiltroContainer).forEach(anio => params.append('anios[]', anio));
        getCheckedValues(valoracionFiltro).forEach(valor => params.append('valoraciones[]', valor));

        fetch(`/vehiculos?${params.toString()}`)
            .then(res => res.json())
            .then(data => {
                container.innerHTML = '';

                if (!data.vehiculos.length) {
                    container.innerHTML = `<div class="col-12 text-center text-muted">No se encontraron vehículos</div>`;
                    paginationControls.innerHTML = '';
                    paginationInfo.textContent = '';
                    return;
                }

                data.vehiculos.forEach(v => {
                    const rating = parseFloat(v.valoracion) || 0;
                    let estrellas = '';
                    for (let i = 1; i <= 5; i++) {
                        if (rating >= i) estrellas += '<i class="fas fa-star"></i>';
                        else if (rating >= i - 0.5) estrellas += '<i class="fas fa-star-half-alt"></i>';
                        else estrellas += '<i class="far fa-star"></i>';
                    }

                    container.innerHTML += `
                        <div class="col-sm-6 col-md-3 mb-4">
                            <a href="/vehiculo/detalle_vehiculo/${v.id_vehiculos}">
                                <div class="card">
                                    <img src="https://via.placeholder.com/300x180?text=${encodeURIComponent(v.marca)}+${encodeURIComponent(v.modelo)}" class="card-img-top" alt="${v.marca}">
                                    <div class="card-body">
                                        <h5 class="card-title">${v.marca} ${v.modelo}</h5>
                                        <div class="info-row d-flex justify-content-between">
                                            <p class="card-text">${v.precio_dia} €/día</p>
                                            <div class="estrellas">${estrellas}</div>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </div>
                    `;
                });

                renderPagination(data.totalPages);
            })
            .catch(error => {
                console.error('Error al cargar los vehículos:', error);
                container.innerHTML = `<div class="col-12 text-center text-danger">Error al cargar vehículos</div>`;
            });
    }

    [marcaFiltro, precioMin, precioMax].forEach(el =>
        el.addEventListener('input', () => fetchVehiculos({ resetPage: true }))
    );

    [tipoVehiculoFiltro, lugarFiltro, anioFiltroContainer, valoracionFiltro].forEach(container =>
        container.addEventListener('change', () => fetchVehiculos({ resetPage: true }))
    );

    perPageInput.addEventListener('change', () => {
        fetchVehiculos({ resetPage: true });
    });

    fetch('/home-stats')
        .then(res => res.json())
        .then(data => {
            if (data.tipos) {
                tipoVehiculoFiltro.innerHTML = '';
                data.tipos.forEach(tipo => {
                    tipoVehiculoFiltro.innerHTML += `
                        <label class="form-check-label d-block">
                            <input type="checkbox" class="form-check-input" value="${tipo.nombre}">
                            ${tipo.nombre}
                        </label>`;
                });
            }
        });

    fetch('/vehiculos/año')
        .then(res => res.json())
        .then(data => {
            anioFiltroContainer.innerHTML = '';
            data.forEach(anio => {
                anioFiltroContainer.innerHTML += `
                    <label class="form-check-label d-block">
                        <input type="checkbox" class="form-check-input" value="${anio}">
                        ${anio}
                    </label>`;
            });
        });

    fetch('/vehiculos/ciudades')
        .then(res => res.json())
        .then(data => {
            lugarFiltro.innerHTML = '';
            data.forEach(ciudad => {
                lugarFiltro.innerHTML += `
                    <label class="form-check-label d-block">
                        <input type="checkbox" class="form-check-input" value="${ciudad}">
                        ${ciudad}
                    </label>`;
            });
        });    
    
    document.getElementById('resetFiltrosBtn').addEventListener('click', () => {
        // Limpiar campos
        marcaFiltro.value = '';
        precioMin.value = '';
        precioMax.value = '';
        perPageInput.value = 8;
    
        // Desmarcar todos los checkboxes
        [tipoVehiculoFiltro, lugarFiltro, anioFiltroContainer, valoracionFiltro].forEach(container => {
            container.querySelectorAll('input[type="checkbox"]').forEach(cb => cb.checked = false);
        });
    
        // Recargar vehículos
        fetchVehiculos({ resetPage: true });
    });
        

    fetchVehiculos();
});