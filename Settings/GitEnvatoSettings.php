<?php
namespace Modules\GitEnvato\Settings;

use App\Classes\FormInput;
use App\Classes\SettingForm;
use App\Services\SettingsPage;

class GitEnvatoSettings extends SettingsPage
{
    const AUTOLOAD = true;
    const IDENTIFIER = 'gitenvato_settings';

    public function __construct()
    {
        $this->form     =   SettingForm::form(
            title: __m( 'GitEnvato Settings', 'GitEnvato' ),
            description: __m( 'Configure your GitEnvato settings', 'GitEnvato' ),
            tabs: SettingForm::tabs(
                SettingForm::tab(
                    identifier: 'general',
                    label: __m( 'Envato', 'GitEnvato' ),
                    fields: SettingForm::fields(
                        FormInput::text(
                            label: __m( 'Envato Username' ),
                            name: 'gitenvato_username',
                            value: ns()->option->get( 'gitenvato_username' ),
                            description: __m( 'Enter your Envato Username', 'GitEnvato' ),
                        ),
                        FormInput::text(
                            label: __m( 'Envato API Key' ),
                            name: 'gitenvato_api',
                            value: ns()->option->get( 'gitenvato_api' ),
                            description: __m( 'Enter your Envato API key', 'GitEnvato' ),
                        ),
                        FormInput::textarea(
                            label: __m( 'Bash cURL Command', 'GitEnvato' ),
                            name: 'gitenvato_bash_command',
                            value: ns()->option->get( 'gitenvato_bash_command' ),
                            description: __m( 'Enter the bash cURL command to fetch the cookies from Envato', 'GitEnvato' ),
                        ),
                        FormInput::textarea(
                            label: __m( 'Reason', 'GitEnvato' ),
                            name: 'gitenvato_reason',
                            value: ns()->option->get( 'gitenvato_reason' ),
                            description: __m( 'This reason will be used while uploading the item.', 'GitEnvato' ),
                        )
                    )
                ),
                SettingForm::tab(
                    identifier: 'github',
                    label: __m( 'Github', 'GitEnvato' ),
                    fields: SettingForm::fields(
                        FormInput::text(
                            label: __m( 'Webhook Secret', 'GitEnvato' ),
                            name: 'gitenvato_webhook_secret',
                            value: ns()->option->get( 'gitenvato_webhook_secret' ),
                            description: __m( 'Define the webhook secret to authentify incoming requests.', 'GitEnvato' ),
                        ),
                        FormInput::text(
                            label: __m( 'Personal Access Token', 'GitEnvato' ),
                            name: 'gitenvato_token',
                            value: ns()->option->get( 'gitenvato_token' ),
                            description: __m( 'Enter your Github Personal Access Token', 'GitEnvato' ),
                        )
                    )
                )
            )
        );
    }
}