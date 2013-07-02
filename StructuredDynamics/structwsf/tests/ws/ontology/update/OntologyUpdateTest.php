<?php

  namespace StructuredDynamics\structwsf\tests\ws\ontology\update;
  
  use StructuredDynamics\structwsf\framework\WebServiceQuerier;
  use StructuredDynamics\structwsf\php\api\ws\ontology\update\CreateOrUpdateEntityFunction;
  use StructuredDynamics\structwsf\php\api\ws\ontology\update\OntologyUpdateQuery;
  use StructuredDynamics\structwsf\php\api\ws\ontology\update\UpdateEntityUriFunction;
  use StructuredDynamics\structwsf\php\api\ws\ontology\read\OntologyReadQuery;
  use StructuredDynamics\structwsf\php\api\ws\ontology\read\GetClassFunction;
  use StructuredDynamics\structwsf\php\api\ws\ontology\read\GetNamedIndividualFunction;
  use StructuredDynamics\structwsf\php\api\ws\ontology\read\GetPropertyFunction;
  use StructuredDynamics\structwsf\php\api\ws\crud\read\CrudReadQuery;
  use StructuredDynamics\structwsf\php\api\ws\search\SearchQuery;
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

  $settings = new Config(); 
  
  // Database connectivity procedures
  include_once($settings->structwsfInstanceFolder . "framework/ProcessorXML.php");
  include_once($settings->structwsfInstanceFolder . "framework/arc2/ARC2.php");
  
  class OntologyUpdateTest extends \PHPUnit_Framework_TestCase {
    
    static private $outputs = array();
    /*
    static public function setUpBeforeClass() 
    {    
      utilities\createOntology();      
    }
    
    static public function tearDownAfterClass() 
    {
      utilities\deleteOntology();      
    } 
    */ 
    
    public function testWrongEndpointUrl() {
      
      $settings = new Config();          
      
      $wsq = new WebServiceQuerier($settings->endpointUrl . "ontology/update/" . "wrong", 
                                   "post", 
                                   "text/xml",
                                   "ontology=" . urlencode($settings->testOntologyUri) .
                                   "&function=" .
                                   "&parameters=" .
                                   "&reasoner=" .
                                   "&registered_ip=self");
                   
      $this->assertEquals($wsq->getStatus(), "404", "Debugging information: ".var_export($wsq, TRUE));                                       
      $this->assertEquals($wsq->getStatusMessage(), "Not Found", "Debugging information: ".var_export($wsq, TRUE));
      
      unset($wsq);
      unset($settings);
    }

    public function testWrongEndpointMethodGet() {
      
      $settings = new Config();  
      
      $wsq = new WebServiceQuerier($settings->endpointUrl . "ontology/update/", 
                                   "get", 
                                   "text/xml",
                                   "ontology=" . urlencode($settings->testOntologyUri) .
                                   "&function=" .
                                   "&parameters=" .
                                   "&reasoner=" .
                                   "&registered_ip=self");
                                   
      $this->assertEquals($wsq->getStatus(), "405", "Debugging information: ".var_export($wsq, TRUE));                                       
      $this->assertEquals($wsq->getStatusMessage(), "Method Not Allowed", "Debugging information: ".var_export($wsq, TRUE));          
      
      unset($wsq);
      unset($settings);
    }   
    
    public function testValidInterfaceVersion() {
      
      $settings = new Config();  
      
      utilities\deleteOntology();
      
      $this->assertTrue(utilities\createOntology(), "Can't create the ontology, check the /ontology/create/ endpoint first...");      
      
      $createEntity = new CreateOrUpdateEntityFunction();
      
      $createEntity->enableAdvancedIndexation()
                   ->document(file_get_contents($settings->contentDir.'fooModified.owl'));
      
      $ontologyUpdate = new OntologyUpdateQuery($settings->endpointUrl);
      
      $ontologyUpdate->ontology($settings->testOntologyUri)
                     ->enableReasoner()
                     ->createOrUpdateEntity($createEntity)
                     ->sourceInterface($settings->ontologyUpdateInterface)
                     ->sourceInterfaceVersion($settings->ontologyUpdateInterfaceVersion)
                     ->send();
     
      $this->assertEquals($ontologyUpdate->getStatus(), "200", "Debugging information: ".var_export($ontologyUpdate, TRUE));                                       
                              
      utilities\deleteDataset();                              
                                   
      unset($ontologyUpdate);
      unset($settings);   
    }
       
    public function testInvalidInterfaceVersion() {
      
      $settings = new Config();  
                  
      utilities\deleteOntology();
      
      $this->assertTrue(utilities\createOntology(), "Can't create the ontology, check the /ontology/create/ endpoint first...");      
      
      $createEntity = new CreateOrUpdateEntityFunction();
      
      $createEntity->enableAdvancedIndexation()
                   ->document(file_get_contents($settings->contentDir.'fooModified.owl'));
      
      $ontologyUpdate = new OntologyUpdateQuery($settings->endpointUrl);
      
      $ontologyUpdate->ontology($settings->testOntologyUri)
                     ->enableReasoner()
                     ->createOrUpdateEntity($createEntity)
                     ->sourceInterface($settings->ontologyUpdateInterface)
                     ->sourceInterfaceVersion('667.7')
                     ->send();
      
      $this->assertEquals($ontologyUpdate->getStatus(), "400", "Debugging information: ".var_export($ontologyUpdate, TRUE));                                       
      $this->assertEquals($ontologyUpdate->getStatusMessage(), "Bad Request", "Debugging information: ".var_export($ontologyUpdate, TRUE));
      $this->assertEquals($ontologyUpdate->error->id, "WS-ONTOLOGY-UPDATE-303", "Debugging information: ".var_export($ontologyUpdate, TRUE));                                                                          
                              
      utilities\deleteDataset();                              
                                   
      unset($ontologyUpdate);
      unset($settings); 
    }
    
    public function testInterfaceExists() {
      
      $settings = new Config();  
      
      utilities\deleteOntology();
      
      $this->assertTrue(utilities\createOntology(), "Can't create the ontology, check the /ontology/create/ endpoint first...");      
      
      $createEntity = new CreateOrUpdateEntityFunction();
      
      $createEntity->enableAdvancedIndexation()
                   ->document(file_get_contents($settings->contentDir.'fooModified.owl'));
      
      $ontologyUpdate = new OntologyUpdateQuery($settings->endpointUrl);
      
      $ontologyUpdate->ontology($settings->testOntologyUri)
                     ->enableReasoner()
                     ->createOrUpdateEntity($createEntity)
                     ->sourceInterface($settings->ontologyUpdateInterface)
                     ->sourceInterfaceVersion($settings->ontologyUpdateInterfaceVersion)
                     ->send();
     
      $this->assertEquals($ontologyUpdate->getStatus(), "200", "Debugging information: ".var_export($ontologyUpdate, TRUE));                                       
                              
      utilities\deleteDataset();                              
                                   
      unset($ontologyUpdate);
      unset($settings);   
    } 
    
    public function testUnexistingInterface() {
      
      $settings = new Config();  
                  
      utilities\deleteOntology();
      
      $this->assertTrue(utilities\createOntology(), "Can't create the ontology, check the /ontology/create/ endpoint first...");      
      
      $createEntity = new CreateOrUpdateEntityFunction();
      
      $createEntity->enableAdvancedIndexation()
                   ->document(file_get_contents($settings->contentDir.'fooModified.owl'));
      
      $ontologyUpdate = new OntologyUpdateQuery($settings->endpointUrl);
      
      $ontologyUpdate->ontology($settings->testOntologyUri)
                     ->enableReasoner()
                     ->createOrUpdateEntity($createEntity)
                     ->sourceInterface('unexisting-interface')
                     ->sourceInterfaceVersion($settings->ontologyUpdateInterfaceVersion)
                     ->send();
      
      $this->assertEquals($ontologyUpdate->getStatus(), "400", "Debugging information: ".var_export($ontologyUpdate, TRUE));                                       
      $this->assertEquals($ontologyUpdate->getStatusMessage(), "Bad Request", "Debugging information: ".var_export($ontologyUpdate, TRUE));
      $this->assertEquals($ontologyUpdate->error->id, "WS-ONTOLOGY-UPDATE-305", "Debugging information: ".var_export($ontologyUpdate, TRUE));                                                                          
                              
      utilities\deleteDataset();                              
                                   
      unset($ontologyUpdate);
      unset($settings); 
    }
    
    public function testNoOntologyUri() {
      
      $settings = new Config();  
                  
      utilities\deleteOntology();
      
      $this->assertTrue(utilities\createOntology(), "Can't create the ontology, check the /ontology/create/ endpoint first...");      
      
      $createEntity = new CreateOrUpdateEntityFunction();
      
      $createEntity->enableAdvancedIndexation()
                   ->document(file_get_contents($settings->contentDir.'fooModified.owl'));
      
      $ontologyUpdate = new OntologyUpdateQuery($settings->endpointUrl);
      
      $ontologyUpdate->ontology('')
                     ->enableReasoner()
                     ->createOrUpdateEntity($createEntity)
                     ->sourceInterface($settings->ontologyUpdateInterface)
                     ->sourceInterfaceVersion($settings->ontologyUpdateInterfaceVersion)
                     ->send();
      
      $this->assertEquals($ontologyUpdate->getStatus(), "400", "Debugging information: ".var_export($ontologyUpdate, TRUE));                                       
      $this->assertEquals($ontologyUpdate->getStatusMessage(), "Bad Request", "Debugging information: ".var_export($ontologyUpdate, TRUE));
      $this->assertEquals($ontologyUpdate->error->id, "WS-ONTOLOGY-UPDATE-300", "Debugging information: ".var_export($ontologyUpdate, TRUE));                                                                          
                              
      utilities\deleteDataset();                              
                                   
      unset($ontologyUpdate);
      unset($settings); 
    }    
   
    public function testCantLoadOntologyFile() {
      
      $settings = new Config();  
                  
      utilities\deleteOntology();
      
      $this->assertTrue(utilities\createOntology(), "Can't create the ontology, check the /ontology/create/ endpoint first...");      
      
      $createEntity = new CreateOrUpdateEntityFunction();
      
      $createEntity->enableAdvancedIndexation()
                   ->document(file_get_contents($settings->contentDir.'fooModified.owl'));
      
      $ontologyUpdate = new OntologyUpdateQuery($settings->endpointUrl);
      
      $ontologyUpdate->ontology($settings->testOntologyUri.'test')
                     ->enableReasoner()
                     ->createOrUpdateEntity($createEntity)
                     ->sourceInterface($settings->ontologyUpdateInterface)
                     ->sourceInterfaceVersion($settings->ontologyUpdateInterfaceVersion)
                     ->send();
      
      $this->assertEquals($ontologyUpdate->getStatus(), "400", "Debugging information: ".var_export($ontologyUpdate, TRUE));                                       
      $this->assertEquals($ontologyUpdate->getStatusMessage(), "Bad Request", "Debugging information: ".var_export($ontologyUpdate, TRUE));
      $this->assertEquals($ontologyUpdate->error->id, "WS-ONTOLOGY-UPDATE-300", "Debugging information: ".var_export($ontologyUpdate, TRUE));                                                                          
                              
      utilities\deleteDataset();                              
                                   
      unset($ontologyUpdate);
      unset($settings); 
    }    

    public function testUnknownFunctionCall() {
      
      $settings = new Config();  
      
      $wsq = new WebServiceQuerier($settings->endpointUrl . "ontology/update/", 
                                   "post", 
                                   "text/xml",
                                   "ontology=" . urlencode($settings->testOntologyUri) .
                                   "&function=unknown" .
                                   "&parameters=" .
                                   "&reasoner=" .
                                   "&registered_ip=self");
                                   
      $this->assertEquals($wsq->getStatus(), "400", "Debugging information: ".var_export($wsq, TRUE));                                       
      $this->assertEquals($wsq->getStatusMessage(), "Bad Request", "Debugging information: ".var_export($wsq, TRUE));
      $this->assertEquals($wsq->error->id, "WS-ONTOLOGY-UPDATE-201", "Debugging information: ".var_export($wsq, TRUE));                                                                          
      
      unset($wsq);
      unset($settings);
    }     

    public function testMissingOldUriParameter() {
      
      $settings = new Config();  
                  
      utilities\deleteOntology();
      
      $this->assertTrue(utilities\createOntology(), "Can't create the ontology, check the /ontology/create/ endpoint first...");      
      
      $updateEntityUri = new UpdateEntityUriFunction();
      
      $updateEntityUri->enableAdvancedIndexation()
                      ->newUri('')
                      ->oldUri('');
      
      $createEntity = new CreateOrUpdateEntityFunction();
      
      $createEntity->enableAdvancedIndexation()
                   ->document(file_get_contents($settings->contentDir.'fooModified.owl'));
      
      $ontologyUpdate = new OntologyUpdateQuery($settings->endpointUrl);
      
      $ontologyUpdate->ontology($settings->testOntologyUri)
                     ->enableReasoner()
                     ->updateEntityUri($updateEntityUri)
                     ->sourceInterface($settings->ontologyUpdateInterface)
                     ->sourceInterfaceVersion($settings->ontologyUpdateInterfaceVersion)
                     ->send();
      
      $this->assertEquals($ontologyUpdate->getStatus(), "400", "Debugging information: ".var_export($ontologyUpdate, TRUE));                                       
      $this->assertEquals($ontologyUpdate->getStatusMessage(), "Bad Request", "Debugging information: ".var_export($ontologyUpdate, TRUE));
      $this->assertEquals($ontologyUpdate->error->id, "WS-ONTOLOGY-UPDATE-202", "Debugging information: ".var_export($ontologyUpdate, TRUE));                                                                          
                              
      utilities\deleteDataset();                              
                                   
      unset($ontologyUpdate);
      unset($settings); 
    }

    public function testMissingNewUriParameter() {
      
      $settings = new Config();  
                  
      utilities\deleteOntology();
      
      $this->assertTrue(utilities\createOntology(), "Can't create the ontology, check the /ontology/create/ endpoint first...");      
      
      $updateEntityUri = new UpdateEntityUriFunction();
      
      $updateEntityUri->enableAdvancedIndexation()
                      ->newUri('')
                      ->oldUri('test');
      
      $createEntity = new CreateOrUpdateEntityFunction();
      
      $createEntity->enableAdvancedIndexation()
                   ->document(file_get_contents($settings->contentDir.'fooModified.owl'));
      
      $ontologyUpdate = new OntologyUpdateQuery($settings->endpointUrl);
      
      $ontologyUpdate->ontology($settings->testOntologyUri)
                     ->enableReasoner()
                     ->updateEntityUri($updateEntityUri)
                     ->sourceInterface($settings->ontologyUpdateInterface)
                     ->sourceInterfaceVersion($settings->ontologyUpdateInterfaceVersion)
                     ->send();
      
      $this->assertEquals($ontologyUpdate->getStatus(), "400", "Debugging information: ".var_export($ontologyUpdate, TRUE));                                       
      $this->assertEquals($ontologyUpdate->getStatusMessage(), "Bad Request", "Debugging information: ".var_export($ontologyUpdate, TRUE));
      $this->assertEquals($ontologyUpdate->error->id, "WS-ONTOLOGY-UPDATE-203", "Debugging information: ".var_export($ontologyUpdate, TRUE));                                                                          
                              
      utilities\deleteDataset();                              
                                   
      unset($ontologyUpdate);
      unset($settings); 
    }

    public function testCantParseOntologyFile() {
      
      $settings = new Config();  
      
      utilities\deleteOntology();
      
      $this->assertTrue(utilities\createOntology(), "Can't create the ontology, check the /ontology/create/ endpoint first...");      
      
      $createEntity = new CreateOrUpdateEntityFunction();
      
      $createEntity->enableAdvancedIndexation()
                   ->document(file_get_contents($settings->contentDir.'fooModified.owl').'cant-parse');
      
      $ontologyUpdate = new OntologyUpdateQuery($settings->endpointUrl);
      
      $ontologyUpdate->ontology($settings->testOntologyUri)
                     ->enableReasoner()
                     ->createOrUpdateEntity($createEntity)
                     ->sourceInterface($settings->ontologyUpdateInterface)
                     ->sourceInterfaceVersion($settings->ontologyUpdateInterfaceVersion)
                     ->send();
     
      $this->assertEquals($ontologyUpdate->getStatus(), "400", "Debugging information: ".var_export($ontologyUpdate, TRUE));                                       
      $this->assertEquals($ontologyUpdate->getStatusMessage(), "Bad Request", "Debugging information: ".var_export($ontologyUpdate, TRUE));
      $this->assertEquals($ontologyUpdate->error->id, "WS-ONTOLOGY-UPDATE-301", "Debugging information: ".var_export($ontologyUpdate, TRUE));                                                                          
                              
      utilities\deleteDataset();                              
                                   
      unset($ontologyUpdate);
      unset($settings);   
    }
    
    public function test_Class_B_Update_EnabledAdvancedIndexation_OWLAPI()
    {
      
      $settings = new Config();  
      
      utilities\deleteOntology();
      
      $this->assertTrue(utilities\createOntology(), "Can't create the ontology, check the /ontology/create/ endpoint first...");      
                               
      $createEntity = new CreateOrUpdateEntityFunction();
      
      $createEntity->enableAdvancedIndexation()
                   ->document(file_get_contents($settings->contentDir.'fooModified.owl'));
      
      $ontologyUpdate = new OntologyUpdateQuery($settings->endpointUrl);
      
      $ontologyUpdate->ontology($settings->testOntologyUri)
                     ->enableReasoner()
                     ->createOrUpdateEntity($createEntity)
                     ->sourceInterface($settings->ontologyUpdateInterface)
                     ->sourceInterfaceVersion($settings->ontologyUpdateInterfaceVersion)
                     ->send();
     
      $this->assertEquals($ontologyUpdate->getStatus(), "200", "Debugging information: ".var_export($ontologyUpdate, TRUE));                                       
              
      $getClassFunction = new GetClassFunction();
      
      $getClassFunction->uri('http://foo.org/test#B');
              
      $ontologyRead = new OntologyReadQuery($settings->endpointUrl);              
      
      $ontologyRead->enableReasoner()
                   ->mime('application/rdf+xml')
                   ->ontology($settings->testOntologyUri)
                   ->getClass($getClassFunction)
                   ->sourceInterface($settings->ontologyReadInterface)
                   ->sourceInterfaceVersion($settings->ontologyReadInterfaceVersion)
                   ->send();

      $this->assertEquals($ontologyRead->getStatus(), "200", "Debugging information: ".var_export($ontologyRead, TRUE));                                       

      utilities\validateParameterApplicationRdfXml($this, $ontologyRead);
      
      $this->assertTrue(utilities\compareRdf($ontologyRead->getResultset(), file_get_contents($settings->contentDir.'validation/ontology_update_class_b_update.xml'), TRUE));
              
                              
      utilities\deleteDataset();                              
                                   
      unset($ontologyUpdate);
      unset($settings);        
    }
    
    public function test_Class_C_Update_EnabledAdvancedIndexation_OWLAPI()
    {
      
      $settings = new Config();  
      
      utilities\deleteOntology();
      
      $this->assertTrue(utilities\createOntology(), "Can't create the ontology, check the /ontology/create/ endpoint first...");      
                               
      $createEntity = new CreateOrUpdateEntityFunction();
      
      $createEntity->enableAdvancedIndexation()
                   ->document(file_get_contents($settings->contentDir.'fooModified.owl'));
      
      $ontologyUpdate = new OntologyUpdateQuery($settings->endpointUrl);
      
      $ontologyUpdate->ontology($settings->testOntologyUri)
                     ->enableReasoner()
                     ->createOrUpdateEntity($createEntity)
                     ->sourceInterface($settings->ontologyUpdateInterface)
                     ->sourceInterfaceVersion($settings->ontologyUpdateInterfaceVersion)
                     ->send();
     
      $this->assertEquals($ontologyUpdate->getStatus(), "200", "Debugging information: ".var_export($ontologyUpdate, TRUE));                                       
              
      $getClassFunction = new GetClassFunction();
      
      $getClassFunction->uri('http://foo.org/test#C');
              
      $ontologyRead = new OntologyReadQuery($settings->endpointUrl);              
      
      $ontologyRead->enableReasoner()
                   ->mime('application/rdf+xml')
                   ->ontology($settings->testOntologyUri)
                   ->getClass($getClassFunction)
                   ->sourceInterface($settings->ontologyReadInterface)
                   ->sourceInterfaceVersion($settings->ontologyReadInterfaceVersion)
                   ->send();

      $this->assertEquals($ontologyRead->getStatus(), "200", "Debugging information: ".var_export($ontologyRead, TRUE));                                       

      utilities\validateParameterApplicationRdfXml($this, $ontologyRead);
      
      $this->assertTrue(utilities\compareRdf($ontologyRead->getResultset(), file_get_contents($settings->contentDir.'validation/ontology_update_class_c_update.xml'), TRUE));
              
                              
      utilities\deleteDataset();                              
                                   
      unset($ontologyUpdate);
      unset($settings);        
    }
    
    public function test_AnnotationProperty_Update_EnabledAdvancedIndexation_OWLAPI()
    {
      
      $settings = new Config();  
      
      utilities\deleteOntology();
      
      $this->assertTrue(utilities\createOntology(), "Can't create the ontology, check the /ontology/create/ endpoint first...");      
                               
      $createEntity = new CreateOrUpdateEntityFunction();
      
      $createEntity->enableAdvancedIndexation()
                   ->document(file_get_contents($settings->contentDir.'fooModified.owl'));
      
      $ontologyUpdate = new OntologyUpdateQuery($settings->endpointUrl);
      
      $ontologyUpdate->ontology($settings->testOntologyUri)
                     ->enableReasoner()
                     ->createOrUpdateEntity($createEntity)
                     ->sourceInterface($settings->ontologyUpdateInterface)
                     ->sourceInterfaceVersion($settings->ontologyUpdateInterfaceVersion)
                     ->send();
     
      $this->assertEquals($ontologyUpdate->getStatus(), "200", "Debugging information: ".var_export($ontologyUpdate, TRUE));                                       
              
              
              
      $getPropertyFunction = new GetPropertyFunction();
      
      $getPropertyFunction->uri('http://foo.org/test#aA');
              
      $ontologyRead = new OntologyReadQuery($settings->endpointUrl);              
      
      $ontologyRead->enableReasoner()
                   ->mime('application/rdf+xml')
                   ->ontology($settings->testOntologyUri)
                   ->getProperty($getPropertyFunction)
                   ->sourceInterface($settings->ontologyReadInterface)
                   ->sourceInterfaceVersion($settings->ontologyReadInterfaceVersion)
                   ->send();

      $this->assertEquals($ontologyRead->getStatus(), "200", "Debugging information: ".var_export($ontologyRead, TRUE));                                       

      utilities\validateParameterApplicationRdfXml($this, $ontologyRead);
      
      $this->assertTrue(utilities\compareRdf($ontologyRead->getResultset(), file_get_contents($settings->contentDir.'validation/ontology_update_annotation_property_update.xml'), TRUE));
              
                              
      utilities\deleteDataset();                              
                                   
      unset($ontologyUpdate);
      unset($settings);        
    }        
    
    public function test_DatatypeProperty_Update_EnabledAdvancedIndexation_OWLAPI()
    {
      
      $settings = new Config();  
      
      utilities\deleteOntology();
      
      $this->assertTrue(utilities\createOntology(), "Can't create the ontology, check the /ontology/create/ endpoint first...");      
                               
      $createEntity = new CreateOrUpdateEntityFunction();
      
      $createEntity->enableAdvancedIndexation()
                   ->document(file_get_contents($settings->contentDir.'fooModified.owl'));
      
      $ontologyUpdate = new OntologyUpdateQuery($settings->endpointUrl);
      
      $ontologyUpdate->ontology($settings->testOntologyUri)
                     ->enableReasoner()
                     ->createOrUpdateEntity($createEntity)
                     ->sourceInterface($settings->ontologyUpdateInterface)
                     ->sourceInterfaceVersion($settings->ontologyUpdateInterfaceVersion)
                     ->send();
     
      $this->assertEquals($ontologyUpdate->getStatus(), "200", "Debugging information: ".var_export($ontologyUpdate, TRUE));                                       
              
      $getPropertyFunction = new GetPropertyFunction();
      
      $getPropertyFunction->uri('http://foo.org/test#dpD');
              
      $ontologyRead = new OntologyReadQuery($settings->endpointUrl);              
      
      $ontologyRead->enableReasoner()
                   ->mime('application/rdf+xml')
                   ->ontology($settings->testOntologyUri)
                   ->getProperty($getPropertyFunction)
                   ->sourceInterface($settings->ontologyReadInterface)
                   ->sourceInterfaceVersion($settings->ontologyReadInterfaceVersion)
                   ->send();

      $this->assertEquals($ontologyRead->getStatus(), "200", "Debugging information: ".var_export($ontologyRead, TRUE));                                       

      utilities\validateParameterApplicationRdfXml($this, $ontologyRead);
      
      $this->assertTrue(utilities\compareRdf($ontologyRead->getResultset(), file_get_contents($settings->contentDir.'validation/ontology_update_datatype_property_update.xml'), TRUE));
              
                              
      utilities\deleteDataset();                              
                                   
      unset($ontologyUpdate);
      unset($settings);        
    }        
    
    public function test_ObjectProperty_Update_EnabledAdvancedIndexation_OWLAPI()
    {
      
      $settings = new Config();  
      
      utilities\deleteOntology();
      
      $this->assertTrue(utilities\createOntology(), "Can't create the ontology, check the /ontology/create/ endpoint first...");      
                               
      $createEntity = new CreateOrUpdateEntityFunction();
      
      $createEntity->enableAdvancedIndexation()
                   ->document(file_get_contents($settings->contentDir.'fooModified.owl'));
      
      $ontologyUpdate = new OntologyUpdateQuery($settings->endpointUrl);
      
      $ontologyUpdate->ontology($settings->testOntologyUri)
                     ->enableReasoner()
                     ->createOrUpdateEntity($createEntity)
                     ->sourceInterface($settings->ontologyUpdateInterface)
                     ->sourceInterfaceVersion($settings->ontologyUpdateInterfaceVersion)
                     ->send();
     
      $this->assertEquals($ontologyUpdate->getStatus(), "200", "Debugging information: ".var_export($ontologyUpdate, TRUE));                                       
              
      $getPropertyFunction = new GetPropertyFunction();
      
      $getPropertyFunction->uri('http://foo.org/test#opA');
              
      $ontologyRead = new OntologyReadQuery($settings->endpointUrl);              
      
      $ontologyRead->enableReasoner()
                   ->mime('application/rdf+xml')
                   ->ontology($settings->testOntologyUri)
                   ->getProperty($getPropertyFunction)
                   ->sourceInterface($settings->ontologyReadInterface)
                   ->sourceInterfaceVersion($settings->ontologyReadInterfaceVersion)
                   ->send();

      $this->assertEquals($ontologyRead->getStatus(), "200", "Debugging information: ".var_export($ontologyRead, TRUE));                                       

      utilities\validateParameterApplicationRdfXml($this, $ontologyRead);
      
      $this->assertTrue(utilities\compareRdf($ontologyRead->getResultset(), file_get_contents($settings->contentDir.'validation/ontology_update_object_property_update.xml'), TRUE));
              
                              
      utilities\deleteDataset();                              
                                   
      unset($ontologyUpdate);
      unset($settings);        
    }
    
    public function test_NamedIndividual_Update_EnabledAdvancedIndexation_OWLAPI()
    {
      
      $settings = new Config();  
      
      utilities\deleteOntology();
      
      $this->assertTrue(utilities\createOntology(), "Can't create the ontology, check the /ontology/create/ endpoint first...");      
                               
      $createEntity = new CreateOrUpdateEntityFunction();
      
      $createEntity->enableAdvancedIndexation()
                   ->document(file_get_contents($settings->contentDir.'fooModified.owl'));
      
      $ontologyUpdate = new OntologyUpdateQuery($settings->endpointUrl);
      
      $ontologyUpdate->ontology($settings->testOntologyUri)
                     ->enableReasoner()
                     ->createOrUpdateEntity($createEntity)
                     ->sourceInterface($settings->ontologyUpdateInterface)
                     ->sourceInterfaceVersion($settings->ontologyUpdateInterfaceVersion)
                     ->send();
     
      $this->assertEquals($ontologyUpdate->getStatus(), "200", "Debugging information: ".var_export($ontologyUpdate, TRUE));                                       
              
      $getNamedIndidivualFunction = new GetNamedIndividualFunction();
              
      $getNamedIndidivualFunction->uri('http://foo.org/test#niE');
              
      $ontologyRead = new OntologyReadQuery($settings->endpointUrl);              
      
      $ontologyRead->enableReasoner()
                   ->mime('application/rdf+xml')
                   ->ontology($settings->testOntologyUri)
                   ->getNamedIndividual($getNamedIndidivualFunction)
                   ->sourceInterface($settings->ontologyReadInterface)
                   ->sourceInterfaceVersion($settings->ontologyReadInterfaceVersion)
                   ->send();

      $this->assertEquals($ontologyRead->getStatus(), "200", "Debugging information: ".var_export($ontologyRead, TRUE));                                       

      utilities\validateParameterApplicationRdfXml($this, $ontologyRead);
      
      $this->assertTrue(utilities\compareRdf($ontologyRead->getResultset(), file_get_contents($settings->contentDir.'validation/ontology_update_named_individual_update.xml'), TRUE));
              
                              
      utilities\deleteDataset();                              
                                   
      unset($ontologyUpdate);
      unset($settings);        
    }  
   
    public function test_Class_B_Update_EnabledAdvancedIndexation_TripleStore()
    {
      
      $settings = new Config();  
      
      utilities\deleteOntology();
      
      $this->assertTrue(utilities\createOntology(), "Can't create the ontology, check the /ontology/create/ endpoint first...");      
                               
      $createEntity = new CreateOrUpdateEntityFunction();
      
      $createEntity->enableAdvancedIndexation()
                   ->document(file_get_contents($settings->contentDir.'fooModified.owl'));
      
      $ontologyUpdate = new OntologyUpdateQuery($settings->endpointUrl);
      
      $ontologyUpdate->ontology($settings->testOntologyUri)
                     ->enableReasoner()
                     ->createOrUpdateEntity($createEntity)
                     ->sourceInterface($settings->ontologyUpdateInterface)
                     ->sourceInterfaceVersion($settings->ontologyUpdateInterfaceVersion)
                     ->send();
     
      $this->assertEquals($ontologyUpdate->getStatus(), "200", "Debugging information: ".var_export($ontologyUpdate, TRUE));                                       
              
      $crudRead = new CrudReadQuery($settings->endpointUrl);
      
      $crudRead->dataset($settings->testOntologyUri)
               ->mime('application/rdf+xml')
               ->uri('http://foo.org/test#B')
               ->excludeLinksback()
               ->excludeReification()
               ->sourceInterface($settings->crudReadInterface)
               ->sourceInterfaceVersion($settings->crudReadInterfaceVersion)
               ->send();
                            
      $this->assertEquals($crudRead->getStatus(), "200", "Debugging information: ".var_export($crudRead, TRUE));                                       

      utilities\validateParameterApplicationRdfXml($this, $crudRead);
    
      $this->assertTrue(utilities\compareRdf($crudRead->getResultset(), file_get_contents($settings->contentDir.'validation/ontology_update_class_b_update.xml'), TRUE));
              
      utilities\deleteDataset();                              
                                   
      unset($ontologyUpdate);
      unset($settings);        
    }
    
    public function test_Class_C_Update_EnabledAdvancedIndexation_TripleStore()
    {
      
      $settings = new Config();  
      
      utilities\deleteOntology();
      
      $this->assertTrue(utilities\createOntology(), "Can't create the ontology, check the /ontology/create/ endpoint first...");      
                               
      $createEntity = new CreateOrUpdateEntityFunction();
      
      $createEntity->enableAdvancedIndexation()
                   ->document(file_get_contents($settings->contentDir.'fooModified.owl'));
      
      $ontologyUpdate = new OntologyUpdateQuery($settings->endpointUrl);
      
      $ontologyUpdate->ontology($settings->testOntologyUri)
                     ->enableReasoner()
                     ->createOrUpdateEntity($createEntity)
                     ->sourceInterface($settings->ontologyUpdateInterface)
                     ->sourceInterfaceVersion($settings->ontologyUpdateInterfaceVersion)
                     ->send();
     
      $this->assertEquals($ontologyUpdate->getStatus(), "200", "Debugging information: ".var_export($ontologyUpdate, TRUE));                                       
                            
      $crudRead = new CrudReadQuery($settings->endpointUrl);
      
      $crudRead->dataset($settings->testOntologyUri)
               ->mime('application/rdf+xml')
               ->uri('http://foo.org/test#C')
               ->excludeLinksback()
               ->excludeReification()
               ->sourceInterface($settings->crudReadInterface)
               ->sourceInterfaceVersion($settings->crudReadInterfaceVersion)
               ->send();
                            
      $this->assertEquals($crudRead->getStatus(), "200", "Debugging information: ".var_export($crudRead, TRUE));                                       

      utilities\validateParameterApplicationRdfXml($this, $crudRead);

      $expected = str_replace('<rdfs:subClassOf rdf:resource="http://www.w3.org/2002/07/owl#Thing" />', '', file_get_contents($settings->contentDir.'validation/ontology_update_class_c_update.xml'));
    
      $this->assertTrue(utilities\compareRdf($crudRead->getResultset(), $expected, TRUE));
                              
      utilities\deleteDataset();                              
                                   
      unset($ontologyUpdate);
      unset($settings);        
    }
    
    public function test_AnnotationProperty_Update_EnabledAdvancedIndexation_TripleStore()
    {
      
      $settings = new Config();  
      
      utilities\deleteOntology();
      
      $this->assertTrue(utilities\createOntology(), "Can't create the ontology, check the /ontology/create/ endpoint first...");      
                               
      $createEntity = new CreateOrUpdateEntityFunction();
      
      $createEntity->enableAdvancedIndexation()
                   ->document(file_get_contents($settings->contentDir.'fooModified.owl'));
      
      $ontologyUpdate = new OntologyUpdateQuery($settings->endpointUrl);
      
      $ontologyUpdate->ontology($settings->testOntologyUri)
                     ->enableReasoner()
                     ->createOrUpdateEntity($createEntity)
                     ->sourceInterface($settings->ontologyUpdateInterface)
                     ->sourceInterfaceVersion($settings->ontologyUpdateInterfaceVersion)
                     ->send();
     
      $this->assertEquals($ontologyUpdate->getStatus(), "200", "Debugging information: ".var_export($ontologyUpdate, TRUE));                                       
              
      $crudRead = new CrudReadQuery($settings->endpointUrl);
      
      $crudRead->dataset($settings->testOntologyUri)
               ->mime('application/rdf+xml')
               ->uri('http://foo.org/test#aA')
               ->excludeLinksback()
               ->excludeReification()
               ->sourceInterface($settings->crudReadInterface)
               ->sourceInterfaceVersion($settings->crudReadInterfaceVersion)
               ->send();
                            
      $this->assertEquals($crudRead->getStatus(), "200", "Debugging information: ".var_export($crudRead, TRUE));                                       

      utilities\validateParameterApplicationRdfXml($this, $crudRead);
    
      $this->assertTrue(utilities\compareRdf($crudRead->getResultset(), file_get_contents($settings->contentDir.'validation/ontology_update_annotation_property_update.xml'), TRUE));

      utilities\deleteDataset();                              
                                   
      unset($ontologyUpdate);
      unset($settings);        
    }        

    public function test_DatatypeProperty_Update_EnabledAdvancedIndexation_TripleStore()
    {
      
      $settings = new Config();  
      
      utilities\deleteOntology();
      
      $this->assertTrue(utilities\createOntology(), "Can't create the ontology, check the /ontology/create/ endpoint first...");      
                               
      $createEntity = new CreateOrUpdateEntityFunction();
      
      $createEntity->enableAdvancedIndexation()
                   ->document(file_get_contents($settings->contentDir.'fooModified.owl'));
      
      $ontologyUpdate = new OntologyUpdateQuery($settings->endpointUrl);
      
      $ontologyUpdate->ontology($settings->testOntologyUri)
                     ->enableReasoner()
                     ->createOrUpdateEntity($createEntity)
                     ->sourceInterface($settings->ontologyUpdateInterface)
                     ->sourceInterfaceVersion($settings->ontologyUpdateInterfaceVersion)
                     ->send();
     
      $this->assertEquals($ontologyUpdate->getStatus(), "200", "Debugging information: ".var_export($ontologyUpdate, TRUE));                                       
              
      $crudRead = new CrudReadQuery($settings->endpointUrl);
      
      $crudRead->dataset($settings->testOntologyUri)
               ->mime('application/rdf+xml')
               ->uri('http://foo.org/test#dpD')
               ->excludeLinksback()
               ->excludeReification()
               ->sourceInterface($settings->crudReadInterface)
               ->sourceInterfaceVersion($settings->crudReadInterfaceVersion)
               ->send();
                            
      $this->assertEquals($crudRead->getStatus(), "200", "Debugging information: ".var_export($crudRead, TRUE));                                       

      utilities\validateParameterApplicationRdfXml($this, $crudRead);
    
      $expected = str_replace('<rdfs:subPropertyOf rdf:resource="http://www.w3.org/2002/07/owl#topDataProperty" />',  '', file_get_contents($settings->contentDir.'validation/ontology_update_datatype_property_update.xml'));
    
      $this->assertTrue(utilities\compareRdf($crudRead->getResultset(), $expected, TRUE));
                              
      utilities\deleteDataset();                              
                                   
      unset($ontologyUpdate);
      unset($settings);        
    }        

    public function test_ObjectProperty_Update_EnabledAdvancedIndexation_TripleStore()
    {      
      $settings = new Config();  
      
      utilities\deleteOntology();
      
      $this->assertTrue(utilities\createOntology(), "Can't create the ontology, check the /ontology/create/ endpoint first...");      
                               
      $createEntity = new CreateOrUpdateEntityFunction();
      
      $createEntity->enableAdvancedIndexation()
                   ->document(file_get_contents($settings->contentDir.'fooModified.owl'));
      
      $ontologyUpdate = new OntologyUpdateQuery($settings->endpointUrl);
      
      $ontologyUpdate->ontology($settings->testOntologyUri)
                     ->enableReasoner()
                     ->createOrUpdateEntity($createEntity)
                     ->sourceInterface($settings->ontologyUpdateInterface)
                     ->sourceInterfaceVersion($settings->ontologyUpdateInterfaceVersion)
                     ->send();
     
      $this->assertEquals($ontologyUpdate->getStatus(), "200", "Debugging information: ".var_export($ontologyUpdate, TRUE));                                       
              
      $crudRead = new CrudReadQuery($settings->endpointUrl);
      
      $crudRead->dataset($settings->testOntologyUri)
               ->mime('application/rdf+xml')
               ->uri('http://foo.org/test#opA')
               ->excludeLinksback()
               ->excludeReification()
               ->sourceInterface($settings->crudReadInterface)
               ->sourceInterfaceVersion($settings->crudReadInterfaceVersion)
               ->send();
                            
      $this->assertEquals($crudRead->getStatus(), "200", "Debugging information: ".var_export($crudRead, TRUE));                                       

      utilities\validateParameterApplicationRdfXml($this, $crudRead);
    
      $expected = str_replace('<rdfs:subPropertyOf rdf:resource="http://www.w3.org/2002/07/owl#topObjectProperty" />', '', file_get_contents($settings->contentDir.'validation/ontology_update_object_property_update.xml'));
      $expected = str_replace('<umbel:superPropertyOf rdf:resource="http://foo.org/test#opB" />', '', $expected);
    
      $this->assertTrue(utilities\compareRdf($crudRead->getResultset(), $expected, TRUE));
                              
      utilities\deleteDataset();                              
                                   
      unset($ontologyUpdate);
      unset($settings);        
    }

    public function test_NamedIndividual_Update_EnabledAdvancedIndexation_TripleStore()
    {
      
      $settings = new Config();  
      
      utilities\deleteOntology();
      
      $this->assertTrue(utilities\createOntology(), "Can't create the ontology, check the /ontology/create/ endpoint first...");      
                               
      $createEntity = new CreateOrUpdateEntityFunction();
      
      $createEntity->enableAdvancedIndexation()
                   ->document(file_get_contents($settings->contentDir.'fooModified.owl'));
      
      $ontologyUpdate = new OntologyUpdateQuery($settings->endpointUrl);
      
      $ontologyUpdate->ontology($settings->testOntologyUri)
                     ->enableReasoner()
                     ->createOrUpdateEntity($createEntity)
                     ->sourceInterface($settings->ontologyUpdateInterface)
                     ->sourceInterfaceVersion($settings->ontologyUpdateInterfaceVersion)
                     ->send();
     
      $this->assertEquals($ontologyUpdate->getStatus(), "200", "Debugging information: ".var_export($ontologyUpdate, TRUE));                                       
              
              
      $crudRead = new CrudReadQuery($settings->endpointUrl);
      
      $crudRead->dataset($settings->testOntologyUri)
               ->mime('application/rdf+xml')
               ->uri('http://foo.org/test#niE')
               ->excludeLinksback()
               ->excludeReification()
               ->sourceInterface($settings->crudReadInterface)
               ->sourceInterfaceVersion($settings->crudReadInterfaceVersion)
               ->send();
                            
      $this->assertEquals($crudRead->getStatus(), "200", "Debugging information: ".var_export($crudRead, TRUE));                                       

      utilities\validateParameterApplicationRdfXml($this, $crudRead);
    
      $actual = str_replace('ns0:E', 'owl:Thing', $crudRead->getResultset());
      $actual = str_replace('<rdf:type rdf:resource="http://www.w3.org/2002/07/owl#NamedIndividual" />', '<rdf:type rdf:resource="http://www.w3.org/2002/07/owl#NamedIndividual" /><rdf:type rdf:resource="http://foo.org/test#E" />', $actual);
    
      $this->assertTrue(utilities\compareRdf($actual, file_get_contents($settings->contentDir.'validation/ontology_update_named_individual_update.xml'), TRUE));
                              
      utilities\deleteDataset();                              
                                   
      unset($ontologyUpdate);
      unset($settings);        
    }
   
    public function test_Class_B_Update_EnabledAdvancedIndexation_Solr()
    {      
      $settings = new Config();  
      
      utilities\deleteOntology();
      
      $this->assertTrue(utilities\createOntology(), "Can't create the ontology, check the /ontology/create/ endpoint first...");      
                               
      $createEntity = new CreateOrUpdateEntityFunction();
      
      $createEntity->enableAdvancedIndexation()
                   ->document(file_get_contents($settings->contentDir.'fooModified.owl'));
      
      $ontologyUpdate = new OntologyUpdateQuery($settings->endpointUrl);
      
      $ontologyUpdate->ontology($settings->testOntologyUri)
                     ->enableReasoner()
                     ->createOrUpdateEntity($createEntity)
                     ->sourceInterface($settings->ontologyUpdateInterface)
                     ->sourceInterfaceVersion($settings->ontologyUpdateInterfaceVersion)
                     ->send();
     
      $this->assertEquals($ontologyUpdate->getStatus(), "200", "Debugging information: ".var_export($ontologyUpdate, TRUE));                                       
       
      $search = new SearchQuery($settings->endpointUrl);
      
      $search->attributeValuesFilters('uri', 'http://foo.org/test#B')
             ->excludeAggregates()
             ->excludeScores()
             ->excludeSpellcheck()
             ->mime('application/rdf+xml')
             ->datasetFilter($settings->testOntologyUri)
             ->send();
                                                          
      $this->assertEquals($search->getStatus(), "200", "Debugging information: ".var_export($search, TRUE));                                       

      utilities\validateParameterApplicationRdfXml($this, $search);
    
      // Fix the expected RDF to fit the specificities of the Search endpoint resultset
      $expected = preg_replace('/xml\:lang=".*"/', '', file_get_contents($settings->contentDir.'validation/ontology_update_class_b_update.xml'));
      $expected = str_replace('rdfs:label', 'iron:prefLabel', $expected);
    
      $this->assertTrue(utilities\compareRdf($search->getResultset(), $expected, TRUE));              
                              
      utilities\deleteDataset();                              
                                   
      unset($ontologyUpdate);
      unset($settings);        
    }  
    
          
    public function test_Class_C_Update_EnabledAdvancedIndexation_Solr()
    {
      
      $settings = new Config();  
      
      utilities\deleteOntology();
      
      $this->assertTrue(utilities\createOntology(), "Can't create the ontology, check the /ontology/create/ endpoint first...");      
                               
      $createEntity = new CreateOrUpdateEntityFunction();
      
      $createEntity->enableAdvancedIndexation()
                   ->document(file_get_contents($settings->contentDir.'fooModified.owl'));
      
      $ontologyUpdate = new OntologyUpdateQuery($settings->endpointUrl);
      
      $ontologyUpdate->ontology($settings->testOntologyUri)
                     ->enableReasoner()
                     ->createOrUpdateEntity($createEntity)
                     ->sourceInterface($settings->ontologyUpdateInterface)
                     ->sourceInterfaceVersion($settings->ontologyUpdateInterfaceVersion)
                     ->send();
     
      $this->assertEquals($ontologyUpdate->getStatus(), "200", "Debugging information: ".var_export($ontologyUpdate, TRUE));                                       
              
      $search = new SearchQuery($settings->endpointUrl);
      
      $search->attributeValuesFilters('uri', 'http://foo.org/test#B')
             ->excludeAggregates()
             ->excludeScores()
             ->excludeSpellcheck()
             ->mime('application/rdf+xml')
             ->datasetFilter($settings->testOntologyUri)
             ->send();
                                                          
      $this->assertEquals($search->getStatus(), "200", "Debugging information: ".var_export($search, TRUE));                                       

      utilities\validateParameterApplicationRdfXml($this, $search);
    
      // Fix the expected RDF to fit the specificities of the Search endpoint resultset
      $expected = preg_replace('/xml\:lang=".*"/', '', file_get_contents($settings->contentDir.'validation/ontology_update_class_b_update.xml'));
      $expected = str_replace('rdfs:label', 'iron:prefLabel', $expected);
    
      $this->assertTrue(utilities\compareRdf($search->getResultset(), $expected, TRUE));              
                              
      utilities\deleteDataset();                              
                                   
      unset($ontologyUpdate);
      unset($settings);        
    }
    
    public function test_AnnotationProperty_Update_EnabledAdvancedIndexation_Solr()
    {
      
      $settings = new Config();  
      
      utilities\deleteOntology();
      
      $this->assertTrue(utilities\createOntology(), "Can't create the ontology, check the /ontology/create/ endpoint first...");      
                               
      $createEntity = new CreateOrUpdateEntityFunction();
      
      $createEntity->enableAdvancedIndexation()
                   ->document(file_get_contents($settings->contentDir.'fooModified.owl'));
      
      $ontologyUpdate = new OntologyUpdateQuery($settings->endpointUrl);
      
      $ontologyUpdate->ontology($settings->testOntologyUri)
                     ->enableReasoner()
                     ->createOrUpdateEntity($createEntity)
                     ->sourceInterface($settings->ontologyUpdateInterface)
                     ->sourceInterfaceVersion($settings->ontologyUpdateInterfaceVersion)
                     ->send();
     
      $this->assertEquals($ontologyUpdate->getStatus(), "200", "Debugging information: ".var_export($ontologyUpdate, TRUE));                                       
              
      $search = new SearchQuery($settings->endpointUrl);
      
      $search->attributeValuesFilters('uri', 'http://foo.org/test#B')
             ->excludeAggregates()
             ->excludeScores()
             ->excludeSpellcheck()
             ->mime('application/rdf+xml')
             ->datasetFilter($settings->testOntologyUri)
             ->send();
                                                          
      $this->assertEquals($search->getStatus(), "200", "Debugging information: ".var_export($search, TRUE));                                       

      utilities\validateParameterApplicationRdfXml($this, $search);
    
      // Fix the expected RDF to fit the specificities of the Search endpoint resultset
      $expected = preg_replace('/xml\:lang=".*"/', '', file_get_contents($settings->contentDir.'validation/ontology_update_class_b_update.xml'));
      $expected = str_replace('rdfs:label', 'iron:prefLabel', $expected);
    
      $this->assertTrue(utilities\compareRdf($search->getResultset(), $expected, TRUE));              
                              
      utilities\deleteDataset();                              
                                   
      unset($ontologyUpdate);
      unset($settings);        
    }        

    public function test_DatatypeProperty_Update_EnabledAdvancedIndexation_Solr()
    {
      
      $settings = new Config();  
      
      utilities\deleteOntology();
      
      $this->assertTrue(utilities\createOntology(), "Can't create the ontology, check the /ontology/create/ endpoint first...");      
                               
      $createEntity = new CreateOrUpdateEntityFunction();
      
      $createEntity->enableAdvancedIndexation()
                   ->document(file_get_contents($settings->contentDir.'fooModified.owl'));
      
      $ontologyUpdate = new OntologyUpdateQuery($settings->endpointUrl);
      
      $ontologyUpdate->ontology($settings->testOntologyUri)
                     ->enableReasoner()
                     ->createOrUpdateEntity($createEntity)
                     ->sourceInterface($settings->ontologyUpdateInterface)
                     ->sourceInterfaceVersion($settings->ontologyUpdateInterfaceVersion)
                     ->send();
     
      $this->assertEquals($ontologyUpdate->getStatus(), "200", "Debugging information: ".var_export($ontologyUpdate, TRUE));                                       
              
      $search = new SearchQuery($settings->endpointUrl);
      
      $search->attributeValuesFilters('uri', 'http://foo.org/test#B')
             ->excludeAggregates()
             ->excludeScores()
             ->excludeSpellcheck()
             ->mime('application/rdf+xml')
             ->datasetFilter($settings->testOntologyUri)
             ->send();
                                                          
      $this->assertEquals($search->getStatus(), "200", "Debugging information: ".var_export($search, TRUE));                                       

      utilities\validateParameterApplicationRdfXml($this, $search);
    
      // Fix the expected RDF to fit the specificities of the Search endpoint resultset
      $expected = preg_replace('/xml\:lang=".*"/', '', file_get_contents($settings->contentDir.'validation/ontology_update_class_b_update.xml'));
      $expected = str_replace('rdfs:label', 'iron:prefLabel', $expected);
    
      $this->assertTrue(utilities\compareRdf($search->getResultset(), $expected, TRUE));              
                              
      utilities\deleteDataset();                              
                                   
      unset($ontologyUpdate);
      unset($settings);        
    }        

    public function test_ObjectProperty_Update_EnabledAdvancedIndexation_Solr()
    {
      
      $settings = new Config();  
      
      utilities\deleteOntology();
      
      $this->assertTrue(utilities\createOntology(), "Can't create the ontology, check the /ontology/create/ endpoint first...");      
                               
      $createEntity = new CreateOrUpdateEntityFunction();
      
      $createEntity->enableAdvancedIndexation()
                   ->document(file_get_contents($settings->contentDir.'fooModified.owl'));
      
      $ontologyUpdate = new OntologyUpdateQuery($settings->endpointUrl);
      
      $ontologyUpdate->ontology($settings->testOntologyUri)
                     ->enableReasoner()
                     ->createOrUpdateEntity($createEntity)
                     ->sourceInterface($settings->ontologyUpdateInterface)
                     ->sourceInterfaceVersion($settings->ontologyUpdateInterfaceVersion)
                     ->send();
     
      $this->assertEquals($ontologyUpdate->getStatus(), "200", "Debugging information: ".var_export($ontologyUpdate, TRUE));                                       
              
      $search = new SearchQuery($settings->endpointUrl);
      
      $search->attributeValuesFilters('uri', 'http://foo.org/test#B')
             ->excludeAggregates()
             ->excludeScores()
             ->excludeSpellcheck()
             ->mime('application/rdf+xml')
             ->datasetFilter($settings->testOntologyUri)
             ->send();
                                                          
      $this->assertEquals($search->getStatus(), "200", "Debugging information: ".var_export($search, TRUE));                                       

      utilities\validateParameterApplicationRdfXml($this, $search);
    
      // Fix the expected RDF to fit the specificities of the Search endpoint resultset
      $expected = preg_replace('/xml\:lang=".*"/', '', file_get_contents($settings->contentDir.'validation/ontology_update_class_b_update.xml'));
      $expected = str_replace('rdfs:label', 'iron:prefLabel', $expected);
    
      $this->assertTrue(utilities\compareRdf($search->getResultset(), $expected, TRUE));              
                              
      utilities\deleteDataset();                              
                                   
      unset($ontologyUpdate);
      unset($settings);        
    }

    public function test_NamedIndividual_Update_EnabledAdvancedIndexation_Solr()
    {
      
      $settings = new Config();  
      
      utilities\deleteOntology();
      
      $this->assertTrue(utilities\createOntology(), "Can't create the ontology, check the /ontology/create/ endpoint first...");      
                               
      $createEntity = new CreateOrUpdateEntityFunction();
      
      $createEntity->enableAdvancedIndexation()
                   ->document(file_get_contents($settings->contentDir.'fooModified.owl'));
      
      $ontologyUpdate = new OntologyUpdateQuery($settings->endpointUrl);
      
      $ontologyUpdate->ontology($settings->testOntologyUri)
                     ->enableReasoner()
                     ->createOrUpdateEntity($createEntity)
                     ->sourceInterface($settings->ontologyUpdateInterface)
                     ->sourceInterfaceVersion($settings->ontologyUpdateInterfaceVersion)
                     ->send();
     
      $this->assertEquals($ontologyUpdate->getStatus(), "200", "Debugging information: ".var_export($ontologyUpdate, TRUE));                                       
              
      $search = new SearchQuery($settings->endpointUrl);
      
      $search->attributeValuesFilters('uri', 'http://foo.org/test#B')
             ->excludeAggregates()
             ->excludeScores()
             ->excludeSpellcheck()
             ->mime('application/rdf+xml')
             ->datasetFilter($settings->testOntologyUri)
             ->send();
                                                          
      $this->assertEquals($search->getStatus(), "200", "Debugging information: ".var_export($search, TRUE));                                       

      utilities\validateParameterApplicationRdfXml($this, $search);
    
      // Fix the expected RDF to fit the specificities of the Search endpoint resultset
      $expected = preg_replace('/xml\:lang=".*"/', '', file_get_contents($settings->contentDir.'validation/ontology_update_class_b_update.xml'));
      $expected = str_replace('rdfs:label', 'iron:prefLabel', $expected);
    
      $this->assertTrue(utilities\compareRdf($search->getResultset(), $expected, TRUE));              
                              
      utilities\deleteDataset();                              
                                   
      unset($ontologyUpdate);
      unset($settings);        
    }
    
    public function test_Class_B_Update_DisabledAdvancedIndexation_OWLAPI()
    {
      
      $settings = new Config();  
      
      utilities\deleteOntology();
      
      $this->assertTrue(utilities\createOntology(FALSE), "Can't create the ontology, check the /ontology/create/ endpoint first...");      
                               
      $createEntity = new CreateOrUpdateEntityFunction();
      
      $createEntity->disableAdvancedIndexation()
                   ->document(file_get_contents($settings->contentDir.'fooModified.owl'));
      
      $ontologyUpdate = new OntologyUpdateQuery($settings->endpointUrl);
      
      $ontologyUpdate->ontology($settings->testOntologyUri)
                     ->enableReasoner()
                     ->createOrUpdateEntity($createEntity)
                     ->sourceInterface($settings->ontologyUpdateInterface)
                     ->sourceInterfaceVersion($settings->ontologyUpdateInterfaceVersion)
                     ->send();
     
      $this->assertEquals($ontologyUpdate->getStatus(), "200", "Debugging information: ".var_export($ontologyUpdate, TRUE));                                       
              
      $getClassFunction = new GetClassFunction();
      
      $getClassFunction->uri('http://foo.org/test#B');
              
      $ontologyRead = new OntologyReadQuery($settings->endpointUrl);              
      
      $ontologyRead->enableReasoner()
                   ->mime('application/rdf+xml') 
                   ->ontology($settings->testOntologyUri)
                   ->getClass($getClassFunction)
                   ->sourceInterface($settings->ontologyReadInterface)
                   ->sourceInterfaceVersion($settings->ontologyReadInterfaceVersion)
                   ->send();

      $this->assertEquals($ontologyRead->getStatus(), "200", "Debugging information: ".var_export($ontologyRead, TRUE));                                       

      utilities\validateParameterApplicationRdfXml($this, $ontologyRead);
      
      $this->assertTrue(utilities\compareRdf($ontologyRead->getResultset(), file_get_contents($settings->contentDir.'validation/ontology_update_class_b_update.xml'), TRUE));
              
                              
      utilities\deleteDataset();                              
                                   
      unset($ontologyUpdate);
      unset($settings);        
    }
    
    public function test_Class_C_Update_DisabledAdvancedIndexation_OWLAPI()
    {
      
      $settings = new Config();  
      
      utilities\deleteOntology();
      
      $this->assertTrue(utilities\createOntology(FALSE), "Can't create the ontology, check the /ontology/create/ endpoint first...");      
                               
      $createEntity = new CreateOrUpdateEntityFunction();
      
      $createEntity->disableAdvancedIndexation()
                   ->document(file_get_contents($settings->contentDir.'fooModified.owl'));
      
      $ontologyUpdate = new OntologyUpdateQuery($settings->endpointUrl);
      
      $ontologyUpdate->ontology($settings->testOntologyUri)
                     ->enableReasoner()
                     ->createOrUpdateEntity($createEntity)
                     ->sourceInterface($settings->ontologyUpdateInterface)
                     ->sourceInterfaceVersion($settings->ontologyUpdateInterfaceVersion)
                     ->send();
     
      $this->assertEquals($ontologyUpdate->getStatus(), "200", "Debugging information: ".var_export($ontologyUpdate, TRUE));                                       
              
      $getClassFunction = new GetClassFunction();
      
      $getClassFunction->uri('http://foo.org/test#C');
              
      $ontologyRead = new OntologyReadQuery($settings->endpointUrl);              
      
      $ontologyRead->enableReasoner()
                   ->mime('application/rdf+xml') 
                   ->ontology($settings->testOntologyUri)
                   ->getClass($getClassFunction)
                   ->sourceInterface($settings->ontologyReadInterface)
                   ->sourceInterfaceVersion($settings->ontologyReadInterfaceVersion)
                   ->send();

      $this->assertEquals($ontologyRead->getStatus(), "200", "Debugging information: ".var_export($ontologyRead, TRUE));                                       

      utilities\validateParameterApplicationRdfXml($this, $ontologyRead);
      
      $this->assertTrue(utilities\compareRdf($ontologyRead->getResultset(), file_get_contents($settings->contentDir.'validation/ontology_update_class_c_update.xml'), TRUE));
              
                              
      utilities\deleteDataset();                              
                                   
      unset($ontologyUpdate);
      unset($settings);        
    }
    
    public function test_AnnotationProperty_Update_DisabledAdvancedIndexation_OWLAPI()
    {
      
      $settings = new Config();  
      
      utilities\deleteOntology();
      
      $this->assertTrue(utilities\createOntology(FALSE), "Can't create the ontology, check the /ontology/create/ endpoint first...");      
                               
      $createEntity = new CreateOrUpdateEntityFunction();
      
      $createEntity->disableAdvancedIndexation()
                   ->document(file_get_contents($settings->contentDir.'fooModified.owl'));
      
      $ontologyUpdate = new OntologyUpdateQuery($settings->endpointUrl);
      
      $ontologyUpdate->ontology($settings->testOntologyUri)
                     ->enableReasoner()
                     ->createOrUpdateEntity($createEntity)
                     ->sourceInterface($settings->ontologyUpdateInterface)
                     ->sourceInterfaceVersion($settings->ontologyUpdateInterfaceVersion)
                     ->send();
     
      $this->assertEquals($ontologyUpdate->getStatus(), "200", "Debugging information: ".var_export($ontologyUpdate, TRUE));                                       
              
              
              
      $getPropertyFunction = new GetPropertyFunction();
      
      $getPropertyFunction->uri('http://foo.org/test#aA');
              
      $ontologyRead = new OntologyReadQuery($settings->endpointUrl);              
      
      $ontologyRead->enableReasoner()
                   ->mime('application/rdf+xml') 
                   ->ontology($settings->testOntologyUri)
                   ->getProperty($getPropertyFunction)
                   ->sourceInterface($settings->ontologyReadInterface)
                   ->sourceInterfaceVersion($settings->ontologyReadInterfaceVersion)
                   ->send();

      $this->assertEquals($ontologyRead->getStatus(), "200", "Debugging information: ".var_export($ontologyRead, TRUE));                                       

      utilities\validateParameterApplicationRdfXml($this, $ontologyRead);
      
      $this->assertTrue(utilities\compareRdf($ontologyRead->getResultset(), file_get_contents($settings->contentDir.'validation/ontology_update_annotation_property_update.xml'), TRUE));
              
                              
      utilities\deleteDataset();                              
                                   
      unset($ontologyUpdate);
      unset($settings);        
    }        
    
    public function test_DatatypeProperty_Update_DisabledAdvancedIndexation_OWLAPI()
    {
      
      $settings = new Config();  
      
      utilities\deleteOntology();
      
      $this->assertTrue(utilities\createOntology(FALSE), "Can't create the ontology, check the /ontology/create/ endpoint first...");      
                               
      $createEntity = new CreateOrUpdateEntityFunction();
      
      $createEntity->disableAdvancedIndexation()
                   ->document(file_get_contents($settings->contentDir.'fooModified.owl'));
      
      $ontologyUpdate = new OntologyUpdateQuery($settings->endpointUrl);
      
      $ontologyUpdate->ontology($settings->testOntologyUri)
                     ->enableReasoner()
                     ->createOrUpdateEntity($createEntity)
                     ->sourceInterface($settings->ontologyUpdateInterface)
                     ->sourceInterfaceVersion($settings->ontologyUpdateInterfaceVersion)
                     ->send();
     
      $this->assertEquals($ontologyUpdate->getStatus(), "200", "Debugging information: ".var_export($ontologyUpdate, TRUE));                                       
              
      $getPropertyFunction = new GetPropertyFunction();
      
      $getPropertyFunction->uri('http://foo.org/test#dpD');
              
      $ontologyRead = new OntologyReadQuery($settings->endpointUrl);              
      
      $ontologyRead->enableReasoner()
                   ->mime('application/rdf+xml') 
                   ->ontology($settings->testOntologyUri)
                   ->getProperty($getPropertyFunction)
                   ->sourceInterface($settings->ontologyReadInterface)
                   ->sourceInterfaceVersion($settings->ontologyReadInterfaceVersion)
                   ->send();

      $this->assertEquals($ontologyRead->getStatus(), "200", "Debugging information: ".var_export($ontologyRead, TRUE));                                       

      utilities\validateParameterApplicationRdfXml($this, $ontologyRead);
      
      $this->assertTrue(utilities\compareRdf($ontologyRead->getResultset(), file_get_contents($settings->contentDir.'validation/ontology_update_datatype_property_update.xml'), TRUE));
              
                              
      utilities\deleteDataset();                              
                                   
      unset($ontologyUpdate);
      unset($settings);        
    }        
    
    public function test_ObjectProperty_Update_DisabledAdvancedIndexation_OWLAPI()
    {
      
      $settings = new Config();  
      
      utilities\deleteOntology();
      
      $this->assertTrue(utilities\createOntology(FALSE), "Can't create the ontology, check the /ontology/create/ endpoint first...");      
                               
      $createEntity = new CreateOrUpdateEntityFunction();
      
      $createEntity->disableAdvancedIndexation()
                   ->document(file_get_contents($settings->contentDir.'fooModified.owl'));
      
      $ontologyUpdate = new OntologyUpdateQuery($settings->endpointUrl);
      
      $ontologyUpdate->ontology($settings->testOntologyUri)
                     ->enableReasoner()
                     ->createOrUpdateEntity($createEntity)
                     ->sourceInterface($settings->ontologyUpdateInterface)
                     ->sourceInterfaceVersion($settings->ontologyUpdateInterfaceVersion)
                     ->send();
     
      $this->assertEquals($ontologyUpdate->getStatus(), "200", "Debugging information: ".var_export($ontologyUpdate, TRUE));                                       
              
      $getPropertyFunction = new GetPropertyFunction();
      
      $getPropertyFunction->uri('http://foo.org/test#opA');
              
      $ontologyRead = new OntologyReadQuery($settings->endpointUrl);              
      
      $ontologyRead->enableReasoner()
                   ->mime('application/rdf+xml') 
                   ->ontology($settings->testOntologyUri)
                   ->getProperty($getPropertyFunction)
                   ->sourceInterface($settings->ontologyReadInterface)
                   ->sourceInterfaceVersion($settings->ontologyReadInterfaceVersion)
                   ->send();

      $this->assertEquals($ontologyRead->getStatus(), "200", "Debugging information: ".var_export($ontologyRead, TRUE));                                       

      utilities\validateParameterApplicationRdfXml($this, $ontologyRead);
      
      $this->assertTrue(utilities\compareRdf($ontologyRead->getResultset(), file_get_contents($settings->contentDir.'validation/ontology_update_object_property_update.xml'), TRUE));
              
                              
      utilities\deleteDataset();                              
                                   
      unset($ontologyUpdate);
      unset($settings);        
    }
    
    public function test_NamedIndividual_Update_DisabledAdvancedIndexation_OWLAPI()
    {
      
      $settings = new Config();  
      
      utilities\deleteOntology();
      
      $this->assertTrue(utilities\createOntology(FALSE), "Can't create the ontology, check the /ontology/create/ endpoint first...");      
                               
      $createEntity = new CreateOrUpdateEntityFunction();
      
      $createEntity->disableAdvancedIndexation()
                   ->document(file_get_contents($settings->contentDir.'fooModified.owl'));
      
      $ontologyUpdate = new OntologyUpdateQuery($settings->endpointUrl);
      
      $ontologyUpdate->ontology($settings->testOntologyUri)
                     ->enableReasoner()
                     ->createOrUpdateEntity($createEntity)
                     ->sourceInterface($settings->ontologyUpdateInterface)
                     ->sourceInterfaceVersion($settings->ontologyUpdateInterfaceVersion)
                     ->send();
     
      $this->assertEquals($ontologyUpdate->getStatus(), "200", "Debugging information: ".var_export($ontologyUpdate, TRUE));                                       
              
      $getNamedIndidivualFunction = new GetNamedIndividualFunction();
              
      $getNamedIndidivualFunction->uri('http://foo.org/test#niE');
              
      $ontologyRead = new OntologyReadQuery($settings->endpointUrl);              
      
      $ontologyRead->enableReasoner()
                   ->mime('application/rdf+xml') 
                   ->ontology($settings->testOntologyUri)
                   ->getNamedIndividual($getNamedIndidivualFunction)
                   ->sourceInterface($settings->ontologyReadInterface)
                   ->sourceInterfaceVersion($settings->ontologyReadInterfaceVersion)
                   ->send();

      $this->assertEquals($ontologyRead->getStatus(), "200", "Debugging information: ".var_export($ontologyRead, TRUE));                                       

      utilities\validateParameterApplicationRdfXml($this, $ontologyRead);
      
      $this->assertTrue(utilities\compareRdf($ontologyRead->getResultset(), file_get_contents($settings->contentDir.'validation/ontology_update_named_individual_update.xml'), TRUE));
              
                              
      utilities\deleteDataset();                              
                                   
      unset($ontologyUpdate);
      unset($settings);        
    }  
    
    public function test_Class_B_Update_DisabledAdvancedIndexation_TripleStore()
    {
      
      $settings = new Config();  
      
      utilities\deleteOntology();
      
      $this->assertTrue(utilities\createOntology(FALSE), "Can't create the ontology, check the /ontology/create/ endpoint first...");      
                               
      $createEntity = new CreateOrUpdateEntityFunction();
      
      $createEntity->disableAdvancedIndexation()
                   ->document(file_get_contents($settings->contentDir.'fooModified.owl'));
      
      $ontologyUpdate = new OntologyUpdateQuery($settings->endpointUrl);
      
      $ontologyUpdate->ontology($settings->testOntologyUri)
                     ->enableReasoner()
                     ->createOrUpdateEntity($createEntity)
                     ->sourceInterface($settings->ontologyUpdateInterface)
                     ->sourceInterfaceVersion($settings->ontologyUpdateInterfaceVersion)
                     ->send();
     
      $this->assertEquals($ontologyUpdate->getStatus(), "200", "Debugging information: ".var_export($ontologyUpdate, TRUE));                                       
              
      $crudRead = new CrudReadQuery($settings->endpointUrl);
      
      $crudRead->dataset($settings->testOntologyUri)
               ->mime('resultset')
               ->uri('http://foo.org/test#B')
               ->excludeLinksback()
               ->excludeReification()
               ->sourceInterface($settings->crudReadInterface)
               ->sourceInterfaceVersion($settings->crudReadInterfaceVersion)
               ->send();

      $this->assertEquals($crudRead->getStatus(), "400", "Debugging information: ".var_export($crudRead, TRUE));                                       
      $this->assertEquals($crudRead->getStatusMessage(), "Bad Request", "Debugging information: ".var_export($crudRead, TRUE));
      $this->assertEquals($crudRead->error->id, "WS-CRUD-READ-300", "Debugging information: ".var_export($crudRead, TRUE));                                                                          
                                    
      utilities\deleteDataset();                              
                                   
      unset($ontologyUpdate);
      unset($settings);        
    }
    
    public function test_Class_C_Update_DisabledAdvancedIndexation_TripleStore()
    {
      
      $settings = new Config();  
      
      utilities\deleteOntology();
      
      $this->assertTrue(utilities\createOntology(FALSE), "Can't create the ontology, check the /ontology/create/ endpoint first...");      
                               
      $createEntity = new CreateOrUpdateEntityFunction();
      
      $createEntity->disableAdvancedIndexation()
                   ->document(file_get_contents($settings->contentDir.'fooModified.owl'));
      
      $ontologyUpdate = new OntologyUpdateQuery($settings->endpointUrl);
      
      $ontologyUpdate->ontology($settings->testOntologyUri)
                     ->enableReasoner()
                     ->createOrUpdateEntity($createEntity)
                     ->sourceInterface($settings->ontologyUpdateInterface)
                     ->sourceInterfaceVersion($settings->ontologyUpdateInterfaceVersion)
                     ->send();
     
      $this->assertEquals($ontologyUpdate->getStatus(), "200", "Debugging information: ".var_export($ontologyUpdate, TRUE));                                       
              
      $crudRead = new CrudReadQuery($settings->endpointUrl);
      
      $crudRead->dataset($settings->testOntologyUri)
               ->mime('resultset')
               ->uri('http://foo.org/test#C')
               ->excludeLinksback()
               ->excludeReification()
               ->sourceInterface($settings->crudReadInterface)
               ->sourceInterfaceVersion($settings->crudReadInterfaceVersion)
               ->send();

      $this->assertEquals($crudRead->getStatus(), "400", "Debugging information: ".var_export($crudRead, TRUE));                                       
      $this->assertEquals($crudRead->getStatusMessage(), "Bad Request", "Debugging information: ".var_export($crudRead, TRUE));
      $this->assertEquals($crudRead->error->id, "WS-CRUD-READ-300", "Debugging information: ".var_export($crudRead, TRUE));                                                                          
                              
      utilities\deleteDataset();                              
                                   
      unset($ontologyUpdate);
      unset($settings);        
    }
    
    public function test_AnnotationProperty_Update_DisabledAdvancedIndexation_TripleStore()
    {
      
      $settings = new Config();  
      
      utilities\deleteOntology();
      
      $this->assertTrue(utilities\createOntology(FALSE), "Can't create the ontology, check the /ontology/create/ endpoint first...");      
                               
      $createEntity = new CreateOrUpdateEntityFunction();
      
      $createEntity->disableAdvancedIndexation()
                   ->document(file_get_contents($settings->contentDir.'fooModified.owl'));
      
      $ontologyUpdate = new OntologyUpdateQuery($settings->endpointUrl);
      
      $ontologyUpdate->ontology($settings->testOntologyUri)
                     ->enableReasoner()
                     ->createOrUpdateEntity($createEntity)
                     ->sourceInterface($settings->ontologyUpdateInterface)
                     ->sourceInterfaceVersion($settings->ontologyUpdateInterfaceVersion)
                     ->send();
     
      $this->assertEquals($ontologyUpdate->getStatus(), "200", "Debugging information: ".var_export($ontologyUpdate, TRUE));                                       
                              
      $crudRead = new CrudReadQuery($settings->endpointUrl);
      
      $crudRead->dataset($settings->testOntologyUri)
               ->mime('resultset')
               ->uri('http://foo.org/test#aA')
               ->excludeLinksback()
               ->excludeReification()
               ->sourceInterface($settings->crudReadInterface)
               ->sourceInterfaceVersion($settings->crudReadInterfaceVersion)
               ->send();

      $this->assertEquals($crudRead->getStatus(), "400", "Debugging information: ".var_export($crudRead, TRUE));                                       
      $this->assertEquals($crudRead->getStatusMessage(), "Bad Request", "Debugging information: ".var_export($crudRead, TRUE));
      $this->assertEquals($crudRead->error->id, "WS-CRUD-READ-300", "Debugging information: ".var_export($crudRead, TRUE));                                                                          
                              
      utilities\deleteDataset();                              
                                   
      unset($ontologyUpdate);
      unset($settings);        
    }        

    public function test_DatatypeProperty_Update_DisabledAdvancedIndexation_TripleStore()
    {
      
      $settings = new Config();  
      
      utilities\deleteOntology();
      
      $this->assertTrue(utilities\createOntology(FALSE), "Can't create the ontology, check the /ontology/create/ endpoint first...");      
                               
      $createEntity = new CreateOrUpdateEntityFunction();
      
      $createEntity->disableAdvancedIndexation()
                   ->document(file_get_contents($settings->contentDir.'fooModified.owl'));
      
      $ontologyUpdate = new OntologyUpdateQuery($settings->endpointUrl);
      
      $ontologyUpdate->ontology($settings->testOntologyUri)
                     ->enableReasoner()
                     ->createOrUpdateEntity($createEntity)
                     ->sourceInterface($settings->ontologyUpdateInterface)
                     ->sourceInterfaceVersion($settings->ontologyUpdateInterfaceVersion)
                     ->send();     
     
      $this->assertEquals($ontologyUpdate->getStatus(), "200", "Debugging information: ".var_export($ontologyUpdate, TRUE));                                       
                    
      $crudRead = new CrudReadQuery($settings->endpointUrl);
      
      $crudRead->dataset($settings->testOntologyUri)
               ->mime('resultset')
               ->uri('http://foo.org/test#dpD')
               ->excludeLinksback()
               ->excludeReification()
               ->sourceInterface($settings->crudReadInterface)
               ->sourceInterfaceVersion($settings->crudReadInterfaceVersion)
               ->send();

      $this->assertEquals($crudRead->getStatus(), "400", "Debugging information: ".var_export($crudRead, TRUE));                                       
      $this->assertEquals($crudRead->getStatusMessage(), "Bad Request", "Debugging information: ".var_export($crudRead, TRUE));
      $this->assertEquals($crudRead->error->id, "WS-CRUD-READ-300", "Debugging information: ".var_export($crudRead, TRUE));                                                                            
                              
      utilities\deleteDataset();                              
                                   
      unset($ontologyUpdate);
      unset($settings);        
    }        

    public function test_ObjectProperty_Update_DisabledAdvancedIndexation_TripleStore()
    {
      
      $settings = new Config();  
      
      utilities\deleteOntology();
      
      $this->assertTrue(utilities\createOntology(FALSE), "Can't create the ontology, check the /ontology/create/ endpoint first...");      
                               
      $createEntity = new CreateOrUpdateEntityFunction();
      
      $createEntity->disableAdvancedIndexation()
                   ->document(file_get_contents($settings->contentDir.'fooModified.owl'));
      
      $ontologyUpdate = new OntologyUpdateQuery($settings->endpointUrl);
      
      $ontologyUpdate->ontology($settings->testOntologyUri)
                     ->enableReasoner()
                     ->createOrUpdateEntity($createEntity)
                     ->sourceInterface($settings->ontologyUpdateInterface)
                     ->sourceInterfaceVersion($settings->ontologyUpdateInterfaceVersion)
                     ->send();
     
      $this->assertEquals($ontologyUpdate->getStatus(), "200", "Debugging information: ".var_export($ontologyUpdate, TRUE));                                       
              
              
      $crudRead = new CrudReadQuery($settings->endpointUrl);
      
      $crudRead->dataset($settings->testOntologyUri)
               ->mime('resultset')
               ->uri('http://foo.org/test#opA')
               ->excludeLinksback()
               ->excludeReification()
               ->sourceInterface($settings->crudReadInterface)
               ->sourceInterfaceVersion($settings->crudReadInterfaceVersion)
               ->send();

      $this->assertEquals($crudRead->getStatus(), "400", "Debugging information: ".var_export($crudRead, TRUE));                                       
      $this->assertEquals($crudRead->getStatusMessage(), "Bad Request", "Debugging information: ".var_export($crudRead, TRUE));
      $this->assertEquals($crudRead->error->id, "WS-CRUD-READ-300", "Debugging information: ".var_export($crudRead, TRUE));                                                                          
                              
      utilities\deleteDataset();                              
                                   
      unset($ontologyUpdate);
      unset($settings);        
    }

    public function test_NamedIndividual_Update_DisabledAdvancedIndexation_TripleStore()
    {
      
      $settings = new Config();  
      
      utilities\deleteOntology();
      
      $this->assertTrue(utilities\createOntology(FALSE), "Can't create the ontology, check the /ontology/create/ endpoint first...");      
                               
      $createEntity = new CreateOrUpdateEntityFunction();
      
      $createEntity->disableAdvancedIndexation()
                   ->document(file_get_contents($settings->contentDir.'fooModified.owl'));
      
      $ontologyUpdate = new OntologyUpdateQuery($settings->endpointUrl);
      
      $ontologyUpdate->ontology($settings->testOntologyUri)
                     ->enableReasoner()
                     ->createOrUpdateEntity($createEntity)
                     ->sourceInterface($settings->ontologyUpdateInterface)
                     ->sourceInterfaceVersion($settings->ontologyUpdateInterfaceVersion)
                     ->send();
     
      $this->assertEquals($ontologyUpdate->getStatus(), "200", "Debugging information: ".var_export($ontologyUpdate, TRUE));                                       
              
      $crudRead = new CrudReadQuery($settings->endpointUrl);
      
      $crudRead->dataset($settings->testOntologyUri)
               ->mime('resultset')
               ->uri('http://foo.org/test#niE')
               ->excludeLinksback()
               ->excludeReification()
               ->sourceInterface($settings->crudReadInterface)
               ->sourceInterfaceVersion($settings->crudReadInterfaceVersion)
               ->send();

      $this->assertEquals($crudRead->getStatus(), "400", "Debugging information: ".var_export($crudRead, TRUE));                                       
      $this->assertEquals($crudRead->getStatusMessage(), "Bad Request", "Debugging information: ".var_export($crudRead, TRUE));
      $this->assertEquals($crudRead->error->id, "WS-CRUD-READ-300", "Debugging information: ".var_export($crudRead, TRUE));                                                                          
                              
      utilities\deleteDataset();                              
                                   
      unset($ontologyUpdate);
      unset($settings);        
    }
    
    public function test_Class_B_Update_DisabledAdvancedIndexation_Solr()
    {      
      $settings = new Config();  
      
      utilities\deleteOntology();
      
      $this->assertTrue(utilities\createOntology(FALSE), "Can't create the ontology, check the /ontology/create/ endpoint first...");      
                               
      $createEntity = new CreateOrUpdateEntityFunction();
      
      $createEntity->disableAdvancedIndexation()
                   ->document(file_get_contents($settings->contentDir.'fooModified.owl'));
      
      $ontologyUpdate = new OntologyUpdateQuery($settings->endpointUrl);
      
      $ontologyUpdate->ontology($settings->testOntologyUri)
                     ->enableReasoner()
                     ->createOrUpdateEntity($createEntity)
                     ->sourceInterface($settings->ontologyUpdateInterface)
                     ->sourceInterfaceVersion($settings->ontologyUpdateInterfaceVersion)
                     ->send();
     
      $this->assertEquals($ontologyUpdate->getStatus(), "200", "Debugging information: ".var_export($ontologyUpdate, TRUE));                                       
       
      $search = new SearchQuery($settings->endpointUrl);
      
      $search->attributeValuesFilters('uri', 'http://foo.org/test#B')
             ->excludeAggregates()
             ->excludeScores()
             ->excludeSpellcheck()
             ->mime('resultset')
             ->datasetFilter($settings->testOntologyUri)
             ->send();
                                                          
      $this->assertEquals($search->getStatus(), "200", "Debugging information: ".var_export($search, TRUE));                                       

      $this->assertTrue(count($search->getResultset()->getSubjects()) == 0);              
                              
      utilities\deleteDataset();                              
                                   
      unset($ontologyUpdate);
      unset($settings);        
    }  
    
          
    public function test_Class_C_Update_DisabledAdvancedIndexation_Solr()
    {
      
      $settings = new Config();  
      
      utilities\deleteOntology();
      
      $this->assertTrue(utilities\createOntology(FALSE), "Can't create the ontology, check the /ontology/create/ endpoint first...");      
                               
      $createEntity = new CreateOrUpdateEntityFunction();
      
      $createEntity->disableAdvancedIndexation()
                   ->document(file_get_contents($settings->contentDir.'fooModified.owl'));
      
      $ontologyUpdate = new OntologyUpdateQuery($settings->endpointUrl);
      
      $ontologyUpdate->ontology($settings->testOntologyUri)
                     ->enableReasoner()
                     ->createOrUpdateEntity($createEntity)
                     ->sourceInterface($settings->ontologyUpdateInterface)
                     ->sourceInterfaceVersion($settings->ontologyUpdateInterfaceVersion)
                     ->send();
     
      $this->assertEquals($ontologyUpdate->getStatus(), "200", "Debugging information: ".var_export($ontologyUpdate, TRUE));                                       
              
      $search = new SearchQuery($settings->endpointUrl);
      
      $search->attributeValuesFilters('uri', 'http://foo.org/test#B')
             ->excludeAggregates()
             ->excludeScores()
             ->excludeSpellcheck()
             ->mime('resultset')
             ->datasetFilter($settings->testOntologyUri)
             ->send();
                                                          
      $this->assertEquals($search->getStatus(), "200", "Debugging information: ".var_export($search, TRUE));                                       

      $this->assertTrue(count($search->getResultset()->getSubjects()) == 0);              
                              
      utilities\deleteDataset();                              
                                   
      unset($ontologyUpdate);
      unset($settings);        
    }
    
    public function test_AnnotationProperty_Update_DisabledAdvancedIndexation_Solr()
    {
      
      $settings = new Config();  
      
      utilities\deleteOntology();
      
      $this->assertTrue(utilities\createOntology(FALSE), "Can't create the ontology, check the /ontology/create/ endpoint first...");      
                               
      $createEntity = new CreateOrUpdateEntityFunction();
      
      $createEntity->disableAdvancedIndexation()
                   ->document(file_get_contents($settings->contentDir.'fooModified.owl'));
      
      $ontologyUpdate = new OntologyUpdateQuery($settings->endpointUrl);
      
      $ontologyUpdate->ontology($settings->testOntologyUri)
                     ->enableReasoner()
                     ->createOrUpdateEntity($createEntity)
                     ->sourceInterface($settings->ontologyUpdateInterface)
                     ->sourceInterfaceVersion($settings->ontologyUpdateInterfaceVersion)
                     ->send();
     
      $this->assertEquals($ontologyUpdate->getStatus(), "200", "Debugging information: ".var_export($ontologyUpdate, TRUE));                                       
              
      $search = new SearchQuery($settings->endpointUrl);
      
      $search->attributeValuesFilters('uri', 'http://foo.org/test#B')
             ->excludeAggregates()
             ->excludeScores()
             ->excludeSpellcheck()
             ->mime('resultset')
             ->datasetFilter($settings->testOntologyUri)
             ->send();
                                                          
      $this->assertEquals($search->getStatus(), "200", "Debugging information: ".var_export($search, TRUE));                                       

      $this->assertTrue(count($search->getResultset()->getSubjects()) == 0);              

      utilities\deleteDataset();                              
                                   
      unset($ontologyUpdate);
      unset($settings);        
    }        

    public function test_DatatypeProperty_Update_DisabledAdvancedIndexation_Solr()
    {
      
      $settings = new Config();  
      
      utilities\deleteOntology();
      
      $this->assertTrue(utilities\createOntology(FALSE), "Can't create the ontology, check the /ontology/create/ endpoint first...");      
                               
      $createEntity = new CreateOrUpdateEntityFunction();
      
      $createEntity->disableAdvancedIndexation()
                   ->document(file_get_contents($settings->contentDir.'fooModified.owl'));
      
      $ontologyUpdate = new OntologyUpdateQuery($settings->endpointUrl);
      
      $ontologyUpdate->ontology($settings->testOntologyUri)
                     ->enableReasoner()
                     ->createOrUpdateEntity($createEntity)
                     ->sourceInterface($settings->ontologyUpdateInterface)
                     ->sourceInterfaceVersion($settings->ontologyUpdateInterfaceVersion)
                     ->send();
     
      $this->assertEquals($ontologyUpdate->getStatus(), "200", "Debugging information: ".var_export($ontologyUpdate, TRUE));                                       
              
      $search = new SearchQuery($settings->endpointUrl);
      
      $search->attributeValuesFilters('uri', 'http://foo.org/test#B')
             ->excludeAggregates()
             ->excludeScores()
             ->excludeSpellcheck()
             ->mime('resultset')
             ->datasetFilter($settings->testOntologyUri)
             ->send();
                                                          
      $this->assertEquals($search->getStatus(), "200", "Debugging information: ".var_export($search, TRUE));                                       

      $this->assertTrue(count($search->getResultset()->getSubjects()) == 0);              

      utilities\deleteDataset();                              
                                   
      unset($ontologyUpdate);
      unset($settings);        
    }        

    public function test_ObjectProperty_Update_DisabledAdvancedIndexation_Solr()
    {
      
      $settings = new Config();  
      
      utilities\deleteOntology();
      
      $this->assertTrue(utilities\createOntology(FALSE), "Can't create the ontology, check the /ontology/create/ endpoint first...");      
                               
      $createEntity = new CreateOrUpdateEntityFunction();
      
      $createEntity->disableAdvancedIndexation()
                   ->document(file_get_contents($settings->contentDir.'fooModified.owl'));
      
      $ontologyUpdate = new OntologyUpdateQuery($settings->endpointUrl);
      
      $ontologyUpdate->ontology($settings->testOntologyUri)
                     ->enableReasoner()
                     ->createOrUpdateEntity($createEntity)
                     ->sourceInterface($settings->ontologyUpdateInterface)
                     ->sourceInterfaceVersion($settings->ontologyUpdateInterfaceVersion)
                     ->send();
     
      $this->assertEquals($ontologyUpdate->getStatus(), "200", "Debugging information: ".var_export($ontologyUpdate, TRUE));                                       
              
      $search = new SearchQuery($settings->endpointUrl);
      
      $search->attributeValuesFilters('uri', 'http://foo.org/test#B')
             ->excludeAggregates()
             ->excludeScores()
             ->excludeSpellcheck()
             ->mime('resultset')
             ->datasetFilter($settings->testOntologyUri)
             ->send();
                                                          
      $this->assertEquals($search->getStatus(), "200", "Debugging information: ".var_export($search, TRUE));                                       

      $this->assertTrue(count($search->getResultset()->getSubjects()) == 0);              

      utilities\deleteDataset();                              
                                   
      unset($ontologyUpdate);
      unset($settings);        
    }

    public function test_NamedIndividual_Update_DisabledAdvancedIndexation_Solr()
    {
      
      $settings = new Config();  
      
      utilities\deleteOntology();
      
      $this->assertTrue(utilities\createOntology(FALSE), "Can't create the ontology, check the /ontology/create/ endpoint first...");      
                               
      $createEntity = new CreateOrUpdateEntityFunction();
      
      $createEntity->disableAdvancedIndexation()
                   ->document(file_get_contents($settings->contentDir.'fooModified.owl'));
      
      $ontologyUpdate = new OntologyUpdateQuery($settings->endpointUrl);
      
      $ontologyUpdate->ontology($settings->testOntologyUri)
                     ->enableReasoner()
                     ->createOrUpdateEntity($createEntity)
                     ->sourceInterface($settings->ontologyUpdateInterface)
                     ->sourceInterfaceVersion($settings->ontologyUpdateInterfaceVersion)
                     ->send();
     
      $this->assertEquals($ontologyUpdate->getStatus(), "200", "Debugging information: ".var_export($ontologyUpdate, TRUE));                                       
              
      $search = new SearchQuery($settings->endpointUrl);
      
      $search->attributeValuesFilters('uri', 'http://foo.org/test#B')
             ->excludeAggregates()
             ->excludeScores()
             ->excludeSpellcheck()
             ->mime('resultset')
             ->datasetFilter($settings->testOntologyUri)
             ->send();
                                                          
      $this->assertEquals($search->getStatus(), "200", "Debugging information: ".var_export($search, TRUE));                                       

      $this->assertTrue(count($search->getResultset()->getSubjects()) == 0);              

      utilities\deleteDataset();                              
                                   
      unset($ontologyUpdate);
      unset($settings);        
    }    
  }

  
?>