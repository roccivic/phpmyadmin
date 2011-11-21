<?php
/* vim: set expandtab sw=4 ts=4 sts=4: */
/**
 * navigation css file from theme Original
 *
 * @package PhpMyAdmin-theme
 * @subpackage pmahomme
 */

// unplanned execution path
if (!defined('PMA_MINIMUM_COMMON') && !defined('TESTSUITE')) {
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
    -moz-border-radius:2px;
    -webkit-border-radius:2px;
    border-radius:2px;

    -moz-box-shadow:0 1px 2px #ddd;
    -webkit-box-shadow:0 1px 2px #ddd;
    box-shadow:0 1px 2px #ddd;

    border:1px solid #aaa;
    color:#333333;
    padding:3px;
    background:url(<?php echo $_SESSION['PMA_Theme']->getImgPath(); ?>input_bg.gif)
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

img {
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

/******************************************************************************/
/* classes */

.expander {
	cursor: pointer;
}

/******************************************************************************/
/* specific elements */

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

/* Navigation tree*/
#navigation_tree {
    margin: 5px 0 0 10px;
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
    clear: both;
    padding: 0;
    list-style-type: none;
    margin: 0;
}
#navigation_tree ul ul {
    position: relative;
}
#navigation_tree li {
    white-space: nowrap;
    clear: both;
    min-height: 16px;
}
#navigation_tree img {
	margin: 0;
}
#navigation_tree div.block {
    position: relative;
    width:1.5em;
    height:1.5em;
    min-width: 16px;
    min-height: 16px;
    float: <?php echo $left; ?>;
}
#navigation_tree div.block i,
#navigation_tree div.block b {
    width: 1.5em;
    height: 1.5em;
    min-width: 16px;
    min-height: 8px;
    position: absolute;
    bottom: 0.7em;
    <?php echo $left; ?>: 0.75em;
    z-index: 0;
}
#navigation_tree div.block i { /* Top and right segments for the tree element connections */
    display: block;
    border-<?php echo $left; ?>: 1px solid #666;
    border-bottom: 1px solid #666;
}
#navigation_tree div.block i.first { /* Removes top segment */
    border-<?php echo $left; ?>: 0;
}
#navigation_tree div.block b { /* Bottom segment for the tree element connections */
    display: block;
    height: 0.75em;
    bottom: 0;
    left: 0.75em;
    border-<?php echo $left; ?>: 1px solid #666;
}
#navigation_tree div.block a {
    position: absolute;
    left: 50%;
    top: 50%;
    z-index: 10;
}
#navigation_tree div.block img {
    position: relative;
    top: -7px;
    left: -7px;
}
#navigation_tree div.throbber img {
    top: 2px;
    left: 2px;
}
#navigation_tree li.last > ul {
    background: none;
}
#navigation_tree li > a, #navigation_tree li > i {
    line-height: 1.5em;
    height: 1.5em;
    padding-<?php echo $left; ?>: 0.3em;
}
#navigation_tree .list_container {
    border-<?php echo $left; ?>: 1px solid #666;
    margin-<?php echo $left; ?>: 0.75em;
    padding-<?php echo $left; ?>: 0.75em;
}
#navigation_tree .last > .list_container {
    border-<?php echo $left; ?>: 0 solid #666;
}

/* Fast filter */
li.fast_filter {
    padding-<?php echo $left; ?>: 0.75em;
    margin-<?php echo $left; ?>: 0.75em;
    padding-<?php echo $right; ?>: 10px;
    border-<?php echo $left; ?>: 1px solid #666;
}
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
    z-index: 600;
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
