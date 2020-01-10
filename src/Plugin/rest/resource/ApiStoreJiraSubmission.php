<?php

namespace Drupal\api_store\Plugin\rest\resource;

use Drupal\rest\Plugin\ResourceBase;
use Drupal\rest\ResourceResponse;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Provides a ApiStoreJiraSubmission Resource
 *
 * @RestResource(
 *   id = "jira_submission",
 *   label = @Translation("ApiStoreJiraSubmission"),
 *   uri_paths = {
 *     "canonical" = "/api_store/jira_submission",
 *     "https://www.drupal.org/link-relations/create" = "/api_store/jira_submission"
 *   }
 * )
 */
class ApiStoreJiraSubmission extends ResourceBase {
  /**
   * Responds to entity POST requests.
   * @return \Drupal\rest\JsonResponse
   */
  public function post($data) {
    $http_client = \Drupal::httpClient();
    $current_user = \Drupal::currentUser();
    $user = \Drupal\user\Entity\User::load($current_user->id());
    $email = $user->getEmail();
	
	if (!empty($email)){
		$email = $data->email->value;
	}
	
    $config = \Drupal::config('api_store.settings');
    $jira_endpoint = $config->get('jira_endpoint');

    $res = $http_client->request('POST', $jira_endpoint, [
		'headers' => [
			'Content-type' => 'application/x-www-form-urlencoded'
		],
        'form_params' => [
            'user' => $email,
            'email' => $email,
			'summary' => $data->summary->value,
			'description' => $data->description->value
        ]
    ]);

    $output = $res->getBody();

    return new JsonResponse( json_decode($output) );
  }
}
