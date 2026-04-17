<?php
session_start();
if (!isset($_SESSION['user_login_status']) AND $_SESSION['user_login_status'] != 1) {
	header("location: ../login");
	exit;
	}
	$active_almacenes="active";
	$active_borrador="";
	$active_cancel="";
    $active_productos="";
	$active_lista_productos="";
	$active_nueva="";
	$active_remisiones="";
	$active_vehiculos="";
	$active_cancel="";
    $sctive_nuevo_producto='';
	$active_usuarios="";
	$active_terceros="";
	$active_provedores='';
	
require_once ("../../config/db.php");//Contiene las variables de configuracion para conectar a la base de datos
require_once ("../../config/conexion.php");//Contiene funcion que conecta a la base de datos

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema de Generación de Etiquetas</title>
    <link href="css//vanilla-dataTables.min.css" rel="stylesheet" type="text/css">
     <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link
      rel="stylesheet"
      href="https://cdn.jsdelivr.net/npm/@fontsource/source-sans-3@5.0.12/index.css"
      integrity="sha256-tXJfXfp6Ewt1ilPzLDtQnJV4hclT9XuaZUKyUvmyr+Q="
      crossorigin="anonymous"
    />
    <link rel="stylesheet" href="../../../dist/css/custom_styles.css" />
    <!--end::Fonts-->
    <!--begin::Third Party Plugin(OverlayScrollbars)-->
    <link
      rel="stylesheet"
      href="https://cdn.jsdelivr.net/npm/overlayscrollbars@2.10.1/styles/overlayscrollbars.min.css"
      integrity="sha256-tZHrRjVqNSRyWg2wbppGnT833E/Ys0DHWGwT04GiqQg="
      crossorigin="anonymous"
    />
    <!--end::Third Party Plugin(OverlayScrollbars)-->
    <!--begin::Third Party Plugin(Bootstrap Icons)-->
    <link
      rel="stylesheet"
      href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css"
      integrity="sha256-9kPW/n5nn53j4WMRYAxe9c1rCY96Oogo/MKSVdKzPmI="
      crossorigin="anonymous"
    />
    <!--end::Third Party Plugin(Bootstrap Icons)-->
    <!--begin::Required Plugin(AdminLTE)-->
    <link rel="stylesheet" href="../../../dist/css/adminlte.css" />
    
    <link href="https://cdn.jsdelivr.net/npm/vanilla-datatables@latest/dist/vanilla-dataTables.min.css" rel="stylesheet" type="text/css">
   
    <!--end::Required Plugin(AdminLTE)-->
    
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/boxicons@latest/css/boxicons.min.css">
	<link rel=icon href='img/logo-icon.png' sizes="32x32" type="image/png">


    <!--<script type="text/javascript" src="PDFJS/app.js"></script>-->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js" charset="utf-8"></script>
   <script src="https://cdn.jsdelivr.net/npm/vanilla-datatables@latest/dist/vanilla-dataTables.min.js" type="text/javascript"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.9.2/html2pdf.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
     <script
      src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js"
      integrity="sha384-0pUGZvbkm6XF6gxjEnlmuGrJXVbNuzT9qBBavbLwCsOGabYfZo0T0to5eqruptLy"
      crossorigin="anonymous"
    ></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
    <!-- <script src="../js/vanilla-dataTables.min.js" type="text/javascript"></script> -->
   
    <script src="../js/JsBarcode.all.min.js"></script>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f5f5f5;
        }
        .container {
            max-width: 1000px;
            margin: 0 auto;
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        h1 {
            text-align: center;
            color: #333;
        }
        .form-group {
            margin-bottom: 15px;
        }
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        input, button {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
        }
        button {
            background-color: #4CAF50;
            color: white;
            border: none;
            cursor: pointer;
            font-size: 16px;
            margin-top: 10px;
        }
        button:hover {
            background-color: #45a049;
        }
        .etiqueta {
            margin-top: 30px;
            padding: 20px;
            border: 1px dashed #ccc;
            text-align: center;
            display: none;
        }
        .etiqueta h2 {
            margin-top: 0;
        }
        #barcode {
            margin: 20px 0;
        }
        .busqueda {
            margin-top: 30px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .reimprimir-btn {
            background-color: #2196F3;
            padding: 5px 10px;
            font-size: 14px;
            width: auto;
        }
        .reimprimir-btn:hover {
            background-color: #0b7dda;
        }
        /* Estilos para impresión */
@media print {
    body * {
        visibility: hidden;
    }
    .etiqueta, .etiqueta * {
        visibility: visible;
    }
    .etiqueta {
        position: absolute;
        left: 0;
        top: 0;
        width: 100%;
        border: none;
        padding: 0;
        margin: 0;
    }
    #imprimir-btn {
        display: none;
    }
}
    </style>
