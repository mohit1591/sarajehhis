<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Path_panel_price_model extends CI_Model {

	var $table = 'path_test';
	var $column = array('path_panel_price.id','path_panel_price.relation', 'path_panel_price.status','path_panel_price.created_date');  
	var $order = array('id' => 'desc');

	public function __construct()
	{
		parent::__construct();
		$this->load->database();
	}
    private function _get_datatables_query($branch_id='',$not_in_test='')
    {
        //print_r($_POST);die;
        $cases='';
        $panel_test_search = $this->session->userdata('panel_test_search');
        $users_data = $this->session->userdata('auth_users');
        $company_data =  $this->session->userdata('company_data');
        
        if(isset($_POST['dept_id']) && !empty($_POST['dept_id']))
        {
          $test_master_search = array('dept_id'=>$_POST['dept_id']);
          $this->session->set_userdata('test_master_search',$test_master_search);  
        }
        
        if(isset($_POST['test_head']) && !empty($_POST['test_head']))
        {
          $test_master_search = array('dept_id'=>$_POST['dept_id'],'test_head'=>$_POST['test_head']);
          $this->session->set_userdata('test_master_search',$test_master_search);  
        }
        
       if(!empty($_POST['paneln_ids']))
        {

            $cases= '(CASE WHEN path_panel_price.charge>0 THEN path_panel_price.charge ELSE path_test.rate END) as path_price';
        }
        else
        {
           $cases= 'path_test.rate as path_price';
        }
        $parent_branch_details = $this->session->userdata('parent_branches_data');
        $sub_branch_details = $this->session->userdata('sub_branches_data');
        $this->db->select("path_test.*,".$cases.",hms_department.department, path_test_heads.test_heads"); 
        if(!empty($_POST['paneln_ids']))
        {
            $this->db->join('path_panel_price','path_panel_price.test_id=path_test.id AND path_panel_price.panel_id = "'.$_POST['paneln_ids'].'"','left');
        }
        else
        {
           $this->db->join('path_panel_price','path_panel_price.test_id=path_test.id','left'); 
        }

        if(!empty(trim($panel_test_search)))
        {
            $this->db->where('path_test.test_name LIKE "'.$panel_test_search.'%"');
        }
        
        $this->db->from($this->table);  
         $this->db->group_by('path_test.id');  
        if(!empty($not_in_test))
        {
            //$this->db->where('path_test.id NOT IN ('.$not_in_test.')');
        }
        
        $this->db->where('path_test.is_deleted',0);
        if($users_data['users_role']=='2')
        {
            if(!empty($branch_id))
            {
                if($branch_id=='inherit')
                {
                  
                    if(!empty($parent_branch_details)){
                        $id_list = [];
                        foreach($parent_branch_details as $id){
                            if(!empty($id) && $id>0){
                                $id_list[]  = $id['parent_id'];
                            } 
                        }
                        $branch_ids = implode(',', $id_list);
                        //print_r($id_list);die;
                        $this->db->where('path_test.branch_id IN('.$branch_ids.')');
                    }
                   $this->db->where('path_test.id NOT IN (select download_id from path_test where branch_id IN ("'.$users_data['parent_id'].'"))');
                }
                else if($branch_id==$users_data['parent_id'])
                {
                    $this->db->where('path_test.branch_id',$users_data['parent_id']);
                    $this->db->where('path_test.id NOT IN (select download_id from path_test where branch_id IN ("'.$users_data['parent_id'].'"))');
                
                }
                else{
                        $this->db->where('path_test.branch_id',$branch_id);
                        $this->db->where('path_test.id NOT IN (select download_id from path_test where branch_id IN ("'.$users_data['parent_id'].'"))');
                }
            }
            else
            {
                $this->db->where('path_test.branch_id',$users_data['parent_id']);
            }
        }
        else if($users_data['users_role']=='3')
        {
            $this->db->where('path_test.branch_id',$company_data['id']);
        }
        else if($users_data['users_role']=='1')
        {
           $this->db->where('path_test.branch_id',$users_data['parent_id']);
        }
        
        
        $this->db->join('path_test_heads','path_test_heads.id = path_test.test_head_id','left');
        $this->db->join('hms_department','hms_department.id = path_test.dept_id','left');
        $test_heads = $this->session->userdata('master_test_head');


        // if(!empty($test_heads))
        // {
        //     $this->db->where('path_test.test_head_id',$test_heads);
        // }
        
        if(!empty($_POST['dept_id']))
        {
            $this->db->where('path_test.dept_id',$_POST['dept_id']);
        }

         
        
        if(!empty($_POST['test_head']))
        {
            $this->db->where('path_test.test_head_id',$_POST['test_head']);
        }
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
    
    function count_filtered($branch_id='',$not_in_test='')
    {
        $this->_get_datatables_query($branch_id,$not_in_test);
        $query = $this->db->get();
        return $query->num_rows();
    }
    function get_datatables($branch_id='',$not_in_test='')
    {
        $this->_get_datatables_query($branch_id,$not_in_test);
        if($_POST['length'] != -1)
        $this->db->limit($_POST['length'], $_POST['start']);
        $query = $this->db->get(); 
        //echo $this->db->last_query();die;
        return $query->result();
    }
    public function count_all()
    {
        $this->db->from($this->table);
        return $this->db->count_all_results();
    }	
    public function get_transactional_doctors_list(){
    	$users_data = $this->session->userdata('auth_users');
    	$this->db->select('hms_doctors.doctor_name,hms_doctors.id');
    	$this->db->where('doctor_pay_type','2');
    	$this->db->where('branch_id',$users_data['parent_id']);
    	$query = $this->db->get('hms_doctors');
    	$result = $query->result();
    	return $result;
    }
    public function get_test_departments($module_id='5')
    {
    	/*$users_data = $this->session->userdata('auth_users');
        $this->db->select("hms_department.id,hms_department.branch_id,hms_department.module,hms_department.department,hms_department.ip_address,hms_department.is_deleted,hms_department.deleted_by,hms_department.deleted_date,hms_department.created_by,hms_department.modified_by,hms_department.modified_date,hms_department.created_date, (CASE WHEN hms_department.branch_id=0 THEN 1 ELSE hms_department_to_department_status.status END) status");
        //(CASE WHEN hms_department.branch_id=0 THEN hms_department.status ELSE hms_department_to_department_status.status END) as status 
        $this->db->from('hms_department'); 
         $this->db->join('hms_department_to_department_status','hms_department_to_department_status.department_id = hms_department.id','left');

        if(!empty($module_id))
        {
           
            $this->db->where('hms_department.module',$module_id); 
        }
        if(!empty($branch_id))
        {
            $this->db->where('(hms_department.branch_id='.$branch_id.' OR hms_department.branch_id=0)'); 
        }
        else
        {
            $this->db->where('(hms_department.branch_id='.$users_data['parent_id'].' OR hms_department.branch_id=0)'); 
        }
        
        $this->db->where('hms_department_to_department_status.status','1');   
        $query = $this->db->get();
        $result = $query->result(); 
        return $result;*/

        $users_data = $this->session->userdata('auth_users');
        //$this->db->select('*');
        $this->db->select("hms_department.id,hms_department.branch_id,hms_department.module,hms_department.department,hms_department.ip_address,hms_department.is_deleted,hms_department.deleted_by,hms_department.deleted_date,hms_department.created_by,hms_department.modified_by,hms_department.modified_date,hms_department.created_date, (CASE WHEN hms_department.branch_id=0 THEN 1 ELSE hms_department_to_department_status.status END) status");
        
        $this->db->where('hms_department.is_deleted',0);
        $this->db->from('hms_department'); 
         $this->db->join('hms_department_to_department_status','hms_department_to_department_status.department_id = hms_department.id AND hms_department_to_department_status.branch_id="'.$users_data.'"','left');

        if(!empty($module_id))
        {
           $this->db->where('hms_department.module',$module_id); 
        }
        $this->db->where('(hms_department.branch_id='.$users_data['parent_id'].' OR hms_department.branch_id=0)'); 
        $this->db->where('hms_department_to_department_status.status','1');   
        $query = $this->db->get();
        $result = $query->result(); 
        return $result;
    }
    public function get_test_heads($department_id=''){
        $users_data = $this->session->userdata('auth_users');
    	if(!empty($department_id)){
    	    $this->db->select('path_test_heads.*');
    	    $this->db->where('dept_id',$department_id);
            $this->db->where('is_deleted',0);
            $this->db->where('branch_id',$users_data['parent_id']);
    	    $query = $this->db->get('path_test_heads');
    	    $result = $query->result();
    	    return $result;
    	}
    }
    public function get_test_list()
    {
    	$post = $this->input->post();

        $users_data = $this->session->userdata('auth_users');
        if(!empty($post['doctors_id']))
        {
           
           
    	   $this->db->select('path_test.branch_id,path_test.rate,path_test.base_rate,path_test.id,path_test.test_name,path_test.test_head_id,path_test.dept_id,hms_department.department,path_test_to_doctors.rate as test_rate,path_test_to_doctors.base_rate as test_base_rate,path_test_to_doctors.test_id,path_test_to_doctors.doc_id');
        }
        else if(!empty($post['branch_id']))
        {
           
           
            $this->db->select('path_test.*,path_panel_price.id,path_panel_price.panel_id,path_panel_price.test_id,(CASE WHEN path_panel_price.charge>0 THEN path_panel_price.charge ELSE path_test.rate END) as path_price,path_test.branch_id,,path_test.id,path_test.test_name,path_test.test_head_id,path_test.dept_id,hms_department.department');
            $this->db->join('path_panel_price','path_panel_price.test_id=path_test.id','left');
        }
        else
        {
           
            $this->db->select('path_test.branch_id,path_test.rate,path_test.base_rate,path_test.id,path_test.test_name,path_test.test_head_id,path_test.dept_id,hms_department.department,path_test_to_doctors.rate as test_rate,path_test_to_doctors.base_rate as test_base_rate,path_test_to_doctors.test_id,path_test_to_doctors.doc_id');
        }
    	$this->db->from('path_test');
    	$this->db->join("hms_department","path_test.dept_id=hms_department.id","left");
        // if(!empty($post['doctors_id']))
        // {
          
        //     $this->db->join("path_test_to_doctors","path_test.id=path_test_to_doctors.test_id and path_test_to_doctors.doc_id=".$post['doctors_id'],"left");
        // }
        if(!empty($post['paneln_ids']))
        {
          
            $this->db->where('path_panel_price.panel_id',$post['paneln_ids']);
        }
        else if(!empty($post['branch_id']))
        {
           
           // $this->db->join("path_test_to_doctors","path_test.id=path_test_to_doctors.test_id and path_test_to_doctors.branch_id=".$post['branch_id']."  and path_test_to_doctors.doc_id=0" ,"left");
        }
        else
        {
           
//$this->db->join("path_test_to_doctors","path_test.id=path_test_to_doctors.test_id and path_test_to_doctors.branch_id=".$users_data['parent_id'],"left");
        }
     
    	if(!empty($post['department_id']))
    	{
            $this->db->where('path_test.dept_id',$post['department_id']); 
    	}

        if(!empty($post['test_head_id']))
        { 
            
            $this->db->where('path_test.test_head_id',$post['test_head_id']);
        }

    	if(!empty($post['branch_id']) && $post['branch_id']!=$users_data['parent_id'])
    	{
            
    		$this->db->where('path_test.branch_id',$post['branch_id']);
            
    	}
    	else
    	{
           
    		$this->db->where('path_test.branch_id',$users_data['parent_id']);
    	}
 
        //$this->db->group_by('path_test.id');
    	$query = $this->db->get();
        //echo $this->db->last_query();die;
    	$result = $query->result_array();
    	//print_r($result);die;
    	return $result;


    }
    public function save_panel_rate()
    {
        $data = array();
        $post = $this->input->post();

        $users_data = $this->session->userdata('auth_users');
        if(isset($post) && !empty($post))
        {
            
            if(!empty($post['test_id']))
            {
              
                $result = $this->get_path_panel_charge($post['test_head_id'],$post['test_id'],$post['paneln_ids']);
               //print '<pre>' ;print_r($result);die;  
                if(!empty($result))
                {
                    $data = array(
                        'branch_id'=>$users_data['parent_id'],
                        'test_id'=>$post['test_id'],
                        'test_head_id'=>$post['test_head_id'],
                        'charge'=>$post['price'],
                        'type'=>0,
                        'panel_id'=>$post['paneln_ids']
                    );

                    $this->db->where('branch_id',$users_data['parent_id']);
                    $this->db->where('(test_id='.$result[0]->test_id.' and test_head_id='.$result[0]->test_head_id.' and panel_id='.$result[0]->panel_id.' and id='.$result[0]->id.')');
                    $this->db->update('path_panel_price',$data);
                   // echo $this->db->last_query();die;
                }
                else
                {
                    $data = array(
                        'branch_id'=>$users_data['parent_id'],
                        'test_id'=>$post['test_id'],
                        'test_head_id'=>$post['test_head_id'],
                        'charge'=>$post['price'],
                        'type'=>0,
                        'panel_id'=>$post['paneln_ids']
                    );
                    $this->db->set('created_by',$users_data['id']);
                    $this->db->set('created_date',date('Y-m-d H:i:s'));

                    //$this->db->set('modified_by',$user_data['id']);
                    //$this->db->set('modified_date',date('Y-m-d H:i:s'));
                    $this->db->insert('path_panel_price',$data);
                    //echo $this->db->last_query();die;
                }
            }
         
        }
    }


    public function save_panel_all_rate()
    {
        //echo 'dd';die;
        $data = array();
        $post = $this->input->post();
        $users_data = $this->session->userdata('auth_users');
         $test_master_head = $this->session->userdata('master_test_head');
    //  print_r($test_master_head);die;
       
        if(isset($post) && !empty($post['test_id']))
        {
           $branch_id = $users_data['parent_id'];
           
            foreach($post['test_id'] as $test_data)
            {  
                if(isset($test_data['test_id']) && $test_data['test_id']>0)
                {
                    $this->db->where('branch_id',$branch_id);
                    $this->db->where('test_id',$test_data['test_id']);
                    $this->db->where('test_head_id',$post['test_heads_id']);
                    $this->db->where('panel_id',$post['paneln_ids']);
                    $this->db->delete('path_panel_price');
                    
                       $data = array(
                            'branch_id'=>$branch_id,
                            'test_id'=>$test_data['test_id'],
                            'test_head_id'=>$post['test_heads_id'],
                            'charge'=>$test_data['path_price'],
                            'type'=>0,
                            'panel_id'=>$post['paneln_ids']
                        );
                    $this->db->insert('path_panel_price',$data);
                } 
            }
        }
    }
    function get_path_panel_charge($testhead_ids="",$test_id="",$paneln_ids="")
    {
       $this->db->select('*');
       $this->db->where('panel_id',$paneln_ids);
       $this->db->where('test_id',$test_id);
       $res= $this->db->get('path_panel_price')->result();
       return $res;
    }
    public function get_test_to_doctors()
    {
        $post = $this->input->post();
        $result = array();
        if(isset($post) && !empty($post))
        {
            $this->db->select('*');
            if(!empty($post['doc_id']))
            {
                $this->db->where('doc_id',$post['doc_id']);
            }
            if(!empty($post['test_id']))
            {
                $this->db->where('test_id',$post['test_id']);
            }
            if(!empty($post['branch_id']))
            {
                $this->db->where('branch_id',$post['branch_id']);
            }
            $query = $this->db->get('path_test_to_doctors');
            $result = $query->result_array();
            
        }
        return $result;
    }

    public function doctor_rate_plan($doctor_id="")
    {
        $users_data = $this->session->userdata('auth_users');  
        $company_data = $this->session->userdata('company_data');  
        $this->db->select('path_rate_plan.*');     
        $this->db->where('hms_doctors.branch_id',$company_data['parent_id']); 
        if(!empty($doctor_id))
        {
          $this->db->where('hms_doctors.id',$doctor_id);  
        } 
        $this->db->join('path_rate_plan','path_rate_plan.id = hms_doctors.rate_plan_id');
        $query_branch = $this->db->get('hms_doctors'); 
        $result_branch = $query_branch->result(); 
        return $result_branch;
    }
   
    public function doctor_test_price($test_id="", $doctor_id="")
    {
        $users_data = $this->session->userdata('auth_users');  
        $this->db->select('path_doctor_test_price.base_rate as doctor_base_rate, path_test.base_rate as test_base_rate'); 
        if(!empty($test_id))
        {
          $this->db->where('path_test.id',$test_id);  
        }  
        $this->db->join('path_doctor_test_price','path_doctor_test_price.test_id = path_test.id AND path_doctor_test_price.doctor_id = "'.$doctor_id.'"', 'left');
        $query_branch = $this->db->get('path_test'); 
        //echo $this->db->last_query();die;
        $result_branch = $query_branch->row_array(); 
        return $result_branch;
    }

    public function doctor_profile_price($profile_id="", $doctor_id="")
    {
        $users_data = $this->session->userdata('auth_users');  
        $this->db->select('path_profile_to_price.base_rate as doctor_base_rate, path_profile.base_rate as test_base_rate'); 
        if(!empty($profile_id))
        {
          $this->db->where('path_profile.id',$profile_id);  
        }  
        $this->db->join('path_profile_to_price','path_profile_to_price.profile_id = path_profile.id AND path_profile_to_price.doctor_id = "'.$doctor_id.'"', 'left');
        $query_branch = $this->db->get('path_profile'); 
        //echo $this->db->last_query();die;
        $result_branch = $query_branch->row_array(); 
        return $result_branch;
    }
    
    
    public function search_test_data()
	{
		$users_data = $this->session->userdata('auth_users');
        $parent_branch_details = $this->session->userdata('parent_branches_data');
        $sub_branch_details = $this->session->userdata('sub_branches_data');
		$this->db->select("path_test.*, path_unit.unit, hms_department.department, path_test_heads.test_heads, path_test_method.test_method, path_sample_type.sample_type"); 
		$this->db->from($this->table);   
        $this->db->where('path_test.is_deleted',0);
        $this->db->where('path_test.branch_id',$users_data['parent_id']);
        $this->db->join('path_unit','path_unit.id = path_test.unit_id','left');
        $this->db->join('path_test_method','path_test_method.id = path_test.method_id','left');
        $this->db->join('path_sample_type','path_sample_type.id = path_test.sample_test','left'); 
        $this->db->join('path_test_heads','path_test_heads.id = path_test.test_head_id','left');
        $this->db->join('hms_department','hms_department.id = path_test.dept_id','left');
        $test_master_search = $this->session->userdata('test_master_search');

        if(!empty($test_master_search) && isset($test_master_search['test_head']) && !empty($test_master_search['test_head']))
        {
        	$this->db->where('path_test.test_head_id',$test_master_search['test_head']);
        }
        
		if(!empty($test_master_search) && isset($test_master_search['dept_id']) && !empty($test_master_search['dept_id']))
		{
		    $this->db->where('path_test.dept_id',$test_master_search['dept_id']);
		}
		$this->db->order_by('path_test.id','DESC');
		 
		$query = $this->db->get(); 
		//echo $this->db->last_query();die;
		return $query->result();
	}
	
   public function get_panel_name($panel_id)
   {
        $this->db->select('hms_insurance_company.insurance_company');
        $this->db->where('hms_insurance_company.id',$panel_id);
        $query= $this->db->get('hms_insurance_company');
        //echo $this->db->last_query();die; 
        $results = $query->result();
        return $results[0]->insurance_company;
   }
   
   public function udpate_all_test($test_all_datas= array())
    {
    	//echo "<pre>"; print_r($test_all_datas); exit; 
    	$users_data = $this->session->userdata('auth_users'); 

    	///////////////// Set department //////////
		if(!empty($test_all_datas))
		{
		    foreach($test_all_datas as $test_all_data)
		    {
		    	
		    
    			    $result = $this->get_path_panel_charge($test_all_data['test_head_id'],$test_all_data['id'],$test_all_data['panel_id']);
                   //print '<pre>' ;print_r($result);die;  
                    if(!empty($result))
                    {
                        $data = array(
                            'branch_id'=>$users_data['parent_id'],
                            'test_id'=>$test_all_data['id'],
                            'test_head_id'=>$test_all_data['test_head_id'],
                            'charge'=>$test_all_data['rate'],
                            'type'=>0,
                            'panel_id'=>$test_all_data['panel_id']
                        );
    
                        $this->db->where('branch_id',$users_data['parent_id']);
                        $this->db->where('(test_id='.$result[0]->test_id.' and test_head_id='.$result[0]->test_head_id.' and panel_id='.$result[0]->panel_id.')');
                        $this->db->update('path_panel_price',$data);
                        //echo "inn";
                        //echo $this->db->last_query();die;
                    }
                    else
                    {
                        $data = array(
                            'branch_id'=>$users_data['parent_id'],
                            'test_id'=>$test_all_data['id'],
                            'test_head_id'=>$test_all_data['test_head_id'],
                            'charge'=>$test_all_data['rate'],
                            'type'=>0,
                            'panel_id'=>$test_all_data['panel_id']
                        );
                        $this->db->set('created_by',$users_data['id']);
                        $this->db->set('created_date',date('Y-m-d H:i:s'));
    
                        //$this->db->set('modified_by',$user_data['id']);
                        //$this->db->set('modified_date',date('Y-m-d H:i:s'));
                        $this->db->insert('path_panel_price',$data);
                       // echo $this->db->last_query();die;
                    }
			
			

				}

			}
    }


}
?>