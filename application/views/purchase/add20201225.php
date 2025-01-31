<?php
$this->load->model('medicine_entry/medicine_entry_model','medicine_entry');
$users_data = $this->session->userdata('auth_users'); 
$field_list = mandatory_section_field_list(10);
//print_r($this->session->userdata('medicine_id')); ?>
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
<script type="text/javascript" src="<?php echo ROOT_JS_PATH; ?>validation.js"></script>
<script type="text/javascript" src="<?php echo ROOT_JS_PATH; ?>bootstrap.min.js"></script> 

<!-- datatable js -->
<script src="<?php echo ROOT_JS_PATH; ?>jquery.dataTables.min.js"></script>
<script src="<?php echo ROOT_JS_PATH; ?>dataTables.bootstrap.min.js"></script>
<!--<link rel="stylesheet" type="text/css" href="<?php echo ROOT_CSS_PATH; ?>bootstrap-datepicker.css">
<script type="text/javascript" src="<?php echo ROOT_JS_PATH; ?>bootstrap-datepicker.js"></script>-->

<script type="text/javascript" src="<?php echo ROOT_JS_PATH; ?>moment-with-locales.js"></script>

<link rel="stylesheet" type="text/css" href="<?php echo ROOT_CSS_PATH; ?>bootstrap-datepicker.css">
<script type="text/javascript" src="<?php echo ROOT_JS_PATH; ?>bootstrap-datepicker.js"></script>
<link rel="stylesheet" type="text/css" href="<?php echo ROOT_CSS_PATH; ?>bootstrap-datetimepicker.css">
<script type="text/javascript" src="<?php echo ROOT_JS_PATH; ?>bootstrap-datetimepicker.js"></script>


</head>

<body onload="list_added_medicine();">


<div class="container-fluid">
 <?php
  $this->load->view('include/header');
  $this->load->view('include/inner_header');
 ?>

<div class="modal fade" tabindex="-1" role="dialog" id="medicine_namess">
  <div class="modal-dialog" role="document">
    <div class="modal-content modal-60">
    <div class="modal-header">
         <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4>Vendor Rate List</h4>
    </div>
    <div class="modal-body">
        <table class="table table-bordered text-center">
            <tr id="vender_list">
                <th width="100"  class="text-center">Medicin Name</th>
                <th class="text-center">Vendor Name</th>
                <th class="text-center">Parchase Date</th>
                <th class="text-center">Purchase Rate</th>
            </tr>
            
        </table>
   
    </div> 
</div>
</div>
</div>
<section class="userlist">
 
   <form id="purchase_form" action="<?php echo current_url(); ?>" method="post"> 
    <input type="hidden" name="data_id" id="type_id" value="<?php echo $form_data['data_id']; ?>" />
<div class="purchase_medicine">
    <div class="row">
        <div class="col-md-12">
            <div class="grp">
            <?php if(!empty($form_data['vendor_id'])){?>
                 <span class="new_vendor"><input type="radio" name="new_vendor" onClick="window.location='<?php echo base_url('purchase/');?>add/';"> <label>New Vendor</label></span>
                <span class="new_vendor"><input type="radio" name="new_vendor" checked onClick="window.location='<?php echo base_url('vendor');?>';"> <label>Registered Vendor</label></span>
               <?php  }else{?>
                 <span class="new_vendor"><input type="radio" name="new_vendor" checked onClick="window.location='<?php echo base_url('purchase/');?>add/';"> <label>New Vendor</label></span>
                <span class="new_vendor"><input type="radio" name="new_vendor" onClick="window.location='<?php echo base_url('vendor');?>';"> <label>Registered Vendor</label></span>
                    <?php }?>

              <input type="text" name="estimate_no" id="estimate_no" placeholder="Purchase Estimate no" class="estimate_no"> <label></label>
          
               
            </div>
        </div>
    </div>    <!-- endRow -->



