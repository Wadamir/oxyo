<?php echo $header; ?>
<?php echo $column_left; ?>
<div id="content" class="module-yamarket-fusion">
	<div class="page-header">
		<div class="container-fluid" style="position: relative;">
			<div class="pull-right">
				<a href="<?php echo $cancel; ?>" data-toggle="tooltip" title="<?php echo $button_cancel; ?>" class="btn btn-default"><i class="fa fa-reply"></i></a>
			</div>
			<h1><?php echo $heading_title; ?></h1>
			<ul class="breadcrumb">
				<?php foreach ($breadcrumbs as $breadcrumb) { ?>
				<li><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a></li>
				<?php } ?>
			</ul>
		</div>
	</div>
	<div class="container">
		<div class="content">
			<div id="aside-center">
				
						<div class="row">
			<div class="col-sm-6 col-sm-push-3">
				<div class="card" id="auth">
									<div class="body-card">
										<!-- Start content-->					
					<div class="panel-body">
						<h1 class="text-center"><?= $text_auth_title ?></h1>
						<div class="text-muted"><?= $text_auth_description ?></div>
						<form id="form-auth" action="<?= $action ?>" method="post">
							<output name="error"></output>
							<div class="form-group">
								<label for="input-email"><?= $entry_auth_email; ?></label>
								<input id="input-email" type="text" class="form-control" name="email" placeholder="<?= $entry_auth_email; ?>">
							</div>
							<div class="form-group">
								<label for="input-password"><?= $entry_auth_password; ?></label>
								<input id="input-password" type="password" class="form-control" name="password" placeholder="<?= $entry_auth_password; ?>">
							</div>
							<div class="form-group text-center">
								<input class="btn btn-primary" type="submit" value="<?= $button_login; ?>">
							</div>
						</form>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<script>
jQuery(function() {
	$('#form-auth').submit(function() {
		var $output = $(this).find('output[name=error]');
		
		$.ajax({
			url: this.action,
			type: 'post',
			data: $(this).serialize(),
			dataType: 'json',
			beforeSend: function() {
				$output.empty();
			},
			success: function(json) {
				if (json['success']) {
					location.reload(true);
				}

				if (json['error']) {
					json['error'].forEach(function(el) {
						$output.append('<div class="alert alert-danger">' + el + '</div>');
					});
				}
			}
		});

		return false;
	});
});
</script>