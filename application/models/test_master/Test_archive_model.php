<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Test_archive_model extends CI_Model {

	var $table = 'path_test';
	var $column = array('path_test.id','path_test.test_code','path_test.test_name', 'path_test.test_type_id', 'hms_department.department', 'path_test_method.test_method', 'path_unit.unit','path_test.base_rate','path_test.created_date');  
		var $order = array('sort_order'=>'asc','id' => 'desc');

	public function __construct()
	{
		parent::__construct();
		$this->load->database();
	}

	private function _get_datatables_query($branch_id='')
	{
		$users_data = $this->session->userdata('auth_users');
		$this->db->select("path_test.*, path_unit.unit, hms_department.department, path_test_method.test_method"); 
		$this->db->from($this->table);  
        $this->db->where('path_test.branch_id = "'.$users_data['parent_id'].'"');
        $this->db->where('path_test.is_deleted','1');
        $this->db->join('path_unit','path_unit.id = path_test.unit_id','left');
        $this->db->join('path_test_method','path_test_method.id = path_test.method_id','left');
        $this->db->join('hms_department','hms_department.id = path_test.dept_id','left');
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

	function get_datatables($branch_id='')
	{
		$this->_get_datatables_query($branch_id);
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
			$this->db->update('path_test');
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
    		$emp_ids = implode(',', $id_list);
			$user_data = $this->session->userdata('auth_users');
			$this->db->set('is_deleted',0);
			$this->db->set('deleted_by',$user_data['id']);
			$this->db->set('deleted_date',date('Y-m-d H:i:s'));
			$this->db->where('id IN ('.$emp_ids.')');
			$this->db->update('path_test');
    	} 
    }

    public function trash($id="")
    {
    	$user_data = $this->session->userdata('auth_users');
    	// if(!empty($id) && $id>0)
    	// {  
			// $this->db->where('id',$id);
			// $this->db->delete('path_test');
		
            // Test Under
			// $this->db->where('parent_id',$id);
			// $this->db->delete('path_test_under');
			// End Test Under

			// Test Formula
			// $this->db->where('test_id',$id);
			// $this->db->delete('path_test_formula');
		    // End Test Formula   

			// Test Range
			// $this->db->where('test_id',$id);
			// $this->db->delete('path_test_range');
            // End Test Range      
    	// } 
    	if(!empty($id) && $id>0)
    	{  
			
			//path_test
		    $this->db->set('is_deleted',2);
			$this->db->set('deleted_by',$user_data['id']);
			$this->db->set('deleted_date',date('Y-m-d H:i:s'));
			$this->db->where('id',$id);
			$this->db->update('path_test');
			//end path_test

			// Test Under
			$this->db->set('is_deleted',2);
			$this->db->set('deleted_by',$user_data['id']);
			$this->db->set('deleted_date',date('Y-m-d H:i:s'));
			$this->db->where('parent_id',$id);
			$this->db->update('path_test_under');
			// End Test Under

			// Test Formula
		    $this->db->set('is_deleted',2);
			$this->db->set('deleted_by',$user_data['id']);
			$this->db->set('deleted_date',date('Y-m-d H:i:s'));
			$this->db->where('test_id',$id);
			$this->db->update('path_test_formula');
			// End Test Formula   

			// Test Range
			$this->db->set('is_deleted',2);
			$this->db->set('deleted_by',$user_data['id']);
			$this->db->set('deleted_date',date('Y-m-d H:i:s'));
			$this->db->where('test_id',$id);
			$this->db->update('path_test_range');
			// End Test Range      
    	} 
    }

    public function trashall($ids=array())
    {
    	$user_data = $this->session->userdata('auth_users');
   //  	if(!empty($ids))
   //  	{
   //  		$id_list = [];
   //  		foreach($ids as $id)
   //  		{
   //  			if(!empty($id) && $id>0)
   //  			{
   //                $id_list[]  = $id;
   //  			} 
   //  		}
   //  		$branch_ids = implode(',', $id_list); 
			// $this->db->where('id IN ('.$branch_ids.')');
			// $this->db->delete('path_test'); 

			// // Test Under
			// $this->db->where('parent_id IN ('.$branch_ids.')');
			// $this->db->delete('path_test_under');
			// // End Test Under

			// // Test Formula
			// $this->db->where('test_id IN ('.$branch_ids.')');
			// $this->db->delete('path_test_formula');
			// // End Test Formula   

			// // Test Range
			// $this->db->where('test_id IN ('.$branch_ids.')');
			// $this->db->delete('path_test_range');
			// // End Test Range 
   //  	} 
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
			
			$this->db->set('is_deleted',2);
			$this->db->set('deleted_by',$user_data['id']);
			$this->db->set('deleted_date',date('Y-m-d H:i:s'));
			$this->db->where('id IN ('.$branch_ids.')');
			$this->db->update('path_test');

			// Test Under
		
			$this->db->set('is_deleted',2);
			$this->db->set('deleted_by',$user_data['id']);
			$this->db->set('deleted_date',date('Y-m-d H:i:s'));
			$this->db->where('parent_id IN ('.$branch_ids.')');
			$this->db->update('path_test_under');
			// End Test Under

			// Test Formula
		
			$this->db->set('is_deleted',2);
			$this->db->set('deleted_by',$user_data['id']);
			$this->db->set('deleted_date',date('Y-m-d H:i:s'));
			$this->db->where('test_id IN ('.$branch_ids.')');
			$this->db->update('path_test_formula');
			// End Test Formula   

			// Test Range
			
			$this->db->set('is_deleted',2);
			$this->db->set('deleted_by',$user_data['id']);
			$this->db->set('deleted_date',date('Y-m-d H:i:s'));
			$this->db->where('test_id IN ('.$branch_ids.')');
			$this->db->update('path_test_range');
			// End Test Range 
    	} 
    }
 

}
?>