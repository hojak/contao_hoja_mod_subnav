<?php

namespace HoJa\SubNav;



/**
 * @package   hoja_mod_subnav
 * @author    Holger Janßen
 * @license   LGPL
 * @copyright Holger Janßen, 2015
 */
 
class ModuleHoJaSubNav extends \Module
{

	/**
	 * Template
	 * @var string
	 */
	protected $strTemplate = 'mod_hoja_subnav';
	protected $strElementTemplate = 'mod_hoja_subnav_element';
	


	/**
	 * Do not display the module if there are no menu items
	 * @return string
	 */
	public function generate()
	{
		if (TL_MODE == 'BE')
		{
			$objTemplate = new \BackendTemplate('be_wildcard');

			$objTemplate->wildcard = '### ' . utf8_strtoupper($GLOBALS['TL_LANG']['FMD']['mod_nav_hoja_subnav'][0]) . ' ###';
			$objTemplate->title = $this->headline;
			$objTemplate->id = $this->id;
			$objTemplate->link = $this->name;
			$objTemplate->href = 'contao/main.php?do=themes&amp;table=tl_module&amp;act=edit&amp;id=' . $this->id;

			return $objTemplate->parse();
		}
		
		
		// Always return an array (see #4616)
		$this->pages = deserialize($this->pages, true);

		if (empty($this->pages) || $this->pages[0] == '')
		{
				return '';
		}


		$strBuffer = parent::generate();
		
		//return ($this->Template->navi_menu != '') ? $strBuffer : '';
		return $strBuffer;
	}


	/**
	 * Generate the module
	 */
	protected function compile()
	{
		global $objPage;
		
		// Get all groups of the current front end user
		$this->fe_groups = array();
		if (FE_USER_LOGGED_IN)
		{
			$this->import('FrontendUser', 'User');
			$this->fe_groups = $this->User->groups;
		}
		
		// get the selected navigation items
		$selectedItems = $this->getSelectedItems();
			
		// additional template stuff
		//$objTemplate = new \FrontendTemplate( $this->hoja_subnav_template );
		
		
		$this->Template->request = ampersand(\Environment::get('indexFreeRequest'));
		$this->Template->skipId = 'skipNavigation' . $this->id;
		$this->Template->skipNavigation = specialchars($GLOBALS['TL_LANG']['MSC']['skipNavigation']);
		$this->Template->type = get_class($this);
		$this->Template->cssID = $this->cssID; // see #4897 and 6129
		$this->Template->level = 'level_1';
		
		
		switch ( $this->columnType ) {
			case 'manual':
				$this->Template->navi_menu = $this->renderManualColumns ( $selectedItems );
				break;
			case 'balanced':
				$this->Template->navi_menu = $this->renderBalancedColumns ( $selectedItems );
				break;
			default:
				$this->Template->navi_menu = $this->renderNormal ( $selectedItems );
		}
		
	}
	
	
	
	
	protected function renderNormal ( $pageObjects, $level = 0, $class = "" ) {
		$objTemplate = new \FrontendTemplate( $this->strElementTemplate );
		$objTemplate->type = get_class($this);
		$objTemplate->cssID = $this->cssID; // see #4897
		$objTemplate->level = 'level_' . $level;
		$objTemplate->hoja_ul_class = "hoja_subnav";
		
		if ( $class )
			$objTemplate->hoja_ul_class .= " " . $class;
		
		
		$items = array ();
		foreach ( $pageObjects as $obj ) {
			$data = $this->prepareNode ( $obj );
			
			if ( $level < $this->noLevels ) {
				// open page object
				
				$subpages = $this->getSubPages ( $data['id']);
							
				if ( $subpages ) {
					$data['subitems'] = $this->renderNormal ( $subpages, $level +1 );
				}
			}
			
			$items[] = $data;
		}
				
		$objTemplate->items = $items;
		return !empty($items) ? $objTemplate->parse() : '';
	}
	
	
	
