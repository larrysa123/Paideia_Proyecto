document.addEventListener('DOMContentLoaded', async function() {
    cargarDashboardAdmin();
});

async function cargarDashboardAdmin() {
    try {
        const respuesta = await fetch(BASE_URL + 'app/api/admin.php?accion=dashboard');
        const resultado = await respuesta.json();

        if (resultado.status === 'success') {
            const usuarios = resultado.data.usuarios;
            const cursos = resultado.data.cursos;

            // 1. Actualizar los contadores de las pestañas
            document.getElementById('count-usuarios').innerText = usuarios.length;
            document.getElementById('count-cursos').innerText = cursos.length;

            // 2. Pintar la tabla de Usuarios
            const tbodyUsuarios = document.getElementById('tabla-usuarios');
            tbodyUsuarios.innerHTML = '';
            
            if(usuarios.length === 0) {
                tbodyUsuarios.innerHTML = '<tr><td colspan="5" class="text-center py-4 text-muted">No hay usuarios registrados.</td></tr>';
            } else {
                usuarios.forEach(u => {
                    let badgeColor = u.nombre_rol === 'administrador' ? 'bg-danger' : 
                                    (u.nombre_rol === 'profesor' ? 'bg-info text-dark' : 'bg-secondary');
                    
                    tbodyUsuarios.innerHTML += `
                        <tr>
                            <td class="text-muted">#${u.id_usuario}</td>
                            <td class="fw-bold text-dark">${u.nombre} ${u.apellidos}</td>
                            <td>${u.email}</td>
                            <td><span class="badge ${badgeColor}">${u.nombre_rol.toUpperCase()}</span></td>
                            <td class="text-end">
                                <button class="btn btn-sm btn-outline-danger" onclick="alert('Próximamente: Eliminar usuario ${u.id_usuario}')" title="Eliminar Usuario">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </td>
                        </tr>
                    `;
                });
            }

            // 3. Pintar la tabla de Cursos
            const tbodyCursos = document.getElementById('tabla-cursos');
            tbodyCursos.innerHTML = '';
            
            if(cursos.length === 0) {
                tbodyCursos.innerHTML = '<tr><td colspan="5" class="text-center py-4 text-muted">No hay cursos creados.</td></tr>';
            } else {
                cursos.forEach(c => {
                    let badgeEstado = c.estado === 'publicado' ? 'bg-success' : 'bg-warning text-dark';
                    
                    tbodyCursos.innerHTML += `
                        <tr>
                            <td class="text-muted">#${c.id_curso}</td>
                            <td class="fw-bold text-dark">${c.titulo}</td>
                            <td><i class="bi bi-person-video3 me-1"></i> ${c.profesor}</td>
                            <td><span class="badge ${badgeEstado}">${c.estado.toUpperCase()}</span></td>
                            <td class="text-end">
                                <a href="../cursos/detalle.php?id=${c.id_curso}" target="_blank" class="btn btn-sm btn-outline-secondary" title="Ver Curso">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <button class="btn btn-sm btn-outline-danger" onclick="alert('Próximamente: Eliminar curso ${c.id_curso}')" title="Eliminar Curso">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </td>
                        </tr>
                    `;
                });
            }
        } else {
            alert("Error al cargar el panel: " + resultado.mensaje);
        }
    } catch (error) {
        console.error("Error cargando dashboard de admin:", error);
    }
}