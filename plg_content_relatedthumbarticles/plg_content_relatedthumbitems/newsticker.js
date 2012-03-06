var Ticker = new Class({
    setOptions: function(options) {
        this.options = Object.extend({
            speed: 5000,
            delay: 5000,
            direction: 'vertical',
            onComplete: Class.empty,
            onStart: Class.empty
        }, options || {});
    },
    initialize: function(el, options) {
        this.setOptions(options);
        this.el = $(el);
        this.items = this.el.getElements('li');
        var w = 0;
        var h = 0;
        if (this.options.direction.toLowerCase() == 'horizontal') {
            h = this.el.getSize().size.y;
            this.items.each(function(li, index) {
                w += li.getSize().size.x;
            });
        } else {
            w = this.el.getSize().size.x;
            this.items.each(function(li, index) {
                h += li.getSize().size.y;
            });
        }
        this.el.setStyles({
            position: 'absolute',
            top: 0,
            left: 0,
            width: w,
            height: h
        });
        this.fx = new Fx.Styles(this.el, {duration:this.options.speed,onComplete:function() {
            var i = (this.current == 0) ? this.items.length : this.current;
            this.items[i - 1].injectInside(this.el);
            this.el.setStyles({
                left:0,
                top:0
            });
        }.bind(this)});
        this.current = 0;
        this.next();
    },
    next: function() {
        this.current++;
        if (this.current >= this.items.length) this.current = 0;
        var pos = this.items[this.current];
        this.fx.start({
            top: -pos.offsetTop,
            left: -pos.offsetLeft
        });
        this.next.bind(this).delay(this.options.delay + this.options.speed);
    }
});