</head>
<body>
      <?php
include '../navbar.php';
include '../aside_menu.php';
    ?>
    <div class="container">
        <h1>Sistema de Generación de Etiquetas</h1>
        
        <div class="form-group">
            <label for="referencia">Referencia:</label>
            <input type="text" id="referencia" required>
        </div>
        
        <div class="form-group">
            <label for="lote">Lote:</label>
            <input type="text" id="lote" required>
        </div>
        
        <div class="form-group">
            <label for="caducidad">Fecha de Caducidad:</label>
            <input type="date" id="caducidad" required>
        </div>
        
        <button id="generar-btn">Generar Etiqueta</button>
        
        <div class="etiqueta" id="etiqueta-container">
            <h2 id="etiqueta-titulo"></h2>
            <svg id="barcode"></svg>
            <p id="etiqueta-info"></p>
            <button id="imprimir-btn">Imprimir Etiqueta</button>
        </div>
        
        <div class="busqueda">
            <h2>Buscar Etiquetas</h2>
            <div class="form-group">
                <label for="busqueda">Buscar (referencia, lote o código):</label>
                <input type="text" id="busqueda">
            </div>
            <button id="buscar-btn">Buscar</button>
            
            <div id="resultados">
                <table id="resultados-table">
                
            </div>
        </div>
    </div>
    <script src="js/jquery.min.js" charset="utf-8"> </script>
    <script src="js/qrcode.min.js"></script>
    <script>
       


        
        document.addEventListener('DOMContentLoaded', function() {
            // Generar nueva etiqueta
            document.getElementById('generar-btn').addEventListener('click', generarEtiqueta);
            
            // Buscar etiquetas existentes
            document.getElementById('buscar-btn').addEventListener('click', buscarEtiquetas);
            
            // Imprimir etiqueta
            document.getElementById('imprimir-btn').addEventListener('click',manejarCodigoBarras);
          
            // Configurar fecha actual como valor por defecto
            const today = new Date();
            const formattedDate = today.toISOString().substr(0, 10);
            document.getElementById('caducidad').value = formattedDate;
        });
        
        function generarEtiqueta() {
            const referencia = document.getElementById('referencia').value.trim();
            const lote = document.getElementById('lote').value.trim();
            const caducidad = document.getElementById('caducidad').value.trim();
            
            if (!referencia || !lote || !caducidad) {
                alert('Por favor complete todos los campos');
                return;
            }
            
            const data = new FormData();
            data.append('action', 'generar');
            data.append('referencia', referencia);
            data.append('lote', lote);
            data.append('caducidad', caducidad);
            
            fetch('backend.php', {
                method: 'POST',
                body: data
            })
            .then(response => response.json())
            .then(data => {
                if (data.error) {
                    alert(data.error);
                    return;
                }
                
                mostrarEtiqueta(data.referencia, data.codigo_barras, data.id);
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Ocurrió un error al generar la etiqueta');
            });
        }
       // Reemplaza TODO el contenido del script desde la primera función mostrarEtiqueta hasta el final con esto:

// function mostrarEtiqueta(referencia, codigoBarras, id) {
//     const etiquetaContainer = document.getElementById('etiqueta-container');
//     etiquetaContainer.innerHTML = `
//         <h2 id="etiqueta-titulo">${referencia}</h2>
//         <svg id="barcode"></svg>
//         <p id="etiqueta-info" style="display:none;">Código: ${codigoBarras} | ID: ${id}</p>
//         <button id="imprimir-btn">Imprimir Etiqueta</button>
//     `;

