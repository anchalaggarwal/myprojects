<?php

require_once 'google-api-php-client/src/Google_Client.php';
require_once 'google-api-php-client/src/contrib/Google_AnalyticsService.php';
session_start();
$client = new Google_Client();
$client->setApplicationName('Hello Analytics API Sample');

// Visit //code.google.com/apis/console?api=analytics to generate your
// client id, client secret, and to register your redirect uri.
$client->setClientId('insert_your_oauth2_client_id');
$client->setClientSecret('insert_your_oauth2_client_secret');
$client->setRedirectUri('insert_your_oauth2_redirect_uri');
$client->setDeveloperKey('insert_your_developer_key');
$client->setScopes(array('https://www.googleapis.com/auth/analytics.readonly'));

// Magic. Returns objects from the Analytics Service instead of associative arrays.
$client->setUseObjects(true);

if (isset($_GET['code'])) {
    $client->authenticate();
    $_SESSION['token'] = $client->getAccessToken();
    $redirect = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'];
    header('Location: ' . filter_var($redirect, FILTER_SANITIZE_URL));
}
if (isset($_SESSION['token'])) {
    $client->setAccessToken($_SESSION['token']);
}
if (!$client->getAccessToken()) {
    $authUrl = $client->createAuthUrl();
    print "<a class='login' href='$authUrl'>Connect Me!</a>";
} else {
    // Create analytics service object. See next step below.
    $analytics = new Google_AnalyticsService($client);
    runMainDemo($analytics);
}

function runMainDemo(&$analytics) {
    try {

        // Step 2. Get the user's first profile ID.
        $profileId = getFirstProfileId($analytics);

        if (isset($profileId)) {

            // Step 3. Query the Core Reporting API.
            $results = getResults($analytics, $profileId);

            // Step 4. Output the results.
            printResults($results);
        }
    } catch (apiServiceException $e) {
        // Error from the API.
        print 'There was an API error : ' . $e->getCode() . ' : ' . $e->getMessage();
    } catch (Exception $e) {
        print 'There wan a general error : ' . $e->getMessage();
    }
}

function getFirstprofileId(&$analytics) {
    $accounts = $analytics->management_accounts->listManagementAccounts();

    if (count($accounts->getItems()) > 0) {
        $items = $accounts->getItems();
        $firstAccountId = $items[0]->getId();

        $webproperties = $analytics->management_webproperties
                ->listManagementWebproperties($firstAccountId);

        if (count($webproperties->getItems()) > 0) {
            $items = $webproperties->getItems();
            $firstWebpropertyId = $items[0]->getId();

            $profiles = $analytics->management_profiles
                    ->listManagementProfiles($firstAccountId, $firstWebpropertyId);

            if (count($profiles->getItems()) > 0) {
                $items = $profiles->getItems();
                return $items[0]->getId();
            } else {
                throw new Exception('No profiles found for this user.');
            }
        } else {
            throw new Exception('No webproperties found for this user.');
        }
    } else {
        throw new Exception('No accounts found for this user.');
    }
}

function getResults(&$analytics, $profileId) {
    return $analytics->data_ga->get(
                    'ga:' . $profileId, '<START DATE>', '<END DATE>', 'ga:visits');
}

function printResults(&$results) {
    if (count($results->getRows()) > 0) {
        $profileName = $results->getProfileInfo()->getProfileName();
        $rows = $results->getRows();
        $visits = $rows[0][0];

        print "<p>First profile found: $profileName</p>";
        print "<p>Total visits: $visits</p>";
    } else {
        print '<p>No results found.</p>';
    }
}

?>