<div class="row">
    
    <div class="col-md-4">
        
        <div class="row m-b-5">
            <div class="col-sm-4">
                <label><input type="hidden" name="vendor_id" value="<?php if(isset($form_data['vendor_id'])){echo $form_data['vendor_id'];}else{ echo '';}?>"/> Vendor Code</label>
            </div>
            <div class="col-sm-8">
                <input type="hidden" value="<?php echo $form_data['vendor_code'];?>" name="vendor_code" />
                <div class="vendor_code"><b><?php echo $form_data['vendor_code'];?></b></div>
            </div>
        </div>
        
       
        <div class="row m-b-5">
             <div class="col-sm-4"><label>Invoice no <?php if(!empty($field_list)){
                    if($field_list[0]['mandatory_field_id']==51 && $field_list[0]['mandatory_branch_id']==$users_data['parent_id']){?>         
                         <span class="star">*</span>
                    <?php 
                    }
               } 
          ?></label></div>
            <div class="col-sm-8">
                 
                <input type="text" class="m_input_default" name="invoice_id" value="<?php echo $form_data['invoice_id'];?>" autofocus="">
                    <div class="">
                    <?php if(!empty($field_list)){
                         if($field_list[0]['mandatory_field_id']=='51' && $field_list[0]['mandatory_branch_id']==$users_data['parent_id']){
                              if(!empty($form_error)){ echo form_error('invoice_id'); }
                         }
                    }
          ?>
                    </div>
            </div>
        </div>
        
        <div class="row m-b-5">
            <div class="col-sm-4">
                <label>purchase no.</label>
            </div>
            <div class="col-sm-8">
                <input type="text" class="m_input_default" name="purchase_no" value="<?php echo $form_data['purchase_no'];?>">
            </div>
        </div>
        
      

       <div class="row m-b-5">
             <div class="col-sm-4 pr-0"><label>Vendor name <?php if(!empty($field_list)){
                    if($field_list[1]['mandatory_field_id']==52 && $field_list[1]['mandatory_branch_id']==$users_data['parent_id']){?>         
                         <span class="star">*</span>
                    <?php 
                    }
               } 
          ?></label></div>
            <div class="col-sm-8">
                 
               <input type="text" name="name" class="alpha_numeric_space m_input_default txt_firstCap" id="ven_name" value="<?php echo $form_data['name'];?>">
                    <div class="">
                    <?php if(!empty($field_list)){
                         if($field_list[1]['mandatory_field_id']=='52' && $field_list[1]['mandatory_branch_id']==$users_data['parent_id']){
                              if(!empty($form_error)){ echo form_error('name'); }
                         }
                    }
          ?>
                    </div>
            </div>
        </div>
        
        
        
        <div class="row m-b-5">
            <div class="col-sm-4"></div>
            <div class="col-sm-8"></div>
        </div>

    </div> <!-- //4 -->




    
    <div class="col-md-4">
        
        <div class="row m-b-5">
            <div class="col-sm-5"><label>Mobile No. <?php if(!empty($field_list)){
                    if($field_list[2]['mandatory_field_id']==53 && $field_list[2]['mandatory_branch_id']==$users_data['parent_id']){?>         
                         <span class="star">*</span>
                    <?php 
                    }
               } 
          ?></label></div>
            <div class="col-sm-7">
                 <input type="text" name="" readonly="readonly" value="<?php echo $form_data['country_code'];?>" class="country_code m_c_code" placeholder="+91" style="width:59px;"> 
                <input type="text" name="mobile" class="number m_number" id="mobile_no" maxlength="10" value="<?php echo $form_data['mobile'];?>" onKeyPress="return isNumberKey(event);">
                    <div class="">
                    <?php if(!empty($field_list)){
                         if($field_list[2]['mandatory_field_id']=='53' && $field_list[2]['mandatory_branch_id']==$users_data['parent_id']){
                              if(!empty($form_error)){ echo form_error('mobile'); }
                         }
                    }
          ?>
                    </div>
            </div>
        </div>

        
        <div class="row m-b-5">
            <div class="col-sm-4">
                <label>email id</label>
            </div>
            <div class="col-sm-8">
                <input type="text" class="m_input_default" name="email" value="<?php echo $form_data['email'];?>">
                <?php if(!empty($form_error)){ echo form_error('email'); } ?>
            </div>
        </div>
        
        <div class="row m-b-5">
            <div class="col-sm-4">
                <label style="width:39%;">purchase date/time</label>
            </div>
            <div class="col-sm-8">
                <input type="text" name="purchase_date" style="width: 110px;" class="datepicker m_input_default" value="<?php if($form_data['purchase_date']=='00-00-0000' || empty($form_data['purchase_date'])){echo '';}else{echo $form_data['purchase_date'];}?>">
                <input type="text" name="purchase_time" class="w-80px datepicker3 m_input_default" placeholder="Time" value="<?php echo $form_data['purchase_time']; ?>">
            </div>
        </div>
        
         <div class="row m-b-5">
            <div class="col-sm-4">
                <label>Vendor GST No.</label>
            </div>
            <div class="col-sm-8">
                <input type="text" name="vendor_gst" id="vendor_gst_no" value="<?php echo $form_data['vendor_gst']; ?>">
            </div>
        </div>

    </div> <!-- //4 -->


    
   <div class="col-md-4">
        
       <div class="row m-b-3">
            <div class="col-xs-3">
               <label>Address 1</label>
            </div>
            <div class="col-xs-4">
               <input type="text" name="address" id="" class="address" value="<?php echo $form_data['address'];?>"/>
               <?php //if(!empty($form_error)){ echo form_error('address'); } ?>
            </div>
          </div>

           <div class="row m-b-3">
            <div class="col-xs-3">
               <label>Address 2</label>
            </div>
            <div class="col-xs-4">
               <input type="text"  name="address2" class="address" value="<?php echo $form_data['address2'];?>"/>
              
            </div>
          </div>
           <div class="row m-b-3">
            <div class="col-xs-3">
               <label>Address 3</label>
            </div>
            <div class="col-xs-4">
               <input type="text" name="address3" class="address" value="<?php echo $form_data['address3'];?>"/>
               
            </div>
          </div>

           
            <div class="row m-b-3">
            <div class="col-xs-3">
                <label class="address">Remarks</label>
                </div>
            <div class="col-xs-4">
                <textarea type="text" id="remarks" class="m_input_default" name="remarks"><?php echo $form_data['remarks'];?></textarea>
            </div>
          </div>

    </div> <!-- //4 -->

