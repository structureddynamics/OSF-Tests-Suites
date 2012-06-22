<?php
    //require_once("C:/Program Files (x86)/NuSphere/PhpED/php53/PEAR/PHPUnit/Autoload.php");
    
    include_once("../tests/Config.php");
    include_once("../tests/validators.php");
    include_once("../tests/utilities.php");
    
    ini_set("memory_limit","256M");
    set_time_limit(3600);

    $settings = new Config(); 
    
    // Database connectivity procedures
    include_once($settings->structwsfInstanceFolder . "framework/WebServiceQuerier.php");
    
    class AuthListerTest extends PHPUnit_Framework_TestCase {
      
        static private $outputs = array();
        
        public function testWrongEndpointUrl() {
          
          $settings = new Config();          
          
          $wsq = new WebServiceQuerier($settings->endpointUrl . "auth/lister/" . "wrong", 
                                       "get", 
                                       "text/xml",
                                       "mode=" . urlencode("dataset") .
                                       "&dataset=" . urlencode($settings->testDataset) .
                                       "&target_webservice=" . urlencode("all") .
                                       "&registered_ip=" . urlencode("self"));
                                       
          $this->assertEquals($wsq->getStatus(), "404", "Debugging information: ".var_export($wsq, TRUE));                                       
          $this->assertEquals($wsq->getStatusMessage(), "Not Found", "Debugging information: ".var_export($wsq, TRUE));
          
          unset($wsq);
          unset($settings);
          
          //self::$outputs = array_merge(self::$outputs, array(var_export($wsq, TRUE)));
        }
        
        public function testWrongEndpointMethodPost() {
          
          $settings = new Config();  
          
          deleteDataset();
          
          $this->assertTrue(createDataset(), "Can't create the dataset, check the /dataset/create/ endpoint first...");
          
          $wsq = new WebServiceQuerier($settings->endpointUrl . "auth/lister/", 
                                       "post", 
                                       "text/xml",
                                       "mode=" . urlencode("dataset") .
                                       "&dataset=" . urlencode($settings->testDataset) .
                                       "&target_webservice=" . urlencode("all") .
                                       "&registered_ip=" . urlencode("self"));
                                       
          $this->assertEquals($wsq->getStatus(), "405", "Debugging information: ".var_export($wsq, TRUE));                                       
          $this->assertEquals($wsq->getStatusMessage(), "Method Not Allowed", "Debugging information: ".var_export($wsq, TRUE));          

          deleteDataset();
          
          unset($wsq);
          unset($settings);
        }   
        
        public function testUnsupportedSerializationMime() {
          
          $settings = new Config();  
          
          deleteDataset();
          
          $this->assertTrue(createDataset(), "Can't create the dataset, check the /dataset/create/ endpoint first...");
          
          $wsq = new WebServiceQuerier($settings->endpointUrl . "auth/lister/", 
                                       "get", 
                                       "text/xml"."not+supported",
                                       "mode=" . urlencode("dataset") .
                                       "&dataset=" . urlencode($settings->testDataset) .
                                       "&target_webservice=" . urlencode("all") .
                                       "&registered_ip=" . urlencode("self"));
                                       
          $this->assertEquals($wsq->getStatus(), "406", "Debugging information: ".var_export($wsq, TRUE));                                       
          $this->assertEquals($wsq->getStatusMessage(), "Not Acceptable", "Debugging information: ".var_export($wsq, TRUE));          

          deleteDataset();
          
          unset($wsq);
          unset($settings);
        }                   
        
        //
        // Test all serializations of the mode=dataset param
        //
        
        public function testParameter_Mode_Dataset_Serialization_TEXT_XML() {
          
          $settings = new Config();  

          deleteDataset();
          
          $this->assertTrue(createDataset(), "Can't create the dataset, check the /dataset/create/ endpoint first...");
          
          $wsq = new WebServiceQuerier($settings->endpointUrl . "auth/lister/", 
                                       "get", 
                                       "text/xml",
                                       "mode=" . urlencode("dataset") .
                                       "&dataset=" . urlencode($settings->testDataset) .
                                       "&target_webservice=" . urlencode("all") .
                                       "&registered_ip=" . urlencode("self"));
                                
          validateParameterTextXml($this, $wsq);

          deleteDataset();

          unset($wsq);
          unset($settings);
        }  
        
        public function testParameter_Mode_Dataset_Serialization_APPLICATION_JSON() {
          
          $settings = new Config();  
          
          deleteDataset();
          
          $this->assertTrue(createDataset(), "Can't create the dataset, check the /dataset/create/ endpoint first...");
          
          $wsq = new WebServiceQuerier($settings->endpointUrl . "auth/lister/", 
                                       "get", 
                                       "application/json",
                                       "mode=" . urlencode("dataset") .
                                       "&dataset=" . urlencode($settings->testDataset) .
                                       "&target_webservice=" . urlencode("all") .
                                       "&registered_ip=" . urlencode("self"));
                                
          validateParameterApplicationJson($this, $wsq);
          
          deleteDataset();
          
          unset($wsq);
          unset($settings);
        }   
        
        public function testParameter_Mode_Dataset_Serialization_APPLICATION_RDF_XML() {
          
          $settings = new Config();  

          deleteDataset();
          
          $this->assertTrue(createDataset(), "Can't create the dataset, check the /dataset/create/ endpoint first...");
          
          $wsq = new WebServiceQuerier($settings->endpointUrl . "auth/lister/", 
                                       "get", 
                                       "application/rdf+xml",
                                       "mode=" . urlencode("dataset") .
                                       "&dataset=" . urlencode($settings->testDataset) .
                                       "&target_webservice=" . urlencode("all") .
                                       "&registered_ip=" . urlencode("self"));
                                
          validateParameterApplicationRdfXml($this, $wsq);

          deleteDataset();
          
          unset($wsq);
          unset($settings);
        }       
                          
        public function testParameter_Mode_Dataset_Serialization_APPLICATION_RDF_N3() {
          
          $settings = new Config();  
          
          deleteDataset();
          
          $this->assertTrue(createDataset(), "Can't create the dataset, check the /dataset/create/ endpoint first...");
          
          $wsq = new WebServiceQuerier($settings->endpointUrl . "auth/lister/", 
                                       "get", 
                                       "application/rdf+n3",
                                       "mode=" . urlencode("dataset") .
                                       "&dataset=" . urlencode($settings->testDataset) .
                                       "&target_webservice=" . urlencode("all") .
                                       "&registered_ip=" . urlencode("self"));
                                
          validateParameterApplicationRdfN3($this, $wsq);

          deleteDataset();
          
          unset($wsq);
          unset($settings);
        }  
        
        //
        // Test all serializations of the mode=ws param
        //
        
        public function testParameter_Mode_Ws_Serialization_TEXT_XML() {
          
          $settings = new Config();  
          
          deleteDataset();
          
          $this->assertTrue(createDataset(), "Can't create the dataset, check the /dataset/create/ endpoint first...");
          
          $wsq = new WebServiceQuerier($settings->endpointUrl . "auth/lister/", 
                                       "get", 
                                       "text/xml",
                                       "mode=" . urlencode("ws") .
                                       "&dataset=" . urlencode($settings->testDataset) .
                                       "&target_webservice=" . urlencode("all") .
                                       "&registered_ip=" . urlencode("self"));
                                
          validateParameterTextXml($this, $wsq);

          deleteDataset();
          
          unset($wsq);
          unset($settings);
        }  
        
        public function testParameter_Mode_Ws_Serialization_APPLICATION_JSON() {
          
          $settings = new Config();  

          deleteDataset();
          
          $this->assertTrue(createDataset(), "Can't create the dataset, check the /dataset/create/ endpoint first...");
          
          $wsq = new WebServiceQuerier($settings->endpointUrl . "auth/lister/", 
                                       "get", 
                                       "application/json",
                                       "mode=" . urlencode("ws") .
                                       "&dataset=" . urlencode($settings->testDataset) .
                                       "&target_webservice=" . urlencode("all") .
                                       "&registered_ip=" . urlencode("self"));
                                
          validateParameterApplicationJson($this, $wsq);

          deleteDataset();
          
          unset($wsq);
          unset($settings);
        }   
        
        public function testParameter_Mode_Ws_Serialization_APPLICATION_RDF_XML() {
          
          $settings = new Config();  

          deleteDataset();
          
          $this->assertTrue(createDataset(), "Can't create the dataset, check the /dataset/create/ endpoint first...");
          
          $wsq = new WebServiceQuerier($settings->endpointUrl . "auth/lister/", 
                                       "get", 
                                       "application/rdf+xml",
                                       "mode=" . urlencode("ws") .
                                       "&dataset=" . urlencode($settings->testDataset) .
                                       "&target_webservice=" . urlencode("all") .
                                       "&registered_ip=" . urlencode("self"));
                                
          validateParameterApplicationRdfXml($this, $wsq);
          
          unset($wsq);
          unset($settings);
        }       
                          
        public function testParameter_Mode_Ws_Serialization_APPLICATION_RDF_N3() {
          
          $settings = new Config();  

          deleteDataset();
          
          $this->assertTrue(createDataset(), "Can't create the dataset, check the /dataset/create/ endpoint first...");
          
          $wsq = new WebServiceQuerier($settings->endpointUrl . "auth/lister/", 
                                       "get", 
                                       "application/rdf+n3",
                                       "mode=" . urlencode("ws") .
                                       "&dataset=" . urlencode($settings->testDataset) .
                                       "&target_webservice=" . urlencode("all") .
                                       "&registered_ip=" . urlencode("self"));
                                
          validateParameterApplicationRdfN3($this, $wsq);

          deleteDataset();
          
          unset($wsq);
          unset($settings);
        }  
        
        //
        // Test all serializations of the mode=access_dataset param
        //
        
        public function testParameter_Mode_AccessDataset_TargetWebservice_All_Serialization_TEXT_XML() {
          
          $settings = new Config();  

          deleteDataset();
          
          $this->assertTrue(createDataset(), "Can't create the dataset, check the /dataset/create/ endpoint first...");
          
          $wsq = new WebServiceQuerier($settings->endpointUrl . "auth/lister/", 
                                       "get", 
                                       "text/xml",
                                       "mode=" . urlencode("access_dataset") .
                                       "&dataset=" . urlencode($settings->testDataset) .
                                       "&target_webservice=" . urlencode("all") .
                                       "&registered_ip=" . urlencode("self"));
                 
          validateParameterTextXml($this, $wsq);
         
          deleteDataset();          
          
          unset($wsq);
          unset($settings);
        }  
        
        public function testParameter_Mode_AccessDataset_TargetWebservice_All_Serialization_APPLICATION_JSON() {
                                               
          $settings = new Config();  
          
          deleteDataset();
          
          $this->assertTrue(createDataset(), "Can't create the dataset, check the /dataset/create/ endpoint first...");          
          
          $wsq = new WebServiceQuerier($settings->endpointUrl . "auth/lister/", 
                                       "get", 
                                       "application/json",
                                       "mode=" . urlencode("access_dataset") .
                                       "&dataset=" . urlencode($settings->testDataset) .
                                       "&target_webservice=" . urlencode("all") .
                                       "&registered_ip=" . urlencode("self"));
                                
          validateParameterApplicationJson($this, $wsq);
          
          deleteDataset();
          
          unset($wsq);
          unset($settings);
        }   
        
        public function testParameter_Mode_AccessDataset_TargetWebservice_All_Serialization_APPLICATION_RDF_XML() {
          
          $settings = new Config();  

          deleteDataset();
          
          $this->assertTrue(createDataset(), "Can't create the dataset, check the /dataset/create/ endpoint first...");
          
          $wsq = new WebServiceQuerier($settings->endpointUrl . "auth/lister/", 
                                       "get", 
                                       "application/rdf+xml",
                                       "mode=" . urlencode("access_dataset") .
                                       "&dataset=" . urlencode($settings->testDataset) .
                                       "&target_webservice=" . urlencode("all") .
                                       "&registered_ip=" . urlencode("self"));
                                
          validateParameterApplicationRdfXml($this, $wsq);
          
          deleteDataset();
          
          unset($wsq);
          unset($settings);
        }       
                          
        public function testParameter_Mode_AccessDataset_TargetWebservice_All_Serialization_APPLICATION_RDF_N3() {
          
          $settings = new Config();  
          
          deleteDataset();
          
          $this->assertTrue(createDataset(), "Can't create the dataset, check the /dataset/create/ endpoint first...");
          
          $wsq = new WebServiceQuerier($settings->endpointUrl . "auth/lister/", 
                                       "get", 
                                       "application/rdf+n3",
                                       "mode=" . urlencode("access_dataset") .
                                       "&dataset=" . urlencode($settings->testDataset) .
                                       "&target_webservice=" . urlencode("all") .
                                       "&registered_ip=" . urlencode("self"));
                                
          validateParameterApplicationRdfN3($this, $wsq);

          deleteDataset();
          
          unset($wsq);
          unset($settings);
        }
        
        public function testParameter_Mode_AccessDataset_TargetWebservice_None_Serialization_TEXT_XML() {
          
          $settings = new Config();  

          deleteDataset();
          
          $this->assertTrue(createDataset(), "Can't create the dataset, check the /dataset/create/ endpoint first...");
          
          $wsq = new WebServiceQuerier($settings->endpointUrl . "auth/lister/", 
                                       "get", 
                                       "text/xml",
                                       "mode=" . urlencode("access_dataset") .
                                       "&dataset=" . urlencode($settings->testDataset) .
                                       "&target_webservice=" . urlencode("none") .
                                       "&registered_ip=" . urlencode("self"));
                 
          validateParameterTextXml($this, $wsq);
         
          deleteDataset();          
          
          unset($wsq);
          unset($settings);
        }  
        
        public function testParameter_Mode_AccessDataset_TargetWebservice_None_Serialization_APPLICATION_JSON() {
                                               
          $settings = new Config();  
          
          deleteDataset();
          
          $this->assertTrue(createDataset(), "Can't create the dataset, check the /dataset/create/ endpoint first...");          
          
          $wsq = new WebServiceQuerier($settings->endpointUrl . "auth/lister/", 
                                       "get", 
                                       "application/json",
                                       "mode=" . urlencode("access_dataset") .
                                       "&dataset=" . urlencode($settings->testDataset) .
                                       "&target_webservice=" . urlencode("none") .
                                       "&registered_ip=" . urlencode("self"));
                                
          validateParameterApplicationJson($this, $wsq);
          
          deleteDataset();
          
          unset($wsq);
          unset($settings);
        }   
        
        public function testParameter_Mode_AccessDataset_TargetWebservice_None_Serialization_APPLICATION_RDF_XML() {
          
          $settings = new Config();  

          deleteDataset();
          
          $this->assertTrue(createDataset(), "Can't create the dataset, check the /dataset/create/ endpoint first...");
          
          $wsq = new WebServiceQuerier($settings->endpointUrl . "auth/lister/", 
                                       "get", 
                                       "application/rdf+xml",
                                       "mode=" . urlencode("access_dataset") .
                                       "&dataset=" . urlencode($settings->testDataset) .
                                       "&target_webservice=" . urlencode("none") .
                                       "&registered_ip=" . urlencode("self"));
                                
          validateParameterApplicationRdfXml($this, $wsq);
          
          deleteDataset();
          
          unset($wsq);
          unset($settings);
        }       
                          
        public function testParameter_Mode_AccessDataset_TargetWebservice_None_Serialization_APPLICATION_RDF_N3() {
          
          $settings = new Config();  
          
          deleteDataset();
          
          $this->assertTrue(createDataset(), "Can't create the dataset, check the /dataset/create/ endpoint first...");
          
          $wsq = new WebServiceQuerier($settings->endpointUrl . "auth/lister/", 
                                       "get", 
                                       "application/rdf+n3",
                                       "mode=" . urlencode("access_dataset") .
                                       "&dataset=" . urlencode($settings->testDataset) .
                                       "&target_webservice=" . urlencode("none") .
                                       "&registered_ip=" . urlencode("self"));
                                
          validateParameterApplicationRdfN3($this, $wsq);

          deleteDataset();
          
          unset($wsq);
          unset($settings);
        }        
        
        public function testParameter_Mode_AccessDataset_TargetWebservice_TargetWS_Serialization_TEXT_XML() {
          
          $settings = new Config();  

          deleteDataset();
          
          $this->assertTrue(createDataset(), "Can't create the dataset, check the /dataset/create/ endpoint first...");
          
          $wsq = new WebServiceQuerier($settings->endpointUrl . "auth/lister/", 
                                       "get", 
                                       "text/xml",
                                       "mode=" . urlencode("access_dataset") .
                                       "&dataset=" . urlencode($settings->testDataset) .
                                       "&target_webservice=" . urlencode($settings->endpointUri."crud/create/") .
                                       "&registered_ip=" . urlencode("self"));
                 
          validateParameterTextXml($this, $wsq);
         
          deleteDataset();          
          
          unset($wsq);
          unset($settings);
        }  
        
        public function testParameter_Mode_AccessDataset_TargetWebservice_TargetWS_Serialization_APPLICATION_JSON() {
                                               
          $settings = new Config();  
          
          deleteDataset();
          
          $this->assertTrue(createDataset(), "Can't create the dataset, check the /dataset/create/ endpoint first...");          
          
          $wsq = new WebServiceQuerier($settings->endpointUrl . "auth/lister/", 
                                       "get", 
                                       "application/json",
                                       "mode=" . urlencode("access_dataset") .
                                       "&dataset=" . urlencode($settings->testDataset) .
                                       "&target_webservice=" . urlencode($settings->endpointUri."crud/create/") .
                                       "&registered_ip=" . urlencode("self"));
                                
          validateParameterApplicationJson($this, $wsq);
          
          deleteDataset();
          
          unset($wsq);
          unset($settings);
        }   
        
        public function testParameter_Mode_AccessDataset_TargetWebservice_TargetWS_Serialization_APPLICATION_RDF_XML() {
          
          $settings = new Config();  

          deleteDataset();
          
          $this->assertTrue(createDataset(), "Can't create the dataset, check the /dataset/create/ endpoint first...");
          
          $wsq = new WebServiceQuerier($settings->endpointUrl . "auth/lister/", 
                                       "get", 
                                       "application/rdf+xml",
                                       "mode=" . urlencode("access_dataset") .
                                       "&dataset=" . urlencode($settings->testDataset) .
                                       "&target_webservice=" . urlencode($settings->endpointUri."crud/create/") .
                                       "&registered_ip=" . urlencode("self"));
                                
          validateParameterApplicationRdfXml($this, $wsq);
          
          deleteDataset();
          
          unset($wsq);
          unset($settings);
        }       
                          
        public function testParameter_Mode_AccessDataset_TargetWebservice_TargetWS_Serialization_APPLICATION_RDF_N3() {
          
          $settings = new Config();  
          
          deleteDataset();
          
          $this->assertTrue(createDataset(), "Can't create the dataset, check the /dataset/create/ endpoint first...");
          
          $wsq = new WebServiceQuerier($settings->endpointUrl . "auth/lister/", 
                                       "get", 
                                       "application/rdf+n3",
                                       "mode=" . urlencode("access_dataset") .
                                       "&dataset=" . urlencode($settings->testDataset) .
                                       "&target_webservice=" . urlencode($settings->endpointUri."crud/create/") .
                                       "&registered_ip=" . urlencode("self"));
                                
          validateParameterApplicationRdfN3($this, $wsq);

          deleteDataset();
          
          unset($wsq);
          unset($settings);
        }        
        
        //
        // Test all serializations of the mode=access_user param
        //
        
        public function testParameter_Mode_AccessUser_TargetWebservice_All_Serialization_TEXT_XML() {
          
          $settings = new Config();  
          
          deleteDataset();
          
          $this->assertTrue(createDataset(), "Can't create the dataset, check the /dataset/create/ endpoint first...");
          
          $wsq = new WebServiceQuerier($settings->endpointUrl . "auth/lister/", 
                                       "get", 
                                       "text/xml",
                                       "mode=" . urlencode("access_user") .
                                       "&dataset=" . urlencode($settings->testDataset) .
                                       "&target_webservice=" . urlencode("all") .
                                       "&registered_ip=" . urlencode("self"));
                                
          validateParameterTextXml($this, $wsq);
          
          deleteDataset();
          
          unset($wsq);
          unset($settings);
        }  
        
        public function testParameter_Mode_AccessUser_TargetWebservice_All_Serialization_APPLICATION_JSON() {
          
          $settings = new Config();  
          
          deleteDataset();
          
          $this->assertTrue(createDataset(), "Can't create the dataset, check the /dataset/create/ endpoint first...");
          
          $wsq = new WebServiceQuerier($settings->endpointUrl . "auth/lister/", 
                                       "get", 
                                       "application/json",
                                       "mode=" . urlencode("access_user") .
                                       "&dataset=" . urlencode($settings->testDataset) .
                                       "&target_webservice=" . urlencode("all") .
                                       "&registered_ip=" . urlencode("self"));
                                
          validateParameterApplicationJson($this, $wsq);
          
          deleteDataset();
          
          unset($wsq);
          unset($settings);
        }   
         
        public function testParameter_Mode_AccessUser_TargetWebservice_All_Serialization_APPLICATION_RDF_XML() {

          $settings = new Config();  
          
          deleteDataset();
          
          $this->assertTrue(createDataset(), "Can't create the dataset, check the /dataset/create/ endpoint first...");

          $wsq = new WebServiceQuerier($settings->endpointUrl . "auth/lister/", 
                                       "get", 
                                       "application/rdf+xml",
                                       "mode=" . urlencode("access_user") .
                                       "&dataset=" . urlencode($settings->testDataset) .
                                       "&target_webservice=" . urlencode("all") .
                                       "&registered_ip=" . urlencode("self"));
                     
          validateParameterApplicationRdfXml($this, $wsq);

          deleteDataset();
          
          unset($wsq);
          unset($settings);
        }       
                          
        public function testParameter_Mode_AccessUser_TargetWebservice_All_Serialization_APPLICATION_RDF_N3() {

          $settings = new Config();  
          
          deleteDataset();
          
          $this->assertTrue(createDataset(), "Can't create the dataset, check the /dataset/create/ endpoint first...");
          
          $wsq = new WebServiceQuerier($settings->endpointUrl . "auth/lister/", 
                                       "get", 
                                       "application/rdf+n3",
                                       "mode=" . urlencode("access_user") .
                                       "&dataset=" . urlencode($settings->testDataset) .
                                       "&target_webservice=" . urlencode("all") .
                                       "&registered_ip=" . urlencode("self"));
                                
          validateParameterApplicationRdfN3($this, $wsq);
          
          deleteDataset();
          
          unset($wsq);
          unset($settings);
        }   
        
        public function testParameter_Mode_AccessUser_TargetWebservice_None_Serialization_TEXT_XML() {
          
          $settings = new Config();  
          
          deleteDataset();
          
          $this->assertTrue(createDataset(), "Can't create the dataset, check the /dataset/create/ endpoint first...");
          
          $wsq = new WebServiceQuerier($settings->endpointUrl . "auth/lister/", 
                                       "get", 
                                       "text/xml",
                                       "mode=" . urlencode("access_user") .
                                       "&dataset=" . urlencode($settings->testDataset) .
                                       "&target_webservice=" . urlencode("none") .
                                       "&registered_ip=" . urlencode("self"));
                                
          validateParameterTextXml($this, $wsq);
          
          deleteDataset();
          
          unset($wsq);
          unset($settings);
        }  
        
        public function testParameter_Mode_AccessUser_TargetWebservice_None_Serialization_APPLICATION_JSON() {
          
          $settings = new Config();  
          
          deleteDataset();
          
          $this->assertTrue(createDataset(), "Can't create the dataset, check the /dataset/create/ endpoint first...");
          
          $wsq = new WebServiceQuerier($settings->endpointUrl . "auth/lister/", 
                                       "get", 
                                       "application/json",
                                       "mode=" . urlencode("access_user") .
                                       "&dataset=" . urlencode($settings->testDataset) .
                                       "&target_webservice=" . urlencode("none") .
                                       "&registered_ip=" . urlencode("self"));
                                
          validateParameterApplicationJson($this, $wsq);
          
          deleteDataset();
          
          unset($wsq);
          unset($settings);
        }   
         
        public function testParameter_Mode_AccessUser_TargetWebservice_None_Serialization_APPLICATION_RDF_XML() {

          $settings = new Config();  
          
          deleteDataset();
          
          $this->assertTrue(createDataset(), "Can't create the dataset, check the /dataset/create/ endpoint first...");

          $wsq = new WebServiceQuerier($settings->endpointUrl . "auth/lister/", 
                                       "get", 
                                       "application/rdf+xml",
                                       "mode=" . urlencode("access_user") .
                                       "&dataset=" . urlencode($settings->testDataset) .
                                       "&target_webservice=" . urlencode("none") .
                                       "&registered_ip=" . urlencode("self"));
                     
          validateParameterApplicationRdfXml($this, $wsq);

          deleteDataset();
          
          unset($wsq);
          unset($settings);
        }       
                          
        public function testParameter_Mode_AccessUser_TargetWebservice_None_Serialization_APPLICATION_RDF_N3() {

          $settings = new Config();  
          
          deleteDataset();
          
          $this->assertTrue(createDataset(), "Can't create the dataset, check the /dataset/create/ endpoint first...");
          
          $wsq = new WebServiceQuerier($settings->endpointUrl . "auth/lister/", 
                                       "get", 
                                       "application/rdf+n3",
                                       "mode=" . urlencode("access_user") .
                                       "&dataset=" . urlencode($settings->testDataset) .
                                       "&target_webservice=" . urlencode("none") .
                                       "&registered_ip=" . urlencode("self"));
                                
          validateParameterApplicationRdfN3($this, $wsq);
          
          deleteDataset();
          
          unset($wsq);
          unset($settings);
        }
        
        public function testParameter_Mode_AccessUser_TargetWebservice_TargetWS_Serialization_TEXT_XML() {
          
          $settings = new Config();  
          
          deleteDataset();
          
          $this->assertTrue(createDataset(), "Can't create the dataset, check the /dataset/create/ endpoint first...");
          
          $wsq = new WebServiceQuerier($settings->endpointUrl . "auth/lister/", 
                                       "get", 
                                       "text/xml",
                                       "mode=" . urlencode("access_user") .
                                       "&dataset=" . urlencode($settings->testDataset) .
                                       "&target_webservice=" . urlencode($settings->endpointUri."crud/create/") .
                                       "&registered_ip=" . urlencode("self"));
                                
          validateParameterTextXml($this, $wsq);
          
          deleteDataset();
          
          unset($wsq);
          unset($settings);
        }  
        
        public function testParameter_Mode_AccessUser_TargetWebservice_TargetWS_Serialization_APPLICATION_JSON() {
          
          $settings = new Config();  
          
          deleteDataset();
          
          $this->assertTrue(createDataset(), "Can't create the dataset, check the /dataset/create/ endpoint first...");
          
          $wsq = new WebServiceQuerier($settings->endpointUrl . "auth/lister/", 
                                       "get", 
                                       "application/json",
                                       "mode=" . urlencode("access_user") .
                                       "&dataset=" . urlencode($settings->testDataset) .
                                       "&target_webservice=" . urlencode($settings->endpointUri."crud/create/") .
                                       "&registered_ip=" . urlencode("self"));
                                
          validateParameterApplicationJson($this, $wsq);
          
          deleteDataset();
          
          unset($wsq);
          unset($settings);
        }   
         
        public function testParameter_Mode_AccessUser_TargetWebservice_TargetWS_Serialization_APPLICATION_RDF_XML() {

          $settings = new Config();  
          
          deleteDataset();
          
          $this->assertTrue(createDataset(), "Can't create the dataset, check the /dataset/create/ endpoint first...");

          $wsq = new WebServiceQuerier($settings->endpointUrl . "auth/lister/", 
                                       "get", 
                                       "application/rdf+xml",
                                       "mode=" . urlencode("access_user") .
                                       "&dataset=" . urlencode($settings->testDataset) .
                                       "&target_webservice=" . urlencode($settings->endpointUri."crud/create/") .
                                       "&registered_ip=" . urlencode("self"));
                     
          validateParameterApplicationRdfXml($this, $wsq);

          deleteDataset();
          
          unset($wsq);
          unset($settings);
        }       
                          
        public function testParameter_Mode_AccessUser_TargetWebservice_TargetWS_Serialization_APPLICATION_RDF_N3() {

          $settings = new Config();  
          
          deleteDataset();
          
          $this->assertTrue(createDataset(), "Can't create the dataset, check the /dataset/create/ endpoint first...");
          
          $wsq = new WebServiceQuerier($settings->endpointUrl . "auth/lister/", 
                                       "get", 
                                       "application/rdf+n3",
                                       "mode=" . urlencode("access_user") .
                                       "&dataset=" . urlencode($settings->testDataset) .
                                       "&target_webservice=" . urlencode($settings->endpointUri."crud/create/") .
                                       "&registered_ip=" . urlencode("self"));
                                
          validateParameterApplicationRdfN3($this, $wsq);
          
          deleteDataset();
          
          unset($wsq);
          unset($settings);
        }        
        
        static public function tearDownAfterClass() {
            echo implode("\n", self::$outputs);
        }        
    }
 
?>