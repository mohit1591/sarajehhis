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

<link rel="stylesheet" type="text/css" href="<?php echo ROOT_CSS_PATH; ?>bootstrap-datepicker.css">
<script type="text/javascript" src="<?php echo ROOT_JS_PATH; ?>bootstrap-datepicker.js"></script>
<script type="text/javascript" src="<?php echo ROOT_JS_PATH; ?>validation.js"></script> 



</head>

<body id="bal_list" onload="return get_balance_clearance_list()">


<div class="container-fluid">
 <?php
  $this->load->view('include/header');
  $this->load->view('include/inner_header');
 ?>
<!-- ============================= Main content start here ===================================== -->
<section class="userlist">
    <div class="userlist-box">

         <form id="top_search_form">
            
               <!-- bootstrap data table -->
               <div class="grp_box m-b-5">

                  <div class="grp">
                    <label></label>  
                       <?php
                       $balance_search_data = $this->session->userdata('balance_search_data');
                       $users_data = $this->session->userdata('auth_users'); 

                      if (array_key_exists("permission",$users_data)){
                           $permission_section = $users_data['permission']['section'];
                           $permission_action = $users_data['permission']['action'];
                      }
                      else{
                           $permission_section = array();
                           $permission_action = array();
                      }
                       

                       if(in_array('85',$permission_section)){ 
                       ?>
                       <input type="checkbox" <?php if(!empty($balance_search_data) && in_array('2',$balance_search_data['section_id'])){ echo 'checked="checked"'; } ?> name="section_id[]" onclick="return form_submit()" value="2"> OPD
                       <?php } ?>  
                      <input type="checkbox" <?php if(!empty($balance_search_data) && in_array('1',$balance_search_data['section_id'])){ echo 'checked="checked"'; } ?> name="section_id[]" onclick="return form_submit()" value="1"> Pathology

                      <?php
                      if(in_array('60',$permission_section)){ ?>
                       <input type="checkbox" name="section_id[]" <?php if(!empty($balance_search_data) && in_array('3',$balance_search_data['section_id'])){ echo 'checked="checked"'; } ?> onclick="return form_submit()" value="3"> Medicine
                       <?php }  if(in_array('151',$permission_section)){  ?>
                       <input type="checkbox" name="section_id[]" <?php if(!empty($balance_search_data) && in_array('4',$balance_search_data['section_id'])){ echo 'checked="checked"'; } ?> onclick="return form_submit()" value="4"> Billing <?php } 

                       if(in_array('85',$permission_section)){  ?>
                       <input type="checkbox" name="section_id[]" <?php if(!empty($balance_search_data) && in_array('5',$balance_search_data['section_id'])){ echo 'checked="checked"'; } ?> onclick="return form_submit()" value="5"> IPD <?php } ?>
                       
                  </div>
                    <div class="grp">
                    <input type="radio" name="type" value="1" checked  onclick="get_patient_drop_down(this.value), get_balance_clearance_list(1);"> <label>Patient</label>
                    </div>

                    <!---<div class="grp">
                    <input type="radio" name="type" value="2" onclick="get_patient_drop_down(this.value), get_balance_clearance_list(2);"> <label>Vendor</label>
                    </div>-->
                    <?php
                    if(!empty($branch_list))
                    {
                    ?>
                    <div class="grp" id="branch_autodrop">
                      <label>Branch</label>
                      <select name="sub_branch_id" id="sub_branch_id">
                          <option value="<?php echo $users_data['parent_id']; ?>">Self</option>
                          <?php
                           foreach($branch_list as $branch)
                           {
                          ?>
                           <option value="<?php echo $branch['id']; ?>"><?php echo $branch['branch_name']; ?></option>
                          <?php
                           }
                          ?>
                      </select>
                    </div>
                    <?php  
                    }
                    else
                    {
                    ?>
                    <input type="hidden" value="<?php echo $users_data['parent_id']; ?>" name="sub_branch_id" id="sub_branch_id">
                    <?php  
                    }
                    ?>

                    <div id="previous_data">
                      

                      <div class="grp" id="selection_criteria">Searching Criteria 
                        <select id="selection_criteria_list" name="selection_criteria_list" onchange="get_selected_value(this.value);">
                        <option value=''>Select Option</option>
                        <option value="patient_name">Patient Name</option>
                        <option value="p_mobile_no">Mobile No.</option>
                        </select>
             </div>
                     <div class="grp" id="search_box_patient_name" style="display:none;"><input type="text" name="patient_name"  id="patient_name"/></div>
                      <?php if(!empty($form_error)){ echo form_error('patient_name'); } ?>
                     <div class="grp" id="search_box_mobile_no" onkeypress="return isNumberKey(event);" style="display:none;"><input type="text" name="mobile_no" id="mobile_no"/></div> 
                      <?php if(!empty($form_error)){ echo form_error('mobile_no'); } ?>
                    <div class="grp" id="branch_list"> <div id="child_branch" class="grp"></div>  </div>
                   <button type="button" class="btn-custom" onclick="get_balance_clearance_list(1);">Search</button>
                     <input type="hidden" value="1" name="type" id="type"/>
                    </div>
                    
                    <div id="print_data"></div>

                  
                    <?php //$this->load->view('balance_clearance/drop_down_data');?>
                   
               </div>
                <!-- bootstrap data table -->
             
                <!-- bootstrap data table -->
          <table id="table" class="table table-striped table-bordered balance_clearance" cellspacing="0" width="100%">
               <thead class="bg-theme">
                    <tr>
                      <th>Sr.No.</th>
                      <th>Patient Name </th>
                      <th>Mobile No.</th>
                      <th>Date </th>
                      <th>Patient Reg. No.</th>
                      <th>Department</th>
                      <!-- <th>Discount</th>  -->
                      <th>Balance </th> 
                      <th>Action</th>
                       
                    </tr>
               </thead>  
               <tbody id="bal_list1">
               </tbody>
          </table>
          </form>
     </div> <!-- close -->
     <div class="userlist-right">
         
       
      <div class="btns">
     
             
               <button class="btn-exit m-t-30px" onclick="window.location.href='<?php echo base_url(); ?>'">
                    <i class="fa fa-sign-out"></i> Exit
               </button>
         </div>
    </div> 
    <!-- right --> 
