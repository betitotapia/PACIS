
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title> Remision PACIS </title>
<meta name="robots" content="noindex,nofollow" />
<meta name="viewport" content="width=device-width; initial-scale=1.0;" />

<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.9.2/html2pdf.bundle.min.js"></script>
<style type="text/css">
  @import url(https://fonts.googleapis.com/css?family=Open+Sans:400,700);
  body { margin: 0; padding: 0; background: #ffffffff; }
  div, p, a, li, td { -webkit-text-size-adjust: none; }
  .ReadMsgBody { width: 100%; background-color: #ffffffff; }
  .ExternalClass { width: 100%; background-color: #ffffff; }
  body { width: 100%; height: 100%; background-color: #ffffffff; margin: 0; padding: 0; -webkit-font-smoothing: antialiased; }
  html { width: 100%; }
  p { padding: 0 !important; margin-top: 0 !important; margin-right: 0 !important; margin-bottom: 0 !important; margin-left: 0 !important; }
  .visibleMobile { display: none; }
  .hiddenMobile { display: block; }

  @media only screen and (max-width: 600px) {
  body { width: auto !important; }
  table[class=fullTable] { width: 96% !important; clear: both; }
  table[class=fullPadding] { width: 85% !important; clear: both; }
  table[class=col] { width: 45% !important; }
  .erase { display: none; }
  }

  @media only screen and (max-width: 420px) {
  table[class=fullTable] { width: 100% !important; clear: both; }
  table[class=fullPadding] { width: 85% !important; clear: both; }
  table[class=col] { width: 100% !important; clear: both; }
  table[class=col] td { text-align: left !important; }
  .erase { display: none; font-size: 0; max-height: 0; line-height: 0; padding: 0; }
  .visibleMobile { display: block !important; }
  .hiddenMobile { display: none !important; }
  }
</style>


<!-- Header -->
<table width="100%" border="0" cellpadding="0" cellspacing="0" align="center" class="fullTable" bgcolor="#e1e1e1">
  <tr>
    <td height="20"></td>
  </tr>
  <tr>
    <td>
      <table width="800" border="0" cellpadding="0" cellspacing="0" align="center" class="fullTable" bgcolor="#ffffff" style="border-radius: 10px 10px 0 0;">
        <tr class="hiddenMobile">
          <td height="40"></td>
        </tr>
        <tr class="visibleMobile">
          <td height="30"></td>
        </tr>

        <tr>
          <td>
            <table width="660" border="0" cellpadding="0" cellspacing="0" align="center" class="fullPadding">
              <tbody>
                <tr>
                  <td>
                    <table width="450" border="0" cellpadding="0" cellspacing="0" align="left" class="col-4">
                      <tbody>
                        <tr>
							            <td align="left"><img style="width:80%; margin-top:-7%; " src="../assets/img/<?php echo get_row('perfil','logo_url', 'id_perfil', 1);?>" alt="Logo"><br></td>
                          <!-- <td align="left"> <img src="http://www.supah.it/dribbble/017/logo.png" width="32" height="32" alt="logo" border="0" /></td> -->
                          <td align="center" style="width:50%; font-size:11px; font-family:Arial, sans-serif; color:#ff5e19ff; font-weight:bold;">
                                CERRADA KONHUNLICH MZ 7 LT12 CASA A, Número 504,<br>
                                Colonia SUPERMANZANA 504, Estado Quintana Roo, Municipio de Benito Juárez
                          </td>
                            </tr>
                        <tr class="hiddenMobile">
                          <td height="40"></td>
                        </tr>
                        <tr class="visibleMobile">
                          <td height="20"></td>
                        </tr>
                        <tr>
                         <td style="width:60%; margin-top:-5%; font-size: 14px; color: #3d3c3cff; font-family: 'Open Sans',  sans-serif; line-height: 20px; vertical-align: top; text-align: left;">
                            <?php 
				$sql_cliente=mysqli_query($con,"select * from clientes where id_cliente='$id_cliente'");
				echo "<br> <b>Razon Social:";
				$rw_cliente=mysqli_fetch_array($sql_cliente);
				echo $rw_cliente['nombre_cliente'];
        echo "<br><span> Dependencia: </span>";
        echo $rw_factura['hospital'];
        echo "<br><span> No. de Proveedor:</span>";
        echo ' '.$rw_factura['no_proveedor']."</b>";
				echo "<br>Dirección:";
				echo $rw_cliente['calle'],$rw_cliente['num_int'],$rw_cliente['num_ext'];
				echo "<br> Teléfono:";
				echo $rw_cliente['telefono'];				
				echo "<br>  Email: ";
				echo $rw_cliente['email'];
				echo "<br> RFC: ";
				echo $rw_cliente['rfc'];
			?>
                         
                        </td>
                        </tr>
                      </tbody>
                    </table>
                   <table width="200" border="0" cellpadding="0" cellspacing="0" align="right" class="col">
                      <tbody>
                        <tr class="visibleMobile">
                          <td height="20"></td>
                        </tr>
                        <tr>
                          <td height="5"></td>
                        </tr>
                        <tr>
                          <td style="font-size: 21px; color: #ff0000; letter-spacing: -1px; font-family: 'Open Sans', sans-serif; line-height: 1; vertical-align: top; text-align: right;">
                           REMISIÓN
                          </td>
                        </tr>
                        <tr>
                        <tr class="hiddenMobile">
                          <td height="20"></td>
                        </tr>
                        <tr class="visibleMobile">
                          <td height="0"></td>
                        </tr>
                        <tr>
                          <td style="font-size: 18px; font-weight: bold; color: #cb2a0aff; font-family: 'Open Sans', sans-serif; line-height: 18px; vertical-align: top; text-align: right;">
                            <small>FOLIO</small>: P<?php echo $letra_ventas; ?>-<b><?php echo $numero_factura ?></b><br />
                            <small>Fecha: <b><?php echo $fecha?></b> </small>
                          </td>
                        </tr>
                      </tbody>
                    </table>
                  </td>
                </tr>
              </tbody>
            </table>
          </td>
        </tr>
      </table>
    </td>
  </tr>
</table>
<!-- /Header -->
<!-- Order Details -->
<table width="100%" border="0" cellpadding="0" cellspacing="0" align="center" class="fullTable" bgcolor="#e1e1e1">
  <tbody>
    <tr>
      <td>
        <table width="800" border="0" cellpadding="0" cellspacing="0" align="center" class="fullTable" bgcolor="#ffffff">
          <tbody>
            <tr>
            <tr class="hiddenMobile">
              <td height="60"></td>
            </tr>
            <tr class="visibleMobile">
              <td height="40"></td>
            </tr>
            <tr>
              <td>
                <table width="82.5%" border="0" cellpadding="0" cellspacing="0" align="center" class="fullPadding">
                  <tbody>
                    <tr>
                      <th style="font-size: 14px; font-family: 'Open Sans', sans-serif; color: #5b5b5b; font-weight: normal; line-height: 1; vertical-align: top; padding: 0 10px 7px 0;" width="32%" align="left">
                       Descripción
                      </th>
                      <th style="font-size: 16px; font-family: 'Open Sans', sans-serif; color: #5b5b5b; font-weight: normal; line-height: 1; vertical-align: top; padding: 0 0 7px;" align="left">
                        <small>Referencia</small>
                      </th>
					  <th style="font-size: 14px; font-family: 'Open Sans', sans-serif; color: #5b5b5b; font-weight: normal; line-height: 1; vertical-align: top; padding: 0 0 7px;" align="center">
                        Lote
                      </th>
                      </th>
					  <th style="font-size: 14px; font-family: 'Open Sans', sans-serif; color: #5b5b5b; font-weight: normal; line-height: 1; vertical-align: top; padding: 0 0 7px;" align="center">
                       Caducidad
                      </th>
                      <th style="font-size: 14px; font-family: 'Open Sans', sans-serif; color: #5b5b5b; font-weight: normal; line-height: 1; vertical-align: top; padding: 0 0 7px;" align="center">
                        Cantidad
                      </th>
                        <th style="font-size: 14px; font-family: 'Open Sans', sans-serif; color: #5b5b5b; font-weight: normal; line-height: 1; vertical-align: top; padding: 0 0 7px;" align="center">
                       IVA
                      </th>
                      <th style="font-size: 14px; font-family: 'Open Sans', sans-serif; color: #1e2b33; font-weight: normal; line-height: 1; vertical-align: top; padding: 0 0 7px;" align="right">
                        Subtotal
                      </th>
                      <th style="font-size: 14px; font-family: 'Open Sans', sans-serif; color: #1e2b33; font-weight: normal; line-height: 1; vertical-align: top; padding: 0 0 7px;" align="right">
                        Total
                      </th>
                    </tr>
                    <tr>
                      <td height="1" style="background: #ff5e19ff;" colspan="12"></td>
                    </tr>
                    <tr>
                      <td height="10" colspan="12"></td>
                    </tr>
					<?php

			$nums=1;
			$item=1;
			$sumador_total=0;

			$sql=mysqli_query($con, "SELECT f.id_detalle, f.numero_factura, f.id_producto, f.cantidad, f.precio_venta,f.id_vendedor,f.iva,  
      p.id_producto, p.referencia, p.descripcion, p.existencias, p.lote,p.caducidad,p.precio_producto,p.id_almacen,
      a.id_almacen, a.numero_almacen, a.descripcion as nombre_almacen 
      FROM  detalle_factura f
      INNER JOIN products p ON  f.id_producto = p.id_producto
			INNER JOIN almacenes a ON f.almacen = a.id_almacen
			WHERE f.numero_factura = '" . $numero_factura . "' AND f.id_vendedor ='" . $id_vendedor . "' order by f.id_detalle asc");
      
			while ($row = mysqli_fetch_array($sql)) {
			$id_tmp = $row["id_detalle"];
				$id_producto = $row["id_producto"];
				$referencia = $row['referencia'];
				$almacen = $row['id_almacen'];
				$lote = $row['lote'];
				$nombre_almacen = $row['nombre_almacen'];
				$caducidad=$row['caducidad'];
				$cantidad = $row['cantidad'];
				$nombre_producto = $row['descripcion'];
				$precio_venta = $row['precio_venta'];
				$precio_venta_f = number_format($precio_venta, 2); //Formateo variables
				$precio_venta_r = str_replace(",", "", $precio_venta_f); //Reemplazo las comas
				$precio_total = $precio_venta_r * $cantidad;
				$precio_total_f = number_format($precio_total, 2); //Precio total formateado
				$precio_total_r = str_replace(",", "", $precio_total_f); //Reemplazo las comas
				$sumador_total += $precio_total_r; //Sumador
				$fecha_caducidad=date_create($caducidad);
        $iva=$row['iva'];
			
        if ($iva == 1) {
			$s_iva=	$precio_venta*0.16;
			}else{
				$s_iva=0;
			}

				$no_item=$item++;

				if ($nums%2==0) {
					$clase = "clouds";
				} else {
					$clase = "silver";
				}
				
	?>
                    <tr>
                       <td style="font-size: 14px; font-family: 'Open Sans', sans-serif; color: #000000ff;  line-height: 18px;  vertical-align: top; padding:10px 0;" class="article"> <?php echo $nombre_producto; ?>  </td>
                      <td style="font-size: 14px; font-family: 'Open Sans', sans-serif; color: #2b2b2bff;  line-height: 18px;  vertical-align: top; padding:10px 0;"><?php echo $referencia; ?> </td>
                      <td style="font-size: 14px; font-family: 'Open Sans', sans-serif; color: #2b2b2bff;  line-height: 18px;  vertical-align: top; padding:10px 0;" align="center"><?php echo $lote; ?> </td>
                      <td style="font-size: 14px; font-family: 'Open Sans', sans-serif; color: #2b2b2bff;  line-height: 18px;  vertical-align: top; padding:10px 0;" align="center"><?php echo $caducidad ?></td> 
                      <td style="font-size: 14px; font-family: 'Open Sans', sans-serif; color: #2b2b2bff;  line-height: 18px;  vertical-align: top; padding:10px 0;" align="right"><?php echo number_format($cantidad,2,'.',',') ?></td> 
                       <td style="font-size: 14px; font-family: 'Open Sans', sans-serif; color: #2b2b2bff;  line-height: 18px;  vertical-align: top; padding:10px 0;" align="right"><?php if ($iva == 1) {echo "NO";}else{echo "SI";} ?></td>
                      <td style="font-size: 14px; font-family: 'Open Sans', sans-serif; color: #2b2b2bff;  line-height: 18px;  vertical-align: top; padding:10px 0;" align="right"><?php echo number_format($precio_venta,2,'.',',') ?></td> 
                      <td style="font-size: 14px; font-family: 'Open Sans', sans-serif; color: #2b2b2bff;  line-height: 18px;  vertical-align: top; padding:10px 0;"  align="right">$<?php echo number_format($precio_total,2,'.',','); ?> </td> 
                    </tr>

					<?php
						$nums++;
					}
								$impuesto=get_row('perfil','impuesto', 'id_perfil', 1);
								$subtotal=number_format($sumador_total,2,'.','');
								$total_iva = (($subtotal * $impuesto) / 100)-$s_iva;
								$total_iva=number_format($total_iva,2,'.','');
								$total_factura=$subtotal+$total_iva;
					?>
                    <tr>
                      <td height="1" colspan="12" style="border-bottom:1px solid #e4e4e4"></td>
                    </tr>
                   
                    <tr>
                      <td height="1" colspan="12" style="border-bottom:1px solid #e4e4e4"></td>
                    </tr>
                  </tbody>
                </table>
              </td>
            </tr>
            <tr>
              <td height="20"></td>
            </tr>
          </tbody>
        </table>
      </td>
    </tr>
  </tbody>
</table>
<!-- /Order Details -->
<!-- Total -->
<table width="100%" border="0" cellpadding="0" cellspacing="0" align="center" class="fullTable" bgcolor="#e1e1e1">
  <tbody>
    <tr>
      <td>
        <table width="800" border="0" cellpadding="0" cellspacing="0" align="center" class="fullTable" bgcolor="#ffffff">
          <tbody>
            <tr>
              <td>

                <!-- Table Total -->
                <table width="680" border="0" cellpadding="0" cellspacing="0" align="center" class="fullPadding">
                  <tbody>
                    <tr>
                      <td style="font-size: 14px; font-family: 'Open Sans', sans-serif; color: #646a6e; line-height: 22px; vertical-align: top; text-align:right; ">
                        Subtotal
                      </td>
                      <td style="font-size: 14px; font-family: 'Open Sans', sans-serif; color: #646a6e; line-height: 22px; vertical-align: top; text-align:right; white-space:nowrap;" width="80">
                        $<?php echo number_format($subtotal,2,'.',','); ?>
                      </td>
                    </tr>
                     <tr>
                      <td style="font-size: 14px; font-family: 'Open Sans', sans-serif; color: #646a6e; line-height: 22px; vertical-align: top; text-align:right; ">IVA <?php echo $impuesto ?> % </td>
                      <td style="font-size: 14px; font-family: 'Open Sans', sans-serif; color: #646a6e; line-height: 22px; vertical-align: top; text-align:right; ">
                        $<?php echo number_format($total_iva,2,'.',',') ?>
                      </td>
                    </tr>
                    <tr>
                     
                    </tr>
                    <tr>
                      <td style="font-size: 14px; font-family: 'Open Sans', sans-serif; color: #000; line-height: 22px; vertical-align: top; text-align:right; ">
                        <strong>Total</strong>
                      </td>
                      <td style="font-size: 14px; font-family: 'Open Sans', sans-serif; color: #000; line-height: 22px; vertical-align: top; text-align:right; ">
                        <strong>$ <?php echo number_format($total_factura,2,'.',',') ?></strong>
                      </td>
                    </tr>
                   
                  </tbody>
                </table>
                <!-- /Table Total -->

              </td>
            </tr>
          </tbody>
        </table>
      </td>
    </tr>
  </tbody>
</table>
<!-- /Total -->
<!-- Information -->
<table width="100%" border="0" cellpadding="0" cellspacing="0" align="center" class="fullTable" bgcolor="#e1e1e1">
  <tbody>
    <tr>
      <td>
        <table width="800" border="0" cellpadding="0" cellspacing="0" align="center" class="fullTable" bgcolor="#ffffff">
          <tbody>
            <tr>
            <tr class="hiddenMobile">
              <td height="60"></td>
            </tr>
            <tr class="visibleMobile">
              <td height="40"></td>
            </tr>
            <tr>
              <td>
                <table width="680" border="0" cellpadding="0" cellspacing="0" align="center" class="fullPadding">
                  <tbody>
                    <tr>
                      <td>
                        <table width="220" border="0" cellpadding="0" cellspacing="0" align="left" class="col">

                          <tbody>
                            <tr>
                              <td style="font-size: 11px; font-family: 'Open Sans', sans-serif; color: #5b5b5b; line-height: 1; vertical-align: top; ">
                                <strong></strong>
                              </td>
                            </tr>
                            <tr>
                              <td width="100%" height="10"></td>
                            </tr>
                            <tr>
                              <td style="font-size: 12px; font-family: 'Open Sans', sans-serif; color: #5b5b5b; line-height: 20px; vertical-align: top; ">
                                
                              </td>
                            </tr>
                          </tbody>
                        </table>


                        <table width="220" border="0" cellpadding="0" cellspacing="0" align="right" class="col">
                          <tbody>
                            <tr class="visibleMobile">
                              <td height="20"></td>
                            </tr>
                            <tr>
                              <td style="font-size: 11px; font-family: 'Open Sans', sans-serif; color: #5b5b5b; line-height: 1; vertical-align: top; ">
                                <strong></strong>
                              </td>
                            </tr>
                            <tr>
                              <td width="100%" height="10"></td>
                            </tr>
                            <tr>
                              <td style="font-size: 12px; font-family: 'Open Sans', sans-serif; color: #5b5b5b; line-height: 20px; vertical-align: top; ">
                                <a href="#" style="color: #ff0000; text-decoration:underline;"></a><br>
                                <a href="#" style="color:#b0b0b0;"></a>
                              </td>
                            </tr>
                          </tbody>
                        </table>
                      </td>
                    </tr>
                  </tbody>
                </table>
              </td>
            </tr>
            <tr>
              <td>
                <table width="680" border="0" cellpadding="0" cellspacing="0" align="center" class="fullPadding">
                  <tbody>
                    <tr>
                      <td>
                        <table width="220" border="0" cellpadding="0" cellspacing="0" align="left" class="col">
                          <tbody>
                            <tr class="hiddenMobile">
                              <td height="35"></td>
                            </tr>
                            <tr class="visibleMobile">
                              <td height="20"></td>
                            </tr>
                            <tr>
                              
                            </tr>
                          </tbody>
                        </table>


                        <table width="220" border="0" cellpadding="0" cellspacing="0" align="right" class="col">
                          <tbody>
                            <tr class="hiddenMobile">
                              <td height="35"></td>
                            </tr>
                            <tr class="visibleMobile">
                              <td height="20"></td>
                            </tr>
                            <tr>
                              <td style="font-size: 11px; font-family: 'Open Sans', sans-serif; color: #5b5b5b; line-height: 1; vertical-align: top; ">
                                <strong></strong>
                              </td>
                            </tr>
                            <tr>
                              <td width="100%" height="10"></td>
                            </tr>
                            <tr>
                              <td style="font-size: 12px; font-family: 'Open Sans', sans-serif; color: #5b5b5b; line-height: 20px; vertical-align: top; ">
                               
                              </td>
                            </tr>
                          </tbody>
                        </table>
                      </td>
                    </tr>
                  </tbody>
                </table>
              </td>
            </tr>
            <tr class="hiddenMobile">
              <td height="60"></td>
            </tr>
            <tr class="visibleMobile">
              <td height="30"></td>
            </tr>
          </tbody>
        </table>
      </td>
    </tr>
  </tbody>
</table>
<!-- /Information -->
<table width="100%" border="0" cellpadding="0" cellspacing="0" align="center" class="fullTable" bgcolor="#e1e1e1">

  <tr>
    <td>
      <table width="800" border="0" cellpadding="0" cellspacing="0" align="center" class="fullTable" bgcolor="#ffffff" style="border-radius: 0 0 10px 10px;">
        <tr>
          <td>
            <table width="480" border="0" cellpadding="0" cellspacing="0" align="center" class="fullPadding">
              <tbody>
                <tr>
                  <td style="font-size: 12px; color: #5b5b5b; font-family: 'Open Sans', sans-serif; line-height: 18px; vertical-align: top; text-align: left;">
                   
                  </td>
                </tr>
              </tbody>
            </table>
          </td>
        </tr>
        <tr class="spacer">
          <td height="50"></td>
        </tr>

      </table>
    </td>
  </tr>
  <tr>
    <td height="20"></td>
  </tr>
</table>

<script>
function toPdf(letra,factura){

		const $elementoParaConvertir = document.body; // <-- Aquí puedes elegir cualquier elemento del DOM
        html2pdf()
            .set({
                margin: .3,
                filename: 'remision PACIS-'+letra+'-'+factura+'.pdf',
                image: {
                    type: 'jpeg',
                    quality: 0.98
                },
                html2canvas: {
                    scale: 3, // A mayor escala, mejores gráficos, pero más peso
                    letterRendering: true,
                },
                jsPDF: {
                    unit: "in",
                    format: "letter",
                    orientation: 'portrait' // landscape o portrait
                }
            })
            .from($elementoParaConvertir)
            .save()
            .catch(err => console.log(err));
   
	}
	
</script>
