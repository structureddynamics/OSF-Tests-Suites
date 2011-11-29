<?php
  include_once("../tests/Config.php");
  include_once("../tests/validators.php");
  
  ini_set("memory_limit","256M");
  set_time_limit(3600);

  $settings = new Config(); 
  
  // Database connectivity procedures
  include_once($settings->structwsfInstanceFolder . "framework/WebServiceQuerier.php");
  include_once("../tests/utilities.php");
  
  class DatasetUpdateTest extends PHPUnit_Framework_TestCase {
    
    static private $outputs = array();

    public function testWrongEndpointUrl() {
      
      $settings = new Config();          
      
      $wsq = new WebServiceQuerier($settings->endpointUrl . "dataset/update/" . "wrong", 
                                   "post", 
                                   "text/xml",
                                   "uri=" . urlencode($settings->testDataset) .
                                   "&title=" . urlencode("This is a testing dataset".$settings->datasetUpdateString) .
                                   "&description=" . urlencode("This is a testing dataset".$settings->datasetUpdateString) .
                                   "&contributors=" . urlencode("http://test.com/user/bob".$settings->datasetUpdateString."/") .
                                   "&modified=" . urlencode(date("Y-m-d")));
                   
      $this->assertEquals($wsq->getStatus(), "404", "Debugging information: ".var_export($wsq, TRUE));                                       
      $this->assertEquals($wsq->getStatusMessage(), "Not Found", "Debugging information: ".var_export($wsq, TRUE));
      
      unset($wsq);
      unset($settings);
    }
    
    public function testWrongEndpointMethodGet() {
      
      $settings = new Config();  
      
      $wsq = new WebServiceQuerier($settings->endpointUrl . "dataset/update/", 
                                   "get", 
                                   "text/xml",
                                   "uri=" . urlencode($settings->testDataset) .
                                   "&title=" . urlencode("This is a testing dataset".$settings->datasetUpdateString) .
                                   "&description=" . urlencode("This is a testing dataset".$settings->datasetUpdateString) .
                                   "&contributors=" . urlencode("http://test.com/user/bob".$settings->datasetUpdateString."/") .
                                   "&modified=" . urlencode(date("Y-m-d")));
                                   
      $this->assertEquals($wsq->getStatus(), "405", "Debugging information: ".var_export($wsq, TRUE));                                       
      $this->assertEquals($wsq->getStatusMessage(), "Method Not Allowed", "Debugging information: ".var_export($wsq, TRUE));          
      
      unset($wsq);
      unset($settings);
    }    
    
    public function  testUpdateDataset() {
      
      $settings = new Config();  
      
      deleteDataset();
      
      $this->assertTrue(createDataset(), "Can't create the dataset, check the /dataset/create/ endpoint first...");
            
      // Create the new dataset
      $wsq = new WebServiceQuerier($settings->endpointUrl . "dataset/update/", 
                                   "post", 
                                   "text/xml",
                                   "uri=" . urlencode($settings->testDataset) .
                                   "&title=" . urlencode("This is a testing dataset".$settings->datasetUpdateString) .
                                   "&description=" . urlencode("This is a testing dataset".$settings->datasetUpdateString) .
                                   "&contributors=" . urlencode("http://test.com/user/bob".$settings->datasetUpdateString."/") .
                                   "&modified=" . urlencode(date("Y-m-d")));
                                   
      $this->assertEquals($wsq->getStatus(), "200", "Debugging information: ".var_export($wsq, TRUE));                                       
      
      deleteDataset();
      
      unset($wsq);
      unset($settings);
    }  
    

    public function  testUpdateDatasetValidateUpdatedContent() {
      
      $settings = new Config();  
      
      deleteDataset();
      
      $this->assertTrue(createDataset(), "Can't create the dataset, check the /dataset/create/ endpoint first...");
      
      // Create the new dataset
      $wsq = new WebServiceQuerier($settings->endpointUrl . "dataset/update/", 
                                   "post", 
                                   "text/xml",
                                   "uri=" . urlencode($settings->testDataset) .
                                   "&title=" . urlencode("This is a testing dataset".$settings->datasetUpdateString) .
                                   "&description=" . urlencode("This is a testing dataset".$settings->datasetUpdateString) .
                                   "&contributors=" . urlencode("http://test.com/user/bob".$settings->datasetUpdateString."/") .
                                   "&modified=" . urlencode(date("Y-m-d")));
                                   
      $this->assertEquals($wsq->getStatus(), "200", "Debugging information: ".var_export($wsq, TRUE));                                       
      $resultset = readDataset();
      
      if(!$resultset)
      {
        $this->assertEquals(TRUE, FALSE, "Can't read the dataset, check the /dataset/read/ endpoint first...");
      }
      else
      {
        $this->assertXmlStringEqualsXmlString($settings->datasetUpdatedReadStructXMLResultset, $resultset);
      }
      
      deleteDataset();

      unset($wsq);
      unset($settings);
    }                      
  
    public function  testUpdateDatasetMissingDatasetIRI() {
      
      $settings = new Config();  
      
      deleteDataset();
      
      $this->assertTrue(createDataset(), "Can't create the dataset, check the /dataset/create/ endpoint first...");
            
      $wsq = new WebServiceQuerier($settings->endpointUrl . "dataset/update/", 
                                   "post", 
                                   "text/xml",
                                   "uri=" . 
                                   "&title=" . urlencode("This is a testing dataset".$settings->datasetUpdateString) .
                                   "&description=" . urlencode("This is a testing dataset".$settings->datasetUpdateString) .
                                   "&contributors=" . urlencode("http://test.com/user/bob".$settings->datasetUpdateString."/") .
                                   "&modified=" . urlencode(date("Y-m-d")));
                                   
      $this->assertEquals($wsq->getStatus(), "400", "Debugging information: ".var_export($wsq, TRUE));                                       
      $this->assertEquals($wsq->getStatusMessage(), "Bad Request", "Debugging information: ".var_export($wsq, TRUE));
      $this->assertEquals($wsq->error->id, "WS-DATASET-UPDATE-200", "Debugging information: ".var_export($wsq, TRUE));                                       

      deleteDataset();
      
      unset($wsq);
      unset($settings);
    }
  
    public function  testUpdateDatasetInvalidIRI() {
      
      $settings = new Config();  
      
      deleteDataset();
      
      $this->assertTrue(createDataset(), "Can't create the dataset, check the /dataset/create/ endpoint first...");
      
      $wsq = new WebServiceQuerier($settings->endpointUrl . "dataset/update/", 
                                   "post", 
                                   "text/xml",
                                   "uri=" . urlencode($settings->testDataset) . "<>" .
                                   "&title=" . urlencode("This is a testing dataset".$settings->datasetUpdateString) .
                                   "&description=" . urlencode("This is a testing dataset".$settings->datasetUpdateString) .
                                   "&contributors=" . urlencode("http://test.com/user/bob".$settings->datasetUpdateString."/") .
                                   "&modified=" . urlencode(date("Y-m-d")));
                                   
      $this->assertEquals($wsq->getStatus(), "400", "Debugging information: ".var_export($wsq, TRUE));                                       
      $this->assertEquals($wsq->getStatusMessage(), "Bad Request", "Debugging information: ".var_export($wsq, TRUE));
      $this->assertEquals($wsq->error->id, "WS-DATASET-UPDATE-203", "Debugging information: ".var_export($wsq, TRUE));                                       

      deleteDataset();
            
      unset($wsq);
      unset($settings);
    }  
    
    public function  testUpdateDatasetDatasetIRINotExisting() {
      
      $settings = new Config();  
      
      deleteDataset();
      
      $this->assertTrue(createDataset(), "Can't create the dataset, check the /dataset/create/ endpoint first...");      
      
      $wsq = new WebServiceQuerier($settings->endpointUrl . "dataset/update/", 
                                   "post", 
                                   "text/xml",
                                   "uri=" . urlencode($settings->testDataset) . "missing/" .
                                   "&title=" . urlencode("This is a testing dataset".$settings->datasetUpdateString) .
                                   "&description=" . urlencode("This is a testing dataset".$settings->datasetUpdateString) .
                                   "&contributors=" . urlencode("http://test.com/user/bob".$settings->datasetUpdateString."/") .
                                   "&modified=" . urlencode(date("Y-m-d")));
                                   
      $this->assertEquals($wsq->getStatus(), "400", "Debugging information: ".var_export($wsq, TRUE));                                       
      $this->assertEquals($wsq->getStatusMessage(), "Bad Request", "Debugging information: ".var_export($wsq, TRUE));
      $this->assertEquals($wsq->error->id, "WS-DATASET-UPDATE-202", "Debugging information: ".var_export($wsq, TRUE));                                       

      deleteDataset();
      
      unset($wsq);
      unset($settings);
    } 
    
    public function  testUpdateDatasetInvalidContributorsIRI() {
      
      $settings = new Config();  
      
      deleteDataset();
      
      $this->assertTrue(createDataset(), "Can't create the dataset, check the /dataset/create/ endpoint first...");
      
      $wsq = new WebServiceQuerier($settings->endpointUrl . "dataset/update/", 
                                   "post", 
                                   "text/xml",
                                   "uri=" . urlencode($settings->testDataset) .
                                   "&title=" . urlencode("This is a testing dataset".$settings->datasetUpdateString) .
                                   "&description=" . urlencode("This is a testing dataset".$settings->datasetUpdateString) .
                                   "&contributors=" . urlencode("http://test.com/user/bob".$settings->datasetUpdateString."/".";"."http://test.com/user/bob".$settings->datasetUpdateString."/2/"."<>") .
                                   "&modified=" . urlencode(date("Y-m-d")));
                                   
      $this->assertEquals($wsq->getStatus(), "400", "Debugging information: ".var_export($wsq, TRUE));                                       
      $this->assertEquals($wsq->getStatusMessage(), "Bad Request", "Debugging information: ".var_export($wsq, TRUE));
      $this->assertEquals($wsq->error->id, "WS-DATASET-UPDATE-204", "Debugging information: ".var_export($wsq, TRUE));                                       

      deleteDataset();
      
      unset($wsq);
      unset($settings);
    }         
  }

  
?>