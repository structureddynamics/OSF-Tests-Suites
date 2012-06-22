<?php

  namespace StructuredDynamics\structwsf\tests\ws\auth\validator;
  
  use \StructuredDynamics\structwsf\framework\WebServiceQuerier;
  use \StructuredDynamics\structwsf\php\api\ws\auth\validator\AuthValidatorQuery;
  use \StructuredDynamics\structwsf\tests\Config;
  use \StructuredDynamics\structwsf\tests as utilities;
   
  include_once("../../SplClassLoader.php");
  include_once("../tests/validators.php");
  include_once("../tests/utilities.php");  
  
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
    
  class AuthValidatorTest extends \PHPUnit_Framework_TestCase {
    
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
      
      utilities\deleteDataset();

      $this->assertTrue(utilities\createDataset(), "Can't create the dataset, check the /dataset/create/ endpoint first...");
      
      $authValidator = new AuthValidatorQuery($settings->endpointUrl);     
      
      $authValidator->ip("");
      
      $authValidator->datasets(array($settings->testDataset));
      
      $authValidator->webServiceUri($settings->endpointUri."crud/create/");
      
      $authValidator->send();
      
      $this->assertEquals($authValidator->getStatus(), "400", "Debugging information: ".var_export($authValidator, TRUE));                                       
      $this->assertEquals($authValidator->getStatusMessage(), "Bad Request", "Debugging information: ".var_export($authValidator, TRUE));
      $this->assertEquals($authValidator->error->id, "WS-AUTH-VALIDATOR-200", "Debugging information: ".var_export($authValidator, TRUE));                                       

      utilities\deleteDataset();
      
      unset($authValidator);
      unset($settings);
    } 
    
    public function  testNoTargetDataset() {
      
      $settings = new Config();  
      
      utilities\deleteDataset();
      
      $this->assertTrue(utilities\createDataset(), "Can't create the dataset, check the /dataset/create/ endpoint first...");
            
      $authValidator = new AuthValidatorQuery($settings->endpointUrl);     
      
      $authValidator->ip($_SERVER['REMOTE_ADDR']);
      
      $authValidator->datasets(array());
      
      $authValidator->webServiceUri($settings->endpointUri."crud/create/");
      
      $authValidator->send();            
            
      $this->assertEquals($authValidator->getStatus(), "400", "Debugging information: ".var_export($authValidator, TRUE));                                       
      $this->assertEquals($authValidator->getStatusMessage(), "Bad Request", "Debugging information: ".var_export($authValidator, TRUE));
      $this->assertEquals($authValidator->error->id, "WS-AUTH-VALIDATOR-201", "Debugging information: ".var_export($authValidator, TRUE));                                       

      utilities\deleteDataset();
      
      unset($authValidator);
      unset($settings);
    } 
    
    public function  testNoWebServiceURI() {
      
      $settings = new Config();  
      
      utilities\deleteDataset();
      
      $this->assertTrue(utilities\createDataset(), "Can't create the dataset, check the /dataset/create/ endpoint first...");
            
      $authValidator = new AuthValidatorQuery($settings->endpointUrl);     
      
      $authValidator->ip($_SERVER['REMOTE_ADDR']);
      
      $authValidator->datasets(array($settings->testDataset));
      
      $authValidator->webServiceUri("");
      
      $authValidator->send();             
                                   
      $this->assertEquals($authValidator->getStatus(), "400", "Debugging information: ".var_export($authValidator, TRUE));                                       
      $this->assertEquals($authValidator->getStatusMessage(), "Bad Request", "Debugging information: ".var_export($authValidator, TRUE));
      $this->assertEquals($authValidator->error->id, "WS-AUTH-VALIDATOR-202", "Debugging information: ".var_export($authValidator, TRUE));                                       

      utilities\deleteDataset();
      
      unset($authValidator);
      unset($settings);
    }     
    
    public function  testInvalidDatasetIRI() {
      
      $settings = new Config();  
      
      utilities\deleteDataset();
      
      $this->assertTrue(utilities\createDataset(), "Can't create the dataset, check the /dataset/create/ endpoint first...");
            
      $authValidator = new AuthValidatorQuery($settings->endpointUrl);     
      
      $authValidator->ip($_SERVER['REMOTE_ADDR']);
      
      $authValidator->datasets(array($settings->testDataset, $settings->testDataset."<>"));
      
      $authValidator->webServiceUri($settings->endpointUri."crud/create/");
      
      $authValidator->send();                                        
                                   
      $this->assertEquals($authValidator->getStatus(), "400", "Debugging information: ".var_export($authValidator, TRUE));                                       
      $this->assertEquals($authValidator->getStatusMessage(), "Bad Request", "Debugging information: ".var_export($authValidator, TRUE));
      $this->assertEquals($authValidator->error->id, "WS-AUTH-VALIDATOR-203", "Debugging information: ".var_export($authValidator, TRUE));                                       

      utilities\deleteDataset();
      
      unset($authValidator);
      unset($settings);
    }     
    
    public function  testInvalidWebServiceIRI() {
      
      $settings = new Config();  
      
      utilities\deleteDataset();
      
      $this->assertTrue(utilities\createDataset(), "Can't create the dataset, check the /dataset/create/ endpoint first...");
            
      $authValidator = new AuthValidatorQuery($settings->endpointUrl);     
      
      $authValidator->ip($_SERVER['REMOTE_ADDR']);
      
      $authValidator->datasets(array($settings->testDataset));
      
      $authValidator->webServiceUri($settings->endpointUri."wsf/crud/create/"."<>");
      
      $authValidator->send();               
                                   
      $this->assertEquals($authValidator->getStatus(), "400", "Debugging information: ".var_export($authValidator, TRUE));                                       
      $this->assertEquals($authValidator->getStatusMessage(), "Bad Request", "Debugging information: ".var_export($authValidator, TRUE));
      $this->assertEquals($authValidator->error->id, "WS-AUTH-VALIDATOR-204", "Debugging information: ".var_export($authValidator, TRUE));                                       

      utilities\deleteDataset();
      
      unset($authValidator);
      unset($settings);
    }    
    
    public function  testValidateQuery() {
      
      $settings = new Config();  
      
      utilities\deleteDataset();
      
      $this->assertTrue(utilities\createDataset(), "Can't create the dataset, check the /dataset/create/ endpoint first...");
            
      $authValidator = new AuthValidatorQuery($settings->endpointUrl);     
      
      $authValidator->ip($_SERVER['REMOTE_ADDR']);
      
      $authValidator->datasets(array($settings->testDataset));
      
      $authValidator->webServiceUri($settings->endpointUri."crud/create/");
      
      $authValidator->send();             
                                   
      $this->assertEquals($authValidator->getStatus(), "200", "Debugging information: ".var_export($authValidator, TRUE));                                       

      utilities\deleteDataset();
      
      unset($authValidator);
      unset($settings);
    }   
    
    public function  testTargetWebServiceNotRegistered() {
      
      $settings = new Config();  
      
      utilities\deleteDataset();
      
      $this->assertTrue(utilities\createDataset(), "Can't create the dataset, check the /dataset/create/ endpoint first...");
            
      $authValidator = new AuthValidatorQuery($settings->endpointUrl);     
      
      $authValidator->ip($_SERVER['REMOTE_ADDR']);
      
      $authValidator->datasets(array($settings->testDataset));
      
      $authValidator->webServiceUri($settings->endpointUri."crud/create/"."not-registered");
      
      $authValidator->send();             
                                   
      $this->assertEquals($authValidator->getStatus(), "500", "Debugging information: ".var_export($authValidator, TRUE));                                       
      $this->assertEquals($authValidator->getStatusMessage(), "Internal Error", "Debugging information: ".var_export($authValidator, TRUE));
      $this->assertEquals($authValidator->error->id, "WS-AUTH-VALIDATOR-301", "Debugging information: ".var_export($authValidator, TRUE));                                       
                                      

      utilities\deleteDataset();
      
      unset($authValidator);
      unset($settings);
    }    
          
    public function  testDatasetNotExisting() {
      
      $settings = new Config();  
      
      utilities\deleteDataset();
      
      $this->assertTrue(utilities\createDataset(), "Can't create the dataset, check the /dataset/create/ endpoint first...");
            
      $authValidator = new AuthValidatorQuery($settings->endpointUrl);     
      
      $authValidator->ip($_SERVER['REMOTE_ADDR']);
      
      $authValidator->datasets(array($settings->testDataset."not-existing"));
      
      $authValidator->webServiceUri($settings->endpointUri."crud/create/");
      
      $authValidator->send();              
                                   
      $this->assertEquals($authValidator->getStatus(), "403", "Debugging information: ".var_export($authValidator, TRUE));                                       
      $this->assertEquals($authValidator->getStatusMessage(), "Forbidden", "Debugging information: ".var_export($authValidator, TRUE));
      $this->assertEquals($authValidator->error->id, "WS-AUTH-VALIDATOR-303", "Debugging information: ".var_export($authValidator, TRUE));                                       
                                      

      utilities\deleteDataset();
      
      unset($authValidator);
      unset($settings);
    } 

    public function  testOneOfDatasetNotExisting() {
      
      $settings = new Config();  
      
      utilities\deleteDataset();
      
      $this->assertTrue(utilities\createDataset(), "Can't create the dataset, check the /dataset/create/ endpoint first...");
            
      $authValidator = new AuthValidatorQuery($settings->endpointUrl);     
      
      $authValidator->ip($_SERVER['REMOTE_ADDR']);
      
      $authValidator->datasets(array($settings->testDataset, $settings->testDataset . "not-existing/"));
      
      $authValidator->webServiceUri($settings->endpointUri."crud/create/");
      
      $authValidator->send();                
                                   
      $this->assertEquals($authValidator->getStatus(), "403", "Debugging information: ".var_export($authValidator, TRUE));                                       
      $this->assertEquals($authValidator->getStatusMessage(), "Forbidden", "Debugging information: ".var_export($authValidator, TRUE));
      $this->assertEquals($authValidator->error->id, "WS-AUTH-VALIDATOR-303", "Debugging information: ".var_export($authValidator, TRUE));                                       

      utilities\deleteDataset();
      
      unset($authValidator);
      unset($settings);
    } 
    
    public function  testNoCreatePermissions() {
      
      $settings = new Config();  
      
      utilities\deleteDataset();
      
      $this->assertTrue(utilities\createDatasetGlobalPermissionsNone(), "Can't create the dataset, check the /dataset/create/ endpoint first...");
      $this->assertTrue(utilities\createNoAccess_AccessRecord(), "Can't create the access record, check the /auth/registrar/access/ endpoint first...");
            
      $authValidator = new AuthValidatorQuery($settings->endpointUrl);     
      
      $authValidator->ip($settings->randomRequester);
      
      $authValidator->datasets(array($settings->testDataset));
      
      $authValidator->webServiceUri($settings->endpointUri."crud/create/");
      
      $authValidator->send();    
                                   
      $this->assertEquals($authValidator->getStatus(), "403", "Debugging information: ".var_export($authValidator, TRUE));                                       
      $this->assertEquals($authValidator->getStatusMessage(), "Forbidden", "Debugging information: ".var_export($authValidator, TRUE));
      $this->assertEquals($authValidator->error->id, "WS-AUTH-VALIDATOR-304", "Debugging information: ".var_export($authValidator, TRUE));                                       

      utilities\deleteDataset();
      
      unset($authValidator);
      unset($settings);
    }    
    
    public function  testNoDeletePermissions() {
      
      $settings = new Config();  
      
      utilities\deleteDataset();
      
      $this->assertTrue(utilities\createDatasetGlobalPermissionsNone(), "Can't create the dataset, check the /dataset/create/ endpoint first...");
      $this->assertTrue(utilities\createNoAccess_AccessRecord(), "Can't create the access record, check the /auth/registrar/access/ endpoint first...");
            
      $authValidator = new AuthValidatorQuery($settings->endpointUrl);     
      
      $authValidator->ip($settings->randomRequester);
      
      $authValidator->datasets(array($settings->testDataset));
      
      $authValidator->webServiceUri($settings->endpointUri."crud/delete/");
      
      $authValidator->send();              

      $this->assertEquals($authValidator->getStatus(), "403", "Debugging information: ".var_export($authValidator, TRUE));                                       
      $this->assertEquals($authValidator->getStatusMessage(), "Forbidden", "Debugging information: ".var_export($authValidator, TRUE));
      $this->assertEquals($authValidator->error->id, "WS-AUTH-VALIDATOR-307", "Debugging information: ".var_export($authValidator, TRUE));                                       
                                      

      utilities\deleteDataset();
      
      unset($authValidator);
      unset($settings);
    } 
    
    public function  testNoUpdatePermissions() {
      
      $settings = new Config();  
      
      utilities\deleteDataset();
      
      $this->assertTrue(utilities\createDatasetGlobalPermissionsNone(), "Can't create the dataset, check the /dataset/create/ endpoint first...");
      $this->assertTrue(utilities\createNoAccess_AccessRecord(), "Can't create the access record, check the /auth/registrar/access/ endpoint first...");
            
      $authValidator = new AuthValidatorQuery($settings->endpointUrl);     
      
      $authValidator->ip($settings->randomRequester);
      
      $authValidator->datasets(array($settings->testDataset));
      
      $authValidator->webServiceUri($settings->endpointUri."crud/update/");
      
      $authValidator->send();              
                                   
      $this->assertEquals($authValidator->getStatus(), "403", "Debugging information: ".var_export($authValidator, TRUE));                                       
      $this->assertEquals($authValidator->getStatusMessage(), "Forbidden", "Debugging information: ".var_export($authValidator, TRUE));
      $this->assertEquals($authValidator->error->id, "WS-AUTH-VALIDATOR-305", "Debugging information: ".var_export($authValidator, TRUE));                                       

      utilities\deleteDataset();
      
      unset($authValidator);
      unset($settings);
    }  
    
    public function  testNoReadPermissions() {
      
      $settings = new Config();  
      
      utilities\deleteDataset();
      
      $this->assertTrue(utilities\createDatasetGlobalPermissionsNone(), "Can't create the dataset, check the /dataset/create/ endpoint first...");
      $this->assertTrue(utilities\createNoAccess_AccessRecord(), "Can't create the access record, check the /auth/registrar/access/ endpoint first...");
            
      $authValidator = new AuthValidatorQuery($settings->endpointUrl);     
      
      $authValidator->ip($settings->randomRequester);
      
      $authValidator->datasets(array($settings->testDataset));
      
      $authValidator->webServiceUri($settings->endpointUri."crud/read/");
      
      $authValidator->send();              
                                   
      $this->assertEquals($authValidator->getStatus(), "403", "Debugging information: ".var_export($authValidator, TRUE));                                       
      $this->assertEquals($authValidator->getStatusMessage(), "Forbidden", "Debugging information: ".var_export($authValidator, TRUE));
      $this->assertEquals($authValidator->error->id, "WS-AUTH-VALIDATOR-306", "Debugging information: ".var_export($authValidator, TRUE));                                       

      utilities\deleteDataset();
      
      unset($authValidator);
      unset($settings);
    } 

    public function  testCreatePermissionsWithGlobalUser() {
      
      $settings = new Config();  
      
      utilities\deleteDataset();
      
      $this->assertTrue(utilities\createDataset(), "Can't create the dataset, check the /dataset/create/ endpoint first...");
      $this->assertTrue(utilities\createNoAccess_AccessRecord(), "Can't create the access record, check the /auth/registrar/access/ endpoint first...");
            
      $authValidator = new AuthValidatorQuery($settings->endpointUrl);     
      
      $authValidator->ip($_SERVER['REMOTE_ADDR']);
      
      $authValidator->datasets(array($settings->testDataset));
      
      $authValidator->webServiceUri($settings->endpointUri."crud/create/");
      
      $authValidator->send();                
                                   
      $this->assertEquals($authValidator->getStatus(), "200", "Debugging information: ".var_export($authValidator, TRUE));                                       

      utilities\deleteDataset();
      
      unset($authValidator);
      unset($settings);
    }    
    
    public function  testDeletePermissionsWithGlobalUser() {
      
      $settings = new Config();  
      
      utilities\deleteDataset();
      
      $this->assertTrue(utilities\createDataset(), "Can't create the dataset, check the /dataset/create/ endpoint first...");
      $this->assertTrue(utilities\createNoAccess_AccessRecord(), "Can't create the access record, check the /auth/registrar/access/ endpoint first...");
            
      $authValidator = new AuthValidatorQuery($settings->endpointUrl);     
      
      $authValidator->ip($settings->randomRequester);
      
      $authValidator->datasets(array($settings->testDataset));
      
      $authValidator->webServiceUri($settings->endpointUri."crud/delete/");
      
      $authValidator->send();              
                                   
      $this->assertEquals($authValidator->getStatus(), "200", "Debugging information: ".var_export($authValidator, TRUE));                                       

      utilities\deleteDataset();
      
      unset($authValidator);
      unset($settings);
    }    

    public function  testUpdatePermissionsWithGlobalUser() {
      
      $settings = new Config();  
      
      utilities\deleteDataset();
      
      $this->assertTrue(utilities\createDataset(), "Can't create the dataset, check the /dataset/create/ endpoint first...");
      $this->assertTrue(utilities\createNoAccess_AccessRecord(), "Can't create the access record, check the /auth/registrar/access/ endpoint first...");
            
      $authValidator = new AuthValidatorQuery($settings->endpointUrl);     
      
      $authValidator->ip($settings->randomRequester);
      
      $authValidator->datasets(array($settings->testDataset));
      
      $authValidator->webServiceUri($settings->endpointUri."crud/update/");
      
      $authValidator->send();              
                                   
      $this->assertEquals($authValidator->getStatus(), "200", "Debugging information: ".var_export($authValidator, TRUE));                                       

      utilities\deleteDataset();
      
      unset($authValidator);
      unset($settings);
    }    
    
    public function  testReadPermissionsWithGlobalUser() {
      
      $settings = new Config();  
      
      utilities\deleteDataset();
      
      $this->assertTrue(utilities\createDataset(), "Can't create the dataset, check the /dataset/create/ endpoint first...");
      $this->assertTrue(utilities\createNoAccess_AccessRecord(), "Can't create the access record, check the /auth/registrar/access/ endpoint first...");
            
      $authValidator = new AuthValidatorQuery($settings->endpointUrl);     
      
      $authValidator->ip($settings->randomRequester);
      
      $authValidator->datasets(array($settings->testDataset));
      
      $authValidator->webServiceUri($settings->endpointUri."crud/read/");
      
      $authValidator->send();              
                                   
      $this->assertEquals($authValidator->getStatus(), "200", "Debugging information: ".var_export($authValidator, TRUE));                                       

      utilities\deleteDataset();
      
      unset($wsq);
      unset($settings);
    }    
    
    public function  testNoCreatePermissionsTwoDatasets() {
      
      $settings = new Config();  
      
      utilities\deleteTwoDatasets();
      
      $this->assertTrue(utilities\createTwoDatasetsGlobalPermissionsNone(), "Can't create the dataset, check the /dataset/create/ endpoint first...");
      $this->assertTrue(utilities\createNoAccess_AccessRecord(), "Can't create the access record, check the /auth/registrar/access/ endpoint first...");
            
      $authValidator = new AuthValidatorQuery($settings->endpointUrl);     
      
      $authValidator->ip($settings->randomRequester);
      
      $authValidator->datasets(array($settings->testDataset, $settings->testDataset."2/"));
      
      $authValidator->webServiceUri($settings->endpointUri."crud/create/");
      
      $authValidator->send();              
            
      $this->assertEquals($authValidator->getStatus(), "403", "Debugging information: ".var_export($authValidator, TRUE));                                       
      $this->assertEquals($authValidator->getStatusMessage(), "Forbidden", "Debugging information: ".var_export($authValidator, TRUE));
      $this->assertEquals($authValidator->error->id, "WS-AUTH-VALIDATOR-304", "Debugging information: ".var_export($authValidator, TRUE));                                       
                                      

      utilities\deleteTwoDatasets();
      
      unset($authValidator);
      unset($settings);
    }    
    
    public function  testNoDeletePermissionsTwoDatasets() {
      
      $settings = new Config();  
      
      utilities\deleteTwoDatasets();
      
      $this->assertTrue(utilities\createTwoDatasetsGlobalPermissionsNone(), "Can't create the dataset, check the /dataset/create/ endpoint first...");
      $this->assertTrue(utilities\createNoAccess_AccessRecord(), "Can't create the access record, check the /auth/registrar/access/ endpoint first...");
            
      $authValidator = new AuthValidatorQuery($settings->endpointUrl);     
      
      $authValidator->ip($settings->randomRequester);
      
      $authValidator->datasets(array($settings->testDataset, $settings->testDataset."2/"));
      
      $authValidator->webServiceUri($settings->endpointUri."crud/delete/");
      
      $authValidator->send();                          
                                   
      $this->assertEquals($authValidator->getStatus(), "403", "Debugging information: ".var_export($authValidator, TRUE));                                       
      $this->assertEquals($authValidator->getStatusMessage(), "Forbidden", "Debugging information: ".var_export($authValidator, TRUE));
      $this->assertEquals($authValidator->error->id, "WS-AUTH-VALIDATOR-307", "Debugging information: ".var_export($authValidator, TRUE));                                       
                                      

      utilities\deleteTwoDatasets();
      
      unset($authValidator);
      unset($settings);
    }    
    
    public function  testNoUpdatePermissionsTwoDatasets() {
      
      $settings = new Config();  
      
      utilities\deleteTwoDatasets();
      
      $this->assertTrue(utilities\createTwoDatasetsGlobalPermissionsNone(), "Can't create the dataset, check the /dataset/create/ endpoint first...");
      $this->assertTrue(utilities\createNoAccess_AccessRecord(), "Can't create the access record, check the /auth/registrar/access/ endpoint first...");
            
      $authValidator = new AuthValidatorQuery($settings->endpointUrl);     
      
      $authValidator->ip($settings->randomRequester);
      
      $authValidator->datasets(array($settings->testDataset, $settings->testDataset."2/"));
      
      $authValidator->webServiceUri($settings->endpointUri."crud/update/");
      
      $authValidator->send();                          
                                   
      $this->assertEquals($authValidator->getStatus(), "403", "Debugging information: ".var_export($authValidator, TRUE));                                       
      $this->assertEquals($authValidator->getStatusMessage(), "Forbidden", "Debugging information: ".var_export($authValidator, TRUE));
      $this->assertEquals($authValidator->error->id, "WS-AUTH-VALIDATOR-305", "Debugging information: ".var_export($authValidator, TRUE));                                       
                                      

      utilities\deleteTwoDatasets();
      
      unset($authValidator);
      unset($settings);
    }    
    
    public function  testNoReadPermissionsTwoDatasets() {
      
      $settings = new Config();  
      
      utilities\deleteTwoDatasets();
      
      $this->assertTrue(utilities\createTwoDatasetsGlobalPermissionsNone(), "Can't create the dataset, check the /dataset/create/ endpoint first...");
      $this->assertTrue(utilities\createNoAccess_AccessRecord(), "Can't create the access record, check the /auth/registrar/access/ endpoint first...");
            
      $authValidator = new AuthValidatorQuery($settings->endpointUrl);     
      
      $authValidator->ip($settings->randomRequester);
      
      $authValidator->datasets(array($settings->testDataset, $settings->testDataset."2/"));
      
      $authValidator->webServiceUri($settings->endpointUri."crud/read/");
      
      $authValidator->send();              
                                   
      $this->assertEquals($authValidator->getStatus(), "403", "Debugging information: ".var_export($authValidator, TRUE));                                       
      $this->assertEquals($authValidator->getStatusMessage(), "Forbidden", "Debugging information: ".var_export($authValidator, TRUE));
      $this->assertEquals($authValidator->error->id, "WS-AUTH-VALIDATOR-306", "Debugging information: ".var_export($authValidator, TRUE));                                       
                                      

      utilities\deleteTwoDatasets();
      
      unset($authValidator);
      unset($settings);
    }  
    
    public function  testCreatePermissionsWithGlobalUserTwoDatasets() {
      
      $settings = new Config();  
      
      utilities\deleteTwoDatasets();
      
      $this->assertTrue(utilities\createTwoDatasets(), "Can't create the dataset, check the /dataset/create/ endpoint first...");
      $this->assertTrue(utilities\createNoAccess_AccessRecord(), "Can't create the access record, check the /auth/registrar/access/ endpoint first...");
            
      $authValidator = new AuthValidatorQuery($settings->endpointUrl);     
      
      $authValidator->ip($settings->randomRequester);
      
      $authValidator->datasets(array($settings->testDataset, $settings->testDataset."2/"));
      
      $authValidator->webServiceUri($settings->endpointUri."crud/create/");
      
      $authValidator->send();              
                                   
      $this->assertEquals($authValidator->getStatus(), "200", "Debugging information: ".var_export($authValidator, TRUE));                                       

      utilities\deleteTwoDatasets();
      
      unset($authValidator);
      unset($settings);
    }        
    
    public function  testUpdatePermissionsWithGlobalUserTwoDatasets() {
      
      $settings = new Config();  
      
      utilities\deleteTwoDatasets();
      
      $this->assertTrue(utilities\createTwoDatasets(), "Can't create the dataset, check the /dataset/create/ endpoint first...");
      $this->assertTrue(utilities\createNoAccess_AccessRecord(), "Can't create the access record, check the /auth/registrar/access/ endpoint first...");
            
      $authValidator = new AuthValidatorQuery($settings->endpointUrl);     
      
      $authValidator->ip($settings->randomRequester);
      
      $authValidator->datasets(array($settings->testDataset, $settings->testDataset."2/"));
      
      $authValidator->webServiceUri($settings->endpointUri."crud/create/");
      
      $authValidator->send();              
                                   
      $this->assertEquals($authValidator->getStatus(), "200", "Debugging information: ".var_export($authValidator, TRUE));                                       

      utilities\deleteTwoDatasets();
      
      unset($authValidator);
      unset($settings);
    }        
    
    
    public function  testDeletePermissionsWithGlobalUserTwoDatasets() {
      
      $settings = new Config();  
      
      utilities\deleteTwoDatasets();
      
      $this->assertTrue(utilities\createTwoDatasets(), "Can't create the dataset, check the /dataset/create/ endpoint first...");
      $this->assertTrue(utilities\createNoAccess_AccessRecord(), "Can't create the access record, check the /auth/registrar/access/ endpoint first...");
            
      $authValidator = new AuthValidatorQuery($settings->endpointUrl);     
      
      $authValidator->ip($settings->randomRequester);
      
      $authValidator->datasets(array($settings->testDataset, $settings->testDataset."2/"));
      
      $authValidator->webServiceUri($settings->endpointUri."crud/create/");
      
      $authValidator->send();              
                                   
      $this->assertEquals($authValidator->getStatus(), "200", "Debugging information: ".var_export($authValidator, TRUE));                                       

      utilities\deleteTwoDatasets();
      
      unset($authValidator);
      unset($settings);
    }        
    
    public function  testReadPermissionsWithGlobalUserTwoDatasets() {
      
      $settings = new Config();  
      
      utilities\deleteTwoDatasets();
      
      $this->assertTrue(utilities\createTwoDatasets(), "Can't create the dataset, check the /dataset/create/ endpoint first...");
      $this->assertTrue(utilities\createNoAccess_AccessRecord(), "Can't create the access record, check the /auth/registrar/access/ endpoint first...");
            
      $authValidator = new AuthValidatorQuery($settings->endpointUrl);     
      
      $authValidator->ip($settings->randomRequester);
      
      $authValidator->datasets(array($settings->testDataset, $settings->testDataset."2/"));
      
      $authValidator->webServiceUri($settings->endpointUri."crud/create/");
      
      $authValidator->send();              
                                   
      $this->assertEquals($authValidator->getStatus(), "200", "Debugging information: ".var_export($authValidator, TRUE));                                       

      utilities\deleteTwoDatasets();
      
      unset($authValidator);
      unset($settings);
    }        
  }

  
?>