<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Disease extends CI_Controller {

    public function __construct()
    {
        parent::__construct();
        auth_users();
        $this->load->model('dental/disease/Disease_model','disease');
        $this->load->library('form_validation');
    }

    public function index()
    { 
        //echo "hi";die;
        unauthorise_permission('278','1639');
        $data['page_title'] = ' Disease List'; 
        $this->load->view('dental/disease/list',$data);
    }

    public function ajax_list()
    { 
       
        unauthorise_permission('278','1639');
         $users_data = $this->session->userdata('auth_users');
        $list = $this->disease->get_datatables();  
        $data = array();
        $no = $_POST['start'];
        $i = 1;
        $total_num = count($list);
        foreach ($list as $chief_complaints) {
         // print_r($chief_complaints);die;
            $no++;
            $row = array();
            if($chief_complaints->status==1)
            {
                $status = '<font color="green">Active</font>';
            }   
            else{
                $status = '<font color="red">Inactive</font>';
            } 
            
            ////////// Check  List /////////////////
            $check_script = "";
            if($i==$total_num)
            {
            /*$check_script = "<script>$('#selectAll').on('click', function () { 
                                  if ($(this).hasClass('allChecked')) {
                                      $('.checklist').prop('checked', false);
                                  } else {
                                      $('.checklist').prop('checked', true);
                                  }
                                  $(this).toggleClass('allChecked');
                              })</script>";*/
            }                 
            ////////// Check list end ///////////// 
            $row[] = '<input type="checkbox" name="employee[]" class="checklist" value="'.$chief_complaints->id.'">'.$check_script; 
            $row[] = $chief_complaints->disease_name;  
            $row[] = $status;
            //$row[] = date('d-M-Y H:i A',strtotime($chief_complaints->created_date)); 
           
          $btnedit='';
          $btndelete='';
          if(in_array('1641',$users_data['permission']['action'])){
               $btnedit = ' <a onClick="return edit_disease('.$chief_complaints->id.');" class="btn-custom" href="javascript:void(0)" style="'.$chief_complaints->id.'" title="Edit"><i class="fa fa-pencil" aria-hidden="true"></i> Edit</a>';
          }
          if(in_array('1642',$users_data['permission']['action'])){
               $btndelete = ' <a class="btn-custom" onClick="return delete_disease('.$chief_complaints->id.')" href="javascript:void(0)" title="Delete" data-url="512"><i class="fa fa-trash"></i> Delete</a> '; 
          }
          $row[] = $btnedit.$btndelete;
             
        
            $data[] = $row;
            $i++;
        }

        $output = array(
                        "draw" => $_POST['draw'],
                        "recordsTotal" => $this->disease->count_all(),
                        "recordsFiltered" => $this->disease->count_filtered(),
                        "data" => $data,
                );
        //output to json format
        echo json_encode($output);
    }
    
    
    public function add()
    {
        unauthorise_permission('278','1640');
        $data['page_title'] = "Add Disease";  
        $post = $this->input->post();
        $data['form_error'] = []; 
        $data['form_data'] = array(
                                  'data_id'=>"", 
                                  'disease_name'=>"",
                                  'status'=>"1"
                                  );    

        if(isset($post) && !empty($post))
        {   
            $data['form_data'] = $this->_validate();
            if($this->form_validation->run() == TRUE)
            {
                $this->disease->save();
                echo 1;
                return false;
                
            }
            else
            {
                $data['form_error'] = validation_errors();  
            }     
        }
       $this->load->view('dental/disease/add',$data);       
    }
    
    public function edit($id="")
    {
      unauthorise_permission('278','1641');
     if(isset($id) && !empty($id) && is_numeric($id))
      {      
        $result = $this->disease->get_by_id($id);  
        $data['page_title'] = "Update Disease";  
        $post = $this->input->post();
        $data['form_error'] = ''; 
        $data['form_data'] = array(
                                  'data_id'=>$result['id'],
                                  'disease_name'=>$result['disease_name'], 
                                  'status'=>$result['status']
                                  );  
        
        if(isset($post) && !empty($post))
        {   
            $data['form_data'] = $this->_validate();
            if($this->form_validation->run() == TRUE)
            {
                $this->disease->save();
                echo 1;
                return false;
                
            }
            else
            {
                $data['form_error'] = validation_errors();  
            }     
        }
       $this->load->view('dental/disease/add',$data);       
      }
    }
     
    private function _validate()
    {
        $post = $this->input->post();    
        $this->form_validation->set_error_delimiters('<div class="text-danger">', '</div>');  
        $this->form_validation->set_rules('disease_name', 'disease name', 'trim|required|callback_disease'); 
        
        if ($this->form_validation->run() == FALSE) 
        {  
            //$reg_no = generate_unique_id(2); 
            $data['form_data'] = array(
                                        'data_id'=>$post['data_id'],
                                        'disease_name'=>$post['disease_name'], 
                                        'status'=>$post['status']
                                       ); 
            return $data['form_data'];
        }   
    }


    /* callbackurl */
    /* check validation laready exit */

    public function disease($str){
 
          $post = $this->input->post();
          if(!empty($post['disease_name']))
          {
               $this->load->model('dental/general/dental_general_model','general'); 
               if(!empty($post['data_id']) && $post['data_id']>0)
               {
                  
                    $data_cat= $this->disease->get_by_id($post['data_id']);
                     // echo "<pre>"; print_r($data_cat); die;
                      if(strtolower($data_cat['disease_name'])==strtolower($str) && $post['data_id']==$data_cat['id'])
                      {
                           
                          return true;  
                      }
                      else
                      {
                        $check_complain = $this->general->check_disease($str);

                        if(empty($check_complain))
                        {
                        return true;
                        }
                        else
                        {
                        $this->form_validation->set_message('disease_name', 'The disease already exists.');
                        return false;
                        }
                      }
               }
               else
               {
                    $cheif_complain = $this->general->check_disease($post['disease_name'], $post['data_id']);
                    if(empty($cheif_complain))
                    {
                         return true;
                    }
                    else
                    {
                         $this->form_validation->set_message('disease_name', 'The disease already exists.');
                         return false;
                    }
               }  
          }
          else
          {
               $this->form_validation->set_message('disease_name', 'disease field is required.');
               return false; 
          } 
     }
     /* check validation laready exit */
 
    public function delete($id="")
    {
       unauthorise_permission('278','1642');
       if(!empty($id) && $id>0)
       {
           $result = $this->disease->delete($id);
           $response = "Disease Successfully deleted.";
           echo $response;
       }
    }

    function deleteall()
    {
       unauthorise_permission('278','1642');
        $post = $this->input->post();  
        if(!empty($post))
        {
            $result = $this->disease->deleteall($post['row_id']);
            $response = "Chief Complaints successfully deleted.";
            echo $response;
        }
    }

    public function view($id="")
    {  
     if(isset($id) && !empty($id) && is_numeric($id))
      {      
        $data['form_data'] = $this->chief_complaints->get_by_id($id);  
        $data['page_title'] = $data['form_data']['chief_complaints']." detail";
        $this->load->view('eye/chief_complaints/view',$data);     
      }
    }  


    ///// employee Archive Start  ///////////////
    public function archive()
    {
        unauthorise_permission('278','1643');
        $data['page_title'] = 'Disease Archive List';
        $this->load->helper('url');
        $this->load->view('dental/disease/archive',$data);
    }

    public function archive_ajax_list()
    {
        unauthorise_permission('278','1643');
        $users_data = $this->session->userdata('auth_users');
        $this->load->model('dental/disease/disease_archive_model','disease_archive'); 

        $list = $this->disease_archive->get_datatables(); 
       // print_r($list);die; 
        $data = array();
        $no = $_POST['start'];
        $i = 1;
        $total_num = count($list);
        foreach ($list as $chief_complaints) { 
            $no++;
            $row = array();
            if($chief_complaints->status==1)
            {
                $status = '<font color="green">Active</font>';
            }   
            else{
                $status = '<font color="red">Inactive</font>';
            } 
            
            ////////// Check  List /////////////////
            $check_script = "";
            if($i==$total_num)
            {
            /*$check_script = "<script>$('#selectAll').on('click', function () { 
                                  if ($(this).hasClass('allChecked')) {
                                      $('.checklist').prop('checked', false);
                                  } else {
                                      $('.checklist').prop('checked', true);
                                  }
                                  $(this).toggleClass('allChecked');
                              })</script>";*/
            }                 
            ////////// Check list end ///////////// 
            $row[] = '<input type="checkbox" name="employee[]" class="checklist" value="'.$chief_complaints->id.'">'.$check_script; 
            $row[] = $chief_complaints->disease_name;  
            $row[] = $status;
            ///$row[] = date('d-M-Y H:i A',strtotime($chief_complaints->created_date)); 
            
          $btnrestore='';
          $btndelete='';
          if(in_array('1645',$users_data['permission']['action'])){
               $btnrestore = ' <a onClick="return restore_disease('.$chief_complaints->id.');" class="btn-custom" href="javascript:void(0)"  title="Restore"><i class="fa fa-window-restore" aria-hidden="true"></i> Restore </a>';
          }
          if(in_array('1644',$users_data['permission']['action'])){
               $btndelete = ' <a onClick="return trash('.$chief_complaints->id.');" class="btn-custom" href="javascript:void(0)" title="Delete"><i class="fa fa-trash" aria-hidden="true"></i> Delete</a>'; 
           }
          $row[] = $btnrestore.$btndelete;
        
            $data[] = $row;
            $i++;
        }

        $output = array(
                        "draw" => $_POST['draw'],
                        "recordsTotal" => $this->disease_archive->count_all(),
                        "recordsFiltered" => $this->disease_archive->count_filtered(),
                        "data" => $data,
                );
        //output to json format
        echo json_encode($output);
    }

    public function restore($id="")
    {
        unauthorise_permission('278','1645');
        $this->load->model('dental/disease/disease_archive_model','disease_archive');
       if(!empty($id) && $id>0)
       {
           $result = $this->disease_archive->restore($id);
           $response = "Disease successfully restore in Disease list.";
           echo $response;
       }
    }

    function restoreall()
    { 
       unauthorise_permission('278','1645');
        $this->load->model('dental/disease/disease_archive_model','disease_archive');
        $post = $this->input->post();  
        if(!empty($post))
        {
            $result = $this->disease_archive->restoreall($post['row_id']);
            $response = "Disease successfully restore in Disease list.";
            echo $response;
        }
    }

    public function trash($id="")
    {
        unauthorise_permission('278','1644');
           $this->load->model('dental/disease/disease_archive_model','disease_archive');
       if(!empty($id) && $id>0)
       {
           $result = $this->disease_archive->trash($id);
           $response = "Disease successfully restore in Disease list.";
           echo $response;
       }
    }

    function trashall()
    {
        unauthorise_permission('278','1644');
         $this->load->model('dental/disease/disease_archive_model','disease_archive');
        $post = $this->input->post();  
        if(!empty($post))
        {
            $result = $this->disease_archive->trashall($post['row_id']);
            $response = "Disease successfully deleted parmanently.";
            echo $response;
        }
    }
    ///// employee Archive end  ///////////////

  public function disease_dropdown()
  {

      $disease_list = $this->disease->disease_list();
      $dropdown = '<option value="">Select disease</option>'; 
      if(!empty($disease_list))
      {
        foreach($disease_list as $disease)
        {
           $dropdown .= '<option value="'.$disease->id.'">'.$disease->disease_name.'</option>';
        }
      } 
      echo $dropdown; 
  }

}
?>