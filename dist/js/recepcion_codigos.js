window.onload = () => {
    // --- Estado GS1 ---
    let codigo01 = null;
    let codigo17 = null;

    // Elementos
    const input = document.getElementById('codigo_recep');      // input donde escaneas
    const mensajeElement = document.getElementById('msg_recep'); // div de mensajes

    if (!input) {
        console.warn("No se encontró el input #codigo_recep en la página de recepción.");
        return;
    }

    limpiarMensaje();

    // Evento principal para escanear códigos en recepción
    input.addEventListener('keypress', function (e) {
        if (e.key === 'Enter') {
            const codigo = input.value.trim();
            input.value = '';
            limpiarMensaje();

            const operacion = 'sumar'; // en recepciones SIEMPRE es entrada

            // 1. Código largo (≥30 caracteres) que comienza con 01
            if (codigo.length >= 30 && codigo.startsWith("01")) {
                procesarCodigoLargoRecep(codigo, operacion);
            }
            // 2. Código que comienza con 01 (GTIN)
            else if (codigo.startsWith("01")) {
                procesarCodigo01Recep(codigo);
            }
            // 3. Código que comienza con 17 (Caducidad/Lote)
            else if (codigo.startsWith("17")) {
                procesarCodigo17Recep(codigo, operacion);
            }
            // 4. Código que comienza con 113 (formato propio SUMED)
            else if (codigo.startsWith("113")) {
                procesarCodigoSumedRecep(codigo, operacion);
            }
            // 5. Código simple (EAN, etc.)
            else {
                procesarCodigoSimpleRecep(codigo, operacion);
            }
        }
    });

    // ============= AUXILIARES DE MENSAJE =============
    function mostrarMensaje(texto, tipo = 'info') {
        if (!mensajeElement) return;
        mensajeElement.textContent = texto;
        mensajeElement.className = `alert alert-${tipo}`;
        mensajeElement.style.display = 'block';
        setTimeout(() => {
            if (mensajeElement.textContent === texto) {
                mensajeElement.style.display = 'none';
            }
        }, 5000);
    }

    function limpiarMensaje() {
        if (!mensajeElement) return;
        mensajeElement.textContent = '';
        mensajeElement.className = 'alert';
        mensajeElement.style.display = 'none';
    }

    // ========== FUNCIONES PARA CÓDIGOS GS1 (adaptadas de ingreso) ========== //

    function procesarCodigo01Recep(codigo) {
        if (codigo.length >= 16) { // 01 + 14 o más dígitos
            codigo01 = codigo; // guardamos TODO el bloque que empieza en 01
            if (/^\d{2}\d{14}/.test(codigo01.substring(0, 16))) {
                mostrarMensaje('Ahora escanea el código de caducidad/lote (17...)', 'info');
            } else {
                mostrarMensaje('GTIN inválido en código 01', 'danger');
                resetearGS1();
            }
        } else {
            mostrarMensaje('Código 01 incompleto (necesita al menos 16 caracteres)', 'danger');
        }
    }

    function procesarCodigo17Recep(codigo, operacion) {
        // Si ya tenemos el código 01 (se escaneó antes)
        if (codigo01) {
            if (codigo.length >= 8) { // 17 + 6 dígitos de fecha
                const fechaCaducidad = codigo.substring(2, 8); // AAMMDD
                // Aquí el lote puede venir pegado después del 17AAMMDD, o con otros AI
                // Usamos la misma idea general que en ingreso: caducidad y lote se extraen del string
                let lote = "";
                if (codigo.length > 8) {
                    lote = codigo.substring(8); // si tu formato real es distinto, aquí se ajusta
                }

                const caducidadFormateada =
                    `20${fechaCaducidad.substring(0, 2)}-${fechaCaducidad.substring(2, 4)}-${fechaCaducidad.substring(4, 6)}`;

                const codigoCompleto = codigo01 + codigo;

                // En recepción: mandamos directamente a insertar en tmp_recepcion
                enviarDatosRecepcion(codigoCompleto, lote, caducidadFormateada);

                resetearGS1();
            } else {
                mostrarMensaje('Código 17 incompleto', 'danger');
            }
        }
        // Si no se escaneó antes el 01, solo avisamos
        else {
            codigo17 = codigo;
            mostrarMensaje('Primero escanea el código de producto (01...)', 'warning');
        }
    }

    function resetearGS1() {
        codigo01 = null;
        codigo17 = null;
    }

    function procesarCodigoLargoRecep(codigo, operacion) {
        try {
            // Ejemplo: 01(14) 17(6) 10(lote...) → aquí asumo tu mismo patrón que en ingreso
            const cad = codigo.substring(18, 24);  // AAMMDD
            const lote = codigo.substring(26);     // resto como lote

            const cadYMD = `20${cad.substring(0, 2)}-${cad.substring(2, 4)}-${cad.substring(4, 6)}`;

            enviarDatosRecepcion(codigo, lote, cadYMD);
        } catch (e) {
            console.error(e);
            mostrarMensaje('Error en formato de código largo', 'danger');
        }
    }

    function procesarCodigoSumedRecep(codigo, operacion) {
        try {
            // Mismo patrón que en ingreso para códigos internos SUMED
            const codigoProducto = codigo.substring(0, 19);
            const cad = codigo.substring(21, 27);
            const lote = codigo.substring(29);
            const cadYMD = `20${cad.substring(0, 2)}-${cad.substring(2, 4)}-${cad.substring(4, 6)}`;

            enviarDatosRecepcion(codigoProducto, lote, cadYMD);
        } catch (e) {
            console.error(e);
            mostrarMensaje('Error en formato de código SUMED', 'danger');
        }
    }

    function procesarCodigoSimpleRecep(codigo, operacion) {
        // Aquí consideramos que el código simple solo identifica el producto (EAN, etc.)
        // Lote/caducidad pueden venir de la BD o capturarse manual después si quieres.
        const codigoLimpio = codigo.trim();
        if (!codigoLimpio) {
            mostrarMensaje('Código no válido', 'danger');
            return;
        }

        // Mandamos el código, y el servidor resolverá lote/caducidad
        enviarDatosRecepcion(codigoLimpio, '', '');
    }

    // ========== ENVÍO A TMP_RECEPCION (NO mueve inventario directo) ========== //

    // ================== ESTADO GLOBAL PARA LA RECEPCIÓN ==================
let ultimoCodigoRecep      = null;
let ultimoLoteRecep        = '';
let ultimaCaducidadRecep   = '';
let ultimaCantidadRecep    = 1;
let ultimaReferenciaRecep  = '';

// Elemento de mensajes (agrega en tu HTML un <div id="msg_recep"></div>)
const msgRecepEl = document.getElementById('msg_recep');

function mostrarMensajeRecep(texto, tipo = 'info') {
    if (!msgRecepEl) {
        alert(texto);
        return;
    }
    msgRecepEl.textContent = texto;
    msgRecepEl.className = `alert alert-${tipo}`;
    msgRecepEl.style.display = 'block';
    setTimeout(() => {
        if (msgRecepEl.textContent === texto) {
            msgRecepEl.style.display = 'none';
        }
    }, 5000);
}

// ================== ENVÍO PRINCIPAL A PHP (1a FASE) ==================
function enviarDatosRecepcion(codigo, lote, caducidad) {
    const almacenEl    = document.getElementById('id_almacen');
    const refInput     = document.getElementById('referencia_recep');
    const cantInput    = document.getElementById('cantidad_recep');

    if (!almacenEl || !almacenEl.value) {
        mostrarMensajeRecep('Debes seleccionar un almacén.', 'warning');
        return;
    }

    const id_almacen = almacenEl.value;
    const referencia = refInput && refInput.value ? refInput.value.trim() : '';
    const cantidad   = cantInput && cantInput.value ? parseInt(cantInput.value, 10) : 1;

    if (!codigo) {
        mostrarMensajeRecep('Código vacío.', 'danger');
        return;
    }
    if (!cantidad || cantidad <= 0) {
        mostrarMensajeRecep('La cantidad debe ser mayor a cero.', 'warning');
        return;
    }

    const data = new URLSearchParams();
    data.append('codigo', codigo);
    data.append('lote', lote || '');
    data.append('caducidad', caducidad || '');
    data.append('referencia', referencia);
    data.append('cantidad', cantidad);
    data.append('id_almacen', id_almacen);

    fetch("../../ajax/agregar_item_recepcion_gs1.php", {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: data.toString()
    })
    .then(res => res.text())
    .then(resp => {
        const r = resp.trim();
        if (r === "OK") {
            // Limpiar campos y recargar tabla
            const codigoInput = document.getElementById('codigo_recep');
            if (codigoInput) codigoInput.value = '';
            if (cantInput)    cantInput.value = 1;
            if (refInput)     refInput.value = '';

            if (window.jQuery) {
                $("#resultado_recepcion").load("../../ajax/tabla_tmp_recepcion.php");
            }
            mostrarMensajeRecep('Producto agregado a la recepción.', 'success');
        } else if (r === "NECESITA_REFERENCIA") {
            // Guardamos datos para segunda fase
            ultimoCodigoRecep     = codigo;
            ultimoLoteRecep       = lote || '';
            ultimaCaducidadRecep  = caducidad || '';
            ultimaCantidadRecep   = cantidad;
            ultimaReferenciaRecep = referencia;

            abrirModalNuevoProductoRecep();
        } else {
            mostrarMensajeRecep(r, 'danger');
        }
    })
    .catch(err => {
        console.error(err);
        mostrarMensajeRecep('Error al procesar el código en recepción.', 'danger');
    });
}

// ============= MODAL "NUEVO PRODUCTO" PARA RECEPCIÓN (2a FASE) =============
function abrirModalNuevoProductoRecep() {
    const modalEl = document.getElementById('modalNuevoProducto');
    if (!modalEl) {
        alert('No se encontró el modal "modalNuevoProducto". Inclúyelo en la vista de recepción.');
        return;
    }

    const refInput     = document.getElementById('modalReferencia');
    const descInput    = document.getElementById('modalDescripcion');
    const costoInput   = document.getElementById('modalCosto');
    const precioInput  = document.getElementById('modalPrecio');
    const exentoChk    = document.getElementById('modalExentoIva'); // opcional
    const btnGuardar   = document.getElementById('btnGuardarProducto');

    if (!refInput || !descInput || !costoInput || !precioInput || !btnGuardar) {
        alert('Faltan campos dentro del modal (Referencia, Descripción, Costo, Precio o botón Guardar).');
        return;
    }

    // Limpiar / precargar
    refInput.value    = ultimaReferenciaRecep || '';
    descInput.value   = '';
    costoInput.value  = '';
    precioInput.value = '';
    if (exentoChk) exentoChk.checked = false;

    const modal = new bootstrap.Modal(modalEl);
    modal.show();

    // Importante: asignamos handler cada vez (sobrescribe el anterior)
    btnGuardar.onclick = function () {
        const referencia  = refInput.value.trim();
        const descripcion = descInput.value.trim();
        const costo       = costoInput.value.trim();
        const precio      = precioInput.value.trim();
        const exentoIva   = exentoChk && exentoChk.checked ? 1 : 0;

        if (!referencia || !descripcion || costo === '' || precio === '') {
            alert('Referencia, descripción, costo y precio son obligatorios.');
            return;
        }

        const almacenEl = document.getElementById('id_almacen');
        if (!almacenEl || !almacenEl.value) {
            alert('Debes seleccionar un almacén.');
            return;
        }
        const id_almacen = almacenEl.value;

        const data2 = new URLSearchParams();
        data2.append('codigo',     ultimoCodigoRecep);
        data2.append('lote',       ultimoLoteRecep);
        data2.append('caducidad',  ultimaCaducidadRecep);
        data2.append('referencia', referencia);
        data2.append('cantidad',   ultimaCantidadRecep);
        data2.append('id_almacen', id_almacen);
        data2.append('descripcion', descripcion);
        data2.append('costo',       costo);
        data2.append('precio',      precio);
        data2.append('exento_iva',  exentoIva);

        fetch("../../ajax/agregar_item_recepcion_gs1.php", {
            method: "POST",
            headers: { "Content-Type": "application/x-www-form-urlencoded" },
            body: data2.toString()
        })
        .then(res => res.text())
        .then(resp => {
            const r = resp.trim();
            if (r === "OK") {
                modal.hide();

                // Recargar tabla de recepción
                if (window.jQuery) {
                    $("#resultado_recepcion").load("../../ajax/tabla_tmp_recepcion.php");
                }

                mostrarMensajeRecep('Producto creado y agregado a la recepción.', 'success');

                // Limpiar estado
                ultimoCodigoRecep    = null;
                ultimoLoteRecep      = '';
                ultimaCaducidadRecep = '';
                ultimaCantidadRecep  = 1;
                ultimaReferenciaRecep= '';
            } else {
                mostrarMensajeRecep(r, 'danger');
            }
        })
        .catch(err => {
            console.error(err);
            mostrarMensajeRecep('Error al guardar el nuevo producto.', 'danger');
        });
    };
}

} 
// ================== FUNCIONES EXISTENTES EN RECEPCIÓN ==================