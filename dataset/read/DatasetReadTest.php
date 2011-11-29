<?php
  include_once("../tests/Config.php");
  include_once("../tests/validators.php");
  
  ini_set("memory_limit","256M");
  set_time_limit(3600);

  $settings = new Config(); 
  
  // Database connectivity procedures
  include_once($settings->structwsfInstanceFolder . "framework/ProcessorXML.php");
  include_once($settings->structwsfInstanceFolder . "framework/arc2/ARC2.php");
  include_once($settings->structwsfInstanceFolder . "framework/WebServiceQuerier.php");
  include_once("../tests/utilities.php");
  
  class DatasetReadTest extends PHPUnit_Framework_TestCase {
    
    static private $outputs = array();

    public function testWrongEndpointUrl() {
      
      $settings = new Config();          
      
      $wsq = new WebServiceQuerier($settings->endpointUrl . "dataset/read/" . "wrong", 
                                   "get", 
                                   "text/xml",
                                   "uri=" . urlencode($settings->testDataset) .
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
                                   "&meta=" . urlencode("True"));
                                   
      $this->assertEquals($wsq->getStatus(), "405", "Debugging information: ".var_export($wsq, TRUE));                                       
      $this->assertEquals($wsq->getStatusMessage(), "Method Not Allowed", "Debugging information: ".var_export($wsq, TRUE));          
      
      unset($wsq);
      unset($settings);
    }    
    
    public function  testReadDataset_Serialization_TEXT_XML() {
      
      $settings = new Config();  
      
      deleteDataset();
      
      $this->assertTrue(createDataset(), "Can't create the dataset, check the /dataset/create/ endpoint first...");
            
      // Create the new dataset
      $wsq = new WebServiceQuerier($settings->endpointUrl . "dataset/read/", 
                                   "get", 
                                   "text/xml",
                                   "uri=" . urlencode($settings->testDataset) .
                                   "&meta=" . urlencode("True"));
                                   
      $this->assertEquals($wsq->getStatus(), "200", "Debugging information: ".var_export($wsq, TRUE));                                       
      
      $this->assertXmlStringEqualsXmlString($settings->datasetReadStructXMLResultset, $wsq->getResultset());
      
      deleteDataset();
      
      unset($wsq);
      unset($settings);
    }
    
    public function  testReadDataset_Serialization_APPLICATION_JSON() {
      
      $settings = new Config();  
      
      deleteDataset();
      
      $this->assertTrue(createDataset(), "Can't create the dataset, check the /dataset/create/ endpoint first...");
            
      // Create the new dataset
      $wsq = new WebServiceQuerier($settings->endpointUrl . "dataset/read/", 
                                   "get", 
                                   "application/json",
                                   "uri=" . urlencode($settings->testDataset) .
                                   "&meta=" . urlencode("True"));
                                   
      $this->assertEquals($wsq->getStatus(), "200", "Debugging information: ".var_export($wsq, TRUE));                                       
      
      $json1 = json_decode($wsq->getResultset());
      $json2 = json_decode($settings->datasetReadStructJSONResultset);
      
      $this->assertTrue($json1 == $json2, "JSON resultsets not identical. Debugging information: ".var_export($wsq, TRUE));
      
      deleteDataset();
      
      unset($wsq);
      unset($settings);
    }  
    
    public function  testReadDataset_Serialization_APPLICATION_RDF_XML() {
      
      $settings = new Config();  
      
      deleteDataset();
      
      $this->assertTrue(createDataset(), "Can't create the dataset, check the /dataset/create/ endpoint first...");
            
      // Create the new dataset
      $wsq = new WebServiceQuerier($settings->endpointUrl . "dataset/read/", 
                                   "get", 
                                   "application/rdf+xml",
                                   "uri=" . urlencode($settings->testDataset) .
                                   "&meta=" . urlencode("True"));
                                   
      $this->assertEquals($wsq->getStatus(), "200", "Debugging information: ".var_export($wsq, TRUE));                                       
      
      $this->assertXmlStringEqualsXmlString($settings->datasetReadStructRDFXMLResultset, $wsq->getResultset());
      
      deleteDataset();
      
      unset($wsq);
      unset($settings);
    }      
    
    public function  testReadDataset_Serialization_APPLICATION_RDF_N3() {
      
      $settings = new Config();  
      
      deleteDataset();
      
      $this->assertTrue(createDataset(), "Can't create the dataset, check the /dataset/create/ endpoint first...");
            
      // Create the new dataset
      $wsq = new WebServiceQuerier($settings->endpointUrl . "dataset/read/", 
                                   "get", 
                                   "application/rdf+n3",
                                   "uri=" . urlencode($settings->testDataset) .
                                   "&meta=" . urlencode("True"));
                                   
      $this->assertEquals($wsq->getStatus(), "200", "Debugging information: ".var_export($wsq, TRUE));                                       
      
      $parser1 = ARC2::getTurtleParser();
      $parser1->parse($settings->testDataset, $wsq->getResultset());
      $resourceIndex1 = $parser1->getSimpleIndex(0);
      
      $parser2 = ARC2::getTurtleParser();
      $parser2->parse($settings->testDataset, $settings->datasetReadStructRDFN3Resultset);
      $resourceIndex2 = $parser2->getSimpleIndex(0);
      
      $this->assertTrue($resourceIndex1 == $resourceIndex2, "RDF+N3 resultsets not identical. Debugging information: ".var_export($wsq->getResultset() , TRUE));
      
      deleteDataset();
      
      unset($wsq);
      unset($settings);
    }      

    public function  testReadAllDatasets_Serialization_TEXT_XML() {
      $settings = new Config();  
      
      deleteTwoDatasets();
      
      $this->assertTrue(createTwoDatasets(), "Can't create the datasets, check the /dataset/create/ endpoint first...");
            
      // Create the new dataset
      $wsq = new WebServiceQuerier($settings->endpointUrl . "dataset/read/", 
                                   "get", 
                                   "text/xml",
                                   "uri=" . urlencode("all") .
                                   "&meta=" . urlencode("True"));
                           
      $this->assertEquals($wsq->getStatus(), "200", "Debugging information: ".var_export($wsq, TRUE));                                       

      $xml = new ProcessorXML();
      $xml->loadXML($wsq->getResultset());

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

      $this->assertEquals(2, $founds, "Created datasets not found. Debugging information: ".var_export($wsq, TRUE));
      
      deleteTwoDatasets();
      
      unset($wsq);
      unset($settings);
    }
    
    public function  testReadAllDatasets_Serialization_APPLICATION_JSON() {
      $settings = new Config();  
      
      $wsq = new WebServiceQuerier($settings->endpointUrl . "dataset/read/", 
                                   "get", 
                                   "application/json",
                                   "uri=" . urlencode("all") .
                                   "&meta=" . urlencode("True"));
                            
      validateParameterApplicationJson($this, $wsq);
      
      unset($wsq);
      unset($settings);
    }
    
    public function  testReadAllDatasets_Serialization_APPLICATION_RDF_XML() {
      $settings = new Config();  
      
      $wsq = new WebServiceQuerier($settings->endpointUrl . "dataset/read/", 
                                   "get", 
                                   "application/rdf+xml",
                                   "uri=" . urlencode("all") .
                                   "&meta=" . urlencode("True"));
                            
      validateParameterApplicationRdfXml($this, $wsq);
      
      unset($wsq);
      unset($settings);
    }

    public function  testReadAllDatasets_Serialization_APPLICATION_RDF_N3() {
      $settings = new Config();  
      
      $wsq = new WebServiceQuerier($settings->endpointUrl . "dataset/read/", 
                                   "get", 
                                   "application/rdf+n3",
                                   "uri=" . urlencode("all") .
                                   "&meta=" . urlencode("True"));
                            
      validateParameterApplicationRdfN3($this, $wsq);
      
      unset($wsq);
      unset($settings);
    }
    
        
    public function  testReadDatasetNoDatasetUriSpecified() {
      
      $settings = new Config();  
      
      deleteDataset();
      
      $this->assertTrue(createDataset(), "Can't create the dataset, check the /dataset/create/ endpoint first...");      
      
      $wsq = new WebServiceQuerier($settings->endpointUrl . "dataset/read/", 
                                   "get", 
                                   "text/xml",
                                   "uri=" . 
                                   "&meta=" . urlencode("True"));
                                   
      $this->assertEquals($wsq->getStatus(), "400", "Debugging information: ".var_export($wsq, TRUE));                                       
      $this->assertEquals($wsq->getStatusMessage(), "Bad Request", "Debugging information: ".var_export($wsq, TRUE));
      $this->assertEquals($wsq->error->id, "WS-DATASET-READ-200", "Debugging information: ".var_export($wsq, TRUE));                                       
      
      deleteDataset();
      
      unset($wsq);
      unset($settings);
    }
    
    public function  testReadDatasetInvalidDatasetIRI() {
      
      $settings = new Config();  
      
      deleteDataset();
      
      $this->assertTrue(createDataset(), "Can't create the dataset, check the /dataset/create/ endpoint first...");
      
      $wsq = new WebServiceQuerier($settings->endpointUrl . "dataset/read/", 
                                   "get", 
                                   "text/xml",
                                   "uri=" . urlencode($settings->testDataset) . "<>" .
                                   "&meta=" . urlencode("True"));
                                   
      $this->assertEquals($wsq->getStatus(), "400", "Debugging information: ".var_export($wsq, TRUE));                                       
      $this->assertEquals($wsq->getStatusMessage(), "Bad Request", "Debugging information: ".var_export($wsq, TRUE));
      $this->assertEquals($wsq->error->id, "WS-DATASET-READ-201", "Debugging information: ".var_export($wsq, TRUE));                                       
      
      deleteDataset();
      
      unset($wsq);
      unset($settings);
    }  
  }

  
?>