<?php

    namespace StructuredDynamics\structwsf\tests\ws\auth\lister;
    
    use StructuredDynamics\structwsf\framework\WebServiceQuerier;
    use StructuredDynamics\structwsf\php\api\ws\auth\lister\AuthListerQuery;
    use StructuredDynamics\structwsf\tests\Config;
    use StructuredDynamics\structwsf\tests as utilities;
     
    include_once("SplClassLoader.php");
    include_once("validators.php");
    include_once("utilities.php");  
    
    // Load the \tests namespace where all the test code is located 
    $loader_tests = new \SplClassLoader('StructuredDynamics\structwsf\tests', realpath("../../../"));
    $loader_tests->register();
   
    // Load the \ws namespace where all the web service code is located 
    $loader_ws = new \SplClassLoader('StructuredDynamics\structwsf\php\api\ws', realpath("../../../"));
    $loader_ws->register();  
    
    // Load the \php\api\framework namespace where all the web service code is located 
    $loader_ws = new \SplClassLoader('StructuredDynamics\structwsf\php\api\framework', realpath("../../../"));
    $loader_ws->register();  
   
    // Load the \framework namespace where all the supporting (utility) code is located
    $loader_framework = new \SplClassLoader('StructuredDynamics\structwsf\framework', realpath("../../../"));
    $loader_framework->register();      

    ini_set("memory_limit","256M");
    set_time_limit(3600);

    $settings = new Config(); 

    class AuthListerTest extends \PHPUnit_Framework_TestCase {
      
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
          
          utilities\deleteDataset();
          
          $this->assertTrue(utilities\createDataset(), "Can't create the dataset, check the /dataset/create/ endpoint first...");
          
          $wsq = new WebServiceQuerier($settings->endpointUrl . "auth/lister/", 
                                       "post", 
                                       "text/xml",
                                       "mode=" . urlencode("dataset") .
                                       "&dataset=" . urlencode($settings->testDataset) .
                                       "&target_webservice=" . urlencode("all") .
                                       "&registered_ip=" . urlencode("self"));
                                       
          $this->assertEquals($wsq->getStatus(), "405", "Debugging information: ".var_export($wsq, TRUE));                                       
          $this->assertEquals($wsq->getStatusMessage(), "Method Not Allowed", "Debugging information: ".var_export($wsq, TRUE));          

          utilities\deleteDataset();
          
          unset($wsq);
          unset($settings);
        }   
        
        public function testUnsupportedSerializationMime() {
          
          $settings = new Config();  
          
          utilities\deleteDataset();
          
          $this->assertTrue(utilities\createDataset(), "Can't create the dataset, check the /dataset/create/ endpoint first...");
          
          $wsq = new WebServiceQuerier($settings->endpointUrl . "auth/lister/", 
                                       "get", 
                                       "text/xml"."not+supported",
                                       "mode=" . urlencode("dataset") .
                                       "&dataset=" . urlencode($settings->testDataset) .
                                       "&target_webservice=" . urlencode("all") .
                                       "&registered_ip=" . urlencode("self"));
                                       
          $this->assertEquals($wsq->getStatus(), "406", "Debugging information: ".var_export($wsq, TRUE));                                       
          $this->assertEquals($wsq->getStatusMessage(), "Not Acceptable", "Debugging information: ".var_export($wsq, TRUE));          

          utilities\deleteDataset();
          
          unset($wsq);
          unset($settings);
        }                   
        
        //
        // Test all serializations of the mode=dataset param
        //
        
        public function testParameter_Mode_Dataset_Serialization_TEXT_XML() {
          
          $settings = new Config();  

          utilities\deleteDataset();
          
          $this->assertTrue(utilities\createDataset(), "Can't create the dataset, check the /dataset/create/ endpoint first...");
          
          $authLister = new AuthListerQuery($settings->endpointUrl);
          
          $authLister->mime("text/xml");
          
          $authLister->getDatasetsUri($settings->testDataset);
          
          $authLister->includeAllWebServiceUris();
          
          $authLister->registeredIp("self");
          
          $authLister->send();
                               
          utilities\validateParameterTextXml($this, $authLister);

          utilities\deleteDataset();

          unset($authLister);
          unset($settings);
        }  
        
        public function testParameter_Mode_Dataset_Serialization_APPLICATION_JSON() {
          
          $settings = new Config();  
          
          utilities\deleteDataset();
          
          $this->assertTrue(utilities\createDataset(), "Can't create the dataset, check the /dataset/create/ endpoint first...");

          $authLister = new AuthListerQuery($settings->endpointUrl);
          
          $authLister->mime("application/json");
          
          $authLister->getDatasetsUri($settings->testDataset);
          
          $authLister->includeAllWebServiceUris();
          
          $authLister->registeredIp("self");
          
          $authLister->send();
                                
          utilities\validateParameterApplicationJson($this, $authLister);
          
          utilities\deleteDataset();
          
          unset($authLister);
          unset($settings);
        }   
        
        public function testParameter_Mode_Dataset_Serialization_APPLICATION_RDF_XML() {
          
          $settings = new Config();  

          utilities\deleteDataset();
          
          $this->assertTrue(utilities\createDataset(), "Can't create the dataset, check the /dataset/create/ endpoint first...");
          
          $authLister = new AuthListerQuery($settings->endpointUrl);
          
          $authLister->mime("application/rdf+xml");
          
          $authLister->getDatasetsUri($settings->testDataset);
          
          $authLister->includeAllWebServiceUris();
          
          $authLister->registeredIp("self");
          
          $authLister->send();
                                
          utilities\validateParameterApplicationRdfXml($this, $authLister);

          utilities\deleteDataset();
          
          unset($authLister);
          unset($settings);
        }       
                          
        public function testParameter_Mode_Dataset_Serialization_APPLICATION_RDF_N3() {
          
          $settings = new Config();  
          
          utilities\deleteDataset();
          
          $this->assertTrue(utilities\createDataset(), "Can't create the dataset, check the /dataset/create/ endpoint first...");

          $authLister = new AuthListerQuery($settings->endpointUrl);
          
          $authLister->mime("application/rdf+n3");
          
          $authLister->getDatasetsUri($settings->testDataset);
          
          $authLister->includeAllWebServiceUris();
          
          $authLister->registeredIp("self");
          
          $authLister->send();
                                
          utilities\validateParameterApplicationRdfN3($this, $authLister);

          utilities\deleteDataset();
          
          unset($authLister);
          unset($settings);
        }  
        
        //
        // Test all serializations of the mode=ws param
        //
        
        public function testParameter_Mode_Ws_Serialization_TEXT_XML() {
          
          $settings = new Config();  
          
          utilities\deleteDataset();
          
          $this->assertTrue(utilities\createDataset(), "Can't create the dataset, check the /dataset/create/ endpoint first...");
          
          $authLister = new AuthListerQuery($settings->endpointUrl);
          
          $authLister->mime("text/xml");
          
          $authLister->getRegisteredWebServiceEndpointsUri();
          
          $authLister->includeAllWebServiceUris();
          
          $authLister->registeredIp("self");
          
          $authLister->send();
          
          utilities\validateParameterTextXml($this, $authLister);

          utilities\deleteDataset();
          
          unset($authLister);
          unset($settings);
        }  
        
        public function testParameter_Mode_Ws_Serialization_APPLICATION_JSON() {
          
          $settings = new Config();  

          utilities\deleteDataset();
          
          $this->assertTrue(utilities\createDataset(), "Can't create the dataset, check the /dataset/create/ endpoint first...");
          
          $authLister = new AuthListerQuery($settings->endpointUrl);
          
          $authLister->mime("application/json");
          
          $authLister->getRegisteredWebServiceEndpointsUri();
          
          $authLister->includeAllWebServiceUris();
          
          $authLister->registeredIp("self");
          
          $authLister->send();
                                
          utilities\validateParameterApplicationJson($this, $authLister);

          utilities\deleteDataset();
          
          unset($authLister);
          unset($settings);
        }   
        
        public function testParameter_Mode_Ws_Serialization_APPLICATION_RDF_XML() {
          
          $settings = new Config();  

          utilities\deleteDataset();
          
          $this->assertTrue(utilities\createDataset(), "Can't create the dataset, check the /dataset/create/ endpoint first...");
          
          $authLister = new AuthListerQuery($settings->endpointUrl);
          
          $authLister->mime("application/rdf+xml");
          
          $authLister->getRegisteredWebServiceEndpointsUri();
          
          $authLister->includeAllWebServiceUris();
          
          $authLister->registeredIp("self");
          
          $authLister->send();
                                          
          utilities\validateParameterApplicationRdfXml($this, $authLister);
          
          unset($authLister);
          unset($settings);
        }       
                          
        public function testParameter_Mode_Ws_Serialization_APPLICATION_RDF_N3() {
          
          $settings = new Config();  

          utilities\deleteDataset();
          
          $this->assertTrue(utilities\createDataset(), "Can't create the dataset, check the /dataset/create/ endpoint first...");
          
          $authLister = new AuthListerQuery($settings->endpointUrl);
          
          $authLister->mime("application/rdf+n3");
          
          $authLister->getRegisteredWebServiceEndpointsUri();
          
          $authLister->includeAllWebServiceUris();
          
          $authLister->registeredIp("self");
          
          $authLister->send();
                                
          utilities\validateParameterApplicationRdfN3($this, $authLister);

          utilities\deleteDataset();
          
          unset($authLister);
          unset($settings);
        }  
        
        //
        // Test all serializations of the mode=access_dataset param
        //
        
        public function testParameter_Mode_AccessDataset_TargetWebservice_All_Serialization_TEXT_XML() {
          
          $settings = new Config();  

          utilities\deleteDataset();
          
          $this->assertTrue(utilities\createDataset(), "Can't create the dataset, check the /dataset/create/ endpoint first...");
          
          $authLister = new AuthListerQuery($settings->endpointUrl);
          
          $authLister->mime("text/xml");
          
          $authLister->getDatasetUsersAccesses($settings->testDataset);
          
          $authLister->includeAllWebServiceUris();
          
          $authLister->registeredIp("self");
          
          $authLister->send();
                 
          utilities\validateParameterTextXml($this, $authLister);
         
          utilities\deleteDataset();          
          
          unset($authLister);
          unset($settings);
        }  
        
        public function testParameter_Mode_AccessDataset_TargetWebservice_All_Serialization_APPLICATION_JSON() {
                                               
          $settings = new Config();  
          
          utilities\deleteDataset();
          
          $this->assertTrue(utilities\createDataset(), "Can't create the dataset, check the /dataset/create/ endpoint first...");          
          
          $authLister = new AuthListerQuery($settings->endpointUrl);
          
          $authLister->mime("application/json");
          
          $authLister->getDatasetUsersAccesses($settings->testDataset);
          
          $authLister->includeAllWebServiceUris();
          
          $authLister->registeredIp("self");
          
          $authLister->send();
          
          utilities\validateParameterApplicationJson($this, $authLister);
          
          utilities\deleteDataset();
          
          unset($authLister);
          unset($settings);
        }   
        
        public function testParameter_Mode_AccessDataset_TargetWebservice_All_Serialization_APPLICATION_RDF_XML() {
          
          $settings = new Config();  

          utilities\deleteDataset();
          
          $this->assertTrue(utilities\createDataset(), "Can't create the dataset, check the /dataset/create/ endpoint first...");
          
          $authLister = new AuthListerQuery($settings->endpointUrl);
          
          $authLister->mime("application/rdf+xml");
          
          $authLister->getDatasetUsersAccesses($settings->testDataset);
          
          $authLister->includeAllWebServiceUris();
          
          $authLister->registeredIp("self");
          
          $authLister->send();
                                
          utilities\validateParameterApplicationRdfXml($this, $authLister);
          
          utilities\deleteDataset();
          
          unset($authLister);
          unset($settings);
        }       
                          
        public function testParameter_Mode_AccessDataset_TargetWebservice_All_Serialization_APPLICATION_RDF_N3() {
          
          $settings = new Config();  
          
          utilities\deleteDataset();
          
          $this->assertTrue(utilities\createDataset(), "Can't create the dataset, check the /dataset/create/ endpoint first...");

          $authLister = new AuthListerQuery($settings->endpointUrl);
          
          $authLister->mime("application/rdf+n3");
          
          $authLister->getDatasetUsersAccesses($settings->testDataset);
          
          $authLister->includeAllWebServiceUris();
          
          $authLister->registeredIp("self");
          
          $authLister->send();
                                
          utilities\validateParameterApplicationRdfN3($this, $authLister);

          utilities\deleteDataset();
          
          unset($authLister);
          unset($settings);
        }
        
        public function testParameter_Mode_AccessDataset_TargetWebservice_None_Serialization_TEXT_XML() {
          
          $settings = new Config();  

          utilities\deleteDataset();
          
          $this->assertTrue(utilities\createDataset(), "Can't create the dataset, check the /dataset/create/ endpoint first...");
          
          $authLister = new AuthListerQuery($settings->endpointUrl);
          
          $authLister->mime("text/xml");
          
          $authLister->getDatasetUsersAccesses($settings->testDataset);
          
          $authLister->includeNoWebServiceUris();
          
          $authLister->registeredIp("self");
          
          $authLister->send();
          
          utilities\validateParameterTextXml($this, $authLister);
         
          utilities\deleteDataset();          
          
          unset($authLister);
          unset($settings);
        }  
        
        public function testParameter_Mode_AccessDataset_TargetWebservice_None_Serialization_APPLICATION_JSON() {
                                               
          $settings = new Config();  
          
          utilities\deleteDataset();
          
          $this->assertTrue(utilities\createDataset(), "Can't create the dataset, check the /dataset/create/ endpoint first...");          
          
          $authLister = new AuthListerQuery($settings->endpointUrl);
          
          $authLister->mime("application/json");
          
          $authLister->getDatasetUsersAccesses($settings->testDataset);
          
          $authLister->includeNoWebServiceUris();
          
          $authLister->registeredIp("self");
          
          $authLister->send();
                                
          utilities\validateParameterApplicationJson($this, $authLister);
          
          utilities\deleteDataset();
          
          unset($authLister);
          unset($settings);
        }   
        
        public function testParameter_Mode_AccessDataset_TargetWebservice_None_Serialization_APPLICATION_RDF_XML() {
          
          $settings = new Config();  

          utilities\deleteDataset();
          
          $this->assertTrue(utilities\createDataset(), "Can't create the dataset, check the /dataset/create/ endpoint first...");
          
          $authLister = new AuthListerQuery($settings->endpointUrl);
          
          $authLister->mime("application/rdf+xml");
          
          $authLister->getDatasetUsersAccesses($settings->testDataset);
          
          $authLister->includeNoWebServiceUris();
          
          $authLister->registeredIp("self");
          
          $authLister->send();
                                
          utilities\validateParameterApplicationRdfXml($this, $authLister);
          
          utilities\deleteDataset();
          
          unset($authLister);
          unset($settings);
        }       
                          
        public function testParameter_Mode_AccessDataset_TargetWebservice_None_Serialization_APPLICATION_RDF_N3() {
          
          $settings = new Config();  
          
          utilities\deleteDataset();
          
          $this->assertTrue(utilities\createDataset(), "Can't create the dataset, check the /dataset/create/ endpoint first...");
          
          $authLister = new AuthListerQuery($settings->endpointUrl);
          
          $authLister->mime("application/rdf+n3");
          
          $authLister->getDatasetUsersAccesses($settings->testDataset);
          
          $authLister->includeNoWebServiceUris();
          
          $authLister->registeredIp("self");
          
          $authLister->send();
                                
          utilities\validateParameterApplicationRdfN3($this, $authLister);

          utilities\deleteDataset();
          
          unset($authLister);
          unset($settings);
        }        
        
        public function testParameter_Mode_AccessDataset_TargetWebservice_TargetWS_Serialization_TEXT_XML() {
          
          $settings = new Config();  

          utilities\deleteDataset();
          
          $this->assertTrue(utilities\createDataset(), "Can't create the dataset, check the /dataset/create/ endpoint first...");
          
          $authLister = new AuthListerQuery($settings->endpointUrl);
          
          $authLister->mime("text/xml");
          
          $authLister->getDatasetUsersAccesses($settings->testDataset);
          
          $authLister->includeTargerWebServiceUri($settings->endpointUri."crud/create/");
          
          $authLister->registeredIp("self");
          
          $authLister->send();
                 
          utilities\validateParameterTextXml($this, $authLister);
         
          utilities\deleteDataset();          
          
          unset($authLister);
          unset($settings);
        }  
        
        public function testParameter_Mode_AccessDataset_TargetWebservice_TargetWS_Serialization_APPLICATION_JSON() {
                                               
          $settings = new Config();  
          
          utilities\deleteDataset();
          
          $this->assertTrue(utilities\createDataset(), "Can't create the dataset, check the /dataset/create/ endpoint first...");          
          
          $authLister = new AuthListerQuery($settings->endpointUrl);
          
          $authLister->mime("application/json");
          
          $authLister->getDatasetUsersAccesses($settings->testDataset);
          
          $authLister->includeTargerWebServiceUri($settings->endpointUri."crud/create/");
          
          $authLister->registeredIp("self");
          
          $authLister->send();          
                                
          utilities\validateParameterApplicationJson($this, $authLister);
          
          utilities\deleteDataset();
          
          unset($authLister);
          unset($settings);
        }   
        
        public function testParameter_Mode_AccessDataset_TargetWebservice_TargetWS_Serialization_APPLICATION_RDF_XML() {
          
          $settings = new Config();  

          utilities\deleteDataset();
          
          $this->assertTrue(utilities\createDataset(), "Can't create the dataset, check the /dataset/create/ endpoint first...");
          
          $authLister = new AuthListerQuery($settings->endpointUrl);
          
          $authLister->mime("application/rdf+xml");
          
          $authLister->getDatasetUsersAccesses($settings->testDataset);
          
          $authLister->includeTargerWebServiceUri($settings->endpointUri."crud/create/");
          
          $authLister->registeredIp("self");
          
          $authLister->send();          
                                
          utilities\validateParameterApplicationRdfXml($this, $authLister);
          
          utilities\deleteDataset();
          
          unset($authLister);
          unset($settings);
        }       
                          
        public function testParameter_Mode_AccessDataset_TargetWebservice_TargetWS_Serialization_APPLICATION_RDF_N3() {
          
          $settings = new Config();  
          
          utilities\deleteDataset();
          
          $this->assertTrue(utilities\createDataset(), "Can't create the dataset, check the /dataset/create/ endpoint first...");
          
          $authLister = new AuthListerQuery($settings->endpointUrl);
          
          $authLister->mime("application/rdf+n3");
          
          $authLister->getDatasetUsersAccesses($settings->testDataset);
          
          $authLister->includeTargerWebServiceUri($settings->endpointUri."crud/create/");
          
          $authLister->registeredIp("self");
          
          $authLister->send();          
        
          utilities\validateParameterApplicationRdfN3($this, $authLister);

          utilities\deleteDataset();
          
          unset($authLister);
          unset($settings);
        }        
        
        //
        // Test all serializations of the mode=access_user param
        //
        
        public function testParameter_Mode_AccessUser_TargetWebservice_All_Serialization_TEXT_XML() {
          
          $settings = new Config();  
          
          utilities\deleteDataset();
          
          $this->assertTrue(utilities\createDataset(), "Can't create the dataset, check the /dataset/create/ endpoint first...");
          
          $authLister = new AuthListerQuery($settings->endpointUrl);
          
          $authLister->mime("text/xml");
          
          $authLister->getUserAccesses("self");
          
          $authLister->includeAllWebServiceUris();
          
          $authLister->registeredIp("self");
          
          $authLister->send();          
                                
          utilities\validateParameterTextXml($this, $authLister);
          
          utilities\deleteDataset();
          
          unset($authLister);
          unset($settings);
        }  
        
        public function testParameter_Mode_AccessUser_TargetWebservice_All_Serialization_APPLICATION_JSON() {
          
          $settings = new Config();  
          
          utilities\deleteDataset();
          
          $this->assertTrue(utilities\createDataset(), "Can't create the dataset, check the /dataset/create/ endpoint first...");
          
          $authLister = new AuthListerQuery($settings->endpointUrl);
          
          $authLister->mime("application/json");
          
          $authLister->getUserAccesses("self");
          
          $authLister->includeAllWebServiceUris();
          
          $authLister->registeredIp("self");
          
          $authLister->send();            
                                
          utilities\validateParameterApplicationJson($this, $authLister);
          
          utilities\deleteDataset();
          
          unset($authLister);
          unset($settings);
        }   
         
        public function testParameter_Mode_AccessUser_TargetWebservice_All_Serialization_APPLICATION_RDF_XML() {

          $settings = new Config();  
          
          utilities\deleteDataset();
          
          $this->assertTrue(utilities\createDataset(), "Can't create the dataset, check the /dataset/create/ endpoint first...");

          $authLister = new AuthListerQuery($settings->endpointUrl);
          
          $authLister->mime("application/rdf+xml");
          
          $authLister->getUserAccesses("self");
          
          $authLister->includeAllWebServiceUris();
          
          $authLister->registeredIp("self");
          
          $authLister->send();            
                     
          utilities\validateParameterApplicationRdfXml($this, $authLister);

          utilities\deleteDataset();
          
          unset($authLister);
          unset($settings);
        }       
                          
        public function testParameter_Mode_AccessUser_TargetWebservice_All_Serialization_APPLICATION_RDF_N3() {

          $settings = new Config();  
          
          utilities\deleteDataset();
          
          $this->assertTrue(utilities\createDataset(), "Can't create the dataset, check the /dataset/create/ endpoint first...");
          
          $authLister = new AuthListerQuery($settings->endpointUrl);
          
          $authLister->mime("application/rdf+n3");
          
          $authLister->getUserAccesses("self");
          
          $authLister->includeAllWebServiceUris();
          
          $authLister->registeredIp("self");
          
          $authLister->send();            
          
          utilities\validateParameterApplicationRdfN3($this, $authLister);
          
          utilities\deleteDataset();
          
          unset($authLister);
          unset($settings);
        }   
        
        public function testParameter_Mode_AccessUser_TargetWebservice_None_Serialization_TEXT_XML() {
          
          $settings = new Config();  
          
          utilities\deleteDataset();
          
          $this->assertTrue(utilities\createDataset(), "Can't create the dataset, check the /dataset/create/ endpoint first...");
          
          $authLister = new AuthListerQuery($settings->endpointUrl);
          
          $authLister->mime("text/xml");
          
          $authLister->getUserAccesses("self");
          
          $authLister->includeNoWebServiceUris();
          
          $authLister->registeredIp("self");
          
          $authLister->send();            
                                
          utilities\validateParameterTextXml($this, $authLister);
          
          utilities\deleteDataset();
          
          unset($authLister);
          unset($settings);
        }  
        
        public function testParameter_Mode_AccessUser_TargetWebservice_None_Serialization_APPLICATION_JSON() {
          
          $settings = new Config();  
          
          utilities\deleteDataset();
          
          $this->assertTrue(utilities\createDataset(), "Can't create the dataset, check the /dataset/create/ endpoint first...");
          
          $authLister = new AuthListerQuery($settings->endpointUrl);
          
          $authLister->mime("application/json");
          
          $authLister->getUserAccesses("self");
          
          $authLister->includeNoWebServiceUris();
          
          $authLister->registeredIp("self");
          
          $authLister->send();               
                                
          utilities\validateParameterApplicationJson($this, $authLister);
          
          utilities\deleteDataset();
          
          unset($authLister);
          unset($settings);
        }   
         
        public function testParameter_Mode_AccessUser_TargetWebservice_None_Serialization_APPLICATION_RDF_XML() {

          $settings = new Config();  
          
          utilities\deleteDataset();
          
          $this->assertTrue(utilities\createDataset(), "Can't create the dataset, check the /dataset/create/ endpoint first...");

          $authLister = new AuthListerQuery($settings->endpointUrl);
          
          $authLister->mime("application/rdf+xml");
          
          $authLister->getUserAccesses("self");
          
          $authLister->includeNoWebServiceUris();
          
          $authLister->registeredIp("self");
          
          $authLister->send();     
                     
          utilities\validateParameterApplicationRdfXml($this, $authLister);

          utilities\deleteDataset();
          
          unset($authLister);
          unset($settings);
        }       
                          
        public function testParameter_Mode_AccessUser_TargetWebservice_None_Serialization_APPLICATION_RDF_N3() {

          $settings = new Config();  
          
          utilities\deleteDataset();
          
          $this->assertTrue(utilities\createDataset(), "Can't create the dataset, check the /dataset/create/ endpoint first...");
          
          $authLister = new AuthListerQuery($settings->endpointUrl);
          
          $authLister->mime("application/rdf+n3");
          
          $authLister->getUserAccesses("self");
          
          $authLister->includeNoWebServiceUris();
          
          $authLister->registeredIp("self");
          
          $authLister->send();               
                                
          utilities\validateParameterApplicationRdfN3($this, $authLister);
          
          utilities\deleteDataset();
          
          unset($authLister);
          unset($settings);
        }
        
        public function testParameter_Mode_AccessUser_TargetWebservice_TargetWS_Serialization_TEXT_XML() {
          
          $settings = new Config();  
          
          utilities\deleteDataset();
          
          $this->assertTrue(utilities\createDataset(), "Can't create the dataset, check the /dataset/create/ endpoint first...");
          
          $authLister = new AuthListerQuery($settings->endpointUrl);
          
          $authLister->mime("text/xml");
          
          $authLister->getUserAccesses("self");
          
          $authLister->includeTargerWebServiceUri($settings->endpointUri."crud/create/");
          
          $authLister->registeredIp("self");
          
          $authLister->send();               
                                
          utilities\validateParameterTextXml($this, $authLister);
          
          utilities\deleteDataset();
          
          unset($authLister);
          unset($settings);
        }  
        
        public function testParameter_Mode_AccessUser_TargetWebservice_TargetWS_Serialization_APPLICATION_JSON() {
          
          $settings = new Config();  
          
          utilities\deleteDataset();
          
          $this->assertTrue(utilities\createDataset(), "Can't create the dataset, check the /dataset/create/ endpoint first...");
          
          $authLister = new AuthListerQuery($settings->endpointUrl);
          
          $authLister->mime("application/json");
          
          $authLister->getUserAccesses("self");
          
          $authLister->includeNoWebServiceUris();
          
          $authLister->registeredIp("self");
          
          $authLister->send();               
                                
          utilities\validateParameterApplicationJson($this, $authLister);
          
          utilities\deleteDataset();
          
          unset($authLister);
          unset($settings);
        }   
         
        public function testParameter_Mode_AccessUser_TargetWebservice_TargetWS_Serialization_APPLICATION_RDF_XML() {

          $settings = new Config();  
          
          utilities\deleteDataset();
          
          $this->assertTrue(utilities\createDataset(), "Can't create the dataset, check the /dataset/create/ endpoint first...");

          $authLister = new AuthListerQuery($settings->endpointUrl);
          
          $authLister->mime("application/rdf+xml");
          
          $authLister->getUserAccesses("self");
          
          $authLister->includeNoWebServiceUris();
          
          $authLister->registeredIp("self");
          
          $authLister->send();               
                     
          utilities\validateParameterApplicationRdfXml($this, $authLister);

          utilities\deleteDataset();
          
          unset($authLister);
          unset($settings);
        }       
                          
        public function testParameter_Mode_AccessUser_TargetWebservice_TargetWS_Serialization_APPLICATION_RDF_N3() {

          $settings = new Config();  
          
          utilities\deleteDataset();
          
          $this->assertTrue(utilities\createDataset(), "Can't create the dataset, check the /dataset/create/ endpoint first...");
          
          $authLister = new AuthListerQuery($settings->endpointUrl);
          
          $authLister->mime("application/rdf+n3");
          
          $authLister->getUserAccesses("self");
          
          $authLister->includeNoWebServiceUris();
          
          $authLister->registeredIp("self");
          
          $authLister->send();               
                                
          utilities\validateParameterApplicationRdfN3($this, $authLister);
          
          utilities\deleteDataset();
          
          unset($authLister);
          unset($settings);
        }        
        
        static public function tearDownAfterClass() {
            echo implode("\n", self::$outputs);
        }        
    }
 
?>