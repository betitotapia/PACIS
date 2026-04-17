<?php
session_start();
if (!isset($_SESSION['user_login_status']) || $_SESSION['user_login_status'] != 1) {
    header("location: ../login");
    exit;
}
include("../../config/db.php");
include("../../config/conexion.php");

$id_oc = isset($_GET['id_oc']) ? (int)$_GET['id_oc'] : 0;
if($id_oc<=0){ die("OC inválida"); }

$q = mysqli_query($con, "SELECT * FROM ordenes_compra WHERE id_oc=$id_oc LIMIT 1");
$oc = mysqli_fetch_assoc($q);
if(!$oc){ die("No existe la OC"); }

// catálogos
$proveedores = mysqli_query($con,"SELECT id_proveedor, nombre_provedor FROM proveedores ORDER BY nombre_provedor");
$almacenes   = mysqli_query($con,"SELECT id_almacen, numero_almacen, descripcion FROM almacenes ORDER BY numero_almacen");

$det = mysqli_query($con,"SELECT * FROM ordenes_compra_detalle WHERE id_oc=$id_oc");
include("../header.php");
?>

<body class="layout-fixed sidebar-expand-lg sidebar-mini sidebar-collapse sidebar-dark-info  bg-body-tertiary">
<div class="app-wrapper">
<?php
include("../navbar.php");
include("../aside_menu.php");
?>
<main class="app-main">
    <div class="app-content">
        <div class="container-fluid">
    
        <div class="card card-primary card-outline">
            <div class="card-body">
    
            <h4>Editar Orden de Compra</h4>
            <hr>
<form id="form_oc">
  <input type="hidden" name="id_oc" value="<?php echo $id_oc; ?>">

  <div class="row">
    <div class="col-md-3">
      <label>Folio</label>
      <input class="form-control" value="<?php echo htmlspecialchars($oc['folio_oc']); ?>" readonly>
    </div>

    <div class="col-md-3">
      <label>Fecha</label>
      <input type="datetime-local" name="fecha_oc" class="form-control"
        value="<?php echo date('Y-m-d\TH:i', strtotime($oc['fecha_oc'])); ?>">
    </div>

    <div class="col-md-3">
      <label>Proveedor</label>
      <select name="id_proveedor" class="form-control" required>
        <?php while($p=mysqli_fetch_assoc($proveedores)){ ?>
          <option value="<?php echo $p['id_proveedor']; ?>"
            <?php if($p['id_proveedor']==$oc['id_proveedor']) echo 'selected'; ?>>
            <?php echo htmlspecialchars($p['nombre_provedor']); ?>
          </option>
        <?php } ?>
      </select>
    </div>

    <div class="col-md-3">
      <label>Almacén</label>
      <select name="id_almacen" class="form-control" required>
        <?php while($a=mysqli_fetch_assoc($almacenes)){ ?>
          <option value="<?php echo $a['id_almacen']; ?>"
            <?php if($a['id_almacen']==$oc['id_almacen']) echo 'selected'; ?>>
            <?php echo $a['numero_almacen'].' - '.htmlspecialchars($a['descripcion']); ?>
          </option>
        <?php } ?>
      </select>
    </div>
  </div>

  <div class="row" style="margin-top:10px;">
    <div class="col-md-3">
      <label>Estatus</label>
      <select name="estatus" class="form-control">
        <?php
          $estatus = ['BORRADOR','ABIERTA','PARCIAL','CERRADA','CANCELADA'];
          foreach($estatus as $e){
            $sel = ($oc['estatus']===$e) ? 'selected' : '';
            echo "<option $sel value='$e'>$e</option>";
          }
        ?>
      </select>
    </div>
    <div class="col-md-9">
      <label>Observaciones</label>
      <input name="observaciones" class="form-control" value="<?php echo htmlspecialchars($oc['observaciones']); ?>">
    </div>
  </div>

  <hr>

  <div class="table-responsive">
    <table class="table table-bordered" id="tabla_det">
      <thead>
        <tr>
          <th>Referencia</th>
          <th>Descripción</th>
          <th class="text-right">Cant. Sol.</th>
          <th class="text-right">Cant. Rec.</th>
          <th class="text-right">Costo</th>
          <th></th>
        </tr>
      </thead>
      <tbody>
        <?php while($d=mysqli_fetch_assoc($det)){ ?>
          <tr>
            <td><input name="referencia[]" class="form-control" value="<?php echo htmlspecialchars($d['referencia']); ?>"></td>
            <td><input name="descripcion[]" class="form-control" value="<?php echo htmlspecialchars($d['descripcion']); ?>"></td>
            <td><input name="cantidad_solicitada[]" type="number" step="0.01" class="form-control" value="<?php echo $d['cantidad_solicitada']; ?>"></td>
            <td><input name="cantidad_recibida[]" type="number" step="0.01" class="form-control" value="<?php echo $d['cantidad_recibida']; ?>"></td>
            <td><input name="costo_unitario[]" type="number" step="0.0001" class="form-control" value="<?php echo $d['costo_unitario']; ?>"></td>
            <td class="text-center">
              <a href="#" onclick="this.closest('tr').remove(); return false;">
                <i class="fa fa-trash text-danger"></i>
              </a>
            </td>
          </tr>
        <?php } ?>
      </tbody>
    </table>
  </div>

  <button type="button" class="btn btn-default" onclick="agregarFila();">+ Agregar renglón</button>
  <button type="button" class="btn btn-primary" onclick="guardarEdicionOC();">Guardar cambios</button>
</form>
            </div>
        </div>
    
        </div>
    </div>
</main>
</div>  

<?php include("../footer.php"); ?>
</body>


<script>
function agregarFila(){
  const tr = document.createElement('tr');
  tr.innerHTML = `
    <td><input name="referencia[]" class="form-control"></td>
    <td><input name="descripcion[]" class="form-control"></td>
    <td><input name="cantidad_solicitada[]" type="number" step="0.01" class="form-control" value="0"></td>
    <td><input name="cantidad_recibida[]" type="number" step="0.01" class="form-control" value="0"></td>
    <td><input name="costo_unitario[]" type="number" step="0.0001" class="form-control" value="0"></td>
    <td class="text-center"><a href="#" onclick="this.closest('tr').remove(); return false;"><i class="fa fa-trash text-danger"></i></a></td>
  `;
  document.querySelector('#tabla_det tbody').appendChild(tr);
}

function guardarEdicionOC(){
  $.ajax({
    type: "POST",
    url: "../../ajax/actualizar_oc.php",
    data: $("#form_oc").serialize(),
    success: function(r){
      if(r === "OK"){
        alert("Orden actualizada");
        window.location.href = "index.php";
      } else {
        alert(r);
      }
    }
  });
}
</script>