</div> <!-- mainRow -->

    <div class="purchase_medicine_tbl_box" id="medicine_table">
       <div class="box_scroll" > <!-- class="left" on 07-12-2017-->
            <table class="table table-bordered table-striped m_pur_tbl1">
                <thead class="bg-theme">
                    <tr>
                        <th class="40" align="center"><input type="checkbox" name="" onClick="toggle(this); add_check();"></th>
                            <th>Medicine Name</th>
                            <th>Packing</th>
                            <th>Conv.</th>
                            <th>Medicine Code</th>
                            <th>HSN No.</th>
                            <th>Mfg.Company</th>
                            <th>Barcode</th>
                            <th>Unit1</th>
                            <th>Unit2</th>
                            <th>MRP</th>
                            <th>Purchase Rate</th>
                            <th>Discount(%)</th>
                            <th>CGST (%)</th>
                            <th>SGST (%)</th>
                            <th>IGST (%)</th>
                        
                    </tr>
                </thead>
                <tbody>
                   <tr id="previours_row">
                        <td colspan=""></td>
                        <td colspan=""><input type="text" name="medicine_name" class="w-150px" id="medicine_name" onkeyup="search_func(this.value);" /></td>
                        <td colspan=""><input type="text" name="packing" id="packing" class="w-60px"  onkeyup="search_func(this.value);"/></td>
                         <td colspan=""><input type="text" name="conv" class="w-60px" id="conv" onkeyup="search_func(this.value);"/></td>

                        <td colspan=""><input type="text" name="medicine_code" class="w-60px" id="medicine_code" onkeyup="search_func(this.value);" /></td>
                        <td colspan=""><input type="text" name="hsn_no" id="hsn_no" class="w-60px"  onkeyup="search_func(this.value);" /></td>
                        <td colspan=""><input type="text" name="medicine_company" class="w-150px"  id="medicine_company" onkeyup="search_func(this.value);" /></td>
                        <td colspan=""><input type="text" name="bar_code" id="bar_code" class="w-60px" onkeyup="search_func(this.value);" /></td>
                        
                       <td colspan="">
                        <select name="unit1" value="unit1" class="w-150px" onchange="search_func();">
                        <option value="">
                          Select Unit1  
                        </option>
                        <?php foreach($unit_list as $unit1){?>
                        <option value="<?php echo $unit1->id;?>"><?php echo $unit1->medicine_unit;?></option>
                        <?php }?>
                        </select></td>
                        <td colspan=""><select name="unit2" value="unit2" onchange="search_func();" class="w-150px" > 
                        <option value="">
                          Select Unit2  
                        </option>
                        <?php foreach($unit_list as $unit1){?>
                        <option value="<?php echo $unit1->id;?>"><?php echo $unit1->medicine_unit;?></option>
                        <?php }?>
                        </select></td>
                        
                        <td colspan=""><input type="text" name="mrp" id="mrp" class="w-60px"  onkeyup="search_func(this.value);" /></td>
                        <td colspan=""><input type="text" name="p_rate" id="p_rate" class="w-60px"  onkeyup="search_func(this.value);"/></td>
                        <td colspan=""><input type="text" name="discount" id="discount" class="w-80px"  onkeyup="search_func(this.value);"/></td>
                        <td colspan=""><input type="text" name="cgst" id="cgst" class="w-60px" onkeyup="search_func(this.value);" /></td>
                         <td colspan=""><input type="text" name="sgst" id="sgst" class="w-60px" onkeyup="search_func(this.value);" /></td>
                          <td colspan=""><input type="text" name="igst" id="igst" class="w-60px" onkeyup="search_func(this.value);" /></td>
                        
                    </tr>

                   <tr class="append_row">
                        <td colspan="16" class="text-danger" align="center">No record found</td>
                    </tr>
                </tbody>
            </table>
        </div> <!-- left -->
        
        <div class="right relative">
            <div class="fixed">
                <button class="btn-save" id="purchase_submit" type="button"><i class="fa fa-floppy-o"></i> <?php echo $button_value; ?></button>
                <!--<button class="btn-save"><i class="fa fa-refresh"></i> Update</button>-->
                <a href="<?php echo base_url('purchase');?>" class="btn-anchor"><i class="fa fa-sign-out"></i> Exit</a>
            </div>
        </div> <!-- dont delete this div -->

        <!-- right -->
    </div> <!-- purchase_medicine_tbl_box -->


