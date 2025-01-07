<?php
namespace Modules\GitEnvato\Services;

use App\Models\Role;
use DOMXPath;
use Illuminate\Support\Facades\Http;
use Modules\GitEnvato\Models\Repository;
use Modules\GitEnvato\Settings\GitEnvatoSettings;

class EnvatoService
{
    public function __construct()
    {
        // ...
    }

    public function extractCookies( $command )
    {
        // Match the cookies string within the -H 'cookie: ...' part
        if (preg_match("/-H 'cookie: (.*?)'/", $command, $matches)) {
            $cookieString = $matches[1];

            // Split cookies into key-value pairs
            $cookies = explode("; ", $cookieString);

            // Parse cookies into an associative array
            $cookieArray = [];
            foreach ($cookies as $cookie) {
                list($key, $value) = explode("=", $cookie, 2);
                $cookieArray[trim($key)] = trim($value);
            }

            return $cookieArray;
        }

        return [];
    }

    public function extractHost( $command )
    {
        if (preg_match("/curl 'https?:\/\/([^\/]+)/", $command, $matches)) {
            return $matches[1];
        } else {
            return null;
        }
    }

    public function getAuthenticityToken()
    {
        // using the cookies, we'll load the author_dashboard page
        // using laravel HTTP client and get the value of the meta tag "csrf-token"
        $response = $this->request( 'https://themeforest.net/user/'.ns()->option->get('gitenvato_username').'/author_dashboard' );
        $html = $response->body();

        // Parse the HTML to get the csrf-token
        $dom = new \DOMDocument();
        @$dom->loadHTML($html);
        $meta = $dom->getElementsByTagName('meta');
        foreach ($meta as $tag) {
            if ($tag->getAttribute('name') === 'csrf-token') {
                return $tag->getAttribute('content');
            }
        }

        return null;
    }

    public function getUploadedItemID( $repository, $itemName )
    {
        $host = $this->extractHost( ns()->option->get( 'gitenvato_bash_command' ) );

        $response = $this->request(
            url: 'https://' . $host . '/item/nexopos-4x-pos-crm-inventory-manager/' . $repository->envato_item_id . '/item_updates/submit_update'
        );

        $html = $response->body();

        // Parse get the select input having has name "temporary_files_to_assign[source]" loop over the option and return the
        // option value for which the option text matches the $itemName
        $dom = new \DOMDocument();

        @$dom->loadHTML($html);
        $xpath  =   new \DOMXPath($dom);
        $select = $xpath->query( '//select[@name="temporary_files_to_assign[source]"]' );

        if ( $select->length > 0 ) {
            $options = $select->item(0)->getElementsByTagName('option');
            foreach ($options as $option) {
                if ( $option->textContent === $itemName ) {
                    return $option->getAttribute('value');
                }
            }
        }

        return null;
    }

    private function request( string $url, string $method = 'GET', array $data = [], bool $asForm = false )
    {
        $host = $this->extractHost( ns()->option->get( 'gitenvato_bash_command' ) );

        $request    =   Http::withHeaders([
                'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/131.0.0.0 Safari/537.36',
                'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8',
                'Accept-Encoding' => 'gzip, deflate, br',
                'Accept-Language' => 'en-US,en;q=0.5',
                'Cache-Control' => 'no-cache',
                'Connection' => 'keep-alive',
                'Host' => $host,
                'Pragma' => 'no-cache',
                'Referer' => 'https://' . $host . '/user/'.ns()->option->get('gitenvato_username').'/author_dashboard',
                'Sec-Fetch-Dest' => 'document',
                'Sec-Fetch-Mode' => 'navigate',
                'Sec-Fetch-Site' => 'same-origin',
                'Sec-Fetch-User' => '?1',
                'Upgrade-Insecure-Requests' => '1',
            ])
            ->withCookies( $this->extractCookies( ns()->option->get( 'gitenvato_bash_command' ) ), $host );

        /**
         * If the request should be sent as a form
         * we'll convert the data to a form data.
         */
        if ( $asForm ) {
            $request    =   $request->asForm();
        }

        if ( $method === 'POST' ) {
            return $request->post( $url, $data );
        } else {
            return $request->get( $url );
        }
    }

    public function getUploadForm( Repository $repository )
    {
        $response   =   $this->request( 'https://' . $repository->marketplace . '/item/envato-item/'.$repository->envato_item_id.'/item_updates/submit_update' );
        $html = $response->body();

        return $this->extractForm( $html );        
    }

    private function extractForm( $html )
    {
        $doc = new \DOMDocument();
        $doc->loadHTML($html);
        $xpath = new DOMXPath($doc);

        $formElements = $xpath->query('//form[@id="upload-form"]//descendant::input | //form[@id="upload-form"]//descendant::textarea | //form[@id="upload-form"]//descendant::select');

        // Extract values into an array
        $formData = [];
        foreach ($formElements as $element) {
            $name = $element->getAttribute('name');
            if (!$name) {
                continue; // Skip elements without a name attribute
            }

            if ($element->tagName === 'select') {
                // For <select>, get the selected option
                $selectedOption = $xpath->query('.//option[@selected]', $element);
                $value = $selectedOption->length > 0 ? $selectedOption->item(0)->getAttribute('value') : null;
            } elseif ($element->tagName === 'input' && $element->getAttribute('type') === 'checkbox') {
                // For checkboxes, only include if checked
                $value = $element->hasAttribute('checked') ? $element->getAttribute('value') : null;
            } else {
                // For other inputs and textarea
                $value = $element->getAttribute('value') ?: $element->nodeValue;
            }

            if ($value !== null) {
                $formData[$name] = $value;
            }
        }

        return $formData;
    }

    public function keepCookiesAlive(): void
    {
        $host   =   $this->extractHost( ns()->option->get( 'gitenvato_bash_command' ) );
        $response = $this->request( 'https://' . $host . '/user/' . ns()->option->get( 'gitenvato_username' ) );
        $html = $response->body();

        /**
         * from here we should search for any reference
         * of the logged user. If it's not available
         * we might have been redirected (the user is not logged)
         */
        if ( strpos( $html, ns()->option->get( 'gitenvato_username' ) ) === false ) {
            ns()->notification->create(
                title: __m( 'Expired Cookies', 'GitEnvato' ),
                description: __m( 'Your cookies have expired, Consider providing a bash command request to your Envato account.', 'GitEnvato' ),
                url: ns()->route( 'ns.dashboard.settings', [ 'identifier' => GitEnvatoSettings::IDENTIFIER ]),
                identifier: 'gitenvato_cookies_expired'
            )->dispatchForGroup( Role::ADMIN );

            throw new \Exception( 'Cookies expired' );
        }
    }

    public function uploadItem( Repository $repository, array $form )
    {
        $host = $this->extractHost( ns()->option->get( 'gitenvato_bash_command' ) );

        $response = $this->request(
            url: 'https://' . $host . '/item/envato-item/'.$repository->envato_item_id.'/item_updates/submit_update',
            method: 'POST',
            data: $form,
            asForm: true
        );

        return $response;
    }
}