######## This is a small Google Analytics Demo Application #######
Requirements:
Web Server (Apache)
PHP 5.3 or higher
Google Analytics Account
Google Api account

Installation
The installation is very easy.
Just put the complete source code into the document root of your web server.

###### Parameters to edit ######
FileName : HelloAnalytics.php

$client->setClientId('insert_your_oauth2_client_id');
$client->setClientSecret('insert_your_oauth2_client_secret');
$client->setRedirectUri('insert_your_oauth2_redirect_uri');
$client->setDeveloperKey('insert_your_developer_key');

All above can be found at your api console.
Please note that redirect URI needs to point to the page HelloAnalytics.php only.

Next,
function getResults(&$analytics, $profileId) {
    return $analytics->data_ga->get(
                    'ga:' . $profileId, '<START DATE>', '<END DATE>', 'ga:visits');
}

Specify the start and end date for which report needs to be fetched.

Note: If you are operating from behind the proxy, edit
google-api-php-client\src\io\Google_CurlIO.php

    private $curlParams = array(
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_FOLLOWLOCATION => 0,
        CURLOPT_FAILONERROR => false,
        CURLOPT_SSL_VERIFYPEER => true,
        CURLOPT_HEADER => true,
        CURLOPT_VERBOSE => false,
        //CURLOPT_PROXY => '<IP>',
        //CURLOPT_PROXYPORT => <PORT>,
    );
	
And you are done.