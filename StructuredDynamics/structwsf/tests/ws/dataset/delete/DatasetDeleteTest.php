<?php

  namespace StructuredDynamics\structwsf\tests\ws\dataset\delete;
  
  use StructuredDynamics\structwsf\framework\WebServiceQuerier;
  use StructuredDynamics\structwsf\php\api\ws\dataset\delete\DatasetDeleteQuery;
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
  
  class DatasetDeleteTest extends \PHPUnit_Framework_TestCase {
    
    static private $outputs = array();

    public function testWrongEndpointUrl() {
      
      $settings = new Config();          
      
      $wsq = new WebServiceQuerier($settings->endpointUrl . "dataset/delete/" . "wrong", 
                                   "get", 
                                   "text/xml",
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
      
      $datasetDelete->uri($settings->testDataset);
      
      $datasetDelete->sourceInterface("default");
      
      $datasetDelete->sourceInterfaceVersion($settings->datasetDeleteInterfaceVersion);

      $datasetDelete->send();
                           
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
      
      $datasetDelete->uri($settings->testDataset);
      
      $datasetDelete->sourceInterface("default");
      
      $datasetDelete->sourceInterfaceVersion("667.4");

      $datasetDelete->send();
                           
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
      
      $datasetDelete->uri($settings->testDataset);
      
      $datasetDelete->sourceInterface("default");

      $datasetDelete->send();
                           
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
      
      $datasetDelete->uri($settings->testDataset);
      
      $datasetDelete->sourceInterface("default-not-existing");

      $datasetDelete->send();
                           
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
      
      $datasetDelete->uri($settings->testDataset);

      $datasetDelete->send();
                                   
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
      
      $datasetDelete->uri($settings->testDataset);

      $datasetDelete->send();      
                                   
      $this->assertEquals($datasetDelete->getStatus(), "200", "Debugging information: ".var_export($datasetDelete, TRUE)); 
                                            
      $this->assertFalse(utilities\readDataset(), "Dataset still existing...");

      unset($datasetDelete);
      unset($settings);
    }                     
    
    public function  testCreateDatasetNoDatasetUriSpecified() {
      
      $settings = new Config();  
      
      $datasetDelete = new DatasetDeleteQuery($settings->endpointUrl);
      
      $datasetDelete->uri("");

      $datasetDelete->send();
                                   
      $this->assertEquals($datasetDelete->getStatus(), "400", "Debugging information: ".var_export($datasetDelete, TRUE));                                       
      $this->assertEquals($datasetDelete->getStatusMessage(), "Bad Request", "Debugging information: ".var_export($datasetDelete, TRUE));
      $this->assertEquals($datasetDelete->error->id, "WS-DATASET-DELETE-200", "Debugging information: ".var_export($datasetDelete, TRUE));                  
      
      unset($datasetDelete);
      unset($settings);
    }
    
    public function  testCreateDatasetInvalidDatasetIRI() {
      
      $settings = new Config();  
                                   
      $datasetDelete = new DatasetDeleteQuery($settings->endpointUrl);
      
      $datasetDelete->uri($settings->testDataset."<>");

      $datasetDelete->send();                                   
                                   
      $this->assertEquals($datasetDelete->getStatus(), "400", "Debugging information: ".var_export($datasetDelete, TRUE));                                       
      $this->assertEquals($datasetDelete->getStatusMessage(), "Bad Request", "Debugging information: ".var_export($datasetDelete, TRUE));
      $this->assertEquals($datasetDelete->error->id, "WS-DATASET-DELETE-201", "Debugging information: ".var_export($datasetDelete, TRUE));                                       
      
      unset($datasetDelete);
      unset($settings);
    }
  }

  
?>