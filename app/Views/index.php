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
        <select class="selectpicker " required name="conns[]" multiple data-live-search="true" data-actions-box="true" id="conn">

          <?php foreach ($dbs as $index => $groups) { ?>
            <optgroup label="<?php echo $index ?>">
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
                  <select class="selectpicker " required name="ids[]" multiple data-live-search="true" data-actions-box="true" id="ids">
                    <?php foreach ($dbs as $index => $groups) { ?>
                      <optgroup label="<?php echo $index ?>">
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
            <form  method="post" class="form-horizontal" id="form1">
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
        $('.loader').css('display', 'block');
        $('#mensajes').empty();
        var data = {
          id: $('#conn').val(),
          query: $('#query').val()
        };
        $.ajax({
          url: '<?php echo base_url('/Home/ejecutar');  ?>',
          method: 'post',
          data: data,
          dataType: 'json',
          success: function(respuesta) {
            $('.loader').css('display', 'none');

            if(respuesta.errores  !== null )
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
    </script>
 <?php $this->endSection() ?>
