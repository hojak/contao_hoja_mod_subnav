<?php

/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2005-2015 Leo Feyer
 *
 * @license LGPL-3.0+
 */


/**
 * Register the namespaces
 */
ClassLoader::addNamespaces(array
(
	'HoJa\SubNav',
));


/**
 * Register the classes
 */
ClassLoader::addClasses(array
(
	// Modules
	'HoJa\\SubNav\\ModuleHoJaSubNav' => 'system/modules/hoja_mod_subnav/modules/ModuleHoJaSubNav.php',
));


/**
 * Register the templates
 */
TemplateLoader::addFiles(array
(
	'mod_hoja_subnav_element' => 'system/modules/hoja_mod_subnav/templates',
	'mod_hoja_subnav' => 'system/modules/hoja_mod_subnav/templates',
));
