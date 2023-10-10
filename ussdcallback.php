<?php
// Read the variables sent via POST from our API
$sessionId = $_POST["sessionId"];
$serviceCode = $_POST["serviceCode"];
$phoneNumber = $_POST["phoneNumber"];
$text = $_POST["text"];


// Function to fetch token balances from API
function fetchTokenBalances($phoneNumber) {
  $url = "https://mocha-api.vercel.app/api/balance?phone=$phoneNumber&display=SMS";
  $response = file_get_contents($url);
  $data = json_decode($response, true);

  if (isset($data['tokenName'])) {
    $tokenBalances = array();

    foreach ($data['tokenName'] as $token) {
      $tokenBalances[] = [
        'amount' => $token['tokenAmount'],
        'decimals' => $token['tokenDecimals'],
      ];
    }

    return $tokenBalances;
  }

  return array(); // Return an empty array if no token balances found
}

// Function to fetch token names from their mint addresses
function fetchTokenNames($tokenBalances) {
  $tokenNames = array();

  foreach ($tokenBalances as $balance) {
    $tokenNames[] = [
      'amount' => $balance['tokenAmount'],
      'decimals' => $balance['tokenDecimals'],
      'name' => fetchTokenName($balance['tokenName']), // Fetch the token name
    ];
  }

  return $tokenNames;
}

// Function to fetch the name of a token from its mint address
function fetchTokenName($mintAddress) {
    $phoneNumber = $_POST["phoneNumber"];
  $apiUrl = "https://mocha-api.vercel.app/api/balance?phone=$phoneNumber&display=SMS";
  $response = file_get_contents($apiUrl);

  if ($response === false) {
    return 'Unknown Token'; // Handle the case where the API request fails
  }

  $data = json_decode($response, true);

  // Check if the API response contains 'tokens' data
  if (isset($data['tokenName']) && is_array($data['tokenName'])) {
    $tokenNameMapping = array();

    // Iterate through the tokens in the API response
    foreach ($data['tokenName'] as $token) {
      // Use the mint address as the key and the token name as the value in the mapping
      $tokenNameMapping[$token['tokenName']] = $token['tokenName'];
    }

    // Check if the provided mint address exists in the mapping
    if (isset($tokenNameMapping[$mintAddress])) {
      return $tokenNameMapping[$mintAddress];
    }
  }

  return 'Unknown Token'; // Return 'Unknown Token' if the mint address is not found in the mapping or if the API response doesn't contain the expected data
}
    
if ($text == "") {
    // This is the first request. Note how we start the response with CON
    $response  = "CON Welcome to ChristePay \n";
    $response .= "1. Check Balance \n";
    $response .= "2. Check Wallet Address \n";
    $response .= "3. Make a Transfer \n";

} 

//------------ First Level Response from the User ------------>

else if ($text == "1") {
 // Fetch all token balances
 $url = "https://mocha-api.vercel.app/api/balance?phone=$phoneNumber&display=SMS";
 $tokenBalances = fetchTokenBalances($phoneNumber);
 $tokenNames = fetchTokenNames($tokenBalances);
 $responseText = "Your request is successful. You will receive a confirmation by SMS \n";
 $response = "END " . $responseText;

 foreach ($tokenBalances as $balance) {      
     $response .= "{$token['tokenName']}: {$token['tokenAmount']}\n";
 }
}

  

//----------- Second Level Response From the user ------------>

else if ($text == "2") {

// Get the contents from the API
$url = "https://mocha-api.vercel.app/api/me?phone=$phoneNumber";
$response = file_get_contents($url);

// Decode the JSON response
$data = json_decode($response, true);

// Extract the address
$address = $data['address'];

// Print the address
//echo $address;
    // Business logic for the Second level response
    $responseText2 = "Your request is successful. You will receive a confirmation by SMS \n";
    $response = "END " . $responseText2;
}




// Echo the response back to the API
header('Content-type: text/plain');
echo $response;