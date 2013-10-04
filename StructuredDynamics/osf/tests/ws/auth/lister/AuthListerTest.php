<?php

    namespace StructuredDynamics\osf\tests\ws\auth\lister;
    
    use StructuredDynamics\osf\framework\WebServiceQuerier;
    use StructuredDynamics\osf\php\api\ws\auth\lister\AuthListerQuery;
    use StructuredDynamics\osf\tests\Config;
    use StructuredDynamics\osf\tests as utilities;
     
    include_once("SplClassLoader.php");
    include_once("validators.php");
    include_once("utilities.php");  
    
    // Load the \tests namespace where all the test code is located 
    $loader_tests = new \SplClassLoader('StructuredDynamics\osf\tests', realpath("../../../"));
    $loader_tests->register();
   
    // Load the \ws namespace where all the web service code is located 
    $loader_ws = new \SplClassLoader('StructuredDynamics\osf\php\api\ws', realpath("../../../"));
    $loader_ws->register();  
    
    // Load the \php\api\framework namespace where all the web service code is located 
    $loader_ws = new \SplClassLoader('StructuredDynamics\osf\php\api\framework', realpath("../../../"));
    $loader_ws->register();  
   
    // Load the \framework namespace where all the supporting (utility) code is located
    $loader_framework = new \SplClassLoader('StructuredDynamics\osf\framework', realpath("../../../"));
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
                                       "&interface=". urlencode($settings->authListerInterface) .
                                       "&version=". urlencode($settings->authListerInterfaceVersion),
                                       $settings->applicationID,
                                       $settings->apiKey,
                                       $settings->userID);
                                       
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
                                       "&interface=". urlencode($settings->authListerInterface) .
                                       "&version=". urlencode($settings->authListerInterfaceVersion),
                                       $settings->applicationID,
                                       $settings->apiKey,
                                       $settings->userID);
                                       
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
                                       "&interface=". urlencode($settings->authListerInterface) .
                                       "&version=". urlencode($settings->authListerInterfaceVersion),
                                       $settings->applicationID,
                                       $settings->apiKey,
                                       $settings->userID);
                                       
          $this->assertEquals($wsq->getStatus(), "406", "Debugging information: ".var_export($wsq, TRUE));                                       
          $this->assertEquals($wsq->getStatusMessage(), "Not Acceptable", "Debugging information: ".var_export($wsq, TRUE));          

          utilities\deleteDataset();
          
          unset($wsq);
          unset($settings);
        }  
                
        public function testUnknownMode() {
          
          $settings = new Config();  
          
          utilities\deleteDataset();
          
          $this->assertTrue(utilities\createDataset(), "Can't create the dataset, check the /dataset/create/ endpoint first...");
          
          $wsq = new WebServiceQuerier($settings->endpointUrl . "auth/lister/", 
                                       "get", 
                                       "text/xml",
                                       "mode=" . urlencode("unknown") .
                                       "&dataset=" . urlencode($settings->testDataset) .
                                       "&target_webservice=" . urlencode("all") .
                                       "&interface=". urlencode($settings->authListerInterface) .
                                       "&version=". urlencode($settings->authListerInterfaceVersion),
                                       $settings->applicationID,
                                       $settings->apiKey,
                                       $settings->userID);
                                       
          $this->assertEquals($wsq->getStatus(), "400", "Debugging information: ".var_export($wsq, TRUE));                                       
          $this->assertEquals($wsq->getStatusMessage(), "Bad Request", "Debugging information: ".var_export($wsq, TRUE));
          $this->assertEquals($wsq->error->id, "WS-AUTH-LISTER-200", "Debugging information: ".var_export($wsq, TRUE));                                       

          utilities\deleteDataset();
          
          unset($wsq);
          unset($settings);
        }        
            
        public function testMissingDatasetUri() {
          
            $settings = new Config();  

            utilities\deleteDataset();
            
            $this->assertTrue(utilities\createDataset(), "Can't create the dataset, check the /dataset/create/ endpoint first...");
            
            $authLister = new AuthListerQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
            
            $authLister->getDatasetUsersAccesses("")
                       ->mime("text/xml")
                       ->sourceInterface($settings->authListerInterface)
                       ->sourceInterfaceVersion($settings->authListerInterfaceVersion)
                       ->send();
                                 
            $this->assertEquals($authLister->getStatus(), "400", "Debugging information: ".var_export($authLister, TRUE));                                       
            $this->assertEquals($authLister->getStatusMessage(), "Bad Request", "Debugging information: ".var_export($authLister, TRUE));
            $this->assertEquals($authLister->error->id, "WS-AUTH-LISTER-201", "Debugging information: ".var_export($authLister, TRUE));                                       

            utilities\deleteDataset();

            unset($authLister);
            unset($settings);  
        }    
        
        public function testMissingGroupUri() {
          
            $settings = new Config();  

            utilities\deleteDataset();
            
            $this->assertTrue(utilities\createDataset(), "Can't create the dataset, check the /dataset/create/ endpoint first...");
            
            $authLister = new AuthListerQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
            
            $authLister->getGroupUsers("")
                       ->mime("text/xml")
                       ->sourceInterface($settings->authListerInterface)
                       ->sourceInterfaceVersion($settings->authListerInterfaceVersion)
                       ->send();
                                 
            $this->assertEquals($authLister->getStatus(), "400", "Debugging information: ".var_export($authLister, TRUE));                                       
            $this->assertEquals($authLister->getStatusMessage(), "Bad Request", "Debugging information: ".var_export($authLister, TRUE));
            $this->assertEquals($authLister->error->id, "WS-AUTH-LISTER-202", "Debugging information: ".var_export($authLister, TRUE));                                       

            utilities\deleteDataset();

            unset($authLister);
            unset($settings);  
        }
            
        public function testValidInterfaceVersion() {
          
            $settings = new Config();  

            utilities\deleteDataset();
            
            $this->assertTrue(utilities\createDataset(), "Can't create the dataset, check the /dataset/create/ endpoint first...");
            
            $authLister = new AuthListerQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
            
            $authLister->mime("text/xml")
                       ->getDatasetsUri($settings->testDataset)
                       ->includeAllWebServiceUris()
                       ->sourceInterface($settings->authListerInterface)
                       ->sourceInterfaceVersion($settings->authListerInterfaceVersion)
                       ->send();
                                 
            $this->assertEquals($authLister->getStatus(), "200", "Debugging information: ".var_export($authLister, TRUE));                                       

            utilities\deleteDataset();

            unset($authLister);
            unset($settings);  
        }
        
        
        public function testInvalidInterfaceVersion() {
          
            $settings = new Config();  

            utilities\deleteDataset();
            
            $this->assertTrue(utilities\createDataset(), "Can't create the dataset, check the /dataset/create/ endpoint first...");
            
            $authLister = new AuthListerQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
            
            $authLister->mime("text/xml")
                       ->getDatasetsUri($settings->testDataset)
                       ->includeAllWebServiceUris()
                       ->sourceInterface($settings->authListerInterface)
                       ->sourceInterfaceVersion("667.4")
                       ->send();
                                 
            $this->assertEquals($authLister->getStatus(), "400", "Debugging information: ".var_export($authLister, TRUE));                                       
            $this->assertEquals($authLister->getStatusMessage(), "Bad Request", "Debugging information: ".var_export($authLister, TRUE));
            $this->assertEquals($authLister->error->id, "WS-AUTH-LISTER-307", "Debugging information: ".var_export($authLister, TRUE));                                       

            utilities\deleteDataset();

            unset($authLister);
            unset($settings);        
        }            
        
        //
        // Test existing interface
        //
        
        public function testInterfaceExists() {
          
          $settings = new Config();  

          utilities\deleteDataset();
          
          $this->assertTrue(utilities\createDataset(), "Can't create the dataset, check the /dataset/create/ endpoint first...");
          
          $authLister = new AuthListerQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
          
          $authLister->mime("text/xml")
                     ->getDatasetsUri($settings->testDataset)
                     ->includeAllWebServiceUris()
                     ->sourceInterface($settings->authListerInterface)
                     ->sourceInterfaceVersion($settings->authListerInterfaceVersion)
                     ->send();
                               
          $this->assertEquals($authLister->getStatus(), "200", "Debugging information: ".var_export($authLister, TRUE));                                       

          utilities\deleteDataset();

          unset($authLister);
          unset($settings);
        }  
        
        //
        // Test unexisting interface
        //
        
        public function testInterfaceNotExisting() {
          
          $settings = new Config();  

          utilities\deleteDataset();
          
          $this->assertTrue(utilities\createDataset(), "Can't create the dataset, check the /dataset/create/ endpoint first...");
          
          $authLister = new AuthListerQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
          
          $authLister->mime("text/xml")
                     ->getDatasetsUri($settings->testDataset)
                     ->includeAllWebServiceUris()
                     ->sourceInterface("default-not-existing")
                     ->sourceInterfaceVersion($settings->authListerInterfaceVersion)
                     ->send();
                               
          $this->assertEquals($authLister->getStatus(), "400", "Debugging information: ".var_export($authLister, TRUE));                                       
          $this->assertEquals($authLister->getStatusMessage(), "Bad Request", "Debugging information: ".var_export($authLister, TRUE));
          $this->assertEquals($authLister->error->id, "WS-AUTH-LISTER-305", "Debugging information: ".var_export($authLister, TRUE));                                       

          utilities\deleteDataset();

          unset($authLister);
          unset($settings);
        }  
        
        //
        // Test all serializations of the mode=dataset param
        //
        
        public function testParameter_Mode_Dataset_Serialization_TEXT_XML() {
          
          $settings = new Config();  

          utilities\deleteDataset();
          
          $this->assertTrue(utilities\createDataset(), "Can't create the dataset, check the /dataset/create/ endpoint first...");
          
          $authLister = new AuthListerQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
          
          $authLister->mime("text/xml")
                     ->getDatasetsUri($settings->testDataset)
                     ->includeAllWebServiceUris()
                     ->sourceInterface($settings->authListerInterface)
                     ->sourceInterfaceVersion($settings->authListerInterfaceVersion)
                     ->send();
                               
          utilities\validateParameterTextXml($this, $authLister);

          utilities\deleteDataset();

          unset($authLister);
          unset($settings);
        }  
        
        public function testParameter_Mode_Dataset_Serialization_APPLICATION_JSON() {
          
          $settings = new Config();  
          
          utilities\deleteDataset();
          
          $this->assertTrue(utilities\createDataset(), "Can't create the dataset, check the /dataset/create/ endpoint first...");

          $authLister = new AuthListerQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
          
          $authLister->mime("application/json")
                     ->getDatasetsUri($settings->testDataset)
                     ->includeAllWebServiceUris()
                     ->sourceInterface($settings->authListerInterface)
                     ->sourceInterfaceVersion($settings->authListerInterfaceVersion)
                     ->send();
                                
          utilities\validateParameterApplicationJson($this, $authLister);
          
          utilities\deleteDataset();
          
          unset($authLister);
          unset($settings);
        }   
        
        public function testParameter_Mode_Dataset_Serialization_APPLICATION_RDF_XML() {
          
          $settings = new Config();  

          utilities\deleteDataset();
          
          $this->assertTrue(utilities\createDataset(), "Can't create the dataset, check the /dataset/create/ endpoint first...");
          
          $authLister = new AuthListerQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
          
          $authLister->mime("application/rdf+xml")
                     ->getDatasetsUri($settings->testDataset)
                     ->includeAllWebServiceUris()
                     ->sourceInterface($settings->authListerInterface)
                     ->sourceInterfaceVersion($settings->authListerInterfaceVersion)
                     ->send();
                                
          utilities\validateParameterApplicationRdfXml($this, $authLister);

          utilities\deleteDataset();
          
          unset($authLister);
          unset($settings);
        }       
                          
        public function testParameter_Mode_Dataset_Serialization_APPLICATION_RDF_N3() {
          
          $settings = new Config();  
          
          utilities\deleteDataset();
          
          $this->assertTrue(utilities\createDataset(), "Can't create the dataset, check the /dataset/create/ endpoint first...");

          $authLister = new AuthListerQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
          
          $authLister->mime("application/rdf+n3")
                     ->getDatasetsUri($settings->testDataset)
                     ->includeAllWebServiceUris()
                     ->sourceInterface($settings->authListerInterface)
                     ->sourceInterfaceVersion($settings->authListerInterfaceVersion)
                     ->send();
                                
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
          
          $authLister = new AuthListerQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
          
          $authLister->mime("text/xml")
                     ->getRegisteredWebServiceEndpointsUri()
                     ->includeAllWebServiceUris()
                     ->sourceInterface($settings->authListerInterface)
                     ->sourceInterfaceVersion($settings->authListerInterfaceVersion)
                     ->send();
          
          utilities\validateParameterTextXml($this, $authLister);

          utilities\deleteDataset();
          
          unset($authLister);
          unset($settings);
        }  
        
        public function testParameter_Mode_Ws_Serialization_APPLICATION_JSON() {
          
          $settings = new Config();  

          utilities\deleteDataset();
          
          $this->assertTrue(utilities\createDataset(), "Can't create the dataset, check the /dataset/create/ endpoint first...");
          
          $authLister = new AuthListerQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
          
          $authLister->mime("application/json")
                     ->getRegisteredWebServiceEndpointsUri()
                     ->includeAllWebServiceUris()
                     ->sourceInterface($settings->authListerInterface)
                     ->sourceInterfaceVersion($settings->authListerInterfaceVersion)
                     ->send();
                                
          utilities\validateParameterApplicationJson($this, $authLister);

          utilities\deleteDataset();
          
          unset($authLister);
          unset($settings);
        }   
        
        public function testParameter_Mode_Ws_Serialization_APPLICATION_RDF_XML() {
          
          $settings = new Config();  

          utilities\deleteDataset();
          
          $this->assertTrue(utilities\createDataset(), "Can't create the dataset, check the /dataset/create/ endpoint first...");
          
          $authLister = new AuthListerQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
          
          $authLister->mime("application/rdf+xml")
                     ->getRegisteredWebServiceEndpointsUri()
                     ->includeAllWebServiceUris()
                     ->sourceInterface($settings->authListerInterface)
                     ->sourceInterfaceVersion($settings->authListerInterfaceVersion)
                     ->send();
                                          
          utilities\validateParameterApplicationRdfXml($this, $authLister);
          
          unset($authLister);
          unset($settings);
        }       
                          
        public function testParameter_Mode_Ws_Serialization_APPLICATION_RDF_N3() {
          
          $settings = new Config();  

          utilities\deleteDataset();
          
          $this->assertTrue(utilities\createDataset(), "Can't create the dataset, check the /dataset/create/ endpoint first...");
          
          $authLister = new AuthListerQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
          
          $authLister->mime("application/rdf+n3")
                     ->getRegisteredWebServiceEndpointsUri()
                     ->includeAllWebServiceUris()
                     ->sourceInterface($settings->authListerInterface)
                     ->sourceInterfaceVersion($settings->authListerInterfaceVersion)
                     ->send();
                                
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
          
          $authLister = new AuthListerQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
          
          $authLister->mime("text/xml")
                     ->getDatasetUsersAccesses($settings->testDataset)
                     ->includeAllWebServiceUris()
                     ->sourceInterface($settings->authListerInterface)
                     ->sourceInterfaceVersion($settings->authListerInterfaceVersion)
                     ->send();
       
          utilities\validateParameterTextXml($this, $authLister);
         
          utilities\deleteDataset();          
          
          unset($authLister);
          unset($settings);
        }  
        
        public function testParameter_Mode_AccessDataset_TargetWebservice_All_Serialization_APPLICATION_JSON() {
                                               
          $settings = new Config();  
          
          utilities\deleteDataset();
          
          $this->assertTrue(utilities\createDataset(), "Can't create the dataset, check the /dataset/create/ endpoint first...");          
          
          $authLister = new AuthListerQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
          
          $authLister->mime("application/json")
                     ->getDatasetUsersAccesses($settings->testDataset)
                     ->includeAllWebServiceUris()
                     ->sourceInterface($settings->authListerInterface)
                     ->sourceInterfaceVersion($settings->authListerInterfaceVersion)
                     ->send();
          
          utilities\validateParameterApplicationJson($this, $authLister);
          
          utilities\deleteDataset();
          
          unset($authLister);
          unset($settings);
        }   
        
        public function testParameter_Mode_AccessDataset_TargetWebservice_All_Serialization_APPLICATION_RDF_XML() {
          
          $settings = new Config();  

          utilities\deleteDataset();
          
          $this->assertTrue(utilities\createDataset(), "Can't create the dataset, check the /dataset/create/ endpoint first...");
          
          $authLister = new AuthListerQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
          
          $authLister->mime("application/rdf+xml")
                     ->getDatasetUsersAccesses($settings->testDataset)
                     ->includeAllWebServiceUris()
                     ->sourceInterface($settings->authListerInterface)
                     ->sourceInterfaceVersion($settings->authListerInterfaceVersion)
                     ->send();
                                
          utilities\validateParameterApplicationRdfXml($this, $authLister);
          
          utilities\deleteDataset();
          
          unset($authLister);
          unset($settings);
        }       
                          
        public function testParameter_Mode_AccessDataset_TargetWebservice_All_Serialization_APPLICATION_RDF_N3() {
          
          $settings = new Config();  
          
          utilities\deleteDataset();
          
          $this->assertTrue(utilities\createDataset(), "Can't create the dataset, check the /dataset/create/ endpoint first...");

          $authLister = new AuthListerQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
          
          $authLister->mime("application/rdf+n3")
                     ->getDatasetUsersAccesses($settings->testDataset)
                     ->includeAllWebServiceUris()
                     ->sourceInterface($settings->authListerInterface)
                     ->sourceInterfaceVersion($settings->authListerInterfaceVersion)
                     ->send();
                                
          utilities\validateParameterApplicationRdfN3($this, $authLister);

          utilities\deleteDataset();
          
          unset($authLister);
          unset($settings);
        }
        
        public function testParameter_Mode_AccessDataset_TargetWebservice_None_Serialization_TEXT_XML() {
          
          $settings = new Config();  

          utilities\deleteDataset();
          
          $this->assertTrue(utilities\createDataset(), "Can't create the dataset, check the /dataset/create/ endpoint first...");
          
          $authLister = new AuthListerQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
          
          $authLister->mime("text/xml")
                     ->getDatasetUsersAccesses($settings->testDataset)
                     ->sourceInterface($settings->authListerInterface)
                     ->sourceInterfaceVersion($settings->authListerInterfaceVersion)
                     ->includeNoWebServiceUris()
                     ->send();
          
          utilities\validateParameterTextXml($this, $authLister);
         
          utilities\deleteDataset();          
          
          unset($authLister);
          unset($settings);
        }  
        
        public function testParameter_Mode_AccessDataset_TargetWebservice_None_Serialization_APPLICATION_JSON() {
                                               
          $settings = new Config();  
          
          utilities\deleteDataset();
          
          $this->assertTrue(utilities\createDataset(), "Can't create the dataset, check the /dataset/create/ endpoint first...");          
          
          $authLister = new AuthListerQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
          
          $authLister->mime("application/json")
                     ->getDatasetUsersAccesses($settings->testDataset)
                     ->includeNoWebServiceUris()
                     ->sourceInterface($settings->authListerInterface)
                     ->sourceInterfaceVersion($settings->authListerInterfaceVersion)
                     ->send();
                                
          utilities\validateParameterApplicationJson($this, $authLister);
          
          utilities\deleteDataset();
          
          unset($authLister);
          unset($settings);
        }   
        
        public function testParameter_Mode_AccessDataset_TargetWebservice_None_Serialization_APPLICATION_RDF_XML() {
          
          $settings = new Config();  

          utilities\deleteDataset();
          
          $this->assertTrue(utilities\createDataset(), "Can't create the dataset, check the /dataset/create/ endpoint first...");
          
          $authLister = new AuthListerQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
          
          $authLister->mime("application/rdf+xml")
                     ->getDatasetUsersAccesses($settings->testDataset)
                     ->includeNoWebServiceUris()
                     ->sourceInterface($settings->authListerInterface)
                     ->sourceInterfaceVersion($settings->authListerInterfaceVersion)
                     ->send();
                                
          utilities\validateParameterApplicationRdfXml($this, $authLister);
          
          utilities\deleteDataset();
          
          unset($authLister);
          unset($settings);
        }       
                          
        public function testParameter_Mode_AccessDataset_TargetWebservice_None_Serialization_APPLICATION_RDF_N3() {
          
          $settings = new Config();  
          
          utilities\deleteDataset();
          
          $this->assertTrue(utilities\createDataset(), "Can't create the dataset, check the /dataset/create/ endpoint first...");
          
          $authLister = new AuthListerQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
          
          $authLister->mime("application/rdf+n3")
                     ->getDatasetUsersAccesses($settings->testDataset)
                     ->includeNoWebServiceUris()
                     ->sourceInterface($settings->authListerInterface)
                     ->sourceInterfaceVersion($settings->authListerInterfaceVersion)
                     ->send();
                                
          utilities\validateParameterApplicationRdfN3($this, $authLister);

          utilities\deleteDataset();
          
          unset($authLister);
          unset($settings);
        }        
        
        public function testParameter_Mode_AccessDataset_TargetWebservice_TargetWS_Serialization_TEXT_XML() {
          
          $settings = new Config();  

          utilities\deleteDataset();
          
          $this->assertTrue(utilities\createDataset(), "Can't create the dataset, check the /dataset/create/ endpoint first...");
          
          $authLister = new AuthListerQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
          
          $authLister->mime("text/xml")
                     ->getDatasetUsersAccesses($settings->testDataset)
                     ->includeTargerWebServiceUri($settings->endpointUri."crud/create/")
                     ->sourceInterface($settings->authListerInterface)
                     ->sourceInterfaceVersion($settings->authListerInterfaceVersion)
                     ->send();
                 
          utilities\validateParameterTextXml($this, $authLister);
         
          utilities\deleteDataset();          
          
          unset($authLister);
          unset($settings);
        }  
        
        public function testParameter_Mode_AccessDataset_TargetWebservice_TargetWS_Serialization_APPLICATION_JSON() {
                                               
          $settings = new Config();  
          
          utilities\deleteDataset();
          
          $this->assertTrue(utilities\createDataset(), "Can't create the dataset, check the /dataset/create/ endpoint first...");          
          
          $authLister = new AuthListerQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
          
          $authLister->mime("application/json")
                     ->getDatasetUsersAccesses($settings->testDataset)
                     ->includeTargerWebServiceUri($settings->endpointUri."crud/create/")
                     ->sourceInterface($settings->authListerInterface)
                     ->sourceInterfaceVersion($settings->authListerInterfaceVersion)
                     ->send();          
                                
          utilities\validateParameterApplicationJson($this, $authLister);
          
          utilities\deleteDataset();
          
          unset($authLister);
          unset($settings);
        }   
        
        public function testParameter_Mode_AccessDataset_TargetWebservice_TargetWS_Serialization_APPLICATION_RDF_XML() {
          
          $settings = new Config();  

          utilities\deleteDataset();
          
          $this->assertTrue(utilities\createDataset(), "Can't create the dataset, check the /dataset/create/ endpoint first...");
          
          $authLister = new AuthListerQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
          
          $authLister->mime("application/rdf+xml")
                     ->getDatasetUsersAccesses($settings->testDataset)
                     ->includeTargerWebServiceUri($settings->endpointUri."crud/create/")
                     ->sourceInterface($settings->authListerInterface)
                     ->sourceInterfaceVersion($settings->authListerInterfaceVersion)
                     ->send();          
                                
          utilities\validateParameterApplicationRdfXml($this, $authLister);
          
          utilities\deleteDataset();
          
          unset($authLister);
          unset($settings);
        }       
                          
        public function testParameter_Mode_AccessDataset_TargetWebservice_TargetWS_Serialization_APPLICATION_RDF_N3() {
          
          $settings = new Config();  
          
          utilities\deleteDataset();
          
          $this->assertTrue(utilities\createDataset(), "Can't create the dataset, check the /dataset/create/ endpoint first...");
          
          $authLister = new AuthListerQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
          
          $authLister->mime("application/rdf+n3")
                     ->getDatasetUsersAccesses($settings->testDataset)
                     ->includeTargerWebServiceUri($settings->endpointUri."crud/create/")
                     ->sourceInterface($settings->authListerInterface)
                     ->sourceInterfaceVersion($settings->authListerInterfaceVersion)
                     ->send();          
        
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
          
          $authLister = new AuthListerQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
          
          $authLister->mime("text/xml")
                     ->getUserAccesses()
                     ->includeAllWebServiceUris()
                     ->sourceInterface($settings->authListerInterface)
                     ->sourceInterfaceVersion($settings->authListerInterfaceVersion)
                     ->send();          
                                
          utilities\validateParameterTextXml($this, $authLister);
          
          utilities\deleteDataset();
          
          unset($authLister);
          unset($settings);
        }  
        
        public function testParameter_Mode_AccessUser_TargetWebservice_All_Serialization_APPLICATION_JSON() {
          
          $settings = new Config();  
          
          utilities\deleteDataset();
          
          $this->assertTrue(utilities\createDataset(), "Can't create the dataset, check the /dataset/create/ endpoint first...");
          
          $authLister = new AuthListerQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
          
          $authLister->mime("application/json")
                     ->getUserAccesses()
                     ->includeAllWebServiceUris()
                     ->sourceInterface($settings->authListerInterface)
                     ->sourceInterfaceVersion($settings->authListerInterfaceVersion)
                     ->send();            
                                
          utilities\validateParameterApplicationJson($this, $authLister);
          
          utilities\deleteDataset();
          
          unset($authLister);
          unset($settings);
        }   
         
        public function testParameter_Mode_AccessUser_TargetWebservice_All_Serialization_APPLICATION_RDF_XML() {

          $settings = new Config();  
          
          utilities\deleteDataset();
          
          $this->assertTrue(utilities\createDataset(), "Can't create the dataset, check the /dataset/create/ endpoint first...");

          $authLister = new AuthListerQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
          
          $authLister->mime("application/rdf+xml")
                     ->getUserAccesses()
                     ->includeAllWebServiceUris()
                     ->sourceInterface($settings->authListerInterface)
                     ->sourceInterfaceVersion($settings->authListerInterfaceVersion)
                     ->send();            
                     
          utilities\validateParameterApplicationRdfXml($this, $authLister);

          utilities\deleteDataset();
          
          unset($authLister);
          unset($settings);
        }       
                          
        public function testParameter_Mode_AccessUser_TargetWebservice_All_Serialization_APPLICATION_RDF_N3() {

          $settings = new Config();  
          
          utilities\deleteDataset();
          
          $this->assertTrue(utilities\createDataset(), "Can't create the dataset, check the /dataset/create/ endpoint first...");
          
          $authLister = new AuthListerQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
          
          $authLister->mime("application/rdf+n3")
                     ->getUserAccesses()
                     ->includeAllWebServiceUris()
                     ->sourceInterface($settings->authListerInterface)
                     ->sourceInterfaceVersion($settings->authListerInterfaceVersion)
                     ->send();            
          
          utilities\validateParameterApplicationRdfN3($this, $authLister);
          
          utilities\deleteDataset();
          
          unset($authLister);
          unset($settings);
        }   
        
        public function testParameter_Mode_AccessUser_TargetWebservice_None_Serialization_TEXT_XML() {
          
          $settings = new Config();  
          
          utilities\deleteDataset();
          
          $this->assertTrue(utilities\createDataset(), "Can't create the dataset, check the /dataset/create/ endpoint first...");
          
          $authLister = new AuthListerQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
          
          $authLister->mime("text/xml")
                     ->getUserAccesses()
                     ->includeNoWebServiceUris()
                     ->sourceInterface($settings->authListerInterface)
                     ->sourceInterfaceVersion($settings->authListerInterfaceVersion)
                     ->send();            
                                
          utilities\validateParameterTextXml($this, $authLister);
          
          utilities\deleteDataset();
          
          unset($authLister);
          unset($settings);
        }  
        
        public function testParameter_Mode_AccessUser_TargetWebservice_None_Serialization_APPLICATION_JSON() {
          
          $settings = new Config();  
          
          utilities\deleteDataset();
          
          $this->assertTrue(utilities\createDataset(), "Can't create the dataset, check the /dataset/create/ endpoint first...");
          
          $authLister = new AuthListerQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
          
          $authLister->mime("application/json")
                     ->getUserAccesses()
                     ->includeNoWebServiceUris()
                     ->sourceInterface($settings->authListerInterface)
                     ->sourceInterfaceVersion($settings->authListerInterfaceVersion)
                     ->send();               
                                
          utilities\validateParameterApplicationJson($this, $authLister);
          
          utilities\deleteDataset();
          
          unset($authLister);
          unset($settings);
        }   
         
        public function testParameter_Mode_AccessUser_TargetWebservice_None_Serialization_APPLICATION_RDF_XML() {

          $settings = new Config();  
          
          utilities\deleteDataset();
          
          $this->assertTrue(utilities\createDataset(), "Can't create the dataset, check the /dataset/create/ endpoint first...");

          $authLister = new AuthListerQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
          
          $authLister->mime("application/rdf+xml")
                     ->getUserAccesses()
                     ->includeNoWebServiceUris()
                     ->sourceInterface($settings->authListerInterface)
                     ->sourceInterfaceVersion($settings->authListerInterfaceVersion)
                     ->send();     
                     
          utilities\validateParameterApplicationRdfXml($this, $authLister);

          utilities\deleteDataset();
          
          unset($authLister);
          unset($settings);
        }       
                          
        public function testParameter_Mode_AccessUser_TargetWebservice_None_Serialization_APPLICATION_RDF_N3() {

          $settings = new Config();  
          
          utilities\deleteDataset();
          
          $this->assertTrue(utilities\createDataset(), "Can't create the dataset, check the /dataset/create/ endpoint first...");
          
          $authLister = new AuthListerQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
          
          $authLister->mime("application/rdf+n3")
                     ->getUserAccesses()
                     ->includeNoWebServiceUris()
                     ->sourceInterface($settings->authListerInterface)
                     ->sourceInterfaceVersion($settings->authListerInterfaceVersion)
                     ->send();               
                                
          utilities\validateParameterApplicationRdfN3($this, $authLister);
          
          utilities\deleteDataset();
          
          unset($authLister);
          unset($settings);
        }
        
        public function testParameter_Mode_AccessUser_TargetWebservice_TargetWS_Serialization_TEXT_XML() {
          
          $settings = new Config();  
          
          utilities\deleteDataset();
          
          $this->assertTrue(utilities\createDataset(), "Can't create the dataset, check the /dataset/create/ endpoint first...");
          
          $authLister = new AuthListerQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
          
          $authLister->mime("text/xml")
                     ->getUserAccesses()
                     ->includeTargerWebServiceUri($settings->endpointUri."crud/create/")
                     ->sourceInterface($settings->authListerInterface)
                     ->sourceInterfaceVersion($settings->authListerInterfaceVersion)
                     ->send();               
                                
          utilities\validateParameterTextXml($this, $authLister);
          
          utilities\deleteDataset();
          
          unset($authLister);
          unset($settings);
        }  
        
        public function testParameter_Mode_AccessUser_TargetWebservice_TargetWS_Serialization_APPLICATION_JSON() {
          
          $settings = new Config();  
          
          utilities\deleteDataset();
          
          $this->assertTrue(utilities\createDataset(), "Can't create the dataset, check the /dataset/create/ endpoint first...");
          
          $authLister = new AuthListerQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
          
          $authLister->mime("application/json")
                     ->getUserAccesses()
                     ->includeNoWebServiceUris()
                     ->sourceInterface($settings->authListerInterface)
                     ->sourceInterfaceVersion($settings->authListerInterfaceVersion)
                     ->send();               
                                
          utilities\validateParameterApplicationJson($this, $authLister);
          
          utilities\deleteDataset();
          
          unset($authLister);
          unset($settings);
        }   
         
        public function testParameter_Mode_AccessUser_TargetWebservice_TargetWS_Serialization_APPLICATION_RDF_XML() {

          $settings = new Config();  
          
          utilities\deleteDataset();
          
          $this->assertTrue(utilities\createDataset(), "Can't create the dataset, check the /dataset/create/ endpoint first...");

          $authLister = new AuthListerQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
          
          $authLister->mime("application/rdf+xml")
                     ->getUserAccesses()
                     ->includeNoWebServiceUris()
                     ->sourceInterface($settings->authListerInterface)
                     ->sourceInterfaceVersion($settings->authListerInterfaceVersion)
                     ->send();               
                     
          utilities\validateParameterApplicationRdfXml($this, $authLister);

          utilities\deleteDataset();
          
          unset($authLister);
          unset($settings);
        }       
                          
        public function testParameter_Mode_AccessUser_TargetWebservice_TargetWS_Serialization_APPLICATION_RDF_N3() {

          $settings = new Config();  
          
          utilities\deleteDataset();
          
          $this->assertTrue(utilities\createDataset(), "Can't create the dataset, check the /dataset/create/ endpoint first...");
          
          $authLister = new AuthListerQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
          
          $authLister->mime("application/rdf+n3")
                     ->getUserAccesses()
                     ->includeNoWebServiceUris()
                     ->sourceInterface($settings->authListerInterface)
                     ->sourceInterfaceVersion($settings->authListerInterfaceVersion)
                     ->send();               
                                
          utilities\validateParameterApplicationRdfN3($this, $authLister);
          
          utilities\deleteDataset();
          
          unset($authLister);
          unset($settings);
        }  
        
        public function testParameter_Mode_Groups_Serialization_TEXT_XML() {
          
          $settings = new Config();  
          
          utilities\deleteDataset();
          
          $this->assertTrue(utilities\createDataset(), "Can't create the dataset, check the /dataset/create/ endpoint first...");
          
          $authLister = new AuthListerQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
          
          $authLister->mime("text/xml")
                     ->getGroups()
                     ->includeAllWebServiceUris()
                     ->sourceInterface($settings->authListerInterface)
                     ->sourceInterfaceVersion($settings->authListerInterfaceVersion)
                     ->send();
          
          utilities\validateParameterTextXml($this, $authLister);

          utilities\deleteDataset();
          
          unset($authLister);
          unset($settings);
        }  
        
        public function testParameter_Mode_Groups_Serialization_APPLICATION_JSON() {
          
          $settings = new Config();  

          utilities\deleteDataset();
          
          $this->assertTrue(utilities\createDataset(), "Can't create the dataset, check the /dataset/create/ endpoint first...");
          
          $authLister = new AuthListerQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
          
          $authLister->mime("application/json")
                     ->getGroups()
                     ->includeAllWebServiceUris()
                     ->sourceInterface($settings->authListerInterface)
                     ->sourceInterfaceVersion($settings->authListerInterfaceVersion)
                     ->send();
                                
          utilities\validateParameterApplicationJson($this, $authLister);

          utilities\deleteDataset();
          
          unset($authLister);
          unset($settings);
        }   
        
        public function testParameter_Mode_Groups_Serialization_APPLICATION_RDF_XML() {
          
          $settings = new Config();  

          utilities\deleteDataset();
          
          $this->assertTrue(utilities\createDataset(), "Can't create the dataset, check the /dataset/create/ endpoint first...");
          
          $authLister = new AuthListerQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
          
          $authLister->mime("application/rdf+xml")
                     ->getGroups()
                     ->includeAllWebServiceUris()
                     ->sourceInterface($settings->authListerInterface)
                     ->sourceInterfaceVersion($settings->authListerInterfaceVersion)
                     ->send();
                                          
          utilities\validateParameterApplicationRdfXml($this, $authLister);
          
          unset($authLister);
          unset($settings);
        }       
                          
        public function testParameter_Mode_Groups_Serialization_APPLICATION_RDF_N3() {
          
          $settings = new Config();  

          utilities\deleteDataset();
          
          $this->assertTrue(utilities\createDataset(), "Can't create the dataset, check the /dataset/create/ endpoint first...");
          
          $authLister = new AuthListerQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
          
          $authLister->mime("application/rdf+n3")
                     ->getGroups()
                     ->includeAllWebServiceUris()
                     ->sourceInterface($settings->authListerInterface)
                     ->sourceInterfaceVersion($settings->authListerInterfaceVersion)
                     ->send();
                                
          utilities\validateParameterApplicationRdfN3($this, $authLister);

          utilities\deleteDataset();
          
          unset($authLister);
          unset($settings);
        }
        
        public function testParameter_Mode_Group_Users_Serialization_TEXT_XML() {
          
          $settings = new Config();  
          
          utilities\deleteDataset();
          
          $this->assertTrue(utilities\createDataset(), "Can't create the dataset, check the /dataset/create/ endpoint first...");
          
          $authLister = new AuthListerQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
          
          $authLister->mime("text/xml")
                     ->getGroupUsers($settings->adminGroup)
                     ->includeAllWebServiceUris()
                     ->sourceInterface($settings->authListerInterface)
                     ->sourceInterfaceVersion($settings->authListerInterfaceVersion)
                     ->send();
          
          utilities\validateParameterTextXml($this, $authLister);

          utilities\deleteDataset();
          
          unset($authLister);
          unset($settings);
        }  
        
        public function testParameter_Mode_Group_Users_Serialization_APPLICATION_JSON() {
          
          $settings = new Config();  

          utilities\deleteDataset();
          
          $this->assertTrue(utilities\createDataset(), "Can't create the dataset, check the /dataset/create/ endpoint first...");
          
          $authLister = new AuthListerQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
          
          $authLister->mime("application/json")
                     ->getGroupUsers($settings->adminGroup)
                     ->includeAllWebServiceUris()
                     ->sourceInterface($settings->authListerInterface)
                     ->sourceInterfaceVersion($settings->authListerInterfaceVersion)
                     ->send();
                                
          utilities\validateParameterApplicationJson($this, $authLister);

          utilities\deleteDataset();
          
          unset($authLister);
          unset($settings);
        }   
        
        public function testParameter_Mode_Group_Users_Serialization_APPLICATION_RDF_XML() {
          
          $settings = new Config();  

          utilities\deleteDataset();
          
          $this->assertTrue(utilities\createDataset(), "Can't create the dataset, check the /dataset/create/ endpoint first...");
          
          $authLister = new AuthListerQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
          
          $authLister->mime("application/rdf+xml")
                     ->getGroupUsers($settings->adminGroup)
                     ->includeAllWebServiceUris()
                     ->sourceInterface($settings->authListerInterface)
                     ->sourceInterfaceVersion($settings->authListerInterfaceVersion)
                     ->send();
                                          
          utilities\validateParameterApplicationRdfXml($this, $authLister);
          
          unset($authLister);
          unset($settings);
        }       
                          
        public function testParameter_Mode_Group_Users_Serialization_APPLICATION_RDF_N3() {
          
          $settings = new Config();  

          utilities\deleteDataset();
          
          $this->assertTrue(utilities\createDataset(), "Can't create the dataset, check the /dataset/create/ endpoint first...");
          
          $authLister = new AuthListerQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
          
          $authLister->mime("application/rdf+n3")
                     ->getGroupUsers($settings->adminGroup)
                     ->includeAllWebServiceUris()
                     ->sourceInterface($settings->authListerInterface)
                     ->sourceInterfaceVersion($settings->authListerInterfaceVersion)
                     ->send();
                                
          utilities\validateParameterApplicationRdfN3($this, $authLister);

          utilities\deleteDataset();
          
          unset($authLister);
          unset($settings);
        }
        
        public function testParameter_Mode_User_Groups_Serialization_TEXT_XML() {
          
          $settings = new Config();  
          
          utilities\deleteDataset();
          
          $this->assertTrue(utilities\createDataset(), "Can't create the dataset, check the /dataset/create/ endpoint first...");
          
          $authLister = new AuthListerQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
          
          $authLister->mime("text/xml")
                     ->getUserGroups()
                     ->includeAllWebServiceUris()
                     ->sourceInterface($settings->authListerInterface)
                     ->sourceInterfaceVersion($settings->authListerInterfaceVersion)
                     ->send();
          
          utilities\validateParameterTextXml($this, $authLister);

          utilities\deleteDataset();
          
          unset($authLister);
          unset($settings);
        }  
        
        public function testParameter_Mode_User_Groups_Serialization_APPLICATION_JSON() {
          
          $settings = new Config();  

          utilities\deleteDataset();
          
          $this->assertTrue(utilities\createDataset(), "Can't create the dataset, check the /dataset/create/ endpoint first...");
          
          $authLister = new AuthListerQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
          
          $authLister->mime("application/json")
                     ->getUserGroups()
                     ->includeAllWebServiceUris()
                     ->sourceInterface($settings->authListerInterface)
                     ->sourceInterfaceVersion($settings->authListerInterfaceVersion)
                     ->send();
                                
          utilities\validateParameterApplicationJson($this, $authLister);

          utilities\deleteDataset();
          
          unset($authLister);
          unset($settings);
        }   
        
        public function testParameter_Mode_User_Groups_Serialization_APPLICATION_RDF_XML() {
          
          $settings = new Config();  

          utilities\deleteDataset();
          
          $this->assertTrue(utilities\createDataset(), "Can't create the dataset, check the /dataset/create/ endpoint first...");
          
          $authLister = new AuthListerQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
          
          $authLister->mime("application/rdf+xml")
                     ->getUserGroups()
                     ->includeAllWebServiceUris()
                     ->sourceInterface($settings->authListerInterface)
                     ->sourceInterfaceVersion($settings->authListerInterfaceVersion)
                     ->send();
                                          
          utilities\validateParameterApplicationRdfXml($this, $authLister);
          
          unset($authLister);
          unset($settings);
        }       
                          
        public function testParameter_Mode_User_Groups_Serialization_APPLICATION_RDF_N3() {
          
          $settings = new Config();  

          utilities\deleteDataset();
          
          $this->assertTrue(utilities\createDataset(), "Can't create the dataset, check the /dataset/create/ endpoint first...");
          
          $authLister = new AuthListerQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
          
          $authLister->mime("application/rdf+n3")
                     ->getUserGroups()
                     ->includeAllWebServiceUris()
                     ->sourceInterface($settings->authListerInterface)
                     ->sourceInterfaceVersion($settings->authListerInterfaceVersion)
                     ->send();
                                
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