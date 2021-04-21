<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Crud con boostrap y datatables</title>

  <link href="bootstrap-4.3.1/css/bootstrap.min.css" rel="stylesheet">
  <link href="datatables/datatables.min.css" rel="stylesheet">

  <script src="js/jquery-3.4.1.js"></script>
  <script src="js/popper.min.js"></script>
  <script src="bootstrap-4.3.1/js/bootstrap.min.js"></script>
  <script src="datatables/datatables.min.js"></script>   
</head>

<body>
  <div class="container">

    <div class="row">
      <div class="col-12">
        <table class="table table-striped table-bordered table-hover" id="tablaarticulos">
          <thead>
            <tr>
              <td>Código</td>
              <td>Descripción</td>
              <td>Precio</td>
              <td>Modificar</td>
              <td>Borrar</td>
            </tr>
          </thead>
        </table>
        <button class="btn btn-sm btn-primary" id="BotonAgregar">Agregar artículo</button>
      </div>
    </div>

    <!-- Formulario (Agregar, Modificar) -->

    <div class="modal fade" id="FormularioArticulo" tabindex="-1" role="dialog">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">

            <button type="button" class="close" data-dismiss="modal">
              <span aria-hidden="true">×</span>
            </button>
          </div>
          <div class="modal-body">
            <input type="hidden" id="Codigo">
            <div class="form-row">
              <div class="form-group col-md-12">
                <label>Descripción:</label>
                <input type="text" id="Descripcion" class="form-control" placeholder="">
              </div>
            </div>

            <div class="form-row">
              <div class="form-group col-md-12">
                <label>Precio:</label>
                <input type="number" id="Precio" class="form-control" placeholder="">
              </div>
            </div>

            <div class="modal-footer">
              <button type="button" id="ConfirmarAgregar" class="btn btn-success">Agregar</button>
              <button type="button" id="ConfirmarModificar" class="btn btn-success">Modificar</button>
              <button type="button" class="btn btn-success" data-dismiss="modal">Cancelar</button>
            </div>

          </div>
        </div>
      </div>

    </div>

    <script>
      document.addEventListener("DOMContentLoaded", function() {
        
        let tabla1 = $("#tablaarticulos").DataTable({
          "ajax": {
            url: "datos.php?accion=listar",
            dataSrc: ""
          },
          "columns": [{
              "data": "codigo"
            },
            {
              "data": "descripcion"
            },
            {
              "data": "precio"
            },
            {
              "data": null,
              "orderable": false
            },
            {
              "data": null,
              "orderable": false
            }
          ],
          "columnDefs": [{
            targets: 3,
            "defaultContent": "<button class='btn btn-sm btn-primary botonmodificar'>Modifica?</button>",
            data: null
          }, {
            targets: 4,
            "defaultContent": "<button class='btn btn-sm btn-primary botonborrar'>Borra?</button>",
            data: null
          }],
          "language": {
            "url": "DataTables/spanish.json",
          },
        });

        //Eventos de botones de la aplicación
        $('#BotonAgregar').click(function() {
          $('#ConfirmarAgregar').show();
          $('#ConfirmarModificar').hide();
          limpiarFormulario();
          $("#FormularioArticulo").modal('show');
        });

        $('#ConfirmarAgregar').click(function() {
          $("#FormularioArticulo").modal('hide');
          let registro = recuperarDatosFormulario();
          agregarRegistro(registro);
        });

        $('#ConfirmarModificar').click(function() {
          $("#FormularioArticulo").modal('hide');
          let registro = recuperarDatosFormulario();
          modificarRegistro(registro);
        });

        $('#tablaarticulos tbody').on('click', 'button.botonmodificar', function() {
          $('#ConfirmarAgregar').hide();
          $('#ConfirmarModificar').show();
          let registro = tabla1.row($(this).parents('tr')).data();
          recuperarRegistro(registro.codigo);
        });

        $('#tablaarticulos tbody').on('click', 'button.botonborrar', function() {
          if (confirm("¿Realmente quiere borrar el artículo?")) {
            let registro = tabla1.row($(this).parents('tr')).data();
            borrarRegistro(registro.codigo);
          }
        });

        // funciones que interactuan con el formulario de entrada de datos
        function limpiarFormulario() {
          $('#Codigo').val('');
          $('#Descripcion').val('');
          $('#Precio').val('');
        }

        function recuperarDatosFormulario() {
          let registro = {
            codigo: $('#Codigo').val(),
            descripcion: $('#Descripcion').val(),
            precio: $('#Precio').val()
          };
          return registro;
        }


        // funciones para comunicarse con el servidor via ajax
        function agregarRegistro(registro) {
          $.ajax({
            type: 'POST',
            url: 'datos.php?accion=agregar',
            data: registro,
            success: function(msg) {
              tabla1.ajax.reload();
            },
            error: function() {
              alert("Hay un problema");
            }
          });
        }

        function borrarRegistro(codigo) {
          $.ajax({
            type: 'GET',
            url: 'datos.php?accion=borrar&codigo=' + codigo,
            data: '',
            success: function(msg) {
              tabla1.ajax.reload();
            },
            error: function() {
              alert("Hay un problema");
            }
          });
        }

        function recuperarRegistro(codigo) {
          $.ajax({
            type: 'GET',
            url: 'datos.php?accion=consultar&codigo=' + codigo,
            data: '',
            success: function(datos) {
              $('#Codigo').val(datos[0].codigo);
              $('#Descripcion').val(datos[0].descripcion);
              $('#Precio').val(datos[0].precio);
              $("#FormularioArticulo").modal('show');
            },
            error: function() {
              alert("Hay un problema");
            }
          });
        }

        function modificarRegistro(registro) {
          $.ajax({
            type: 'POST',
            url: 'datos.php?accion=modificar&codigo=' + registro.codigo,
            data: registro,
            success: function(msg) {
              tabla1.ajax.reload();
            },
            error: function() {
              alert("Hay un problema");
            }
          });
        }

      });
    </script>

</body>

</html>