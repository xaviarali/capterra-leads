<?php
/******************************************************************
 *
 *  helper.php
 *   - contains helper functions for fetching data,etc.
 *   Developed by: Zaaviar Ali
 *          Email: xylex@cenedex.com
 *                 Cenedex Solutions  
 *
 * ***************************************************************/
      /*
       * takes url as an argument
       * Returns: Html String or false on error
       */
       function fetchHTML($url)
       {
           // intialize a cURL object
           $ch = curl_init($url); 
           // set-up data-return arguemt
           curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
          // activate binary-transfer 
           curl_setopt($ch, CURLOPT_BINARYTRANSFER, true); 
          // turn off SSL verification
           curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE); 
          // fetch the html and store it into an object 
           $result = curl_exec($ch); 
          // close the request
           curl_close($ch);
           // return the html or false in case of error
           return $result;  
       }

  ?>