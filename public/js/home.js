document.addEventListener('DOMContentLoaded', () => {
    // ===== Mostrar estadísticas =====
    fetch('/home-stats')
        .then(res => res.json())
        .then(data => {
            document.querySelector('.stat-box .fa-users + .stat-content h3').textContent = data.usuariosClientes;
            document.querySelector('.fa-car + .stat-content h3').textContent = new Intl.NumberFormat().format(data.vehiculos);
            document.querySelector('.fa-star + .stat-content h3').textContent = data.valoracionMedia;

            const container = document.querySelector('.btn-group-toggle');
            if (container && data.tipos) {
                container.innerHTML = '';
                data.tipos.forEach((tipo, index) => {
                    container.innerHTML += `
                        <label class="btn btn-outline-primary ${index === 0 ? 'active' : ''}">
                          <input type="radio" name="tipoVehiculo" value="${tipo.nombre}" autocomplete="off" ${index === 0 ? 'checked' : ''}>
                          ${tipo.nombre}
                        </label>
                    `;
                });
            }
        });

    // ===== Actualizar breadcrumb según tipo =====
    const breadcrumbTipo = document.getElementById('breadcrumb-tipo');
    const tipoInicial = document.querySelector('input[name="tipoVehiculo"]:checked');
    if (tipoInicial && breadcrumbTipo) breadcrumbTipo.textContent = tipoInicial.value;

    document.addEventListener('change', e => {
        if (e.target.name === 'tipoVehiculo') {
            breadcrumbTipo.textContent = e.target.value;
        }
    });

    // ===== Variables =====
    let currentPage = 1;
    const container = document.getElementById('vehiculos-container');
    const paginationControls = document.getElementById('pagination-controls');
    const paginationInfo = document.getElementById('pagination-info');

    const perPageInput = document.getElementById('perPageInput');
    const marcaFiltro = document.getElementById('marcaFiltro');
    const anioFiltro = document.getElementById('anioFiltro');
    const precioMin = document.getElementById('precioMin');
    const precioMax = document.getElementById('precioMax');
    const valoracionMin = document.getElementById('valoracionMin');

    // ===== Cargar años disponibles =====
    fetch('/vehiculos/año')
        .then(res => res.json())
        .then(data => {
            data.forEach(anio => {
                const option = document.createElement('option');
                option.value = anio;
                option.textContent = anio;
                anioFiltro.appendChild(option);
            });
        });

    // ===== Renderizar paginación =====
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

    // ===== Obtener vehículos =====
    function fetchVehiculos({ resetPage = false } = {}) {
        if (resetPage) currentPage = 1;

        const perPage = parseInt(perPageInput.value);

        const params = new URLSearchParams({
            page: currentPage,
            perPage: perPage,
            marca: marcaFiltro.value.trim(),
            anio: anioFiltro.value,
            precioMin: precioMin.value,
            precioMax: precioMax.value,
            valoracionMin: valoracionMin.value
        });

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

    // ===== Eventos =====
    [marcaFiltro, anioFiltro, precioMin, precioMax, valoracionMin].forEach(el =>
        el.addEventListener('input', () => fetchVehiculos({ resetPage: true }))
    );

    perPageInput.addEventListener('change', () => {
        if (parseInt(perPageInput.value) >= 1) {
            fetchVehiculos({ resetPage: true });
        }
    });

    // ===== Inicio =====
    fetchVehiculos();
});