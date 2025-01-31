<?php
$users_data = $this->session->userdata('auth_users');

?>
<link rel="stylesheet" type="text/css" href="<?php echo ROOT_CSS_PATH; ?>bootstrap-datepicker.css">
<script type="text/javascript" src="<?php echo ROOT_JS_PATH; ?>bootstrap-datepicker.js"></script>
<div class="modal-dialog emp-add-add">
<div class="overlay-loader" id="overlay-loader">
        <img src="<?php echo ROOT_IMAGES_PATH; ?>loader.gif" class="aj-loader">
    </div>
  <div class="modal-content"> 
  <form  id="relation" class="form-inline">

      <div class="modal-header">
          <button type="button" class="close"  data-number="1" aria-label="Close"><span aria-hidden="true">×</span></button>
          <h4><?php echo $page_title; ?></h4> 
      </div>
      
     <div class="modal-body">   
          <div class="row">
               <div class="col-md-12">
                    <div class="row m-b-5">
                         <div class="col-md-5">
                              <label><b>From Date:</b></label>
                         </div>
                         <div class="col-md-7">
                              <input type="text" id="exp_start_date" name="from_date" class="datepicker start_datepicker" value="<?php echo date('d-m-Y'); ?>" onblur="reset_search_value()">
                         </div>
                    </div>
                    <div class="row">
                         <div class="col-md-5">      
                              <label><b>To Date:</b></label>
                         </div>
                         <div class="col-md-7">
                              <input type="text" name="to_date" id="exp_end_date" class="datepicker datepicker_to end_datepicker" value="<?php echo date('d-m-Y'); ?>" onblur="reset_search_value()">
                         </div>
                    </div>
                     <div class="row m-t-5">
                         <div class="col-md-5">      
                              <label><b>Department:</b></label>
                         </div>
                         <div class="col-md-7">
                          <select name="department_type" id="department_type">
                                  <option value="">Select Department</option>
                                    <?php  foreach($dept_list as $key=>$dept){?>
                                  <option value="<?php echo $key;  ?>"><?php echo $dept;?></option>
                                     <?php }?>
                                </select>
                         </div>
                    </div>
                         
               </div> <!-- 12 -->
          </div> <!-- row -->  
         
      </div> <!-- modal-body --> 

      <div class="modal-footer"> 
         <?php if(in_array('252',$users_data['permission']['action'])) {
         ?>
              <input type="button"  class="btn-update" onClick="return expenses_report()" name="submit" value="Print" />
          <?php } ?>
         <button type="button" class="btn-cancel" data-number="1">Close</button>
      </div>
</form>     

<script type="text/javascript">

$('.start_datepicker').datepicker({
    format: 'dd-mm-yyyy', 
    autoclose: true, 
    endDate : new Date(), 
  }).on("change", function(selectedDate) 
  { 
      var start_data = $('.start_datepicker').val();
      $('.end_datepicker').datepicker('setStartDate', start_data); 
      var start_date=  $("#exp_start_date").val();
    var end_date=  $("#exp_end_date").val();
    $("#exp_end_date").datepicker('setStartDate',start_date); 
    $("#exp_end_date").datepicker({ minDate: selectedDate });
  });

  $('.end_datepicker').datepicker({
    format: 'dd-mm-yyyy',     
    autoclose: true,  
  }).on("change", function(selectedDate) 
  {   
     var start_date=  $("#exp_start_date").val();
    var end_date=  $("#exp_end_date").val();
    $("#exp_end_date").datepicker('setStartDate',start_date); 
    $("#exp_end_date").datepicker({ minDate: selectedDate });
  }); 
<?php $branch_id_n =  $this->session->userdata('sub_branches_data'); $users_data = $this->session->userdata('auth_users');  ?>
function expenses_report()
{ 
    var start_date = $('#exp_start_date').val();
    var end_date = $('#exp_end_date').val(); 
    var department_type = $('#department_type').val(); 
    window.open('<?php echo base_url('reports/module_profit_loss_reports?') ?>start_date='+start_date+'&end_date='+end_date+'&module='+department_type,'mywin','width=800,height=600');
}  

 var count = 0; 
 function reset_search_value(){
     $("#print_opt").val('');
     
 }
 function reset_date_search(){
     $("#exp_start_date").val('');
     $("#exp_end_date").val('');
 }
 $("button[data-number=1]").click(function(){
    $('#load_add_collection_modal_popup').modal('hide');
});
</script>
</div><!-- /.modal-content -->
    
</div><!-- /.modal-dialog -->
<div id="load_add_collection_modal_popup" class="modal fade" role="dialog" data-backdrop="static" data-keyboard="false"></div>