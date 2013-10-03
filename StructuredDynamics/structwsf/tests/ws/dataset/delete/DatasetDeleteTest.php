<?php

  namespace StructuredDynamics\osf\tests\ws\dataset\delete;
  
  use StructuredDynamics\osf\framework\WebServiceQuerier;
  use StructuredDynamics\osf\php\api\ws\dataset\delete\DatasetDeleteQuery;
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
  
  class DatasetDeleteTest extends \PHPUnit_Framework_TestCase {
    
    static private $outputs = array();

    public function testWrongEndpointUrl() {
      
      $settings = new Config();          
      
      $wsq = new WebServiceQuerier($settings->endpointUrl . "dataset/delete/" . "wrong", 
                                   "get", 
                                   "text/xml",
                                   "&interface=". urlencode($settings->datasetDeleteInterface) .
                                   "&version=". urlencode($settings->datasetDeleteInterfaceVersion) .
                                   "uri=" . urlencode($settings->testDataset));
                   
      $this->assertEquals($wsq->getStatus(), "404", "Debugging information: ".var_export($wsq, TRUE));                                       
      $this->assertEquals($wsq->getStatusMessage(), "Not Found", "Debugging information: ".var_export($wsq, TRUE));
      
      unset($wsq);
      unset($settings);
    }
    
    public function testWrongEndpointMethodPost() {
      
      $settings = new Config();  
      
      $wsq = new WebServiceQuerier($settings->endpointUrl . "dataset/delete/", 
                                   "post", 
                                   "text/xml",
                                   "&interface=". urlencode($settings->datasetDeleteInterface) .
                                   "&version=". urlencode($settings->datasetDeleteInterfaceVersion) .
                                   "uri=" . urlencode($settings->testDataset));
                                   
      $this->assertEquals($wsq->getStatus(), "405", "Debugging information: ".var_export($wsq, TRUE));                                       
      $this->assertEquals($wsq->getStatusMessage(), "Method Not Allowed", "Debugging information: ".var_export($wsq, TRUE));          
      
      unset($wsq);
      unset($settings);
    }    
    
    public function testValidInterfaceVersion() {
      
      $settings = new Config();  

      utilities\deleteDataset();
      
      $this->assertTrue(utilities\createDataset(), "Can't create the dataset, check the /dataset/create/ endpoint first...");
      
      $datasetDelete = new DatasetDeleteQuery($settings->endpointUrl);
      
      $datasetDelete->uri($settings->testDataset)
                    ->sourceInterface("default")
                    ->sourceInterface($settings->datasetDeleteInterface)
                    ->sourceInterfaceVersion($settings->datasetDeleteInterfaceVersion)
                    ->send();
                           
      $this->assertEquals($datasetDelete->getStatus(), "200", "Debugging information: ".var_export($datasetDelete, TRUE));                                       

      utilities\deleteDataset();

      unset($datasetDelete);
      unset($settings);  
    }
    
    
    public function testInvalidInterfaceVersion() {
      
      $settings = new Config();  

      utilities\deleteDataset();
      
      $this->assertTrue(utilities\createDataset(), "Can't create the dataset, check the /dataset/create/ endpoint first...");
      
      $datasetDelete = new DatasetDeleteQuery($settings->endpointUrl);
      
      $datasetDelete->uri($settings->testDataset)
                    ->sourceInterface($settings->datasetDeleteInterface)
                    ->sourceInterfaceVersion("667.4")
                    ->send();
                           
      $this->assertEquals($datasetDelete->getStatus(), "400", "Debugging information: ".var_export($datasetDelete, TRUE));                                       
      $this->assertEquals($datasetDelete->getStatusMessage(), "Bad Request", "Debugging information: ".var_export($datasetDelete, TRUE));
      $this->assertEquals($datasetDelete->error->id, "WS-DATASET-DELETE-308", "Debugging information: ".var_export($datasetDelete, TRUE));                                       

      utilities\deleteDataset();

      unset($datasetDelete);
      unset($settings);  
    }    
    
    //
    // Test existing interface
    //
    
    public function testInterfaceExists() {
      
      $settings = new Config();  

      utilities\deleteDataset();
      
      $this->assertTrue(utilities\createDataset(), "Can't create the dataset, check the /dataset/create/ endpoint first...");
      
      $datasetDelete = new DatasetDeleteQuery($settings->endpointUrl);
      
      $datasetDelete->uri($settings->testDataset)
                    ->sourceInterface($settings->datasetDeleteInterface)
                    ->sourceInterfaceVersion($settings->datasetDeleteInterfaceVersion)
                    ->send();
                           
      $this->assertEquals($datasetDelete->getStatus(), "200", "Debugging information: ".var_export($datasetDelete, TRUE));                                       

      utilities\deleteDataset();

      unset($datasetDelete);
      unset($settings);
    }  
    
    //
    // Test unexisting interface
    //
    
    public function testInterfaceNotExisting() {
      
      $settings = new Config();  

      utilities\deleteDataset();
      
      $this->assertTrue(utilities\createDataset(), "Can't create the dataset, check the /dataset/create/ endpoint first...");
      
      $datasetDelete = new DatasetDeleteQuery($settings->endpointUrl);
      
      $datasetDelete->uri($settings->testDataset)
                    ->sourceInterface("default-not-existing")
                    ->sourceInterfaceVersion($settings->datasetDeleteInterfaceVersion)
                    ->send();
                           
      $this->assertEquals($datasetDelete->getStatus(), "400", "Debugging information: ".var_export($datasetDelete, TRUE));                                       
      $this->assertEquals($datasetDelete->getStatusMessage(), "Bad Request", "Debugging information: ".var_export($datasetDelete, TRUE));
      $this->assertEquals($datasetDelete->error->id, "WS-DATASET-DELETE-307", "Debugging information: ".var_export($datasetDelete, TRUE));                                       

      utilities\deleteDataset();

      unset($datasetDelete);
      unset($settings);
    }     
    
    public function  testDeleteDataset() {
      
      $settings = new Config();  
      
      utilities\deleteDataset();
      
      $this->assertTrue(utilities\createDataset(), "Can't create the dataset, check the /dataset/create/ endpoint first...");
      
      $datasetDelete = new DatasetDeleteQuery($settings->endpointUrl);
      
      $datasetDelete->uri($settings->testDataset)
                    ->sourceInterface($settings->datasetDeleteInterface)
                    ->sourceInterfaceVersion($settings->datasetDeleteInterfaceVersion)
                    ->send();
                                   
      $this->assertEquals($datasetDelete->getStatus(), "200", "Debugging information: ".var_export($datasetDelete, TRUE));    
                                    
      unset($datasetDelete);
      unset($settings);
    }  
    
    public function  testDeleteDatasetValidateDeletedContent() {
      
      $settings = new Config();  
      
      utilities\deleteDataset();
      
      $this->assertTrue(utilities\createDataset(), "Can't create the dataset, check the /dataset/create/ endpoint first...");
            
      $this->assertTrue((utilities\readDataset() !== FALSE ? TRUE : FALSE), "Can't read the dataset, check the /dataset/read/ endpoint first...");
      
      $datasetDelete = new DatasetDeleteQuery($settings->endpointUrl);
      
      $datasetDelete->uri($settings->testDataset)
                    ->sourceInterface($settings->datasetDeleteInterface)
                    ->sourceInterfaceVersion($settings->datasetDeleteInterfaceVersion)
                    ->send();      
                                   
      $this->assertEquals($datasetDelete->getStatus(), "200", "Debugging information: ".var_export($datasetDelete, TRUE)); 
                                            
      $this->assertFalse(utilities\readDataset(), "Dataset still existing...");

      unset($datasetDelete);
      unset($settings);
    }                     
    
    public function  testCreateDatasetNoDatasetUriSpecified() {
      
      $settings = new Config();  
      
      $datasetDelete = new DatasetDeleteQuery($settings->endpointUrl);
      
      $datasetDelete->uri("")
                    ->sourceInterface($settings->datasetDeleteInterface)
                    ->sourceInterfaceVersion($settings->datasetDeleteInterfaceVersion)
                    ->send();
                                   
      $this->assertEquals($datasetDelete->getStatus(), "400", "Debugging information: ".var_export($datasetDelete, TRUE));                                       
      $this->assertEquals($datasetDelete->getStatusMessage(), "Bad Request", "Debugging information: ".var_export($datasetDelete, TRUE));
      $this->assertEquals($datasetDelete->error->id, "WS-DATASET-DELETE-200", "Debugging information: ".var_export($datasetDelete, TRUE));                  
      
      unset($datasetDelete);
      unset($settings);
    }
    
    public function  testCreateDatasetInvalidDatasetIRI() {
      
      $settings = new Config();  
                                   
      $datasetDelete = new DatasetDeleteQuery($settings->endpointUrl);
      
      $datasetDelete->uri($settings->testDataset."<>")
                    ->sourceInterface($settings->datasetDeleteInterface)
                    ->sourceInterfaceVersion($settings->datasetDeleteInterfaceVersion)
                    ->send();                                   
                                   
      $this->assertEquals($datasetDelete->getStatus(), "400", "Debugging information: ".var_export($datasetDelete, TRUE));                                       
      $this->assertEquals($datasetDelete->getStatusMessage(), "Bad Request", "Debugging information: ".var_export($datasetDelete, TRUE));
      $this->assertEquals($datasetDelete->error->id, "WS-DATASET-DELETE-201", "Debugging information: ".var_export($datasetDelete, TRUE));                                       
      
      unset($datasetDelete);
      unset($settings);
    }
  }

  
?>