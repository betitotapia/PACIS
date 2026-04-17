<?php
session_start();
date_default_timezone_set('America/Mexico_City');
if (!isset($_SESSION['user_login_status']) AND $_SESSION['user_login_status'] != 1) {
	header("location: ../login");
	exit;
	}
	$active_nueva="active";
	$active_productos="";
	$active_borrador="";
	$active_lista_productos="";
	$active_remisiones="";
	$active_vehiculos="";
	$active_cancel="";
	$active_almacenes='';
	$active_usuarios="";
    $active_terceros="";
    $active_provedores='';
  $active_recepciones="";
	
require_once ("../../config/db.php");//Contiene las variables de configuracion para conectar a la base de datos
require_once ("../../config/conexion.php");//Contiene funcion que conecta a la base de datos


if (isset($_GET['id']))//codigo elimina un elemento del array
{
$id_tmp=intval($_GET['id']);
$cantidad = intval($_GET['cantidad']);
$producto = intval($_GET['producto']);
$delete=mysqli_query($con, "DELETE FROM detalle_factura WHERE id_detalle='".$id_tmp."'");
$sql_descuento=mysqli_query($con, "SELECT * FROM products WHERE id_producto='".$producto."'");
$row_descuento=mysqli_fetch_array($sql_descuento);
$existencias=$row_descuento['existencias']+$cantidad;
$update_existencias=mysqli_query($con, "UPDATE products SET existencias='".$existencias."' WHERE id_producto='".$producto."'");
if ($delete && $update_existencias){

echo"<script>console.log('se borro el id: ".$id_tmp."');</script>";

}
}


if (isset($_GET['id_factura']))
	{
		$id_factura=intval($_GET['id_factura']);
		$sql_factura=mysqli_query($con,"select * from facturas where  id_factura='".$id_factura."'");
		$count=mysqli_num_rows($sql_factura);
		if ($count==1)
		{
				$rw_factura=mysqli_fetch_array($sql_factura);
				$id_vendedor = $rw_factura['id_vendedor'] ?? "";
                $fecha_factura = !empty($rw_factura['fecha_factura']) ? date("d/m/Y", strtotime($rw_factura['fecha_factura'])) : "";
                $estado_factura = $rw_factura['estado_factura'] ?? "";
                $nueva_remision = $rw_factura['numero_factura'] ?? "";
                $compra = $rw_factura['compra'] ?? "";
                $cotizacion = $rw_factura['cotizacion'] ?? "";
                $id_cliente = $rw_factura['id_cliente'] ?? "";
                $doctor = $rw_factura['doctor'] ?? "";
                $paciente = $rw_factura['paciente'] ?? "";
                $material = $rw_factura['material'] ?? "";
                $pago = $rw_factura['pago'] ?? "";
                $d_factura = $rw_factura['d_factura'] ?? "";
                $observaciones = $rw_factura['observaciones'] ?? "";
				$_SESSION['id_factura']=$id_factura;
				$_SESSION['numero_factura']=$nueva_remision;
		}	
		else
		{
			//header("location: index.php");
			exit;	
		}
	} 
	else{
	//	header("location: facturas.php");
		exit;
	}
?>

<!DOCTYPE html>
<html lang="en">
<?php
include '../header.php';
?>

<body class="layout-fixed sidebar-expand-lg sidebar-mini sidebar-collapse sidebar-dark-info  bg-body-tertiary">
    <!--begin::App Wrapper-->
    <div class="app-wrapper">
        <!--begin::Header-->
        <?php
