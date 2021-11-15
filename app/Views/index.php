<?php $this->extend('layout'); ?>

<?php $this->section('content'); ?>

<style>
  .table-wrapper {
    max-height: 25em;
    overflow: auto;
    display: inline-block;
  }

  .loader {
    border: 16px solid #f3f3f3;
    border-radius: 50%;
    border-top: 16px solid #3498db;
    width: 75px;
    height: 75px;
    -webkit-animation: spin 2s linear infinite;
    animation: spin 1s linear infinite;
    margin: auto;
    left: 0;
    right: 0;
    top: 0;
    bottom: 0;
    position: fixed;
    display: none;
  }

  @-webkit-keyframes spin {
    0% {
      -webkit-transform: rotate(0deg);
    }

    100% {
      -webkit-transform: rotate(360deg);
    }
  }

  @keyframes spin {
    0% {
      transform: rotate(0deg);
    }

    100% {
      transform: rotate(360deg);
    }
  }
</style>

<div class="loader"></div>
<div class="container ">

  <div class="float-left border" style=" height: 15em;">
    <h4 align="center">Instancias</h4>
    <select class="selectpicker " data-selected-text-format="count" required name="conns[]" multiple data-live-search="true" data-actions-box="true" id="conn">

      <?php foreach ($dbs as $index => $groups) { ?>
        <optgroup label="<?php echo $index ?>" data-group="<?php echo $index ?>">
          <?php foreach ($groups as $db) { ?>
            <option value="<?php echo $db->id ?>"><?php echo $db->db ?></option>
          <?php } ?>
        </optgroup>
      <?php } ?>
    </select>
    <br>
    <button data-toggle="modal" data-target="#modalGrupos" class="btn btn-primary ml-2 mt-2">Mostrar Grupos</button>
  </div>
  <br>
  <!-- Modal Grupos -->
  <div class="modal fade " id="modalGrupos" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Grupos de DB's</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <form id="formGroups">
            <label for="ids">Seleccione las DB's para agrupar
              <select class="selectpicker" required name="ids[]" multiple data-live-search="true" data-actions-box="true" id="ids">
                <?php foreach ($dbs as $index => $groups) { ?>
                  <optgroup label="<?php echo $index ?>" data-group="<?php echo $index ?>">
                    <?php foreach ($groups as $db) { ?>
                      <option value="<?php echo $db->id ?>"><?php echo $db->db ?></option>
                    <?php } ?>
                  </optgroup>
                <?php } ?>
              </select>
            </label>
            <input type="text" class=" form-control " id="new" placeholder="Nombre del Grupo" name="new">
          </form>
        </div>
        <div class="modal-footer">
          <button type="button" id="updateGoups" class="btn btn-primary">Actualizar</button>
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
        </div>
      </div>
    </div>
  </div>
  <!-- FIn Modal Grupos  -->

  <div class="float-right">
    <button class="btn btn-success " id="ejecutar">Ejecutar consulta</button>
    <br><br>
    <button class="btn btn-secondary" id="guardar">Guardar consulta</button>
    <br>
    <button class="btn btn-warning" data-toggle="modal" data-target="#modal-add-db">Agregar Base de Datos</button>
    <br>
    <br>
    <a href="<?php echo base_url('/Home/consultas') ?>" target="_blank">
      <button class="btn btn-primary">Ver Consultas Guardadas</button>
    </a>
  </div>
</div>
<div class="container" align="center">

  <textarea placeholder="Escriba la consulta" class="form-control bg-dark text-light " style=" width: 600px;height: 400px;" id="query"></textarea>
</div>
<br>


<div id="mensajes"></div>

<!-- MODALS -->
<button type="button" class="btn btn-primary" data-toggle="modal" data-target="#modalNotas">
  Notas
</button>

