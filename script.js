jQuery(document).ready(function(){
            
            jQuery("a[href*='http://']:not([href*='"+window.location.hostname+"'])").addClass("outgoing").attr("target","_blank");
            
            jQuery('.outgoing').live('click',function(event){
                        
                        event.preventDefault();
                        event.stopPropagation();
                        
                        var data = {
                                    action: 'outgoing_count',
                                    link: this.href,
                                    page: window.location.pathname
                        };
                        
                        jQuery.post(ajaxurl, data, function(response) {});

                        window.open(jQuery(this).attr('href'), '_blank');      
                        
            });
});