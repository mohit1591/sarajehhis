<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Low_vision_model extends CI_Model
{

    var $table = 'hms_low_vision'; // Define the table name
    var $order = array('pres_id' => 'desc'); // Define the default order

    // Define the columns you want to retrieve from the table
    var $column = array(
        'hms_low_vision.id',
        'hms_low_vision.branch_id', 
        'hms_low_vision.booking_code', 
        'hms_low_vision.pres_id',
        'hms_low_vision.patient_id', 
        'hms_low_vision.booking_id',
        'hms_low_vision.color_vision', // New column
        'hms_low_vision.contrast_sensivity', // New column
        'hms_low_vision.amsler_grid', // New column
        'hms_low_vision.lva_trial', // New column
        'hms_low_vision.distance_lva', // New column
        'hms_low_vision.near_lva', // New column
        'hms_low_vision.non_optical_device', // New column
        'hms_low_vision.final_advice', // New column
        'hms_low_vision.referred_for', // New column
        'hms_low_vision.follow_up', // New column
        'hms_low_vision.optometrist_signature',
        'hms_low_vision.doctor_signature',
        'hms_low_vision.status', 
        'hms_low_vision.is_deleted', 
        'hms_low_vision.deleted_by', 
        'hms_low_vision.deleted_date', 
        'hms_low_vision.ip_address',
        'hms_low_vision.created_by',
        'hms_low_vision.modified_by',
        'hms_low_vision.modified_date',
        'hms_low_vision.created_date',
        'hms_patient.patient_name',
        'hms_patient.mobile_no',
        'hms_patient.patient_code'
    );


    public function __construct()
    {
        parent::__construct();
        $this->load->database();

    }

    private function _get_datatables_query()
    {
        $search = $this->session->userdata('prescription_search');

        // Build the query
        $this->db->select("hms_low_vision.id as refraction_id, hms_low_vision.*, hms_patient.*, hms_patient_category.patient_category as patient_category_name, hms_opd_booking.*, hms_doctors.doctor_name");
        $this->db->from($this->table);
        $this->db->join('hms_patient', 'hms_patient.id = hms_low_vision.patient_id', 'left');
        $this->db->join('hms_patient_category', 'hms_patient_category.id=hms_patient.patient_category', 'left');
        $this->db->join('hms_opd_booking', 'hms_opd_booking.id = hms_low_vision.booking_id', 'left');
        $this->db->join('hms_doctors', 'hms_doctors.id = hms_opd_booking.attended_doctor', 'left');
        $this->db->where('hms_low_vision.is_deleted', '0');
        
        $i = 0;

        // Handle search filters if available
        foreach ($this->column as $item) {
            if (!empty($_POST['search']['value'])) {
                if ($i === 0) {
                    $this->db->group_start();
                    $this->db->like($item, $_POST['search']['value']);
                } else {
                    $this->db->or_like($item, $_POST['search']['value']);
                }

                if ($i == count($this->column) - 1) {
                    $this->db->group_end();
                }
            }
            $i++;
        }

        // Apply filters from search data
        if (!empty($search)) {
            if (!empty($search['start_date'])) {
                $start_date = date('Y-m-d 00:00:00', strtotime($search['start_date']));
                $this->db->where('hms_low_vision.created_date >=', $start_date);
            }

            if (!empty($search['end_date'])) {
                $end_date = date('Y-m-d 23:59:59', strtotime($search['end_date']));
                $this->db->where('hms_low_vision.created_date <=', $end_date);
            }

            if (!empty($search['patient_name'])) {
                $this->db->like('hms_patient.patient_name', $search['patient_name'], 'after');
            }

            if (!empty($search['patient_code'])) {
                $this->db->where('hms_patient.patient_code', $search['patient_code']);
            }

            if (!empty($search['mobile_no'])) {
                $this->db->where('hms_patient.mobile_no', $search['mobile_no']);
            }
        }

        // Handle order by
        if (isset($_POST['order'])) {
            $this->db->order_by($this->column[$_POST['order']['0']['column']], $_POST['order']['0']['dir']);
        } else if (isset($this->order)) {
            $order = $this->order;
            $this->db->order_by(key($order), $order[key($order)]);
        }
    }


    public function get_datatables()
    {
        // Call the datatables query method
        $this->_get_datatables_query();
        
        // Execute the query and handle errors silently
        $query = $this->db->get();

        // Return an empty array if the query fails
        if ($query === false) {
            return []; // Return an empty result in case of failure
        }

        return $query->result(); // Return the result if successful
    }


    public function count_filtered()
    {
        $this->_get_datatables_query();
        $query = $this->db->get();
        return $query->num_rows();
    }

    public function get_by_id($id)
    {
        // echo $id;die;
        $this->db->select('hms_low_vision.*, hms_patient.*');
        $this->db->from($this->table);
        $this->db->join('hms_patient', 'hms_patient.id = hms_low_vision.patient_id', 'left');
        $this->db->where('hms_low_vision.id', $id);
        $this->db->where('hms_low_vision.is_deleted', '0');
        $query = $this->db->get();
        return $query->row_array();
    }

    public function count_all()
    {
        $this->db->from($this->table);
        return $this->db->count_all_results();
    }

    public function get_patient_name_by_booking_id($booking_id)
    {
        // echo $booking_id;
        // Join hms_std_eye_prescription with hms_patient, hms_token, and hms_patient_category to get all details
        $this->db->select('hms_std_eye_prescription.*, hms_patient.*, hms_token.token_no, hms_patient_category.patient_category'); // Select necessary columns including category_name
        $this->db->from('hms_std_eye_prescription');
        $this->db->join('hms_patient', 'hms_patient.id = hms_std_eye_prescription.patient_id', 'left');
        $this->db->join('hms_token', 'hms_token.patient_id = hms_patient.id', 'left'); // Join with hms_token on patient_id
        $this->db->join('hms_patient_category', 'hms_patient_category.id = hms_patient.patient_category', 'left'); // Join with hms_patient_category on patient_category
        $this->db->where('hms_std_eye_prescription.patient_id', $booking_id); // Filter by booking_id
        $this->db->where('hms_std_eye_prescription.is_deleted', '0'); // Check if not deleted

        $query = $this->db->get();
        // echo "<pre>";print_r($query);

        if ($query->num_rows() > 0) {
            return $query->row_array(); // Return all columns as an associative array including category_name
        }

        return null; // Return null if no patient is found
    }

    // Method to get prescription refraction details by booking ID and prescription ID
    public function get_prescription_refraction_new_by_id($booking_id = '', $presc_id = '')
    {
        $this->db->select('*');
        $this->db->from($this->table); // Use the defined table name
        $this->db->where('pres_id', $presc_id);
        $this->db->where('patient_id', $booking_id);

        $result = $this->db->get()->row_array();
        return $result; // Return the result as an associative array
    }

    // New method to fetch all non-deleted refractions
    public function get_all_refractions()
    {
        $this->db->select('*');
        $this->db->from($this->table);
        $this->db->where('is_deleted', 0); // Filter out deleted records
        $query = $this->db->get();
        return $query->result(); // Return the result as an array of objects
    }

    public function get_booking_by_id($booking_id)
	{
		// Select all fields from both tables
		$this->db->select('hms_opd_booking.*, hms_patient.*'); // Select all fields
		$this->db->from('hms_opd_booking'); // Start with the bookings table
		$this->db->join('hms_patient', 'hms_patient.id = hms_opd_booking.patient_id', 'left'); // Join with the patient table

		// Filter by the booking ID
		$this->db->where('hms_opd_booking.id', $booking_id); // Assuming 'id' is the primary key for bookings
		$query = $this->db->get();

		// Check if any results were returned
		if ($query->num_rows() > 0) {
			return $query->row_array(); // Return the first result as an associative array
		}

		return null; // Return null if no data found
	}

    // Method to save or update a refraction record
    public function save($data)
    {
        // echo "<pre>";print_r($data['id']);die('dsfsdfsf');
        // If a pres_id exists, update the record
        if (!empty($data['id'])) {
            $this->db->where('id', $data['id']);
            $this->db->update($this->table, $data);
        } else {
            // Otherwise, insert a new record
            // echo "<pre>";print_r($data);die;
            $this->db->insert($this->table, $data);
        }
    }

    // Method to delete a refraction record
    public function delete($pres_id = "")
    {
        if (!empty($pres_id)) {
            $this->db->set('is_deleted', 1);
            $this->db->where('pres_id', $pres_id);
            $this->db->update($this->table); // Update the record to mark it as deleted
        }
    }

    public function search_report_data($id = '', $booking_id = '')
    {
        // echo $booking_id;die;
        // echo $booking_id;die;
        $this->db->select("
            hms_low_vision.id AS low_vision_id,
            hms_low_vision.color_vision,
            hms_low_vision.contrast_sensivity, 
            hms_low_vision.amsler_grid,
            hms_low_vision.lva_trial,
            hms_low_vision.distance_lva,
            hms_low_vision.near_lva,
            hms_low_vision.non_optical_device,
            hms_low_vision.final_advice,
            hms_low_vision.referred_for,
            hms_low_vision.follow_up,
            hms_low_vision.created_date,
            hms_low_vision.booking_id,
            hms_low_vision.patient_id,
            hms_patient.simulation_id,
            hms_patient.patient_name,
            hms_patient.patient_code,
            hms_patient.gender,
            hms_patient.mobile_no,
            hms_patient.age_y,
            hms_patient.age_m,
            hms_patient.age_d,
            hms_opd_booking.dilate_status,
            hms_opd_booking.app_type,
            hms_opd_booking.token_no,
            hms_opd_booking.booking_code
        ");

        $this->db->from('hms_low_vision');
        $this->db->join('hms_patient', 'hms_patient.id = hms_low_vision.patient_id', 'left');
        $this->db->join('hms_opd_booking', 'hms_opd_booking.id = hms_low_vision.booking_id', 'left');
        $this->db->where('hms_low_vision.patient_id', $booking_id);
        $this->db->where('hms_low_vision.is_deleted', '0');

        $query = $this->db->get();

        // Check if the query executed successfully
        if ($query->num_rows() > 0) {
            // Return the result as an array of objects
            return $query->result();
        } else {
            // Return an empty array if no results were found
            return [];
        }
    }

    public function get_by_low_vision_status($booking_id = '', $patient_id = '')
    {
        // Build the query
        $this->db->select('booking_id');
        $this->db->from('hms_low_vision');
        $this->db->where('hms_low_vision.booking_id', $booking_id);
        $this->db->where('hms_low_vision.patient_id', $patient_id);
        $this->db->where('hms_low_vision.is_deleted', 0);
        
        // Execute the query
        $query = $this->db->get();

        // If query failed, return false silently
        if ($query === false) {
            return false; // Silently return false in case of a query error
        }

        // If query is successful but no rows found, return false
        if ($query->num_rows() > 0) {
            return 1; // Return 1 if a match is found
        }

        // Return false if no rows match the criteria
        return false;
    }

}
