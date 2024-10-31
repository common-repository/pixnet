<div class="wrap">
    <div id="venue_notice"></div>
    <div id="poststuff">
        <div id="postbox-container" class="postbox-container">
            <div class="meta-box-sortables ui-sortable" id="normal-sortables">
                <div class="postbox " id="test1">
                    <div title="Click to toggle" class="handlediv"><br></div><h3 class="hndle"><span>PA-Code-Venue</span></h3>
                    <div class="inside">
                    <p>
                        <label for="pacode">PA Code Venue</label>
                        <input id='venue' type="text" name="pavenue" placeholder="XX-XXXXXXXXXXXXX" value="<?php echo get_option('pavenue'); ?>" /><br>
                        <label for="gtmcode">GTM (AMP only)</label>
                        <input id='gtm' type="text" name="pagtm" placeholder="GTM-XXXXXXX" value="<?php echo get_option('pagtm'); ?>" /><br>
                        <div id='venue_btn' class="button button-primary">Save Modifications</div>
                   </p>
                    </div>
                </div>
        </div>
    </div>
</div>