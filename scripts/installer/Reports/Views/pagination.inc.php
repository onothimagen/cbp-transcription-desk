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