////**************************************************** */
function mostrarEtiqueta(referencia, contenidoQR, id) {
    const etiquetaContainer = document.getElementById('etiqueta-container');
    
    // Limpiamos el contenedor y añadimos los nuevos elementos
    etiquetaContainer.innerHTML = `
        <h2 id="etiqueta-titulo" style=" font-size:18px;">${referencia}</h2>
        <div style="display:flex; justify-content:center; align-items:center; width:100%;">
        <div id="qr-code" class="qr-code-display"></div>
        </div>
        <p id="etiqueta-info" style="font-size:8;margin-left:2%;">QR: ${contenidoQR} | ID: ${id}</p>
        
        <button id="imprimir-btn" class="btn-imprimir">Imprimir Etiqueta</button>
        <button id="descargar-btn" class="btn-descargar">Descargar QR</button>
      
    `;
    
    // Generamos el código QR
    const qr = qrcode(0, 'L');
    qr.addData(contenidoQR);
    qr.make();
    document.getElementById('qr-code').innerHTML = qr.createImgTag(4);
    const info = document.getElementById('etiqueta-info').textContent;
    
    // Añadimos evento al botón de imprimir
    document.getElementById('imprimir-btn').addEventListener('click', () => {
        imprimirCodigoQR(contenidoQR, referencia, info);
    });
    
    // Añadimos evento al botón de descargar
    document.getElementById('descargar-btn').addEventListener('click', () => {
        generarYDescargarQRPNG(contenidoQR, referencia);
    });
    
    // Mostramos la información al hacer clic en el título
    document.getElementById('etiqueta-titulo').addEventListener('click', () => {
        const info = document.getElementById('etiqueta-info');
        info.style.display = info.style.display === 'none' ? 'block' : 'none';
    });


// Cargamos la librería QR si no está disponible
if (typeof qrcode === 'undefined') {
    const script = document.createElement('script');
    script.src = '../js/qrcode.min.js';
    document.head.appendChild(script);
}
////**************************************************** */

    // Generar código de barras
    // JsBarcode("#barcode", codigoBarras, {
    //     format: "CODE128",
    //     lineColor: "#000",
    //     width: .9,
    //     height: 50,
    //     margin: 10,
    //     displayValue: true
    // });
    
    // Agregar evento de impresión
    const imprimirBtn = document.getElementById('imprimir-btn');
    imprimirBtn.addEventListener('click',manejarCodigoBarras);
    
    etiquetaContainer.style.display = 'block';
}

// Función principal que maneja ambas opciones
function manejarCodigoBarras() {
    const referencia = document.getElementById('etiqueta-titulo').textContent;
    const info = document.getElementById('etiqueta-info').textContent;
    const codigoBarras = info.match(/Código: (.+?) \|/)[1];
    
    // Mostrar diálogo personalizado (puedes usar un modal más elegante si prefieres)
    const imprimir = confirm("¿Qué acción deseas realizar?\n\nAceptar: Imprimir código de barras\nCancelar: Descargar como imagen PNG");
    
    if (imprimir) {
        imprimirCodigoQR(codigoBarras, referencia, info);
       // imprimirCodigoBarras(codigoBarras, referencia, info);
    } else {
        generarYDescargarPNG(codigoBarras, referencia);
    }
}

// Función para imprimir
function imprimirCodigoBarras(codigoBarras, referencia, info) {
    const ventanaImpresion = window.open('', '_blank');
    
ventanaImpresion.document.write(`
    <!DOCTYPE html>
    <html>
    <head>
        <title>Etiqueta ${referencia}</title>
        <style>
            @page { 
                size: auto; 
                margin: 0; 
            }
            body { 
                font-family: Arial; 
                text-align: center; 
                margin-top: 15%;
                padding: 0;
                height: 62mm; /* Ancho estándar para etiquetas QL-800 */
                width: 100mm; /* Ancho estándar para etiquetas QL-800 */
            }
            .etiqueta-container {
                padding: 2mm;
                box-sizing: border-box;
            }
            h2 { 
                margin: 0 0 0 0; 
                font-size: 10 pt; 
                font-weight: bold;
            }
            p { 
                margin: 2mm 0; 
                font-size: 10pt; 
            }
            svg { 
                margin: 1mm auto; 
                display: block; 
                max-width: 110%; 
                height: auto; 
            }
        </style>
    </head>
    <body>
        <div class="etiqueta-container">
            <h2>${referencia}</h2>
            <svg id="barcode-print"></svg>
        </div>
        <script src="js/JsBarcode.all.min.js"><\/script>
        <script>
            window.onload = function() {
                JsBarcode("#barcode-print", "${codigoBarras}", {
                    format: "CODE128",
                    lineColor: "#000",
                    width:1.4,  // Reducido para evitar superposición
                    height: 50,  // Altura óptima para legibilidad
                    displayValue: true,
                    fontSize: 12,
                    margin: 5,   // Márgenes más ajustados
                    marginTop: 5,
                    marginBottom: 5,
                    marginLeft: 5,
                    marginRight: 5
                });
                setTimeout(function() {
                    window.print();
                    window.close();
                }, 300);
            }
        <\/script>
    </body>
    </html>
`);
ventanaImpresion.document.close();
}


