<?php
/* WebClient class handle connect web service and get XML data from web service
*
* @Author: Lulin Song created on April 16, 2013
*
* $Id: WebClient.php 863 2013-04-19 18:52:50Z song $
* $LastChangedDate: 2013-04-19 14:52:50 -0400 (Fri, 19 Apr 2013) $
* $LastChangedBy: song $
* $LastChangedRevision: 863 $
*/

class WebClient {
  private $url; //Web service base URI
  protected $params; //Web service params

       //Constructor
       //$webservice_url: Web Service URL such as http://www.earthchemportal.org/citation_stats
       //$urlParams : parameters pass to web services. It is an array.
       //             eg. { 'v'=>'xml','id'=>'345' }
       function __construct( $webservice_url, $urlParams)
       {
           //Assemble the whole url string
           $this->url=$webservice_url;
           $this->params = $urlParams;
           $firstParam=true;
           foreach ($urlParams as $key => $value )
           {
               if( $firstParam )
               {
                 $this->url .= '?'.$key."=".$value; 
                 $firstParam=false;
               }
               else
               {
                 $this->url .= "&".$key."=".$value; 
               }
           }
       }

       public function getSimpleXMLElement()
       {
           $xml=file_get_contents($this->url);
           $data = new SimpleXMLElement($xml);
           return $data;
       }

       //Implement the following so print this class object will work.
       function __toString()
       {
           print $this->url; 
       }
}

?>
