/* vim: set expandtab sw=4 ts=4 sts=4: */
/**
 * function used in or for navigation frame
 *
 * @package phpMyAdmin-Navigation
 */

/**
 * opens/closes (hides/shows) tree elements
 * loads data via ajax, if possible
 */
$(document).ready(function() {
	$('#navigation_tree a.expander').live('click', function(event) {
        if ($(this).hasClass('ajax') || $('#navigation_tree').hasClass('light') != true) {
            event.preventDefault();
	        event.stopImmediatePropagation();
            var $this = $(this);
	        var $children = $this.closest('li').children('div.list_container');
            var $icon = $this.parent().find('img');
            if ($this.hasClass('loaded')) {
		        if ($icon.is('.ic_b_plus')) {
			        $icon.removeClass('ic_b_plus').addClass('ic_b_minus');
			        $children.show('fast');
		        } else {
			        $icon.removeClass('ic_b_minus').addClass('ic_b_plus');
			        $children.hide('fast');
		        }
            } else {
                var $destination = $this.closest('li');
                var $throbber = $('.throbber').first().clone().show();
                $icon.hide();
                $throbber.insertBefore($icon);
                $.get($this.attr('href'), {'ajax_request': true, 'getTree': true}, function (data) {
                    if (data.success === true) {
                        $this.addClass('loaded');
                        $destination.append(data.message);
		                $icon.removeClass('ic_b_plus').addClass('ic_b_minus');
		                $destination.children('div.list_container').show('fast');
                        if ($destination.find('ul > li').length == 1) {
                            $destination.find('ul > li').find('a.expander.container').click();
                        }
                    }
                    $icon.show();
                    $throbber.remove();
                });
            }
        }
        $(this).blur();
	});
});

/**
 * @var Object PMA_resizeHandler Handles the resizing of the navigation
 *                               frame by storing the width value in a
 *                               cookie and by propagating the changes
 *                               in width across multiple browser windows.
 */
