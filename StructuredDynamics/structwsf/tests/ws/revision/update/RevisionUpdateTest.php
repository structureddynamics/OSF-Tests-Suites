<?php

  namespace StructuredDynamics\structwsf\tests\ws\revision\update;
  
  use StructuredDynamics\structwsf\framework\WebServiceQuerier;
  use StructuredDynamics\structwsf\php\api\ws\revision\lister\RevisionListerQuery;
  use StructuredDynamics\structwsf\php\api\ws\revision\read\RevisionReadQuery;
  use StructuredDynamics\structwsf\php\api\ws\revision\update\RevisionUpdateQuery;
  use StructuredDynamics\structwsf\framework\Resultset;
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
  
  ini_set("memory_limit","256M");
  set_time_limit(3600);

  $settings = new Config(); 
  
  class RevisionUpdateTest extends \PHPUnit_Framework_TestCase {
    
    static private $outputs = array();

    public function testWrongEndpointUrl() {
      
      $settings = new Config();          
      
      $wsq = new WebServiceQuerier($settings->endpointUrl . "revision/update/" . "wrong", 
                                   "get", 
                                   "text/xml",
                                   "&revuri=" . urlencode('') .
                                   "&dataset=" . urlencode($settings->testDataset) .
                                   "&lifecycle=" . urlencode('published') .
                                   "&interface=". urlencode($settings->revisionUpdateInterface) .
                                   "&version=". urlencode($settings->revisionUpdateInterfaceVersion) .
                                   "&registered_ip=" . urlencode("Self"));        
                         
      $this->assertEquals($wsq->getStatus(), "404", "Debugging information: ".var_export($wsq, TRUE));                                       
      $this->assertEquals($wsq->getStatusMessage(), "Not Found", "Debugging information: ".var_export($wsq, TRUE));
      
      unset($wsq);
      unset($settings);
    }

    public function testWrongEndpointMethodPost() {
      
      $settings = new Config();  
      
      $wsq = new WebServiceQuerier($settings->endpointUrl . "revision/update/", 
                                   "post", 
                                   "text/xml",
                                   "&revuri=" . urlencode('') .
                                   "&dataset=" . urlencode($settings->testDataset) .
                                   "&lifecycle=" . urlencode('published') .
                                   "&interface=". urlencode($settings->revisionUpdateInterface) .
                                   "&version=". urlencode($settings->revisionUpdateInterfaceVersion) .
                                   "&registered_ip=" . urlencode("Self"));        
                                   
      $this->assertEquals($wsq->getStatus(), "405", "Debugging information: ".var_export($wsq, TRUE));                                       
      $this->assertEquals($wsq->getStatusMessage(), "Method Not Allowed", "Debugging information: ".var_export($wsq, TRUE));          
      
      unset($wsq);
      unset($settings);
    }    
    
    public function testValidInterfaceVersion() {
      
      $settings = new Config();  
      
      utilities\deleteRevisionedRecord();
      
      $this->assertTrue(utilities\createRevisionedRecord(), "Can't create revisioned records...");

      $revuri = utilities\getLastRevisionUri('http://foo.com/datasets/tests/foo');
      
      $this->assertFalse($revuri === FALSE, "Debugging information: ".var_export($revuri, TRUE));                                       

      $revisionUpdate = new RevisionUpdateQuery($settings->endpointUrl);
      
      $revisionUpdate->revisionUri($revuri)
                     ->dataset($settings->testDataset)
                     ->isExperimental()
                     ->sourceInterface($settings->revisionReadInterface)
                     ->sourceInterfaceVersion($settings->revisionReadInterfaceVersion)                   
                     ->send();
                           
      $this->assertEquals($revisionUpdate->getStatus(), "200", "Debugging information: ".var_export($revisionUpdate, TRUE)); 
          
      utilities\deleteRevisionedRecord();

      unset($revisionUpdate);
      unset($settings);    
    }
    
    public function testInvalidInterfaceVersion() {
      
      $settings = new Config();  
      
      utilities\deleteRevisionedRecord();
      
      $this->assertTrue(utilities\createRevisionedRecord(), "Can't create revisioned records...");

      $revuri = utilities\getLastRevisionUri('http://foo.com/datasets/tests/foo');
      
      $this->assertFalse($revuri === FALSE, "Debugging information: ".var_export($revuri, TRUE));                                       

      $revisionUpdate = new RevisionUpdateQuery($settings->endpointUrl);
      
      $revisionUpdate->revisionUri($revuri)
                     ->dataset($settings->testDataset)
                     ->isExperimental()
                     ->sourceInterface($settings->revisionReadInterface)
                     ->sourceInterfaceVersion('667.6')                   
                     ->send();
                           
      $this->assertEquals($revisionUpdate->getStatus(), "400", "Debugging information: ".var_export($revisionUpdate, TRUE));                                       
      $this->assertEquals($revisionUpdate->getStatusMessage(), "Bad Request", "Debugging information: ".var_export($revisionUpdate, TRUE));
      $this->assertEquals($revisionUpdate->error->id, "WS-REVISION-UPDATE-302", "Debugging information: ".var_export($revisionUpdate, TRUE));                                       
          
      utilities\deleteRevisionedRecord();

      unset($revisionUpdate);
      unset($settings);                 
    }  
    
    public function testInterfaceNotExisting() {
      $settings = new Config();  
      
      utilities\deleteRevisionedRecord();
      
      $this->assertTrue(utilities\createRevisionedRecord(), "Can't create revisioned records...");

      $revuri = utilities\getLastRevisionUri('http://foo.com/datasets/tests/foo');
      
      $this->assertFalse($revuri === FALSE, "Debugging information: ".var_export($revuri, TRUE));                                       

      $revisionUpdate = new RevisionUpdateQuery($settings->endpointUrl);
      
      $revisionUpdate->revisionUri($revuri)
                     ->dataset($settings->testDataset)
                     ->isExperimental()
                     ->sourceInterface('unexisting interface')
                     ->sourceInterfaceVersion($settings->revisionReadInterfaceVersion)                   
                     ->send();
                           
      $this->assertEquals($revisionUpdate->getStatus(), "400", "Debugging information: ".var_export($revisionUpdate, TRUE));                                       
      $this->assertEquals($revisionUpdate->getStatusMessage(), "Bad Request", "Debugging information: ".var_export($revisionUpdate, TRUE));
      $this->assertEquals($revisionUpdate->error->id, "WS-REVISION-UPDATE-300", "Debugging information: ".var_export($revisionUpdate, TRUE));                                       
          
      utilities\deleteRevisionedRecord();

      unset($revisionUpdate);
      unset($settings);  
    }   
    
    public function testMissingRevisionUri() {
      
      $settings = new Config();  
      
      utilities\deleteRevisionedRecord();
      
      $this->assertTrue(utilities\createRevisionedRecord(), "Can't create revisioned records...");

      $revuri = utilities\getLastRevisionUri('http://foo.com/datasets/tests/foo');
      
      $this->assertFalse($revuri === FALSE, "Debugging information: ".var_export($revuri, TRUE));                                       

      $revisionUpdate = new RevisionUpdateQuery($settings->endpointUrl);
      
      $revisionUpdate->revisionUri('')
                     ->dataset($settings->testDataset)
                     ->isExperimental()
                     ->sourceInterface($settings->revisionReadInterface)
                     ->sourceInterfaceVersion($settings->revisionReadInterfaceVersion)                   
                     ->send();
                           
      $this->assertEquals($revisionUpdate->getStatus(), "400", "Debugging information: ".var_export($revisionUpdate, TRUE));                                       
      $this->assertEquals($revisionUpdate->getStatusMessage(), "Bad Request", "Debugging information: ".var_export($revisionUpdate, TRUE));
      $this->assertEquals($revisionUpdate->error->id, "WS-REVISION-UPDATE-201", "Debugging information: ".var_export($revisionUpdate, TRUE));                                       
          
      utilities\deleteRevisionedRecord();

      unset($revisionUpdate);
      unset($settings);    
    }  
    
    public function testMissingDatasetUri() {
      
      $settings = new Config();  
      
      utilities\deleteRevisionedRecord();
      
      $this->assertTrue(utilities\createRevisionedRecord(), "Can't create revisioned records...");

      $revuri = utilities\getLastRevisionUri('http://foo.com/datasets/tests/foo');
      
      $this->assertFalse($revuri === FALSE, "Debugging information: ".var_export($revuri, TRUE));                                       

      $revisionUpdate = new RevisionUpdateQuery($settings->endpointUrl);
      
      $revisionUpdate->revisionUri($revuri)
                     ->dataset('')
                     ->isExperimental()
                     ->sourceInterface($settings->revisionReadInterface)
                     ->sourceInterfaceVersion($settings->revisionReadInterfaceVersion)                   
                     ->send();
                           
      $this->assertEquals($revisionUpdate->getStatus(), "400", "Debugging information: ".var_export($revisionUpdate, TRUE));                                       
      $this->assertEquals($revisionUpdate->getStatusMessage(), "Bad Request", "Debugging information: ".var_export($revisionUpdate, TRUE));
      $this->assertEquals($revisionUpdate->error->id, "WS-REVISION-UPDATE-200", "Debugging information: ".var_export($revisionUpdate, TRUE));                                       
          
      utilities\deleteRevisionedRecord();

      unset($revisionUpdate);
      unset($settings);    
    }
     
    public function testUnknownLife() {
      
      $settings = new Config();  
      
      utilities\deleteRevisionedRecord();
      
      $this->assertTrue(utilities\createRevisionedRecord(), "Can't create revisioned records...");

      $revuri = utilities\getLastRevisionUri('http://foo.com/datasets/tests/foo');
      
      $this->assertFalse($revuri === FALSE, "Debugging information: ".var_export($revuri, TRUE));                                       

      $revisionUpdate = new RevisionUpdateQuery($settings->endpointUrl);
      
      $wsq = new WebServiceQuerier($settings->endpointUrl . "revision/update/", 
                                   "get", 
                                   "text/xml",
                                   "&revuri=" . urlencode($revuri) .
                                   "&dataset=" . urlencode($settings->testDataset) .
                                   "&lifecycle=" . urlencode('unknown-stage') .
                                   "&interface=". urlencode($settings->revisionUpdateInterface) .
                                   "&version=". urlencode($settings->revisionUpdateInterfaceVersion) .
                                   "&registered_ip=" . urlencode("Self"));       
                           
      $this->assertEquals($wsq->getStatus(), "400", "Debugging information: ".var_export($wsq, TRUE));                                       
      $this->assertEquals($wsq->getStatusMessage(), "Bad Request", "Debugging information: ".var_export($wsq, TRUE));
      $this->assertEquals($wsq->error->id, "WS-REVISION-UPDATE-303", "Debugging information: ".var_export($wsq, TRUE));                                       
          
      utilities\deleteRevisionedRecord();

      unset($wsq);
      unset($settings);    
    }
 
    public function test_Update_Stage_Experimental() {
      
      $settings = new Config();  
      
      utilities\deleteRevisionedRecord();
      
      $this->assertTrue(utilities\createRevisionedRecord(), "Can't create revisioned records...");

      $revuri = utilities\getLastRevisionUri('http://foo.com/datasets/tests/foo');
      
      $this->assertFalse($revuri === FALSE, "Debugging information: ".var_export($revuri, TRUE));                                       

      $revisionUpdate = new RevisionUpdateQuery($settings->endpointUrl);
      
      $revisionUpdate->revisionUri($revuri)
                     ->dataset($settings->testDataset)
                     ->isExperimental()
                     ->sourceInterface($settings->revisionReadInterface)
                     ->sourceInterfaceVersion($settings->revisionReadInterfaceVersion)                   
                     ->send();
                           
      $this->assertEquals($revisionUpdate->getStatus(), "200", "Debugging information: ".var_export($revisionUpdate, TRUE)); 
      
      $revisionRead = new RevisionReadQuery($settings->endpointUrl);
      
      $revisionRead->revisionUri($revuri)
                   ->dataset($settings->testDataset)
                   ->mime('resultset')
                   ->getRevision()
                   ->sourceInterface($settings->revisionReadInterface)
                   ->sourceInterfaceVersion($settings->revisionReadInterfaceVersion)                   
                   ->send();

      $resultset = $revisionRead->getResultset()->getResultset();
      
      $this->assertTrue($resultset[$settings->testDataset][$revuri]['http://purl.org/ontology/wsf#revisionStatus'][0]['uri'] == 'http://purl.org/ontology/wsf#experimental', "The updated revision doesn't have the status 'wsf:experimental'");                      

      utilities\deleteRevisionedRecord();

      unset($revisionUpdate);
      unset($settings);    
    } 
 
    public function test_Update_Stage_Archive() {
      
      $settings = new Config();  
      
      utilities\deleteRevisionedRecord();
      
      $this->assertTrue(utilities\createRevisionedRecord(), "Can't create revisioned records...");

      $revuri = utilities\getLastRevisionUri('http://foo.com/datasets/tests/foo');
      
      $this->assertFalse($revuri === FALSE, "Debugging information: ".var_export($revuri, TRUE));                                       

      $revisionUpdate = new RevisionUpdateQuery($settings->endpointUrl);
      
      $revisionUpdate->revisionUri($revuri)
                     ->dataset($settings->testDataset)
                     ->isArchive()
                     ->sourceInterface($settings->revisionReadInterface)
                     ->sourceInterfaceVersion($settings->revisionReadInterfaceVersion)                   
                     ->send();
                           
      $this->assertEquals($revisionUpdate->getStatus(), "200", "Debugging information: ".var_export($revisionUpdate, TRUE)); 
      
      $revisionRead = new RevisionReadQuery($settings->endpointUrl);
      
      $revisionRead->revisionUri($revuri)
                   ->dataset($settings->testDataset)
                   ->mime('resultset')
                   ->getRevision()
                   ->sourceInterface($settings->revisionReadInterface)
                   ->sourceInterfaceVersion($settings->revisionReadInterfaceVersion)                   
                   ->send();

      $resultset = $revisionRead->getResultset()->getResultset();
      
      $this->assertTrue($resultset[$settings->testDataset][$revuri]['http://purl.org/ontology/wsf#revisionStatus'][0]['uri'] == 'http://purl.org/ontology/wsf#archive', "The updated revision doesn't have the status 'wsf:archive'");

      utilities\deleteRevisionedRecord();

      unset($revisionUpdate);
      unset($settings);    
    }    
 
    public function test_Update_Stage_Harvesting() {
      
      $settings = new Config();  
      
      utilities\deleteRevisionedRecord();
      
      $this->assertTrue(utilities\createRevisionedRecord(), "Can't create revisioned records...");

      $revuri = utilities\getLastRevisionUri('http://foo.com/datasets/tests/foo');
      
      $this->assertFalse($revuri === FALSE, "Debugging information: ".var_export($revuri, TRUE));                                       

      $revisionUpdate = new RevisionUpdateQuery($settings->endpointUrl);
      
      $revisionUpdate->revisionUri($revuri)
                     ->dataset($settings->testDataset)
                     ->isHarvesting()
                     ->sourceInterface($settings->revisionReadInterface)
                     ->sourceInterfaceVersion($settings->revisionReadInterfaceVersion)                   
                     ->send();
                           
      $this->assertEquals($revisionUpdate->getStatus(), "200", "Debugging information: ".var_export($revisionUpdate, TRUE)); 
      
      $revisionRead = new RevisionReadQuery($settings->endpointUrl);
      
      $revisionRead->revisionUri($revuri)
                   ->dataset($settings->testDataset)
                   ->mime('resultset')
                   ->getRevision()
                   ->sourceInterface($settings->revisionReadInterface)
                   ->sourceInterfaceVersion($settings->revisionReadInterfaceVersion)                   
                   ->send();

      $resultset = $revisionRead->getResultset()->getResultset();
      
      $this->assertTrue($resultset[$settings->testDataset][$revuri]['http://purl.org/ontology/wsf#revisionStatus'][0]['uri'] == 'http://purl.org/ontology/wsf#harvesting', "The updated revision doesn't have the status 'wsf:harvesting'");

      utilities\deleteRevisionedRecord();

      unset($revisionUpdate);
      unset($settings);    
    }   

    public function test_Update_Stage_PreRelease() {
      
      $settings = new Config();  
      
      utilities\deleteRevisionedRecord();
      
      $this->assertTrue(utilities\createRevisionedRecord(), "Can't create revisioned records...");

      $revuri = utilities\getLastRevisionUri('http://foo.com/datasets/tests/foo');
      
      $this->assertFalse($revuri === FALSE, "Debugging information: ".var_export($revuri, TRUE));                                       

      $revisionUpdate = new RevisionUpdateQuery($settings->endpointUrl);
      
      $revisionUpdate->revisionUri($revuri)
                     ->dataset($settings->testDataset)
                     ->isPreRelease()
                     ->sourceInterface($settings->revisionReadInterface)
                     ->sourceInterfaceVersion($settings->revisionReadInterfaceVersion)                   
                     ->send();
                           
      $this->assertEquals($revisionUpdate->getStatus(), "200", "Debugging information: ".var_export($revisionUpdate, TRUE)); 
      
      $revisionRead = new RevisionReadQuery($settings->endpointUrl);
      
      $revisionRead->revisionUri($revuri)
                   ->dataset($settings->testDataset)
                   ->mime('resultset')
                   ->getRevision()
                   ->sourceInterface($settings->revisionReadInterface)
                   ->sourceInterfaceVersion($settings->revisionReadInterfaceVersion)                   
                   ->send();

      $resultset = $revisionRead->getResultset()->getResultset();
      
      $this->assertTrue($resultset[$settings->testDataset][$revuri]['http://purl.org/ontology/wsf#revisionStatus'][0]['uri'] == 'http://purl.org/ontology/wsf#pre_release', "The updated revision doesn't have the status 'wsf:pre_release'");

      utilities\deleteRevisionedRecord();

      unset($revisionUpdate);
      unset($settings);    
    }   
 
    public function test_Update_Stage_Published() {
      
      $settings = new Config();  
      
      utilities\deleteRevisionedRecord();
      
      $this->assertTrue(utilities\createRevisionedRecord(), "Can't create revisioned records...");

      $revuri = utilities\getLastRevisionUri('http://foo.com/datasets/tests/foo');
      
      $this->assertFalse($revuri === FALSE, "Debugging information: ".var_export($revuri, TRUE));                                       

      $revisionUpdate = new RevisionUpdateQuery($settings->endpointUrl);
      
      $revisionUpdate->revisionUri($revuri)
                     ->dataset($settings->testDataset)
                     ->isPublished()
                     ->sourceInterface($settings->revisionReadInterface)
                     ->sourceInterfaceVersion($settings->revisionReadInterfaceVersion)                   
                     ->send();
                           
      $this->assertEquals($revisionUpdate->getStatus(), "200", "Debugging information: ".var_export($revisionUpdate, TRUE)); 
      
      $revisionRead = new RevisionReadQuery($settings->endpointUrl);
      
      $revisionRead->revisionUri($revuri)
                   ->dataset($settings->testDataset)
                   ->mime('resultset')
                   ->getRevision()
                   ->sourceInterface($settings->revisionReadInterface)
                   ->sourceInterfaceVersion($settings->revisionReadInterfaceVersion)                   
                   ->send();

      $resultset = $revisionRead->getResultset()->getResultset();
      
      $this->assertTrue($resultset[$settings->testDataset][$revuri]['http://purl.org/ontology/wsf#revisionStatus'][0]['uri'] == 'http://purl.org/ontology/wsf#published', "The updated revision doesn't have the status 'wsf:published'");

      utilities\deleteRevisionedRecord();

      unset($revisionUpdate);
      unset($settings);    
    }   
 
    public function test_Update_Stage_Staging() {
      
      $settings = new Config();  
      
      utilities\deleteRevisionedRecord();
      
      $this->assertTrue(utilities\createRevisionedRecord(), "Can't create revisioned records...");

      $revuri = utilities\getLastRevisionUri('http://foo.com/datasets/tests/foo');
      
      $this->assertFalse($revuri === FALSE, "Debugging information: ".var_export($revuri, TRUE));                                       

      $revisionUpdate = new RevisionUpdateQuery($settings->endpointUrl);
      
      $revisionUpdate->revisionUri($revuri)
                     ->dataset($settings->testDataset)
                     ->isStaging()
                     ->sourceInterface($settings->revisionReadInterface)
                     ->sourceInterfaceVersion($settings->revisionReadInterfaceVersion)                   
                     ->send();
                           
      $this->assertEquals($revisionUpdate->getStatus(), "200", "Debugging information: ".var_export($revisionUpdate, TRUE)); 
      
      $revisionRead = new RevisionReadQuery($settings->endpointUrl);
      
      $revisionRead->revisionUri($revuri)
                   ->dataset($settings->testDataset)
                   ->mime('resultset')
                   ->getRevision()
                   ->sourceInterface($settings->revisionReadInterface)
                   ->sourceInterfaceVersion($settings->revisionReadInterfaceVersion)                   
                   ->send();

      $resultset = $revisionRead->getResultset()->getResultset();
      
      $this->assertTrue($resultset[$settings->testDataset][$revuri]['http://purl.org/ontology/wsf#revisionStatus'][0]['uri'] == 'http://purl.org/ontology/wsf#staging', "The updated revision doesn't have the status 'wsf:staging'");

      utilities\deleteRevisionedRecord();

      unset($revisionUpdate);
      unset($settings);    
    }   

    public function test_Update_Stage_Unspecified() {
      
      $settings = new Config();  
      
      utilities\deleteRevisionedRecord();
      
      $this->assertTrue(utilities\createRevisionedRecord(), "Can't create revisioned records...");

      $revuri = utilities\getLastRevisionUri('http://foo.com/datasets/tests/foo');
      
      $this->assertFalse($revuri === FALSE, "Debugging information: ".var_export($revuri, TRUE));                                       

      $revisionUpdate = new RevisionUpdateQuery($settings->endpointUrl);
      
      $revisionUpdate->revisionUri($revuri)
                     ->dataset($settings->testDataset)
                     ->isUnspecified()
                     ->sourceInterface($settings->revisionReadInterface)
                     ->sourceInterfaceVersion($settings->revisionReadInterfaceVersion)                   
                     ->send();
                           
      $this->assertEquals($revisionUpdate->getStatus(), "200", "Debugging information: ".var_export($revisionUpdate, TRUE)); 
      
      $revisionRead = new RevisionReadQuery($settings->endpointUrl);
      
      $revisionRead->revisionUri($revuri)
                   ->dataset($settings->testDataset)
                   ->mime('resultset')
                   ->getRevision()
                   ->sourceInterface($settings->revisionReadInterface)
                   ->sourceInterfaceVersion($settings->revisionReadInterfaceVersion)                   
                   ->send();

      $resultset = $revisionRead->getResultset()->getResultset();
      
      $this->assertTrue($resultset[$settings->testDataset][$revuri]['http://purl.org/ontology/wsf#revisionStatus'][0]['uri'] == 'http://purl.org/ontology/wsf#unspecified', "The updated revision doesn't have the status 'wsf:unspecified'");

      utilities\deleteRevisionedRecord();

      unset($revisionUpdate);
      unset($settings);    
    }   
  }
  
?>