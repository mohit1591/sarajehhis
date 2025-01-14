<!DOCTYPE html>
<html>
<head>
<title><?php echo $page_title.PAGE_TITLE; 
$users_data = $this->session->userdata('auth_users');
?></title>
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
<script type="text/javascript" src="<?php echo ROOT_JS_PATH; ?>validation.js"></script>
<!-- datatable js -->
<script src="<?php echo ROOT_JS_PATH; ?>jquery.dataTables.min.js"></script>
<script src="<?php echo ROOT_JS_PATH; ?>dataTables.bootstrap.min.js"></script>
<style type="text/css">
.update_data
{
  float: left;
  display: block;
  margin-left: 10px;
  color: green;
}  
</style>
<script type="text/javascript">
var save_method; 
var table; 
<?php if(in_array('855',$users_data['permission']['action'])): ?>
     $(document).ready(function() { 
            table = $('#table').DataTable({  
                  "processing": true, 
				  "serverSide": true, 
				  "retrieve" : true,
				  "order": [], 
				  "pageLength": 20,
                  "ajax": {
                        "url": "<?php echo base_url('test_master/ajax_list')?>",
                        "type": "POST",
                        "data":function(d){
                              d.branch_id =  $("#sub_branch_id :selected").val();
                              d.dept_id =  $("#dept_id :selected").val();
                              d.test_head =  $("#test_head :selected").val();
                              return d;
                        },
						
                  }, 
                  "columnDefs": [{ 
                        "targets": [ 0 , -1 ], //last column
                        "orderable": false, //set not orderable
                  }]
            });
     });  
<?php endif; ?>
$(document).ready(function(){
      var $modal = $('#load_add_unit_modal_popup');
      $('#modal_add').on('click', function(){
            $modal.load('<?php echo base_url().'test_master/add/' ?>',
                  {
                  //'id1': '1',
                  //'id2': '2'
                  },
                  function(){
                        $modal.modal('show');
                  }
            );
      });
     // check when users select inherit from branch list
      var selctedBranchOpt = $('#sub_branch_id :selected').val();
      if(selctedBranchOpt=='inherit'){
        <?php if(in_array('856',$users_data['permission']['action'])): ?>
          document.getElementById("btn-add").style.display="none";
        <?php endif;?>  
        <?php if(in_array('858',$users_data['permission']['action'])): ?>  
          document.getElementById("deleteAll").style.display="none";
        <?php endif;?>  
        <?php if(in_array('859',$users_data['permission']['action'])): ?>  
          document.getElementById("archive").style.display="none";
        <?php endif;?>  
        <?php if(in_array('862',$users_data['permission']['action'])): ?>  
          document.getElementById("downloadAll").style.display="block";
        <?php endif;?>  
      
      }else{
        <?php if(in_array('856',$users_data['permission']['action'])): ?>
           document.getElementById("btn-add").style.display="block";
        <?php endif;?>   
        <?php if(in_array('858',$users_data['permission']['action'])): ?>
          document.getElementById("deleteAll").style.display="block";
        <?php endif;?>  
        <?php if(in_array('859',$users_data['permission']['action'])): ?>  
          document.getElementById("archive").style.display="block";
        <?php endif;?>  
        <?php if(in_array('862',$users_data['permission']['action'])): ?>  
          document.getElementById("downloadAll").style.display="none";
        <?php endif;?>  
      }

});

