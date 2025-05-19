document.addEventListener('DOMContentLoaded', function () {
    const enlacesGrupo = document.querySelectorAll('.grupo-link');
    const contenidoGrupo = document.getElementById('contenidoGrupo');

    enlacesGrupo.forEach(link => {
        link.addEventListener('click', function (e) {
            e.preventDefault();

            const nombre = this.dataset.nombre;
            const participantes = this.dataset.participantes;
            const miembros = JSON.parse(this.dataset.miembros); // esto debe ser un array en formato JSON

            contenidoGrupo.innerHTML = `
                <div class="chat-wrapper d-flex flex-column h-100">

                    <!-- Encabezado -->
                    <div class="d-flex justify-content-between align-items-center border-bottom pb-2 mb-3">
                        <div>
                            <h5 class="mb-0">Grupo: ${nombre}</h5>
                        </div>
                        <button class="btn btn-outline-secondary btn-sm" id="infoGrupoBtn">
                            <i class="fa-solid fa-circle-info"></i> Info
                        </button>
                    </div>

                    <!-- Mensajes -->
                    <div class="flex-grow-1 overflow-auto mb-3" id="mensajes">
                        <!-- Aquí se cargarán los mensajes -->
                    </div>

                    <!-- Input en parte inferior -->
                    <div class="input-mensaje">
                        <input type="text" class="form-control me-2" placeholder="Escribe un mensaje...">
                        <button class="btn" style="background-color: #8c4ae2; color: white;">
                            <i class="fa-solid fa-paper-plane"></i>
                        </button>
                    </div>
                </div>
            `;


            // Activar botón de info recién insertado
            document.getElementById('infoGrupoBtn').addEventListener('click', function () {
                // Mostrar nombre
                document.getElementById('modalNombreGrupo').textContent = nombre;

                // Mostrar miembros
                const ul = document.getElementById('modalMiembrosGrupo');
                ul.innerHTML = '';
                miembros.forEach(nombreMiembro => {
                    const li = document.createElement('li');
                    li.textContent = nombreMiembro;
                    ul.appendChild(li);
                });

                // Mostrar modal
                const modal = new bootstrap.Modal(document.getElementById('infoGrupoModal'));
                modal.show();
            });
        });
    });
});
