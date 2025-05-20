<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use App\Models\Grupo;
use App\Models\Message;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ChoferController extends Controller
{
    /**
     * Extrae la sede de un usuario chofer basado en su email
     * Por ejemplo: de chofer.barcelona1@carflow.com obtiene 'Barcelona'
     */
    private function obtenerSedeDeEmail($email)
    {
        $partes = explode('@', $email);
        $username = $partes[0]; // chofer.barcelona1

        $partesSede = explode('.', $username);
        if (count($partesSede) >= 2) {
            $sedeConNumero = $partesSede[1]; // barcelona1
            // Extraer solo el texto de la sede (eliminar números)
            preg_match('/([a-zA-Z]+)/', $sedeConNumero, $matches);
            if (isset($matches[1])) {
                // Capitalizar la primera letra
                return ucfirst($matches[1]); // Barcelona
            }
        }

        return 'Central'; // Valor por defecto si no se puede determinar
    }

    /**
     * Muestra el dashboard específico para la sede del chofer logueado
     */
    public function dashboard()
    {
        $usuarioActual = Auth::user();
        $sede = $this->obtenerSedeDeEmail($usuarioActual->email);

        // Obtener todos los choferes de la misma sede
        $choferesDeLaSede = User::where('id_roles', 6) // ID 6 = rol de chofer
            ->where(function ($query) use ($sede) {
                $query->where('email', 'LIKE', "chofer.{$sede}%@carflow.com")
                    ->orWhere('email', 'LIKE', "chofer.{$sede}%@%");
            })
            ->get();

        return view('chofers.dashboard', [
            'sede' => $sede,
            'choferesCompaneros' => $choferesDeLaSede
        ]);
    }

    /**
     * Muestra la vista para que los clientes soliciten un chofer
     */
    public function pideCoche()
    {
        return view('chofers.cliente-pide');
    }

    /**
     * API para obtener los choferes de una sede específica
     */
    public function getChoferesPorSede($sede)
    {
        $choferes = User::where('id_roles', 6) // ID 6 = rol de chofer
            ->where(function ($query) use ($sede) {
                $query->where('email', 'LIKE', "chofer.{$sede}%@carflow.com")
                    ->orWhere('email', 'LIKE', "chofer.{$sede}%@%");
            })
            ->get(['id_usuario', 'nombre', 'email', 'telefono', 'foto_perfil']);

        return response()->json($choferes);
    }

    // Método para mostrar la vista del chat
    public function showChatView()
    {
        $usuarioActual = Auth::user();
        $sede = $this->obtenerSedeDeEmail($usuarioActual->email);

        // Obtener choferes compañeros
        $choferesDeLaSede = User::where('id_roles', 6)
            ->where(function ($query) use ($sede) {
                $query->where('email', 'LIKE', "chofer.{$sede}%@carflow.com")
                    ->orWhere('email', 'LIKE', "chofer.{$sede}%@%");
            })
            ->get();

        // Inicializar grupos de chat si es necesario
        $this->inicializarGruposChat($usuarioActual, $sede, $choferesDeLaSede);

        // Obtener los grupos donde el usuario actual está asignado mediante la relación muchos a muchos
        $grupos = $usuarioActual->grupos()->get();

        return view('chofers.chat', [
            'choferesCompaneros' => $choferesDeLaSede,
            'grupos' => $grupos,
        ]);
    }
    
    /**
     * Inicializa los grupos de chat para un usuario si no tiene ninguno
     */
    private function inicializarGruposChat($usuarioActual, $sede, $choferesDeLaSede)
    {
        // Verificar si el usuario ya tiene grupos
        $tieneGrupos = DB::table('grupo_usuario')
            ->where('id_usuario', $usuarioActual->id_usuario)
            ->exists();
            
        if (!$tieneGrupos) {
            // Verificar si hay un grupo general para la sede
            $nombreGrupoGeneral = "Choferes {$sede}";
            $grupoGeneral = Grupo::where('nombre', $nombreGrupoGeneral)->first();
            
            if (!$grupoGeneral) {
                // Crear grupo general para todos los choferes de la sede
                $grupoGeneral = new Grupo();
                $grupoGeneral->nombre = $nombreGrupoGeneral;
                $grupoGeneral->fecha_creacion = now();
                $grupoGeneral->save();
                
                // Añadir a todos los choferes de la sede al grupo
                foreach ($choferesDeLaSede as $chofer) {
                    DB::table('grupo_usuario')->insert([
                        'grupo_id' => $grupoGeneral->id,
                        'id_usuario' => $chofer->id_usuario,
                        'created_at' => now(),
                        'updated_at' => now()
                    ]);
                }
                
                // Añadir mensaje de bienvenida
                $mensaje = new Message();
                $mensaje->message = "¡Bienvenidos al grupo de choferes de {$sede}!";
                $mensaje->sender_id = $usuarioActual->id_usuario;
                $mensaje->grupo_id = $grupoGeneral->id;
                $mensaje->save();
            } else {
                // Asegurarse de que el usuario actual está en el grupo general
                $yaEstaEnGrupo = DB::table('grupo_usuario')
                    ->where('grupo_id', $grupoGeneral->id)
                    ->where('id_usuario', $usuarioActual->id_usuario)
                    ->exists();
                    
                if (!$yaEstaEnGrupo) {
                    DB::table('grupo_usuario')->insert([
                        'grupo_id' => $grupoGeneral->id,
                        'id_usuario' => $usuarioActual->id_usuario,
                        'created_at' => now(),
                        'updated_at' => now()
                    ]);
                }
            }
        }
    }



    public function storeGrupo(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'imagen_grupo' => 'nullable|image|max:2048',
            'usuarios' => 'nullable|array'
        ]);

        $usuarioActual = Auth::user();

        $grupo = new Grupo();
        $grupo->nombre = $request->nombre;
        $grupo->fecha_creacion = now();

        if ($request->hasFile('imagen_grupo')) {
            $grupo->imagen_grupo = $request->file('imagen_grupo')->store('img', 'public');
        }

        $grupo->save();

        // Primero asignamos al creador del grupo
        DB::table('grupo_usuario')->insert([
            'grupo_id' => $grupo->id,
            'id_usuario' => $usuarioActual->id_usuario,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        // Asociar usuarios adicionales al grupo (si se seleccionaron)
        if ($request->has('usuarios')) {
            $usuariosSeleccionados = array_diff($request->usuarios, [$usuarioActual->id_usuario]);
            
            foreach ($usuariosSeleccionados as $userId) {
                DB::table('grupo_usuario')->insert([
                    'grupo_id' => $grupo->id,
                    'id_usuario' => $userId,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }
        }

        return redirect()->back()->with('success', 'Grupo creado correctamente.');
    }
    
    /**
     * Método para obtener los mensajes de un grupo específico
     * Se usa vía AJAX para cargar y actualizar mensajes en tiempo real
     */
    public function getGrupoMensajes(Request $request)
    {
        try {
            $request->validate([
                'grupo_id' => 'required|integer|exists:grupos,id',
                'last_id' => 'nullable|integer' // Para traer solo mensajes nuevos
            ]);
            
            $usuarioActual = Auth::user();
            $grupoId = $request->grupo_id;
            $lastId = $request->last_id ?? 0;
            
            // Obtener información del grupo para depuración
            $grupoInfo = Grupo::find($grupoId);
            if (!$grupoInfo) {
                return response()->json([
                    'error' => 'El grupo no existe',
                    'debug' => ['grupo_id' => $grupoId]
                ], 404);
            }
            
            // Verificar que el usuario pertenece al grupo usando la tabla pivote
            $miembrosGrupo = DB::table('grupo_usuario')
                ->where('grupo_id', $grupoId)
                ->pluck('id_usuario')
                ->toArray();
                
            $userBelongsToGroup = in_array($usuarioActual->id_usuario, $miembrosGrupo);
            
            // Si el usuario no está en el grupo pero debería estar, intentamos añadirlo (corrección automática)
            if (!$userBelongsToGroup && $grupoInfo) {
                // Añadir el usuario al grupo
                DB::table('grupo_usuario')->insert([
                    'grupo_id' => $grupoId,
                    'id_usuario' => $usuarioActual->id_usuario,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
                
                // Actualizar el estado
                $userBelongsToGroup = true;
            }
                
            if (!$userBelongsToGroup) {
                return response()->json([
                    'error' => 'No tienes acceso a este grupo',
                    'debug' => [
                        'user_id' => $usuarioActual->id_usuario, 
                        'grupo_id' => $grupoId,
                        'miembros_grupo' => $miembrosGrupo
                    ]
                ], 403);
            }
            
            // Obtener mensajes más recientes que el último ID conocido
            $mensajes = Message::where('grupo_id', $grupoId)
                ->when($lastId > 0, function($query) use ($lastId) {
                    return $query->where('id', '>', $lastId);
                })
                ->orderBy('created_at', 'asc')
                ->get();
                
            // Cargar todos los usuarios necesarios para los mensajes en una sola consulta eficiente
            $userIds = $mensajes->pluck('user_id')->unique()->filter()->toArray();
            $users = User::whereIn('id_usuario', $userIds)
                ->select('id_usuario', 'nombre', 'foto_perfil')
                ->get()
                ->keyBy('id_usuario');
                
            // Formato para cada mensaje
            $formattedMessages = $mensajes->map(function($mensaje) use ($usuarioActual, $users) {
                // Determinar si el mensaje es propio
                $isOwn = $mensaje->user_id == $usuarioActual->id_usuario;
                
                // Obtener el remitente si existe
                $sender = isset($users[$mensaje->user_id]) ? $users[$mensaje->user_id] : null;
                
                return [
                    'id' => $mensaje->id,
                    'message' => $mensaje->message,
                    'sender_name' => $sender ? $sender->nombre : 'Usuario desconocido',
                    'sender_avatar' => $sender ? $sender->foto_perfil : null,
                    'created_at' => Carbon::parse($mensaje->created_at)->format('d/m/Y H:i'),
                    'is_own' => $isOwn,
                    'user_id' => $mensaje->user_id // Esto nos ayuda a depurar si es necesario
                ];
            });
            
            return response()->json([
                'messages' => $formattedMessages,
                'last_id' => $mensajes->count() > 0 ? $mensajes->last()->id : $lastId,
                'grupo_info' => [
                    'id' => $grupoInfo->id,
                    'nombre' => $grupoInfo->nombre,
                    'miembros_count' => count($miembrosGrupo)
                ]
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Error al procesar la solicitud: ' . $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ], 500);
        }
    }
    
    /**
     * Método para enviar un mensaje a un grupo
     * Se usa vía AJAX para enviar mensajes en tiempo real
     */
    public function sendGrupoMensaje(Request $request)
    {
        try {
            $request->validate([
                'grupo_id' => 'required|integer|exists:grupos,id',
                'message' => 'required|string|max:1000'
            ]);
            
            $usuarioActual = Auth::user();
            $grupoId = $request->grupo_id;
            
            // Verificar que el grupo existe
            $grupoInfo = Grupo::find($grupoId);
            if (!$grupoInfo) {
                return response()->json([
                    'error' => 'El grupo no existe',
                    'debug' => ['grupo_id' => $grupoId]
                ], 404);
            }
            
            // Obtener todos los miembros del grupo
            $miembrosGrupo = DB::table('grupo_usuario')
                ->where('grupo_id', $grupoId)
                ->pluck('id_usuario')
                ->toArray();
                
            // Verificar que el usuario está en la lista de miembros
            $userBelongsToGroup = in_array($usuarioActual->id_usuario, $miembrosGrupo);
            
            // Si el usuario no está en el grupo pero debería estar, intentamos añadirlo (corrección automática)
            if (!$userBelongsToGroup && $grupoInfo) {
                // Añadir el usuario al grupo
                DB::table('grupo_usuario')->insert([
                    'grupo_id' => $grupoId,
                    'id_usuario' => $usuarioActual->id_usuario,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
                
                // Actualizar el estado
                $userBelongsToGroup = true;
                
                // Actualizar la lista de miembros
                $miembrosGrupo[] = $usuarioActual->id_usuario;
            }
            
            if (!$userBelongsToGroup) {
                return response()->json([
                    'error' => 'No tienes acceso a este grupo',
                    'debug' => [
                        'user_id' => $usuarioActual->id_usuario, 
                        'grupo_id' => $grupoId,
                        'miembros_grupo' => $miembrosGrupo,
                        'grupo_info' => $grupoInfo ? $grupoInfo->toArray() : null
                    ]
                ], 403);
            }
            
            // Crear el mensaje - Ahora con los campos correctos según la estructura de la tabla
            $mensaje = new Message();
            $mensaje->message = $request->message;
            $mensaje->user_id = $usuarioActual->id_usuario; // Usar user_id en lugar de sender_id
            $mensaje->sender_type = 'user'; // Indicar que el remitente es un usuario
            $mensaje->grupo_id = $grupoId;
            $mensaje->gestor_id = null; // No aplica para mensaje de grupo
            $mensaje->save();
            
            // Devolver el mensaje con formato - usando los campos correctos
            return response()->json([
                'message' => [
                    'id' => $mensaje->id,
                    'message' => $mensaje->message,
                    'sender_name' => $usuarioActual->nombre,
                    'sender_avatar' => $usuarioActual->foto_perfil,
                    'created_at' => Carbon::parse($mensaje->created_at)->format('d/m/Y H:i'),
                    'is_own' => true,
                    'debug_info' => [
                        'sender_type' => $mensaje->sender_type,
                        'user_id' => $mensaje->user_id
                    ]
                ],
                'success' => true
            ]);
            
        } catch (\Exception $e) {
            // Aquí capturamos cualquier error, incluyendo la creación del mensaje
            return response()->json([
                'error' => 'Error al procesar la solicitud: ' . $e->getMessage(),
                'stack' => $e->getTraceAsString(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ], 500);
        }
    }
    
    /**
     * Método para obtener la lista de grupos a los que pertenece el usuario
     * Se usa vía AJAX para actualizar la lista de grupos
     */
    public function getUserGrupos()
    {
        try {
            $usuarioActual = Auth::user();
            
            if (!$usuarioActual) {
                return response()->json([
                    'error' => 'Usuario no autenticado',
                    'code' => 'AUTH_ERROR'
                ], 401);
            }
            
            // Verificar si la relación grupos existe en el modelo User
            if (!method_exists($usuarioActual, 'grupos')) {
                return response()->json([
                    'error' => 'Relación grupos no definida en el modelo User',
                    'code' => 'RELATION_ERROR',
                    'user_info' => [
                        'id' => $usuarioActual->id_usuario,
                        'nombre' => $usuarioActual->nombre,
                        'methods' => get_class_methods(get_class($usuarioActual))
                    ]
                ], 500);
            }
            
            // Verificar si la tabla 'grupo_usuario' existe
            try {
                $tablaExiste = DB::select("SHOW TABLES LIKE 'grupo_usuario'");
                if (empty($tablaExiste)) {
                    return response()->json([
                        'error' => 'La tabla grupo_usuario no existe en la base de datos',
                        'code' => 'TABLE_MISSING'
                    ], 500);
                }
            } catch (\Exception $e) {
                return response()->json([
                    'error' => 'Error al verificar la tabla grupo_usuario: ' . $e->getMessage(),
                    'code' => 'TABLE_CHECK_ERROR'
                ], 500);
            }
            
            // Obtener todos los grupos a los que pertenece el usuario con la relación muchos a muchos
            // Aplicamos primero un query builder para verificar si la relación funciona
            $grupoIds = DB::table('grupo_usuario')
                ->where('id_usuario', $usuarioActual->id_usuario)
                ->pluck('grupo_id')
                ->toArray();
                
            if (empty($grupoIds)) {
                // Si no hay grupos, devolver array vacío en lugar de error
                return response()->json([
                    'grupos' => [],
                    'debug_info' => [
                        'mensaje' => 'No se encontraron grupos para este usuario',
                        'user_id' => $usuarioActual->id_usuario
                    ]
                ]);
            }
            
            // Obtener los grupos directamente de la tabla Grupo
            $grupos = Grupo::whereIn('id', $grupoIds)
                ->get();
                
            // Formato para cada grupo con información de depuración
            $formattedGrupos = $grupos->map(function($grupo) use ($usuarioActual) {
                try {
                    // Contar miembros mediante la tabla pivote para asegurar precisión
                    $miembrosCount = DB::table('grupo_usuario')
                        ->where('grupo_id', $grupo->id)
                        ->count();
                        
                    // Lista real de usuarios para debugging
                    $miembrosIds = DB::table('grupo_usuario')
                        ->where('grupo_id', $grupo->id)
                        ->pluck('id_usuario')
                        ->toArray();
                    
                    // Obtener información de usuarios
                    $usuarios = User::whereIn('id_usuario', $miembrosIds)
                        ->select('id_usuario', 'nombre', 'foto_perfil')
                        ->get()
                        ->map(function($user) {
                            return [
                                'id' => $user->id_usuario,
                                'nombre' => $user->nombre,
                                'foto' => $user->foto_perfil
                            ];
                        })->toArray();
                        
                    return [
                        'id' => $grupo->id,
                        'nombre' => $grupo->nombre,
                        'imagen' => $grupo->imagen_grupo,
                        'usuarios_count' => $miembrosCount,
                        'usuarios' => $usuarios,
                        'debug_info' => [
                            'miembros_ids' => $miembrosIds
                        ]
                    ];
                } catch (\Exception $e) {
                    // Si hay un error procesando un grupo específico, incluir información del error pero continuar
                    return [
                        'id' => $grupo->id,
                        'nombre' => $grupo->nombre, 
                        'error' => 'Error procesando grupo: ' . $e->getMessage(),
                        'usuarios_count' => 0,
                        'usuarios' => []
                    ];
                }
            });
            
            return response()->json([
                'grupos' => $formattedGrupos,
                'debug_info' => [
                    'user_id' => $usuarioActual->id_usuario,
                    'grupo_ids' => $grupoIds,
                    'timestamp' => now()->format('Y-m-d H:i:s')
                ]
            ]);
        } catch (\Exception $e) {
            // Error general
            return response()->json([
                'error' => 'Error al obtener grupos: ' . $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'code' => 'GENERAL_ERROR'
            ], 500);
        }
    }
}