<?php //if(empty($this->uri->segment(4))){ ?>
    <div class="purchase_medicine_tbl_box" id="medicine_select">
       
    </div> <!-- purchase_medicine_tbl_box -->
<div class="purchase_medicine_bottom">
        <div class="left">
            <div class="right_box">
                <div class="purchase_medicine_mod_of_payment">
                   <b>Mode of Payment</b>
                    <select  name="payment_mode" onChange="payment_function(this.value,'');">
                       <?php foreach($payment_mode as $payment_mode) 
                       {?>
                        <option value="<?php echo $payment_mode->id;?>" <?php if($form_data['payment_mode']== $payment_mode->id){ echo 'selected';}?>><?php echo $payment_mode->payment_mode;?></option>
                        <?php }?>
                         
                    </select>
                </div>
                 <div id="updated_payment_detail">
                 <?php if(!empty($form_data['field_name']))
                 { foreach ($form_data['field_name'] as $field_names) {

                      $tot_values= explode('_',$field_names);

                    ?>

                   <div class="purchase_medicine_mod_of_payment"><label><?php echo $tot_values[1];?><span class="star">*</span></label>
                   <input type="text" name="field_name[]" value="<?php echo $tot_values[0];?>" onkeypress="payment_calc_all();">
                   <input type="hidden" name="field_id[]" value="<?php echo $tot_values[2];?>" onkeypress="payment_calc_all();">
                      <div class="f_right">
                     <?php 
                        if(empty($tot_values[0]))
                        {
                            if(!empty($form_error)){ echo '<div class="text-danger">The '.strtolower($tot_values[1]).' field is required.</div>'; } 
                        }
                      ?>
                     </div>
                   </div>
                   
                   <?php } }?>
                 
                   </div>
                  
                <div id="payment_detail">
                    

                </div>

                <div class="purchase_medicine_mod_of_payment">
                    <label>Total Amount</label>
                    <input type="text" name="total_amount" id="total_amount" onChange="payment_calc_all();" value="<?php echo $form_data['total_amount'];?>" readonly>
                </div>

                    <?php $discount_vals = get_setting_value('ENABLE_DISCOUNT'); 
                        if(isset($discount_vals) && $discount_vals==1){?>

                    <div class="purchase_medicine_mod_of_payment">
                        <label>Discount</label>
                        <div class="grp">

                        <input class="input-tiny price_float" type="text" value="<?php echo $form_data['discount_percent'];?>" id="discount_all" onkeyup="payment_calc_all();" placeholder="%" name="discount_percent">

                        <input type="text" name="discount_amount"  id="discount_amount" value="<?php echo $form_data['discount_amount'];?>" readonly>
                        </div>
                    </div>

                        <?php }else{?>

                    <div class="purchase_medicine_mod_of_payment">
                        <label></label>
                        <div class="grp">
                        <input class="input-tiny price_float" type="hidden" value="0" id="discount_all" onkeyup="payment_calc_all();" placeholder="%" name="discount_percent">
                        <input type="hidden" name="discount_amount"  id="discount_amount" value="0" readonly>
                        </div>
                    </div>

                       <?php  }

                    ?>
                

           
              
                <div class="purchase_medicine_mod_of_payment">
                    <label>CGST(Rs.) </label>
                   <input type="text" name="cgst_amount"  id="cgst_amount"  value="<?php echo $form_data['cgst_amount'];?>" readonly>
                </div>

                 <div class="purchase_medicine_mod_of_payment">
                    <label>SGST(Rs.) </label>
                    <input type="text" name="sgst_amount"  id="sgst_amount"  value="<?php echo $form_data['sgst_amount'];?>" readonly>
                </div>

                <div class="purchase_medicine_mod_of_payment">
                    <label>IGST(Rs.) </label>
                   <input type="text" name="igst_amount"  id="igst_amount"  value="<?php echo $form_data['igst_amount'];?>" readonly>
                </div>

                <div class="purchase_medicine_mod_of_payment">
                    <label>Net Amount</label>
                    <input type="text" name="net_amount"  id="net_amount" onkeyup="payment_calc_all();" readonly value="<?php echo $form_data['net_amount'];?>">
                </div>
                <div class="purchase_medicine_mod_of_payment">
                    <label>Pay Amount<span class="star">*</span></label>
                    <input type="text" name="pay_amount"  id="pay_amount" value="<?php echo $form_data['pay_amount']; ?>" class=""  onblur="payemt_vals(1);">
                    <div class="f_right"><?php if(!empty($form_error)){ echo form_error('pay_amount'); } ?>
                    </div>
                </div>

                 <div class="purchase_medicine_mod_of_payment">
                    <label>Balance</label>
                     <input type="text" name="balance_due"  id="balance_due" value="<?php echo $balance= $form_data['net_amount']-$form_data['pay_amount']; ?>" class="price_float" readonly >
                   
                </div>


            </div> <!-- right_box -->

            
        </div> <!-- left -->
        <div class="right">
           
        </div> <!-- dont delete this div -->
    </div> <!-- purchase_medicine_bottom -->





