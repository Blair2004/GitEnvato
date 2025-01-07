<?php

/**
 * Github To Envato Controller
 * @since 1.0
 * @package modules/GitEnvato
**/

namespace Modules\GitEnvato\Http\Controllers;
use Illuminate\Support\Facades\View;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Modules\GitEnvato\Crud\RepositoryCrud;
use Modules\GitEnvato\Jobs\PublishReleaseJob;
use Modules\GitEnvato\Models\Repository;
use Modules\GitEnvato\Services\EnvatoService;

class GitEnvatoController extends Controller
{
    public function __construct( public EnvatoService $envatoService )
    {
        // ...
    }

    public function listRepositories()
    {
        return RepositoryCrud::table();
    }

    public function showCreateForm()
    {
        return RepositoryCrud::form();
    }

    public function showEditForm( Repository $repository )
    {
        return RepositoryCrud::form( $repository );
    }

    public function webhook( Request $request )
    {
        /**
         * We'll first verify the webhook signature to ensure the request is coming from Github.
         */
        $signature = 'sha1=' . hash_hmac( 'sha1', $request->getContent(), ns()->option->get( 'gitenvato_webhook_secret' ) );

        if ( $signature !== $request->header( 'X-Hub-Signature' ) ) {
            return response()->json([ 'error' => 'Unauthorized' ], 401 );
        }

        if ( $request->input( 'action' ) === 'released' ) {
            $tagname    =   $request->input( 'release' )[ 'tag_name' ];
            $name   =   $request->input( 'repository' )[ 'name' ];
            $hash   =   md5( $tagname . $name );
            $itemName   =   $name . '-' . $tagname . '-' . $hash .'.zip';
            $zipballUrl     =   $request->input( 'release' )[ 'zipball_url' ];
            $repository     =   Repository::where( 'name', $request->input( 'repository' )[ 'full_name' ] )->first();

            if ( ! $repository ) {
                return response()->json([ 'error' => 'Remote repository name not matching any local repositories' ], 404 );
            }

            if ( ! $zipballUrl ) {
                return response()->json([ 'error' => 'Invalid release' ], 400 );
            }

            $downloadResponse = Http::withToken( ns()->option->get( 'gitenvato_token' ) )
                ->withHeaders([
                    'User-Agent' => 'GitEnvato',
                    'Accept' => 'application/vnd.github.v3+json' // Standard GitHub API response
                ])
                ->get($zipballUrl);
            
            if ( $downloadResponse->failed() ) {
                return response()->json([ 'error' => 'Failed to download release' ], 500 );
            }

            Storage::disk( 'envato' )->put( $itemName, $downloadResponse->body());

            /**
             * We'll delay the publish
             * as the item doesn't appear on the uploaded items
             * not before 1 minute.
             */
            PublishReleaseJob::dispatch( $repository, $itemName )->delay( now()->addMinutes(2) );
        }
    }
}
