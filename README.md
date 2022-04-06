# twitch_api_scopes
manage for twitch api scopes

## How to use

put suitable location in your service and, test example is...

```php
// instance
$scope = new Scope();

// set scope which you wanna use to true. "setMods()" is for multiple params.
$scope->setMod('chat:read', true);
$scope->setMod('chat:edit', true);

$listOfActiveScopes = $scope->getScopes(true);
foreach($listOfActiveScopes as $value) {
    echo $value,"\n";
}

// Please write in API params.
// This class has __toString() feature
echo (string)$scope;
//            $response = $this->client->request( 'POST', self::TOKEN_PATH, [
//                'headers'         => [],
//                'query'           => [
//                    'client_id'     => $this->clientId,
//                    'client_secret' => $this->clientSecret,
//                    'grant_type'    => 'client_credentials',
//                    'scope'         => (string)$scope,
//                ],
//            ]);
```
