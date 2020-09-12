
<nav class="navbar navbar-expand-lg bg-dark">
		<a href="index.php" class="navbar-brand text-white"></i>Twitter</a>

		<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#collapsibleNavbar">
			<span class="navbar-toggler-icon"></span>
		</button>

		<!--Navbar Link-->
		<div class="collapse navbar-collapse" id="collapsibleNavbar">
			<ul class="navbar-nav ml-auto">
				<li>
					<input type="text" name="search_user" id="search_user" class="form-control input-sm" placeholder="Search User" autocomplete="off" style=" width: 400px; margin-right: 180px;">
				</li>
				<li class="nav-item dropdown">
					<a class="nav-link dropdown-toggle text-white" data-toggle="dropdown" href="#" id="view_notification">
					Notification
					<?php 
						$total_notification = count_notification($connect, $_SESSION["user_id"]);
						if($total_notification > 0){
							echo '<span class="badge badge-danger" id="total_notification">'.$total_notification.'</span>';
						}
					?>
					</a>
					<ul class="dropdown-menu bg-dark text-white" style="width: 200px;">
						<?= Load_notification($connect, $_SESSION["user_id"]); ?>
					</ul>
				</li>
				<li class="nav-item dropdown">
					<a href="#" class="nav-link dropdown-toggle text-white" id="navbardrop" data-toggle="dropdown">Hi, <?= $_SESSION["username"]; ?>
					</a>
					<div class="dropdown-menu">
						<a href="profile.php" class="dropdown-item"><i class="fas fa-user"></i>&nbsp;Profile</a>
						<a href="logout.php" class="dropdown-item"><i class="fas fa-sign-out-alt"></i>&nbsp;Logout</a>
					</div>
				</li>
			</ul>
		</div>
	</nav>

	<script type="text/javascript">
		$(document).ready(function(){
			$('#search_user').typeahead({
				source:function(query, result){
					$('.typeahead').css('position','absolute');
					var action = 'search_user';
					$.ajax({
						url:'action.php',
						method: "POST",
						data:{query:query, action:action},
						dataType:"json",
						success:function(data){
							result($.map(data, function(item){
								return item;
							}));
						}
					})
				}
			});

			$(document).on('click', '.typeahead li', function(){
				var search_query = $(this).text();
				window.location.href = "wall.php?data="+search_query;
			});
		});
	</script>