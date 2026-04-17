window.onload = () => {
    // Variables de estado
    let operacion = 'sumar';
    let codigo01 = null;
    let codigo17 = null;
    let cantidadPendienteAjuste = null;
    const input = document.getElementById('codigo');
    const mensajeElement = document.getElementById('mensaje');
    const modoElement = document.getElementById('modo');
    // Configurar operación predeterminada
    document.querySelector('input[value="sumar"]').checked = true;
    document.getElementById('modo_operacion').textContent = 'Sumando';
    document.getElementById('operacion_modo').style.backgroundColor = 'green';
    
    // Manejar cambios entre sumar/restar

    document.querySelectorAll('input[name="operacion"]').forEach(el => {
        el.addEventListener('change', () => {
          operacion = document.querySelector('input[name="operacion"]:checked').value;
          switch (operacion) {
            case 'sumar':
              tipo_operacion = 'success';
              document.getElementById('modo_operacion').textContent = 'Estas Sumando';
              document.getElementById('operacion_modo').style.backgroundColor = 'green';
              break;
            case 'restar':
              tipo_operacion = 'danger';
              document.getElementById('modo_operacion').textContent = 'Estas Restando';
              document.getElementById('operacion_modo').style.backgroundColor = 'red';
              break;
            case 'ajuste':
              tipo_operacion = 'warning';
              document.getElementById('modo_operacion').textContent = 'Estas Ajustando';
              document.getElementById('operacion_modo').style.backgroundColor = '#ffc107';
              break;

          }
         mostrarMensaje(`Modo: ${operacion.toUpperCase()}`, tipo_operacion); 

        });
      });
      
    // Evento principal para escanear códigos
    input.addEventListener('keypress', function(e) {
      if (e.key === 'Enter') {
        const codigo = input.value.trim();
        input.value = '';
        limpiarMensaje();

       
        // 1. Código largo (≥30 caracteres)
        if (codigo.length >= 30 && codigo.startsWith("01")) {
          procesarCodigoLargo(codigo, operacion);
        }
        // 2. Código que comienza con 01 (GTIN)
        else if (codigo.startsWith("01")) {
          procesarCodigo01(codigo);
        }
        // 3. Código que comienza con 17 (Caducidad/Lote)
        else if (codigo.startsWith("17")) {
          procesarCodigo17(codigo, operacion);
        }
        //4. Código que comienza con 113 (Lote)

        else if (codigo.startsWith("113")) {
          procesarCodigoSumed(codigo, operacion);
        } 
        
        // 5. Código simple
        else {
          procesarCodigoSimple(codigo, operacion);
          
        }
      }
    });
   //=============FUNCION EXTRAER LOTES==================//

    function extraerLoteDesde(codigo, index10) {
  const posiblesAIs = [
    '00','01','02','10','11','12','13','14','15','16','17','20','21','22','23','30',
    '31','32','33','34','35','36','37','90','91','92','93','94','95','96','97','98','99'
  ];
  const inicioLote = index10 + 2;
  const data = codigo.substring(inicioLote);

  for (let i = 1; i < data.length - 1; i++) {
    const posibleAI = data.substring(i, i + 2);
    const hayMas = (i + 2) < data.length;

    if (posiblesAIs.includes(posibleAI) && hayMas) {
      return data.substring(0, i); // Corta antes del nuevo AI
    }
  }

  return data; // No hay otro AI válido → todo es lote
}
    // ========== FUNCIONES PARA CÓDIGOS GS1 ========== //
  
    function procesarCodigo01(codigo) {
      if (codigo.length >= 16) { // 01 + 14 dígitos GTIN
       // codigo01 = codigo.substring(2, 16); // Extraemos solo el GTIN
        codigo01=codigo;
        if (/^\d{16}$/.test(codigo01)) {
          mostrarMensaje('Ahora escanea el código de caducidad (17...)', 'info');
        } else {
          mostrarMensaje('GTIN inválido: debe tener 16 dígitos', 'danger');
          resetearGS1();
        }
      } else {
        mostrarMensaje('Código 01 incompleto (necesita 16 caracteres)', 'danger');
      }
    }
  
    function procesarCodigo17(codigo, operacion) {
      // Si ya tenemos el código 01, procesamos completo
      if (codigo01) {
          if (codigo.length >= 8) { // 17 + 6 dígitos fecha
              const fechaCaducidad = codigo.substring(2, 8);
              const lote = codigo.substring(10); // CORRECCIÓN: Cambiado de 10 a 8
              codigoCompleto=codigo01+codigo;
              if (/^\d{6}$/.test(fechaCaducidad)) {
                  const caducidadFormateada = `20${fechaCaducidad.substring(0, 2)}-${fechaCaducidad.substring(2, 4)}-${fechaCaducidad.substring(4, 6)}`;
                  if (operacion === 'ajuste') {
                      mostrarModalCantidad(codigoCompleto, lote, caducidadFormateada, '', operacion);
                    } else {
                      enviarDatos(codigoCompleto, lote, caducidadFormateada, '', operacion, '');
                    }
                  resetearGS1();
              } else {
                  mostrarMensaje('Fecha inválida en código 17 (debe ser AAMMDD)', 'danger');
              }
          } else {
              mostrarMensaje('Código 17 incompleto', 'danger');
          }
      } 
      // Si no tenemos el código 01, lo guardamos y pedimos el 01
      else {
          codigo17 = codigo;
          mostrarMensaje('Primero escanea el código de producto (01...)', 'warning');
      }
  }
  
    function resetearGS1() {
      codigo01 = null;
      codigo17 = null;
    }
  
    // ========== FUNCIONES PARA OTROS CÓDIGOS ========== //
  
    function procesarCodigoLargo(codigo, operacion) {
      try {
       // const codigoProducto = codigo.substring(0, 16);
        const cad = codigo.substring(18, 24);
        const lote = codigo.substring(26);

        const cadYMD = `20${cad.substring(0, 2)}-${cad.substring(2, 4)}-${cad.substring(4, 6)}`;
        if (operacion === 'ajuste') {
            mostrarModalCantidad(codigo, lote, cadYMD, '', operacion);
          } else {
            enviarDatos(codigo, lote, cadYMD, '', operacion, '');
          }
        
      } catch (e) {
        console.error(e);
        mostrarMensaje('Error en formato de código largo', 'danger');
      }
    }


    function procesarCodigoSumed(codigo, operacion) {
     try {
        const codigoProducto = codigo.substring(0, 19);
        const cad = codigo.substring(21,27);
        const lote = codigo.substring(29);
        const cadYMD = `20${cad.substring(0, 2)}-${cad.substring(2, 4)}-${cad.substring(4, 6)}`;
      console.log(codigoProducto, lote, cadYMD, '', operacion);
       if (operacion === 'ajuste') {
        mostrarModalCantidad(codigoProducto, lote, cadYMD, '', operacion);
      } else {
        enviarDatos(codigoProducto, lote, cadYMD, '', operacion);
      }

      } catch (e) {
        mostrarMensaje('Error en formato de código sumed', 'danger');
      }
    }

function procesarCodigoSimple(codigo, operacion) {
  // Normaliza a 16 dígitos máximo (ajusta si tu "código simple" usa otra longitud)
  const codigoLimpio = codigo.replace(/\D/g, '').substring(0, 16);

  if (!codigoLimpio) {
    mostrarMensaje('Código no válido', 'danger');
    return;
  }

  fetch(`../../ajax/verificar_codigo.php?codigo=${codigoLimpio}`)
    .then(res => res.text())
    .then(resp => {
      // Caso 1: NO EXISTE -> mostrar modalRegistroLote
      if (resp !== 'existe') {
        mostrarModalRegistroLote(codigoLimpio, ({ lote, caducidad, referencia, descripcion, costo, precio }) => {
          if (operacion === 'ajuste') {
            // Primero pedimos cantidad y luego enviamos todo junto (mostrarModalCantidad ya llama a enviarDatos)
            mostrarModalCantidad(
              codigoLimpio, lote, caducidad, referencia, operacion, descripcion, costo, precio
            );
          } else {
            // Alta normal del nuevo producto con lote
            enviarDatos(
              codigoLimpio, lote, caducidad, referencia, operacion, descripcion, costo, precio
            );
          }
        });
        return;
      }

      // Caso 2: SÍ EXISTE -> mostrar modalLoteExistente
      mostrarModalLoteExistente(codigoLimpio, ({ lote, caducidad, referencia }) => {
        if (operacion === 'ajuste') {
          // Primero cantidad y luego enviar
          mostrarModalCantidad(
            codigoLimpio, lote, caducidad, referencia, operacion
          );
        } else {
          // Solo enviar movimiento (sumar/restar) sin pedir cantidad adicional
          enviarDatos(
            codigoLimpio, lote, caducidad, referencia, operacion
          );
        }
      });
    })
    .catch(() => {
      mostrarMensaje('Error al verificar código', 'danger');
    });
}
     
  
    // ========== FUNCIONES AUXILIARES ========== //
    
function enviarDatos(codigo, lote, caducidad, referencia, operacion, descripcion = '', costo = '', precio = '',cantidad = '') {
  
  const almacen = document.getElementById('id_almacen').value
  const data = new URLSearchParams();

  if (almacen === "" || almacen === null) {
        Swal.fire({
            icon: 'warning',
            title: 'Campo requerido',
            text: 'Debes seleccionar una opción en el campo Almacén.',
        });
        return false;
    }


  data.append('codigo', codigo);
  data.append('lote', lote);
  data.append('caducidad', caducidad);
  data.append('referencia', referencia);
  data.append('operacion', operacion);
  data.append('almacen', almacen);

  if (descripcion) data.append('descripcion', descripcion);
  if (costo) data.append('costo', costo);
  if (precio) data.append('precio', precio);
   if (cantidad !== '' && cantidad !== null && cantidad !== undefined) {
    data.append('cantidad', cantidad);
  }

  fetch("../../ajax/procesar.php", {
    method: "POST",
    headers: { "Content-Type": "application/x-www-form-urlencoded" },
    body: data
  })
   .then(res => res.text())
      .then(data => {
          if (data === "NECESITA_REFERENCIA") {
            
  mostrarModalNuevoProducto(codigo, lote, caducidad, operacion);

          } else {
              mostrarMensaje(data, 'success');
              cargarTabla();
              cantidadPendienteAjuste = null;
          }
      })
      .catch(err => {
          mostrarMensaje('Error al procesar el código', 'danger');
      });
  }

   function mostrarModalNuevoProducto(codigo, lote, caducidad, operacion) {
    
  //document.getElementById('modalNuevoProducto').style.display = 'block';
  const modal = new bootstrap.Modal(document.getElementById('modalNuevoProducto'));
  modal.show();

  document.getElementById('btnGuardarProducto').onclick = function () {
    const referencia = document.getElementById('modalReferencia').value.trim();
    const descripcion = document.getElementById('modalDescripcion').value.trim();
    const costo = document.getElementById('modalCosto').value.trim();
    const precio = document.getElementById('modalPrecio').value.trim();

    if (!referencia || !descripcion || !costo || !precio) {
      alert("Todos los campos son obligatorios.");
      return;
    } 

    cerrarModal(modal);
 enviarDatos(codigo, lote, caducidad, referencia, operacion,descripcion, costo, precio, cantidadPendienteAjuste);
 cantidadPendienteAjuste = null;
  };
}

    function mostrarMensaje(texto, tipo = 'info') {
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
      mensajeElement.textContent = '';
      mensajeElement.className = 'alert';
    }

// --- Modal de cantidad (ajuste) ---
function mostrarModalCantidad(codigo, lote, caducidad, referencia, operacion, descripcion = '', costo = '', precio = '') {
const modal1 = new bootstrap.Modal(document.getElementById('modalCantidad'));
  //const modalEl = document.getElementById('modalCantidad');
   
 
  // Limpia estado visual previo
  const input = document.getElementById('modalCantidadInput');
  const errEl = document.getElementById('modalCantidadError');
  input.value = '';
  errEl.textContent = '';
  errEl.style.display = 'none';

  // Mostrar
 modal1.show();

  // Handlers de botones (mismo patrón que modalNuevoProducto)
  document.getElementById('btnGuardarCantidad').onclick = function () {
    const val = (input.value || '').trim();
    if (!/^[+-]?\d+$/.test(val)) {
      errEl.textContent = 'Ingresa un entero válido con signo (ej. +5 o -3)';
      errEl.style.display = 'block';
      return;
    }
    const cantidad = parseInt(val, 10);

      // NUEVO: conservar la cantidad para el caso de producto nuevo
  cantidadPendienteAjuste = cantidad;

    cerrarModal(modal1);
    // Llamar a enviarDatos con cantidad (solo en ajuste)
    enviarDatos(codigo, lote, caducidad, referencia, operacion, descripcion, costo, precio, cantidad);
  };

  // Si cancelan, simplemente cerrar (no enviamos nada)
  document.getElementById('btnCancelarCantidad').onclick = function () {
    cerrarModal(modal1);
  };
}

///////////////****modal registro lote  ///////// */

function mostrarModalRegistroLote(codigo, onSave) {
  const modalEl = document.getElementById('modalRegistroLote');
  const modal = new bootstrap.Modal(modalEl);
  modal.show();

  const $ = sel => modalEl.querySelector(sel);

  $('#btnGuardarRegistroLote').onclick = function () {
    const lote = ($('#regLote').value || '').trim();
    const caducidad = ($('#regCad').value || '').trim();
    const referencia = ($('#regReferencia').value || '').trim();
    const descripcion = ($('#regDescripcion').value || '').trim();
    const costo = ($('#regCosto').value || '').trim();
    const precio = ($('#regPrecio').value || '').trim();

    if (!lote || !/^\d{4}-\d{2}-\d{2}$/.test(caducidad) || !referencia || !descripcion || !costo || !precio) {
      alert('Completa todos los campos. Fecha en formato yyyy-mm-dd.');
      return;
    }

    modal.hide();
    onSave({ lote, caducidad, referencia, descripcion, costo, precio });
  };

  $('#btnCancelarRegistroLote').onclick = function () {
    modal.hide();
  };
}
////////////////////////////lote existente ////////
function mostrarModalLoteExistente(codigo, onSave) {
  const modalEl = document.getElementById('modalLoteExistente');
  const modal = new bootstrap.Modal(modalEl);
  modal.show();

  const $ = sel => modalEl.querySelector(sel);

  $('#btnGuardarLoteExistente').onclick = function () {
    // Adapta estos selectores a tu UI real (ej: un <select> de lotes o inputs ya llenos)
    const lote = ($('#existLote').value || '').trim();
    const caducidad = ($('#existCad').value || '').trim();
    const referencia = ($('#existReferencia')?.value || '').trim();

    if (!lote || !/^\d{4}-\d{2}-\d{2}$/.test(caducidad)) {
      alert('Selecciona un lote y una caducidad válidos (yyyy-mm-dd).');
      return;
    }

    modal.hide();
    onSave({ lote, caducidad, referencia });
  };

  $('#btnCancelarLoteExistente').onclick = function () {
    modal.hide();
  };
}
// Envoltura que decide si pide cantidad (ajuste) y luego llama a enviarDatos




    // function getCantidadAjuste() {
    //     let cantidad;
    //     do {
    //       cantidad = prompt("Ingrese la cantidad para ajustar (ej: +5 para sumar, -3 para restar):");
          
    //       if (cantidad === null) return null; // Usuario canceló
          
    //       if (!/^[+-]?\d+$/.test(cantidad)) {
    //         alert("Por favor ingrese un número entero válido con + o - (ej: +5, -2)");
    //         cantidad = undefined;
    //       }
    //     } while (cantidad === undefined);
        
    //     return parseInt(cantidad);
    //   }
  
    function cargarTabla() {
      fetch("../../ajax/obtener_registros.php")
        .then(res => res.json())
        .then(data => {
          const tabla = document.querySelector("#tabla");
          
          if (window.dataTable) {
            window.dataTable.destroy();
            tabla.innerHTML = `
              <thead>
                <tr>
                 
                  <th>Referencia</th>
                  <th>Descripción</th>
                  <th>Lote</th>
                  <th>Caducidad</th>
                  <th>Costo</th>
                  <th>Existencias</th>
                  <th>Almacen</th>
                </tr>
              </thead>
              <tbody></tbody>
            `;
          }
  
          const tbody = tabla.querySelector("tbody");
          data.forEach(row => {
            tbody.innerHTML += `
              <tr>
               
                <td>${row.referencia}</td>
                <td>${row.descripcion}</td>
                <td>${row.lote}</td>
                <td>${row.caducidad}</td>
                <td>${Number(row.costo).toLocaleString('es-MX', { style: 'currency', currency: 'MXN' })}</td>
                <td>${row.existencias}</td>
                <td>${row.id_almacen}</td>
               
              </tr>
            `;
          });
          window.dataTable = new DataTable(tabla, {
            perPage: 10,
            searchable: true,
            labels: {
              placeholder: "Buscar...",
              perPage: "{select} registros por página",
              noRows: "No se encontraron registros"
            }
          });
  
          document.querySelectorAll(".eliminar").forEach(btn => {
            btn.addEventListener("click", function() {
              if (confirm("¿Eliminar este producto?")) {
                fetch(`eliminar_producto.php?id=${this.dataset.id}`, {
                  method: "DELETE"
                }).then(() => cargarTabla());
              }
            });
          });
        });
    }
  
    // Cargar tabla al inicio
    cargarTabla();
  };
 

function cerrarModal(modal) {
 // document.getElementById('modalNuevoProducto').style.display = 'none';

modal.hide();
}