</section> <!-- cbranch -->
<?php
$this->load->view('include/footer');
?>
<script>
  
 if ($("#section_id").is(":checked")) 
 { 
    form_submit();
  } 

   function form_submit()
   { 
    $.ajax({
           url: "<?php echo base_url('balance_clearance/search_data/'); ?>", 
           type: 'POST',
           data: $('#top_search_form').serialize() ,
           success: function(result)
           { 
              get_balance_clearance_list(); 
           }
        });    

 
  }

     function isNumberKey(evt) {
      var charCode = (evt.which) ? evt.which : event.keyCode;
      if (charCode != 46 && charCode > 31 && (charCode < 48 || charCode > 57)) {
          return false;
      } else {
          return true;
      }      
  }

  function get_all_branch()
  { 
     $.post('<?php echo base_url('balance_clearance/get_allsub_branch_list/'); ?>',{},function(result){

          $("#child_branch").html(result);
     });
   
 }

function get_selected_value(value)
{ 
      if(value=='patient_name')
      {
            document.getElementById("search_box_patient_name").style.display="block";
             document.getElementById("search_box_mobile_no").style.display="none";
      }
      else if(value=='p_mobile_no')
      {
             document.getElementById("search_box_mobile_no").style.display="block";
              document.getElementById("search_box_patient_name").style.display="none";
      }
      else if(value=='v_mobile_no')
      {
             document.getElementById("search_box_vendor_mobile_no").style.display="block";
              document.getElementById("search_box_vendor_name").style.display="none";
      }
       else if(value=='vendor_name')
      {
             document.getElementById("search_box_vendor_name").style.display="block";
              document.getElementById("search_box_vendor_mobile_no").style.display="none";
      }
      else
      {
             document.getElementById("search_box_patient_name").style.display="none";
              document.getElementById("search_box_mobile_no").style.display="none";
      }
}

function get_balance_clearance_list(vals)
{
       //alert(vals);
    $("#bal_list1 tr").remove(); 
    patientName = $("#patient_name").val();
    vendorName = $("#vendor_name").val(); 
    vendorMobileNo= $("#vendor_mobile_no").val(); 
    mobileNo = $("#mobile_no").val();
    sub_branch_id = $("#sub_branch_id").val(); 
    $.post('<?php echo base_url('balance_clearance/balance_list/'); ?>',{'sub_branch_id':sub_branch_id,'patient_name':patientName,'mobile_no':mobileNo,'type':vals,'vendor_name':vendorName,'vendor_mobile_no':vendorMobileNo},function(result)
    { 
          if(result!='')
          {
            $("#bal_list1").html(result);
          }

    });
}



</script>

<script>
  function get_patient_drop_down(type)
  {

    $.ajax({
                 type: "POST",
                 url: "<?php echo base_url('balance_clearance/get_drop_down_value/'); ?>", 
                 data: {type: type},
                 success: function(result)
                 {
                  
                   $('#print_data').html(result);
                   $('#branch_autodrop').html('');
                   $('#previous_data').html('');
                   
                    get_all_branch();
                 }
              });

  }



function pay_now_to_branch(id,parent_id,section_id,balance,branch_id)
{ 
   
    var $modal = $('#load_add_pay_now_modal_popup');
    $modal.load('<?php echo base_url().'balance_clearance/pay_now/' ?>'+id+'/'+parent_id+'/'+section_id+'/'+branch_id,
    {'bal': balance},
     function(){
          $modal.modal('show');
     });
}

</script>

<!-- Confirmation Box -->

    


</div><!-- container-fluid -->
<div id="load_add_pay_now_modal_popup" class="modal fade modal-45" role="dialog" data-backdrop="static" data-keyboard="false"></div>

</body>
</html>