include '../navbar.php';
include '../aside_menu.php';
    ?>
        <main class="app-main">
            <!--begin::App Content Header-->
            <div class="app-content-header">
                <div class="container-fluid">
                    <div class="btn-group pull-right">

                        <div class="card card-info card-outline mb-4">
                            <div class="card-header">
                                <div class="card-title">NUEVA REMISION <b><?php echo $nueva_remision ?></b></div>
                            </div>
                            <?php
         $sql_remision=mysqli_query($con,"SELECT * FROM facturas WHERE numero_factura = '$nueva_remision' AND id_vendedor = '$id_vendedor'");
            $rw_remision=mysqli_fetch_array($sql_remision);

            if ($id_cliente!='') {
                $id_cliente = $rw_remision['id_cliente'];
            $sql_cliente=mysqli_query($con,"SELECT * FROM clientes WHERE id_cliente = $id_cliente");
            $rw_cliente=mysqli_fetch_array($sql_cliente);
           
            $nombre_cliente = isset($rw_cliente['nombre_cliente']) ? $rw_cliente['nombre_cliente'] : "";
            $calle          = isset($rw_cliente['calle']) ? $rw_cliente['calle'] : "";
            $colonia        = isset($rw_cliente['colonia']) ? $rw_cliente['colonia'] : "";
            $num_ext        = isset($rw_cliente['num_ext']) ? $rw_cliente['num_ext'] : "";
            $num_int        = isset($rw_cliente['num_int']) ? $rw_cliente['num_int'] : "";
            $rfc            = isset($rw_cliente['rfc']) ? $rw_cliente['rfc'] : "";
            $telefono_cliente = isset($rw_cliente['telefono']) ? $rw_cliente['telefono'] : "";
            $email_cliente  = isset($rw_cliente['email']) ? $rw_cliente['email'] : "";
            } 
         ?>

                            <form onkeydown="return event.key != 'Enter';" class="form-horizontal needs-validation"
                                id="datos_remision">
                                <div class="card-body">
                                    <!--begin::Row-->
                                    <div class="row g-3">
                                        <!--begin::Col-->
                                        <div class="col-12">
                                            <div class="form-group row">
                                                <label for="nombre" class="col-md-1 control-label">Cliente</label>
                                                <div class="col-md-5">
                                                    <input type="text" class="form-control input-sm" name="nombre"
                                                        id="nombre_cliente" placeholder="Selecciona un cliente"
                                                        value="<?php echo $nombre_cliente ?>" required>
                                                    <input id="id_cliente" type='hidden' name='cliente_id'
                                                        value="<?php echo $rw_remision['id_cliente'] ?>">
                                                </div>
                                                <!---aqui_el hospital--->
                                                  <label for="hospital" class="col-md-1 control-label" style="font-size: 14px">Dependencia</label>
                                                <div class="col-md-5">
                                                    <input type="text" class="form-control input-sm" name="hospital_f"
                                                        id="hospital" placeholder="Hospital"
                                                        value="<?php echo $rw_remision['hospital'] ?> ">
                                                </div> 
                                            </div>
                                            <br>
                                            <div class="form-group row">
                                                <label for="rfc" class="col-sm-1 control-label">RFC</label>
                                                <div class="col-md-2">
                                                    <input type="text" name="rfc" class="form-control input-sm"
                                                        id="rfc_cliente" placeholder="RFC"
                                                        value="<?php echo $rfc?>" readonly>
                                                </div>
                                                <label for="telefono_cliente"
                                                    class="col-md-1 control-label">Teléfono</label>
                                                <div class="col-md-2">
                                                    <input type="text" name="telefono_cliente"
                                                        class="form-control input-sm" id="telefono_cliente"
                                                        placeholder="Teléfono"
                                                        value="<?php echo $telefono_cliente ?>" readonly>
                                                </div>
                                                <label for="mail" class="col-md-1 control-label">Email</label>
                                                <div class="col-md-2">
                                                    <input type="text" class="form-control input-sm" name="mail"
                                                        id="mail" placeholder="Email"
                                                        value="<?php echo $email_cliente ?>" readonly>
                                                </div>
                                                <label for="tel2" class="col-md-1 control-label">Fecha</label>
                                                <div class="col-md-2">
                                                    <input type="text" class="form-control input-sm" name="fecha_f"
                                                        id="fecha" value="<?php  
                                                        $fecha_original = $rw_remision['fecha_factura'];
                                                        $nueva_fecha = date("d/m/Y", strtotime($fecha_original));
                                                        echo $nueva_fecha; ?>" readonly>
                                                </div>
                                            </div>
                                            <br>
                                            <div class="form-group row">
                                                <label for="compra" class="col-md-1 control-label">Orden de
                                                    compra</label>
                                                <div class="col-md-2">
                                                    <input type="text" class="form-control input-sm" name="compra_f"
                                                        id="compra" placeholder="Orden de compra:"
                                                        value="<?php echo $rw_remision['compra'] ?>">
                                                </div>
                                                <label for="cotizacion" class="col-md-1 control-label">Cotización
                                                    no.</label>
                                                <div class="col-md-2">
                                                    <input type="text" class="form-control input-sm" name="cotizacion_f"
                                                        id="cotizacion" placeholder="Cotización"
                                                        value="<?php echo $rw_remision['cotizacion'] ?>" required>
                                                </div>

                                                <label for="doctor" class="col-md-1 control-label">Doctor</label>
                                                <div class="col-md-2">
                                                    <input type="text" class="form-control input-sm" name="doctor_f"
                                                        id="doctor" placeholder="Nombre del Doctor"
                                                        value="<?php echo $rw_remision['doctor'] ?>" required>
                                                </div>

                                                <label for="paciente" class="col-md-1 control-label">Paciente</label>
                                                <div class="col-md-2">
                                                    <input type="text" class="form-control input-sm" name="paciente_f"
                                                        id="paciente" placeholder="Paciente"
                                                        value="<?php echo $rw_remision['paciente'] ?> ">
                                                </div>
                                            </div>
                                            <br>
                                            <div class="form-group row">



                                                <label for="material" class="col-md-1 control-label">Material
                                                    de:</label>
                                                <div class="col-sm-2">
                                                    <select class='form-control input-sm ' id="material"
                                                        name="material_f">
                                                        <option selected value="<?php echo $rw_remision['material'] ?>">
                                                            <?php echo $rw_remision['material'] ?></option>
                                                        <option value="Consignación">Consignación</option>
                                                        <option value="Donación">Donación</option>
                                                        <option value="Venta">Venta</option>
                                                        <option value="Reposición de consigna">Reposición de consigna
                                                        </option>
                                                        <option value="Prestamo">Prestamo</option>

                                                        </option>
                                                    </select>
                                                </div>
                                                <label for="pago" class="col-sm-1 control-label">Condiciones de
                                                    pago</label>
                                                <div class="col-md-2">
                                                    <select class='form-control input-sm ' id="pago" name="pago_f">
                                                        <option selected value="<?php echo $rw_remision['pago'] ?>">
                                                            <?php echo $rw_remision['pago'] ?></option>
                                                        <option value="Efectivo">Efectivo</option>
                                                        <option value="Transferencia">Transferencia</option>
                                                        <option value="Crédito">Crédito</option>
                                                        </option>
                                                    </select>
                                                </div>

                                                <label for="" class="col-sm-1 control-label">Factura</label>
                                                <div class="col-md-2">
                                                    <select class='form-control input-sm' id="d_factura"
                                                        name="d_factura_f">
                                                        <option selected
                                                            value="<?php echo $rw_remision['d_factura'] ?>">
                                                            <?php echo $rw_remision['d_factura'] ?></option>
                                                        <option value="SI">SI</option>
                                                        <option value="PUBLICO EN GENERAL">PUBLICO EN GENERAL</option>
                                                        </option>
                                                    </select>
                                                </div>
                                               <label for="proveedor" class="col-md-1 control-label">No. Proveedor</label>
                                                <div class="col-md-2">
                                                    <input type="text" class="form-control input-sm" name="proveedor_f"
                                                        id="proveedor" placeholder="No. de proveedor"
                                                        value="<?php echo $rw_remision['no_proveedor'] ?>">
                                                </div>
                                            </div>
                                            <br>
                                            <div class="form-group row">
                                                <label for="vendedor" class="col-md-1 control-label">Vendedor</label>
                                                <div class="col-md-2">
                                                    <?php
                                              $sql_vendedor2=mysqli_query($con,"SELECT * FROM users WHERE user_id = $id_vendedor order by nombre");
                                              $rw = mysqli_fetch_array($sql_vendedor2);	
                                              $id_vendedor=$rw["user_id"];
                                              $nombre_vendedor=$rw["nombre"];
                                              $letra = $rw['letra'];
                                              ?>
                                                                                <input type="hidden" class="form-control input-sm" name="vendedor"
                                                        id="id_vendedor" name="vendedor_id"
                                                        value="<?php echo $id_vendedor?>">
                                                    <input type="text" class="form-control input-sm" name="id_vendedor"
                                                        id="" value="<?php echo $nombre_vendedor?>" readonly>
                                                </div>
                                                <label for="letra" class="col-md-1 control-label">LETRA</label>
                                                <div class="col-md-2" id="letras">
                                                    <input type='text' class='form-control input-sm' name='letra_ventas'
                                                        id='letra_ventas' readonly value="<?php echo $letra?>">
                                                </div>

                                                <label for="letra" class="col-md-2 control-label" >Observaciones</label>
                                                <div class="col-md-3" id="letras">
                                                    <textarea name="observaciones_f" id="observaciones" rows="3"
                                                        cols="50"></textarea>
                                                    <input type="hidden" name="numero_factura" id="numero_factura"
                                                        value="<?php echo $nueva_remision?>">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                       <button type="button"
                                                class="btn btn-block btn-LG bg_icons-highpurple botones_cel"
                                                data-bs-toggle="modal" data-bs-target="#buscar_productos"
                                                id="btnAgregarProductos" onclick="load(1);">
                                                Agregar Productos <i class="bi bi-cart-plus-fill"></i>
                                       </button>
                                        <button type="submit" class=" btn btn-block btn-LG g-label-success botones_cel">
                                            Guardar <i class="bi bi-floppy2"></i> </button>
                                        <button type="button" class="btn btn-block btn-LG bg_icons-orange botones_cel"
                                            onclick="cerrar_remision()">Finalizar Remision <i
                                                class="bi bi-x-circle"></i></button>


                                    </div>
                                </div>
                        </div>
                        </form>

                        <!---------------------modal------------------->

                        <!--------------------- end modal------------------->

                    </div>
                </div>
            </div>
            <!-- === ALTA INLINE DE PRODUCTOS (pegado sobre #resultados) 
<div class="row g-2 align-items-end mb-2">
  <div class="col-12 col-md-6 position-relative">
    <label class="form-label small">Producto</label>
    <input id="findProd" type="search" class="form-control" placeholder="Buscar por clave, descripción o SKU…">
    <div id="dropProds" class="dropdown-menu w-100"></div>
  </div>
  <div class="col-6 col-md-2">
    <label class="form-label small">Cantidad</label>
    <input id="qty" type="number" min="1" value="1" class="form-control">
  </div>
  <div class="col-6 col-md-2">
    <label class="form-label small">Precio</label>
    <input id="price" type="number" min="0" step="0.01" class="form-control" placeholder="$">
  </div>
  <div class="col-12 col-md-2 d-grid">
    <button id="addLine" class="btn btn-primary mt-3 mt-md-0">Agregar</button>
  </div>
</div>

 Si el producto tiene múltiples lotes/caducidades/almacenes 
<div id="loteRow" class="row g-2 align-items-end mb-3 d-none">
  <div class="col-12 col-md-4">
    <label class="form-label small">Lote</label>
    <select id="lote" class="form-select"></select>
  </div>
  <div class="col-6 col-md-4">
    <label class="form-label small">Caducidad</label>
    <input id="caducidad" type="date" class="form-control">
  </div>
  <div class="col-6 col-md-4">
    <label class="form-label small">Almacén</label>
    <select id="almacen" class="form-select"></select>
  </div>
</div>
 === /ALTA INLINE === -->
            <div id="resultados" class='' style="margin-top:10px"></div>

    </div>
    </div>

    <script type="text/javascript" src="../../js/VentanaCentrada.js"></script>
    <script type="text/javascript" src="../../js/editar_remision.js"> </script>
    <link rel="stylesheet" href="//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">
    <script src="//code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    <?php
	include("../modal/buscar_productos.php");
	include("../footer.php");
	?>

<script>
document.addEventListener('DOMContentLoaded', function () {
  /* === CONFIG === */
  const URL_BUSCAR  = '../../ajax/buscar_productos_json.php';   // <-- SINGULAR (la que dijiste que creaste)
  const URL_AGREGAR = '../../ajax/agregar_facturacion.php';

  function getNumeroFactura(){ return document.getElementById('numero_factura')?.value || ''; }
  function getIdVendedor(){ return document.getElementById('id_vendedor')?.value || ''; }

  // Intercepta el botón del modal SOLO en móvil
  const btn = document.getElementById('btnAgregarProductos');
  if (btn) {
    btn.addEventListener('click', function(e){
      if (window.matchMedia('(max-width: 576px)').matches) {
        e.preventDefault();
        document.getElementById('findProd')?.focus();
      }
    });
  }

  // Refs
  const $find = document.getElementById('findProd');
  const $drop = document.getElementById('dropProds');
  const $qty  = document.getElementById('qty');
  const $price= document.getElementById('price');
  const $loteRow = document.getElementById('loteRow');
  const $lote = document.getElementById('lote');
  const $cad  = document.getElementById('caducidad');
  const $alm  = document.getElementById('almacen');

  // Asegura que el input exista; si no, avisa en consola
  if (!$find) {
    console.error('[inline] No encontré #findProd en el DOM.');
    return; // evita seguir para que lo notes en consola
  }
  console.log('[inline] listo, tengo #findProd:', $find);

  // utils
  function debounce(fn, ms){ let t; return (...a)=>{ clearTimeout(t); t=setTimeout(()=>fn(...a), ms); }; }
  function normalizeResults(payload){
    if (Array.isArray(payload)) return payload;
    if (payload && Array.isArray(payload.data)) return payload.data;
    if (payload && typeof payload==='object' && !payload.error){
      const vals = Object.values(payload);
      if (vals.length && typeof vals[0]==='object') return vals;
    }
    return [];
  }

  /* === BÚSQUEDA === */
  const buscarDeb = debounce(()=>buscar($find.value.trim()), 220);
  $find.addEventListener('input', buscarDeb);
  $find.addEventListener('focus', ()=>{ if($drop.children.length) $drop.classList.add('show'); });
  document.addEventListener('click', (e)=>{ if(!e.target.closest('#dropProds') && e.target!==$find) $drop.classList.remove('show'); });

  async function buscar(q){
    if (!q || q.length < 2) { $drop.classList.remove('show'); return; }
    console.log('[inline] buscar:', q);

    try {
      const resp = await fetch(URL_BUSCAR, {
        method: 'POST',
        headers: { 'X-Requested-With': 'XMLHttpRequest' },
        body: new URLSearchParams({ q, format: 'json' })
      });

      const text = await resp.text();
      if (!resp.ok || !text.trim()) {
        console.error('[inline] respuesta vacía o HTTP', resp.status, text.slice(0,300));
        renderSugerencias([]); return;
      }

      let raw;
      try { raw = JSON.parse(text); }
      catch { console.error('[inline] NO JSON:', text.slice(0,300)); renderSugerencias([]); return; }

      const items = normalizeResults(raw);
      console.log('[inline] items:', items);
      renderSugerencias(items);

    } catch (err) {
      console.error('[inline] fetch error', err);
      $drop.classList.remove('show');
    }
  }

function renderSugerencias(items){
  if (!items.length){ $drop.innerHTML=''; $drop.classList.remove('show'); return; }

  $drop.innerHTML = items.map(it=>{
    const ref   = it.referencia ?? '';
    const desc  = it.descripcion ?? '';
    const n     = it.lotes?.length ?? 0;
    const stockTotal = (it.lotes||[]).reduce((acc,l)=> acc + (Number(l.stock)||0), 0);
    const precio= Number(it.precio ?? 0);

    return `
      <button type="button" class="dropdown-item">
        <div class="d-flex justify-content-between">
          <div>
            <div><strong>${ref}</strong> — ${desc}</div>
            <div class="small text-muted">${n} lote(s) • Stock total: ${stockTotal}</div>
          </div>
          <div class="text-end">$${precio.toFixed(2)}</div>
        </div>
      </button>
    `;
  }).join('');

  [...$drop.children].forEach((btn, idx)=>{
    btn.onclick = ()=>{
      const it = items[idx];
      prodSel = it;
      $find.value = `${it.referencia ?? ''} — ${it.descripcion ?? ''}`;
      $price.value = Number(it.precio ?? 0).toFixed(2);
      $drop.classList.remove('show');

      hydrateLotes(it.lotes || []);     // ← ahora SÍ mostramos el selector
      $loteRow.classList.remove('d-none');
      $qty.focus();
    };
  });

  $drop.classList.add('show');
}

function hydrateLotes(raw){
  // Acepta arreglo o string JSON
  let lotes = [];
  if (Array.isArray(raw)) lotes = raw;
  else if (typeof raw === 'string') {
    try { lotes = JSON.parse(raw); } catch(_) { lotes = []; }
  }

  if (!lotes.length) {
    $loteRow.classList.add('d-none');
    $lote.innerHTML = '';
    $cad.value = '';
    $alm.innerHTML = '';
    return;
  }

  $loteRow.classList.remove('d-none');

  // Opcional: ordenar por caducidad asc o por mayor stock
  // lotes.sort((a,b)=> (new Date(a.caducidad||'2100-01-01')) - (new Date(b.caducidad||'2100-01-01')));

  $lote.innerHTML = lotes.map((l,i)=> `
    <option value="${i}">
      ${l.lote ?? ''} (Stock: ${Number(l.stock ?? 0)}) — Almacén: ${l.almacen ?? '-'}
    </option>
  `).join('');

  $lote.onchange = ()=>{
    const L = lotes[$lote.value];
    $cad.value = L?.caducidad ?? '';
    const arr = Array.isArray(L?.almacen) ? L.almacen : [L?.almacen ?? ''];
    $alm.innerHTML = arr.map(a=>`<option value="${a}">${a}</option>`).join('');
  };

  // Inicializa con la primera opción
  $lote.selectedIndex = 0;
  $lote.dispatchEvent(new Event('change'));
}

  /* === AGREGAR LÍNEA === */
  let prodSel = null;
  document.getElementById('addLine')?.addEventListener('click', agregarInline);
  $find.addEventListener('keydown', (e)=>{ if(e.key==='Enter'){ e.preventDefault(); agregarInline(); } });

  function agregarInline(){
    if(!prodSel){ $find.focus(); return; }
    const cantidad = Math.max(1, Number($qty.value||1));
    const precio   = Math.max(0, Number($price.value||0));
    const lotes = prodSel.lotes || [];
    const loteInfo = (!$loteRow.classList.contains('d-none') && $lote.value!=='' && lotes.length) ? (lotes[$lote.value]||{}) : {};
    const lote     = loteInfo.lote ?? '';
    const caducidad= $cad.value || loteInfo.caducidad || '';
    const almacen  = $alm.value || loteInfo.almacen || '';

    if (isNaN(cantidad)){ alert('Esto no es un número'); $qty.focus(); return; }
    if (isNaN(precio) || precio===0){ alert('Por favor ingresa un precio'); $price.focus(); return; }
    if (!lote){ alert('Por favor ingresa el lote'); $lote.focus(); return; }

    $.ajax({
      type: "POST",
      url: URL_AGREGAR,
      data: {
        id: prodSel.id ?? prodSel.id_producto,
        precio_venta: precio,
        cantidad: cantidad,
        lote: lote,
        caducidad: caducidad,
        referencia: prodSel.referencia ?? '',
        descripcion: prodSel.descripcion ?? '',
        almacen: almacen,
        numero_factura: getNumeroFactura(),
        id_vendedor: getIdVendedor()
      },
      beforeSend: function(){ $("#resultados").html("Mensaje: Cargando..."); },
      success: function(html){
        $("#resultados").html(html);
        if (typeof cargar_remision === 'function') cargar_remision();
        prodSel = null; $find.value=''; $qty.value=1; $price.value='';
        $loteRow.classList.add('d-none'); $find.focus();
      }
    });
  }
});
////////////**********fin de agregar remision   ////////// */
$("#datos_remision").submit(function(event) {

    var parametros = $(this).serialize();
    var id_cliente = $("#id_cliente").val().trim();

    if (id_cliente === "") {
        Swal.fire({
            icon: 'warning',
            title: '¡Atención!',
            text: 'Debes seleccionar un cliente.',
            confirmButtonText: 'Aceptar'
        });

        return; // Detiene el envío si no hay cliente
    }
    $.ajax({
        type: "POST",
        url: "../../ajax/datos_remision.php",
        data: parametros,
        beforeSend: function(objeto) {
            $("#resultados_ajax").html("Mensaje: Cargando...");
        },
        success: function(datos) {
            Swal.fire({
                title: "Remisión guardada exitosamente",
                text: "OK!",
                icon: "success"
            });

        }
    });
    event.preventDefault();
})
function cerrar_remision() {
    var id_cliente = $("#id_cliente").val().trim();
    var numero_factura = $("#numero_factura").val().trim();
    var letra_ventas = $("#letra_ventas").val().trim();
    var observaciones = $("#observaciones").val().trim();
    var fecha = $("#fecha").val().trim();
    var compra = $("#compra").val().trim();
    var cotizacion = $("#cotizacion").val().trim();
    var doctor = $("#doctor").val().trim();
    var paciente = $("#paciente").val().trim();
    var material = $("#material").val().trim();
    var pago = $("#pago").val().trim();
    var d_factura = $("#d_factura").val().trim();
    var id_vendedor = $("#id_vendedor").val().trim();
    var hospital = $("#hospital").val().trim();
    var proveedor = $("#proveedor").val().trim();

    // Validar si algún campo está vacío
    if (
        id_cliente === "" || numero_factura === "" || letra_ventas === "" ||
         fecha === "" || compra === "" ||
        cotizacion === "" || doctor === "" || paciente === "" ||
        material === "" || pago === "" || d_factura === "" || id_vendedor === ""
        || hospital === "" || proveedor === ""
    ) {
        Swal.fire({
            icon: 'warning',
            title: 'Campos incompletos',
            text: 'Todos los campos deben ser llenados antes de continuar.'
        });
        return; // detener ejecución si hay campos vacíos
    }

    // Si todo está lleno, ejecutar acción
    VentanaCentrada(
        '../../pdf/print_remision.php?id_cliente=' + id_cliente +
        '&numero_factura=' + numero_factura +
        '&letra_ventas=' + letra_ventas +
        '&fecha=' + fecha +
        '&compra=' + compra +
        '&cotizacion=' + cotizacion +
        '&doctor=' + doctor +
        '&paciente=' + paciente +
        '&material=' + material +
        '&pago=' + pago +
        '&d_factura=' + d_factura +
        '&id_vendedor=' + id_vendedor +
        '&observaciones=' + observaciones +
        '&hospital=' + hospital +
        '&proveedor=' + proveedor

    );
}




$(function() {
    $("#nombre_cliente").autocomplete({
        source: "../../ajax/autocomplete/clientes.php",
        minLength: 2,
        select: function(event, ui) {
            $('#id_cliente').val(ui.item.id_cliente);
            $('#nombre_cliente').val(ui.item.nombre_cliente);
            $('#rfc_cliente').val(ui.item.rfc_cliente);
            $('#calle_cliente').val(ui.item.calle_cliente);
            $('#telefono_cliente').val(ui.item.telefono_cliente);
            $('#mail').val(ui.item.emailpred);
            $('#numext_cliente').val(ui.item.numext_cliente);
            $('#colonia_cliente').val(ui.item.colonia_cliente);
        }
    });


});

$("#nombre_cliente").on("keydown", function(event) {
    if (event.keyCode == $.ui.keyCode.LEFT || event.keyCode == $.ui.keyCode.RIGHT || event.keyCode == $.ui
        .keyCode.UP || event.keyCode == $.ui.keyCode.DOWN || event.keyCode == $.ui.keyCode.DELETE || event
        .keyCode == $.ui.keyCode.BACKSPACE) {
        $("#id_cliente").val("");
        $("#rfc_cliente").val("");
        $("#telefono_cliente").val("");
        $('#calle_cliente').val("");
        $('#numext_cliente').val("");
        $('#colonia_cliente').val("");
        $('#mail').val("");
    }
    if (event.keyCode == $.ui.keyCode.DELETE) {
        $("#nombre_cliente").val("");
        $("#id_cliente").val("");
        $("#rfc_cliente").val("");
        $("#telefono_cliente").val("");
        $("#mail").val("");
        $('#calle_cliente').val("");
        $('#numext_cliente').val("");
        $('#colonia_cliente').val("");
    }
});
function exc_iva(id) {
  const ivaCheckbox = document.getElementById("exento_" + id);
  if (ivaCheckbox) {
    const ivaValue = ivaCheckbox.checked ? 1 : 0;

    // Enviar solicitud al servidor con el nuevo valor
    fetch("../../ajax/actualizar_iva.php", {
      method: "POST",
      headers: {
        "Content-Type": "application/json"
      },
      body: JSON.stringify({ id: id, iva: ivaValue })
    })
    
 .then(response => response.text())
    .then(data => {
      console.log(data);
       cargar_remision(); // Llamada a la función después de recibir la respuesta
    })

    .catch(error => console.error("Error:", error));
  }
}




const SELECTOR_SIDEBAR_WRAPPER = '.sidebar-wrapper';
const Default = {
    scrollbarTheme: 'os-theme-light',
    scrollbarAutoHide: 'leave',
    scrollbarClickScroll: true,
};
document.addEventListener('DOMContentLoaded', function() {
    const sidebarWrapper = document.querySelector(SELECTOR_SIDEBAR_WRAPPER);
    if (sidebarWrapper && typeof OverlayScrollbarsGlobal?.OverlayScrollbars !== 'undefined') {
        OverlayScrollbarsGlobal.OverlayScrollbars(sidebarWrapper, {
            scrollbars: {
                theme: Default.scrollbarTheme,
                autoHide: Default.scrollbarAutoHide,
                clickScroll: Default.scrollbarClickScroll,
            },
        });
    }
});

</script>

</body>

</html>