function edit_unit(id)
{
  var $modal = $('#load_add_unit_modal_popup');
  $modal.load('<?php echo base_url().'test_master/edit/' ?>'+id,
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
        $.post('<?php echo base_url('test_master/save_sort_order_data/'); ?>',{'test_id':id,'sort_order_value':value},function(result){
            //if(result!=''){
                reload_table();
            //}

        })
    }
}
function view_unit(id)
{
  var $modal = $('#load_add_unit_modal_popup');
  $modal.load('<?php echo base_url().'test_master/view/' ?>'+id,
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
function downloadcheckboxValues() 
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
     allbranch_test_downloads(allVals);
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
                      url: "<?php echo base_url('test_master/deleteall');?>",
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

 function delete_test(test_id)
 {    
    $('#confirm').modal({
      backdrop: 'static',
      keyboard: false
    })
    .one('click', '#delete', function(e)
    { 
        $.ajax({
                 url: "<?php echo base_url('test_master/delete/'); ?>"+test_id, 
                 success: function(result)
                 {
                    flash_session_msg(result);
                    reload_table(); 
                 }
              });
    });     
 }


function download_test(tid)
{ 
  if(tid>0)
  {
      $.ajax({
          url: "<?php echo base_url('test_master/download_test/'); ?>"+tid, 
          success: function(result)
          { 
            flash_session_msg(result);
            reload_table(); 
          }
      });
  }
}


function allbranch_test_downloads(allVals)
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
                      url: "<?php echo base_url('test_master/downloadall');?>",
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
<!-- ============================= Main content start here ===================================== -->
<section class="userlist">
    <div class="userlist-box">
     <!-- // -->
      <div class="row m-b-5">
           <div class="col-md-12">
                <div class="row">
                     <div class="col-md-4" id="branch_box">
                          <div id="child_branch" class="patient_sub_branch"></div>
                     </div> <!-- 6 -->
                     <div class="col-md-4 ">
                         <b>Department</b> 
                         <select name="department" class="m_input_default" id="dept_id" onChange="reload_table();  get_test_head(this.value);">
						     <option value="">Select Department</option>
							 <?php 
							 if(!empty($dept_list))
							 {
							   foreach($dept_list as $dept)
							   {
							     echo '<option value="'.$dept->id.'">'.$dept->department.'</option>';
							   }
							 }
							 ?> 
						  </select>
                     </div>
					 <div class="col-md-4">
                         <b>Test Head</b> <select name="test_head" class="m_input_default" id="test_head" onChange="reload_table();">
						     <option value="">Select Test Head</option> 
						  </select>
                     </div>
					  <!-- 6 -->
                </div> <!-- innerRow -->
           </div> <!-- 12 -->
      </div> <!-- row -->


    <form> 
       <!-- bootstrap data table -->
       <?php if(in_array('855',$users_data['permission']['action'])): ?>
             <table id="table" class="table table-striped table-bordered test_master_list" cellspacing="0" width="100%">
                 <thead class="bg-theme">
                     <tr>
                         <?php
                         if($users_data['users_role']!='3')
                         {
                         ?>
                         <th width="40" align="center"> <input type="checkbox" name="selectall" class="" id="selectAll" value=""> </th> 
                         <?php
                         }
                         ?>
                         <th> Test Code </th>
                         <th> Test name </th>
                         <th> Test Type </th> 
                         <th> Department </th>
                         <th> Test Heads </th> 
                         <th> Unit </th>
                         <th> Price </th> 
                         <th>Result Heading</th>
                         <th>Sort Order</th>
                         <?php
                         if($users_data['users_role']!='3')
                         {
                         ?>
                         <th> Action </th>
                         <?php
                         }
                         ?>
                     </tr>
                 </thead>  
             </table> 
          <?php endif; ?>
    </form>
   </div> <!-- close -->    
<?php
if($users_data['users_role']!=3)
{
?>
   <div class="userlist-right relative">
    <div class="fixed">
        <div class="btns">
          
               <?php if(in_array('856',$users_data['permission']['action'])): ?>
                      <button class="btn-update" id="btn-add" onClick="window.location.href='<?php echo base_url('test_master/add/'); ?>'">
                         <i class="fa fa-plus"></i> New
                      </button>
               <?php endif;?>
			   <a href="<?php echo base_url('test_master/test_excel'); ?>" class="btn-anchor m-b-2" id="excel_export">
              <i class="fa fa-file-excel-o"></i> Excel
              </a>

              <a  id="csv_export" href="<?php echo base_url('test_master/test_csv'); ?>" class="btn-anchor m-b-2">
              <i class="fa fa-file-word-o"></i> CSV
              </a>

              <a  id="pdf_export" href="<?php echo base_url('test_master/test_pdf'); ?>" class="btn-anchor m-b-2">
              <i class="fa fa-file-pdf-o"></i> PDF
              </a>

              <a  id="print_export" href="javascript:void(0)" class="btn-anchor m-b-2" onClick="return print_window_page('<?php echo base_url("test_master/test_print"); ?>');">
              <i class="fa fa-print"></i> Print
              </a> 


               
              
              <?php 
            if(in_array('856',$users_data['permission']['action'])) { ?>
                <a href="<?php echo base_url('test_master/sample_import_test_master_excel'); ?>" class="btn-anchor m-b-2">
                <i class="fa fa-file-excel-o"></i> Sample(.xls)
                </a>
              <?php } if(in_array('856',$users_data['permission']['action'])) { ?>
                 <a id="open_model" href="javascript:void(0)" class="btn-anchor m-b-2">
                <i class="fa fa-file-excel-o"></i> Import(.xls)
                </a>
                <?php }

              ?> 


              <div class="dropdown">
                <button class="btn-anchor dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">  
                  Update Data
                  <span class="caret"></span>
                </button>
                <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                  <a class="dropdown-item update_data" title="Export data for update" href="<?php echo base_url('test_master/update_test_excel'); ?>"> <i class="fa fa-file-excel-o"></i> Export Data</a>
                  <a class="dropdown-item update_data" id="update_open_model" href="javascript:void(0);" title="Import data for update"> <i class="fa fa-file-excel-o"></i> Import Data</a> 
                </div>
              </div>
			  
               <?php if(in_array('858',$users_data['permission']['action'])): ?>
                      <button class="btn-update" id="deleteAll" onClick="return checkboxValues();">
                         <i class="fa fa-trash"></i> Delete
                      </button>
               <?php endif;?>
                <?php if(in_array('862',$users_data['permission']['action'])): ?>
                      <button class="btn-save" id="downloadAll" onClick="return downloadcheckboxValues();">
                         <i class="fa fa-download"></i> Download
                      </button>
               <?php endif;?>
               <?php if(in_array('855',$users_data['permission']['action'])): ?>
                     <button class="btn-update" id="reload" onClick="reload_table()">
                          <i class="fa fa-refresh"></i> Reload
                     </button>
                <?php endif;?>
                <?php if(in_array('859',$users_data['permission']['action'])): ?>
                     <button class="btn-exit" id="archive" onClick="window.location.href='<?php echo base_url('test_master/archive'); ?>'">
                         <i class="fa fa-archive"></i> Archive
                      </button> 
                 <?php endif;?>


        <button class="btn-exit" onClick="window.location.href='<?php echo base_url(); ?>'">
          <i class="fa fa-sign-out"></i> Exit
        </button>
        </div>
        </div>
    </div> 
<?php
 }
?>
</section> <!-- section close -->
<?php
$this->load->view('include/footer');
$flash_msg = $this->session->flashdata('flash_msg');
if(isset($flash_msg) && !empty($flash_msg))
{
 echo '<script>flash_session_msg("'.$flash_msg.'");</script>';
}
?>
 
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
    
</div><!-- container-fluid -->
<div id="load_test_master_import_modal_popup" class="modal fade modal-40" role="dialog" data-backdrop="static" data-keyboard="false"></div>
</body>
<script>
     $(document).ready(function(){
  //reload_table();
   $('#selectAll').on('click', function () { //alert('test');
                                 
         if ($("#selectAll").hasClass('allChecked')) {
             $('.checklist').prop('checked', false);
         } else {
             $('.checklist').prop('checked', true);
         }
         $(this).toggleClass('allChecked');
    });
}); 
     $(document).ready(function(){
          $.post('<?php echo base_url('test_master/get_allsub_branch_list/'); ?>',{},function(result)
          { 
             if(result=="")
             {
               $('#branch_box').remove();
             }
             else
             {
               $("#child_branch").html(result);
             } 
          });
     });
	 
	 function get_test_head(dapt_id)
	 {
	   var parent_id = $('#sub_branch_id').val();
	   if(parent_id=='inherit')
	   {
	      var branch_id = 0;
	   }
	   else 
	   {
	     var branch_id = parent_id;
	   }
	   $.post('<?php echo base_url('test_heads/test_heads_dropdown/'); ?>'+dapt_id+'/'+branch_id,{},function(result){
               $("#test_head").html(result);
          });
	 }
	 
     var count = 0;
     function get_selected_branch_test_master_list(val)
	 {   
        reload_table();
        if(val=='inherit')
        {
             <?php if(in_array('856',$users_data['permission']['action'])): ?>
             document.getElementById("btn-add").style.display="none";
             <?php endif;?>
             <?php if(in_array('858',$users_data['permission']['action'])): ?>
             document.getElementById("deleteAll").style.display="none";
             <?php endif;?>
             <?php if(in_array('859',$users_data['permission']['action'])): ?>
             document.getElementById("archive").style.display="none";
             <?php endif;?>
             document.getElementById("excel_export").style.display="none";
             document.getElementById("csv_export").style.display="none";
             document.getElementById("pdf_export").style.display="none";
             document.getElementById("print_export").style.display="none";
             <?php if(in_array('862',$users_data['permission']['action'])): ?>
             document.getElementById("downloadAll").style.display="block";
             <?php endif;?>

        }
        else
        {
            <?php if(in_array('856',$users_data['permission']['action'])): ?>
             document.getElementById("btn-add").style.display="block";
             <?php endif;?>
             <?php if(in_array('858',$users_data['permission']['action'])): ?>
             document.getElementById("deleteAll").style.display="block";
              <?php endif;?>
              <?php if(in_array('859',$users_data['permission']['action'])): ?>
             document.getElementById("archive").style.display="block";
             <?php endif;?>
             document.getElementById("excel_export").style.display="block";
             document.getElementById("csv_export").style.display="block";
             document.getElementById("pdf_export").style.display="block";
             document.getElementById("print_export").style.display="block";
             <?php if(in_array('862',$users_data['permission']['action'])): ?>
             document.getElementById("downloadAll").style.display="none";
             <?php endif;?>
        }
     }
     
     var $modal = $('#load_test_master_import_modal_popup');
        $('#open_model').on('click', function(){
        //  alert();
      $modal.load('<?php echo base_url().'test_master/import_test_master_excel' ?>',
      { 
      },
      function(){
      $modal.modal('show');
      });

      });



     var $modal = $('#load_test_master_import_modal_popup');
        $('#update_open_model').on('click', function(){
        //  alert();
      $modal.load('<?php echo base_url().'test_master/import_update_test_master_excel' ?>',
      { 
      },
      function(){
      $modal.modal('show');
      });

      }); 
      
      function update_result_heading_status(test_id,status)
        {
        
            if(test_id!=''){
                $.post('<?php echo base_url('test_master/save_test_result_heading_status/'); ?>',{'test_id':test_id,'status':status},function(result){
                    if(result!=''){
                        reload_table();
                        var msg = 'Test result heading updated successfully.';
                        flash_session_msg(msg);
                    }
        
                })
            }
        }
</script>
</html>