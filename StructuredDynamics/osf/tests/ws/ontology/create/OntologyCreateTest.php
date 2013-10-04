<?php

  namespace StructuredDynamics\osf\tests\ws\ontology\create;
  
  use StructuredDynamics\osf\framework\WebServiceQuerier;
  use StructuredDynamics\osf\php\api\ws\ontology\create\OntologyCreateQuery;
  use StructuredDynamics\osf\php\api\ws\ontology\read\OntologyReadQuery;
  use StructuredDynamics\osf\tests\Config;
  use \StructuredDynamics\osf\php\api\framework\CRUDPermission;
  use \StructuredDynamics\osf\php\api\ws\auth\registrar\access\AuthRegistrarAccessQuery;
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
  
  class OntologyCreateTest extends \PHPUnit_Framework_TestCase {
    
    static public function tearDownAfterClass()    
    {
      utilities\deleteOntology();      
    }      
    
    static private $outputs = array();
    
    public function testWrongEndpointUrl() {
      
      $settings = new Config();          
      
      $wsq = new WebServiceQuerier($settings->endpointUrl . "ontology/create/" . "wrong", 
                                   "post", 
                                   "text/xml",
                                   "uri=" . urlencode($settings->testOntologyUri) .
                                   "&advancedIndexation=" . urlencode("True") .
                                   "&reasoner=" . urlencode("True") .
                                   "&interface=". urlencode($settings->ontologyCreateInterface) .
                                   "&version=". urlencode($settings->ontologyCreateInterfaceVersion),
                                   $settings->applicationID,
                                   $settings->apiKey,
                                   $settings->userID);
                         
      $this->assertEquals($wsq->getStatus(), "404", "Debugging information: ".var_export($wsq, TRUE));                                       
      $this->assertEquals($wsq->getStatusMessage(), "Not Found", "Debugging information: ".var_export($wsq, TRUE));
      
      unset($wsq);
      unset($settings);
    }
    
    public function testWrongEndpointMethodGet() {
      
      $settings = new Config();  
      
      $wsq = new WebServiceQuerier($settings->endpointUrl . "ontology/create/", 
                                   "get", 
                                   "text/xml",
                                   "uri=" . urlencode($settings->testOntologyUri) .
                                   "&advancedIndexation=" . urlencode("True") .
                                   "&reasoner=" . urlencode("True") .
                                   "&interface=". urlencode($settings->ontologyCreateInterface) .
                                   "&version=". urlencode($settings->ontologyCreateInterfaceVersion),
                                   $settings->applicationID,
                                   $settings->apiKey,
                                   $settings->userID);

                                   
      $this->assertEquals($wsq->getStatus(), "405", "Debugging information: ".var_export($wsq, TRUE));                                       
      $this->assertEquals($wsq->getStatusMessage(), "Method Not Allowed", "Debugging information: ".var_export($wsq, TRUE));          
      
      unset($wsq);
      unset($settings);
    }    
    
    public function testValidInterfaceVersion() {
      
      $settings = new Config();  

      utilities\deleteOntology();
       
      $ontologyCreate = new OntologyCreateQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
      
      $ontologyCreate->uri($settings->testOntologyUri)
                     ->enableAdvancedIndexation()
                     ->enableReasoner()
                     ->sourceInterface($settings->ontologyCreateInterface)
                     ->sourceInterfaceVersion($settings->ontologyCreateInterfaceVersion)
                     ->send();
                           
      $this->assertEquals($ontologyCreate->getStatus(), "200", "Debugging information: ".var_export($ontologyCreate, TRUE));                                       

      utilities\deleteOntology();

      unset($ontologyCreate);
      unset($settings);    
    }
    
    
    public function testInvalidInterfaceVersion() {
      
      $settings = new Config();  

      utilities\deleteOntology();
                 
      $ontologyCreate = new OntologyCreateQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
      
      $ontologyCreate->uri($settings->testOntologyUri)
                     ->enableAdvancedIndexation()
                     ->enableReasoner()
                     ->sourceInterface($settings->ontologyCreateInterface)
                     ->sourceInterfaceVersion("667.4")
                     ->send();
                           
      $this->assertEquals($ontologyCreate->getStatus(), "400", "Debugging information: ".var_export($ontologyCreate, TRUE));                                       
      $this->assertEquals($ontologyCreate->getStatusMessage(), "Bad Request", "Debugging information: ".var_export($ontologyCreate, TRUE));
      $this->assertEquals($ontologyCreate->error->id, "WS-ONTOLOGY-CREATE-304", "Debugging information: ".var_export($ontologyCreate, TRUE));                                       

      utilities\deleteOntology();

      unset($ontologyCreate);
      unset($settings);    
    }    
    
    //
    // Test existing interface
    //
    
    public function testInterfaceExists() {
      
      $settings = new Config();  

      utilities\deleteOntology();
                 
      $ontologyCreate = new OntologyCreateQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
      
      $ontologyCreate->uri($settings->testOntologyUri)
                     ->enableAdvancedIndexation()
                     ->enableReasoner()
                     ->sourceInterface($settings->ontologyCreateInterface)
                     ->sourceInterfaceVersion($settings->ontologyCreateInterfaceVersion)
                     ->send();
                           
      $this->assertEquals($ontologyCreate->getStatus(), "200", "Debugging information: ".var_export($ontologyCreate, TRUE));                                       

      utilities\deleteOntology();

      unset($ontologyCreate);
      unset($settings);
    }  
    
    //
    // Test unexisting interface
    //
    
    public function testInterfaceNotExisting() {
      
      $settings = new Config();  

      utilities\deleteOntology();
                 
      $ontologyCreate = new OntologyCreateQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
      
      $ontologyCreate->uri($settings->testOntologyUri)
                     ->enableAdvancedIndexation()
                     ->enableReasoner()
                     ->sourceInterface("default-not-existing")
                     ->sourceInterfaceVersion($settings->ontologyCreateInterfaceVersion)
                     ->send();
                           
      $this->assertEquals($ontologyCreate->getStatus(), "400", "Debugging information: ".var_export($ontologyCreate, TRUE));                                       
      $this->assertEquals($ontologyCreate->getStatusMessage(), "Bad Request", "Debugging information: ".var_export($ontologyCreate, TRUE));
      $this->assertEquals($ontologyCreate->error->id, "WS-ONTOLOGY-CREATE-303", "Debugging information: ".var_export($ontologyCreate, TRUE));                                       

      utilities\deleteOntology();

      unset($ontologyCreate);
      unset($settings);
    }     
    
    public function  testCreateOntology() {
      
      $settings = new Config();  
        
      utilities\deleteOntology();
                 
      $ontologyCreate = new OntologyCreateQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
      
      $ontologyCreate->uri($settings->testOntologyUri)
                     ->enableAdvancedIndexation()
                     ->enableReasoner()
                     ->sourceInterface($settings->ontologyCreateInterface)
                     ->sourceInterfaceVersion($settings->ontologyCreateInterfaceVersion)
                     ->send();
                                   
      $this->assertEquals($ontologyCreate->getStatus(), "200", "Debugging information: ".var_export($ontologyCreate, TRUE));                                       
      
      utilities\deleteOntology();
      
      unset($ontologyCreate);
      unset($settings);
    }  
    
    public function  testCreateOntologyValidateCreatedContent() {
      
      $settings = new Config();  
      
      // Make sure the ontology doesn't exists
      utilities\deleteOntology();
      
      $ontologyCreate = new OntologyCreateQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
      
      $ontologyCreate->uri($settings->testOntologyUri)
                     ->enableAdvancedIndexation()
                     ->enableReasoner()
                     ->sourceInterface($settings->ontologyCreateInterface)
                     ->sourceInterfaceVersion($settings->ontologyCreateInterfaceVersion)
                     ->send();          

      $this->assertEquals($ontologyCreate->getStatus(), "200", "Debugging information: ".var_export($ontologyCreate, TRUE));                                       
      
      unset($ontologyCreate);   
      
      // Create the permissions for the "administrators" group    
      $crudPermissions = new CRUDPermission(TRUE, TRUE, TRUE, TRUE);      
      
      $authRegistrarAccess = new AuthRegistrarAccessQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
      
      $authRegistrarAccess->create('', $settings->testOntologyUri, $crudPermissions, $settings->datasetWebservices)
                          ->mime('text/xml')
                          ->sourceInterface($settings->authRegistrarAccessInterface)
                          ->sourceInterfaceVersion($settings->authRegistrarAccessInterfaceVersion)
                          ->send();
                           
      if(!$authRegistrarAccess->isSuccessful())
      {
        return(FALSE);
      }
      
      unset($authRegistrarAccess);   
      
      $ontologyRead = new OntologyReadQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
      
      $ontologyRead->mime("application/rdf+xml")
                   ->ontology($settings->testOntologyUri)
                   ->getSerialized()
                   ->enableReasoner()
                   ->sourceInterface($settings->ontologyCreateInterface)
                   ->sourceInterfaceVersion($settings->ontologyCreateInterfaceVersion)
                   ->send();     

      $this->assertEquals($ontologyRead->getStatus(), "200", "Debugging information: ".var_export($ontologyRead, TRUE));                                       
      
      utilities\validateParameterApplicationRdfXml($this, $ontologyRead);
      
      utilities\deleteOntology();

      unset($ontologyRead);
      unset($settings);
    }                       
    
    public function  testCreateOntologyNoOntologyUriSpecified() {
      
      $settings = new Config();  
      
      utilities\deleteOntology();
      
      $ontologyCreate = new OntologyCreateQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
      
      $ontologyCreate->uri("")
                     ->enableAdvancedIndexation()
                     ->enableReasoner()
                     ->sourceInterface($settings->ontologyCreateInterface)
                     ->sourceInterfaceVersion($settings->ontologyCreateInterfaceVersion)
                     ->send();        
                                   
      $this->assertEquals($ontologyCreate->getStatus(), "400", "Debugging information: ".var_export($ontologyCreate, TRUE));                                       
      $this->assertEquals($ontologyCreate->getStatusMessage(), "Bad Request", "Debugging information: ".var_export($ontologyCreate, TRUE));
      $this->assertEquals($ontologyCreate->error->id, "WS-ONTOLOGY-CREATE-200", "Debugging information: ".var_export($ontologyCreate, TRUE));                                       
      
      unset($ontologyCreate);
      unset($settings);
    }
    
    public function  testCreateOntologyOntologyAlreadyExisting() {
      
      $settings = new Config();  
       
      utilities\deleteOntology();    
       
      $this->assertTrue(utilities\createOntology(), "Can't create the ontology, check the /ontology/create/ endpoint first...");
            
      $ontologyCreate = new OntologyCreateQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
      
      $ontologyCreate->uri($settings->testOntologyUri)
                     ->enableAdvancedIndexation()
                     ->enableReasoner()
                     ->sourceInterface($settings->ontologyCreateInterface)
                     ->sourceInterfaceVersion($settings->ontologyCreateInterfaceVersion)
                     ->send();              

                                   
      $this->assertEquals($ontologyCreate->getStatus(), "400", "Debugging information: ".var_export($ontologyCreate, TRUE));                                       
      $this->assertEquals($ontologyCreate->getStatusMessage(), "Bad Request", "Debugging information: ".var_export($ontologyCreate, TRUE));
      $this->assertEquals($ontologyCreate->error->id, "WS-ONTOLOGY-CREATE-302", "Debugging information: ".var_export($ontologyCreate, TRUE));                                       
      
      utilities\deleteOntology();   
      
      unset($ontologyCreate);
      unset($settings);
    }    
    
    public function  testCreateOntologyInvalidOntology() {
      
      $settings = new Config();  
      
      $ontologyCreate = new OntologyCreateQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
      
      $ontologyCreate->uri($settings->testInvalidOntologyUri)
                     ->enableAdvancedIndexation()
                     ->enableReasoner()
                     ->sourceInterface($settings->ontologyCreateInterface)
                     ->sourceInterfaceVersion($settings->ontologyCreateInterfaceVersion)
                     ->send();        
                                   
      $this->assertEquals($ontologyCreate->getStatus(), "400", "Debugging information: ".var_export($ontologyCreate, TRUE));                                       
      $this->assertEquals($ontologyCreate->getStatusMessage(), "Bad Request", "Debugging information: ".var_export($ontologyCreate, TRUE));
      $this->assertEquals($ontologyCreate->error->id, "WS-ONTOLOGY-CREATE-300", "Debugging information: ".var_export($ontologyCreate, TRUE));                                       
      
      unset($ontologyCreate);
      unset($settings);
    }
   
  }

  
?>