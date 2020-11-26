<?php $this->extend('layout'); ?>

<?php $this->section('content'); ?>
<a href="<?php echo base_url() ?>">
<button class="btn btn-success float-right">Volver al Gestor</button>
</a> 
<table class="table text-dark" id="tabla">
<thead>
    <tr>
    <th>Id</th>
    <th>Nombre</th>
    <th>Descripcion</th>
    <th>Fecha</th>
    </tr>
</thead>
</table> 




<div id ="modalSave" class="modal" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Guardar Consulta</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <form  method = "post" class = "form-horizontal" id="form1">
        	<div class = "container">
	        	<div class="form-group row">
					<div class ="col">
						<label class = "text-muted" 
						style="margin-top:10px;"> Guardar como: </label>		
						
		        		<input type="text" required name="saveAs" id = "saveAs" 
		        		class ="form-control"/>

			      		<label class = "text-muted" 
						style="margin-top:10px;"> Descripcion: </label>		
						
		        		<input type="text" required name="inputDetails" id = "details" 
		        		class ="form-control"/>
						
						<label class = "text-muted" 
						style="margin-top:10px;"> Fecha: </label>

		        		<input type="date" required name="fecha" id = "date" placeholder="Fecha" 
		        		class ="form-control"/>	
                        <input type="hidden" id="consulta_id">
					</div>
					<div class = "col">
		        		<label class = "text-muted" 
						style="margin-left:2px; margin-top:10px;"> Consulta: </label>		
						
                        
		        		<textarea type="text" required name="queryInfo" id = "queryInfo" 
		        		class ="form-control"
		        		style="height:300px; ">
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

<div id="porta" ></div>
<script>
var consultas;
$(document).ready(function(){
    $(document).on('click','.update',editar);
    $(document).on('click','.copy',copiar);

    tabla();        
            
})

function tabla(){
  $('#tabla').DataTable().destroy();
    $.ajax({
            url:'<?php echo base_url('/Home/getConsultas')  ?>',
            method: 'get',
           async: false,
            success: function(response){
                consultas =JSON.parse(response);
                $('#tabla').DataTable( {
                data: consultas,
                columns: [
                    { data: 'id' },
                    { data: 'nombreConsulta' },
                    { data: 'descripcion' },
                    { data: 'id',render:function ( data ) {
                    return '<button class="update btn btn-primary" value="'+data+'">Editar</button> <button class="copy btn btn-secondary" value="'+data+'">Copiar</button>';
                    } 
                    },
                ]
            } );
            }
           
            })
}
function copiar() {
    var id = $(this).val()-1;
    let consulta = consultas[id];
    var dummy = $('<input>').val(consulta.consulta).appendTo('#porta').select()
    document.execCommand('copy');
    $('#porta').empty();
  alert("Consulta copiada al portapapeles.");
}
function editar(){
    var id = $(this).val()-1;
    console.log(id);
    let consulta = consultas[id];
    $("#saveAs").val(consulta.nombreConsulta);
    $("#details").val(consulta.descripcion);
    $("#date").val(consulta.fecha);
    $("#queryInfo").val(consulta.consulta);
    $("#consulta_id").val(consulta.id);
    $('#modalSave').modal('show');

}



$("#btnSaveQuery").click(()=>{
    let nombreConsulta  =   $("#saveAs").val(),
        descripcion     =   $("#details").val(),
        fecha     =   $("#date").val(),
        consulta     =   $("#queryInfo").val(),
        id     =   $("#consulta_id").val();
        if(nombreConsulta =='' || descripcion =='' ||fecha =='' ||consulta =='' ){
            alert('Hay campos vacios.');
        }else {

        
        obj = {
            nombreConsulta  : nombreConsulta,
            descripcion     : descripcion,
            fecha           : fecha,
            consulta        : consulta,
            id              : id
        };
  
    $.ajax({
            url:'<?php echo base_url()  ?>/Home/updateConsulta',
            method: 'post',
            data: obj,
            dataType: 'json',
            success: function(data){
             tabla();
             alert('Actualizado correctamente');
             $('#modalSave').modal('hide');

            }
        
            })
        }
})


$("textarea").keydown(function(e) {
    if(e.keyCode === 9) { // tab was pressed
        // get caret position/selection
        var start = this.selectionStart;
        var end = this.selectionEnd;

        var $this = $(this);
        var value = $this.val();

        // set textarea value to: text before caret + tab + text after caret
        $this.val(value.substring(0, start)
                    + "\t"
                    + value.substring(end));

        // put caret at right position again (add one for the tab)
        this.selectionStart = this.selectionEnd = start + 1;

        // prevent the focus lose
        e.preventDefault();
    }
});
</script>

<?php $this->endSection() ?>
