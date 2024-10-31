jQuery(document).ready( function () {
  jQuery('#venue_btn').click(function(){
    jQuery('#venue_notice').empty();
    jQuery.ajax({
        type: "POST",
        url: ajaxurl,
        dataType: "json",
        data: {
            action: 'saveVenue',
			gtm: jQuery('#gtm').val(),
            venue: jQuery('#venue').val()
        },
        success: function(response){
          if (response === true) {
            jQuery('#venue_notice').append('<div class="updated notice"><strong><p>Venue has been saved</p></strong></div>');
            jQuery('#venue_notice > div').fadeOut(3000, function() {
                $(this).empty();
            });
          }
        },
        error: function(){
          jQuery('#venue_notice').append('<div class="error notice"><strong><p>Venue update failed</p></strong></div>');
        }
    });
  });
});