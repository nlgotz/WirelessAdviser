{% extends "base.tpl" %}
{% block container %} 
<div class="row-fluid">
	<div class="span12">
		<script type="text/javascript">
		$(document).ready(function() {
			$('#inventory').dataTable( {
				"bProcessing": true,
				"bServerSide": true,
				"iDisplayLength": 25,
				//"bFilter": false,
				"sAjaxSource": "{{name.site}}api.php?q={{name.t}}",
				"sPaginationType": "bootstrap",
				//"sDom": "<'row'<'span6'l><'span6'f>r>t<'row'<'span6'i><'span6'p>>"
				"sDom": '<"top"fp>rt<"bottom"lip><"clear">',
				"sWrapper": "dataTables_wrapper form-inline",
				"aaSorting": [[ 0, "desc" ]]
			} );
		} );
		</script>
		<table cellpadding="0" cellspacing="0" border="0" class="table table-striped table-bordered" id="inventory">
			<thead>
				<tr>
					<th>Time</th>
					<th>Device ID</th>
					<th>Device</th>
					<th>Type</th>
					<th>Severity</th>
					<th>Description</th>
				</tr>
			</thead>
			<tbody>
			</tbody>
		</table>				
	</div><!--/span-->
</div><!--/row-->

{% endblock %}