</div> <!-- purchase_medicine -->

</section> <!-- section close -->
</form>




<?php
$this->load->view('include/footer');
?>
 
</div><!-- container-fluid -->
</body>
</html>

<div id="confirm_print" class="modal fade dlt-modal">
      <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-theme"><h4>Are You Sure?</h4></div>
          <div class="modal-footer">
            <a  data-dismiss="modal" class="btn-anchor"  onClick="return print_window_page('<?php echo base_url("purchase/print_purchase_recipt"); ?>');">Print</a>

            <button type="button" data-dismiss="modal" class="btn-cancel" id="cancel">Close</button>
          </div>
        </div>
      </div>  
    </div>
<script>
$('.datepicker3').datetimepicker({
     format: 'LT'
  });
$('#purchase_submit').click(function(){  
    $(':input[id=purchase_submit]').prop('disabled', true);
   $('#purchase_form').submit();
});

$('#igst_percent').keyup(function(){
  if ($(this).val() > 100){
      alert('IGST should be less then 100');
    //$('#error_msg_vat').html('Gst should be less then 100');
  }
});
$('#cgst_percent').keyup(function(){
  if ($(this).val() > 100){
      alert('CGST should be less then 100');
    //$('#error_msg_vat').html('Gst should be less then 100');
  }
});
$('#sgst_percent').keyup(function(){
  if ($(this).val() > 100){
      alert('SGST should be less then 100');
    //$('#error_msg_vat').html('Gst should be less then 100');
  }
});


 function payment_function(value,error_field){
    $('#updated_payment_detail').html('');
     $.ajax({
        type: "POST",
        url: "<?php echo base_url('purchase/get_payment_mode_data')?>",
        data: {'payment_mode_id' : value,'error_field':error_field},
       success: function(msg){
         $('#payment_detail').html(msg);
        }
    });
     
   
 $('.datepicker').datepicker({
                    format: "dd-mm-yyyy",
                    autoclose: true
                });  
   
  }
$(document).ready(function(){
   
  payment_calc_all();
   
    $('.datepicker').datepicker({
                    format: "dd-mm-yyyy",
                    autoclose: true
                }); 
});