	protected function renderBalancedColumns ( $pageRows ) 
	{
		$flatData = array ();
		foreach ( $pageRows as $row )
			$this->loadFlatNodeData ( $flatData, $row );
	
		$size = sizeof ( $flatData );;			
		$max_col_size = ceil ( $size / $this->noColumns );
		
		$large_cols = $size % $this->noColumns;
		if ( $large_cols == 0 ) $large_cols = $this->noColumns;
		
		$columns = array ();
		$item = 0;
		for ( $col = 0; $col < $large_cols; $col ++) {
			for ( $i = 0; $i< $max_col_size; $i++ ) {
				$columns[ $col][] = $flatData[$item];
				
				$item ++;
			}
		}
		
		for ( $col = $large_cols; $col<$this->noColumns; $col++ ) {
			for ( $i = 0; $i< $max_col_size-1; $i++ ) {
				$columns[ $col][] = $flatData[$item];
				$item ++;
			}
		}
		
		$result = "";
		for ( $i=0; $i<sizeof ( $columns); $i++)
			$result .= $this->renderColumn ( $columns[$i]);
	
		return $result;
	}

	
	protected function renderManualColumns ( $pageRows ) 
	{
		$breaks = deserialize($this->manualBreaks, true);
		
		$flatData = array ();
		foreach ( $pageRows as $row )
			$this->loadFlatNodeData ( $flatData, $row );
	
		$size = sizeof ( $flatData );;			
		$max_col_size = ceil ( $size / $this->noColumns );
		
		$columns = array ();
		$current_col = 0;
		for ( $i=0; $i<$size; $i++) {
			if ( sizeof ( $columns[$current_col]) > 0 && in_array ( $flatData[$i]['id'], $breaks ))
				$current_col ++;
	
			$columns[ $current_col][] = $flatData[$i];
		}
					
		$result = "";
		for ( $i=0; $i<sizeof ( $columns); $i++)
			$result .= $this->renderColumn ( $columns[$i]);
	
		return $result;
	}
	
	
	
	protected function loadFlatNodeData ( &$flatData,  $datarow, $level = 0 ) {
		$datarow ['hoja_level'] = $level;
		$flatData[] = $datarow;
		
		$subpages = $this->getSubPages ( $datarow['id']);
		foreach( $subpages as $page ) {
			$this->loadFlatNodeData ($flatData, $page, $level +1 );
		}
	}
	
	
	protected function renderColumns ( $pageObjects ) {
		$tree = array ();
		foreach ( $pageObjects as $row ) $tree[] = $this->createNavTreeNode ( $row);

		$size = 0;
		foreach ( $tree as $rootNode )
			$size += $rootNode['size'];
			
		$max_col_size = ceil ( ($size / $this->noColumns) * 1.5 );
		
		$columns = array ();
		$current_col = 0;
		$current_col_size = 0;
		
		foreach ( $tree as $rootNode ) {
			if ( $current_col_size > 0 && $current_col_size + $rootNode['size'] > $max_col_size && $current_col < $this->noColumns -1 ) {
				$current_col ++;
				$current_col_size = 0;
			}
			
			$current_col_size += $rootNode['size'];
			$columns[$current_col][] = $rootNode['data'];
		}
		
		
		$result = "";
		
		foreach ( $columns as $column )
			$result .= "<!-- start column -->\n".$this->renderNormal ( $column, 1, "hoja_subnav_column" )."\n<!-- end column -->\n\n";
			
		return $result;
	}
	
	
	
