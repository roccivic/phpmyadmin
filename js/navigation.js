/* vim: set expandtab sw=4 ts=4 sts=4: */
/**
 * function used in or for navigation frame
 */

/**
 * opens/closes (hides/shows) tree elements
 *
 * @param   string  id          id of the element in the DOM
 * @param   boolean only_open   do not close/hide element
 */
function toggle(id, only_open)
{
    var el = document.getElementById('subel' + id);
    if (! el) {
        return false;
    }

    var img = document.getElementById('el' + id + 'Img');

    if (el.style.display == 'none' || only_open) {
        el.style.display = '';
        if (img) {
            var newimg = PMA_getImage('b_minus.png');
            img.className = newimg.attr('class');
            img.src = newimg.attr('src');
            img.alt = '-';
        }
    } else {
        el.style.display = 'none';
        if (img) {
            var newimg = PMA_getImage('b_plus.png');
            img.className = newimg.attr('class');
            img.src = newimg.attr('src');
            img.alt = '+';
        }
    }
    return true;
}

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
     * @var object width Stores the widths of the navigation frame:
     *                   the actual width, as defined in the cookie
     *                   and an old recorded value
     */
    width: undefined,
    /**
     * Initializes this Objects
     *
     * @return nothing
     */
    init: function () {
        this.cookie_name = 'navigation_frame_width';
        if (window.parent.frames[0] != undefined && window.parent.frames[1] != undefined) { // if we have two frames
            var elm = "<div id='collapse_frame' style='display:none;'>";
            elm    += "<div class='collapse_top'><div>%s</div></div>";
            elm    += "<div class='collapse_bottom'><div>%s</div></div>";
            elm    += "</div>";
            if (parent.parent.text_dir == 'ltr') {
                var $f0 = $(window.parent.frames[0].document);
                var $f1 = $(window.parent.frames[1].document);
            } else {
                var $f0 = $(window.parent.frames[1].document);
                var $f1 = $(window.parent.frames[0].document);
            }
            /**
             * @var function func A local function that attaches the
             *                    'collapse' buttons to both the
             *                    navigation and the content frames
             */
            var func = function ($obj, label) {
                $obj.find('div#collapse_frame').remove();
                $obj.find('body').append(elm.replace(/%s/g, label));
                $obj.find('div#collapse_frame > div').each(function () {
                    $(this).click(function () {
                        PMA_resizeHandler.collapse();
                    });
                });
            };
            func($f0, '«');
            this.left = $f0.find('div#collapse_frame');
            func($f1, '»');
            this.right = $f1.find('div#collapse_frame');
        }
        if ($f0.find('div#collapse_frame').length + $f1.find('div#collapse_frame').length == 2) {
            $f0.find('div#collapse_frame').show();
            // ready
            this.main();
            setInterval('PMA_resizeHandler.main()', 600);
        } else {
            setTimeout('PMA_resizeHandler.init()', 300);
            return false;
        }
    },
    /**
     * Main function, it is called at regular interval
     *
     * @return nothing
     */
    main: function () {
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
            if (this.width.cookie != null
                && this.width.cookie > 0
                && parent.document != document
                && this.width.cookie != this.width.actual
            ) {
                this.setWidth(this.width.cookie, true, false);
            }
        } else {
            var width = this.getWidth();
            if (width > 1) {
                // The frame width is more than 1 pixel, put it's state
                // is 'collapsed'. Change state to 'not collapsed'.
                this.collapsed = false;
                // show/hide 'collapse' buttons as necessary
                this.left.show('slow');
                this.right.hide();
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
            this.right.show('slow');
        } else {
            this.setWidth(PMA_cookie.get(this.cookie_name), true);
            this.collapsed = false;
            // show/hide 'collapse' buttons as necessary
            this.left.show('slow');
            this.right.hide();
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
 * hide all LI elements with second A tag which doesn`t contain requested value
 *
 * @param   string  value    requested value
 */
function fast_filter(value)
{
    lowercase_value = value.toLowerCase();
    $("#subel0 a[class!='tableicon']").each(function(idx,elem){
        $elem = $(elem);
        // .indexOf is case sensitive so convert to lowercase to compare
        if (value && $elem.html().toLowerCase().indexOf(lowercase_value) == -1) {
            $elem.parent().hide();
        } else {
            $elem.parents('li').show();
        }
    });
}

/**
 * Clears fast filter.
 */
function clear_fast_filter()
{
    var elm = $('#NavFilter input');
    elm.val('');
    fast_filter('');
    elm.focus();
}

/**
 * Reloads the recent tables list.
 */
function PMA_reloadRecentTable()
{
    $.get('navigation.php', {
            'token': window.parent.token,
            'server': window.parent.server,
            'ajax_request': true,
            'recent_table': true},
        function (data) {
            if (data.success == true) {
                $('#recentTable').html(data.options);
            }
        });
}

// Performed on load
$(document).ready(function(){
    // Frame resize handler
    PMA_resizeHandler.init();
    // Display filter
    $('#NavFilter').css('display', 'inline');
    $('input[id="fast_filter"]').focus(function() {
        if($(this).attr("value") === "filter tables by name") {
            clear_fast_filter();
        }
    });
    $('#clear_fast_filter').click(clear_fast_filter);
    $('#fast_filter').focus(function (evt) {evt.target.select();});
    $('#fast_filter').keyup(function (evt) {fast_filter(evt.target.value);});

    /* Jump to recent table */
    $('#recentTable').change(function() {
        if (this.value != '') {
            var arr = jQuery.parseJSON(this.value);
            window.parent.setDb(arr['db']);
            window.parent.setTable(arr['table']);
            window.parent.refreshMain($('#LeftDefaultTabTable')[0].value);
        }
    });

    /* Create table */
    $('#newtable a.ajax').click(function(event){
        event.preventDefault();
        /*Getting the url */
        var url = $('#newtable a').attr("href");
        if (url.substring(0, 15) == "tbl_create.php?") {
             url = url.substring(15);
        }
        url = url +"&num_fields=&ajax_request=true";
        /*Creating a div on the frame_content frame */
        var div = parent.frame_content.$('<div id="create_table_dialog"></div>');
        var target = "tbl_create.php";

        /*
         * Calling to the createTableDialog function
         * (needs to be done in the context of frame_content in order
         *  for the qtip tooltips to work)
         * */
        parent.frame_content.PMA_createTableDialog(div , url , target);
    });//end of create new table
});//end of document get ready

