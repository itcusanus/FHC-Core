<?php
$this->load->view('templates/FHC-Header',
	array(
		'title' => 'FH-Complete',
		'jquery' => true,
		'bootstrap' => true,
		'fontawesome' => true,
		'sbadmintemplate' => true
	)
);
?>
<body>
<div id="wrapper">
	<?php

	$navigationHeaderArray = array('headertext' => 'FH-Complete', 'headertextlink' => base_url('index.ci.php/'));
	$navigationMenuArray = array(
			'Dashboard' => array('link' => '#', 'description' => 'Dashboard', 'icon' => 'dashboard'),
			'Lehre' => array('link' => '#', 'icon' => 'graduation-cap', 'description' => 'Lehre', 'expand' => true,
				'children'=> array(
					'CIS' => array('link' => CIS_ROOT, 'icon' => '', 'description' => 'CIS', 'expand' => true),
					'Infocenter' => array('link' => base_url('index.ci.php/system/infocenter/InfoCenter'), 'icon' => 'info', 'description' => 'Infocenter', 'expand' => true),
				)
			),
			'Administration' => array('link' => '#', 'icon' => 'gear', 'description' => 'Administration', 'expand' => false,
				'children'=> array(
					'Vilesci' => array('link' => base_url('vilesci/'), 'icon' => '', 'description' => 'Vilesci', 'expand' => true),
					'Extensions' => array('link' => base_url('index.ci.php/system/extensions/Manager'), 'icon' => 'cubes', 'description' => 'Extensions Manager', 'expand' => true),
					'Datenschutz' => array('link' => base_url('index.ci.php/extensions/FHC-Core-DSMS/export'), 'description' => 'Datenschutz', 'icon' => 'legal','expand' => true)
				)
			),
		);

	echo $this->widgetlib->widget(
		'NavigationWidget',
		array(
			'navigationHeader' => $navigationHeaderArray,
			'navigationMenu' => $navigationMenuArray
		)
	);

	?>
	<div id="page-wrapper">
		<div class="container-fluid">
			<div class="row">
				<div class="col-lg-12">
					<h3 class="page-header">FH-Complete</h3>
				</div>
			</div>
			<div class="row">
				<div class="col-lg-3 col-md-6">
					<div class="panel panel-primary">
						<div class="panel-heading">
							<div class="row">
								<div class="col-xs-3">
									<i class="fa fa-comments fa-5x"></i>
								</div>
								<div class="col-xs-9 text-right">
									<div class="huge">26</div>
									<div>neue Messages</div>
								</div>
							</div>
						</div>
						<a href="#">
							<div class="panel-footer">
								<span class="pull-left">View Details</span>
								<span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
								<div class="clearfix"></div>
							</div>
						</a>
					</div>
				</div>
				<div class="col-lg-3 col-md-6">
					<div class="panel panel-green">
						<div class="panel-heading">
							<div class="row">
								<div class="col-xs-3">
									<i class="fa fa-tasks fa-5x"></i>
								</div>
								<div class="col-xs-9 text-right">
									<div class="huge">12</div>
									<div>neue Interessenten</div>
								</div>
							</div>
						</div>
						<a href="#">
							<div class="panel-footer">
								<span class="pull-left">View Details</span>
								<span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
								<div class="clearfix"></div>
							</div>
						</a>
					</div>
				</div>
				<div class="col-lg-3 col-md-6">
					<div class="panel panel-yellow">
						<div class="panel-heading">
							<div class="row">
								<div class="col-xs-3">
									<i class="fa fa-clock-o fa-5x"></i>
								</div>
								<div class="col-xs-9 text-right">
									<div class="huge">124</div>
									<div>inaktive Interessenten</div>
								</div>
							</div>
						</div>
						<a href="#">
							<div class="panel-footer">
								<span class="pull-left">View Details</span>
								<span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
								<div class="clearfix"></div>
							</div>
						</a>
					</div>
				</div>
				<div class="col-lg-3 col-md-6">
					<div class="panel panel-red">
						<div class="panel-heading">
							<div class="row">
								<div class="col-xs-3">
									<i class="fa fa-support fa-5x"></i>
								</div>
								<div class="col-xs-9 text-right">
									<div class="huge">13</div>
									<div>Support Tickets!</div>
								</div>
							</div>
						</div>
						<a href="#">
							<div class="panel-footer">
								<span class="pull-left">View Details</span>
								<span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
								<div class="clearfix"></div>
							</div>
						</a>
					</div>
				</div>
			</div>
			<!-- /.row -->

			<span>
			<?php
			//$this->load->view('system/infocenter/infocenterData.php');
			?>
		</span>
		</div>
	</div>
</div>
<script>
	//javascript hacks for bootstrap
	$("select").addClass("form-control");
	$("input[type=text]").addClass("form-control");
	$("input[type=button]").addClass("btn btn-default");
	$("#tableDataset").addClass('table-bordered');
</script>
</body>
<?php $this->load->view('templates/FHC-Footer'); ?>
