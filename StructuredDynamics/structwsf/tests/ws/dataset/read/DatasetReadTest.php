<?php

  namespace StructuredDynamics\structwsf\tests\ws\dataset\read;
  
  use StructuredDynamics\structwsf\framework\WebServiceQuerier;
  use StructuredDynamics\structwsf\php\api\ws\dataset\read\DatasetReadQuery;
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

  include_once($settings->structwsfInstanceFolder . "framework/ProcessorXML.php");
  include_once($settings->structwsfInstanceFolder . "framework/arc2/ARC2.php");
  
  class DatasetReadTest extends \PHPUnit_Framework_TestCase {
    
    static private $outputs = array();

    public function testWrongEndpointUrl() {
      
      $settings = new Config();          
      
      $wsq = new WebServiceQuerier($settings->endpointUrl . "dataset/read/" . "wrong", 
                                   "get", 
                                   "text/xml",
                                   "uri=" . urlencode($settings->testDataset) .
                                   "&interface=". urlencode($settings->datasetReadInterface) .
                                   "&version=". urlencode($settings->datasetReadInterfaceVersion) .
                                   "&meta=" . urlencode("True"));
                   
      $this->assertEquals($wsq->getStatus(), "404", "Debugging information: ".var_export($wsq, TRUE));                                       
      $this->assertEquals($wsq->getStatusMessage(), "Not Found", "Debugging information: ".var_export($wsq, TRUE));
      
      unset($wsq);
      unset($settings);
    }
    
    public function testWrongEndpointMethodPost() {
      
      $settings = new Config();  
      
      $wsq = new WebServiceQuerier($settings->endpointUrl . "dataset/read/", 
                                   "post", 
                                   "text/xml",
                                   "uri=" . urlencode($settings->testDataset) .
                                   "&interface=". urlencode($settings->datasetReadInterface) .
                                   "&version=". urlencode($settings->datasetReadInterfaceVersion) .
                                   "&meta=" . urlencode("True"));
                                   
      $this->assertEquals($wsq->getStatus(), "405", "Debugging information: ".var_export($wsq, TRUE));                                       
      $this->assertEquals($wsq->getStatusMessage(), "Method Not Allowed", "Debugging information: ".var_export($wsq, TRUE));          
      
      unset($wsq);
      unset($settings);
    }    
    
    public function testValidInterfaceVersion() {
      
      $settings = new Config();  

      utilities\deleteDataset();
      
      $this->assertTrue(utilities\createDataset(), "Can't create the dataset, check the /dataset/create/ endpoint first...");
            
      $datasetRead = new DatasetReadQuery($settings->endpointUrl);

      $datasetRead->mime("text/xml")
                  ->uri($settings->testDataset)
                  ->includeMeta()
                  ->sourceInterface($settings->datasetReadInterface)
                  ->sourceInterfaceVersion($settings->datasetReadInterfaceVersion)
                  ->send();
                           
      $this->assertEquals($datasetRead->getStatus(), "200", "Debugging information: ".var_export($datasetRead, TRUE));                                       

      utilities\deleteDataset();

      unset($datasetRead);
      unset($settings);  
    }
    
    
    public function testInvalidInterfaceVersion() {
      
      $settings = new Config();  

      utilities\deleteDataset();
      
      $this->assertTrue(utilities\createDataset(), "Can't create the dataset, check the /dataset/create/ endpoint first...");
            
      $datasetRead = new DatasetReadQuery($settings->endpointUrl);

      $datasetRead->mime("text/xml")
                  ->uri($settings->testDataset)
                  ->includeMeta()
                  ->sourceInterface($settings->datasetReadInterface)
                  ->sourceInterfaceVersion("667.4")
                  ->send();
                           
      $this->assertEquals($datasetRead->getStatus(), "400", "Debugging information: ".var_export($datasetRead, TRUE));                                       
      $this->assertEquals($datasetRead->getStatusMessage(), "Bad Request", "Debugging information: ".var_export($datasetRead, TRUE));
      $this->assertEquals($datasetRead->error->id, "WS-DATASET-READ-307", "Debugging information: ".var_export($datasetRead, TRUE));                                       

      utilities\deleteDataset();

      unset($datasetRead);
      unset($settings);
    }    
    
    //
    // Test existing interface
    //
    
    public function testInterfaceExists() {
      
      $settings = new Config();  

      utilities\deleteDataset();
      
      $this->assertTrue(utilities\createDataset(), "Can't create the dataset, check the /dataset/create/ endpoint first...");
            
      $datasetRead = new DatasetReadQuery($settings->endpointUrl);

      $datasetRead->mime("text/xml")
                  ->uri($settings->testDataset)
                  ->includeMeta()
                  ->sourceInterface($settings->datasetReadInterface)
                  ->sourceInterfaceVersion($settings->datasetReadInterfaceVersion)
                  ->send();
                           
      $this->assertEquals($datasetRead->getStatus(), "200", "Debugging information: ".var_export($datasetRead, TRUE));                                       

      utilities\deleteDataset();

      unset($datasetRead);
      unset($settings);
    }  
    
    //
    // Test unexisting interface
    //
    
    public function testInterfaceNotExisting() {
      
      $settings = new Config();  

      utilities\deleteDataset();
      
      $this->assertTrue(utilities\createDataset(), "Can't create the dataset, check the /dataset/create/ endpoint first...");
            
      $datasetRead = new DatasetReadQuery($settings->endpointUrl);

      $datasetRead->mime("text/xml")
                  ->uri($settings->testDataset)
                  ->includeMeta()
                  ->sourceInterface("default-not-existing")
                  ->sourceInterfaceVersion($settings->datasetReadInterfaceVersion)
                  ->send();
                           
      $this->assertEquals($datasetRead->getStatus(), "400", "Debugging information: ".var_export($datasetRead, TRUE));                                       
      $this->assertEquals($datasetRead->getStatusMessage(), "Bad Request", "Debugging information: ".var_export($datasetRead, TRUE));
      $this->assertEquals($datasetRead->error->id, "WS-DATASET-READ-306", "Debugging information: ".var_export($datasetRead, TRUE));                                       

      utilities\deleteDataset();

      unset($datasetRead);
      unset($settings);
    }     
    
    public function  testReadDataset_Serialization_TEXT_XML() {
      
      $settings = new Config();  
      
      utilities\deleteDataset();
      
      $this->assertTrue(utilities\createDataset(), "Can't create the dataset, check the /dataset/create/ endpoint first...");
            
      $datasetRead = new DatasetReadQuery($settings->endpointUrl);

      $datasetRead->mime("text/xml")
                  ->uri($settings->testDataset)
                  ->includeMeta()
                  ->sourceInterface($settings->datasetReadInterface)
                  ->sourceInterfaceVersion($settings->datasetReadInterfaceVersion)
                  ->send();
                                   
      $this->assertEquals($datasetRead->getStatus(), "200", "Debugging information: ".var_export($datasetRead, TRUE));                                       
      
      $this->assertXmlStringEqualsXmlString($settings->datasetReadStructXMLResultset, $datasetRead->getResultset());
      
      utilities\deleteDataset();
      
      unset($datasetRead);
      unset($settings);
    }
    
    public function  testReadDataset_Serialization_APPLICATION_JSON() {
      
      $settings = new Config();  
      
      utilities\deleteDataset();
      
      $this->assertTrue(utilities\createDataset(), "Can't create the dataset, check the /dataset/create/ endpoint first...");
            
      $datasetRead = new DatasetReadQuery($settings->endpointUrl);

      $datasetRead->mime("application/json")
                  ->uri($settings->testDataset)
                  ->includeMeta()
                  ->sourceInterface($settings->datasetReadInterface)
                  ->sourceInterfaceVersion($settings->datasetReadInterfaceVersion)
                  ->send();
                                   
      $this->assertEquals($datasetRead->getStatus(), "200", "Debugging information: ".var_export($datasetRead, TRUE));                                       
      
      $json1 = json_decode($datasetRead->getResultset());
      $json2 = json_decode($settings->datasetReadStructJSONResultset);
      
      $this->assertTrue($json1 == $json2, "JSON resultsets not identical. Debugging information: ".var_export($datasetRead, TRUE));
      
      utilities\deleteDataset();
      
      unset($datasetRead);
      unset($settings);
    }  
    
    public function  testReadDataset_Serialization_APPLICATION_RDF_XML() {
      
      $settings = new Config();  
      
      utilities\deleteDataset();
      
      $this->assertTrue(utilities\createDataset(), "Can't create the dataset, check the /dataset/create/ endpoint first...");
            
      $datasetRead = new DatasetReadQuery($settings->endpointUrl);

      $datasetRead->mime("application/rdf+xml")
                  ->uri($settings->testDataset)
                  ->includeMeta()
                  ->sourceInterface($settings->datasetReadInterface)
                  ->sourceInterfaceVersion($settings->datasetReadInterfaceVersion)
                  ->send();            
            
      $this->assertEquals($datasetRead->getStatus(), "200", "Debugging information: ".var_export($datasetRead, TRUE));                                       
      
      $this->assertXmlStringEqualsXmlString($settings->datasetReadStructRDFXMLResultset, $datasetRead->getResultset());
      
      utilities\deleteDataset();
      
      unset($datasetRead);
      unset($settings);
    }      
    
    public function  testReadDataset_Serialization_APPLICATION_RDF_N3() {
      
      $settings = new Config();  
      
      utilities\deleteDataset();
      
      $this->assertTrue(utilities\createDataset(), "Can't create the dataset, check the /dataset/create/ endpoint first...");
            
      // Create the new dataset
      $datasetRead = new DatasetReadQuery($settings->endpointUrl);

      $datasetRead->mime("application/rdf+n3")
                  ->uri($settings->testDataset)
                  ->includeMeta()
                  ->sourceInterface($settings->datasetReadInterface)
                  ->sourceInterfaceVersion($settings->datasetReadInterfaceVersion)
                  ->send();      
                                   
      $this->assertEquals($datasetRead->getStatus(), "200", "Debugging information: ".var_export($datasetRead, TRUE));                                       
      
      $parser1 = \ARC2::getTurtleParser();
      $parser1->parse($settings->testDataset, $datasetRead->getResultset());
      $resourceIndex1 = $parser1->getSimpleIndex(0);
      
      $parser2 = \ARC2::getTurtleParser();
      $parser2->parse($settings->testDataset, $settings->datasetReadStructRDFN3Resultset);
      $resourceIndex2 = $parser2->getSimpleIndex(0);
      
      $this->assertTrue($resourceIndex1 == $resourceIndex2, "RDF+N3 resultsets not identical. Debugging information: ".var_export($datasetRead->getResultset() , TRUE));
      
      utilities\deleteDataset();
      
      unset($datasetRead);
      unset($settings);
    }      

    public function  testReadAllDatasets_Serialization_TEXT_XML() {
      $settings = new Config();  
      
      utilities\deleteTwoDatasets();
      
      $this->assertTrue(utilities\createTwoDatasets(), "Can't create the datasets, check the /dataset/create/ endpoint first...");
            
      $datasetRead = new DatasetReadQuery($settings->endpointUrl);

      $datasetRead->mime("text/xml")
                  ->uri("all")
                  ->includeMeta()
                  ->sourceInterface($settings->datasetReadInterface)
                  ->sourceInterfaceVersion($settings->datasetReadInterfaceVersion)
                  ->send();            
            
      $this->assertEquals($datasetRead->getStatus(), "200", "Debugging information: ".var_export($datasetRead, TRUE));                                       

      $xml = new \StructuredDynamics\structwsf\ws\framework\ProcessorXML();
      $xml->loadXML($datasetRead->getResultset());

      $subjects = $xml->getSubjects();

      $founds = 0;
       
      foreach($subjects as $subject)
      {             
        if($xml->getURI($subject) == $settings->testDataset ||
           $xml->getURI($subject) == $settings->testDataset."2/")
        {
          $founds++;         
        }
      }

      $this->assertEquals(2, $founds, "Created datasets not found. Debugging information: ".var_export($datasetRead, TRUE));
      
      utilities\deleteTwoDatasets();
      
      unset($datasetRead);
      unset($settings);
    }
    
    public function  testReadAllDatasets_Serialization_APPLICATION_JSON() {
      $settings = new Config();  
      
      utilities\deleteTwoDatasets();
      
      $this->assertTrue(utilities\createTwoDatasets(), "Can't create the datasets, check the /dataset/create/ endpoint first...");
      
      $datasetRead = new DatasetReadQuery($settings->endpointUrl);

      $datasetRead->mime("application/json")
                  ->uri("all")
                  ->includeMeta()
                  ->sourceInterface($settings->datasetReadInterface)
                  ->sourceInterfaceVersion($settings->datasetReadInterfaceVersion)
                  ->send();            
     
      utilities\validateParameterApplicationJson($this, $datasetRead);
      
      utilities\deleteTwoDatasets();
      
      unset($datasetRead);
      unset($settings);
    }
    
    public function  testReadAllDatasets_Serialization_APPLICATION_RDF_XML() {
      $settings = new Config();  
      
      utilities\deleteTwoDatasets();
      
      $this->assertTrue(utilities\createTwoDatasets(), "Can't create the datasets, check the /dataset/create/ endpoint first...");              
      
      $datasetRead = new DatasetReadQuery($settings->endpointUrl);

      $datasetRead->mime("application/rdf+xml")
                  ->uri("all")
                  ->includeMeta()
                  ->sourceInterface($settings->datasetReadInterface)
                  ->sourceInterfaceVersion($settings->datasetReadInterfaceVersion)
                  ->send();            
                            
      utilities\validateParameterApplicationRdfXml($this, $datasetRead);
      
      utilities\deleteTwoDatasets();
      
      unset($datasetRead);
      unset($settings);
    }

    public function  testReadAllDatasets_Serialization_APPLICATION_RDF_N3() {
      $settings = new Config();  
      
      utilities\deleteTwoDatasets();
      
      $this->assertTrue(utilities\createTwoDatasets(), "Can't create the datasets, check the /dataset/create/ endpoint first...");      
      
      $datasetRead = new DatasetReadQuery($settings->endpointUrl);

      $datasetRead->mime("application/rdf+n3")
                  ->uri("all")
                  ->includeMeta()
                  ->sourceInterface($settings->datasetReadInterface)
                  ->sourceInterfaceVersion($settings->datasetReadInterfaceVersion)
                  ->send();            
                            
      utilities\validateParameterApplicationRdfN3($this, $datasetRead);
      
      utilities\deleteTwoDatasets();
      
      unset($datasetRead);
      unset($settings);
    }
    
        
    public function  testReadDatasetNoDatasetUriSpecified() {
      
      $settings = new Config();  
      
      utilities\deleteDataset();
      
      $this->assertTrue(utilities\createDataset(), "Can't create the dataset, check the /dataset/create/ endpoint first...");      
      
      $datasetRead = new DatasetReadQuery($settings->endpointUrl);

      $datasetRead->mime("text/xml")
                  ->uri("")
                  ->includeMeta()
                  ->sourceInterface($settings->datasetReadInterface)
                  ->sourceInterfaceVersion($settings->datasetReadInterfaceVersion)
                  ->send();            
                                   
      $this->assertEquals($datasetRead->getStatus(), "400", "Debugging information: ".var_export($datasetRead, TRUE));                                       
      $this->assertEquals($datasetRead->getStatusMessage(), "Bad Request", "Debugging information: ".var_export($datasetRead, TRUE));
      $this->assertEquals($datasetRead->error->id, "WS-DATASET-READ-200", "Debugging information: ".var_export($datasetRead, TRUE));                                       
      
      utilities\deleteDataset();
      
      unset($datasetRead);
      unset($settings);
    }
    
    public function  testReadDatasetInvalidDatasetIRI() {
      
      $settings = new Config();  
      
      utilities\deleteDataset();
      
      $this->assertTrue(utilities\createDataset(), "Can't create the dataset, check the /dataset/create/ endpoint first...");
      
      $datasetRead = new DatasetReadQuery($settings->endpointUrl);

      $datasetRead->mime("text/xml")
                  ->uri($settings->testDataset . "<>")
                  ->includeMeta()
                  ->sourceInterface($settings->datasetReadInterface)
                  ->sourceInterfaceVersion($settings->datasetReadInterfaceVersion)
                  ->send();            
                                   
      $this->assertEquals($datasetRead->getStatus(), "400", "Debugging information: ".var_export($datasetRead, TRUE));                                       
      $this->assertEquals($datasetRead->getStatusMessage(), "Bad Request", "Debugging information: ".var_export($datasetRead, TRUE));
      $this->assertEquals($datasetRead->error->id, "WS-DATASET-READ-201", "Debugging information: ".var_export($datasetRead, TRUE));                                       
      
      utilities\deleteDataset();
      
      unset($datasetRead);
      unset($settings);
    }  
  }

  
?>