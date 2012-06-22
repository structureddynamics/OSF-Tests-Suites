<?php
  include_once("../tests/Config.php");
  include_once("../tests/validators.php");
  
  ini_set("memory_limit","256M");
  set_time_limit(3600);

  $settings = new Config(); 
  
  // Database connectivity procedures
  include_once($settings->structwsfInstanceFolder . "framework/WebServiceQuerier.php");
  include_once("../tests/utilities.php");
  
  class DatasetCreateTest extends PHPUnit_Framework_TestCase {
    
    static private $outputs = array();

    public function testWrongEndpointUrl() {
      
      $settings = new Config();          
      
      $wsq = new WebServiceQuerier($settings->endpointUrl . "dataset/create/" . "wrong", 
                                   "post", 
                                   "text/xml",
                                   "uri=" . urlencode($settings->testDataset) .
                                   "&title=" . urlencode("This is a testing dataset") .
                                   "&description=" . urlencode("This is a testing dataset") .
                                   "&creator=" . urlencode("http://test.com/user/bob/") .
                                   "&webservices=" . urlencode($settings->datasetWebservices) .
                                   "&globalPermissions=" . urlencode("True;True;True;True"));
                   
      $this->assertEquals($wsq->getStatus(), "404", "Debugging information: ".var_export($wsq, TRUE));                                       
      $this->assertEquals($wsq->getStatusMessage(), "Not Found", "Debugging information: ".var_export($wsq, TRUE));
      
      unset($wsq);
      unset($settings);
    }
    
    public function testWrongEndpointMethodGet() {
      
      $settings = new Config();  
      
      $wsq = new WebServiceQuerier($settings->endpointUrl . "dataset/create/", 
                                   "get", 
                                   "text/xml",
                                   "uri=" . urlencode($settings->testDataset) .
                                   "&title=" . urlencode("This is a testing dataset") .
                                   "&description=" . urlencode("This is a testing dataset") .
                                   "&creator=" . urlencode("http://test.com/user/bob/") .
                                   "&webservices=" . urlencode($settings->datasetWebservices) .
                                   "&globalPermissions=" . urlencode("True;True;True;True"));
                                   
      $this->assertEquals($wsq->getStatus(), "405", "Debugging information: ".var_export($wsq, TRUE));                                       
      $this->assertEquals($wsq->getStatusMessage(), "Method Not Allowed", "Debugging information: ".var_export($wsq, TRUE));          
      
      unset($wsq);
      unset($settings);
    }    
    
    public function  testCreateDataset() {
      
      $settings = new Config();  
      
      // Make sure the dataset doesn't exists
      $this->assertTrue(deleteDataset(), "Can't delete the dataset, check the /dataset/delete/ endpoint first...");
            
      // Create the new dataset
      $wsq = new WebServiceQuerier($settings->endpointUrl . "dataset/create/", 
                                   "post", 
                                   "text/xml",
                                   "uri=" . urlencode($settings->testDataset) .
                                   "&title=" . urlencode("This is a testing dataset") .
                                   "&description=" . urlencode("This is a testing dataset") .
                                   "&creator=" . urlencode("http://test.com/user/bob/") .
                                   "&webservices=" . urlencode($settings->datasetWebservices) .
                                   "&globalPermissions=" . urlencode("True;True;True;True"));
                                   
      $this->assertEquals($wsq->getStatus(), "200", "Debugging information: ".var_export($wsq, TRUE));                                       
      
      deleteDataset();
      
      unset($wsq);
      unset($settings);
    }  
    
    public function  testCreateDatasetValidateCreatedContent() {
      
      $settings = new Config();  
      
      // Make sure the dataset doesn't exists
      $this->assertTrue(deleteDataset(), "Can't delete the dataset, check the /dataset/delete/ endpoint first...");
      
      // Create the new dataset
      $wsq = new WebServiceQuerier($settings->endpointUrl . "dataset/create/", 
                                   "post", 
                                   "text/xml",
                                   "uri=" . urlencode($settings->testDataset) .
                                   "&title=" . urlencode("This is a testing dataset") .
                                   "&description=" . urlencode("This is a testing dataset") .
                                   "&creator=" . urlencode("http://test.com/user/bob/") .
                                   "&webservices=" . urlencode($settings->datasetWebservices) .
                                   "&globalPermissions=" . urlencode("True;True;True;True"));
                                   
      $this->assertEquals($wsq->getStatus(), "200", "Debugging information: ".var_export($wsq, TRUE));                                       
      $resultset = readDataset();
      
      if(!$resultset)
      {
        $this->assertEquals(TRUE, FALSE, "Can't read the dataset, check the /dataset/read/ endpoint first...");
      }
      else
      {
        $this->assertXmlStringEqualsXmlString($settings->datasetReadStructXMLResultset, $resultset);
      }
      
      deleteDataset();

      unset($wsq);
      unset($settings);
    }                   
    
    public function  testCreateDatasetDuplicated() {
      
      $settings = new Config();  
      
      $this->assertTrue(createDataset(), "Can't create the dataset, check the /dataset/create/ endpoint first...");
      
      $wsq = new WebServiceQuerier($settings->endpointUrl . "dataset/create/", 
                                   "post", 
                                   "text/xml",
                                   "uri=" . urlencode($settings->testDataset) .
                                   "&title=" . urlencode("This is a testing dataset") .
                                   "&description=" . urlencode("This is a testing dataset") .
                                   "&creator=" . urlencode("http://test.com/user/bob/") .
                                   "&webservices=" . urlencode($settings->datasetWebservices) .
                                   "&globalPermissions=" . urlencode("True;True;True;True"));
                                   
      $this->assertEquals($wsq->getStatus(), "400", "Debugging information: ".var_export($wsq, TRUE));                                       
      $this->assertEquals($wsq->getStatusMessage(), "Bad Request", "Debugging information: ".var_export($wsq, TRUE));
      $this->assertEquals($wsq->error->id, "WS-DATASET-CREATE-202", "Debugging information: ".var_export($wsq, TRUE));                                       
      
      deleteDataset($settings->testDataset);
      
      unset($wsq);
      unset($settings);
    }      
    
    public function  testCreateDatasetNoDatasetUriSpecified() {
      
      $settings = new Config();  
      
      $wsq = new WebServiceQuerier($settings->endpointUrl . "dataset/create/", 
                                   "post", 
                                   "text/xml",
                                   "uri=" . 
                                   "&title=" . urlencode("This is a testing dataset") .
                                   "&description=" . urlencode("This is a testing dataset") .
                                   "&creator=" . urlencode("http://test.com/user/bob/") .
                                   "&webservices=" . urlencode($settings->datasetWebservices) .
                                   "&globalPermissions=" . urlencode("True;True;True;True"));
                                   
      $this->assertEquals($wsq->getStatus(), "400", "Debugging information: ".var_export($wsq, TRUE));                                       
      $this->assertEquals($wsq->getStatusMessage(), "Bad Request", "Debugging information: ".var_export($wsq, TRUE));
      $this->assertEquals($wsq->error->id, "WS-DATASET-CREATE-200", "Debugging information: ".var_export($wsq, TRUE));                                       
      
      unset($wsq);
      unset($settings);
    }
    
    public function  testCreateDatasetInvalidDatasetIRI() {
      
      $settings = new Config();  
      
      $wsq = new WebServiceQuerier($settings->endpointUrl . "dataset/create/", 
                                   "post", 
                                   "text/xml",
                                   "uri=" . urlencode($settings->testDataset) . "<>" .
                                   "&title=" . urlencode("This is a testing dataset") .
                                   "&description=" . urlencode("This is a testing dataset") .
                                   "&creator=" . urlencode("http://test.com/user/bob/") .
                                   "&webservices=" . urlencode($settings->datasetWebservices) .
                                   "&globalPermissions=" . urlencode("True;True;True;True"));
                                   
      $this->assertEquals($wsq->getStatus(), "400", "Debugging information: ".var_export($wsq, TRUE));                                       
      $this->assertEquals($wsq->getStatusMessage(), "Bad Request", "Debugging information: ".var_export($wsq, TRUE));
      $this->assertEquals($wsq->error->id, "WS-DATASET-CREATE-203", "Debugging information: ".var_export($wsq, TRUE));                                       
      
      unset($wsq);
      unset($settings);
    }
    
    public function  testCreateDatasetInvalidCreatorIRI() {
      
      $settings = new Config();  
      
      $wsq = new WebServiceQuerier($settings->endpointUrl . "dataset/create/", 
                                   "post", 
                                   "text/xml",
                                   "uri=" . urlencode($settings->testDataset) .
                                   "&title=" . urlencode("This is a testing dataset") .
                                   "&description=" . urlencode("This is a testing dataset") .
                                   "&creator=" . urlencode("http://test.com/user/bob/") . "<>" . 
                                   "&webservices=" . urlencode($settings->datasetWebservices) .
                                   "&globalPermissions=" . urlencode("True;True;True;True"));
                                   
      $this->assertEquals($wsq->getStatus(), "400", "Debugging information: ".var_export($wsq, TRUE));                                       
      $this->assertEquals($wsq->getStatusMessage(), "Bad Request", "Debugging information: ".var_export($wsq, TRUE));
      $this->assertEquals($wsq->error->id, "WS-DATASET-CREATE-204", "Debugging information: ".var_export($wsq, TRUE));                                       
      
      unset($wsq);
      unset($settings);
    } 
               
    public function  testCreateDatasetEmptyCreatorIRI() {
      
      $settings = new Config();  
      
      $wsq = new WebServiceQuerier($settings->endpointUrl . "dataset/create/", 
                                   "post", 
                                   "text/xml",
                                   "uri=" . urlencode($settings->testDataset) .
                                   "&title=" . urlencode("This is a testing dataset") .
                                   "&description=" . urlencode("This is a testing dataset") .
                                   "&creator=" .
                                   "&webservices=" . urlencode($settings->datasetWebservices) .
                                   "&globalPermissions=" . urlencode("True;True;True;True"));
                                   
      $this->assertEquals($wsq->getStatus(), "200", "Debugging information: ".var_export($wsq, TRUE));                                       
      
      deleteDataset($settings->testDataset);
      
      unset($wsq);
      unset($settings);
    } 
  }

  
?>