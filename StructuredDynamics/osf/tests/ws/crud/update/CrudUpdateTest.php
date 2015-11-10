<?php

  namespace StructuredDynamics\osf\tests\ws\crud\update;
  
  use StructuredDynamics\osf\framework\WebServiceQuerier;
  use StructuredDynamics\osf\php\api\ws\crud\update\CrudUpdateQuery;
  use StructuredDynamics\osf\php\api\ws\revision\lister\RevisionListerQuery;
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
  
  class CrudUpdateTest extends \PHPUnit_Framework_TestCase {
    
    static private $outputs = array();
    
    public function testWrongEndpointUrl() {
      
      $settings = new Config();          
      
      $wsq = new WebServiceQuerier($settings->endpointUrl . "crud/update/" . "wrong", 
                                   "post", 
                                   "text/xml",
                                   "document=" . urlencode(file_get_contents($settings->contentDir.'crud_update.n3')) .
                                   "&mime=" . urlencode("application/rdf+n3") .
                                   "&dataset=" . urlencode($settings->testDataset) .
                                   "&lifecycle=" . urlencode("published") .
                                   "&revision=" . urlencode("true") .
                                   "&interface=". urlencode($settings->crudCreateInterface) .
                                   "&version=". urlencode($settings->crudCreateInterfaceVersion),
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
      
      $wsq = new WebServiceQuerier($settings->endpointUrl . "crud/update/", 
                                   "get", 
                                   "text/xml",
                                   "document=" . urlencode(file_get_contents($settings->contentDir.'crud_update.n3')) .
                                   "&mime=" . urlencode("application/rdf+n3") .
                                   "&dataset=" . urlencode($settings->testDataset) .
                                   "&lifecycle=" . urlencode("published") .
                                   "&revision=" . urlencode("true") .
                                   "&interface=". urlencode($settings->crudCreateInterface) .
                                   "&version=". urlencode($settings->crudCreateInterfaceVersion),
                                   $settings->applicationID,
                                   $settings->apiKey,
                                   $settings->userID);
   
      $this->assertEquals($wsq->getStatus(), "405", "Debugging information: ".var_export($wsq, TRUE));                                       
      $this->assertEquals($wsq->getStatusMessage(), "Method Not Allowed", "Debugging information: ".var_export($wsq, TRUE));          
      
      unset($wsq);
      unset($settings);
    }    
    
    public function testUnknownLifecycleStage() {
      
      $settings = new Config();  

      utilities\deleteUnrevisionedRecord();
      
      $this->assertTrue(utilities\createUnrevisionedRecord(), "Can't create the revisioned records...");
      
      $wsq = new WebServiceQuerier($settings->endpointUrl . "crud/update/", 
                                   "post", 
                                   "text/xml",
                                   "document=" . urlencode(file_get_contents($settings->contentDir.'crud_update.n3')) .
                                   "&mime=" . urlencode("application/rdf+n3") .
                                   "&dataset=" . urlencode($settings->testDataset) .
                                   "&lifecycle=" . urlencode("unknown-lifecycle-stage") .
                                   "&revision=" . urlencode("true") .
                                   "&interface=". urlencode($settings->crudCreateInterface) .
                                   "&version=". urlencode($settings->crudCreateInterfaceVersion),
                                   $settings->applicationID,
                                   $settings->apiKey,
                                   $settings->userID);
   
      $this->assertEquals($wsq->getStatus(), "400", "Debugging information: ".var_export($wsq, TRUE));                                       
      $this->assertEquals($wsq->getStatusMessage(), "Bad Request", "Debugging information: ".var_export($wsq, TRUE));
      $this->assertEquals($wsq->error->id, "WS-CRUD-UPDATE-312", "Debugging information: ".var_export($wsq, TRUE));                                       
      
      utilities\deleteUnrevisionedRecord();      
      
      unset($wsq);
      unset($settings);
    }     
    
    public function testValidInterfaceVersion() {
      
      $settings = new Config();  
      
      utilities\deleteUnrevisionedRecord();
      
      $this->assertTrue(utilities\createUnrevisionedRecord(), "Can't create the revisioned records...");
                 
      $crudUpdate = new CrudUpdateQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
      
      $crudUpdate->createRevision()
                 ->isPublished()
                 ->dataset($settings->testDataset)
                 ->document(file_get_contents($settings->contentDir.'crud_update.n3'))
                 ->documentMimeIsRdfN3()
                 ->sourceInterface($settings->crudUpdateInterface)
                 ->sourceInterfaceVersion($settings->crudUpdateInterfaceVersion)
                 ->send();

      $this->assertEquals($crudUpdate->getStatus(), "200", "Debugging information: ".var_export($crudUpdate, TRUE));                                       

      utilities\deleteUnrevisionedRecord();
      
      unset($crudCreate);
      unset($settings);    
    }
    
    
    public function testInvalidInterfaceVersion() {
      
      $settings = new Config();  
      
      utilities\deleteUnrevisionedRecord();
      
      $this->assertTrue(utilities\createUnrevisionedRecord(), "Can't create the revisioned records...");
                 
      $crudUpdate = new CrudUpdateQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
      
      $crudUpdate->createRevision()
                 ->isPublished()
                 ->dataset($settings->testDataset)
                 ->document(file_get_contents($settings->contentDir.'crud_update.n3'))
                 ->documentMimeIsRdfN3()
                 ->sourceInterface($settings->crudUpdateInterface)
                 ->sourceInterfaceVersion("667.4")
                 ->send();                 
                                            
      $this->assertEquals($crudUpdate->getStatus(), "400", "Debugging information: ".var_export($crudUpdate, TRUE));                                       
      $this->assertEquals($crudUpdate->getStatusMessage(), "Bad Request", "Debugging information: ".var_export($crudUpdate, TRUE));
      $this->assertEquals($crudUpdate->error->id, "WS-CRUD-UPDATE-311", "Debugging information: ".var_export($crudUpdate, TRUE));                                       

      utilities\deleteUnrevisionedRecord();

      unset($crudUpdate);
      unset($settings);            
    }    
    
    public function testInterfaceExists() {
      
      $settings = new Config();  
      
      utilities\deleteUnrevisionedRecord();
      
      $this->assertTrue(utilities\createUnrevisionedRecord(), "Can't create the revisioned records...");
                 
      $crudUpdate = new CrudUpdateQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
      
      $crudUpdate->createRevision()
                 ->isPublished()
                 ->dataset($settings->testDataset)
                 ->document(file_get_contents($settings->contentDir.'crud_update.n3'))
                 ->documentMimeIsRdfN3()
                 ->sourceInterface($settings->crudUpdateInterface)
                 ->sourceInterfaceVersion($settings->crudUpdateInterfaceVersion)
                 ->send();
                           
      $this->assertEquals($crudUpdate->getStatus(), "200", "Debugging information: ".var_export($crudUpdate, TRUE));                                       

      utilities\deleteUnrevisionedRecord();
      
      unset($crudCreate);
      unset($settings);  
    }  

    
    public function testInterfaceNotExisting() {
      
      $settings = new Config();  
      
      utilities\deleteUnrevisionedRecord();
      
      $this->assertTrue(utilities\createUnrevisionedRecord(), "Can't create the revisioned records...");
                 
      $crudUpdate = new CrudUpdateQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
      
      $crudUpdate->createRevision()
                 ->isPublished()
                 ->dataset($settings->testDataset)
                 ->document(file_get_contents($settings->contentDir.'crud_update.n3'))
                 ->documentMimeIsRdfN3()
                 ->sourceInterface("default-not-existing")
                 ->sourceInterfaceVersion($settings->crudUpdateInterfaceVersion)
                 ->send();
                           
      $this->assertEquals($crudUpdate->getStatus(), "400", "Debugging information: ".var_export($crudUpdate, TRUE));                                       
      $this->assertEquals($crudUpdate->getStatusMessage(), "Bad Request", "Debugging information: ".var_export($crudUpdate, TRUE));
      $this->assertEquals($crudUpdate->error->id, "WS-CRUD-UPDATE-310", "Debugging information: ".var_export($crudUpdate, TRUE));                                       

      utilities\deleteUnrevisionedRecord();
      
      unset($crudUpdate);
      unset($settings); 
    }     
    
    public function testNoRDFDocumentToIndex() {
      
      $settings = new Config();  
      
      utilities\deleteUnrevisionedRecord();
      
      $this->assertTrue(utilities\createUnrevisionedRecord(), "Can't create the unrevisioned records...");
                 
      $crudUpdate = new CrudUpdateQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
      
      $crudUpdate->createRevision()
                 ->isPublished()
                 ->dataset($settings->testDataset)
                 ->document('')
                 ->documentMimeIsRdfN3()
                 ->sourceInterface($settings->crudUpdateInterface)
                 ->sourceInterfaceVersion($settings->crudUpdateInterfaceVersion)
                 ->send();
                           
      $this->assertEquals($crudUpdate->getStatus(), "400", "Debugging information: ".var_export($crudUpdate, TRUE));                                       
      $this->assertEquals($crudUpdate->getStatusMessage(), "Bad Request", "Debugging information: ".var_export($crudUpdate, TRUE));
      $this->assertEquals($crudUpdate->error->id, "WS-CRUD-UPDATE-200", "Debugging information: ".var_export($crudUpdate, TRUE));                                       

      utilities\deleteUnrevisionedRecord();
      
      unset($crudCreate);
      unset($settings);        
    }
    
    public function testNoDatasetSpecified() {
      
      $settings = new Config();  
      
      utilities\deleteUnrevisionedRecord();
      
      $this->assertTrue(utilities\createUnrevisionedRecord(), "Can't create the unrevisioned records...");
                 
      $crudUpdate = new CrudUpdateQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
      
      $crudUpdate->createRevision()
                 ->isPublished()
                 ->dataset('')
                 ->document(file_get_contents($settings->contentDir.'crud_update.n3'))
                 ->documentMimeIsRdfN3()
                 ->sourceInterface($settings->crudUpdateInterface)
                 ->sourceInterfaceVersion($settings->crudUpdateInterfaceVersion)
                 ->send();
                           
      $this->assertEquals($crudUpdate->getStatus(), "403", "Debugging information: ".var_export($crudUpdate, TRUE));                                       
      $this->assertEquals($crudUpdate->getStatusMessage(), "Forbidden", "Debugging information: ".var_export($crudUpdate, TRUE));
      $this->assertEquals($crudUpdate->error->id, "WS-AUTH-VALIDATION-104", "Debugging information: ".var_export($crudUpdate, TRUE));                                       

      utilities\deleteUnrevisionedRecord();
      
      unset($crudCreate);
      unset($settings);      
    }
    
    public function testCantParseRdfN3Document() {
      $settings = new Config();  
      
      utilities\deleteUnrevisionedRecord();
      
      $this->assertTrue(utilities\createUnrevisionedRecord(), "Can't create the unrevisioned records...");
                 
      $crudUpdate = new CrudUpdateQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
      
      $crudUpdate->createRevision()
                 ->isPublished()
                 ->dataset($settings->testDataset)
                 ->document(file_get_contents($settings->contentDir.'crud_update.n3').'unparsable')
                 ->documentMimeIsRdfN3()
                 ->sourceInterface($settings->crudUpdateInterface)
                 ->sourceInterfaceVersion($settings->crudUpdateInterfaceVersion)
                 ->send();
                           
      $this->assertEquals($crudUpdate->getStatus(), "400", "Debugging information: ".var_export($crudUpdate, TRUE));                                       
      $this->assertEquals($crudUpdate->getStatusMessage(), "Bad Request", "Debugging information: ".var_export($crudUpdate, TRUE));
      $this->assertEquals($crudUpdate->error->id, "WS-CRUD-UPDATE-307", "Debugging information: ".var_export($crudUpdate, TRUE));                                       

      utilities\deleteUnrevisionedRecord();
      
      unset($crudCreate);
      unset($settings);         
    }
    
    public function testCantParseRdfXMLDocument() {
      $settings = new Config();  
      
      utilities\deleteUnrevisionedRecord();
      
      $this->assertTrue(utilities\createUnrevisionedRecord(), "Can't create the unrevisioned records...");
                 
      $crudUpdate = new CrudUpdateQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
      
      $crudUpdate->createRevision()
                 ->isPublished()
                 ->dataset($settings->testDataset)
                 ->document(file_get_contents($settings->contentDir.'crud_create.xml').'unparsable')
                 ->documentMimeIsRdfXML()
                 ->sourceInterface($settings->crudUpdateInterface)
                 ->sourceInterfaceVersion($settings->crudUpdateInterfaceVersion)
                 ->send();
                           
      $this->assertEquals($crudUpdate->getStatus(), "400", "Debugging information: ".var_export($crudUpdate, TRUE));                                       
      $this->assertEquals($crudUpdate->getStatusMessage(), "Bad Request", "Debugging information: ".var_export($crudUpdate, TRUE));
      $this->assertEquals($crudUpdate->error->id, "WS-CRUD-UPDATE-307", "Debugging information: ".var_export($crudUpdate, TRUE));                                       

      utilities\deleteUnrevisionedRecord();
      
      unset($crudUpdate);
      unset($settings);  
    }    
    
    public function testUpdateLatestUnpublished() {
      
      $settings = new Config();  
      
      utilities\deleteRevisionedRecord();
      
      $this->assertTrue(utilities\createRevisionedRecord(FALSE), "Can't create the unrevisioned records...");
                 
      $crudUpdate = new CrudUpdateQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
      
      $crudUpdate->createRevision()
                 ->isPublished()
                 ->dataset($settings->testDataset)
                 ->document(file_get_contents($settings->contentDir.'crud_update.n3'))
                 ->documentMimeIsRdfN3()
                 ->sourceInterface($settings->crudUpdateInterface)
                 ->sourceInterfaceVersion($settings->crudUpdateInterfaceVersion)
                 ->send();
                           
      $this->assertEquals($crudUpdate->getStatus(), "400", "Debugging information: ".var_export($crudUpdate, TRUE));                                       
      $this->assertEquals($crudUpdate->getStatusMessage(), "Bad Request", "Debugging information: ".var_export($crudUpdate, TRUE));
      $this->assertEquals($crudUpdate->error->id, "WS-CRUD-UPDATE-313", "Debugging information: ".var_export($crudUpdate, TRUE));                                       

      utilities\deleteUnrevisionedRecord();
      
      unset($crudCreate);
      unset($settings);  
    }    
    
    public function testUpdateCreateNoRevision() {
      
      $settings = new Config();  
      
      utilities\deleteRevisionedRecord();
      
      $this->assertTrue(utilities\createRevisionedRecord(FALSE), "Can't create the unrevisioned records...");
                 
      $crudUpdate = new CrudUpdateQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
      
      $crudUpdate->ignoreRevision()
                 ->isPublished()
                 ->dataset($settings->testDataset)
                 ->document(file_get_contents($settings->contentDir.'crud_update.n3'))
                 ->documentMimeIsRdfN3()
                 ->sourceInterface($settings->crudUpdateInterface)
                 ->sourceInterfaceVersion($settings->crudUpdateInterfaceVersion)
                 ->send();
                           
      $this->assertEquals($crudUpdate->getStatus(), "200", "Debugging information: ".var_export($crudUpdate, TRUE));        

      $revisionLister = new RevisionListerQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
      
      $revisionLister->dataset($settings->testDataset)
                     ->shortResults()
                     ->uri('http://foo.com/datasets/tests/foo')
                     ->mime('resultset')
                     ->send();
                     
      $this->assertEquals($revisionLister->getStatus(), "200", "Debugging information: ".var_export($revisionLister, TRUE));        
      
      $resultset = $revisionLister->getResultset()->getResultset();

      $this->assertTrue((count($resultset['unspecified']) == 2), "Debugging information: ".var_export($resultset, TRUE));        
      
      utilities\deleteUnrevisionedRecord();
      
      unset($crudCreate);
      unset($settings);  
    }    

    public function testUpdate_withRevision_UnrevisionedRecord_Published_RDFN3() {
      
      $settings = new Config();  
      
      utilities\deleteUnrevisionedRecord();
      
      $this->assertTrue(utilities\createUnrevisionedRecord(), "Can't create the unrevisioned records...");
                 
      $crudUpdate = new CrudUpdateQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
      
      $crudUpdate->createRevision()
                 ->isPublished()
                 ->dataset($settings->testDataset)
                 ->document(file_get_contents($settings->contentDir.'crud_update.n3'))
                 ->documentMimeIsRdfN3()
                 ->sourceInterface($settings->crudUpdateInterface)
                 ->sourceInterfaceVersion($settings->crudUpdateInterfaceVersion)
                 ->send();
                           
      $this->assertEquals($crudUpdate->getStatus(), "200", "Debugging information: ".var_export($crudUpdate, TRUE));                                       

      utilities\deleteUnrevisionedRecord();
      
      unset($crudCreate);
      unset($settings);  
    }   
    
    public function testUpdate_withRevision_RevisionedRecord_Published_RDFN3() {
      
      $settings = new Config();  
      
      utilities\deleteRevisionedRecord();
      
      $this->assertTrue(utilities\createRevisionedRecord(), "Can't create the revisioned records...");

      $crudUpdate = new CrudUpdateQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
      
      $crudUpdate->createRevision()
                 ->isPublished()
                 ->dataset($settings->testDataset)
                 ->document(file_get_contents($settings->contentDir.'crud_update.n3'))
                 ->documentMimeIsRdfN3()
                 ->sourceInterface($settings->crudUpdateInterface)
                 ->sourceInterfaceVersion($settings->crudUpdateInterfaceVersion)
                 ->send();
                           
      $this->assertEquals($crudUpdate->getStatus(), "200", "Debugging information: ".var_export($crudUpdate, TRUE));                                       

      utilities\deleteRevisionedRecord();
      
      unset($crudCreate);
      unset($settings);  
    } 
    

    public function testUpdate_withRevision_UnrevisionedRecord_Published_RDFXML() {
      
      $settings = new Config();  
      
      utilities\deleteUnrevisionedRecord();
      
      $this->assertTrue(utilities\createUnrevisionedRecord(), "Can't create the unrevisioned records...");
                 
      $crudUpdate = new CrudUpdateQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
      
      $crudUpdate->createRevision()
                 ->isPublished()
                 ->dataset($settings->testDataset)
                 ->document(file_get_contents($settings->contentDir.'crud_update.xml'))
                 ->documentMimeIsRdfXml()
                 ->sourceInterface($settings->crudUpdateInterface)
                 ->sourceInterfaceVersion($settings->crudUpdateInterfaceVersion)
                 ->send();
                           
      $this->assertEquals($crudUpdate->getStatus(), "200", "Debugging information: ".var_export($crudUpdate, TRUE));                                       

      utilities\deleteUnrevisionedRecord();
      
      unset($crudCreate);
      unset($settings);  
    }   
    
    public function testUpdate_withRevision_RevisionedRecord_Published_RDFXML() {
      
      $settings = new Config();  
      
      utilities\deleteRevisionedRecord();
      
      $this->assertTrue(utilities\createRevisionedRecord(), "Can't create the revisioned records...");

      $crudUpdate = new CrudUpdateQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
      
      $crudUpdate->createRevision()
                 ->isPublished()
                 ->dataset($settings->testDataset)
                 ->document(file_get_contents($settings->contentDir.'crud_update.xml'))
                 ->documentMimeIsRdfXml()
                 ->sourceInterface($settings->crudUpdateInterface)
                 ->sourceInterfaceVersion($settings->crudUpdateInterfaceVersion)
                 ->send();
                           
      $this->assertEquals($crudUpdate->getStatus(), "200", "Debugging information: ".var_export($crudUpdate, TRUE));                                       

      utilities\deleteRevisionedRecord();
      
      unset($crudCreate);
      unset($settings);  
    }     
    
    public function testUpdate_withRevision_UnrevisionedRecord_Achived_RDFN3() {
      
      $settings = new Config();  
      
      utilities\deleteUnrevisionedRecord();
      
      $this->assertTrue(utilities\createUnrevisionedRecord(), "Can't create the unrevisioned records...");
                 
      $crudUpdate = new CrudUpdateQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
      
      $crudUpdate->createRevision()
                 ->isArchive()
                 ->dataset($settings->testDataset)
                 ->document(file_get_contents($settings->contentDir.'crud_update.n3'))
                 ->documentMimeIsRdfN3()
                 ->sourceInterface($settings->crudUpdateInterface)
                 ->sourceInterfaceVersion($settings->crudUpdateInterfaceVersion)
                 ->send();
                           
      $this->assertEquals($crudUpdate->getStatus(), "200", "Debugging information: ".var_export($crudUpdate, TRUE));                                       

      utilities\deleteUnrevisionedRecord();
      
      unset($crudCreate);
      unset($settings);  
    }   
    
    public function testUpdate_withRevision_RevisionedRecord_Achived_RDFN3() {
      
      $settings = new Config();  
      
      utilities\deleteRevisionedRecord();
      
      $this->assertTrue(utilities\createRevisionedRecord(), "Can't create the revisioned records...");

      $crudUpdate = new CrudUpdateQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
      
      $crudUpdate->createRevision()
                 ->isArchive()
                 ->dataset($settings->testDataset)
                 ->document(file_get_contents($settings->contentDir.'crud_update.n3'))
                 ->documentMimeIsRdfN3()
                 ->sourceInterface($settings->crudUpdateInterface)
                 ->sourceInterfaceVersion($settings->crudUpdateInterfaceVersion)
                 ->send();
                           
      $this->assertEquals($crudUpdate->getStatus(), "200", "Debugging information: ".var_export($crudUpdate, TRUE));                                       

      utilities\deleteRevisionedRecord();
      
      unset($crudCreate);
      unset($settings);  
    } 
    

    public function testUpdate_withRevision_UnrevisionedRecord_Achived_RDFXML() {
      
      $settings = new Config();  
      
      utilities\deleteUnrevisionedRecord();
      
      $this->assertTrue(utilities\createUnrevisionedRecord(), "Can't create the unrevisioned records...");
                 
      $crudUpdate = new CrudUpdateQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
      
      $crudUpdate->createRevision()
                 ->isArchive()
                 ->dataset($settings->testDataset)
                 ->document(file_get_contents($settings->contentDir.'crud_update.xml'))
                 ->documentMimeIsRdfXml()
                 ->sourceInterface($settings->crudUpdateInterface)
                 ->sourceInterfaceVersion($settings->crudUpdateInterfaceVersion)
                 ->send();
                           
      $this->assertEquals($crudUpdate->getStatus(), "200", "Debugging information: ".var_export($crudUpdate, TRUE));                                       

      utilities\deleteUnrevisionedRecord();
      
      unset($crudCreate);
      unset($settings);  
    }   
    
    public function testUpdate_withRevision_RevisionedRecord_Achived_RDFXML() {
      
      $settings = new Config();  
      
      utilities\deleteRevisionedRecord();
      
      $this->assertTrue(utilities\createRevisionedRecord(), "Can't create the revisioned records...");

      $crudUpdate = new CrudUpdateQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
      
      $crudUpdate->createRevision()
                 ->isArchive()
                 ->dataset($settings->testDataset)
                 ->document(file_get_contents($settings->contentDir.'crud_update.xml'))
                 ->documentMimeIsRdfXml()
                 ->sourceInterface($settings->crudUpdateInterface)
                 ->sourceInterfaceVersion($settings->crudUpdateInterfaceVersion)
                 ->send();
                           
      $this->assertEquals($crudUpdate->getStatus(), "200", "Debugging information: ".var_export($crudUpdate, TRUE));                                       

      utilities\deleteRevisionedRecord();
      
      unset($crudCreate);
      unset($settings);  
    }  
    
    public function testUpdate_withRevision_UnrevisionedRecord_Experimental_RDFN3() {
      
      $settings = new Config();  
      
      utilities\deleteUnrevisionedRecord();
      
      $this->assertTrue(utilities\createUnrevisionedRecord(), "Can't create the unrevisioned records...");
                 
      $crudUpdate = new CrudUpdateQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
      
      $crudUpdate->createRevision()
                 ->isExperimental()
                 ->dataset($settings->testDataset)
                 ->document(file_get_contents($settings->contentDir.'crud_update.n3'))
                 ->documentMimeIsRdfN3()
                 ->sourceInterface($settings->crudUpdateInterface)
                 ->sourceInterfaceVersion($settings->crudUpdateInterfaceVersion)
                 ->send();
                           
      $this->assertEquals($crudUpdate->getStatus(), "200", "Debugging information: ".var_export($crudUpdate, TRUE));                                       

      utilities\deleteUnrevisionedRecord();
      
      unset($crudCreate);
      unset($settings);  
    }   
    
    public function testUpdate_withRevision_RevisionedRecord_Experimental_RDFN3() {
      
      $settings = new Config();  
      
      utilities\deleteRevisionedRecord();
      
      $this->assertTrue(utilities\createRevisionedRecord(), "Can't create the revisioned records...");

      $crudUpdate = new CrudUpdateQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
      
      $crudUpdate->createRevision()
                 ->isExperimental()
                 ->dataset($settings->testDataset)
                 ->document(file_get_contents($settings->contentDir.'crud_update.n3'))
                 ->documentMimeIsRdfN3()
                 ->sourceInterface($settings->crudUpdateInterface)
                 ->sourceInterfaceVersion($settings->crudUpdateInterfaceVersion)
                 ->send();
                           
      $this->assertEquals($crudUpdate->getStatus(), "200", "Debugging information: ".var_export($crudUpdate, TRUE));                                       

      utilities\deleteRevisionedRecord();
      
      unset($crudCreate);
      unset($settings);  
    } 
    

    public function testUpdate_withRevision_UnrevisionedRecord_Experimental_RDFXML() {
      
      $settings = new Config();  
      
      utilities\deleteUnrevisionedRecord();
      
      $this->assertTrue(utilities\createUnrevisionedRecord(), "Can't create the unrevisioned records...");
                 
      $crudUpdate = new CrudUpdateQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
      
      $crudUpdate->createRevision()
                 ->isExperimental()
                 ->dataset($settings->testDataset)
                 ->document(file_get_contents($settings->contentDir.'crud_update.xml'))
                 ->documentMimeIsRdfXml()
                 ->sourceInterface($settings->crudUpdateInterface)
                 ->sourceInterfaceVersion($settings->crudUpdateInterfaceVersion)
                 ->send();
                           
      $this->assertEquals($crudUpdate->getStatus(), "200", "Debugging information: ".var_export($crudUpdate, TRUE));                                       

      utilities\deleteUnrevisionedRecord();
      
      unset($crudCreate);
      unset($settings);  
    }   
    
    public function testUpdate_withRevision_RevisionedRecord_Experimental_RDFXML() {
      
      $settings = new Config();  
      
      utilities\deleteRevisionedRecord();
      
      $this->assertTrue(utilities\createRevisionedRecord(), "Can't create the revisioned records...");

      $crudUpdate = new CrudUpdateQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
      
      $crudUpdate->createRevision()
                 ->isExperimental()
                 ->dataset($settings->testDataset)
                 ->document(file_get_contents($settings->contentDir.'crud_update.xml'))
                 ->documentMimeIsRdfXml()
                 ->sourceInterface($settings->crudUpdateInterface)
                 ->sourceInterfaceVersion($settings->crudUpdateInterfaceVersion)
                 ->send();
                           
      $this->assertEquals($crudUpdate->getStatus(), "200", "Debugging information: ".var_export($crudUpdate, TRUE));                                       

      utilities\deleteRevisionedRecord();
      
      unset($crudCreate);
      unset($settings);  
    }
    
    public function testUpdate_withRevision_UnrevisionedRecord_Harvesting_RDFN3() {
      
      $settings = new Config();  
      
      utilities\deleteUnrevisionedRecord();
      
      $this->assertTrue(utilities\createUnrevisionedRecord(), "Can't create the unrevisioned records...");
                 
      $crudUpdate = new CrudUpdateQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
      
      $crudUpdate->createRevision()
                 ->isHarvesting()
                 ->dataset($settings->testDataset)
                 ->document(file_get_contents($settings->contentDir.'crud_update.n3'))
                 ->documentMimeIsRdfN3()
                 ->sourceInterface($settings->crudUpdateInterface)
                 ->sourceInterfaceVersion($settings->crudUpdateInterfaceVersion)
                 ->send();
                           
      $this->assertEquals($crudUpdate->getStatus(), "200", "Debugging information: ".var_export($crudUpdate, TRUE));                                       

      utilities\deleteUnrevisionedRecord();
      
      unset($crudCreate);
      unset($settings);  
    }   
    
    public function testUpdate_withRevision_RevisionedRecord_Harvesting_RDFN3() {
      
      $settings = new Config();  
      
      utilities\deleteRevisionedRecord();
      
      $this->assertTrue(utilities\createRevisionedRecord(), "Can't create the revisioned records...");

      $crudUpdate = new CrudUpdateQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
      
      $crudUpdate->createRevision()
                 ->isHarvesting()
                 ->dataset($settings->testDataset)
                 ->document(file_get_contents($settings->contentDir.'crud_update.n3'))
                 ->documentMimeIsRdfN3()
                 ->sourceInterface($settings->crudUpdateInterface)
                 ->sourceInterfaceVersion($settings->crudUpdateInterfaceVersion)
                 ->send();
                           
      $this->assertEquals($crudUpdate->getStatus(), "200", "Debugging information: ".var_export($crudUpdate, TRUE));                                       

      utilities\deleteRevisionedRecord();
      
      unset($crudCreate);
      unset($settings);  
    } 
    

    public function testUpdate_withRevision_UnrevisionedRecord_Harvesting_RDFXML() {
      
      $settings = new Config();  
      
      utilities\deleteUnrevisionedRecord();
      
      $this->assertTrue(utilities\createUnrevisionedRecord(), "Can't create the unrevisioned records...");
                 
      $crudUpdate = new CrudUpdateQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
      
      $crudUpdate->createRevision()
                 ->isHarvesting()
                 ->dataset($settings->testDataset)
                 ->document(file_get_contents($settings->contentDir.'crud_update.xml'))
                 ->documentMimeIsRdfXml()
                 ->sourceInterface($settings->crudUpdateInterface)
                 ->sourceInterfaceVersion($settings->crudUpdateInterfaceVersion)
                 ->send();
                           
      $this->assertEquals($crudUpdate->getStatus(), "200", "Debugging information: ".var_export($crudUpdate, TRUE));                                       

      utilities\deleteUnrevisionedRecord();
      
      unset($crudCreate);
      unset($settings);  
    }   
    
    public function testUpdate_withRevision_RevisionedRecord_Harvesting_RDFXML() {
      
      $settings = new Config();  
      
      utilities\deleteRevisionedRecord();
      
      $this->assertTrue(utilities\createRevisionedRecord(), "Can't create the revisioned records...");

      $crudUpdate = new CrudUpdateQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
      
      $crudUpdate->createRevision()
                 ->isHarvesting()
                 ->dataset($settings->testDataset)
                 ->document(file_get_contents($settings->contentDir.'crud_update.xml'))
                 ->documentMimeIsRdfXml()
                 ->sourceInterface($settings->crudUpdateInterface)
                 ->sourceInterfaceVersion($settings->crudUpdateInterfaceVersion)
                 ->send();
                           
      $this->assertEquals($crudUpdate->getStatus(), "200", "Debugging information: ".var_export($crudUpdate, TRUE));                                       

      utilities\deleteRevisionedRecord();
      
      unset($crudCreate);
      unset($settings);  
    }    
    
    public function testUpdate_withRevision_UnrevisionedRecord_PreRelease_RDFN3() {
      
      $settings = new Config();  
      
      utilities\deleteUnrevisionedRecord();
      
      $this->assertTrue(utilities\createUnrevisionedRecord(), "Can't create the unrevisioned records...");
                 
      $crudUpdate = new CrudUpdateQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
      
      $crudUpdate->createRevision()
                 ->isPreRelease()
                 ->dataset($settings->testDataset)
                 ->document(file_get_contents($settings->contentDir.'crud_update.n3'))
                 ->documentMimeIsRdfN3()
                 ->sourceInterface($settings->crudUpdateInterface)
                 ->sourceInterfaceVersion($settings->crudUpdateInterfaceVersion)
                 ->send();
                           
      $this->assertEquals($crudUpdate->getStatus(), "200", "Debugging information: ".var_export($crudUpdate, TRUE));                                       

      utilities\deleteUnrevisionedRecord();
      
      unset($crudCreate);
      unset($settings);  
    }   
    
    public function testUpdate_withRevision_RevisionedRecord_PreRelease_RDFN3() {
      
      $settings = new Config();  
      
      utilities\deleteRevisionedRecord();
      
      $this->assertTrue(utilities\createRevisionedRecord(), "Can't create the revisioned records...");

      $crudUpdate = new CrudUpdateQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
      
      $crudUpdate->createRevision()
                 ->isPreRelease()
                 ->dataset($settings->testDataset)
                 ->document(file_get_contents($settings->contentDir.'crud_update.n3'))
                 ->documentMimeIsRdfN3()
                 ->sourceInterface($settings->crudUpdateInterface)
                 ->sourceInterfaceVersion($settings->crudUpdateInterfaceVersion)
                 ->send();
                           
      $this->assertEquals($crudUpdate->getStatus(), "200", "Debugging information: ".var_export($crudUpdate, TRUE));                                       

      utilities\deleteRevisionedRecord();
      
      unset($crudCreate);
      unset($settings);  
    } 
    

    public function testUpdate_withRevision_UnrevisionedRecord_PreRelease_RDFXML() {
      
      $settings = new Config();  
      
      utilities\deleteUnrevisionedRecord();
      
      $this->assertTrue(utilities\createUnrevisionedRecord(), "Can't create the unrevisioned records...");
                 
      $crudUpdate = new CrudUpdateQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
      
      $crudUpdate->createRevision()
                 ->isPreRelease()
                 ->dataset($settings->testDataset)
                 ->document(file_get_contents($settings->contentDir.'crud_update.xml'))
                 ->documentMimeIsRdfXml()
                 ->sourceInterface($settings->crudUpdateInterface)
                 ->sourceInterfaceVersion($settings->crudUpdateInterfaceVersion)
                 ->send();
                           
      $this->assertEquals($crudUpdate->getStatus(), "200", "Debugging information: ".var_export($crudUpdate, TRUE));                                       

      utilities\deleteUnrevisionedRecord();
      
      unset($crudCreate);
      unset($settings);  
    }   
    
    public function testUpdate_withRevision_RevisionedRecord_PreRelease_RDFXML() {
      
      $settings = new Config();  
      
      utilities\deleteRevisionedRecord();
      
      $this->assertTrue(utilities\createRevisionedRecord(), "Can't create the revisioned records...");

      $crudUpdate = new CrudUpdateQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
      
      $crudUpdate->createRevision()
                 ->isPreRelease()
                 ->dataset($settings->testDataset)
                 ->document(file_get_contents($settings->contentDir.'crud_update.xml'))
                 ->documentMimeIsRdfXml()
                 ->sourceInterface($settings->crudUpdateInterface)
                 ->sourceInterfaceVersion($settings->crudUpdateInterfaceVersion)
                 ->send();
                           
      $this->assertEquals($crudUpdate->getStatus(), "200", "Debugging information: ".var_export($crudUpdate, TRUE));                                       

      utilities\deleteRevisionedRecord();
      
      unset($crudCreate);
      unset($settings);  
    }    
    
    public function testUpdate_withRevision_UnrevisionedRecord_Staging_RDFN3() {
      
      $settings = new Config();  
      
      utilities\deleteUnrevisionedRecord();
      
      $this->assertTrue(utilities\createUnrevisionedRecord(), "Can't create the unrevisioned records...");
                 
      $crudUpdate = new CrudUpdateQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
      
      $crudUpdate->createRevision()
                 ->isStaging()
                 ->dataset($settings->testDataset)
                 ->document(file_get_contents($settings->contentDir.'crud_update.n3'))
                 ->documentMimeIsRdfN3()
                 ->sourceInterface($settings->crudUpdateInterface)
                 ->sourceInterfaceVersion($settings->crudUpdateInterfaceVersion)
                 ->send();
                           
      $this->assertEquals($crudUpdate->getStatus(), "200", "Debugging information: ".var_export($crudUpdate, TRUE));                                       

      utilities\deleteUnrevisionedRecord();
      
      unset($crudCreate);
      unset($settings);  
    }   
    
    public function testUpdate_withRevision_RevisionedRecord_Staging_RDFN3() {
      
      $settings = new Config();  
      
      utilities\deleteRevisionedRecord();
      
      $this->assertTrue(utilities\createRevisionedRecord(), "Can't create the revisioned records...");

      $crudUpdate = new CrudUpdateQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
      
      $crudUpdate->createRevision()
                 ->isStaging()
                 ->dataset($settings->testDataset)
                 ->document(file_get_contents($settings->contentDir.'crud_update.n3'))
                 ->documentMimeIsRdfN3()
                 ->sourceInterface($settings->crudUpdateInterface)
                 ->sourceInterfaceVersion($settings->crudUpdateInterfaceVersion)
                 ->send();
                           
      $this->assertEquals($crudUpdate->getStatus(), "200", "Debugging information: ".var_export($crudUpdate, TRUE));                                       

      utilities\deleteRevisionedRecord();
      
      unset($crudCreate);
      unset($settings);  
    } 
    

    public function testUpdate_withRevision_UnrevisionedRecord_Staging_RDFXML() {
      
      $settings = new Config();  
      
      utilities\deleteUnrevisionedRecord();
      
      $this->assertTrue(utilities\createUnrevisionedRecord(), "Can't create the unrevisioned records...");
                 
      $crudUpdate = new CrudUpdateQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
      
      $crudUpdate->createRevision()
                 ->isStaging()
                 ->dataset($settings->testDataset)
                 ->document(file_get_contents($settings->contentDir.'crud_update.xml'))
                 ->documentMimeIsRdfXml()
                 ->sourceInterface($settings->crudUpdateInterface)
                 ->sourceInterfaceVersion($settings->crudUpdateInterfaceVersion)
                 ->send();
                           
      $this->assertEquals($crudUpdate->getStatus(), "200", "Debugging information: ".var_export($crudUpdate, TRUE));                                       

      utilities\deleteUnrevisionedRecord();
      
      unset($crudCreate);
      unset($settings);  
    }   
    
    public function testUpdate_withRevision_RevisionedRecord_Staging_RDFXML() {
      
      $settings = new Config();  
      
      utilities\deleteRevisionedRecord();
      
      $this->assertTrue(utilities\createRevisionedRecord(), "Can't create the revisioned records...");

      $crudUpdate = new CrudUpdateQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
      
      $crudUpdate->createRevision()
                 ->isStaging()
                 ->dataset($settings->testDataset)
                 ->document(file_get_contents($settings->contentDir.'crud_update.xml'))
                 ->documentMimeIsRdfXml()
                 ->sourceInterface($settings->crudUpdateInterface)
                 ->sourceInterfaceVersion($settings->crudUpdateInterfaceVersion)
                 ->send();
                           
      $this->assertEquals($crudUpdate->getStatus(), "200", "Debugging information: ".var_export($crudUpdate, TRUE));                                       

      utilities\deleteRevisionedRecord();
      
      unset($crudCreate);
      unset($settings);  
    }
    
    public function testUpdate_withRevision_UnrevisionedRecord_Unspecified_RDFN3() {
      
      $settings = new Config();  
      
      utilities\deleteUnrevisionedRecord();
      
      $this->assertTrue(utilities\createUnrevisionedRecord(), "Can't create the unrevisioned records...");
                 
      $crudUpdate = new CrudUpdateQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
      
      $crudUpdate->createRevision()
                 ->isUnspecified()
                 ->dataset($settings->testDataset)
                 ->document(file_get_contents($settings->contentDir.'crud_update.n3'))
                 ->documentMimeIsRdfN3()
                 ->sourceInterface($settings->crudUpdateInterface)
                 ->sourceInterfaceVersion($settings->crudUpdateInterfaceVersion)
                 ->send();
                           
      $this->assertEquals($crudUpdate->getStatus(), "200", "Debugging information: ".var_export($crudUpdate, TRUE));                                       

      utilities\deleteUnrevisionedRecord();
      
      unset($crudCreate);
      unset($settings);  
    }   
    
    public function testUpdate_withRevision_RevisionedRecord_Unspecified_RDFN3() {
      
      $settings = new Config();  
      
      utilities\deleteRevisionedRecord();
      
      $this->assertTrue(utilities\createRevisionedRecord(), "Can't create the revisioned records...");

      $crudUpdate = new CrudUpdateQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
      
      $crudUpdate->createRevision()
                 ->isUnspecified()
                 ->dataset($settings->testDataset)
                 ->document(file_get_contents($settings->contentDir.'crud_update.n3'))
                 ->documentMimeIsRdfN3()
                 ->sourceInterface($settings->crudUpdateInterface)
                 ->sourceInterfaceVersion($settings->crudUpdateInterfaceVersion)
                 ->send();
                           
      $this->assertEquals($crudUpdate->getStatus(), "200", "Debugging information: ".var_export($crudUpdate, TRUE));                                       

      utilities\deleteRevisionedRecord();
      
      unset($crudCreate);
      unset($settings);  
    } 
    

    public function testUpdate_withRevision_UnrevisionedRecord_Unspecified_RDFXML() {
      
      $settings = new Config();  
      
      utilities\deleteUnrevisionedRecord();
      
      $this->assertTrue(utilities\createUnrevisionedRecord(), "Can't create the unrevisioned records...");
                 
      $crudUpdate = new CrudUpdateQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
      
      $crudUpdate->createRevision()
                 ->isUnspecified()
                 ->dataset($settings->testDataset)
                 ->document(file_get_contents($settings->contentDir.'crud_update.xml'))
                 ->documentMimeIsRdfXml()
                 ->sourceInterface($settings->crudUpdateInterface)
                 ->sourceInterfaceVersion($settings->crudUpdateInterfaceVersion)
                 ->send();
                           
      $this->assertEquals($crudUpdate->getStatus(), "200", "Debugging information: ".var_export($crudUpdate, TRUE));                                       

      utilities\deleteUnrevisionedRecord();
      
      unset($crudCreate);
      unset($settings);  
    }   
    
    public function testUpdate_withRevision_RevisionedRecord_Unspecified_RDFXML() {
      
      $settings = new Config();  
      
      utilities\deleteRevisionedRecord();
      
      $this->assertTrue(utilities\createRevisionedRecord(), "Can't create the revisioned records...");

      $crudUpdate = new CrudUpdateQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
      
      $crudUpdate->createRevision()
                 ->isUnspecified()
                 ->dataset($settings->testDataset)
                 ->document(file_get_contents($settings->contentDir.'crud_update.xml'))
                 ->documentMimeIsRdfXml()
                 ->sourceInterface($settings->crudUpdateInterface)
                 ->sourceInterfaceVersion($settings->crudUpdateInterfaceVersion)
                 ->send();
                           
      $this->assertEquals($crudUpdate->getStatus(), "200", "Debugging information: ".var_export($crudUpdate, TRUE));                                       

      utilities\deleteRevisionedRecord();
      
      unset($crudCreate);
      unset($settings);  
    }    
  }

  
?>