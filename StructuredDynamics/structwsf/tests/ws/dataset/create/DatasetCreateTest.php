<?php

  namespace StructuredDynamics\osf\tests\ws\dataset\create;
  
  use StructuredDynamics\osf\framework\WebServiceQuerier;
  use StructuredDynamics\osf\php\api\framework\CRUDPermission;
  use StructuredDynamics\osf\php\api\ws\dataset\create\DatasetCreateQuery;
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
  $loader_api_framework = new \SplClassLoader('StructuredDynamics\osf\php\api\framework', realpath("../../../"));
  $loader_api_framework->register();  
 
  // Load the \framework namespace where all the supporting (utility) code is located
  $loader_framework = new \SplClassLoader('StructuredDynamics\osf\framework', realpath("../../../"));
  $loader_framework->register(); 
  
  ini_set("memory_limit","256M");
  set_time_limit(3600);

  $settings = new Config(); 
  
  class DatasetCreateTest extends \PHPUnit_Framework_TestCase {
    
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
                                   "&webservices=" . urlencode(implode(';', $settings->datasetWebservices)) .
                                   "&interface=". urlencode($settings->datasetCreateInterface) .
                                   "&version=". urlencode($settings->datasetCreateInterfaceVersion) .
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
                                   "&webservices=" . urlencode(implode(';', $settings->datasetWebservices)) .
                                   "&interface=". urlencode($settings->datasetCreateInterface) .
                                   "&version=". urlencode($settings->datasetCreateInterfaceVersion) .
                                   "&globalPermissions=" . urlencode("True;True;True;True"));
                                   
      $this->assertEquals($wsq->getStatus(), "405", "Debugging information: ".var_export($wsq, TRUE));                                       
      $this->assertEquals($wsq->getStatusMessage(), "Method Not Allowed", "Debugging information: ".var_export($wsq, TRUE));          
      
      unset($wsq);
      unset($settings);
    }    
    
    public function testValidInterfaceVersion() {
      
      $settings = new Config();  

      // Make sure the dataset doesn't exists
      $this->assertTrue(utilities\deleteDataset(), "Can't delete the dataset, check the /dataset/delete/ endpoint first...");
            
      $datasetCreate = new DatasetCreateQuery($settings->endpointUrl);
      
      $datasetCreate->uri($settings->testDataset)
                    ->title("This is a testing dataset")
                    ->description("This is a testing dataset")
                    ->creator("http://test.com/user/bob/")
                    ->sourceInterface($settings->datasetCreateInterface)
                    ->sourceInterfaceVersion($settings->datasetCreateInterfaceVersion)
                    ->targetWebservices($settings->datasetWebservices);
      
      $permissions = new CRUDPermission(TRUE, TRUE, TRUE, TRUE);
      
      $datasetCreate->globalPermissions($permissions);
      
      $datasetCreate->send();
                           
      $this->assertEquals($datasetCreate->getStatus(), "200", "Debugging information: ".var_export($datasetCreate, TRUE));                                       

      utilities\deleteDataset();

      unset($datasetCreate);
      unset($settings);  
    }
    
    
    public function testInvalidInterfaceVersion() {
      
      $settings = new Config();  

      // Make sure the dataset doesn't exists
      $this->assertTrue(utilities\deleteDataset(), "Can't delete the dataset, check the /dataset/delete/ endpoint first...");
            
      $datasetCreate = new DatasetCreateQuery($settings->endpointUrl);
      
      $datasetCreate->uri($settings->testDataset)
                    ->title("This is a testing dataset")
                    ->description("This is a testing dataset")
                    ->creator("http://test.com/user/bob/")
                    ->sourceInterface($settings->datasetCreateInterface)
                    ->sourceInterfaceVersion("667.4")
                    ->targetWebservices($settings->datasetWebservices);
      
      $permissions = new CRUDPermission(TRUE, TRUE, TRUE, TRUE);
      
      $datasetCreate->globalPermissions($permissions);
      
      $datasetCreate->send();
                           
      $this->assertEquals($datasetCreate->getStatus(), "400", "Debugging information: ".var_export($datasetCreate, TRUE));                                       
      $this->assertEquals($datasetCreate->getStatusMessage(), "Bad Request", "Debugging information: ".var_export($datasetCreate, TRUE));
      $this->assertEquals($datasetCreate->error->id, "WS-DATASET-CREATE-302", "Debugging information: ".var_export($datasetCreate, TRUE));                                       

      utilities\deleteDataset();

      unset($datasetCreate);
      unset($settings);                             
    }    
    
    
    //
    // Test existing interface
    //
    
    public function testInterfaceExists() {
      
      $settings = new Config();  

      // Make sure the dataset doesn't exists
      $this->assertTrue(utilities\deleteDataset(), "Can't delete the dataset, check the /dataset/delete/ endpoint first...");
            
      $datasetCreate = new DatasetCreateQuery($settings->endpointUrl);
      
      $datasetCreate->uri($settings->testDataset)
                    ->title("This is a testing dataset")
                    ->description("This is a testing dataset")
                    ->creator("http://test.com/user/bob/")
                    ->sourceInterface($settings->datasetCreateInterface)
                    ->sourceInterfaceVersion($settings->datasetCreateInterfaceVersion)
                    ->targetWebservices($settings->datasetWebservices);
      
      $permissions = new CRUDPermission(TRUE, TRUE, TRUE, TRUE);
      
      $datasetCreate->globalPermissions($permissions)
                    ->send();
                           
      $this->assertEquals($datasetCreate->getStatus(), "200", "Debugging information: ".var_export($datasetCreate, TRUE));                                       

      utilities\deleteDataset();

      unset($datasetCreate);
      unset($settings);
    }  
    
    //
    // Test unexisting interface
    //
    
    public function testInterfaceNotExisting() {
      
      $settings = new Config();  

      // Make sure the dataset doesn't exists
      $this->assertTrue(utilities\deleteDataset(), "Can't delete the dataset, check the /dataset/delete/ endpoint first...");
            
      $datasetCreate = new DatasetCreateQuery($settings->endpointUrl);
      
      $datasetCreate->uri($settings->testDataset)
                    ->title("This is a testing dataset")
                    ->description("This is a testing dataset")
                    ->creator("http://test.com/user/bob/")
                    ->sourceInterface("default-not-existing")
                    ->sourceInterfaceVersion($settings->datasetCreateInterfaceVersion)
                    ->targetWebservices($settings->datasetWebservices);
      
      $permissions = new CRUDPermission(TRUE, TRUE, TRUE, TRUE);
      
      $datasetCreate->globalPermissions($permissions);
      
      $datasetCreate->send();
                           
      $this->assertEquals($datasetCreate->getStatus(), "400", "Debugging information: ".var_export($datasetCreate, TRUE));                                       
      $this->assertEquals($datasetCreate->getStatusMessage(), "Bad Request", "Debugging information: ".var_export($datasetCreate, TRUE));
      $this->assertEquals($datasetCreate->error->id, "WS-DATASET-CREATE-301", "Debugging information: ".var_export($datasetCreate, TRUE));                                       

      utilities\deleteDataset();

      unset($datasetCreate);
      unset($settings);
    }     
    
    public function  testCreateDataset() {
      
      $settings = new Config();  
      
      // Make sure the dataset doesn't exists
      $this->assertTrue(utilities\deleteDataset(), "Can't delete the dataset, check the /dataset/delete/ endpoint first...");
            
      $datasetCreate = new DatasetCreateQuery($settings->endpointUrl);
      
      $datasetCreate->uri($settings->testDataset)
                    ->title("This is a testing dataset")
                    ->description("This is a testing dataset")
                    ->creator("http://test.com/user/bob/")
                    ->sourceInterface($settings->datasetCreateInterface)
                    ->sourceInterfaceVersion($settings->datasetCreateInterfaceVersion)
                    ->targetWebservices($settings->datasetWebservices);
      
      $permissions = new CRUDPermission(TRUE, TRUE, TRUE, TRUE);
      
      $datasetCreate->globalPermissions($permissions);
      
      $datasetCreate->send();
                                               
      $this->assertEquals($datasetCreate->getStatus(), "200", "Debugging information: ".var_export($datasetCreate, TRUE));                                       
      
      utilities\deleteDataset();
      
      unset($datasetCreate);
      unset($settings);
    }  
    
    public function  testCreateDatasetValidateCreatedContent() {
      
      $settings = new Config();  
      
      // Make sure the dataset doesn't exists
      $this->assertTrue(utilities\deleteDataset(), "Can't delete the dataset, check the /dataset/delete/ endpoint first...");
      
      // Create the new dataset
      $datasetCreate = new DatasetCreateQuery($settings->endpointUrl);
      
      $datasetCreate->uri($settings->testDataset)
                    ->title("This is a testing dataset")
                    ->description("This is a testing dataset")
                    ->sourceInterface($settings->datasetCreateInterface)
                    ->sourceInterfaceVersion($settings->datasetCreateInterfaceVersion)
                    ->creator("http://test.com/user/bob/")
                    ->targetWebservices($settings->datasetWebservices);
      
      $permissions = new CRUDPermission(TRUE, TRUE, TRUE, TRUE);
      
      $datasetCreate->globalPermissions($permissions);
      
      $datasetCreate->send();      
                                   
      $this->assertEquals($datasetCreate->getStatus(), "200", "Debugging information: ".var_export($datasetCreate, TRUE));    
                                         
      $resultset = utilities\readDataset();
      
      if(!$resultset)
      {
        $this->assertEquals(TRUE, FALSE, "Can't read the dataset, check the /dataset/read/ endpoint first...");
      }
      else
      {
        $this->assertXmlStringEqualsXmlString($settings->datasetReadStructXMLResultset, $resultset);
      }
      
      utilities\deleteDataset();

      unset($datasetCreate);
      unset($settings);
    }                   
    
    public function  testCreateDatasetDuplicated() {
      
      $settings = new Config();  
      
      $this->assertTrue(utilities\createDataset(), "Can't create the dataset, check the /dataset/create/ endpoint first...");
      
      $datasetCreate = new DatasetCreateQuery($settings->endpointUrl);
      
      $datasetCreate->uri($settings->testDataset)
                    ->title("This is a testing dataset")
                    ->description("This is a testing dataset")
                    ->sourceInterface($settings->datasetCreateInterface)
                    ->sourceInterfaceVersion($settings->datasetCreateInterfaceVersion)
                    ->creator("http://test.com/user/bob/")
                    ->targetWebservices($settings->datasetWebservices);
      
      $permissions = new CRUDPermission(TRUE, TRUE, TRUE, TRUE);
      
      $datasetCreate->globalPermissions($permissions);
      
      $datasetCreate->send();       
                                   
      $this->assertEquals($datasetCreate->getStatus(), "400", "Debugging information: ".var_export($datasetCreate, TRUE));                                       
      $this->assertEquals($datasetCreate->getStatusMessage(), "Bad Request", "Debugging information: ".var_export($datasetCreate, TRUE));
      $this->assertEquals($datasetCreate->error->id, "WS-DATASET-CREATE-202", "Debugging information: ".var_export($datasetCreate, TRUE));                                       
      
      utilities\deleteDataset($settings->testDataset);
      
      unset($datasetCreate);
      unset($settings);
    }      
    
    public function  testCreateDatasetNoDatasetUriSpecified() {
      
      $settings = new Config();  
      
      $datasetCreate = new DatasetCreateQuery($settings->endpointUrl);
      
      $datasetCreate->uri("")
                    ->title("This is a testing dataset")
                    ->description("This is a testing dataset")
                    ->sourceInterface($settings->datasetCreateInterface)
                    ->sourceInterfaceVersion($settings->datasetCreateInterfaceVersion)
                    ->creator("http://test.com/user/bob/")
                    ->targetWebservices($settings->datasetWebservices);
      
      $permissions = new CRUDPermission(TRUE, TRUE, TRUE, TRUE);
      
      $datasetCreate->globalPermissions($permissions);
      
      $datasetCreate->send();       
                                   
      $this->assertEquals($datasetCreate->getStatus(), "400", "Debugging information: ".var_export($datasetCreate, TRUE));                                       
      $this->assertEquals($datasetCreate->getStatusMessage(), "Bad Request", "Debugging information: ".var_export($datasetCreate, TRUE));
      $this->assertEquals($datasetCreate->error->id, "WS-DATASET-CREATE-200", "Debugging information: ".var_export($datasetCreate, TRUE));                                       
      
      unset($datasetCreate);
      unset($settings);
    }
    
    public function  testCreateDatasetInvalidDatasetIRI() {
      
      $settings = new Config();  
      
      $datasetCreate = new DatasetCreateQuery($settings->endpointUrl);
      
      $datasetCreate->uri($settings->testDataset."<>")
                    ->title("This is a testing dataset")
                    ->description("This is a testing dataset")
                    ->sourceInterface($settings->datasetCreateInterface)
                    ->sourceInterfaceVersion($settings->datasetCreateInterfaceVersion)
                    ->creator("http://test.com/user/bob/")
                    ->targetWebservices($settings->datasetWebservices);
      
      $permissions = new CRUDPermission(TRUE, TRUE, TRUE, TRUE);
      
      $datasetCreate->globalPermissions($permissions);
      
      $datasetCreate->send();       
                                   
      $this->assertEquals($datasetCreate->getStatus(), "400", "Debugging information: ".var_export($datasetCreate, TRUE));                                       
      $this->assertEquals($datasetCreate->getStatusMessage(), "Bad Request", "Debugging information: ".var_export($datasetCreate, TRUE));
      $this->assertEquals($datasetCreate->error->id, "WS-DATASET-CREATE-203", "Debugging information: ".var_export($datasetCreate, TRUE));                                       
      
      unset($datasetCreate);
      unset($settings);
    }
    
    public function  testCreateDatasetInvalidCreatorIRI() {
      
      $settings = new Config();  
      
      $datasetCreate = new DatasetCreateQuery($settings->endpointUrl);
      
      $datasetCreate->uri($settings->testDataset)
                    ->title("This is a testing dataset")
                    ->description("This is a testing dataset")
                    ->sourceInterface($settings->datasetCreateInterface)
                    ->sourceInterfaceVersion($settings->datasetCreateInterfaceVersion)
                    ->creator("http://test.com/user/bob/"."<>")
                    ->targetWebservices($settings->datasetWebservices);
      
      $permissions = new CRUDPermission(TRUE, TRUE, TRUE, TRUE);
      
      $datasetCreate->globalPermissions($permissions);
      
      $datasetCreate->send();       
                                   
      $this->assertEquals($datasetCreate->getStatus(), "400", "Debugging information: ".var_export($datasetCreate, TRUE));                                       
      $this->assertEquals($datasetCreate->getStatusMessage(), "Bad Request", "Debugging information: ".var_export($datasetCreate, TRUE));
      $this->assertEquals($datasetCreate->error->id, "WS-DATASET-CREATE-204", "Debugging information: ".var_export($datasetCreate, TRUE));                                       
      
      unset($datasetCreate);
      unset($settings);
    } 
               
    public function  testCreateDatasetEmptyCreatorIRI() {
      
      $settings = new Config();  
      
      $datasetCreate = new DatasetCreateQuery($settings->endpointUrl);
      
      $datasetCreate->uri($settings->testDataset)
                    ->title("This is a testing dataset")
                    ->description("This is a testing dataset")
                    ->sourceInterface($settings->datasetCreateInterface)
                    ->sourceInterfaceVersion($settings->datasetCreateInterfaceVersion)
                    ->creator("")
                    ->targetWebservices($settings->datasetWebservices);
      
      $permissions = new CRUDPermission(TRUE, TRUE, TRUE, TRUE);
      
      $datasetCreate->globalPermissions($permissions);
      
      $datasetCreate->send(); 
                                   
      $this->assertEquals($datasetCreate->getStatus(), "200", "Debugging information: ".var_export($datasetCreate, TRUE));                                       
      
      utilities\deleteDataset($settings->testDataset);
      
      unset($datasetCreate);
      unset($settings);
    } 
  }

  
?>