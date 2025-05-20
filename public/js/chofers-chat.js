document.addEventListener('DOMContentLoaded', function () {
    // Variables globales
    let grupoActivo = null;
    let ultimoMensajeId = 0;
    let intervalActualizacion = null;
    const contenidoGrupo = document.getElementById('contenidoGrupo');
    
    // Función para cargar los grupos del usuario vía AJAX
    function cargarGrupos() {
        console.log('Cargando grupos del usuario...');
        
        // Obtener el token CSRF para garantizar que está en todas las solicitudes
        let csrfToken;
        try {
            const metaTag = document.querySelector('meta[name="csrf-token"]');
            if (!metaTag) {
                console.error('No se encontró el meta tag de CSRF');
                csrfToken = '';
            } else {
                csrfToken = metaTag.getAttribute('content');
                console.log('CSRF token obtenido correctamente:', csrfToken.substring(0, 5) + '...');
            }
        } catch (e) {
            console.error('Error al obtener el token CSRF:', e);
            csrfToken = '';
        }
        
        fetch('/api/chofers/grupos', {
            method: 'GET',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            },
            credentials: 'same-origin'
        })
            .then(response => {
                console.log('Respuesta recibida para grupos con estado:', response.status);
                
                // En caso de error 500, intentar obtener más información
                if (response.status === 500) {
                    // Intentar leer el cuerpo del error
                    return response.json()
                        .then(errorData => {
                            console.error('Detalles del error 500:', errorData);
                            throw new Error('Error del servidor (500): ' + 
                                (errorData.error ? errorData.error : 'Error interno del servidor'));
                        })
                        .catch(err => {
                            // Si no se puede leer el JSON, usar el error genérico
                            console.error('No se pudo leer detalles del error 500');
                            throw new Error('Error interno del servidor (500)');
                        });
                }
                
                if (!response.ok) {
                    throw new Error('Error en la respuesta del servidor: ' + response.status);
                }
                
                return response.json();
            })
            .then(data => {
                console.log('Respuesta de grupos:', data);
                const listaGrupos = document.querySelector('.izq_grupos .list-group');
                
                // Si no hay grupos, mostrar mensaje
                if (!data.grupos || data.grupos.length === 0) {
                    console.log('No se encontraron grupos');
                    listaGrupos.innerHTML = '<li class="list-group-item">No perteneces a ningún grupo aún.</li>';
                    return;
                }
                
                console.log('Encontrados', data.grupos.length, 'grupos');
                
                // Limpiar lista actual
                listaGrupos.innerHTML = '';
                
                // Añadir cada grupo a la lista
                data.grupos.forEach(grupo => {
                    console.log('Procesando grupo:', grupo.id, grupo.nombre, 'con', grupo.usuarios_count, 'miembros');
                    console.log('Debug info:', grupo.debug_info);
                    
                    // Extraer nombres de usuario para mostrar en el modal de info
                    let usuarios = [];
                    if (grupo.usuarios && Array.isArray(grupo.usuarios)) {
                        usuarios = grupo.usuarios.map(u => u.nombre);
                        console.log('Nombres de usuarios en el grupo:', usuarios);
                    } else {
                        console.warn('No hay usuarios o no es un array para el grupo', grupo.id);
                    }
                    
                    // Garantizar que usuarios es válido para JSON.stringify
                    const usuariosStr = JSON.stringify(usuarios);
                    console.log('String de usuarios:', usuariosStr);
                    
                    listaGrupos.innerHTML += `
                        <li class="list-group-item">
                            <a href="javascript:void(0)" class="grupo-link d-flex align-items-center text-decoration-none" 
                               data-id="${grupo.id}" 
                               data-nombre="${grupo.nombre}" 
                               data-participantes="${grupo.usuarios_count}" 
                               data-miembros='${usuariosStr}'>
                                <div class="me-2">
                                    ${grupo.imagen ? 
                                    `<img src="${grupo.imagen}" class="rounded-circle" width="40" height="40" alt="${grupo.nombre}">` : 
                                    `<div class="rounded-circle bg-secondary text-white d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;"><i class="fa-solid fa-users"></i></div>`}
                                </div>
                                <div class="flex-grow-1">
                                    <h6 class="mb-0">${grupo.nombre}</h6>
                                    <small class="text-muted">${grupo.usuarios_count} miembros</small>
                                </div>
                                <i class="fa-solid fa-chevron-right text-muted"></i>
                            </a>
                        </li>
                    `;
                });
                
                console.log('HTML de grupos actualizado, configurando eventos...');
                
                // Añadir eventos a los nuevos enlaces
                configurarEnlacesGrupo();
            })
            .catch(error => {
                console.error('Error al cargar grupos:', error);
                alert('Error al cargar los grupos: ' + error.message);
            });
    }
    
    // Función para configurar los eventos de los enlaces de grupo
    function configurarEnlacesGrupo() {
        console.log('Configurando enlaces para los grupos');
        const enlacesGrupo = document.querySelectorAll('.grupo-link');
        console.log('Encontrados', enlacesGrupo.length, 'enlaces de grupo');
        
        // Eliminar los eventListeners anteriores para evitar duplicados
        enlacesGrupo.forEach(link => {
            const nuevoLink = link.cloneNode(true);
            link.parentNode.replaceChild(nuevoLink, link);
        });
        
        // Volver a seleccionar los enlaces después de clonarlos
        const nuevosEnlaces = document.querySelectorAll('.grupo-link');
        
        nuevosEnlaces.forEach(link => {
            link.addEventListener('click', function (e) {
                e.preventDefault();
                console.log('Clic en grupo:', this.dataset.id, this.dataset.nombre);
                
                // Limpiar intervalo anterior si existe
                if (intervalActualizacion) {
                    clearInterval(intervalActualizacion);
                }
                
                // Almacenar el ID del grupo activo
                grupoActivo = this.dataset.id;
                ultimoMensajeId = 0;
                
                console.log('Grupo activo establecido a:', grupoActivo);
                
                const nombre = this.dataset.nombre;
                const participantes = this.dataset.participantes;
                let miembros = [];
                
                try {
                    miembros = JSON.parse(this.dataset.miembros);
                    console.log('Miembros del grupo:', miembros);
                } catch (error) {
                    console.error('Error al parsear miembros:', error, 'Valor original:', this.dataset.miembros);
                    // Si hay error al parsear, usar un array vacío
                    miembros = [];
                }
                
                // Resaltar el grupo seleccionado
                nuevosEnlaces.forEach(l => l.parentElement.classList.remove('active'));
                this.parentElement.classList.add('active');
                
                // Mostrar la interfaz del chat
                mostrarInterfazChat(nombre);
                
                // Cargar mensajes del grupo
                cargarMensajes();
                
                // Configurar intervalo para actualizar mensajes cada 5 segundos
                intervalActualizacion = setInterval(cargarMensajes, 5000);
            });
        });
    }
    
    // Función para mostrar la interfaz del chat
    function mostrarInterfazChat(nombreGrupo) {
        contenidoGrupo.innerHTML = `
            <div class="chat-wrapper d-flex flex-column h-100">
                <!-- Encabezado -->
                <div class="d-flex justify-content-between align-items-center border-bottom pb-2 mb-3">
                    <div>
                        <h5 class="mb-0">Grupo: ${nombreGrupo}</h5>
                    </div>
                    <button class="btn btn-outline-secondary btn-sm" id="infoGrupoBtn">
                        <i class="fa-solid fa-circle-info"></i> Info
                    </button>
                </div>

                <!-- Mensajes -->
                <div class="flex-grow-1 overflow-auto mb-3" id="mensajes">
                    <div class="text-center text-muted p-3">
                        <div class="spinner-border spinner-border-sm" role="status">
                            <span class="visually-hidden">Cargando...</span>
                        </div>
                        <p>Cargando mensajes...</p>
                    </div>
                </div>

                <!-- Input en parte inferior -->
                <div class="input-mensaje">
                    <input type="text" class="form-control me-2" id="mensaje-input" placeholder="Escribe un mensaje...">
                    <button class="btn" id="enviar-mensaje" style="background-color: #8c4ae2; color: white;">
                        <i class="fa-solid fa-paper-plane"></i>
                    </button>
                </div>
            </div>
        `;
        
        // Activar botón de info
        document.getElementById('infoGrupoBtn').addEventListener('click', mostrarInfoGrupo);
        
        // Configurar envío de mensaje
        document.getElementById('enviar-mensaje').addEventListener('click', enviarMensaje);
        document.getElementById('mensaje-input').addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                enviarMensaje();
            }
        });
    }
    
    // Función para mostrar información del grupo
    function mostrarInfoGrupo() {
        const grupoLink = document.querySelector(`.grupo-link[data-id="${grupoActivo}"]`);
        if (!grupoLink) return;
        
        const nombre = grupoLink.dataset.nombre;
        const miembros = JSON.parse(grupoLink.dataset.miembros);
        
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
    }
    
    // Función para cargar mensajes del grupo activo
    function cargarMensajes() {
        if (!grupoActivo) {
            console.log('No hay grupo activo, no se cargan mensajes');
            return;
        }
        
        console.log('Cargando mensajes para el grupo:', grupoActivo, 'desde ID:', ultimoMensajeId);
        
        // Obtener el token CSRF
        let csrfToken;
        try {
            const metaTag = document.querySelector('meta[name="csrf-token"]');
            if (!metaTag) {
                console.error('No se encontró el meta tag de CSRF');
                csrfToken = '';
            } else {
                csrfToken = metaTag.getAttribute('content');
                console.log('CSRF token obtenido correctamente');
            }
        } catch (e) {
            console.error('Error al obtener el token CSRF:', e);
            csrfToken = '';
        }
        
        const formData = new FormData();
        formData.append('grupo_id', grupoActivo);
        formData.append('last_id', ultimoMensajeId);
        
        // Mostrar que estamos intentando cargar mensajes
        console.log('Enviando solicitud para cargar mensajes...');
        
        fetch('/api/chofers/mensajes', {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': csrfToken
            },
            credentials: 'same-origin'
        })
        .then(response => {
            console.log('Respuesta recibida con estado:', response.status);
            if (!response.ok) {
                throw new Error('Error en la respuesta del servidor: ' + response.status);
            }
            return response.json();
        })
        .then(data => {
            console.log('Datos de mensajes recibidos:', data);
            // Actualizar último ID conocido
            ultimoMensajeId = data.last_id;
            const contenedorMensajes = document.getElementById('mensajes');
            
            // Siempre eliminar el spinner de carga si está presente
            if (contenedorMensajes.querySelector('.spinner-border')) {
                contenedorMensajes.innerHTML = '';
            }
            
            // Si hay mensajes nuevos, añadirlos
            if (data.messages && data.messages.length > 0) {                
                // Añadir cada mensaje nuevo
                data.messages.forEach(mensaje => {
                    const claseAlineacion = mensaje.is_own ? 'align-self-end bg-primary text-white' : 'align-self-start bg-light';
                    
                    contenedorMensajes.innerHTML += `
                        <div class="mensaje ${claseAlineacion} p-2 rounded mb-2 mw-75" style="max-width: 75%">
                            <div class="d-flex align-items-center mb-1">
                                <small class="fw-bold">${mensaje.sender_name}</small>
                            </div>
                            <div>${mensaje.message}</div>
                            <div class="text-end">
                                <small class="text-muted">${mensaje.created_at}</small>
                            </div>
                        </div>
                    `;
                });
                
                // Hacer scroll hasta el último mensaje
                contenedorMensajes.scrollTop = contenedorMensajes.scrollHeight;
            } else if (ultimoMensajeId === 0) {
                // Si no hay mensajes y es la primera carga, mostrar mensaje
                contenedorMensajes.innerHTML = `
                    <div class="text-center text-muted p-3">
                        <p>No hay mensajes en este grupo. ¡Sé el primero en escribir algo!</p>
                    </div>
                `;
            } else if (contenedorMensajes.innerHTML === '') {
                // Si por alguna razón el contenedor está vacío, mostrar mensaje
                contenedorMensajes.innerHTML = `
                    <div class="text-center text-muted p-3">
                        <p>No hay mensajes en este grupo. ¡Sé el primero en escribir algo!</p>
                    </div>
                `;
            }
        })
        .catch(error => {
            console.error('Error al cargar mensajes:', error);
        });
    }
    
    // Función para enviar un mensaje
    function enviarMensaje() {
        if (!grupoActivo) {
            console.log('No hay grupo activo, no se puede enviar mensaje');
            return;
        }
        
        const input = document.getElementById('mensaje-input');
        const mensaje = input.value.trim();
        
        if (!mensaje) {
            console.log('Mensaje vacío, no se envía');
            return;
        }
        
        console.log('Preparando para enviar mensaje al grupo:', grupoActivo);
        
        // Obtener el token CSRF
        let csrfToken;
        try {
            const metaTag = document.querySelector('meta[name="csrf-token"]');
            if (!metaTag) {
                console.error('No se encontró el meta tag de CSRF');
                csrfToken = '';
            } else {
                csrfToken = metaTag.getAttribute('content');
                console.log('CSRF token obtenido correctamente para envío:', csrfToken.substring(0, 5) + '...');
            }
        } catch (e) {
            console.error('Error al obtener el token CSRF:', e);
            csrfToken = '';
        }
        
        const formData = new FormData();
        formData.append('grupo_id', grupoActivo);
        formData.append('message', mensaje);
        
        console.log('Datos del mensaje:', {
            grupo_id: grupoActivo,
            mensaje: mensaje.substring(0, 20) + (mensaje.length > 20 ? '...' : '')
        });
        
        // Deshabilitar el botón y el input mientras se envía
        const botonEnviar = document.getElementById('enviar-mensaje');
        botonEnviar.disabled = true;
        input.disabled = true;
        
        console.log('Enviando mensaje...');
        
        fetch('/api/chofers/mensajes/enviar', {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            },
            credentials: 'same-origin'
        })
        .then(response => {
            console.log('Respuesta recibida con estado:', response.status);
            
            // Habilitar el botón y el input de nuevo
            botonEnviar.disabled = false;
            input.disabled = false;
            
            // En caso de error 500, intentar obtener más información
            if (response.status === 500) {
                // Intentar leer el cuerpo del error
                return response.json()
                    .then(errorData => {
                        console.error('Detalles del error 500:', errorData);
                        if (errorData.error) {
                            throw new Error('Error del servidor: ' + errorData.error);
                        } else {
                            throw new Error('Error interno del servidor');
                        }
                    })
                    .catch(err => {
                        console.error('No se pudo leer detalles del error 500');
                        throw new Error('Error interno del servidor (500)');
                    });
            }
            
            if (!response.ok) {
                throw new Error('Error en la respuesta del servidor: ' + response.status);
            }
            
            return response.json();
        })
        .then(data => {
            console.log('Datos del mensaje enviado:', data);
            if (data.success) {
                // Limpiar input
                input.value = '';
                
                // Añadir mensaje a la conversación
                const contenedorMensajes = document.getElementById('mensajes');
                const mensaje = data.message;
                
                // Si es el primer mensaje, limpiar el mensaje de "no hay mensajes"
                if (contenedorMensajes.querySelector('.text-center.text-muted')) {
                    contenedorMensajes.innerHTML = '';
                }
                
                contenedorMensajes.innerHTML += `
                    <div class="mensaje align-self-end bg-primary text-white p-2 rounded mb-2" style="max-width: 75%">
                        <div class="d-flex align-items-center mb-1">
                            <small class="fw-bold">${mensaje.sender_name}</small>
                        </div>
                        <div>${mensaje.message}</div>
                        <div class="text-end">
                            <small class="text-muted">${mensaje.created_at}</small>
                        </div>
                    </div>
                `;
                
                // Actualizar el último ID de mensaje
                ultimoMensajeId = mensaje.id;
                
                // Hacer scroll hasta el último mensaje
                contenedorMensajes.scrollTop = contenedorMensajes.scrollHeight;
            }
        })
        .catch(error => {
            console.error('Error al enviar mensaje:', error);
            // Habilitar el botón y el input si hubo un error
            const botonEnviar = document.getElementById('enviar-mensaje');
            botonEnviar.disabled = false;
            input.disabled = false;
            alert('Error al enviar el mensaje: ' + error.message);
        });
    }
    
    // Actualizar el form action para crear grupo
    const formCrearGrupo = document.querySelector('#crearGrupoModal form');
    if (formCrearGrupo) {
        formCrearGrupo.setAttribute('action', '/chofers/grupos');
    }
    
    // Iniciar carga de grupos al cargar la página
    cargarGrupos();
    
    // Evento para recargar grupos después de crear uno nuevo
    formCrearGrupo.addEventListener('submit', function(e) {
        // El envío del formulario se hace normalmente, pero queremos recargar los grupos después
        
        // Mostrar un mensaje de carga o procesamiento
        const submitBtn = this.querySelector('button[type="submit"]');
        const originalText = submitBtn.innerHTML;
        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Creando...'
        submitBtn.disabled = true;
        
        setTimeout(() => {
            // Cerrar el modal
            const modal = bootstrap.Modal.getInstance(document.getElementById('crearGrupoModal'));
            if (modal) {
                modal.hide();
            }
            
            // Recargar los grupos
            cargarGrupos();
            
            // Restaurar el botón
            submitBtn.innerHTML = originalText;
            submitBtn.disabled = false;
        }, 1500); // Esperar 1.5 segundos para asegurar que el grupo se haya creado
    });
});
