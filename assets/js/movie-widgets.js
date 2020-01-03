( function($) {
    $('.movie-seen-toggle').click(function(){
        var $this = $(this);
        var $parentDiv = $this.parent().parent();
        event.preventDefault();
        var classes = $.grep(this.className.split(" "), function(v, i){
            return v.indexOf('post-') === 0;
        }).join();
        var movieID = classes.slice(5);
        //user marked movie as unseen
        if($parentDiv.hasClass('seen')) { 
            $this.text('Mark as Seen');
            $parentDiv.removeClass('seen');
            $parentDiv.addClass('unseen');
            $.ajax( {
                url: ajax_url,
                type: 'POST',
                data: {
                    action      :   'update_seen_status',
                    'seen'      :   0,
                    'movieID'   :   movieID,
                }
            });
        //user marked movie as seen
        } else {
            $this.text('Mark as Unseen');
            $parentDiv.addClass('seen');
            $parentDiv.removeClass('unseen');
            $.ajax( {
                url: ajax_url,
                type: 'POST',
                data: {
                    action      :   'update_seen_status',
                    'seen'      :   1,
                    'movieID'   :   movieID,
                }
            });
        }
    });
})(jQuery);