function add_check()
{
    var timerA = setInterval(function(){  
      child_medicine_vals();
      clearInterval(timerA); 
    }, 1000);
}
function isNumberKey(evt) {
    var charCode = (evt.which) ? evt.which : event.keyCode;
    if (charCode != 46 && charCode > 31 && (charCode < 48 || charCode > 57)) {
        return false;
    } else {
        return true;
    }      
}
function add_new_medicine(){
    var $modal = $('#load_add_modal_popup');
    $modal.load('<?php echo base_url().'medicine_entry/add/' ?>',
    {
    //alert();
    //'id1': '1',
    //'id2': '2'
    },
    function(){
    $modal.modal('show');
    });
}
function search_func()
{  
    var medicine_name = $('#medicine_name').val();
    var medicine_code = $('#medicine_code').val();
    var packing = $('#packing').val();
    var hsn_no = $('#hsn_no').val();
    var conv = $('#conv').val();
    var bar_code = $('#bar_code').val();
    //var mfc_date = $('#mfc_date').val();
    var medicine_company = $('#medicine_company').val();
    var unit1 = $("#unit1" ).val();
    //alert(unit1);
    var unit2 = $('#unit2').val();
    var mrp = $('#mrp').val();
    var p_rate = $('#p_rate').val();
    var discount = $('#discount').val();
   // var vat = $('#vat').val();
   var cgst = $('#cgst').val();
   var sgst = $('#sgst').val();
   var igst = $('#igst').val();

    $.ajax({
       type: "POST",
       url: "<?php echo base_url('purchase/ajax_list_medicine')?>",
       data: {'medicine_name' : medicine_name,'medicine_code':medicine_code,'hsn_no':hsn_no,'conv':conv,'medicine_company':medicine_company,'unit1':unit1,'unit2':unit2,'mrp':mrp,'p_rate':p_rate,'discount':discount,'cgst':cgst,'sgst':sgst,'igst':igst,'packing':packing,'bar_code':bar_code},
       dataType: "json",
       success: function(msg){
       // alert(msg.data);
        $(".append_row").remove();
        $("#previours_row").after(msg.data);
                   //Receiving the result of search here
       }
    }); 
}

