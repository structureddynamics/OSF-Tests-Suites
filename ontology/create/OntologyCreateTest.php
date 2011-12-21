<?php
  include_once("../tests/Config.php");
  include_once("../tests/validators.php");
  
  ini_set("memory_limit","256M");
  set_time_limit(3600);

  $settings = new Config(); 
  
  // Database connectivity procedures
  include_once($settings->structwsfInstanceFolder . "framework/WebServiceQuerier.php");
  include_once("../tests/utilities.php");
  
  class OntologyCreateTest extends PHPUnit_Framework_TestCase {
    
    static private $outputs = array();

    public function testWrongEndpointUrl() {
      
      $settings = new Config();          
      
      $wsq = new WebServiceQuerier($settings->endpointUrl . "ontology/create/" . "wrong", 
                                   "post", 
                                   "text/xml",
                                   "uri=" . urlencode($settings->testOntologyUri) .
                                   "&globalPermissions=" . urlencode("True;True;True;True") .
                                   "&advancedIndexation=" . urlencode("True") .
                                   "&reasoner=" . urlencode("True") .
                                   "&registered_ip=" . urlencode("Self"));        
                         
      $this->assertEquals($wsq->getStatus(), "404", "Debugging information: ".var_export($wsq, TRUE));                                       
      $this->assertEquals($wsq->getStatusMessage(), "Not Found", "Debugging information: ".var_export($wsq, TRUE));
      
      unset($wsq);
      unset($settings);
    }
    
    public function testWrongEndpointMethodGet() {
      
      $settings = new Config();  
      
      $wsq = new WebServiceQuerier($settings->endpointUrl . "ontology/create/", 
                                   "get", 
                                   "text/xml",
                                   "uri=" . urlencode($settings->testOntologyUri) .
                                   "&globalPermissions=" . urlencode("True;True;True;True") .
                                   "&advancedIndexation=" . urlencode("True") .
                                   "&reasoner=" . urlencode("True") .
                                   "&registered_ip=" . urlencode("Self"));        

                                   
      $this->assertEquals($wsq->getStatus(), "405", "Debugging information: ".var_export($wsq, TRUE));                                       
      $this->assertEquals($wsq->getStatusMessage(), "Method Not Allowed", "Debugging information: ".var_export($wsq, TRUE));          
      
      unset($wsq);
      unset($settings);
    }    
    
    public function  testCreateOntology() {
      
      $settings = new Config();  
        
      deleteOntology();  
                 
      // Create the new ontology
      $wsq = new WebServiceQuerier($settings->endpointUrl . "ontology/create/", 
                                   "post", 
                                   "text/xml",
                                   "uri=" . urlencode($settings->testOntologyUri) .
                                   "&globalPermissions=" . urlencode("True;True;True;True") .
                                   "&advancedIndexation=" . urlencode("True") .
                                   "&reasoner=" . urlencode("True") .
                                   "&registered_ip=" . urlencode("Self"));        

                                   
      $this->assertEquals($wsq->getStatus(), "200", "Debugging information: ".var_export($wsq, TRUE));                                       
      
      deleteDataset();
      
      unset($wsq);
      unset($settings);
    }  
    
    public function  testCreateOntologyValidateCreatedContent() {
      
      $settings = new Config();  
      
      deleteOntology();
      
      // Make sure the ontology doesn't exists
      $this->assertTrue(deleteDataset(), "Can't delete the dataset, check the /dataset/delete/ endpoint first...");
      
      // Create the new ontology
      $wsq = new WebServiceQuerier($settings->endpointUrl . "ontology/create/", 
                                   "post", 
                                   "text/xml",
                                   "uri=" . urlencode($settings->testOntologyUri) .
                                   "&globalPermissions=" . urlencode("True;True;True;True") .
                                   "&advancedIndexation=" . urlencode("True") .
                                   "&reasoner=" . urlencode("True") .
                                   "&registered_ip=" . urlencode("Self"));        

      $this->assertEquals($wsq->getStatus(), "200", "Debugging information: ".var_export($wsq, TRUE));                                       
      
      unset($wsq);
      
      $wsq = new WebServiceQuerier($settings->endpointUrl . "ontology/read/", 
                                   "post", 
                                   "text/xml",
                                   "ontology=" . urlencode($settings->testOntologyUri) .
                                   "&function=" . urlencode("getSerialized") .
                                   "&parameters=" .
                                   "&reasoner=" . urlencode("True") .
                                   "&registered_ip=self");      

      $this->assertEquals($wsq->getStatus(), "200", "Debugging information: ".var_export($wsq, TRUE));                                       
      
      validateParameterApplicationRdfXml($this, $wsq);
      
      deleteDataset();

      unset($wsq);
      unset($settings);
    }                       
    
    public function  testCreateOntologyNoOntologyUriSpecified() {
      
      $settings = new Config();  
      
      deleteOntology();
      
      $wsq = new WebServiceQuerier($settings->endpointUrl . "ontology/create/", 
                                   "post", 
                                   "text/xml",
                                   "uri=" . urlencode("") .
                                   "&globalPermissions=" . urlencode("True;True;True;True") .
                                   "&advancedIndexation=" . urlencode("True") .
                                   "&reasoner=" . urlencode("True") .
                                   "&registered_ip=" . urlencode("Self"));   
                                   
      $this->assertEquals($wsq->getStatus(), "400", "Debugging information: ".var_export($wsq, TRUE));                                       
      $this->assertEquals($wsq->getStatusMessage(), "Bad Request", "Debugging information: ".var_export($wsq, TRUE));
      $this->assertEquals($wsq->error->id, "WS-ONTOLOGY-CREATE-200", "Debugging information: ".var_export($wsq, TRUE));                                       
      
      unset($wsq);
      unset($settings);
    }
    
    public function  testCreateOntologyOntologyAlreadyExisting() {
      
      $settings = new Config();  
       
      deleteOntology();    
       
      $this->assertTrue(createOntology(), "Can't create the ontology, check the /ontology/create/ endpoint first...");
            
      $wsq = new WebServiceQuerier($settings->endpointUrl . "ontology/create/", 
                                   "post", 
                                   "text/xml",
                                   "uri=" . urlencode($settings->testOntologyUri) .
                                   "&globalPermissions=" . urlencode("True;True;True;True") .
                                   "&advancedIndexation=" . urlencode("True") .
                                   "&reasoner=" . urlencode("True") .
                                   "&registered_ip=" . urlencode("Self"));   
                                   
      $this->assertEquals($wsq->getStatus(), "400", "Debugging information: ".var_export($wsq, TRUE));                                       
      $this->assertEquals($wsq->getStatusMessage(), "Bad Request", "Debugging information: ".var_export($wsq, TRUE));
      $this->assertEquals($wsq->error->id, "WS-ONTOLOGY-CREATE-302", "Debugging information: ".var_export($wsq, TRUE));                                       
      
      deleteOntology();   
      
      unset($wsq);
      unset($settings);
    }    
    
    public function  testCreateOntologyInvalidOntology() {
      
      $settings = new Config();  
      
      $wsq = new WebServiceQuerier($settings->endpointUrl . "ontology/create/", 
                                   "post", 
                                   "text/xml",
                                   "uri=" . urlencode($settings->testInvalidOntologyUri) .
                                   "&globalPermissions=" . urlencode("True;True;True;True") .
                                   "&advancedIndexation=" . urlencode("True") .
                                   "&reasoner=" . urlencode("True") .
                                   "&registered_ip=" . urlencode("Self"));  
                                   
      $this->assertEquals($wsq->getStatus(), "400", "Debugging information: ".var_export($wsq, TRUE));                                       
      $this->assertEquals($wsq->getStatusMessage(), "Bad Request", "Debugging information: ".var_export($wsq, TRUE));
      $this->assertEquals($wsq->error->id, "WS-ONTOLOGY-CREATE-300", "Debugging information: ".var_export($wsq, TRUE));                                       
      
      unset($wsq);
      unset($settings);
    }
   
  }

  
?>