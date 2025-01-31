<div class="modal-dialog emp-add-add">
<div class="overlay-loader" id="overlay-loader">
        <img src="<?php echo ROOT_IMAGES_PATH; ?>loader.gif" class="aj-loader">
    </div>
  <div class="modal-content"> 
  <form  id="medicine_kit_add" class="form-inline">
  <input type="hidden" name="data_id" id="type_id" value="<?php echo $form_data['data_id']; ?>" /> 
  <input type="hidden" name="opt" id="opt" value="<?php echo $form_data['opt']; ?>" /> 
  <input type="hidden" name="row_id" id="row_id" value="<?php echo $form_data['row_id']; ?>" />
      <div class="modal-header">
          <button type="button" class="close"  data-number="1" aria-label="Close"><span aria-hidden="true">×</span></button>
          <h4><?php echo $page_title; ?></h4> 
      </div>
      
      <div class="modal-body">   
          <div class="row">
            <div class="col-md-12 m-b1">
              <div class="row">
                <div class="col-md-4">
                    <label>Vaccination Kit Name<span class="star">*</span></label>
                </div>
                <div class="col-md-8">
                    <input type="text" name="vaccination_kit_name"  value="<?php echo $form_data['vaccination_kit_name']; ?>">
                    
                    <?php if(!empty($form_error)){ echo form_error('vaccination_kit_name'); } ?>
                </div>
              </div> <!-- innerrow -->
              <div class="row">
                <div class="col-md-4">
                    <label>Kit Quantity<span class="star">*</span></label>
                </div>
                <div class="col-md-8">
                    <input type="text" name="quantity" class="numeric" value="">
                    
                    <?php if(!empty($form_error)){ echo form_error('quantity'); } ?>
                </div>
              </div> <!-- innerrow -->
            </div> <!-- 12 -->
          </div> <!-- row -->  
		
      </div> <!-- modal-body --> 

      <div class="modal-footer"> 
         <input type="submit"  class="btn-update" name="submit" value="Save" />
         <button type="button" class="btn-cancel" data-number="1">Close</button>
      </div>
</form>     

<script>  

function isNumberKey(evt) {
    var charCode = (evt.which) ? evt.which : event.keyCode;
    if (charCode != 46 && charCode > 31 && (charCode < 48 || charCode > 57)) {
        return false;
    } else {
        return true;
    }      
}
 
$("#medicine_kit_add").on("submit", function(event) { 
  event.preventDefault(); 
  $('#overlay-loader').show();
  var ids = $('#type_id').val();
  var opt = $('#opt').val();
  var row_id = $('#row_id').val();
  // if(ids!="" && !isNaN(ids))
  // { 
    var path = '/'+ids+'/'+opt+'/'+row_id;
    var msg = 'Vaccination Kit Quantity successfully Add.';
  // }
  // else
  // {
  //   var path = 'add/';
  //   var msg = 'quantity_add successfully created.';
  // } 
  //alert('ddd');return false;
  $.ajax({
    url: "<?php echo base_url('packages_vaccination/add_vaccination_kit_quantity'); ?>"+path,
    type: "post",
    data: $(this).serialize(),
    success: function(result) {
      if(result==1)
      {
        $('#load_add_quantity_add_modal_popup').modal('hide');
        flash_session_msg(msg);
        // get_quantity_add();
        if(opt=='add'){
           reload_table();
        }
        else if(opt=='edit'){
            window.location.href="<?php echo base_url(); ?>packages_vaccination/add_vaccination_kit_qty_manage/"+ids;
        }
      } 
      else
      {
        $("#load_add_quantity_add_modal_popup").html(result);
      }       
      $('#overlay-loader').hide();
    }
  });
}); 

$("button[data-number=1]").click(function(){
    $('#load_add_quantity_add_modal_popup').modal('hide');
});

// function get_quantity_add()
// {
//    $.ajax({url: "<?php echo base_url(); ?>quantity_add/quantity_add_dropdown/", 
//     success: function(result)
//     {
//       $('#quantity_add_id').html(result); 
//     } 
//   });
// }
</script>  
<!-- Delete confirmation box -->  
<!-- Delete confirmation end --> 
        </div><!-- /.modal-content -->
    
</div><!-- /.modal-dialog -->