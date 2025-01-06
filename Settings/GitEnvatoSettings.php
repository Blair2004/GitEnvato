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
                        )
                    )
                )
            )
        );
    }
}