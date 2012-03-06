jQuery(function() {
    /**
     * the list of posts
     */
    var $list = jQuery('#rp_list ul');
    /**
     * number of related posts
     */
    var elems_cnt = $list.children().length;

    /**
     * show the first set of posts.
     * 200 is the initial left margin for the list elements
     */
    load(200);

    function load(initial) {
        $list.find('li').hide().andSelf().find('div').css('margin-left', -initial + 'px');
        var loaded = 0;
        //show 5 random posts from all the ones in the list.
        //Make sure not to repeat
        while (loaded < 5) {
            var r = Math.floor(Math.random() * elems_cnt);
            var $elem = $list.find('li:nth-child(' + (r + 1) + ')');
            if ($elem.is(':visible'))
                continue;
            else
                $elem.show();
            ++loaded;
        }
        //animate them
        var d = 200;
        $list.find('li:visible div').each(function() {
            jQuery(this).stop().animate({
                'marginLeft':'-50px'
            }, d += 100);
        });
    }

    /**
     * hovering over the list elements makes them slide out
     */
    $list.find('li:visible').live('mouseenter',
        function () {
            jQuery(this).find('div').stop().animate({
                'marginLeft':'-220px'
            }, 200);
        }).live('mouseleave', function () {
            jQuery(this).find('div').stop().animate({
                'marginLeft':'-50px'
            }, 200);
        });

    /**
     * when clicking the shuffle button,
     * show 5 random posts
     */
    jQuery('#rp_shuffle').unbind('click')
        .bind('click', shuffle)
        .stop()
        .animate({'margin-left':'-18px'}, 700);

    function shuffle() {
        $list.find('li:visible div').stop().animate({
            'marginLeft':'60px'
        }, 200, function() {
            load(-60);
        });
    }
});