	protected function renderColumn ( $item_data ) {
		$objTemplate = new \FrontendTemplate( $this->strElementTemplate );
		$objTemplate->type = get_class($this);
		$objTemplate->cssID = $this->cssID; // see #4897
		$objTemplate->level = 'level_' . $level;
		$objTemplate->hoja_ul_class = "hoja_subnav_column";
		
		
		$items = array ();
		foreach ( $item_data as $item ) {
			$data = $this->prepareNode ( $item );
			$data['class'] .=  " " . "subnav_lvl_".$item['hoja_level'];
			$items[] = $data;
		}
				
		$objTemplate->items = $items;
		return !empty($items) ? $objTemplate->parse() : '';	
	}
	
	
	protected function createNavTreeNode ( $row, $level = 0 ) {
		$result = array( 
			'data' => $row,
			'level' => $level,
			'subitems' => array ()
		);
		
		$subitems =  $this->getSubPages ( $row['id'] );
		foreach ( $subitems as $item )
			$result['subitems'][] = $this->createNavTreeNode($item, $level+1);

		
		$size = 1;
		foreach ( $result['subitems'] as $subrow )
			$size += $subrow['size'];
		$result['size'] = $size;
		
		return $result;
	}
	
	
	
	
	protected function prepareColumns ( $tree ) {
		$count = sizeof ( $this->flatData );
		$max_size = ceil ( $count / $this->noColumns * 1.5) ;
		$columns = array ();
		
		$current_col = 0;
		$current_size = 0;
		
		$current_flat_pos = 0;
		
		
		foreach ( $tree as $node ) {
			$node_size = $this->getNodeSize ( $node );
			
			if ( $current_size != 0 && ($current_size + $node_size > $max_size) && $current_col < $this->noColumns -1 ) {
				$current_col ++;
				$current_size = 0;
			}
			
			
			$current_size += $node_size;
			
			for ( $i=0; $i<$node_size; $i++ ) 
				$columns[$current_col][] = $this->flatData[$current_flat_pos+$i];
			$current_flat_pos += $node_size;
		}
		
		return $columns;
	}
	

	
	
	protected function prepareNode ( $rowdata ) {
		global $objPage;

		switch ($rowdata['type'])
		{
			case 'redirect':
				$href = $rowdata['url'];
				break;

			case 'forward':
				if (($objNext = \PageModel::findPublishedById($rowdata['jumpTo'])) !== null)
				{
					$strForceLang = null;
					$objNext->loadDetails();

					// Check the target page language (see #4706)
					if ($GLOBALS['TL_CONFIG']['addLanguageToUrl'])
					{
						$strForceLang = $objNext->language;
					}

					$href = $this->generateFrontendUrl($objNext->row(), null, $strForceLang);

					// Add the domain if it differs from the current one (see #3765)
					if ($objNext->domain != '' && $objNext->domain != \Environment::get('host'))
					{
						$href = ($objNext->rootUseSSL ? 'https://' : 'http://') . $objNext->domain . TL_PATH . '/' . $href;
					}
					break;
				}
				// DO NOT ADD A break; STATEMENT

			default:
				$href = $this->generateFrontendUrl($rowdata, null, $rowdata['rootLanguage']);

				// Add the domain if it differs from the current one (see #3765)
				if ($rowdata['domain'] != '' && $rowdata['domain'] != \Environment::get('host'))
				{
					$href = ($rowdata['rootUseSSL'] ? 'https://' : 'http://') . $rowdata['domain'] . TL_PATH . '/' . $href;
				}
				break;
		}

		// Active page
		if ($objPage->id == $rowdata['id'])
		{
			$strClass = trim($rowdata['cssClass']);

			$rowdata['isActive'] = true;
			$rowdata['class'] = trim('active ' . $strClass);
			$rowdata['title'] = specialchars($rowdata['title'], true);
			$rowdata['pageTitle'] = specialchars($rowdata['pageTitle'], true);
			$rowdata['link'] = $rowdata['title'];
			$rowdata['href'] = $href;
			$rowdata['nofollow'] = (strncmp($rowdata['robots'], 'noindex', 7) === 0);
			$rowdata['target'] = '';
			$rowdata['description'] = str_replace(array("\n", "\r"), array(' ' , ''), $rowdata['description']);

			// Override the link target
			if ($rowdata['type'] == 'redirect' && $rowdata['target'])
			{
				$rowdata['target'] = ($objPage->outputFormat == 'xhtml') ? ' onclick="return !window.open(this.href)"' : ' target="_blank"';
			}
		}

		// Regular page
		else
		{
			$strClass = trim($rowdata['cssClass'] . (in_array($rowdata['id'], $objPage->trail) ? ' trail' : ''));
			$rowdata['isActive'] = false;
			$rowdata['class'] = $strClass;
			$rowdata['title'] = specialchars($rowdata['title'], true);
			$rowdata['pageTitle'] = specialchars($rowdata['pageTitle'], true);
			$rowdata['link'] = $rowdata['title'];
			$rowdata['href'] = $href;
			$rowdata['nofollow'] = (strncmp($rowdata['robots'], 'noindex', 7) === 0);
			$rowdata['target'] = '';
			$rowdata['description'] = str_replace(array("\n", "\r"), array(' ' , ''), $rowdata['description']);

			// Override the link target
			if ($rowdata['type'] == 'redirect' && $rowdata['target'])
			{
				$rowdata['target'] = ($objPage->outputFormat == 'xhtml') ? ' onclick="return !window.open(this.href)"' : ' target="_blank"';
			}
		}
		
		
		return $rowdata;
	}
	
	
	
