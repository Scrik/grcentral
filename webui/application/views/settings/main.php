<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>

<div class="card mt-2">
	<div class="card-body">
		<h1><?=lang('settings_index_head');?></h1>
		<p><?=lang('settings_index_text');?></p>
		<hr class="hr">
		<div class="row">
			<div class="col-3">
				<img src="<?=base_url('style/img/url.png');?>" width="200px" class="rounded float-left mr-4" alt="URL Image">
			</div>
			<div class="col">
				<?=lang('settings_index_urls');?>:
				<table class="table">
					<tr>
						<th>Config Server Path</th>
						<td><?=parse_url(base_url(), PHP_URL_HOST);?>/provisioning/cfg</td>
						<td>
							<? if ($modules_status['cfg_enable_get'] == 'on'):  ?>
							<span class="badge badge-success"><?=lang('settings_index_status_on');?></span>
							<? else: ?>
							<span class="badge badge-danger" data-toggle="tooltip" data-placement="top" title="<?=lang('settings_index_status_off_descr');?>"><?=lang('settings_index_status_off');?></span>
							<? endif; ?>
						</td>
					</tr>
					<tr>
						<th>Firmware Server Path</th>
						<td><?=parse_url(base_url(), PHP_URL_HOST);?>/provisioning/fw</td>
						<td>
							<? if ($modules_status['fw_enable_update'] == 'on'):  ?>
							<span class="badge badge-success"><?=lang('settings_index_status_on');?></span>
							<? else: ?>
							<span class="badge badge-danger" data-toggle="tooltip" data-placement="top" title="<?=lang('settings_index_status_off_descr');?>"><?=lang('settings_index_status_off');?></span>
							<? endif; ?>
						</td>
					</tr>
					<tr>
						<th>Phonebook XML Server Path</th>
						<td><?=parse_url(base_url(), PHP_URL_HOST);?>/provisioning/pb</td>
						<td>
							<? if ($modules_status['pb_generate_enable'] == 'on'):  ?>
							<span class="badge badge-success"><?=lang('settings_index_status_on');?></span>
							<? else: ?>
							<span class="badge badge-danger" data-toggle="tooltip" data-placement="top" title="<?=lang('settings_index_status_off_descr');?>"><?=lang('settings_index_status_off');?></span>
							<? endif; ?>
						</td>
					</tr>
				</table>
			</div>
		</div>
	</div>
</div>