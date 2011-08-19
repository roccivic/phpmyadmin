<?php
/* vim: set expandtab sw=4 ts=4 sts=4: */
/**
 * navigation css file from theme Original
 *
 * @package PhpMyAdmin-theme
 * @subpackage pmahomme
 */

// unplanned execution path
if (!defined('PMA_MINIMUM_COMMON')) {
    exit();
}
?>
/******************************************************************************/
/* general tags */
html {
    font-size: <?php echo (null !== $GLOBALS['PMA_Config']->get('fontsize') ? $GLOBALS['PMA_Config']->get('fontsize') : $_COOKIE['pma_fontsize']); ?>;
}

input, select, textarea {
    font-size: 1em;
}

body {
<?php if (! empty($GLOBALS['cfg']['FontFamily'])) { ?>
    font-family:        <?php echo $GLOBALS['cfg']['FontFamily']; ?>;
<?php } ?>
    background:         url(./themes/pmahomme/img/left_nav_bg.png) repeat-y right 0% #f3f3f3;
    border-right:       1px solid #aaa;
    color:              <?php echo $GLOBALS['cfg']['NaviColor']; ?>;
    margin:             0;
    padding:            0;
}

a img {
    border: 0;
}

a:link,
a:visited,
a:active {
    text-decoration:    none;
    color:              #0000FF;
}

form {
    margin:             0;
    padding:            0;
    display:            inline;
}

/* buttons in some browsers (eg. Konqueror) are block elements,
   this breaks design */
button {
    display:            inline;
}

/******************************************************************************/
/* classes */

.nowrap {
    white-space:        nowrap;
}

.expander {
	cursor: pointer;
}

/******************************************************************************/
/* specific elements */

div#pmalogo {
    <?php //better echo $GLOBALS['cfg']['logoBGC']; ?>
}

select#select_server,
div#recentTableList select {
    width: 100%;
}

div#pmalogo,
div#leftframelinks,
div#serverChoice,
div#recentTableList,
div#navidbpageselector {
    color: #333;
    text-align: center;
    margin: 5px 10px 0px 10px;
}

div#navidbpageselector a,
div#navidbpageselector select{
    color: <?php echo $GLOBALS['cfg']['NaviColor']; ?>;
    margin: 0.2em;
}

div#leftframelinks a img.icon {
    margin: 0.3em;
    border: 0px;
}

/* Navigation tree*/
#navigation_tree {
    margin: 5px 10px 0px 10px;
    color: #444;
}
#navigation_tree a {
    color: <?php echo $GLOBALS['cfg']['NaviColor']; ?>;
}
#navigation_tree a:hover {
    color: <?php echo $GLOBALS['cfg']['NaviPointerColor']; ?>;
    text-decoration: underline;
}
#navigation_tree ul {
    padding-<?php echo $left; ?>: 0;
    margin-bottom: 0.2em;
    list-style-type: none;
}
#navigation_tree ul ul {
    padding-<?php echo $left; ?>: 0.5em;
    border-<?php echo $left; ?>: 1px dotted <?php echo $GLOBALS['cfg']['NaviColor']; ?>;
    border-bottom: 1px dotted <?php echo $GLOBALS['cfg']['NaviColor']; ?>;
}
#navigation_tree img {
	margin: 0;
}

/* Fast filter */
li.fast_filter input {
    width: 100%;
}
li.fast_filter span {
    position: relative;
    <?php echo $right; ?>: 1.5em;
    padding: 0.2em;
    cursor: pointer;
    font-weight: bold;
    color: #800;
}

/* Button for collapsing the frame */
div#collapse_frame .collapse_top {
    top: -1px;
    position: fixed;
    <?php echo $right; ?>: 1px;
    margin: 0;
}
div#collapse_frame .collapse_bottom {
    bottom: 1px;
    position: fixed;
    <?php echo $right; ?>: 1px;
    margin: 0;
}
div#collapse_frame div div {
    cursor: pointer;
    padding: 0.2em 0.3em;
    color: #444;
    font-weight: bold;
    background: #ccc;
    background: rgba(204, 204, 204, 0.6);
    position: relative;
    -webkit-box-shadow: 0 0 2px 0 #444;
    -moz-box-shadow: 0 0 2px 0 #444;
    box-shadow: 0 0 2px 0 #444; 
}
