<?php

  namespace StructuredDynamics\osf\tests\ws\crud\read;
  
  use StructuredDynamics\osf\framework\WebServiceQuerier;
  use StructuredDynamics\osf\php\api\ws\crud\read\CrudReadQuery;
  use StructuredDynamics\osf\tests\Config;
  use StructuredDynamics\osf\tests\content\validation\CrudReadContentValidation;
  use StructuredDynamics\osf\framework\Resultset;
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
  
  class CrudReadTest extends \PHPUnit_Framework_TestCase {
    
    static private $outputs = array();

    public function testWrongEndpointUrl() {
      
      $settings = new Config();          
      
      $wsq = new WebServiceQuerier($settings->endpointUrl . "crud/read/" . "wrong", 
                                   "post", 
                                   "text/xml",
                                   "uri=" . urlencode('http://foo.com/datasets/tests/foo') .
                                   "&dataset=" . urlencode($settings->testDataset) .
                                   "&include_linksback=" . urlencode('False') .
                                   "&include_reification=" . urlencode('False') .
                                   "&include_attributes_list=" . urlencode('') .
                                   "&lang=" . urlencode('en') .
                                   "&interface=". urlencode($settings->crudReadInterface) .
                                   "&version=". urlencode($settings->crudReadInterfaceVersion),
                                   $settings->applicationID,
                                   $settings->apiKey,
                                   $settings->userID);
                         
      $this->assertEquals($wsq->getStatus(), "404", "Debugging information: ".var_export($wsq, TRUE));                                       
      $this->assertEquals($wsq->getStatusMessage(), "Not Found", "Debugging information: ".var_export($wsq, TRUE));
      
      unset($wsq);
      unset($settings);
    }
    
    public function testEndpointMethodGet() {
      
      $settings = new Config();  
      
      utilities\deleteRevisionedRecord();
      
      $this->assertTrue(utilities\createRevisionedRecord(), "Can't create the revision record");
      
      $wsq = new WebServiceQuerier($settings->endpointUrl . "crud/read/", 
                                   "get", 
                                   "text/xml",
                                   "uri=" . urlencode('http://foo.com/datasets/tests/foo') .
                                   "&dataset=" . urlencode($settings->testDataset) .
                                   "&include_linksback=" . urlencode('False') .
                                   "&include_reification=" . urlencode('False') .
                                   "&include_attributes_list=" . urlencode('') .
                                   "&lang=" . urlencode('en') .
                                   "&interface=". urlencode($settings->crudReadInterface) .
                                   "&version=". urlencode($settings->crudReadInterfaceVersion),
                                   $settings->applicationID,
                                   $settings->apiKey,
                                   $settings->userID);
                                   
      $this->assertEquals($wsq->getStatus(), "200", "Debugging information: ".var_export($wsq, TRUE));                                       
      
      utilities\deleteRevisionedRecord();

      unset($wsq);
      unset($settings);
    } 
               
    public function testEndpointMethodPost() {
      
      $settings = new Config();  
      
      utilities\deleteRevisionedRecord();
      
      $this->assertTrue(utilities\createRevisionedRecord(), "Can't create the revision record");      
      
      $wsq = new WebServiceQuerier($settings->endpointUrl . "crud/read/", 
                                   "get", 
                                   "text/xml",
                                   "uri=" . urlencode('http://foo.com/datasets/tests/foo') .
                                   "&dataset=" . urlencode($settings->testDataset) .
                                   "&include_linksback=" . urlencode('False') .
                                   "&include_reification=" . urlencode('False') .
                                   "&include_attributes_list=" . urlencode('') .
                                   "&lang=" . urlencode('en') .
                                   "&interface=". urlencode($settings->crudReadInterface) .
                                   "&version=". urlencode($settings->crudReadInterfaceVersion),
                                   $settings->applicationID,
                                   $settings->apiKey,
                                   $settings->userID);
                                   
      $this->assertEquals($wsq->getStatus(), "200", "Debugging information: ".var_export($wsq, TRUE));                                       
            
      utilities\deleteRevisionedRecord();      
      
      unset($wsq);
      unset($settings);
    }
    
    public function testValidInterfaceVersion() {
      
      $settings = new Config();  
      
      utilities\deleteRevisionedRecord();
      
      $this->assertTrue(utilities\createRevisionedRecord(), "Can't create the revision record");      
                 
      $crudRead = new CrudReadQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
      
      $crudRead->uri('http://foo.com/datasets/tests/foo')               
               ->dataset($settings->testDataset)
               ->excludeLinksback()
               ->excludeReification()
               ->sourceInterface($settings->crudReadInterface)
               ->sourceInterfaceVersion($settings->crudReadInterfaceVersion)
               ->send();
                           
      $this->assertEquals($crudRead->getStatus(), "200", "Debugging information: ".var_export($crudRead, TRUE));                                       

      utilities\deleteRevisionedRecord();

      unset($crudCreate);
      unset($settings);    
    }
    
    public function testInvalidInterfaceVersion() {
      
      $settings = new Config();  
      
      utilities\deleteRevisionedRecord();
      
      $this->assertTrue(utilities\createRevisionedRecord(), "Can't create the revision record");      
                 
      $crudRead = new CrudReadQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
      
      $crudRead->uri('http://foo.com/datasets/tests/foo')               
               ->dataset($settings->testDataset)
               ->excludeLinksback()
               ->excludeReification()
               ->sourceInterface($settings->crudReadInterface)
               ->sourceInterfaceVersion("667.4")
               ->send();
                           
      $this->assertEquals($crudRead->getStatus(), "400", "Debugging information: ".var_export($crudRead, TRUE));                                       
      $this->assertEquals($crudRead->getStatusMessage(), "Bad Request", "Debugging information: ".var_export($crudRead, TRUE));
      $this->assertEquals($crudRead->error->id, "WS-CRUD-READ-306", "Debugging information: ".var_export($crudRead, TRUE));                                       

      utilities\deleteRevisionedRecord();

      unset($crudCreate);
      unset($settings);            
    }    
    
    public function testInterfaceExists() {
      
      $settings = new Config();  
      
      utilities\deleteRevisionedRecord();
      
      $this->assertTrue(utilities\createRevisionedRecord(), "Can't create the revision record");      
                 
      $crudRead = new CrudReadQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
      
      $crudRead->uri('http://foo.com/datasets/tests/foo')               
               ->dataset($settings->testDataset)
               ->excludeLinksback()
               ->excludeReification()
               ->sourceInterface($settings->crudReadInterface)
               ->sourceInterfaceVersion($settings->crudReadInterfaceVersion)
               ->send();
                           
      $this->assertEquals($crudRead->getStatus(), "200", "Debugging information: ".var_export($crudRead, TRUE));                                       

      utilities\deleteRevisionedRecord();

      unset($crudCreate);
      unset($settings);    
    }  
    
    public function testInterfaceNotExisting() {
      
      $settings = new Config();  
      
      utilities\deleteRevisionedRecord();
      
      $this->assertTrue(utilities\createRevisionedRecord(), "Can't create the revision record");      
                 
      $crudRead = new CrudReadQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
      
      $crudRead->uri('http://foo.com/datasets/tests/foo')               
               ->dataset($settings->testDataset)
               ->excludeLinksback()
               ->excludeReification()
               ->sourceInterface('default-not-existing')
               ->sourceInterfaceVersion($settings->crudReadInterfaceVersion)
               ->send();
                           
      $this->assertEquals($crudRead->getStatus(), "400", "Debugging information: ".var_export($crudRead, TRUE));                                       
      $this->assertEquals($crudRead->getStatusMessage(), "Bad Request", "Debugging information: ".var_export($crudRead, TRUE));
      $this->assertEquals($crudRead->error->id, "WS-CRUD-READ-305", "Debugging information: ".var_export($crudRead, TRUE));                                       

      utilities\deleteRevisionedRecord();

      unset($crudCreate);
      unset($settings);       
    }     
    
    public function testLanguageNoLanguageSpecified() {
      
      $settings = new Config();  
      
      utilities\deleteRevisionedRecord();
      
      $this->assertTrue(utilities\createRevisionedRecord(), "Can't create the revision record");      
                 
      $crudRead = new CrudReadQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
      
      $crudRead->uri('http://foo.com/datasets/tests/foo')               
               ->dataset($settings->testDataset)
               ->excludeLinksback()
               ->mime('resultset')
               ->lang('')
               ->excludeReification()
               ->sourceInterface($settings->crudReadInterface)
               ->sourceInterfaceVersion($settings->crudReadInterfaceVersion)
               ->send();

      $this->assertEquals($crudRead->getStatus(), "200", "Debugging information: ".var_export($crudRead, TRUE));                                       

      $resultset = $crudRead->getResultset()->getResultset();                                                                                                                                                                                                   

      $this->assertTrue(isset($resultset[$settings->testDataset]['http://foo.com/datasets/tests/foo']['http://purl.org/ontology/iron#description'][0]['lang']) && $resultset[$settings->testDataset]['http://foo.com/datasets/tests/foo']['http://purl.org/ontology/iron#description'][0]['lang'] == 'en');
      
      utilities\deleteRevisionedRecord();

      unset($crudCreate);
      unset($settings);    
    }      
    
    public function testLanguageEnglishSpecified() {
      
      $settings = new Config();  
      
      utilities\deleteRevisionedRecord();
      
      $this->assertTrue(utilities\createRevisionedRecord(), "Can't create the revision record");      
                 
      $crudRead = new CrudReadQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
      
      $crudRead->uri('http://foo.com/datasets/tests/foo')               
               ->dataset($settings->testDataset)
               ->excludeLinksback()
               ->mime('resultset')
               ->lang('en')
               ->excludeReification()
               ->sourceInterface($settings->crudReadInterface)
               ->sourceInterfaceVersion($settings->crudReadInterfaceVersion)
               ->send();

      $this->assertEquals($crudRead->getStatus(), "200", "Debugging information: ".var_export($crudRead, TRUE));                                       

      $resultset = $crudRead->getResultset()->getResultset();                                                                                                                                                                                                   

      $this->assertTrue(isset($resultset[$settings->testDataset]['http://foo.com/datasets/tests/foo']['http://purl.org/ontology/iron#description'][0]['lang']) && $resultset[$settings->testDataset]['http://foo.com/datasets/tests/foo']['http://purl.org/ontology/iron#description'][0]['lang'] == 'en');
      
      utilities\deleteRevisionedRecord();

      unset($crudCreate);
      unset($settings);    
    }      
           
    public function testLanguageFrenchMissingSpecified() {
      
      $settings = new Config();  
      
      utilities\deleteRevisionedRecord();
      
      $this->assertTrue(utilities\createRevisionedRecord(), "Can't create the revision record");      
                 
      $crudRead = new CrudReadQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
      
      $crudRead->uri('http://foo.com/datasets/tests/foo')               
               ->dataset($settings->testDataset)
               ->excludeLinksback()
               ->mime('resultset')
               ->lang('fr')
               ->excludeReification()
               ->sourceInterface($settings->crudReadInterface)
               ->sourceInterfaceVersion($settings->crudReadInterfaceVersion)
               ->send();

      // Check if the endpoint is configured to have the FR language.
      if($crudRead->isSuccessful())
      {
        $this->assertEquals($crudRead->getStatus(), "200", "Debugging information: ".var_export($crudRead, TRUE));                                       

        $resultset = $crudRead->getResultset()->getResultset();                                                                                                                                                                                                   

        $this->assertFalse(isset($resultset[$settings->testDataset]['http://foo.com/datasets/tests/foo']['http://purl.org/ontology/iron#description'][0]['lang']));
      }
      else
      {
        $this->assertEquals($crudRead->getStatus(), "400", "Debugging information: ".var_export($crudRead, TRUE));                                       
        $this->assertEquals($crudRead->getStatusMessage(), "Bad Request", "Debugging information: ".var_export($crudRead, TRUE));
        $this->assertEquals($crudRead->error->id, "WS-CRUD-READ-308", "Debugging information: ".var_export($crudRead, TRUE));                                       
      }
      
      utilities\deleteRevisionedRecord();

      unset($crudCreate);
      unset($settings);    
    }  
    
    public function testDatasetNotRegisteredInOSF() {
      
      $settings = new Config(); 

      utilities\deleteUnrevisionedRecord();
      
      $this->assertTrue(utilities\createUnrevisionedRecord(), "Can't create the unrevision record");      
                 
      $crudRead = new CrudReadQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
      
      $crudRead->uri('http://foo.com/datasets/tests/foo')               
               ->mime('application/rdf+xml')
               ->dataset($settings->testDataset.'/unregistered')
               ->excludeLinksback()
               ->excludeReification()
               ->sourceInterface($settings->crudReadInterface)
               ->sourceInterfaceVersion($settings->crudReadInterfaceVersion)
               ->send();
      
      $this->assertEquals($crudRead->getStatus(), "403", "Debugging information: ");                                       
      $this->assertEquals($crudRead->getStatusMessage(), "Forbidden", "Debugging information: ");
      $this->assertEquals($crudRead->error->id, "WS-AUTH-VALIDATION-104", "Debugging information: ");
      
      utilities\deleteUnrevisionedRecord();

      unset($crudCreate);
      unset($settings);            
    }  
           
    public function test_GetOneRecord_Unrevisioned_NoLinksback_NoReification_RDFXML() {
      
      $settings = new Config(); 

      utilities\deleteUnrevisionedRecord();
      
      $this->assertTrue(utilities\createUnrevisionedRecord(), "Can't create the unrevision record");      
                 
      $crudRead = new CrudReadQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
      
      $crudRead->uri('http://foo.com/datasets/tests/foo')               
               ->mime('application/rdf+xml')
               ->dataset($settings->testDataset)
               ->excludeLinksback()
               ->excludeReification()
               ->sourceInterface($settings->crudReadInterface)
               ->sourceInterfaceVersion($settings->crudReadInterfaceVersion)
               ->send();
      
      $this->assertEquals($crudRead->getStatus(), "200", "Debugging information: ".var_export($crudRead, TRUE));                                       
      utilities\validateParameterApplicationRdfXml($this, $crudRead);
    
      $this->assertTrue(utilities\compareRdf($crudRead->getResultset(), file_get_contents($settings->contentDir.'validation/crud_read_unrevisioned_onerecord_nolinksback_noreification.xml')));
      
      utilities\deleteUnrevisionedRecord();

      unset($crudCreate);
      unset($settings);         
    }  
    
    public function test_GetOneRecord_Unrevisioned_NoLinksback_NoReification_RDFN3() {
      
      $settings = new Config(); 

      utilities\deleteUnrevisionedRecord();
      
      $this->assertTrue(utilities\createUnrevisionedRecord(), "Can't create the unrevision record");      
                 
      $crudRead = new CrudReadQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
      
      $crudRead->uri('http://foo.com/datasets/tests/foo')               
               ->mime('application/rdf+n3')
               ->dataset($settings->testDataset)
               ->excludeLinksback()
               ->excludeReification()
               ->sourceInterface($settings->crudReadInterface)
               ->sourceInterfaceVersion($settings->crudReadInterfaceVersion)
               ->send();
      
      $this->assertEquals($crudRead->getStatus(), "200", "Debugging information: ".var_export($crudRead, TRUE));                                       
      
      utilities\validateParameterApplicationRdfN3($this, $crudRead);
      
      $this->assertTrue(utilities\compareRdf($crudRead->getResultset(), file_get_contents($settings->contentDir.'validation/crud_read_unrevisioned_onerecord_nolinksback_noreification.xml')));
      
      utilities\deleteUnrevisionedRecord();

      unset($crudCreate);
      unset($settings);         
    } 
    
    public function test_GetOneRecord_Unrevisioned_NoLinksback_NoReification_structJSON() {
      
      $settings = new Config(); 

      utilities\deleteUnrevisionedRecord();
      
      $this->assertTrue(utilities\createUnrevisionedRecord(), "Can't create the unrevision record");      
                 
      $crudRead = new CrudReadQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
      
      $crudRead->uri('http://foo.com/datasets/tests/foo')               
               ->mime('application/json')
               ->dataset($settings->testDataset)
               ->excludeLinksback()
               ->excludeReification()
               ->sourceInterface($settings->crudReadInterface)
               ->sourceInterfaceVersion($settings->crudReadInterfaceVersion)
               ->send();
      
      $this->assertEquals($crudRead->getStatus(), "200", "Debugging information: ".var_export($crudRead, TRUE));                                       
      
      utilities\validateParameterApplicationJson($this, $crudRead);

      $diff = array();      
      
      $this->assertTrue(utilities\compareStructJSON($crudRead->getResultset(), file_get_contents($settings->contentDir.'validation/crud_read_unrevisioned_onerecord_nolinksback_noreification.json'), TRUE, $diff), "Actual:\n\n".$crudRead->getResultset()."\n\nExpected:\n\n".file_get_contents($settings->contentDir.'validation/crud_read_unrevisioned_onerecord_nolinksback_noreification.json')."\n\nDifference:\n\n".var_export($diff, TRUE));
      
      utilities\deleteUnrevisionedRecord();

      unset($crudCreate);
      unset($settings);         
    }    
        
    public function test_GetOneRecord_Unrevisioned_NoLinksback_NoReification_structXML() {
      
      $settings = new Config(); 

      utilities\deleteUnrevisionedRecord();
      
      $this->assertTrue(utilities\createUnrevisionedRecord(), "Can't create the unrevision record");      
                 
      $crudRead = new CrudReadQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
      
      $crudRead->uri('http://foo.com/datasets/tests/foo')               
               ->mime('text/xml')
               ->dataset($settings->testDataset)
               ->excludeLinksback()
               ->excludeReification()
               ->sourceInterface($settings->crudReadInterface)
               ->sourceInterfaceVersion($settings->crudReadInterfaceVersion)
               ->send();
      
      $this->assertEquals($crudRead->getStatus(), "200", "Debugging information: ".var_export($crudRead, TRUE));                                       
      
      utilities\validateParameterTextXml($this, $crudRead);

      $resultset = new Resultset($settings->endpointUrl);
      
      // To test the structXML serialization, we import it into a Resultset() object and we export as RDF+XML
      // then we compare it to the expected RDF+XML resultset.
      $resultset->importStructXMLResultset($crudRead->getResultset());
            
      $this->assertTrue(utilities\compareRdf($resultset->getResultsetRDFXML(), file_get_contents($settings->contentDir.'validation/crud_read_unrevisioned_onerecord_nolinksback_noreification.xml')));
      
      utilities\deleteUnrevisionedRecord();

      unset($crudCreate);
      unset($settings);         
    }     
     
    
    public function test_GetOneRecord_Unrevisioned_NoLinksback_NoReification_Resultset() {
      
      $settings = new Config(); 

      utilities\deleteUnrevisionedRecord();
      
      $this->assertTrue(utilities\createUnrevisionedRecord(), "Can't create the unrevision record");      
                 
      $crudRead = new CrudReadQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
      
      $crudRead->uri('http://foo.com/datasets/tests/foo')               
               ->mime('resultset')
               ->dataset($settings->testDataset)
               ->excludeLinksback()
               ->excludeReification()
               ->sourceInterface($settings->crudReadInterface)
               ->sourceInterfaceVersion($settings->crudReadInterfaceVersion)
               ->send();
      
      $this->assertEquals($crudRead->getStatus(), "200", "Debugging information: ".var_export($crudRead, TRUE));                                       
      
      $resultset = new Resultset($settings->endpointUrl);
      
      // To test the Resultset we export it as RDF+XML
      // then we compare it to the expected RDF+XML resultset.
      $this->assertTrue(utilities\compareRdf($crudRead->getResultset()->getResultsetRDFXML(), file_get_contents($settings->contentDir.'validation/crud_read_unrevisioned_onerecord_nolinksback_noreification.xml')));
      
      utilities\deleteUnrevisionedRecord();

      unset($crudCreate);
      unset($settings);         
    }    

    public function test_GetOneRecord_Revisioned_NoLinksback_NoReification_RDFXML() {
      
      $settings = new Config(); 

      utilities\deleteRevisionedRecord();
      
      $this->assertTrue(utilities\createRevisionedRecord(), "Can't create the revision record");      
                 
      $crudRead = new CrudReadQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
      
      $crudRead->uri('http://foo.com/datasets/tests/foo')               
               ->mime('application/rdf+xml')
               ->dataset($settings->testDataset)
               ->excludeLinksback()
               ->excludeReification()
               ->sourceInterface($settings->crudReadInterface)
               ->sourceInterfaceVersion($settings->crudReadInterfaceVersion)
               ->send();

      $this->assertEquals($crudRead->getStatus(), "200", "Debugging information: ".var_export($crudRead, TRUE));                                       
      utilities\validateParameterApplicationRdfXml($this, $crudRead);
      
      $this->assertTrue(utilities\compareRdf($crudRead->getResultset(), file_get_contents($settings->contentDir.'validation/crud_read_revisioned_onerecord_nolinksback_noreification.xml')));
      
      utilities\deleteRevisionedRecord();

      unset($crudCreate);
      unset($settings);         
    }  
    
    public function test_GetOneRecord_Revisioned_NoLinksback_NoReification_RDFN3() {
      
      $settings = new Config(); 

      utilities\deleteRevisionedRecord();
      
      $this->assertTrue(utilities\createRevisionedRecord(), "Can't create the revision record");      
                 
      $crudRead = new CrudReadQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
      
      $crudRead->uri('http://foo.com/datasets/tests/foo')               
               ->mime('application/rdf+n3')
               ->dataset($settings->testDataset)
               ->excludeLinksback()
               ->excludeReification()
               ->sourceInterface($settings->crudReadInterface)
               ->sourceInterfaceVersion($settings->crudReadInterfaceVersion)
               ->send();
      
      $this->assertEquals($crudRead->getStatus(), "200", "Debugging information: ".var_export($crudRead, TRUE));                                       
      
      utilities\validateParameterApplicationRdfN3($this, $crudRead);
      
      $this->assertTrue(utilities\compareRdf($crudRead->getResultset(), file_get_contents($settings->contentDir.'validation/crud_read_revisioned_onerecord_nolinksback_noreification.xml')));
      
      utilities\deleteRevisionedRecord();

      unset($crudCreate);
      unset($settings);         
    } 
    
    public function test_GetOneRecord_Revisioned_NoLinksback_NoReification_structJSON() {
      
      $settings = new Config(); 

      utilities\deleteRevisionedRecord();
      
      $this->assertTrue(utilities\createRevisionedRecord(), "Can't create the revision record");      
                 
      $crudRead = new CrudReadQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
      
      $crudRead->uri('http://foo.com/datasets/tests/foo')               
               ->mime('application/json')
               ->dataset($settings->testDataset)
               ->excludeLinksback()
               ->excludeReification()
               ->sourceInterface($settings->crudReadInterface)
               ->sourceInterfaceVersion($settings->crudReadInterfaceVersion)
               ->send();
      
      $this->assertEquals($crudRead->getStatus(), "200", "Debugging information: ".var_export($crudRead, TRUE));                                       
      
      utilities\validateParameterApplicationJson($this, $crudRead);

      $this->assertTrue(utilities\compareStructJSON($crudRead->getResultset(), file_get_contents($settings->contentDir.'validation/crud_read_revisioned_onerecord_nolinksback_noreification.json')));
      
      utilities\deleteRevisionedRecord();

      unset($crudCreate);
      unset($settings);         
    }    
        
    public function test_GetOneRecord_Revisioned_NoLinksback_NoReification_structXML() {
      
      $settings = new Config(); 

      utilities\deleteRevisionedRecord();
      
      $this->assertTrue(utilities\createRevisionedRecord(), "Can't create the revision record");      
                 
      $crudRead = new CrudReadQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
      
      $crudRead->uri('http://foo.com/datasets/tests/foo')               
               ->mime('text/xml')
               ->dataset($settings->testDataset)
               ->excludeLinksback()
               ->excludeReification()
               ->sourceInterface($settings->crudReadInterface)
               ->sourceInterfaceVersion($settings->crudReadInterfaceVersion)
               ->send();
      
      $this->assertEquals($crudRead->getStatus(), "200", "Debugging information: ".var_export($crudRead, TRUE));                                       
      
      utilities\validateParameterTextXml($this, $crudRead);

      $resultset = new Resultset($settings->endpointUrl);
      
      // To test the structXML serialization, we import it into a Resultset() object and we export as RDF+XML
      // then we compare it to the expected RDF+XML resultset.
      $resultset->importStructXMLResultset($crudRead->getResultset());
            
      $this->assertTrue(utilities\compareRdf($resultset->getResultsetRDFXML(), file_get_contents($settings->contentDir.'validation/crud_read_revisioned_onerecord_nolinksback_noreification.xml')));
      
      utilities\deleteRevisionedRecord();

      unset($crudCreate);
      unset($settings);         
    }     
     
    
    public function test_GetOneRecord_Revisioned_NoLinksback_NoReification_Resultset() {
      
      $settings = new Config(); 

      utilities\deleteRevisionedRecord();
      
      $this->assertTrue(utilities\createRevisionedRecord(), "Can't create the revision record");      
                 
      $crudRead = new CrudReadQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
      
      $crudRead->uri('http://foo.com/datasets/tests/foo')               
               ->mime('resultset')
               ->dataset($settings->testDataset)
               ->excludeLinksback()
               ->excludeReification()
               ->sourceInterface($settings->crudReadInterface)
               ->sourceInterfaceVersion($settings->crudReadInterfaceVersion)
               ->send();
      
      $this->assertEquals($crudRead->getStatus(), "200", "Debugging information: ".var_export($crudRead, TRUE));                                       
      
      $resultset = new Resultset($settings->endpointUrl);
      
      // To test the Resultset we export it as RDF+XML
      // then we compare it to the expected RDF+XML resultset.
      $this->assertTrue(utilities\compareRdf($crudRead->getResultset()->getResultsetRDFXML(), file_get_contents($settings->contentDir.'validation/crud_read_revisioned_onerecord_nolinksback_noreification.xml')));
      
      utilities\deleteRevisionedRecord();

      unset($crudCreate);
      unset($settings);         
    }
    
    public function test_GetOneRecord_Revisioned_Linksback_NoReification_RDFXML() {
      
      $settings = new Config(); 

      utilities\deleteRevisionedRecord();
      
      $this->assertTrue(utilities\createRevisionedRecord(), "Can't create the revision record");      
                 
      $crudRead = new CrudReadQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
      
      $crudRead->uri('http://foo.com/datasets/tests/foo')               
               ->mime('application/rdf+xml')
               ->dataset($settings->testDataset)
               ->includeLinksback()
               ->excludeReification()
               ->sourceInterface($settings->crudReadInterface)
               ->sourceInterfaceVersion($settings->crudReadInterfaceVersion)
               ->send();

      $this->assertEquals($crudRead->getStatus(), "200", "Debugging information: ".var_export($crudRead, TRUE));                                       
      utilities\validateParameterApplicationRdfXml($this, $crudRead);

      $this->assertTrue(utilities\compareRdf($crudRead->getResultset(), file_get_contents($settings->contentDir.'validation/crud_read_revisioned_onerecord_linksback_noreification.xml')));
      
      utilities\deleteRevisionedRecord();

      unset($crudCreate);
      unset($settings);         
    }  

    public function test_GetOneRecord_Revisioned_Linksback_NoReification_RDFN3() {
      
      $settings = new Config(); 

      utilities\deleteRevisionedRecord();
      
      $this->assertTrue(utilities\createRevisionedRecord(), "Can't create the revision record");      
                 
      $crudRead = new CrudReadQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
      
      $crudRead->uri('http://foo.com/datasets/tests/foo')               
               ->mime('application/rdf+n3')
               ->dataset($settings->testDataset)
               ->includeLinksback()
               ->excludeReification()
               ->sourceInterface($settings->crudReadInterface)
               ->sourceInterfaceVersion($settings->crudReadInterfaceVersion)
               ->send();
      
      $this->assertEquals($crudRead->getStatus(), "200", "Debugging information: ".var_export($crudRead, TRUE));                                       
      
      utilities\validateParameterApplicationRdfN3($this, $crudRead);
      
      $this->assertTrue(utilities\compareRdf($crudRead->getResultset(), file_get_contents($settings->contentDir.'validation/crud_read_revisioned_onerecord_linksback_noreification.xml')));
      
      utilities\deleteRevisionedRecord();

      unset($crudCreate);
      unset($settings);         
    } 
    
    public function test_GetOneRecord_Revisioned_Linksback_NoReification_structJSON() {
      
      $settings = new Config(); 

      utilities\deleteRevisionedRecord();
      
      $this->assertTrue(utilities\createRevisionedRecord(), "Can't create the revision record");      
                 
      $crudRead = new CrudReadQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
      
      $crudRead->uri('http://foo.com/datasets/tests/foo')               
               ->mime('application/json')
               ->dataset($settings->testDataset)
               ->includeLinksback()
               ->excludeReification()
               ->sourceInterface($settings->crudReadInterface)
               ->sourceInterfaceVersion($settings->crudReadInterfaceVersion)
               ->send();
      
      $this->assertEquals($crudRead->getStatus(), "200", "Debugging information: ".var_export($crudRead, TRUE));                                       

      utilities\validateParameterApplicationJson($this, $crudRead);

      $this->assertTrue(utilities\compareStructJSON($crudRead->getResultset(), file_get_contents($settings->contentDir.'validation/crud_read_revisioned_onerecord_linksback_noreification.json')));
      
      utilities\deleteRevisionedRecord();

      unset($crudCreate);
      unset($settings);         
    }    
        
    public function test_GetOneRecord_Revisioned_Linksback_NoReification_structXML() {
      
      $settings = new Config(); 

      utilities\deleteRevisionedRecord();
      
      $this->assertTrue(utilities\createRevisionedRecord(), "Can't create the revision record");      
                 
      $crudRead = new CrudReadQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
      
      $crudRead->uri('http://foo.com/datasets/tests/foo')               
               ->mime('text/xml')
               ->dataset($settings->testDataset)
               ->includeLinksback()
               ->excludeReification()
               ->sourceInterface($settings->crudReadInterface)
               ->sourceInterfaceVersion($settings->crudReadInterfaceVersion)
               ->send();
      
      $this->assertEquals($crudRead->getStatus(), "200", "Debugging information: ".var_export($crudRead, TRUE));                                       
      
      utilities\validateParameterTextXml($this, $crudRead);

      $resultset = new Resultset($settings->endpointUrl);
      
      // To test the structXML serialization, we import it into a Resultset() object and we export as RDF+XML
      // then we compare it to the expected RDF+XML resultset.
      $resultset->importStructXMLResultset($crudRead->getResultset());
            
      $this->assertTrue(utilities\compareRdf($resultset->getResultsetRDFXML(), file_get_contents($settings->contentDir.'validation/crud_read_revisioned_onerecord_linksback_noreification.xml')));
      
      utilities\deleteRevisionedRecord();

      unset($crudCreate);
      unset($settings);         
    }     
     
    
    public function test_GetOneRecord_Revisioned_Linksback_NoReification_Resultset() {
      
      $settings = new Config(); 

      utilities\deleteRevisionedRecord();
      
      $this->assertTrue(utilities\createRevisionedRecord(), "Can't create the revision record");      
                 
      $crudRead = new CrudReadQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
      
      $crudRead->uri('http://foo.com/datasets/tests/foo')               
               ->mime('resultset')
               ->dataset($settings->testDataset)
               ->includeLinksback()
               ->excludeReification()
               ->sourceInterface($settings->crudReadInterface)
               ->sourceInterfaceVersion($settings->crudReadInterfaceVersion)
               ->send();
      
      $this->assertEquals($crudRead->getStatus(), "200", "Debugging information: ".var_export($crudRead, TRUE));                                       
      
      $resultset = new Resultset($settings->endpointUrl);
      
      // To test the Resultset we export it as RDF+XML
      // then we compare it to the expected RDF+XML resultset.
      $this->assertTrue(utilities\compareRdf($crudRead->getResultset()->getResultsetRDFXML(), file_get_contents($settings->contentDir.'validation/crud_read_revisioned_onerecord_linksback_noreification.xml')));
      
      utilities\deleteRevisionedRecord();

      unset($crudCreate);
      unset($settings);         
    }          
       
    public function test_GetOneRecord_Revisioned_Linksback_Reification_RDFXML() {
      
      $settings = new Config(); 

      utilities\deleteRevisionedRecord();
      
      $this->assertTrue(utilities\createRevisionedRecord(), "Can't create the revision record");      
                 
      $crudRead = new CrudReadQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
      
      $crudRead->uri('http://foo.com/datasets/tests/foo')               
               ->mime('application/rdf+xml')
               ->dataset($settings->testDataset)
               ->includeLinksback()
               ->includeReification()
               ->sourceInterface($settings->crudReadInterface)
               ->sourceInterfaceVersion($settings->crudReadInterfaceVersion)
               ->send();
                              
      $this->assertEquals($crudRead->getStatus(), "200", "Debugging information: ".var_export($crudRead, TRUE));                                       
      utilities\validateParameterApplicationRdfXml($this, $crudRead);

      $this->assertTrue(utilities\compareRdf($crudRead->getResultset(), file_get_contents($settings->contentDir.'validation/crud_read_revisioned_onerecord_linksback_reification.xml')));
      
      utilities\deleteRevisionedRecord();

      unset($crudCreate);
      unset($settings);         
    }  

    public function test_GetOneRecord_Revisioned_Linksback_Reification_RDFN3() {
      
      $settings = new Config(); 

      utilities\deleteRevisionedRecord();
      
      $this->assertTrue(utilities\createRevisionedRecord(), "Can't create the revision record");      
                 
      $crudRead = new CrudReadQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
      
      $crudRead->uri('http://foo.com/datasets/tests/foo')               
               ->mime('application/rdf+n3')
               ->dataset($settings->testDataset)
               ->includeLinksback()
               ->includeReification()
               ->sourceInterface($settings->crudReadInterface)
               ->sourceInterfaceVersion($settings->crudReadInterfaceVersion)
               ->send();
      
      $this->assertEquals($crudRead->getStatus(), "200", "Debugging information: ".var_export($crudRead, TRUE));                                       
      
      utilities\validateParameterApplicationRdfN3($this, $crudRead);
      
      $this->assertTrue(utilities\compareRdf($crudRead->getResultset(), file_get_contents($settings->contentDir.'validation/crud_read_revisioned_onerecord_linksback_reification.xml')));
      
      utilities\deleteRevisionedRecord();

      unset($crudCreate);
      unset($settings);         
    } 
    
    public function test_GetOneRecord_Revisioned_Linksback_Reification_structJSON() {
      
      $settings = new Config(); 

      utilities\deleteRevisionedRecord();
      
      $this->assertTrue(utilities\createRevisionedRecord(), "Can't create the revision record");      
                 
      $crudRead = new CrudReadQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
      
      $crudRead->uri('http://foo.com/datasets/tests/foo')               
               ->mime('application/json')
               ->dataset($settings->testDataset)
               ->includeLinksback()
               ->includeReification()
               ->sourceInterface($settings->crudReadInterface)
               ->sourceInterfaceVersion($settings->crudReadInterfaceVersion)
               ->send();
      
      $this->assertEquals($crudRead->getStatus(), "200", "Debugging information: ".var_export($crudRead, TRUE));                                       

      utilities\validateParameterApplicationJson($this, $crudRead);
            
      $this->assertTrue(utilities\compareStructJSON($crudRead->getResultset(), file_get_contents($settings->contentDir.'validation/crud_read_revisioned_onerecord_linksback_reification.json')));
      
      utilities\deleteRevisionedRecord();

      unset($crudCreate);
      unset($settings);         
    }    
        
    public function test_GetOneRecord_Revisioned_Linksback_Reification_structXML() {
      
      $settings = new Config(); 

      utilities\deleteRevisionedRecord();
      
      $this->assertTrue(utilities\createRevisionedRecord(), "Can't create the revision record");      
                 
      $crudRead = new CrudReadQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
      
      $crudRead->uri('http://foo.com/datasets/tests/foo')               
               ->mime('text/xml')
               ->dataset($settings->testDataset)
               ->includeLinksback()
               ->includeReification()
               ->sourceInterface($settings->crudReadInterface)
               ->sourceInterfaceVersion($settings->crudReadInterfaceVersion)
               ->send();
      
      $this->assertEquals($crudRead->getStatus(), "200", "Debugging information: ".var_export($crudRead, TRUE));                                       
      
      utilities\validateParameterTextXml($this, $crudRead);

      $resultset = new Resultset($settings->endpointUrl);
      
      // To test the structXML serialization, we import it into a Resultset() object and we export as RDF+XML
      // then we compare it to the expected RDF+XML resultset.
      $resultset->importStructXMLResultset($crudRead->getResultset());
            
      $this->assertTrue(utilities\compareRdf($resultset->getResultsetRDFXML(), file_get_contents($settings->contentDir.'validation/crud_read_revisioned_onerecord_linksback_reification.xml')));
      
      utilities\deleteRevisionedRecord();

      unset($crudCreate);
      unset($settings);         
    }     
     
    
    public function test_GetOneRecord_Revisioned_Linksback_Reification_Resultset() {
      
      $settings = new Config(); 

      utilities\deleteRevisionedRecord();
      
      $this->assertTrue(utilities\createRevisionedRecord(), "Can't create the revision record");      
                 
      $crudRead = new CrudReadQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
      
      $crudRead->uri('http://foo.com/datasets/tests/foo')               
               ->mime('resultset')
               ->dataset($settings->testDataset)
               ->includeLinksback()
               ->includeReification()
               ->sourceInterface($settings->crudReadInterface)
               ->sourceInterfaceVersion($settings->crudReadInterfaceVersion)
               ->send();
      
      $this->assertEquals($crudRead->getStatus(), "200", "Debugging information: ".var_export($crudRead, TRUE));                                       
      
      $resultset = new Resultset($settings->endpointUrl);
      
      // To test the Resultset we export it as RDF+XML
      // then we compare it to the expected RDF+XML resultset.
      $this->assertTrue(utilities\compareRdf($crudRead->getResultset()->getResultsetRDFXML(), file_get_contents($settings->contentDir.'validation/crud_read_revisioned_onerecord_linksback_reification.xml')));
      
      utilities\deleteRevisionedRecord();

      unset($crudCreate);
      unset($settings);         
    }          

    public function test_GetOneRecord_Revisioned_Linksback_Reification_OneAttribute_RDFXML() {
      
      $settings = new Config(); 

      utilities\deleteRevisionedRecord();
      
      $this->assertTrue(utilities\createRevisionedRecord(), "Can't create the revision record");      
                 
      $crudRead = new CrudReadQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
      
      $crudRead->uri('http://foo.com/datasets/tests/foo')               
               ->includeAttributes(array('http://purl.org/ontology/wsf#related_product'))
               ->mime('application/rdf+xml')
               ->dataset($settings->testDataset)      
               ->includeLinksback()
               ->includeReification()
               ->sourceInterface($settings->crudReadInterface)
               ->sourceInterfaceVersion($settings->crudReadInterfaceVersion)
               ->send();

      $this->assertEquals($crudRead->getStatus(), "200", "Debugging information: ".var_export($crudRead, TRUE));                                       
      utilities\validateParameterApplicationRdfXml($this, $crudRead);

      $this->assertTrue(utilities\compareRdf($crudRead->getResultset(), file_get_contents($settings->contentDir.'validation/crud_read_revisioned_onerecord_linksback_reification_oneattribute.xml')));
      
      utilities\deleteRevisionedRecord();

      unset($crudCreate);
      unset($settings);         
    }  

    public function test_GetOneRecord_Revisioned_Linksback_Reification_OneAttribute_RDFN3() {
      
      $settings = new Config(); 

      utilities\deleteRevisionedRecord();
      
      $this->assertTrue(utilities\createRevisionedRecord(), "Can't create the revision record");      
                 
      $crudRead = new CrudReadQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
      
      $crudRead->uri('http://foo.com/datasets/tests/foo')               
               ->includeAttributes(array('http://purl.org/ontology/wsf#related_product'))
               ->mime('application/rdf+n3')
               ->dataset($settings->testDataset)
               ->includeLinksback()
               ->includeReification()
               ->sourceInterface($settings->crudReadInterface)
               ->sourceInterfaceVersion($settings->crudReadInterfaceVersion)
               ->send();
      
      $this->assertEquals($crudRead->getStatus(), "200", "Debugging information: ".var_export($crudRead, TRUE));                                       
      
      utilities\validateParameterApplicationRdfN3($this, $crudRead);
      
      $this->assertTrue(utilities\compareRdf($crudRead->getResultset(), file_get_contents($settings->contentDir.'validation/crud_read_revisioned_onerecord_linksback_reification_oneattribute.xml')));
      
      utilities\deleteRevisionedRecord();

      unset($crudCreate);
      unset($settings);         
    } 
    
    public function test_GetOneRecord_Revisioned_Linksback_Reification_OneAttribute_structJSON() {
      
      $settings = new Config(); 

      utilities\deleteRevisionedRecord();
      
      $this->assertTrue(utilities\createRevisionedRecord(), "Can't create the revision record");      
                 
      $crudRead = new CrudReadQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
      
      $crudRead->uri('http://foo.com/datasets/tests/foo')               
               ->includeAttributes(array('http://purl.org/ontology/wsf#related_product'))
               ->mime('application/json')
               ->dataset($settings->testDataset)
               ->includeLinksback()
               ->includeReification()
               ->sourceInterface($settings->crudReadInterface)
               ->sourceInterfaceVersion($settings->crudReadInterfaceVersion)
               ->send();
      
      $this->assertEquals($crudRead->getStatus(), "200", "Debugging information: ".var_export($crudRead, TRUE));                                       

      utilities\validateParameterApplicationJson($this, $crudRead);

      $this->assertTrue(utilities\compareStructJSON($crudRead->getResultset(), file_get_contents($settings->contentDir.'validation/crud_read_revisioned_onerecord_linksback_reification_oneattribute.json')));
      
      utilities\deleteRevisionedRecord();

      unset($crudCreate);
      unset($settings);         
    }    
        
    public function test_GetOneRecord_Revisioned_Linksback_Reification_OneAttribute_structXML() {
      
      $settings = new Config(); 

      utilities\deleteRevisionedRecord();
      
      $this->assertTrue(utilities\createRevisionedRecord(), "Can't create the revision record");      
                 
      $crudRead = new CrudReadQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
      
      $crudRead->uri('http://foo.com/datasets/tests/foo')               
               ->includeAttributes(array('http://purl.org/ontology/wsf#related_product'))
               ->mime('text/xml')
               ->dataset($settings->testDataset)
               ->includeLinksback()
               ->includeReification()
               ->sourceInterface($settings->crudReadInterface)
               ->sourceInterfaceVersion($settings->crudReadInterfaceVersion)
               ->send();
      
      $this->assertEquals($crudRead->getStatus(), "200", "Debugging information: ".var_export($crudRead, TRUE));                                       
      
      utilities\validateParameterTextXml($this, $crudRead);

      $resultset = new Resultset($settings->endpointUrl);
      
      // To test the structXML serialization, we import it into a Resultset() object and we export as RDF+XML
      // then we compare it to the expected RDF+XML resultset.
      $resultset->importStructXMLResultset($crudRead->getResultset());
            
      $this->assertTrue(utilities\compareRdf($resultset->getResultsetRDFXML(), file_get_contents($settings->contentDir.'validation/crud_read_revisioned_onerecord_linksback_reification_oneattribute.xml')));
      
      utilities\deleteRevisionedRecord();

      unset($crudCreate);
      unset($settings);         
    }     
     
    
    public function test_GetOneRecord_Revisioned_Linksback_Reification_OneAttribute_Resultset() {
      
      $settings = new Config(); 

      utilities\deleteRevisionedRecord();
      
      $this->assertTrue(utilities\createRevisionedRecord(), "Can't create the revision record");      
                 
      $crudRead = new CrudReadQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
      
      $crudRead->uri('http://foo.com/datasets/tests/foo')               
               ->includeAttributes(array('http://purl.org/ontology/wsf#related_product'))
               ->mime('resultset')
               ->dataset($settings->testDataset)
               ->includeLinksback()
               ->includeReification()
               ->sourceInterface($settings->crudReadInterface)
               ->sourceInterfaceVersion($settings->crudReadInterfaceVersion)
               ->send();
      
      $this->assertEquals($crudRead->getStatus(), "200", "Debugging information: ".var_export($crudRead, TRUE));                                       
      
      $resultset = new Resultset($settings->endpointUrl);
      
      // To test the Resultset we export it as RDF+XML
      // then we compare it to the expected RDF+XML resultset.
      $this->assertTrue(utilities\compareRdf($crudRead->getResultset()->getResultsetRDFXML(), file_get_contents($settings->contentDir.'validation/crud_read_revisioned_onerecord_linksback_reification_oneattribute.xml')));
      
      utilities\deleteRevisionedRecord();

      unset($crudCreate);
      unset($settings);         
    }    
    
    public function test_GetTwoRecords_Unrevisioned_NoLinksback_NoReification_RDFXML() {
      
      $settings = new Config(); 

      utilities\deleteUnrevisionedRecord();
      
      $this->assertTrue(utilities\createUnrevisionedRecord(), "Can't create the unrevision record");      
                 
      $crudRead = new CrudReadQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
      
      $crudRead->uri(array('http://foo.com/datasets/tests/foo', 'http://foo.com/datasets/tests/bar'))               
               ->mime('application/rdf+xml')
               ->dataset(array($settings->testDataset, $settings->testDataset))
               ->excludeLinksback()
               ->excludeReification()
               ->sourceInterface($settings->crudReadInterface)
               ->sourceInterfaceVersion($settings->crudReadInterfaceVersion)
               ->send();
                   
      $this->assertEquals($crudRead->getStatus(), "200", "Debugging information: ".var_export($crudRead, TRUE));                                       
      utilities\validateParameterApplicationRdfXml($this, $crudRead);
      
      $this->assertTrue(utilities\compareRdf($crudRead->getResultset(), file_get_contents($settings->contentDir.'validation/crud_read_unrevisioned_tworecords_nolinksback_noreification.xml')));
      
      utilities\deleteUnrevisionedRecord();

      unset($crudCreate);
      unset($settings);         
    }     
        
    public function test_GetTwoRecords_Unrevisioned_NoLinksback_NoReification_RDFN3() {
      
      $settings = new Config(); 

      utilities\deleteUnrevisionedRecord();
      
      $this->assertTrue(utilities\createUnrevisionedRecord(), "Can't create the unrevision record");      
                 
      $crudRead = new CrudReadQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
      
      $crudRead->uri(array('http://foo.com/datasets/tests/foo', 'http://foo.com/datasets/tests/bar'))               
               ->mime('application/rdf+n3')
               ->dataset(array($settings->testDataset, $settings->testDataset))
               ->excludeLinksback()
               ->excludeReification()
               ->sourceInterface($settings->crudReadInterface)
               ->sourceInterfaceVersion($settings->crudReadInterfaceVersion)
               ->send();
      
      $this->assertEquals($crudRead->getStatus(), "200", "Debugging information: ".var_export($crudRead, TRUE));                                       
      
      utilities\validateParameterApplicationRdfN3($this, $crudRead);
      
      $this->assertTrue(utilities\compareRdf($crudRead->getResultset(), file_get_contents($settings->contentDir.'validation/crud_read_unrevisioned_tworecords_nolinksback_noreification.xml')));
      
      utilities\deleteUnrevisionedRecord();

      unset($crudCreate);
      unset($settings);         
    } 
    
    public function test_GetTwoRecords_Unrevisioned_NoLinksback_NoReification_structJSON() {
      
      $settings = new Config(); 

      utilities\deleteUnrevisionedRecord();
      
      $this->assertTrue(utilities\createUnrevisionedRecord(), "Can't create the unrevision record");      
                 
      $crudRead = new CrudReadQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
      
      $crudRead->uri(array('http://foo.com/datasets/tests/foo', 'http://foo.com/datasets/tests/bar'))               
               ->mime('application/json')
               ->dataset(array($settings->testDataset, $settings->testDataset))
               ->excludeLinksback()
               ->excludeReification()
               ->sourceInterface($settings->crudReadInterface)
               ->sourceInterfaceVersion($settings->crudReadInterfaceVersion)
               ->send();
      
      $this->assertEquals($crudRead->getStatus(), "200", "Debugging information: ".var_export($crudRead, TRUE));                                       
      
      utilities\validateParameterApplicationJson($this, $crudRead);

      $this->assertTrue(utilities\compareStructJSON($crudRead->getResultset(), file_get_contents($settings->contentDir.'validation/crud_read_unrevisioned_tworecords_nolinksback_noreification.json')));
      
      utilities\deleteUnrevisionedRecord();

      unset($crudCreate);
      unset($settings);         
    }    
        
    public function test_GetTwoRecords_Unrevisioned_NoLinksback_NoReification_structXML() {
      
      $settings = new Config(); 

      utilities\deleteUnrevisionedRecord();
      
      $this->assertTrue(utilities\createUnrevisionedRecord(), "Can't create the unrevision record");      
                 
      $crudRead = new CrudReadQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
      
      $crudRead->uri(array('http://foo.com/datasets/tests/foo', 'http://foo.com/datasets/tests/bar'))               
               ->mime('text/xml')
               ->dataset(array($settings->testDataset, $settings->testDataset))
               ->excludeLinksback()
               ->excludeReification()
               ->sourceInterface($settings->crudReadInterface)
               ->sourceInterfaceVersion($settings->crudReadInterfaceVersion)
               ->send();
      
      $this->assertEquals($crudRead->getStatus(), "200", "Debugging information: ".var_export($crudRead, TRUE));                                       
      
      utilities\validateParameterTextXml($this, $crudRead);

      $resultset = new Resultset($settings->endpointUrl);
      
      // To test the structXML serialization, we import it into a Resultset() object and we export as RDF+XML
      // then we compare it to the expected RDF+XML resultset.
      $resultset->importStructXMLResultset($crudRead->getResultset());
            
      $this->assertTrue(utilities\compareRdf($resultset->getResultsetRDFXML(), file_get_contents($settings->contentDir.'validation/crud_read_unrevisioned_tworecords_nolinksback_noreification.xml')));
      
      utilities\deleteUnrevisionedRecord();

      unset($crudCreate);
      unset($settings);         
    }     
     
    
    public function test_GetTwoRecords_Unrevisioned_NoLinksback_NoReification_Resultset() {
      
      $settings = new Config(); 

      utilities\deleteUnrevisionedRecord();
      
      $this->assertTrue(utilities\createUnrevisionedRecord(), "Can't create the unrevision record");      
                 
      $crudRead = new CrudReadQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
      
      $crudRead->uri(array('http://foo.com/datasets/tests/foo', 'http://foo.com/datasets/tests/bar'))               
               ->mime('resultset')
               ->dataset(array($settings->testDataset, $settings->testDataset))
               ->excludeLinksback()
               ->excludeReification()
               ->sourceInterface($settings->crudReadInterface)
               ->sourceInterfaceVersion($settings->crudReadInterfaceVersion)
               ->send();
      
      $this->assertEquals($crudRead->getStatus(), "200", "Debugging information: ".var_export($crudRead, TRUE));                                       
      
      $resultset = new Resultset($settings->endpointUrl);
      
      // To test the Resultset we export it as RDF+XML
      // then we compare it to the expected RDF+XML resultset.
      $this->assertTrue(utilities\compareRdf($crudRead->getResultset()->getResultsetRDFXML(), file_get_contents($settings->contentDir.'validation/crud_read_unrevisioned_tworecords_nolinksback_noreification.xml')));
      
      utilities\deleteUnrevisionedRecord();

      unset($crudCreate);
      unset($settings);         
    }     

    public function test_GetTwoRecords_Revisioned_NoLinksback_NoReification_RDFXML() {
      
      $settings = new Config(); 

      utilities\deleteRevisionedRecord();
      
      $this->assertTrue(utilities\createRevisionedRecord(), "Can't create the revision record");      
                 
      $crudRead = new CrudReadQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
      
      $crudRead->uri(array('http://foo.com/datasets/tests/foo', 'http://foo.com/datasets/tests/bar'))               
               ->mime('application/rdf+xml')
               ->dataset(array($settings->testDataset, $settings->testDataset))
               ->excludeLinksback()
               ->excludeReification()
               ->sourceInterface($settings->crudReadInterface)
               ->sourceInterfaceVersion($settings->crudReadInterfaceVersion)
               ->send();

      $this->assertEquals($crudRead->getStatus(), "200", "Debugging information: ".var_export($crudRead, TRUE));                                       
      utilities\validateParameterApplicationRdfXml($this, $crudRead);
      
      $this->assertTrue(utilities\compareRdf($crudRead->getResultset(), file_get_contents($settings->contentDir.'validation/crud_read_revisioned_tworecords_nolinksback_noreification.xml')));
      
      utilities\deleteRevisionedRecord();

      unset($crudCreate);
      unset($settings);         
    }     
   
    public function test_GetTwoRecords_Revisioned_NoLinksback_NoReification_RDFN3() {
      
      $settings = new Config(); 

      utilities\deleteRevisionedRecord();
      
      $this->assertTrue(utilities\createRevisionedRecord(), "Can't create the revision record");      
                 
      $crudRead = new CrudReadQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
      
      $crudRead->uri(array('http://foo.com/datasets/tests/foo', 'http://foo.com/datasets/tests/bar'))               
               ->mime('application/rdf+n3')
               ->dataset(array($settings->testDataset, $settings->testDataset))
               ->excludeLinksback()
               ->excludeReification()
               ->sourceInterface($settings->crudReadInterface)
               ->sourceInterfaceVersion($settings->crudReadInterfaceVersion)
               ->send();
      
      $this->assertEquals($crudRead->getStatus(), "200", "Debugging information: ".var_export($crudRead, TRUE));                                       
      
      utilities\validateParameterApplicationRdfN3($this, $crudRead);
      
      $this->assertTrue(utilities\compareRdf($crudRead->getResultset(), file_get_contents($settings->contentDir.'validation/crud_read_revisioned_tworecords_nolinksback_noreification.xml')));
      
      utilities\deleteRevisionedRecord();

      unset($crudCreate);
      unset($settings);         
    } 
    
    public function test_GetTwoRecords_Revisioned_NoLinksback_NoReification_structJSON() {
      
      $settings = new Config(); 

      utilities\deleteRevisionedRecord();
      
      $this->assertTrue(utilities\createRevisionedRecord(), "Can't create the revision record");      
                 
      $crudRead = new CrudReadQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
      
      $crudRead->uri(array('http://foo.com/datasets/tests/foo', 'http://foo.com/datasets/tests/bar'))               
               ->mime('application/json')
               ->dataset(array($settings->testDataset, $settings->testDataset))
               ->excludeLinksback()
               ->excludeReification()
               ->sourceInterface($settings->crudReadInterface)
               ->sourceInterfaceVersion($settings->crudReadInterfaceVersion)
               ->send();
   
      $this->assertEquals($crudRead->getStatus(), "200", "Debugging information: ".var_export($crudRead, TRUE));                                       
      
      utilities\validateParameterApplicationJson($this, $crudRead);

      $this->assertTrue(utilities\compareStructJSON($crudRead->getResultset(), file_get_contents($settings->contentDir.'validation/crud_read_revisioned_tworecords_nolinksback_noreification.json')));
      
      utilities\deleteRevisionedRecord();

      unset($crudCreate);
      unset($settings);         
    }    
        
    public function test_GetTwoRecords_Revisioned_NoLinksback_NoReification_structXML() {
      
      $settings = new Config(); 

      utilities\deleteRevisionedRecord();
      
      $this->assertTrue(utilities\createRevisionedRecord(), "Can't create the revision record");      
                 
      $crudRead = new CrudReadQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
      
      $crudRead->uri(array('http://foo.com/datasets/tests/foo', 'http://foo.com/datasets/tests/bar'))               
               ->mime('text/xml')
               ->dataset(array($settings->testDataset, $settings->testDataset))
               ->excludeLinksback()
               ->excludeReification()
               ->sourceInterface($settings->crudReadInterface)
               ->sourceInterfaceVersion($settings->crudReadInterfaceVersion)
               ->send();
      
      $this->assertEquals($crudRead->getStatus(), "200", "Debugging information: ".var_export($crudRead, TRUE));                                       
      
      utilities\validateParameterTextXml($this, $crudRead);

      $resultset = new Resultset($settings->endpointUrl);
      
      // To test the structXML serialization, we import it into a Resultset() object and we export as RDF+XML
      // then we compare it to the expected RDF+XML resultset.
      $resultset->importStructXMLResultset($crudRead->getResultset());
            
      $this->assertTrue(utilities\compareRdf($resultset->getResultsetRDFXML(), file_get_contents($settings->contentDir.'validation/crud_read_revisioned_tworecords_nolinksback_noreification.xml')));
      
      utilities\deleteRevisionedRecord();

      unset($crudCreate);
      unset($settings);         
    }     
     
    
    public function test_GetTwoRecords_Revisioned_NoLinksback_NoReification_Resultset() {
      
      $settings = new Config(); 

      utilities\deleteRevisionedRecord();
      
      $this->assertTrue(utilities\createRevisionedRecord(), "Can't create the revision record");      
                 
      $crudRead = new CrudReadQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
      
      $crudRead->uri(array('http://foo.com/datasets/tests/foo', 'http://foo.com/datasets/tests/bar'))               
               ->mime('resultset')
               ->dataset(array($settings->testDataset, $settings->testDataset))
               ->excludeLinksback()
               ->excludeReification()
               ->sourceInterface($settings->crudReadInterface)
               ->sourceInterfaceVersion($settings->crudReadInterfaceVersion)
               ->send();
      
      $this->assertEquals($crudRead->getStatus(), "200", "Debugging information: ".var_export($crudRead, TRUE));                                       
      
      $resultset = new Resultset($settings->endpointUrl);
      
      // To test the Resultset we export it as RDF+XML
      // then we compare it to the expected RDF+XML resultset.
      $this->assertTrue(utilities\compareRdf($crudRead->getResultset()->getResultsetRDFXML(), file_get_contents($settings->contentDir.'validation/crud_read_revisioned_tworecords_nolinksback_noreification.xml')));
      
      utilities\deleteRevisionedRecord();

      unset($crudCreate);
      unset($settings);         
    }    

    public function test_GetTwoRecords_Revisioned_Linksback_NoReification_RDFXML() {
      
      $settings = new Config(); 

      utilities\deleteRevisionedRecord();
      
      $this->assertTrue(utilities\createRevisionedRecord(), "Can't create the revision record");      
                 
      $crudRead = new CrudReadQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
      
      $crudRead->uri(array('http://foo.com/datasets/tests/foo', 'http://foo.com/datasets/tests/bar'))               
               ->mime('application/rdf+xml')
               ->dataset(array($settings->testDataset, $settings->testDataset))
               ->includeLinksback()
               ->excludeReification()
               ->sourceInterface($settings->crudReadInterface)
               ->sourceInterfaceVersion($settings->crudReadInterfaceVersion)
               ->send();

      $this->assertEquals($crudRead->getStatus(), "200", "Debugging information: ".var_export($crudRead, TRUE));                                       
      utilities\validateParameterApplicationRdfXml($this, $crudRead);
      
      $this->assertTrue(utilities\compareRdf($crudRead->getResultset(), file_get_contents($settings->contentDir.'validation/crud_read_revisioned_tworecords_linksback_noreification.xml')));
      
      utilities\deleteRevisionedRecord();

      unset($crudCreate);
      unset($settings);         
    }  

    public function test_GetTwoRecords_Revisioned_Linksback_NoReification_RDFN3() {
      
      $settings = new Config(); 

      utilities\deleteRevisionedRecord();
      
      $this->assertTrue(utilities\createRevisionedRecord(), "Can't create the revision record");      
                 
      $crudRead = new CrudReadQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
      
      $crudRead->uri(array('http://foo.com/datasets/tests/foo', 'http://foo.com/datasets/tests/bar'))               
               ->mime('application/rdf+n3')
               ->dataset(array($settings->testDataset, $settings->testDataset))
               ->includeLinksback()
               ->excludeReification()
               ->sourceInterface($settings->crudReadInterface)
               ->sourceInterfaceVersion($settings->crudReadInterfaceVersion)
               ->send();
      
      $this->assertEquals($crudRead->getStatus(), "200", "Debugging information: ".var_export($crudRead, TRUE));                                       
      
      utilities\validateParameterApplicationRdfN3($this, $crudRead);
      
      $this->assertTrue(utilities\compareRdf($crudRead->getResultset(), file_get_contents($settings->contentDir.'validation/crud_read_revisioned_tworecords_linksback_noreification.xml')));
      
      utilities\deleteRevisionedRecord();

      unset($crudCreate);
      unset($settings);         
    } 
    
    public function test_GetTwoRecords_Revisioned_Linksback_NoReification_structJSON() {
      
      $settings = new Config(); 

      utilities\deleteRevisionedRecord();
      
      $this->assertTrue(utilities\createRevisionedRecord(), "Can't create the revision record");      
                 
      $crudRead = new CrudReadQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
      
      $crudRead->uri(array('http://foo.com/datasets/tests/foo', 'http://foo.com/datasets/tests/bar'))               
               ->mime('application/json')
               ->dataset(array($settings->testDataset, $settings->testDataset))
               ->includeLinksback()
               ->excludeReification()
               ->sourceInterface($settings->crudReadInterface)
               ->sourceInterfaceVersion($settings->crudReadInterfaceVersion)
               ->send();

      $this->assertEquals($crudRead->getStatus(), "200", "Debugging information: ".var_export($crudRead, TRUE));                                       

      utilities\validateParameterApplicationJson($this, $crudRead);

      $this->assertTrue(utilities\compareStructJSON($crudRead->getResultset(), file_get_contents($settings->contentDir.'validation/crud_read_revisioned_tworecords_linksback_noreification.json')));
      
      utilities\deleteRevisionedRecord();

      unset($crudCreate);
      unset($settings);         
    }    
        
    public function test_GetTwoRecords_Revisioned_Linksback_NoReification_structXML() {
      
      $settings = new Config(); 

      utilities\deleteRevisionedRecord();
      
      $this->assertTrue(utilities\createRevisionedRecord(), "Can't create the revision record");      
                 
      $crudRead = new CrudReadQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
      
      $crudRead->uri(array('http://foo.com/datasets/tests/foo', 'http://foo.com/datasets/tests/bar'))               
               ->mime('text/xml')
               ->dataset(array($settings->testDataset, $settings->testDataset))
               ->includeLinksback()
               ->excludeReification()
               ->sourceInterface($settings->crudReadInterface)
               ->sourceInterfaceVersion($settings->crudReadInterfaceVersion)
               ->send();
      
      $this->assertEquals($crudRead->getStatus(), "200", "Debugging information: ".var_export($crudRead, TRUE));                                       
      
      utilities\validateParameterTextXml($this, $crudRead);

      $resultset = new Resultset($settings->endpointUrl);
      
      // To test the structXML serialization, we import it into a Resultset() object and we export as RDF+XML
      // then we compare it to the expected RDF+XML resultset.
      $resultset->importStructXMLResultset($crudRead->getResultset());
            
      $this->assertTrue(utilities\compareRdf($resultset->getResultsetRDFXML(), file_get_contents($settings->contentDir.'validation/crud_read_revisioned_tworecords_linksback_noreification.xml')));
      
      utilities\deleteRevisionedRecord();

      unset($crudCreate);
      unset($settings);         
    }     
     
    
    public function test_GetTwoRecords_Revisioned_Linksback_NoReification_Resultset() {
      
      $settings = new Config(); 

      utilities\deleteRevisionedRecord();
      
      $this->assertTrue(utilities\createRevisionedRecord(), "Can't create the revision record");      
                 
      $crudRead = new CrudReadQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
      
      $crudRead->uri(array('http://foo.com/datasets/tests/foo', 'http://foo.com/datasets/tests/bar'))               
               ->mime('resultset')
               ->dataset(array($settings->testDataset, $settings->testDataset))
               ->includeLinksback()
               ->excludeReification()
               ->sourceInterface($settings->crudReadInterface)
               ->sourceInterfaceVersion($settings->crudReadInterfaceVersion)
               ->send();
      
      $this->assertEquals($crudRead->getStatus(), "200", "Debugging information: ".var_export($crudRead, TRUE));                                       
      
      $resultset = new Resultset($settings->endpointUrl);
      
      // To test the Resultset we export it as RDF+XML
      // then we compare it to the expected RDF+XML resultset.
      $this->assertTrue(utilities\compareRdf($crudRead->getResultset()->getResultsetRDFXML(), file_get_contents($settings->contentDir.'validation/crud_read_revisioned_tworecords_linksback_noreification.xml')));
      
      utilities\deleteRevisionedRecord();

      unset($crudCreate);
      unset($settings);         
    }

    public function test_GetTwoRecords_Revisioned_Linksback_Reification_RDFXML() {
      
      $settings = new Config(); 

      utilities\deleteRevisionedRecord();
      
      $this->assertTrue(utilities\createRevisionedRecord(), "Can't create the revision record");      
                 
      $crudRead = new CrudReadQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
      
      $crudRead->uri(array('http://foo.com/datasets/tests/foo', 'http://foo.com/datasets/tests/bar'))               
               ->mime('application/rdf+xml')
               ->dataset(array($settings->testDataset, $settings->testDataset))
               ->includeLinksback()
               ->includeReification()
               ->sourceInterface($settings->crudReadInterface)
               ->sourceInterfaceVersion($settings->crudReadInterfaceVersion)
               ->send();
                              
      $this->assertEquals($crudRead->getStatus(), "200", "Debugging information: ".var_export($crudRead, TRUE));                                       
      utilities\validateParameterApplicationRdfXml($this, $crudRead);

      $this->assertTrue(utilities\compareRdf($crudRead->getResultset(), file_get_contents($settings->contentDir.'validation/crud_read_revisioned_tworecords_linksback_reification.xml')));
      
      utilities\deleteRevisionedRecord();

      unset($crudCreate);
      unset($settings);         
    }  

    public function test_GetTwoRecords_Revisioned_Linksback_Reification_RDFN3() {
      
      $settings = new Config(); 

      utilities\deleteRevisionedRecord();
      
      $this->assertTrue(utilities\createRevisionedRecord(), "Can't create the revision record");      
                 
      $crudRead = new CrudReadQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
      
      $crudRead->uri(array('http://foo.com/datasets/tests/foo', 'http://foo.com/datasets/tests/bar'))               
               ->mime('application/rdf+n3')
               ->dataset(array($settings->testDataset, $settings->testDataset))
               ->includeLinksback()
               ->includeReification()
               ->sourceInterface($settings->crudReadInterface)
               ->sourceInterfaceVersion($settings->crudReadInterfaceVersion)
               ->send();
      
      $this->assertEquals($crudRead->getStatus(), "200", "Debugging information: ".var_export($crudRead, TRUE));                                       
      
      utilities\validateParameterApplicationRdfN3($this, $crudRead);
      
      $this->assertTrue(utilities\compareRdf($crudRead->getResultset(), file_get_contents($settings->contentDir.'validation/crud_read_revisioned_tworecords_linksback_reification.xml')));
      
      utilities\deleteRevisionedRecord();

      unset($crudCreate);
      unset($settings);         
    } 
    
    public function test_GetTwoRecords_Revisioned_Linksback_Reification_structJSON() {
      
      $settings = new Config(); 

      utilities\deleteRevisionedRecord();
      
      $this->assertTrue(utilities\createRevisionedRecord(), "Can't create the revision record");      
                 
      $crudRead = new CrudReadQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
      
      $crudRead->uri(array('http://foo.com/datasets/tests/foo', 'http://foo.com/datasets/tests/bar'))               
               ->mime('application/json')
               ->dataset(array($settings->testDataset, $settings->testDataset))
               ->includeLinksback()
               ->includeReification()
               ->sourceInterface($settings->crudReadInterface)
               ->sourceInterfaceVersion($settings->crudReadInterfaceVersion)
               ->send();
      
      $this->assertEquals($crudRead->getStatus(), "200", "Debugging information: ".var_export($crudRead, TRUE));                                       

      utilities\validateParameterApplicationJson($this, $crudRead);

      $this->assertTrue(utilities\compareStructJSON($crudRead->getResultset(), file_get_contents($settings->contentDir.'validation/crud_read_revisioned_tworecords_linksback_reification.json')));
      
      utilities\deleteRevisionedRecord();

      unset($crudCreate);
      unset($settings);         
    }    
        
    public function test_GetTwoRecords_Revisioned_Linksback_Reification_structXML() {
      
      $settings = new Config(); 

      utilities\deleteRevisionedRecord();
      
      $this->assertTrue(utilities\createRevisionedRecord(), "Can't create the revision record");      
                 
      $crudRead = new CrudReadQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
      
      $crudRead->uri(array('http://foo.com/datasets/tests/foo', 'http://foo.com/datasets/tests/bar'))               
               ->mime('text/xml')
               ->dataset(array($settings->testDataset, $settings->testDataset))
               ->includeLinksback()
               ->includeReification()
               ->sourceInterface($settings->crudReadInterface)
               ->sourceInterfaceVersion($settings->crudReadInterfaceVersion)
               ->send();
      
      $this->assertEquals($crudRead->getStatus(), "200", "Debugging information: ".var_export($crudRead, TRUE));                                       
      
      utilities\validateParameterTextXml($this, $crudRead);

      $resultset = new Resultset($settings->endpointUrl);
      
      // To test the structXML serialization, we import it into a Resultset() object and we export as RDF+XML
      // then we compare it to the expected RDF+XML resultset.
      $resultset->importStructXMLResultset($crudRead->getResultset());
            
      $this->assertTrue(utilities\compareRdf($resultset->getResultsetRDFXML(), file_get_contents($settings->contentDir.'validation/crud_read_revisioned_tworecords_linksback_reification.xml')));
      
      utilities\deleteRevisionedRecord();

      unset($crudCreate);
      unset($settings);         
    }     
     
    
    public function test_GetTwoRecords_Revisioned_Linksback_Reification_Resultset() {
      
      $settings = new Config(); 

      utilities\deleteRevisionedRecord();
      
      $this->assertTrue(utilities\createRevisionedRecord(), "Can't create the revision record");      
                 
      $crudRead = new CrudReadQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
      
      $crudRead->uri(array('http://foo.com/datasets/tests/foo', 'http://foo.com/datasets/tests/bar'))               
               ->mime('resultset')
               ->dataset(array($settings->testDataset, $settings->testDataset))
               ->includeLinksback()
               ->includeReification()
               ->sourceInterface($settings->crudReadInterface)
               ->sourceInterfaceVersion($settings->crudReadInterfaceVersion)
               ->send();
      
      $this->assertEquals($crudRead->getStatus(), "200", "Debugging information: ".var_export($crudRead, TRUE));                                       
      
      $resultset = new Resultset($settings->endpointUrl);
      
      // To test the Resultset we export it as RDF+XML
      // then we compare it to the expected RDF+XML resultset.
      $this->assertTrue(utilities\compareRdf($crudRead->getResultset()->getResultsetRDFXML(), file_get_contents($settings->contentDir.'validation/crud_read_revisioned_tworecords_linksback_reification.xml')));
      
      utilities\deleteRevisionedRecord();

      unset($crudCreate);
      unset($settings);         
    }

    public function test_GetTwoRecords_Revisioned_Linksback_Reification_OneAttribute_RDFXML() {
      
      $settings = new Config(); 

      utilities\deleteRevisionedRecord();
      
      $this->assertTrue(utilities\createRevisionedRecord(), "Can't create the revision record");      
                 
      $crudRead = new CrudReadQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
      
      $crudRead->uri(array('http://foo.com/datasets/tests/foo', 'http://foo.com/datasets/tests/bar'))               
               ->includeAttributes(array('http://purl.org/ontology/wsf#related_product'))
               ->mime('application/rdf+xml')
               ->dataset(array($settings->testDataset, $settings->testDataset))      
               ->includeLinksback()
               ->includeReification()
               ->sourceInterface($settings->crudReadInterface)
               ->sourceInterfaceVersion($settings->crudReadInterfaceVersion)
               ->send();

      $this->assertEquals($crudRead->getStatus(), "200", "Debugging information: ".var_export($crudRead, TRUE));                                       
      utilities\validateParameterApplicationRdfXml($this, $crudRead);

      $this->assertTrue(utilities\compareRdf($crudRead->getResultset(), file_get_contents($settings->contentDir.'validation/crud_read_revisioned_tworecords_linksback_reification_oneattribute.xml')));
      
      utilities\deleteRevisionedRecord();

      unset($crudCreate);
      unset($settings);         
    }  

    public function test_GetTwoRecords_Revisioned_Linksback_Reification_OneAttribute_RDFN3() {
      
      $settings = new Config(); 

      utilities\deleteRevisionedRecord();
      
      $this->assertTrue(utilities\createRevisionedRecord(), "Can't create the revision record");      
                 
      $crudRead = new CrudReadQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
      
      $crudRead->uri(array('http://foo.com/datasets/tests/foo', 'http://foo.com/datasets/tests/bar'))               
               ->includeAttributes(array('http://purl.org/ontology/wsf#related_product'))
               ->mime('application/rdf+n3')
               ->dataset(array($settings->testDataset, $settings->testDataset))
               ->includeLinksback()
               ->includeReification()
               ->sourceInterface($settings->crudReadInterface)
               ->sourceInterfaceVersion($settings->crudReadInterfaceVersion)
               ->send();
      
      $this->assertEquals($crudRead->getStatus(), "200", "Debugging information: ".var_export($crudRead, TRUE));                                       
      
      utilities\validateParameterApplicationRdfN3($this, $crudRead);
      
      $this->assertTrue(utilities\compareRdf($crudRead->getResultset(), file_get_contents($settings->contentDir.'validation/crud_read_revisioned_tworecords_linksback_reification_oneattribute.xml')));
      
      utilities\deleteRevisionedRecord();

      unset($crudCreate);
      unset($settings);         
    } 
    
    public function test_GetTwoRecords_Revisioned_Linksback_Reification_OneAttribute_structJSON() {
      
      $settings = new Config(); 

      utilities\deleteRevisionedRecord();
      
      $this->assertTrue(utilities\createRevisionedRecord(), "Can't create the revision record");      
                 
      $crudRead = new CrudReadQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
      
      $crudRead->uri(array('http://foo.com/datasets/tests/foo', 'http://foo.com/datasets/tests/bar'))               
               ->includeAttributes(array('http://purl.org/ontology/wsf#related_product'))
               ->mime('application/json')
               ->dataset(array($settings->testDataset, $settings->testDataset))
               ->includeLinksback()
               ->includeReification()
               ->sourceInterface($settings->crudReadInterface)
               ->sourceInterfaceVersion($settings->crudReadInterfaceVersion)
               ->send();
      
      $this->assertEquals($crudRead->getStatus(), "200", "Debugging information: ".var_export($crudRead, TRUE));                                       

      utilities\validateParameterApplicationJson($this, $crudRead);

      $this->assertTrue(utilities\compareStructJSON($crudRead->getResultset(), file_get_contents($settings->contentDir.'validation/crud_read_revisioned_tworecords_linksback_reification_oneattribute.json')));
      
      utilities\deleteRevisionedRecord();

      unset($crudCreate);
      unset($settings);         
    }    
        
    public function test_GetTwoRecords_Revisioned_Linksback_Reification_OneAttribute_structXML() {
      
      $settings = new Config(); 

      utilities\deleteRevisionedRecord();
      
      $this->assertTrue(utilities\createRevisionedRecord(), "Can't create the revision record");      
                 
      $crudRead = new CrudReadQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
      
      $crudRead->uri(array('http://foo.com/datasets/tests/foo', 'http://foo.com/datasets/tests/bar'))               
               ->includeAttributes(array('http://purl.org/ontology/wsf#related_product'))
               ->mime('text/xml')
               ->dataset(array($settings->testDataset, $settings->testDataset))
               ->includeLinksback()
               ->includeReification()
               ->sourceInterface($settings->crudReadInterface)
               ->sourceInterfaceVersion($settings->crudReadInterfaceVersion)
               ->send();
      
      $this->assertEquals($crudRead->getStatus(), "200", "Debugging information: ".var_export($crudRead, TRUE));                                       
      
      utilities\validateParameterTextXml($this, $crudRead);

      $resultset = new Resultset($settings->endpointUrl);
      
      // To test the structXML serialization, we import it into a Resultset() object and we export as RDF+XML
      // then we compare it to the expected RDF+XML resultset.
      $resultset->importStructXMLResultset($crudRead->getResultset());
            
      $this->assertTrue(utilities\compareRdf($resultset->getResultsetRDFXML(), file_get_contents($settings->contentDir.'validation/crud_read_revisioned_tworecords_linksback_reification_oneattribute.xml')));
      
      utilities\deleteRevisionedRecord();

      unset($crudCreate);
      unset($settings);         
    }     
         
    public function test_GetTwoRecords_Revisioned_Linksback_Reification_OneAttribute_Resultset() {
      
      $settings = new Config(); 

      utilities\deleteRevisionedRecord();
      
      $this->assertTrue(utilities\createRevisionedRecord(), "Can't create the revision record");      
                 
      $crudRead = new CrudReadQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
      
      $crudRead->uri(array('http://foo.com/datasets/tests/foo', 'http://foo.com/datasets/tests/bar'))               
               ->includeAttributes(array('http://purl.org/ontology/wsf#related_product'))
               ->mime('resultset')
               ->dataset(array($settings->testDataset, $settings->testDataset))
               ->includeLinksback()
               ->includeReification()
               ->sourceInterface($settings->crudReadInterface)
               ->sourceInterfaceVersion($settings->crudReadInterfaceVersion)
               ->send();
      
      $this->assertEquals($crudRead->getStatus(), "200", "Debugging information: ".var_export($crudRead, TRUE));                                       
      
      $resultset = new Resultset($settings->endpointUrl);
      
      // To test the Resultset we export it as RDF+XML
      // then we compare it to the expected RDF+XML resultset.
      $this->assertTrue(utilities\compareRdf($crudRead->getResultset()->getResultsetRDFXML(), file_get_contents($settings->contentDir.'validation/crud_read_revisioned_tworecords_linksback_reification_oneattribute.xml')));
      
      utilities\deleteRevisionedRecord();

      unset($crudCreate);
      unset($settings);         
    }  
  }

  
?>