// Función mejorada para generar y descargar PNG
function generarYDescargarPNG(codigoBarras, referencia) {
    // Crear contenedor temporal
    const tempDiv = document.createElement('div');
    tempDiv.style.position = 'absolute';
    tempDiv.style.left = '-9999px';
    tempDiv.innerHTML = '<svg id="temp-barcode"></svg>';
    document.body.appendChild(tempDiv);
    
    // Generar el código de barras con parámetros optimizados
    JsBarcode("#temp-barcode", codigoBarras, {
        format: "CODE128",
        lineColor: "#000",
        width: 1,  // Ancho de línea reducido
        height: 40,  // Altura ajustada
        displayValue: true,
        fontSize: 12,
        margin: 5    // Márgenes más ajustados
    });
    
    // Esperar a que se renderice
    setTimeout(() => {
        const svgElement = document.getElementById('temp-barcode');
        const svgData = new XMLSerializer().serializeToString(svgElement);
        const canvas = document.createElement('canvas');
        const ctx = canvas.getContext('2d');
        const img = new Image();
        
        img.onload = function() {
            // Configurar canvas
            canvas.width = img.width;
            canvas.height = img.height;
            ctx.drawImage(img, 0, 0);
            
            // Crear enlace de descarga
            const pngFile = canvas.toDataURL('image/png');
            const downloadLink = document.createElement('a');
            downloadLink.href = pngFile;
            downloadLink.download = `codigo-barras-${referencia.replace(/[^a-z0-9]/gi, '_')}.png`;
            
            // Descargar y limpiar
            document.body.appendChild(downloadLink);
            downloadLink.click();
            document.body.removeChild(downloadLink);
            document.body.removeChild(tempDiv);
        };
        
        img.src = 'data:image/svg+xml;base64,' + btoa(unescape(encodeURIComponent(svgData)));
    }, 100);
}

// Cambia tu botón para llamar a manejarCodigoBarras() en lugar de imprimirEtiqueta()
// Llamar a la función de opciones en lugar de imprimir directamente
// generarOpcionesCodigoBarras();
function buscarEtiquetas() {
            const query = document.getElementById('busqueda').value.trim();
            
            if (!query) {
                alert('Por favor ingrese un término de búsqueda');
                return;
            }
            
            const data = new FormData();
            data.append('action', 'buscar');
            data.append('query', query);
            
            fetch('backend.php', {
                method: 'POST',
                body: data
            })
            .then(response => response.json())
            .then(data => {
                if (data.error) {
                    alert(data.error);
                    return;
                }
                
                const tbody = document.querySelector('#resultados-table tbody');
                tbody.innerHTML = '';
                
                if (data.etiquetas.length === 0) {
                    const row = document.createElement('tr');
                    row.innerHTML = '<td colspan="6">No se encontraron resultados</td>';
                    tbody.appendChild(row);
                    return;
                }
                
                data.etiquetas.forEach(etiqueta => {
                    const row = document.createElement('tr');
                    row.innerHTML = `
                        <td>${etiqueta.id}</td>
                        <td>${etiqueta.referencia}</td>
                        <td>${etiqueta.lote}</td>
                        <td>${etiqueta.caducidad}</td>
                        <td>${etiqueta.codigo_barras}</td>
                        <td><button class="reimprimir-btn" data-id="${etiqueta.id}">Reimprimir</button></td>
                    `;
                    tbody.appendChild(row);
                });
                
                // Agregar eventos a los botones de reimpresión
                document.querySelectorAll('.reimprimir-btn').forEach(btn => {
                    btn.addEventListener('click', function() {
                        reimprimirEtiqueta(this.getAttribute('data-id'));
                    });
                });
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Ocurrió un error al buscar etiquetas');
            });
        }

