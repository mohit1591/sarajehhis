<html><head>
<title>Doctor Report</title>
<?php
if($print_status==1)
{
?>
<script type="text/javascript">
window.print();
window.onfocus=function(){ window.close();}
</script>
<?php	
}
?>
<style>
body
{
	font-size: 10px;
}	
td
{
	padding-left:3px;
}

    .header-title {
        text-align: center;  /* Center the text */
        font-weight: bold;   /* Make the text bold */
        font-size: 18px;     /* Increase font size */
        padding: 10px;       /* Add padding inside the div */
        border: 2px solid black;  /* Add a solid border */
        margin-bottom: 20px; /* Add margin below for spacing */
        display: inline-block;
        width: 100%;
        box-sizing: border-box; /* Ensure padding does not affect width */
    }

</style>
</head><body>
    <div class="header-title"><?php echo $mainHeader; ?></div>
<table width="100%" cellpadding="0" cellspacing="0" border="1px" style="font:12px Arial;">
 <tr>
    <th>S.No.</th>
    <th>Doctor Reg. No.</th>
    <th>Doctor Name</th>
    <th>Specialization</th>
    <th>Doctor Type</th> 
    <th>Mobile</th>
    <th>Email</th>
    <th>Status</th> 
 </tr>
 
 <?php
   if(!empty($data_list))
   {
   	 ?>
   	 
   	 <?php
   	 $i=1;
   	 
   	 foreach($data_list as $doctors)
   	 {
          $state = "";
          if(!empty($doctors->state))
          {
          $state = " ( ".ucfirst(strtolower($doctors->state))." )";
          }
          $doctor_type = array('0'=>'Referral','1'=>'Attended','2'=>'Referral/Attended');
          $specilization_name ="";
          $specilization_name = $this->general_model->get_specilization_name($doctors->specilization_id);
          $doctor_types = $doctor_type[$doctors->doctor_type];
          $status = array('0'=>'Inactive','1'=>'Active');
          $doctor_status = $status[$doctors->status];
   	   ?>
   	    <tr>
   	        <td style="text-align: left;"><?php echo $i; ?>.</td>
      		 	<td style="text-align: left;"><?php echo $doctors->doctor_code; ?></td>
      		 	<td style="text-align: left;"><?php echo $doctors->doctor_name; ?></td>
            <td style="text-align: left;"><?php echo $specilization_name; ?></td>
      		 	<td style="text-align: left;"><?php echo $doctor_types; ?></td> 
      		 	<td style="text-align: left;"><?php echo $doctors->mobile_no; ?></td>
            <td style="text-align: left;"><?php echo $doctors->email; ?></td>
            <td style="text-align: left;"><?php echo $doctor_status; ?></td>   
		    </tr>
   	   <?php
   	   $i++;	
   	 }	
   }
 ?> 
</table>
</body></html>