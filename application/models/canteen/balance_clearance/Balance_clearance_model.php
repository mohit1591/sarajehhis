<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Balance_clearance_model extends CI_Model 
{
var $column = array('hms_payment.credit','hms_payment.debit', 'hms_doctors.doctor_name');  
	var $order = array('id' => 'desc');

	public function __construct()
	{
		parent::__construct();
		$this->load->database();
	}
    
	
	public function patient_to_balclearlist($type=1)
	{
           
		$post = $this->input->post();
		//print'<pre>';print_r($post);die;
		$users_data = $this->session->userdata('auth_users');
		$balance_search_data = $this->session->userdata('balance_search_data');
		$result= array(); 
		if(isset($post) || !empty($post))
		{ 
			if(isset($balance_search_data) && !empty($balance_search_data))
			{ 
				$is_deleted_column = '';
				$where_deleted = '';
				if(in_array('1',$balance_search_data['section_id']))
				{
                  $is_deleted_column .= ', path_test_booking.is_deleted as test_deleted';
                  $where_deleted = ' path_test_booking.is_deleted!=2 ';
				}

				if(in_array('2',$balance_search_data['section_id']) || in_array('4',$balance_search_data['section_id']))
				{
                  $is_deleted_column .= ', hms_opd_booking.is_deleted as  opd_deleted';
                  $where_deleted = ' hms_opd_booking.is_deleted!=2 ';
				}

				if(in_array('3',$balance_search_data['section_id']))
				{
                  $is_deleted_column .= ', hms_medicine_sale.is_deleted as medicine_deleted';
                  $where_deleted = ' hms_medicine_sale.is_deleted!=2 ';
				}

				if(in_array('5',$balance_search_data['section_id']))
				{
                  $is_deleted_column .= ', hms_ipd_booking.is_deleted as ipd_deleted';
                  $where_deleted = ' hms_ipd_booking.is_deleted!=2 ';
				}
				if(in_array('7',$balance_search_data['section_id']))
				{
                  $is_deleted_column .= ', hms_vaccination_sale.is_deleted as vaccine_deleted';
                  $where_deleted = ' hms_vaccination_sale.is_deleted!=2 ';
				}
				
				if(in_array('8',$balance_search_data['section_id']))
				{
                  $is_deleted_column .= ', hms_operation_booking.is_deleted as ot_deleted';
                  $where_deleted = ' hms_operation_booking.is_deleted!=2';
				}
				if(in_array('10',$balance_search_data['section_id']))
				{
                  $is_deleted_column .= ', hms_blood_patient_to_recipient.is_deleted as blood_deleted';
                  $where_deleted = ' hms_blood_patient_to_recipient.is_deleted!=2';
				}
				if(in_array('13',$balance_search_data['section_id']))
				{
                 $is_deleted_column .= ', hms_ambulance_booking.is_deleted as ambulance_deleted';
                  $where_deleted = 'hms_ambulance_booking.is_deleted!=2';
				}
				
			}	

			if(!empty($where_deleted))
			{
				$where_deleted .= ' AND';
			}
			 
			//$this->db->select('hms_patient.*,hms_payment.branch_id, (CASE WHEN hms_payment.section_id = 1 THEN "Pathology" WHEN hms_payment.section_id = 2 THEN "OPD" WHEN hms_payment.section_id=3 THEN "Medicine" WHEN hms_payment.section_id=4 THEN "Billing" WHEN hms_payment.section_id=5 THEN "IPD" ELSE "" END) as department, hms_payment.created_date as date, hms_payment.discount_amount as discount, hms_payment.parent_id, hms_payment.section_id,hms_payment.type, (select sum(credit)-sum(debit) as balance from hms_payment as  payment where (CASE WHEN payment.section_id = 1 THEN path_test_booking.is_deleted !=2 WHEN payment.section_id IN (2,4) THEN hms_opd_booking.is_deleted !=2 WHEN payment.section_id = 3 THEN hms_medicine_sale.is_deleted !=2 WHEN payment.section_id = 5 THEN hms_ipd_booking.is_deleted !=2 ELSE TRUE END) AND payment.section_id = hms_payment.section_id AND payment.parent_id = hms_payment.parent_id) as balance '.$is_deleted_column.'');
                        $this->db->select('hms_patient.id,hms_patient.patient_name,hms_patient.mobile_no,hms_patient.patient_code,hms_payment.branch_id, (CASE WHEN hms_payment.section_id = 1 THEN "Pathology" WHEN hms_payment.section_id = 2 THEN "OPD" WHEN hms_payment.section_id=3 THEN "Medicine" WHEN hms_payment.section_id=4 THEN "Billing" WHEN hms_payment.section_id=5 THEN "IPD" WHEN hms_payment.section_id=7 THEN "Vaccine" WHEN hms_payment.section_id=8 THEN "OT"  WHEN hms_payment.section_id=13 THEN "Ambulance" ELSE "" END) as department, hms_payment.created_date as date, hms_payment.discount_amount as discount, hms_payment.parent_id, hms_payment.section_id,hms_payment.type, (select sum(credit)-sum(debit) as balance from hms_payment as  payment where 
                        	'.$where_deleted.'
                        	 payment.section_id = hms_payment.section_id AND payment.parent_id = hms_payment.parent_id  AND branch_id = "'.$users_data['parent_id'].'") as balance '.$is_deleted_column.'');
			$this->db->from('hms_patient');
			//$this->db->where('(select sum(credit)-sum(debit) as balance from hms_payment as  payment where payment.section_id = hms_payment.section_id AND payment.parent_id = hms_payment.parent_id AND branch_id = "'.$users_data['parent_id'].'") > 0');
			$this->db->where('hms_patient.is_deleted != 2');
			//$this->db->where('(select sum(hms_payment.credit)-sum(hms_payment.debit) from hms_payment as payment where patient_id = hms_patient.id) > 0');
			$this->db->join('hms_payment','hms_payment.patient_id=hms_patient.id'); 
			$this->db->join('hms_doctors','hms_doctors.id=hms_payment.doctor_id AND hms_doctors.doctor_pay_type = 1','left');  
			if(!empty($post['patient_name']))
			{ 
			   $this->db->like('hms_patient.patient_name',$post['patient_name']);
			}
			if(!empty($post['mobile_no']))
			{
			   $this->db->like('hms_patient.mobile_no',$post['mobile_no']);
			}
			

			if(isset($post['sub_branch_id']) && !empty($post['sub_branch_id']))
			{
			$this->db->where('hms_payment.branch_id',$post['sub_branch_id']);
			} 
			else
			{
			   $this->db->where('hms_payment.branch_id',$users_data['parent_id']);
			}

			if(in_array('1',$balance_search_data['section_id']))
			{
              $this->db->join('path_test_booking','path_test_booking.id = hms_payment.parent_id AND hms_payment.section_id = 1','left'); 
			}

			if(in_array('2',$balance_search_data['section_id']) || in_array('4',$balance_search_data['section_id']))
			{
              $this->db->join('hms_opd_booking','hms_opd_booking.id = hms_payment.parent_id AND hms_payment.section_id IN (2,4)','left'); 
			}

			if(in_array('3',$balance_search_data['section_id']))
			{
              $this->db->join('hms_medicine_sale','hms_medicine_sale.id = hms_payment.parent_id AND hms_payment.section_id = 3','left'); 
			}

			if(in_array('5',$balance_search_data['section_id']))
			{
              $this->db->join('hms_ipd_booking','hms_ipd_booking.id = hms_payment.parent_id AND hms_payment.section_id = 5','left'); 
			}
			if(in_array('7',$balance_search_data['section_id']))
			{
              $this->db->join('hms_vaccination_sale','hms_vaccination_sale.id = hms_payment.parent_id AND hms_payment.section_id = 7','left'); 
			}
			
			if(in_array('8',$balance_search_data['section_id']))
			{
              $this->db->join('hms_operation_booking','hms_operation_booking.id = hms_payment.parent_id AND hms_payment.section_id = 8','left');
			}
			
			if(in_array('10',$balance_search_data['section_id']))
			{
              $this->db->join('hms_blood_patient_to_recipient','hms_blood_patient_to_recipient.id = hms_payment.parent_id AND hms_payment.section_id = 10','left'); 
			}
			if(in_array('13',$balance_search_data['section_id']))
			{
              $this->db->join('hms_ambulance_booking','hms_ambulance_booking.id = hms_payment.parent_id AND hms_payment.section_id = 13','left'); 
			}

      


			if(isset($balance_search_data) && !empty($balance_search_data))
			{   

				/*if(in_array('1',$balance_search_data['section_id']))
				{
                  $this->db->join('path_test_booking','path_test_booking.id = hms_payment.parent_id','left'); 
				}

				if(in_array('2',$balance_search_data['section_id']) || in_array('4',$balance_search_data['section_id']))
				{
                  $this->db->join('hms_opd_booking','hms_opd_booking.id = hms_payment.parent_id','left');
				}

				if(in_array('3',$balance_search_data['section_id']))
				{ 
                  $this->db->join('hms_medicine_sale','hms_medicine_sale.id = hms_payment.parent_id','left'); 
				}

				if(in_array('5',$balance_search_data['section_id']))
				{ 
                  $this->db->join('hms_ipd_booking','hms_ipd_booking.id = hms_payment.parent_id','left'); 
				}*/
				$imp_sec_id = implode(',', $balance_search_data['section_id']);
				$this->db->where('hms_payment.section_id IN ('.$imp_sec_id.')');
			}
			
			$emp_ids='';
    		if($users_data['emp_id']>0)
    		{
    			if($users_data['record_access']=='1')
    			{
    				$emp_ids= $users_data['id'];
    			}
    		}
    		elseif(!empty($balance_search_data["employee"]) && is_numeric($balance_search_data['employee']))
            {
                $emp_ids=  $balance_search_data["employee"];
            }
			
			if(isset($emp_ids) && !empty($emp_ids))
			{ 
				$this->db->where('hms_patient.created_by IN ('.$emp_ids.')');
			}

			$this->db->group_by('hms_payment.parent_id, hms_payment.section_id');
			$this->db->having('balance>0');
			$this->db->order_by('hms_payment.id','DESC');
			$query = $this->db->get();
		//	echo $this->db->last_query();die;
			$result = $query->result_array();
         
		}
		return $result;
	}

	public function payment_to_branch()
	{
		
		$post = $this->input->post();
		$users_data = $this->session->userdata('auth_users');
		if(isset($post) && !empty($post))
		{
			$time_c= date("H:i:s");
			$paid_date = date('Y-m-d', strtotime($post['paid_date']));
			$data = array(
							'branch_id'=>$post['branch_id'],
							'parent_id'=>$post['parent_id'], 
							'section_id'=>$post['section_id'], 
							'patient_id'=>$post['patient_id'], 
							'debit'=>$post['balance'],
							'pay_mode'=>$post['payment_mode'],
							//'bank_name'=>$post['bank_name'],
							//'cheque_no'=>$post['cheque_no'],
							//'transection_no'=>$post['transection_no'],
							//'card_no'=>$post['card_no'],
							'created_by'=>$users_data['id'],
							'created_date'=>$paid_date.' '.$time_c
							 );
			 
			$this->db->insert('hms_payment',$data);
			$insert_id= $this->db->insert_id();

		if($post['section_id']==1)
		{
			if(in_array('218',$users_data['permission']['section']))
			{
			if($post['balance']>0)
			{
			$hospital_receipt_no= check_hospital_receipt_no();
			$data_receipt_data= array('branch_id'=>$users_data['parent_id'],
										'section_id'=>11,
										'payment_id'=>$insert_id,
										'parent_id'=>$post['parent_id'],
										'reciept_prefix'=>$hospital_receipt_no['prefix'],
										'reciept_suffix'=>$hospital_receipt_no['suffix'],
										'created_by'=>$users_data['id'],
										'created_date'=>date('Y-m-d H:i:s')
										);
			$this->db->insert('hms_branch_hospital_no',$data_receipt_data);

			
			}
			}
		}
		if($post['section_id']==2)
		{
			if(in_array('218',$users_data['permission']['section']))
			{
			if($post['balance']>0)
			{
			$hospital_receipt_no= check_hospital_receipt_no();
			$data_receipt_data= array('branch_id'=>$users_data['parent_id'],
										'section_id'=>8,
										'payment_id'=>$insert_id,
										'parent_id'=>$post['parent_id'],
										'reciept_prefix'=>$hospital_receipt_no['prefix'],
										'reciept_suffix'=>$hospital_receipt_no['suffix'],
										'created_by'=>$users_data['id'],
										'created_date'=>date('Y-m-d H:i:s')
										);
			$this->db->insert('hms_branch_hospital_no',$data_receipt_data);
			//echo $this->db->last_query(); exit;
			}
			}
		}
		if($post['section_id']==3)
		{
			if(in_array('218',$users_data['permission']['section']))
			{
			if($post['balance']>0)
			{
			$hospital_receipt_no= check_hospital_receipt_no();
			$data_receipt_data= array('branch_id'=>$users_data['parent_id'],
										'section_id'=>10,
										'payment_id'=>$insert_id,
										'parent_id'=>$post['parent_id'],
										'reciept_prefix'=>$hospital_receipt_no['prefix'],
										'reciept_suffix'=>$hospital_receipt_no['suffix'],
										'created_by'=>$users_data['id'],
										'created_date'=>date('Y-m-d H:i:s')
										);
			$this->db->insert('hms_branch_hospital_no',$data_receipt_data);
			}
			}
		}
		if($post['section_id']==4)
		{
			if(in_array('218',$users_data['permission']['section']))
			{
			if($post['balance']>0)
			{
			$hospital_receipt_no= check_hospital_receipt_no();
			$data_receipt_data= array('branch_id'=>$users_data['parent_id'],
										'section_id'=>12,
										'payment_id'=>$insert_id,
										'parent_id'=>$post['parent_id'],
										'reciept_prefix'=>$hospital_receipt_no['prefix'],
										'reciept_suffix'=>$hospital_receipt_no['suffix'],
										'created_by'=>$users_data['id'],
										'created_date'=>date('Y-m-d H:i:s')
										);
			$this->db->insert('hms_branch_hospital_no',$data_receipt_data);
			}
			}
		}
		if($post['section_id']==5)
		{
			if(in_array('218',$users_data['permission']['section']))
			{
			if($post['balance']>0)
			{
			$hospital_receipt_no= check_hospital_receipt_no();
			$data_receipt_data= array('branch_id'=>$users_data['parent_id'],
										'section_id'=>9,
										'payment_id'=>$insert_id,
										'parent_id'=>$post['parent_id'],
										'reciept_prefix'=>$hospital_receipt_no['prefix'],
										'reciept_suffix'=>$hospital_receipt_no['suffix'],
										'created_by'=>$users_data['id'],
										'created_date'=>date('Y-m-d H:i:s')
										);
			$this->db->insert('hms_branch_hospital_no',$data_receipt_data);
			}
			}
		}

		if($post['section_id']==7)
		{
			if(in_array('218',$users_data['permission']['section']))
			{
			if($post['balance']>0)
			{
			$hospital_receipt_no= check_hospital_receipt_no();
			$data_receipt_data= array('branch_id'=>$users_data['parent_id'],
										'section_id'=>13,
										'payment_id'=>$insert_id,
										'parent_id'=>$post['parent_id'],
										'reciept_prefix'=>$hospital_receipt_no['prefix'],
										'reciept_suffix'=>$hospital_receipt_no['suffix'],
										'created_by'=>$users_data['id'],
										'created_date'=>date('Y-m-d H:i:s')
										);
			$this->db->insert('hms_branch_hospital_no',$data_receipt_data);
			}
			}
		}
		
		if($post['section_id']==8)
		{
			if(in_array('218',$users_data['permission']['section']))
			{
			if($post['balance']>0)
			{
			$hospital_receipt_no= check_hospital_receipt_no();
			$data_receipt_data= array('branch_id'=>$users_data['parent_id'],
										'section_id'=>15,
										'payment_id'=>$insert_id,
										'parent_id'=>$post['parent_id'],
										'reciept_prefix'=>$hospital_receipt_no['prefix'],
										'reciept_suffix'=>$hospital_receipt_no['suffix'],
										'created_by'=>$users_data['id'],
										'created_date'=>date('Y-m-d H:i:s')
										);
			$this->db->insert('hms_branch_hospital_no',$data_receipt_data);
			}
			}
		}
		
		/* blood bank code */
		if($post['section_id']==10)
		{
			if(in_array('218',$users_data['permission']['section']))
			{
			if($post['balance']>0)
			{
			$hospital_receipt_no= check_hospital_receipt_no();
			$data_receipt_data= array('branch_id'=>$users_data['parent_id'],
										'section_id'=>18,
										'payment_id'=>$insert_id,
										'parent_id'=>$post['parent_id'],
										'reciept_prefix'=>$hospital_receipt_no['prefix'],
										'reciept_suffix'=>$hospital_receipt_no['suffix'],
										'created_by'=>$users_data['id'],
										'created_date'=>date('Y-m-d H:i:s')
										);
			$this->db->insert('hms_branch_hospital_no',$data_receipt_data);
			}
			}
		}

		/* blood bank code */


			/*add sales banlk detail*/
              if(!empty($post['field_name']))
                {
                $post_field_value_name= $post['field_name'];
                $counter_name= count($post_field_value_name); 
                for($i=0;$i<$counter_name;$i++) 
                {
                $data_field_value= array(
                'field_value'=>$post['field_name'][$i],
                'field_id'=>$post['field_id'][$i],
                'type'=>5,
                'section_id'=>8,
                'p_mode_id'=>$post['payment_mode'],
                'branch_id'=>$users_data['parent_id'],
                'parent_id'=>$insert_id,
                'ip_address'=>$_SERVER['REMOTE_ADDR']
                );
                $this->db->set('created_by',$users_data['id']);
                $this->db->set('created_date',date('Y-m-d H:i:s'));
                $this->db->insert('hms_payment_mode_field_value_acc_section',$data_field_value);

                }
                }

            /*add sales banlk detail*/  
			//echo $this->db->last_query(); exit;
			return $insert_id;
			
		}

	}

	public function patient_balance_receipt_data($id="",$section_id="")
	{
          
       
       // echo $this->session->userdata('balance');die;
		if(!empty($id))
		{ 
			$result_booking=array();
			$user_data = $this->session->userdata('auth_users');
			
			if($section_id==1)
			{
				$result_booking=array();
		    	$user_data = $this->session->userdata('auth_users');
				$this->db->select("path_test_booking.lab_reg_no, path_test_booking.booking_date,  path_test_booking.attended_doctor,  path_test_booking.referral_doctor,  hms_patient.*,hms_simulation.simulation,hms_payment_mode.payment_mode as payment_modes,hms_payment.debit, 
					(select sum(credit)-sum(debit) from hms_payment as sub_pay where sub_pay.section_id = hms_payment.section_id AND sub_pay.parent_id = hms_payment.parent_id AND sub_pay.parent_id = hms_payment.parent_id) as balance, 
					(select sum(credit)-sum(debit) from hms_payment as total_pay where total_pay.section_id = hms_payment.section_id AND total_pay.parent_id = hms_payment.parent_id AND  total_pay.patient_id = hms_payment.patient_id AND total_pay.created_date < hms_payment.created_date) as total_amount, hms_payment.pay_mode as payment_mode,hms_branch_hospital_no.reciept_prefix,hms_branch_hospital_no.reciept_suffix,hms_insurance_type.insurance_type as insurance_name, hms_insurance_company.insurance_company"); 
				$this->db->join('hms_patient','hms_patient.id = hms_payment.patient_id','left');
				
				$this->db->join('hms_insurance_type',' hms_insurance_type.id = hms_patient.insurance_type_id','left'); // insurance type
                $this->db->join('hms_insurance_company',' hms_insurance_company.id = hms_patient.ins_company_id','left'); // insurance type
                                $this->db->join('hms_branch_hospital_no','hms_branch_hospital_no.payment_id = hms_payment.id AND hms_branch_hospital_no.section_id=11','left');
				$this->db->join('hms_simulation','hms_simulation.id = hms_patient.simulation_id','left'); 
				$this->db->join('path_test_booking','path_test_booking.patient_id = hms_patient.id','left');     
				$this->db->join('hms_payment_mode','hms_payment_mode.id = hms_payment.pay_mode','left');
				$this->db->where('hms_payment.id = "'.$id.'"'); 
				$this->db->where('hms_payment.section_id',1); 
				$this->db->order_by('path_test_booking.id','DESC');  
				$this->db->from('hms_payment');
				$result_booking['booking_list'] = $this->db->get()->result();
				$billing_particuler_arr = array('test_id'=>'','test_name'=>'Balance Clearance', 'amount'=>$result_booking['booking_list'][0]->debit);
				$object = (object) $billing_particuler_arr; 
				$result_booking['booking_list']['test_booking_list'][0] = $object;
				//echo $this->db->last_query(); exit;
				return $result_booking;
		
			}
			if($section_id==3)
			{
			$this->db->select("hms_patient.*,hms_users.*,hms_patient.patient_name as name,hms_medicine_sale.sale_no as recepit_no,hms_medicine_sale.sale_date as date,hms_medicine_sale.remarks as remk,hms_doctors.doctor_name,hms_payment_mode.payment_mode,hms_payment.pay_mode,hms_patient.patient_code as code,hms_payment.id as p_id,hms_simulation.simulation,hms_payment.debit, (select sum(credit)-sum(debit) from hms_payment as sub_pay where sub_pay.section_id = hms_payment.section_id AND sub_pay.parent_id = hms_payment.parent_id AND sub_pay.patient_id = hms_payment.patient_id AND sub_pay.created_date <= hms_payment.created_date) as balance, (select sum(credit)-sum(debit) from hms_payment as total_pay where total_pay.section_id = hms_payment.section_id AND total_pay.parent_id = hms_payment.parent_id AND  total_pay.patient_id = hms_payment.patient_id AND total_pay.created_date < hms_payment.created_date) as total_amount,hms_payment.created_date as c_date,hms_branch_hospital_no.reciept_prefix,hms_branch_hospital_no.reciept_suffix"); 
			$this->db->join('hms_payment','hms_payment.patient_id = hms_patient.id');

			$this->db->join('hms_branch_hospital_no','hms_branch_hospital_no.payment_id = hms_payment.id AND hms_branch_hospital_no.section_id=10','left');

			$this->db->join('hms_simulation','hms_simulation.id = hms_patient.simulation_id','left'); 
			$this->db->join('hms_payment_mode','hms_payment_mode.id = hms_payment.pay_mode','left');
			
			$this->db->join('hms_medicine_sale','hms_medicine_sale.patient_id = hms_patient.id','left'); 
			$this->db->join('hms_doctors','hms_doctors.id = hms_medicine_sale.refered_id','left');
			$this->db->join('hms_users','hms_users.id = hms_payment.created_by');	 
            $this->db->where('hms_payment.section_id',3); 
			$this->db->where('hms_payment.id = "'.$id.'"'); 
			$this->db->group_by('hms_patient.id','DESC');  
			$this->db->from('hms_patient');
			$result_patient['sales_list'] = $this->db->get()->result();
			//echo $this->db->last_query(); exit;
			return $result_patient;
			}
			elseif($section_id==2)
			{
				//OPD Booking

				$this->db->select("hms_patient.*,hms_users.*,hms_patient.patient_name as name,hms_opd_booking.booking_code as recepit_no,hms_opd_booking.booking_date as date,hms_doctors.doctor_name,hms_payment_mode.payment_mode,hms_payment.pay_mode,hms_patient.patient_code as code,hms_payment.id as p_id,hms_simulation.simulation,hms_payment.debit, (select sum(credit)-sum(debit) from hms_payment as sub_pay where sub_pay.section_id = hms_payment.section_id AND sub_pay.parent_id = hms_payment.parent_id AND  sub_pay.patient_id = hms_payment.patient_id AND sub_pay.created_date <= hms_payment.created_date) as balance, (select sum(credit)-sum(debit) from hms_payment as total_pay where total_pay.section_id = hms_payment.section_id AND total_pay.parent_id = hms_payment.parent_id AND total_pay.patient_id = hms_payment.patient_id AND total_pay.created_date < hms_payment.created_date) as total_amount,hms_payment.created_date as c_date,hms_branch_hospital_no.reciept_prefix,hms_branch_hospital_no.reciept_suffix,(CASE WHEN hms_patient.insurance_type=1 THEN 'TPA' ELSE 'Normal' END ) as insurance_type_name, hms_insurance_type.insurance_type as insurance_name, hms_insurance_company.insurance_company"); 
				$this->db->join('hms_payment','hms_payment.patient_id = hms_patient.id');
$this->db->join('hms_branch_hospital_no','hms_branch_hospital_no.payment_id = hms_payment.id AND hms_branch_hospital_no.section_id=8','left');

			$this->db->join('hms_simulation','hms_simulation.id = hms_patient.simulation_id','left'); 
			$this->db->join('hms_payment_mode','hms_payment_mode.id = hms_payment.pay_mode');
				
			$this->db->join('hms_opd_booking','hms_opd_booking.patient_id = hms_patient.id','left'); 
			$this->db->join('hms_doctors','hms_doctors.id = hms_opd_booking.referral_doctor','left'); 
			$this->db->join('hms_users','hms_users.id = hms_payment.created_by');
			$this->db->join('hms_insurance_type',' hms_insurance_type.id = hms_opd_booking.insurance_type_id','left'); // insurance type
		    $this->db->join('hms_insurance_company',' hms_insurance_company.id = hms_opd_booking.ins_company_id','left'); // insurance type
			$this->db->where('hms_payment.id = "'.$id.'"'); 
			$this->db->where('hms_payment.section_id',2); 
			$this->db->group_by('hms_payment.parent_id, hms_payment.section_id');  
			$this->db->from('hms_patient');
			$result_patient['sales_list'] = $this->db->get()->result();
		        // echo "<pre>";print_r($result_patient);die;
			return $result_patient;

			}
			elseif($section_id==4)
			{
				//Billing

				$this->db->select("hms_patient.*,hms_users.*,hms_patient.patient_name as name,hms_opd_booking.reciept_code as recepit_no,hms_opd_booking.booking_date as date,hms_doctors.doctor_name,hms_payment_mode.payment_mode,hms_payment.pay_mode,hms_patient.patient_code as code,hms_payment.id as p_id,hms_simulation.simulation,hms_payment.debit, (select sum(credit)-sum(debit) from hms_payment as sub_pay where sub_pay.section_id = hms_payment.section_id AND sub_pay.parent_id = hms_payment.parent_id AND sub_pay.patient_id = hms_payment.patient_id AND sub_pay.created_date <= hms_payment.created_date) as balance, (select sum(credit)-sum(debit) from hms_payment as total_pay where total_pay.section_id = hms_payment.section_id AND total_pay.parent_id = hms_payment.parent_id AND  total_pay.patient_id = hms_payment.patient_id AND total_pay.created_date < hms_payment.created_date) as total_amount,hms_payment.created_date as c_date,hms_branch_hospital_no.reciept_prefix,hms_branch_hospital_no.reciept_suffix,(CASE WHEN hms_patient.insurance_type=1 THEN 'TPA' ELSE 'Normal' END ) as insurance_type_name, hms_insurance_type.insurance_type as insurance_name, hms_insurance_company.insurance_company"); 
			$this->db->join('hms_payment','hms_payment.patient_id = hms_patient.id');

$this->db->join('hms_branch_hospital_no','hms_branch_hospital_no.payment_id = hms_payment.id AND hms_branch_hospital_no.section_id=12','left');

			$this->db->join('hms_simulation','hms_simulation.id = hms_patient.simulation_id','left'); 
		    $this->db->join('hms_payment_mode','hms_payment_mode.id = hms_payment.pay_mode','left');
			
			$this->db->join('hms_opd_booking','hms_opd_booking.patient_id = hms_patient.id','left'); 
			$this->db->join('hms_doctors','hms_doctors.id = hms_opd_booking.referral_doctor','left'); 
			$this->db->join('hms_users','hms_users.id = hms_payment.created_by');
			$this->db->join('hms_insurance_type',' hms_insurance_type.id = hms_opd_booking.insurance_type_id','left'); // insurance type
		    $this->db->join('hms_insurance_company',' hms_insurance_company.id = hms_opd_booking.ins_company_id','left'); // insurance type
			$this->db->where('hms_payment.id = "'.$id.'"'); 
			$this->db->where('hms_payment.section_id',4); 
			$this->db->group_by('hms_patient.id','DESC');  
			$this->db->from('hms_patient');
			$result_patient['sales_list'] = $this->db->get()->result();
			//echo $this->db->last_query(); exit;
			return $result_patient;
			}

			elseif($section_id==5)
			{
				//IPD Booking

				$user_data = $this->session->userdata('auth_users');
				$this->db->select("hms_patient.*,hms_users.*,hms_patient.patient_name as name,hms_ipd_booking.ipd_no,hms_ipd_booking.admission_date as date,hms_doctors.doctor_name,hms_payment_mode.payment_mode,hms_payment.pay_mode,hms_patient.patient_code as code,hms_payment.id as p_id,hms_simulation.simulation,hms_payment.debit, (select sum(credit)-sum(debit) from hms_payment as sub_pay where sub_pay.section_id = hms_payment.section_id AND sub_pay.parent_id = hms_payment.parent_id AND  sub_pay.patient_id = hms_payment.patient_id AND sub_pay.created_date <= hms_payment.created_date) as balance, (select sum(credit)-sum(debit) from hms_payment as total_pay where total_pay.section_id = hms_payment.section_id AND total_pay.parent_id = hms_payment.parent_id AND  total_pay.patient_id = hms_payment.patient_id AND total_pay.created_date < hms_payment.created_date) as total_amount,hms_payment.created_date as c_date,hms_ipd_panel_type.panel_type,hms_doctors.doctor_name,hms_ipd_rooms.room_no,hms_ipd_room_to_bad.bad_no,hms_ipd_room_category.room_category,hms_branch_hospital_no.reciept_prefix,hms_branch_hospital_no.reciept_suffix,(CASE WHEN hms_patient.insurance_type=1 THEN 'TPA' ELSE 'Normal' END ) as insurance_type, hms_insurance_type.insurance_type as insurance_type_name, hms_insurance_company.insurance_company as insurance_company_name, hms_patient.polocy_no as insurance_policy_no,hms_patient.tpa_id, hms_patient.ins_amount as insurance_amount, hms_patient.ins_authorization_no as auth_no"); 
				$this->db->join('hms_payment','hms_payment.patient_id = hms_patient.id');

				$this->db->join('hms_branch_hospital_no','hms_branch_hospital_no.payment_id = hms_payment.id AND hms_branch_hospital_no.section_id=9','left');

				$this->db->join('hms_ipd_booking','hms_ipd_booking.patient_id = hms_patient.id AND hms_ipd_booking.id = hms_payment.parent_id','left'); 
				$this->db->join('hms_payment_mode','hms_payment_mode.id = hms_payment.pay_mode','left');
				
				//$this->db->join('hms_patient','hms_patient.id = hms_ipd_booking.patient_id','left');
				//$this->db->join('hms_ipd_packages','hms_ipd_packages.id = hms_ipd_booking.package_id','left');
				$this->db->join('hms_simulation','hms_simulation.id = hms_patient.simulation_id','left');
				$this->db->join('hms_ipd_panel_company','hms_ipd_panel_company.id = hms_ipd_booking.panel_name','left');
				$this->db->join('hms_ipd_panel_type','hms_ipd_panel_type.id = hms_ipd_booking.panel_type','left');
				$this->db->join('hms_ipd_room_to_bad','hms_ipd_room_to_bad.id = hms_ipd_booking.bad_id','left');
				
				$this->db->join('hms_doctors','hms_doctors.id = hms_ipd_booking.attend_doctor_id','left');
				$this->db->join('hms_ipd_rooms','hms_ipd_rooms.id = hms_ipd_booking.room_id','left');
				$this->db->join('hms_ipd_room_category','hms_ipd_room_category.id = hms_ipd_booking.room_type_id','left');
				
				$this->db->join('hms_users','hms_users.id = hms_payment.created_by');
				$this->db->join('hms_insurance_type',' hms_insurance_type.id = hms_ipd_booking.panel_type','left'); // insurance type name
        		$this->db->join('hms_insurance_company','hms_insurance_company.id = hms_ipd_booking.panel_name','left'); // insurance company
				$this->db->where('hms_payment.id = "'.$id.'"'); 
				$this->db->where('hms_payment.section_id',5); 
				//$this->db->where('hms_ipd_booking.discharge_status =1'); 
				$this->db->group_by('hms_patient.id','DESC');  
				$this->db->from('hms_patient');

				
				$result_patient['sales_list']= $this->db->get()->result();
				//echo $this->db->last_query(); exit;
		
			return $result_patient;

			}
			else if($section_id==7)
			{
				//echo 'asdsa';die;
				$this->db->select("hms_patient.*,hms_users.*,hms_patient.patient_name as name,hms_vaccination_sale.sale_no as recepit_no,hms_vaccination_sale.sale_date as date,hms_vaccination_sale.remarks as remk,hms_doctors.doctor_name,hms_payment_mode.payment_mode,hms_payment.pay_mode,hms_patient.patient_code as code,hms_payment.id as p_id,hms_simulation.simulation,hms_payment.debit, (select sum(credit)-sum(debit) from hms_payment as sub_pay where sub_pay.section_id = hms_payment.section_id AND sub_pay.parent_id = hms_payment.parent_id AND sub_pay.patient_id = hms_payment.patient_id AND sub_pay.created_date <= hms_payment.created_date) as balance, (select sum(credit)-sum(debit) from hms_payment as total_pay where total_pay.section_id = hms_payment.section_id AND total_pay.parent_id = hms_payment.parent_id AND  total_pay.patient_id = hms_payment.patient_id AND total_pay.created_date < hms_payment.created_date) as total_amount,hms_payment.created_date as c_date,hms_branch_hospital_no.reciept_prefix,hms_branch_hospital_no.reciept_suffix"); 
				$this->db->join('hms_payment','hms_payment.patient_id = hms_patient.id');

				$this->db->join('hms_branch_hospital_no','hms_branch_hospital_no.payment_id = hms_payment.id AND hms_branch_hospital_no.section_id=13','left');

				$this->db->join('hms_simulation','hms_simulation.id = hms_patient.simulation_id','left'); 
				$this->db->join('hms_payment_mode','hms_payment_mode.id = hms_payment.pay_mode','left');

				$this->db->join('hms_vaccination_sale','hms_vaccination_sale.patient_id = hms_patient.id','left'); 
				$this->db->join('hms_doctors','hms_doctors.id = hms_vaccination_sale.refered_id','left');
				$this->db->join('hms_users','hms_users.id = hms_payment.created_by');	 
				$this->db->where('hms_payment.section_id',7); 
				$this->db->where('hms_payment.id = "'.$id.'"'); 
				$this->db->group_by('hms_patient.id','DESC');  
				$this->db->from('hms_patient');
				$result_patient['sales_list'] = $this->db->get()->result();
				//echo $this->db->last_query(); exit;
				return $result_patient;
			}
			
			else if($section_id==8)
			{

				//echo 'asdsa';die;
				$this->db->select("hms_patient.*,hms_users.*,hms_patient.patient_name as name,hms_operation_booking.booking_code as recepit_no,hms_operation_booking.operation_date as date,hms_operation_booking.remarks as remk,hms_payment_mode.payment_mode,hms_payment.pay_mode,hms_patient.patient_code as code,hms_payment.id as p_id,hms_simulation.simulation,hms_payment.debit, (select sum(credit)-sum(debit) from hms_payment as sub_pay where sub_pay.section_id = hms_payment.section_id AND sub_pay.parent_id = hms_payment.parent_id AND sub_pay.patient_id = hms_payment.patient_id AND sub_pay.created_date <= hms_payment.created_date) as balance, (select sum(credit)-sum(debit) from hms_payment as total_pay where total_pay.section_id = hms_payment.section_id AND total_pay.parent_id = hms_payment.parent_id AND  total_pay.patient_id = hms_payment.patient_id AND total_pay.created_date < hms_payment.created_date) as total_amount,hms_payment.created_date as c_date,hms_branch_hospital_no.reciept_prefix,hms_branch_hospital_no.reciept_suffix"); 
				$this->db->join('hms_payment','hms_payment.patient_id = hms_patient.id');

				$this->db->join('hms_branch_hospital_no','hms_branch_hospital_no.payment_id = hms_payment.id AND hms_branch_hospital_no.section_id=13','left');

				$this->db->join('hms_simulation','hms_simulation.id = hms_patient.simulation_id','left'); 
				$this->db->join('hms_payment_mode','hms_payment_mode.id = hms_payment.pay_mode','left');

				$this->db->join('hms_operation_booking','hms_operation_booking.patient_id = hms_patient.id','left'); 
				//$this->db->join('hms_doctors','hms_doctors.id = hms_vaccination_sale.refered_id','left');
				$this->db->join('hms_users','hms_users.id = hms_payment.created_by');	 
				$this->db->where('hms_payment.section_id',8); 
				$this->db->where('hms_payment.id = "'.$id.'"'); 
				$this->db->group_by('hms_patient.id','DESC');  
				$this->db->from('hms_patient');
				$result_patient['sales_list'] = $this->db->get()->result();
				//echo $this->db->last_query(); exit;
				return $result_patient;
			}
			
			/* balance clearance code for blood bank */
			else if($section_id==10)
			{

				//echo 'asdsa';die;
				$this->db->select("hms_patient.*,hms_users.*,hms_patient.patient_name as name,hms_blood_patient_to_recipient.issue_code as recepit_no,hms_blood_patient_to_recipient.requirement_date as date,hms_payment_mode.payment_mode,hms_payment.pay_mode,hms_patient.patient_code as code,hms_payment.id as p_id,hms_simulation.simulation,hms_payment.debit, (select sum(credit)-sum(debit) from hms_payment as sub_pay where sub_pay.section_id = hms_payment.section_id AND sub_pay.parent_id = hms_payment.parent_id AND sub_pay.patient_id = hms_payment.patient_id AND sub_pay.created_date <= hms_payment.created_date) as balance, (select sum(credit)-sum(debit) from hms_payment as total_pay where total_pay.section_id = hms_payment.section_id AND total_pay.parent_id = hms_payment.parent_id AND  total_pay.patient_id = hms_payment.patient_id AND total_pay.created_date < hms_payment.created_date) as total_amount,hms_payment.created_date as c_date,hms_branch_hospital_no.reciept_prefix,hms_branch_hospital_no.reciept_suffix,hms_blood_group.blood_group ,(CASE WHEN hms_blood_patient_to_recipient.referred_by =1 THEN concat(hms_hospital.hospital_name,' (Hospital)')  ELSE concat('Dr. ',hms_doctors.doctor_name)END) as doctor_hospital_name"); 
				$this->db->join('hms_payment','hms_payment.patient_id = hms_patient.id');
				 $this->db->join('hms_blood_patient_to_recipient','hms_blood_patient_to_recipient.patient_id = hms_patient.id','left'); 
				 
				 $this->db->join('hms_blood_group','hms_blood_group.id=hms_blood_patient_to_recipient.blood_group_id', 'Left');
				$this->db->join('hms_branch_hospital_no','hms_branch_hospital_no.payment_id = hms_payment.id AND hms_branch_hospital_no.section_id=13','left');

				$this->db->join('hms_simulation','hms_simulation.id = hms_patient.simulation_id','left'); 

				
				$this->db->join('hms_payment_mode','hms_payment_mode.id = hms_payment.pay_mode','left');
				$this->db->join('hms_doctors','hms_doctors.id = hms_blood_patient_to_recipient.doctor_id','left');
				$this->db->join('hms_hospital','hms_hospital.id = hms_blood_patient_to_recipient.hospital_id','left');
			 
				//$this->db->join('hms_doctors','hms_doctors.id = hms_vaccination_sale.refered_id','left');
				$this->db->join('hms_users','hms_users.id = hms_payment.created_by');	 
				$this->db->where('hms_payment.section_id',10); 
				$this->db->where('hms_payment.id = "'.$id.'"'); 
				$this->db->group_by('hms_patient.id','DESC');  
				$this->db->from('hms_patient');
				$result_patient['sales_list'] = $this->db->get()->result();
				//echo $this->db->last_query(); exit;
				return $result_patient;
			}
			/* balance clearance code for blood bank */
//print_r($section_id);die;
			elseif($section_id==13)
			{
				//OPD Booking

				$this->db->select("hms_patient.*,hms_users.*,hms_patient.patient_name as name,hms_ambulance_booking.booking_no as recepit_no,hms_ambulance_booking.booking_date as date,hms_doctors.doctor_name,hms_payment_mode.payment_mode,hms_payment.pay_mode,hms_patient.patient_code as code,hms_payment.id as p_id,hms_simulation.simulation,hms_payment.debit, (select sum(credit)-sum(debit) from hms_payment as sub_pay where sub_pay.section_id = hms_payment.section_id AND sub_pay.parent_id = hms_payment.parent_id AND  sub_pay.patient_id = hms_payment.patient_id AND sub_pay.created_date <= hms_payment.created_date) as balance, (select sum(credit)-sum(debit) from hms_payment as total_pay where total_pay.section_id = hms_payment.section_id AND total_pay.parent_id = hms_payment.parent_id AND total_pay.patient_id = hms_payment.patient_id AND total_pay.created_date < hms_payment.created_date) as total_amount,hms_payment.created_date as c_date,hms_branch_hospital_no.reciept_prefix,hms_branch_hospital_no.reciept_suffix,(CASE WHEN hms_patient.insurance_type=1 THEN 'TPA' ELSE 'Normal' END ) as insurance_type_name"); 
				$this->db->join('hms_payment','hms_payment.patient_id = hms_patient.id');
$this->db->join('hms_branch_hospital_no','hms_branch_hospital_no.payment_id = hms_payment.id AND hms_branch_hospital_no.section_id=8','left');

			$this->db->join('hms_simulation','hms_simulation.id = hms_patient.simulation_id','left'); 
			$this->db->join('hms_payment_mode','hms_payment_mode.id = hms_payment.pay_mode');
				
			$this->db->join('hms_ambulance_booking','hms_ambulance_booking.patient_id = hms_patient.id','left'); 
			$this->db->join('hms_doctors','hms_doctors.id = hms_ambulance_booking.reffered','left'); 
			$this->db->join('hms_users','hms_users.id = hms_payment.created_by');
		//	$this->db->join('hms_insurance_type',' hms_insurance_type.id = hms_ambulance_booking.insurance_type_id','left'); // insurance type
		   // $this->db->join('hms_insurance_company',' hms_insurance_company.id = hms_ambulance_booking.ins_company_id','left'); // insurance type
			$this->db->where('hms_payment.id = "'.$id.'"'); 
			$this->db->where('hms_payment.section_id',13); 
			$this->db->group_by('hms_payment.parent_id, hms_payment.section_id');  
			$this->db->from('hms_patient');
			$result_patient['sales_list'] = $this->db->get()->result();
			// echo $this->db->last_query();die;
		 //        echo "<pre>";print_r($result_patient);die;
			return $result_patient;

			}

		} 
	
	}

	public function get_balance_previous($id="",$patient_id="",$payment_date="",$section_id=""){
          $date_new= date('Y-m-d',strtotime($payment_date));
		  $new_payment_date=$date_new.' '.date('H:i:s');
		if($section_id==3)
			{
			$this->db->select("sum(hms_payment.credit-hms_payment.debit) as balance"); 
			$this->db->where('hms_payment.created_date < (select created_date from hms_payment as sub_pay where sub_pay.id="'.$id.'")');
			$this->db->where('hms_payment.patient_id',$patient_id);
			$this->db->where('hms_payment.section_id',3);
			$this->db->from('hms_payment');
			$result_patient['balance'] = $this->db->get()->result();
			//echo $this->db->last_query();die;
			return $result_patient;
			
			}
			elseif($section_id==2)
			{
			$this->db->select("sum(hms_payment.credit-hms_payment.debit) as balance"); 
			$this->db->where('hms_payment.created_date < (select created_date from hms_payment as sub_pay where sub_pay.id="'.$id.'")');
			$this->db->where('hms_payment.patient_id',$patient_id);
			$this->db->where('hms_payment.section_id',2);
			$this->db->from('hms_payment');
			$result_patient['balance'] = $this->db->get()->result();

			return $result_patient;
			}
			elseif($section_id==4)
			{
			$this->db->select("sum(hms_payment.credit-hms_payment.debit) as balance"); 
			$this->db->where('hms_payment.created_date < (select created_date from hms_payment as sub_pay where sub_pay.id="'.$id.'")');
			$this->db->where('hms_payment.patient_id',$patient_id);
			$this->db->where('hms_payment.section_id',4);
			$this->db->from('hms_payment');
			$result_patient['balance'] = $this->db->get()->result();

			return $result_patient;
			}
			elseif($section_id==5)
			{
			$this->db->select("sum(hms_payment.credit-hms_payment.debit) as balance"); 
			$this->db->where('hms_payment.created_date < (select created_date from hms_payment as sub_pay where sub_pay.id="'.$id.'")');
			$this->db->where('hms_payment.patient_id',$patient_id);
			$this->db->where('hms_payment.section_id',5);
			$this->db->from('hms_payment');
			$result_patient['balance'] = $this->db->get()->result();
//echo $this->db->last_query();
			return $result_patient;
			}
			else if($section_id==7)
			{
			$this->db->select("sum(hms_payment.credit-hms_payment.debit) as balance"); 
			$this->db->where('hms_payment.created_date < (select created_date from hms_payment as sub_pay where sub_pay.id="'.$id.'")');
			$this->db->where('hms_payment.patient_id',$patient_id);
			$this->db->where('hms_payment.section_id',7);
			$this->db->from('hms_payment');
			$result_patient['balance'] = $this->db->get()->result();
			//echo $this->db->last_query();die;
			return $result_patient;
			
			}
			else if($section_id==8)
			{
			$this->db->select("sum(hms_payment.credit-hms_payment.debit) as balance"); 
			$this->db->where('hms_payment.created_date < (select created_date from hms_payment as sub_pay where sub_pay.id="'.$id.'")');
			$this->db->where('hms_payment.patient_id',$patient_id);
			$this->db->where('hms_payment.section_id',8);
			$this->db->from('hms_payment');
			$result_patient['balance'] = $this->db->get()->result();
			//echo $this->db->last_query();die;
			return $result_patient;
			
			}
			else if($section_id==10)
			{
			$this->db->select("sum(hms_payment.credit-hms_payment.debit) as balance"); 
			$this->db->where('hms_payment.created_date < (select created_date from hms_payment as sub_pay where sub_pay.id="'.$id.'")');
			$this->db->where('hms_payment.patient_id',$patient_id);
			$this->db->where('hms_payment.section_id',10);
			$this->db->from('hms_payment');
			$result_patient['balance'] = $this->db->get()->result();
			//echo $this->db->last_query();die;
			return $result_patient;
			
			}
			elseif($section_id==13)
			{
			$this->db->select("sum(hms_payment.credit-hms_payment.debit) as balance"); 
			$this->db->where('hms_payment.created_date < (select created_date from hms_payment as sub_pay where sub_pay.id="'.$id.'")');
			$this->db->where('hms_payment.patient_id',$patient_id);
			$this->db->where('hms_payment.section_id',13);
			$this->db->from('hms_payment');
			$result_patient['balance'] = $this->db->get()->result();

			return $result_patient;
			}
		 
		 
			
	}
	//12 july 2017
	public function patient_balance_receipt_data_old($id="",$type="")
	{
       // echo $this->session->userdata('balance');die;
		if(!empty($id))
		{ 
			 $result_booking=array();
			 $user_data = $this->session->userdata('auth_users');

			if($type==1){

				$this->db->select("hms_patient.*,hms_patient.patient_name as name,hms_medicine_sale.sale_no as recepit_no,hms_medicine_sale.sale_date as date,hms_doctors.doctor_name,hms_payment.pay_mode,hms_patient.patient_code as code,hms_payment.id as p_id,hms_simulation.simulation,hms_payment.debit, (select sum(credit)-sum(debit) from hms_payment as sub_pay where sub_pay.patient_id = hms_payment.patient_id AND sub_pay.created_date <= hms_payment.created_date) as balance, (select sum(credit)-sum(debit) from hms_payment as total_pay where total_pay.patient_id = hms_payment.patient_id AND total_pay.created_date < hms_payment.created_date) as total_amount"); 
			  $this->db->join('hms_payment','hms_payment.patient_id = hms_patient.id');
			  $this->db->join('hms_simulation','hms_simulation.id = hms_patient.simulation_id','left'); 
			   
			    $this->db->join('hms_medicine_sale','hms_medicine_sale.patient_id = hms_patient.id','left'); 
			     $this->db->join('hms_doctors','hms_doctors.id = hms_medicine_sale.refered_id','left'); 
			    
			$this->db->where('hms_payment.doctor_id','0'); 
			$this->db->where('hms_payment.parent_id','0'); 
			$this->db->where('hms_payment.id = "'.$id.'"'); 
			$this->db->group_by('hms_patient.id','DESC');  
			$this->db->from('hms_patient');
			$result_patient['sales_list'] = $this->db->get()->result();
			}
			if($type==2){
				$this->db->select("hms_medicine_vendors.*,hms_medicine_vendors.name as name,hms_medicine_vendors.vendor_id as code,hms_payment.pay_mode,hms_medicine_purchase.purchase_date as date,hms_medicine_purchase.invoice_id as recepit_no,hms_medicine_vendors.mobile as mobile_no,hms_payment.debit, (select sum(credit)-sum(debit) from hms_payment as sub_pay where sub_pay.patient_id = hms_payment.patient_id AND sub_pay.created_date <= hms_payment.created_date) as balance, (select sum(credit)-sum(debit) from hms_payment as total_pay where total_pay.patient_id = hms_payment.patient_id AND total_pay.created_date < hms_payment.created_date) as total_amount"); 
			        $this->db->join('hms_payment','hms_payment.vendor_id = hms_medicine_vendors.id','left');
                $this->db->join('hms_medicine_purchase','hms_medicine_purchase.vendor_id = hms_medicine_vendors.id','left'); 
				$this->db->where('hms_payment.doctor_id','0'); 
				$this->db->where('hms_payment.parent_id','0'); 
				$this->db->where('hms_payment.id = "'.$id.'"'); 
				$this->db->group_by('hms_medicine_vendors.id','DESC');  
				$this->db->from('hms_medicine_vendors');
					$result_patient['sales_list'] = $this->db->get()->result();
			
			}
          return $result_patient;
			
		} 
	}
	function template_format($data="")
	{
    	$users_data = $this->session->userdata('auth_users'); 
    	$this->db->select('hms_print_branch_template.*');
    	$this->db->where($data);
    	$this->db->where('branch_id  IN ('.$users_data['parent_id'].')'); 
    	//$this->db->where('printer_paper_type!=0');
    	$this->db->from('hms_print_branch_template');
    	$query=$this->db->get()->row();
    	//echo $this->db->last_query();
    	//print_r($query);exit;
    	return $query;

    }

      function path_template_format($data="")
	{
    	$users_data = $this->session->userdata('auth_users'); 
    	$this->db->select('path_print_branch_template.*');
    	$this->db->where($data);
    	$this->db->where('branch_id',$users_data['parent_id']); 
    	$this->db->where('printer_paper_type!=0');
    	$this->db->from('path_print_branch_template');
    	$query=$this->db->get()->row();
    	//echo $this->db->last_query();die;
    	//print_r($query);exit;
    	return $query;

    }
	
} 
?>