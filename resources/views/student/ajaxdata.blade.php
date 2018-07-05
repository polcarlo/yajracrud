<!DOCTYPE html>
<html>
<head>
        <link href="//netdna.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css" rel="stylesheet">
        <link rel="stylesheet" href="//cdn.datatables.net/1.10.7/css/jquery.dataTables.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/1.5.2/css/buttons.dataTables.min.css">
</head>
<body>
	<button type="button" name="add" id="add_data" class="btn btn-success">Add</button>
	<table id="student_table" class="table table-bordered" style="width: 100%">
		<thead>
			<tr>
				<th> First Name</th>
				<th> Last Name</th>
				<th>Action</th>
			</tr>
		</thead>
	</table>

        <div id="studentModal" class="modal fade" role="dialog">
        	<div class="modal-dialog">
        		<div class="modal-content">
        			<form method="post" id="student_form">
        				<div class="modal-header">
        					<button type="button" class="close" data-dismiss="modal">&times;</button>
        					<h4 class="modal-title">Add Data</h4>
        				</div>
        				<div class="modal-body">
        					{{csrf_field()}}
        					<span id="form_output"></span>
        					<div class="form-group">
        						<label>Enter First Name</label>
        					<input type="text" name="first_name" id="first_name" class="form-control">
        					</div>
        					<div class="form-group">
        						<label>Enter Last Name</label>
        					<input type="text" name="last_name" id="last_name" class="form-control">
        					</div>
        				</div>
        				<div class="modal-footer">
        					<input type="hidden" name="student_id" id="student_id" value="">
        					<input type="hidden" name="button_action" id="button_action" value="insert">
        					<input type="submit" name="submit" id="action" value="add" class="btn btn-info">
        					<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        				</div>
        			</form>
        		</div>
        	</div>
        </div>

        <script src="//code.jquery.com/jquery.js"></script>
        <!-- DataTables -->
        <script src="//cdn.datatables.net/1.10.7/js/jquery.dataTables.min.js"></script>
        <!-- Bootstrap JavaScript -->
        <script src="//netdna.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js"></script>

<script src="https://cdn.datatables.net/buttons/1.5.2/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.5.2/js/buttons.flash.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/1.5.2/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.5.2/js/buttons.print.min.js"></script>

<script>
	$(document).ready(function(){
		//View Database
	var table =	$('#student_table').DataTable({
			"processing": true,
			"serverSide": true,
			"ajax" : "{{ route('ajaxdata.getdata')}}",
			"columns":[
				{"data":"first_name"},
				{"data":"last_name"},
				{"data": "action", orderable:false, searchable: false}
			],
			dom: 'Bfrtip',
        	buttons: [
        		{
					extend: 'csv',
        			title: 'Copy'
        		},
        		{
					extend: 'copyHtml5',
        			title: 'Copy'
        		},
        	 	{
                	extend: 'excelHtml5',
                	title: 'Data export'
            	},
            	{
               	 	extend: 'pdfHtml5',
                	title: 'Data export'
            	},
            	'print',
            //	'copy', 'csv', 'excel', 'pdf', 'print'
        	]
		});



		//Insert Database
		$('#add_data').click(function(){
				$('#studentModal').modal('show');
				$('#student_form')[0].reset();
				$('#form_output').html('');
				$('#button_action').val('insert');
				$('#action').val('Add');
		});

		$('#student_form').on('submit', function(event){
			event.preventDefault();
			var form_data = $(this).serialize();
			$.ajax({
					url: "{{ route('ajaxdata.postdata') }}",
					method:"POST",
					data:form_data,
					dataType:"json",
					success: function(data)
					{
						if(data.error.length >0)
						{
							var error_html = '';

							for(var count = 0; count < data.error.length;count++)
							{
								error_html += '<div class="alert alert-danger">'+data.error[count]+'</div>';
							}
							$('#form_output').html(error_html);
						} else
						{
							$('#form_output').html(data.success);
							$('#student_form')[0].reset();
							$('#action').val('add');
							$('.modal-title').text('Add Data');
							$('#button_action').val('insert');
							$('#student_table').DataTable().ajax.reload();

						}
					}

			});
		});

			//Edit
			$(document).on('click', '.edit', function(){
				var id = $(this).attr("id");
				$.ajax({
					url: "{{route('ajaxdata.fetchdata')}}",
					method: 'get',
					data:{id:id},
					dataType: 'json',
					success:function(data)
					{
						$('#first_name').val(data.first_name);
						$('#last_name').val(data.last_name);
						$('#student_id').val(id);
						$('#studentModal').modal('show');
						$('#action').val('Edit');
						$('.modal-title').text('Edit Data');
						$('#button_action').val('update');

					}
				});
			});


		//Delete
		$(document).on('click', '.delete', function(){
			var id = $(this).attr('id');
			if(confirm("Are you sure?"))
			{
				$.ajax({
					url: "{{ route('ajaxdata.removedata')}}",
					method:"get",
					data:{id:id},
					success:function(data)
					{
						alert(data);
						$('#student_table').DataTable().ajax.reload();
					}
				})
			}else
			{
				return false;
			}	
		});

	});

</script>
	</body>
	</html>