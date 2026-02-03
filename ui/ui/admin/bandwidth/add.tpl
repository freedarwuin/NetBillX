{include file="sections/header.tpl"}

<div class="row">
	<div class="col-sm-6">
		<div class="panel panel-primary panel-hovered panel-stacked mb30">
			<div class="panel-heading">{Lang::T('Add New Bandwidth')}</div>
			<div class="panel-body">
				<form class="form-horizontal" method="post" role="form" action="{Text::url('bandwidth/add-post')}">
					<div class="form-group">
						<label class="col-md-3 control-label">{Lang::T('Bandwidth Name')}</label>
						<div class="col-md-9">
							<input type="text" class="form-control" id="name" name="name">
						</div>
					</div>
					<div class="form-group">
						<label class="col-md-3 control-label">{Lang::T('Rate Download')}</label>
						<div class="col-md-6">
							<input type="text" class="form-control" id="rate_down" name="rate_down">
						</div>
						<div class="col-md-3">
							<select class="form-control" id="rate_down_unit" name="rate_down_unit">
								<option value="Kbps">Kbps</option>
								<option value="Mbps">Mbps</option>
							</select>
						</div>
					</div>
					<div class="form-group">
						<label class="col-md-3 control-label">{Lang::T('Rate Upload')}</label>
						<div class="col-md-6">
							<input type="text" class="form-control" id="rate_up" name="rate_up">
						</div>
						<div class="col-md-3">
							<select class="form-control" id="rate_up_unit" name="rate_up_unit">
								<option value="Kbps">Kbps</option>
								<option value="Mbps">Mbps</option>
							</select>
						</div>
					</div>
					<div class="panel-heading">{Lang::T('Optional')}</div>
					<div class="form-group">
						<label class="col-md-3 control-label">Burst Limit</label>
						<div class="col-md-9">
							<input type="text" class="form-control" id="burst_limit" name="burst[]" placeholder="[Burst/Limit]">
						</div>
					</div>
					<div class="form-group">
						<label class="col-md-3 control-label">Burst Threshold</label>
						<div class="col-md-9">
							<input type="text" class="form-control" id="burst_threshold" name="burst[]" placeholder="[Burst/Threshold]">
						</div>
					</div>
					<div class="form-group">
						<label class="col-md-3 control-label">Burst Time</label>
						<div class="col-md-9">
							<input type="text" class="form-control" id="burst_time" name="burst[]" placeholder="[Burst/Time]">
						</div>
					</div>
					<div class="form-group">
						<label class="col-md-3 control-label">Priority</label>
						<div class="col-md-9">
							<input type="number" class="form-control" id="burst_priority" name="burst[]" placeholder="[Priority]">
						</div>
					</div>
					<div class="form-group">
						<label class="col-md-3 control-label">Limit At</label>
						<div class="col-md-9">
							<input type="text" class="form-control" id="burst_limit_at" name="burst[]" placeholder="[Limit/At]">
						</div>
					</div>
					<div class="form-group">
						<div class="col-lg-offset-2 col-lg-10">
							<button class="btn btn-primary" onclick="return ask(this, '{Lang::T("Continue the Bandwidth addition process?")}')" type="submit">{Lang::T('Save')}</button>
							Or <a href="{Text::url('bandwidth/list')}">{Lang::T('Cancel')}</a>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
	<div class="col-sm-4">
	<div class="panel panel-default">
    	<div class="panel-heading">{Lang::T('Burst Limit Preset')}</div>
    	<div class="list-group">

    		<a href="#" class="list-group-item active">Planes actuales</a>

    		<!-- 10 / 40 / 80 -->
    		<a href="javascript:burstIt('10M/10M 20M/20M 15M/15M 16/16 8 5M/5M')" class="list-group-item">
    			10M to 20M
    		</a>

    		<a href="javascript:burstIt('40M/40M 80M/80M 60M/60M 16/16 8 20M/20M')" class="list-group-item">
    			40M to 80M
    		</a>

    		<a href="javascript:burstIt('80M/80M 160M/160M 120M/120M 16/16 8 40M/40M')" class="list-group-item">
    			80M to 160M
    		</a>

    		<a href="#" class="list-group-item active">Planes alta velocidad</a>

    		<!-- 100 - 300 -->
    		<a href="javascript:burstIt('100M/100M 200M/200M 150M/150M 16/16 8 50M/50M')" class="list-group-item">
    			100M to 200M
    		</a>

    		<a href="javascript:burstIt('150M/150M 300M/300M 225M/225M 16/16 8 75M/75M')" class="list-group-item">
    			150M to 300M
    		</a>

    		<a href="javascript:burstIt('200M/200M 400M/400M 300M/300M 16/16 8 100M/100M')" class="list-group-item">
    			200M to 400M
    		</a>

    		<a href="javascript:burstIt('250M/250M 500M/500M 375M/375M 16/16 8 125M/125M')" class="list-group-item">
    			250M to 500M
    		</a>

    		<a href="javascript:burstIt('300M/300M 600M/600M 450M/450M 16/16 8 150M/150M')" class="list-group-item">
    			300M to 600M
    		</a>

    	</div>
    </div>
</div>
</div>

<script>
function burstIt(value) {
	var b = value.split(" ");
	$("#burst_limit").val(b[1]);
	$("#burst_threshold").val(b[2]);
	$("#burst_time").val(b[3]);
	$("#burst_priority").val(b[4]);
	$("#burst_limit_at").val(b[5]);
	var a = b[0].split("/");
	$("#rate_down").val(a[0].replace('M',''));
	$("#rate_up").val(a[1].replace('M',''));
	$('#rate_down_unit').val('Mbps');
	$('#rate_up_unit').val('Mbps');
	window.scrollTo(0, 0);
}
</script>

{include file="sections/footer.tpl"}
