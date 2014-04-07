<?php

  namespace StructuredDynamics\osf\tests\ws\ontology\read;
  
  use StructuredDynamics\osf\framework\WebServiceQuerier;
  use StructuredDynamics\osf\php\api\ws\ontology\read\OntologyReadQuery;  
  use StructuredDynamics\osf\php\api\ws\ontology\read\GetClassesFunction;
  use StructuredDynamics\osf\php\api\ws\ontology\read\GetClassFunction;
  use StructuredDynamics\osf\php\api\ws\ontology\read\GetDisjointClassesFunction;
  use StructuredDynamics\osf\php\api\ws\ontology\read\GetDisjointPropertiesFunction;
  use StructuredDynamics\osf\php\api\ws\ontology\read\GetEquivalentClassesFunction;
  use StructuredDynamics\osf\php\api\ws\ontology\read\GetEquivalentPropertiesFunction;
  use StructuredDynamics\osf\php\api\ws\ontology\read\GetLoadedOntologiesFunction;
  use StructuredDynamics\osf\php\api\ws\ontology\read\GetNamedIndividualFunction;
  use StructuredDynamics\osf\php\api\ws\ontology\read\GetNamedIndividualsFunction;
  use StructuredDynamics\osf\php\api\ws\ontology\read\GetPropertiesFunction;
  use StructuredDynamics\osf\php\api\ws\ontology\read\GetPropertyFunction;
  use StructuredDynamics\osf\php\api\ws\ontology\read\GetSubClassesFunction;
  use StructuredDynamics\osf\php\api\ws\ontology\read\GetSubPropertiesFunction;
  use StructuredDynamics\osf\php\api\ws\ontology\read\GetSuperClassesFunction;
  use StructuredDynamics\osf\php\api\ws\ontology\read\GetSuperPropertiesFunction;
  use StructuredDynamics\osf\tests\Config;
  use \StructuredDynamics\osf\framework\Resultset;
  use \StructuredDynamics\osf\framework\Namespaces;
  use \StructuredDynamics\osf\framework\Subject;
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
  
  // Database connectivity procedures
  include_once($settings->osfInstanceFolder . "framework/ProcessorXML.php");
  include_once($settings->osfInstanceFolder . "framework/arc2/ARC2.php");
  
  class OntologyReadTest extends \PHPUnit_Framework_TestCase {
    
    static private $outputs = array();

    static public function setUpBeforeClass() 
    {    
      utilities\createOntology();      
    }

    static public function tearDownAfterClass()    
    {
      utilities\deleteOntology();      
    }  
    
    public function testWrongEndpointUrl() {
      
      $settings = new Config();          
      
      $wsq = new WebServiceQuerier($settings->endpointUrl . "ontology/read/" . "wrong", 
                                   "post", 
                                   "text/xml",
                                   "ontology=" . urlencode($settings->testOntologyUri) .
                                   "&function=" .
                                   "&parameters=" .
                                   "&reasoner=",
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
      
      $wsq = new WebServiceQuerier($settings->endpointUrl . "ontology/read/", 
                                   "get", 
                                   "text/xml",
                                   "ontology=" . urlencode($settings->testOntologyUri) .
                                   "&function=" .
                                   "&parameters=" .
                                   "&reasoner=",
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
      
      $ontologyRead = new OntologyReadQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
      
      $getClass = new GetClassFunction();
      
      $getClass->uri('http://foo.org/test#B');
      
      $ontologyRead->enableReasoner()
                   ->getClass($getClass)
                   ->ontology($settings->testOntologyUri)
                   ->sourceInterface($settings->ontologyReadInterface)
                   ->sourceInterfaceVersion($settings->ontologyReadInterfaceVersion)
                   ->send();
      
      $this->assertEquals($ontologyRead->getStatus(), "200", "Debugging information: ".var_export($ontologyRead, TRUE));                                       
      
      unset($ontologyRead);
      unset($settings);   
    }
    
    
    public function testInvalidInterfaceVersion() {
      
      $settings = new Config();  
      
      $ontologyRead = new OntologyReadQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
      
      $getClass = new GetClassFunction();
      
      $getClass->uri('http://foo.org/test#B');
      
      $ontologyRead->enableReasoner()
                   ->getClass($getClass)
                   ->ontology($settings->testOntologyUri)
                   ->sourceInterface($settings->ontologyReadInterface)
                   ->sourceInterfaceVersion('667.3')
                   ->send();
      
      $this->assertEquals($ontologyRead->getStatus(), "400", "Debugging information: ".var_export($ontologyRead, TRUE));                                       
      $this->assertEquals($ontologyRead->getStatusMessage(), "Bad Request", "Debugging information: ".var_export($ontologyRead, TRUE));
      $this->assertEquals($ontologyRead->error->id, "WS-ONTOLOGY-READ-302", "Debugging information: ".var_export($ontologyRead, TRUE));                                                                          
      
      unset($ontologyRead);
      unset($settings);   
    }    
    
    public function testInterfaceExists() {
      $settings = new Config();  
      
      $ontologyRead = new OntologyReadQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
      
      $getClass = new GetClassFunction();
      
      $getClass->uri('http://foo.org/test#B');
      
      $ontologyRead->enableReasoner()
                   ->getClass($getClass)
                   ->ontology($settings->testOntologyUri)
                   ->sourceInterface($settings->ontologyReadInterface)
                   ->sourceInterfaceVersion($settings->ontologyReadInterfaceVersion)
                   ->send();
      
      $this->assertEquals($ontologyRead->getStatus(), "200", "Debugging information: ".var_export($ontologyRead, TRUE));                                       
      
      unset($ontologyRead);
      unset($settings);
    }  

    public function testInterfaceNotExisting() {
      
      $settings = new Config();  
      
      $ontologyRead = new OntologyReadQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
      
      $getClass = new GetClassFunction();
      
      $getClass->uri('http://foo.org/test#B');
      
      $ontologyRead->enableReasoner()
                   ->getClass($getClass)
                   ->ontology($settings->testOntologyUri)
                   ->sourceInterface('unexisting-interface')
                   ->sourceInterfaceVersion($settings->ontologyReadInterfaceVersion)
                   ->send();
      
      $this->assertEquals($ontologyRead->getStatus(), "400", "Debugging information: ".var_export($ontologyRead, TRUE));                                       
      $this->assertEquals($ontologyRead->getStatusMessage(), "Bad Request", "Debugging information: ".var_export($ontologyRead, TRUE));
      $this->assertEquals($ontologyRead->error->id, "WS-ONTOLOGY-READ-301", "Debugging information: ".var_export($ontologyRead, TRUE));                                                                          
      
      unset($ontologyRead);
      unset($settings);   
    }     
    
    public function test_getClass_RDFXML() {
      
      $settings = new Config();  
      
      $ontologyRead = new OntologyReadQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
      
      $getClass = new GetClassFunction();
      
      $getClass->uri('http://foo.org/test#B');
      
      $ontologyRead->enableReasoner()
                   ->mime('application/rdf+xml')
                   ->getClass($getClass)
                   ->ontology($settings->testOntologyUri)
                   ->sourceInterface($settings->ontologyReadInterface)
                   ->sourceInterfaceVersion($settings->ontologyReadInterfaceVersion)
                   ->send();
      
      $this->assertEquals($ontologyRead->getStatus(), "200", "Debugging information: ".var_export($ontologyRead, TRUE));                                       

      utilities\validateParameterApplicationRdfXml($this, $ontologyRead);
      
      $this->assertTrue(utilities\compareRdf($ontologyRead->getResultset(), file_get_contents($settings->contentDir.'validation/ontology_read_class_b.xml'), TRUE));

      unset($ontologyRead);
      unset($settings);   
    }     
       
    public function test_getClass_RDFN3() {
      
      $settings = new Config();  
      
      $ontologyRead = new OntologyReadQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
      
      $getClass = new GetClassFunction();
      
      $getClass->uri('http://foo.org/test#B');
      
      $ontologyRead->enableReasoner()
                   ->mime('application/rdf+n3')
                   ->getClass($getClass)
                   ->ontology($settings->testOntologyUri)
                   ->sourceInterface($settings->ontologyReadInterface)
                   ->sourceInterfaceVersion($settings->ontologyReadInterfaceVersion)
                   ->send();
      
      $this->assertEquals($ontologyRead->getStatus(), "200", "Debugging information: ".var_export($ontologyRead, TRUE));                                       
      
      utilities\validateParameterApplicationRdfN3($this, $ontologyRead);
      
      $this->assertTrue(utilities\compareRdf($ontologyRead->getResultset(), file_get_contents($settings->contentDir.'validation/ontology_read_class_b.xml'), TRUE));
      
      unset($ontologyRead);
      unset($settings);   
    }            
       
    public function test_getClass_structJSON() {
      
      $settings = new Config();  
      
      $ontologyRead = new OntologyReadQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
      
      $getClass = new GetClassFunction();
      
      $getClass->uri('http://foo.org/test#B');
      
      $ontologyRead->enableReasoner()
                   ->mime('application/json')
                   ->getClass($getClass)
                   ->ontology($settings->testOntologyUri)
                   ->sourceInterface($settings->ontologyReadInterface)
                   ->sourceInterfaceVersion($settings->ontologyReadInterfaceVersion)
                   ->send();
      
      $this->assertEquals($ontologyRead->getStatus(), "200", "Debugging information: ".var_export($ontologyRead, TRUE));                                       
      
      utilities\validateParameterApplicationJson($this, $ontologyRead);
            
      $this->assertTrue(utilities\compareStructJSON($ontologyRead->getResultset(), file_get_contents($settings->contentDir.'validation/ontology_read_class_b.json')));
            
      unset($ontologyRead);
      unset($settings);   
    }     
  
    public function test_getClass_structXML() {
      
      $settings = new Config();  
      
      $ontologyRead = new OntologyReadQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
      
      $getClass = new GetClassFunction();
      
      $getClass->uri('http://foo.org/test#B');
      
      $ontologyRead->enableReasoner()
                   ->mime('text/xml')
                   ->getClass($getClass)
                   ->ontology($settings->testOntologyUri)
                   ->sourceInterface($settings->ontologyReadInterface)
                   ->sourceInterfaceVersion($settings->ontologyReadInterfaceVersion)
                   ->send();
      
      $this->assertEquals($ontologyRead->getStatus(), "200", "Debugging information: ".var_export($ontologyRead, TRUE));                                       
      
      utilities\validateParameterTextXml($this, $ontologyRead);

      $resultset = new Resultset($settings->endpointUrl);
    
      // To test the structXML serialization, we import it into a Resultset() object and we export as RDF+XML
      // then we compare it to the expected RDF+XML resultset.
      $resultset->importStructXMLResultset($ontologyRead->getResultset());
      
      $actual = str_replace('<iron:prefLabel>B</iron:prefLabel>', '', $resultset->getResultsetRDFXML());

      $this->assertTrue(utilities\compareRdf($actual, file_get_contents($settings->contentDir.'validation/ontology_read_class_b.xml'), TRUE));

      unset($ontologyRead);
      unset($settings);   
    }          
           
    public function test_getClass_Resultset() {
      
      $settings = new Config();  
      
      $ontologyRead = new OntologyReadQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
      
      $getClass = new GetClassFunction();
      
      $getClass->uri('http://foo.org/test#B');
      
      $ontologyRead->enableReasoner()
                   ->mime('resultset')
                   ->getClass($getClass)
                   ->ontology($settings->testOntologyUri)
                   ->sourceInterface($settings->ontologyReadInterface)
                   ->sourceInterfaceVersion($settings->ontologyReadInterfaceVersion)
                   ->send();
      
      $this->assertEquals($ontologyRead->getStatus(), "200", "Debugging information: ".var_export($ontologyRead, TRUE));                                       
      
      $actual = str_replace('<iron:prefLabel>B</iron:prefLabel>', '', $ontologyRead->getResultset()->getResultsetRDFXML());      
      
      // To test the Resultset we export it as RDF+XML
      // then we compare it to the expected RDF+XML resultset.
      $this->assertTrue(utilities\compareRdf($actual, file_get_contents($settings->contentDir.'validation/ontology_read_class_b.xml'), TRUE));
  
      unset($ontologyRead);
      unset($settings);   
    }         
    
    public function test_getClasses_Mode_URI_Limit_1_Offset_0_Reasoner_Enabled() {
      
      $settings = new Config();  
      
      $ontologyRead = new OntologyReadQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
      
      $getClasses = new GetClassesFunction();
      
      $getClasses->getClassesUris()                 
                 ->limit(1)
                 ->offset(0);
      
      $ontologyRead->enableReasoner()
                   ->mime('resultset')
                   ->getClasses($getClasses)
                   ->ontology($settings->testOntologyUri)
                   ->sourceInterface($settings->ontologyReadInterface)
                   ->sourceInterfaceVersion($settings->ontologyReadInterfaceVersion)
                   ->send();
      
      $this->assertEquals($ontologyRead->getStatus(), "200", "Debugging information: ".var_export($ontologyRead, TRUE));                                       

      $resultset = $ontologyRead->getResultset()->getResultset();

      $this->assertTrue(count($resultset['unspecified']) == 1);
      $this->assertTrue(isset($resultset['unspecified']['http://foo.org/test#A']));
      $this->assertTrue(count($resultset['unspecified']['http://foo.org/test#A']) == 1);

      unset($ontologyRead);
      unset($settings);   
    }     
    
    public function test_getClasses_Mode_URI_Limit_2_Offset_1_Reasoner_Enabled() {
      
      $settings = new Config();  
      
      $ontologyRead = new OntologyReadQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
      
      $getClasses = new GetClassesFunction();
      
      $getClasses->getClassesUris()                 
                 ->limit(2)
                 ->offset(1);
      
      $ontologyRead->enableReasoner()
                   ->mime('resultset')
                   ->getClasses($getClasses)
                   ->ontology($settings->testOntologyUri)
                   ->sourceInterface($settings->ontologyReadInterface)
                   ->sourceInterfaceVersion($settings->ontologyReadInterfaceVersion)
                   ->send();
      
      $this->assertEquals($ontologyRead->getStatus(), "200", "Debugging information: ".var_export($ontologyRead, TRUE));                                       

      $resultset = $ontologyRead->getResultset()->getResultset();

      $this->assertTrue(count($resultset['unspecified']) == 2);
      $this->assertTrue(isset($resultset['unspecified']['http://foo.org/test#B']));
      $this->assertTrue(count($resultset['unspecified']['http://foo.org/test#B']) == 1);
      $this->assertTrue(isset($resultset['unspecified']['http://foo.org/test#G']));
      $this->assertTrue(count($resultset['unspecified']['http://foo.org/test#G']) == 1);

      unset($ontologyRead);
      unset($settings);   
    }  
    
    public function test_getClasses_Mode_Description_Limit_1_Offset_0_Reasoner_Enabled() {
      
      $settings = new Config();  
      
      $ontologyRead = new OntologyReadQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
      
      $getClasses = new GetClassesFunction();
      
      $getClasses->getClassesDescriptions()                 
                 ->limit(1)
                 ->offset(0);
      
      $ontologyRead->enableReasoner()
                   ->mime('resultset')
                   ->getClasses($getClasses)
                   ->ontology($settings->testOntologyUri)
                   ->sourceInterface($settings->ontologyReadInterface)
                   ->sourceInterfaceVersion($settings->ontologyReadInterfaceVersion)
                   ->send();
      
      $this->assertEquals($ontologyRead->getStatus(), "200", "Debugging information: ".var_export($ontologyRead, TRUE));                                       

      $resultset = $ontologyRead->getResultset()->getResultset();

      $this->assertTrue(count($resultset['unspecified']) == 1);
      $this->assertTrue(isset($resultset['unspecified']['http://foo.org/test#A']));
      $this->assertTrue(count($resultset['unspecified']['http://foo.org/test#A']) == 5);

      unset($ontologyRead);
      unset($settings);   
    }     
    
    public function test_getClasses_Mode_Description_Limit_2_Offset_1_Reasoner_Enabled() {
      
      $settings = new Config();  
      
      $ontologyRead = new OntologyReadQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
      
      $getClasses = new GetClassesFunction();
      
      $getClasses->getClassesDescriptions()                 
                 ->limit(2)
                 ->offset(1);
      
      $ontologyRead->enableReasoner()
                   ->mime('resultset')
                   ->getClasses($getClasses)
                   ->ontology($settings->testOntologyUri)
                   ->sourceInterface($settings->ontologyReadInterface)
                   ->sourceInterfaceVersion($settings->ontologyReadInterfaceVersion)
                   ->send();
      
      $this->assertEquals($ontologyRead->getStatus(), "200", "Debugging information: ".var_export($ontologyRead, TRUE));                                       

      $resultset = $ontologyRead->getResultset()->getResultset();

      $this->assertTrue(count($resultset['unspecified']) == 2);
      $this->assertTrue(isset($resultset['unspecified']['http://foo.org/test#B']));
      $this->assertTrue(count($resultset['unspecified']['http://foo.org/test#B']) == 5);
      $this->assertTrue(isset($resultset['unspecified']['http://foo.org/test#G']));
      $this->assertTrue(count($resultset['unspecified']['http://foo.org/test#G']) == 5);

      unset($ontologyRead);
      unset($settings);   
    } 

    public function test_getSubClasses_Mode_URI_Direct_Reasoner_Enabled() {
      
      $settings = new Config();  
      
      $ontologyRead = new OntologyReadQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
      
      $getSubClasses = new GetSubClassesFunction();
      
      $getSubClasses->uri('http://foo.org/test#A')
                    ->getClassesUris()
                    ->directSubClasses();
      
      $ontologyRead->enableReasoner()
                   ->mime('resultset')
                   ->getSubClasses($getSubClasses)
                   ->ontology($settings->testOntologyUri)
                   ->sourceInterface($settings->ontologyReadInterface)
                   ->sourceInterfaceVersion($settings->ontologyReadInterfaceVersion)
                   ->send();
      
      $this->assertEquals($ontologyRead->getStatus(), "200", "Debugging information: ".var_export($ontologyRead, TRUE));                                       

      $resultset = $ontologyRead->getResultset()->getResultset();

      $this->assertTrue(count($resultset['unspecified']) == 1);
      $this->assertTrue(isset($resultset['unspecified']['http://foo.org/test#B']));
      $this->assertTrue(count($resultset['unspecified']['http://foo.org/test#B']) == 1);

      unset($ontologyRead);
      unset($settings);   
    }
    
    public function test_getSubClasses_Mode_Description_Direct_Reasoner_Enabled() {
      
      $settings = new Config();  
      
      $ontologyRead = new OntologyReadQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
      
      $getSubClasses = new GetSubClassesFunction();
      
      $getSubClasses->uri('http://foo.org/test#A')
                    ->getClassesDescriptions()
                    ->directSubClasses();
      
      $ontologyRead->enableReasoner()
                   ->mime('resultset')
                   ->getSubClasses($getSubClasses)
                   ->ontology($settings->testOntologyUri)
                   ->sourceInterface($settings->ontologyReadInterface)
                   ->sourceInterfaceVersion($settings->ontologyReadInterfaceVersion)
                   ->send();
      
      $this->assertEquals($ontologyRead->getStatus(), "200", "Debugging information: ".var_export($ontologyRead, TRUE));                                       

      $resultset = $ontologyRead->getResultset()->getResultset();

      $this->assertTrue(count($resultset['unspecified']) == 1);
      $this->assertTrue(isset($resultset['unspecified']['http://foo.org/test#B']));
      $this->assertTrue(count($resultset['unspecified']['http://foo.org/test#B']) == 5);

      unset($ontologyRead);
      unset($settings);   
    }    

    public function test_getSubClasses_Mode_Hierarchy_Direct_Reasoner_Enabled() {
      
      $settings = new Config();  
      
      $ontologyRead = new OntologyReadQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
      
      $getSubClasses = new GetSubClassesFunction();
      
      $getSubClasses->uri('http://foo.org/test#A')
                    ->getHierarchy()
                    ->directSubClasses();
      
      $ontologyRead->enableReasoner()
                   ->mime('resultset')
                   ->getSubClasses($getSubClasses)
                   ->ontology($settings->testOntologyUri)
                   ->sourceInterface($settings->ontologyReadInterface)
                   ->sourceInterfaceVersion($settings->ontologyReadInterfaceVersion)
                   ->send();
      
      $this->assertEquals($ontologyRead->getStatus(), "200", "Debugging information: ".var_export($ontologyRead, TRUE));                                       

      $resultset = $ontologyRead->getResultset()->getResultset();

      $this->assertTrue(count($resultset['unspecified']) == 1);
      $this->assertTrue(isset($resultset['unspecified']['http://foo.org/test#B']));
      $this->assertTrue($resultset['unspecified']['http://foo.org/test#B']['http://purl.org/ontology/sco#hasSubClasses'][0]['value'] == 'true');
      $this->assertTrue(count($resultset['unspecified']['http://foo.org/test#B']) == 4);

      unset($ontologyRead);
      unset($settings);   
    }    

    public function test_getSubClasses_Mode_URI_All_Reasoner_Enabled() {
      
      $settings = new Config();  
      
      $ontologyRead = new OntologyReadQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
      
      $getSubClasses = new GetSubClassesFunction();
      
      $getSubClasses->uri('http://foo.org/test#A')
                    ->getClassesUris()
                    ->allSubClasses();
                    
      $ontologyRead->enableReasoner()
                   ->mime('resultset')
                   ->getSubClasses($getSubClasses)
                   ->ontology($settings->testOntologyUri)
                   ->sourceInterface($settings->ontologyReadInterface)
                   ->sourceInterfaceVersion($settings->ontologyReadInterfaceVersion)
                   ->send();
      
      $this->assertEquals($ontologyRead->getStatus(), "200", "Debugging information: ".var_export($ontologyRead, TRUE));                                       

      $resultset = $ontologyRead->getResultset()->getResultset();

      $this->assertTrue(count($resultset['unspecified']) == 3);
      $this->assertTrue(isset($resultset['unspecified']['http://foo.org/test#B']));
      $this->assertTrue(count($resultset['unspecified']['http://foo.org/test#B']) == 1);
      $this->assertTrue(isset($resultset['unspecified']['http://foo.org/test#C']));
      $this->assertTrue(count($resultset['unspecified']['http://foo.org/test#C']) == 1);
      $this->assertTrue(isset($resultset['unspecified']['http://www.w3.org/2002/07/owl#Nothing']));
      $this->assertTrue(count($resultset['unspecified']['http://www.w3.org/2002/07/owl#Nothing']) == 1);

      unset($ontologyRead);
      unset($settings);   
    }
    
    public function test_getSubClasses_Mode_Description_All_Reasoner_Enabled() {
      
      $settings = new Config();  
      
      $ontologyRead = new OntologyReadQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
      
      $getSubClasses = new GetSubClassesFunction();
      
      $getSubClasses->uri('http://foo.org/test#A')
                    ->getClassesDescriptions()
                    ->allSubClasses();
      
      $ontologyRead->enableReasoner()
                   ->mime('resultset')
                   ->getSubClasses($getSubClasses)
                   ->ontology($settings->testOntologyUri)
                   ->sourceInterface($settings->ontologyReadInterface)
                   ->sourceInterfaceVersion($settings->ontologyReadInterfaceVersion)
                   ->send();
      
      $this->assertEquals($ontologyRead->getStatus(), "200", "Debugging information: ".var_export($ontologyRead, TRUE));                                       

      $resultset = $ontologyRead->getResultset()->getResultset();

      $this->assertTrue(count($resultset['unspecified']) == 3);
      $this->assertTrue(isset($resultset['unspecified']['http://foo.org/test#B']));
      $this->assertTrue(count($resultset['unspecified']['http://foo.org/test#B']) == 5);
      $this->assertTrue(isset($resultset['unspecified']['http://foo.org/test#C']));
      $this->assertTrue(count($resultset['unspecified']['http://foo.org/test#C']) == 4);
      $this->assertTrue(isset($resultset['unspecified']['http://www.w3.org/2002/07/owl#Nothing']));
      $this->assertTrue(count($resultset['unspecified']['http://www.w3.org/2002/07/owl#Nothing']) == 1);


      unset($ontologyRead);
      unset($settings);   
    }    

    public function test_getSubClasses_Mode_Hierarchy_All_Reasoner_Enabled() {
      
      $settings = new Config();  
      
      $ontologyRead = new OntologyReadQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
      
      $getSubClasses = new GetSubClassesFunction();
      
      $getSubClasses->uri('http://foo.org/test#A')
                    ->getHierarchy()
                    ->allSubClasses();
      
      $ontologyRead->enableReasoner()
                   ->mime('resultset')
                   ->getSubClasses($getSubClasses)
                   ->ontology($settings->testOntologyUri)
                   ->sourceInterface($settings->ontologyReadInterface)
                   ->sourceInterfaceVersion($settings->ontologyReadInterfaceVersion)
                   ->send();
      
      $this->assertEquals($ontologyRead->getStatus(), "200", "Debugging information: ".var_export($ontologyRead, TRUE));                                       

      $resultset = $ontologyRead->getResultset()->getResultset();

      $this->assertTrue(count($resultset['unspecified']) == 1);
      $this->assertTrue(isset($resultset['unspecified']['http://foo.org/test#B']));
      $this->assertTrue($resultset['unspecified']['http://foo.org/test#B']['http://purl.org/ontology/sco#hasSubClasses'][0]['value'] == 'true');
      $this->assertTrue(count($resultset['unspecified']['http://foo.org/test#B']) == 4);

      unset($ontologyRead);
      unset($settings);   
    } 
       
    public function test_getClasses_Mode_URI_Limit_1_Offset_0_Reasoner_Disabled() {
      
      $settings = new Config();  
      
      $ontologyRead = new OntologyReadQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
      
      $getClasses = new GetClassesFunction();
      
      $getClasses->getClassesUris()                 
                 ->limit(1)
                 ->offset(0);
      
      $ontologyRead->disableReasoner()
                   ->mime('resultset')
                   ->getClasses($getClasses)
                   ->ontology($settings->testOntologyUri)
                   ->sourceInterface($settings->ontologyReadInterface)
                   ->sourceInterfaceVersion($settings->ontologyReadInterfaceVersion)
                   ->send();
      
      $this->assertEquals($ontologyRead->getStatus(), "200", "Debugging information: ".var_export($ontologyRead, TRUE));                                       

      $resultset = $ontologyRead->getResultset()->getResultset();

      $this->assertTrue(count($resultset['unspecified']) == 1);
      $this->assertTrue(isset($resultset['unspecified']['http://foo.org/test#A']));
      $this->assertTrue(count($resultset['unspecified']['http://foo.org/test#A']) == 1);

      unset($ontologyRead);
      unset($settings);   
    }     
    
    public function test_getClasses_Mode_URI_Limit_2_Offset_1_Reasoner_Disabled() {
      
      $settings = new Config();  
      
      $ontologyRead = new OntologyReadQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
      
      $getClasses = new GetClassesFunction();
      
      $getClasses->getClassesUris()                 
                 ->limit(2)
                 ->offset(1);
      
      $ontologyRead->disableReasoner()
                   ->mime('resultset')
                   ->getClasses($getClasses)
                   ->ontology($settings->testOntologyUri)
                   ->sourceInterface($settings->ontologyReadInterface)
                   ->sourceInterfaceVersion($settings->ontologyReadInterfaceVersion)
                   ->send();
      
      $this->assertEquals($ontologyRead->getStatus(), "200", "Debugging information: ".var_export($ontologyRead, TRUE));                                       

      $resultset = $ontologyRead->getResultset()->getResultset();

      $this->assertTrue(count($resultset['unspecified']) == 2);
      $this->assertTrue(isset($resultset['unspecified']['http://foo.org/test#B']));
      $this->assertTrue(count($resultset['unspecified']['http://foo.org/test#B']) == 1);
      $this->assertTrue(isset($resultset['unspecified']['http://foo.org/test#G']));
      $this->assertTrue(count($resultset['unspecified']['http://foo.org/test#G']) == 1);

      unset($ontologyRead);
      unset($settings);   
    }  
    
    public function test_getClasses_Mode_Description_Limit_1_Offset_0_Reasoner_Disabled() {
      
      $settings = new Config();  
      
      $ontologyRead = new OntologyReadQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
      
      $getClasses = new GetClassesFunction();
      
      $getClasses->getClassesDescriptions()                 
                 ->limit(1)
                 ->offset(0);
      
      $ontologyRead->disableReasoner()
                   ->mime('resultset')
                   ->getClasses($getClasses)
                   ->ontology($settings->testOntologyUri)
                   ->sourceInterface($settings->ontologyReadInterface)
                   ->sourceInterfaceVersion($settings->ontologyReadInterfaceVersion)
                   ->send();
      
      $this->assertEquals($ontologyRead->getStatus(), "200", "Debugging information: ".var_export($ontologyRead, TRUE));                                       

      $resultset = $ontologyRead->getResultset()->getResultset();

      $this->assertTrue(count($resultset['unspecified']) == 1);
      $this->assertTrue(isset($resultset['unspecified']['http://foo.org/test#A']));
      $this->assertTrue(count($resultset['unspecified']['http://foo.org/test#A']) == 4);

      unset($ontologyRead);
      unset($settings);   
    }     
    
    public function test_getClasses_Mode_Description_Limit_2_Offset_1_Reasoner_Disabled() {
      
      $settings = new Config();  
      
      $ontologyRead = new OntologyReadQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
      
      $getClasses = new GetClassesFunction();
      
      $getClasses->getClassesDescriptions()                 
                 ->limit(2)
                 ->offset(1);
      
      $ontologyRead->disableReasoner()
                   ->mime('resultset')
                   ->getClasses($getClasses)
                   ->ontology($settings->testOntologyUri)
                   ->sourceInterface($settings->ontologyReadInterface)
                   ->sourceInterfaceVersion($settings->ontologyReadInterfaceVersion)
                   ->send();
      
      $this->assertEquals($ontologyRead->getStatus(), "200", "Debugging information: ".var_export($ontologyRead, TRUE));                                       

      $resultset = $ontologyRead->getResultset()->getResultset();

      $this->assertTrue(count($resultset['unspecified']) == 2);
      $this->assertTrue(isset($resultset['unspecified']['http://foo.org/test#B']));
      $this->assertTrue(count($resultset['unspecified']['http://foo.org/test#B']) == 5);
      $this->assertTrue(isset($resultset['unspecified']['http://foo.org/test#G']));
      $this->assertTrue(count($resultset['unspecified']['http://foo.org/test#G']) == 4);

      unset($ontologyRead);
      unset($settings);   
    } 

    public function test_getSubClasses_Mode_URI_Direct_Reasoner_Disabled() {
      
      $settings = new Config();  
      
      $ontologyRead = new OntologyReadQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
      
      $getSubClasses = new GetSubClassesFunction();
      
      $getSubClasses->uri('http://foo.org/test#A')
                    ->getClassesUris()
                    ->directSubClasses();
      
      $ontologyRead->disableReasoner()
                   ->mime('resultset')
                   ->getSubClasses($getSubClasses)
                   ->ontology($settings->testOntologyUri)
                   ->sourceInterface($settings->ontologyReadInterface)
                   ->sourceInterfaceVersion($settings->ontologyReadInterfaceVersion)
                   ->send();
      
      $this->assertEquals($ontologyRead->getStatus(), "200", "Debugging information: ".var_export($ontologyRead, TRUE));                                       

      $resultset = $ontologyRead->getResultset()->getResultset();

      $this->assertTrue(count($resultset['unspecified']) == 1);
      $this->assertTrue(isset($resultset['unspecified']['http://foo.org/test#B']));
      $this->assertTrue(count($resultset['unspecified']['http://foo.org/test#B']) == 1);

      unset($ontologyRead);
      unset($settings);   
    }
    
    public function test_getSubClasses_Mode_Description_Direct_Reasoner_Disabled() {
      
      $settings = new Config();  
      
      $ontologyRead = new OntologyReadQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
      
      $getSubClasses = new GetSubClassesFunction();
      
      $getSubClasses->uri('http://foo.org/test#A')
                    ->getClassesDescriptions()
                    ->directSubClasses();
      
      $ontologyRead->disableReasoner()
                   ->mime('resultset')
                   ->getSubClasses($getSubClasses)
                   ->ontology($settings->testOntologyUri)
                   ->sourceInterface($settings->ontologyReadInterface)
                   ->sourceInterfaceVersion($settings->ontologyReadInterfaceVersion)
                   ->send();
      
      $this->assertEquals($ontologyRead->getStatus(), "200", "Debugging information: ".var_export($ontologyRead, TRUE));                                       

      $resultset = $ontologyRead->getResultset()->getResultset();

      $this->assertTrue(count($resultset['unspecified']) == 1);
      $this->assertTrue(isset($resultset['unspecified']['http://foo.org/test#B']));
      $this->assertTrue(count($resultset['unspecified']['http://foo.org/test#B']) == 5);

      unset($ontologyRead);
      unset($settings);   
    }    

    public function test_getSubClasses_Mode_Hierarchy_Direct_Reasoner_Disabled() {
      
      $settings = new Config();  
      
      $ontologyRead = new OntologyReadQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
      
      $getSubClasses = new GetSubClassesFunction();
      
      $getSubClasses->uri('http://foo.org/test#A')
                    ->getHierarchy()
                    ->directSubClasses();
      
      $ontologyRead->disableReasoner()
                   ->mime('resultset')
                   ->getSubClasses($getSubClasses)
                   ->ontology($settings->testOntologyUri)
                   ->sourceInterface($settings->ontologyReadInterface)
                   ->sourceInterfaceVersion($settings->ontologyReadInterfaceVersion)
                   ->send();
      
      $this->assertEquals($ontologyRead->getStatus(), "200", "Debugging information: ".var_export($ontologyRead, TRUE));                                       

      $resultset = $ontologyRead->getResultset()->getResultset();

      $this->assertTrue(count($resultset['unspecified']) == 1);
      $this->assertTrue(isset($resultset['unspecified']['http://foo.org/test#B']));
      $this->assertTrue($resultset['unspecified']['http://foo.org/test#B']['http://purl.org/ontology/sco#hasSubClasses'][0]['value'] == 'true');
      $this->assertTrue(count($resultset['unspecified']['http://foo.org/test#B']) == 4);

      unset($ontologyRead);
      unset($settings);   
    }    
    
    public function test_getSubClasses_Mode_URI_All_Reasoner_Disabled() {
      
      $settings = new Config();  
      
      $ontologyRead = new OntologyReadQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
      
      $getSubClasses = new GetSubClassesFunction();
      
      $getSubClasses->uri('http://foo.org/test#A')
                    ->getClassesUris()
                    ->allSubClasses();
                    
      $ontologyRead->disableReasoner()
                   ->mime('resultset')
                   ->getSubClasses($getSubClasses)
                   ->ontology($settings->testOntologyUri)
                   ->sourceInterface($settings->ontologyReadInterface)
                   ->sourceInterfaceVersion($settings->ontologyReadInterfaceVersion)
                   ->send();
      
      $this->assertEquals($ontologyRead->getStatus(), "200", "Debugging information: ".var_export($ontologyRead, TRUE));                                       

      $resultset = $ontologyRead->getResultset()->getResultset();

      $this->assertTrue(count($resultset['unspecified']) == 1);
      $this->assertTrue(isset($resultset['unspecified']['http://foo.org/test#B']));
      $this->assertTrue(count($resultset['unspecified']['http://foo.org/test#B']) == 1);

      unset($ontologyRead);
      unset($settings);   
    }
    
    public function test_getSubClasses_Mode_Description_All_Reasoner_Disabled() {
      
      $settings = new Config();  
      
      $ontologyRead = new OntologyReadQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
      
      $getSubClasses = new GetSubClassesFunction();
      
      $getSubClasses->uri('http://foo.org/test#A')
                    ->getClassesDescriptions()
                    ->allSubClasses();
      
      $ontologyRead->disableReasoner()
                   ->mime('resultset')
                   ->getSubClasses($getSubClasses)
                   ->ontology($settings->testOntologyUri)
                   ->sourceInterface($settings->ontologyReadInterface)
                   ->sourceInterfaceVersion($settings->ontologyReadInterfaceVersion)
                   ->send();
      
      $this->assertEquals($ontologyRead->getStatus(), "200", "Debugging information: ".var_export($ontologyRead, TRUE));                                       

      $resultset = $ontologyRead->getResultset()->getResultset();

      $this->assertTrue(count($resultset['unspecified']) == 1);
      $this->assertTrue(isset($resultset['unspecified']['http://foo.org/test#B']));
      $this->assertTrue(count($resultset['unspecified']['http://foo.org/test#B']) == 5);

      unset($ontologyRead);
      unset($settings);   
    }    

    public function test_getSubClasses_Mode_Hierarchy_All_Reasoner_Disabled() {
      
      $settings = new Config();  
      
      $ontologyRead = new OntologyReadQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
      
      $getSubClasses = new GetSubClassesFunction();
      
      $getSubClasses->uri('http://foo.org/test#A')
                    ->getHierarchy()
                    ->allSubClasses();
      
      $ontologyRead->disableReasoner()
                   ->mime('resultset')
                   ->getSubClasses($getSubClasses)
                   ->ontology($settings->testOntologyUri)
                   ->sourceInterface($settings->ontologyReadInterface)
                   ->sourceInterfaceVersion($settings->ontologyReadInterfaceVersion)
                   ->send();
      
      $this->assertEquals($ontologyRead->getStatus(), "200", "Debugging information: ".var_export($ontologyRead, TRUE));                                       

      $resultset = $ontologyRead->getResultset()->getResultset();

      $this->assertTrue(count($resultset['unspecified']) == 1);
      $this->assertTrue(isset($resultset['unspecified']['http://foo.org/test#B']));
      $this->assertTrue($resultset['unspecified']['http://foo.org/test#B']['http://purl.org/ontology/sco#hasSubClasses'][0]['value'] == 'true');
      $this->assertTrue(count($resultset['unspecified']['http://foo.org/test#B']) == 4);

      unset($ontologyRead);
      unset($settings);   
    }        
    
    public function test_getSuperClasses_Mode_URI_Direct_Reasoner_Enabled() {
      
      $settings = new Config();  
      
      $ontologyRead = new OntologyReadQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
      
      $getSuperClasses = new GetSuperClassesFunction();
      
      $getSuperClasses->uri('http://foo.org/test#C')
                    ->getClassesUris()
                    ->directSuperClasses();
      
      $ontologyRead->enableReasoner()
                   ->mime('resultset')
                   ->getSuperClasses($getSuperClasses)
                   ->ontology($settings->testOntologyUri)
                   ->sourceInterface($settings->ontologyReadInterface)
                   ->sourceInterfaceVersion($settings->ontologyReadInterfaceVersion)
                   ->send();
      
      $this->assertEquals($ontologyRead->getStatus(), "200", "Debugging information: ".var_export($ontologyRead, TRUE));                                       

      $resultset = $ontologyRead->getResultset()->getResultset();

      $this->assertTrue(count($resultset['unspecified']) == 1);
      $this->assertTrue(isset($resultset['unspecified']['http://foo.org/test#B']));
      $this->assertTrue(count($resultset['unspecified']['http://foo.org/test#B']) == 1);

      unset($ontologyRead);
      unset($settings);   
    }
    
    public function test_getSuperClasses_Mode_Description_Direct_Reasoner_Enabled() {
      
      $settings = new Config();  
      
      $ontologyRead = new OntologyReadQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
      
      $getSuperClasses = new GetSuperClassesFunction();
      
      $getSuperClasses->uri('http://foo.org/test#C')
                    ->getClassesDescriptions()
                    ->directSuperClasses();
      
      $ontologyRead->enableReasoner()
                   ->mime('resultset')
                   ->getSuperClasses($getSuperClasses)
                   ->ontology($settings->testOntologyUri)
                   ->sourceInterface($settings->ontologyReadInterface)
                   ->sourceInterfaceVersion($settings->ontologyReadInterfaceVersion)
                   ->send();
      
      $this->assertEquals($ontologyRead->getStatus(), "200", "Debugging information: ".var_export($ontologyRead, TRUE));                                       

      $resultset = $ontologyRead->getResultset()->getResultset();

      $this->assertTrue(count($resultset['unspecified']) == 1);
      $this->assertTrue(isset($resultset['unspecified']['http://foo.org/test#B']));
      $this->assertTrue(count($resultset['unspecified']['http://foo.org/test#B']) == 5);

      unset($ontologyRead);
      unset($settings);   
    }    

    public function test_getSuperClasses_Mode_URI_All_Reasoner_Enabled() {
      
      $settings = new Config();  
      
      $ontologyRead = new OntologyReadQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
      
      $getSuperClasses = new GetSuperClassesFunction();
      
      $getSuperClasses->uri('http://foo.org/test#C')
                    ->getClassesUris()
                    ->allSuperClasses();
                    
      $ontologyRead->enableReasoner()
                   ->mime('resultset')
                   ->getSuperClasses($getSuperClasses)
                   ->ontology($settings->testOntologyUri)
                   ->sourceInterface($settings->ontologyReadInterface)
                   ->sourceInterfaceVersion($settings->ontologyReadInterfaceVersion)
                   ->send();
      
      $this->assertEquals($ontologyRead->getStatus(), "200", "Debugging information: ".var_export($ontologyRead, TRUE));                                       

      $resultset = $ontologyRead->getResultset()->getResultset();

      $this->assertTrue(count($resultset['unspecified']) == 3);
      $this->assertTrue(isset($resultset['unspecified']['http://foo.org/test#B']));
      $this->assertTrue(count($resultset['unspecified']['http://foo.org/test#B']) == 1);
      $this->assertTrue(isset($resultset['unspecified']['http://foo.org/test#A']));
      $this->assertTrue(count($resultset['unspecified']['http://foo.org/test#A']) == 1);
      $this->assertTrue(isset($resultset['unspecified']['http://www.w3.org/2002/07/owl#Thing']));
      $this->assertTrue(count($resultset['unspecified']['http://www.w3.org/2002/07/owl#Thing']) == 1);

      unset($ontologyRead);
      unset($settings);   
    }
    
    public function test_getSuperClasses_Mode_Description_All_Reasoner_Enabled() {
      
      $settings = new Config();  
      
      $ontologyRead = new OntologyReadQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
      
      $getSuperClasses = new GetSuperClassesFunction();
      
      $getSuperClasses->uri('http://foo.org/test#C')
                    ->getClassesDescriptions()
                    ->allSuperClasses();
      
      $ontologyRead->enableReasoner()
                   ->mime('resultset')
                   ->getSuperClasses($getSuperClasses)
                   ->ontology($settings->testOntologyUri)
                   ->sourceInterface($settings->ontologyReadInterface)
                   ->sourceInterfaceVersion($settings->ontologyReadInterfaceVersion)
                   ->send();
      
      $this->assertEquals($ontologyRead->getStatus(), "200", "Debugging information: ".var_export($ontologyRead, TRUE));                                       

      $resultset = $ontologyRead->getResultset()->getResultset();

      $this->assertTrue(count($resultset['unspecified']) == 3);
      $this->assertTrue(isset($resultset['unspecified']['http://foo.org/test#B']));
      $this->assertTrue(count($resultset['unspecified']['http://foo.org/test#B']) == 5);
      $this->assertTrue(isset($resultset['unspecified']['http://foo.org/test#A']));
      $this->assertTrue(count($resultset['unspecified']['http://foo.org/test#A']) == 5);
      $this->assertTrue(isset($resultset['unspecified']['http://www.w3.org/2002/07/owl#Thing']));
      $this->assertTrue(count($resultset['unspecified']['http://www.w3.org/2002/07/owl#Thing']) == 2);


      unset($ontologyRead);
      unset($settings);   
    }    
    
    public function test_getSuperClasses_Mode_URI_Direct_Reasoner_Disabled() {
      
      $settings = new Config();  
      
      $ontologyRead = new OntologyReadQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
      
      $getSuperClasses = new GetSuperClassesFunction();
      
      $getSuperClasses->uri('http://foo.org/test#C')
                    ->getClassesUris()
                    ->directSuperClasses();
      
      $ontologyRead->disableReasoner()
                   ->mime('resultset')
                   ->getSuperClasses($getSuperClasses)
                   ->ontology($settings->testOntologyUri)
                   ->sourceInterface($settings->ontologyReadInterface)
                   ->sourceInterfaceVersion($settings->ontologyReadInterfaceVersion)
                   ->send();
      
      $this->assertEquals($ontologyRead->getStatus(), "200", "Debugging information: ".var_export($ontologyRead, TRUE));                                       

      $resultset = $ontologyRead->getResultset()->getResultset();

      $this->assertTrue(count($resultset['unspecified']) == 1);
      $this->assertTrue(isset($resultset['unspecified']['http://foo.org/test#B']));
      $this->assertTrue(count($resultset['unspecified']['http://foo.org/test#B']) == 1);

      unset($ontologyRead);
      unset($settings);   
    }
    
    public function test_getSuperClasses_Mode_Description_Direct_Reasoner_Disabled() {
      
      $settings = new Config();  
      
      $ontologyRead = new OntologyReadQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
      
      $getSuperClasses = new GetSuperClassesFunction();
      
      $getSuperClasses->uri('http://foo.org/test#C')
                    ->getClassesDescriptions()
                    ->directSuperClasses();
      
      $ontologyRead->disableReasoner()
                   ->mime('resultset')
                   ->getSuperClasses($getSuperClasses)
                   ->ontology($settings->testOntologyUri)
                   ->sourceInterface($settings->ontologyReadInterface)
                   ->sourceInterfaceVersion($settings->ontologyReadInterfaceVersion)
                   ->send();
      
      $this->assertEquals($ontologyRead->getStatus(), "200", "Debugging information: ".var_export($ontologyRead, TRUE));                                       

      $resultset = $ontologyRead->getResultset()->getResultset();

      $this->assertTrue(count($resultset['unspecified']) == 1);
      $this->assertTrue(isset($resultset['unspecified']['http://foo.org/test#B']));
      $this->assertTrue(count($resultset['unspecified']['http://foo.org/test#B']) == 5);

      unset($ontologyRead);
      unset($settings);   
    }    
       
    public function test_getSuperClasses_Mode_URI_All_Reasoner_Disabled() {
      
      $settings = new Config();  
      
      $ontologyRead = new OntologyReadQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
      
      $getSuperClasses = new GetSuperClassesFunction();
      
      $getSuperClasses->uri('http://foo.org/test#C')
                    ->getClassesUris()
                    ->allSuperClasses();
                    
      $ontologyRead->disableReasoner()
                   ->mime('resultset')
                   ->getSuperClasses($getSuperClasses)
                   ->ontology($settings->testOntologyUri)
                   ->sourceInterface($settings->ontologyReadInterface)
                   ->sourceInterfaceVersion($settings->ontologyReadInterfaceVersion)
                   ->send();
      
      $this->assertEquals($ontologyRead->getStatus(), "200", "Debugging information: ".var_export($ontologyRead, TRUE));                                       

      $resultset = $ontologyRead->getResultset()->getResultset();

      $this->assertTrue(count($resultset['unspecified']) == 1);
      $this->assertTrue(isset($resultset['unspecified']['http://foo.org/test#B']));
      $this->assertTrue(count($resultset['unspecified']['http://foo.org/test#B']) == 1);

      unset($ontologyRead);
      unset($settings);   
    }
    
    public function test_getSuperClasses_Mode_Description_All_Reasoner_Disabled() {
      
      $settings = new Config();  
      
      $ontologyRead = new OntologyReadQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
      
      $getSuperClasses = new GetSuperClassesFunction();
      
      $getSuperClasses->uri('http://foo.org/test#C')
                    ->getClassesDescriptions()
                    ->allSuperClasses();
      
      $ontologyRead->disableReasoner()
                   ->mime('resultset')
                   ->getSuperClasses($getSuperClasses)
                   ->ontology($settings->testOntologyUri)
                   ->sourceInterface($settings->ontologyReadInterface)
                   ->sourceInterfaceVersion($settings->ontologyReadInterfaceVersion)
                   ->send();
      
      $this->assertEquals($ontologyRead->getStatus(), "200", "Debugging information: ".var_export($ontologyRead, TRUE));                                       

      $resultset = $ontologyRead->getResultset()->getResultset();

      $this->assertTrue(count($resultset['unspecified']) == 1);
      $this->assertTrue(isset($resultset['unspecified']['http://foo.org/test#B']));
      $this->assertTrue(count($resultset['unspecified']['http://foo.org/test#B']) == 5);

      unset($ontologyRead);
      unset($settings);   
    }    
    
    public function test_getEquivalentClasses_Mode_URI_Reasoner_Enabled() {
      
      $settings = new Config();  
      
      $ontologyRead = new OntologyReadQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
      
      $getEquivalentClasses = new GetEquivalentClassesFunction();
      
      $getEquivalentClasses->uri('http://foo.org/test#D')
                    ->getClassesUris();
      
      $ontologyRead->enableReasoner()
                   ->mime('resultset')
                   ->getEquivalentClasses($getEquivalentClasses)
                   ->ontology($settings->testOntologyUri)
                   ->sourceInterface($settings->ontologyReadInterface)
                   ->sourceInterfaceVersion($settings->ontologyReadInterfaceVersion)
                   ->send();
      
      $this->assertEquals($ontologyRead->getStatus(), "200", "Debugging information: ".var_export($ontologyRead, TRUE));                                       

      $resultset = $ontologyRead->getResultset()->getResultset();

      $this->assertTrue(count($resultset['unspecified']) == 1);
      $this->assertTrue(isset($resultset['unspecified']['http://foo.org/test#E']));
      $this->assertTrue(count($resultset['unspecified']['http://foo.org/test#E']) == 1);

      unset($ontologyRead);
      unset($settings);   
    }
    
    public function test_getEquivalentClasses_Mode_Description_Reasoner_Enabled() {
      
      $settings = new Config();  
      
      $ontologyRead = new OntologyReadQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
      
      $getEquivalentClasses = new GetEquivalentClassesFunction();
      
      $getEquivalentClasses->uri('http://foo.org/test#D')
                    ->getClassesDescriptions();
      
      $ontologyRead->enableReasoner()
                   ->mime('resultset')
                   ->getEquivalentClasses($getEquivalentClasses)
                   ->ontology($settings->testOntologyUri)
                   ->sourceInterface($settings->ontologyReadInterface)
                   ->sourceInterfaceVersion($settings->ontologyReadInterfaceVersion)
                   ->send();
      
      $this->assertEquals($ontologyRead->getStatus(), "200", "Debugging information: ".var_export($ontologyRead, TRUE));                                       

      $resultset = $ontologyRead->getResultset()->getResultset();

      $this->assertTrue(count($resultset['unspecified']) == 1);
      $this->assertTrue(isset($resultset['unspecified']['http://foo.org/test#E']));
      $this->assertTrue(count($resultset['unspecified']['http://foo.org/test#E']) == 5);

      unset($ontologyRead);
      unset($settings);   
    }    

    public function test_getEquivalentClasses_Mode_URI_Reasoner_Disabled() {
      
      $settings = new Config();  
      
      $ontologyRead = new OntologyReadQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
      
      $getEquivalentClasses = new GetEquivalentClassesFunction();
      
      $getEquivalentClasses->uri('http://foo.org/test#D')
                    ->getClassesUris();
      
      $ontologyRead->disableReasoner()
                   ->mime('resultset')
                   ->getEquivalentClasses($getEquivalentClasses)
                   ->ontology($settings->testOntologyUri)
                   ->sourceInterface($settings->ontologyReadInterface)
                   ->sourceInterfaceVersion($settings->ontologyReadInterfaceVersion)
                   ->send();
      
      $this->assertEquals($ontologyRead->getStatus(), "200", "Debugging information: ".var_export($ontologyRead, TRUE));                                       

      $resultset = $ontologyRead->getResultset()->getResultset();

      $this->assertTrue(count($resultset['unspecified']) == 1);
      $this->assertTrue(isset($resultset['unspecified']['http://foo.org/test#E']));
      $this->assertTrue(count($resultset['unspecified']['http://foo.org/test#E']) == 1);

      unset($ontologyRead);
      unset($settings);   
    }
    
    public function test_getEquivalentClasses_Mode_Description_Reasoner_Disabled() {
      
      $settings = new Config();  
      
      $ontologyRead = new OntologyReadQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
      
      $getEquivalentClasses = new GetEquivalentClassesFunction();
      
      $getEquivalentClasses->uri('http://foo.org/test#D')
                    ->getClassesDescriptions();
      
      $ontologyRead->disableReasoner()
                   ->mime('resultset')
                   ->getEquivalentClasses($getEquivalentClasses)
                   ->ontology($settings->testOntologyUri)
                   ->sourceInterface($settings->ontologyReadInterface)
                   ->sourceInterfaceVersion($settings->ontologyReadInterfaceVersion)
                   ->send();
      
      $this->assertEquals($ontologyRead->getStatus(), "200", "Debugging information: ".var_export($ontologyRead, TRUE));                                       

      $resultset = $ontologyRead->getResultset()->getResultset();

      $this->assertTrue(count($resultset['unspecified']) == 1);
      $this->assertTrue(isset($resultset['unspecified']['http://foo.org/test#E']));
      $this->assertTrue(count($resultset['unspecified']['http://foo.org/test#E']) == 4);

      unset($ontologyRead);
      unset($settings);   
    }    
  
    public function test_getDisjointClasses_Mode_URI_Reasoner_Enabled() {
      
      $settings = new Config();  
      
      $ontologyRead = new OntologyReadQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
      
      $getDisjointClasses = new GetDisjointClassesFunction();
      
      $getDisjointClasses->uri('http://foo.org/test#F')
                    ->getClassesUris();
      
      $ontologyRead->enableReasoner()
                   ->mime('resultset')
                   ->getDisjointClasses($getDisjointClasses)
                   ->ontology($settings->testOntologyUri)
                   ->sourceInterface($settings->ontologyReadInterface)
                   ->sourceInterfaceVersion($settings->ontologyReadInterfaceVersion)
                   ->send();
      
      $this->assertEquals($ontologyRead->getStatus(), "200", "Debugging information: ".var_export($ontologyRead, TRUE));                                       

      $resultset = $ontologyRead->getResultset()->getResultset();

      $this->assertTrue(count($resultset['unspecified']) == 1);
      $this->assertTrue(isset($resultset['unspecified']['http://foo.org/test#G']));
      $this->assertTrue(count($resultset['unspecified']['http://foo.org/test#G']) == 1);

      unset($ontologyRead);
      unset($settings);   
    }
    
    public function test_getDisjointClasses_Mode_Description_Reasoner_Enabled() {
      
      $settings = new Config();  
      
      $ontologyRead = new OntologyReadQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
      
      $getDisjointClasses = new GetDisjointClassesFunction();
      
      $getDisjointClasses->uri('http://foo.org/test#F')
                    ->getClassesDescriptions();
      
      $ontologyRead->enableReasoner()
                   ->mime('resultset')
                   ->getDisjointClasses($getDisjointClasses)
                   ->ontology($settings->testOntologyUri)
                   ->sourceInterface($settings->ontologyReadInterface)
                   ->sourceInterfaceVersion($settings->ontologyReadInterfaceVersion)
                   ->send();
      
      $this->assertEquals($ontologyRead->getStatus(), "200", "Debugging information: ".var_export($ontologyRead, TRUE));                                       

      $resultset = $ontologyRead->getResultset()->getResultset();

      $this->assertTrue(count($resultset['unspecified']) == 1);
      $this->assertTrue(isset($resultset['unspecified']['http://foo.org/test#G']));
      $this->assertTrue(count($resultset['unspecified']['http://foo.org/test#G']) == 5);

      unset($ontologyRead);
      unset($settings);   
    }      
    
    public function test_getDisjointClasses_Mode_URI_Reasoner_Disabled() {
      
      $settings = new Config();  
      
      $ontologyRead = new OntologyReadQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
      
      $getDisjointClasses = new GetDisjointClassesFunction();
      
      $getDisjointClasses->uri('http://foo.org/test#F')
                    ->getClassesUris();
      
      $ontologyRead->disableReasoner()
                   ->mime('resultset')
                   ->getDisjointClasses($getDisjointClasses)
                   ->ontology($settings->testOntologyUri)
                   ->sourceInterface($settings->ontologyReadInterface)
                   ->sourceInterfaceVersion($settings->ontologyReadInterfaceVersion)
                   ->send();
      
      $this->assertEquals($ontologyRead->getStatus(), "200", "Debugging information: ".var_export($ontologyRead, TRUE));                                       

      $resultset = $ontologyRead->getResultset()->getResultset();

      $this->assertTrue(count($resultset['unspecified']) == 1);
      $this->assertTrue(isset($resultset['unspecified']['http://foo.org/test#G']));
      $this->assertTrue(count($resultset['unspecified']['http://foo.org/test#G']) == 1);

      unset($ontologyRead);
      unset($settings);   
    }
    
    public function test_getDisjointClasses_Mode_Description_Reasoner_Disabled() {
      
      $settings = new Config();  
      
      $ontologyRead = new OntologyReadQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
      
      $getDisjointClasses = new GetDisjointClassesFunction();
      
      $getDisjointClasses->uri('http://foo.org/test#F')
                    ->getClassesDescriptions();
      
      $ontologyRead->disableReasoner()
                   ->mime('resultset')
                   ->getDisjointClasses($getDisjointClasses)
                   ->ontology($settings->testOntologyUri)
                   ->sourceInterface($settings->ontologyReadInterface)
                   ->sourceInterfaceVersion($settings->ontologyReadInterfaceVersion)
                   ->send();
      
      $this->assertEquals($ontologyRead->getStatus(), "200", "Debugging information: ".var_export($ontologyRead, TRUE));                                       

      $resultset = $ontologyRead->getResultset()->getResultset();

      $this->assertTrue(count($resultset['unspecified']) == 1);
      $this->assertTrue(isset($resultset['unspecified']['http://foo.org/test#G']));
      $this->assertTrue(count($resultset['unspecified']['http://foo.org/test#G']) == 4);

      unset($ontologyRead);
      unset($settings);   
    }
                
    public function test_GetDatatypeProperty_RDFXML() {
      
      $settings = new Config();  
      
      $ontologyRead = new OntologyReadQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
      
      $getProperty = new GetPropertyFunction();
      
      $getProperty->uri('http://foo.org/test#dpA');
      
      $ontologyRead->enableReasoner()
                   ->mime('application/rdf+xml')
                   ->getProperty($getProperty)
                   ->ontology($settings->testOntologyUri)
                   ->sourceInterface($settings->ontologyReadInterface)
                   ->sourceInterfaceVersion($settings->ontologyReadInterfaceVersion)
                   ->send();
      
      $this->assertEquals($ontologyRead->getStatus(), "200", "Debugging information: ".var_export($ontologyRead, TRUE));                                       

      utilities\validateParameterApplicationRdfXml($this, $ontologyRead);

      $this->assertTrue(utilities\compareRdf($ontologyRead->getResultset(), file_get_contents($settings->contentDir.'validation/ontology_read_datatype_property_dpa.xml'), TRUE));

      unset($ontologyRead);
      unset($settings);   
    }     
       
    public function test_GetDatatypeProperty_RDFN3() {
      
      $settings = new Config();  
      
      $ontologyRead = new OntologyReadQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
      
      $getProperty = new GetPropertyFunction();
      
      $getProperty->uri('http://foo.org/test#dpA');
      
      $ontologyRead->enableReasoner()
                   ->mime('application/rdf+n3')
                   ->getProperty($getProperty)
                   ->ontology($settings->testOntologyUri)
                   ->sourceInterface($settings->ontologyReadInterface)
                   ->sourceInterfaceVersion($settings->ontologyReadInterfaceVersion)
                   ->send();
      
      $this->assertEquals($ontologyRead->getStatus(), "200", "Debugging information: ".var_export($ontologyRead, TRUE));                                       
      
      utilities\validateParameterApplicationRdfN3($this, $ontologyRead);
      
      $this->assertTrue(utilities\compareRdf($ontologyRead->getResultset(), file_get_contents($settings->contentDir.'validation/ontology_read_datatype_property_dpa.xml'), TRUE));
      
      unset($ontologyRead);
      unset($settings);   
    }            
       
    public function test_GetDatatypeProperty_structJSON() {
      
      $settings = new Config();  
      
      $ontologyRead = new OntologyReadQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
      
      $getProperty = new GetPropertyFunction();
      
      $getProperty->uri('http://foo.org/test#dpA');
      
      $ontologyRead->enableReasoner()
                   ->mime('application/json')
                   ->getProperty($getProperty)
                   ->ontology($settings->testOntologyUri)
                   ->sourceInterface($settings->ontologyReadInterface)
                   ->sourceInterfaceVersion($settings->ontologyReadInterfaceVersion)
                   ->send();
      
      $this->assertEquals($ontologyRead->getStatus(), "200", "Debugging information: ".var_export($ontologyRead, TRUE));                                       
      
      utilities\validateParameterApplicationJson($this, $ontologyRead);
            
      $this->assertTrue(utilities\compareStructJSON($ontologyRead->getResultset(), file_get_contents($settings->contentDir.'validation/ontology_read_datatype_property_dpa.json')));
            
      unset($ontologyRead);
      unset($settings);   
    }     
  
    public function test_GetDatatypeProperty_structXML() {
      
      $settings = new Config();  
      
      $ontologyRead = new OntologyReadQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
      
      $getProperty = new GetPropertyFunction();
      
      $getProperty->uri('http://foo.org/test#dpA');
      
      $ontologyRead->enableReasoner()
                   ->mime('text/xml')
                   ->getProperty($getProperty)
                   ->ontology($settings->testOntologyUri)
                   ->sourceInterface($settings->ontologyReadInterface)
                   ->sourceInterfaceVersion($settings->ontologyReadInterfaceVersion)
                   ->send();
      
      $this->assertEquals($ontologyRead->getStatus(), "200", "Debugging information: ".var_export($ontologyRead, TRUE));                                       
      
      utilities\validateParameterTextXml($this, $ontologyRead);

      $resultset = new Resultset($settings->endpointUrl);
    
      // To test the structXML serialization, we import it into a Resultset() object and we export as RDF+XML
      // then we compare it to the expected RDF+XML resultset.
      $resultset->importStructXMLResultset($ontologyRead->getResultset());
      
      $actual = str_replace('<iron:prefLabel>datatype property A</iron:prefLabel>', '', $resultset->getResultsetRDFXML());

      $this->assertTrue(utilities\compareRdf($actual, file_get_contents($settings->contentDir.'validation/ontology_read_datatype_property_dpa.xml'), TRUE));

      unset($ontologyRead);
      unset($settings);   
    }          
           
    public function test_GetDatatypeProperty_Resultset() {
      
      $settings = new Config();  
      
      $ontologyRead = new OntologyReadQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
      
      $getProperty = new GetPropertyFunction();
      
      $getProperty->uri('http://foo.org/test#dpA');
      
      $ontologyRead->enableReasoner()
                   ->mime('resultset')
                   ->getProperty($getProperty)
                   ->ontology($settings->testOntologyUri)
                   ->sourceInterface($settings->ontologyReadInterface)
                   ->sourceInterfaceVersion($settings->ontologyReadInterfaceVersion)
                   ->send();
      
      $this->assertEquals($ontologyRead->getStatus(), "200", "Debugging information: ".var_export($ontologyRead, TRUE));                                       
      
      $actual = str_replace('<iron:prefLabel>datatype property A</iron:prefLabel>', '', $ontologyRead->getResultset()->getResultsetRDFXML());      
      
      // To test the Resultset we export it as RDF+XML
      // then we compare it to the expected RDF+XML resultset.
      $this->assertTrue(utilities\compareRdf($actual, file_get_contents($settings->contentDir.'validation/ontology_read_datatype_property_dpa.xml'), TRUE));
  
      unset($ontologyRead);
      unset($settings);   
    }
    
    public function test_GetObjectProperty_RDFXML() {
      
      $settings = new Config();  
      
      $ontologyRead = new OntologyReadQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
      
      $getProperty = new GetPropertyFunction();
      
      $getProperty->uri('http://foo.org/test#opB');
      
      $ontologyRead->enableReasoner()
                   ->mime('application/rdf+xml')
                   ->getProperty($getProperty)
                   ->ontology($settings->testOntologyUri)
                   ->sourceInterface($settings->ontologyReadInterface)
                   ->sourceInterfaceVersion($settings->ontologyReadInterfaceVersion)
                   ->send();
      
      $this->assertEquals($ontologyRead->getStatus(), "200", "Debugging information: ".var_export($ontologyRead, TRUE));                                       

      utilities\validateParameterApplicationRdfXml($this, $ontologyRead);

      $this->assertTrue(utilities\compareRdf($ontologyRead->getResultset(), file_get_contents($settings->contentDir.'validation/ontology_read_object_property_opb.xml'), TRUE));

      unset($ontologyRead);
      unset($settings);   
    }     
       
    public function test_GetObjectProperty_RDFN3() {
      
      $settings = new Config();  
      
      $ontologyRead = new OntologyReadQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
      
      $getProperty = new GetPropertyFunction();
      
      $getProperty->uri('http://foo.org/test#opB');
      
      $ontologyRead->enableReasoner()
                   ->mime('application/rdf+n3')
                   ->getProperty($getProperty)
                   ->ontology($settings->testOntologyUri)
                   ->sourceInterface($settings->ontologyReadInterface)
                   ->sourceInterfaceVersion($settings->ontologyReadInterfaceVersion)
                   ->send();
      
      $this->assertEquals($ontologyRead->getStatus(), "200", "Debugging information: ".var_export($ontologyRead, TRUE));                                       
      
      utilities\validateParameterApplicationRdfN3($this, $ontologyRead);
      
      $this->assertTrue(utilities\compareRdf($ontologyRead->getResultset(), file_get_contents($settings->contentDir.'validation/ontology_read_object_property_opb.xml'), TRUE));
      
      unset($ontologyRead);
      unset($settings);   
    }            
       
    public function test_GetObjectProperty_structJSON() {
      
      $settings = new Config();  
      
      $ontologyRead = new OntologyReadQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
      
      $getProperty = new GetPropertyFunction();
      
      $getProperty->uri('http://foo.org/test#opB');
      
      $ontologyRead->enableReasoner()
                   ->mime('application/json')
                   ->getProperty($getProperty)
                   ->ontology($settings->testOntologyUri)
                   ->sourceInterface($settings->ontologyReadInterface)
                   ->sourceInterfaceVersion($settings->ontologyReadInterfaceVersion)
                   ->send();
      
      $this->assertEquals($ontologyRead->getStatus(), "200", "Debugging information: ".var_export($ontologyRead, TRUE));                                       
   
      utilities\validateParameterApplicationJson($this, $ontologyRead);
            
      $this->assertTrue(utilities\compareStructJSON($ontologyRead->getResultset(), file_get_contents($settings->contentDir.'validation/ontology_read_object_property_opb.json')));
            
      unset($ontologyRead);
      unset($settings);   
    }     
  
    public function test_GetObjectProperty_structXML() {
      
      $settings = new Config();  
      
      $ontologyRead = new OntologyReadQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
      
      $getProperty = new GetPropertyFunction();
      
      $getProperty->uri('http://foo.org/test#opB');
      
      $ontologyRead->enableReasoner()
                   ->mime('text/xml')
                   ->getProperty($getProperty)
                   ->ontology($settings->testOntologyUri)
                   ->sourceInterface($settings->ontologyReadInterface)
                   ->sourceInterfaceVersion($settings->ontologyReadInterfaceVersion)
                   ->send();
      
      $this->assertEquals($ontologyRead->getStatus(), "200", "Debugging information: ".var_export($ontologyRead, TRUE));                                       
      
      utilities\validateParameterTextXml($this, $ontologyRead);

      $resultset = new Resultset($settings->endpointUrl);
    
      // To test the structXML serialization, we import it into a Resultset() object and we export as RDF+XML
      // then we compare it to the expected RDF+XML resultset.
      $resultset->importStructXMLResultset($ontologyRead->getResultset());
      
      $actual = str_replace('<iron:prefLabel>object property B</iron:prefLabel>', '', $resultset->getResultsetRDFXML());

      $this->assertTrue(utilities\compareRdf($actual, file_get_contents($settings->contentDir.'validation/ontology_read_object_property_opb.xml'), TRUE));

      unset($ontologyRead);
      unset($settings);   
    }          
           
    public function test_GetObjectProperty_Resultset() {
      
      $settings = new Config();  
      
      $ontologyRead = new OntologyReadQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
      
      $getProperty = new GetPropertyFunction();
      
      $getProperty->uri('http://foo.org/test#opB');
      
      $ontologyRead->enableReasoner()
                   ->mime('resultset')
                   ->getProperty($getProperty)
                   ->ontology($settings->testOntologyUri)
                   ->sourceInterface($settings->ontologyReadInterface)
                   ->sourceInterfaceVersion($settings->ontologyReadInterfaceVersion)
                   ->send();
      
      $this->assertEquals($ontologyRead->getStatus(), "200", "Debugging information: ".var_export($ontologyRead, TRUE));                                       
      
      $actual = str_replace('<iron:prefLabel>object property B</iron:prefLabel>', '', $ontologyRead->getResultset()->getResultsetRDFXML());      
      
      // To test the Resultset we export it as RDF+XML
      // then we compare it to the expected RDF+XML resultset.
      $this->assertTrue(utilities\compareRdf($actual, file_get_contents($settings->contentDir.'validation/ontology_read_object_property_opb.xml'), TRUE));
  
      unset($ontologyRead);
      unset($settings);   
    }        
    
    public function test_GetAnnotationProperty_RDFXML() {
      
      $settings = new Config();  
      
      $ontologyRead = new OntologyReadQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
      
      $getProperty = new GetPropertyFunction();
      
      $getProperty->uri('http://foo.org/test#aA');
      
      $ontologyRead->enableReasoner()
                   ->mime('application/rdf+xml')
                   ->getProperty($getProperty)
                   ->ontology($settings->testOntologyUri)
                   ->sourceInterface($settings->ontologyReadInterface)
                   ->sourceInterfaceVersion($settings->ontologyReadInterfaceVersion)
                   ->send();
      
      $this->assertEquals($ontologyRead->getStatus(), "200", "Debugging information: ".var_export($ontologyRead, TRUE));                                       

      utilities\validateParameterApplicationRdfXml($this, $ontologyRead);

      $this->assertTrue(utilities\compareRdf($ontologyRead->getResultset(), file_get_contents($settings->contentDir.'validation/ontology_read_annotation_property_aa.xml'), TRUE));

      unset($ontologyRead);
      unset($settings);   
    }     
       
    public function test_GetAnnotationProperty_RDFN3() {
      
      $settings = new Config();  
      
      $ontologyRead = new OntologyReadQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
      
      $getProperty = new GetPropertyFunction();
      
      $getProperty->uri('http://foo.org/test#aA');
      
      $ontologyRead->enableReasoner()
                   ->mime('application/rdf+n3')
                   ->getProperty($getProperty)
                   ->ontology($settings->testOntologyUri)
                   ->sourceInterface($settings->ontologyReadInterface)
                   ->sourceInterfaceVersion($settings->ontologyReadInterfaceVersion)
                   ->send();
      
      $this->assertEquals($ontologyRead->getStatus(), "200", "Debugging information: ".var_export($ontologyRead, TRUE));                                       
      
      utilities\validateParameterApplicationRdfN3($this, $ontologyRead);
      
      $this->assertTrue(utilities\compareRdf($ontologyRead->getResultset(), file_get_contents($settings->contentDir.'validation/ontology_read_annotation_property_aa.xml'), TRUE));
      
      unset($ontologyRead);
      unset($settings);   
    }            
       
    public function test_GetAnnotationProperty_structJSON() {
      
      $settings = new Config();  
      
      $ontologyRead = new OntologyReadQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
      
      $getProperty = new GetPropertyFunction();
      
      $getProperty->uri('http://foo.org/test#aA');
      
      $ontologyRead->enableReasoner()
                   ->mime('application/json')
                   ->getProperty($getProperty)
                   ->ontology($settings->testOntologyUri)
                   ->sourceInterface($settings->ontologyReadInterface)
                   ->sourceInterfaceVersion($settings->ontologyReadInterfaceVersion)
                   ->send();
      
      $this->assertEquals($ontologyRead->getStatus(), "200", "Debugging information: ".var_export($ontologyRead, TRUE));                                       
      
      utilities\validateParameterApplicationJson($this, $ontologyRead);
            
      $this->assertTrue(utilities\compareStructJSON($ontologyRead->getResultset(), file_get_contents($settings->contentDir.'validation/ontology_read_annotation_property_aa.json')));
            
      unset($ontologyRead);
      unset($settings);   
    }     
  
    public function test_GetAnnotationProperty_structXML() {
      
      $settings = new Config();  
      
      $ontologyRead = new OntologyReadQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
      
      $getProperty = new GetPropertyFunction();
      
      $getProperty->uri('http://foo.org/test#aA');
      
      $ontologyRead->enableReasoner()
                   ->mime('text/xml')
                   ->getProperty($getProperty)
                   ->ontology($settings->testOntologyUri)
                   ->sourceInterface($settings->ontologyReadInterface)
                   ->sourceInterfaceVersion($settings->ontologyReadInterfaceVersion)
                   ->send();
      
      $this->assertEquals($ontologyRead->getStatus(), "200", "Debugging information: ".var_export($ontologyRead, TRUE));                                       
      
      utilities\validateParameterTextXml($this, $ontologyRead);

      $resultset = new Resultset($settings->endpointUrl);
    
      // To test the structXML serialization, we import it into a Resultset() object and we export as RDF+XML
      // then we compare it to the expected RDF+XML resultset.
      $resultset->importStructXMLResultset($ontologyRead->getResultset());
      
      $actual = str_replace('<iron:prefLabel>aA</iron:prefLabel>', '', $resultset->getResultsetRDFXML());

      $this->assertTrue(utilities\compareRdf($actual, file_get_contents($settings->contentDir.'validation/ontology_read_annotation_property_aa.xml'), TRUE));

      unset($ontologyRead);
      unset($settings);   
    }          
           
    public function test_GetAnnotationProperty_Resultset() {
      
      $settings = new Config();  
      
      $ontologyRead = new OntologyReadQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
      
      $getProperty = new GetPropertyFunction();
      
      $getProperty->uri('http://foo.org/test#aA');
      
      $ontologyRead->enableReasoner()
                   ->mime('resultset')
                   ->getProperty($getProperty)
                   ->ontology($settings->testOntologyUri)
                   ->sourceInterface($settings->ontologyReadInterface)
                   ->sourceInterfaceVersion($settings->ontologyReadInterfaceVersion)
                   ->send();
      
      $this->assertEquals($ontologyRead->getStatus(), "200", "Debugging information: ".var_export($ontologyRead, TRUE));                                       
      
      $actual = str_replace('<iron:prefLabel>aA</iron:prefLabel>', '', $ontologyRead->getResultset()->getResultsetRDFXML());      
      
      // To test the Resultset we export it as RDF+XML
      // then we compare it to the expected RDF+XML resultset.
      $this->assertTrue(utilities\compareRdf($actual, file_get_contents($settings->contentDir.'validation/ontology_read_annotation_property_aa.xml'), TRUE));
  
      unset($ontologyRead);
      unset($settings);   
    }  
      
    public function test_getProperties_All_TypesMode_URI_Limit_1_Offset_0_Reasoner_Enabled() {
      
      $settings = new Config();  
      
      $ontologyRead = new OntologyReadQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
      
      $getProperties = new GetPropertiesFunction();
      
      $getProperties->getPropertiesUris()
                 ->getAllPropertiesTypes()                 
                 ->limit(1)
                 ->offset(0);
      
      $ontologyRead->enableReasoner()
                   ->mime('resultset')
                   ->getProperties($getProperties)
                   ->ontology($settings->testOntologyUri)
                   ->sourceInterface($settings->ontologyReadInterface)
                   ->sourceInterfaceVersion($settings->ontologyReadInterfaceVersion)
                   ->send();
      
      $this->assertEquals($ontologyRead->getStatus(), "200", "Debugging information: ".var_export($ontologyRead, TRUE));                                       

      $resultset = $ontologyRead->getResultset()->getResultset();

      $this->assertTrue(count($resultset['unspecified']) == 1);
      $this->assertTrue(isset($resultset['unspecified']['http://foo.org/test#dpC']));
      $this->assertTrue(count($resultset['unspecified']['http://foo.org/test#dpC']) == 1);

      unset($ontologyRead);
      unset($settings);   
    }     
   
    public function test_getProperties_All_TypesMode_URI_Limit_2_Offset_1_Reasoner_Enabled() {
      
      $settings = new Config();  
      
      $ontologyRead = new OntologyReadQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
      
      $getProperties = new GetPropertiesFunction();
      
      $getProperties->getPropertiesUris()
                 ->getAllPropertiesTypes()                 
                 ->limit(2)
                 ->offset(1);
      
      $ontologyRead->enableReasoner()
                   ->mime('resultset')
                   ->getProperties($getProperties)
                   ->ontology($settings->testOntologyUri)
                   ->sourceInterface($settings->ontologyReadInterface)
                   ->sourceInterfaceVersion($settings->ontologyReadInterfaceVersion)
                   ->send();
      
      $this->assertEquals($ontologyRead->getStatus(), "200", "Debugging information: ".var_export($ontologyRead, TRUE));                                       

      $resultset = $ontologyRead->getResultset()->getResultset();

      $this->assertTrue(count($resultset['unspecified']) == 2);
      $this->assertTrue(isset($resultset['unspecified']['http://foo.org/test#dpA']));
      $this->assertTrue(count($resultset['unspecified']['http://foo.org/test#dpA']) == 1);
      $this->assertTrue(isset($resultset['unspecified']['http://foo.org/test#dpD']));
      $this->assertTrue(count($resultset['unspecified']['http://foo.org/test#dpD']) == 1);

      unset($ontologyRead);
      unset($settings);   
    }  
    
    public function test_getProperties_All_TypesMode_Description_Limit_1_Offset_0_Reasoner_Enabled() {
      
      $settings = new Config();  
      
      $ontologyRead = new OntologyReadQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
      
      $getProperties = new GetPropertiesFunction();
      
      $getProperties->getPropertiesDescriptions()
                 ->getAllPropertiesTypes()                 
                 ->limit(1)
                 ->offset(0);
      
      $ontologyRead->enableReasoner()
                   ->mime('resultset')
                   ->getProperties($getProperties)
                   ->ontology($settings->testOntologyUri)
                   ->sourceInterface($settings->ontologyReadInterface)
                   ->sourceInterfaceVersion($settings->ontologyReadInterfaceVersion)
                   ->send();
      
      $this->assertEquals($ontologyRead->getStatus(), "200", "Debugging information: ".var_export($ontologyRead, TRUE));                                       

      $resultset = $ontologyRead->getResultset()->getResultset();

      $this->assertTrue(count($resultset['unspecified']) == 1);
      $this->assertTrue(isset($resultset['unspecified']['http://foo.org/test#dpC']));
      $this->assertTrue(count($resultset['unspecified']['http://foo.org/test#dpC']) == 4);

      unset($ontologyRead);
      unset($settings);   
    }     
    
    public function test_getProperties_All_TypesMode_Description_Limit_2_Offset_1_Reasoner_Enabled() {
      
      $settings = new Config();  
      
      $ontologyRead = new OntologyReadQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
      
      $getProperties = new GetPropertiesFunction();
      
      $getProperties->getPropertiesDescriptions()
                 ->getAllPropertiesTypes()                 
                 ->limit(2)
                 ->offset(1);
      
      $ontologyRead->enableReasoner()
                   ->mime('resultset')
                   ->getProperties($getProperties)
                   ->ontology($settings->testOntologyUri)
                   ->sourceInterface($settings->ontologyReadInterface)
                   ->sourceInterfaceVersion($settings->ontologyReadInterfaceVersion)
                   ->send();
      
      $this->assertEquals($ontologyRead->getStatus(), "200", "Debugging information: ".var_export($ontologyRead, TRUE));                                       

      $resultset = $ontologyRead->getResultset()->getResultset();

      $this->assertTrue(count($resultset['unspecified']) == 2);
      $this->assertTrue(isset($resultset['unspecified']['http://foo.org/test#dpD']));
      $this->assertTrue(count($resultset['unspecified']['http://foo.org/test#dpD']) == 5);
      $this->assertTrue(isset($resultset['unspecified']['http://foo.org/test#dpA']));
      $this->assertTrue(count($resultset['unspecified']['http://foo.org/test#dpA']) == 5);

      unset($ontologyRead);
      unset($settings);   
    }

    public function test_getProperties_All_TypesMode_URI_Limit_1_Offset_0_Reasoner_Disabled() {
      
      $settings = new Config();  
      
      $ontologyRead = new OntologyReadQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
      
      $getProperties = new GetPropertiesFunction();
      
      $getProperties->getPropertiesUris()
                 ->getAllPropertiesTypes()                 
                 ->limit(1)
                 ->offset(0);
      
      $ontologyRead->disableReasoner()
                   ->mime('resultset')
                   ->getProperties($getProperties)
                   ->ontology($settings->testOntologyUri)
                   ->sourceInterface($settings->ontologyReadInterface)
                   ->sourceInterfaceVersion($settings->ontologyReadInterfaceVersion)
                   ->send();
      
      $this->assertEquals($ontologyRead->getStatus(), "200", "Debugging information: ".var_export($ontologyRead, TRUE));                                       

      $resultset = $ontologyRead->getResultset()->getResultset();

      $this->assertTrue(count($resultset['unspecified']) == 1);
      $this->assertTrue(isset($resultset['unspecified']['http://foo.org/test#dpC']));
      $this->assertTrue(count($resultset['unspecified']['http://foo.org/test#dpC']) == 1);

      unset($ontologyRead);
      unset($settings);   
    }     
    
    public function test_getProperties_All_TypesMode_URI_Limit_2_Offset_1_Reasoner_Disabled() {
      
      $settings = new Config();  
      
      $ontologyRead = new OntologyReadQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
      
      $getProperties = new GetPropertiesFunction();
      
      $getProperties->getPropertiesUris()
                 ->getAllPropertiesTypes()                 
                 ->limit(2)
                 ->offset(1);
      
      $ontologyRead->disableReasoner()
                   ->mime('resultset')
                   ->getProperties($getProperties)
                   ->ontology($settings->testOntologyUri)
                   ->sourceInterface($settings->ontologyReadInterface)
                   ->sourceInterfaceVersion($settings->ontologyReadInterfaceVersion)
                   ->send();
      
      $this->assertEquals($ontologyRead->getStatus(), "200", "Debugging information: ".var_export($ontologyRead, TRUE));                                       

      $resultset = $ontologyRead->getResultset()->getResultset();

      $this->assertTrue(count($resultset['unspecified']) == 2);
      $this->assertTrue(isset($resultset['unspecified']['http://foo.org/test#dpD']));
      $this->assertTrue(count($resultset['unspecified']['http://foo.org/test#dpD']) == 1);
      $this->assertTrue(isset($resultset['unspecified']['http://foo.org/test#dpA']));
      $this->assertTrue(count($resultset['unspecified']['http://foo.org/test#dpA']) == 1);

      unset($ontologyRead);
      unset($settings);   
    }  
    
    public function test_getProperties_All_TypesMode_Description_Limit_1_Offset_0_Reasoner_Disabled() {
      
      $settings = new Config();  
      
      $ontologyRead = new OntologyReadQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
      
      $getProperties = new GetPropertiesFunction();
      
      $getProperties->getPropertiesDescriptions()
                 ->getAllPropertiesTypes()                 
                 ->limit(1)
                 ->offset(0);
      
      $ontologyRead->disableReasoner()
                   ->mime('resultset')
                   ->getProperties($getProperties)
                   ->ontology($settings->testOntologyUri)
                   ->sourceInterface($settings->ontologyReadInterface)
                   ->sourceInterfaceVersion($settings->ontologyReadInterfaceVersion)
                   ->send();
      
      $this->assertEquals($ontologyRead->getStatus(), "200", "Debugging information: ".var_export($ontologyRead, TRUE));                                       

      $resultset = $ontologyRead->getResultset()->getResultset();

      $this->assertTrue(count($resultset['unspecified']) == 1);
      $this->assertTrue(isset($resultset['unspecified']['http://foo.org/test#dpC']));
      $this->assertTrue(count($resultset['unspecified']['http://foo.org/test#dpC']) == 4);

      unset($ontologyRead);
      unset($settings);   
    }     
    
    public function test_getProperties_All_TypesMode_Description_Limit_2_Offset_1_Reasoner_Disabled() {
      
      $settings = new Config();  
      
      $ontologyRead = new OntologyReadQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
      
      $getProperties = new GetPropertiesFunction();
      
      $getProperties->getPropertiesDescriptions()
                 ->getAllPropertiesTypes()                 
                 ->limit(2)
                 ->offset(1);
      
      $ontologyRead->disableReasoner()
                   ->mime('resultset')
                   ->getProperties($getProperties)
                   ->ontology($settings->testOntologyUri)
                   ->sourceInterface($settings->ontologyReadInterface)
                   ->sourceInterfaceVersion($settings->ontologyReadInterfaceVersion)
                   ->send();
      
      $this->assertEquals($ontologyRead->getStatus(), "200", "Debugging information: ".var_export($ontologyRead, TRUE));                                       

      $resultset = $ontologyRead->getResultset()->getResultset();

      $this->assertTrue(count($resultset['unspecified']) == 2);
      $this->assertTrue(isset($resultset['unspecified']['http://foo.org/test#dpD']));
      $this->assertTrue(count($resultset['unspecified']['http://foo.org/test#dpD']) == 5);
      $this->assertTrue(isset($resultset['unspecified']['http://foo.org/test#dpA']));
      $this->assertTrue(count($resultset['unspecified']['http://foo.org/test#dpA']) == 5);

      unset($ontologyRead);
      unset($settings);   
    }              
    
    public function test_getProperties_Datatype_TypesMode_URI_Limit_1_Offset_0_Reasoner_Enabled() {
      
      $settings = new Config();  
      
      $ontologyRead = new OntologyReadQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
      
      $getProperties = new GetPropertiesFunction();
      
      $getProperties->getPropertiesUris()
                 ->getDatatypeProperties()                 
                 ->limit(1)
                 ->offset(0);
      
      $ontologyRead->enableReasoner()
                   ->mime('resultset')
                   ->getProperties($getProperties)
                   ->ontology($settings->testOntologyUri)
                   ->sourceInterface($settings->ontologyReadInterface)
                   ->sourceInterfaceVersion($settings->ontologyReadInterfaceVersion)
                   ->send();
      
      $this->assertEquals($ontologyRead->getStatus(), "200", "Debugging information: ".var_export($ontologyRead, TRUE));                                       

      $resultset = $ontologyRead->getResultset()->getResultset();

      $this->assertTrue(count($resultset['unspecified']) == 1);
      $this->assertTrue(isset($resultset['unspecified']['http://foo.org/test#dpC']));
      $this->assertTrue(count($resultset['unspecified']['http://foo.org/test#dpC']) == 1);

      unset($ontologyRead);
      unset($settings);   
    }     
    
    public function test_getProperties_Datatype_TypesMode_URI_Limit_2_Offset_1_Reasoner_Enabled() {
      
      $settings = new Config();  
      
      $ontologyRead = new OntologyReadQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
      
      $getProperties = new GetPropertiesFunction();
      
      $getProperties->getPropertiesUris()
                 ->getDatatypeProperties()                 
                 ->limit(2)
                 ->offset(1);
      
      $ontologyRead->enableReasoner()
                   ->mime('resultset')
                   ->getProperties($getProperties)
                   ->ontology($settings->testOntologyUri)
                   ->sourceInterface($settings->ontologyReadInterface)
                   ->sourceInterfaceVersion($settings->ontologyReadInterfaceVersion)
                   ->send();
      
      $this->assertEquals($ontologyRead->getStatus(), "200", "Debugging information: ".var_export($ontologyRead, TRUE));                                       

      $resultset = $ontologyRead->getResultset()->getResultset();

      $this->assertTrue(count($resultset['unspecified']) == 2);
      $this->assertTrue(isset($resultset['unspecified']['http://foo.org/test#dpD']));
      $this->assertTrue(count($resultset['unspecified']['http://foo.org/test#dpD']) == 1);
      $this->assertTrue(isset($resultset['unspecified']['http://foo.org/test#dpA']));
      $this->assertTrue(count($resultset['unspecified']['http://foo.org/test#dpA']) == 1);

      unset($ontologyRead);
      unset($settings);   
    }  
    
    public function test_getProperties_Datatype_TypesMode_Description_Limit_1_Offset_0_Reasoner_Enabled() {
      
      $settings = new Config();  
      
      $ontologyRead = new OntologyReadQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
      
      $getProperties = new GetPropertiesFunction();
      
      $getProperties->getPropertiesDescriptions()
                 ->getDatatypeProperties()                 
                 ->limit(1)
                 ->offset(0);
      
      $ontologyRead->enableReasoner()
                   ->mime('resultset')
                   ->getProperties($getProperties)
                   ->ontology($settings->testOntologyUri)
                   ->sourceInterface($settings->ontologyReadInterface)
                   ->sourceInterfaceVersion($settings->ontologyReadInterfaceVersion)
                   ->send();
      
      $this->assertEquals($ontologyRead->getStatus(), "200", "Debugging information: ".var_export($ontologyRead, TRUE));                                       

      $resultset = $ontologyRead->getResultset()->getResultset();

      $this->assertTrue(count($resultset['unspecified']) == 1);
      $this->assertTrue(isset($resultset['unspecified']['http://foo.org/test#dpC']));
      $this->assertTrue(count($resultset['unspecified']['http://foo.org/test#dpC']) == 4);

      unset($ontologyRead);
      unset($settings);   
    }     
    
    public function test_getProperties_Datatype_TypesMode_Description_Limit_2_Offset_1_Reasoner_Enabled() {
      
      $settings = new Config();  
      
      $ontologyRead = new OntologyReadQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
      
      $getProperties = new GetPropertiesFunction();
      
      $getProperties->getPropertiesDescriptions()
                 ->getDatatypeProperties()                 
                 ->limit(2)
                 ->offset(1);
      
      $ontologyRead->enableReasoner()
                   ->mime('resultset')
                   ->getProperties($getProperties)
                   ->ontology($settings->testOntologyUri)
                   ->sourceInterface($settings->ontologyReadInterface)
                   ->sourceInterfaceVersion($settings->ontologyReadInterfaceVersion)
                   ->send();
      
      $this->assertEquals($ontologyRead->getStatus(), "200", "Debugging information: ".var_export($ontologyRead, TRUE));                                       

      $resultset = $ontologyRead->getResultset()->getResultset();

      $this->assertTrue(count($resultset['unspecified']) == 2);
      $this->assertTrue(isset($resultset['unspecified']['http://foo.org/test#dpD']));
      $this->assertTrue(count($resultset['unspecified']['http://foo.org/test#dpD']) == 5);
      $this->assertTrue(isset($resultset['unspecified']['http://foo.org/test#dpA']));
      $this->assertTrue(count($resultset['unspecified']['http://foo.org/test#dpA']) == 5);

      unset($ontologyRead);
      unset($settings);   
    }
    
       
    public function test_getProperties_Datatype_TypesMode_URI_Limit_1_Offset_0_Reasoner_Disabled() {
      
      $settings = new Config();  
      
      $ontologyRead = new OntologyReadQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
      
      $getProperties = new GetPropertiesFunction();
      
      $getProperties->getPropertiesUris()
                 ->getDatatypeProperties()                 
                 ->limit(1)
                 ->offset(0);
      
      $ontologyRead->disableReasoner()
                   ->mime('resultset')
                   ->getProperties($getProperties)
                   ->ontology($settings->testOntologyUri)
                   ->sourceInterface($settings->ontologyReadInterface)
                   ->sourceInterfaceVersion($settings->ontologyReadInterfaceVersion)
                   ->send();
      
      $this->assertEquals($ontologyRead->getStatus(), "200", "Debugging information: ".var_export($ontologyRead, TRUE));                                       

      $resultset = $ontologyRead->getResultset()->getResultset();

      $this->assertTrue(count($resultset['unspecified']) == 1);
      $this->assertTrue(isset($resultset['unspecified']['http://foo.org/test#dpC']));
      $this->assertTrue(count($resultset['unspecified']['http://foo.org/test#dpC']) == 1);

      unset($ontologyRead);
      unset($settings);   
    }     
    
    public function test_getProperties_Datatype_TypesMode_URI_Limit_2_Offset_1_Reasoner_Disabled() {
      
      $settings = new Config();  
      
      $ontologyRead = new OntologyReadQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
      
      $getProperties = new GetPropertiesFunction();
      
      $getProperties->getPropertiesUris()
                 ->getDatatypeProperties()                 
                 ->limit(2)
                 ->offset(1);
      
      $ontologyRead->disableReasoner()
                   ->mime('resultset')
                   ->getProperties($getProperties)
                   ->ontology($settings->testOntologyUri)
                   ->sourceInterface($settings->ontologyReadInterface)
                   ->sourceInterfaceVersion($settings->ontologyReadInterfaceVersion)
                   ->send();
      
      $this->assertEquals($ontologyRead->getStatus(), "200", "Debugging information: ".var_export($ontologyRead, TRUE));                                       

      $resultset = $ontologyRead->getResultset()->getResultset();

      $this->assertTrue(count($resultset['unspecified']) == 2);
      $this->assertTrue(isset($resultset['unspecified']['http://foo.org/test#dpD']));
      $this->assertTrue(count($resultset['unspecified']['http://foo.org/test#dpD']) == 1);
      $this->assertTrue(isset($resultset['unspecified']['http://foo.org/test#dpA']));
      $this->assertTrue(count($resultset['unspecified']['http://foo.org/test#dpA']) == 1);

      unset($ontologyRead);
      unset($settings);   
    }  
    
    public function test_getProperties_Datatype_TypesMode_Description_Limit_1_Offset_0_Reasoner_Disabled() {
      
      $settings = new Config();  
      
      $ontologyRead = new OntologyReadQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
      
      $getProperties = new GetPropertiesFunction();
      
      $getProperties->getPropertiesDescriptions()
                 ->getDatatypeProperties()                 
                 ->limit(1)
                 ->offset(0);
      
      $ontologyRead->disableReasoner()
                   ->mime('resultset')
                   ->getProperties($getProperties)
                   ->ontology($settings->testOntologyUri)
                   ->sourceInterface($settings->ontologyReadInterface)
                   ->sourceInterfaceVersion($settings->ontologyReadInterfaceVersion)
                   ->send();
      
      $this->assertEquals($ontologyRead->getStatus(), "200", "Debugging information: ".var_export($ontologyRead, TRUE));                                       

      $resultset = $ontologyRead->getResultset()->getResultset();

      $this->assertTrue(count($resultset['unspecified']) == 1);
      $this->assertTrue(isset($resultset['unspecified']['http://foo.org/test#dpC']));
      $this->assertTrue(count($resultset['unspecified']['http://foo.org/test#dpC']) == 4);

      unset($ontologyRead);
      unset($settings);   
    }     
    
    public function test_getProperties_Datatype_TypesMode_Description_Limit_2_Offset_1_Reasoner_Disabled() {
      
      $settings = new Config();  
      
      $ontologyRead = new OntologyReadQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
      
      $getProperties = new GetPropertiesFunction();
      
      $getProperties->getPropertiesDescriptions()
                 ->getDatatypeProperties()                 
                 ->limit(2)
                 ->offset(1);
      
      $ontologyRead->disableReasoner()
                   ->mime('resultset')
                   ->getProperties($getProperties)
                   ->ontology($settings->testOntologyUri)
                   ->sourceInterface($settings->ontologyReadInterface)
                   ->sourceInterfaceVersion($settings->ontologyReadInterfaceVersion)
                   ->send();
      
      $this->assertEquals($ontologyRead->getStatus(), "200", "Debugging information: ".var_export($ontologyRead, TRUE));                                       

      $resultset = $ontologyRead->getResultset()->getResultset();

      $this->assertTrue(count($resultset['unspecified']) == 2);
      $this->assertTrue(isset($resultset['unspecified']['http://foo.org/test#dpD']));
      $this->assertTrue(count($resultset['unspecified']['http://foo.org/test#dpD']) == 5);
      $this->assertTrue(isset($resultset['unspecified']['http://foo.org/test#dpA']));
      $this->assertTrue(count($resultset['unspecified']['http://foo.org/test#dpA']) == 5);

      unset($ontologyRead);
      unset($settings);   
    }    
   
    public function test_getProperties_Object_TypesMode_URI_Limit_1_Offset_0_Reasoner_Enabled() {
      
      $settings = new Config();  
      
      $ontologyRead = new OntologyReadQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
      
      $getProperties = new GetPropertiesFunction();
      
      $getProperties->getPropertiesUris()
                 ->getObjectProperties()                 
                 ->limit(1)
                 ->offset(0);
      
      $ontologyRead->enableReasoner()
                   ->mime('resultset')
                   ->getProperties($getProperties)
                   ->ontology($settings->testOntologyUri)
                   ->sourceInterface($settings->ontologyReadInterface)
                   ->sourceInterfaceVersion($settings->ontologyReadInterfaceVersion)
                   ->send();
      
      $this->assertEquals($ontologyRead->getStatus(), "200", "Debugging information: ".var_export($ontologyRead, TRUE));                                       

      $resultset = $ontologyRead->getResultset()->getResultset();

      $this->assertTrue(count($resultset['unspecified']) == 1);
      $this->assertTrue(isset($resultset['unspecified']['http://foo.org/test#opB']));
      $this->assertTrue(count($resultset['unspecified']['http://foo.org/test#opB']) == 1);

      unset($ontologyRead);
      unset($settings);   
    }     
    
    public function test_getProperties_Object_TypesMode_URI_Limit_2_Offset_1_Reasoner_Enabled() {
      
      $settings = new Config();  
      
      $ontologyRead = new OntologyReadQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
      
      $getProperties = new GetPropertiesFunction();
      
      $getProperties->getPropertiesUris()
                 ->getObjectProperties()                 
                 ->limit(2)
                 ->offset(1);
      
      $ontologyRead->enableReasoner()
                   ->mime('resultset')
                   ->getProperties($getProperties)
                   ->ontology($settings->testOntologyUri)
                   ->sourceInterface($settings->ontologyReadInterface)
                   ->sourceInterfaceVersion($settings->ontologyReadInterfaceVersion)
                   ->send();
      
      $this->assertEquals($ontologyRead->getStatus(), "200", "Debugging information: ".var_export($ontologyRead, TRUE));                                       

      $resultset = $ontologyRead->getResultset()->getResultset();

      $this->assertTrue(count($resultset['unspecified']) == 2);
      $this->assertTrue(isset($resultset['unspecified']['http://foo.org/test#opC']));
      $this->assertTrue(count($resultset['unspecified']['http://foo.org/test#opC']) == 1);
      $this->assertTrue(isset($resultset['unspecified']['http://foo.org/test#opA']));
      $this->assertTrue(count($resultset['unspecified']['http://foo.org/test#opA']) == 1);

      unset($ontologyRead);
      unset($settings);   
    }  
    
    public function test_getProperties_Object_TypesMode_Description_Limit_1_Offset_0_Reasoner_Enabled() {
      
      $settings = new Config();  
      
      $ontologyRead = new OntologyReadQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
      
      $getProperties = new GetPropertiesFunction();
      
      $getProperties->getPropertiesDescriptions()
                 ->getObjectProperties()                 
                 ->limit(1)
                 ->offset(0);
      
      $ontologyRead->enableReasoner()
                   ->mime('resultset')
                   ->getProperties($getProperties)
                   ->ontology($settings->testOntologyUri)
                   ->sourceInterface($settings->ontologyReadInterface)
                   ->sourceInterfaceVersion($settings->ontologyReadInterfaceVersion)
                   ->send();
      
      $this->assertEquals($ontologyRead->getStatus(), "200", "Debugging information: ".var_export($ontologyRead, TRUE));                                       

      $resultset = $ontologyRead->getResultset()->getResultset();

      $this->assertTrue(count($resultset['unspecified']) == 1);
      $this->assertTrue(isset($resultset['unspecified']['http://foo.org/test#opB']));
      $this->assertTrue(count($resultset['unspecified']['http://foo.org/test#opB']) == 7);

      unset($ontologyRead);
      unset($settings);   
    }     
    
    public function test_getProperties_Object_TypesMode_Description_Limit_2_Offset_1_Reasoner_Enabled() {
      
      $settings = new Config();  
      
      $ontologyRead = new OntologyReadQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
      
      $getProperties = new GetPropertiesFunction();
      
      $getProperties->getPropertiesDescriptions()
                 ->getObjectProperties()                 
                 ->limit(2)
                 ->offset(1);
      
      $ontologyRead->enableReasoner()
                   ->mime('resultset')
                   ->getProperties($getProperties)
                   ->ontology($settings->testOntologyUri)
                   ->sourceInterface($settings->ontologyReadInterface)
                   ->sourceInterfaceVersion($settings->ontologyReadInterfaceVersion)
                   ->send();
      
      $this->assertEquals($ontologyRead->getStatus(), "200", "Debugging information: ".var_export($ontologyRead, TRUE));                                       

      $resultset = $ontologyRead->getResultset()->getResultset();

      $this->assertTrue(count($resultset['unspecified']) == 2);
      $this->assertTrue(isset($resultset['unspecified']['http://foo.org/test#opC']));
      $this->assertTrue(count($resultset['unspecified']['http://foo.org/test#opC']) == 5);
      $this->assertTrue(isset($resultset['unspecified']['http://foo.org/test#opA']));
      $this->assertTrue(count($resultset['unspecified']['http://foo.org/test#opA']) == 6);

      unset($ontologyRead);
      unset($settings);   
    }
    
       
    public function test_getProperties_Object_TypesMode_URI_Limit_1_Offset_0_Reasoner_Disabled() {
      
      $settings = new Config();  
      
      $ontologyRead = new OntologyReadQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
      
      $getProperties = new GetPropertiesFunction();
      
      $getProperties->getPropertiesUris()
                 ->getObjectProperties()                 
                 ->limit(1)
                 ->offset(0);
      
      $ontologyRead->disableReasoner()
                   ->mime('resultset')
                   ->getProperties($getProperties)
                   ->ontology($settings->testOntologyUri)
                   ->sourceInterface($settings->ontologyReadInterface)
                   ->sourceInterfaceVersion($settings->ontologyReadInterfaceVersion)
                   ->send();
      
      $this->assertEquals($ontologyRead->getStatus(), "200", "Debugging information: ".var_export($ontologyRead, TRUE));                                       

      $resultset = $ontologyRead->getResultset()->getResultset();

      $this->assertTrue(count($resultset['unspecified']) == 1);
      $this->assertTrue(isset($resultset['unspecified']['http://foo.org/test#opB']));
      $this->assertTrue(count($resultset['unspecified']['http://foo.org/test#opB']) == 1);

      unset($ontologyRead);
      unset($settings);   
    }     
    
    public function test_getProperties_Object_TypesMode_URI_Limit_2_Offset_1_Reasoner_Disabled() {
      
      $settings = new Config();  
      
      $ontologyRead = new OntologyReadQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
      
      $getProperties = new GetPropertiesFunction();
      
      $getProperties->getPropertiesUris()
                 ->getObjectProperties()                 
                 ->limit(2)
                 ->offset(1);
      
      $ontologyRead->disableReasoner()
                   ->mime('resultset')
                   ->getProperties($getProperties)
                   ->ontology($settings->testOntologyUri)
                   ->sourceInterface($settings->ontologyReadInterface)
                   ->sourceInterfaceVersion($settings->ontologyReadInterfaceVersion)
                   ->send();
      
      $this->assertEquals($ontologyRead->getStatus(), "200", "Debugging information: ".var_export($ontologyRead, TRUE));                                       

      $resultset = $ontologyRead->getResultset()->getResultset();

      $this->assertTrue(count($resultset['unspecified']) == 2);
      $this->assertTrue(isset($resultset['unspecified']['http://foo.org/test#opC']));
      $this->assertTrue(count($resultset['unspecified']['http://foo.org/test#opC']) == 1);
      $this->assertTrue(isset($resultset['unspecified']['http://foo.org/test#opA']));
      $this->assertTrue(count($resultset['unspecified']['http://foo.org/test#opA']) == 1);

      unset($ontologyRead);
      unset($settings);   
    }  
    
    public function test_getProperties_Object_TypesMode_Description_Limit_1_Offset_0_Reasoner_Disabled() {
      
      $settings = new Config();  
      
      $ontologyRead = new OntologyReadQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
      
      $getProperties = new GetPropertiesFunction();
      
      $getProperties->getPropertiesDescriptions()
                 ->getObjectProperties()                 
                 ->limit(1)
                 ->offset(0);
      
      $ontologyRead->disableReasoner()
                   ->mime('resultset')
                   ->getProperties($getProperties)
                   ->ontology($settings->testOntologyUri)
                   ->sourceInterface($settings->ontologyReadInterface)
                   ->sourceInterfaceVersion($settings->ontologyReadInterfaceVersion)
                   ->send();
      
      $this->assertEquals($ontologyRead->getStatus(), "200", "Debugging information: ".var_export($ontologyRead, TRUE));                                       

      $resultset = $ontologyRead->getResultset()->getResultset();

      $this->assertTrue(count($resultset['unspecified']) == 1);
      $this->assertTrue(isset($resultset['unspecified']['http://foo.org/test#opB']));
      $this->assertTrue(count($resultset['unspecified']['http://foo.org/test#opB']) == 7);

      unset($ontologyRead);
      unset($settings);   
    }     
    
    public function test_getProperties_Object_TypesMode_Description_Limit_2_Offset_1_Reasoner_Disabled() {
      
      $settings = new Config();  
      
      $ontologyRead = new OntologyReadQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
      
      $getProperties = new GetPropertiesFunction();
      
      $getProperties->getPropertiesDescriptions()
                 ->getObjectProperties()                 
                 ->limit(2)
                 ->offset(1);
      
      $ontologyRead->disableReasoner()
                   ->mime('resultset')
                   ->getProperties($getProperties)
                   ->ontology($settings->testOntologyUri)
                   ->sourceInterface($settings->ontologyReadInterface)
                   ->sourceInterfaceVersion($settings->ontologyReadInterfaceVersion)
                   ->send();
      
      $this->assertEquals($ontologyRead->getStatus(), "200", "Debugging information: ".var_export($ontologyRead, TRUE));                                       

      $resultset = $ontologyRead->getResultset()->getResultset();

      $this->assertTrue(count($resultset['unspecified']) == 2);
      $this->assertTrue(isset($resultset['unspecified']['http://foo.org/test#opC']));
      $this->assertTrue(count($resultset['unspecified']['http://foo.org/test#opC']) == 5);
      $this->assertTrue(isset($resultset['unspecified']['http://foo.org/test#opA']));
      $this->assertTrue(count($resultset['unspecified']['http://foo.org/test#opA']) == 6);

      unset($ontologyRead);
      unset($settings);   
    }
    
    public function test_getProperties_Annotation_TypesMode_URI_Limit_1_Offset_0_Reasoner_Enabled() {
      
      $settings = new Config();  
      
      $ontologyRead = new OntologyReadQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
      
      $getProperties = new GetPropertiesFunction();
      
      $getProperties->getPropertiesUris()
                 ->getAnnotationProperties()                 
                 ->limit(1)
                 ->offset(0);
      
      $ontologyRead->enableReasoner()
                   ->mime('resultset')
                   ->getProperties($getProperties)
                   ->ontology($settings->testOntologyUri)
                   ->sourceInterface($settings->ontologyReadInterface)
                   ->sourceInterfaceVersion($settings->ontologyReadInterfaceVersion)
                   ->send();
      
      $this->assertEquals($ontologyRead->getStatus(), "200", "Debugging information: ".var_export($ontologyRead, TRUE));                                       

      $resultset = $ontologyRead->getResultset()->getResultset();

      $this->assertTrue(count($resultset['unspecified']) == 1);
      $this->assertTrue(isset($resultset['unspecified']['http://foo.org/test#aA']));
      $this->assertTrue(count($resultset['unspecified']['http://foo.org/test#aA']) == 1);

      unset($ontologyRead);
      unset($settings);   
    }     
    
    public function test_getProperties_Annotation_TypesMode_URI_Limit_2_Offset_1_Reasoner_Enabled() {
      
      $settings = new Config();  
      
      $ontologyRead = new OntologyReadQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
      
      $getProperties = new GetPropertiesFunction();
      
      $getProperties->getPropertiesUris()
                 ->getAnnotationProperties()                 
                 ->limit(2)
                 ->offset(1);
      
      $ontologyRead->enableReasoner()
                   ->mime('resultset')
                   ->getProperties($getProperties)
                   ->ontology($settings->testOntologyUri)
                   ->sourceInterface($settings->ontologyReadInterface)
                   ->sourceInterfaceVersion($settings->ontologyReadInterfaceVersion)
                   ->send();
      
      $this->assertEquals($ontologyRead->getStatus(), "200", "Debugging information: ".var_export($ontologyRead, TRUE));                                       

      $resultset = $ontologyRead->getResultset()->getResultset();

      $this->assertTrue(count($resultset['unspecified']) == 2);
      $this->assertTrue(isset($resultset['unspecified']['http://www.w3.org/2000/01/rdf-schema#label']));
      $this->assertTrue(count($resultset['unspecified']['http://www.w3.org/2000/01/rdf-schema#label']) == 1);
      $this->assertTrue(isset($resultset['unspecified']['http://www.w3.org/2000/01/rdf-schema#comment']));
      $this->assertTrue(count($resultset['unspecified']['http://www.w3.org/2000/01/rdf-schema#comment']) == 1);

      unset($ontologyRead);
      unset($settings);   
    }  
    
    public function test_getProperties_Annotation_TypesMode_Description_Limit_1_Offset_0_Reasoner_Enabled() {
      
      $settings = new Config();  
      
      $ontologyRead = new OntologyReadQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
      
      $getProperties = new GetPropertiesFunction();
      
      $getProperties->getPropertiesDescriptions()
                 ->getAnnotationProperties()                 
                 ->limit(1)
                 ->offset(0);
      
      $ontologyRead->enableReasoner()
                   ->mime('resultset')
                   ->getProperties($getProperties)
                   ->ontology($settings->testOntologyUri)
                   ->sourceInterface($settings->ontologyReadInterface)
                   ->sourceInterfaceVersion($settings->ontologyReadInterfaceVersion)
                   ->send();
      
      $this->assertEquals($ontologyRead->getStatus(), "200", "Debugging information: ".var_export($ontologyRead, TRUE));                                       

      $resultset = $ontologyRead->getResultset()->getResultset();

      $this->assertTrue(count($resultset['unspecified']) == 1);
      $this->assertTrue(isset($resultset['unspecified']['http://foo.org/test#aA']));
      $this->assertTrue(count($resultset['unspecified']['http://foo.org/test#aA']) == 3);

      unset($ontologyRead);
      unset($settings);   
    }     
    
    public function test_getProperties_Annotation_TypesMode_Description_Limit_2_Offset_1_Reasoner_Enabled() {
      
      $settings = new Config();  
      
      $ontologyRead = new OntologyReadQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
      
      $getProperties = new GetPropertiesFunction();
      
      $getProperties->getPropertiesDescriptions()
                 ->getAnnotationProperties()                 
                 ->limit(2)
                 ->offset(1);
      
      $ontologyRead->enableReasoner()
                   ->mime('resultset')
                   ->getProperties($getProperties)
                   ->ontology($settings->testOntologyUri)
                   ->sourceInterface($settings->ontologyReadInterface)
                   ->sourceInterfaceVersion($settings->ontologyReadInterfaceVersion)
                   ->send();
      
      $this->assertEquals($ontologyRead->getStatus(), "200", "Debugging information: ".var_export($ontologyRead, TRUE));                                       

      $resultset = $ontologyRead->getResultset()->getResultset();

      $this->assertTrue(count($resultset['unspecified']) == 2);
      $this->assertTrue(isset($resultset['unspecified']['http://www.w3.org/2000/01/rdf-schema#label']));
      $this->assertTrue(count($resultset['unspecified']['http://www.w3.org/2000/01/rdf-schema#label']) == 1);
      $this->assertTrue(isset($resultset['unspecified']['http://www.w3.org/2000/01/rdf-schema#comment']));
      $this->assertTrue(count($resultset['unspecified']['http://www.w3.org/2000/01/rdf-schema#comment']) == 1);

      unset($ontologyRead);
      unset($settings);   
    }
    
       
    public function test_getProperties_Annotation_TypesMode_URI_Limit_1_Offset_0_Reasoner_Disabled() {
      
      $settings = new Config();  
      
      $ontologyRead = new OntologyReadQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
      
      $getProperties = new GetPropertiesFunction();
      
      $getProperties->getPropertiesUris()
                 ->getAnnotationProperties()                 
                 ->limit(1)
                 ->offset(0);
      
      $ontologyRead->disableReasoner()
                   ->mime('resultset')
                   ->getProperties($getProperties)
                   ->ontology($settings->testOntologyUri)
                   ->sourceInterface($settings->ontologyReadInterface)
                   ->sourceInterfaceVersion($settings->ontologyReadInterfaceVersion)
                   ->send();
      
      $this->assertEquals($ontologyRead->getStatus(), "200", "Debugging information: ".var_export($ontologyRead, TRUE));                                       

      $resultset = $ontologyRead->getResultset()->getResultset();

      $this->assertTrue(count($resultset['unspecified']) == 1);
      $this->assertTrue(isset($resultset['unspecified']['http://foo.org/test#aA']));
      $this->assertTrue(count($resultset['unspecified']['http://foo.org/test#aA']) == 1);

      unset($ontologyRead);
      unset($settings);   
    }     
    
    public function test_getProperties_Annotation_TypesMode_URI_Limit_2_Offset_1_Reasoner_Disabled() {
      
      $settings = new Config();  
      
      $ontologyRead = new OntologyReadQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
      
      $getProperties = new GetPropertiesFunction();
      
      $getProperties->getPropertiesUris()
                 ->getAnnotationProperties()                 
                 ->limit(2)
                 ->offset(1);
      
      $ontologyRead->disableReasoner()
                   ->mime('resultset')
                   ->getProperties($getProperties)
                   ->ontology($settings->testOntologyUri)
                   ->sourceInterface($settings->ontologyReadInterface)
                   ->sourceInterfaceVersion($settings->ontologyReadInterfaceVersion)
                   ->send();
      
      $this->assertEquals($ontologyRead->getStatus(), "200", "Debugging information: ".var_export($ontologyRead, TRUE));                                       

      $resultset = $ontologyRead->getResultset()->getResultset();

      $this->assertTrue(count($resultset['unspecified']) == 2);
      $this->assertTrue(isset($resultset['unspecified']['http://www.w3.org/2000/01/rdf-schema#label']));
      $this->assertTrue(count($resultset['unspecified']['http://www.w3.org/2000/01/rdf-schema#label']) == 1);
      $this->assertTrue(isset($resultset['unspecified']['http://www.w3.org/2000/01/rdf-schema#comment']));
      $this->assertTrue(count($resultset['unspecified']['http://www.w3.org/2000/01/rdf-schema#comment']) == 1);

      unset($ontologyRead);
      unset($settings);   
    }  
    
    public function test_getProperties_Annotation_TypesMode_Description_Limit_1_Offset_0_Reasoner_Disabled() {
      
      $settings = new Config();  
      
      $ontologyRead = new OntologyReadQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
      
      $getProperties = new GetPropertiesFunction();
      
      $getProperties->getPropertiesDescriptions()
                 ->getAnnotationProperties()                 
                 ->limit(1)
                 ->offset(0);
      
      $ontologyRead->disableReasoner()
                   ->mime('resultset')
                   ->getProperties($getProperties)
                   ->ontology($settings->testOntologyUri)
                   ->sourceInterface($settings->ontologyReadInterface)
                   ->sourceInterfaceVersion($settings->ontologyReadInterfaceVersion)
                   ->send();
      
      $this->assertEquals($ontologyRead->getStatus(), "200", "Debugging information: ".var_export($ontologyRead, TRUE));                                       

      $resultset = $ontologyRead->getResultset()->getResultset();

      $this->assertTrue(count($resultset['unspecified']) == 1);
      $this->assertTrue(isset($resultset['unspecified']['http://foo.org/test#aA']));
      $this->assertTrue(count($resultset['unspecified']['http://foo.org/test#aA']) == 3);

      unset($ontologyRead);
      unset($settings);   
    }     
    
    public function test_getProperties_Annotation_TypesMode_Description_Limit_2_Offset_1_Reasoner_Disabled() {
      
      $settings = new Config();  
      
      $ontologyRead = new OntologyReadQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
      
      $getProperties = new GetPropertiesFunction();
      
      $getProperties->getPropertiesDescriptions()
                 ->getAnnotationProperties()                 
                 ->limit(2)
                 ->offset(1);
      
      $ontologyRead->disableReasoner()
                   ->mime('resultset')
                   ->getProperties($getProperties)
                   ->ontology($settings->testOntologyUri)
                   ->sourceInterface($settings->ontologyReadInterface)
                   ->sourceInterfaceVersion($settings->ontologyReadInterfaceVersion)
                   ->send();
      
      $this->assertEquals($ontologyRead->getStatus(), "200", "Debugging information: ".var_export($ontologyRead, TRUE));                                       

      $resultset = $ontologyRead->getResultset()->getResultset();

      $this->assertTrue(count($resultset['unspecified']) == 2);
      $this->assertTrue(isset($resultset['unspecified']['http://www.w3.org/2000/01/rdf-schema#label']));
      $this->assertTrue(count($resultset['unspecified']['http://www.w3.org/2000/01/rdf-schema#label']) == 1);
      $this->assertTrue(isset($resultset['unspecified']['http://www.w3.org/2000/01/rdf-schema#comment']));
      $this->assertTrue(count($resultset['unspecified']['http://www.w3.org/2000/01/rdf-schema#comment']) == 1);

      unset($ontologyRead);
      unset($settings);   
    } 
    
    public function test_getSubProperties_Datatype_Mode_Uris_Direct_Reasoner_Enabled() {
      
      $settings = new Config();  
      
      $ontologyRead = new OntologyReadQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
      
      $getSubProperties = new GetSubPropertiesFunction();
      
      $getSubProperties->uri('http://foo.org/test#dpA')
                       ->directSubProperties()
                       ->getDatatypeProperties()
                       ->getPropertiesUris();
      
      $ontologyRead->enableReasoner()
                   ->mime('resultset')
                   ->getSubProperties($getSubProperties)
                   ->ontology($settings->testOntologyUri)
                   ->sourceInterface($settings->ontologyReadInterface)
                   ->sourceInterfaceVersion($settings->ontologyReadInterfaceVersion)
                   ->send();
      
      $this->assertEquals($ontologyRead->getStatus(), "200", "Debugging information: ".var_export($ontologyRead, TRUE));                                       

      $resultset = $ontologyRead->getResultset()->getResultset();

      $this->assertTrue(count($resultset['unspecified']) == 1);
      $this->assertTrue(isset($resultset['unspecified']['http://foo.org/test#dpB']));
      $this->assertTrue(count($resultset['unspecified']['http://foo.org/test#dpB']) == 1);

      unset($ontologyRead);
      unset($settings);   
    }  
    
    public function test_getSubProperties_Datatype_Mode_Uris_All_Reasoner_Enabled() {
      
      $settings = new Config();  
      
      $ontologyRead = new OntologyReadQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
      
      $getSubProperties = new GetSubPropertiesFunction();
      
      $getSubProperties->uri('http://foo.org/test#dpA')
                       ->allSubProperties()
                       ->getDatatypeProperties()
                       ->getPropertiesUris();
      
      $ontologyRead->enableReasoner()
                   ->mime('resultset')
                   ->getSubProperties($getSubProperties)
                   ->ontology($settings->testOntologyUri)
                   ->sourceInterface($settings->ontologyReadInterface)
                   ->sourceInterfaceVersion($settings->ontologyReadInterfaceVersion)
                   ->send();
      
      $this->assertEquals($ontologyRead->getStatus(), "200", "Debugging information: ".var_export($ontologyRead, TRUE));                                       

      $resultset = $ontologyRead->getResultset()->getResultset();

      $this->assertTrue(count($resultset['unspecified']) == 2);
      $this->assertTrue(isset($resultset['unspecified']['http://foo.org/test#dpB']));
      $this->assertTrue(count($resultset['unspecified']['http://foo.org/test#dpB']) == 1);
      $this->assertTrue(isset($resultset['unspecified']['http://foo.org/test#dpC']));
      $this->assertTrue(count($resultset['unspecified']['http://foo.org/test#dpC']) == 1);

      unset($ontologyRead);
      unset($settings);   
    }
    
    public function test_getSubProperties_Datatype_Mode_Descriptions_Direct_Reasoner_Enabled() {
      
      $settings = new Config();  
      
      $ontologyRead = new OntologyReadQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
      
      $getSubProperties = new GetSubPropertiesFunction();
      
      $getSubProperties->uri('http://foo.org/test#dpA')
                       ->directSubProperties()
                       ->getDatatypeProperties()
                       ->getPropertiesDescriptions();
      
      $ontologyRead->enableReasoner()
                   ->mime('resultset')
                   ->getSubProperties($getSubProperties)
                   ->ontology($settings->testOntologyUri)
                   ->sourceInterface($settings->ontologyReadInterface)
                   ->sourceInterfaceVersion($settings->ontologyReadInterfaceVersion)
                   ->send();
      
      $this->assertEquals($ontologyRead->getStatus(), "200", "Debugging information: ".var_export($ontologyRead, TRUE));                                       

      $resultset = $ontologyRead->getResultset()->getResultset();

      $this->assertTrue(count($resultset['unspecified']) == 1);
      $this->assertTrue(isset($resultset['unspecified']['http://foo.org/test#dpB']));
      $this->assertTrue(count($resultset['unspecified']['http://foo.org/test#dpB']) == 5);

      unset($ontologyRead);
      unset($settings);   
    }  
    
    public function test_getSubProperties_Datatype_Mode_Descriptions_All_Reasoner_Enabled() {
      
      $settings = new Config();  
      
      $ontologyRead = new OntologyReadQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
      
      $getSubProperties = new GetSubPropertiesFunction();
      
      $getSubProperties->uri('http://foo.org/test#dpA')
                       ->allSubProperties()
                       ->getDatatypeProperties()
                       ->getPropertiesDescriptions();
      
      $ontologyRead->enableReasoner()
                   ->mime('resultset')
                   ->getSubProperties($getSubProperties)
                   ->ontology($settings->testOntologyUri)
                   ->sourceInterface($settings->ontologyReadInterface)
                   ->sourceInterfaceVersion($settings->ontologyReadInterfaceVersion)
                   ->send();
      
      $this->assertEquals($ontologyRead->getStatus(), "200", "Debugging information: ".var_export($ontologyRead, TRUE));                                       

      $resultset = $ontologyRead->getResultset()->getResultset();

      $this->assertTrue(count($resultset['unspecified']) == 2);
      $this->assertTrue(isset($resultset['unspecified']['http://foo.org/test#dpB']));
      $this->assertTrue(count($resultset['unspecified']['http://foo.org/test#dpB']) == 5);
      $this->assertTrue(isset($resultset['unspecified']['http://foo.org/test#dpC']));
      $this->assertTrue(count($resultset['unspecified']['http://foo.org/test#dpC']) == 4);
      
      unset($resultset);
      unset($getSubProperties);
      unset($ontologyRead);
      unset($settings);   
    }    
    
    public function test_getSubProperties_Datatype_Mode_Uris_Direct_Reasoner_Disabled() {
      
      $settings = new Config();  
      
      $ontologyRead = new OntologyReadQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
      
      $getSubProperties = new GetSubPropertiesFunction();
      
      $getSubProperties->uri('http://foo.org/test#dpA')
                       ->directSubProperties()
                       ->getDatatypeProperties()
                       ->getPropertiesUris();
      
      $ontologyRead->disableReasoner()
                   ->mime('resultset')
                   ->getSubProperties($getSubProperties)
                   ->ontology($settings->testOntologyUri)
                   ->sourceInterface($settings->ontologyReadInterface)
                   ->sourceInterfaceVersion($settings->ontologyReadInterfaceVersion)
                   ->send();
      
      $this->assertEquals($ontologyRead->getStatus(), "200", "Debugging information: ".var_export($ontologyRead, TRUE));                                       

      $resultset = $ontologyRead->getResultset()->getResultset();

      $this->assertTrue(count($resultset['unspecified']) == 1);
      $this->assertTrue(isset($resultset['unspecified']['http://foo.org/test#dpB']));
      $this->assertTrue(count($resultset['unspecified']['http://foo.org/test#dpB']) == 1);

      unset($ontologyRead);
      unset($settings);   
    }  
    
    public function test_getSubProperties_Datatype_Mode_Uris_All_Reasoner_Disabled() {
      
      $settings = new Config();  
      
      $ontologyRead = new OntologyReadQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
      
      $getSubProperties = new GetSubPropertiesFunction();
      
      $getSubProperties->uri('http://foo.org/test#dpA')
                       ->allSubProperties()
                       ->getDatatypeProperties()
                       ->getPropertiesUris();
      
      $ontologyRead->disableReasoner()
                   ->mime('resultset')
                   ->getSubProperties($getSubProperties)
                   ->ontology($settings->testOntologyUri)
                   ->sourceInterface($settings->ontologyReadInterface)
                   ->sourceInterfaceVersion($settings->ontologyReadInterfaceVersion)
                   ->send();
      
      $this->assertEquals($ontologyRead->getStatus(), "200", "Debugging information: ".var_export($ontologyRead, TRUE));                                       

      $resultset = $ontologyRead->getResultset()->getResultset();

      $this->assertTrue(count($resultset['unspecified']) == 1);
      $this->assertTrue(isset($resultset['unspecified']['http://foo.org/test#dpB']));
      $this->assertTrue(count($resultset['unspecified']['http://foo.org/test#dpB']) == 1);

      unset($ontologyRead);
      unset($settings);   
    }
    
   public function test_getSubProperties_Datatype_Mode_Descriptions_Direct_Reasoner_Disabled() {
      
      $settings = new Config();  
      
      $ontologyRead = new OntologyReadQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
      
      $getSubProperties = new GetSubPropertiesFunction();
      
      $getSubProperties->uri('http://foo.org/test#dpA')
                       ->directSubProperties()
                       ->getDatatypeProperties()
                       ->getPropertiesDescriptions();
      
      $ontologyRead->disableReasoner()
                   ->mime('resultset')
                   ->getSubProperties($getSubProperties)
                   ->ontology($settings->testOntologyUri)
                   ->sourceInterface($settings->ontologyReadInterface)
                   ->sourceInterfaceVersion($settings->ontologyReadInterfaceVersion)
                   ->send();
      
      $this->assertEquals($ontologyRead->getStatus(), "200", "Debugging information: ".var_export($ontologyRead, TRUE));                                       

      $resultset = $ontologyRead->getResultset()->getResultset();

      $this->assertTrue(count($resultset['unspecified']) == 1);
      $this->assertTrue(isset($resultset['unspecified']['http://foo.org/test#dpB']));
      $this->assertTrue(count($resultset['unspecified']['http://foo.org/test#dpB']) == 5);

      unset($ontologyRead);
      unset($settings);   
    }  
    
    public function test_getSubProperties_Datatype_Mode_Descriptions_All_Reasoner_Disabled() {
      $settings = new Config();  
      
      $ontologyRead = new OntologyReadQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
      
      $getSubProperties = new GetSubPropertiesFunction();
      
      $getSubProperties->uri('http://foo.org/test#dpA')
                       ->allSubProperties()
                       ->getDatatypeProperties()
                       ->getPropertiesDescriptions();
      
      $ontologyRead->disableReasoner()
                   ->mime('resultset')
                   ->getSubProperties($getSubProperties)
                   ->ontology($settings->testOntologyUri)
                   ->sourceInterface($settings->ontologyReadInterface)
                   ->sourceInterfaceVersion($settings->ontologyReadInterfaceVersion)
                   ->send();
      
      $this->assertEquals($ontologyRead->getStatus(), "200", "Debugging information: ".var_export($ontologyRead, TRUE));                                       

      $resultset = $ontologyRead->getResultset()->getResultset();

      $this->assertTrue(count($resultset['unspecified']) == 1);
      $this->assertTrue(isset($resultset['unspecified']['http://foo.org/test#dpB']));
      $this->assertTrue(count($resultset['unspecified']['http://foo.org/test#dpB']) == 5);

      unset($ontologyRead);
      unset($settings);   
    }    

    public function test_getSubProperties_Object_Mode_Uris_Direct_Reasoner_Enabled() {
      
      $settings = new Config();  
      
      $ontologyRead = new OntologyReadQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
      
      $getSubProperties = new GetSubPropertiesFunction();
      
      $getSubProperties->uri('http://foo.org/test#opA')
                       ->directSubProperties()
                       ->getObjectProperties()
                       ->getPropertiesUris();
      
      $ontologyRead->enableReasoner()
                   ->mime('resultset')
                   ->getSubProperties($getSubProperties)
                   ->ontology($settings->testOntologyUri)
                   ->sourceInterface($settings->ontologyReadInterface)
                   ->sourceInterfaceVersion($settings->ontologyReadInterfaceVersion)
                   ->send();
      
      $this->assertEquals($ontologyRead->getStatus(), "200", "Debugging information: ".var_export($ontologyRead, TRUE));                                       

      $resultset = $ontologyRead->getResultset()->getResultset();

      $this->assertTrue(count($resultset['unspecified']) == 1);
      $this->assertTrue(isset($resultset['unspecified']['http://foo.org/test#opB']));
      $this->assertTrue(count($resultset['unspecified']['http://foo.org/test#opB']) == 1);

      unset($ontologyRead);
      unset($settings);   
    }  
    
    public function test_getSubProperties_Object_Mode_Uris_All_Reasoner_Enabled() {
      
      $settings = new Config();  
      
      $ontologyRead = new OntologyReadQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
      
      $getSubProperties = new GetSubPropertiesFunction();
      
      $getSubProperties->uri('http://foo.org/test#opA')
                       ->allSubProperties()
                       ->getObjectProperties()
                       ->getPropertiesUris();
      
      $ontologyRead->enableReasoner()
                   ->mime('resultset')
                   ->getSubProperties($getSubProperties)
                   ->ontology($settings->testOntologyUri)
                   ->sourceInterface($settings->ontologyReadInterface)
                   ->sourceInterfaceVersion($settings->ontologyReadInterfaceVersion)
                   ->send();
      
      $this->assertEquals($ontologyRead->getStatus(), "200", "Debugging information: ".var_export($ontologyRead, TRUE));                                       

      $resultset = $ontologyRead->getResultset()->getResultset();

      $this->assertTrue(count($resultset['unspecified']) == 2);
      $this->assertTrue(isset($resultset['unspecified']['http://foo.org/test#opB']));
      $this->assertTrue(count($resultset['unspecified']['http://foo.org/test#opB']) == 1);
      $this->assertTrue(isset($resultset['unspecified']['http://foo.org/test#opC']));
      $this->assertTrue(count($resultset['unspecified']['http://foo.org/test#opC']) == 1);

      unset($ontologyRead);
      unset($settings);   
    }
    
    public function test_getSubProperties_Object_Mode_Descriptions_Direct_Reasoner_Enabled() {
      
      $settings = new Config();  
      
      $ontologyRead = new OntologyReadQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
      
      $getSubProperties = new GetSubPropertiesFunction();
      
      $getSubProperties->uri('http://foo.org/test#opA')
                       ->directSubProperties()
                       ->getObjectProperties()
                       ->getPropertiesDescriptions();
      
      $ontologyRead->enableReasoner()
                   ->mime('resultset')
                   ->getSubProperties($getSubProperties)
                   ->ontology($settings->testOntologyUri)
                   ->sourceInterface($settings->ontologyReadInterface)
                   ->sourceInterfaceVersion($settings->ontologyReadInterfaceVersion)
                   ->send();
      
      $this->assertEquals($ontologyRead->getStatus(), "200", "Debugging information: ".var_export($ontologyRead, TRUE));                                       

      $resultset = $ontologyRead->getResultset()->getResultset();

      $this->assertTrue(count($resultset['unspecified']) == 1);
      $this->assertTrue(isset($resultset['unspecified']['http://foo.org/test#opB']));
      $this->assertTrue(count($resultset['unspecified']['http://foo.org/test#opB']) == 7);

      unset($ontologyRead);
      unset($settings);   
    }  
    
    public function test_getSubProperties_Object_Mode_Descriptions_All_Reasoner_Enabled() {
      
      $settings = new Config();  
      
      $ontologyRead = new OntologyReadQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
      
      $getSubProperties = new GetSubPropertiesFunction();
      
      $getSubProperties->uri('http://foo.org/test#opA')
                       ->allSubProperties()
                       ->getObjectProperties()
                       ->getPropertiesDescriptions();
      
      $ontologyRead->enableReasoner()
                   ->mime('resultset')
                   ->getSubProperties($getSubProperties)
                   ->ontology($settings->testOntologyUri)
                   ->sourceInterface($settings->ontologyReadInterface)
                   ->sourceInterfaceVersion($settings->ontologyReadInterfaceVersion)
                   ->send();
      
      $this->assertEquals($ontologyRead->getStatus(), "200", "Debugging information: ".var_export($ontologyRead, TRUE));                                       

      $resultset = $ontologyRead->getResultset()->getResultset();

      $this->assertTrue(count($resultset['unspecified']) == 2);
      $this->assertTrue(isset($resultset['unspecified']['http://foo.org/test#opB']));
      $this->assertTrue(count($resultset['unspecified']['http://foo.org/test#opB']) == 7);
      $this->assertTrue(isset($resultset['unspecified']['http://foo.org/test#opC']));
      $this->assertTrue(count($resultset['unspecified']['http://foo.org/test#opC']) == 5);

      unset($ontologyRead);
      unset($settings);   
    }    
  
    public function test_getSubProperties_Object_Mode_Uris_Direct_Reasoner_Disabled() {
      
      $settings = new Config();  
      
      $ontologyRead = new OntologyReadQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
      
      $getSubProperties = new GetSubPropertiesFunction();
      
      $getSubProperties->uri('http://foo.org/test#opA')
                       ->directSubProperties()
                       ->getObjectProperties()
                       ->getPropertiesUris();
      
      $ontologyRead->disableReasoner()
                   ->mime('resultset')
                   ->getSubProperties($getSubProperties)
                   ->ontology($settings->testOntologyUri)
                   ->sourceInterface($settings->ontologyReadInterface)
                   ->sourceInterfaceVersion($settings->ontologyReadInterfaceVersion)
                   ->send();
      
      $this->assertEquals($ontologyRead->getStatus(), "200", "Debugging information: ".var_export($ontologyRead, TRUE));                                       

      $resultset = $ontologyRead->getResultset()->getResultset();

      $this->assertTrue(count($resultset['unspecified']) == 1);
      $this->assertTrue(isset($resultset['unspecified']['http://foo.org/test#opB']));
      $this->assertTrue(count($resultset['unspecified']['http://foo.org/test#opB']) == 1);

      unset($ontologyRead);
      unset($settings);   
    }  
    
    public function test_getSubProperties_Object_Mode_Uris_All_Reasoner_Disabled() {
      
      $settings = new Config();  
      
      $ontologyRead = new OntologyReadQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
      
      $getSubProperties = new GetSubPropertiesFunction();
      
      $getSubProperties->uri('http://foo.org/test#opA')
                       ->allSubProperties()
                       ->getObjectProperties()
                       ->getPropertiesUris();
      
      $ontologyRead->disableReasoner()
                   ->mime('resultset')
                   ->getSubProperties($getSubProperties)
                   ->ontology($settings->testOntologyUri)
                   ->sourceInterface($settings->ontologyReadInterface)
                   ->sourceInterfaceVersion($settings->ontologyReadInterfaceVersion)
                   ->send();
      
      $this->assertEquals($ontologyRead->getStatus(), "200", "Debugging information: ".var_export($ontologyRead, TRUE));                                       

      $resultset = $ontologyRead->getResultset()->getResultset();

      $this->assertTrue(count($resultset['unspecified']) == 1);
      $this->assertTrue(isset($resultset['unspecified']['http://foo.org/test#opB']));
      $this->assertTrue(count($resultset['unspecified']['http://foo.org/test#opB']) == 1);

      unset($ontologyRead);
      unset($settings);   
    }
    
   public function test_getSubProperties_Object_Mode_Descriptions_Direct_Reasoner_Disabled() {
      
      $settings = new Config();  
      
      $ontologyRead = new OntologyReadQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
      
      $getSubProperties = new GetSubPropertiesFunction();
      
      $getSubProperties->uri('http://foo.org/test#opA')
                       ->directSubProperties()
                       ->getObjectProperties()
                       ->getPropertiesDescriptions();
      
      $ontologyRead->disableReasoner()
                   ->mime('resultset')
                   ->getSubProperties($getSubProperties)
                   ->ontology($settings->testOntologyUri)
                   ->sourceInterface($settings->ontologyReadInterface)
                   ->sourceInterfaceVersion($settings->ontologyReadInterfaceVersion)
                   ->send();
      
      $this->assertEquals($ontologyRead->getStatus(), "200", "Debugging information: ".var_export($ontologyRead, TRUE));                                       

      $resultset = $ontologyRead->getResultset()->getResultset();

      $this->assertTrue(count($resultset['unspecified']) == 1);
      $this->assertTrue(isset($resultset['unspecified']['http://foo.org/test#opB']));
      $this->assertTrue(count($resultset['unspecified']['http://foo.org/test#opB']) == 7);

      unset($ontologyRead);
      unset($settings);   
    }  
    
    public function test_getSubProperties_Object_Mode_Descriptions_All_Reasoner_Disabled() {
      
      $settings = new Config();  
      
      $ontologyRead = new OntologyReadQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
      
      $getSubProperties = new GetSubPropertiesFunction();
      
      $getSubProperties->uri('http://foo.org/test#opA')
                       ->allSubProperties()
                       ->getObjectProperties()
                       ->getPropertiesDescriptions();
      
      $ontologyRead->disableReasoner()
                   ->mime('resultset')
                   ->getSubProperties($getSubProperties)
                   ->ontology($settings->testOntologyUri)
                   ->sourceInterface($settings->ontologyReadInterface)
                   ->sourceInterfaceVersion($settings->ontologyReadInterfaceVersion)
                   ->send();
      
      $this->assertEquals($ontologyRead->getStatus(), "200", "Debugging information: ".var_export($ontologyRead, TRUE));                                       

      $resultset = $ontologyRead->getResultset()->getResultset();

      $this->assertTrue(count($resultset['unspecified']) == 1);
      $this->assertTrue(isset($resultset['unspecified']['http://foo.org/test#opB']));
      $this->assertTrue(count($resultset['unspecified']['http://foo.org/test#opB']) == 7);

      unset($ontologyRead);
      unset($settings);   
    }    
    
    public function test_getSuperProperties_Datatype_Mode_Uris_Direct_Reasoner_Enabled() {
      
      $settings = new Config();  
      
      $ontologyRead = new OntologyReadQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
      
      $getSuperProperties = new getSuperPropertiesFunction();
      
      $getSuperProperties->uri('http://foo.org/test#dpC')
                       ->directSuperProperties()
                       ->getDatatypeProperties()
                       ->getPropertiesUris();
      
      $ontologyRead->enableReasoner()
                   ->mime('resultset')
                   ->getSuperProperties($getSuperProperties)
                   ->ontology($settings->testOntologyUri)
                   ->sourceInterface($settings->ontologyReadInterface)
                   ->sourceInterfaceVersion($settings->ontologyReadInterfaceVersion)
                   ->send();
      
      $this->assertEquals($ontologyRead->getStatus(), "200", "Debugging information: ".var_export($ontologyRead, TRUE));                                       

      $resultset = $ontologyRead->getResultset()->getResultset();

      $this->assertTrue(count($resultset['unspecified']) == 1);
      $this->assertTrue(isset($resultset['unspecified']['http://foo.org/test#dpB']));
      $this->assertTrue(count($resultset['unspecified']['http://foo.org/test#dpB']) == 1);

      unset($ontologyRead);
      unset($settings);   
    }  
    
    public function test_getSuperProperties_Datatype_Mode_Uris_All_Reasoner_Enabled() {
      
      $settings = new Config();  
      
      $ontologyRead = new OntologyReadQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
      
      $getSuperProperties = new getSuperPropertiesFunction();
      
      $getSuperProperties->uri('http://foo.org/test#dpC')
                       ->allSuperProperties()
                       ->getDatatypeProperties()
                       ->getPropertiesUris();
      
      $ontologyRead->enableReasoner()
                   ->mime('resultset')
                   ->getSuperProperties($getSuperProperties)
                   ->ontology($settings->testOntologyUri)
                   ->sourceInterface($settings->ontologyReadInterface)
                   ->sourceInterfaceVersion($settings->ontologyReadInterfaceVersion)
                   ->send();
      
      $this->assertEquals($ontologyRead->getStatus(), "200", "Debugging information: ".var_export($ontologyRead, TRUE));                                       

      $resultset = $ontologyRead->getResultset()->getResultset();

      $this->assertTrue(count($resultset['unspecified']) == 3);
      $this->assertTrue(isset($resultset['unspecified']['http://foo.org/test#dpB']));
      $this->assertTrue(count($resultset['unspecified']['http://foo.org/test#dpB']) == 1);
      $this->assertTrue(isset($resultset['unspecified']['http://foo.org/test#dpA']));
      $this->assertTrue(count($resultset['unspecified']['http://foo.org/test#dpA']) == 1);
      $this->assertTrue(isset($resultset['unspecified']['http://www.w3.org/2002/07/owl#topDataProperty']));
      $this->assertTrue(count($resultset['unspecified']['http://www.w3.org/2002/07/owl#topDataProperty']) == 1);

      unset($ontologyRead);
      unset($settings);   
    }
    
    public function test_getSuperProperties_Datatype_Mode_Descriptions_Direct_Reasoner_Enabled() {
      
      $settings = new Config();  
      
      $ontologyRead = new OntologyReadQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
      
      $getSuperProperties = new getSuperPropertiesFunction();
      
      $getSuperProperties->uri('http://foo.org/test#dpC')
                       ->directSuperProperties()
                       ->getDatatypeProperties()
                       ->getPropertiesDescriptions();
      
      $ontologyRead->enableReasoner()
                   ->mime('resultset')
                   ->getSuperProperties($getSuperProperties)
                   ->ontology($settings->testOntologyUri)
                   ->sourceInterface($settings->ontologyReadInterface)
                   ->sourceInterfaceVersion($settings->ontologyReadInterfaceVersion)
                   ->send();
      
      $this->assertEquals($ontologyRead->getStatus(), "200", "Debugging information: ".var_export($ontologyRead, TRUE));                                       

      $resultset = $ontologyRead->getResultset()->getResultset();

      $this->assertTrue(count($resultset['unspecified']) == 1);
      $this->assertTrue(isset($resultset['unspecified']['http://foo.org/test#dpB']));
      $this->assertTrue(count($resultset['unspecified']['http://foo.org/test#dpB']) == 5);

      unset($ontologyRead);
      unset($settings);   
    }  
    
    public function test_getSuperProperties_Datatype_Mode_Descriptions_All_Reasoner_Enabled() {
      
      $settings = new Config();  
      
      $ontologyRead = new OntologyReadQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
      
      $getSuperProperties = new getSuperPropertiesFunction();
      
      $getSuperProperties->uri('http://foo.org/test#dpC')
                       ->allSuperProperties()
                       ->getDatatypeProperties()
                       ->getPropertiesDescriptions();
      
      $ontologyRead->enableReasoner()
                   ->mime('resultset')
                   ->getSuperProperties($getSuperProperties)
                   ->ontology($settings->testOntologyUri)
                   ->sourceInterface($settings->ontologyReadInterface)
                   ->sourceInterfaceVersion($settings->ontologyReadInterfaceVersion)
                   ->send();
      
      $this->assertEquals($ontologyRead->getStatus(), "200", "Debugging information: ".var_export($ontologyRead, TRUE));                                       

      $resultset = $ontologyRead->getResultset()->getResultset();

      $this->assertTrue(count($resultset['unspecified']) == 3);
      $this->assertTrue(isset($resultset['unspecified']['http://foo.org/test#dpB']));
      $this->assertTrue(count($resultset['unspecified']['http://foo.org/test#dpB']) == 5);
      $this->assertTrue(isset($resultset['unspecified']['http://foo.org/test#dpA']));
      $this->assertTrue(count($resultset['unspecified']['http://foo.org/test#dpA']) == 5);
      $this->assertTrue(isset($resultset['unspecified']['http://www.w3.org/2002/07/owl#topDataProperty']));
      $this->assertTrue(count($resultset['unspecified']['http://www.w3.org/2002/07/owl#topDataProperty']) == 3);

      unset($ontologyRead);
      unset($settings);   
    }    
  
    public function test_getSuperProperties_Datatype_Mode_Uris_Direct_Reasoner_Disabled() {
      
      $settings = new Config();  
      
      $ontologyRead = new OntologyReadQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
      
      $getSuperProperties = new getSuperPropertiesFunction();
      
      $getSuperProperties->uri('http://foo.org/test#dpC')
                       ->directSuperProperties()
                       ->getDatatypeProperties()
                       ->getPropertiesUris();
      
      $ontologyRead->disableReasoner()
                   ->mime('resultset')
                   ->getSuperProperties($getSuperProperties)
                   ->ontology($settings->testOntologyUri)
                   ->sourceInterface($settings->ontologyReadInterface)
                   ->sourceInterfaceVersion($settings->ontologyReadInterfaceVersion)
                   ->send();
      
      $this->assertEquals($ontologyRead->getStatus(), "200", "Debugging information: ".var_export($ontologyRead, TRUE));                                       

      $resultset = $ontologyRead->getResultset()->getResultset();

      $this->assertTrue(count($resultset['unspecified']) == 1);
      $this->assertTrue(isset($resultset['unspecified']['http://foo.org/test#dpB']));
      $this->assertTrue(count($resultset['unspecified']['http://foo.org/test#dpB']) == 1);

      unset($ontologyRead);
      unset($settings);   
    }  
    
    public function test_getSuperProperties_Datatype_Mode_Uris_All_Reasoner_Disabled() {
      
      $settings = new Config();  
      
      $ontologyRead = new OntologyReadQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
      
      $getSuperProperties = new getSuperPropertiesFunction();
      
      $getSuperProperties->uri('http://foo.org/test#dpC')
                       ->allSuperProperties()
                       ->getDatatypeProperties()
                       ->getPropertiesUris();
      
      $ontologyRead->disableReasoner()
                   ->mime('resultset')
                   ->getSuperProperties($getSuperProperties)
                   ->ontology($settings->testOntologyUri)
                   ->sourceInterface($settings->ontologyReadInterface)
                   ->sourceInterfaceVersion($settings->ontologyReadInterfaceVersion)
                   ->send();
      
      $this->assertEquals($ontologyRead->getStatus(), "200", "Debugging information: ".var_export($ontologyRead, TRUE));                                       

      $resultset = $ontologyRead->getResultset()->getResultset();

      $this->assertTrue(count($resultset['unspecified']) == 1);
      $this->assertTrue(isset($resultset['unspecified']['http://foo.org/test#dpB']));
      $this->assertTrue(count($resultset['unspecified']['http://foo.org/test#dpB']) == 1);

      unset($ontologyRead);
      unset($settings);   
    }
    
   public function test_getSuperProperties_Datatype_Mode_Descriptions_Direct_Reasoner_Disabled() {
      
      $settings = new Config();  
      
      $ontologyRead = new OntologyReadQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
      
      $getSuperProperties = new getSuperPropertiesFunction();
      
      $getSuperProperties->uri('http://foo.org/test#dpC')
                       ->directSuperProperties()
                       ->getDatatypeProperties()
                       ->getPropertiesDescriptions();
      
      $ontologyRead->disableReasoner()
                   ->mime('resultset')
                   ->getSuperProperties($getSuperProperties)
                   ->ontology($settings->testOntologyUri)
                   ->sourceInterface($settings->ontologyReadInterface)
                   ->sourceInterfaceVersion($settings->ontologyReadInterfaceVersion)
                   ->send();
      
      $this->assertEquals($ontologyRead->getStatus(), "200", "Debugging information: ".var_export($ontologyRead, TRUE));                                       

      $resultset = $ontologyRead->getResultset()->getResultset();

      $this->assertTrue(count($resultset['unspecified']) == 1);
      $this->assertTrue(isset($resultset['unspecified']['http://foo.org/test#dpB']));
      $this->assertTrue(count($resultset['unspecified']['http://foo.org/test#dpB']) == 5);

      unset($ontologyRead);
      unset($settings);   
    }  
    
    public function test_getSuperProperties_Datatype_Mode_Descriptions_All_Reasoner_Disabled() {
      
      $settings = new Config();  
      
      $ontologyRead = new OntologyReadQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
      
      $getSuperProperties = new getSuperPropertiesFunction();
      
      $getSuperProperties->uri('http://foo.org/test#dpC')
                       ->allSuperProperties()
                       ->getDatatypeProperties()
                       ->getPropertiesDescriptions();
      
      $ontologyRead->disableReasoner()
                   ->mime('resultset')
                   ->getSuperProperties($getSuperProperties)
                   ->ontology($settings->testOntologyUri)
                   ->sourceInterface($settings->ontologyReadInterface)
                   ->sourceInterfaceVersion($settings->ontologyReadInterfaceVersion)
                   ->send();
      
      $this->assertEquals($ontologyRead->getStatus(), "200", "Debugging information: ".var_export($ontologyRead, TRUE));                                       

      $resultset = $ontologyRead->getResultset()->getResultset();

      $this->assertTrue(count($resultset['unspecified']) == 1);
      $this->assertTrue(isset($resultset['unspecified']['http://foo.org/test#dpB']));
      $this->assertTrue(count($resultset['unspecified']['http://foo.org/test#dpB']) == 5);

      unset($ontologyRead);
      unset($settings);   
    }    

    public function test_getSuperProperties_Object_Mode_Uris_Direct_Reasoner_Enabled() {
      
      $settings = new Config();  
      
      $ontologyRead = new OntologyReadQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
      
      $getSuperProperties = new getSuperPropertiesFunction();
      
      $getSuperProperties->uri('http://foo.org/test#opC')
                       ->directSuperProperties()
                       ->getObjectProperties()
                       ->getPropertiesUris();
      
      $ontologyRead->enableReasoner()
                   ->mime('resultset')
                   ->getSuperProperties($getSuperProperties)
                   ->ontology($settings->testOntologyUri)
                   ->sourceInterface($settings->ontologyReadInterface)
                   ->sourceInterfaceVersion($settings->ontologyReadInterfaceVersion)
                   ->send();
      
      $this->assertEquals($ontologyRead->getStatus(), "200", "Debugging information: ".var_export($ontologyRead, TRUE));                                       

      $resultset = $ontologyRead->getResultset()->getResultset();

      $this->assertTrue(count($resultset['unspecified']) == 1);
      $this->assertTrue(isset($resultset['unspecified']['http://foo.org/test#opB']));
      $this->assertTrue(count($resultset['unspecified']['http://foo.org/test#opB']) == 1);

      unset($ontologyRead);
      unset($settings);   
    }  
    
    public function test_getSuperProperties_Object_Mode_Uris_All_Reasoner_Enabled() {
      
      $settings = new Config();  
      
      $ontologyRead = new OntologyReadQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
      
      $getSuperProperties = new getSuperPropertiesFunction();
      
      $getSuperProperties->uri('http://foo.org/test#opC')
                       ->allSuperProperties()
                       ->getObjectProperties()
                       ->getPropertiesUris();
      
      $ontologyRead->enableReasoner()
                   ->mime('resultset')
                   ->getSuperProperties($getSuperProperties)
                   ->ontology($settings->testOntologyUri)
                   ->sourceInterface($settings->ontologyReadInterface)
                   ->sourceInterfaceVersion($settings->ontologyReadInterfaceVersion)
                   ->send();
      
      $this->assertEquals($ontologyRead->getStatus(), "200", "Debugging information: ".var_export($ontologyRead, TRUE));                                       

      $resultset = $ontologyRead->getResultset()->getResultset();

      $this->assertTrue(count($resultset['unspecified']) == 3);
      $this->assertTrue(isset($resultset['unspecified']['http://foo.org/test#opB']));
      $this->assertTrue(count($resultset['unspecified']['http://foo.org/test#opB']) == 1);
      $this->assertTrue(isset($resultset['unspecified']['http://foo.org/test#opA']));
      $this->assertTrue(count($resultset['unspecified']['http://foo.org/test#opA']) == 1);
      $this->assertTrue(isset($resultset['unspecified']['http://www.w3.org/2002/07/owl#topObjectProperty']));
      $this->assertTrue(count($resultset['unspecified']['http://www.w3.org/2002/07/owl#topObjectProperty']) == 1);

      unset($ontologyRead);
      unset($settings);   
    }
    
    public function test_getSuperProperties_Object_Mode_Descriptions_Direct_Reasoner_Enabled() {
      
      $settings = new Config();  
      
      $ontologyRead = new OntologyReadQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
      
      $getSuperProperties = new getSuperPropertiesFunction();
      
      $getSuperProperties->uri('http://foo.org/test#opC')
                       ->directSuperProperties()
                       ->getObjectProperties()
                       ->getPropertiesDescriptions();
      
      $ontologyRead->enableReasoner()
                   ->mime('resultset')
                   ->getSuperProperties($getSuperProperties)
                   ->ontology($settings->testOntologyUri)
                   ->sourceInterface($settings->ontologyReadInterface)
                   ->sourceInterfaceVersion($settings->ontologyReadInterfaceVersion)
                   ->send();
      
      $this->assertEquals($ontologyRead->getStatus(), "200", "Debugging information: ".var_export($ontologyRead, TRUE));                                       

      $resultset = $ontologyRead->getResultset()->getResultset();

      $this->assertTrue(count($resultset['unspecified']) == 1);
      $this->assertTrue(isset($resultset['unspecified']['http://foo.org/test#opB']));
      $this->assertTrue(count($resultset['unspecified']['http://foo.org/test#opB']) == 7);

      unset($ontologyRead);
      unset($settings);   
    }  
    
    public function test_getSuperProperties_Object_Mode_Descriptions_All_Reasoner_Enabled() {
      
      $settings = new Config();  
      
      $ontologyRead = new OntologyReadQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
      
      $getSuperProperties = new getSuperPropertiesFunction();
      
      $getSuperProperties->uri('http://foo.org/test#opC')
                       ->allSuperProperties()
                       ->getObjectProperties()
                       ->getPropertiesDescriptions();
      
      $ontologyRead->enableReasoner()
                   ->mime('resultset')
                   ->getSuperProperties($getSuperProperties)
                   ->ontology($settings->testOntologyUri)
                   ->sourceInterface($settings->ontologyReadInterface)
                   ->sourceInterfaceVersion($settings->ontologyReadInterfaceVersion)
                   ->send();
      
      $this->assertEquals($ontologyRead->getStatus(), "200", "Debugging information: ".var_export($ontologyRead, TRUE));                                       

      $resultset = $ontologyRead->getResultset()->getResultset();

      $this->assertTrue(count($resultset['unspecified']) == 3);
      $this->assertTrue(isset($resultset['unspecified']['http://foo.org/test#opB']));
      $this->assertTrue(count($resultset['unspecified']['http://foo.org/test#opB']) == 7);
      $this->assertTrue(isset($resultset['unspecified']['http://foo.org/test#opA']));
      $this->assertTrue(count($resultset['unspecified']['http://foo.org/test#opA']) == 6);
      $this->assertTrue(isset($resultset['unspecified']['http://www.w3.org/2002/07/owl#topObjectProperty']));
      $this->assertTrue(count($resultset['unspecified']['http://www.w3.org/2002/07/owl#topObjectProperty']) == 3);
      
      unset($ontologyRead);
      unset($settings);   
    }    
  
    public function test_getSuperProperties_Object_Mode_Uris_Direct_Reasoner_Disabled() {
      
      $settings = new Config();  
      
      $ontologyRead = new OntologyReadQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
      
      $getSuperProperties = new getSuperPropertiesFunction();
      
      $getSuperProperties->uri('http://foo.org/test#opC')
                       ->directSuperProperties()
                       ->getObjectProperties()
                       ->getPropertiesUris();
      
      $ontologyRead->disableReasoner()
                   ->mime('resultset')
                   ->getSuperProperties($getSuperProperties)
                   ->ontology($settings->testOntologyUri)
                   ->sourceInterface($settings->ontologyReadInterface)
                   ->sourceInterfaceVersion($settings->ontologyReadInterfaceVersion)
                   ->send();
      
      $this->assertEquals($ontologyRead->getStatus(), "200", "Debugging information: ".var_export($ontologyRead, TRUE));                                       

      $resultset = $ontologyRead->getResultset()->getResultset();

      $this->assertTrue(count($resultset['unspecified']) == 1);
      $this->assertTrue(isset($resultset['unspecified']['http://foo.org/test#opB']));
      $this->assertTrue(count($resultset['unspecified']['http://foo.org/test#opB']) == 1);

      unset($ontologyRead);
      unset($settings);   
    }  
    
    public function test_getSuperProperties_Object_Mode_Uris_All_Reasoner_Disabled() {
      
      $settings = new Config();  
      
      $ontologyRead = new OntologyReadQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
      
      $getSuperProperties = new getSuperPropertiesFunction();
      
      $getSuperProperties->uri('http://foo.org/test#opC')
                       ->allSuperProperties()
                       ->getObjectProperties()
                       ->getPropertiesUris();
      
      $ontologyRead->disableReasoner()
                   ->mime('resultset')
                   ->getSuperProperties($getSuperProperties)
                   ->ontology($settings->testOntologyUri)
                   ->sourceInterface($settings->ontologyReadInterface)
                   ->sourceInterfaceVersion($settings->ontologyReadInterfaceVersion)
                   ->send();
      
      $this->assertEquals($ontologyRead->getStatus(), "200", "Debugging information: ".var_export($ontologyRead, TRUE));                                       

      $resultset = $ontologyRead->getResultset()->getResultset();

      $this->assertTrue(count($resultset['unspecified']) == 1);
      $this->assertTrue(isset($resultset['unspecified']['http://foo.org/test#opB']));
      $this->assertTrue(count($resultset['unspecified']['http://foo.org/test#opB']) == 1);

      unset($ontologyRead);
      unset($settings);   
    }
    
   public function test_getSuperProperties_Object_Mode_Descriptions_Direct_Reasoner_Disabled() {
      
      $settings = new Config();  
      
      $ontologyRead = new OntologyReadQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
      
      $getSuperProperties = new getSuperPropertiesFunction();
      
      $getSuperProperties->uri('http://foo.org/test#opC')
                       ->directSuperProperties()
                       ->getObjectProperties()
                       ->getPropertiesDescriptions();
      
      $ontologyRead->disableReasoner()
                   ->mime('resultset')
                   ->getSuperProperties($getSuperProperties)
                   ->ontology($settings->testOntologyUri)
                   ->sourceInterface($settings->ontologyReadInterface)
                   ->sourceInterfaceVersion($settings->ontologyReadInterfaceVersion)
                   ->send();
      
      $this->assertEquals($ontologyRead->getStatus(), "200", "Debugging information: ".var_export($ontologyRead, TRUE));                                       

      $resultset = $ontologyRead->getResultset()->getResultset();

      $this->assertTrue(count($resultset['unspecified']) == 1);
      $this->assertTrue(isset($resultset['unspecified']['http://foo.org/test#opB']));
      $this->assertTrue(count($resultset['unspecified']['http://foo.org/test#opB']) == 7);

      unset($ontologyRead);
      unset($settings);   
    }  
    
    public function test_getSuperProperties_Object_Mode_Descriptions_All_Reasoner_Disabled() {
      
      $settings = new Config();  
      
      $ontologyRead = new OntologyReadQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
      
      $getSuperProperties = new getSuperPropertiesFunction();
      
      $getSuperProperties->uri('http://foo.org/test#opC')
                       ->allSuperProperties()
                       ->getObjectProperties()
                       ->getPropertiesDescriptions();
      
      $ontologyRead->disableReasoner()
                   ->mime('resultset')
                   ->getSuperProperties($getSuperProperties)
                   ->ontology($settings->testOntologyUri)
                   ->sourceInterface($settings->ontologyReadInterface)
                   ->sourceInterfaceVersion($settings->ontologyReadInterfaceVersion)
                   ->send();
      
      $this->assertEquals($ontologyRead->getStatus(), "200", "Debugging information: ".var_export($ontologyRead, TRUE));                                       

      $resultset = $ontologyRead->getResultset()->getResultset();

      $this->assertTrue(count($resultset['unspecified']) == 1);
      $this->assertTrue(isset($resultset['unspecified']['http://foo.org/test#opB']));
      $this->assertTrue(count($resultset['unspecified']['http://foo.org/test#opB']) == 7);

      unset($ontologyRead);
      unset($settings);   
    }
    
    public function test_getDisjointProperties_Datatype_Mode_Uris_Reasoner_Enabled() {
      
      $settings = new Config();  
      
      $ontologyRead = new OntologyReadQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
      
      $getDisjointProperties = new getDisjointPropertiesFunction();
      
      $getDisjointProperties->uri('http://foo.org/test#dpG')
                       ->getDatatypeProperties()
                       ->getPropertiesUris();
      
      $ontologyRead->enableReasoner()
                   ->mime('resultset')
                   ->getDisjointProperties($getDisjointProperties)
                   ->ontology($settings->testOntologyUri)
                   ->sourceInterface($settings->ontologyReadInterface)
                   ->sourceInterfaceVersion($settings->ontologyReadInterfaceVersion)
                   ->send();
      
      $this->assertEquals($ontologyRead->getStatus(), "200", "Debugging information: ".var_export($ontologyRead, TRUE));                                       

      $resultset = $ontologyRead->getResultset()->getResultset();

      $this->assertTrue(count($resultset['unspecified']) == 2);
      $this->assertTrue(isset($resultset['unspecified']['http://foo.org/test#dpF']));
      $this->assertTrue(count($resultset['unspecified']['http://foo.org/test#dpF']) == 1);
      $this->assertTrue(isset($resultset['unspecified']['http://www.w3.org/2002/07/owl#bottomDataProperty']));
      $this->assertTrue(count($resultset['unspecified']['http://www.w3.org/2002/07/owl#bottomDataProperty']) == 1);

      unset($ontologyRead);
      unset($settings);   
    }  
    
    public function test_getDisjointProperties_Datatype_Mode_Descriptions_Reasoner_Enabled() {
      
      $settings = new Config();  
      
      $ontologyRead = new OntologyReadQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
      
      $getDisjointProperties = new getDisjointPropertiesFunction();
      
      $getDisjointProperties->uri('http://foo.org/test#dpG')
                       ->getDatatypeProperties()
                       ->getPropertiesDescriptions();
      
      $ontologyRead->enableReasoner()
                   ->mime('resultset')
                   ->getDisjointProperties($getDisjointProperties)
                   ->ontology($settings->testOntologyUri)
                   ->sourceInterface($settings->ontologyReadInterface)
                   ->sourceInterfaceVersion($settings->ontologyReadInterfaceVersion)
                   ->send();
      
      $this->assertEquals($ontologyRead->getStatus(), "200", "Debugging information: ".var_export($ontologyRead, TRUE));                                       

      $resultset = $ontologyRead->getResultset()->getResultset();

      $this->assertTrue(count($resultset['unspecified']) == 2);
      $this->assertTrue(isset($resultset['unspecified']['http://foo.org/test#dpF']));
      $this->assertTrue(count($resultset['unspecified']['http://foo.org/test#dpF']) == 5);
      $this->assertTrue(isset($resultset['unspecified']['http://www.w3.org/2002/07/owl#bottomDataProperty']));
      $this->assertTrue(count($resultset['unspecified']['http://www.w3.org/2002/07/owl#bottomDataProperty']) == 2);
      
      unset($ontologyRead);
      unset($settings);   
    }     
  
    public function test_getDisjointProperties_Datatype_Mode_Uris_Reasoner_Disabled() {
      
      $settings = new Config();  
      
      $ontologyRead = new OntologyReadQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
      
      $getDisjointProperties = new getDisjointPropertiesFunction();
      
      $getDisjointProperties->uri('http://foo.org/test#dpG')
                       ->getDatatypeProperties()
                       ->getPropertiesUris();
      
      $ontologyRead->disableReasoner()
                   ->mime('resultset')
                   ->getDisjointProperties($getDisjointProperties)
                   ->ontology($settings->testOntologyUri)
                   ->sourceInterface($settings->ontologyReadInterface)
                   ->sourceInterfaceVersion($settings->ontologyReadInterfaceVersion)
                   ->send();
      
      $this->assertEquals($ontologyRead->getStatus(), "200", "Debugging information: ".var_export($ontologyRead, TRUE));                                       

      $resultset = $ontologyRead->getResultset()->getResultset();

      $this->assertTrue(count($resultset['unspecified']) == 1);
      $this->assertTrue(isset($resultset['unspecified']['http://foo.org/test#dpF']));
      $this->assertTrue(count($resultset['unspecified']['http://foo.org/test#dpF']) == 1);
      
      unset($ontologyRead);
      unset($settings);   
    }  
    
    public function test_getDisjointProperties_Datatype_Mode_Descriptions_Reasoner_Disabled() {
      
      $settings = new Config();  
      
      $ontologyRead = new OntologyReadQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
      
      $getDisjointProperties = new getDisjointPropertiesFunction();
      
      $getDisjointProperties->uri('http://foo.org/test#dpG')
                       ->getDatatypeProperties()
                       ->getPropertiesDescriptions();
      
      $ontologyRead->disableReasoner()
                   ->mime('resultset')
                   ->getDisjointProperties($getDisjointProperties)
                   ->ontology($settings->testOntologyUri)
                   ->sourceInterface($settings->ontologyReadInterface)
                   ->sourceInterfaceVersion($settings->ontologyReadInterfaceVersion)
                   ->send();
      
      $this->assertEquals($ontologyRead->getStatus(), "200", "Debugging information: ".var_export($ontologyRead, TRUE));                                       

      $resultset = $ontologyRead->getResultset()->getResultset();

      $this->assertTrue(count($resultset['unspecified']) == 1);
      $this->assertTrue(isset($resultset['unspecified']['http://foo.org/test#dpF']));
      $this->assertTrue(count($resultset['unspecified']['http://foo.org/test#dpF']) == 5);

      unset($ontologyRead);
      unset($settings);   
    }  

    public function test_getDisjointProperties_Object_Mode_Uris_Reasoner_Enabled() {
      
      $settings = new Config();  
      
      $ontologyRead = new OntologyReadQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
      
      $getDisjointProperties = new getDisjointPropertiesFunction();
      
      $getDisjointProperties->uri('http://foo.org/test#opG')
                       ->getObjectProperties()
                       ->getPropertiesUris();
      
      $ontologyRead->enableReasoner()
                   ->mime('resultset')
                   ->getDisjointProperties($getDisjointProperties)
                   ->ontology($settings->testOntologyUri)
                   ->sourceInterface($settings->ontologyReadInterface)
                   ->sourceInterfaceVersion($settings->ontologyReadInterfaceVersion)
                   ->send();
      
      $this->assertEquals($ontologyRead->getStatus(), "200", "Debugging information: ".var_export($ontologyRead, TRUE));                                       

      $resultset = $ontologyRead->getResultset()->getResultset();

      $this->assertTrue(count($resultset['unspecified']) == 2);
      $this->assertTrue(isset($resultset['unspecified']['http://foo.org/test#opF']));
      $this->assertTrue(count($resultset['unspecified']['http://foo.org/test#opF']) == 1);
      $this->assertTrue(isset($resultset['unspecified']['http://www.w3.org/2002/07/owl#bottomObjectProperty']));
      $this->assertTrue(count($resultset['unspecified']['http://www.w3.org/2002/07/owl#bottomObjectProperty']) == 1);

      unset($ontologyRead);
      unset($settings);   
    }  
    
    public function test_getDisjointProperties_Object_Mode_Descriptions_Reasoner_Enabled() {
      
      $settings = new Config();  
      
      $ontologyRead = new OntologyReadQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
      
      $getDisjointProperties = new getDisjointPropertiesFunction();
      
      $getDisjointProperties->uri('http://foo.org/test#opG')
                       ->getObjectProperties()
                       ->getPropertiesDescriptions();
      
      $ontologyRead->enableReasoner()
                   ->mime('resultset')
                   ->getDisjointProperties($getDisjointProperties)
                   ->ontology($settings->testOntologyUri)
                   ->sourceInterface($settings->ontologyReadInterface)
                   ->sourceInterfaceVersion($settings->ontologyReadInterfaceVersion)
                   ->send();
      
      $this->assertEquals($ontologyRead->getStatus(), "200", "Debugging information: ".var_export($ontologyRead, TRUE));                                       

      $resultset = $ontologyRead->getResultset()->getResultset();

      $this->assertTrue(count($resultset['unspecified']) == 2);
      $this->assertTrue(isset($resultset['unspecified']['http://foo.org/test#opF']));
      $this->assertTrue(count($resultset['unspecified']['http://foo.org/test#opF']) == 5);
      $this->assertTrue(isset($resultset['unspecified']['http://www.w3.org/2002/07/owl#bottomObjectProperty']));
      $this->assertTrue(count($resultset['unspecified']['http://www.w3.org/2002/07/owl#bottomObjectProperty']) == 2);

      unset($ontologyRead);
      unset($settings);   
    }  
  
    public function test_getDisjointProperties_Object_Mode_Uris_Reasoner_Disabled() {
      
      $settings = new Config();  
      
      $ontologyRead = new OntologyReadQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
      
      $getDisjointProperties = new getDisjointPropertiesFunction();
      
      $getDisjointProperties->uri('http://foo.org/test#opG')
                       ->getObjectProperties()
                       ->getPropertiesUris();
      
      $ontologyRead->disableReasoner()
                   ->mime('resultset')
                   ->getDisjointProperties($getDisjointProperties)
                   ->ontology($settings->testOntologyUri)
                   ->sourceInterface($settings->ontologyReadInterface)
                   ->sourceInterfaceVersion($settings->ontologyReadInterfaceVersion)
                   ->send();
      
      $this->assertEquals($ontologyRead->getStatus(), "200", "Debugging information: ".var_export($ontologyRead, TRUE));                                       

      $resultset = $ontologyRead->getResultset()->getResultset();

      $this->assertTrue(count($resultset['unspecified']) == 1);
      $this->assertTrue(isset($resultset['unspecified']['http://foo.org/test#opF']));
      $this->assertTrue(count($resultset['unspecified']['http://foo.org/test#opF']) == 1);

      unset($ontologyRead);
      unset($settings);   
    }  
    
   public function test_getDisjointProperties_Object_Mode_Descriptions_Reasoner_Disabled() {
      
      $settings = new Config();  
      
      $ontologyRead = new OntologyReadQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
      
      $getDisjointProperties = new GetDisjointPropertiesFunction();
      
      $getDisjointProperties->uri('http://foo.org/test#opG')
                       ->getObjectProperties()
                       ->getPropertiesDescriptions();
      
      $ontologyRead->disableReasoner()
                   ->mime('resultset')
                   ->getDisjointProperties($getDisjointProperties)
                   ->ontology($settings->testOntologyUri)
                   ->sourceInterface($settings->ontologyReadInterface)
                   ->sourceInterfaceVersion($settings->ontologyReadInterfaceVersion)
                   ->send();
      
      $this->assertEquals($ontologyRead->getStatus(), "200", "Debugging information: ".var_export($ontologyRead, TRUE));                                       

      $resultset = $ontologyRead->getResultset()->getResultset();

      $this->assertTrue(count($resultset['unspecified']) == 1);
      $this->assertTrue(isset($resultset['unspecified']['http://foo.org/test#opF']));
      $this->assertTrue(count($resultset['unspecified']['http://foo.org/test#opF']) == 5);

      unset($ontologyRead);
      unset($settings);   
    }  

    public function test_getEquivalentProperties_Datatype_Mode_Uris_Reasoner_Enabled() {
      
      $settings = new Config();  
      
      $ontologyRead = new OntologyReadQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
      
      $getEquivalentProperties = new getEquivalentPropertiesFunction();
      
      $getEquivalentProperties->uri('http://foo.org/test#dpD')
                       ->getDatatypeProperties()
                       ->getPropertiesUris();
      
      $ontologyRead->enableReasoner()
                   ->mime('resultset')
                   ->getEquivalentProperties($getEquivalentProperties)
                   ->ontology($settings->testOntologyUri)
                   ->sourceInterface($settings->ontologyReadInterface)
                   ->sourceInterfaceVersion($settings->ontologyReadInterfaceVersion)
                   ->send();
      
      $this->assertEquals($ontologyRead->getStatus(), "200", "Debugging information: ".var_export($ontologyRead, TRUE));                                       

      $resultset = $ontologyRead->getResultset()->getResultset();

      $this->assertTrue(count($resultset['unspecified']) == 1);
      $this->assertTrue(isset($resultset['unspecified']['http://foo.org/test#dpE']));
      $this->assertTrue(count($resultset['unspecified']['http://foo.org/test#dpE']) == 1);

      unset($ontologyRead);
      unset($settings);   
    }  
    
    public function test_getEquivalentProperties_Datatype_Mode_Descriptions_Reasoner_Enabled() {
      
      $settings = new Config();  
      
      $ontologyRead = new OntologyReadQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
      
      $getEquivalentProperties = new getEquivalentPropertiesFunction();
      
      $getEquivalentProperties->uri('http://foo.org/test#dpD')
                       ->getDatatypeProperties()
                       ->getPropertiesDescriptions();
      
      $ontologyRead->enableReasoner()
                   ->mime('resultset')
                   ->getEquivalentProperties($getEquivalentProperties)
                   ->ontology($settings->testOntologyUri)
                   ->sourceInterface($settings->ontologyReadInterface)
                   ->sourceInterfaceVersion($settings->ontologyReadInterfaceVersion)
                   ->send();
      
      $this->assertEquals($ontologyRead->getStatus(), "200", "Debugging information: ".var_export($ontologyRead, TRUE));                                       

      $resultset = $ontologyRead->getResultset()->getResultset();

      $this->assertTrue(count($resultset['unspecified']) == 1);
      $this->assertTrue(isset($resultset['unspecified']['http://foo.org/test#dpE']));
      $this->assertTrue(count($resultset['unspecified']['http://foo.org/test#dpE']) == 5);
      
      unset($ontologyRead);
      unset($settings);   
    }     
  
    public function test_getEquivalentProperties_Datatype_Mode_Uris_Reasoner_Disabled() {
      
      $settings = new Config();  
      
      $ontologyRead = new OntologyReadQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
      
      $getEquivalentProperties = new getEquivalentPropertiesFunction();
      
      $getEquivalentProperties->uri('http://foo.org/test#dpD')
                       ->getDatatypeProperties()
                       ->getPropertiesUris();
      
      $ontologyRead->disableReasoner()
                   ->mime('resultset')
                   ->getEquivalentProperties($getEquivalentProperties)
                   ->ontology($settings->testOntologyUri)
                   ->sourceInterface($settings->ontologyReadInterface)
                   ->sourceInterfaceVersion($settings->ontologyReadInterfaceVersion)
                   ->send();
      
      $this->assertEquals($ontologyRead->getStatus(), "200", "Debugging information: ".var_export($ontologyRead, TRUE));                                       

      $resultset = $ontologyRead->getResultset()->getResultset();

      $this->assertTrue(count($resultset['unspecified']) == 1);
      $this->assertTrue(isset($resultset['unspecified']['http://foo.org/test#dpE']));
      $this->assertTrue(count($resultset['unspecified']['http://foo.org/test#dpE']) == 1);
      
      unset($ontologyRead);
      unset($settings);   
    }  
    
    public function test_getEquivalentProperties_Datatype_Mode_Descriptions_Reasoner_Disabled() {
      
      $settings = new Config();  
      
      $ontologyRead = new OntologyReadQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
      
      $getEquivalentProperties = new getEquivalentPropertiesFunction();
      
      $getEquivalentProperties->uri('http://foo.org/test#dpD')
                       ->getDatatypeProperties()
                       ->getPropertiesDescriptions();
      
      $ontologyRead->disableReasoner()
                   ->mime('resultset')
                   ->getEquivalentProperties($getEquivalentProperties)
                   ->ontology($settings->testOntologyUri)
                   ->sourceInterface($settings->ontologyReadInterface)
                   ->sourceInterfaceVersion($settings->ontologyReadInterfaceVersion)
                   ->send();
      
      $this->assertEquals($ontologyRead->getStatus(), "200", "Debugging information: ".var_export($ontologyRead, TRUE));                                       

      $resultset = $ontologyRead->getResultset()->getResultset();

      $this->assertTrue(count($resultset['unspecified']) == 1);
      $this->assertTrue(isset($resultset['unspecified']['http://foo.org/test#dpE']));
      $this->assertTrue(count($resultset['unspecified']['http://foo.org/test#dpE']) == 5);

      unset($ontologyRead);
      unset($settings);   
    }  

    public function test_getEquivalentProperties_Object_Mode_Uris_Reasoner_Enabled() {
      
      $settings = new Config();  
      
      $ontologyRead = new OntologyReadQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
      
      $getEquivalentProperties = new getEquivalentPropertiesFunction();
      
      $getEquivalentProperties->uri('http://foo.org/test#opD')
                       ->getObjectProperties()
                       ->getPropertiesUris();
      
      $ontologyRead->enableReasoner()
                   ->mime('resultset')
                   ->getEquivalentProperties($getEquivalentProperties)
                   ->ontology($settings->testOntologyUri)
                   ->sourceInterface($settings->ontologyReadInterface)
                   ->sourceInterfaceVersion($settings->ontologyReadInterfaceVersion)
                   ->send();
      
      $this->assertEquals($ontologyRead->getStatus(), "200", "Debugging information: ".var_export($ontologyRead, TRUE));                                       

      $resultset = $ontologyRead->getResultset()->getResultset();

      $this->assertTrue(count($resultset['unspecified']) == 1);
      $this->assertTrue(isset($resultset['unspecified']['http://foo.org/test#opE']));
      $this->assertTrue(count($resultset['unspecified']['http://foo.org/test#opE']) == 1);

      unset($ontologyRead);
      unset($settings);   
    }  
    
    public function test_getEquivalentProperties_Object_Mode_Descriptions_Reasoner_Enabled() {
      
      $settings = new Config();  
      
      $ontologyRead = new OntologyReadQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
      
      $getEquivalentProperties = new getEquivalentPropertiesFunction();
      
      $getEquivalentProperties->uri('http://foo.org/test#opD')
                       ->getObjectProperties()
                       ->getPropertiesDescriptions();
      
      $ontologyRead->enableReasoner()
                   ->mime('resultset')
                   ->getEquivalentProperties($getEquivalentProperties)
                   ->ontology($settings->testOntologyUri)
                   ->sourceInterface($settings->ontologyReadInterface)
                   ->sourceInterfaceVersion($settings->ontologyReadInterfaceVersion)
                   ->send();
      
      $this->assertEquals($ontologyRead->getStatus(), "200", "Debugging information: ".var_export($ontologyRead, TRUE));                                       

      $resultset = $ontologyRead->getResultset()->getResultset();

      $this->assertTrue(count($resultset['unspecified']) == 1);
      $this->assertTrue(isset($resultset['unspecified']['http://foo.org/test#opE']));
      $this->assertTrue(count($resultset['unspecified']['http://foo.org/test#opE']) == 5);

      unset($ontologyRead);
      unset($settings);   
    }  
  
    public function test_getEquivalentProperties_Object_Mode_Uris_Reasoner_Disabled() {
      
      $settings = new Config();  
      
      $ontologyRead = new OntologyReadQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
      
      $getEquivalentProperties = new getEquivalentPropertiesFunction();
      
      $getEquivalentProperties->uri('http://foo.org/test#opD')
                       ->getObjectProperties()
                       ->getPropertiesUris();
      
      $ontologyRead->disableReasoner()
                   ->mime('resultset')
                   ->getEquivalentProperties($getEquivalentProperties)
                   ->ontology($settings->testOntologyUri)
                   ->sourceInterface($settings->ontologyReadInterface)
                   ->sourceInterfaceVersion($settings->ontologyReadInterfaceVersion)
                   ->send();
      
      $this->assertEquals($ontologyRead->getStatus(), "200", "Debugging information: ".var_export($ontologyRead, TRUE));                                       

      $resultset = $ontologyRead->getResultset()->getResultset();

      $this->assertTrue(count($resultset['unspecified']) == 1);
      $this->assertTrue(isset($resultset['unspecified']['http://foo.org/test#opE']));
      $this->assertTrue(count($resultset['unspecified']['http://foo.org/test#opE']) == 1);

      unset($ontologyRead);
      unset($settings);   
    }  
    
   public function test_getEquivalentProperties_Object_Mode_Descriptions_Reasoner_Disabled() {
      
      $settings = new Config();  
      
      $ontologyRead = new OntologyReadQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
      
      $getEquivalentProperties = new getEquivalentPropertiesFunction();
      
      $getEquivalentProperties->uri('http://foo.org/test#opD')
                       ->getObjectProperties()
                       ->getPropertiesDescriptions();
      
      $ontologyRead->disableReasoner()
                   ->mime('resultset')
                   ->getEquivalentProperties($getEquivalentProperties)
                   ->ontology($settings->testOntologyUri)
                   ->sourceInterface($settings->ontologyReadInterface)
                   ->sourceInterfaceVersion($settings->ontologyReadInterfaceVersion)
                   ->send();
      
      $this->assertEquals($ontologyRead->getStatus(), "200", "Debugging information: ".var_export($ontologyRead, TRUE));                                       

      $resultset = $ontologyRead->getResultset()->getResultset();

      $this->assertTrue(count($resultset['unspecified']) == 1);
      $this->assertTrue(isset($resultset['unspecified']['http://foo.org/test#opE']));
      $this->assertTrue(count($resultset['unspecified']['http://foo.org/test#opE']) == 5);

      unset($ontologyRead);
      unset($settings);   
    }  

    public function test_getNamedIndividual_RDFXML() {
      
      $settings = new Config();  
      
      $ontologyRead = new OntologyReadQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
      
      $getNamedIndividual = new GetNamedIndividualFunction();
      
      $getNamedIndividual->uri('http://foo.org/test#niA');
      
      $ontologyRead->enableReasoner()
                   ->mime('application/rdf+xml')
                   ->getNamedIndividual($getNamedIndividual)
                   ->ontology($settings->testOntologyUri)
                   ->sourceInterface($settings->ontologyReadInterface)
                   ->sourceInterfaceVersion($settings->ontologyReadInterfaceVersion)
                   ->send();
      
      $this->assertEquals($ontologyRead->getStatus(), "200", "Debugging information: ".var_export($ontologyRead, TRUE));                                       

      utilities\validateParameterApplicationRdfXml($this, $ontologyRead);

      $this->assertTrue(utilities\compareRdf($ontologyRead->getResultset(), file_get_contents($settings->contentDir.'validation/ontology_read_named_individual_a.xml'), TRUE));

      unset($ontologyRead);
      unset($settings);   
    }     
       
    public function test_getNamedIndividual_RDFN3() {
      
      $settings = new Config();  
      
      $ontologyRead = new OntologyReadQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
      
      $getNamedIndividual = new GetNamedIndividualFunction();
      
      $getNamedIndividual->uri('http://foo.org/test#niA');
      
      $ontologyRead->enableReasoner()
                   ->mime('application/rdf+n3')
                   ->getNamedIndividual($getNamedIndividual)
                   ->ontology($settings->testOntologyUri)
                   ->sourceInterface($settings->ontologyReadInterface)
                   ->sourceInterfaceVersion($settings->ontologyReadInterfaceVersion)
                   ->send();
      
      $this->assertEquals($ontologyRead->getStatus(), "200", "Debugging information: ".var_export($ontologyRead, TRUE));                                       
      
      utilities\validateParameterApplicationRdfN3($this, $ontologyRead);
      
      $this->assertTrue(utilities\compareRdf($ontologyRead->getResultset(), file_get_contents($settings->contentDir.'validation/ontology_read_named_individual_a.xml'), TRUE));
      
      unset($ontologyRead);
      unset($settings);   
    }            
       
    public function test_getNamedIndividual_structJSON() {
      
      $settings = new Config();  
      
      $ontologyRead = new OntologyReadQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
      
      $getNamedIndividual = new GetNamedIndividualFunction();
      
      $getNamedIndividual->uri('http://foo.org/test#niA');
      
      $ontologyRead->enableReasoner()
                   ->mime('application/json')
                   ->getNamedIndividual($getNamedIndividual)
                   ->ontology($settings->testOntologyUri)
                   ->sourceInterface($settings->ontologyReadInterface)
                   ->sourceInterfaceVersion($settings->ontologyReadInterfaceVersion)
                   ->send();
      
      $this->assertEquals($ontologyRead->getStatus(), "200", "Debugging information: ".var_export($ontologyRead, TRUE));                                       
      
      utilities\validateParameterApplicationJson($this, $ontologyRead);
            
      $this->assertTrue(utilities\compareStructJSON($ontologyRead->getResultset(), file_get_contents($settings->contentDir.'validation/ontology_read_named_individual_a.json')));
            
      unset($ontologyRead);
      unset($settings);   
    }     
  
    public function test_getNamedIndividual_structXML() {
      
      $settings = new Config();  
      
      $ontologyRead = new OntologyReadQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
      
      $getNamedIndividual = new GetNamedIndividualFunction();
      
      $getNamedIndividual->uri('http://foo.org/test#niA');
      
      $ontologyRead->enableReasoner()
                   ->mime('text/xml')
                   ->getNamedIndividual($getNamedIndividual)
                   ->ontology($settings->testOntologyUri)
                   ->sourceInterface($settings->ontologyReadInterface)
                   ->sourceInterfaceVersion($settings->ontologyReadInterfaceVersion)
                   ->send();
      
      $this->assertEquals($ontologyRead->getStatus(), "200", "Debugging information: ".var_export($ontologyRead, TRUE));                                       
      
      utilities\validateParameterTextXml($this, $ontologyRead);

      $resultset = new Resultset($settings->endpointUrl);
    
      // To test the structXML serialization, we import it into a Resultset() object and we export as RDF+XML
      // then we compare it to the expected RDF+XML resultset.
      $resultset->importStructXMLResultset($ontologyRead->getResultset());
      
      $actual = str_replace('<iron:prefLabel>B</iron:prefLabel>', '', $resultset->getResultsetRDFXML());

      $this->assertTrue(utilities\compareRdf($actual, file_get_contents($settings->contentDir.'validation/ontology_read_named_individual_a.xml'), TRUE));

      unset($ontologyRead);
      unset($settings);   
    }          
           
    public function test_getNamedIndividual_Resultset() {
      
      $settings = new Config();  
      
      $ontologyRead = new OntologyReadQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
      
      $getNamedIndividual = new GetNamedIndividualFunction();
      
      $getNamedIndividual->uri('http://foo.org/test#niA');
      
      $ontologyRead->enableReasoner()
                   ->mime('resultset')
                   ->getNamedIndividual($getNamedIndividual)
                   ->ontology($settings->testOntologyUri)
                   ->sourceInterface($settings->ontologyReadInterface)
                   ->sourceInterfaceVersion($settings->ontologyReadInterfaceVersion)
                   ->send();
      
      $this->assertEquals($ontologyRead->getStatus(), "200", "Debugging information: ".var_export($ontologyRead, TRUE));                                       
      
      $actual = str_replace('<iron:prefLabel>B</iron:prefLabel>', '', $ontologyRead->getResultset()->getResultsetRDFXML());      
      
      // To test the Resultset we export it as RDF+XML
      // then we compare it to the expected RDF+XML resultset.
      $this->assertTrue(utilities\compareRdf($actual, file_get_contents($settings->contentDir.'validation/ontology_read_named_individual_a.xml'), TRUE));
  
      unset($ontologyRead);
      unset($settings);   
    }    

    public function test_getNamedIndividuals_niD_Mode_Uris_Direct_Limit_1_Offset_0_Reasoner_Enabled() {
      
      $settings = new Config();  
      
      $ontologyRead = new OntologyReadQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
      
      $getNamedIndividuals = new GetNamedIndividualsFunction();
      
      $getNamedIndividuals->classUri('http://foo.org/test#D')
                          ->getNamedIndividualsUris()
                          ->directNamedIndividuals()
                          ->limit(1)
                          ->offset(0);
      
      $ontologyRead->enableReasoner()
                   ->getNamedIndividuals($getNamedIndividuals)
                   ->mime('resultset')
                   ->ontology($settings->testOntologyUri)
                   ->sourceInterface($settings->ontologyReadInterface)
                   ->sourceInterfaceVersion($settings->ontologyReadInterfaceVersion)
                   ->send();
      
      $this->assertEquals($ontologyRead->getStatus(), "200", "Debugging information: ".var_export($ontologyRead, TRUE));                                       

      $resultset = $ontologyRead->getResultset()->getResultset();

      $this->assertTrue(count($resultset['unspecified']) == 1);
      $this->assertTrue(isset($resultset['unspecified']['http://foo.org/test#niD']));
      $this->assertTrue(count($resultset['unspecified']['http://foo.org/test#niD']) == 1);

      unset($ontologyRead);
      unset($settings);   
    }
    
    public function test_getNamedIndividuals_niD_Mode_Uris_Direct_Limit_2_Offset_0_Reasoner_Enabled() {
      
      $settings = new Config();  
      
      $ontologyRead = new OntologyReadQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
      
      $getNamedIndividuals = new GetNamedIndividualsFunction();
      
      $getNamedIndividuals->classUri('http://foo.org/test#D')
                          ->getNamedIndividualsUris()
                          ->directNamedIndividuals()
                          ->limit(2)
                          ->offset(0);
      
      $ontologyRead->enableReasoner()
                   ->getNamedIndividuals($getNamedIndividuals)
                   ->mime('resultset')
                   ->ontology($settings->testOntologyUri)
                   ->sourceInterface($settings->ontologyReadInterface)
                   ->sourceInterfaceVersion($settings->ontologyReadInterfaceVersion)
                   ->send();
      
      $this->assertEquals($ontologyRead->getStatus(), "200", "Debugging information: ".var_export($ontologyRead, TRUE));                                       

      $resultset = $ontologyRead->getResultset()->getResultset();

      $this->assertTrue(count($resultset['unspecified']) == 2);
      $this->assertTrue(isset($resultset['unspecified']['http://foo.org/test#niD']));
      $this->assertTrue(count($resultset['unspecified']['http://foo.org/test#niD']) == 1);
      $this->assertTrue(isset($resultset['unspecified']['http://foo.org/test#niE']));
      $this->assertTrue(count($resultset['unspecified']['http://foo.org/test#niE']) == 1);

      unset($ontologyRead);
      unset($settings);   
    }    
        
    public function test_getNamedIndividuals_niD_Mode_Uris_Direct_Limit_1_Offset_1_Reasoner_Enabled() {
      
      $settings = new Config();  
      
      $ontologyRead = new OntologyReadQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
      
      $getNamedIndividuals = new GetNamedIndividualsFunction();
      
      $getNamedIndividuals->classUri('http://foo.org/test#D')
                          ->getNamedIndividualsUris()
                          ->directNamedIndividuals()
                          ->limit(1)
                          ->offset(1);
      
      $ontologyRead->enableReasoner()
                   ->getNamedIndividuals($getNamedIndividuals)
                   ->mime('resultset')
                   ->ontology($settings->testOntologyUri)
                   ->sourceInterface($settings->ontologyReadInterface)
                   ->sourceInterfaceVersion($settings->ontologyReadInterfaceVersion)
                   ->send();
      
      $this->assertEquals($ontologyRead->getStatus(), "200", "Debugging information: ".var_export($ontologyRead, TRUE));                                       

      $resultset = $ontologyRead->getResultset()->getResultset();

      $this->assertTrue(count($resultset['unspecified']) == 1);
      $this->assertTrue(isset($resultset['unspecified']['http://foo.org/test#niE']));
      $this->assertTrue(count($resultset['unspecified']['http://foo.org/test#niE']) == 1);

      unset($ontologyRead);
      unset($settings);   
    }    
    
    public function test_getNamedIndividuals_niD_Mode_Uris_All_Limit_1_Offset_0_Reasoner_Enabled() {
      
      $settings = new Config();  
      
      $ontologyRead = new OntologyReadQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
      
      $getNamedIndividuals = new GetNamedIndividualsFunction();
      
      $getNamedIndividuals->classUri('http://foo.org/test#D')
                          ->getNamedIndividualsUris()
                          ->allNamedIndividuals()
                          ->limit(1)
                          ->offset(0);
      
      $ontologyRead->enableReasoner()
                   ->getNamedIndividuals($getNamedIndividuals)
                   ->mime('resultset')
                   ->ontology($settings->testOntologyUri)
                   ->sourceInterface($settings->ontologyReadInterface)
                   ->sourceInterfaceVersion($settings->ontologyReadInterfaceVersion)
                   ->send();
      
      $this->assertEquals($ontologyRead->getStatus(), "200", "Debugging information: ".var_export($ontologyRead, TRUE));                                       

      $resultset = $ontologyRead->getResultset()->getResultset();

      $this->assertTrue(count($resultset['unspecified']) == 1);
      $this->assertTrue(isset($resultset['unspecified']['http://foo.org/test#niD']));
      $this->assertTrue(count($resultset['unspecified']['http://foo.org/test#niD']) == 1);

      unset($ontologyRead);
      unset($settings);   
    }
    
    public function test_getNamedIndividuals_niD_Mode_Uris_All_Limit_2_Offset_0_Reasoner_Enabled() {
      
      $settings = new Config();  
      
      $ontologyRead = new OntologyReadQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
      
      $getNamedIndividuals = new GetNamedIndividualsFunction();
      
      $getNamedIndividuals->classUri('http://foo.org/test#D')
                          ->getNamedIndividualsUris()
                          ->allNamedIndividuals()
                          ->limit(2)
                          ->offset(0);
      
      $ontologyRead->enableReasoner()
                   ->getNamedIndividuals($getNamedIndividuals)
                   ->mime('resultset')
                   ->ontology($settings->testOntologyUri)
                   ->sourceInterface($settings->ontologyReadInterface)
                   ->sourceInterfaceVersion($settings->ontologyReadInterfaceVersion)
                   ->send();
      
      $this->assertEquals($ontologyRead->getStatus(), "200", "Debugging information: ".var_export($ontologyRead, TRUE));                                       

      $resultset = $ontologyRead->getResultset()->getResultset();

      $this->assertTrue(count($resultset['unspecified']) == 2);
      $this->assertTrue(isset($resultset['unspecified']['http://foo.org/test#niD']));
      $this->assertTrue(count($resultset['unspecified']['http://foo.org/test#niD']) == 1);
      $this->assertTrue(isset($resultset['unspecified']['http://foo.org/test#niE']));
      $this->assertTrue(count($resultset['unspecified']['http://foo.org/test#niE']) == 1);

      unset($ontologyRead);
      unset($settings);   
    }    
        
    public function test_getNamedIndividuals_niD_Mode_Uris_All_Limit_1_Offset_1_Reasoner_Enabled() {
      
      $settings = new Config();  
      
      $ontologyRead = new OntologyReadQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
      
      $getNamedIndividuals = new GetNamedIndividualsFunction();
      
      $getNamedIndividuals->classUri('http://foo.org/test#D')
                          ->getNamedIndividualsUris()
                          ->allNamedIndividuals()
                          ->limit(1)
                          ->offset(1);
      
      $ontologyRead->enableReasoner()
                   ->getNamedIndividuals($getNamedIndividuals)
                   ->mime('resultset')
                   ->ontology($settings->testOntologyUri)
                   ->sourceInterface($settings->ontologyReadInterface)
                   ->sourceInterfaceVersion($settings->ontologyReadInterfaceVersion)
                   ->send();
      
      $this->assertEquals($ontologyRead->getStatus(), "200", "Debugging information: ".var_export($ontologyRead, TRUE));                                       

      $resultset = $ontologyRead->getResultset()->getResultset();

      $this->assertTrue(count($resultset['unspecified']) == 1);
      $this->assertTrue(isset($resultset['unspecified']['http://foo.org/test#niE']));
      $this->assertTrue(count($resultset['unspecified']['http://foo.org/test#niE']) == 1);

      unset($ontologyRead);
      unset($settings);   
    }       
    
    public function test_getNamedIndividuals_niD_Mode_Descriptions_Direct_Limit_1_Offset_0_Reasoner_Enabled() {
      
      $settings = new Config();  
      
      $ontologyRead = new OntologyReadQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
      
      $getNamedIndividuals = new GetNamedIndividualsFunction();
      
      $getNamedIndividuals->classUri('http://foo.org/test#D')
                          ->getNamedIndividualsDescriptions()
                          ->directNamedIndividuals()
                          ->limit(1)
                          ->offset(0);
      
      $ontologyRead->enableReasoner()
                   ->getNamedIndividuals($getNamedIndividuals)
                   ->mime('resultset')
                   ->ontology($settings->testOntologyUri)
                   ->sourceInterface($settings->ontologyReadInterface)
                   ->sourceInterfaceVersion($settings->ontologyReadInterfaceVersion)
                   ->send();
      
      $this->assertEquals($ontologyRead->getStatus(), "200", "Debugging information: ".var_export($ontologyRead, TRUE));                                       

      $resultset = $ontologyRead->getResultset()->getResultset();

      $this->assertTrue(count($resultset['unspecified']) == 1);
      $this->assertTrue(isset($resultset['unspecified']['http://foo.org/test#niD']));
      $this->assertTrue(count($resultset['unspecified']['http://foo.org/test#niD']) == 2);

      unset($ontologyRead);
      unset($settings);   
    }
    
    public function test_getNamedIndividuals_niD_Mode_Descriptions_Direct_Limit_2_Offset_0_Reasoner_Enabled() {
      
      $settings = new Config();  
      
      $ontologyRead = new OntologyReadQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
      
      $getNamedIndividuals = new GetNamedIndividualsFunction();
      
      $getNamedIndividuals->classUri('http://foo.org/test#D')
                          ->getNamedIndividualsDescriptions()
                          ->directNamedIndividuals()
                          ->limit(2)
                          ->offset(0);
      
      $ontologyRead->enableReasoner()
                   ->getNamedIndividuals($getNamedIndividuals)
                   ->mime('resultset')
                   ->ontology($settings->testOntologyUri)
                   ->sourceInterface($settings->ontologyReadInterface)
                   ->sourceInterfaceVersion($settings->ontologyReadInterfaceVersion)
                   ->send();
      
      $this->assertEquals($ontologyRead->getStatus(), "200", "Debugging information: ".var_export($ontologyRead, TRUE));                                       

      $resultset = $ontologyRead->getResultset()->getResultset();

      $this->assertTrue(count($resultset['unspecified']) == 2);
      $this->assertTrue(isset($resultset['unspecified']['http://foo.org/test#niD']));
      $this->assertTrue(count($resultset['unspecified']['http://foo.org/test#niD']) == 2);
      $this->assertTrue(isset($resultset['unspecified']['http://foo.org/test#niE']));
      $this->assertTrue(count($resultset['unspecified']['http://foo.org/test#niE']) == 3);

      unset($ontologyRead);
      unset($settings);   
    }    
        
    public function test_getNamedIndividuals_niD_Mode_Descriptions_Direct_Limit_1_Offset_1_Reasoner_Enabled() {
      
      $settings = new Config();  
      
      $ontologyRead = new OntologyReadQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
      
      $getNamedIndividuals = new GetNamedIndividualsFunction();
      
      $getNamedIndividuals->classUri('http://foo.org/test#D')
                          ->getNamedIndividualsDescriptions()
                          ->directNamedIndividuals()
                          ->limit(1)
                          ->offset(1);
      
      $ontologyRead->enableReasoner()
                   ->getNamedIndividuals($getNamedIndividuals)
                   ->mime('resultset')
                   ->ontology($settings->testOntologyUri)
                   ->sourceInterface($settings->ontologyReadInterface)
                   ->sourceInterfaceVersion($settings->ontologyReadInterfaceVersion)
                   ->send();
      
      $this->assertEquals($ontologyRead->getStatus(), "200", "Debugging information: ".var_export($ontologyRead, TRUE));                                       

      $resultset = $ontologyRead->getResultset()->getResultset();

      $this->assertTrue(count($resultset['unspecified']) == 1);
      $this->assertTrue(isset($resultset['unspecified']['http://foo.org/test#niE']));
      $this->assertTrue(count($resultset['unspecified']['http://foo.org/test#niE']) == 3);

      unset($ontologyRead);
      unset($settings);   
    }    
    
    public function test_getNamedIndividuals_niD_Mode_Descriptions_All_Limit_1_Offset_0_Reasoner_Enabled() {
      
      $settings = new Config();  
      
      $ontologyRead = new OntologyReadQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
      
      $getNamedIndividuals = new GetNamedIndividualsFunction();
      
      $getNamedIndividuals->classUri('http://foo.org/test#D')
                          ->getNamedIndividualsDescriptions()
                          ->allNamedIndividuals()
                          ->limit(1)
                          ->offset(0);
      
      $ontologyRead->enableReasoner()
                   ->getNamedIndividuals($getNamedIndividuals)
                   ->mime('resultset')
                   ->ontology($settings->testOntologyUri)
                   ->sourceInterface($settings->ontologyReadInterface)
                   ->sourceInterfaceVersion($settings->ontologyReadInterfaceVersion)
                   ->send();
      
      $this->assertEquals($ontologyRead->getStatus(), "200", "Debugging information: ".var_export($ontologyRead, TRUE));                                       

      $resultset = $ontologyRead->getResultset()->getResultset();

      $this->assertTrue(count($resultset['unspecified']) == 1);
      $this->assertTrue(isset($resultset['unspecified']['http://foo.org/test#niD']));
      $this->assertTrue(count($resultset['unspecified']['http://foo.org/test#niD']) == 2);

      unset($ontologyRead);
      unset($settings);   
    }
    
    public function test_getNamedIndividuals_niD_Mode_Descriptions_All_Limit_2_Offset_0_Reasoner_Enabled() {
      
      $settings = new Config();  
      
      $ontologyRead = new OntologyReadQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
      
      $getNamedIndividuals = new GetNamedIndividualsFunction();
      
      $getNamedIndividuals->classUri('http://foo.org/test#D')
                          ->getNamedIndividualsDescriptions()
                          ->allNamedIndividuals()
                          ->limit(2)
                          ->offset(0);
      
      $ontologyRead->enableReasoner()
                   ->getNamedIndividuals($getNamedIndividuals)
                   ->mime('resultset')
                   ->ontology($settings->testOntologyUri)
                   ->sourceInterface($settings->ontologyReadInterface)
                   ->sourceInterfaceVersion($settings->ontologyReadInterfaceVersion)
                   ->send();
      
      $this->assertEquals($ontologyRead->getStatus(), "200", "Debugging information: ".var_export($ontologyRead, TRUE));                                       

      $resultset = $ontologyRead->getResultset()->getResultset();

      $this->assertTrue(count($resultset['unspecified']) == 2);
      $this->assertTrue(isset($resultset['unspecified']['http://foo.org/test#niD']));
      $this->assertTrue(count($resultset['unspecified']['http://foo.org/test#niD']) == 2);
      $this->assertTrue(isset($resultset['unspecified']['http://foo.org/test#niE']));
      $this->assertTrue(count($resultset['unspecified']['http://foo.org/test#niE']) == 3);

      unset($ontologyRead);
      unset($settings);   
    }    
        
    public function test_getNamedIndividuals_niD_Mode_Descriptions_All_Limit_1_Offset_1_Reasoner_Enabled() {
      
      $settings = new Config();  
      
      $ontologyRead = new OntologyReadQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
      
      $getNamedIndividuals = new GetNamedIndividualsFunction();
      
      $getNamedIndividuals->classUri('http://foo.org/test#D')
                          ->getNamedIndividualsDescriptions()
                          ->allNamedIndividuals()
                          ->limit(1)
                          ->offset(1);
      
      $ontologyRead->enableReasoner()
                   ->getNamedIndividuals($getNamedIndividuals)
                   ->mime('resultset')
                   ->ontology($settings->testOntologyUri)
                   ->sourceInterface($settings->ontologyReadInterface)
                   ->sourceInterfaceVersion($settings->ontologyReadInterfaceVersion)
                   ->send();
      
      $this->assertEquals($ontologyRead->getStatus(), "200", "Debugging information: ".var_export($ontologyRead, TRUE));                                       

      $resultset = $ontologyRead->getResultset()->getResultset();

      $this->assertTrue(count($resultset['unspecified']) == 1);
      $this->assertTrue(isset($resultset['unspecified']['http://foo.org/test#niE']));
      $this->assertTrue(count($resultset['unspecified']['http://foo.org/test#niE']) == 3);

      unset($ontologyRead);
      unset($settings);   
    }    

    public function test_getNamedIndividuals_All_Mode_Uris_Direct_Limit_1_Offset_0_Reasoner_Enabled() {
      
      $settings = new Config();  
      
      $ontologyRead = new OntologyReadQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
      
      $getNamedIndividuals = new GetNamedIndividualsFunction();
      
      $getNamedIndividuals->classUri('all')
                          ->getNamedIndividualsUris()
                          ->directNamedIndividuals()
                          ->limit(1)
                          ->offset(0);
      
      $ontologyRead->enableReasoner()
                   ->getNamedIndividuals($getNamedIndividuals)
                   ->mime('resultset')
                   ->ontology($settings->testOntologyUri)
                   ->sourceInterface($settings->ontologyReadInterface)
                   ->sourceInterfaceVersion($settings->ontologyReadInterfaceVersion)
                   ->send();
      
      $this->assertEquals($ontologyRead->getStatus(), "200", "Debugging information: ".var_export($ontologyRead, TRUE));                                       

      $resultset = $ontologyRead->getResultset()->getResultset();

      $this->assertTrue(count($resultset['unspecified']) == 1);
      $this->assertTrue(isset($resultset['unspecified']['http://foo.org/test#niB']));
      $this->assertTrue(count($resultset['unspecified']['http://foo.org/test#niB']) == 1);

      unset($ontologyRead);
      unset($settings);   
    }
    
    public function test_getNamedIndividuals_All_Mode_Uris_Direct_Limit_2_Offset_0_Reasoner_Enabled() {
      
      $settings = new Config();  
      
      $ontologyRead = new OntologyReadQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
      
      $getNamedIndividuals = new GetNamedIndividualsFunction();
      
      $getNamedIndividuals->classUri('all')
                          ->getNamedIndividualsUris()
                          ->directNamedIndividuals()
                          ->limit(2)
                          ->offset(0);
      
      $ontologyRead->enableReasoner()
                   ->getNamedIndividuals($getNamedIndividuals)
                   ->mime('resultset')
                   ->ontology($settings->testOntologyUri)
                   ->sourceInterface($settings->ontologyReadInterface)
                   ->sourceInterfaceVersion($settings->ontologyReadInterfaceVersion)
                   ->send();
      
      $this->assertEquals($ontologyRead->getStatus(), "200", "Debugging information: ".var_export($ontologyRead, TRUE));                                       

      $resultset = $ontologyRead->getResultset()->getResultset();

      $this->assertTrue(count($resultset['unspecified']) == 2);
      $this->assertTrue(isset($resultset['unspecified']['http://foo.org/test#niB']));
      $this->assertTrue(count($resultset['unspecified']['http://foo.org/test#niB']) == 1);
      $this->assertTrue(isset($resultset['unspecified']['http://foo.org/test#niC']));
      $this->assertTrue(count($resultset['unspecified']['http://foo.org/test#niC']) == 1);

      unset($ontologyRead);
      unset($settings);   
    }    
        
    public function test_getNamedIndividuals_All_Mode_Uris_Direct_Limit_1_Offset_1_Reasoner_Enabled() {
      
      $settings = new Config();  
      
      $ontologyRead = new OntologyReadQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
      
      $getNamedIndividuals = new GetNamedIndividualsFunction();
      
      $getNamedIndividuals->classUri('all')
                          ->getNamedIndividualsUris()
                          ->directNamedIndividuals()
                          ->limit(1)
                          ->offset(1);
      
      $ontologyRead->enableReasoner()
                   ->getNamedIndividuals($getNamedIndividuals)
                   ->mime('resultset')
                   ->ontology($settings->testOntologyUri)
                   ->sourceInterface($settings->ontologyReadInterface)
                   ->sourceInterfaceVersion($settings->ontologyReadInterfaceVersion)
                   ->send();
      
      $this->assertEquals($ontologyRead->getStatus(), "200", "Debugging information: ".var_export($ontologyRead, TRUE));                                       

      $resultset = $ontologyRead->getResultset()->getResultset();

      $this->assertTrue(count($resultset['unspecified']) == 1);
      $this->assertTrue(isset($resultset['unspecified']['http://foo.org/test#niC']));
      $this->assertTrue(count($resultset['unspecified']['http://foo.org/test#niC']) == 1);

      unset($ontologyRead);
      unset($settings);   
    }    
    
    public function test_getNamedIndividuals_All_Mode_Uris_All_Limit_1_Offset_0_Reasoner_Enabled() {
      
      $settings = new Config();  
      
      $ontologyRead = new OntologyReadQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
      
      $getNamedIndividuals = new GetNamedIndividualsFunction();
      
      $getNamedIndividuals->classUri('all')
                          ->getNamedIndividualsUris()
                          ->allNamedIndividuals()
                          ->limit(1)
                          ->offset(0);
      
      $ontologyRead->enableReasoner()
                   ->getNamedIndividuals($getNamedIndividuals)
                   ->mime('resultset')
                   ->ontology($settings->testOntologyUri)
                   ->sourceInterface($settings->ontologyReadInterface)
                   ->sourceInterfaceVersion($settings->ontologyReadInterfaceVersion)
                   ->send();
      
      $this->assertEquals($ontologyRead->getStatus(), "200", "Debugging information: ".var_export($ontologyRead, TRUE));                                       

      $resultset = $ontologyRead->getResultset()->getResultset();

      $this->assertTrue(count($resultset['unspecified']) == 1);
      $this->assertTrue(isset($resultset['unspecified']['http://foo.org/test#niB']));
      $this->assertTrue(count($resultset['unspecified']['http://foo.org/test#niB']) == 1);

      unset($ontologyRead);
      unset($settings);   
    }
    
    public function test_getNamedIndividuals_All_Mode_Uris_All_Limit_2_Offset_0_Reasoner_Enabled() {
      
      $settings = new Config();  
      
      $ontologyRead = new OntologyReadQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
      
      $getNamedIndividuals = new GetNamedIndividualsFunction();
      
      $getNamedIndividuals->classUri('all')
                          ->getNamedIndividualsUris()
                          ->allNamedIndividuals()
                          ->limit(2)
                          ->offset(0);
      
      $ontologyRead->enableReasoner()
                   ->getNamedIndividuals($getNamedIndividuals)
                   ->mime('resultset')
                   ->ontology($settings->testOntologyUri)
                   ->sourceInterface($settings->ontologyReadInterface)
                   ->sourceInterfaceVersion($settings->ontologyReadInterfaceVersion)
                   ->send();
      
      $this->assertEquals($ontologyRead->getStatus(), "200", "Debugging information: ".var_export($ontologyRead, TRUE));                                       

      $resultset = $ontologyRead->getResultset()->getResultset();

      $this->assertTrue(count($resultset['unspecified']) == 2);
      $this->assertTrue(isset($resultset['unspecified']['http://foo.org/test#niB']));
      $this->assertTrue(count($resultset['unspecified']['http://foo.org/test#niB']) == 1);
      $this->assertTrue(isset($resultset['unspecified']['http://foo.org/test#niC']));
      $this->assertTrue(count($resultset['unspecified']['http://foo.org/test#niC']) == 1);

      unset($ontologyRead);
      unset($settings);   
    }    
        
    public function test_getNamedIndividuals_All_Mode_Uris_All_Limit_1_Offset_1_Reasoner_Enabled() {
      
      $settings = new Config();  
      
      $ontologyRead = new OntologyReadQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
      
      $getNamedIndividuals = new GetNamedIndividualsFunction();
      
      $getNamedIndividuals->classUri('all')
                          ->getNamedIndividualsUris()
                          ->allNamedIndividuals()
                          ->limit(1)
                          ->offset(1);
      
      $ontologyRead->enableReasoner()
                   ->getNamedIndividuals($getNamedIndividuals)
                   ->mime('resultset')
                   ->ontology($settings->testOntologyUri)
                   ->sourceInterface($settings->ontologyReadInterface)
                   ->sourceInterfaceVersion($settings->ontologyReadInterfaceVersion)
                   ->send();
      
      $this->assertEquals($ontologyRead->getStatus(), "200", "Debugging information: ".var_export($ontologyRead, TRUE));                                       

      $resultset = $ontologyRead->getResultset()->getResultset();

      $this->assertTrue(count($resultset['unspecified']) == 1);
      $this->assertTrue(isset($resultset['unspecified']['http://foo.org/test#niC']));
      $this->assertTrue(count($resultset['unspecified']['http://foo.org/test#niC']) == 1);

      unset($ontologyRead);
      unset($settings);   
    }       
    
    public function test_getNamedIndividuals_All_Mode_Descriptions_Direct_Limit_1_Offset_0_Reasoner_Enabled() {
      
      $settings = new Config();  
      
      $ontologyRead = new OntologyReadQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
      
      $getNamedIndividuals = new GetNamedIndividualsFunction();
      
      $getNamedIndividuals->classUri('all')
                          ->getNamedIndividualsDescriptions()
                          ->directNamedIndividuals()
                          ->limit(1)
                          ->offset(0);
      
      $ontologyRead->enableReasoner()
                   ->getNamedIndividuals($getNamedIndividuals)
                   ->mime('resultset')
                   ->ontology($settings->testOntologyUri)
                   ->sourceInterface($settings->ontologyReadInterface)
                   ->sourceInterfaceVersion($settings->ontologyReadInterfaceVersion)
                   ->send();
      
      $this->assertEquals($ontologyRead->getStatus(), "200", "Debugging information: ".var_export($ontologyRead, TRUE));                                       

      $resultset = $ontologyRead->getResultset()->getResultset();

      $this->assertTrue(count($resultset['unspecified']) == 1);
      $this->assertTrue(isset($resultset['unspecified']['http://foo.org/test#niB']));
      $this->assertTrue(count($resultset['unspecified']['http://foo.org/test#niB']) == 2);

      unset($ontologyRead);
      unset($settings);   
    }
    
    public function test_getNamedIndividuals_All_Mode_Descriptions_Direct_Limit_2_Offset_0_Reasoner_Enabled() {
      
      $settings = new Config();  
      
      $ontologyRead = new OntologyReadQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
      
      $getNamedIndividuals = new GetNamedIndividualsFunction();
      
      $getNamedIndividuals->classUri('all')
                          ->getNamedIndividualsDescriptions()
                          ->directNamedIndividuals()
                          ->limit(2)
                          ->offset(0);
      
      $ontologyRead->enableReasoner()
                   ->getNamedIndividuals($getNamedIndividuals)
                   ->mime('resultset')
                   ->ontology($settings->testOntologyUri)
                   ->sourceInterface($settings->ontologyReadInterface)
                   ->sourceInterfaceVersion($settings->ontologyReadInterfaceVersion)
                   ->send();
      
      $this->assertEquals($ontologyRead->getStatus(), "200", "Debugging information: ".var_export($ontologyRead, TRUE));                                       

      $resultset = $ontologyRead->getResultset()->getResultset();

      $this->assertTrue(count($resultset['unspecified']) == 2);
      $this->assertTrue(isset($resultset['unspecified']['http://foo.org/test#niB']));
      $this->assertTrue(count($resultset['unspecified']['http://foo.org/test#niB']) == 2);
      $this->assertTrue(isset($resultset['unspecified']['http://foo.org/test#niC']));
      $this->assertTrue(count($resultset['unspecified']['http://foo.org/test#niC']) == 2);

      unset($ontologyRead);
      unset($settings);   
    }    
        
    public function test_getNamedIndividuals_All_Mode_Descriptions_Direct_Limit_1_Offset_1_Reasoner_Enabled() {
      
      $settings = new Config();  
      
      $ontologyRead = new OntologyReadQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
      
      $getNamedIndividuals = new GetNamedIndividualsFunction();
      
      $getNamedIndividuals->classUri('all')
                          ->getNamedIndividualsDescriptions()
                          ->directNamedIndividuals()
                          ->limit(1)
                          ->offset(1);
      
      $ontologyRead->enableReasoner()
                   ->getNamedIndividuals($getNamedIndividuals)
                   ->mime('resultset')
                   ->ontology($settings->testOntologyUri)
                   ->sourceInterface($settings->ontologyReadInterface)
                   ->sourceInterfaceVersion($settings->ontologyReadInterfaceVersion)
                   ->send();
      
      $this->assertEquals($ontologyRead->getStatus(), "200", "Debugging information: ".var_export($ontologyRead, TRUE));                                       

      $resultset = $ontologyRead->getResultset()->getResultset();

      $this->assertTrue(count($resultset['unspecified']) == 1);
      $this->assertTrue(isset($resultset['unspecified']['http://foo.org/test#niC']));
      $this->assertTrue(count($resultset['unspecified']['http://foo.org/test#niC']) == 2);

      unset($ontologyRead);
      unset($settings);   
    }    
    
    public function test_getNamedIndividuals_All_Mode_Descriptions_All_Limit_1_Offset_0_Reasoner_Enabled() {
      
      $settings = new Config();  
      
      $ontologyRead = new OntologyReadQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
      
      $getNamedIndividuals = new GetNamedIndividualsFunction();
      
      $getNamedIndividuals->classUri('all')
                          ->getNamedIndividualsDescriptions()
                          ->allNamedIndividuals()
                          ->limit(1)
                          ->offset(0);
      
      $ontologyRead->enableReasoner()
                   ->getNamedIndividuals($getNamedIndividuals)
                   ->mime('resultset')
                   ->ontology($settings->testOntologyUri)
                   ->sourceInterface($settings->ontologyReadInterface)
                   ->sourceInterfaceVersion($settings->ontologyReadInterfaceVersion)
                   ->send();
      
      $this->assertEquals($ontologyRead->getStatus(), "200", "Debugging information: ".var_export($ontologyRead, TRUE));                                       

      $resultset = $ontologyRead->getResultset()->getResultset();

      $this->assertTrue(count($resultset['unspecified']) == 1);
      $this->assertTrue(isset($resultset['unspecified']['http://foo.org/test#niB']));
      $this->assertTrue(count($resultset['unspecified']['http://foo.org/test#niB']) == 2);

      unset($ontologyRead);
      unset($settings);   
    }
    
    public function test_getNamedIndividuals_All_Mode_Descriptions_All_Limit_2_Offset_0_Reasoner_Enabled() {
      
      $settings = new Config();  
      
      $ontologyRead = new OntologyReadQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
      
      $getNamedIndividuals = new GetNamedIndividualsFunction();
      
      $getNamedIndividuals->classUri('all')
                          ->getNamedIndividualsDescriptions()
                          ->allNamedIndividuals()
                          ->limit(2)
                          ->offset(0);
      
      $ontologyRead->enableReasoner()
                   ->getNamedIndividuals($getNamedIndividuals)
                   ->mime('resultset')
                   ->ontology($settings->testOntologyUri)
                   ->sourceInterface($settings->ontologyReadInterface)
                   ->sourceInterfaceVersion($settings->ontologyReadInterfaceVersion)
                   ->send();
      
      $this->assertEquals($ontologyRead->getStatus(), "200", "Debugging information: ".var_export($ontologyRead, TRUE));                                       

      $resultset = $ontologyRead->getResultset()->getResultset();

      $this->assertTrue(count($resultset['unspecified']) == 2);
      $this->assertTrue(isset($resultset['unspecified']['http://foo.org/test#niB']));
      $this->assertTrue(count($resultset['unspecified']['http://foo.org/test#niB']) == 2);
      $this->assertTrue(isset($resultset['unspecified']['http://foo.org/test#niC']));
      $this->assertTrue(count($resultset['unspecified']['http://foo.org/test#niC']) == 2);

      unset($ontologyRead);
      unset($settings);   
    }    
        
    public function test_getNamedIndividuals_All_Mode_Descriptions_All_Limit_1_Offset_1_Reasoner_Enabled() {
      
      $settings = new Config();  
      
      $ontologyRead = new OntologyReadQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
      
      $getNamedIndividuals = new GetNamedIndividualsFunction();
      
      $getNamedIndividuals->classUri('all')
                          ->getNamedIndividualsDescriptions()
                          ->allNamedIndividuals()
                          ->limit(1)
                          ->offset(1);
      
      $ontologyRead->enableReasoner()
                   ->getNamedIndividuals($getNamedIndividuals)
                   ->mime('resultset')
                   ->ontology($settings->testOntologyUri)
                   ->sourceInterface($settings->ontologyReadInterface)
                   ->sourceInterfaceVersion($settings->ontologyReadInterfaceVersion)
                   ->send();
      
      $this->assertEquals($ontologyRead->getStatus(), "200", "Debugging information: ".var_export($ontologyRead, TRUE));                                       

      $resultset = $ontologyRead->getResultset()->getResultset();

      $this->assertTrue(count($resultset['unspecified']) == 1);
      $this->assertTrue(isset($resultset['unspecified']['http://foo.org/test#niC']));
      $this->assertTrue(count($resultset['unspecified']['http://foo.org/test#niC']) == 2);

      unset($ontologyRead);
      unset($settings);   
    }    
    
    public function test_getNamedIndividuals_niD_Mode_Uris_Direct_Limit_1_Offset_0_Reasoner_Disabled() {
      
      $settings = new Config();  
      
      $ontologyRead = new OntologyReadQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
      
      $getNamedIndividuals = new GetNamedIndividualsFunction();
      
      $getNamedIndividuals->classUri('http://foo.org/test#D')
                          ->getNamedIndividualsUris()
                          ->directNamedIndividuals()
                          ->limit(1)
                          ->offset(0);
      
      $ontologyRead->disableReasoner()
                   ->getNamedIndividuals($getNamedIndividuals)
                   ->mime('resultset')
                   ->ontology($settings->testOntologyUri)
                   ->sourceInterface($settings->ontologyReadInterface)
                   ->sourceInterfaceVersion($settings->ontologyReadInterfaceVersion)
                   ->send();
      
      $this->assertEquals($ontologyRead->getStatus(), "200", "Debugging information: ".var_export($ontologyRead, TRUE));                                       

      $resultset = $ontologyRead->getResultset()->getResultset();

      $this->assertTrue(count($resultset['unspecified']) == 1);
      $this->assertTrue(isset($resultset['unspecified']['http://foo.org/test#niD']));
      $this->assertTrue(count($resultset['unspecified']['http://foo.org/test#niD']) == 1);

      unset($ontologyRead);
      unset($settings);   
    }
    
    public function test_getNamedIndividuals_niD_Mode_Uris_Direct_Limit_2_Offset_0_Reasoner_Disabled() {
      
      $settings = new Config();  
      
      $ontologyRead = new OntologyReadQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
      
      $getNamedIndividuals = new GetNamedIndividualsFunction();
      
      $getNamedIndividuals->classUri('http://foo.org/test#D')
                          ->getNamedIndividualsUris()
                          ->directNamedIndividuals()
                          ->limit(2)
                          ->offset(0);
      
      $ontologyRead->disableReasoner()
                   ->getNamedIndividuals($getNamedIndividuals)
                   ->mime('resultset')
                   ->ontology($settings->testOntologyUri)
                   ->sourceInterface($settings->ontologyReadInterface)
                   ->sourceInterfaceVersion($settings->ontologyReadInterfaceVersion)
                   ->send();
      
      $this->assertEquals($ontologyRead->getStatus(), "200", "Debugging information: ".var_export($ontologyRead, TRUE));                                       

      $resultset = $ontologyRead->getResultset()->getResultset();

      $this->assertTrue(count($resultset['unspecified']) == 1);
      $this->assertTrue(isset($resultset['unspecified']['http://foo.org/test#niD']));
      $this->assertTrue(count($resultset['unspecified']['http://foo.org/test#niD']) == 1);

      unset($ontologyRead);
      unset($settings);   
    }    
        
    public function test_getNamedIndividuals_niD_Mode_Uris_Direct_Limit_1_Offset_1_Reasoner_Disabled() {
      
      $settings = new Config();  
      
      $ontologyRead = new OntologyReadQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
      
      $getNamedIndividuals = new GetNamedIndividualsFunction();
      
      $getNamedIndividuals->classUri('http://foo.org/test#D')
                          ->getNamedIndividualsUris()
                          ->directNamedIndividuals()
                          ->limit(1)
                          ->offset(1);
      
      $ontologyRead->disableReasoner()
                   ->getNamedIndividuals($getNamedIndividuals)
                   ->mime('resultset')
                   ->ontology($settings->testOntologyUri)
                   ->sourceInterface($settings->ontologyReadInterface)
                   ->sourceInterfaceVersion($settings->ontologyReadInterfaceVersion)
                   ->send();
      
      $this->assertEquals($ontologyRead->getStatus(), "200", "Debugging information: ".var_export($ontologyRead, TRUE));                                       

      $resultset = $ontologyRead->getResultset()->getResultset();

      $this->assertTrue(count($resultset) == 0);

      unset($ontologyRead);
      unset($settings);   
    }    
    
    public function test_getNamedIndividuals_niD_Mode_Uris_All_Limit_1_Offset_0_Reasoner_Disabled() {
      
      $settings = new Config();  
      
      $ontologyRead = new OntologyReadQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
      
      $getNamedIndividuals = new GetNamedIndividualsFunction();
      
      $getNamedIndividuals->classUri('http://foo.org/test#D')
                          ->getNamedIndividualsUris()
                          ->allNamedIndividuals()
                          ->limit(1)
                          ->offset(0);
      
      $ontologyRead->disableReasoner()
                   ->getNamedIndividuals($getNamedIndividuals)
                   ->mime('resultset')
                   ->ontology($settings->testOntologyUri)
                   ->sourceInterface($settings->ontologyReadInterface)
                   ->sourceInterfaceVersion($settings->ontologyReadInterfaceVersion)
                   ->send();
      
      $this->assertEquals($ontologyRead->getStatus(), "200", "Debugging information: ".var_export($ontologyRead, TRUE));                                       

      $resultset = $ontologyRead->getResultset()->getResultset();

      $this->assertTrue(count($resultset['unspecified']) == 1);
      $this->assertTrue(isset($resultset['unspecified']['http://foo.org/test#niD']));
      $this->assertTrue(count($resultset['unspecified']['http://foo.org/test#niD']) == 1);

      unset($ontologyRead);
      unset($settings);   
    }
    
    public function test_getNamedIndividuals_niD_Mode_Uris_All_Limit_2_Offset_0_Reasoner_Disabled() {
      
      $settings = new Config();  
      
      $ontologyRead = new OntologyReadQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
      
      $getNamedIndividuals = new GetNamedIndividualsFunction();
      
      $getNamedIndividuals->classUri('http://foo.org/test#D')
                          ->getNamedIndividualsUris()
                          ->allNamedIndividuals()
                          ->limit(2)
                          ->offset(0);
      
      $ontologyRead->disableReasoner()
                   ->getNamedIndividuals($getNamedIndividuals)
                   ->mime('resultset')
                   ->ontology($settings->testOntologyUri)
                   ->sourceInterface($settings->ontologyReadInterface)
                   ->sourceInterfaceVersion($settings->ontologyReadInterfaceVersion)
                   ->send();
      
      $this->assertEquals($ontologyRead->getStatus(), "200", "Debugging information: ".var_export($ontologyRead, TRUE));                                       

      $resultset = $ontologyRead->getResultset()->getResultset();

      $this->assertTrue(count($resultset['unspecified']) == 1);
      $this->assertTrue(isset($resultset['unspecified']['http://foo.org/test#niD']));
      $this->assertTrue(count($resultset['unspecified']['http://foo.org/test#niD']) == 1);

      unset($ontologyRead);
      unset($settings);   
    }    
        
    public function test_getNamedIndividuals_niD_Mode_Uris_All_Limit_1_Offset_1_Reasoner_Disabled() {
      
      $settings = new Config();  
      
      $ontologyRead = new OntologyReadQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
      
      $getNamedIndividuals = new GetNamedIndividualsFunction();
      
      $getNamedIndividuals->classUri('http://foo.org/test#D')
                          ->getNamedIndividualsUris()
                          ->allNamedIndividuals()
                          ->limit(1)
                          ->offset(1);
      
      $ontologyRead->disableReasoner()
                   ->getNamedIndividuals($getNamedIndividuals)
                   ->mime('resultset')
                   ->ontology($settings->testOntologyUri)
                   ->sourceInterface($settings->ontologyReadInterface)
                   ->sourceInterfaceVersion($settings->ontologyReadInterfaceVersion)
                   ->send();
      
      $this->assertEquals($ontologyRead->getStatus(), "200", "Debugging information: ".var_export($ontologyRead, TRUE));                                       

      $resultset = $ontologyRead->getResultset()->getResultset();

      $this->assertTrue(count($resultset) == 0);

      unset($ontologyRead);
      unset($settings);   
    }       
    
    public function test_getNamedIndividuals_niD_Mode_Descriptions_Direct_Limit_1_Offset_0_Reasoner_Disabled() {
      
      $settings = new Config();  
      
      $ontologyRead = new OntologyReadQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
      
      $getNamedIndividuals = new GetNamedIndividualsFunction();
      
      $getNamedIndividuals->classUri('http://foo.org/test#D')
                          ->getNamedIndividualsDescriptions()
                          ->directNamedIndividuals()
                          ->limit(1)
                          ->offset(0);
      
      $ontologyRead->disableReasoner()
                   ->getNamedIndividuals($getNamedIndividuals)
                   ->mime('resultset')
                   ->ontology($settings->testOntologyUri)
                   ->sourceInterface($settings->ontologyReadInterface)
                   ->sourceInterfaceVersion($settings->ontologyReadInterfaceVersion)
                   ->send();
      
      $this->assertEquals($ontologyRead->getStatus(), "200", "Debugging information: ".var_export($ontologyRead, TRUE));                                       

      $resultset = $ontologyRead->getResultset()->getResultset();

      $this->assertTrue(count($resultset['unspecified']) == 1);
      $this->assertTrue(isset($resultset['unspecified']['http://foo.org/test#niD']));
      $this->assertTrue(count($resultset['unspecified']['http://foo.org/test#niD']) == 2);

      unset($ontologyRead);
      unset($settings);   
    }
    
    public function test_getNamedIndividuals_niD_Mode_Descriptions_Direct_Limit_2_Offset_0_Reasoner_Disabled() {
      
      $settings = new Config();  
      
      $ontologyRead = new OntologyReadQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
      
      $getNamedIndividuals = new GetNamedIndividualsFunction();
      
      $getNamedIndividuals->classUri('http://foo.org/test#D')
                          ->getNamedIndividualsDescriptions()
                          ->directNamedIndividuals()
                          ->limit(2)
                          ->offset(0);
      
      $ontologyRead->disableReasoner()
                   ->getNamedIndividuals($getNamedIndividuals)
                   ->mime('resultset')
                   ->ontology($settings->testOntologyUri)
                   ->sourceInterface($settings->ontologyReadInterface)
                   ->sourceInterfaceVersion($settings->ontologyReadInterfaceVersion)
                   ->send();
      
      $this->assertEquals($ontologyRead->getStatus(), "200", "Debugging information: ".var_export($ontologyRead, TRUE));                                       

      $resultset = $ontologyRead->getResultset()->getResultset();

      $this->assertTrue(count($resultset['unspecified']) == 1);
      $this->assertTrue(isset($resultset['unspecified']['http://foo.org/test#niD']));
      $this->assertTrue(count($resultset['unspecified']['http://foo.org/test#niD']) == 2);

      unset($ontologyRead);
      unset($settings);   
    }    
        
    public function test_getNamedIndividuals_niD_Mode_Descriptions_Direct_Limit_1_Offset_1_Reasoner_Disabled() {
      
      $settings = new Config();  
      
      $ontologyRead = new OntologyReadQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
      
      $getNamedIndividuals = new GetNamedIndividualsFunction();
      
      $getNamedIndividuals->classUri('http://foo.org/test#D')
                          ->getNamedIndividualsDescriptions()
                          ->directNamedIndividuals()
                          ->limit(1)
                          ->offset(1);
      
      $ontologyRead->disableReasoner()
                   ->getNamedIndividuals($getNamedIndividuals)
                   ->mime('resultset')
                   ->ontology($settings->testOntologyUri)
                   ->sourceInterface($settings->ontologyReadInterface)
                   ->sourceInterfaceVersion($settings->ontologyReadInterfaceVersion)
                   ->send();
      
      $this->assertEquals($ontologyRead->getStatus(), "200", "Debugging information: ".var_export($ontologyRead, TRUE));                                       

      $resultset = $ontologyRead->getResultset()->getResultset();

      $this->assertTrue(count($resultset) == 0);

      unset($ontologyRead);
      unset($settings);   
    }    
    
    public function test_getNamedIndividuals_niD_Mode_Descriptions_All_Limit_1_Offset_0_Reasoner_Disabled() {
      
      $settings = new Config();  
      
      $ontologyRead = new OntologyReadQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
      
      $getNamedIndividuals = new GetNamedIndividualsFunction();
      
      $getNamedIndividuals->classUri('http://foo.org/test#D')
                          ->getNamedIndividualsDescriptions()
                          ->allNamedIndividuals()
                          ->limit(1)
                          ->offset(0);
      
      $ontologyRead->disableReasoner()
                   ->getNamedIndividuals($getNamedIndividuals)
                   ->mime('resultset')
                   ->ontology($settings->testOntologyUri)
                   ->sourceInterface($settings->ontologyReadInterface)
                   ->sourceInterfaceVersion($settings->ontologyReadInterfaceVersion)
                   ->send();
      
      $this->assertEquals($ontologyRead->getStatus(), "200", "Debugging information: ".var_export($ontologyRead, TRUE));                                       

      $resultset = $ontologyRead->getResultset()->getResultset();

      $this->assertTrue(count($resultset['unspecified']) == 1);
      $this->assertTrue(isset($resultset['unspecified']['http://foo.org/test#niD']));
      $this->assertTrue(count($resultset['unspecified']['http://foo.org/test#niD']) == 2);

      unset($ontologyRead);
      unset($settings);   
    }
    
    public function test_getNamedIndividuals_niD_Mode_Descriptions_All_Limit_2_Offset_0_Reasoner_Disabled() {
      
      $settings = new Config();  
      
      $ontologyRead = new OntologyReadQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
      
      $getNamedIndividuals = new GetNamedIndividualsFunction();
      
      $getNamedIndividuals->classUri('http://foo.org/test#D')
                          ->getNamedIndividualsDescriptions()
                          ->allNamedIndividuals()
                          ->limit(2)
                          ->offset(0);
      
      $ontologyRead->disableReasoner()
                   ->getNamedIndividuals($getNamedIndividuals)
                   ->mime('resultset')
                   ->ontology($settings->testOntologyUri)
                   ->sourceInterface($settings->ontologyReadInterface)
                   ->sourceInterfaceVersion($settings->ontologyReadInterfaceVersion)
                   ->send();
      
      $this->assertEquals($ontologyRead->getStatus(), "200", "Debugging information: ".var_export($ontologyRead, TRUE));                                       

      $resultset = $ontologyRead->getResultset()->getResultset();

      $this->assertTrue(count($resultset['unspecified']) == 1);
      $this->assertTrue(isset($resultset['unspecified']['http://foo.org/test#niD']));
      $this->assertTrue(count($resultset['unspecified']['http://foo.org/test#niD']) == 2);

      unset($ontologyRead);
      unset($settings);   
    }    
        
    public function test_getNamedIndividuals_niD_Mode_Descriptions_All_Limit_1_Offset_1_Reasoner_Disabled() {
      
      $settings = new Config();  
      
      $ontologyRead = new OntologyReadQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
      
      $getNamedIndividuals = new GetNamedIndividualsFunction();
      
      $getNamedIndividuals->classUri('http://foo.org/test#D')
                          ->getNamedIndividualsDescriptions()
                          ->allNamedIndividuals()
                          ->limit(1)
                          ->offset(1);
      
      $ontologyRead->disableReasoner()
                   ->getNamedIndividuals($getNamedIndividuals)
                   ->mime('resultset')
                   ->ontology($settings->testOntologyUri)
                   ->sourceInterface($settings->ontologyReadInterface)
                   ->sourceInterfaceVersion($settings->ontologyReadInterfaceVersion)
                   ->send();
      
      $this->assertEquals($ontologyRead->getStatus(), "200", "Debugging information: ".var_export($ontologyRead, TRUE));                                       

      $resultset = $ontologyRead->getResultset()->getResultset();

      $this->assertTrue(count($resultset) == 0);

      unset($ontologyRead);
      unset($settings);   
    }    

    public function test_getNamedIndividuals_All_Mode_Uris_Direct_Limit_1_Offset_0_Reasoner_Disabled() {
      
      $settings = new Config();  
      
      $ontologyRead = new OntologyReadQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
      
      $getNamedIndividuals = new GetNamedIndividualsFunction();
      
      $getNamedIndividuals->classUri('all')
                          ->getNamedIndividualsUris()
                          ->directNamedIndividuals()
                          ->limit(1)
                          ->offset(0);
      
      $ontologyRead->disableReasoner()
                   ->getNamedIndividuals($getNamedIndividuals)
                   ->mime('resultset')
                   ->ontology($settings->testOntologyUri)
                   ->sourceInterface($settings->ontologyReadInterface)
                   ->sourceInterfaceVersion($settings->ontologyReadInterfaceVersion)
                   ->send();
      
      $this->assertEquals($ontologyRead->getStatus(), "200", "Debugging information: ".var_export($ontologyRead, TRUE));                                       

      $resultset = $ontologyRead->getResultset()->getResultset();

      $this->assertTrue(count($resultset['unspecified']) == 1);
      $this->assertTrue(isset($resultset['unspecified']['http://foo.org/test#niB']));
      $this->assertTrue(count($resultset['unspecified']['http://foo.org/test#niB']) == 1);

      unset($ontologyRead);
      unset($settings);   
    }
    
    public function test_getNamedIndividuals_All_Mode_Uris_Direct_Limit_2_Offset_0_Reasoner_Disabled() {
      
      $settings = new Config();  
      
      $ontologyRead = new OntologyReadQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
      
      $getNamedIndividuals = new GetNamedIndividualsFunction();
      
      $getNamedIndividuals->classUri('all')
                          ->getNamedIndividualsUris()
                          ->directNamedIndividuals()
                          ->limit(2)
                          ->offset(0);
      
      $ontologyRead->disableReasoner()
                   ->getNamedIndividuals($getNamedIndividuals)
                   ->mime('resultset')
                   ->ontology($settings->testOntologyUri)
                   ->sourceInterface($settings->ontologyReadInterface)
                   ->sourceInterfaceVersion($settings->ontologyReadInterfaceVersion)
                   ->send();
      
      $this->assertEquals($ontologyRead->getStatus(), "200", "Debugging information: ".var_export($ontologyRead, TRUE));                                       

      $resultset = $ontologyRead->getResultset()->getResultset();

      $this->assertTrue(count($resultset['unspecified']) == 2);
      $this->assertTrue(isset($resultset['unspecified']['http://foo.org/test#niB']));
      $this->assertTrue(count($resultset['unspecified']['http://foo.org/test#niB']) == 1);
      $this->assertTrue(isset($resultset['unspecified']['http://foo.org/test#niC']));
      $this->assertTrue(count($resultset['unspecified']['http://foo.org/test#niC']) == 1);

      unset($ontologyRead);
      unset($settings);   
    }    
        
    public function test_getNamedIndividuals_All_Mode_Uris_Direct_Limit_1_Offset_1_Reasoner_Disabled() {
      
      $settings = new Config();  
      
      $ontologyRead = new OntologyReadQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
      
      $getNamedIndividuals = new GetNamedIndividualsFunction();
      
      $getNamedIndividuals->classUri('all')
                          ->getNamedIndividualsUris()
                          ->directNamedIndividuals()
                          ->limit(1)
                          ->offset(1);
      
      $ontologyRead->disableReasoner()
                   ->getNamedIndividuals($getNamedIndividuals)
                   ->mime('resultset')
                   ->ontology($settings->testOntologyUri)
                   ->sourceInterface($settings->ontologyReadInterface)
                   ->sourceInterfaceVersion($settings->ontologyReadInterfaceVersion)
                   ->send();
      
      $this->assertEquals($ontologyRead->getStatus(), "200", "Debugging information: ".var_export($ontologyRead, TRUE));                                       

      $resultset = $ontologyRead->getResultset()->getResultset();

      $this->assertTrue(count($resultset['unspecified']) == 1);
      $this->assertTrue(isset($resultset['unspecified']['http://foo.org/test#niC']));
      $this->assertTrue(count($resultset['unspecified']['http://foo.org/test#niC']) == 1);

      unset($ontologyRead);
      unset($settings);   
    }    
    
    public function test_getNamedIndividuals_All_Mode_Uris_All_Limit_1_Offset_0_Reasoner_Disabled() {
      
      $settings = new Config();  
      
      $ontologyRead = new OntologyReadQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
      
      $getNamedIndividuals = new GetNamedIndividualsFunction();
      
      $getNamedIndividuals->classUri('all')
                          ->getNamedIndividualsUris()
                          ->allNamedIndividuals()
                          ->limit(1)
                          ->offset(0);
      
      $ontologyRead->disableReasoner()
                   ->getNamedIndividuals($getNamedIndividuals)
                   ->mime('resultset')
                   ->ontology($settings->testOntologyUri)
                   ->sourceInterface($settings->ontologyReadInterface)
                   ->sourceInterfaceVersion($settings->ontologyReadInterfaceVersion)
                   ->send();
      
      $this->assertEquals($ontologyRead->getStatus(), "200", "Debugging information: ".var_export($ontologyRead, TRUE));                                       

      $resultset = $ontologyRead->getResultset()->getResultset();

      $this->assertTrue(count($resultset['unspecified']) == 1);
      $this->assertTrue(isset($resultset['unspecified']['http://foo.org/test#niB']));
      $this->assertTrue(count($resultset['unspecified']['http://foo.org/test#niB']) == 1);

      unset($ontologyRead);
      unset($settings);   
    }
    
    public function test_getNamedIndividuals_All_Mode_Uris_All_Limit_2_Offset_0_Reasoner_Disabled() {
      
      $settings = new Config();  
      
      $ontologyRead = new OntologyReadQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
      
      $getNamedIndividuals = new GetNamedIndividualsFunction();
      
      $getNamedIndividuals->classUri('all')
                          ->getNamedIndividualsUris()
                          ->allNamedIndividuals()
                          ->limit(2)
                          ->offset(0);
      
      $ontologyRead->disableReasoner()
                   ->getNamedIndividuals($getNamedIndividuals)
                   ->mime('resultset')
                   ->ontology($settings->testOntologyUri)
                   ->sourceInterface($settings->ontologyReadInterface)
                   ->sourceInterfaceVersion($settings->ontologyReadInterfaceVersion)
                   ->send();
      
      $this->assertEquals($ontologyRead->getStatus(), "200", "Debugging information: ".var_export($ontologyRead, TRUE));                                       

      $resultset = $ontologyRead->getResultset()->getResultset();

      $this->assertTrue(count($resultset['unspecified']) == 2);
      $this->assertTrue(isset($resultset['unspecified']['http://foo.org/test#niB']));
      $this->assertTrue(count($resultset['unspecified']['http://foo.org/test#niB']) == 1);
      $this->assertTrue(isset($resultset['unspecified']['http://foo.org/test#niC']));
      $this->assertTrue(count($resultset['unspecified']['http://foo.org/test#niC']) == 1);

      unset($ontologyRead);
      unset($settings);   
    }    
        
    public function test_getNamedIndividuals_All_Mode_Uris_All_Limit_1_Offset_1_Reasoner_Disabled() {
      
      $settings = new Config();  
      
      $ontologyRead = new OntologyReadQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
      
      $getNamedIndividuals = new GetNamedIndividualsFunction();
      
      $getNamedIndividuals->classUri('all')
                          ->getNamedIndividualsUris()
                          ->allNamedIndividuals()
                          ->limit(1)
                          ->offset(1);
      
      $ontologyRead->disableReasoner()
                   ->getNamedIndividuals($getNamedIndividuals)
                   ->mime('resultset')
                   ->ontology($settings->testOntologyUri)
                   ->sourceInterface($settings->ontologyReadInterface)
                   ->sourceInterfaceVersion($settings->ontologyReadInterfaceVersion)
                   ->send();
      
      $this->assertEquals($ontologyRead->getStatus(), "200", "Debugging information: ".var_export($ontologyRead, TRUE));                                       

      $resultset = $ontologyRead->getResultset()->getResultset();

      $this->assertTrue(count($resultset['unspecified']) == 1);
      $this->assertTrue(isset($resultset['unspecified']['http://foo.org/test#niC']));
      $this->assertTrue(count($resultset['unspecified']['http://foo.org/test#niC']) == 1);

      unset($ontologyRead);
      unset($settings);   
    }       
    
    public function test_getNamedIndividuals_All_Mode_Descriptions_Direct_Limit_1_Offset_0_Reasoner_Disabled() {
      
      $settings = new Config();  
      
      $ontologyRead = new OntologyReadQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
      
      $getNamedIndividuals = new GetNamedIndividualsFunction();
      
      $getNamedIndividuals->classUri('all')
                          ->getNamedIndividualsDescriptions()
                          ->directNamedIndividuals()
                          ->limit(1)
                          ->offset(0);
      
      $ontologyRead->disableReasoner()
                   ->getNamedIndividuals($getNamedIndividuals)
                   ->mime('resultset')
                   ->ontology($settings->testOntologyUri)
                   ->sourceInterface($settings->ontologyReadInterface)
                   ->sourceInterfaceVersion($settings->ontologyReadInterfaceVersion)
                   ->send();
      
      $this->assertEquals($ontologyRead->getStatus(), "200", "Debugging information: ".var_export($ontologyRead, TRUE));                                       

      $resultset = $ontologyRead->getResultset()->getResultset();

      $this->assertTrue(count($resultset['unspecified']) == 1);
      $this->assertTrue(isset($resultset['unspecified']['http://foo.org/test#niB']));
      $this->assertTrue(count($resultset['unspecified']['http://foo.org/test#niB']) == 2);

      unset($ontologyRead);
      unset($settings);   
    }
    
    public function test_getNamedIndividuals_All_Mode_Descriptions_Direct_Limit_2_Offset_0_Reasoner_Disabled() {
      
      $settings = new Config();  
      
      $ontologyRead = new OntologyReadQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
      
      $getNamedIndividuals = new GetNamedIndividualsFunction();
      
      $getNamedIndividuals->classUri('all')
                          ->getNamedIndividualsDescriptions()
                          ->directNamedIndividuals()
                          ->limit(2)
                          ->offset(0);
      
      $ontologyRead->disableReasoner()
                   ->getNamedIndividuals($getNamedIndividuals)
                   ->mime('resultset')
                   ->ontology($settings->testOntologyUri)
                   ->sourceInterface($settings->ontologyReadInterface)
                   ->sourceInterfaceVersion($settings->ontologyReadInterfaceVersion)
                   ->send();
      
      $this->assertEquals($ontologyRead->getStatus(), "200", "Debugging information: ".var_export($ontologyRead, TRUE));                                       

      $resultset = $ontologyRead->getResultset()->getResultset();

      $this->assertTrue(count($resultset['unspecified']) == 2);
      $this->assertTrue(isset($resultset['unspecified']['http://foo.org/test#niB']));
      $this->assertTrue(count($resultset['unspecified']['http://foo.org/test#niB']) == 2);
      $this->assertTrue(isset($resultset['unspecified']['http://foo.org/test#niC']));
      $this->assertTrue(count($resultset['unspecified']['http://foo.org/test#niC']) == 2);

      unset($ontologyRead);
      unset($settings);   
    }    
        
    public function test_getNamedIndividuals_All_Mode_Descriptions_Direct_Limit_1_Offset_1_Reasoner_Disabled() {
      
      $settings = new Config();  
      
      $ontologyRead = new OntologyReadQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
      
      $getNamedIndividuals = new GetNamedIndividualsFunction();
      
      $getNamedIndividuals->classUri('all')
                          ->getNamedIndividualsDescriptions()
                          ->directNamedIndividuals()
                          ->limit(1)
                          ->offset(1);
      
      $ontologyRead->disableReasoner()
                   ->getNamedIndividuals($getNamedIndividuals)
                   ->mime('resultset')
                   ->ontology($settings->testOntologyUri)
                   ->sourceInterface($settings->ontologyReadInterface)
                   ->sourceInterfaceVersion($settings->ontologyReadInterfaceVersion)
                   ->send();
      
      $this->assertEquals($ontologyRead->getStatus(), "200", "Debugging information: ".var_export($ontologyRead, TRUE));                                       

      $resultset = $ontologyRead->getResultset()->getResultset();

      $this->assertTrue(count($resultset['unspecified']) == 1);
      $this->assertTrue(isset($resultset['unspecified']['http://foo.org/test#niC']));
      $this->assertTrue(count($resultset['unspecified']['http://foo.org/test#niC']) == 2);

      unset($ontologyRead);
      unset($settings);   
    }    
    
    public function test_getNamedIndividuals_All_Mode_Descriptions_All_Limit_1_Offset_0_Reasoner_Disabled() {
      
      $settings = new Config();  
      
      $ontologyRead = new OntologyReadQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
      
      $getNamedIndividuals = new GetNamedIndividualsFunction();
      
      $getNamedIndividuals->classUri('all')
                          ->getNamedIndividualsDescriptions()
                          ->allNamedIndividuals()
                          ->limit(1)
                          ->offset(0);
      
      $ontologyRead->disableReasoner()
                   ->getNamedIndividuals($getNamedIndividuals)
                   ->mime('resultset')
                   ->ontology($settings->testOntologyUri)
                   ->sourceInterface($settings->ontologyReadInterface)
                   ->sourceInterfaceVersion($settings->ontologyReadInterfaceVersion)
                   ->send();
      
      $this->assertEquals($ontologyRead->getStatus(), "200", "Debugging information: ".var_export($ontologyRead, TRUE));                                       

      $resultset = $ontologyRead->getResultset()->getResultset();

      $this->assertTrue(count($resultset['unspecified']) == 1);
      $this->assertTrue(isset($resultset['unspecified']['http://foo.org/test#niB']));
      $this->assertTrue(count($resultset['unspecified']['http://foo.org/test#niB']) == 2);

      unset($ontologyRead);
      unset($settings);   
    }
    
    public function test_getNamedIndividuals_All_Mode_Descriptions_All_Limit_2_Offset_0_Reasoner_Disabled() {
      
      $settings = new Config();  
      
      $ontologyRead = new OntologyReadQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
      
      $getNamedIndividuals = new GetNamedIndividualsFunction();
      
      $getNamedIndividuals->classUri('all')
                          ->getNamedIndividualsDescriptions()
                          ->allNamedIndividuals()
                          ->limit(2)
                          ->offset(0);
      
      $ontologyRead->disableReasoner()
                   ->getNamedIndividuals($getNamedIndividuals)
                   ->mime('resultset')
                   ->ontology($settings->testOntologyUri)
                   ->sourceInterface($settings->ontologyReadInterface)
                   ->sourceInterfaceVersion($settings->ontologyReadInterfaceVersion)
                   ->send();
      
      $this->assertEquals($ontologyRead->getStatus(), "200", "Debugging information: ".var_export($ontologyRead, TRUE));                                       

      $resultset = $ontologyRead->getResultset()->getResultset();

      $this->assertTrue(count($resultset['unspecified']) == 2);
      $this->assertTrue(isset($resultset['unspecified']['http://foo.org/test#niB']));
      $this->assertTrue(count($resultset['unspecified']['http://foo.org/test#niB']) == 2);
      $this->assertTrue(isset($resultset['unspecified']['http://foo.org/test#niC']));
      $this->assertTrue(count($resultset['unspecified']['http://foo.org/test#niC']) == 2);

      unset($ontologyRead);
      unset($settings);   
    }    
        
    public function test_getNamedIndividuals_All_Mode_Descriptions_All_Limit_1_Offset_1_Reasoner_Disabled() {
      
      $settings = new Config();  
      
      $ontologyRead = new OntologyReadQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
      
      $getNamedIndividuals = new GetNamedIndividualsFunction();
      
      $getNamedIndividuals->classUri('all')
                          ->getNamedIndividualsDescriptions()
                          ->allNamedIndividuals()
                          ->limit(1)
                          ->offset(1);
      
      $ontologyRead->disableReasoner()
                   ->getNamedIndividuals($getNamedIndividuals)
                   ->mime('resultset')
                   ->ontology($settings->testOntologyUri)
                   ->sourceInterface($settings->ontologyReadInterface)
                   ->sourceInterfaceVersion($settings->ontologyReadInterfaceVersion)
                   ->send();
      
      $this->assertEquals($ontologyRead->getStatus(), "200", "Debugging information: ".var_export($ontologyRead, TRUE));                                       

      $resultset = $ontologyRead->getResultset()->getResultset();

      $this->assertTrue(count($resultset['unspecified']) == 1);
      $this->assertTrue(isset($resultset['unspecified']['http://foo.org/test#niC']));
      $this->assertTrue(count($resultset['unspecified']['http://foo.org/test#niC']) == 2);

      unset($ontologyRead);
      unset($settings);   
    }    
    
    public function test_getLoadedOntologies_Uris() {
      
      $settings = new Config();  
      
      $ontologyRead = new OntologyReadQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
      
      $getLoadedOntologies = new  GetLoadedOntologiesFunction();
      
      $getLoadedOntologies->modeUris();
      
      $ontologyRead->enableReasoner()
                   ->getLoadedOntologies($getLoadedOntologies)
                   ->mime('resultset')
                   ->ontology($settings->testOntologyUri)
                   ->sourceInterface($settings->ontologyReadInterface)
                   ->sourceInterfaceVersion($settings->ontologyReadInterfaceVersion)
                   ->send();
      
      $this->assertEquals($ontologyRead->getStatus(), "200", "Debugging information: ".var_export($ontologyRead, TRUE));                                       

      $resultset = $ontologyRead->getResultset()->getResultset();
      
      $this->assertTrue(count($resultset['unspecified'][$settings->testOntologyUri]) == 1);

      unset($ontologyRead);
      unset($settings);   
    }   
          
    public function test_getLoadedOntologies_Descriptions() {
      
      $settings = new Config();  
      
      $ontologyRead = new OntologyReadQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
      
      $getLoadedOntologies = new  GetLoadedOntologiesFunction();
      
      $getLoadedOntologies->modeDescriptions();
      
      $ontologyRead->enableReasoner()
                   ->getLoadedOntologies($getLoadedOntologies)
                   ->mime('resultset')
                   ->ontology($settings->testOntologyUri)
                   ->sourceInterface($settings->ontologyReadInterface)
                   ->sourceInterfaceVersion($settings->ontologyReadInterfaceVersion)
                   ->send();
      
      $this->assertEquals($ontologyRead->getStatus(), "200", "Debugging information: ".var_export($ontologyRead, TRUE));                                       

      $resultset = $ontologyRead->getResultset()->getResultset();
    
      $this->assertTrue(count($resultset['unspecified'][$settings->testOntologyUri]) == 5);

      unset($ontologyRead);
      unset($settings);   
    }
              
    public function test_getOntologies() {
      
      $settings = new Config();  
      
      $ontologyRead = new OntologyReadQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
                  
      $ontologyRead->enableReasoner()
                   ->getOntologies()
                   ->mime('resultset')
                   ->ontology($settings->testOntologyUri)
                   ->sourceInterface($settings->ontologyReadInterface)
                   ->sourceInterfaceVersion($settings->ontologyReadInterfaceVersion)
                   ->send();
      
      $this->assertEquals($ontologyRead->getStatus(), "200", "Debugging information: ".var_export($ontologyRead, TRUE));                                       

      $resultset = $ontologyRead->getResultset()->getResultset();

      $this->assertTrue(count($resultset['unspecified']['http://foo.org/test#']) == 1);

      unset($ontologyRead);
      unset($settings);   
    }
    
    public function test_getSerialized() {
      
      $settings = new Config();  
      
      $ontologyRead = new OntologyReadQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
                  
      $ontologyRead->enableReasoner()
                   ->getSerialized()
                   ->mime('application/rdf+xml')
                   ->ontology($settings->testOntologyUri)
                   ->sourceInterface($settings->ontologyReadInterface)
                   ->sourceInterfaceVersion($settings->ontologyReadInterfaceVersion)
                   ->send();
      
      $this->assertEquals($ontologyRead->getStatus(), "200", "Debugging information: ".var_export($ontologyRead, TRUE));                                       

      utilities\validateParameterApplicationRdfXml($this, $ontologyRead);
      
      $this->assertTrue(utilities\compareRdf($ontologyRead->getResultset(), file_get_contents($settings->contentDir.'foo.owl'), TRUE));


      unset($ontologyRead);
      unset($settings);   
    }    

    public function test_getIronXMLSchema() {
      
      $settings = new Config();  
      
      $ontologyRead = new OntologyReadQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
                  
      $ontologyRead->enableReasoner()
                   ->getIronXMLSchema()
                   ->mime('resultset')
                   ->ontology($settings->testOntologyUri)
                   ->sourceInterface($settings->ontologyReadInterface)
                   ->sourceInterfaceVersion($settings->ontologyReadInterfaceVersion)
                   ->send();
      
      $this->assertEquals($ontologyRead->getStatus(), "200", "Debugging information: ".var_export($ontologyRead, TRUE));                                       

      $resultset = $ontologyRead->getResultset()->getResultset();
      
      $schema = $resultset['unspecified']['file://localhost'.str_replace('/ws/', '/tests/', $settings->osfInstanceFolder).'content/foo.owl']['http://purl.org/ontology/wsf#serializedIronXMLSchema'][0]['value'];

      $errors = array();
      $this->assertEquals(utilities\isValidXML($schema, $errors), TRUE, "[Test is valid XML] Debugging information: ".var_export($errors, TRUE));                                       
      $this->assertEquals(utilities\isValidXML($schema . "this is invalid XML", $errors), FALSE, "[Test is invalid XML] Debugging information: ".var_export($errors, TRUE));                                       

      $expected = new \DOMDocument;
      $expected->loadXML(file_get_contents($settings->contentDir.'validation/ontology_read_ironxml_schema.xml'));
 
      $actual = new \DOMDocument;
      $actual->loadXML($schema);      
      
      $this->assertEqualXMLStructure($expected->firstChild, $actual->firstChild, TRUE);         

      unset($ontologyRead);
      unset($settings);   
    }    

    public function test_getIronSJONSchema() {
      
      $settings = new Config();  
      
      $ontologyRead = new OntologyReadQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
                  
      $ontologyRead->enableReasoner()
                   ->getIronJSONSchema()
                   ->mime('resultset')
                   ->ontology($settings->testOntologyUri)
                   ->sourceInterface($settings->ontologyReadInterface)
                   ->sourceInterfaceVersion($settings->ontologyReadInterfaceVersion)
                   ->send();
      
      $this->assertEquals($ontologyRead->getStatus(), "200", "Debugging information: ".var_export($ontologyRead, TRUE));                                       

      $resultset = $ontologyRead->getResultset()->getResultset();
      
      $schema = $resultset['unspecified']['file://localhost'.str_replace('/ws/', '/tests/', $settings->osfInstanceFolder).'content/foo.owl']['http://purl.org/ontology/wsf#serializedIronJSONSchema'][0]['value'];
      $errors = array();
      
      $this->assertEquals(utilities\isValidJSON($schema, $errors), TRUE, "[Test is valid JSON] Debugging information: ".var_export($errors, TRUE));                                       
      $this->assertEquals(utilities\isValidJSON($schema . "this is invalid JSON", $errors), FALSE, "[Test is invalid JSON] Debugging information: ".var_export($errors, TRUE));                                       
      
      $this->assertTrue(utilities\compareStructJSON($schema, file_get_contents($settings->contentDir.'validation/ontology_read_ironjson_schema.json'), FALSE));


      unset($ontologyRead);
      unset($settings);   
    }    

    
  }
  
?>