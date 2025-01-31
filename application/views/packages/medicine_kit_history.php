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
// <?php if(in_array('571',$users_data['permission']['action'])): ?>
$(document).ready(function() { 
     //////IPD table Datatable Calls//////////
    ipd_table = $('#ipd_table').DataTable({  
        "processing": true, 
        "serverSide": true, 
        "order": [], 
        "pageLength": '20',
        "ajax": {
            "url": "<?php echo base_url('packages/medicine_kit_history_ajax_list')?>",
            "type": "POST",
            "data":{'opt_id':'1'},
            
        }, 
        "columnDefs": [
        { 
            "targets": [ 0 , -1 ], //last column
            "orderable": false, //set not orderable

        },
        ],

    });

    //////OPD table Datatable Calls//////////
    opd_table = $('#opd_table').DataTable({  
        "processing": true, 
        "serverSide": true, 
        "order": [], 
        "pageLength": '20',
        "ajax": {
            "url": "<?php echo base_url('packages/medicine_kit_history_ajax_list')?>",
            "type": "POST",
            "data":{'opt_id':'2'},
            
        }, 
        "columnDefs": [
        { 
            "targets": [ 0 , -1 ], //last column
            "orderable": false, //set not orderable

        },
        ],

    });

    //////Medicine Kit Allot table Datatable Calls//////////
    medicine_kit_allot = $('#medicine_kit_allot').DataTable({  
        "processing": true, 
        "serverSide": true, 
        "order": [], 
        "pageLength": '20',
        "ajax": {
            "url": "<?php echo base_url('packages/medicine_kit_history_ajax_list')?>",
            "type": "POST",
            "data":{'opt_id':'3'},
            
        }, 
        "columnDefs": [
        { 
            "targets": [ 0 , -1 ], //last column
            "orderable": false, //set not orderable

        },
        ],

    });
});

// <?php endif;?>
 



$(document).ready(function(){
var $modal = $('#load_add_elem_temp_modal_popup');
$('#modal_add').on('click', function(){
$modal.load('<?php echo base_url().'packages/add/' ?>',
{
  //'id1': '1',
  //'id2': '2'
  },
function(){
$modal.modal('show');
});

});

});

function edit_packages(id)
{

    window.location.href="<?php echo base_url().'packages/edit/' ?>"+id;
}

