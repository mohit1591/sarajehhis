<html><head>
<title>Day Care Patient Report</title>
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
</style>
</head><body>
<table width="100%" cellpadding="0" cellspacing="0" border="1px" style="font:13px Arial;">
 <tr>

    <!-- <th>S.No.</th> -->
    <th>Day Care No.</th>
    <th>Patient Name</th>
    <th><?php echo $data= get_setting_value('PATIENT_REG_NO');?></th>
    <th>Doctor Name</th>
    <th>Specialization</th>
    <th>Booking Date</th>
    <th>Age</th>
    <th>Gender</th>
    <th>Mobile No.</th>
    
   <!--  <th>Created Date</th> -->

 </tr>
 <?php
   if(!empty($data_list))
   {	 
   	 $i=1;
   	 foreach($data_list as $opds)
   	 {
          if($opds->booking_status==0)
            {
                $booking_status = '<font color="red">Pending</font>';
            }   
            elseif($opds->booking_status==1){
                $booking_status = '<font color="green">Confirm</font>';
            }                 
            elseif($opds->booking_status==2){
                $booking_status = '<font color="blue">Attended</font>';
            } 
           

            $attended_doctor_name ="";
            $attended_doctor_name = get_doctor_name($opds->attended_doctor);
            $specialization_id = get_specilization_name($opds->specialization_id);
            $booking_code = $opds->booking_code;
            
            $patient_name = $opds->patient_name;
            
            $booking_date = date('d-m-Y',strtotime($opds->booking_date));
             if($opds->appointment_date!='0000-00-00')
            {
              $appointment_date = date('d-m-Y',strtotime($opds->appointment_date)); 
            }
            else
            {
                $appointment_date = ""; 
            }
                $genders = array('0'=>'Female','1'=>'Male');
                $gender = $genders[$opds->gender];
                
                $age_y = $opds->age_y;
                $age_m = $opds->age_m;
                $age_d = $opds->age_d;
                $age = "";
                if($age_y>0)
                {
                $year = 'Years';
                if($age_y==1)
                {
                  $year = 'Year';
                }
                $age .= $age_y." ".$year;
                }
                if($age_m>0)
                {
                $month = 'Months';
                if($age_m==1)
                {
                  $month = 'Month';
                }
                $age .= ", ".$age_m." ".$month;
                }
                if($age_d>0)
                {
                $day = 'Days';
                if($age_d==1)
                {
                  $day = 'Day';
                }
                $age .= ", ".$age_d." ".$day;
                }
                $patient_age =  $age;
            $create_date = date('d-M-Y H:i A',strtotime($opds->created_date));
   	   ?>
   	    <tr>
   	       <!--  <td align="center">< ?php echo $i; ?>.</td> -->
      		 	<td><?php echo $opds->booking_code; ?></td>
            <td><?php echo $opds->patient_name; ?></td>
      		 	<td><?php echo $opds->patient_code; ?></td>
      		 	<td><?php echo $attended_doctor_name; ?></td>
            <td><?php echo $specialization_id; ?></td>
            <td><?php echo $booking_date; ?></td>
            <td><?php echo $patient_age; ?></td>
            <td><?php echo $opds->gender; ?></td>
            <td><?php echo $opds->mobile_no; ?></td>
            
            
      		 	<!-- <td>< ?php echo $create_date; ?></td> -->
      		 
      	
		 </tr>
   	   <?php
   	   $i++;	
   	 }	
   }
 ?> 
</table>
</body></html>