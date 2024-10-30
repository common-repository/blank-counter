jQuery(function(){

            // Tabs
            jQuery('#blankcounter-tabs').tabs();

            //hover states on the static widgets
            jQuery('#dialog_link, ul#icons li').hover(
                        function() {
                                    jQuery(this).addClass('ui-state-hover');
                        },
                        function() {
                                    jQuery(this).removeClass('ui-state-hover');
                        }
                        );

});