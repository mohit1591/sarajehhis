<?php
$users_data = $this->session->userdata('auth_users');
?>
<!DOCTYPE html>
<html>
<head>
<title><?php echo $page_title.PAGE_TITLE; ?></title>
<meta name="viewport" content="width=1024">


<!-- bootstrap -->
<link rel="stylesheet" type="text/css" href="<?php echo ROOT_CSS_PATH; ?>bootstrap.min.css">
<link rel="stylesheet" type="text/css" href="<?php echo ROOT_CSS_PATH; ?>dataTables.bootstrap.min.css">
<link rel="stylesheet" type="text/css" href="<?php echo ROOT_CSS_PATH; ?>bootstrap-datatable.min.css">
<link rel="stylesheet" type="text/css" href="<?php echo ROOT_CSS_PATH; ?>font-awesome.min.css">

<!-- links -->
<link rel="stylesheet" type="text/css" href="<?php echo ROOT_CSS_PATH; ?>my_layout.css">
<link rel="stylesheet" type="text/css" href="<?php echo ROOT_CSS_PATH; ?>menu_style.css">
<link rel="stylesheet" type="text/css" href="<?php echo ROOT_CSS_PATH; ?>menu_for_all.css">
<link rel="stylesheet" type="text/css" href="<?php echo ROOT_CSS_PATH; ?>withoutresponsive.css">

<!-- js -->
<script type="text/javascript" src="<?php echo ROOT_JS_PATH; ?>jquery.min.js"></script>
<script type="text/javascript" src="<?php echo ROOT_JS_PATH; ?>bootstrap.min.js"></script> 

<!-- datatable js -->
<script src="<?php echo ROOT_JS_PATH; ?>jquery.dataTables.min.js"></script>
<script src="<?php echo ROOT_JS_PATH; ?>dataTables.bootstrap.min.js"></script>
<script type="text/javascript">
var save_method; 
var table;
<?php
//if(in_array('509',$users_data['permission']['action'])){
if(in_array('509',$users_data['permission']['action']) || in_array('121',$users_data['permission']['section'])){
?>
$(document).ready(function() { 
    table = $('#table').DataTable({  
        "processing": true, 
        "serverSide": true, 
        "order": [], 
        "pageLength": '20',
        "ajax": {
            "url": "<?php echo base_url('admissionnotes/suggestion/ajax_list')?>",
            "type": "POST",
        }, 
        "columnDefs": [
        { 
            "targets": [ 0 , -1 ], //last column
            "orderable": false, //set not orderable

        },
        ],

    });
}); 
<?php } ?>


$(document).ready(function(){
var $modal = $('#load_add_suggestion_modal_popup');
$('#modal_add').on('click', function(){
$modal.load('<?php echo base_url().'admissionnotes/suggestion/add/' ?>',
{
  //'id1': '1',
  //'id2': '2'
  },
function(){
$modal.modal('show');
});

});

});

function edit_suggestion(id)
{
  var $modal = $('#load_add_suggestion_modal_popup');
  $modal.load('<?php echo base_url().'admissionnotes/suggestion/edit/' ?>'+id,
  {
    //'id1': '1',
    //'id2': '2'
    },
  function(){
  $modal.modal('show');
  });
}

function view_suggestion(id)
{
  var $modal = $('#load_add_ssuggestion_modal_popup');
  $modal.load('<?php echo base_url().'admissionnotes/suggestion/view/' ?>'+id,
  {
    //'id1': '1',
    //'id2': '2'
    },
  function(){
  $modal.modal('show');
  });
}

function reload_table()
{
    table.ajax.reload(null,false); //reload datatable ajax 
}


 
function checkboxValues() 
{         
    $('#table').dataTable();
     var allVals = [];
     $(':checkbox').each(function() 
     {
       if($(this).prop('checked')==true)
       {
            allVals.push($(this).val());
       } 
     });
     allbranch_delete(allVals);
}

function allbranch_delete(allVals)
 {    
   if(allVals!="")
   {
       $('#confirm').modal({
      backdrop: 'static',
      keyboard: false
        })
        .one('click', '#delete', function(e)
        {
            $.ajax({
                      type: "POST",
                      url: "<?php echo base_url('admissionnotes/suggestion/deleteall');?>",
                      data: {row_id: allVals},
                      success: function(result) 
                      {
                            flash_session_msg(result);
                            reload_table();  
                      }
                  });
        });
   } 
    else{
      $('#confirm-select').modal({
          backdrop: 'static',
          keyboard: false
        });
   }
 }
</script>

</head>

<body>


