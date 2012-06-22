<?php
  include_once("../tests/Config.php");
  include_once("../tests/validators.php");
  
  ini_set("memory_limit","256M");
  set_time_limit(3600);

  $settings = new Config(); 
  
  // Database connectivity procedures
  include_once($settings->structwsfInstanceFolder . "framework/WebServiceQuerier.php");
  include_once("../tests/utilities.php");
  
  class AuthValidatorTest extends PHPUnit_Framework_TestCase {
    
    static private $outputs = array();

    public function testWrongEndpointUrl() {
      
      $settings = new Config();          
      
      $wsq = new WebServiceQuerier($settings->endpointUrl . "auth/validator/" . "wrong", 
                                   "post", 
                                   "text/xml",
                                   "ip=" . urlencode($_SERVER['REMOTE_ADDR']) .
                                   "&datasets=" . urlencode($settings->testDataset) .
                                   "&ws_uri=" . urlencode($settings->endpointUrl."crud/create/"));
                   
      $this->assertEquals($wsq->getStatus(), "404", "Debugging information: ".var_export($wsq, TRUE));                                       
      $this->assertEquals($wsq->getStatusMessage(), "Not Found", "Debugging information: ".var_export($wsq, TRUE));
      
      unset($wsq);
      unset($settings);
    }
    
    
    public function testWrongEndpointMethodGet() {
      
      $settings = new Config();  
      
      $wsq = new WebServiceQuerier($settings->endpointUrl . "auth/validator/", 
                                   "get", 
                                   "text/xml",
                                   "ip=" . urlencode($_SERVER['REMOTE_ADDR']) .
                                   "&datasets=" . urlencode($settings->testDataset) .
                                   "&ws_uri=" . urlencode($settings->endpointUrl."crud/create/"));
                                   
      $this->assertEquals($wsq->getStatus(), "405", "Debugging information: ".var_export($wsq, TRUE));                                       
      $this->assertEquals($wsq->getStatusMessage(), "Method Not Allowed", "Debugging information: ".var_export($wsq, TRUE));          
      
      unset($wsq);
      unset($settings);
    }    
    
    public function  testNoRequesterIP() {
      
      $settings = new Config();  
      
      deleteDataset();
      
      $this->assertTrue(createDataset(), "Can't create the dataset, check the /dataset/create/ endpoint first...");
            
      $wsq = new WebServiceQuerier($settings->endpointUrl . "auth/validator/", 
                                   "post", 
                                   "text/xml",
                                   "ip=" . 
                                   "&datasets=" . urlencode($settings->testDataset) .
                                   "&ws_uri=" . urlencode($settings->endpointUrl."crud/create/"));
                                   
      $this->assertEquals($wsq->getStatus(), "400", "Debugging information: ".var_export($wsq, TRUE));                                       
      $this->assertEquals($wsq->getStatusMessage(), "Bad Request", "Debugging information: ".var_export($wsq, TRUE));
      $this->assertEquals($wsq->error->id, "WS-AUTH-VALIDATOR-200", "Debugging information: ".var_export($wsq, TRUE));                                       

      deleteDataset();
      
      unset($wsq);
      unset($settings);
    } 
    
    public function  testNoTargetDataset() {
      
      $settings = new Config();  
      
      deleteDataset();
      
      $this->assertTrue(createDataset(), "Can't create the dataset, check the /dataset/create/ endpoint first...");
            
      $wsq = new WebServiceQuerier($settings->endpointUrl . "auth/validator/", 
                                   "post", 
                                   "text/xml",
                                   "ip=" . urlencode($_SERVER['REMOTE_ADDR']) .
                                   "&datasets=" . 
                                   "&ws_uri=" . urlencode($settings->endpointUrl."crud/create/"));
                                   
      $this->assertEquals($wsq->getStatus(), "400", "Debugging information: ".var_export($wsq, TRUE));                                       
      $this->assertEquals($wsq->getStatusMessage(), "Bad Request", "Debugging information: ".var_export($wsq, TRUE));
      $this->assertEquals($wsq->error->id, "WS-AUTH-VALIDATOR-201", "Debugging information: ".var_export($wsq, TRUE));                                       

      deleteDataset();
      
      unset($wsq);
      unset($settings);
    } 
    
    public function  testNoWebServiceURI() {
      
      $settings = new Config();  
      
      deleteDataset();
      
      $this->assertTrue(createDataset(), "Can't create the dataset, check the /dataset/create/ endpoint first...");
            
      $wsq = new WebServiceQuerier($settings->endpointUrl . "auth/validator/", 
                                   "post", 
                                   "text/xml",
                                   "ip=" . urlencode($_SERVER['REMOTE_ADDR']) .
                                   "&datasets=" . urlencode($settings->testDataset) .
                                   "&ws_uri=");
                                   
      $this->assertEquals($wsq->getStatus(), "400", "Debugging information: ".var_export($wsq, TRUE));                                       
      $this->assertEquals($wsq->getStatusMessage(), "Bad Request", "Debugging information: ".var_export($wsq, TRUE));
      $this->assertEquals($wsq->error->id, "WS-AUTH-VALIDATOR-202", "Debugging information: ".var_export($wsq, TRUE));                                       

      deleteDataset();
      
      unset($wsq);
      unset($settings);
    }     
    
    public function  testInvalidDatasetIRI() {
      
      $settings = new Config();  
      
      deleteDataset();
      
      $this->assertTrue(createDataset(), "Can't create the dataset, check the /dataset/create/ endpoint first...");
            
      $wsq = new WebServiceQuerier($settings->endpointUrl . "auth/validator/", 
                                   "post", 
                                   "text/xml",
                                   "ip=" . urlencode($_SERVER['REMOTE_ADDR']) .
                                   "&datasets=" . urlencode($settings->testDataset) . ";" . urlencode($settings->testDataset . "<>") .
                                   "&ws_uri=" . urlencode($settings->endpointUrl."crud/create/"));
                                   
      $this->assertEquals($wsq->getStatus(), "400", "Debugging information: ".var_export($wsq, TRUE));                                       
      $this->assertEquals($wsq->getStatusMessage(), "Bad Request", "Debugging information: ".var_export($wsq, TRUE));
      $this->assertEquals($wsq->error->id, "WS-AUTH-VALIDATOR-203", "Debugging information: ".var_export($wsq, TRUE));                                       

      deleteDataset();
      
      unset($wsq);
      unset($settings);
    }     
    
    
    public function  testInvalidWebServiceIRI() {
      
      $settings = new Config();  
      
      deleteDataset();
      
      $this->assertTrue(createDataset(), "Can't create the dataset, check the /dataset/create/ endpoint first...");
            
      $wsq = new WebServiceQuerier($settings->endpointUrl . "auth/validator/", 
                                   "post", 
                                   "text/xml",
                                   "ip=" . urlencode($_SERVER['REMOTE_ADDR']) .
                                   "&datasets=" . urlencode($settings->testDataset) .
                                   "&ws_uri=" . urlencode($settings->endpointUrl."crud/create/" . "<>"));
                                   
      $this->assertEquals($wsq->getStatus(), "400", "Debugging information: ".var_export($wsq, TRUE));                                       
      $this->assertEquals($wsq->getStatusMessage(), "Bad Request", "Debugging information: ".var_export($wsq, TRUE));
      $this->assertEquals($wsq->error->id, "WS-AUTH-VALIDATOR-204", "Debugging information: ".var_export($wsq, TRUE));                                       

      deleteDataset();
      
      unset($wsq);
      unset($settings);
    }    
    
    public function  testValidateQuery() {
      
      $settings = new Config();  
      
      deleteDataset();
      
      $this->assertTrue(createDataset(), "Can't create the dataset, check the /dataset/create/ endpoint first...");
            
      $wsq = new WebServiceQuerier($settings->endpointUrl . "auth/validator/", 
                                   "post", 
                                   "text/xml",
                                   "ip=" . urlencode($settings->requesterIP) .
                                   "&datasets=" . urlencode($settings->testDataset) .
                                   "&ws_uri=" . urlencode($settings->endpointUri."crud/create/"));
                                   
      $this->assertEquals($wsq->getStatus(), "200", "Debugging information: ".var_export($wsq, TRUE));                                       

      deleteDataset();
      
      unset($wsq);
      unset($settings);
    }   
    
    public function  testTargetWebServiceNotRegistered() {
      
      $settings = new Config();  
      
      deleteDataset();
      
      $this->assertTrue(createDataset(), "Can't create the dataset, check the /dataset/create/ endpoint first...");
            
      $wsq = new WebServiceQuerier($settings->endpointUrl . "auth/validator/", 
                                   "post", 
                                   "text/xml",
                                   "ip=" . urlencode($settings->requesterIP) .
                                   "&datasets=" . urlencode($settings->testDataset) .
                                   "&ws_uri=" . urlencode($settings->endpointUri."crud/create/" . "not-registered/"));
                                   
      $this->assertEquals($wsq->getStatus(), "500", "Debugging information: ".var_export($wsq, TRUE));                                       
      $this->assertEquals($wsq->getStatusMessage(), "Internal Error", "Debugging information: ".var_export($wsq, TRUE));
      $this->assertEquals($wsq->error->id, "WS-AUTH-VALIDATOR-301", "Debugging information: ".var_export($wsq, TRUE));                                       
                                      

      deleteDataset();
      
      unset($wsq);
      unset($settings);
    }    
                    
    public function  testDatasetNotExisting() {
      
      $settings = new Config();  
      
      deleteDataset();
      
      $this->assertTrue(createDataset(), "Can't create the dataset, check the /dataset/create/ endpoint first...");
            
      $wsq = new WebServiceQuerier($settings->endpointUrl . "auth/validator/", 
                                   "post", 
                                   "text/xml",
                                   "ip=" . urlencode($settings->requesterIP) .
                                   "&datasets=" . urlencode($settings->testDataset . "not-existing/") .
                                   "&ws_uri=" . urlencode($settings->endpointUri."crud/create/"));
                                   
      $this->assertEquals($wsq->getStatus(), "403", "Debugging information: ".var_export($wsq, TRUE));                                       
      $this->assertEquals($wsq->getStatusMessage(), "Forbidden", "Debugging information: ".var_export($wsq, TRUE));
      $this->assertEquals($wsq->error->id, "WS-AUTH-VALIDATOR-303", "Debugging information: ".var_export($wsq, TRUE));                                       
                                      

      deleteDataset();
      
      unset($wsq);
      unset($settings);
    } 
                    
    public function  testOneOfDatasetNotExisting() {
      
      $settings = new Config();  
      
      deleteDataset();
      
      $this->assertTrue(createDataset(), "Can't create the dataset, check the /dataset/create/ endpoint first...");
            
      $wsq = new WebServiceQuerier($settings->endpointUrl . "auth/validator/", 
                                   "post", 
                                   "text/xml",
                                   "ip=" . urlencode($settings->requesterIP) .
                                   "&datasets=" . urlencode($settings->testDataset) . ";" . urlencode($settings->testDataset . "not-existing/") .
                                   "&ws_uri=" . urlencode($settings->endpointUri."crud/create/"));
                                   
      $this->assertEquals($wsq->getStatus(), "403", "Debugging information: ".var_export($wsq, TRUE));                                       
      $this->assertEquals($wsq->getStatusMessage(), "Forbidden", "Debugging information: ".var_export($wsq, TRUE));
      $this->assertEquals($wsq->error->id, "WS-AUTH-VALIDATOR-303", "Debugging information: ".var_export($wsq, TRUE));                                       
                                      

      deleteDataset();
      
      unset($wsq);
      unset($settings);
    } 
    
    public function  testNoCreatePermissions() {
      
      $settings = new Config();  
      
      deleteDataset();
      
      $this->assertTrue(createDatasetGlobalPermissionsNone(), "Can't create the dataset, check the /dataset/create/ endpoint first...");
      $this->assertTrue(createNoAccess_AccessRecord(), "Can't create the access record, check the /auth/registrar/access/ endpoint first...");
            
      $wsq = new WebServiceQuerier($settings->endpointUrl . "auth/validator/", 
                                   "post", 
                                   "text/xml",
                                   "ip=" . urlencode($settings->randomRequester) .
                                   "&datasets=" . urlencode($settings->testDataset) .
                                   "&ws_uri=" . urlencode($settings->endpointUri."crud/create/"));
                                   
      $this->assertEquals($wsq->getStatus(), "403", "Debugging information: ".var_export($wsq, TRUE));                                       
      $this->assertEquals($wsq->getStatusMessage(), "Forbidden", "Debugging information: ".var_export($wsq, TRUE));
      $this->assertEquals($wsq->error->id, "WS-AUTH-VALIDATOR-304", "Debugging information: ".var_export($wsq, TRUE));                                       
                                      

      deleteDataset();
      
      unset($wsq);
      unset($settings);
    }    
    
    public function  testNoDeletePermissions() {
      
      $settings = new Config();  
      
      deleteDataset();
      
      $this->assertTrue(createDatasetGlobalPermissionsNone(), "Can't create the dataset, check the /dataset/create/ endpoint first...");
      $this->assertTrue(createNoAccess_AccessRecord(), "Can't create the access record, check the /auth/registrar/access/ endpoint first...");
            
      $wsq = new WebServiceQuerier($settings->endpointUrl . "auth/validator/", 
                                   "post", 
                                   "text/xml",
                                   "ip=" . urlencode($settings->randomRequester) .
                                   "&datasets=" . urlencode($settings->testDataset) .
                                   "&ws_uri=" . urlencode($settings->endpointUri."crud/delete/"));
                                   
      $this->assertEquals($wsq->getStatus(), "403", "Debugging information: ".var_export($wsq, TRUE));                                       
      $this->assertEquals($wsq->getStatusMessage(), "Forbidden", "Debugging information: ".var_export($wsq, TRUE));
      $this->assertEquals($wsq->error->id, "WS-AUTH-VALIDATOR-307", "Debugging information: ".var_export($wsq, TRUE));                                       
                                      

      deleteDataset();
      
      unset($wsq);
      unset($settings);
    } 
    
    public function  testNoUpdatePermissions() {
      
      $settings = new Config();  
      
      deleteDataset();
      
      $this->assertTrue(createDatasetGlobalPermissionsNone(), "Can't create the dataset, check the /dataset/create/ endpoint first...");
      $this->assertTrue(createNoAccess_AccessRecord(), "Can't create the access record, check the /auth/registrar/access/ endpoint first...");
            
      $wsq = new WebServiceQuerier($settings->endpointUrl . "auth/validator/", 
                                   "post", 
                                   "text/xml",
                                   "ip=" . urlencode($settings->randomRequester) .
                                   "&datasets=" . urlencode($settings->testDataset) .
                                   "&ws_uri=" . urlencode($settings->endpointUri."crud/update/"));
                                   
      $this->assertEquals($wsq->getStatus(), "403", "Debugging information: ".var_export($wsq, TRUE));                                       
      $this->assertEquals($wsq->getStatusMessage(), "Forbidden", "Debugging information: ".var_export($wsq, TRUE));
      $this->assertEquals($wsq->error->id, "WS-AUTH-VALIDATOR-305", "Debugging information: ".var_export($wsq, TRUE));                                       
                                      

      deleteDataset();
      
      unset($wsq);
      unset($settings);
    }  
    
    public function  testNoReadPermissions() {
      
      $settings = new Config();  
      
      deleteDataset();
      
      $this->assertTrue(createDatasetGlobalPermissionsNone(), "Can't create the dataset, check the /dataset/create/ endpoint first...");
      $this->assertTrue(createNoAccess_AccessRecord(), "Can't create the access record, check the /auth/registrar/access/ endpoint first...");
            
      $wsq = new WebServiceQuerier($settings->endpointUrl . "auth/validator/", 
                                   "post", 
                                   "text/xml",
                                   "ip=" . urlencode($settings->randomRequester) .
                                   "&datasets=" . urlencode($settings->testDataset) .
                                   "&ws_uri=" . urlencode($settings->endpointUri."crud/read/"));
                                   
      $this->assertEquals($wsq->getStatus(), "403", "Debugging information: ".var_export($wsq, TRUE));                                       
      $this->assertEquals($wsq->getStatusMessage(), "Forbidden", "Debugging information: ".var_export($wsq, TRUE));
      $this->assertEquals($wsq->error->id, "WS-AUTH-VALIDATOR-306", "Debugging information: ".var_export($wsq, TRUE));                                       
                                      

      deleteDataset();
      
      unset($wsq);
      unset($settings);
    } 

    public function  testCreatePermissionsWithGlobalUser() {
      
      $settings = new Config();  
      
      deleteDataset();
      
      $this->assertTrue(createDataset(), "Can't create the dataset, check the /dataset/create/ endpoint first...");
      $this->assertTrue(createNoAccess_AccessRecord(), "Can't create the access record, check the /auth/registrar/access/ endpoint first...");
            
      $wsq = new WebServiceQuerier($settings->endpointUrl . "auth/validator/", 
                                   "post", 
                                   "text/xml",
                                   "ip=" . urlencode($settings->randomRequester) .
                                   "&datasets=" . urlencode($settings->testDataset) .
                                   "&ws_uri=" . urlencode($settings->endpointUri."crud/create/"));
                                   
      $this->assertEquals($wsq->getStatus(), "200", "Debugging information: ".var_export($wsq, TRUE));                                       

      deleteDataset();
      
      unset($wsq);
      unset($settings);
    }    
    
    public function  testDeletePermissionsWithGlobalUser() {
      
      $settings = new Config();  
      
      deleteDataset();
      
      $this->assertTrue(createDataset(), "Can't create the dataset, check the /dataset/create/ endpoint first...");
      $this->assertTrue(createNoAccess_AccessRecord(), "Can't create the access record, check the /auth/registrar/access/ endpoint first...");
            
      $wsq = new WebServiceQuerier($settings->endpointUrl . "auth/validator/", 
                                   "post", 
                                   "text/xml",
                                   "ip=" . urlencode($settings->randomRequester) .
                                   "&datasets=" . urlencode($settings->testDataset) .
                                   "&ws_uri=" . urlencode($settings->endpointUri."crud/delete/"));
                                   
      $this->assertEquals($wsq->getStatus(), "200", "Debugging information: ".var_export($wsq, TRUE));                                       

      deleteDataset();
      
      unset($wsq);
      unset($settings);
    }    

    public function  testUpdatePermissionsWithGlobalUser() {
      
      $settings = new Config();  
      
      deleteDataset();
      
      $this->assertTrue(createDataset(), "Can't create the dataset, check the /dataset/create/ endpoint first...");
      $this->assertTrue(createNoAccess_AccessRecord(), "Can't create the access record, check the /auth/registrar/access/ endpoint first...");
            
      $wsq = new WebServiceQuerier($settings->endpointUrl . "auth/validator/", 
                                   "post", 
                                   "text/xml",
                                   "ip=" . urlencode($settings->randomRequester) .
                                   "&datasets=" . urlencode($settings->testDataset) .
                                   "&ws_uri=" . urlencode($settings->endpointUri."crud/update/"));
                                   
      $this->assertEquals($wsq->getStatus(), "200", "Debugging information: ".var_export($wsq, TRUE));                                       

      deleteDataset();
      
      unset($wsq);
      unset($settings);
    }    
    
    public function  testReadPermissionsWithGlobalUser() {
      
      $settings = new Config();  
      
      deleteDataset();
      
      $this->assertTrue(createDataset(), "Can't create the dataset, check the /dataset/create/ endpoint first...");
      $this->assertTrue(createNoAccess_AccessRecord(), "Can't create the access record, check the /auth/registrar/access/ endpoint first...");
            
      $wsq = new WebServiceQuerier($settings->endpointUrl . "auth/validator/", 
                                   "post", 
                                   "text/xml",
                                   "ip=" . urlencode($settings->randomRequester) .
                                   "&datasets=" . urlencode($settings->testDataset) .
                                   "&ws_uri=" . urlencode($settings->endpointUri."crud/read/"));
                                   
      $this->assertEquals($wsq->getStatus(), "200", "Debugging information: ".var_export($wsq, TRUE));                                       

      deleteDataset();
      
      unset($wsq);
      unset($settings);
    }    
    
    
    public function  testNoCreatePermissionsTwoDatasets() {
      
      $settings = new Config();  
      
      deleteTwoDatasets();
      
      $this->assertTrue(createTwoDatasetsGlobalPermissionsNone(), "Can't create the dataset, check the /dataset/create/ endpoint first...");
      $this->assertTrue(createNoAccess_AccessRecord(), "Can't create the access record, check the /auth/registrar/access/ endpoint first...");
            
      $wsq = new WebServiceQuerier($settings->endpointUrl . "auth/validator/", 
                                   "post", 
                                   "text/xml",
                                   "ip=" . urlencode($settings->randomRequester) .
                                   "&datasets=" . urlencode($settings->testDataset) . ";" . urlencode($settings->testDataset."2/") .
                                   "&ws_uri=" . urlencode($settings->endpointUri."crud/create/"));
                                   
      $this->assertEquals($wsq->getStatus(), "403", "Debugging information: ".var_export($wsq, TRUE));                                       
      $this->assertEquals($wsq->getStatusMessage(), "Forbidden", "Debugging information: ".var_export($wsq, TRUE));
      $this->assertEquals($wsq->error->id, "WS-AUTH-VALIDATOR-304", "Debugging information: ".var_export($wsq, TRUE));                                       
                                      

      deleteTwoDatasets();
      
      unset($wsq);
      unset($settings);
    }    
    
    public function  testNoDeletePermissionsTwoDatasets() {
      
      $settings = new Config();  
      
      deleteTwoDatasets();
      
      $this->assertTrue(createTwoDatasetsGlobalPermissionsNone(), "Can't create the dataset, check the /dataset/create/ endpoint first...");
      $this->assertTrue(createNoAccess_AccessRecord(), "Can't create the access record, check the /auth/registrar/access/ endpoint first...");
            
      $wsq = new WebServiceQuerier($settings->endpointUrl . "auth/validator/", 
                                   "post", 
                                   "text/xml",
                                   "ip=" . urlencode($settings->randomRequester) .
                                   "&datasets=" . urlencode($settings->testDataset) . ";" . urlencode($settings->testDataset."2/") .
                                   "&ws_uri=" . urlencode($settings->endpointUri."crud/delete/"));
                                   
      $this->assertEquals($wsq->getStatus(), "403", "Debugging information: ".var_export($wsq, TRUE));                                       
      $this->assertEquals($wsq->getStatusMessage(), "Forbidden", "Debugging information: ".var_export($wsq, TRUE));
      $this->assertEquals($wsq->error->id, "WS-AUTH-VALIDATOR-307", "Debugging information: ".var_export($wsq, TRUE));                                       
                                      

      deleteTwoDatasets();
      
      unset($wsq);
      unset($settings);
    }    
    
    public function  testNoUpdatePermissionsTwoDatasets() {
      
      $settings = new Config();  
      
      deleteTwoDatasets();
      
      $this->assertTrue(createTwoDatasetsGlobalPermissionsNone(), "Can't create the dataset, check the /dataset/create/ endpoint first...");
      $this->assertTrue(createNoAccess_AccessRecord(), "Can't create the access record, check the /auth/registrar/access/ endpoint first...");
            
      $wsq = new WebServiceQuerier($settings->endpointUrl . "auth/validator/", 
                                   "post", 
                                   "text/xml",
                                   "ip=" . urlencode($settings->randomRequester) .
                                   "&datasets=" . urlencode($settings->testDataset) . ";" . urlencode($settings->testDataset."2/") .
                                   "&ws_uri=" . urlencode($settings->endpointUri."crud/update/"));
                                   
      $this->assertEquals($wsq->getStatus(), "403", "Debugging information: ".var_export($wsq, TRUE));                                       
      $this->assertEquals($wsq->getStatusMessage(), "Forbidden", "Debugging information: ".var_export($wsq, TRUE));
      $this->assertEquals($wsq->error->id, "WS-AUTH-VALIDATOR-305", "Debugging information: ".var_export($wsq, TRUE));                                       
                                      

      deleteTwoDatasets();
      
      unset($wsq);
      unset($settings);
    }    
    
    public function  testNoReadPermissionsTwoDatasets() {
      
      $settings = new Config();  
      
      deleteTwoDatasets();
      
      $this->assertTrue(createTwoDatasetsGlobalPermissionsNone(), "Can't create the dataset, check the /dataset/create/ endpoint first...");
      $this->assertTrue(createNoAccess_AccessRecord(), "Can't create the access record, check the /auth/registrar/access/ endpoint first...");
            
      $wsq = new WebServiceQuerier($settings->endpointUrl . "auth/validator/", 
                                   "post", 
                                   "text/xml",
                                   "ip=" . urlencode($settings->randomRequester) .
                                   "&datasets=" . urlencode($settings->testDataset) . ";" . urlencode($settings->testDataset."2/") .
                                   "&ws_uri=" . urlencode($settings->endpointUri."crud/read/"));
                                   
      $this->assertEquals($wsq->getStatus(), "403", "Debugging information: ".var_export($wsq, TRUE));                                       
      $this->assertEquals($wsq->getStatusMessage(), "Forbidden", "Debugging information: ".var_export($wsq, TRUE));
      $this->assertEquals($wsq->error->id, "WS-AUTH-VALIDATOR-306", "Debugging information: ".var_export($wsq, TRUE));                                       
                                      

      deleteTwoDatasets();
      
      unset($wsq);
      unset($settings);
    }  
    
    public function  testCreatePermissionsWithGlobalUserTwoDatasets() {
      
      $settings = new Config();  
      
      deleteTwoDatasets();
      
      $this->assertTrue(createTwoDatasets(), "Can't create the dataset, check the /dataset/create/ endpoint first...");
      $this->assertTrue(createNoAccess_AccessRecord(), "Can't create the access record, check the /auth/registrar/access/ endpoint first...");
            
      $wsq = new WebServiceQuerier($settings->endpointUrl . "auth/validator/", 
                                   "post", 
                                   "text/xml",
                                   "ip=" . urlencode($settings->randomRequester) .
                                   "&datasets=" . urlencode($settings->testDataset) . ";" . urlencode($settings->testDataset."2/") .
                                   "&ws_uri=" . urlencode($settings->endpointUri."crud/create/"));
                                   
      $this->assertEquals($wsq->getStatus(), "200", "Debugging information: ".var_export($wsq, TRUE));                                       

      deleteTwoDatasets();
      
      unset($wsq);
      unset($settings);
    }        
    
    public function  testUpdatePermissionsWithGlobalUserTwoDatasets() {
      
      $settings = new Config();  
      
      deleteTwoDatasets();
      
      $this->assertTrue(createTwoDatasets(), "Can't create the dataset, check the /dataset/create/ endpoint first...");
      $this->assertTrue(createNoAccess_AccessRecord(), "Can't create the access record, check the /auth/registrar/access/ endpoint first...");
            
      $wsq = new WebServiceQuerier($settings->endpointUrl . "auth/validator/", 
                                   "post", 
                                   "text/xml",
                                   "ip=" . urlencode($settings->randomRequester) .
                                   "&datasets=" . urlencode($settings->testDataset) . ";" . urlencode($settings->testDataset."2/") .
                                   "&ws_uri=" . urlencode($settings->endpointUri."crud/update/"));
                                   
      $this->assertEquals($wsq->getStatus(), "200", "Debugging information: ".var_export($wsq, TRUE));                                       

      deleteTwoDatasets();
      
      unset($wsq);
      unset($settings);
    }        
    
    
    public function  testDeletePermissionsWithGlobalUserTwoDatasets() {
      
      $settings = new Config();  
      
      deleteTwoDatasets();
      
      $this->assertTrue(createTwoDatasets(), "Can't create the dataset, check the /dataset/create/ endpoint first...");
      $this->assertTrue(createNoAccess_AccessRecord(), "Can't create the access record, check the /auth/registrar/access/ endpoint first...");
            
      $wsq = new WebServiceQuerier($settings->endpointUrl . "auth/validator/", 
                                   "post", 
                                   "text/xml",
                                   "ip=" . urlencode($settings->randomRequester) .
                                   "&datasets=" . urlencode($settings->testDataset) . ";" . urlencode($settings->testDataset."2/") .
                                   "&ws_uri=" . urlencode($settings->endpointUri."crud/delete/"));
                                   
      $this->assertEquals($wsq->getStatus(), "200", "Debugging information: ".var_export($wsq, TRUE));                                       

      deleteTwoDatasets();
      
      unset($wsq);
      unset($settings);
    }        
    
    public function  testReadPermissionsWithGlobalUserTwoDatasets() {
      
      $settings = new Config();  
      
      deleteTwoDatasets();
      
      $this->assertTrue(createTwoDatasets(), "Can't create the dataset, check the /dataset/create/ endpoint first...");
      $this->assertTrue(createNoAccess_AccessRecord(), "Can't create the access record, check the /auth/registrar/access/ endpoint first...");
            
      $wsq = new WebServiceQuerier($settings->endpointUrl . "auth/validator/", 
                                   "post", 
                                   "text/xml",
                                   "ip=" . urlencode($settings->randomRequester) .
                                   "&datasets=" . urlencode($settings->testDataset) . ";" . urlencode($settings->testDataset."2/") .
                                   "&ws_uri=" . urlencode($settings->endpointUri."crud/read/"));
                                   
      $this->assertEquals($wsq->getStatus(), "200", "Debugging information: ".var_export($wsq, TRUE));                                       

      deleteTwoDatasets();
      
      unset($wsq);
      unset($settings);
    }        
  }

  
?>