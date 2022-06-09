$(document).ready(function(){
	$('#subscribeBtn').on('click', function(){
		// empty status
		$('.status').html('');
		
		var regEmail = /^[A-Z0-9._%+-]+@([A-Z0-9-]+\.)+[A-Z]{2,4}$/i;
		var regName = /^[a-zA-Z]+ [a-zA-Z]+$/;
		
		// get values
		var name = $('#name').val();
		var email = $('#email').val();
		
		// validate
		if(name.trim() == '' ){
			// if name is blank
			alert('Please enter your name.');
        	$('#name').focus();
			return false;
		}else if (!regName.test(name)){
			// if name is invalid
			alert('Please enter a valid name (first & last name).');
			$('#name').focus();
			return false;
		}else if(email.trim() == '' ){
			// if email is blank
			alert('Please enter your email.');
        	$('#email').focus();
			return false;
		}else if(email.trim() != '' && !regEmail.test(email)){
			// if email is invalid
			alert('Please enter a valid email.');
        	$('#email').focus();
			return false;
		}else{
			// if passed validation, post using ajax
			$.ajax({
				type:'POST',
				url:'subscription.php',
				dataType: "json",
				data:{subscribe:1,name:name,email:email},
				beforeSend: function () {
					$('#subscribeBtn').attr("disabled", "disabled");
					$('.content-frm').css('opacity', '.5');
				},
				success:function(data){
					if(data.status == 'ok'){
						$('#subsFrm')[0].reset();
						$('.status').html('<p class="success">'+data.msg+'</p>');
					}else{
						$('.status').html('<p class="error">'+data.msg+'</p>');
					}
					$('#subscribeBtn').removeAttr("disabled");
					$('.content-frm').css('opacity', '');
				}
			});
		}
	});
});