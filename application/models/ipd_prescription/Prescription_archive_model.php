<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Prescription_archive_model extends CI_Model {

	var $table = 'hms_ipd_patient_prescription';
	var $column = array('hms_ipd_patient_prescription.id','hms_ipd_patient_prescription.ipd_no','hms_ipd_patient_prescription.patient_name', 'hms_ipd_patient_prescription.mobile_no','hms_ipd_patient_prescription.patient_code', 'hms_ipd_patient_prescription.status', 'hms_ipd_patient_prescription.created_date', 'hms_ipd_patient_prescription.modified_date');  
	var $order = array('id' => 'desc'); 
	public function __construct()
	{
		parent::__construct();
		$this->load->database();
	}

	private function _get_datatables_query()
	{
		$user_data = $this->session->userdata('auth_users');
		
		$this->db->select("hms_ipd_patient_prescription.*,hms_ipd_prescription_patient_test.test_name,hms_ipd_prescription_patient_pres.medicine_name,hms_ipd_prescription_patient_pres.medicine_type,hms_ipd_prescription_patient_pres.medicine_dose,hms_ipd_prescription_patient_pres.medicine_duration,hms_ipd_prescription_patient_pres.medicine_frequency,hms_ipd_prescription_patient_pres.medicine_advice"); 
		$this->db->join('hms_ipd_prescription_patient_test','hms_ipd_prescription_patient_test.prescription_id=hms_ipd_patient_prescription.id','left');
        $this->db->join('hms_ipd_prescription_patient_pres','hms_ipd_prescription_patient_pres.prescription_id=hms_ipd_patient_prescription.id','left');
		$this->db->where('hms_ipd_patient_prescription.is_deleted','1'); 
		$this->db->where('hms_ipd_patient_prescription.branch_id = "'.$user_data['parent_id'].'"'); 
		$this->db->from($this->table); 
		$i = 0;
	
		foreach ($this->column as $item) // loop column 
		{
			if($_POST['search']['value']) // if datatable send POST for search
			{
				
				if($i===0) // first loop
				{
					$this->db->group_start(); // open bracket. query Where with OR clause better with bracket. because maybe can combine with other WHERE with AND. 
					$this->db->like($item, $_POST['search']['value']);
				}
				else
				{
					$this->db->or_like($item, $_POST['search']['value']);
				}

				if(count($this->column) - 1 == $i) //last loop+
					$this->db->group_end(); //close bracket
			}
			$column[$i] = $item; // set column array variable to order processing
			$i++;
		}
		$this->db->group_by('hms_ipd_patient_prescription.id');
		if(isset($_POST['order'])) // here order processing
		{
			$this->db->order_by($column[$_POST['order']['0']['column']], $_POST['order']['0']['dir']);
		} 
		else if(isset($this->order))
		{
			$order = $this->order;
			$this->db->order_by(key($order), $order[key($order)]);
		}
	}

	function get_datatables()
	{
		$this->_get_datatables_query();
		if($_POST['length'] != -1)
		$this->db->limit($_POST['length'], $_POST['start']);
		$query = $this->db->get(); 
		//echo $this->db->last_query();die;
		return $query->result();
	}

	function count_filtered()
	{
		$this->_get_datatables_query();
		$query = $this->db->get();
		return $query->num_rows();
	}

	public function count_all()
	{
		$this->db->from($this->table);
		return $this->db->count_all_results();
	}

	public function restore($id="")
    {
    	if(!empty($id) && $id>0)
    	{
			$user_data = $this->session->userdata('auth_users');
			$this->db->set('is_deleted',0);
			$this->db->set('deleted_by',$user_data['id']);
			$this->db->set('deleted_date',date('Y-m-d H:i:s'));
			$this->db->where('id',$id);
			$this->db->update('hms_ipd_patient_prescription');
    	} 
    }

    public function restoreall($ids=array())
    {
    	if(!empty($ids))
    	{
    		$id_list = [];
    		foreach($ids as $id)
    		{
    			if(!empty($id) && $id>0)
    			{
                  $id_list[]  = $id;
    			} 
    		}
    		$branch_ids = implode(',', $id_list);
			$user_data = $this->session->userdata('auth_users');
			$this->db->set('is_deleted',0);
			$this->db->set('deleted_by',$user_data['id']);
			$this->db->set('deleted_date',date('Y-m-d H:i:s'));
			$this->db->where('id IN ('.$branch_ids.')');
			$this->db->update('hms_ipd_patient_prescription');

    	} 
    }

    public function trash($id="")
    {
    	if(!empty($id) && $id>0)
    	{  
			$user_data = $this->session->userdata('auth_users');
			$this->db->set('is_deleted',2);
			$this->db->set('deleted_by',$user_data['id']);
			$this->db->set('deleted_date',date('Y-m-d H:i:s'));
			$this->db->where('id',$id);
			$this->db->update('hms_ipd_patient_prescription');  
    	} 
    }

    public function trashall($ids=array())
    {
    	if(!empty($ids))
    	{
    		$id_list = [];
    		foreach($ids as $id)
    		{
    			if(!empty($id) && $id>0)
    			{
                  $id_list[]  = $id;
                  
    			} 
    		}
    		$branch_ids = implode(',', $id_list); 
			//$this->db->where('id IN ('.$branch_ids.')');
			//$this->db->delete('hms_patient');

			$user_data = $this->session->userdata('auth_users');
			$this->db->set('is_deleted',2);
			$this->db->set('deleted_by',$user_data['id']);
			$this->db->set('deleted_date',date('Y-m-d H:i:s'));
			$this->db->where('id IN ('.$branch_ids.')');
			$this->db->update('hms_ipd_patient_prescription');
    	} 
    }
 

}
?>