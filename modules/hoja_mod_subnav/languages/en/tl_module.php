<?php

/**
 * @package   hoja_mod_subnav
 * @author    Holger Janßen
 * @license   LGPL
 * @copyright Holger Janßen, 2014
 */


$GLOBALS['TL_LANG']['tl_module']['hoja_module_template'] = array ("template for the module", "Which template should be used to render the complete contents of this module?");
$GLOBALS['TL_LANG']['tl_module']['useColumns'] = array ("separation in blocks / columns","Is the navigation menu to be divided into blocks / columns?");
$GLOBALS['TL_LANG']['tl_module']['noColumns'] = array ("Columns","How many columns should be used for the navigation menu.");
$GLOBALS['TL_LANG']['tl_module']['breakColumns'] = array ("break menus","May column breaks occus within a main item?");
$GLOBALS['TL_LANG']['tl_module']['noLevels'] = array ("Levels","How many menu levels are to be displayed?");
$GLOBALS['TL_LANG']['tl_module']['columnsAtLevel'] = array ("Level for columns","On which navigation level are the columns to be used?");
$GLOBALS['TL_LANG']['tl_module']['manualBreaks'] = array ("manual column breaks", "Choose pages before which the columns of the navigation menu should be broken.");

$GLOBALS['TL_LANG']['tl_module']['columnType'] = array ("Column Type", "Should the navigation menu be devided into columns (blocks), if yes, how?");

$GLOBALS['TL_LANG']['tl_module']['display_legend'] = "display options";
$GLOBALS['TL_LANG']['tl_module']['columns_legend'] = "separation in blocks / columns";



$GLOBALS['TL_LANG']['tl_module']['columnType_options'] = array (
	"flat"	 => "no columns",
	"manual" => "manual breaks",
	"balanced"  => "balanced"
);
