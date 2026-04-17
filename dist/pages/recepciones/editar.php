<?php
include("../../config/db.php");
include("../../config/conexion.php");
include("../../ajax/is_logged.php");

$id_recepcion = isset($_GET['id_recepcion']) ? (int)$_GET['id_recepcion'] : 0;
if($id_recepcion<=0){ die("Recepción inválida"); }

$q = mysqli_query($con, "SELECT * FROM recepciones WHERE id_recepcion=$id_recepcion LIMIT 1");
$rec = mysqli_fetch_assoc($q);
if(!$rec){ die("No existe la recepción"); }
if($rec['estatus']=='CANCELADA'){ die("No se puede editar una recepción cancelada"); }

$proveedores = mysqli_query($con,"SELECT id_proveedor, nombre_provedor FROM proveedores ORDER BY nombre_provedor");
$detalle = mysqli_query($con,"SELECT * FROM recepciones_detalle WHERE id_recepcion=$id_recepcion");
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
    
            <h4>Editar Recepción</h4>
            <hr>
<form id="form_recepcion">
<input type="hidden" name="id_recepcion" value="<?php echo $id_recepcion; ?>">

<div class="row">
  <div class="col-md-3">
    <label>Folio</label>
    <input class="form-control" value="<?php echo $rec['folio']; ?>" readonly>
  </div>

  <div class="col-md-3">
    <label>Fecha</label>
    <input type="datetime-local" name="fecha_recepcion"
      class="form-control"
      value="<?php echo date('Y-m-d\TH:i', strtotime($rec['fecha_recepcion'])); ?>">
  </div>

  <div class="col-md-6">
    <label>Proveedor</label>
    <select name="id_proveedor" class="form-control">
      <?php while($p=mysqli_fetch_assoc($proveedores)){ ?>
        <option value="<?php echo $p['id_proveedor']; ?>"
          <?php if($p['id_proveedor']==$rec['id_proveedor']) echo 'selected'; ?>>
          <?php echo htmlspecialchars($p['nombre_provedor']); ?>
        </option>
      <?php } ?>
    </select>
  </div>
</div>

<hr>

<table class="table table-bordered" id="tabla_det">
<thead>
<tr>
  <th>Referencia</th>
  <th>Descripción</th>
  <th>Lote</th>
  <th class="text-right">Cantidad</th>
  <th class="text-right">Costo</th>
  <th></th>
</tr>
</thead>
<tbody>
<?php while($d=mysqli_fetch_assoc($detalle)){ ?>
<tr>
  <td><input name="referencia[]" class="form-control" value="<?php echo htmlspecialchars($d['referencia']); ?>"></td>
  <td><input name="descripcion[]" class="form-control" value="<?php echo htmlspecialchars($d['descripcion']); ?>"></td>
  <td><input name="lote[]" class="form-control" value="<?php echo htmlspecialchars($d['lote']); ?>"></td>
  <td><input name="cantidad[]" type="number" class="form-control" value="<?php echo $d['cantidad']; ?>"></td>
  <td><input name="costo[]" type="number" step="0.01" class="form-control" value="<?php echo $d['costo_unitario']; ?>"></td>
  <td>
    <a href="#" onclick="this.closest('tr').remove(); return false;">
      <i class="fa fa-trash text-danger"></i>
    </a>
  </td>
</tr>
<?php } ?>
</tbody>
</table>

<button type="button" onclick="agregarFila();" class="btn btn-default">+ Agregar</button>
<button type="button" onclick="guardarEdicion();" class="btn btn-primary">Guardar cambios</button>

</form>
            </div>
        </div>
    
        </div>
    </div>
    
</main>
</div>  

<?php include("../footer.php"); ?>
</body>