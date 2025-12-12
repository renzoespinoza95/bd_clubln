	$(document).ready(function() {
		//Tabs
		$(".tab_container").each(function() {
			//$('<pre class="prettyprint"></pre>').text( $.trim( $(this).children().html() ) ).hide().appendTo( $(this) );
			$('<pre class="prettyprint"></pre>').text( $.trim( $(this).next().html() ) ).hide().appendTo( $(this) );
		});

		$(".tabs a").click(function() {
			$(this).parent().siblings().removeClass('active').end().addClass('active');
			
			$(this).parents('ul').next().children().hide().eq( $(this).parent().index() ).show();
		});

		// pretyPrint
		prettyPrint();

		//Basic contact form validation
		$("#c-form").submit(function() {
			var ok = true;
			
			$.each(['c-name', 'c-mail', 'c-msg'], function(i, el) {
				if ($("#" + el).val().length < 3) {
					ok = false;
				}
			});

			return ok;
		});

		// Add new paragraph (various - inline example)
		$("#add_paragraph").click(function() {
			$(this).parent().next().clone().appendTo( $(this).parent() );
			$.fancybox.update();
		});
		
	});

	// Google +1
	(function() {
		var po = document.createElement('script'); po.type = 'text/javascript'; po.async = true;
		po.src = 'https://apis.google.com/js/plusone.js';
		var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(po, s);
	})();