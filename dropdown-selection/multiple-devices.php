<div class="col-sm-12">
  <div class="custom-control custom-checkbox pl-3">
    <input type="checkbox" id="select_all" style="width: auto; margin-top:10px" />
    <label class="small" > Select All</label>
  </div>
</div>
<div class="col-sm-12 text-right d-flex align-items-center ">
  <div class="col-12">
    <select id="multi_selection_device_id" class="multi_selection_device_id col-12"  multiple size="30" style="max-height: 250px;">
     <?php
     include("device_id_list.php");
     ?>
   </select>
 </div>
</div>
<div class="col-sm-12">
  <div class="custom-control custom-checkbox pl-3">
    <span>Selected : <b><span id="selected_count">0</span></b> </span>
  </div>
</div>