jQuery(document).ready( function () {
  var siteUrl = object_name.siteUrl + '/',
      league_class = 'desktop_league',
      ranking_class = 'no-display',
      is_onrank = object_name.is_onrank,
      is_mobile = (0 == jQuery("#sidebar").length && 0 == jQuery("#secondary").length && object_name.is_mobile) ? true : false,
      stickerUrl_league = object_name.stickerUrl + '/league_',
      stickerUrl_ranking = object_name.stickerUrl + '/ranking_',
      league_link = 'https://pixranking.events.pixnet.net/personal?utm_source=personal-medium&utm_medium=sticker&utm_campaign=achang',
      ranking_link = 'https://pixranking.events.pixnet.net/personalrank?utm_source=personal-medium&utm_medium=stickerrank&utm_campaign=achang'
  ;

  if (is_mobile) {
      league_class = 'mobile_league';
      mobile_div = '<aside class="widget-area"><div id="sticker" class="sticker"></div></aside>';
      league_elements = '<a target="_blank" rel="noopener" href="' + league_link + '"><img alt="社群影響力貼紙" id="pixnet_league" class="' + league_class + '" src="' + stickerUrl_league + 'mobile.jpg"></a>';
      ranking_elements = '<a target="_blank" rel="noopener" href="' + ranking_link + '"><img alt="社群金點賞貼紙" id="pixnet_rank" class="no-display" src="' + stickerUrl_ranking + 'mobile.jpg"></a>';

      jQuery('#main').append(mobile_div);
      jQuery('#sticker').append(league_elements);
      jQuery('#sticker').append(ranking_elements);
  }

  if (is_onrank) {
      league_class = league_class + '_fixed';
      ranking_class = league_class.split('_')[0] + '_rank';
  }

  jQuery('#pixnet_league').attr('class', league_class);
  jQuery('#pixnet_rank').attr('class', ranking_class);

});
