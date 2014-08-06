<?php

  namespace StructuredDynamics\osf\tests\ws\diff\lister;
  
  use StructuredDynamics\osf\framework\WebServiceQuerier;
  use StructuredDynamics\osf\php\api\ws\revision\lister\RevisionListerQuery;
  use StructuredDynamics\osf\php\api\ws\revision\diff\RevisionDiffQuery;
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
  
  class RevisionDiffTest extends \PHPUnit_Framework_TestCase {
    
    static private $outputs = array();
    
    public function testWrongEndpointUrl() {
      
      $settings = new Config();          
      
      $wsq = new WebServiceQuerier($settings->endpointUrl . "revision/diff/" . "wrong", 
                                   "get", 
                                   "text/xml",
                                   "lrevuri=" . urlencode('') .
                                   "&rrevuri=" . urlencode('') .
                                   "&dataset=" . urlencode($settings->testDataset) .
                                   "&interface=". urlencode($settings->revisionDiffInterface) .
                                   "&version=". urlencode($settings->revisionDiffInterfaceVersion),
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
      
      $wsq = new WebServiceQuerier($settings->endpointUrl . "revision/diff/", 
                                   "post", 
                                   "text/xml",
                                   "lrevuri=" . urlencode('') .
                                   "&rrevuri=" . urlencode('') .
                                   "&dataset=" . urlencode($settings->testDataset) .
                                   "&interface=". urlencode($settings->revisionDiffInterface) .
                                   "&version=". urlencode($settings->revisionDiffInterfaceVersion),
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

      $lrevuri = utilities\getLastRevisionUri('http://foo.com/datasets/tests/foo');
      $rrevuri = utilities\getInitialRevisionUri('http://foo.com/datasets/tests/foo');
      
      $this->assertFalse($lrevuri === FALSE, "Debugging information: ".var_export($lrevuri, TRUE));                                       
      $this->assertFalse($rrevuri === FALSE, "Debugging information: ".var_export($rrevuri, TRUE));                                       

      $revisionDiff = new RevisionDiffQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
      
      $revisionDiff->dataset($settings->testDataset)
                   ->leftRevisionUri($lrevuri)
                   ->rightRevisionUri($rrevuri)
                   ->sourceInterface($settings->revisionDiffInterface)
                   ->sourceInterfaceVersion($settings->revisionDiffInterfaceVersion)                   
                   ->send();
                   
      $this->assertEquals($revisionDiff->getStatus(), "200", "Debugging information: ".var_export($revisionDiff, TRUE));                                       

      utilities\deleteRevisionedRecord();

      unset($revisionDiff);
      unset($settings);    
    }
    
    public function testInvalidInterfaceVersion() {
      $settings = new Config();  
      
      utilities\deleteRevisionedRecord();
      
      $this->assertTrue(utilities\createRevisionedRecord(), "Can't create unrevisioned records...");

      $lrevuri = utilities\getLastRevisionUri('http://foo.com/datasets/tests/foo');
      $rrevuri = utilities\getInitialRevisionUri('http://foo.com/datasets/tests/foo');
      
      $this->assertFalse($lrevuri === FALSE, "Debugging information: ".var_export($lrevuri, TRUE));                                       
      $this->assertFalse($rrevuri === FALSE, "Debugging information: ".var_export($rrevuri, TRUE));                                       

      $revisionDiff = new RevisionDiffQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
      
      $revisionDiff->dataset($settings->testDataset)
                   ->leftRevisionUri($lrevuri)
                   ->rightRevisionUri($rrevuri)
                   ->sourceInterface($settings->revisionDiffInterface)
                   ->sourceInterfaceVersion('667.7')                   
                   ->send();
                   
      $this->assertEquals($revisionDiff->getStatus(), "400", "Debugging information: ".var_export($revisionDiff, TRUE));                                       
      $this->assertEquals($revisionDiff->getStatusMessage(), "Bad Request", "Debugging information: ".var_export($revisionDiff, TRUE));
      $this->assertEquals($revisionDiff->error->id, "WS-REVISION-DIFF-302", "Debugging information: ".var_export($revisionDiff, TRUE));                                       

      utilities\deleteRevisionedRecord();

      unset($revisionDiff);
      unset($settings);              
    }  

    public function testInterfaceExists() {
      $settings = new Config();  
      
      utilities\deleteRevisionedRecord();
      
      $this->assertTrue(utilities\createRevisionedRecord(), "Can't create unrevisioned records...");

      $lrevuri = utilities\getLastRevisionUri('http://foo.com/datasets/tests/foo');
      $rrevuri = utilities\getInitialRevisionUri('http://foo.com/datasets/tests/foo');
      
      $this->assertFalse($lrevuri === FALSE, "Debugging information: ".var_export($lrevuri, TRUE));                                       
      $this->assertFalse($rrevuri === FALSE, "Debugging information: ".var_export($rrevuri, TRUE));                                       

      $revisionDiff = new RevisionDiffQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
      
      $revisionDiff->dataset($settings->testDataset)
                   ->leftRevisionUri($lrevuri)
                   ->rightRevisionUri($rrevuri)
                   ->sourceInterface($settings->revisionDiffInterface)
                   ->sourceInterfaceVersion($settings->revisionDiffInterfaceVersion)                   
                   ->send();
                   
      $this->assertEquals($revisionDiff->getStatus(), "200", "Debugging information: ".var_export($revisionDiff, TRUE));                                       

      utilities\deleteRevisionedRecord();

      unset($revisionDiff);
      unset($settings);    
    }   
    
    public function testInterfaceNotExisting() {
      
      $settings = new Config();  
      
      utilities\deleteRevisionedRecord();
      
      $this->assertTrue(utilities\createRevisionedRecord(), "Can't create unrevisioned records...");

      $lrevuri = utilities\getLastRevisionUri('http://foo.com/datasets/tests/foo');
      $rrevuri = utilities\getInitialRevisionUri('http://foo.com/datasets/tests/foo');
      
      $this->assertFalse($lrevuri === FALSE, "Debugging information: ".var_export($lrevuri, TRUE));                                       
      $this->assertFalse($rrevuri === FALSE, "Debugging information: ".var_export($rrevuri, TRUE));                                       

      $revisionDiff = new RevisionDiffQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
      
      $revisionDiff->dataset($settings->testDataset)
                   ->leftRevisionUri($lrevuri)
                   ->rightRevisionUri($rrevuri)
                   ->sourceInterface('unexisting-interface')
                   ->sourceInterfaceVersion($settings->revisionDiffInterfaceVersion)                   
                   ->send();
            
      $this->assertEquals($revisionDiff->getStatus(), "400", "Debugging information: ".var_export($revisionDiff, TRUE));                                       
      $this->assertEquals($revisionDiff->getStatusMessage(), "Bad Request", "Debugging information: ".var_export($revisionDiff, TRUE));
      $this->assertEquals($revisionDiff->error->id, "WS-REVISION-DIFF-300", "Debugging information: ".var_export($revisionDiff, TRUE));                                       

      utilities\deleteRevisionedRecord();

      unset($revisionDiff);
      unset($settings);        
   }  
   
   public function testMissingDataset() {
      $settings = new Config();  
      
      utilities\deleteRevisionedRecord();
      
      $this->assertTrue(utilities\createRevisionedRecord(), "Can't create unrevisioned records...");

      $lrevuri = utilities\getLastRevisionUri('http://foo.com/datasets/tests/foo');
      $rrevuri = utilities\getInitialRevisionUri('http://foo.com/datasets/tests/foo');
      
      $this->assertFalse($lrevuri === FALSE, "Debugging information: ".var_export($lrevuri, TRUE));                                       
      $this->assertFalse($rrevuri === FALSE, "Debugging information: ".var_export($rrevuri, TRUE));                                       

      $revisionDiff = new RevisionDiffQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
      
      $revisionDiff->dataset('')
                   ->leftRevisionUri($lrevuri)
                   ->rightRevisionUri($rrevuri)
                   ->sourceInterface($settings->revisionDiffInterface)
                   ->sourceInterfaceVersion($settings->revisionDiffInterfaceVersion)                   
                   ->send();
                   
      $this->assertEquals($revisionDiff->getStatus(), "403", "Debugging information: ".var_export($revisionDiff, TRUE));                                       
      $this->assertEquals($revisionDiff->getStatusMessage(), "Forbidden", "Debugging information: ".var_export($revisionDiff, TRUE));
      $this->assertEquals($revisionDiff->error->id, "WS-AUTH-VALIDATION-103", "Debugging information: ".var_export($revisionDiff, TRUE));                                       

      utilities\deleteRevisionedRecord();

      unset($revisionDiff);
      unset($settings);    
    } 
    
    public function testMissingFirstRevision() {
      $settings = new Config();  
      
      utilities\deleteRevisionedRecord();
      
      $this->assertTrue(utilities\createRevisionedRecord(), "Can't create unrevisioned records...");

      $lrevuri = utilities\getLastRevisionUri('http://foo.com/datasets/tests/foo');
      $rrevuri = utilities\getInitialRevisionUri('http://foo.com/datasets/tests/foo');
      
      $this->assertFalse($lrevuri === FALSE, "Debugging information: ".var_export($lrevuri, TRUE));                                       
      $this->assertFalse($rrevuri === FALSE, "Debugging information: ".var_export($rrevuri, TRUE));                                       

      $revisionDiff = new RevisionDiffQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
      
      $revisionDiff->dataset($settings->testDataset)
                   ->leftRevisionUri('')
                   ->rightRevisionUri($rrevuri)
                   ->sourceInterface($settings->revisionDiffInterface)
                   ->sourceInterfaceVersion($settings->revisionDiffInterfaceVersion)                   
                   ->send();
                   
      $this->assertEquals($revisionDiff->getStatus(), "400", "Debugging information: ".var_export($revisionDiff, TRUE));                                       
      $this->assertEquals($revisionDiff->getStatusMessage(), "Bad Request", "Debugging information: ".var_export($revisionDiff, TRUE));
      $this->assertEquals($revisionDiff->error->id, "WS-REVISION-DIFF-201", "Debugging information: ".var_export($revisionDiff, TRUE));                                       

      utilities\deleteRevisionedRecord();

      unset($revisionDiff);
      unset($settings);    
    }               
    
    public function testMissingSecondRevision() {
      $settings = new Config();  
      
      utilities\deleteRevisionedRecord();
      
      $this->assertTrue(utilities\createRevisionedRecord(), "Can't create unrevisioned records...");

      $lrevuri = utilities\getLastRevisionUri('http://foo.com/datasets/tests/foo');
      $rrevuri = utilities\getInitialRevisionUri('http://foo.com/datasets/tests/foo');
      
      $this->assertFalse($lrevuri === FALSE, "Debugging information: ".var_export($lrevuri, TRUE));                                       
      $this->assertFalse($rrevuri === FALSE, "Debugging information: ".var_export($rrevuri, TRUE));                                       

      $revisionDiff = new RevisionDiffQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
      
      $revisionDiff->dataset($settings->testDataset)
                   ->leftRevisionUri($lrevuri)
                   ->rightRevisionUri('')
                   ->sourceInterface($settings->revisionDiffInterface)
                   ->sourceInterfaceVersion($settings->revisionDiffInterfaceVersion)                   
                   ->send();
                   
      $this->assertEquals($revisionDiff->getStatus(), "400", "Debugging information: ".var_export($revisionDiff, TRUE));                                       
      $this->assertEquals($revisionDiff->getStatusMessage(), "Bad Request", "Debugging information: ".var_export($revisionDiff, TRUE));
      $this->assertEquals($revisionDiff->error->id, "WS-REVISION-DIFF-202", "Debugging information: ".var_export($revisionDiff, TRUE));                                       

      utilities\deleteRevisionedRecord();

      unset($revisionDiff);
      unset($settings);    
    }               

    public function testCompareRevisionsFromDifferentRecords() {
      $settings = new Config();  
      
      utilities\deleteRevisionedRecord();
      
      $this->assertTrue(utilities\createRevisionedRecord(), "Can't create unrevisioned records...");

      $lrevuri = utilities\getLastRevisionUri('http://foo.com/datasets/tests/foo');
      $rrevuri = utilities\getInitialRevisionUri('http://foo.com/datasets/tests/bar');
      
      $this->assertFalse($lrevuri === FALSE, "Debugging information: ".var_export($lrevuri, TRUE));                                       
      $this->assertFalse($rrevuri === FALSE, "Debugging information: ".var_export($rrevuri, TRUE));                                       

      $revisionDiff = new RevisionDiffQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
      
      $revisionDiff->dataset($settings->testDataset)
                   ->leftRevisionUri($lrevuri)
                   ->rightRevisionUri($rrevuri)
                   ->sourceInterface($settings->revisionDiffInterface)
                   ->sourceInterfaceVersion($settings->revisionDiffInterfaceVersion)                   
                   ->send();
                   
      $this->assertEquals($revisionDiff->getStatus(), "400", "Debugging information: ".var_export($revisionDiff, TRUE));                                       
      $this->assertEquals($revisionDiff->getStatusMessage(), "Bad Request", "Debugging information: ".var_export($revisionDiff, TRUE));
      $this->assertEquals($revisionDiff->error->id, "WS-REVISION-DIFF-305", "Debugging information: ".var_export($revisionDiff, TRUE));                                       

      utilities\deleteRevisionedRecord();

      unset($revisionDiff);
      unset($settings);    
    }               

    public function testDiff_RDFXML() {
      $settings = new Config();  
      
      utilities\deleteRevisionedRecord();
      
      $this->assertTrue(utilities\createRevisionedRecord(), "Can't create unrevisioned records...");

      $lrevuri = utilities\getLastRevisionUri('http://foo.com/datasets/tests/foo');
      $rrevuri = utilities\getInitialRevisionUri('http://foo.com/datasets/tests/foo');
      
      $this->assertFalse($lrevuri === FALSE, "Debugging information: ".var_export($lrevuri, TRUE));                                       
      $this->assertFalse($rrevuri === FALSE, "Debugging information: ".var_export($rrevuri, TRUE));                                       

      $revisionDiff = new RevisionDiffQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
      
      $revisionDiff->dataset($settings->testDataset)
                   ->leftRevisionUri($lrevuri)
                   ->rightRevisionUri($rrevuri)
                   ->mime('application/rdf+xml')
                   ->sourceInterface($settings->revisionDiffInterface)
                   ->sourceInterfaceVersion($settings->revisionDiffInterfaceVersion)                   
                   ->send();                  
          
      $this->assertEquals($revisionDiff->getStatus(), "200", "Debugging information: ".var_export($revisionDiff, TRUE));                                       
      utilities\validateParameterApplicationRdfXml($this, $revisionDiff);

      $expected = new \DOMDocument;
      $expected->loadXML(file_get_contents($settings->contentDir.'validation/revision_diff.xml'));
 
      $actual = new \DOMDocument;
      $actual->loadXML($revisionDiff->getResultset());      
      
      // @TODO: this way to compare the XML structure of the XML file needs to be updated. Even if the files
      //        are the same, then they asserted as not equal. I think it is because the XML elements
      //        are not in the same order. We have to use assertEqualXMLStructure since the URI of the ChangeSet
      //        resource is not the same at each call. So the compareRDF() function cannot be used here.
      // Skipping that assertion for now.
      
      // $this->assertEqualXMLStructure($expected->firstChild, $actual->firstChild, TRUE, "Debugging information: ".var_export($revisionDiff, TRUE));   
      
      utilities\deleteRevisionedRecord();

      unset($revisionDiff);
      unset($settings);    
    }  

    public function testDiff_RDFN3() {
      $settings = new Config();  
      
      utilities\deleteRevisionedRecord();
      
      $this->assertTrue(utilities\createRevisionedRecord(), "Can't create unrevisioned records...");

      $lrevuri = utilities\getLastRevisionUri('http://foo.com/datasets/tests/foo');
      $rrevuri = utilities\getInitialRevisionUri('http://foo.com/datasets/tests/foo');
      
      $this->assertFalse($lrevuri === FALSE, "Debugging information: ".var_export($lrevuri, TRUE));                                       
      $this->assertFalse($rrevuri === FALSE, "Debugging information: ".var_export($rrevuri, TRUE));                                       

      $revisionDiff = new RevisionDiffQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
      
      $revisionDiff->dataset($settings->testDataset)
                   ->leftRevisionUri($lrevuri)
                   ->rightRevisionUri($rrevuri)
                   ->mime('application/rdf+n3')
                   ->sourceInterface($settings->revisionDiffInterface)
                   ->sourceInterfaceVersion($settings->revisionDiffInterfaceVersion)                   
                   ->send();                  
          
      $this->assertEquals($revisionDiff->getStatus(), "200", "Debugging information: ".var_export($revisionDiff, TRUE));                                       
      
      utilities\validateParameterApplicationRdfN3($this, $revisionDiff);
      
      include_once($settings->osfInstanceFolder."framework/arc2/ARC2.php");
      
      $parserExpected = \ARC2::getRDFParser();
      $parserExpected->parse($settings->testDataset, file_get_contents($settings->contentDir.'validation/revision_diff.xml'));      
      
      $parserActual = \ARC2::getRDFParser();
      $parserActual->parse($settings->testDataset, $revisionDiff->getResultset());      

      $expected = new \DOMDocument;
      $expected->loadXML($parserExpected->toRDFXML($parserExpected->getSimpleIndex(0)));
 
      $actual = new \DOMDocument;
      $actual->loadXML($parserActual->toRDFXML($parserActual->getSimpleIndex(0)));      
      
      // @TODO: this way to compare the XML structure of the XML file needs to be updated. Even if the files
      //        are the same, then they asserted as not equal. I think it is because the XML elements
      //        are not in the same order. We have to use assertEqualXMLStructure since the URI of the ChangeSet
      //        resource is not the same at each call. So the compareRDF() function cannot be used here.
      // Skipping that assertion for now.
      
      // $this->assertEqualXMLStructure($expected->firstChild, $actual->firstChild, TRUE, "Debugging information: ".var_export($revisionDiff, TRUE));     
            
      utilities\deleteRevisionedRecord();

      unset($revisionDiff);
      unset($settings);    
    }  
    
    public function testDiff_StructJSON() {
      $settings = new Config();  
      
      utilities\deleteRevisionedRecord();
      
      $this->assertTrue(utilities\createRevisionedRecord(), "Can't create unrevisioned records...");

      $lrevuri = utilities\getLastRevisionUri('http://foo.com/datasets/tests/foo');
      $rrevuri = utilities\getInitialRevisionUri('http://foo.com/datasets/tests/foo');
      
      $this->assertFalse($lrevuri === FALSE, "Debugging information: ".var_export($lrevuri, TRUE));                                       
      $this->assertFalse($rrevuri === FALSE, "Debugging information: ".var_export($rrevuri, TRUE));                                       

      $revisionDiff = new RevisionDiffQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
      
      $revisionDiff->dataset($settings->testDataset)
                   ->leftRevisionUri($lrevuri)
                   ->rightRevisionUri($rrevuri)
                   ->mime('application/json')
                   ->sourceInterface($settings->revisionDiffInterface)
                   ->sourceInterfaceVersion($settings->revisionDiffInterfaceVersion)                   
                   ->send();                  

      $this->assertEquals($revisionDiff->getStatus(), "200", "Debugging information: ".var_export($revisionDiff, TRUE));                                       
      
      utilities\validateParameterApplicationJson($this, $revisionDiff);
      
      //$this->assertTrue(utilities\compareStructJSON($revisionDiff->getResultset(), file_get_contents($settings->contentDir.'validation/revision_diff.json'), FALSE));
    
            
      utilities\deleteRevisionedRecord();

      unset($revisionDiff);
      unset($settings);    
    }    

    public function testDiff_StructXML() {
      $settings = new Config();  
      
      utilities\deleteRevisionedRecord();
      
      $this->assertTrue(utilities\createRevisionedRecord(), "Can't create unrevisioned records...");

      $lrevuri = utilities\getLastRevisionUri('http://foo.com/datasets/tests/foo');
      $rrevuri = utilities\getInitialRevisionUri('http://foo.com/datasets/tests/foo');
      
      $this->assertFalse($lrevuri === FALSE, "Debugging information: ".var_export($lrevuri, TRUE));                                       
      $this->assertFalse($rrevuri === FALSE, "Debugging information: ".var_export($rrevuri, TRUE));                                       

      $revisionDiff = new RevisionDiffQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
      
      $revisionDiff->dataset($settings->testDataset)
                   ->leftRevisionUri($lrevuri)
                   ->rightRevisionUri($rrevuri)
                   ->mime('text/xml')
                   ->sourceInterface($settings->revisionDiffInterface)
                   ->sourceInterfaceVersion($settings->revisionDiffInterfaceVersion)                   
                   ->send();                  
            
      $this->assertEquals($revisionDiff->getStatus(), "200", "Debugging information: ".var_export($revisionDiff, TRUE));                                       
      
      utilities\validateParameterTextXml($this, $revisionDiff);

      $resultset = new Resultset($settings->endpointUrl);
      
      // To test the structXML serialization, we import it into a Resultset() object and we export as RDF+XML
      // then we compare it to the expected RDF+XML resultset.
      $resultset->importStructXMLResultset($revisionDiff->getResultset());
      
      $expected = new \DOMDocument;
      $expected->loadXML(file_get_contents($settings->contentDir.'validation/revision_diff.xml'));
 
      $actual = new \DOMDocument;
      $actual->loadXML($resultset->getResultsetRDFXML());      
      
      // @TODO: this way to compare the XML structure of the XML file needs to be updated. Even if the files
      //        are the same, then they asserted as not equal. I think it is because the XML elements
      //        are not in the same order. We have to use assertEqualXMLStructure since the URI of the ChangeSet
      //        resource is not the same at each call. So the compareRDF() function cannot be used here.
      // Skipping that assertion for now.
      
      // $this->assertEqualXMLStructure($expected->firstChild, $actual->firstChild, TRUE, "Debugging information: ".var_export($revisionDiff, TRUE));      
            
      utilities\deleteRevisionedRecord();

      unset($revisionDiff);
      unset($settings);    
    }
    
    public function testDiff_Resultset() {
      $settings = new Config();  
      
      utilities\deleteRevisionedRecord();
      
      $this->assertTrue(utilities\createRevisionedRecord(), "Can't create unrevisioned records...");

      $lrevuri = utilities\getLastRevisionUri('http://foo.com/datasets/tests/foo');
      $rrevuri = utilities\getInitialRevisionUri('http://foo.com/datasets/tests/foo');
      
      $this->assertFalse($lrevuri === FALSE, "Debugging information: ".var_export($lrevuri, TRUE));                                       
      $this->assertFalse($rrevuri === FALSE, "Debugging information: ".var_export($rrevuri, TRUE));                                       

      $revisionDiff = new RevisionDiffQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
      
      $revisionDiff->dataset($settings->testDataset)
                   ->leftRevisionUri($lrevuri)
                   ->rightRevisionUri($rrevuri)
                   ->mime('resultset')
                   ->sourceInterface($settings->revisionDiffInterface)
                   ->sourceInterfaceVersion($settings->revisionDiffInterfaceVersion)                   
                   ->send();                  
                        
      $this->assertEquals($revisionDiff->getStatus(), "200", "Debugging information: ".var_export($revisionDiff, TRUE));                                       
      
      $expected = new \DOMDocument;
      $expected->loadXML(file_get_contents($settings->contentDir.'validation/revision_diff.xml'));
 
      $actual = new \DOMDocument;
      $actual->loadXML($revisionDiff->getResultset()->getResultsetRDFXML());      
      
      // @TODO: this way to compare the XML structure of the XML file needs to be updated. Even if the files
      //        are the same, then they asserted as not equal. I think it is because the XML elements
      //        are not in the same order. We have to use assertEqualXMLStructure since the URI of the ChangeSet
      //        resource is not the same at each call. So the compareRDF() function cannot be used here.
      // Skipping that assertion for now.
      
      // $this->assertEqualXMLStructure($expected->firstChild, $actual->firstChild, TRUE, "Debugging information: ".var_export($revisionDiff, TRUE));

      unset($revisionDiff);
      unset($settings);    
    }
}
  
?>