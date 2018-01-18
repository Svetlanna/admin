@extends('adminlte::page')

@section('title', 'AdminLTE')

@section('content_header')
    <h1>Dashboard</h1>
@stop

@section('content')
    <p>You are logged in!</p>
    <!DOCTYPE html>
<html>
<head>
  <meta name="_token" content="{!! csrf_token() !!}">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
  <script src="//ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>

  <style>
  .modal-header, h4, .close {
      background-color: #5cb85c;
      color:white !important;
      text-align: center;
      font-size: 30px;
  }td{width:16%;}
  .modal-footer {
      background-color: #f9f9f9;
  }
  </style>
</head>
<body>

<div class="container">
  <!-- Trigger the modal with a button -->
  <button type="button" class="btn btn-default btn-lg" id="myBtn">Add</button>

  <!-- Modal -->


@include('newpeople')

  <table class="table">
  <thead>
    <tr>
      <th scope="col">#</th>
      <th scope="col">First</th>
      <th scope="col">Last</th>
      <th scope="col">gender</th>
      <th scope="col">email</th>
      <th scope="col">phone</th>
    </tr>
  </thead>
  <tbody>


    @foreach($peoples as $key=>$people)
      <tr id="people{!!$people->id!!}">
      <th scope="row">{!!$people->id!!}</th>
      <td>{!!$people->lname!!}</td>
      <td>{!!$people->fname!!}</td>
      <td>
      @if($people->sex==0)
      Male
      @else
      Female
      @endif
      </td>
      <td>{!!$people->email!!}</td>
      <td>{!!$people->phone!!}</td>
      <td><button type="submit" class="btn btn-success btn-edit" data-dismiss="modal" data-id="{{$people->id}}"><span class="glyphicon glyphicon-edit"></span> </button><button type="submit" data-id="{{$people->id}}" class="btn btn-danger btn-remove" data-dismiss="modal"><span class="glyphicon glyphicon-remove" ></span></button></td>
      

    </tr>

      @endforeach
 

  </tbody>
</table>
</div>
 
<script>
$(document).ready(function(){



$.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                }
            });


    $("#myBtn").click(function(){
    	$('#save').val('save');
    	 $("#frmpeople").trigger('reset');
        $("#myModal").modal();
    }); 

 /*   $(".btn-edit").click(function(){
        alert($(this).attr("data-id"));
    }); 
*/

     $("#frmpeople").on('submit',function(e){
     	e.preventDefault();
     	var form=$('#frmpeople');
     	var formdata=form.serialize();
     	var url=form.attr('action');
     	var state=$('#save').val();
     	var type='post';
     	if(state=='update'){
     		type='put';

     	}
     	     			//console.log(formdata);

            $.ajax({
				 async: true,   // this will solve the problem
				 type: "post",
				url: url,
                data :formdata,
                async: true,
               // dataType: 'post',
                success: function(data){
var sex='';
if(data.sex==0){
sax='Male';

}else{
	'Female';
}


var row='<tr id="people">'+
	'<td>'+data.id+'</td>'+
	'<td>'+data.fname+'</td>'+
	'<td>'+data.lname+'</td>'+
	'<td>'+data.sex+'</td>'+
	'<td>'+data.email+'</td>'+
	'<td>'+data.phone+'</td>'+
	'<td><button type="submit" class="btn btn-success btn-edit" data-dismiss="modal" data-id="'+data.id+'"><span class="glyphicon glyphicon-edit"></span></button><button type="submit"class="btn btn-danger btn-remove" data-dismiss="modal" data-id="'+data.id+'"><span class="glyphicon glyphicon-remove"></span></button</td>'+

	'</tr>';
	if(state=='save'){
		$('tbody').append(row);
	}else{
		$('#people'+data.id).replaceWith(row);
	}
$('#frmpeople').trigger('reset');
$('#fname').focus();

     		}
     	})
    });


	$('tbody').delegate('.btn-edit','click',function(){
		var value=$(this).data('id');
		var url="{{URL::to('getUpdate')}}";//console.log(value);
		    $.ajax({
				// async: true,   // this will solve the problem
				type: "GET",
				url: url,
				data: {'id': value},
                success: function(data){
     			console.log(data);
     			$('#id').val(data.id);
     			$('#fname').val(data.fname);
     			$('#lname').val(data.lname);
     			$('#sex').val(data.sex);
     			$('#email').val(data.email);
     			$('#phone').val(data.phone);
     			$('#password').val(data.password);
     			$('#save').val('update');
     			 $("#myModal").modal();

     		}
     	})
	})






$('tbody').delegate('.btn-remove','click',function(){
			var value=$(this).data('id');
		console.log(value);
		var url="{{URL::to('deletePeople')}}";
	if(confirm('Are you sure to delete')==true){

		    $.ajax({
				 //async: true, 
				type : "post",
				url : url,
				data: {'id': value},
                success: function(data){
                	//console.log(data);
	//alert(data.sms);
      	     			$('#people'+value).remove();

     		}
     	})
		}
	})





/*	$('tbody').delegate('.btn-remove','click',function(){
						var value=$(this).data('id');
						console.log(value);
		var url="{{URL::to('deletePeople')}}";
		  if(confirm('Are you sure to delete')==true){
		    $.ajax({
				 async: true,   // this will solve the problem
				type: "put",
				url: url,
				data: {'id': value},
                success: function(data){alert(data.sms);
     			$('#people'+value).remove();


     		}
     	
     	})
}

	/*var value=$(this).data('id');
	console.log(value);
		var url="{{URL::to('deletepeople')}}";
		if(confirm('Are you sure to delete')==true){
			console.log(value);
			$.ajax({
				async: true,   // this will solve the problem

				type: "put",
				url: url,
				data: {'id': value},
                success: function(data){
      	     			alert(data.sms);
      	     			$('#people'+value).remove();

                }


            })
		}

})*/
});
</script>

</body>
</html>

@stop