function child_medicine_vals() 
  {      
   
    var allVals = [];
   
       $('.child_checkbox').each(function() 
       {
         if($(this).prop('checked')==true && !isNaN($(this).val()))
         {
              allVals.push($(this).val());
              
              //barcode.push($('#bar_code_'+this).val());
         } 
       });

       if(allVals!="")
        {
       send_medicine(allVals);
       search_func();
       }
  } 
 function toggle(source) 
  {  
   
     checkboxes = document.getElementsByClassName('child_checkbox');
      for(var i=0, n=checkboxes.length;i<n;i++) {
      checkboxes[i].checked = source.checked;
      }
  }
  function toggle_new(source) 
  {  
     checkboxes = document.getElementsByClassName('booked_checkbox');
      for(var i=0, n=checkboxes.length;i<n;i++) {
      checkboxes[i].checked = source.checked;
      }
  }
   function send_medicine(allVals)
  {   
      
       var barncode=[];
       if(allVals!="")
       {
        //alert(allVals);
            $.each(allVals,function(i){
            // alert(allVals[i]);
            if($('#bar_code_'+allVals[i]).val()!='')
            {
              barncode.push($('#bar_code_'+allVals[i]).val());
              setTimeout(function() {validation_bar_code(allVals[i]); }, 80);   
            }
            else
            {
             barncode.push($('#bar_code_'+allVals[i]).val());   
            }
           
             
            });
            
        //  var bar_code = $('#bar_code_'+allVals).val();
          $.ajax({
                  type: "POST",
                  url: "<?php echo base_url('purchase/set_medicine');?>",
                  data: {medicine_id: allVals, barcode:barncode},
                  dataType: "json",
                  success: function(result) 
                  { 

                    $('#medicine_select').html(result.data); 
                    search_func(); 
                    list_added_medicine();
                    payment_calc_all();  
                  }
              });
       }      
  }

  function list_added_medicine()
  {
    $.ajax({
                    url: "<?php echo base_url(); ?>purchase/ajax_added_medicine", 
                    dataType: "json",
                   success: function(result)
                    {

                      $('#medicine_select').html(result.data); 
                      //payment_calc_all();
                   } 
                 });   
  }

  function medicine_list_vals() 
  {      

       var allVals = [];
       $('.booked_checkbox').each(function() 
       {
         if($(this).prop('checked')==true && !isNaN($(this).val()))
         {
              allVals.push($(this).val());
         } 
       });
        if(allVals!="")
        {
        remove_medicine(allVals);
        search_func();
        }
  
  } 
  function remove_medicine(allVals)
  { 
   if(allVals!="")
   {
      $.ajax({
              type: "POST",
              url: "<?php echo base_url('purchase/remove_medicine_list');?>",
               dataType: "json",
              data: {medicine_id: allVals},
              success: function(result) 
              {  
                $('#medicine_select').html(result.data); 
                search_func();
                 
                list_added_medicine();  
                payment_calc_all();
               
                $('#discount_amount').val('');
                $('#total_amount').val('');
                $('#net_amount').val('');
                $('#igst_amount').val('');
                $('#cgst_amount').val('');
                $('#sgst_amount').val('');
                $('#discount_all').val('');
                $('#igst_percent').val('');
                $('#cgst_percent').val('');
                $('#sgst_percent').val('');
                $('#balance_due').val('');
                $('#pay_amount').val('');

              }
          });
   }
  }


 function payment_cal_perrow(ids){


    var max_qty=+('<?php echo $max_pur_qty;?>');
 
    var purchase_rate = $('#purchase_rate_'+ids).val();
    var conversion = $('#conversion_'+ids).val();
    var medicine_id= $('#medicine_id_'+ids).val();
    var batch_no= $('#batch_no_'+ids).val();
    var hsn_no= $('#hsn_no_'+ids).val();
    var unit1= +($('#unit1_'+ids).val());
    var freeunit1= $('#freeunit1_'+ids).val();
    
    var freeunit2= $('#freeunit2_'+ids).val();
     var bar_code= $('#bar_code_'+ids).val();
   // alert('#unit1'+ids);
    //alert(unit1);
    var unit2= $('#unit2_'+ids).val();
    var mrp= $('#mrp_'+ids).val();
    var manuf_date= $('#manuf_date_'+ids).val();
    var expiry_date= $('#expiry_date_'+ids).val();
    //var vat= $('#vat_'+ids).val();
    var cgst= $('#cgst_'+ids).val();
    var sgst= $('#sgst_'+ids).val();
    var igst= $('#igst_'+ids).val();
    var discount= $('#discount_'+ids).val();
    $('#unit1_max_'+ids).text('');
    //if(unit1 <= max_qty)
    //{
    
        $.ajax({
                type: "POST",
                url: "<?php echo base_url(); ?>purchase/payment_cal_perrow/", 
                dataType: "json",
                data: 'purchase_rate='+purchase_rate+'&batch_no='+batch_no+'&mrp='+mrp+'&conversion='+conversion+'&manuf_date='+manuf_date+'&unit1='+unit1+'&unit2='+unit2+'&medicine_id='+medicine_id+'&expiry_date='+expiry_date+'&cgst='+cgst+'&sgst='+sgst+'&igst='+igst+'&discount='+discount+'&hsn_no='+hsn_no+'&freeunit1='+freeunit1+'&freeunit2='+freeunit2+'&bar_code='+bar_code,
                success: function(result)
                {

                   $('#total_amount_'+ids).val(result.total_amount); 
                  
                   payment_calc_all();
                } 
              });
    //}
    //else{
       // $('#unit1_max_'+ids).text('Purchase qty should be less than '+max_qty);
   // }
 }

   function payment_calc_all(pay)
    {
        
        var discount = $('#discount_all').val();
        var discount_amount= $('#discount_amount').val();
        var data_id= '<?php echo $form_data['data_id'];?>';
       // var vat = $('#vat_percent').val(); 
        var net_amount = $('#net_amount').val();
        var pay_amount = $('#pay_amount').val();
         var cgst = $('#cgst_percent').val();
         var sgst = $('#sgst_percent').val(); 
         var igst = $('#igst_percent').val(); 
         var discount_type = $('#discount_type').val(); 
      //var balance_due = $('#balance_due').val();
      /*if(pay !=undefined)
      {*/
            $.ajax({
            type: "POST",
            url: "<?php echo base_url(); ?>purchase/payment_calc_all/", 
            dataType: "json",
             data: 'discount='+discount+'&sgst='+sgst+'&igst='+igst+'&cgst='+cgst+'&net_amount='+net_amount+'&pay_amount='+pay_amount+'&pay='+pay+'&discount_amount='+discount_amount+'&data_id='+data_id+'&discount_type='+discount_type,
            success: function(result)
            {                
                  $('#discount_amount').val(result.discount_amount);
                  $('#total_amount').val(result.total_amount);
                  $('#net_amount').val(result.net_amount);
                  $('#pay_amount').val(result.pay_amount);
                 // $('#vat_amount').val(result.vat_amount);
                  $('#cgst_amount').val(result.cgst_amount.toFixed(2));
                  $('#igst_amount').val(result.igst_amount);
                  $('#sgst_amount').val(result.sgst_amount);
                  $('#discount_all').val(result.discount);
                  //$('#vat_percent').val(result.vat);
                  $('#cgst_percent').val(result.cgst);
                  $('#sgst_percent').val(result.sgst);
                  $('#igst_percent').val(result.igst);
                  $('#balance_due').val(result.balance_due);
                 
            } 
          });
        }
  //  }

    function payemt_vals(pay)
  {
   
     var timerA = setInterval(function(){  
          payment_calc_all(pay);
          clearInterval(timerA); 
        }, 80);
  }
