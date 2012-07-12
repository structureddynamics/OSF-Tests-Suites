<?php

  namespace StructuredDynamics\structwsf\tests\ws\dataset\update;
  
  use StructuredDynamics\structwsf\framework\WebServiceQuerier;
  use StructuredDynamics\structwsf\php\api\ws\dataset\update\DatasetUpdateQuery;
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
  
  class DatasetUpdateTest extends \PHPUnit_Framework_TestCase {
    
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
                                   "&modified=" . urlencode(date("Y-n-j")));
                   
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
                                   "&modified=" . urlencode(date("Y-n-j")));
                                   
      $this->assertEquals($wsq->getStatus(), "405", "Debugging information: ".var_export($wsq, TRUE));                                       
      $this->assertEquals($wsq->getStatusMessage(), "Method Not Allowed", "Debugging information: ".var_export($wsq, TRUE));          
      
      unset($wsq);
      unset($settings);
    }    
    
    public function testValidInterfaceVersion() {
      
      $settings = new Config();  

      utilities\deleteDataset();
      
      $this->assertTrue(utilities\createDataset(), "Can't create the dataset, check the /dataset/create/ endpoint first...");
            
      $datasetUpdate = new DatasetUpdateQuery($settings->endpointUrl);
      
      $datasetUpdate->uri($settings->testDataset);
      
      $datasetUpdate->title("This is a testing dataset".$settings->datasetUpdateString);
      
      $datasetUpdate->description("This is a testing dataset".$settings->datasetUpdateString);
      
      $datasetUpdate->modified(date("Y-n-j"));
      
      $datasetUpdate->contributors(array("http://test.com/user/bob".$settings->datasetUpdateString."/"));
      
      $datasetUpdate->sourceInterface("default");
      
      $datasetUpdate->sourceInterfaceVersion($settings->datasetUpdateInterfaceVersion);
      
      $datasetUpdate->send();
                           
      $this->assertEquals($datasetUpdate->getStatus(), "200", "Debugging information: ".var_export($datasetUpdate, TRUE));                                       

      utilities\deleteDataset();

      unset($datasetUpdate);
      unset($settings);
    }
    
    
    public function testInvalidInterfaceVersion() {
      
      $settings = new Config();  

      utilities\deleteDataset();
      
      $this->assertTrue(utilities\createDataset(), "Can't create the dataset, check the /dataset/create/ endpoint first...");
            
      $datasetUpdate = new DatasetUpdateQuery($settings->endpointUrl);
      
      $datasetUpdate->uri($settings->testDataset);
      
      $datasetUpdate->title("This is a testing dataset".$settings->datasetUpdateString);
      
      $datasetUpdate->description("This is a testing dataset".$settings->datasetUpdateString);
      
      $datasetUpdate->modified(date("Y-n-j"));
      
      $datasetUpdate->contributors(array("http://test.com/user/bob".$settings->datasetUpdateString."/"));
      
      $datasetUpdate->sourceInterface("default");
      
      $datasetUpdate->sourceInterfaceVersion("667.4");
      
      $datasetUpdate->send();
                           
      $this->assertEquals($datasetUpdate->getStatus(), "400", "Debugging information: ".var_export($datasetUpdate, TRUE));                                       
      $this->assertEquals($datasetUpdate->getStatusMessage(), "Bad Request", "Debugging information: ".var_export($datasetUpdate, TRUE));
      $this->assertEquals($datasetUpdate->error->id, "WS-DATASET-UPDATE-305", "Debugging information: ".var_export($datasetUpdate, TRUE));                                       

      utilities\deleteDataset();

      unset($datasetUpdate);
      unset($settings);
    }    
    
    //
    // Test existing interface
    //
    
    public function testInterfaceExists() {
      
      $settings = new Config();  

      utilities\deleteDataset();
      
      $this->assertTrue(utilities\createDataset(), "Can't create the dataset, check the /dataset/create/ endpoint first...");
            
      $datasetUpdate = new DatasetUpdateQuery($settings->endpointUrl);
      
      $datasetUpdate->uri($settings->testDataset);
      
      $datasetUpdate->title("This is a testing dataset".$settings->datasetUpdateString);
      
      $datasetUpdate->description("This is a testing dataset".$settings->datasetUpdateString);
      
      $datasetUpdate->modified(date("Y-n-j"));
      
      $datasetUpdate->contributors(array("http://test.com/user/bob".$settings->datasetUpdateString."/"));
      
      $datasetUpdate->sourceInterface("default");
      
      $datasetUpdate->send();
                           
      $this->assertEquals($datasetUpdate->getStatus(), "200", "Debugging information: ".var_export($datasetUpdate, TRUE));                                       

      utilities\deleteDataset();

      unset($datasetUpdate);
      unset($settings);
    }  
    
    //
    // Test unexisting interface
    //
    
    public function testInterfaceNotExisting() {
      
      $settings = new Config();  

      utilities\deleteDataset();
      
      $this->assertTrue(utilities\createDataset(), "Can't create the dataset, check the /dataset/create/ endpoint first...");
            
      $datasetUpdate = new DatasetUpdateQuery($settings->endpointUrl);
      
      $datasetUpdate->uri($settings->testDataset);
      
      $datasetUpdate->title("This is a testing dataset".$settings->datasetUpdateString);
      
      $datasetUpdate->description("This is a testing dataset".$settings->datasetUpdateString);
      
      $datasetUpdate->modified(date("Y-n-j"));
      
      $datasetUpdate->contributors(array("http://test.com/user/bob".$settings->datasetUpdateString."/"));
      
      $datasetUpdate->sourceInterface("default-not-existing");
      
      $datasetUpdate->send();
                           
      $this->assertEquals($datasetUpdate->getStatus(), "400", "Debugging information: ".var_export($datasetUpdate, TRUE));                                       
      $this->assertEquals($datasetUpdate->getStatusMessage(), "Bad Request", "Debugging information: ".var_export($datasetUpdate, TRUE));
      $this->assertEquals($datasetUpdate->error->id, "WS-DATASET-UPDATE-304", "Debugging information: ".var_export($datasetUpdate, TRUE));                                       

      utilities\deleteDataset();

      unset($datasetUpdate);
      unset($settings);
    }     
    
    public function  testUpdateDataset() {
      
      $settings = new Config();  
      
      utilities\deleteDataset();
      
      $this->assertTrue(utilities\createDataset(), "Can't create the dataset, check the /dataset/create/ endpoint first...");
            
      $datasetUpdate = new DatasetUpdateQuery($settings->endpointUrl);
      
      $datasetUpdate->uri($settings->testDataset);
      
      $datasetUpdate->title("This is a testing dataset".$settings->datasetUpdateString);
      
      $datasetUpdate->description("This is a testing dataset".$settings->datasetUpdateString);
      
      $datasetUpdate->modified(date("Y-n-j"));
      
      $datasetUpdate->contributors(array("http://test.com/user/bob".$settings->datasetUpdateString."/"));
      
      $datasetUpdate->send();
                                   
      $this->assertEquals($datasetUpdate->getStatus(), "200", "Debugging information: ".var_export($datasetUpdate, TRUE));                                       
      
      utilities\deleteDataset();
      
      unset($datasetUpdate);
      unset($settings);
    }  
    

    public function  testUpdateDatasetValidateUpdatedContent() {
      
      $settings = new Config();  
      
      utilities\deleteDataset();
      
      $this->assertTrue(utilities\createDataset(), "Can't create the dataset, check the /dataset/create/ endpoint first...");
      
      $datasetUpdate = new DatasetUpdateQuery($settings->endpointUrl);
      
      $datasetUpdate->uri($settings->testDataset);
      
      $datasetUpdate->title("This is a testing dataset".$settings->datasetUpdateString);
      
      $datasetUpdate->description("This is a testing dataset".$settings->datasetUpdateString);
      
      $datasetUpdate->modified(date("Y-n-j"));
      
      $datasetUpdate->contributors(array("http://test.com/user/bob".$settings->datasetUpdateString."/"));
      
      $datasetUpdate->send();      
                                   
      $this->assertEquals($datasetUpdate->getStatus(), "200", "Debugging information: ".var_export($datasetUpdate, TRUE));                                       
      $resultset = utilities\readDataset();
      
      if(!$resultset)
      {
        $this->assertEquals(TRUE, FALSE, "Can't read the dataset, check the /dataset/read/ endpoint first...");
      }
      else
      {
        $this->assertXmlStringEqualsXmlString($settings->datasetUpdatedReadStructXMLResultset, $resultset);
      }
      
      utilities\deleteDataset();

      unset($datasetUpdate);
      unset($settings);
    }                      
  
    public function  testUpdateDatasetMissingDatasetIRI() {
      
      $settings = new Config();  
      
      utilities\deleteDataset();
      
      $this->assertTrue(utilities\createDataset(), "Can't create the dataset, check the /dataset/create/ endpoint first...");
      
      $datasetUpdate = new DatasetUpdateQuery($settings->endpointUrl);
      
      $datasetUpdate->uri("");
      
      $datasetUpdate->title("This is a testing dataset".$settings->datasetUpdateString);
      
      $datasetUpdate->description("This is a testing dataset".$settings->datasetUpdateString);
      
      $datasetUpdate->modified(date("Y-n-j"));
      
      $datasetUpdate->contributors(array("http://test.com/user/bob".$settings->datasetUpdateString."/"));
      
      $datasetUpdate->send();      
                                   
      $this->assertEquals($datasetUpdate->getStatus(), "400", "Debugging information: ".var_export($datasetUpdate, TRUE));                                       
      $this->assertEquals($datasetUpdate->getStatusMessage(), "Bad Request", "Debugging information: ".var_export($datasetUpdate, TRUE));
      $this->assertEquals($datasetUpdate->error->id, "WS-DATASET-UPDATE-200", "Debugging information: ".var_export($datasetUpdate, TRUE));                                       

      utilities\deleteDataset();
      
      unset($datasetUpdate);
      unset($settings);
    }
  
    public function  testUpdateDatasetInvalidIRI() {
      
      $settings = new Config();  
      
      utilities\deleteDataset();
      
      $this->assertTrue(utilities\createDataset(), "Can't create the dataset, check the /dataset/create/ endpoint first...");

      $datasetUpdate = new DatasetUpdateQuery($settings->endpointUrl);
      
      $datasetUpdate->uri($settings->testDataset . "<>");
      
      $datasetUpdate->title("This is a testing dataset".$settings->datasetUpdateString);
      
      $datasetUpdate->description("This is a testing dataset".$settings->datasetUpdateString);
      
      $datasetUpdate->modified(date("Y-n-j"));
      
      $datasetUpdate->contributors(array("http://test.com/user/bob".$settings->datasetUpdateString."/"));
      
      $datasetUpdate->send();      
                                   
      $this->assertEquals($datasetUpdate->getStatus(), "400", "Debugging information: ".var_export($datasetUpdate, TRUE));                                       
      $this->assertEquals($datasetUpdate->getStatusMessage(), "Bad Request", "Debugging information: ".var_export($datasetUpdate, TRUE));
      $this->assertEquals($datasetUpdate->error->id, "WS-DATASET-UPDATE-203", "Debugging information: ".var_export($datasetUpdate, TRUE));                                       

      utilities\deleteDataset();
            
      unset($datasetUpdate);
      unset($settings);
    }  
    
    public function  testUpdateDatasetDatasetIRINotExisting() {
      
      $settings = new Config();  
      
      utilities\deleteDataset();
      
      $this->assertTrue(utilities\createDataset(), "Can't create the dataset, check the /dataset/create/ endpoint first...");      
      
      $datasetUpdate = new DatasetUpdateQuery($settings->endpointUrl);
      
      $datasetUpdate->uri($settings->testDataset . "missing/");
      
      $datasetUpdate->title("This is a testing dataset".$settings->datasetUpdateString);
      
      $datasetUpdate->description("This is a testing dataset".$settings->datasetUpdateString);
      
      $datasetUpdate->modified(date("Y-n-j"));
      
      $datasetUpdate->contributors(array("http://test.com/user/bob".$settings->datasetUpdateString."/"));
      
      $datasetUpdate->send();      
                                   
      $this->assertEquals($datasetUpdate->getStatus(), "400", "Debugging information: ".var_export($datasetUpdate, TRUE));                                       
      $this->assertEquals($datasetUpdate->getStatusMessage(), "Bad Request", "Debugging information: ".var_export($datasetUpdate, TRUE));
      $this->assertEquals($datasetUpdate->error->id, "WS-DATASET-UPDATE-202", "Debugging information: ".var_export($datasetUpdate, TRUE));                                       

      utilities\deleteDataset();
      
      unset($datasetUpdate);
      unset($settings);
    } 
    
    public function  testUpdateDatasetInvalidContributorsIRI() {
      
      $settings = new Config();  
      
      utilities\deleteDataset();
      
      $this->assertTrue(utilities\createDataset(), "Can't create the dataset, check the /dataset/create/ endpoint first...");
      
      $datasetUpdate = new DatasetUpdateQuery($settings->endpointUrl);
      
      $datasetUpdate->uri($settings->testDataset);
      
      $datasetUpdate->title("This is a testing dataset".$settings->datasetUpdateString);
      
      $datasetUpdate->description("This is a testing dataset".$settings->datasetUpdateString);
      
      $datasetUpdate->modified(date("Y-n-j"));
      
      $datasetUpdate->contributors(array("http://test.com/user/bob".$settings->datasetUpdateString."/" . "<>"));
      
      $datasetUpdate->send();      
                                   
      $this->assertEquals($datasetUpdate->getStatus(), "400", "Debugging information: ".var_export($datasetUpdate, TRUE));                                       
      $this->assertEquals($datasetUpdate->getStatusMessage(), "Bad Request", "Debugging information: ".var_export($datasetUpdate, TRUE));
      $this->assertEquals($datasetUpdate->error->id, "WS-DATASET-UPDATE-204", "Debugging information: ".var_export($datasetUpdate, TRUE));                                       

      utilities\deleteDataset();
      
      unset($datasetUpdate);
      unset($settings);
    }         
  }

  
?>