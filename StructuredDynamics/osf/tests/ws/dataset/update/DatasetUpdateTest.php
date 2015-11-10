<?php

  namespace StructuredDynamics\osf\tests\ws\dataset\update;
  
  use StructuredDynamics\osf\framework\WebServiceQuerier;
  use StructuredDynamics\osf\php\api\ws\dataset\update\DatasetUpdateQuery;
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
                                   "&interface=". urlencode($settings->datasetUpdateInterface) .
                                   "&version=". urlencode($settings->datasetUpdateInterfaceVersion) .
                                   "&modified=" . urlencode(date("Y-n-j")),
                                   $settings->applicationID,
                                   $settings->apiKey,
                                   $settings->userID);
                   
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
                                   "&interface=". urlencode($settings->datasetUpdateInterface) .
                                   "&version=". urlencode($settings->datasetUpdateInterfaceVersion) .
                                   "&modified=" . urlencode(date("Y-n-j")),
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

      utilities\deleteDataset();
      
      $this->assertTrue(utilities\createDataset(), "Can't create the dataset, check the /dataset/create/ endpoint first...");
            
      $datasetUpdate = new DatasetUpdateQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
      
      $datasetUpdate->uri($settings->testDataset)
                    ->title("This is a testing dataset".$settings->datasetUpdateString)
                    ->description("This is a testing dataset".$settings->datasetUpdateString)
                    ->modified(date("Y-n-j"))
                    ->contributors(array("http://test.com/user/bob".$settings->datasetUpdateString."/"))
                    ->sourceInterface($settings->datasetUpdateInterface)
                    ->sourceInterfaceVersion($settings->datasetUpdateInterfaceVersion)
                    ->send();
                           
      $this->assertEquals($datasetUpdate->getStatus(), "200", "Debugging information: ".var_export($datasetUpdate, TRUE));                                       

      utilities\deleteDataset();

      unset($datasetUpdate);
      unset($settings);
    }
    
    
    public function testInvalidInterfaceVersion() {
      
      $settings = new Config();  

      utilities\deleteDataset();
      
      $this->assertTrue(utilities\createDataset(), "Can't create the dataset, check the /dataset/create/ endpoint first...");
            
      $datasetUpdate = new DatasetUpdateQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
      
      $datasetUpdate->uri($settings->testDataset)
                    ->title("This is a testing dataset".$settings->datasetUpdateString)
                    ->description("This is a testing dataset".$settings->datasetUpdateString)
                    ->modified(date("Y-n-j"))
                    ->contributors(array("http://test.com/user/bob".$settings->datasetUpdateString."/"))
                    ->sourceInterface($settings->datasetUpdateInterface)
                    ->sourceInterfaceVersion("667.4")
                    ->send();
                           
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
            
      $datasetUpdate = new DatasetUpdateQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
      
      $datasetUpdate->uri($settings->testDataset)
                    ->title("This is a testing dataset".$settings->datasetUpdateString)
                    ->description("This is a testing dataset".$settings->datasetUpdateString)
                    ->modified(date("Y-n-j"))
                    ->contributors(array("http://test.com/user/bob".$settings->datasetUpdateString."/"))
                    ->sourceInterface($settings->datasetUpdateInterface)
                    ->sourceInterfaceVersion($settings->datasetUpdateInterfaceVersion)
                    ->send();
                           
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
            
      $datasetUpdate = new DatasetUpdateQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
      
      $datasetUpdate->uri($settings->testDataset)
                    ->title("This is a testing dataset".$settings->datasetUpdateString)
                    ->description("This is a testing dataset".$settings->datasetUpdateString)
                    ->modified(date("Y-n-j"))
                    ->contributors(array("http://test.com/user/bob".$settings->datasetUpdateString."/"))
                    ->sourceInterface("default-not-existing")
                    ->sourceInterfaceVersion($settings->datasetUpdateInterfaceVersion)
                    ->send();
                           
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
            
      $datasetUpdate = new DatasetUpdateQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
      
      $datasetUpdate->uri($settings->testDataset)
                    ->title("This is a testing dataset".$settings->datasetUpdateString)
                    ->description("This is a testing dataset".$settings->datasetUpdateString)
                    ->modified(date("Y-n-j"))
                    ->contributors(array("http://test.com/user/bob".$settings->datasetUpdateString."/"))
                    ->sourceInterface($settings->datasetUpdateInterface)
                    ->sourceInterfaceVersion($settings->datasetUpdateInterfaceVersion)
                    ->send();
                                   
      $this->assertEquals($datasetUpdate->getStatus(), "200", "Debugging information: ".var_export($datasetUpdate, TRUE));                                       
      
      utilities\deleteDataset();
      
      unset($datasetUpdate);
      unset($settings);
    }  
    

    public function  testUpdateDatasetValidateUpdatedContent() {
      
      $settings = new Config();  
      
      utilities\deleteDataset();
      
      $this->assertTrue(utilities\createDataset(), "Can't create the dataset, check the /dataset/create/ endpoint first...");
      
      $datasetUpdate = new DatasetUpdateQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
      
      $datasetUpdate->uri($settings->testDataset)
                    ->title("This is a testing dataset".$settings->datasetUpdateString)
                    ->description("This is a testing dataset".$settings->datasetUpdateString)
                    ->modified(date("Y-n-j"))
                    ->contributors(array("http://test.com/user/bob".$settings->datasetUpdateString."/"))
                    ->sourceInterface($settings->datasetUpdateInterface)
                    ->sourceInterfaceVersion($settings->datasetUpdateInterfaceVersion)
                    ->send();      
                                   
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
      
      $datasetUpdate = new DatasetUpdateQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
      
      $datasetUpdate->uri("")
                    ->title("This is a testing dataset".$settings->datasetUpdateString)
                    ->description("This is a testing dataset".$settings->datasetUpdateString)
                    ->modified(date("Y-n-j"))
                    ->contributors(array("http://test.com/user/bob".$settings->datasetUpdateString."/"))
                    ->sourceInterface($settings->datasetUpdateInterface)
                    ->sourceInterfaceVersion($settings->datasetUpdateInterfaceVersion)
                    ->send();      
                                   
      $this->assertEquals($datasetUpdate->getStatus(), "403", "Debugging information: ".var_export($datasetUpdate, TRUE));                                       
      $this->assertEquals($datasetUpdate->getStatusMessage(), "Forbidden", "Debugging information: ".var_export($datasetUpdate, TRUE));
      $this->assertEquals($datasetUpdate->error->id, "WS-AUTH-VALIDATION-104", "Debugging information: ".var_export($datasetUpdate, TRUE));                                       

      utilities\deleteDataset();
      
      unset($datasetUpdate);
      unset($settings);
    }
  
    public function  testUpdateDatasetInvalidIRI() {
      
      $settings = new Config();  
      
      utilities\deleteDataset();
      
      $this->assertTrue(utilities\createDataset(), "Can't create the dataset, check the /dataset/create/ endpoint first...");

      $datasetUpdate = new DatasetUpdateQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
      
      $datasetUpdate->uri($settings->testDataset . "<>")
                    ->title("This is a testing dataset".$settings->datasetUpdateString)
                    ->description("This is a testing dataset".$settings->datasetUpdateString)
                    ->modified(date("Y-n-j"))
                    ->contributors(array("http://test.com/user/bob".$settings->datasetUpdateString."/"))
                    ->sourceInterface($settings->datasetUpdateInterface)
                    ->sourceInterfaceVersion($settings->datasetUpdateInterfaceVersion)
                    ->send();      
                                                                      
      // We are getting a validation error 102 because the dataset URI is invalid, and so the SPARQL query used
      // by the validation procedure returns an error. So this is the expected behavior.
      $this->assertEquals($datasetUpdate->getStatus(), "500", "Debugging information: ".var_export($datasetUpdate, TRUE));                                       
      $this->assertEquals($datasetUpdate->getStatusMessage(), "Forbidden", "Debugging information: ".var_export($datasetUpdate, TRUE));
      $this->assertEquals($datasetUpdate->error->id, "WS-AUTH-VALIDATION-102", "Debugging information: ".var_export($datasetUpdate, TRUE));                                       
      
      /*                             
      $this->assertEquals($datasetUpdate->getStatus(), "400", "Debugging information: ".var_export($datasetUpdate, TRUE));                                       
      $this->assertEquals($datasetUpdate->getStatusMessage(), "Bad Request", "Debugging information: ".var_export($datasetUpdate, TRUE));
      $this->assertEquals($datasetUpdate->error->id, "WS-DATASET-UPDATE-203", "Debugging information: ".var_export($datasetUpdate, TRUE));                                       
      */
      utilities\deleteDataset();
            
      unset($datasetUpdate);
      unset($settings);
    }  
    
    public function  testUpdateDatasetDatasetIRINotExisting() {
      
      $settings = new Config();  
      
      utilities\deleteDataset();
      
      $this->assertTrue(utilities\createDataset(), "Can't create the dataset, check the /dataset/create/ endpoint first...");      
      
      $datasetUpdate = new DatasetUpdateQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
      
      $datasetUpdate->uri($settings->testDataset . "missing/")
                    ->title("This is a testing dataset".$settings->datasetUpdateString)
                    ->description("This is a testing dataset".$settings->datasetUpdateString)
                    ->modified(date("Y-n-j"))
                    ->contributors(array("http://test.com/user/bob".$settings->datasetUpdateString."/"))
                    ->sourceInterface($settings->datasetUpdateInterface)
                    ->sourceInterfaceVersion($settings->datasetUpdateInterfaceVersion)
                    ->send();      
                                   
      $this->assertEquals($datasetUpdate->getStatus(), "403", "Debugging information: ".var_export($datasetUpdate, TRUE));                                       
      $this->assertEquals($datasetUpdate->getStatusMessage(), "Forbidden", "Debugging information: ".var_export($datasetUpdate, TRUE));
      $this->assertEquals($datasetUpdate->error->id, "WS-AUTH-VALIDATION-104", "Debugging information: ".var_export($datasetUpdate, TRUE));                                       

      utilities\deleteDataset();
      
      unset($datasetUpdate);
      unset($settings);
    } 
    
    public function  testUpdateDatasetInvalidContributorsIRI() {
      
      $settings = new Config();  
      
      utilities\deleteDataset();
      
      $this->assertTrue(utilities\createDataset(), "Can't create the dataset, check the /dataset/create/ endpoint first...");
      
      $datasetUpdate = new DatasetUpdateQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
      
      $datasetUpdate->uri($settings->testDataset)
                    ->title("This is a testing dataset".$settings->datasetUpdateString)
                    ->description("This is a testing dataset".$settings->datasetUpdateString)
                    ->modified(date("Y-n-j"))
                    ->contributors(array("http://test.com/user/bob".$settings->datasetUpdateString."/" . "<>"))
                    ->sourceInterface($settings->datasetUpdateInterface)
                    ->sourceInterfaceVersion($settings->datasetUpdateInterfaceVersion)
                    ->send();      
                                   
      $this->assertEquals($datasetUpdate->getStatus(), "400", "Debugging information: ".var_export($datasetUpdate, TRUE));                                       
      $this->assertEquals($datasetUpdate->getStatusMessage(), "Bad Request", "Debugging information: ".var_export($datasetUpdate, TRUE));
      $this->assertEquals($datasetUpdate->error->id, "WS-DATASET-UPDATE-204", "Debugging information: ".var_export($datasetUpdate, TRUE));                                       

      utilities\deleteDataset();
      
      unset($datasetUpdate);
      unset($settings);
    }         
  }

  
?>