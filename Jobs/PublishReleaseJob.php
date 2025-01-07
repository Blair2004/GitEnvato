<?php
namespace Modules\GitEnvato\Jobs;

use App\Models\Role;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Modules\GitEnvato\Models\Repository;
use Modules\GitEnvato\Services\EnvatoService;
use Throwable;

/**
 * Register Job
**/
class PublishReleaseJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Here you'll resolve your services.
     */
    public function __construct( public Repository $repository, public string $fileName )
    {
        // ...
    }

    /**
     * Here your jobs is being executed
     */
    public function handle( EnvatoService $envatoService )
    {
        /**
         * We'll now pull the version ID as uploaded on Envato
         */
        $itemId = $envatoService->getUploadedItemID( $this->repository, $this->fileName );
        $form   = $envatoService->getUploadForm( $this->repository );
        
        $form[ 'temporary_files_to_assign[source]' ] = $itemId;
        $form[ 'temporary_files_to_assign[thumbnail]' ] = '';
        $form[ 'temporary_files_to_assign[inline_image_preview]' ] = '';
        $form[ 'temporary_files_to_assign[gallery_preview]' ] = '';
        $form[ 'temporary_files_to_assign[html_preview]' ] = '';
        $form[ 'temporary_files_to_assign[video_preview]' ] = '';
        $form[ 'reason' ] = ns()->option->get( 'gitenvato_reason' ) ?: 'New Release';
        $form[ 'notify_buyers' ]    =   1;

        $response = $envatoService->uploadItem( $this->repository, $form );

        /**
         * We need to check if the upload was successful by
         * fetching any error on the response.
         */
        $body   =   $response->body();

        /**
         * We should convert the body as a DomDocument and search for
         * any tag with class "e-alert-box__message". If that exits, we'll pull the text from it and throw an error.
         */
        $dom = new \DOMDocument();
        @$dom->loadHTML( $body );
        $xpath = new \DOMXPath( $dom );
        $alert = $xpath->query( "//*[contains(concat(' ', normalize-space(@class), ' '), ' e-alert-box__message ')]" );

        if ( $alert->length > 0 ) {
            ns()->notification->create(
                title: __m( 'GitEnvato Alert' ),
                description: sprintf( __m( 'Repository %s: %s', 'GitEnvato' ), $this->repository->name, $alert->item( 0 )->textContent ),
                identifier: 'gitenvato_publish_release',
            )->dispatchForGroup( Role::ADMIN );
        }
    }

    public function failed( Throwable $exception ) 
    {
        ns()->notification->create(
            title: __m( 'GitEnvato Exception', 'GitEnvato' ),
            description: sprintf( __m( 'Release %s: %s', 'GitEnvato' ), $this->repository->name, $exception->getMessage() ),
            identifier: 'gitenvato_failed_publish_release',
        )->dispatchForGroup( Role::ADMIN );
    }
}