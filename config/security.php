<?php

// if working locally, don't authenticate
$whitelist = array(
  '127.0.0.1',
  '::1'
);
if(!in_array($_SERVER['REMOTE_ADDR'], $whitelist)){ // working remotely. authenticating.
  if (!True) { // if not on list of authorized people
    header('Location: /notauthorized.html');
    exit;
  }
  /* If I need to check if they have cookie at all...
  header('Location: https://login.umn.edu/idp/profile/SAML2/Redirect/SSO?execution=e2s1');
  exit;
  */
}

?>