<!-- Modal -->
<div class="modal fade" id="modalNotas" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLongTitle">Instrucciones</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <p>Bienvenido al manejador de Bases de Datos multiples. En esta app, puedes correr la misma sentencia en multiples bases de datos a la vez.</p>
        <h4>Notas Rapidas:</h4>
        <h5>Comandos:</h5>
        <p> <strong>CTRL + ENTER</strong>: Ejecuta la consulta escrita en el area de texto (O la seleccion si existe alguna)</p>
        <h5>Funciones de mysql (Procedures, Triggers y demás):</h5>
        <p>Las consultas para crear funciones internas de MySQL, deben realizarse solas en el area de texto, o ejecutarse por separado seleccionandolas y tampoco deben incluir los DELIMITERS caracteristicos de la consola:(Siempre que se ejecute un CREATE, se va a eliminar el objeto anterior)</p>
        <code>
          <pre>
          DELIMITER //

          CREATE PROCEDURE GetAllProducts()
          BEGIN
            SELECT *  FROM products;
          END //

          DELIMITER ;
        </pre>
        </code>
        <p>Quedaría como:</p>
        <code>
          <pre>
          CREATE PROCEDURE GetAllProducts()
          BEGIN
            SELECT *  FROM products;
          END
        </pre>
        </code>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary">Save changes</button>
      </div>
    </div>
  </div>
</div>
<!-- MODAL NOTAS -->


<!-- END MODAL NOTAS -->
<!-- MODAL SAVE -->
<div id="modalSave" class="modal" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Guardar Consulta</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <form method="post" class="form-horizontal" id="form1">
          <div class="container">
            <div class="form-group row">
              <div class="col">
                <label class="text-muted" style="margin-top:10px;"> Guardar como: </label>

                <input type="text" required name="saveAs" id="saveAs" class="form-control" />

                <label class="text-muted" style="margin-top:10px;"> Descripcion: </label>

                <input type="text" required name="inputDetails" id="details" class="form-control" />

                <label class="text-muted" style="margin-top:10px;"> Fecha: </label>

                <input type="date" required name="fecha" id="date" placeholder="Fecha" class="form-control" />
              </div>
              <div class="col">
                <label class="text-muted" style="margin-left:2px; margin-top:10px;"> Consulta: </label>

                <textarea type="text" required name="queryInfo" id="queryInfo" class="form-control" style="height:300px; ">
		        		</textarea>
              </div>
            </div>
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" id="btnSaveQuery" class="btn btn-primary">Guardar</button>
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
      </div>
    </div>
  </div>
</div>
<!-- END MODAL SAVE -->
<!-- MODAL ADD DB -->
<div class="modal fade" tabindex="-1" role="dialog" id="modal-add-db">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Añadir DB</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <form id="formAddDb">

          <div class="row">
            <div class="col-md-4">
              <div class="form-group">
                <label for="exampleInputEmail1">Alias</label>
                <input type="text" class="form-control" name="db">
              </div>
            </div>
            <div class="col-md-4">
            <div class="form-group">
                <label for="exampleInputEmail1">Nombre de la DB</label>
                <input type="text" class="form-control" name="name">
              </div>            </div>
          </div>
          <div class="row">
            <div class="col-md-4">
            <div class="form-group">
                <label for="exampleInputEmail1">Usuario</label>
                <input type="text" class="form-control" name="user">
              </div>
            </div>
            <div class="col-md-4">
            <div class="form-group">
                <label for="exampleInputEmail1">Contraseña</label>
                <input type="password" class="form-control" name="pass">
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-md-4">
            <div class="form-group">
                <label for="exampleInputEmail1">Servidor(host)</label>
                <input type="text" class="form-control" name="host">
              </div>
            </div>
            <div class="col-md-4">
            <div class="form-group">
                <label for="exampleInputEmail1">Puerto</label>
                <input type="number" class="form-control" min="0" name="port" value="3306">
              </div>
            </div>
          </div>
          <br>
          <label>Grupo </label>
          <select name="group" id="select-group" class="selectpicker">
            <?php foreach ($dbs as $index => $groups) : ?>
              <option value="<?php echo $index ?>"><?php echo $index ?></option>
            <?php endforeach; ?>
          </select>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-primary" id="btnSubmitAdd">Guardar</button>
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>
<!-- END MODAL ADD DB -->

