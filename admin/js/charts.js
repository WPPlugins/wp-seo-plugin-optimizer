jQuery(document).ready(function($){
	
	if (typeof google === 'object')
	{
		google.charts.load('current', {'packages':['corechart', "table"]});
	}
	var $ppc = $('.progress-pie-chart');
	var percent = parseInt($ppc.data('percent'));
	var deg = 360*percent/100;

	if (percent > 50) {
		$ppc.addClass('gt-50');
	}
	$('.ppc-progress-fill').css('transform','rotate('+ deg +'deg)');
	$('.ppc-percents span').html(percent+'%');
});
	
