<?php

  namespace StructuredDynamics\structwsf\tests\ws\revision\delete;
  
  use StructuredDynamics\structwsf\framework\WebServiceQuerier;
  use StructuredDynamics\structwsf\php\api\ws\revision\lister\RevisionListerQuery;
  use StructuredDynamics\structwsf\php\api\ws\revision\delete\RevisionDeleteQuery;
  use \StructuredDynamics\structwsf\php\api\ws\revision\update\RevisionUpdateQuery;
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
  
  class RevisionDeleteTest extends \PHPUnit_Framework_TestCase {
    
    static private $outputs = array();

    public function testWrongEndpointUrl() {
      
      $settings = new Config();          
      
      $wsq = new WebServiceQuerier($settings->endpointUrl . "revision/delete/" . "wrong", 
                                   "get", 
                                   "text/xml",
                                   "&revuri=" . urlencode('') .
                                   "&dataset=" . urlencode($settings->testDataset) .
                                   "&interface=". urlencode($settings->revisionDeleteInterface) .
                                   "&version=". urlencode($settings->revisionDeleteInterfaceVersion) .
                                   "&registered_ip=" . urlencode("Self"));        
                         
      $this->assertEquals($wsq->getStatus(), "404", "Debugging information: ".var_export($wsq, TRUE));                                       
      $this->assertEquals($wsq->getStatusMessage(), "Not Found", "Debugging information: ".var_export($wsq, TRUE));
      
      unset($wsq);
      unset($settings);
    }
    
    public function testWrongEndpointMethodPost() {
      
      $settings = new Config();  
      
      $wsq = new WebServiceQuerier($settings->endpointUrl . "revision/delete/", 
                                   "post", 
                                   "text/xml",
                                   "&revuri=" . urlencode('') .
                                   "&dataset=" . urlencode($settings->testDataset) .
                                   "&interface=". urlencode($settings->revisionDeleteInterface) .
                                   "&version=". urlencode($settings->revisionDeleteInterfaceVersion) .
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
      
      // unpublish the revision
      $revisionUpdate = new RevisionUpdateQuery($settings->endpointUrl);
      
      $revisionUpdate->dataset($settings->testDataset)
                     ->isArchive()
                     ->revisionUri($revuri)
                     ->send();

      $this->assertEquals($revisionUpdate->getStatus(), "200", "Debugging information: ".var_export($revisionUpdate, TRUE));                                       
      
      $revisionDelete = new RevisionDeleteQuery($settings->endpointUrl);
      
      $revisionDelete->dataset($settings->testDataset)
                     ->revisionUri($revuri)
                     ->sourceInterface($settings->revisionDeleteInterface)
                     ->sourceInterfaceVersion($settings->revisionDeleteInterfaceVersion)
                     ->send();
                           
      $this->assertEquals($revisionDelete->getStatus(), "200", "Debugging information: ".var_export($revisionDelete, TRUE));                                       

      utilities\deleteRevisionedRecord();

      unset($revisionDelete);
      unset($settings);    
    }
    
    public function testInvalidInterfaceVersion() {
      
      $settings = new Config();  
      
      utilities\deleteRevisionedRecord();
      
      $this->assertTrue(utilities\createRevisionedRecord(), "Can't create unrevisioned records...");

      $revuri = utilities\getLastRevisionUri('http://foo.com/datasets/tests/foo');
      
      $this->assertFalse($revuri === FALSE, "Debugging information: ".var_export($revuri, TRUE));                                       
      
      // unpublish the revision
      $revisionUpdate = new RevisionUpdateQuery($settings->endpointUrl);
      
      $revisionUpdate->dataset($settings->testDataset)
                     ->isArchive()
                     ->revisionUri($revuri)
                     ->send();

      $this->assertEquals($revisionUpdate->getStatus(), "200", "Debugging information: ".var_export($revisionUpdate, TRUE));                                       
      
      $revisionDelete = new RevisionDeleteQuery($settings->endpointUrl);
      
      $revisionDelete->dataset($settings->testDataset)
                     ->revisionUri($revuri)
                     ->sourceInterface($settings->revisionDeleteInterface)
                     ->sourceInterfaceVersion("667.6")
                     ->send();
                           
      $this->assertEquals($revisionDelete->getStatus(), "400", "Debugging information: ".var_export($revisionDelete, TRUE));                                       
      $this->assertEquals($revisionDelete->getStatusMessage(), "Bad Request", "Debugging information: ".var_export($revisionDelete, TRUE));
      $this->assertEquals($revisionDelete->error->id, "WS-REVISION-DELETE-302", "Debugging information: ".var_export($revisionDelete, TRUE));                                       

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
      
      // unpublish the revision
      $revisionUpdate = new RevisionUpdateQuery($settings->endpointUrl);
      
      $revisionUpdate->dataset($settings->testDataset)
                     ->isArchive()
                     ->revisionUri($revuri)
                     ->send();

      $this->assertEquals($revisionUpdate->getStatus(), "200", "Debugging information: ".var_export($revisionUpdate, TRUE));                                       
      
      $revisionDelete = new RevisionDeleteQuery($settings->endpointUrl);
      
      $revisionDelete->dataset($settings->testDataset)
                     ->revisionUri($revuri)
                     ->sourceInterface($settings->revisionDeleteInterface)
                     ->sourceInterfaceVersion($settings->revisionDeleteInterfaceVersion)
                     ->send();
                           
      $this->assertEquals($revisionDelete->getStatus(), "200", "Debugging information: ".var_export($revisionDelete, TRUE));                                       

      utilities\deleteRevisionedRecord();

      unset($revisionDelete);
      unset($settings);   
    }   
    
    public function testInterfaceNotExisting() {
          
      $settings = new Config();  
      
      utilities\deleteRevisionedRecord();
      
      $this->assertTrue(utilities\createRevisionedRecord(), "Can't create unrevisioned records...");

      $revuri = utilities\getLastRevisionUri('http://foo.com/datasets/tests/foo');
      
      $this->assertFalse($revuri === FALSE, "Debugging information: ".var_export($revuri, TRUE));                                       
      
      // unpublish the revision
      $revisionUpdate = new RevisionUpdateQuery($settings->endpointUrl);
      
      $revisionUpdate->dataset($settings->testDataset)
                     ->isArchive()
                     ->revisionUri($revuri)
                     ->send();

      $this->assertEquals($revisionUpdate->getStatus(), "200", "Debugging information: ".var_export($revisionUpdate, TRUE));                                       
      
      $revisionDelete = new RevisionDeleteQuery($settings->endpointUrl);
      
      $revisionDelete->dataset($settings->testDataset)
                     ->revisionUri($revuri)
                     ->sourceInterface('interface-doesnt-exist')
                     ->sourceInterfaceVersion($settings->revisionDeleteInterfaceVersion)
                     ->send();
                           
      $this->assertEquals($revisionDelete->getStatus(), "400", "Debugging information: ".var_export($revisionDelete, TRUE));                                       
      $this->assertEquals($revisionDelete->getStatusMessage(), "Bad Request", "Debugging information: ".var_export($revisionDelete, TRUE));
      $this->assertEquals($revisionDelete->error->id, "WS-REVISION-DELETE-300", "Debugging information: ".var_export($revisionDelete, TRUE));                                       

      utilities\deleteRevisionedRecord();

      unset($revisionDelete);
      unset($settings);       
   }      
    
   public function testDeletePublishedRevision() {
      
      $settings = new Config();  
      
      utilities\deleteRevisionedRecord();
      
      $this->assertTrue(utilities\createRevisionedRecord(), "Can't create unrevisioned records...");

      $revuri = utilities\getLastRevisionUri('http://foo.com/datasets/tests/foo');
      
      $this->assertFalse($revuri === FALSE, "Debugging information: ".var_export($revuri, TRUE));                                       
      
      $revisionDelete = new RevisionDeleteQuery($settings->endpointUrl);
      
      $revisionDelete->dataset($settings->testDataset)
                     ->revisionUri($revuri)
                     ->sourceInterface($settings->revisionDeleteInterface)
                     ->sourceInterfaceVersion($settings->revisionDeleteInterfaceVersion)
                     ->send();
                           
      $this->assertEquals($revisionDelete->getStatus(), "400", "Debugging information: ".var_export($revisionDelete, TRUE));                                       
      $this->assertEquals($revisionDelete->getStatusMessage(), "Bad Request", "Debugging information: ".var_export($revisionDelete, TRUE));
      $this->assertEquals($revisionDelete->error->id, "WS-REVISION-DELETE-304", "Debugging information: ".var_export($revisionDelete, TRUE));                                       

      utilities\deleteRevisionedRecord();

      unset($revisionDelete);
      unset($settings);    
    }    

    public function testDeleteArchivedRevision() {
      
     $settings = new Config();  
      
      utilities\deleteRevisionedRecord();
      
      $this->assertTrue(utilities\createRevisionedRecord(), "Can't create unrevisioned records...");

      $revuri = utilities\getLastRevisionUri('http://foo.com/datasets/tests/foo');

      $this->assertFalse($revuri === FALSE, "Debugging information: ".var_export($revuri, TRUE));                                       

      
      // unpublish the revision           
      $revisionUpdate = new RevisionUpdateQuery($settings->endpointUrl);
      
      $revisionUpdate->dataset($settings->testDataset)
                     ->isArchive()
                     ->revisionUri($revuri)
                     ->send();

      $this->assertEquals($revisionUpdate->getStatus(), "200", "Debugging information: ".var_export($revisionUpdate, TRUE));                                       
      
      $revisionDelete = new RevisionDeleteQuery($settings->endpointUrl);
      
      $revisionDelete->dataset($settings->testDataset)
                     ->revisionUri($revuri)
                     ->sourceInterface($settings->revisionDeleteInterface)
                     ->sourceInterfaceVersion($settings->revisionDeleteInterfaceVersion)
                     ->send();
                           
      $this->assertEquals($revisionDelete->getStatus(), "200", "Debugging information: ".var_export($revisionDelete, TRUE));                                       
      
      $revisionLister = new RevisionListerQuery($settings->endpointUrl);

      $revisionLister->dataset($settings->testDataset)
                     ->mime('resultset')
                     ->uri('http://foo.com/datasets/tests/foo')
                     ->shortResults()
                     ->send();      
      
      $this->assertEquals($revisionLister->getStatus(), "200", "Debugging information: ".var_export($revisionLister, TRUE));                                       
      
      $resultset = $revisionLister->getResultset()->getResultset();

      $this->assertTrue(!isset($resultset['unspecified'][$revuri]), "Debugging information: ".var_export($revisionLister, TRUE));                                       
      
      $found = FALSE;
      
      foreach($resultset['unspecified'] as $revu => $rev)
      {
        if($revu == $revuri)
        {
          $found = TRUE;
          break;
        }
      }

      $this->assertFalse($found, "Debugging information: ".var_export($resultset, TRUE));                                       
      
      utilities\deleteRevisionedRecord();

      unset($revisionDelete);
      unset($settings);   
    }    

    public function testDeleteExperimentalRevision() {
      
     $settings = new Config();  
      
      utilities\deleteRevisionedRecord();
      
      $this->assertTrue(utilities\createRevisionedRecord(), "Can't create unrevisioned records...");

      $revuri = utilities\getLastRevisionUri('http://foo.com/datasets/tests/foo');

      $this->assertFalse($revuri === FALSE, "Debugging information: ".var_export($revuri, TRUE));                                       

      
      // unpublish the revision           
      $revisionUpdate = new RevisionUpdateQuery($settings->endpointUrl);
      
      $revisionUpdate->dataset($settings->testDataset)
                     ->isExperimental()
                     ->revisionUri($revuri)
                     ->send();

      $this->assertEquals($revisionUpdate->getStatus(), "200", "Debugging information: ".var_export($revisionUpdate, TRUE));                                       
      
      $revisionDelete = new RevisionDeleteQuery($settings->endpointUrl);
      
      $revisionDelete->dataset($settings->testDataset)
                     ->revisionUri($revuri)
                     ->sourceInterface($settings->revisionDeleteInterface)
                     ->sourceInterfaceVersion($settings->revisionDeleteInterfaceVersion)
                     ->send();
                           
      $this->assertEquals($revisionDelete->getStatus(), "200", "Debugging information: ".var_export($revisionDelete, TRUE));                                       
      
      $revisionLister = new RevisionListerQuery($settings->endpointUrl);

      $revisionLister->dataset($settings->testDataset)
                     ->mime('resultset')
                     ->uri('http://foo.com/datasets/tests/foo')
                     ->shortResults()
                     ->send();      
      
      $this->assertEquals($revisionLister->getStatus(), "200", "Debugging information: ".var_export($revisionLister, TRUE));                                       
      
      $resultset = $revisionLister->getResultset()->getResultset();

      $this->assertTrue(!isset($resultset['unspecified'][$revuri]), "Debugging information: ".var_export($revisionLister, TRUE));                                       
      
      $found = FALSE;
      
      foreach($resultset['unspecified'] as $revu => $rev)
      {
        if($revu == $revuri)
        {
          $found = TRUE;
          break;
        }
      }

      $this->assertFalse($found, "Debugging information: ".var_export($resultset, TRUE));                                       
      
      utilities\deleteRevisionedRecord();

      unset($revisionDelete);
      unset($settings);   
    }   
    
    public function testDeleteHarvestingRevision() {
      
     $settings = new Config();  
      
      utilities\deleteRevisionedRecord();
      
      $this->assertTrue(utilities\createRevisionedRecord(), "Can't create unrevisioned records...");

      $revuri = utilities\getLastRevisionUri('http://foo.com/datasets/tests/foo');

      $this->assertFalse($revuri === FALSE, "Debugging information: ".var_export($revuri, TRUE));                                       

      
      // unpublish the revision           
      $revisionUpdate = new RevisionUpdateQuery($settings->endpointUrl);
      
      $revisionUpdate->dataset($settings->testDataset)
                     ->isHarvesting()
                     ->revisionUri($revuri)
                     ->send();

      $this->assertEquals($revisionUpdate->getStatus(), "200", "Debugging information: ".var_export($revisionUpdate, TRUE));                                       
      
      $revisionDelete = new RevisionDeleteQuery($settings->endpointUrl);
      
      $revisionDelete->dataset($settings->testDataset)
                     ->revisionUri($revuri)
                     ->sourceInterface($settings->revisionDeleteInterface)
                     ->sourceInterfaceVersion($settings->revisionDeleteInterfaceVersion)
                     ->send();
                           
      $this->assertEquals($revisionDelete->getStatus(), "200", "Debugging information: ".var_export($revisionDelete, TRUE));                                       
      
      $revisionLister = new RevisionListerQuery($settings->endpointUrl);

      $revisionLister->dataset($settings->testDataset)
                     ->mime('resultset')
                     ->uri('http://foo.com/datasets/tests/foo')
                     ->shortResults()
                     ->send();      
      
      $this->assertEquals($revisionLister->getStatus(), "200", "Debugging information: ".var_export($revisionLister, TRUE));                                       
      
      $resultset = $revisionLister->getResultset()->getResultset();

      $this->assertTrue(!isset($resultset['unspecified'][$revuri]), "Debugging information: ".var_export($revisionLister, TRUE));                                       
      
      $found = FALSE;
      
      foreach($resultset['unspecified'] as $revu => $rev)
      {
        if($revu == $revuri)
        {
          $found = TRUE;
          break;
        }
      }

      $this->assertFalse($found, "Debugging information: ".var_export($resultset, TRUE));                                       
      
      utilities\deleteRevisionedRecord();

      unset($revisionDelete);
      unset($settings);   
    }
    
    public function testDeletePreReleaseRevision() {
      
     $settings = new Config();  
      
      utilities\deleteRevisionedRecord();
      
      $this->assertTrue(utilities\createRevisionedRecord(), "Can't create unrevisioned records...");

      $revuri = utilities\getLastRevisionUri('http://foo.com/datasets/tests/foo');

      $this->assertFalse($revuri === FALSE, "Debugging information: ".var_export($revuri, TRUE));                                       

      
      // unpublish the revision           
      $revisionUpdate = new RevisionUpdateQuery($settings->endpointUrl);
      
      $revisionUpdate->dataset($settings->testDataset)
                     ->isPreRelease()
                     ->revisionUri($revuri)
                     ->send();

      $this->assertEquals($revisionUpdate->getStatus(), "200", "Debugging information: ".var_export($revisionUpdate, TRUE));                                       
      
      $revisionDelete = new RevisionDeleteQuery($settings->endpointUrl);
      
      $revisionDelete->dataset($settings->testDataset)
                     ->revisionUri($revuri)
                     ->sourceInterface($settings->revisionDeleteInterface)
                     ->sourceInterfaceVersion($settings->revisionDeleteInterfaceVersion)
                     ->send();
                           
      $this->assertEquals($revisionDelete->getStatus(), "200", "Debugging information: ".var_export($revisionDelete, TRUE));                                       
      
      $revisionLister = new RevisionListerQuery($settings->endpointUrl);

      $revisionLister->dataset($settings->testDataset)
                     ->mime('resultset')
                     ->uri('http://foo.com/datasets/tests/foo')
                     ->shortResults()
                     ->send();      
      
      $this->assertEquals($revisionLister->getStatus(), "200", "Debugging information: ".var_export($revisionLister, TRUE));                                       
      
      $resultset = $revisionLister->getResultset()->getResultset();

      $this->assertTrue(!isset($resultset['unspecified'][$revuri]), "Debugging information: ".var_export($revisionLister, TRUE));                                       
      
      $found = FALSE;
      
      foreach($resultset['unspecified'] as $revu => $rev)
      {
        if($revu == $revuri)
        {
          $found = TRUE;
          break;
        }
      }

      $this->assertFalse($found, "Debugging information: ".var_export($resultset, TRUE));                                       
      
      utilities\deleteRevisionedRecord();

      unset($revisionDelete);
      unset($settings);   
    }    
    
    public function testDeleteStagingRevision() {
      
     $settings = new Config();  
      
      utilities\deleteRevisionedRecord();
      
      $this->assertTrue(utilities\createRevisionedRecord(), "Can't create unrevisioned records...");

      $revuri = utilities\getLastRevisionUri('http://foo.com/datasets/tests/foo');

      $this->assertFalse($revuri === FALSE, "Debugging information: ".var_export($revuri, TRUE));                                       

      
      // unpublish the revision           
      $revisionUpdate = new RevisionUpdateQuery($settings->endpointUrl);
      
      $revisionUpdate->dataset($settings->testDataset)
                     ->isStaging()
                     ->revisionUri($revuri)
                     ->send();

      $this->assertEquals($revisionUpdate->getStatus(), "200", "Debugging information: ".var_export($revisionUpdate, TRUE));                                       
      
      $revisionDelete = new RevisionDeleteQuery($settings->endpointUrl);
      
      $revisionDelete->dataset($settings->testDataset)
                     ->revisionUri($revuri)
                     ->sourceInterface($settings->revisionDeleteInterface)
                     ->sourceInterfaceVersion($settings->revisionDeleteInterfaceVersion)
                     ->send();
                           
      $this->assertEquals($revisionDelete->getStatus(), "200", "Debugging information: ".var_export($revisionDelete, TRUE));                                       
      
      $revisionLister = new RevisionListerQuery($settings->endpointUrl);

      $revisionLister->dataset($settings->testDataset)
                     ->mime('resultset')
                     ->uri('http://foo.com/datasets/tests/foo')
                     ->shortResults()
                     ->send();      
      
      $this->assertEquals($revisionLister->getStatus(), "200", "Debugging information: ".var_export($revisionLister, TRUE));                                       
      
      $resultset = $revisionLister->getResultset()->getResultset();

      $this->assertTrue(!isset($resultset['unspecified'][$revuri]), "Debugging information: ".var_export($revisionLister, TRUE));                                       
      
      $found = FALSE;
      
      foreach($resultset['unspecified'] as $revu => $rev)
      {
        if($revu == $revuri)
        {
          $found = TRUE;
          break;
        }
      }

      $this->assertFalse($found, "Debugging information: ".var_export($resultset, TRUE));                                       
      
      utilities\deleteRevisionedRecord();

      unset($revisionDelete);
      unset($settings);   
    }    
    
    public function testDeleteUnspecifiedRevision() {
      
     $settings = new Config();  
      
      utilities\deleteRevisionedRecord();
      
      $this->assertTrue(utilities\createRevisionedRecord(), "Can't create unrevisioned records...");

      $revuri = utilities\getLastRevisionUri('http://foo.com/datasets/tests/foo');

      $this->assertFalse($revuri === FALSE, "Debugging information: ".var_export($revuri, TRUE));                                       

      
      // unpublish the revision           
      $revisionUpdate = new RevisionUpdateQuery($settings->endpointUrl);
      
      $revisionUpdate->dataset($settings->testDataset)
                     ->isUnspecified()
                     ->revisionUri($revuri)
                     ->send();

      $this->assertEquals($revisionUpdate->getStatus(), "200", "Debugging information: ".var_export($revisionUpdate, TRUE));                                       
      
      $revisionDelete = new RevisionDeleteQuery($settings->endpointUrl);
      
      $revisionDelete->dataset($settings->testDataset)
                     ->revisionUri($revuri)
                     ->sourceInterface($settings->revisionDeleteInterface)
                     ->sourceInterfaceVersion($settings->revisionDeleteInterfaceVersion)
                     ->send();
                           
      $this->assertEquals($revisionDelete->getStatus(), "200", "Debugging information: ".var_export($revisionDelete, TRUE));                                       
      
      $revisionLister = new RevisionListerQuery($settings->endpointUrl);

      $revisionLister->dataset($settings->testDataset)
                     ->mime('resultset')
                     ->uri('http://foo.com/datasets/tests/foo')
                     ->shortResults()
                     ->send();      
      
      $this->assertEquals($revisionLister->getStatus(), "200", "Debugging information: ".var_export($revisionLister, TRUE));                                       
      
      $resultset = $revisionLister->getResultset()->getResultset();

      $this->assertTrue(!isset($resultset['unspecified'][$revuri]), "Debugging information: ".var_export($revisionLister, TRUE));                                       
      
      $found = FALSE;
      
      foreach($resultset['unspecified'] as $revu => $rev)
      {
        if($revu == $revuri)
        {
          $found = TRUE;
          break;
        }
      }

      $this->assertFalse($found, "Debugging information: ".var_export($resultset, TRUE));                                       
      
      utilities\deleteRevisionedRecord();

      unset($revisionDelete);
      unset($settings);   
    }    
    
  }

  
?>