	protected function getSubPages ( $id ) {
		$objSubpages = \PageModel::findPublishedSubpagesWithoutGuestsByPid(
			$id, false, $this instanceof \ModuleSitemap);

		if ( ! $objSubpages ) return array ();
			
		$result = array ();
		while ($objSubpages->next())
		{
			// Skip hidden sitemap pages
			if ($this instanceof \ModuleSitemap && $objSubpages->sitemap == 'map_never')
			{
				continue;
			}

			$subitems = '';
			$_groups = deserialize($objSubpages->groups);

			// Override the domain (see #3765)
			if ($host !== null)
			{
				$objSubpages->domain = $host;
			}
			
			// Do not show protected pages unless a back end or front end user is logged in
			if (!$objSubpages->protected || BE_USER_LOGGED_IN || (is_array($_groups) && count(array_intersect($_groups, $this->fe_groups))) || $this->showProtected || ($this instanceof \ModuleSitemap && $objSubpages->sitemap == 'map_always'))
			{
				$result[] = $objSubpages->row();
			}
		}
	
		return $result;
	}
	
	
	
	
	
	/**
	 * get the selected 'root' entries for this menu
	 **/
	protected function getSelectedItems ()
	{
		global $objPage;

		$items = array();
		$groups = array();

		// Get all active pages
		$objPages = \PageModel::findPublishedRegularWithoutGuestsByIds($this->pages);

		// Return if there are no pages
		if ($objPages === null)
		{
			return array ();
		}

		$arrPages = array();

		// Sort the array keys according to the given order
		if ($this->orderPages != '')
		{
			$tmp = deserialize($this->orderPages);

			if (!empty($tmp) && is_array($tmp))
			{
				$arrPages = array_map(function(){}, array_flip($tmp));
			}
		}

		// Add the items to the pre-sorted array
		while ($objPages->next())
		{
			$arrPages[$objPages->id] = $objPages->current()->loadDetails()->row(); // see #3765
		}


		
		$result = array ();
		foreach ($arrPages as $arrPage)
		{
			// Skip hidden pages (see #5832)
			if (!is_array($arrPage))
			{
				continue;
			}

			$_groups = deserialize($arrPage['groups']);

			// Do not show protected pages unless a back end or front end user is logged in
			if (!$arrPage['protected'] || BE_USER_LOGGED_IN || (is_array($_groups) && count(array_intersect($_groups, $this->fe_groups))) || $this->showProtected)
			{
				// page is to show
				$result[] = $arrPage;
			}
		}
		
		return $result;
	}

                        
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
}
