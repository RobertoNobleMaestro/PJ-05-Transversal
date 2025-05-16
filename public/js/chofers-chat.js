document.addEventListener('DOMContentLoaded', function () {
    const enlacesGrupo = document.querySelectorAll('.grupo-link');
    const contenidoGrupo = document.getElementById('contenidoGrupo');

    enlacesGrupo.forEach(link => {
        link.addEventListener('click', function (e) {
            e.preventDefault();

            const nombre = this.getAttribute('data-nombre');
            const participantes = this.getAttribute('data-participantes');

            contenidoGrupo.innerHTML = `
                <div class="chat-wrapper d-flex flex-column h-100">

                    <!-- Encabezado -->
                    <div class="d-flex justify-content-between align-items-center border-bottom pb-2 mb-3">
                        <h5 class="mb-0">Grupo: ${nombre}</h5>
                        <span class="text-muted">${participantes} miembros</span>
                    </div>

                    <!-- Contenedor de mensajes -->
                    <div class="flex-grow-1 overflow-auto mb-3" id="mensajes">
                        <!-- Aquí se cargarán los mensajes -->
                    </div>

                    <!-- Input de mensaje -->
                    <div class="d-flex">
                        <input type="text" class="form-control me-2" placeholder="Escribe un mensaje...">
                        <button class="btn" style="background-color: #8c4ae2; color: white;">
                            Enviar
                        </button>
                    </div>
                </div>
            `;

        });
    });
});