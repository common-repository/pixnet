<?php
class WP_Widget_Sticker extends WP_Widget {

    function __construct($is_onrank = false)
    {
        $this->is_onrank = $is_onrank;
        $widget_ops = array('classname' => 'side_sticker', 'description' => '痞客邦個人媒體聯盟貼紙放置區塊', 'customize_selective_refresh' => true,);
        $control_ops = array('width' => 300, 'height' => 350, 'id_base' => 'side_sticker_widget');
        parent::__construct('side_sticker_widget', '痞客邦個人媒體聯盟貼紙', $widget_ops, $control_ops);
    }

    function widget($args, $instance)
    {
        $is_amp = (function_exists('is_amp_endpoint') and is_amp_endpoint());
        $width = $height = 336;
        if ($this->is_onrank) {
            $width = $height = 168;
        }

        $league_link = 'https://pixranking.events.pixnet.net/personal?utm_source=personal-medium&utm_medium=sticker&utm_campaign=achang';
        $ranking_link = 'https://pixranking.events.pixnet.net/personalrank?utm_source=personal-medium&utm_medium=stickerrank&utm_campaign=achang';
        $stickerUrl_league = plugins_url('/resource/img', __DIR__ ) . '/league_mobile.jpg';
        $stickerUrl_ranking = plugins_url('/resource/img', __DIR__ ) . '/ranking_mobile.jpg';

        extract($args);
        echo $before_widget;
		if ($is_amp) {
        	echo "
            <div id='sticker' class='sticker'>
            <a target='_blank' href='$league_link'><amp-img width='$width' height='$height' layout='intrinsic' id='pixnet_league' class='no-display' src='$stickerUrl_league' alt='個人媒體聯盟貼紙'></a>";
                    if ($this->is_onrank) {
                        echo "
            <a target='_blank' href='$ranking_link'><amp-img width='$width' height='$height' layout='intrinsic' id='pixnet_rank' class='no-display' src='$stickerUrl_ranking' alt='社群影響力貼紙'></a>
            </div>
                   ";
                    }
		} else {
        	echo "
            <div id='sticker' class='sticker'>
            <a target='_blank' href='$league_link' rel='noopener'><img id='pixnet_league' class='no-display' src='$stickerUrl_league' alt='個人媒體聯盟貼紙' loading='false'></a>
            <a target='_blank' href='$ranking_link' rel='noopener'><img id='pixnet_rank' class='no-display' src='$stickerUrl_ranking' alt='社群影響力貼紙' loading='false'></a>
            </div>
        	";
		}
        echo $after_widget;
    }

    function update($new_instance, $old_instance)
    {

    }

    function form($instance)
    {

    }
}