$('#discount_all').keyup(function(){
  if ($(this).val() > 100){
      alert('Discount should be less then 100');
    //$('#error_msg_vat').html('Gst should be less then 100');
  }
});
 function validation_bar_code(id){
  
    $('#unit1_error_'+id).html('');
    //var val=  $('#batch_no_'+id).val();
   // var unit2= $('#qty_'+id).val();
    var mbid =$('#medicine_id_'+id).val();
    var bar_code =$('#bar_code_'+id).val();
    $.ajax({
      type: "POST",
      url: "<?php echo base_url(); ?>purchase/check_bar_code/", 
      dataType: "json",
       data: 'mbid='+mbid+'&bar_code='+bar_code,
      success: function(result)
      {
        //alert(result);
         if(result==1){
          $('#barcode_error_'+id).html('This Barcode already in used');
         }else{
           $('#barcode_error_'+id).html('');
         }

      } 
    });
  }

$(".txt_firstCap").on('keyup', function(){

   var str = $('.txt_firstCap').val();
   var part_val = str.split(" ");
    for ( var i = 0; i < part_val.length; i++ )
    {
      var j = part_val[i].charAt(0).toUpperCase();
      part_val[i] = j + part_val[i].substr(1);
    }
      
   $('.txt_firstCap').val(part_val.join(" "));
  
  });
  
  <?php
 $flash_success = $this->session->flashdata('success');
 if(isset($flash_success) && !empty($flash_success))
 {
   echo 'flash_session_msg("'.$flash_success.'");';
 }
 $purchase_id = $this->session->userdata('purchase_id');
 ?>
</script>
<script>
$('documnet').ready(function(){
 <?php if(isset($_GET['status']) && isset($purchase_id) && $_GET['status']=='print'){?>
  $('#confirm_print').modal({
      backdrop: 'static',
      keyboard: false
        })
  
  .one('click', '#cancel', function(e)
    { 
        window.location.href='<?php echo base_url('purchase/add');?>'; 
    }) ;
   
       
  <?php }?>
 });
</script>
<!--- Search Estimate ----------->
<!--<script src="<?php //echo ROOT_JS_PATH; ?>jquery-ui.min.js"></script>
  <link rel="stylesheet" type="text/css" href="<?php //echo ROOT_CSS_PATH; ?>jquery-ui.css">-->

<script type="text/javascript">

    $(function () {
 
    var i=1;
    var getData = function (request, response) {  
        
        row = i ;
        $.ajax({
        url : "<?php echo base_url('purchase/estimate_medicine/'); ?>" + request.term,
        dataType: "json",
        method: 'post',
      data: { 
         name_startsWith: request.term,
         
         row_num : row
      },
       success: function( data ) {
      //    alert(data);
         response( $.map( data, function( item ) {
          var code = item.split("|");
          return {
            label: code[0],
            value: code[0],
            data : item
          }
        }));
      }
      });

       
    };

    var selectItem = function (event, ui) 
    {
            //$(".medicine_val").val(ui.item.value);
            var names = ui.item.data.split("|");
           
            $('.estimate_no').val(names[0]);
            $('#ven_name').val(names[2]);
            $('#vendor_gst_no').val(names[3]); 
            $('.m_number').val(names[5]);
            $('.address').val(names[6]);
            set_estimate_medicine(names[0]);
            return false;
    }

    $(".estimate_no").autocomplete({
        source: getData,
        select: selectItem,
        minLength: 1,
        change: function() {  
            //$("#default_vals").val("").css("display", 2);
        }
    });
    });



  function set_estimate_medicine(allVals)
  {   
     //alert(allVals);
   if(allVals!="")
   {
      $.ajax({
              type: "POST",
              url: "<?php echo base_url('purchase/set_estimate_medicine');?>",
              data: {purchase_id: allVals},
              dataType: "json",
              success: function(result) 
              {
              
                $('#medicine_select').html(result.data); 
                //list_added_medicine();
                payment_calc_all();  
             }
          });
   }      
  } 
      function get_medicine_vender(m_id)
      {
        payment_cal_perrow(m_id);
       $.ajax({
              type: "POST",
              url: "<?php echo base_url('purchase/get_medicine_venders');?>",
              data: {medicine_id: m_id},
              dataType: "json",
              success: function(result) 
              {
                
                $('#vender_list').after(result); 
                
             }
          });
      }

      function get_purchase_rates(val,id)
      {
        payment_cal_perrow(id);
        $('#purchase_rate_'+id).val(+(val));
      }
    </script>
    <!--- Search Estimate ----------->
<div id="load_add_modal_popup" class="modal fade" role="dialog" data-backdrop="static" data-keyboard="false"></div>
</div>
<style>

.ui-autocomplete { z-index:2147483647; }
</style>


