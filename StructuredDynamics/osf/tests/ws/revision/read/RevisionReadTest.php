<?php

  namespace StructuredDynamics\osf\tests\ws\revision\read;
  
  use StructuredDynamics\osf\framework\WebServiceQuerier;
  use StructuredDynamics\osf\php\api\ws\revision\lister\RevisionListerQuery;
  use StructuredDynamics\osf\php\api\ws\revision\read\RevisionReadQuery;
  use StructuredDynamics\osf\framework\Resultset;
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
  
  class RevisionReadTest extends \PHPUnit_Framework_TestCase {
    
    static private $outputs = array();

    public function testWrongEndpointUrl() {
      
      $settings = new Config();          
      
      $wsq = new WebServiceQuerier($settings->endpointUrl . "revision/read/" . "wrong", 
                                   "get", 
                                   "text/xml",
                                   "revuri=" . urlencode('') .
                                   "&dataset=" . urlencode($settings->testDataset) .
                                   "&mode=" . urlencode('revision') .
                                   "&interface=". urlencode($settings->revisionReadInterface) .
                                   "&version=". urlencode($settings->revisionReadInterfaceVersion),
                                   $settings->applicationID,
                                   $settings->apiKey,
                                   $settings->userID);
                         
      $this->assertEquals($wsq->getStatus(), "404", "Debugging information: ".var_export($wsq, TRUE));                                       
      $this->assertEquals($wsq->getStatusMessage(), "Not Found", "Debugging information: ".var_export($wsq, TRUE));
      
      unset($wsq);
      unset($settings);
    }
    
    public function testWrongEndpointMethodPost() {
      
      $settings = new Config();  
      
      $wsq = new WebServiceQuerier($settings->endpointUrl . "revision/read/", 
                                   "post", 
                                   "text/xml",
                                   "revuri=" . urlencode('') .
                                   "&dataset=" . urlencode($settings->testDataset) .
                                   "&mode=" . urlencode('revision') .
                                   "&interface=". urlencode($settings->revisionReadInterface) .
                                   "&version=". urlencode($settings->revisionReadInterfaceVersion),
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
      
      utilities\deleteRevisionedRecord();
      
      $this->assertTrue(utilities\createRevisionedRecord(), "Can't create unrevisioned records...");

      $revuri = utilities\getLastRevisionUri('http://foo.com/datasets/tests/foo');
      
      $this->assertFalse($revuri === FALSE, "Debugging information: ".var_export($revuri, TRUE));                                       

      $revisionRead = new RevisionReadQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);      
      
      $revisionRead->dataset($settings->testDataset)
                   ->revisionUri($revuri)
                   ->getRevision()
                   ->sourceInterface($settings->revisionReadInterface)
                   ->sourceInterfaceVersion($settings->revisionReadInterfaceVersion)                   
                   ->send();
                           
      $this->assertEquals($revisionRead->getStatus(), "200", "Debugging information: ".var_export($revisionRead, TRUE));                                       

      utilities\deleteRevisionedRecord();

      unset($revisionRead);
      unset($settings);    
    }
    

    public function testInvalidInterfaceVersion() {
      
       $settings = new Config();  
      
      utilities\deleteRevisionedRecord();
      
      $this->assertTrue(utilities\createRevisionedRecord(), "Can't create unrevisioned records...");

      $revuri = utilities\getLastRevisionUri('http://foo.com/datasets/tests/foo');
      
      $this->assertFalse($revuri === FALSE, "Debugging information: ".var_export($revuri, TRUE));                                       

      $revisionRead = new RevisionReadQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);      
      
      $revisionRead->dataset($settings->testDataset)
                   ->revisionUri($revuri)
                   ->getRevision()
                   ->sourceInterface($settings->revisionReadInterface)
                   ->sourceInterfaceVersion('667.7')                   
                   ->send();
                           
      $this->assertEquals($revisionRead->getStatus(), "400", "Debugging information: ".var_export($revisionRead, TRUE));                                       
      $this->assertEquals($revisionRead->getStatusMessage(), "Bad Request", "Debugging information: ".var_export($revisionRead, TRUE));
      $this->assertEquals($revisionRead->error->id, "WS-REVISION-READ-302", "Debugging information: ".var_export($revisionRead, TRUE));                                       

      utilities\deleteRevisionedRecord();

      unset($revisionDelete);
      unset($settings);                 
    }  
    
    public function testInterfaceExists() {
      $settings = new Config();  
      
      utilities\deleteRevisionedRecord();
      
      $this->assertTrue(utilities\createRevisionedRecord(), "Can't create unrevisioned records...");

      $revuri = utilities\getLastRevisionUri('http://foo.com/datasets/tests/foo');
      
      $this->assertFalse($revuri === FALSE, "Debugging information: ".var_export($revuri, TRUE));                                       

      $revisionRead = new RevisionReadQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);      
      
      $revisionRead->dataset($settings->testDataset)
                   ->revisionUri($revuri)
                   ->getRevision()
                   ->sourceInterface($settings->revisionReadInterface)
                   ->sourceInterfaceVersion($settings->revisionReadInterfaceVersion)                   
                   ->send();
                           
      $this->assertEquals($revisionRead->getStatus(), "200", "Debugging information: ".var_export($revisionRead, TRUE));                                       

      utilities\deleteRevisionedRecord();

      unset($revisionRead);
      unset($settings);
    }   
    
    public function testInterfaceNotExisting() {
      $settings = new Config();  
      
      utilities\deleteRevisionedRecord();
      
      $this->assertTrue(utilities\createRevisionedRecord(), "Can't create unrevisioned records...");

      $revuri = utilities\getLastRevisionUri('http://foo.com/datasets/tests/foo');
      
      $this->assertFalse($revuri === FALSE, "Debugging information: ".var_export($revuri, TRUE));                                       

      $revisionRead = new RevisionReadQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);      
      
      $revisionRead->dataset($settings->testDataset)
                   ->revisionUri($revuri)
                   ->getRevision()
                   ->sourceInterface('unexisting-interface')
                   ->sourceInterfaceVersion($settings->revisionReadInterfaceVersion)                   
                   ->send();
                           
      $this->assertEquals($revisionRead->getStatus(), "400", "Debugging information: ".var_export($revisionRead, TRUE));                                       
      $this->assertEquals($revisionRead->getStatusMessage(), "Bad Request", "Debugging information: ".var_export($revisionRead, TRUE));
      $this->assertEquals($revisionRead->error->id, "WS-REVISION-READ-300", "Debugging information: ".var_export($revisionRead, TRUE));                                       

      utilities\deleteRevisionedRecord();

      unset($revisionRead);
      unset($settings);       
   }  

   public function testModeNotExisting() {
      $settings = new Config();  
      
      utilities\deleteRevisionedRecord();
      
      $this->assertTrue(utilities\createRevisionedRecord(), "Can't create unrevisioned records...");

      $revuri = utilities\getLastRevisionUri('http://foo.com/datasets/tests/foo');
      
      $this->assertFalse($revuri === FALSE, "Debugging information: ".var_export($revuri, TRUE));                                       
      
      $wsq = new WebServiceQuerier($settings->endpointUrl . "revision/read/", 
                                   "get", 
                                   "text/xml",
                                   "revuri=" . urlencode($revuri) .
                                   "&dataset=" . urlencode($settings->testDataset) .
                                   "&mode=" . urlencode('non-existing') .
                                   "&interface=". urlencode($settings->revisionReadInterface) .
                                   "&version=". urlencode($settings->revisionReadInterfaceVersion),
                                   $settings->applicationID,
                                   $settings->apiKey,
                                   $settings->userID);
   
                           
      $this->assertEquals($wsq->getStatus(), "400", "Debugging information: ".var_export($wsq, TRUE));                                       
      $this->assertEquals($wsq->getStatusMessage(), "Bad Request", "Debugging information: ".var_export($wsq, TRUE));
      $this->assertEquals($wsq->error->id, "WS-REVISION-READ-305", "Debugging information: ".var_export($wsq, TRUE));                                       

      utilities\deleteRevisionedRecord();

      unset($wsq);
      unset($settings);       
   }  
        
   public function testRevisionRecordNonExisting() {
      $settings = new Config();  
      
      utilities\deleteRevisionedRecord();
      
      $this->assertTrue(utilities\createRevisionedRecord(), "Can't create unrevisioned records...");

      $revuri = utilities\getLastRevisionUri('http://foo.com/datasets/tests/foo');
      
      $this->assertFalse($revuri === FALSE, "Debugging information: ".var_export($revuri, TRUE));                                       

      $revisionRead = new RevisionReadQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);      
      
      $revisionRead->dataset($settings->testDataset)
                   ->revisionUri($revuri.'unexisting')
                   ->getRevision()
                   ->sourceInterface($settings->revisionReadInterface)
                   ->sourceInterfaceVersion($settings->revisionReadInterfaceVersion)                   
                   ->send();
                           
      $this->assertEquals($revisionRead->getStatus(), "400", "Debugging information: ".var_export($revisionRead, TRUE));                                       
      $this->assertEquals($revisionRead->getStatusMessage(), "Bad Request", "Debugging information: ".var_export($revisionRead, TRUE));
      $this->assertEquals($revisionRead->error->id, "WS-REVISION-READ-306", "Debugging information: ".var_export($revisionRead, TRUE));                                       

      utilities\deleteRevisionedRecord();

      unset($revisionRead);
      unset($settings);
    }         
    
    public function testLastestRevision_RecordMode_RDFXML() {
      $settings = new Config();  
      
      utilities\deleteRevisionedRecord();
      
      $this->assertTrue(utilities\createRevisionedRecord(), "Can't create unrevisioned records...");

      $revuri = utilities\getLastRevisionUri('http://foo.com/datasets/tests/foo');
      
      $this->assertFalse($revuri === FALSE, "Debugging information: ".var_export($revuri, TRUE));                                       

      $revisionRead = new RevisionReadQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);      
      
      $revisionRead->dataset($settings->testDataset)
                   ->mime('application/rdf+xml')  
                   ->revisionUri($revuri)
                   ->getRecord()
                   ->sourceInterface($settings->revisionReadInterface)
                   ->sourceInterfaceVersion($settings->revisionReadInterfaceVersion)                   
                   ->send();

      $this->assertEquals($revisionRead->getStatus(), "200", "Debugging information: ".var_export($revisionRead, TRUE));                                       
      utilities\validateParameterApplicationRdfXml($this, $revisionRead);
      
      $this->assertTrue(utilities\compareRdf($revisionRead->getResultset(), file_get_contents($settings->contentDir.'validation/revision_read_lastestrevision_recordmode.xml')));

      utilities\deleteRevisionedRecord();

      unset($revisionRead);
      unset($settings);
    }
    
    public function testInitialRevision_RecordMode_RDFXML() {
      $settings = new Config();  
      
      utilities\deleteRevisionedRecord();
      
      $this->assertTrue(utilities\createRevisionedRecord(), "Can't create unrevisioned records...");

      $revuri = utilities\getInitialRevisionUri('http://foo.com/datasets/tests/foo');
      
      $this->assertFalse($revuri === FALSE, "Debugging information: ".var_export($revuri, TRUE));                                       

      $revisionRead = new RevisionReadQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);      
      
      $revisionRead->dataset($settings->testDataset)
                   ->mime('application/rdf+xml')  
                   ->revisionUri($revuri)
                   ->getRecord()
                   ->sourceInterface($settings->revisionReadInterface)
                   ->sourceInterfaceVersion($settings->revisionReadInterfaceVersion)                   
                   ->send();

      $this->assertEquals($revisionRead->getStatus(), "200", "Debugging information: ".var_export($revisionRead, TRUE));                                       
      utilities\validateParameterApplicationRdfXml($this, $revisionRead);
      
      $this->assertTrue(utilities\compareRdf($revisionRead->getResultset(), file_get_contents($settings->contentDir.'validation/revision_read_initialrevision_recordmode.xml')));

      utilities\deleteRevisionedRecord();

      unset($revisionRead);
      unset($settings);
    }               

  public function testLastestRevision_RecordMode_RDFN3() {
      $settings = new Config();  
      
      utilities\deleteRevisionedRecord();
      
      $this->assertTrue(utilities\createRevisionedRecord(), "Can't create unrevisioned records...");

      $revuri = utilities\getLastRevisionUri('http://foo.com/datasets/tests/foo');
      
      $this->assertFalse($revuri === FALSE, "Debugging information: ".var_export($revuri, TRUE));                                       

      $revisionRead = new RevisionReadQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);      
      
      $revisionRead->dataset($settings->testDataset)
                   ->mime('application/rdf+n3')  
                   ->revisionUri($revuri)
                   ->getRecord()
                   ->sourceInterface($settings->revisionReadInterface)
                   ->sourceInterfaceVersion($settings->revisionReadInterfaceVersion)                   
                   ->send();

      $this->assertEquals($revisionRead->getStatus(), "200", "Debugging information: ".var_export($revisionRead, TRUE));                                       
      
      utilities\validateParameterApplicationRdfN3($this, $revisionRead);
      
      $this->assertTrue(utilities\compareRdf($revisionRead->getResultset(), file_get_contents($settings->contentDir.'validation/revision_read_lastestrevision_recordmode.xml')));

      utilities\deleteRevisionedRecord();

      unset($revisionRead);
      unset($settings);
    }
    
    public function testInitialRevision_RecordMode_RDFN3() {
      $settings = new Config();  
      
      utilities\deleteRevisionedRecord();
      
      $this->assertTrue(utilities\createRevisionedRecord(), "Can't create unrevisioned records...");

      $revuri = utilities\getInitialRevisionUri('http://foo.com/datasets/tests/foo');
      
      $this->assertFalse($revuri === FALSE, "Debugging information: ".var_export($revuri, TRUE));                                       

      $revisionRead = new RevisionReadQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);      
      
      $revisionRead->dataset($settings->testDataset)
                   ->mime('application/rdf+n3')  
                   ->revisionUri($revuri)
                   ->getRecord()
                   ->sourceInterface($settings->revisionReadInterface)
                   ->sourceInterfaceVersion($settings->revisionReadInterfaceVersion)                   
                   ->send();

      $this->assertEquals($revisionRead->getStatus(), "200", "Debugging information: ".var_export($revisionRead, TRUE));                                       
      
      utilities\validateParameterApplicationRdfN3($this, $revisionRead);
      
      $this->assertTrue(utilities\compareRdf($revisionRead->getResultset(), file_get_contents($settings->contentDir.'validation/revision_read_initialrevision_recordmode.xml')));

      utilities\deleteRevisionedRecord();

      unset($revisionRead);
      unset($settings);
    }               

  public function testLastestRevision_RecordMode_structJSON() {
      $settings = new Config();  
      
      utilities\deleteRevisionedRecord();
      
      $this->assertTrue(utilities\createRevisionedRecord(), "Can't create unrevisioned records...");

      $revuri = utilities\getLastRevisionUri('http://foo.com/datasets/tests/foo');
      
      $this->assertFalse($revuri === FALSE, "Debugging information: ".var_export($revuri, TRUE));                                       

      $revisionRead = new RevisionReadQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);      
      
      $revisionRead->dataset($settings->testDataset)
                   ->mime('application/json')  
                   ->revisionUri($revuri)
                   ->getRecord()
                   ->sourceInterface($settings->revisionReadInterface)
                   ->sourceInterfaceVersion($settings->revisionReadInterfaceVersion)                   
                   ->send();

      $this->assertEquals($revisionRead->getStatus(), "200", "Debugging information: ".var_export($revisionRead, TRUE));                                       
      
      utilities\validateParameterApplicationJson($this, $revisionRead);
            
      $this->assertTrue(utilities\compareStructJSON($revisionRead->getResultset(), file_get_contents($settings->contentDir.'validation/revision_read_lastestrevision_recordmode.json')));
 
      utilities\deleteRevisionedRecord();

      unset($revisionRead);
      unset($settings);
    }
    
    public function testInitialRevision_RecordMode_structJSON() {
      $settings = new Config();  
      
      utilities\deleteRevisionedRecord();
      
      $this->assertTrue(utilities\createRevisionedRecord(), "Can't create unrevisioned records...");

      $revuri = utilities\getInitialRevisionUri('http://foo.com/datasets/tests/foo');
      
      $this->assertFalse($revuri === FALSE, "Debugging information: ".var_export($revuri, TRUE));                                       

      $revisionRead = new RevisionReadQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);      
      
      $revisionRead->dataset($settings->testDataset)
                   ->mime('application/json')  
                   ->revisionUri($revuri)
                   ->getRecord()
                   ->sourceInterface($settings->revisionReadInterface)
                   ->sourceInterfaceVersion($settings->revisionReadInterfaceVersion)                   
                   ->send();

      $this->assertEquals($revisionRead->getStatus(), "200", "Debugging information: ".var_export($revisionRead, TRUE));                                       
      
      utilities\validateParameterApplicationJson($this, $revisionRead);
            
      $this->assertTrue(utilities\compareStructJSON($revisionRead->getResultset(), file_get_contents($settings->contentDir.'validation/revision_read_initialrevision_recordmode.json')));
 
      utilities\deleteRevisionedRecord();

      unset($revisionRead);
      unset($settings);
    } 
    
    public function testLastestRevision_RecordMode_structXML() {
      $settings = new Config();  
      
      utilities\deleteRevisionedRecord();
      
      $this->assertTrue(utilities\createRevisionedRecord(), "Can't create revisioned records...");

      $revuri = utilities\getLastRevisionUri('http://foo.com/datasets/tests/foo');
      
      $this->assertFalse($revuri === FALSE, "Debugging information: ".var_export($revuri, TRUE));                                       

      $revisionRead = new RevisionReadQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);      
      
      $revisionRead->dataset($settings->testDataset)
                   ->mime('text/xml')  
                   ->revisionUri($revuri)
                   ->getRecord()
                   ->sourceInterface($settings->revisionReadInterface)
                   ->sourceInterfaceVersion($settings->revisionReadInterfaceVersion)                   
                   ->send();

      $this->assertEquals($revisionRead->getStatus(), "200", "Debugging information: ".var_export($revisionRead, TRUE));                                       
      
      utilities\validateParameterTextXml($this, $revisionRead);

      $resultset = new Resultset($settings->endpointUrl);
      
      // To test the structXML serialization, we import it into a Resultset() object and we export as RDF+XML
      // then we compare it to the expected RDF+XML resultset.
      $resultset->importStructXMLResultset($revisionRead->getResultset());
            
      $this->assertTrue(utilities\compareRdf($resultset->getResultsetRDFXML(), file_get_contents($settings->contentDir.'validation/revision_read_lastestrevision_recordmode.xml')));
      
      utilities\deleteRevisionedRecord();

      unset($revisionRead);
      unset($settings);
    }
    
    public function testInitialRevision_RecordMode_structXML() {
      $settings = new Config();  
      
      utilities\deleteRevisionedRecord();
      
      $this->assertTrue(utilities\createRevisionedRecord(), "Can't create unrevisioned records...");

      $revuri = utilities\getInitialRevisionUri('http://foo.com/datasets/tests/foo');
      
      $this->assertFalse($revuri === FALSE, "Debugging information: ".var_export($revuri, TRUE));                                       

      $revisionRead = new RevisionReadQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);      
      
      $revisionRead->dataset($settings->testDataset)
                   ->mime('text/xml')  
                   ->revisionUri($revuri)
                   ->getRecord()
                   ->sourceInterface($settings->revisionReadInterface)
                   ->sourceInterfaceVersion($settings->revisionReadInterfaceVersion)                   
                   ->send();

      $this->assertEquals($revisionRead->getStatus(), "200", "Debugging information: ".var_export($revisionRead, TRUE));                                       
      
      utilities\validateParameterTextXml($this, $revisionRead);

      $resultset = new Resultset($settings->endpointUrl);
      
      // To test the structXML serialization, we import it into a Resultset() object and we export as RDF+XML
      // then we compare it to the expected RDF+XML resultset.
      $resultset->importStructXMLResultset($revisionRead->getResultset());
            
      $this->assertTrue(utilities\compareRdf($resultset->getResultsetRDFXML(), file_get_contents($settings->contentDir.'validation/revision_read_initialrevision_recordmode.xml')));
      
      utilities\deleteRevisionedRecord();

      unset($revisionRead);
      unset($settings);
    }                              

    public function testLastestRevision_RecordMode_Resultset() {
      $settings = new Config();  
      
      utilities\deleteRevisionedRecord();
      
      $this->assertTrue(utilities\createRevisionedRecord(), "Can't create unrevisioned records...");

      $revuri = utilities\getLastRevisionUri('http://foo.com/datasets/tests/foo');
      
      $this->assertFalse($revuri === FALSE, "Debugging information: ".var_export($revuri, TRUE));                                       

      $revisionRead = new RevisionReadQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);      
      
      $revisionRead->dataset($settings->testDataset)
                   ->mime('resultset')  
                   ->revisionUri($revuri)
                   ->getRecord()
                   ->sourceInterface($settings->revisionReadInterface)
                   ->sourceInterfaceVersion($settings->revisionReadInterfaceVersion)                   
                   ->send();

      $this->assertEquals($revisionRead->getStatus(), "200", "Debugging information: ".var_export($revisionRead, TRUE));                                       
      
      $resultset = new Resultset($settings->endpointUrl);
      
      // To test the Resultset we export it as RDF+XML
      // then we compare it to the expected RDF+XML resultset.
      $this->assertTrue(utilities\compareRdf($revisionRead->getResultset()->getResultsetRDFXML(), file_get_contents($settings->contentDir.'validation/revision_read_lastestrevision_recordmode.xml')));

      utilities\deleteRevisionedRecord();

      unset($revisionRead);
      unset($settings);
    }
    
    public function testInitialRevision_RecordMode_Resultset() {
      $settings = new Config();  
      
      utilities\deleteRevisionedRecord();
      
      $this->assertTrue(utilities\createRevisionedRecord(), "Can't create unrevisioned records...");

      $revuri = utilities\getInitialRevisionUri('http://foo.com/datasets/tests/foo');
      
      $this->assertFalse($revuri === FALSE, "Debugging information: ".var_export($revuri, TRUE));                                       

      $revisionRead = new RevisionReadQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);      
      
      $revisionRead->dataset($settings->testDataset)
                   ->mime('resultset')  
                   ->revisionUri($revuri)
                   ->getRecord()
                   ->sourceInterface($settings->revisionReadInterface)
                   ->sourceInterfaceVersion($settings->revisionReadInterfaceVersion)                   
                   ->send();

      $this->assertEquals($revisionRead->getStatus(), "200", "Debugging information: ".var_export($revisionRead, TRUE));                                       
      
      $resultset = new Resultset($settings->endpointUrl);
      
      // To test the Resultset we export it as RDF+XML
      // then we compare it to the expected RDF+XML resultset.
      $this->assertTrue(utilities\compareRdf($revisionRead->getResultset()->getResultsetRDFXML(), file_get_contents($settings->contentDir.'validation/revision_read_initialrevision_recordmode.xml')));

      utilities\deleteRevisionedRecord();

      unset($revisionRead);
      unset($settings);
    }               
                          
    public function testLastestRevision_RevisionMode_Resultset() {
      $settings = new Config();  
      
      utilities\deleteRevisionedRecord();
      
      $this->assertTrue(utilities\createRevisionedRecord(), "Can't create unrevisioned records...");

      $revuri = utilities\getLastRevisionUri('http://foo.com/datasets/tests/foo');
      
      $this->assertFalse($revuri === FALSE, "Debugging information: ".var_export($revuri, TRUE));                                       

      $revisionRead = new RevisionReadQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);      
      
      $revisionRead->dataset($settings->testDataset)
                   ->mime('resultset')  
                   ->revisionUri($revuri)
                   ->getRevision()
                   ->sourceInterface($settings->revisionReadInterface)
                   ->sourceInterfaceVersion($settings->revisionReadInterfaceVersion)                   
                   ->send();

      $this->assertEquals($revisionRead->getStatus(), "200", "Debugging information: ".var_export($revisionRead, TRUE));                                       
      
      $resultset = $revisionRead->getResultset()->getResultset();            
      
      $record = $resultset[$settings->testDataset][$revuri];
      
      $this->assertTrue(isset($record['http://purl.org/ontology/wsf#revisionUri']), "Debugging information: ".var_export($resultset, TRUE));                                       
      $this->assertTrue(isset($record['http://purl.org/ontology/wsf#fromDataset']), "Debugging information: ".var_export($resultset, TRUE));                                       
      $this->assertTrue(isset($record['http://purl.org/ontology/wsf#revisionTime']), "Debugging information: ".var_export($resultset, TRUE));                                       
      $this->assertTrue(isset($record['http://purl.org/ontology/wsf#performer']), "Debugging information: ".var_export($resultset, TRUE));                                       
      $this->assertTrue(isset($record['http://purl.org/ontology/wsf#revisionStatus']), "Debugging information: ".var_export($resultset, TRUE));                                       

      utilities\deleteRevisionedRecord();

      unset($revisionRead);
      unset($settings);
    }
    
    public function testInitialRevision_RevisionMode_Resultset() {
      $settings = new Config();  
      
      utilities\deleteRevisionedRecord();
      
      $this->assertTrue(utilities\createRevisionedRecord(), "Can't create unrevisioned records...");

      $revuri = utilities\getInitialRevisionUri('http://foo.com/datasets/tests/foo');
      
      $this->assertFalse($revuri === FALSE, "Debugging information: ".var_export($revuri, TRUE));                                       

      $revisionRead = new RevisionReadQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);      
      
      $revisionRead->dataset($settings->testDataset)
                   ->mime('resultset')  
                   ->revisionUri($revuri)
                   ->getRevision()
                   ->sourceInterface($settings->revisionReadInterface)
                   ->sourceInterfaceVersion($settings->revisionReadInterfaceVersion)                   
                   ->send();

      $this->assertEquals($revisionRead->getStatus(), "200", "Debugging information: ".var_export($revisionRead, TRUE));                                       
      
      $resultset = $revisionRead->getResultset()->getResultset();            
      
      $record = $resultset[$settings->testDataset][$revuri];
      
      $this->assertTrue(isset($record['http://purl.org/ontology/wsf#revisionUri']), "Debugging information: ".var_export($resultset, TRUE));                                       
      $this->assertTrue(isset($record['http://purl.org/ontology/wsf#fromDataset']), "Debugging information: ".var_export($resultset, TRUE));                                       
      $this->assertTrue(isset($record['http://purl.org/ontology/wsf#revisionTime']), "Debugging information: ".var_export($resultset, TRUE));                                       
      $this->assertTrue(isset($record['http://purl.org/ontology/wsf#performer']), "Debugging information: ".var_export($resultset, TRUE));                                       
      $this->assertTrue(isset($record['http://purl.org/ontology/wsf#revisionStatus']), "Debugging information: ".var_export($resultset, TRUE));                                       

      utilities\deleteRevisionedRecord();

      unset($revisionRead);
      unset($settings);
    } 
  }
  
?>