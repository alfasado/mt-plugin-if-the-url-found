package IfTheURLFound::Tags;

use strict;

sub _hdlr_if_the_url_found {
    my ( $ctx, $args, $cond ) = @_;
    my $app = MT->instance;
    my $base = $app->base;
    my $url = $args->{ url };
    my $base_url = quotemeta( $base );
    if ( $url =~ /^$base_url/ ) {
        # File Check.
        my $base_path = $app->document_root();
        my $file = $url;
        $file =~ s/^$base_url/$base_path/;
        my $index = $app->config( 'IndexBasename' );
        $index = 'index' if (! $index );
        if ( $file =~ /\/$/ ) {
            my $blog = $ctx->stash( 'blog' );
            my $file_extension = 'html';
            if ( ( $blog ) && ( $blog->file_extension ) ) {
                $file_extension = $blog->file_extension;
            }
            $file .= $index . '.' . $file_extension;
        }
        require MT::FileMgr;
        my $fmgr = MT::FileMgr->new( 'Local' ) or die MT::FileMgr->errstr;
        if ( $fmgr->exists( $file ) ) {
            return 1;
        } else {
            if ( $args->{ target_dynamic }) {
                require MT::FileInfo;
                my $data = MT::FileInfo->load( { file_path => $file, virtual => 1 } );
                if ( $data ) {
                    return 1;
                }
            }
            return 0;
        }
    } else {
        if (! $args-> { target_outlink } ) {
            return 1;
        }
        require LWP::UserAgent;
        my $remote_ip = $app->remote_ip;
        my $agent = "Mozilla/5.0 (Movable Type IfTheURLFound plugin X_FORWARDED_FOR:$remote_ip)";
        my $ua = LWP::UserAgent->new( agent => $agent );
        my $response = $ua->head( $url );
        if ( $response->is_success ) {
            return 1;
        } else {
            return 0;
        }
    }
    return 1;
}

1;