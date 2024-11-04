<?php
$users_data = $this->session->userdata('auth_users');
?>
<!DOCTYPE html>
<html>

<head>
  <title><?php echo $page_title . PAGE_TITLE; ?></title>
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
  <script type="text/javascript" src="<?php echo ROOT_JS_PATH; ?>bootstrap.min.js"></script>

  <!-- datatable js -->
  <script src="<?php echo ROOT_JS_PATH; ?>jquery.dataTables.min.js"></script>
  <script src="<?php echo ROOT_JS_PATH; ?>dataTables.bootstrap.min.js"></script>
  <link rel="stylesheet" type="text/css" href="<?php echo ROOT_CSS_PATH; ?>bootstrap-datepicker.css">
  <script type="text/javascript" src="<?php echo ROOT_JS_PATH; ?>bootstrap-datepicker.js"></script>
  <script type="text/javascript">
    var save_method;
    var table;
    <?php if (in_array('2485', $users_data['permission']['action'])) { ?>
      $(document).ready(function () {
        table = $('#table').DataTable({
          "processing": true,
          "serverSide": true,
          "order": [],
          "pageLength": '20',
          "ajax": {
            "url": "<?php echo base_url('low_vision/ajax_list') ?>",
            "type": "POST",
          },
          "columnDefs": [
            {
              "targets": [0, -1],
              "orderable": false,
            },
          ],
        });
      });
    <?php } ?>

    $(document).ready(function () {
      var $modal = $('#load_add_modal_popup');
      $('#doctor_add_modal').on('click', function () {
        $modal.load('<?php echo base_url() . 'low_vision/add/' ?>',
          {
            //'id1': '1',
            //'id2': '2'
          },
          function () {
            $modal.modal('show');
          });

      });


      $('#adv_search').on('click', function () {
        $modal.load('<?php echo base_url() . 'low_vision/advance_search/' ?>',
          {
          },
          function () {
            $modal.modal('show');
          });

      });

    });


    function edit_refraction(id) {
      // Redirect to the edit page for refraction with the specified ID
      window.location.href = '<?php echo base_url('low_vision/edit/'); ?>' + id;
    }

    function reload_table() {
      table.ajax.reload(null, false);
    }

    function checkboxValues() {
      $('#table').dataTable();
      var allVals = [];
      $(':checkbox').each(function () {
        if ($(this).prop('checked') == true) {
          allVals.push($(this).val());
        }
      });
      allbranch_delete(allVals);
    }

    function allbranch_delete(allVals) {
      if (allVals != "") {
        $('#confirm').modal({
          backdrop: 'static',
          keyboard: false
        })
          .one('click', '#delete', function (e) {
            $.ajax({
              type: "POST",
              url: "<?php echo base_url('low_vision/deleteall'); ?>",
              data: { row_id: allVals },
              success: function (result) {
                flash_session_msg(result);
                reload_table();
              }
            });
          });
      }
      else {
        $('#confirm-select').modal({
          backdrop: 'static',
          keyboard: false
        });
      }
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
      <div class="userlist-box">
        <div class="row m-b-5">
          <div class="col-xs-12">
            <div class="row">
              <div class="col-xs-6">
                <!-- Search area or other content -->
              </div>
              <div class="col-xs-6"></div>
            </div>
          </div>
        </div>
        <form name="search_form" id="search_form">

          <div class="row">
            <div class="col-sm-4">
              <div class="row m-b-5">
                <div class="col-xs-5"><label>From Date</label></div>
                <div class="col-xs-7">
                  <input id="start_date_patient" name="start_date" class="datepicker start_datepicker m_input_default"
                    type="text" value="<?php echo $form_data['start_date'] ?>">
                </div>
              </div>
              <div class="row m-b-5">
                <div class="col-xs-5"><label><?php echo $data = get_setting_value('PATIENT_REG_NO'); ?></label></div>
                <div class="col-xs-7">
                  <input name="patient_code" class="m_input_default" id="patient_code" onkeyup="return form_submit();"
                    value="<?php echo $form_data['patient_code'] ?>" type="text" autofocus>
                </div>
              </div>
              <div class="row m-b-5">
                <div class="col-xs-5"><label>Mobile No.</label></div>
                <div class="col-xs-7">
                  <input name="mobile_no" value="<?php echo $form_data['mobile_no'] ?>" id="mobile_no"
                    onkeyup="return form_submit();" class="numeric m_input_default" maxlength="10" value="" type="text">
                </div>
              </div>

            </div> <!-- 4 -->

            <div class="col-sm-4">
              <div class="row m-b-5">
                <div class="col-xs-5"><label>To Date</label></div>
                <div class="col-xs-7">
                  <input name="end_date" id="end_date_patient"
                    class="datepicker datepicker_to end_datepicker m_input_default"
                    value="<?php echo $form_data['end_date'] ?>" type="text">
                </div>
              </div>
              <div class="row m-b-5">
                <div class="col-xs-5"><label>Patient Name</label></div>
                <div class="col-xs-7">
                  <input name="patient_name" value="<?php echo $form_data['patient_name'] ?>" id="patient_name"
                    onkeyup="return form_submit();" class="alpha_space m_input_default" value="" type="text">
                </div>
              </div>

              <?php
              $users_data = $this->session->userdata('auth_users');

              if (array_key_exists("permission", $users_data)) {
                $permission_section = $users_data['permission']['section'];
                $permission_action = $users_data['permission']['action'];
              } else {
                $permission_section = array();
                $permission_action = array();
              }
              //print_r($permission_action);
              
              $new_branch_data = array();
              $users_data = $this->session->userdata('auth_users');
              $sub_branch_details = $this->session->userdata('sub_branches_data');
              $parent_branch_details = $this->session->userdata('parent_branches_data');


              if (!empty($users_data['parent_id'])) {
                $new_branch_data['id'] = $users_data['parent_id'];

                $users_new_data[] = $new_branch_data;
                $merg_branch = array_merge($users_new_data, $sub_branch_details);

                $ids = array_column($merg_branch, 'id');
                $branch_id = implode(',', $ids);
                $option = '<option value="' . $branch_id . '">All</option>';
              }

              ?>
              <?php if (in_array('1', $permission_section)) { ?>
                <div class="row m-b-5">
                  <div class="col-xs-5"><label>Branch</label></div>
                  <div class="col-xs-7">



                    <select name="branch_id" id="branch_id" onchange="return form_submit();">
                      <?php echo $option; ?>
                      <option selected="selected" <?php if (isset($_POST['branch_id']) && $_POST['branch_id'] == $users_data['parent_id']) {
                        echo 'selected="selected"';
                      } ?>
           value="<?php echo $users_data['parent_id']; ?>">Self</option>';
                      <?php
                      if (!empty($sub_branch_details)) {
                        $i = 0;
                        foreach ($sub_branch_details as $key => $value) {
                          ?>
                          <option value="<?php echo $sub_branch_details[$i]['id']; ?>" <?php if (isset($_POST['branch_id']) && $_POST['branch_id'] == $sub_branch_details[$i]['id']) {
                               echo 'selected="selected"';
                             } ?>>
                            <?php echo $sub_branch_details[$i]['branch_name']; ?>
                          </option>
                          <?php
                          $i = $i + 1;
                        }

                      }
                      ?>
                    </select>


                  </div>
                </div>

              <?php } else { ?>
                <input type="hidden" name="branch_id" id="branch_id" value="<?php echo $users_data['parent_id']; ?>">
              <?php } ?>

            </div> <!-- 4 -->

            <div class="col-sm-4 d-flex justify-content-center" style="margin-left: 178px;margin-top: 30px;">

              <!--<a class="btn-custom" id="reset_date" onclick="reset_search();"><i class="fa fa-refresh"></i> Reset</a>
<br>
  <a href="javascript:void(0)" class="btn-a-search" id="patient_adv_search">
    <i class="fa fa-cubes" aria-hidden="true"></i> 
    Search
  </a>-->
              <a class="btn-custom" id="reset_date" onclick="reset_search();"> Reset</a>
              <!--<a href="javascript:void(0)" class="btn-custom" id="patient_adv_search">
    <i class="fa fa-cubes" aria-hidden="true"></i> 
    Advance Search
  </a>-->
            </div> <!-- 4 -->


          </div> <!-- row -->


        </form>
        <form>
          <?php if (in_array('2485', $users_data['permission']['action'])) { ?>
            <!-- bootstrap data table -->
            <table id="table" class="table table-striped table-bordered prescription_list_tbl" cellspacing="0"
              width="100%">
              <thead class="bg-theme">
                <tr>
                  <th width="40" align="center"> <input type="checkbox" name="selectall" class="" id="selectAll" value="">
                  </th>
                  <th> Token No </th>
                  <th> OPD No. </th>
                  <th> Patient Reg No. </th>
                  <th> Patient Name </th>
                  <!-- <th> Patient Category </th> -->
                  <th> Mobile No </th>
                  <th> Age </th>
                  <!-- <th> Consultant </th> -->
                  <!-- <th> Lens </th> -->
                  <!-- <th> Comment </th> -->
                  <th> Patient Status </th>
                  <th> Created Date </th>
                  <th> Action </th>
                </tr>
              </thead>
            </table>
          <?php } ?>
        </form>
      </div>
      <div class="userlist-right">
        <div class="btns">
          <?php if (in_array('2486', $users_data['permission']['action'])) { ?>
            <!-- <button class="btn-update" id="1modal_add"
                onclick="window.location.href='<?php echo base_url('low_vision/add'); ?>'">
              <i class="fa fa-plus"></i> New
            </button> -->
          <?php } ?>
          <a data-toggle="tooltip" title="Download list in excel" href="#" id="low_vision_download_excel"
            class="btn-anchor m-b-2">
            <i class="fa fa-file-excel-o"></i> Excel
          </a>
          <a data-toggle="tooltip" title="Download list in pdf" href="#" id="low_vision_download_pdf"
            class="btn-anchor m-b-2">
            <i class="fa fa-file-pdf-o"></i> PDF
          </a>
          <?php if (in_array('2488', $users_data['permission']['action'])) { ?>
            <button class="btn-update" id="deleteAll" onclick="return checkboxValues();">
              <i class="fa fa-trash"></i> Delete
            </button>
          <?php } ?>
          <?php if (in_array('2486', $users_data['permission']['action'])) { ?>
            <button class="btn-update" onclick="reload_table()">
              <i class="fa fa-refresh"></i> Reload
            </button>
          <?php } ?>
          <button class="btn-update" onclick="window.location.href='<?php echo base_url(); ?>'">
            <i class="fa fa-sign-out"></i> Exit
          </button>
        </div>
      </div>

    </section>

    <?php $this->load->view('include/footer'); ?>

    <script>
      $(document).ready(function () {
        form_submit();
      });
      function reset_search() {
        $('#start_date_patient').val('');
        $('#end_date_patient').val('');
        $('#patient_code').val('');
        $('#patient_name').val('');
        $('#mobile_no').val('');

        $.ajax({
          url: "<?php echo base_url(); ?>low_vision/reset_search/",
          success: function (result) {
            reload_table();
          }
        });
      }

      $('.start_datepicker').datepicker({
        format: 'dd-mm-yyyy',
        autoclose: true,
        endDate: new Date(),
      }).on("change", function (selectedDate) {
        var start_data = $('.start_datepicker').val();
        $('.end_datepicker').datepicker('setStartDate', start_data);
        form_submit();
      });

      $('.end_datepicker').datepicker({
        format: 'dd-mm-yyyy',
        autoclose: true,
      }).on("change", function (selectedDate) {
        form_submit();
      });

      function form_submit() {
        $('#search_form').delay(200).submit();
      }
      $("#search_form").on("submit", function (event) {
        event.preventDefault();

        $.ajax({
          url: "<?php echo base_url('low_vision/advance_search/'); ?>",
          type: "post",
          data: $(this).serialize(),
          success: function (result) {
            reload_table();
          }
        });

      });
      function delete_vision(rate_id) {
        $('#confirm').modal({
          backdrop: 'static',
          keyboard: false
        })
          .one('click', '#delete', function (e) {
            $.ajax({
              url: "<?php echo base_url('low_vision/delete/'); ?>" + rate_id,
              success: function (result) {
                flash_session_msg(result);
                reload_table();
              }
            });
          });
      }
      <?php
      $flash_success = $this->session->flashdata('success');
      if (isset($flash_success) && !empty($flash_success)) {
        echo 'flash_session_msg("' . $flash_success . '");';
      }
      ?>

      $(document).ready(function () {
        $('#load_add_vision_popup').on('shown.bs.modal', function (e) {
          $('.inputFocus').focus();
        });
      });
      document.getElementById('low_vision_download_excel').addEventListener('click', function (e) {
        e.preventDefault();


        var fromDate = document.getElementById('start_date_patient').value;
        var toDate = document.getElementById('end_date_patient').value;


        var url = '<?php echo base_url("low_vision/low_vision_excel"); ?>';


        if (fromDate || toDate) {
          url += '?';
          if (fromDate) {
            url += 'start_date=' + encodeURIComponent(fromDate);
          }
          if (toDate) {
            url += (fromDate ? '&' : '') + 'end_date=' + encodeURIComponent(toDate);
          }
        }
        window.location.href = url;
      });

      document.getElementById('low_vision_download_pdf').addEventListener('click', function (e) {
        // alert();
        e.preventDefault();

        var fromDate = document.getElementById('start_date_patient').value;
        var toDate = document.getElementById('end_date_patient').value;


        var fromDateObj = new Date(fromDate);
        var toDateObj = new Date(toDate);



        var url = '<?php echo base_url("low_vision/low_vision_pdf"); ?>';
        url += '?start_date=' + encodeURIComponent(fromDate) + '&end_date=' + encodeURIComponent(toDate);

        window.location.href = url;
      });
    </script>

    <!-- Confirmation Modals -->
    <div id="confirm-select" class="modal fade dlt-modal">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header bg-theme">
            <h4>Please select at least one record.</h4>
          </div>
          <div class="modal-footer">
            <button type="button" data-dismiss="modal" class="btn-cancel">Close</button>
          </div>
        </div>
      </div>
    </div>

    <div id="confirm" class="modal fade dlt-modal">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header bg-theme">
            <h4>Are You Sure?</h4>
          </div>
          <div class="modal-body" style="font-size:8px;">*Data that has been archived for more than 60 days will be
            automatically deleted.</div>
          <div class="modal-footer">
            <button type="button" data-dismiss="modal" class="btn-update" id="delete">Confirm</button>
            <button type="button" data-dismiss="modal" class="btn-cancel">Cancel</button>
          </div>
        </div>
      </div>
    </div>

    <div id="load_add_vision_popup" class="modal fade" role="dialog" data-backdrop="static" data-keyboard="false"></div>
  </div>
</body>

</html>