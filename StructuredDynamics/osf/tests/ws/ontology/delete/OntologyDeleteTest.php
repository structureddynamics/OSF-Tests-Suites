<?php

  namespace StructuredDynamics\osf\tests\ws\ontology\delete;
  
  use StructuredDynamics\osf\framework\WebServiceQuerier;
  use StructuredDynamics\osf\tests\Config;
  use StructuredDynamics\osf\php\api\ws\ontology\delete\OntologyDeleteQuery;
  use StructuredDynamics\osf\php\api\ws\ontology\read\OntologyReadQuery;
  use StructuredDynamics\osf\php\api\ws\ontology\read\GetPropertyFunction;
  use StructuredDynamics\osf\php\api\ws\ontology\read\GetClassFunction;
  use StructuredDynamics\osf\php\api\ws\ontology\read\GetNamedIndividualFunction;
  use StructuredDynamics\osf\php\api\ws\ontology\delete\DeleteClassFunction;
  use StructuredDynamics\osf\php\api\ws\ontology\delete\DeleteNamedIndividualFunction;
  use StructuredDynamics\osf\php\api\ws\ontology\delete\DeletePropertyFunction;
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
  
  class OntologyDeleteTest extends \PHPUnit_Framework_TestCase {
    
    static private $outputs = array();
    
    static public function tearDownAfterClass()    
    {
      utilities\deleteOntology();      
    }      
    
    public function testWrongEndpointUrl() {
      
      $settings = new Config();          
      
      $wsq = new WebServiceQuerier($settings->endpointUrl . "ontology/delete/" . "wrong", 
                                   "post", 
                                   "text/xml",
                                   "ontology=" . urlencode($settings->testOntologyUri) .
                                   "&function=" . urlencode("deleteOntology") .
                                   "&parameters=" . urlencode("") .
                                   "&interface=". urlencode($settings->ontologyDeleteInterface) .
                                   "&version=". urlencode($settings->ontologyDeleteInterfaceVersion),
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
      
      $wsq = new WebServiceQuerier($settings->endpointUrl . "ontology/delete/", 
                                   "get", 
                                   "text/xml",
                                   "ontology=" . urlencode($settings->testOntologyUri) .
                                   "&function=" . urlencode("deleteOntology") .
                                   "&parameters=" . urlencode("") .
                                   "&interface=". urlencode($settings->ontologyDeleteInterface) .
                                   "&version=". urlencode($settings->ontologyDeleteInterfaceVersion),
                                   $settings->applicationID,
                                   $settings->apiKey,
                                   $settings->userID);
                                   
      $this->assertEquals($wsq->getStatus(), "405", "Debugging information: ".var_export($wsq, TRUE));                                       
      $this->assertEquals($wsq->getStatusMessage(), "Method Not Allowed", "Debugging information: ".var_export($wsq, TRUE));          
      
      unset($wsq);
      unset($settings);
    }    
    
    public function  testDeleteOntology_unknownFunctionCall() {
      
      $settings = new Config();  
      
      // Delete Ontology
      $wsq = new WebServiceQuerier($settings->endpointUrl . "ontology/delete/", 
                                   "post", 
                                   "text/xml",
                                   "ontology=" . urlencode($settings->testOntologyUri) .
                                   "&function=" . urlencode("deleteOntology" . "unknown") .
                                   "&parameters=" . urlencode("") .
                                   "&interface=". urlencode($settings->ontologyDeleteInterface) .
                                   "&version=". urlencode($settings->ontologyDeleteInterfaceVersion),
                                   $settings->applicationID,
                                   $settings->apiKey,
                                   $settings->userID);
                                   
      $this->assertEquals($wsq->getStatus(), "400", "Debugging information: ".var_export($wsq, TRUE));                                       
      $this->assertEquals($wsq->getStatusMessage(), "Bad Request", "Debugging information: ".var_export($wsq, TRUE));
      $this->assertEquals($wsq->error->id, "WS-ONTOLOGY-DELETE-200", "Debugging information: ".var_export($wsq, TRUE));    
                                    
      unset($wsq);
      unset($settings);
    }   
    
    public function testValidInterfaceVersion() {
      
      $settings = new Config();  

      utilities\deleteOntology();
      
      $this->assertTrue(utilities\createOntology(), "Can't create the ontology, check the /ontology/create/ endpoint first...");
      
      $ontologyDelete = new OntologyDeleteQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
      
      $ontologyDelete->ontology($settings->testOntologyUri)
                     ->deleteOntology()
                     ->sourceInterface("default")
                     ->sourceInterface($settings->ontologyDeleteInterface)
                     ->sourceInterfaceVersion($settings->ontologyDeleteInterfaceVersion)
                     ->send();
                           
      $this->assertEquals($ontologyDelete->getStatus(), "200", "Debugging information: ".var_export($ontologyDelete, TRUE));                                       

      utilities\deleteDataset();

      unset($ontologyDelete);
      unset($settings);   
    }
    
    
    public function testInvalidInterfaceVersion() {
      
      $settings = new Config();  

      utilities\deleteOntology();
      
      $this->assertTrue(utilities\createOntology(), "Can't create the ontology, check the /ontology/create/ endpoint first...");
      
      $ontologyDelete = new OntologyDeleteQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
      
      $ontologyDelete->ontology($settings->testOntologyUri)
                     ->deleteOntology()
                     ->sourceInterface($settings->ontologyDeleteInterface)
                     ->sourceInterfaceVersion("667.4")
                     ->send();
                           
      $this->assertEquals($ontologyDelete->getStatus(), "400", "Debugging information: ".var_export($ontologyDelete, TRUE));                                       
      $this->assertEquals($ontologyDelete->getStatusMessage(), "Bad Request", "Debugging information: ".var_export($ontologyDelete, TRUE));
      $this->assertEquals($ontologyDelete->error->id, "WS-ONTOLOGY-DELETE-302", "Debugging information: ".var_export($ontologyDelete, TRUE));                                       

      utilities\deleteDataset();

      unset($ontologyDelete);
      unset($settings);                              
    }    
    
    //
    // Test existing interface
    //
    
    public function testInterfaceExists() {
      
      $settings = new Config();  

      utilities\deleteOntology();
      
      $this->assertTrue(utilities\createOntology(), "Can't create the ontology, check the /ontology/create/ endpoint first...");
      
      $ontologyDelete = new OntologyDeleteQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
      
      $ontologyDelete->ontology($settings->testOntologyUri)
                     ->deleteOntology()
                     ->sourceInterface($settings->ontologyDeleteInterface)
                     ->sourceInterfaceVersion($settings->ontologyDeleteInterfaceVersion)
                     ->send();
                           
      $this->assertEquals($ontologyDelete->getStatus(), "200", "Debugging information: ".var_export($ontologyDelete, TRUE));                                       

      utilities\deleteDataset();

      unset($ontologyDelete);
      unset($settings);
    }  
    
    //
    // Test unexisting interface
    //
    
    public function testInterfaceNotExisting() {
      
      $settings = new Config();  

      utilities\deleteOntology();
      
      $this->assertTrue(utilities\createOntology(), "Can't create the ontology, check the /ontology/create/ endpoint first...");
      
      $ontologyDelete = new OntologyDeleteQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
      
      $ontologyDelete->ontology($settings->testOntologyUri)
                     ->deleteOntology()
                     ->sourceInterface("default-not-existing")
                     ->sourceInterfaceVersion($settings->ontologyDeleteInterfaceVersion)
                     ->send();
                           
      $this->assertEquals($ontologyDelete->getStatus(), "400", "Debugging information: ".var_export($ontologyDelete, TRUE));                                       
      $this->assertEquals($ontologyDelete->getStatusMessage(), "Bad Request", "Debugging information: ".var_export($ontologyDelete, TRUE));
      $this->assertEquals($ontologyDelete->error->id, "WS-ONTOLOGY-DELETE-301", "Debugging information: ".var_export($ontologyDelete, TRUE));                                       

      utilities\deleteDataset();

      unset($ontologyDelete);
      unset($settings);
    }     
    
    public function  testDeleteOntology_NoOntologyUriSpecified() {
      
      $settings = new Config();  
      
      $ontologyDelete = new OntologyDeleteQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
      
      $ontologyDelete->ontology("")
                     ->deleteOntology()
                     ->sourceInterface($settings->ontologyDeleteInterface)
                     ->sourceInterfaceVersion($settings->ontologyDeleteInterfaceVersion)
                     ->send();
                                         
      $this->assertEquals($ontologyDelete->getStatus(), "400", "Debugging information: ".var_export($ontologyDelete, TRUE));                                       
      $this->assertEquals($ontologyDelete->getStatusMessage(), "Bad Request", "Debugging information: ".var_export($ontologyDelete, TRUE));
      $this->assertEquals($ontologyDelete->error->id, "WS-ONTOLOGY-DELETE-201", "Debugging information: ".var_export($ontologyDelete, TRUE));    
                                    
      unset($ontologyDelete);
      unset($settings);
    }       

    public function  testDeleteOntology_NoPropertyUriSpecified() {
      
      $settings = new Config();  
      
      $ontologyDelete = new OntologyDeleteQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
      
      $ontologyDelete->ontology($settings->testOntologyUri);
      
      $deletePropertyFunction = new DeletePropertyFunction();
      
      $deletePropertyFunction->uri("");
      
      $ontologyDelete->deleteProperty($deletePropertyFunction)
                     ->sourceInterface($settings->ontologyDeleteInterface)
                     ->sourceInterfaceVersion($settings->ontologyDeleteInterfaceVersion)
                     ->send();
                                   
      $this->assertEquals($ontologyDelete->getStatus(), "400", "Debugging information: ".var_export($ontologyDelete, TRUE));                                       
      $this->assertEquals($ontologyDelete->getStatusMessage(), "Bad Request", "Debugging information: ".var_export($ontologyDelete, TRUE));
      $this->assertEquals($ontologyDelete->error->id, "WS-ONTOLOGY-DELETE-202", "Debugging information: ".var_export($ontologyDelete, TRUE));    
                                    
      unset($ontologyDelete);
      unset($settings);
    } 

    public function  testDeleteOntology_NoNamedIndividualUriSpecified() {
      
      $settings = new Config();  

      $ontologyDelete = new OntologyDeleteQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
      
      $ontologyDelete->ontology($settings->testOntologyUri);
      
      $deleteNamedIndividualFunction = new DeleteNamedIndividualFunction();
      
      $deleteNamedIndividualFunction->uri("");
      
      $ontologyDelete->deleteNamedIndividual($deleteNamedIndividualFunction)
                     ->sourceInterface($settings->ontologyDeleteInterface)
                     ->sourceInterfaceVersion($settings->ontologyDeleteInterfaceVersion)
                     ->send();      
                                   
      $this->assertEquals($ontologyDelete->getStatus(), "400", "Debugging information: ".var_export($ontologyDelete, TRUE));                                       
      $this->assertEquals($ontologyDelete->getStatusMessage(), "Bad Request", "Debugging information: ".var_export($ontologyDelete, TRUE));
      $this->assertEquals($ontologyDelete->error->id, "WS-ONTOLOGY-DELETE-203", "Debugging information: ".var_export($ontologyDelete, TRUE));    
                                    
      unset($ontologyDelete);
      unset($settings);
    }         

    public function  testDeleteOntology_NoClassUriSpecified() {
      
      $settings = new Config();  
      
      $ontologyDelete = new OntologyDeleteQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
      
      $ontologyDelete->ontology($settings->testOntologyUri);
      
      $deleteClassFunction = new DeleteClassFunction();
      
      $deleteClassFunction->uri("");
      
      $ontologyDelete->deleteClass($deleteClassFunction)
                     ->sourceInterface($settings->ontologyDeleteInterface)
                     ->sourceInterfaceVersion($settings->ontologyDeleteInterfaceVersion)
                     ->send();      
                                   
      $this->assertEquals($ontologyDelete->getStatus(), "400", "Debugging information: ".var_export($ontologyDelete, TRUE));                                       
      $this->assertEquals($ontologyDelete->getStatusMessage(), "Bad Request", "Debugging information: ".var_export($ontologyDelete, TRUE));
      $this->assertEquals($ontologyDelete->error->id, "WS-ONTOLOGY-DELETE-204", "Debugging information: ".var_export($ontologyDelete, TRUE));    
                                    
      unset($ontologyDelete);
      unset($settings);
    } 
        
    public function  testDeleteOntology_function_deleteOntology() {
      
      $settings = new Config();  
      
      utilities\deleteOntology();
      
      $this->assertTrue(utilities\createOntology(), "Can't create the ontology, check the /ontology/create/ endpoint first...");
      
      $ontologyDelete = new OntologyDeleteQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
      
      $ontologyDelete->ontology($settings->testOntologyUri)
                     ->deleteOntology()
                     ->sourceInterface($settings->ontologyDeleteInterface)
                     ->sourceInterfaceVersion($settings->ontologyDeleteInterfaceVersion)
                     ->send();
                                   
      $this->assertEquals($ontologyDelete->getStatus(), "200", "Debugging information: ".var_export($ontologyDelete, TRUE));    
                                    
      unset($wsq);
      
      $ontologyRead = new OntologyReadQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
      
      $ontologyRead->mime("application/rdf+xml")
                   ->ontology($settings->testOntologyUri)
                   ->getSerialized()
                   ->enableReasoner()
                   ->sourceInterface($settings->ontologyReadInterface)
                   ->sourceInterfaceVersion($settings->ontologyReadInterfaceVersion)
                   ->send();             

      $this->assertTrue($ontologyRead->getStatus() == ('400' || '403'), "Debugging information: ".var_export($ontologyRead, TRUE));
      $this->assertTrue($ontologyRead->getStatusMessage() == ('Bad Request' || 'Forbidden'), "Debugging information: ".var_export($ontologyRead, TRUE));
      $this->assertTrue($ontologyRead->error->id == ('WS-ONTOLOGY-READ-300' || 'WS-AUTH-VALIDATOR-303'), "Debugging information: ".var_export($ontologyRead, TRUE));    

      unset($ontologyRead);      
      unset($settings);
    } 
    
    public function  testDeleteOntology_function_deleteProperty_Datatype() {
      
      $settings = new Config();  
      
      utilities\deleteOntology();
      
      $this->assertTrue(utilities\createOntology(), "Can't create the ontology, check the /ontology/create/ endpoint first...");
      

      
      // Delete Ontology
      $ontologyDelete = new OntologyDeleteQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
      
      $ontologyDelete->ontology($settings->testOntologyUri);
      
      $deletePropertyFunction = new DeletePropertyFunction();
      
      $deletePropertyFunction->uri($settings->targetDatatypePropertyUri);

      $ontologyDelete->deleteProperty($deletePropertyFunction)
                     ->sourceInterface($settings->ontologyDeleteInterface)
                     ->sourceInterfaceVersion($settings->ontologyDeleteInterfaceVersion)
                     ->send();      
      
      $this->assertEquals($ontologyDelete->getStatus(), "200", "Debugging information: ".var_export($ontologyDelete, TRUE));    
                                    
      unset($ontologyDelete);
      
      $ontologyRead = new OntologyReadQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
      
      $ontologyRead->mime("application/rdf+xml")
                   ->ontology($settings->testOntologyUri);
      
      $getPropertyFunction = new GetPropertyFunction();
      
      $getPropertyFunction->uri($settings->targetDatatypePropertyUri);
      
      $ontologyRead->getProperty($getPropertyFunction)
                   ->enableReasoner()
                   ->sourceInterface($settings->ontologyReadInterface)
                   ->sourceInterfaceVersion($settings->ontologyReadInterfaceVersion)
                   ->send();        
      
      // Since the ontology is not existing anymore, there is not auth information, so it means it as been
      // properly deleted.                                   
      $this->assertEquals($ontologyRead->getStatus(), "400", "Debugging information: ".var_export($ontologyRead, TRUE));                                       
      $this->assertEquals($ontologyRead->getStatusMessage(), "Bad Request", "Debugging information: ".var_export($ontologyRead, TRUE));
      $this->assertEquals($ontologyRead->error->id, "WS-ONTOLOGY-READ-204", "Debugging information: ".var_export($ontologyRead, TRUE));    

      unset($ontologyRead);      
      
      utilities\deleteOntology();      
      
      unset($settings);
    }     
    
    public function  testDeleteOntology_function_deleteProperty_Object() {
      
      $settings = new Config();  
      
      utilities\deleteOntology();
      
      $this->assertTrue(utilities\createOntology(), "Can't create the ontology, check the /ontology/create/ endpoint first...");
      
      // Delete Ontology
      $ontologyDelete = new OntologyDeleteQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
      
      $ontologyDelete->ontology($settings->testOntologyUri);
      
      $deletePropertyFunction = new DeletePropertyFunction();
      
      $deletePropertyFunction->uri($settings->targetObjectPropertyUri);
      
      $ontologyDelete->deleteProperty($deletePropertyFunction)
                     ->sourceInterface($settings->ontologyDeleteInterface)
                     ->sourceInterfaceVersion($settings->ontologyDeleteInterfaceVersion)
                     ->send();       
                                   
      $this->assertEquals($ontologyDelete->getStatus(), "200", "Debugging information: ".var_export($ontologyDelete, TRUE));    
                                    
      unset($ontologyDelete);
      
      // Make sure it is deleted      
      $ontologyRead = new OntologyReadQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
      
      $ontologyRead->mime("application/rdf+xml")
                   ->ontology($settings->testOntologyUri);
      
      $getPropertyFunction = new GetPropertyFunction();
      
      $getPropertyFunction->uri($settings->targetObjectPropertyUri);
      
      $ontologyRead->getProperty($getPropertyFunction)
                   ->enableReasoner()
                   ->sourceInterface($settings->ontologyReadInterface)
                   ->sourceInterfaceVersion($settings->ontologyReadInterfaceVersion)
                   ->send();             

      // Since the ontology is not existing anymore, there is not auth information, so it means it as been
      // properly deleted.                                   
      $this->assertEquals($ontologyRead->getStatus(), "400", "Debugging information: ".var_export($ontologyRead, TRUE));                                       
      $this->assertEquals($ontologyRead->getStatusMessage(), "Bad Request", "Debugging information: ".var_export($ontologyRead, TRUE));
      $this->assertEquals($ontologyRead->error->id, "WS-ONTOLOGY-READ-204", "Debugging information: ".var_export($ontologyRead, TRUE));    

      unset($ontologyRead);      
      
      utilities\deleteOntology();      
      
      unset($settings);
    }   
      
    public function  testDeleteOntology_function_deleteProperty_Annotation() {
      
      $settings = new Config();  
      
      utilities\deleteOntology();
      
      $this->assertTrue(utilities\createOntology(), "Can't create the ontology, check the /ontology/create/ endpoint first...");
      
      // Delete Ontology
      $ontologyDelete = new OntologyDeleteQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
      
      $ontologyDelete->ontology($settings->testOntologyUri);
      
      $deletePropertyFunction = new DeletePropertyFunction();
      
      $deletePropertyFunction->uri($settings->targetAnnotationPropertyUri);
      
      $ontologyDelete->deleteProperty($deletePropertyFunction)
                     ->sourceInterface($settings->ontologyDeleteInterface)
                     ->sourceInterfaceVersion($settings->ontologyDeleteInterfaceVersion)
                     ->send();        
                                   
      $this->assertEquals($ontologyDelete->getStatus(), "200", "Debugging information: ".var_export($ontologyDelete, TRUE));    
                                    
      unset($ontologyDelete);
      
      // Make sure it is deleted     
      $ontologyRead = new OntologyReadQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
      
      $ontologyRead->mime("application/rdf+xml")
                   ->ontology($settings->testOntologyUri);
      
      $getPropertyFunction = new GetPropertyFunction();
      
      $getPropertyFunction->uri($settings->targetAnnotationPropertyUri);
      
      $ontologyRead->getProperty($getPropertyFunction)
                   ->enableReasoner()
                   ->sourceInterface($settings->ontologyReadInterface)
                   ->sourceInterfaceVersion($settings->ontologyReadInterfaceVersion)
                   ->send();    

      // Since the ontology is not existing anymore, there is not auth information, so it means it as been
      // properly deleted.                                   
      $this->assertEquals($ontologyRead->getStatus(), "400", "Debugging information: ".var_export($ontologyRead, TRUE));                                       
      $this->assertEquals($ontologyRead->getStatusMessage(), "Bad Request", "Debugging information: ".var_export($ontologyRead, TRUE));
      $this->assertEquals($ontologyRead->error->id, "WS-ONTOLOGY-READ-204", "Debugging information: ".var_export($ontologyRead, TRUE));    

      unset($ontologyRead);      
      
      utilities\deleteOntology();      
      
      unset($settings);
    }
    
    public function  testDeleteOntology_function_deleteClass() {
      
      $settings = new Config();  
      
      utilities\deleteOntology();
      
      $this->assertTrue(utilities\createOntology(), "Can't create the ontology, check the /ontology/create/ endpoint first...");
      
      // Delete Ontology
      $ontologyDelete = new OntologyDeleteQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
      
      $ontologyDelete->ontology($settings->testOntologyUri);
      
      $deleteClassFunction = new DeleteClassFunction();
      
      $deleteClassFunction->uri($settings->targetClassUri);
      
      $ontologyDelete->deleteClass($deleteClassFunction)
                     ->sourceInterface($settings->ontologyDeleteInterface)
                     ->sourceInterfaceVersion($settings->ontologyDeleteInterfaceVersion)
                     ->send();        
      
      $this->assertEquals($ontologyDelete->getStatus(), "200", "Debugging information: ".var_export($ontologyDelete, TRUE));    
                                    
      unset($ontologyDelete);
      
      // @TODO For some reason, the Named Individual is not in the ontology anymore, it it is still returned
      // by the getOWLClass() API call when we execute this code. Need some more debugging to figure out
      // why this happens, and by getOWLClass() is not returning null.      
      
      // Make sure it is deleted      
      $ontologyRead = new OntologyReadQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
      
      $ontologyRead->mime("application/rdf+xml")
                   ->ontology($settings->testOntologyUri);
      
      $getClassFunction = new GetClassFunction();
      
      $getClassFunction->uri($settings->targetClassUri);
      
      $ontologyRead->getClass($getClassFunction)
                   ->enableReasoner()
                   ->sourceInterface($settings->ontologyReadInterface)
                   ->sourceInterfaceVersion($settings->ontologyReadInterfaceVersion)
                   ->send();  
         
      // Since the ontology is not existing anymore, there is not auth information, so it means it as been
      // properly deleted.                                   
      $this->assertEquals($ontologyRead->getStatus(), "400", "Debugging information: ".var_export($ontologyRead, TRUE));                                       
      $this->assertEquals($ontologyRead->getStatusMessage(), "Bad Request", "Debugging information: ".var_export($ontologyRead, TRUE));
      $this->assertEquals($ontologyRead->error->id, "WS-ONTOLOGY-READ-205", "Debugging information: ".var_export($ontologyRead, TRUE));    

      unset($ontologyRead);      
      
      utilities\deleteOntology();      
      
      unset($settings);
    }    
     
    public function  testDeleteOntology_function_deleteNamedIndividual() {
      
      $settings = new Config();  
      
      utilities\deleteOntology();
      
      $this->assertTrue(utilities\createOntology(), "Can't create the ontology, check the /ontology/create/ endpoint first...");
      
      // Delete Ontology
      $ontologyDelete = new OntologyDeleteQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
      
      $ontologyDelete->ontology($settings->testOntologyUri);
      
      $deleteNamedIndividualFunction = new DeleteNamedIndividualFunction();
      
      $deleteNamedIndividualFunction->uri($settings->targetNamedIndividualUri);
      
      $ontologyDelete->deleteNamedIndividual($deleteNamedIndividualFunction)
                     ->sourceInterface($settings->ontologyDeleteInterface)
                     ->sourceInterfaceVersion($settings->ontologyDeleteInterfaceVersion)
                     ->send();        
                                         
      $this->assertEquals($ontologyDelete->getStatus(), "200", "Debugging information: ".var_export($ontologyDelete, TRUE));    
                                    
      unset($ontologyDelete);
      
      // @TODO For some reason, the Named Individual is not in the ontology anymore, it it is still returned
      // by the getOWLNamedIndividal() API call when we execute this code. Need some more debugging to figure out
      // why this happens, and by getOWLNamedIndividal() is not returning null.
      
      // Make sure it is deleted      
      $ontologyRead = new OntologyReadQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
      
      $ontologyRead->mime("application/rdf+xml")
                   ->ontology($settings->testOntologyUri);
      
      $getNamedIndividualFunction = new GetNamedIndividualFunction();
      
      $getNamedIndividualFunction->uri($settings->targetNamedIndividualUri);
      
      $ontologyRead->getNamedIndividual($getNamedIndividualFunction)
                   ->enableReasoner()
                   ->sourceInterface($settings->ontologyReadInterface)
                   ->sourceInterfaceVersion($settings->ontologyReadInterfaceVersion)
                   ->send();        

      // Since the ontology is not existing anymore, there is not auth information, so it means it as been
      // properly deleted.                                   
      $this->assertEquals($ontologyRead->getStatus(), "400", "Debugging information: ".var_export($ontologyRead, TRUE));                                       
      $this->assertEquals($ontologyRead->getStatusMessage(), "Bad Request", "Debugging information: ".var_export($ontologyRead, TRUE));
      $this->assertEquals($ontologyRead->error->id, "WS-ONTOLOGY-READ-206", "Debugging information: ".var_export($ontologyRead, TRUE));    

      unset($ontologyRead);      
      
      utilities\deleteOntology();      
      
      unset($settings);
    }     
  }

  
?>