$(document).ready(function(){
	var isMobile = /iPhone|iPad|iPod|Android/i.test(navigator.userAgent);
	var select = document.getElementsByClassName('multi_selection_device_id');
	if (!isMobile) {
		$('.multi_selection_device_id').on('mousedown', function(e) {
			e.preventDefault();
			var scroll = this.scrollTop;
			e.target.selected = !e.target.selected;
			setTimeout(() => {
				this.scrollTop = scroll;
			}, 0);
		}).on('mousemove', function(e) {
			e.preventDefault();
		});
	}
	else {
		$('.multi_selection_device_id').on('change', function(e) {
			var options = this.options;
			var scroll = this.scrollTop;
			for (var i = 0; i < options.length; i++) {
				if (options[i].selected) {
					options[i].selected = true;
				}
			}
			setTimeout(() => {
				this.scrollTop = scroll;
			}, 0);
		});
		var selects = document.getElementsByClassName('multi_selection_device_id');
		for (var i = 0; i < selects.length; i++) {
			selects[i].style.height = '50px';
		}
	}
	cancel_all_sections();
	$('#select_all').click(function() {
		if($(this).is(':checked'))
		{
			$('#multi_selection_device_id option').prop("selected", true)
		}
		else
		{
			$('#multi_selection_device_id option').prop("selected", false)
		}
		var count = $("#multi_selection_device_id :selected").length;        
		$('#selected_count').text(count);
	});

	$('#cancel_all').click(function() {
		cancel_all_sections();
	});

	$('#multi_selection_device_id').click(function()
	{
		var count = $("#multi_selection_device_id :selected").length;        
		$('#selected_count').text(count);

	});
	function cancel_all_sections()
	{
		$("#select_all").prop("checked", false);
		$('#multi_selection_device_id option').prop("selected", false);
		var count = $("#multi_selection_device_id :selected").length;        
		$('#selected_count').text(count);
	}
});









$(document).ready(function(){
	var isMobile = /iPhone|iPad|iPod|Android/i.test(navigator.userAgent);
	var select = document.getElementsByClassName('multi_selection_device_id1');
	if (!isMobile) {
		$('.multi_selection_device_id1').on('mousedown', function(e) {
			e.preventDefault();
			var scroll = this.scrollTop;
			e.target.selected = !e.target.selected;
			setTimeout(() => {
				this.scrollTop = scroll;
			}, 0);
		}).on('mousemove', function(e) {
			e.preventDefault();
		});
	}
	else {
		$('.multi_selection_device_id1').on('change', function(e) {
			var options = this.options;
			var scroll = this.scrollTop;
			for (var i = 0; i < options.length; i++) {
				if (options[i].selected) {
					options[i].selected = true;
				}
			}
			setTimeout(() => {
				this.scrollTop = scroll;
			}, 0);
		});
		var selects = document.getElementsByClassName('multi_selection_device_id1');
		for (var i = 0; i < selects.length; i++) {
			selects[i].style.height = '50px';
		}
	}
	cancel_all_sections();
	$('#select_all1').click(function() {
		if($(this).is(':checked'))
		{
			$('#multi_selection_device_id1 option').prop("selected", true)
		}
		else
		{
			$('#multi_selection_device_id1 option').prop("selected", false)
		}
		var count = $("#multi_selection_device_id1 :selected").length;        
		$('#selected_count1').text(count);
	});

	$('#cancel_all').click(function() {
		cancel_all_sections();
	});

	$('#multi_selection_device_id1').click(function()
	{
		var count = $("#multi_selection_device_id1 :selected").length;        
		$('#selected_count1').text(count);

	});
	function cancel_all_sections()
	{
		$("#select_all1").prop("checked", false);
		$('#multi_selection_device_id1 option').prop("selected", false);
		var count = $("#multi_selection_device_id1 :selected").length;        
		$('#selected_count1').text(count);
	}
});