<script>
  $(document).ready(() => {
    $(".alert").hide();

  })



  $("textarea").keydown(function(e) {
    if (e.keyCode === 9) { // tab was pressed
      // get caret position/selection
      var start = this.selectionStart;
      var end = this.selectionEnd;

      var $this = $(this);
      var value = $this.val();

      // set textarea value to: text before caret + tab + text after caret
      $this.val(value.substring(0, start) +
        "\t" +
        value.substring(end));

      // put caret at right position again (add one for the tab)
      this.selectionStart = this.selectionEnd = start + 1;

      // prevent the focus lose
      e.preventDefault();
    }

    if (e.ctrlKey && e.keyCode == 13) {
      $('#ejecutar').trigger("click");

    }


  });

  $('#btnSubmitAdd').on('click',function(){
    var dataFake = $('#formAddDb').serializeArray(),data = {};
    for(let val of dataFake)data[val.name] = val.value;
    addDB(data);
    
  })
  function addDB(data) {
    $(`#conn > [data-group="${data.group}"]`).append(`<option value="${data.db}">${data.db}</option>`);
    $('.selectpicker').selectpicker('refresh');
    $.ajax({
      url: '<?php echo base_url('/Home/addDb')  ?>',
      method: 'post',
      data: data,
      dataType: 'json',
      success: function(data) {
        alert('Base de Datos ingresada con Exito');
        $('.modal').modal('hide')
        window.location.href = window.location.href;
      },

    })
  }

  $("#guardar").click(() => {
    $("#queryInfo").val($('#query').val());
    $('#modalSave').modal('show');
  })

  $("#btnSaveQuery").click(() => {
    let nombreConsulta = $("#saveAs").val(),
      descripcion = $("#details").val(),
      fecha = $("#date").val(),
      consulta = $("#queryInfo").val()
    if (nombreConsulta == '' || descripcion == '' || fecha == '' || consulta == '') {
      alert('Hay campos vacios.');
    } else {


      obj = {
        nombreConsulta: nombreConsulta,
        descripcion: descripcion,
        fecha: fecha,
        consulta: consulta
      };


      $.ajax({
        url: '<?php echo base_url('/Home/saveConsulta')  ?>',
        method: 'post',
        data: obj,
        dataType: 'json',
        success: function(data) {
          alert('¡Consulta guardada con exito!');
          $('#modalSave').modal('hide');
        }

      })
    }
  })

  $("#updateGoups").click(() => {
    let data = $("#formGroups").serializeArray();

    if ($("#new").val() == '') {
      alert('Hay campos vacios.');
      return
    }
    $.ajax({
      url: '<?php echo base_url('/Home/updateGroups')  ?>',
      method: 'post',
      data: data,
      dataType: 'json',
      success: function(data) {
        console.log(data);
        window.location.href = window.location.href;
      },
      error: function(data) {
        alert('Datos Actualizados')
        if (data.status === 200) window.location.href = window.location.href;
      }

    })

  })


  $('#ejecutar').on('click', () => {
    var select = isTextSelected($('#query')[0]);
    if ($('#query').val().trim() === "")
      return alert('Consulta Vacía');
    $('.loader').css('display', 'block');
    $('#mensajes').empty();
    var data = {
      id: $('#conn').val(),
      query: select ? select : $('#query').val()
    };
    $.ajax({
      url: '<?php echo base_url('/Home/ejecutar');  ?>',
      method: 'post',
      data: data,
      dataType: 'json',
      success: function(respuesta) {
        $('.loader').css('display', 'none');

        if (respuesta.errores !== null)
          respuesta.errores.forEach(error => {
            $('#mensajes').append(`
                        <div class="alert alert-danger error fade show" role="alert">
                                ${error}
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    </div>
                        `)
          });
        $(".error").show()


        if (respuesta.correcto != null && /<\/?[a-z][\s\S]*>/i.test(respuesta.correcto) != true) {

          $('#mensajes').append(`
                        <div class="alert alert-success completada fade show" role="alert">
                    Consulta completada con exito
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    </button>
                    </div>`);
          $(".completada").show()
          var duration = 2000; //2 seconds
          setTimeout(function() {
            $('.completada').alert('close');
          }, duration);
        } else {
          $('#mensajes').append(respuesta.correcto);

        }


      },
      error: function(data) {
        $('.loader').css('display', 'none');
        console.log(data);
      }
    })

  });

  function isTextSelected(input) {
    var startPos = input.selectionStart;
    var endPos = input.selectionEnd;

    var selObj = document.getSelection();
    var selectedText = selObj.toString();

    if (selectedText.length != 0) {
      input.focus();
      input.setSelectionRange(startPos, endPos);
      return selectedText;
    } else if (input.value.substring(startPos, endPos).length != 0) {
      input.focus();
      input.setSelectionRange(startPos, endPos);
      return selectedText;
    }
    return false;
  }
</script>
<?php $this->endSection() ?>