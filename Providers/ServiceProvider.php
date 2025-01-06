<?php
namespace Modules\GitEnvato\Providers;

use App\Classes\Hook;
use Illuminate\Support\ServiceProvider as CoreProvider;
use Modules\GitEnvato\Settings\GitEnvatoSettings;

class ServiceProvider extends CoreProvider
{
    public function boot()
    {
        // ...
    }

    public function register()
    {
        Hook::addFilter( 'ns-dashboard-menus', function ( $menus ) {
            $menus  =   array_insert_after( $menus, 'medias', [
                'gitenvato'     =>  [
                    'label' =>  __m( 'GitEnvato', 'GitEnvato' ),
                    'icon'  =>  'la-code-branch',
                    'childrens' =>  [
                        [
                            'label'     =>  __m( 'Repositories', 'GitEnvato' ),
                            'href'       =>  route( 'ns.dashboard.gitenvato-repositories' ),
                        ]
                    ]
                ]
            ]);

            if ( $menus[ 'settings' ] ) {
                $menus[ 'settings' ][ 'childrens' ][]   =   [
                    'label'     =>  __m( 'GitEnvato', 'GitEnvato' ),
                    'href'       =>  route( 'ns.dashboard.settings', [ 'settings' => GitEnvatoSettings::IDENTIFIER ] ),
                ];
            }

            return $menus;
        } );

        /**
         * We need here to extend the filesystem configuration to add FTP credentials as File Storage configuration.
         * We should only do that if the gitenvato_api is provided.
         */
        if ( ns()->option->get( 'gitenvato_api' ) ) {
            config( [
                'filesystems.disks.envato' => [
                    'driver' => 'ftp',
                    'host' => 'ftp.marketplace.envato.com',
                    'username' => ns()->option->get( 'gitenvato_username' ),
                    'password' => ns()->option->get( 'gitenvato_api' ),
                    'port' => 21,
                    'root' => '',
                    'passive' => true,
                    'ssl' => true,
                    'timeout' => 30,
                ],
            ] );
        }
    }
}