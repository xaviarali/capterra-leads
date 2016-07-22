<?php
/************************************************************************************************
 *
 *  PHP script , which fetches data from www.capeterra.com/ and stores into a csv file format.
 *  Developed & Written By: Zaaviar Ali
 *                   Email: xylex@cenedex.com 
 *                          Cenedex Solutions Inc.
 * 
 ************************************************************************************************/
 // start of php script
 // include the helper file
 // the header file contains the function fetchHTML()
 // whose purpose in life is to get html of the provided url
  require('helper.php');
 
 // create a two file handlers for writing csv files
 // for storing usa & canada companies
  $file1 = fopen('usaAndcanada.csv','w');
 // for storing non us & canada companies
  $file2 = fopen('Non-usaAndcanada.csv','w');
  
 // writing header to the csv file
 fputcsv($file1,array('Category','Company Name','Capeterra Profile URL','Company Info','Company Details','Cloud','Country','Website'));
 // writing header to the csv file
 fputcsv($file2,array('Category','Company Name','Capeterra Profile URL','Company Info','Company Details','Cloud','Country','Website'));
 
 // url of capterra's home page 
  $url = "http://www.capterra.com/";
 // fetch list of all categories  from the   url: http://www.capterra.com/
  $CATEGORIES = fetchHTML($url."categories/");
 // error checking , incase if html couldnt be fetched
  if($CATEGORIES === false)
  {
      // scream if categories list couldn't be fetched
      echo "List of Categories couldn't be fetched";
      // dont proceed any further, exit
      return;
  }
  // create an empty array for storing categories
  $CAT_LIST = [];                                                        
  // scrap the list of all categories using REGEX
  if(preg_match_all('@<a href="\s*(.+)">\s*(.+)</a>@',$CATEGORIES,$CAT_LIST) === false)
  {
    // scream if categories list couldn't be fetched
    echo "Categories Mismatched";
    // dont proceed any further, exit
    return;
  }

  // iterate over all the categories
  // why not starting with index 0
  // the categories list fetched before
  // contains some meta-data in the first two indexes
   for($i = 2; $i < 432; $i++)
   {
         // for each categories fetch list of all companies
       $COMPANIES = fetchHTML($url . $CAT_LIST[1][$i]);
       // error-checking
       if($COMPANIES === false)
       {
          // scream if any category page couldn't be fetched
          echo "CAT Page couldn't be fetched.";
          // dont proceed further instead move to the next category
          continue;
       }
       // create an empty array which will store list of all companies from a particular category
       $COM_LIST = [];   
       // scrap list of all companies using REGEX
       if(preg_match_all('@<a href="([^"]+)" class="spotlight-link"@',$COMPANIES,$COM_LIST) === false)
       {
          // scream if companies list couldn't be fetched
          echo "COMPANY LIST couldn't be scraped."; 
          // dont proceed further instead move to the next category
          continue;
       }
       // the result scraped out of category page is multi-demensional 
       // as we are scraping html
       // we get two dimensions, in one dimenstion we get html tags and in other dimenstion
       // we get list of all companies of the particular categoriess
       // we create a variable which stores the number of companies
        $len = count($COM_LIST[1]);
       // a for-loop for iterating over capterra profile page
       // and scrap data out of them
       for($k = 0; $k < $len; $k++)
       {
           // create a variable to store company's capterra profile
           $PROFILE_URL = "http://www.capterra.com".$COM_LIST[1][$k];
           // get each company's page
           $COM_PAGE = fetchHTML("http://www.capterra.com".$COM_LIST[1][$k]);
           // error checking
           if($COM_PAGE === false)
           {
              // scream character a character Y, which conveys a message to the developer that
              // company's capterra profile couldn't be loaded
              echo "Y";
              // dont proceed further instead move to the next company from the list
              continue;
           }  
           // create a variable to store company's info
           $COMPANY_INFO ="N/A";  
           // scrap company's info using REGEX
           if(preg_match('@<p>\s*(.+)</p>@',$COM_PAGE,$COM_INFO)) $COMPANY_INFO = $COM_INFO[1];
           
           // check whether company is on cloud
           $COMPANY_TYPE = "Not Supported"; 
           // create a variable to store company's platform support i.e Cloud , Destkop , Mobile
           $csupport = [];
           // check whether the company is on Cloud or not
           if (preg_match_all('@[Cc]loud@', $COM_PAGE, $csupport))
           {
              // scream character a character Y, which conveys a message to the developer that
              // company is already on the Cloud
              echo "x";
              // dont proceed further instead move to the next company from the list
              continue;
           }
           
            //scrap company's name from its profile page
           $COMPANY_NAME = [];
           // scrap company's name using regex
           if(!preg_match('@<h1 class="beta  no-margin-bottom" itemprop="name">\s*(.+)</h1>@',$COM_PAGE,$COMPANY_NAME))  
           // as company's couldn't be fetched so assign N/A as its value
           $COMPANY_NAME = "N/A";           
           
           // scrap about vendor info
           $VENDOR_STR = "N/A";
           // create a variable to store company's website
           $WEBSITE = "NA";
           // create a variable to store company's orgin-country
           $COUNTRY = "N/A";
           // scrap company's info using regex
           if(preg_match_all('@<li>\s*(.+)</li>@',$COM_PAGE,$VENDOR_INFO)) 
           {
                // applying some math to determine company's info data
                $len = count($VENDOR_INFO[1]) - 25;
                // if some data was scraped
                if($len > 0)
                {
                    // create a variable to store complete string of company's info
                    $VENDOR_STR = "";
                    // loop to concatenate all company's info
                    for($h = 0;$h < $len ;$h++)
                    // concatenate all company's info
                    // for pretty-print
                    // add pipe character at the start and end
                        $VENDOR_STR = $VENDOR_STR . ' | '. $VENDOR_INFO[1][$h];
                }
                // scraping companies website
                if($len >= 2 && $VENDOR_INFO[1][1][0] == 'w' && $VENDOR_INFO[1][1][1] == 'w' && $VENDOR_INFO[1][1][2] == 'w')
                {
                     // store company's Website scraped from its Capeterra Profile Page
                     $WEBSITE = $VENDOR_INFO[1][1];
                } 
           }
           // create a bool type variable for identifying the country
           $flag = FALSE;
           // checking wether the company is from USA/CN or not
           if(preg_match('@United States|Canada@',$COM_PAGE, $cnt)){
               // create a variable to store the country i.e US or CN
               $COUNTRY = $cnt[0];
               // turn the flag to true
               $flag = TRUE;
           }               
           // create an array which holds all the information about the company
           $LIST = array(
                            $CAT_LIST[2][$i] , $COMPANY_NAME[1],$PROFILE_URL, html_entity_decode($VENDOR_STR),html_entity_decode($COMPANY_INFO), $COMPANY_TYPE,$COUNTRY,$WEBSITE
                        );
           // putting it into csv file
           // check for flags value
           // flag is true if Country is US or CN
           if($flag)
           // write data to the file1 i.e USA and CN entries
             fputcsv($file1,$LIST);
           else 
           // write data to the file1 i.e non USA and CN entries
              fputcsv($file2,$LIST);
           
           // scream character a character '.', which conveys a message to the developer that
           // company's capterra profile is not on the Cloud
             echo ".";
       } 
   }

// end of php script 
?>