function imprimirCodigoQR(codigoQR, referencia, info) {
    const ventanaImpresion = window.open('', '_blank');
    
    ventanaImpresion.document.write(`
        <!DOCTYPE html>
        <html>
        <head>
            <title>Etiqueta ${referencia}</title>
            <style>
                @page { 
                    size: auto; 
                    margin: 0; 
                }
                body { 
                    font-family: Arial; 
                    text-align: center; 
                    margin-top: 0%;
                    padding: 0;
                    height: 62mm; /* Ancho estándar para etiquetas QL-800 */
                   
                }
                .etiqueta-container {
                    padding: 2mm;
                    box-sizing: border-box;
                }
                h2 { 
                    margin-left:45%;
                    margin-top: 2%;
                    font-size: 8pt; 
                    font-weight: bold;
                }
                p { 
                    margin: 2mm 0; 
                    font-size: 6pt; 
                }
                .info{
                font-size: 4pt;
                margin-left: 40%;
                }
                .qr-container { 
                    margin: 1mm auto; 
                    display: block; 
                    max-width: 100%; 
                }
                .qr-code {
                    width: 50mm;
                    height: 50mm;
                    margin: 0 auto;
                }
                    .direction{
                    margin-top: -27%;
                    margin-left:-50% !important;
                    line-height: 1.3;
                    }
                    .logo{
                    margin-top: -10%;
                    }
                    .qr-code-display{
                    margin-top:4% !important;
                    }
            </style>
            <script src="js/qrcode.min.js"><\/script>
        </head>
        <body>
            <div class="etiqueta-container">
                <div style="display:flex; justify-content:center; align-items:center; width:100%; margin-top:-2%;">
                <div class='logo'><img src="./assets/img/logo.png" alt="QR Code" id="img_qr" style="width:130px;"></div>
                <div id="qr-code" class="qr-code-display"></div>
             </div>
             <div class='direction'><p>Suministros Médicos de México, 
             <br> 5 "B" Sur 5741, Villa Encantada, 
             <br>Puebla-Puebla <br>Tel:222-604-4913 
             <br> www.sumed.com.mx 
             <br>atencionaclientes@sumed.com.mx
             </p></div>                   
             <p class='info'>${info || ''}</p>   
             <h2 style="">${referencia}</h2>
                     
                <div class="qr-container">
                    <div id="qr-code" class="qr-code"></div>
                </div>
            <script>
                window.onload = function() {
                    const qr = qrcode(0, 'M');
                    qr.addData('${codigoQR}');
                    qr.make();
                    document.getElementById('qr-code').innerHTML = qr.createImgTag(4);
                    
                    setTimeout(function() {
                        window.print();
                        window.close();
                    }, 300);
                }
            <\/script>
        </body>
        </html>
    `);
    ventanaImpresion.document.close();
}

// Función para generar y descargar QR como PNG
function generarYDescargarQRPNG(codigoQR, referencia) {
    // Crear contenedor temporal
    const tempDiv = document.createElement('div');
    tempDiv.style.position = 'absolute';
    tempDiv.style.left = '-9999px';
    tempDiv.innerHTML = '<div id="temp-qr"></div>';
    document.body.appendChild(tempDiv);
    
    // Incluir la librería QR si no está disponible
    if (typeof qrcode === 'undefined') {
        const script = document.createElement('script');
        script.src = 'js/qrcode.min.js';
        script.onload = function() {
            generarQR();
        };
        document.head.appendChild(script);
    } else {
        generarQR();
    }
    
    function generarQR() {
        const qr = qrcode(0, 'M');
        qr.addData(codigoQR);
        qr.make();
        document.getElementById('temp-qr').innerHTML = qr.createImgTag(4);
        
        // Esperar a que se renderice
        setTimeout(() => {
            const imgElement = document.querySelector('#temp-qr img');
            const canvas = document.createElement('canvas');
            const ctx = canvas.getContext('2d');
            
            // Configurar canvas
            canvas.width = imgElement.width;
            canvas.height = imgElement.height;
            ctx.drawImage(imgElement, 0, 0);
            
            // Crear enlace de descarga
            const pngFile = canvas.toDataURL('image/png');
            const downloadLink = document.createElement('a');
            downloadLink.href = pngFile;
            downloadLink.download = `codigo-qr-${referencia.replace(/[^a-z0-9]/gi, '_')}.png`;
            
            // Descargar y limpiar
            document.body.appendChild(downloadLink);
            downloadLink.click();
            document.body.removeChild(downloadLink);
            document.body.removeChild(tempDiv);
        }, 100);
    }
}
        



function reimprimirEtiqueta(id) {
    const data = new FormData();
    data.append('action', 'reimprimir');
    data.append('id', id);
    fetch('backend.php', {
        method: 'POST',
        body: data
    })
    .then(response => response.json())
    .then(data => {
        if (data.error) {
            alert(data.error);
            return;
        }
        
        mostrarEtiqueta(data.referencia, data.codigo_barras, data.id);
        window.scrollTo(0, 0);
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Ocurrió un error al cargar la etiqueta');
    });
}
 $(document).ready(function(){
    load(1);
});

function load(page){
    var q= $("#q").val();
    $("#loader").fadeIn('slow');
    $.ajax({
        url:'./ajax/obtener_codigos.php',
         beforeSend: function(objeto){
      },
        success:function(data){
            $("#resultados").html(data).fadeIn('slow');
            $('#loader').html('');
            
        }
    })
}
</script>
</body>
</html>