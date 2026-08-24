/**
 * app.js - Funcionalidades JavaScript del Portal de Atencion
 */

document.addEventListener('DOMContentLoaded', function () {

    // Toggle del campo de nuevo estado en el formulario de seguimiento
    const radiosAccion = document.querySelectorAll('input[name="accion"]');
    const divNuevoEstado = document.getElementById('div_nuevo_estado');

    if (radiosAccion.length && divNuevoEstado) {
        radiosAccion.forEach(function (radio) {
            radio.addEventListener('change', function () {
                divNuevoEstado.style.display = this.value === 'cambiar_estado' ? 'block' : 'none';
            });
        });
    }

    // Auto-cerrar alertas despues de 5 segundos
    const alertas = document.querySelectorAll('.alert-dismissible');
    alertas.forEach(function (alerta) {
        setTimeout(function () {
            var bsAlert = bootstrap.Alert.getOrCreateInstance(alerta);
            if (bsAlert) bsAlert.close();
        }, 5000);
    });

    // Confirmar antes de enviar formularios de eliminacion
    const btnEliminar = document.querySelectorAll('[data-confirmar]');
    btnEliminar.forEach(function (btn) {
        btn.addEventListener('click', function (e) {
            if (!confirm(this.getAttribute('data-confirmar'))) {
                e.preventDefault();
            }
        });
    });

    // Validacion basica de formulario de registro de solicitud
    const formSolicitud = document.getElementById('formSolicitud');
    if (formSolicitud) {
        formSolicitud.addEventListener('submit', function (e) {
            const descripcion = document.getElementById('descripcion');
            if (descripcion && descripcion.value.trim().length < 20) {
                e.preventDefault();
                alert('La descripcion debe tener al menos 20 caracteres.');
                descripcion.focus();
            }
        });
    }

    // Contador de caracteres para textarea de descripcion
    const textareaDesc = document.getElementById('descripcion');
    if (textareaDesc) {
        const maxLen = 2000;
        const counter = document.createElement('small');
        counter.className = 'text-muted float-end';
        textareaDesc.parentNode.appendChild(counter);

        function updateCounter() {
            const remaining = maxLen - textareaDesc.value.length;
            counter.textContent = textareaDesc.value.length + ' / ' + maxLen;
            counter.className = remaining < 200 ? 'text-danger float-end' : 'text-muted float-end';
        }

        textareaDesc.addEventListener('input', updateCounter);
        updateCounter();
    }

    // Resaltar fila de tabla al pasar el cursor
    const filasTabla = document.querySelectorAll('.table tbody tr');
    filasTabla.forEach(function (fila) {
        fila.style.cursor = 'pointer';
    });

    // Tooltips de Bootstrap (si estan disponibles)
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.forEach(function (el) {
        new bootstrap.Tooltip(el);
    });

});
