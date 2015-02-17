<?php

/**
 * @package   hoja_mod_subnav
 * @author    Holger Janßen
 * @license   LGPL
 * @copyright Holger Janßen, 2014
 */


$GLOBALS['TL_LANG']['tl_module']['useColumns'] = array ("Blockaufteilung","Soll das Navigationsmenü in Blöcoe (Spalten) aufgeteilt werden?");
$GLOBALS['TL_LANG']['tl_module']['noColumns'] = array ("Anzahl Spalten","Auf wie viele Spalten soll die Navigation aufgeteilt werden?");
$GLOBALS['TL_LANG']['tl_module']['breakColumns'] = array ("Spaltenumbruch","Dürfen Spalten innerhalb eines Hauptpunktes umgebrochen werden?");
$GLOBALS['TL_LANG']['tl_module']['noLevels'] = array ("Anzahl Ebenenen","Wie viele Ebenen unterhalb der Auswahl sollen angezeigt werden?");
$GLOBALS['TL_LANG']['tl_module']['manualBreaks'] = array ("Manuelle Umbrüche", "Wählen Sie Seiten, vor denen eine neue Spalte in der Navigation angefangen werden soll!");

$GLOBALS['TL_LANG']['tl_module']['columnType'] = array ("Spalten-Art", "Soll das Menü auf Spalten (Blöcke) aufgeteilt werden und wenn ja, wie?");


$GLOBALS['TL_LANG']['tl_module']['display_legend'] = "Anzeige-Optionen";
$GLOBALS['TL_LANG']['tl_module']['columns_legend'] = "Spalten-/Blockaufteilung";


$GLOBALS['TL_LANG']['tl_module']['columnType_options'] = array (
	"flat"	 => "keine Spalten",
	"manual" => "manueller Umbruch",
	"balanced"  => "ausgeglichen"
);