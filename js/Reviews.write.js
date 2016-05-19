// Reviews.write.js

;$(function(){

	$("#id_listening_last_group").on("click.js", function(){
		var is_recently = $("#id_listening_last_recently").prop('checked');
		if ( is_recently ) {
			$("#id_listening_system_group").hide("fast");
		} else {
			$("#id_listening_system_group").show("fast");
		}
	});

	var body_max_length = 1000;
	var $body = $("#id_body");
	var $review_counter = $("#id_review_counter");
	var getReviewColor = function(counter){
		if (counter < 10) { return "#F33";}
		if (counter < 50) { return "#900";}
		return "";
	};
	var counter = body_max_length - $body.val().length;
	$review_counter.text(counter).css({"color":getReviewColor(counter)});
	$body.on("keyup.js", function(){
		var counter = body_max_length - $(this).val().length;
		$review_counter.text(counter).css({"color":getReviewColor(counter)});
	});
});