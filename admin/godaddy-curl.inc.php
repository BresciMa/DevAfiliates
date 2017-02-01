<?php
curl_setopt ($ch, CURLOPT_PROXYTYPE, CURLPROXY_HTTP);  
curl_setopt ($ch, CURLOPT_PROXY, 'http://proxy.shr.secureserver.net:3128');  
curl_setopt ($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
?>