var PMA_resizeHandler = {
    /**
     * @var object left A jQuery object containing a reference to
     *                  the 'collapse' buttons in the left frame
     */
    left: null,
    /**
     * @var object right A jQuery object containing a reference to
     *                   the 'collapse' buttons in the right frame
     */
    right: null,
    /**
     * @var bool collapsed Stores the state of the navigation frame
     */
    collapsed: false,
    /**
     * @var int collapse_padding Indicates by how many pixels the #serverinfo
     *                           element was shifted right when the navigation
     *                           frame was collapsed
     */
    collapse_padding: 0,
    /**
     * @var object width Stores the widths of the navigation frame:
     *                   the actual width, as defined in the cookie
     *                   and an old recorded value
     */
    width: undefined,
    /**
     * @var string An html snippet with buttons for collapsing the frame
     */
    html: "<div id='collapse_frame' style='display:none;'>"
        + "<div class='collapse_top'><div>%s</div></div>"
        + "<div class='collapse_bottom'><div>%s</div></div>"
        + "</div>",
    /**
     * Initializes this Objects
     *
     * @return nothing
     */
    init: function () {
        this.cookie_name = 'navigation_frame_width';
        if (window.parent.frames[0] != undefined && window.parent.frames[1] != undefined) { // if we have two frames
            this.attach(true);
        }
        if (this.left && this.right && (this.left.length + this.right.length == 2)) {
            this.left.show();
            // ready
            this.main();
            setInterval('PMA_resizeHandler.main()', 600);
        } else {
            setTimeout('PMA_resizeHandler.init()', 300);
            return false;
        }
    },
    attach: function (init) {
        if (parent.parent.text_dir == 'ltr') {
            var $f0 = $(window.parent.frames[0].document);
            var $f1 = $(window.parent.frames[1].document);
        } else {
            var $f0 = $(window.parent.frames[1].document);
            var $f1 = $(window.parent.frames[0].document);
        }
        if (init) {
            this.bind($f0, '«', this.html);
            this.left = $f0.find('div#collapse_frame');
            this.bind($f1, '»', this.html);
            this.right = $f1.find('div#collapse_frame');
        } else {
            if ($f1.find('div#collapse_frame').length == 0) {
                this.bind($f1, '»', this.html);
                this.right = $f1.find('div#collapse_frame');
            }
        }
    },
    /**
     * Attaches the 'collapse' buttons to both the navigation and the content frames
     */
    bind: function ($obj, label, html) {
        $obj.find('div#collapse_frame').remove();
        $obj.find('body').append(html.replace(/%s/g, label));
        $obj.find('div#collapse_frame > div').each(function () {
            $(this).click(function () {
                PMA_resizeHandler.collapse();
            });
        });
    },
    /**
     * Main function, it is called at regular interval
     *
     * @return nothing
     */
    main: function () {
        this.attach(false);
        if (this.width == undefined) {
            // If it's the first time that this function has ever been called
            this.setWidth(PMA_cookie.get(this.cookie_name), false, false);
            if (this.width.cookie != null && this.width.cookie && parent.document != document) {
                this.setWidth(this.width.cookie, true, false);
            } else {
                var width = this.getWidth();
                this.setWidth(width, false, true);
            }
        } else if (! this.collapsed) {
            // If it's any other subsequent time
            // and the navigation frame is not collapsed
            this.width.actual = this.getWidth();
            this.width.cookie = PMA_cookie.get(this.cookie_name);
            if (this.width.actual != this.width.previous) {
                this.setWidth(this.width.actual, false, true);
            }
        } else {
            var width = this.getWidth();
            if (width > 1) {
                // The frame width is more than 1 pixel, put it's state
                // is 'collapsed'. Change state to 'not collapsed'.
                this.collapsed = false;
                // show/hide 'collapse' buttons as necessary
                this.left.show();
                this.right.hide();
            } else if (! this.right.is(':visible')) {
                this.right.show();
            }
        }
    },
    /**
     * Returns the width of the navigation frame in pixels
     *
     * @return int Navigation frame width
     */
    getWidth: function () {
        // Use a default value, in case we
        // can't figure out the width
        var width = 200;
        if (parent.parent.document != document) {
            // Get the correct window object,
            // based on text direction
            if (parent.parent.text_dir == 'ltr') {
                var w = window.parent.frames[0].window;
            } else {
                var w = window.parent.frames[1].window;
            }
            // Try to find out the width of the navigation frame
            if (window.parent.opera) {
                if (w.innerWidth && parseInt(w.innerWidth) === w.innerWidth) {
                    width = w.innerWidth;
                } else if (window.parent.document.getElementById('frame_navigation').offsetWidth
                    && parseInt(window.parent.document.getElementById('frame_navigation').offsetWidth)
                    === window.parent.document.getElementById('frame_navigation').offsetWidth
                ) {
                    width = window.parent.document.getElementById('frame_navigation').offsetWidth;
                }
            } else {
                if (window.parent.document.getElementById('frame_navigation').offsetWidth
                    && parseInt(window.parent.document.getElementById('frame_navigation').offsetWidth)
                    === window.parent.document.getElementById('frame_navigation').offsetWidth
                ) {
                    width = window.parent.document.getElementById('frame_navigation').offsetWidth;
                } else if (w.innerWidth && parseInt(w.innerWidth) === w.innerWidth) {
                    width = w.innerWidth;
                }
            }
        }
        return width;
    },
    /**
     * Saves the width of the frame to memory and optionally
     * can also save this value in a cookie or set the width
     * of the navigation frame to it.
     *
     * @param int           width      The width of the navigation frame in pixels
     * @param bool optional set_frame  true to set the width of the navigation
     *                                 frame to the supplied value
     * @param bool optional set_cookie true to save the width to a cookie
     *
     * @return nothing
     */
    setWidth: function (width, set_frame, set_cookie) {
        // Save the width to memory first
        this.width = {
            actual: width,
            previous: width,
            cookie: width
        };
        if (set_frame) {
            if (parent.parent.document != document) { // Set the frame width only if possible
                if (parent.parent.text_dir == 'ltr') {
                    parent.parent.document.getElementById('mainFrameset').cols = width + ',*';
                } else {
                    parent.parent.document.getElementById('mainFrameset').cols = '*,' + width;
                }
            }
        }
        if (set_cookie) {
            PMA_cookie.set(this.cookie_name, width);
        }
    },
    /**
     * Toggles the state of the navigation frame
     */
    collapse: function () {
        if (! this.collapsed) {
            this.setWidth(1, true);
            this.collapsed = true;
            // show/hide 'collapse' buttons as necessary
            this.left.hide();
            this.right.show();
            var $s_info = $(parent.frame_content.document.getElementById('serverinfo'));
            this.collapse_padding = $s_info.css('padding-left');
            $s_info.css('padding-left', '2.2em');
        } else {
            this.setWidth(PMA_cookie.get(this.cookie_name), true);
            this.collapsed = false;
            // show/hide 'collapse' buttons as necessary
            this.left.show();
            this.right.hide();
            $(parent.frame_content.document.getElementById('serverinfo'))
                .css('padding-left', this.collapse_padding);
        }
    }
};

