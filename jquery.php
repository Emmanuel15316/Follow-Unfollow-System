<script type="text/javascript">
$(document).ready(function(){
	$("#title_button").on('click', function(e){
		e.preventDefault();
		$("#post_title").show();

	});

	$('#fileBtn').on('click', function(e){
		e.preventDefault();
		$('#uploadFile').show();
	});

	$("#post_form").submit(function(e){
		e.preventDefault();

		$.ajax({
			url:"action.php",
			method: "post",
			processData: false,
			contentType: false,
			cache: false,
			data: new FormData(this),
			success:function(data){
				alert(data);
				$("#post_form")[0].reset();
				fetch_post();
			}
		});
	});	

	fetch_post();

	function fetch_post(){
		var action = 'fetch_post';
		$.ajax({
			url: "action.php",
			method: "post",
			data:{action:action},
			success:function(response){
				$("#post_list").html(response);
			}
		});
	}

	fetch_user();
	function fetch_user(){
		var action = 'fetch_user';
		$.ajax({
			url: "action.php",
			method:"POST",
			data:{action:action},
			success:function(response){
				$("#user_list").html(response);
			}
		});
	}	

	$(document).on('click','.action_button', function(){
		var sender_id = $(this).data('sender_id');
		var action = $(this).data('action');
		$.ajax({
			url: "action.php",
			method: "POST",
			data:{sender_id:sender_id, action:action},
			success:function(response){
				fetch_user();
				fetch_post();
			}
		});
	});

	var post_id;
	var user_id;

	$(document).on('click', '.post_comment', function(){
		post_id = $(this).attr('id');
		user_id = $(this).attr('user_id');
		var action = 'fetch_comment';
		$.ajax({
			url: "action.php",
			method:"POST",
			data:{post_id:post_id, user_id:user_id, action:action},
			success:function(response){
				$("#old_comment"+post_id).html(response);
			}
		});
		$("#comment_form"+post_id).slideToggle('slow');
	});

	$(document).on('click', '.submit_comment', function(){
		var comment = $('#comment'+post_id).val();
		var action = 'submit_comment';
		var receiver_id = user_id;
		if(comment != ''){
			$.ajax({
				url: "action.php",
				method: "POST",
				data:{post_id:post_id, receiver_id:receiver_id, comment:comment, action:action},
				success:function(response){
					$('#comment_form'+post_id).slideUp('slow');
					fetch_post();
				}
			});
		}
	});

	$(document).on('click', '.repost', function(){
		var post_id = $(this).data('post_id');
		var action = 'repost';

		$.ajax({
			url: "action.php",
			method: "POST",
			data:{post_id:post_id, action, action},
			success:function(data){
				alert(data);
				fetch_post();
			}
		});
	});

	//Like Button Ajax Request
	$(document).on('click', '.like_button', function(){
		var post_id = $(this).data('post_id');
		var action = 'like';
		$.ajax({
			url: "action.php",
			method:"POST",
			data:{post_id:post_id, action:action},
			success:function(response){
				alert(response);
				fetch_post();
			}
		});
	});

	$('body').tooltip({
		selector:'.like_button',
		title: fetch_post_like_user_list,
		html: true,
		placement: 'right'
	});

	function fetch_post_like_user_list(){
		var fetch_data = '';
		var element = $(this);
		var post_id = element.data('post_id');
		var action = 'like_user_list';
		$.ajax({
			url: "action.php",
			method:"POST",
			data:{post_id:post_id, action:action},
			success:function(response){
				fetch_data = response;
			}
		});
		return fetch_data;
	}

	$('#view_notification').click(function(e){
		  var action = 'update_notification_status';
		  $.ajax({
		  	url:"action.php",
		  	method:"POST",
		  	data:{action:action},
		  	success:function(data){
		  		$('total_notification').remove();
		  	}
		  });
	});
});
</script>