function view_packages(id)
{
  var $modal = $('#load_add_elem_temp_modal_popup');
  $modal.load('<?php echo base_url().'packages/view/' ?>'+id,
  {
    //'id1': '1',
    //'id2': '2'
    },
  function(){
  $modal.modal('show');
  });
}
function sort_test_master(id,value){

    if(id!=''){
        $.post('<?php echo base_url('packages/save_sort_order_data/'); ?>',{'test_id':id,'sort_order_value':value},function(result){
            if(result!=''){
                reload_table();
            }

        })
    }
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
                      url: "<?php echo base_url('packages/deleteall');?>",
                      data: {row_id: allVals},
                      success: function(result) 
                      {
                            flash_session_msg(result);
                            reload_table();  
                      }
                  });
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
<!-- ============================= Main content start here ===================================== -->
<section class="userlist">
    <div class="userlist-box">
    	   

    <!-- // -->
      <div class="row m-b-5">
           <div class="col-md-12">
                <div class="row">
                    <div class="col-md-6">
                         <input type="checkbox"  name="opt_type" id="opt_type" onclick="return get_medicine_kit_history()" value="1"> OPD
                       <input type="checkbox" name="opt_type" id="opt_type" onclick="return get_medicine_kit_history()" value="2"> Alocated Kit
                      
                    </div> <!-- 6 -->
                    <div class="col-md-6 text-right">
                          
                     </div> <!-- 6 -->
                </div> <!-- innerRow -->
           </div> <!-- 12 -->
      </div> <!-- row -->



    <form>
       <?php if(in_array('571',$users_data['permission']['action'])):?>
       <!-- bootstrap data table -->
        <table id="ipd_table" class="table table-striped table-bordered packages_list" cellspacing="0" width="100%">
            <thead class="bg-theme">
                <tr>
                    <th width="40" align="center"> <input type="checkbox" name="selectall" class="" id="selectAll" value=""> </th> 
                    <th> IPD Booking No  </th>
                    <th> Patient Code </th> 
                    <th> Patient Name </th> 
                    <th>Package Name</th>
                     <th>Quantity</th>
                    <th> Created Date</th>
                    <th>Action</th>
                  
                </tr>
            </thead>  
        </table>
        <?php endif; ?>
        <table id="opd_table" class="table table-striped table-bordered packages_list" cellspacing="0" width="100%">
            <thead class="bg-theme">
                <tr>
                    <th width="40" align="center"> <input type="checkbox" name="selectall" class="" id="selectAll" value=""> </th> 
                    <th> IPD Booking No  </th>
                    <th> Patient Code </th> 
                    <th> Patient Name </th> 
                    <th> Package Name</th>
                    <th>Quantity</th>
                    <th> Created Date</th>
                   
                  
                </tr>
            </thead>  
        </table>
        <table id="medicine_kit_allot" class="table table-striped table-bordered packages_list" cellspacing="0" width="100%">
            <thead class="bg-theme">
                <tr>
                    <th width="40" align="center"> <input type="checkbox" name="selectall" class="" id="selectAll" value=""> </th> 
                    <th> S.No.  </th>
                    <th> To Branch </th> 
                    <th> Package Name </th> 
                   
                    <th>Quantity</th>
                    <th> Created Date</th>
                   
                  
                </tr>
            </thead>  
        </table>
    </form>
   </div> <!-- close -->
  	<div class="userlist-right">
  		<div class="btns">
             
         <?php
                   if(in_array('572',$users_data['permission']['action'])) {
          ?>              <button class="btn-update" id="modal_addd" onclick="window.location.href='<?php echo base_url('packages/add'); ?>'"> <i class="fa fa-plus"></i> New </button>
          <?php    }
                
          ?>
          
          <?php if(in_array('574',$users_data['permission']['action'])) {
          ?>
  			<button class="btn-update" id="deleteAll" onclick="return checkboxValues();">
  				<i class="fa fa-trash"></i> Delete
  			</button>
          <?php } ?>
          <?php if(in_array('571',$users_data['permission']['action'])) {
          ?>
               <button class="btn-update" onclick="reload_table()">
                    <i class="fa fa-refresh"></i> Reload
               </button>
          <?php } ?>
          <?php if(in_array('575',$users_data['permission']['action'])) {
          ?>
  			<button class="btn-exit" onclick="window.location.href='<?php echo base_url('packages/archive'); ?>'">
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
<?php
 $flash_success = $this->session->flashdata('success');

 if(isset($flash_success) && !empty($flash_success))
 {
   echo 'flash_session_msg("'.$flash_success.'");';
 }
 ?>  
 function load_allot_model()
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
     var $modal = $('#load_allot_to_branch_modal_popup');
     $modal.load('<?php echo base_url('packages/kit_allot_to_branch/'); ?>',{'medicine_kit_ids':allVals},function(){
          $modal.modal('show');
     });

   
}
function get_medicine_kit_history(){
    var opt_type = $('input[name="opt_type"]:checked');
    
}
 function delete_packages(rate_id)
 {    
    $('#confirm').modal({
      backdrop: 'static',
      keyboard: false
    })
    .one('click', '#delete', function(e)
    { 
        $.ajax({
                 url: "<?php echo base_url('packages/delete/'); ?>"+rate_id, 
                 success: function(result)
                 {
                    flash_session_msg(result);
                    reload_table(); 
                 }
              });
    });     
 }

</script> 
<!-- Confirmation Box -->

    <div id="confirm" class="modal fade dlt-modal">
      <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-theme"><h4>Are You Sure?</h4></div>
          <!-- <div class="modal-body"></div> -->
          <div class="modal-footer">
            <button type="button" data-dismiss="modal" class="btn-update" id="delete">Confirm</button>
            <button type="button" data-dismiss="modal" class="btn-cancel">Cancel</button>
          </div>
        </div>
      </div>  
    </div> <!-- modal -->

<!-- Confirmation Box end -->
<div id="load_add_elem_temp_modal_popup" class="modal fade " role="dialog" data-backdrop="static" data-keyboard="false"></div>
<div id="load_allot_to_branch_modal_popup" class="modal fade" role="dialog" data-backdrop="static" data-keyboard="false"></div>

</div><!--container-fluid-->
</body>
</html>