<div class="container-fluid">
 <?php
  $this->load->view('include/header');
  $this->load->view('include/inner_header');
 ?>

<section class="userlist">
    
<?php
$this->load->view('admissionnotes/common');
?>
              
                    
                    
    
    <div class="userlist-box">
    	 
    <form>
       <?php //if(in_array('509',$users_data['permission']['action'])) {
        if(in_array('509',$users_data['permission']['action']) || in_array('121',$users_data['permission']['section'])){
       ?>
       <!-- bootstrap data table -->
        <table id="table" class="table table-striped table-bordered suggestion_list" cellspacing="0" width="100%">
            <thead>
                <tr>
                    <th width="40" align="center"> <input type="checkbox" name="selectall" class="" id="selectAll" value=""> </th> 
                    <th> Suggestion </th> 
                    <th> Status </th> 
                    <th> Action </th>
                </tr>
            </thead>  
        </table>
        <?php } ?>
    </form>
   </div> <!-- close -->
  	<div class="userlist-right">
  		<div class="btns">
          <?php //if(in_array('510',$users_data['permission']['action'])) {
            if(in_array('510',$users_data['permission']['action']) || in_array('121',$users_data['permission']['section'])){
          ?>
  			<button class="btn-update" id="modal_add">
  				<i class="fa fa-plus"></i> New
  			</button>
          <?php } ?>
          <?php //if(in_array('512',$users_data['permission']['action'])) {
            if(in_array('512',$users_data['permission']['action']) || in_array('121',$users_data['permission']['section'])){
          ?>
  			<button class="btn-update" id="deleteAll" onclick="return checkboxValues();">
  				<i class="fa fa-trash"></i> Delete
  			</button>
          <?php } ?>
          <?php //if(in_array('509',$users_data['permission']['action'])) {
            if(in_array('509',$users_data['permission']['action']) || in_array('121',$users_data['permission']['section'])){
          ?>
               <button class="btn-update" onclick="reload_table()">
                    <i class="fa fa-refresh"></i> Reload
               </button>
          <?php } ?>
          <?php //if(in_array('513',$users_data['permission']['action'])) {
            if(in_array('513',$users_data['permission']['action']) || in_array('121',$users_data['permission']['section'])){
          ?>
  			<button class="btn-exit" onclick="window.location.href='<?php echo base_url('admissionnotes/suggestion/archive'); ?>'">
  				<i class="fa fa-archive"></i> Archive
  			</button>
          <?php } ?>
        <button class="btn-exit" onclick="window.location.href='<?php echo base_url(); ?>'">
          <i class="fa fa-sign-out"></i> Exit
        </button>
  		</div>
  	</div> 
  	<!-- right -->
 
  <!-- cbranch-rslt close -->
 
</section> <!-- cbranch -->
<?php
$this->load->view('include/footer');
?>

<script>  
 function delete_suggestion(rate_id)
 {    
    $('#confirm').modal({
      backdrop: 'static',
      keyboard: false
    })
    .one('click', '#delete', function(e)
    { 
        $.ajax({
                 url: "<?php echo base_url('admissionnotes/suggestion/delete/'); ?>"+rate_id, 
                 success: function(result)
                 {
                    flash_session_msg(result);
                    reload_table(); 
                 }
              });
    });     
 }
$(document).ready(function() {
   $('#load_add_suggestion_modal_popup').on('shown.bs.modal', function(e) {
      $('.inputFocus').focus();
   })
});  
</script> 
<!-- Confirmation Box -->

    <div id="confirm" class="modal fade dlt-modal">
      <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-theme"><h4>Are You Sure?</h4></div>
         <div class="modal-body" style="font-size:8px;">*Data that have been in Archive more than 60 days will be automatically deleted.</div> 
          <div class="modal-footer">
            <button type="button" data-dismiss="modal" class="btn-update" id="delete">Confirm</button>
            <button type="button" data-dismiss="modal" class="btn-cancel">Close</button>
          </div>
        </div>
      </div>  
    </div> <!-- modal -->
<div id="confirm-select" class="modal fade dlt-modal">
      <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-theme"><h4>Please select at-least one record.</h4></div>
          <!-- <div class="modal-body"></div> -->
          <div class="modal-footer">
            <button type="button" data-dismiss="modal" class="btn-cancel">Close</button>
          </div>
        </div>
      </div>  
    </div> <!-- modal --> 
<!-- Confirmation Box end -->
<div id="load_add_suggestion_modal_popup" class="modal fade" role="dialog" data-backdrop="static" data-keyboard="false"></div>
</div> <!-- container_fluid -->
</body>
</html>