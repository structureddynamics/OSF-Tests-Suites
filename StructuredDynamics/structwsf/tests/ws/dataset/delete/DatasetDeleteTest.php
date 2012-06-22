<?php
  include_once("../tests/Config.php");
  include_once("../tests/validators.php");
  
  ini_set("memory_limit","256M");
  set_time_limit(3600);

  $settings = new Config(); 
  
  // Database connectivity procedures
  include_once($settings->structwsfInstanceFolder . "framework/WebServiceQuerier.php");
  include_once("../tests/utilities.php");
  
  class DatasetDeleteTest extends PHPUnit_Framework_TestCase {
    
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
    
    public function  testDeleteDataset() {
      
      $settings = new Config();  
      
      deleteDataset();
      
      $this->assertTrue(createDataset(), "Can't create the dataset, check the /dataset/create/ endpoint first...");
      
      // Create the new dataset
      $wsq = new WebServiceQuerier($settings->endpointUrl . "dataset/delete/", 
                                   "get", 
                                   "text/xml",
                                   "uri=" . urlencode($settings->testDataset));
                                   
      $this->assertEquals($wsq->getStatus(), "200", "Debugging information: ".var_export($wsq, TRUE));    
                                    
      unset($wsq);
      unset($settings);
    }  
    
    public function  testDeleteDatasetValidateDeletedContent() {
      
      $settings = new Config();  
      
      deleteDataset();
      
      $this->assertTrue(createDataset(), "Can't create the dataset, check the /dataset/create/ endpoint first...");
            
      $this->assertTrue((readDataset() !== FALSE ? TRUE : FALSE), "Can't read the dataset, check the /dataset/read/ endpoint first...");
      
      // Create the new dataset
      $wsq = new WebServiceQuerier($settings->endpointUrl . "dataset/delete/", 
                                   "get", 
                                   "text/xml",
                                   "uri=" . urlencode($settings->testDataset));
                                   
      $this->assertEquals($wsq->getStatus(), "200", "Debugging information: ".var_export($wsq, TRUE));                                       
      $this->assertFalse(readDataset(), "Dataset still existing...");

      unset($wsq);
      unset($settings);
    }                     
    
    public function  testCreateDatasetNoDatasetUriSpecified() {
      
      $settings = new Config();  
      
      $wsq = new WebServiceQuerier($settings->endpointUrl . "dataset/delete/", 
                                   "get", 
                                   "text/xml",
                                   "uri=");
                                   
      $this->assertEquals($wsq->getStatus(), "400", "Debugging information: ".var_export($wsq, TRUE));                                       
      $this->assertEquals($wsq->getStatusMessage(), "Bad Request", "Debugging information: ".var_export($wsq, TRUE));
      $this->assertEquals($wsq->error->id, "WS-DATASET-DELETE-200", "Debugging information: ".var_export($wsq, TRUE));                  
      
      unset($wsq);
      unset($settings);
    }
    
    public function  testCreateDatasetInvalidDatasetIRI() {
      
      $settings = new Config();  
      
      $wsq = new WebServiceQuerier($settings->endpointUrl . "dataset/delete/", 
                                   "get", 
                                   "text/xml",
                                   "uri=" . urlencode($settings->testDataset) . "<>");
                                   
      $this->assertEquals($wsq->getStatus(), "400", "Debugging information: ".var_export($wsq, TRUE));                                       
      $this->assertEquals($wsq->getStatusMessage(), "Bad Request", "Debugging information: ".var_export($wsq, TRUE));
      $this->assertEquals($wsq->error->id, "WS-DATASET-DELETE-201", "Debugging information: ".var_export($wsq, TRUE));                                       
      
      unset($wsq);
      unset($settings);
    }
  }

  
?>