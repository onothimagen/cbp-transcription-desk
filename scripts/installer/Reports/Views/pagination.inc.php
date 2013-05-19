<?php

/**
 * Copyright (C) University College London
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License Version 2, as
 * published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 675 Mass Ave, Cambridge, MA 02139, USA.
 *
 * @package CBP Transcription
 * @subpackage Installer
 * @version 1.0
 * @author Ben Parish <b.parish@ulcc.ac.uk>
 * @copyright 2013  University College London
 * @link http://zf2.readthedocs.org/en/latest/tutorials/tutorial.pagination.html
 */
?>
<div id="page-navigation">

<?php


if( $oPages->pageCount > 1 ){

	$sQuery = $_SERVER['QUERY_STRING'];

	//Remove page_num

	$sPos = strpos( $sQuery, '&page_num' );

	if( $sPos !== false ){
		$sQuery = substr( $sQuery, 0, $sPos );
	}

	$sCurrentPageNumber = $oPaginator->getCurrentPageNumber();

	if( property_exists( $oPages, 'previous' ) ){
		?>
		<a href="index.php?<?php echo $sQuery . '&page_num=' . $oPages->previous; ?>">Prev</a>
		<?php
	}

	// Numbered page links


	foreach ( $oPages->pagesInRange as $sPageNum ){
	  	if ( $sPageNum != $oPages->current){ ?>
	    <a href="index.php?<?php echo $sQuery . '&page_num=' . $sPageNum; ?>">
	        <?php echo $sPageNum; ?>
	    </a> |
	  <?php
	  	}else{
			echo $sPageNum;
		};
	}

	if( property_exists( $oPages, 'next' ) ){
		?>
		<a href="index.php?<?php echo $sQuery . '&page_num=' . $oPages->next; ?>">Next</a>
		<?php
	}
}

?>

</div>


