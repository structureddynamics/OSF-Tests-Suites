<?php

  namespace StructuredDynamics\structwsf\tests\ws\crud\create;
  
  use StructuredDynamics\structwsf\framework\WebServiceQuerier;
  use StructuredDynamics\structwsf\php\api\ws\crud\create\CrudCreateQuery;
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
  
  class CrudCreateTest extends \PHPUnit_Framework_TestCase {
    
    static private $outputs = array();

    public function testWrongEndpointUrl() {
      
      $settings = new Config();          
      
      $wsq = new WebServiceQuerier($settings->endpointUrl . "crud/create/" . "wrong", 
                                   "post", 
                                   "text/xml",
                                   "&document=" . urlencode(file_get_contents($settings->contentDir.'crud_create.n3')) .
                                   "&mime=" . urlencode("application/rdf+n3") .
                                   "&dataset=" . urlencode($settings->testDataset) .
                                   "&interface=". urlencode($settings->crudCreateInterface) .
                                   "&version=". urlencode($settings->crudCreateInterfaceVersion) .
                                   "&registered_ip=" . urlencode("Self"));        
                         
      $this->assertEquals($wsq->getStatus(), "404", "Debugging information: ".var_export($wsq, TRUE));                                       
      $this->assertEquals($wsq->getStatusMessage(), "Not Found", "Debugging information: ".var_export($wsq, TRUE));
      
      unset($wsq);
      unset($settings);
    }
    
    public function testWrongEndpointMethodGet() {
      
      $settings = new Config();  
      
      $wsq = new WebServiceQuerier($settings->endpointUrl . "crud/create/", 
                                   "get", 
                                   "text/xml",
                                   "&document=" . urlencode(file_get_contents($settings->contentDir.'crud_create.n3')) .
                                   "&mime=" . urlencode("application/rdf+n3") .
                                   "&dataset=" . urlencode($settings->testDataset) .
                                   "&interface=". urlencode($settings->crudCreateInterface) .
                                   "&version=". urlencode($settings->crudCreateInterfaceVersion) .
                                   "&registered_ip=" . urlencode("Self"));        
   

                                   
      $this->assertEquals($wsq->getStatus(), "405", "Debugging information: ".var_export($wsq, TRUE));                                       
      $this->assertEquals($wsq->getStatusMessage(), "Method Not Allowed", "Debugging information: ".var_export($wsq, TRUE));          
      
      unset($wsq);
      unset($settings);
    }    
    
    public function testValidInterfaceVersion() {
      
      $settings = new Config();  
      
      utilities\deleteDataset();
      
      $this->assertTrue(utilities\createDataset(), "Can't create the dataset, check the /dataset/create/ endpoint first...");
                 
      $crudCreate = new CrudCreateQuery($settings->endpointUrl);
      
      $crudCreate->dataset($settings->testDataset)
                 ->document(file_get_contents($settings->contentDir.'crud_create.n3'))
                 ->documentMimeIsRdfN3()
                 ->enableFullIndexationMode()
                 ->sourceInterface($settings->crudCreateInterface)
                 ->sourceInterfaceVersion($settings->crudCreateInterfaceVersion)
                 ->send();
                           
      $this->assertEquals($crudCreate->getStatus(), "200", "Debugging information: ".var_export($crudCreate, TRUE));                                       

      utilities\deleteDataset();

      unset($crudCreate);
      unset($settings);    
    }
    
    
    public function testInvalidInterfaceVersion() {
      
      $settings = new Config();  
      
      utilities\deleteDataset();
      
      $this->assertTrue(utilities\createDataset(), "Can't create the dataset, check the /dataset/create/ endpoint first...");
                 
      $crudCreate = new CrudCreateQuery($settings->endpointUrl);
      
      $crudCreate->dataset($settings->testDataset)
                 ->document(file_get_contents($settings->contentDir.'crud_create.n3'))
                 ->documentMimeIsRdfN3()
                 ->enableFullIndexationMode()
                 ->sourceInterface($settings->crudCreateInterface)
                 ->sourceInterfaceVersion("667.4")
                 ->send();
                           
      $this->assertEquals($crudCreate->getStatus(), "400", "Debugging information: ".var_export($crudCreate, TRUE));                                       
      $this->assertEquals($crudCreate->getStatusMessage(), "Bad Request", "Debugging information: ".var_export($crudCreate, TRUE));
      $this->assertEquals($crudCreate->error->id, "WS-CRUD-CREATE-308", "Debugging information: ".var_export($crudCreate, TRUE));                                       

      utilities\deleteDataset();

      unset($crudCreate);
      unset($settings);            
    }    
    
    public function testInterfaceExists() {
      
      $settings = new Config();  
      
      utilities\deleteDataset();
      
      $this->assertTrue(utilities\createDataset(), "Can't create the dataset, check the /dataset/create/ endpoint first...");
                 
      $crudCreate = new CrudCreateQuery($settings->endpointUrl);
      
      $crudCreate->dataset($settings->testDataset)
                 ->document(file_get_contents($settings->contentDir.'crud_create.n3'))
                 ->documentMimeIsRdfN3()
                 ->enableFullIndexationMode()
                 ->sourceInterface($settings->crudCreateInterface)
                 ->sourceInterfaceVersion($settings->crudCreateInterfaceVersion)
                 ->send();
                           
      $this->assertEquals($crudCreate->getStatus(), "200", "Debugging information: ".var_export($crudCreate, TRUE));                                       

      utilities\deleteDataset();

      unset($crudCreate);
      unset($settings);   
    }  
    
    public function testInterfaceNotExisting() {
      
      $settings = new Config();  
      
      utilities\deleteDataset();
      
      $this->assertTrue(utilities\createDataset(), "Can't create the dataset, check the /dataset/create/ endpoint first...");
                 
      $crudCreate = new CrudCreateQuery($settings->endpointUrl);
      
      $crudCreate->dataset($settings->testDataset)
                 ->document(file_get_contents($settings->contentDir.'crud_create.n3'))
                 ->documentMimeIsRdfN3()
                 ->enableFullIndexationMode()
                 ->sourceInterface("default-not-existing")
                 ->sourceInterfaceVersion($settings->crudCreateInterfaceVersion)
                 ->send();
                           
      $this->assertEquals($crudCreate->getStatus(), "400", "Debugging information: ".var_export($crudCreate, TRUE));                                       
      $this->assertEquals($crudCreate->getStatusMessage(), "Bad Request", "Debugging information: ".var_export($crudCreate, TRUE));
      $this->assertEquals($crudCreate->error->id, "WS-CRUD-CREATE-307", "Debugging information: ".var_export($crudCreate, TRUE));                                       

      utilities\deleteDataset();

      unset($crudCreate);
      unset($settings);           
    }     
    
    public function testCreateRecordRDFN3FullIndexationMode() {
      
      $settings = new Config();  
      
      utilities\deleteDataset();
      
      $this->assertTrue(utilities\createDataset(), "Can't create the dataset, check the /dataset/create/ endpoint first...");
                 
      $crudCreate = new CrudCreateQuery($settings->endpointUrl);
      
      $crudCreate->dataset($settings->testDataset)
                 ->document(file_get_contents($settings->contentDir.'crud_create.n3'))
                 ->documentMimeIsRdfN3()
                 ->enableFullIndexationMode()
                 ->sourceInterface($settings->crudCreateInterface)
                 ->sourceInterfaceVersion($settings->crudCreateInterfaceVersion)
                 ->send();
                           
      $this->assertEquals($crudCreate->getStatus(), "200", "Debugging information: ".var_export($crudCreate, TRUE));                                       

      utilities\deleteDataset();

      unset($crudCreate);
      unset($settings);   
    }

    public function testCreateRecordRDFXMLFullIndexationMode() {
      
      $settings = new Config();  
      
      utilities\deleteDataset();
      
      $this->assertTrue(utilities\createDataset(), "Can't create the dataset, check the /dataset/create/ endpoint first...");
                 
      $crudCreate = new CrudCreateQuery($settings->endpointUrl);
      
      $crudCreate->dataset($settings->testDataset)
                 ->document(file_get_contents($settings->contentDir.'crud_create.xml'))
                 ->documentMimeIsRdfXML()
                 ->enableFullIndexationMode()
                 ->sourceInterface($settings->crudCreateInterface)
                 ->sourceInterfaceVersion($settings->crudCreateInterfaceVersion)
                 ->send();
                           
      $this->assertEquals($crudCreate->getStatus(), "200", "Debugging information: ".var_export($crudCreate, TRUE));                                       

      utilities\deleteDataset();

      unset($crudCreate);
      unset($settings);   
    }
    
    public function testCreateRecordRDFN3SearchIndexationMode() {
      $settings = new Config();  
      
      utilities\deleteDataset();
      
      $this->assertTrue(utilities\createDataset(), "Can't create the dataset, check the /dataset/create/ endpoint first...");
                 
      $crudCreate = new CrudCreateQuery($settings->endpointUrl);
      
      $crudCreate->dataset($settings->testDataset)
                 ->document(file_get_contents($settings->contentDir.'crud_create.n3'))
                 ->documentMimeIsRdfN3()
                 ->enableTripleStoreIndexationMode()
                 ->sourceInterface($settings->crudCreateInterface)
                 ->sourceInterfaceVersion($settings->crudCreateInterfaceVersion)
                 ->send();
                           
      $this->assertEquals($crudCreate->getStatus(), "200", "Debugging information: ".var_export($crudCreate, TRUE));                   
                 
      $crudCreate = new CrudCreateQuery($settings->endpointUrl);
      
      $crudCreate->dataset($settings->testDataset)
                 ->document(file_get_contents($settings->contentDir.'crud_create.n3'))
                 ->documentMimeIsRdfN3()
                 ->enableSearchIndexationMode()
                 ->sourceInterface($settings->crudCreateInterface)
                 ->sourceInterfaceVersion($settings->crudCreateInterfaceVersion)
                 ->send();
                           
      $this->assertEquals($crudCreate->getStatus(), "200", "Debugging information: ".var_export($crudCreate, TRUE));                                       

      utilities\deleteDataset();

      unset($crudCreate);
      unset($settings);
    }

    public function testCreateRecordRDFXMLSearchIndexationMode() {
      $settings = new Config();  
      
      utilities\deleteDataset();
      
      $this->assertTrue(utilities\createDataset(), "Can't create the dataset, check the /dataset/create/ endpoint first...");

      $crudCreate = new CrudCreateQuery($settings->endpointUrl);
      
      $crudCreate->dataset($settings->testDataset)
                 ->document(file_get_contents($settings->contentDir.'crud_create.n3'))
                 ->documentMimeIsRdfN3()
                 ->enableTripleStoreIndexationMode()
                 ->sourceInterface($settings->crudCreateInterface)
                 ->sourceInterfaceVersion($settings->crudCreateInterfaceVersion)
                 ->send();
                           
      $this->assertEquals($crudCreate->getStatus(), "200", "Debugging information: ".var_export($crudCreate, TRUE));  
                 
      $crudCreate = new CrudCreateQuery($settings->endpointUrl);
      
      $crudCreate->dataset($settings->testDataset)
                 ->document(file_get_contents($settings->contentDir.'crud_create.xml'))
                 ->documentMimeIsRdfXML()
                 ->enableSearchIndexationMode()
                 ->sourceInterface($settings->crudCreateInterface)
                 ->sourceInterfaceVersion($settings->crudCreateInterfaceVersion)
                 ->send();
                           
      $this->assertEquals($crudCreate->getStatus(), "200", "Debugging information: ".var_export($crudCreate, TRUE));                                       

      utilities\deleteDataset();

      unset($crudCreate);
      unset($settings);
    }  
    
    public function testCreateRecordRDFN3TripleStoreIndexationMode() {
      
      $settings = new Config();  
      
      utilities\deleteDataset();
      
      $this->assertTrue(utilities\createDataset(), "Can't create the dataset, check the /dataset/create/ endpoint first...");
                 
      $crudCreate = new CrudCreateQuery($settings->endpointUrl);
      
      $crudCreate->dataset($settings->testDataset)
                 ->document(file_get_contents($settings->contentDir.'crud_create.n3'))
                 ->documentMimeIsRdfN3()
                 ->enableTripleStoreIndexationMode()
                 ->sourceInterface($settings->crudCreateInterface)
                 ->sourceInterfaceVersion($settings->crudCreateInterfaceVersion)
                 ->send();
                           
      $this->assertEquals($crudCreate->getStatus(), "200", "Debugging information: ".var_export($crudCreate, TRUE));                                       

      utilities\deleteDataset();

      unset($crudCreate);
      unset($settings);   
    }

    public function testCreateRecordRDFXMLTripleStoreIndexationMode() {
      
      $settings = new Config();  
      
      utilities\deleteDataset();
      
      $this->assertTrue(utilities\createDataset(), "Can't create the dataset, check the /dataset/create/ endpoint first...");
                 
      $crudCreate = new CrudCreateQuery($settings->endpointUrl);
      
      $crudCreate->dataset($settings->testDataset)
                 ->document(file_get_contents($settings->contentDir.'crud_create.xml'))
                 ->documentMimeIsRdfXML()
                 ->enableTripleStoreIndexationMode()
                 ->sourceInterface($settings->crudCreateInterface)
                 ->sourceInterfaceVersion($settings->crudCreateInterfaceVersion)
                 ->send();
                           
      $this->assertEquals($crudCreate->getStatus(), "200", "Debugging information: ".var_export($crudCreate, TRUE));                                       

      utilities\deleteDataset();

      unset($crudCreate);
      unset($settings);   
    }
    
    public function testNoRDFDocumentToIndex() {
      
      $settings = new Config();  
      
      utilities\deleteDataset();
      
      $this->assertTrue(utilities\createDataset(), "Can't create the dataset, check the /dataset/create/ endpoint first...");
                 
      $crudCreate = new CrudCreateQuery($settings->endpointUrl);
      
      $crudCreate->dataset($settings->testDataset)
                 ->document('')
                 ->documentMimeIsRdfXML()
                 ->enableFullIndexationMode()
                 ->sourceInterface($settings->crudCreateInterface)
                 ->sourceInterfaceVersion($settings->crudCreateInterfaceVersion)
                 ->send();
                           
      $this->assertEquals($crudCreate->getStatus(), "400", "Debugging information: ".var_export($crudCreate, TRUE));                                       
      $this->assertEquals($crudCreate->getStatusMessage(), "Bad Request", "Debugging information: ".var_export($crudCreate, TRUE));
      $this->assertEquals($crudCreate->error->id, "WS-CRUD-CREATE-200", "Debugging information: ".var_export($crudCreate, TRUE));                                       

      utilities\deleteDataset();

      unset($crudCreate);
      unset($settings);       
    }
    
    public function testNoDatasetSpecified() {
      
      $settings = new Config();  
      
      utilities\deleteDataset();
      
      $this->assertTrue(utilities\createDataset(), "Can't create the dataset, check the /dataset/create/ endpoint first...");
                 
      $crudCreate = new CrudCreateQuery($settings->endpointUrl);
      
      $crudCreate->dataset('')
                 ->document(file_get_contents($settings->contentDir.'crud_create.xml'))
                 ->documentMimeIsRdfXML()
                 ->enableFullIndexationMode()
                 ->sourceInterface($settings->crudCreateInterface)
                 ->sourceInterfaceVersion($settings->crudCreateInterfaceVersion)
                 ->send();
                           
      $this->assertEquals($crudCreate->getStatus(), "400", "Debugging information: ".var_export($crudCreate, TRUE));                                       
      $this->assertEquals($crudCreate->getStatusMessage(), "Bad Request", "Debugging information: ".var_export($crudCreate, TRUE));
      $this->assertEquals($crudCreate->error->id, "WS-CRUD-CREATE-202", "Debugging information: ".var_export($crudCreate, TRUE));                                       

      utilities\deleteDataset();

      unset($crudCreate);
      unset($settings);        
    }
    
    public function testCantParseRdfN3Document() {
      $settings = new Config();  
      
      utilities\deleteDataset();
      
      $this->assertTrue(utilities\createDataset(), "Can't create the dataset, check the /dataset/create/ endpoint first...");
                 
      $crudCreate = new CrudCreateQuery($settings->endpointUrl);
      
      $crudCreate->dataset($settings->testDataset)
                 ->document(file_get_contents($settings->contentDir.'crud_create.n3').'unparsable')
                 ->documentMimeIsRdfN3()
                 ->enableFullIndexationMode()
                 ->sourceInterface($settings->crudCreateInterface)
                 ->sourceInterfaceVersion($settings->crudCreateInterfaceVersion)
                 ->send();
                           
      $this->assertEquals($crudCreate->getStatus(), "400", "Debugging information: ".var_export($crudCreate, TRUE));                                       
      $this->assertEquals($crudCreate->getStatusMessage(), "Bad Request", "Debugging information: ".var_export($crudCreate, TRUE));
      $this->assertEquals($crudCreate->error->id, "WS-CRUD-CREATE-301", "Debugging information: ".var_export($crudCreate, TRUE));                                       

      utilities\deleteDataset();

      unset($crudCreate);
      unset($settings);         
    }
    
    public function testCantParseRdfXMLDocument() {
      $settings = new Config();  
      
      utilities\deleteDataset();
      
      $this->assertTrue(utilities\createDataset(), "Can't create the dataset, check the /dataset/create/ endpoint first...");
                 
      $crudCreate = new CrudCreateQuery($settings->endpointUrl);
      
      $crudCreate->dataset($settings->testDataset)
                 ->document(file_get_contents($settings->contentDir.'crud_create.xml').'unparsable')
                 ->documentMimeIsRdfXML()
                 ->enableFullIndexationMode()
                 ->sourceInterface($settings->crudCreateInterface)
                 ->sourceInterfaceVersion($settings->crudCreateInterfaceVersion)
                 ->send();
                           
      $this->assertEquals($crudCreate->getStatus(), "400", "Debugging information: ".var_export($crudCreate, TRUE));                                       
      $this->assertEquals($crudCreate->getStatusMessage(), "Bad Request", "Debugging information: ".var_export($crudCreate, TRUE));
      $this->assertEquals($crudCreate->error->id, "WS-CRUD-CREATE-301", "Debugging information: ".var_export($crudCreate, TRUE));                                       

      utilities\deleteDataset();

      unset($crudCreate);
      unset($settings);         
    }

    public function testRevisionExists() {
      $settings = new Config();  
      
      utilities\deleteRevisionedRecord();
      
      $this->assertTrue(utilities\createRevisionedRecord(), "Can't create the revision record");
                 
      $crudCreate = new CrudCreateQuery($settings->endpointUrl);

      $crudCreate->dataset($settings->testDataset)
                 ->document(file_get_contents($settings->contentDir.'crud_create.n3'))
                 ->documentMimeIsRdfN3()
                 ->enableFullIndexationMode()
                 ->sourceInterface($settings->crudCreateInterface)
                 ->sourceInterfaceVersion($settings->crudCreateInterfaceVersion)
                 ->send();
                           
      $this->assertEquals($crudCreate->getStatus(), "400", "Debugging information: ".var_export($crudCreate, TRUE));                                       
      $this->assertEquals($crudCreate->getStatusMessage(), "Bad Request", "Debugging information: ".var_export($crudCreate, TRUE));
      $this->assertEquals($crudCreate->error->id, "WS-CRUD-CREATE-312", "Debugging information: ".var_export($crudCreate, TRUE));                                       

      utilities\deleteRevisionedRecord();

      unset($crudCreate);
      unset($settings);       
    }

    public function testRecordExistsRDFN3FullIndexationMode() {      
      $settings = new Config();  
      
      utilities\deleteDataset();
      
      $this->assertTrue(utilities\createDataset(), "Can't create the dataset, check the /dataset/create/ endpoint first...");
                 
      $crudCreate = new CrudCreateQuery($settings->endpointUrl);
      
      $crudCreate->dataset($settings->testDataset)
                 ->document(file_get_contents($settings->contentDir.'crud_create.n3'))
                 ->documentMimeIsRdfN3()
                 ->enableFullIndexationMode()
                 ->sourceInterface($settings->crudCreateInterface)
                 ->sourceInterfaceVersion($settings->crudCreateInterfaceVersion)
                 ->send();
                           
      $this->assertEquals($crudCreate->getStatus(), "200", "Debugging information: ".var_export($crudCreate, TRUE));                                       

      // Create a second time
      $crudCreate = new CrudCreateQuery($settings->endpointUrl);
      
      $crudCreate->dataset($settings->testDataset)
                 ->document(file_get_contents($settings->contentDir.'crud_create.n3'))
                 ->documentMimeIsRdfN3()
                 ->enableFullIndexationMode()
                 ->sourceInterface($settings->crudCreateInterface)
                 ->sourceInterfaceVersion($settings->crudCreateInterfaceVersion)
                 ->send();
                           
      $this->assertEquals($crudCreate->getStatus(), "400", "Debugging information: ".var_export($crudCreate, TRUE));                                       
      $this->assertEquals($crudCreate->getStatusMessage(), "Bad Request", "Debugging information: ".var_export($crudCreate, TRUE));
      $this->assertEquals($crudCreate->error->id, "WS-CRUD-CREATE-312", "Debugging information: ".var_export($crudCreate, TRUE));                                       
      
      utilities\deleteDataset();

      unset($crudCreate);
      unset($settings);     
    }

    public function testRecordExistsRDFN3TripleStoreIndexationMode() {      
      $settings = new Config();  
      
      utilities\deleteDataset();
      
      $this->assertTrue(utilities\createDataset(), "Can't create the dataset, check the /dataset/create/ endpoint first...");
                 
      $crudCreate = new CrudCreateQuery($settings->endpointUrl);
      
      $crudCreate->dataset($settings->testDataset)
                 ->document(file_get_contents($settings->contentDir.'crud_create.n3'))
                 ->documentMimeIsRdfN3()
                 ->enableTripleStoreIndexationMode()
                 ->sourceInterface($settings->crudCreateInterface)
                 ->sourceInterfaceVersion($settings->crudCreateInterfaceVersion)
                 ->send();
                           
      $this->assertEquals($crudCreate->getStatus(), "200", "Debugging information: ".var_export($crudCreate, TRUE));                                       

      // Create a second time
      $crudCreate = new CrudCreateQuery($settings->endpointUrl);
      
      $crudCreate->dataset($settings->testDataset)
                 ->document(file_get_contents($settings->contentDir.'crud_create.n3'))
                 ->documentMimeIsRdfN3()
                 ->enableTripleStoreIndexationMode()
                 ->sourceInterface($settings->crudCreateInterface)
                 ->sourceInterfaceVersion($settings->crudCreateInterfaceVersion)
                 ->send();
                           
      $this->assertEquals($crudCreate->getStatus(), "400", "Debugging information: ".var_export($crudCreate, TRUE));                                       
      $this->assertEquals($crudCreate->getStatusMessage(), "Bad Request", "Debugging information: ".var_export($crudCreate, TRUE));
      $this->assertEquals($crudCreate->error->id, "WS-CRUD-CREATE-312", "Debugging information: ".var_export($crudCreate, TRUE));                                       
      
      utilities\deleteDataset();

      unset($crudCreate);
      unset($settings);     
    }
    
    public function testRecordExistsRDFN3SearchIndexationMode() {      
      $settings = new Config();  
      
      utilities\deleteDataset();
      
      $this->assertTrue(utilities\createDataset(), "Can't create the dataset, check the /dataset/create/ endpoint first...");
                 
      $crudCreate = new CrudCreateQuery($settings->endpointUrl);
      
      $crudCreate->dataset($settings->testDataset)
                 ->document(file_get_contents($settings->contentDir.'crud_create.n3'))
                 ->documentMimeIsRdfN3()
                 ->enableTripleStoreIndexationMode()
                 ->sourceInterface($settings->crudCreateInterface)
                 ->sourceInterfaceVersion($settings->crudCreateInterfaceVersion)
                 ->send();
                           
      $this->assertEquals($crudCreate->getStatus(), "200", "Debugging information: ".var_export($crudCreate, TRUE));                                       

      // Create a second time
      $crudCreate = new CrudCreateQuery($settings->endpointUrl);
      
      $crudCreate->dataset($settings->testDataset)
                 ->document(file_get_contents($settings->contentDir.'crud_create.n3'))
                 ->documentMimeIsRdfN3()
                 ->enableSearchIndexationMode()
                 ->sourceInterface($settings->crudCreateInterface)
                 ->sourceInterfaceVersion($settings->crudCreateInterfaceVersion)
                 ->send();
                           
      $this->assertEquals($crudCreate->getStatus(), "200", "Debugging information: ".var_export($crudCreate, TRUE));                                       
      
      utilities\deleteDataset();

      unset($crudCreate);
      unset($settings);     
    }    
    
    public function testRecordSearchIndexationModeUnpublishedRecord() {      
      $settings = new Config();  
      
      utilities\deleteDataset();
      
      $this->assertTrue(utilities\createDataset(), "Can't create the dataset, check the /dataset/create/ endpoint first...");
                 
      // Create a second time
      $crudCreate = new CrudCreateQuery($settings->endpointUrl);
      
      $crudCreate->dataset($settings->testDataset)
                 ->document(file_get_contents($settings->contentDir.'crud_create.n3'))
                 ->documentMimeIsRdfN3()
                 ->enableSearchIndexationMode()
                 ->sourceInterface($settings->crudCreateInterface)
                 ->sourceInterfaceVersion($settings->crudCreateInterfaceVersion)
                 ->send();
                           
      $this->assertEquals($crudCreate->getStatus(), "400", "Debugging information: ".var_export($crudCreate, TRUE));                                       
      $this->assertEquals($crudCreate->getStatusMessage(), "Bad Request", "Debugging information: ".var_export($crudCreate, TRUE));
      $this->assertEquals($crudCreate->error->id, "WS-CRUD-CREATE-313", "Debugging information: ".var_export($crudCreate, TRUE));                                       
      
      utilities\deleteDataset();

      unset($crudCreate);
      unset($settings);     
    }        
  }
  
  
?>