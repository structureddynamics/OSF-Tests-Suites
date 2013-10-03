<?php

  namespace StructuredDynamics\osf\tests\ws\revision\lister;
  
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
  
  class RevisionListerTest extends \PHPUnit_Framework_TestCase {
    
    static private $outputs = array();

    public function testWrongEndpointUrl() {
      
      $settings = new Config();          
      
      $wsq = new WebServiceQuerier($settings->endpointUrl . "revision/lister/" . "wrong", 
                                   "get", 
                                   "text/xml",
                                   "&uri=" . urlencode('') .
                                   "&dataset=" . urlencode($settings->testDataset) .
                                   "&mode=" . urlencode('short') .
                                   "&interface=". urlencode($settings->revisionListerInterface) .
                                   "&version=". urlencode($settings->revisionListerInterfaceVersion) .
                                   "&registered_ip=" . urlencode("Self"));        
                         
      $this->assertEquals($wsq->getStatus(), "404", "Debugging information: ".var_export($wsq, TRUE));                                       
      $this->assertEquals($wsq->getStatusMessage(), "Not Found", "Debugging information: ".var_export($wsq, TRUE));
      
      unset($wsq);
      unset($settings);
    }
    
    public function testWrongEndpointMethodPost() {
      
      $settings = new Config();  
      
      $wsq = new WebServiceQuerier($settings->endpointUrl . "revision/lister/", 
                                   "post", 
                                   "text/xml",
                                   "&uri=" . urlencode('') .
                                   "&dataset=" . urlencode($settings->testDataset) .
                                   "&mode=" . urlencode('short') .
                                   "&interface=". urlencode($settings->revisionListerInterface) .
                                   "&version=". urlencode($settings->revisionListerInterfaceVersion) .
                                   "&registered_ip=" . urlencode("Self"));       
                                   
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

      $revisionLister = new RevisionListerQuery($settings->endpointUrl);
      
      $revisionLister->dataset($settings->testDataset)
                     ->uri('http://foo.com/datasets/tests/foo')
                     ->shortResults()
                     ->sourceInterface($settings->revisionListerInterface)
                     ->sourceInterfaceVersion($settings->revisionListerInterfaceVersion)                   
                     ->send();
            
      $this->assertEquals($revisionLister->getStatus(), "200", "Debugging information: ".var_export($revisionLister, TRUE));                                       

      utilities\deleteRevisionedRecord();

      unset($revisionLister);
      unset($settings);    
    }
    
    public function testInvalidInterfaceVersion() {
      
       $settings = new Config();  
      
      utilities\deleteRevisionedRecord();
      
      $this->assertTrue(utilities\createRevisionedRecord(), "Can't create unrevisioned records...");

      $revuri = utilities\getLastRevisionUri('http://foo.com/datasets/tests/foo');
      
      $this->assertFalse($revuri === FALSE, "Debugging information: ".var_export($revuri, TRUE));                                       

      $revisionLister = new RevisionListerQuery($settings->endpointUrl);
      
      $revisionLister->dataset($settings->testDataset)
                     ->uri('http://foo.com/datasets/tests/foo')
                     ->shortResults()
                     ->sourceInterface($settings->revisionListerInterface)
                     ->sourceInterfaceVersion('666.7')                   
                     ->send();
                           
      $this->assertEquals($revisionLister->getStatus(), "400", "Debugging information: ".var_export($revisionLister, TRUE));                                       
      $this->assertEquals($revisionLister->getStatusMessage(), "Bad Request", "Debugging information: ".var_export($revisionLister, TRUE));
      $this->assertEquals($revisionLister->error->id, "WS-REVISION-LISTER-302", "Debugging information: ".var_export($revisionLister, TRUE));                                       

      utilities\deleteRevisionedRecord();

      unset($revisionLister);
      unset($settings);                 
    }  
    
    public function testInterfaceExists() {
      
      $settings = new Config();  
      
      utilities\deleteRevisionedRecord();
      
      $this->assertTrue(utilities\createRevisionedRecord(), "Can't create unrevisioned records...");

      $revuri = utilities\getLastRevisionUri('http://foo.com/datasets/tests/foo');
      
      $this->assertFalse($revuri === FALSE, "Debugging information: ".var_export($revuri, TRUE));                                       

      $revisionLister = new RevisionListerQuery($settings->endpointUrl);
      
      $revisionLister->dataset($settings->testDataset)
                     ->uri('http://foo.com/datasets/tests/foo')
                     ->shortResults()
                     ->sourceInterface($settings->revisionListerInterface)
                     ->sourceInterfaceVersion($settings->revisionListerInterfaceVersion)                   
                     ->send();
            
      $this->assertEquals($revisionLister->getStatus(), "200", "Debugging information: ".var_export($revisionLister, TRUE));                                       

      utilities\deleteRevisionedRecord();

      unset($revisionLister);
      unset($settings);   
    }   
    
    public function testInterfaceNotExisting() {
      
      $settings = new Config();  
      
      utilities\deleteRevisionedRecord();
      
      $this->assertTrue(utilities\createRevisionedRecord(), "Can't create unrevisioned records...");

      $revuri = utilities\getLastRevisionUri('http://foo.com/datasets/tests/foo');
      
      $this->assertFalse($revuri === FALSE, "Debugging information: ".var_export($revuri, TRUE));                                       

      $revisionLister = new RevisionListerQuery($settings->endpointUrl);
      
      $revisionLister->dataset($settings->testDataset)
                     ->uri('http://foo.com/datasets/tests/foo')
                     ->shortResults()
                     ->sourceInterface('unexisting-interface')
                     ->sourceInterfaceVersion($settings->revisionListerInterfaceVersion)                   
                     ->send();
            
      $this->assertEquals($revisionLister->getStatus(), "400", "Debugging information: ".var_export($revisionLister, TRUE));                                       
      $this->assertEquals($revisionLister->getStatusMessage(), "Bad Request", "Debugging information: ".var_export($revisionLister, TRUE));
      $this->assertEquals($revisionLister->error->id, "WS-REVISION-LISTER-300", "Debugging information: ".var_export($revisionLister, TRUE));                                       

      utilities\deleteRevisionedRecord();

      unset($revisionLister);
      unset($settings);        
   }  

   public function testModeNotExisting() {
      $settings = new Config();  
      
      utilities\deleteRevisionedRecord();
      
      $this->assertTrue(utilities\createRevisionedRecord(), "Can't create unrevisioned records...");

      $revuri = utilities\getLastRevisionUri('http://foo.com/datasets/tests/foo');
      
      $this->assertFalse($revuri === FALSE, "Debugging information: ".var_export($revuri, TRUE));                                       

      $wsq = new WebServiceQuerier($settings->endpointUrl . "revision/lister/", 
                                   "get", 
                                   "text/xml",
                                   "&uri=" . urlencode('http://foo.com/datasets/tests/foo') .
                                   "&dataset=" . urlencode($settings->testDataset) .
                                   "&mode=" . urlencode('unexisting-mode') .
                                   "&interface=". urlencode($settings->revisionListerInterface) .
                                   "&version=". urlencode($settings->revisionListerInterfaceVersion) .
                                   "&registered_ip=" . urlencode("Self"));        
   
                           
      $this->assertEquals($wsq->getStatus(), "400", "Debugging information: ".var_export($wsq, TRUE));                                       
      $this->assertEquals($wsq->getStatusMessage(), "Bad Request", "Debugging information: ".var_export($wsq, TRUE));
      $this->assertEquals($wsq->error->id, "WS-REVISION-LISTER-303", "Debugging information: ".var_export($wsq, TRUE));                                       

      utilities\deleteRevisionedRecord();

      unset($wsq);
      unset($settings);       
   }  
   
    public function testShortMode_RDFXML() {
      
      $settings = new Config();  
      
      utilities\deleteRevisionedRecord();
      
      $this->assertTrue(utilities\createRevisionedRecord(), "Can't create unrevisioned records...");

      $revuri = utilities\getLastRevisionUri('http://foo.com/datasets/tests/foo');
      
      $this->assertFalse($revuri === FALSE, "Debugging information: ".var_export($revuri, TRUE));                                       

      $revisionLister = new RevisionListerQuery($settings->endpointUrl);
      
      $revisionLister->dataset($settings->testDataset)
                     ->uri('http://foo.com/datasets/tests/foo')
                     ->mime('application/rdf+xml')
                     ->shortResults()
                     ->sourceInterface($settings->revisionListerInterface)
                     ->sourceInterfaceVersion($settings->revisionListerInterfaceVersion)                   
                     ->send();
            
      $this->assertEquals($revisionLister->getStatus(), "200", "Debugging information: ".var_export($revisionLister, TRUE));                                       
      utilities\validateParameterApplicationRdfXml($this, $revisionLister);

      $expected = new \DOMDocument;
      $expected->loadXML(file_get_contents($settings->contentDir.'validation/revision_lister_modeshort.xml'));
 
      $actual = new \DOMDocument;
      $actual->loadXML($revisionLister->getResultset());      
      
      $this->assertEqualXMLStructure($expected->firstChild, $actual->firstChild, TRUE);      

      utilities\deleteRevisionedRecord();

      unset($revisionLister);
      unset($settings);   
    } 
            
    public function testShortMode_RDFN3() {
      
      $settings = new Config();  
      
      utilities\deleteRevisionedRecord();
      
      $this->assertTrue(utilities\createRevisionedRecord(), "Can't create unrevisioned records...");

      $revuri = utilities\getLastRevisionUri('http://foo.com/datasets/tests/foo');
      
      $this->assertFalse($revuri === FALSE, "Debugging information: ".var_export($revuri, TRUE));                                       

      $revisionLister = new RevisionListerQuery($settings->endpointUrl);
      
      $revisionLister->dataset($settings->testDataset)
                     ->uri('http://foo.com/datasets/tests/foo')
                     ->mime('application/rdf+n3')
                     ->shortResults()
                     ->sourceInterface($settings->revisionListerInterface)
                     ->sourceInterfaceVersion($settings->revisionListerInterfaceVersion)                   
                     ->send();
            
      $this->assertEquals($revisionLister->getStatus(), "200", "Debugging information: ".var_export($revisionLister, TRUE));                                       
      
      utilities\validateParameterApplicationRdfN3($this, $revisionLister);
      
      include_once($settings->osfInstanceFolder."framework/arc2/ARC2.php");
      
      $parserExpected = \ARC2::getRDFParser();
      $parserExpected->parse($settings->testDataset, file_get_contents($settings->contentDir.'validation/revision_lister_modeshort.xml'));      
      
      $parserActual = \ARC2::getRDFParser();
      $parserActual->parse($settings->testDataset, $revisionLister->getResultset());      

      $expected = new \DOMDocument;
      $expected->loadXML($parserExpected->toRDFXML($parserExpected->getSimpleIndex(0)));
 
      $actual = new \DOMDocument;
      $actual->loadXML($parserActual->toRDFXML($parserActual->getSimpleIndex(0)));      
      
      $this->assertEqualXMLStructure($expected->firstChild, $actual->firstChild, TRUE);      

      utilities\deleteRevisionedRecord();

      unset($revisionLister);
      unset($settings);   
    }

    public function testShortMode_StructJSON() {
      
      $settings = new Config();  
      
      utilities\deleteRevisionedRecord();
      
      $this->assertTrue(utilities\createRevisionedRecord(), "Can't create unrevisioned records...");

      $revuri = utilities\getLastRevisionUri('http://foo.com/datasets/tests/foo');
      
      $this->assertFalse($revuri === FALSE, "Debugging information: ".var_export($revuri, TRUE));                                       

      $revisionLister = new RevisionListerQuery($settings->endpointUrl);
      
      $revisionLister->dataset($settings->testDataset)
                     ->uri('http://foo.com/datasets/tests/foo')
                     ->mime('application/json')
                     ->shortResults()
                     ->sourceInterface($settings->revisionListerInterface)
                     ->sourceInterfaceVersion($settings->revisionListerInterfaceVersion)                   
                     ->send();
            
      $this->assertEquals($revisionLister->getStatus(), "200", "Debugging information: ".var_export($revisionLister, TRUE));                                       
      
      utilities\validateParameterApplicationJson($this, $revisionLister);
      
      $this->assertTrue(utilities\compareStructJSON($revisionLister->getResultset(), file_get_contents($settings->contentDir.'validation/revision_lister_modeshort.json'), FALSE));

      utilities\deleteRevisionedRecord();

      unset($revisionLister);
      unset($settings);   
    }
    
    public function testShortMode_StructXML() {
      
      $settings = new Config();  
      
      utilities\deleteRevisionedRecord();
      
      $this->assertTrue(utilities\createRevisionedRecord(), "Can't create unrevisioned records...");

      $revuri = utilities\getLastRevisionUri('http://foo.com/datasets/tests/foo');
      
      $this->assertFalse($revuri === FALSE, "Debugging information: ".var_export($revuri, TRUE));                                       

      $revisionLister = new RevisionListerQuery($settings->endpointUrl);
      
      $revisionLister->dataset($settings->testDataset)
                     ->uri('http://foo.com/datasets/tests/foo')
                     ->mime('text/xml')
                     ->shortResults()
                     ->sourceInterface($settings->revisionListerInterface)
                     ->sourceInterfaceVersion($settings->revisionListerInterfaceVersion)                   
                     ->send();
            
      $this->assertEquals($revisionLister->getStatus(), "200", "Debugging information: ".var_export($revisionLister, TRUE));                                       
      
      utilities\validateParameterTextXml($this, $revisionLister);

      $resultset = new Resultset($settings->endpointUrl);
      
      // To test the structXML serialization, we import it into a Resultset() object and we export as RDF+XML
      // then we compare it to the expected RDF+XML resultset.
      $resultset->importStructXMLResultset($revisionLister->getResultset());
      
      $expected = new \DOMDocument;
      $expected->loadXML(file_get_contents($settings->contentDir.'validation/revision_lister_modeshort.xml'));
 
      $actual = new \DOMDocument;
      $actual->loadXML($resultset->getResultsetRDFXML());      
      
      $this->assertEqualXMLStructure($expected->firstChild, $actual->firstChild, TRUE);      
            
      utilities\deleteRevisionedRecord();

      unset($revisionLister);
      unset($settings);   
    }
    
    public function testShortMode_Resultset() {
      
      $settings = new Config();  
      
      utilities\deleteRevisionedRecord();
      
      $this->assertTrue(utilities\createRevisionedRecord(), "Can't create unrevisioned records...");

      $revuri = utilities\getLastRevisionUri('http://foo.com/datasets/tests/foo');
      
      $this->assertFalse($revuri === FALSE, "Debugging information: ".var_export($revuri, TRUE));                                       

      $revisionLister = new RevisionListerQuery($settings->endpointUrl);
      
      $revisionLister->dataset($settings->testDataset)
                     ->uri('http://foo.com/datasets/tests/foo')
                     ->mime('resultset')
                     ->shortResults()
                     ->sourceInterface($settings->revisionListerInterface)
                     ->sourceInterfaceVersion($settings->revisionListerInterfaceVersion)                   
                     ->send();
            
      $this->assertEquals($revisionLister->getStatus(), "200", "Debugging information: ".var_export($revisionLister, TRUE));                                       
      
      $expected = new \DOMDocument;
      $expected->loadXML(file_get_contents($settings->contentDir.'validation/revision_lister_modeshort.xml'));
 
      $actual = new \DOMDocument;
      $actual->loadXML($revisionLister->getResultset()->getResultsetRDFXML());      
      
      $this->assertEqualXMLStructure($expected->firstChild, $actual->firstChild, TRUE);      
            
      utilities\deleteRevisionedRecord();

      unset($revisionLister);
      unset($settings);   
    }

    public function testLongMode_RDFXML() {
      
      $settings = new Config();  
      
      utilities\deleteRevisionedRecord();
      
      $this->assertTrue(utilities\createRevisionedRecord(), "Can't create unrevisioned records...");

      $revuri = utilities\getLastRevisionUri('http://foo.com/datasets/tests/foo');
      
      $this->assertFalse($revuri === FALSE, "Debugging information: ".var_export($revuri, TRUE));                                       

      $revisionLister = new RevisionListerQuery($settings->endpointUrl);
      
      $revisionLister->dataset($settings->testDataset)
                     ->uri('http://foo.com/datasets/tests/foo')
                     ->mime('application/rdf+xml')
                     ->longResults()
                     ->sourceInterface($settings->revisionListerInterface)
                     ->sourceInterfaceVersion($settings->revisionListerInterfaceVersion)                   
                     ->send();

      $this->assertEquals($revisionLister->getStatus(), "200", "Debugging information: ".var_export($revisionLister, TRUE));                                       
      utilities\validateParameterApplicationRdfXml($this, $revisionLister);

      $expected = new \DOMDocument;
      $expected->loadXML(file_get_contents($settings->contentDir.'validation/revision_lister_modelong.xml'));
 
      $actual = new \DOMDocument;
      $actual->loadXML($revisionLister->getResultset());      
      
      $this->assertEqualXMLStructure($expected->firstChild, $actual->firstChild, TRUE);      

      utilities\deleteRevisionedRecord();

      unset($revisionLister);
      unset($settings);   
    } 
            
    public function testLongMode_RDFN3() {
      
      $settings = new Config();  
      
      utilities\deleteRevisionedRecord();
      
      $this->assertTrue(utilities\createRevisionedRecord(), "Can't create unrevisioned records...");

      $revuri = utilities\getLastRevisionUri('http://foo.com/datasets/tests/foo');
      
      $this->assertFalse($revuri === FALSE, "Debugging information: ".var_export($revuri, TRUE));                                       

      $revisionLister = new RevisionListerQuery($settings->endpointUrl);
      
      $revisionLister->dataset($settings->testDataset)
                     ->uri('http://foo.com/datasets/tests/foo')
                     ->mime('application/rdf+n3')
                     ->longResults()
                     ->sourceInterface($settings->revisionListerInterface)
                     ->sourceInterfaceVersion($settings->revisionListerInterfaceVersion)                   
                     ->send();
            
      $this->assertEquals($revisionLister->getStatus(), "200", "Debugging information: ".var_export($revisionLister, TRUE));                                       
      
      utilities\validateParameterApplicationRdfN3($this, $revisionLister);
      
      include_once($settings->osfInstanceFolder."framework/arc2/ARC2.php");
      
      $parserExpected = \ARC2::getRDFParser();
      $parserExpected->parse($settings->testDataset, file_get_contents($settings->contentDir.'validation/revision_lister_modelong.xml'));      
      
      $parserActual = \ARC2::getRDFParser();
      $parserActual->parse($settings->testDataset, $revisionLister->getResultset());      

      $expected = new \DOMDocument;
      $expected->loadXML($parserExpected->toRDFXML($parserExpected->getSimpleIndex(0)));
 
      $actual = new \DOMDocument;
      $actual->loadXML($parserActual->toRDFXML($parserActual->getSimpleIndex(0)));      
      
      $this->assertEqualXMLStructure($expected->firstChild, $actual->firstChild, TRUE);      

      utilities\deleteRevisionedRecord();

      unset($revisionLister);
      unset($settings);   
    }

    public function testLongMode_StructJSON() {
      
      $settings = new Config();  
      
      utilities\deleteRevisionedRecord();
      
      $this->assertTrue(utilities\createRevisionedRecord(), "Can't create unrevisioned records...");

      $revuri = utilities\getLastRevisionUri('http://foo.com/datasets/tests/foo');
      
      $this->assertFalse($revuri === FALSE, "Debugging information: ".var_export($revuri, TRUE));                                       

      $revisionLister = new RevisionListerQuery($settings->endpointUrl);
      
      $revisionLister->dataset($settings->testDataset)
                     ->uri('http://foo.com/datasets/tests/foo')
                     ->mime('application/json')
                     ->longResults()
                     ->sourceInterface($settings->revisionListerInterface)
                     ->sourceInterfaceVersion($settings->revisionListerInterfaceVersion)                   
                     ->send();
       
      $this->assertEquals($revisionLister->getStatus(), "200", "Debugging information: ".var_export($revisionLister, TRUE));                                       
      
      utilities\validateParameterApplicationJson($this, $revisionLister);
      
      $this->assertTrue(utilities\compareStructJSON($revisionLister->getResultset(), file_get_contents($settings->contentDir.'validation/revision_lister_modelong.json'), FALSE));

      utilities\deleteRevisionedRecord();

      unset($revisionLister);
      unset($settings);   
    }
    
    public function testLongMode_StructXML() {
      
      $settings = new Config();  
      
      utilities\deleteRevisionedRecord();
      
      $this->assertTrue(utilities\createRevisionedRecord(), "Can't create unrevisioned records...");

      $revuri = utilities\getLastRevisionUri('http://foo.com/datasets/tests/foo');
      
      $this->assertFalse($revuri === FALSE, "Debugging information: ".var_export($revuri, TRUE));                                       

      $revisionLister = new RevisionListerQuery($settings->endpointUrl);
      
      $revisionLister->dataset($settings->testDataset)
                     ->uri('http://foo.com/datasets/tests/foo')
                     ->mime('text/xml')
                     ->longResults()
                     ->sourceInterface($settings->revisionListerInterface)
                     ->sourceInterfaceVersion($settings->revisionListerInterfaceVersion)                   
                     ->send();
            
      $this->assertEquals($revisionLister->getStatus(), "200", "Debugging information: ".var_export($revisionLister, TRUE));                                       
      
      utilities\validateParameterTextXml($this, $revisionLister);

      $resultset = new Resultset($settings->endpointUrl);
      
      // To test the structXML serialization, we import it into a Resultset() object and we export as RDF+XML
      // then we compare it to the expected RDF+XML resultset.
      $resultset->importStructXMLResultset($revisionLister->getResultset());
      
      $expected = new \DOMDocument;
      $expected->loadXML(file_get_contents($settings->contentDir.'validation/revision_lister_modelong.xml'));
 
      $actual = new \DOMDocument;
      $actual->loadXML($resultset->getResultsetRDFXML());      
      
      $this->assertEqualXMLStructure($expected->firstChild, $actual->firstChild, TRUE);      
            
      utilities\deleteRevisionedRecord();

      unset($revisionLister);
      unset($settings);   
    }
    
    public function testLongMode_Resultset() {
      
      $settings = new Config();  
      
      utilities\deleteRevisionedRecord();
      
      $this->assertTrue(utilities\createRevisionedRecord(), "Can't create unrevisioned records...");

      $revuri = utilities\getLastRevisionUri('http://foo.com/datasets/tests/foo');
      
      $this->assertFalse($revuri === FALSE, "Debugging information: ".var_export($revuri, TRUE));                                       

      $revisionLister = new RevisionListerQuery($settings->endpointUrl);
      
      $revisionLister->dataset($settings->testDataset)
                     ->uri('http://foo.com/datasets/tests/foo')
                     ->mime('resultset')
                     ->longResults()
                     ->sourceInterface($settings->revisionListerInterface)
                     ->sourceInterfaceVersion($settings->revisionListerInterfaceVersion)                   
                     ->send();
            
      $this->assertEquals($revisionLister->getStatus(), "200", "Debugging information: ".var_export($revisionLister, TRUE));                                       
      
      $expected = new \DOMDocument;
      $expected->loadXML(file_get_contents($settings->contentDir.'validation/revision_lister_modelong.xml'));
 
      $actual = new \DOMDocument;
      $actual->loadXML($revisionLister->getResultset()->getResultsetRDFXML());      
      
      $this->assertEqualXMLStructure($expected->firstChild, $actual->firstChild, TRUE);      
            
      utilities\deleteRevisionedRecord();

      unset($revisionLister);
      unset($settings);   
    }    
}
  
?>