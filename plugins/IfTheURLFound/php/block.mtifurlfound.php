<?php
function smarty_block_mtifurlfound ( $args, $content, &$ctx, &$repeat ) {
    require_once( 'block.mtiftheurlfound.php' );
    return smarty_block_mtiftheurlfound( $args, $content, $ctx, $repeat );
}
?>