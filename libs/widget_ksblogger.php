<?php
class WP_Widget_Ksblogger extends WP_Widget {

    function __construct($user_name='')
    {
        $widget_ops = [
            'classname' => 'side_ksblogger',
            'description' => '高雄山城貼紙放置區塊',
            'customize_selective_refresh' => true
        ];
        $control_ops = [
            'width' => 300,
            'height' => 350,
            'id_base' => 'side_ksblogger_widget'
        ];
        parent::__construct('side_ksblogger_widget', '痞客邦高雄山城貼紙', $widget_ops, $control_ops);
        $this->user_name = $user_name;
    }

    function widget($args, $instance)
    {
        $is_amp = (function_exists('is_amp_endpoint') and is_amp_endpoint());
        if ($is_amp) {
            $this->widget_amp($args, $instance);
        } else {
            $this->widget_html($args, $instance);
        }
    }

    function widget_amp($args, $instance)
    {
        extract($args);
        echo $before_widget;
        echo '
            <div style="text-align: center;">
                <iframe src="//sticker.events.pixnet.net/ksblogger/?user=' . $this->user_name . '" frameborder="0" scrolling="no" width="180" height="300"></iframe>
            </div>
        ';
        echo $after_widget;
    }

    function widget_html($args, $instance)
    {
        extract($args);
        echo $before_widget;
        echo '
            <div style="text-align: center;">
                <iframe src="//sticker.events.pixnet.net/ksblogger/?user=' . $this->user_name . '" frameborder="0" scrolling="no" width="180" height="300"></iframe>
            </div>
        ';
        echo $after_widget;
    }

    function update($new_instance, $old_instance)
    {

    }

    function form($instance)
    {

    }
}
