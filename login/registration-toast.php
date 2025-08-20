<div class="toast-container position-fixed top-0 end-0 p-3 ">

  <div id="liveToast" class="toast" role="alert" aria-live="assertive" aria-atomic="true" >
    <div class="toast-header bg-danger bg-opacity-75" >
      <img style="height: 25px" class="logo-img rounded me-2"  src="<?php echo BASE_PATH;?>assets/logos/istl_light.png" alt="iScientific">
      <strong class="me-auto text-white">Info</strong>
      <small></small>
      <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
    </div>
    <div class="toast-body bg-danger rounded-bottom">
      <div class="text-center text-white ">
       Please Contact iSTLABS 
     </div>
   </div>
 </div>
</div>

<script type="text/javascript">
  const toastTrigger = document.getElementById('liveToastBtn')
  const toastLiveExample = document.getElementById('liveToast')

  if (toastTrigger) {
    const toastBootstrap = bootstrap.Toast.getOrCreateInstance(toastLiveExample)
    toastTrigger.addEventListener('click', () => {
      toastBootstrap.show()
    })
  }
</script>