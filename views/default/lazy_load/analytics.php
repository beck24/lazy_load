
<script>
	$(document).ready( function() {
		$("img.lazy-load").show().lazyload({
			threshold : 200,
			effect : "fadeIn",
			skip_invisible : false,
			failure_limit: 10
		});
	});
</script>