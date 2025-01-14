<?php
$users_data = $this->session->userdata('auth_users');
$company_data = $this->session->userdata('company_data');
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
<link rel="stylesheet" type="text/css" href="<?php echo ROOT_CSS_PATH; ?>buttons.bootstrap.min.css">

<!-- js -->
<script type="text/javascript" src="<?php echo ROOT_JS_PATH; ?>jquery.min.js"></script>
<script type="text/javascript" src="<?php echo ROOT_JS_PATH; ?>bootstrap.min.js"></script> 
<link rel="stylesheet" type="text/css" href="<?php echo ROOT_CSS_PATH; ?>bootstrap-datepicker.css">
<script type="text/javascript" src="<?php echo ROOT_JS_PATH; ?>bootstrap-datepicker.js"></script>
<script type="text/javascript" src="<?php echo ROOT_JS_PATH; ?>validation.js"></script> 


<!-- datatable js -->
<script src="<?php echo ROOT_JS_PATH; ?>jquery.dataTables.min.js"></script>
<script src="<?php echo ROOT_JS_PATH; ?>dataTables.bootstrap.min.js"></script> 

<script type="text/javascript"> 
var table;
function reset_date_search()
  {
      $('#start_date').val('');
      $('#end_date').val('');
      $.ajax({
         url: "<?php echo base_url('billing/reset_comission_search/'); ?>",  
         success: function(result)
         { 
          reload_table(); 
         }
      });  
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

<div class="overlay-loader" id="overlay-loader">
  <img src="<?php echo ROOT_IMAGES_PATH; ?>loader.gif" class="aj-loader">
</div>  
    
<?php
if($users_data['users_role']!=3)
{
?><form id="branch_doctor_form"> 
    <div class="grp_box m-b-5 media_d_default">  
       <div class="grp media_float_left"> 
        <label><b>Branch:</b></label>  
        <select class="sub_branch" name="branch_id" id="branch_id" onChange="return get_commission_doctor(this.value);">
            <option value="<?php echo $users_data['parent_id']; ?>">Self</option> 
            <?php
            if(!empty($branch_list))
            {
               foreach($branch_list as $branch)
               {
               ?>
                    <option value="<?php echo $branch['id']; ?>"><?php echo $branch['branch_name']; ?></option>
             <?php  
            }
            }
            ?>
        </select>
      </div>

      <div class="grp media_float_left">
       <label><b>Doctor:</b></label>  
       <select name="doctor_id" id="doctor_id" onchange="doctor_comission(this.value);">
       <option value="">Select Doctor</option>
        <?php 
        if(!empty($doctor_list))
        {
          foreach($doctor_list as $doctor)
          {
             echo '<option value="'.$doctor->id.'">'.$doctor->doctor_name.'</option>';
          }
        } 
        ?>
      </select>
      </div>


        <div class="grp media_float_left">

          <label><b>From Date:</b></label>
          <input type="text" id="start_date" name="from_date" class="datepicker start_datepicker media_w_100" value="<?php echo $form_data['from_date']; ?>">          
        </div>

        <div class="grp media_float_left">

          <label><b>To Date:</b></label>
          <input type="text" name="to_date" id="end_date" class="datepicker datepicker_to end_datepicker media_w_100 end_date" value="<?php echo $form_data['to_date']; ?>">       
        </div>

        <div class="grp media_float_left">  

          <a class="btn-custom" id="reset_date" onClick="reset_date_search();">Reset
          </a> 
       </div>

   </div> <!-- grp_box -->

<?php  } ?>


<!-- // -->

  <div class="row">
    <div class="col-xs-11 p-0">
      
      <!-- bootstrap data table -->
        <table id="table" class="table table-striped table-bordered doctor_commission" cellspacing="0" width="100%">
            <thead class="bg-theme">
                <tr>  
                    <th> Doctor/Branch Name </th> 
                    <th> Commission </th>  
                    <th> Paid Amount </th> 
                </tr>  
            </thead>  
            <tbody>
                <tr>
                    <td colspan="3" align="center" class="text-danger"><div class="text-center">No record found.</div></td>
                </tr> 
            </tbody>
        </table> 
        <div class="row">
                <div class="col-md-12">
                    <div class="row">
                        <div class="col-sm-4">
                            <div class="row m-b-5">
                                <div class="col-sm-5"><label>Total Commission</label></div>
                                <div class="col-sm-7"><input type="text" name="total_due" id="total_due" value="" readonly="" /></div>
                            </div> <!-- innerRow -->
                        </div> <!-- 4 -->
                        <div class="col-sm-4">
                            <div class="row m-b-5">
                                <div class="col-sm-5"><label>Paid Amount</label></div>
                                <div class="col-sm-7"><input type="text" name="total_due" id="rec_amount" value="" readonly="" /></div>
                            </div> <!-- innerRow -->
                        </div> <!-- 4 -->
                        <div class="col-sm-4">
                            <div class="row m-b-5">
                                <div class="col-sm-5"><label>Balance </label></div>
                                <div class="col-sm-7"><input type="text" name="balance" id="balance" value=""  readonly="" /></div>
                            </div> <!-- innerRow -->
                        </div> <!-- 4 -->
                    </div>
                </div>
            </div>
            
            <div class="row" id="inner_table" style="display:none;">
            <div class="row" >
                <div class="col-sm-12">
                    
                        <div class="col-sm-4">
                            <div class="row m-b-5">
                                <div class="col-sm-5"><b>Department</b></div>
                                <div class="col-sm-7">
                                    
                                    <select name="department_type" id="department_type" class="pat-select1 m_select_btn" >	
                                             <option value="">Select Department</option>	
                                            <?php  foreach($dept_list as $key=>$dept){?>	
                                             <option value="<?php echo $key;  ?>" <?php if($key==16){ echo 'selected="selected"';}?>><?php echo $dept;?></option>	
                                             <?php }?>	
                                        </select>
                                </div>
                            </div> <!-- innerRow -->
                        </div> <!-- 4 -->
                        
                        
                        <div class="col-sm-4">
                            <div class="row m-b-5">
                                <div class="col-sm-5"><b>Mode of Payment </b></div>
                                <div class="col-sm-7">
                        <select name="pay_mode" id="pay_mode" onChange="return pay_fields(this.value);">
                                      <?php foreach($payment_mode as $payment_mode) 
                                      {?>
                                      <option value="<?php echo $payment_mode->id;?>" ><?php echo $payment_mode->payment_mode;?></option>
                                      <?php }?>
                                    </select>
                        
                        </div></div>
                           
                        </div> <!-- 4 -->
                        
                        
                        <div class="col-sm-4">
                            <div class="row m-b-5">
                                <div class="col-sm-5"><b>Amount to Pay <span class="star">*</span> </b></div>
                                <div class="col-sm-7">
                        
                       <input type="text" class="price_float" name="pay_amount" id="pay_amount" value=""/><div class="doctor_comission_msg" style="color:red;" id="pay_amount_msg"></div></div></div>
                           
                        </div>
                </div>
                </div>        
                 <div class="row" >
                <div class="col-sm-12">
                    <div id="cheque">
                                
                        </div>
                    </div>
                </div>
                <div class="row" >
                <div class="col-sm-12">
                    
                    <div class="col-sm-4">
                            <div class="row m-b-5">
                                <div class="col-sm-5"><b>Remarks</b></div>
                                <div class="col-sm-7">
                        
                        <textarea name="remarks" id="remarks"></textarea> 
                                       
                         </div></div>
                           
                        </div>
                        <div class="col-sm-4">
                            <div class="row m-b-5">
                                <div class="col-sm-5"><b>Payment Date</b></div>
                                <div class="col-sm-7"> <input type="text" readonly="readonly" name="payment_date" value="<?php echo date('d-m-Y'); ?>" class="paydatepicker"/>
                                        </div>
                            </div> <!-- innerRow -->
                        </div> <!-- 4 -->
                        <div class="col-sm-4">
                            <div class="row m-b-5">
                                <div class="col-sm-5"></div>
                                <div class="col-sm-7"> <button type="submit" name="submit" id="submit" class="btn-update"> <i class="fa fa-floppy-o"></i> Pay</button></div>
                            </div> <!-- innerRow -->
                        </div> <!-- 4 -->
                    </div>
                </div>
            </div> <!-- mainrow -->
        </div> <!-- 11 -->
    <div class="col-xs-1 p-0 p-l-5">
      <div class="b_doc_pay">
        <div class="btns"> 
        <select name="printer_id" class="m-b-3" id="printer_id" style="width: 96px;">
                  
                  <option value="1">A4</option>
                  <option value="2">A4/2</option>
                  <option value="3">A4/3</option>
        </select>
        
        
        <?php if(in_array('224',$users_data['permission']['action'])) {?>
          <a href="javascript:void(0)" onClick="return doctor_commission_details();" class="btn-anchor m-b-2">
            <i class="fa fa-money"></i> Commission Details
          </a>
          
          <a href="javascript:void(0)" onClick="return doctor_commission_excel();" class="btn-anchor m-b-2">
            <i class="fa fa-money"></i> Excel
          </a>
          
          
          
          <a href="javascript:void(0)" onClick="return print_letter_head_doctor_commission();" class="btn-anchor m-b-2">
            <i class="fa fa-file-excel-o"></i> Print</a>
          
       <?php } ?>
       
       <?php if(in_array('145',$users_data['permission']['section'])) {?>
          
          <a href="javascript:void(0)" onClick="return test_details();" class="btn-anchor m-b-2">
            <i class="fa fa-money"></i> Test Wise Commission
          </a>
          
          <?php } ?>
           
          <a href="javascript:void(0)" onClick="return doctor_paid_commission_list();" class="btn-anchor m-b-2">
            <i class="fa fa-file-word-o"></i> Payment Details
          </a>
          <?php
        if($users_data['users_role']!=3)
        {
        ?>
          <button type="button" onClick="return receive_now(1)" class="btn-anchor m-b-2">
            <i class="fa fa-money"></i> Pay Now
          </button> 
           <?php } ?>
          <button class="btn-exit" onClick="window.location.href='<?php echo base_url(); ?>'">
            <i class="fa fa-sign-out"></i> Exit
          </button>
        </div>
        </div>
    </div> <!-- 1 -->
  </div> <!-- MainRow -->




     </form>

  <!-- cbranch-rslt close -->
 
</section> <!-- cbranch -->
<?php
$this->load->view('include/footer');
if($users_data['users_role']==3)
{
?>
<script type="text/javascript">
$.ajax({
   url: "<?php echo base_url('billing/total_doctor_comission/').$users_data['parent_id']; ?>", 
   type: 'POST', 
   dataType: "json",
   data: { start_date: '', end_date : '', doctor_id : '<?php echo $users_data['parent_id']; ?>', branch_id : '<?php echo $branch_id; ?>'} ,  
   success: function(result)
   {   
      $('#total_due').val(result.total_due);
      $('#rec_amount').val(result.total_debit);
      $('#balance').val(result.balance); 
      $('#table tbody').html(result.html); 
   }
}); 
</script>
<?php  
}
?>

<script type="text/javascript">
$(document).ready(function(){
   var Vals = $('#pay_mode :selected').val();
   pay_fields(Vals);

});

function doctor_comission(vals)
{
  var doctor_id = $('#doctor_id').val();
  var branch_id = $('#branch_id').val();
  var start_date = $('#start_date').val();
  var end_date = $('#end_date').val();
  var printer_id = $('#printer_id').val();
  $('#overlay-loader').show();
  $.ajax({
         url: "<?php echo base_url('billing/total_doctor_comission/'); ?>"+vals, 
         type: 'POST', 
         dataType: "json",
         data: { start_date: start_date, end_date : end_date, doctor_id : doctor_id, branch_id : branch_id,printer_id:printer_id} ,  
         success: function(result)
         {   
            $('#total_due').val(result.total_due);
            $('#rec_amount').val(result.total_debit);
            $('#balance').val(result.balance); 
            $('#table tbody').html(result.html); 
            $('#overlay-loader').hide();
         }
      });    
}

function get_commission_doctor(vals)
{
  $.ajax({
         url: "<?php echo base_url('billing/get_commission_doctor/'); ?>"+vals, 
         type: 'POST', 
         success: function(result)
         { 
            $('#doctor_id').html(result);
         }
      });    
}
 

function doctor_paid_commission_list()
{
    <?php
   if($users_data['users_role']==3)
   {
      echo "var doctor_id = ".$users_data['parent_id'].";
      var branch_id = ".$branch_id.";
      var start_date = '';
      var end_date = ''; var printer_id ='';";
   }
   else
   {
      echo "var doctor_id = $('#doctor_id').val();
      var branch_id = $('#branch_id').val();
      var start_date = $('#start_date').val();
      var end_date = $('#end_date').val(); var printer_id = $('#printer_id').val();";
   }
  ?> 
   if(doctor_id>0) //echo "var doctor_id = $('#doctor_id').val();
   {    
    window.open('<?php echo base_url('billing/doctor_paid_commission_list?') ?>doctor_id='+doctor_id+'&branch_id='+branch_id+'&start_date='+start_date+'&end_date='+end_date+'&printer_id='+printer_id,'mywin','width=800,height=600');
   }
    else
  {
      alert('Please select doctor first!');
  }
}
   
  

  
$('.datepicker').datepicker({
    format: 'dd-mm-yyyy', 
    autoclose: true, 
    endDate : new Date(), 
  }).on("change", function(selectedDate) 
  { 

      var start_data = $('.datepicker').val();
      $('.datepicker_to').datepicker('setStartDate', start_data);
      form_submit();
  });

  $('.datepicker_to').datepicker({
    format: 'dd-mm-yyyy',     
    autoclose: true,  
  }).on("change", function(selectedDate) 
  {  
      form_submit();
      
  });

function form_submit()
{
  var start_date = $('#start_date').val();
  var end_date = $('#end_date').val();
  var doctor_id = $('#doctor_id').val();
  var branch_id = $('#branch_id').val();
  var printer_id = $('#printer_id').val();
  if(doctor_id>0)
  {
  $('#overlay-loader').show();   
  $.ajax({
         url: "<?php echo base_url('billing/total_doctor_comission/'); ?>"+doctor_id, 
         type: 'POST', 
         dataType: "json",
         data: { start_date: start_date, end_date : end_date, doctor_id : doctor_id, branch_id : branch_id,printer_id:printer_id} ,  
         success: function(result)
         {   
            $('#total_due').val(result.total_due);
            $('#rec_amount').val(result.total_debit);
            $('#balance').val(result.balance); 
            $('#table tbody').html(result.html); 
            $('#overlay-loader').hide();
         }
      });  
      
  }
  else
  {
      alert('Please select doctor first!');
  }
  
}
function reload_table()
{
    //table.ajax.reload(null,false); //reload datatable ajax 

    
}
/*function openPrintWindow(url, name, specs) {
  var printWindow = window.open(url, name, specs);
    var printAndClose = function() {
        if (printWindow.document.readyState == 'complete') {
            clearInterval(sched);
            printWindow.print();
            printWindow.close();
        }
    }
    var sched = setInterval(printAndClose, 200);
}*/


function pay_fields(value)
{
   $('#updated_payment_detail').html('');
     $.ajax({
        type: "POST",
        url: "<?php echo base_url('billing/get_payment_mode_data')?>",
        data: {'payment_mode_id' : value},
       success: function(msg){
         $('#cheque').html(msg);
        }
    });
     
   
 $('.datepicker').datepicker({
                    format: "dd-mm-yyyy",
                    autoclose: true
                });  
} 

function receive_now(vals)
{  

    var doctor_id = $('#doctor_id').val(); 
    if(doctor_id>0)
    {
          if(vals==1)
          {
            $('#inner_table').slideDown();
            $('#pay_amount').val('');
            $('#bank_name').val('');
            $('#cheque_no').val('');
            $('#cheque_date').val('');
            $('#transection_no').val('');
            $('#department_type').val('');
            //$('#vendor_id').val('');
            
        
        
          }
          else
          {
            $('#inner_table').attr('style','display:none');
          }
      }
      else
      {
          alert('Please select doctor first!');
      }
}  

$("#branch_doctor_form").on("submit", function(event) { 
     event.preventDefault();
     var Vals = $('#pay_mode :selected').val();
     $("#transaction_no_msg").html("");
     $("#cheque_no_msg").html("");
     $("#bank_name_msg").html("");
     $("#pay_amount_msg").html("");
     
        if($("#pay_amount").val()==''){
               receive_now(1);
               $("#pay_amount_msg").html("The amount is required");
          }
          if($("#pay_amount").val()!==''){
               $("#pay_amount_msg").html("");
               doctor_commission_final_step();
          }

         
     
    
  
}); 
function doctor_commission_final_step(){
     $.ajax({
          url: "<?php echo base_url('billing/doctor_commission'); ?>",
          type: "post",
          data: $('#branch_doctor_form').serialize(),
          success: function(result) 
          {
               var doctor_id = $('#doctor_id').val();
               var branch_id = $('#branch_id').val(); 
               doctor_comission(doctor_id);
               receive_now(0);
               flash_session_msg('Doctor commission successfully payed.');
          }
     });
}
function doctor_commission_details()
{

  <?php
  if($users_data['users_role']!=3)
  { 
    echo "var doctor_id = $('#doctor_id').val();
      var branch_id = $('#branch_id').val();
      var start_date = $('#start_date').val();
      var end_date = $('#end_date').val(); var printer_id = $('#printer_id').val(); ";
      
   }
   else
   {
      echo "var doctor_id = ".$users_data['parent_id'].";
      var branch_id = ".$branch_id.";
      var start_date = '';
      var end_date = ''; var printer_id = $('#printer_id').val(); ";
   }
  ?> 
  /*var doctor_id = $('#doctor_id').val();
  var branch_id = $('#branch_id').val();
  var start_date = $('#start_date').val();
  var end_date = $('#end_date').val(); */
  if(doctor_id>0)
  {
   window.open('<?php echo base_url('billing/doctor_commission_details?doctor_id=') ?>'+doctor_id+'&branch_id='+branch_id+'&start_date='+start_date+'&end_date='+end_date+'&printer_id='+printer_id,'mywin','width=800,height=600');
  }
   else
  {
      alert('Please select doctor first!');
  }
 }
 
 function test_details()
 {

  <?php
  if($users_data['users_role']!=3)
  { 
    echo "var doctor_id = $('#doctor_id').val();
      var branch_id = $('#branch_id').val();
      var start_date = $('#start_date').val();
      var end_date = $('#end_date').val(); var printer_id = $('#printer_id').val(); ";
      
   }
   else
   {
      echo "var doctor_id = ".$users_data['parent_id'].";
      var branch_id = ".$branch_id.";
      var start_date = '';
      var end_date = ''; var printer_id = $('#printer_id').val(); ";
   }
  ?> 
  /*var doctor_id = $('#doctor_id').val();
  var branch_id = $('#branch_id').val();
  var start_date = $('#start_date').val();
  var end_date = $('#end_date').val(); */
  if(doctor_id>0)
  {
   window.open('<?php echo base_url('billing/test_details?doctor_id=') ?>'+doctor_id+'&branch_id='+branch_id+'&start_date='+start_date+'&end_date='+end_date+'&printer_id='+printer_id,'mywin','width=800,height=600');
  } 
   else
  {
      alert('Please select doctor first!');
  }
 }
 
 function print_letter_head_doctor_commission(id,branch_id)
{  
 
  // var print_option = 1;
  // var id=id;
  // var branch_id=branch_id;
 
<?php
  if($users_data['users_role']!=3)
  { 
    echo "var doctor_id = $('#doctor_id').val();
      var branch_id = $('#branch_id').val();
      var start_date = $('#start_date').val();
      var end_date = $('#end_date').val(); var printer_id = $('#printer_id').val(); ";
      
   }
   else
   {
      echo "var doctor_id = ".$users_data['parent_id'].";
      var branch_id = ".$branch_id.";
      var start_date = '';
      var end_date = ''; var printer_id = $('#printer_id').val(); ";
   }
  ?> 
  if(doctor_id>0) 
  {
 // print_window_page('<?php echo base_url('billing/print_doctor_commission_letter_head/') ?>'+doctor_id+'/'+branch_id);
 print_window_page('<?php echo base_url('billing/print_doctor_commission_letter_head?doctor_id=') ?>'+doctor_id+'&branch_id='+branch_id+'&start_date='+start_date+'&end_date='+end_date+'&printer_id='+printer_id);
  } 
  else
  {
      alert('Please select doctor first!');
  }
  
}



function doctor_commission_excel()
{

  <?php
  if($users_data['users_role']!=3)
  { 
    echo "var doctor_id = $('#doctor_id').val();
      var branch_id = $('#branch_id').val();
      var start_date = $('#start_date').val();
      var end_date = $('#end_date').val(); var printer_id = $('#printer_id').val(); ";
      
   }
   else
   {
      echo "var doctor_id = ".$users_data['parent_id'].";
      var branch_id = ".$branch_id.";
      var start_date = '';
      var end_date = ''; var printer_id = $('#printer_id').val(); ";
   }
  ?> 
 
  if(doctor_id>0)
  {
   window.open('<?php echo base_url('billing/doctor_commission_excel?doctor_id=') ?>'+doctor_id+'&branch_id='+branch_id+'&start_date='+start_date+'&end_date='+end_date+'&printer_id='+printer_id,'mywin','width=800,height=600');
  }
  else
  {
      alert('Please select doctor first!');
  }
 }
 $(document).ready(function(){
              $('.paydatepicker').datepicker({
              format: "dd-mm-yyyy",
              autoclose: true
              }); 
          }); 
</script>
<div id="load_advance_search_modal_popup" class="modal fade" role="dialog" data-backdrop="static" data-keyboard="false"></div>
</body>
</html>