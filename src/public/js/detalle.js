document.addEventListener('DOMContentLoaded', async function () {

    const urlParams = new URLSearchParams(window.location.search);
    const idCurso = urlParams.get('id');
    let precioCursoNum = 0; // Guardaremos el precio para pasarlo al backend

    if (!idCurso) {
        alert("Curso no especificado.");
        window.location.href = '../../index.php';
        return;
    }

    // --- FASE 1: CARGAR DETALLES DEL CURSO ---
    try {
        // Ruta corregida al Alias
        const respuesta = await fetch('/api/cursos.php?id_publico=' + idCurso);
        const resultado = await respuesta.json();

        document.getElementById('cargando-detalle').classList.add('d-none');

        if (resultado.status === 'success') {
            const curso = resultado.data;
            const rutaImagen = curso.imagen ? `../../assets/img/cursos/${curso.imagen}` : 'https://via.placeholder.com/800x400?text=Paideia+Curso';

            precioCursoNum = parseFloat(curso.precio); // Guardamos el valor numérico

            document.getElementById('det-imagen').src = rutaImagen;
            document.getElementById('det-titulo').innerText = curso.titulo;
            document.getElementById('det-descripcion').innerText = curso.descripcion || 'Este curso no tiene descripción detallada aún.';
            document.getElementById('det-precio').innerText = curso.precio + ' €';

            // Actualizamos también el texto dentro del Modal de pago
            document.getElementById('modal-precio-total').innerText = curso.precio + ' €';

            document.getElementById('contenido-detalle').classList.remove('d-none');
        } else {
            document.getElementById('cargando-detalle').innerHTML = `<h3 class="text-danger">${resultado.mensaje}</h3>`;
            document.getElementById('cargando-detalle').classList.remove('d-none');
            return;
        }

    } catch (error) {
        console.error("Error al cargar detalles:", error);
    }

    // --- FASE 2: ABRIR EL MODAL ---
    const btnInscribirse = document.getElementById('btn-inscribirse');
    let modalPagoInstance = null;

    if (btnInscribirse) {
        btnInscribirse.addEventListener('click', function () {
            const modalEl = document.getElementById('modalPago');
            modalPagoInstance = new bootstrap.Modal(modalEl);
            modalPagoInstance.show();
        });
    }

    // --- FASE 3: PROCESAR EL PAGO ---
    const formPago = document.getElementById('form-pago');

    if (formPago) {
        formPago.addEventListener('submit', async function (e) {
            e.preventDefault(); // Evitamos que la página se recargue

            const btnProcesar = document.getElementById('btn-procesar-pago');
            const textoOriginal = btnProcesar.innerHTML;

            // Animación de carga
            btnProcesar.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Procesando pago...';
            btnProcesar.disabled = true;

            try {
                // Ruta corregida al Alias
                const resPedido = await fetch('/api/pedidos.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        id_curso: idCurso,
                        precio: precioCursoNum
                    })
                });

                const final = await resPedido.json();

                if (final.status === 'success') {
                    alert(final.mensaje);
                    window.location.href = '../alumno/mis_cursos.php';
                } else {
                    if (final.code === 'NO_LOGIN') {
                        window.location.href = '../login.php';
                    } else if (final.code === 'YA_INSCRITO') {
                        alert(final.mensaje);
                        window.location.href = '../alumno/mis_cursos.php';
                    } else {
                        alert(final.mensaje);
                    }
                }
            } catch (error) {
                console.error("Error al pagar:", error);
                alert("Error de red al conectar con la pasarela de pago.");
            } finally {
                btnProcesar.innerHTML = textoOriginal;
                btnProcesar.disabled = false;
                if (modalPagoInstance) modalPagoInstance.hide();
            }
        });
    }
});