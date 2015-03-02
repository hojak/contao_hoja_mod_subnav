<?php

/**
 * @package   hoja_mod_subnav
 * @author    Holger Janßen
 * @license   LGPL
 * @copyright Holger Janßen, 2015
 */
 
 
$GLOBALS['TL_DCA']['tl_module']['palettes']['__selector__'][] = 'columnType';

 
$GLOBALS['TL_DCA']['tl_module']['palettes']['hoja_mod_subnav'] 
	= "{title_legend},name,headline,type;{nav_legend},pages;{template_legend:hide},hoja_module_template;{display_legend},noLevels;"
	."{columns_legend},columnType;{expert_legend:hide},guests,cssID,space;";
	 
$GLOBALS['TL_DCA']['tl_module']['subpalettes']['columnType_manual'] = "columnsAtLevel,manualBreaks";
$GLOBALS['TL_DCA']['tl_module']['subpalettes']['columnType_balanced'] = "columnsAtLevel,noColumns";
 
$GLOBALS['TL_DCA']['tl_module']['fields']['noLevels'] = array (
	"label" => &$GLOBALS['TL_LANG']['tl_module']['noLevels'],
	"exclude" => true,
	"inputType" => "text",
	"eval" => array('maxlength'=>5, 'rgxp'=>'digit', 'tl_class'=>'w50'),
	"sql" => "smallint(5) unsigned NOT NULL default '1'"
);

$GLOBALS['TL_DCA']['tl_module']['fields']['hoja_module_template'] = array (
	'label'                   => &$GLOBALS['TL_LANG']['tl_module']['hoja_module_template'],
	'default'                 => 'mod_hoja_subnav',
	'exclude'                 => true,
	'inputType'               => 'select',
	'options_callback'        => array('tl_hoja_subnav_module', 'getHojaTemplates'),
	'sql'                     => "varchar(32) NOT NULL default ''"

);

$GLOBALS['TL_DCA']['tl_module']['fields']['columnType'] = array (
	'label'                   => &$GLOBALS['TL_LANG']['tl_module']['columnType'],
	'exclude'                 => true,
	'inputType'               => 'select',
	'options'				  => &$GLOBALS['TL_LANG']['tl_module']['columnType_options'],
	'eval'                    => array('mandatory'=>true, 'maxlength'=>10, "submitOnChange" => true),
	'sql'                     => "varchar(10) NOT NULL default ''"
);




/*
$GLOBALS['TL_DCA']['tl_module']['fields']['breakColumns'] = array (
	'label'                   => &$GLOBALS['TL_LANG']['tl_module']['breakColumns'],
	'exclude'                 => true,
	'inputType'               => 'checkbox',
	'eval'                    => array('tl_class'=>''),
	'sql'                     => "char(1) NOT NULL default ''"
);
*/

$GLOBALS['TL_DCA']['tl_module']['fields']['noColumns'] = array (
	"label" => &$GLOBALS['TL_LANG']['tl_module']['noColumns'],
	"exclude" => true,
	"inputType" => "text",
	"eval" => array('maxlength'=>5, 'rgxp'=>'digit', 'tl_class'=>''),
	"sql" => "smallint(5) unsigned NOT NULL default '1'"
);

$GLOBALS['TL_DCA']['tl_module']['fields']['columnsAtLevel'] = array (
	"label" => &$GLOBALS['TL_LANG']['tl_module']['columnsAtLevel'],
	"exclude" => true,
	"inputType" => "text",
	"eval" => array('maxlength'=>5, 'rgxp'=>'digit', 'tl_class'=>''),
	"sql" => "smallint(5) unsigned NOT NULL default '0'"
);

$GLOBALS['TL_DCA']['tl_module']['fields']['manualBreaks'] = array (
	'label'                   => &$GLOBALS['TL_LANG']['tl_module']['manualBreaks'],
	'exclude'                 => true,
	'inputType'               => 'pageTree',
	'foreignKey'              => 'tl_page.title',
	'eval'                    => array('multiple'=>true, 'fieldType'=>'checkbox', 'files'=>true, 'mandatory'=>false, 'tl_class'=>''),
	'sql'                     => "blob NULL",
	'relation'                => array('type'=>'hasMany', 'load'=>'lazy')
);




class tl_hoja_subnav_module extends Backend {

	/**
 	 * Return all hoja module templates as array 
	 * @return array
	 */
	public function getHojaTemplates () {
		return $this->getTemplateGroup ("mod_hoja_");
	}	

}


