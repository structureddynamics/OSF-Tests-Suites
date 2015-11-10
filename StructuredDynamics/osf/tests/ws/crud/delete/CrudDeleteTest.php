<?php

  namespace StructuredDynamics\osf\tests\ws\crud\delete;
  
  use StructuredDynamics\osf\framework\WebServiceQuerier;
  use StructuredDynamics\osf\php\api\ws\crud\delete\CrudDeleteQuery;
  use \StructuredDynamics\osf\php\api\ws\crud\read\CrudReadQuery;
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
  
  class CrudDeleteTest extends \PHPUnit_Framework_TestCase {
    
    static private $outputs = array();

    public function testWrongEndpointUrl() {
      
      $settings = new Config();          
      
      $wsq = new WebServiceQuerier($settings->endpointUrl . "crud/delete/" . "wrong", 
                                   "get", 
                                   "text/xml",
                                   "uri=" . urlencode('') .
                                   "&mode=" . urlencode('hard') .
                                   "&dataset=" . urlencode($settings->testDataset) .
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
    
    public function testWrongEndpointMethodPost() {
      
      $settings = new Config();  
      
      $wsq = new WebServiceQuerier($settings->endpointUrl . "crud/delete/", 
                                   "post", 
                                   "text/xml",
                                   "uri=" . urlencode('') .
                                   "&mode=" . urlencode('hard') .
                                   "&dataset=" . urlencode($settings->testDataset) .
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
    
    public function testValidInterfaceVersion() {
      
      $settings = new Config();  
      
      utilities\deleteUnrevisionedRecord();
      
      $this->assertTrue(utilities\createUnrevisionedRecord(), "Can't create unrevisioned records...");

      $crudDelete = new CrudDeleteQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
      
      $crudDelete->dataset($settings->testDataset)
                 ->uri('http://foo.com/datasets/tests/bar')
                 ->hard()
                 ->sourceInterface($settings->crudDeleteInterface)
                 ->sourceInterfaceVersion($settings->crudDeleteInterfaceVersion)
                 ->send();
                           
      $this->assertEquals($crudDelete->getStatus(), "200", "Debugging information: ".var_export($crudDelete, TRUE));                                       

      utilities\deleteUnrevisionedRecord();

      unset($crudDelete);
      unset($settings);    
    }
    
 
    public function testInvalidInterfaceVersion() {
      
      $settings = new Config();  
      
      utilities\deleteUnrevisionedRecord();
      
      $this->assertTrue(utilities\createUnrevisionedRecord(), "Can't create unrevisioned records...");

      $crudDelete = new CrudDeleteQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
      
      $crudDelete->dataset($settings->testDataset)
                 ->uri('http://foo.com/datasets/tests/bar')
                 ->hard()
                 ->sourceInterface($settings->crudDeleteInterface)
                 ->sourceInterfaceVersion("667.4")
                 ->send();
                           
      $this->assertEquals($crudDelete->getStatus(), "400", "Debugging information: ".var_export($crudDelete, TRUE));                                       
      $this->assertEquals($crudDelete->getStatusMessage(), "Bad Request", "Debugging information: ".var_export($crudDelete, TRUE));
      $this->assertEquals($crudDelete->error->id, "WS-CRUD-DELETE-305", "Debugging information: ".var_export($crudDelete, TRUE));                                       

      utilities\deleteUnrevisionedRecord();

      unset($crudDelete);
      unset($settings);                 
    }    
    
    public function testInterfaceExists() {
     
      $settings = new Config();  
      
      utilities\deleteUnrevisionedRecord();
      
      $this->assertTrue(utilities\createUnrevisionedRecord(), "Can't create unrevisioned records...");

      $crudDelete = new CrudDeleteQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
      
      $crudDelete->dataset($settings->testDataset)
                 ->uri('http://foo.com/datasets/tests/bar')
                 ->hard()
                 ->sourceInterface($settings->crudDeleteInterface)
                 ->sourceInterfaceVersion($settings->crudDeleteInterfaceVersion)
                 ->send();
                           
      $this->assertEquals($crudDelete->getStatus(), "200", "Debugging information: ".var_export($crudDelete, TRUE));                                       

      utilities\deleteUnrevisionedRecord();

      unset($crudDelete);
      unset($settings);  
    }  
    
    public function testInterfaceNotExisting() {
          
      $settings = new Config();  
      
      utilities\deleteUnrevisionedRecord();
      
      $this->assertTrue(utilities\createUnrevisionedRecord(), "Can't create unrevisioned records...");

      $crudDelete = new CrudDeleteQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
      
      $crudDelete->dataset($settings->testDataset)
                 ->uri('http://foo.com/datasets/tests/bar')
                 ->hard()
                 ->sourceInterface("default-not-existing")
                 ->sourceInterfaceVersion($settings->crudDeleteInterfaceVersion)
                 ->send();
                           
      $this->assertEquals($crudDelete->getStatus(), "400", "Debugging information: ".var_export($crudDelete, TRUE));                                       
      $this->assertEquals($crudDelete->getStatusMessage(), "Bad Request", "Debugging information: ".var_export($crudDelete, TRUE));
      $this->assertEquals($crudDelete->error->id, "WS-CRUD-DELETE-304", "Debugging information: ".var_export($crudDelete, TRUE));                                       

      utilities\deleteUnrevisionedRecord();

      unset($crudDelete);
      unset($settings);        
    }    
    
    public function testNoDatasetSpecified() {
      
      $settings = new Config();  
      
      utilities\deleteUnrevisionedRecord();
      
      $this->assertTrue(utilities\createUnrevisionedRecord(), "Can't create unrevisioned records...");

      $crudDelete = new CrudDeleteQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
      
      $crudDelete->dataset('')
                 ->uri('http://foo.com/datasets/tests/bar')
                 ->hard()
                 ->sourceInterface($settings->crudDeleteInterface)
                 ->sourceInterfaceVersion($settings->crudDeleteInterfaceVersion)
                 ->send();
                           
      $this->assertEquals($crudDelete->getStatus(), "403", "Debugging information: ".var_export($crudDelete, TRUE));                                       
      $this->assertEquals($crudDelete->getStatusMessage(), "Forbidden", "Debugging information: ".var_export($crudDelete, TRUE));
      $this->assertEquals($crudDelete->error->id, "WS-AUTH-VALIDATION-104", "Debugging information: ".var_export($crudDelete, TRUE));                                       

      utilities\deleteUnrevisionedRecord();

      unset($crudDelete);
      unset($settings);        
    }     
        
    public function testNoResourceURISpecified() {
      
      $settings = new Config();  
      
      utilities\deleteUnrevisionedRecord();
      
      $this->assertTrue(utilities\createUnrevisionedRecord(), "Can't create unrevisioned records...");

      $crudDelete = new CrudDeleteQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
      
      $crudDelete->dataset($settings->testDataset)
                 ->uri('')
                 ->hard()
                 ->sourceInterface($settings->crudDeleteInterface)
                 ->sourceInterfaceVersion($settings->crudDeleteInterfaceVersion)
                 ->send();
                           
      $this->assertEquals($crudDelete->getStatus(), "400", "Debugging information: ".var_export($crudDelete, TRUE));                                       
      $this->assertEquals($crudDelete->getStatusMessage(), "Bad Request", "Debugging information: ".var_export($crudDelete, TRUE));
      $this->assertEquals($crudDelete->error->id, "WS-CRUD-DELETE-200", "Debugging information: ".var_export($crudDelete, TRUE));                                       

      utilities\deleteUnrevisionedRecord();

      unset($crudDelete);
      unset($settings);        
    }  
            
    public function testUnknownDeleteMode() {
      
      $settings = new Config();  
      
      utilities\deleteRevisionedRecord();
      
      $this->assertTrue(utilities\createRevisionedRecord(), "Can't create unrevisioned records...");      
      
      $wsq = new WebServiceQuerier($settings->endpointUrl . "crud/delete/", 
                                   "get", 
                                   "text/xml",
                                   "uri=" . urlencode('http://foo.com/datasets/tests/bar') .
                                   "&mode=" . urlencode('unknown') .
                                   "&dataset=" . urlencode($settings->testDataset) .
                                   "&interface=". urlencode($settings->crudCreateInterface) .
                                   "&version=". urlencode($settings->crudCreateInterfaceVersion),
                                   $settings->applicationID,
                                   $settings->apiKey,
                                   $settings->userID);
       
      $this->assertEquals($wsq->getStatus(), "400", "Debugging information: ".var_export($wsq, TRUE));                                       
      $this->assertEquals($wsq->getStatusMessage(), "Bad Request", "Debugging information: ".var_export($wsq, TRUE));
      $this->assertEquals($wsq->error->id, "WS-CRUD-DELETE-307", "Debugging information: ".var_export($wsq, TRUE));                                       

      utilities\deleteRevisionedRecord();
      
      unset($wsq);
      unset($settings);      
    }      

    public function testDeleteHardMode() {
      
      $settings = new Config();  
      
      utilities\deleteRevisionedRecord();
      
      $this->assertTrue(utilities\createRevisionedRecord(), "Can't create unrevisioned records...");

      $crudDelete = new CrudDeleteQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
      
      $crudDelete->dataset($settings->testDataset)
                 ->uri('http://foo.com/datasets/tests/bar')
                 ->hard()
                 ->sourceInterface($settings->crudDeleteInterface)
                 ->sourceInterfaceVersion($settings->crudDeleteInterfaceVersion)
                 ->send();
                           
      $this->assertEquals($crudDelete->getStatus(), "200", "Debugging information: ".var_export($crudDelete, TRUE));                                       

      $revisionLister = new RevisionListerQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
      
      $revisionLister->dataset($settings->testDataset)
                     ->shortResults()
                     ->uri('http://foo.com/datasets/tests/bar')
                     ->mime('resultset')
                     ->send();
                     
      $this->assertEquals($revisionLister->getStatus(), "200", "Debugging information: ".var_export($revisionLister, TRUE));        
      
      $resultset = $revisionLister->getResultset()->getResultset();

      $this->assertTrue(empty($resultset), "Debugging information: ".var_export($resultset, TRUE));        

      $crudRead = new CrudReadQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
      
      $crudRead->dataset($settings->testDataset)
               ->uri('http://foo.com/datasets/tests/bar')
               ->send();

      $this->assertEquals($crudRead->getStatus(), "400", "Debugging information: ".var_export($crudRead, TRUE));                                       
      $this->assertEquals($crudRead->getStatusMessage(), "Bad Request", "Debugging information: ".var_export($crudRead, TRUE));
      $this->assertEquals($crudRead->error->id, "WS-CRUD-READ-300", "Debugging information: ".var_export($crudRead, TRUE));                                           
      
      utilities\deleteRevisionedRecord();

      unset($crudDelete);
      unset($revisionLister);
      unset($settings);       
    }   
    
    public function testDeleteSoftMode() {
      
      $settings = new Config();  
      
      utilities\deleteRevisionedRecord();
      
      $this->assertTrue(utilities\createRevisionedRecord(), "Can't create unrevisioned records...");

      $crudDelete = new CrudDeleteQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
      
      $crudDelete->dataset($settings->testDataset)
                 ->uri('http://foo.com/datasets/tests/bar')
                 ->soft()
                 ->sourceInterface($settings->crudDeleteInterface)
                 ->sourceInterfaceVersion($settings->crudDeleteInterfaceVersion)
                 ->send();
                           
      $this->assertEquals($crudDelete->getStatus(), "200", "Debugging information: ".var_export($crudDelete, TRUE));                                       

      $revisionLister = new RevisionListerQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
      
      $revisionLister->dataset($settings->testDataset)
                     ->shortResults()
                     ->uri('http://foo.com/datasets/tests/bar')
                     ->mime('resultset')
                     ->send();
                     
      $this->assertEquals($revisionLister->getStatus(), "200", "Debugging information: ".var_export($revisionLister, TRUE));        
      
      $resultset = $revisionLister->getResultset()->getResultset();

      $this->assertTrue((count($resultset['unspecified']) == 2), "Debugging information: ".var_export($resultset, TRUE));        

      $crudRead = new CrudReadQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
      
      $crudRead->dataset($settings->testDataset)
               ->uri('http://foo.com/datasets/tests/bar')
               ->send();

      $this->assertEquals($crudRead->getStatus(), "400", "Debugging information: ".var_export($crudRead, TRUE));                                       
      $this->assertEquals($crudRead->getStatusMessage(), "Bad Request", "Debugging information: ".var_export($crudRead, TRUE));
      $this->assertEquals($crudRead->error->id, "WS-CRUD-READ-300", "Debugging information: ".var_export($crudRead, TRUE));                                           
      
      utilities\deleteRevisionedRecord();

      unset($crudDelete);
      unset($revisionLister);
      unset($settings);       
    }          
  }

  
?>