/**
 * Save and retreive key/value pairs to/from a cookie
 */
var PMA_cookie = {
    /**
     * retrieves a named value from cookie
     *
     * @param   string  name    name of the value to retrieve
     * @return  string  value   value for the given name from cookie
     */
    get: function (name) {
        var start = document.cookie.indexOf(name + "=");
        var len = start + name.length + 1;
        if ((!start) && (name != document.cookie.substring(0, name.length))) {
            return null;
        }
        if (start == -1) {
            return null;
        }
        var end = document.cookie.indexOf(";", len);
        if (end == -1) {
            end = document.cookie.length;
        }
        return unescape(document.cookie.substring(len,end));
    },
    /**
     * stores a named value into cookie
     *
     * @param   string  name    name of value
     * @param   string  value   value to be stored
     * @param   date    expires expire time
     * @param   string  path
     * @param   string  domain
     * @param   bool    secure
     */
    set: function (name, value, expires, path, domain, secure) {
        document.cookie = name + "=" + escape(value) +
            ( (expires) ? ";expires=" + expires.toGMTString() : "") +
            ( (path)    ? ";path=" + path : "") +
            ( (domain)  ? ";domain=" + domain : "") +
            ( (secure)  ? ";secure" : "");
    }
};

/**
 * Reloads the recent tables list.
 */
function PMA_reloadRecentTable() {
    $.get(
        'navigation.php',
        {
            'token':        window.parent.token,
            'server':       window.parent.server,
            'ajax_request': true,
            'recent_table': true
        },
        function (data) {
            if (data.success == true) {
                $('#recentTable').html(data.options);
            }
        }
    );
}

// Performed on load
$(document).ready(function(){
    // Frame resize handler
    PMA_resizeHandler.init();

    // Node highlighting
	$('#navigation_tree.highlight li:not(.fast_filter)').live('mouseover', function () {
        if ($('li:visible', this).length == 0) {
            $(this).css('background', '#ddd');
        }
    });
	$('#navigation_tree.highlight li:not(.fast_filter)').live('mouseout', function () {
        $(this).css('background', '');
    });

    // Bind "clear fast filter"
    $('li.fast_filter > span').live('click', function () {
        // Clear the input and apply the fast filter with empty input
        var value = $(this).prev()[0].defaultValue;
        $(this).prev().val(value).trigger('keyup');
    });
    // Bind "fast filter"
    $('li.fast_filter > input').live('focus', function () {
        if ($(this).val() == this.defaultValue) {
            $(this).val('');
        } else {
            $(this).select();
        }
    });
    $('li.fast_filter > input').live('blur', function () {
        if ($(this).val() == '') {
            $(this).val(this.defaultValue);
        }
    });
    $('li.fast_filter > input').live('keyup', function () {
        var $obj = $(this).parent().parent();
        var str = '';
        if ($(this).val() != this.defaultValue) {
            str = $(this).val().toLowerCase();
        }
        $obj.find('li > a').not('.container').each(function () {
            if ($(this).text().toLowerCase().indexOf(str) != -1) {
                $(this).parent().show().removeClass('hidden');
            } else {
                $(this).parent().hide().addClass('hidden');
            }
        });
        var container_filter = function ($curr, str) {
            $curr.children('li').children('a.container').each(function () {
                var $group = $(this).parent().children('ul');
                if ($group.children('li').children('a.container').length > 0) {
                    container_filter($group); // recursive
                }
                $group.parent().show().removeClass('hidden');
                if ($group.children().not('.hidden').length == 0) {
                    $group.parent().hide().addClass('hidden');
                }
            });
        };
        container_filter($obj, str);
    });

    // Jump to recent table
    $('#recentTable').change(function() {
        if (this.value != '') {
            var arr = jQuery.parseJSON(this.value);
            window.parent.setDb(arr['db']);
            window.parent.setTable(arr['table']);
            window.parent.refreshMain($('#LeftDefaultTabTable')[0].value);
        }
    });
});//end of document get ready
