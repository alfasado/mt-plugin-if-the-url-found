<?php
function smarty_block_mtiftheurlfound ( $args, $content, &$ctx, &$repeat ) {
    $url = $args[ 'url' ];
    $secure       = empty( $_SERVER[ 'HTTPS' ] ) ? '' : 's';
    $base         = "http{$secure}://{$_SERVER[ 'HTTP_HOST' ]}";
    $base_url = preg_quote( $base, '/' );
    if ( preg_match( "/^$base_url/", $url ) ) {
        // File Check.
        $base_path = $_SERVER[ 'DOCUMENT_ROOT' ];
        $file = preg_replace( "/^$base_url/", $base_path, $url );
        $index = $ctx->mt->config( 'IndexBasename' );
        if (! $index ) $index = 'index';
        if ( preg_match( '/\/$/', $file ) ) {
            $blog = $ctx->stash( 'blog' );
            $file_extension = 'html';
            if ( ( $blog ) && ( $blog->file_extension ) ) {
                $file_extension = $blog->file_extension;
            }
            $file .= $index . '.' . $file_extension;
        }
        if ( is_file( $file ) ) {
            return $ctx->_hdlr_if( $args, $content, $ctx, $repeat, TRUE );
        } else {
            if ( $args[ 'target_dynamic' ] ) {
                require_once( 'class.mt_fileinfo.php' );
                $file = $ctx->mt->db()->escape( $file );
                $where = "fileinfo_file_path='{$file}' AND fileinfo_virtual=1";
                $_finfo = new FileInfo;
                $data = $_finfo->Find( $where, FALSE, FALSE, array( 'limit' => 1 ) );
                if ( $data ) {
                    return $ctx->_hdlr_if( $args, $content, $ctx, $repeat, TRUE );
                }
            }
            return $ctx->_hdlr_if( $args, $content, $ctx, $repeat, FALSE );
        }
    } else {
        if (! $args[ 'target_outlink' ] ) {
            return $ctx->_hdlr_if( $args, $content, $ctx, $repeat, TRUE );
        }
        set_error_handler( 'error_handler' );
        $header = @get_headers( $url );
        if ( preg_match( '#^HTTP/.*\s+[200|302|304]+\s#i', $header[0] ) ) {
            return $ctx->_hdlr_if( $args, $content, $ctx, $repeat, TRUE );
        }
        return $ctx->_hdlr_if( $args, $content, $ctx, $repeat, FALSE );
    }
}
function error_handler() {}
?>