<?php
  include_once("../tests/Config.php");
  include_once("../tests/validators.php");
  
  ini_set("memory_limit","256M");
  set_time_limit(3600);

  $settings = new Config(); 
  
  // Database connectivity procedures
  include_once($settings->structwsfInstanceFolder . "framework/WebServiceQuerier.php");
  include_once("../tests/utilities.php");
  
  class OntologyDeleteTest extends PHPUnit_Framework_TestCase {
    
    static private $outputs = array();
    
    public function testWrongEndpointUrl() {
      
      $settings = new Config();          
      
      $wsq = new WebServiceQuerier($settings->endpointUrl . "ontology/delete/" . "wrong", 
                                   "post", 
                                   "text/xml",
                                   "ontology=" . urlencode($settings->testOntologyUri) .
                                   "&function=" . urlencode("deleteOntology") .
                                   "&parameters=" . urlencode("") .
                                   "&registered_ip=" . urlencode("self"));
                   
      $this->assertEquals($wsq->getStatus(), "404", "Debugging information: ".var_export($wsq, TRUE));                                       
      $this->assertEquals($wsq->getStatusMessage(), "Not Found", "Debugging information: ".var_export($wsq, TRUE));
      
      unset($wsq);
      unset($settings);
    }
    
    public function testWrongEndpointMethodGet() {
      
      $settings = new Config();  
      
      $wsq = new WebServiceQuerier($settings->endpointUrl . "ontology/delete/", 
                                   "get", 
                                   "text/xml",
                                   "ontology=" . urlencode($settings->testOntologyUri) .
                                   "&function=" . urlencode("deleteOntology") .
                                   "&parameters=" . urlencode("") .
                                   "&registered_ip=" . urlencode("self"));
                                   
      $this->assertEquals($wsq->getStatus(), "405", "Debugging information: ".var_export($wsq, TRUE));                                       
      $this->assertEquals($wsq->getStatusMessage(), "Method Not Allowed", "Debugging information: ".var_export($wsq, TRUE));          
      
      unset($wsq);
      unset($settings);
    }    
    
    public function  testDeleteOntology_unknownFunctionCall() {
      
      $settings = new Config();  
      
      // Delete Ontology
      $wsq = new WebServiceQuerier($settings->endpointUrl . "ontology/delete/", 
                                   "post", 
                                   "text/xml",
                                   "ontology=" . urlencode($settings->testOntologyUri) .
                                   "&function=" . urlencode("deleteOntology" . "unknown") .
                                   "&parameters=" . urlencode("") .
                                   "&registered_ip=" . urlencode("self"));
                                   
      $this->assertEquals($wsq->getStatus(), "400", "Debugging information: ".var_export($wsq, TRUE));                                       
      $this->assertEquals($wsq->getStatusMessage(), "Bad Request", "Debugging information: ".var_export($wsq, TRUE));
      $this->assertEquals($wsq->error->id, "WS-ONTOLOGY-DELETE-200", "Debugging information: ".var_export($wsq, TRUE));    
                                    
      unset($wsq);
      unset($settings);
    }   
    
    
    public function  testDeleteOntology_NoOntologyUriSpecified() {
      
      $settings = new Config();  
      
      // Delete Ontology
      $wsq = new WebServiceQuerier($settings->endpointUrl . "ontology/delete/", 
                                   "post", 
                                   "text/xml",
                                   "ontology=" . urlencode("") .
                                   "&function=" . urlencode("deleteOntology") .
                                   "&parameters=" . urlencode("") .
                                   "&registered_ip=" . urlencode("self"));
                                   
      $this->assertEquals($wsq->getStatus(), "400", "Debugging information: ".var_export($wsq, TRUE));                                       
      $this->assertEquals($wsq->getStatusMessage(), "Bad Request", "Debugging information: ".var_export($wsq, TRUE));
      $this->assertEquals($wsq->error->id, "WS-ONTOLOGY-DELETE-201", "Debugging information: ".var_export($wsq, TRUE));    
                                    
      unset($wsq);
      unset($settings);
    }       

    public function  testDeleteOntology_NoPropertyUriSpecified() {
      
      $settings = new Config();  
      
      // Delete Ontology
      $wsq = new WebServiceQuerier($settings->endpointUrl . "ontology/delete/", 
                                   "post", 
                                   "text/xml",
                                   "ontology=" . urlencode($settings->testOntologyUri) .
                                   "&function=" . urlencode("deleteProperty") .
                                   "&parameters=" . urlencode("uri=") .
                                   "&registered_ip=" . urlencode("self"));
                                   
      $this->assertEquals($wsq->getStatus(), "400", "Debugging information: ".var_export($wsq, TRUE));                                       
      $this->assertEquals($wsq->getStatusMessage(), "Bad Request", "Debugging information: ".var_export($wsq, TRUE));
      $this->assertEquals($wsq->error->id, "WS-ONTOLOGY-DELETE-202", "Debugging information: ".var_export($wsq, TRUE));    
                                    
      unset($wsq);
      unset($settings);
    } 

    public function  testDeleteOntology_NoNamedIndividualUriSpecified() {
      
      $settings = new Config();  
      
      // Delete Ontology
      $wsq = new WebServiceQuerier($settings->endpointUrl . "ontology/delete/", 
                                   "post", 
                                   "text/xml",
                                   "ontology=" . urlencode($settings->testOntologyUri) .
                                   "&function=" . urlencode("deleteNamedIndividual") .
                                   "&parameters=" . urlencode("uri=") .
                                   "&registered_ip=" . urlencode("self"));
                                   
      $this->assertEquals($wsq->getStatus(), "400", "Debugging information: ".var_export($wsq, TRUE));                                       
      $this->assertEquals($wsq->getStatusMessage(), "Bad Request", "Debugging information: ".var_export($wsq, TRUE));
      $this->assertEquals($wsq->error->id, "WS-ONTOLOGY-DELETE-203", "Debugging information: ".var_export($wsq, TRUE));    
                                    
      unset($wsq);
      unset($settings);
    }         

    public function  testDeleteOntology_NoClassUriSpecified() {
      
      $settings = new Config();  
      
      // Delete Ontology
      $wsq = new WebServiceQuerier($settings->endpointUrl . "ontology/delete/", 
                                   "post", 
                                   "text/xml",
                                   "ontology=" . urlencode($settings->testOntologyUri) .
                                   "&function=" . urlencode("deleteClass") .
                                   "&parameters=" . urlencode("uri=") .
                                   "&registered_ip=" . urlencode("self"));
                                   
      $this->assertEquals($wsq->getStatus(), "400", "Debugging information: ".var_export($wsq, TRUE));                                       
      $this->assertEquals($wsq->getStatusMessage(), "Bad Request", "Debugging information: ".var_export($wsq, TRUE));
      $this->assertEquals($wsq->error->id, "WS-ONTOLOGY-DELETE-204", "Debugging information: ".var_export($wsq, TRUE));    
                                    
      unset($wsq);
      unset($settings);
    } 
        
    public function  testDeleteOntology_function_deleteOntology() {
      
      $settings = new Config();  
      
      deleteOntology();
      
      $this->assertTrue(createOntology(), "Can't create the ontology, check the /ontology/create/ endpoint first...");
      
      // Delete Ontology
      $wsq = new WebServiceQuerier($settings->endpointUrl . "ontology/delete/", 
                                   "post", 
                                   "text/xml",
                                   "ontology=" . urlencode($settings->testOntologyUri) .
                                   "&function=" . urlencode("deleteOntology") .
                                   "&parameters=" . urlencode("") .
                                   "&registered_ip=" . urlencode("self"));
                                   
      $this->assertEquals($wsq->getStatus(), "200", "Debugging information: ".var_export($wsq, TRUE));    
                                    
      unset($wsq);
      
      // Make sure it is deleted      
      $wsq = new WebServiceQuerier($settings->endpointUrl . "ontology/read/", 
                                   "post", 
                                   "text/xml",
                                   "ontology=" . urlencode($settings->testOntologyUri) .
                                   "&function=" .
                                   "&parameters=" .
                                   "&reasoner=" .
                                   "&registered_ip=self");      

      // Since the ontology is not existing anymore, there is not auth information, so it means it as been
      // properly deleted.                                   
      $this->assertEquals($wsq->getStatus(), "403", "Debugging information: ".var_export($wsq, TRUE));                                       
      $this->assertEquals($wsq->getStatusMessage(), "Forbidden", "Debugging information: ".var_export($wsq, TRUE));
      $this->assertEquals($wsq->error->id, "WS-AUTH-VALIDATOR-303", "Debugging information: ".var_export($wsq, TRUE));    

      unset($wsq);      
      unset($settings);
    } 
    
    public function  testDeleteOntology_function_deleteProperty_Datatype() {
      
      $settings = new Config();  
      
      deleteOntology();
      
      $this->assertTrue(createOntology(), "Can't create the ontology, check the /ontology/create/ endpoint first...");
      
      // Delete Ontology
      $wsq = new WebServiceQuerier($settings->endpointUrl . "ontology/delete/", 
                                   "post", 
                                   "text/xml",
                                   "ontology=" . urlencode($settings->testOntologyUri) .
                                   "&function=" . urlencode("deleteProperty") .
                                   "&parameters=" . urlencode("uri=".$settings->targetDatatypePropertyUri) .
                                   "&registered_ip=" . urlencode("self"));
                                   
      $this->assertEquals($wsq->getStatus(), "200", "Debugging information: ".var_export($wsq, TRUE));    
                                    
      unset($wsq);
      
      // Make sure it is deleted      
      $wsq = new WebServiceQuerier($settings->endpointUrl . "ontology/read/", 
                                   "post", 
                                   "text/xml",
                                   "ontology=" . urlencode($settings->testOntologyUri) .
                                   "&function=getProperty" .
                                   "&parameters=" . urlencode("uri=".$settings->targetDatatypePropertyUri) .
                                   "&reasoner=" .
                                   "&registered_ip=self");      

      // Since the ontology is not existing anymore, there is not auth information, so it means it as been
      // properly deleted.                                   
      $this->assertEquals($wsq->getStatus(), "400", "Debugging information: ".var_export($wsq, TRUE));                                       
      $this->assertEquals($wsq->getStatusMessage(), "Bad Request", "Debugging information: ".var_export($wsq, TRUE));
      $this->assertEquals($wsq->error->id, "WS-ONTOLOGY-READ-204", "Debugging information: ".var_export($wsq, TRUE));    

      unset($wsq);      
      
      deleteOntology();      
      
      unset($settings);
    }     
    
    public function  testDeleteOntology_function_deleteProperty_Object() {
      
      $settings = new Config();  
      
      deleteOntology();
      
      $this->assertTrue(createOntology(), "Can't create the ontology, check the /ontology/create/ endpoint first...");
      
      // Delete Ontology
      $wsq = new WebServiceQuerier($settings->endpointUrl . "ontology/delete/", 
                                   "post", 
                                   "text/xml",
                                   "ontology=" . urlencode($settings->testOntologyUri) .
                                   "&function=" . urlencode("deleteProperty") .
                                   "&parameters=" . urlencode("uri=".$settings->targetObjectPropertyUri) .
                                   "&registered_ip=" . urlencode("self"));
                                   
      $this->assertEquals($wsq->getStatus(), "200", "Debugging information: ".var_export($wsq, TRUE));    
                                    
      unset($wsq);
      
      // Make sure it is deleted      
      $wsq = new WebServiceQuerier($settings->endpointUrl . "ontology/read/", 
                                   "post", 
                                   "text/xml",
                                   "ontology=" . urlencode($settings->testOntologyUri) .
                                   "&function=getProperty" .
                                   "&parameters=" . urlencode("uri=".$settings->targetObjectPropertyUri) .
                                   "&reasoner=" .
                                   "&registered_ip=self");      

      // Since the ontology is not existing anymore, there is not auth information, so it means it as been
      // properly deleted.                                   
      $this->assertEquals($wsq->getStatus(), "400", "Debugging information: ".var_export($wsq, TRUE));                                       
      $this->assertEquals($wsq->getStatusMessage(), "Bad Request", "Debugging information: ".var_export($wsq, TRUE));
      $this->assertEquals($wsq->error->id, "WS-ONTOLOGY-READ-204", "Debugging information: ".var_export($wsq, TRUE));    

      unset($wsq);      
      
      deleteOntology();      
      
      unset($settings);
    }   
      
    public function  testDeleteOntology_function_deleteProperty_Annotation() {
      
      $settings = new Config();  
      
      deleteOntology();
      
      $this->assertTrue(createOntology(), "Can't create the ontology, check the /ontology/create/ endpoint first...");
      
      // Delete Ontology
      $wsq = new WebServiceQuerier($settings->endpointUrl . "ontology/delete/", 
                                   "post", 
                                   "text/xml",
                                   "ontology=" . urlencode($settings->testOntologyUri) .
                                   "&function=" . urlencode("deleteProperty") .
                                   "&parameters=" . urlencode("uri=".$settings->targetAnnotationPropertyUri) .
                                   "&registered_ip=" . urlencode("self"));
                                   
      $this->assertEquals($wsq->getStatus(), "200", "Debugging information: ".var_export($wsq, TRUE));    
                                    
      unset($wsq);
      
      // Make sure it is deleted      
      $wsq = new WebServiceQuerier($settings->endpointUrl . "ontology/read/", 
                                   "post", 
                                   "text/xml",
                                   "ontology=" . urlencode($settings->testOntologyUri) .
                                   "&function=getProperty" .
                                   "&parameters=" . urlencode("uri=".$settings->targetAnnotationPropertyUri) .
                                   "&reasoner=" .
                                   "&registered_ip=self");      

      // Since the ontology is not existing anymore, there is not auth information, so it means it as been
      // properly deleted.                                   
      $this->assertEquals($wsq->getStatus(), "400", "Debugging information: ".var_export($wsq, TRUE));                                       
      $this->assertEquals($wsq->getStatusMessage(), "Bad Request", "Debugging information: ".var_export($wsq, TRUE));
      $this->assertEquals($wsq->error->id, "WS-ONTOLOGY-READ-204", "Debugging information: ".var_export($wsq, TRUE));    

      unset($wsq);      
      
      deleteOntology();      
      
      unset($settings);
    }
    
    public function  testDeleteOntology_function_deleteClass() {
      
      $settings = new Config();  
      
      deleteOntology();
      
      $this->assertTrue(createOntology(), "Can't create the ontology, check the /ontology/create/ endpoint first...");
      
      // Delete Ontology
      $wsq = new WebServiceQuerier($settings->endpointUrl . "ontology/delete/", 
                                   "post", 
                                   "text/xml",
                                   "ontology=" . urlencode($settings->testOntologyUri) .
                                   "&function=" . urlencode("deleteClass") .
                                   "&parameters=" . urlencode("uri=".$settings->targetClassUri) .
                                   "&registered_ip=" . urlencode("self"));
                                   
      $this->assertEquals($wsq->getStatus(), "200", "Debugging information: ".var_export($wsq, TRUE));    
                                    
      unset($wsq);
      
      // @TODO For some reason, the Named Individual is not in the ontology anymore, it it is still returned
      // by the getOWLClass() API call when we execute this code. Need some more debugging to figure out
      // why this happens, and by getOWLClass() is not returning null.      
      
      // Make sure it is deleted      
      $wsq = new WebServiceQuerier($settings->endpointUrl . "ontology/read/", 
                                   "post", 
                                   "text/xml",
                                   "ontology=" . urlencode($settings->testOntologyUri) .
                                   "&function=getClass" .
                                   "&parameters=" . urlencode("uri=".$settings->targetClassUri) .
                                   "&reasoner=" . urlencode("True") .
                                   "&registered_ip=self");      

      // Since the ontology is not existing anymore, there is not auth information, so it means it as been
      // properly deleted.                                   
      $this->assertEquals($wsq->getStatus(), "400", "Debugging information: ".var_export($wsq, TRUE));                                       
      $this->assertEquals($wsq->getStatusMessage(), "Bad Request", "Debugging information: ".var_export($wsq, TRUE));
      $this->assertEquals($wsq->error->id, "WS-ONTOLOGY-READ-205", "Debugging information: ".var_export($wsq, TRUE));    

      unset($wsq);      
      
      deleteOntology();      
      
      unset($settings);
    }    
     
    public function  testDeleteOntology_function_deleteNamedIndividual() {
      
      $settings = new Config();  
      
      deleteOntology();
      
      $this->assertTrue(createOntology(), "Can't create the ontology, check the /ontology/create/ endpoint first...");
      
      // Delete Ontology
      $wsq = new WebServiceQuerier($settings->endpointUrl . "ontology/delete/", 
                                   "post", 
                                   "text/xml",
                                   "ontology=" . urlencode($settings->testOntologyUri) .
                                   "&function=" . urlencode("deleteNamedIndividual") .
                                   "&parameters=" . urlencode("uri=".$settings->targetNamedIndividualUri) .
                                   "&registered_ip=" . urlencode("self"));
                                   
      $this->assertEquals($wsq->getStatus(), "200", "Debugging information: ".var_export($wsq, TRUE));    
                                    
      unset($wsq);
      
      // @TODO For some reason, the Named Individual is not in the ontology anymore, it it is still returned
      // by the getOWLNamedIndividal() API call when we execute this code. Need some more debugging to figure out
      // why this happens, and by getOWLNamedIndividal() is not returning null.
      
      // Make sure it is deleted      
      $wsq = new WebServiceQuerier($settings->endpointUrl . "ontology/read/", 
                                   "post", 
                                   "text/xml",
                                   "ontology=" . urlencode($settings->testOntologyUri) .
                                   "&function=getNamedIndividual" .
                                   "&parameters=" . urlencode("uri=".$settings->targetNamedIndividualUri) .
                                   "&reasoner=" .
                                   "&registered_ip=self");      

      // Since the ontology is not existing anymore, there is not auth information, so it means it as been
      // properly deleted.                                   
      $this->assertEquals($wsq->getStatus(), "400", "Debugging information: ".var_export($wsq, TRUE));                                       
      $this->assertEquals($wsq->getStatusMessage(), "Bad Request", "Debugging information: ".var_export($wsq, TRUE));
      $this->assertEquals($wsq->error->id, "WS-ONTOLOGY-READ-206", "Debugging information: ".var_export($wsq, TRUE));    

      unset($wsq);      
      
      deleteOntology();      
      
      unset($settings);
    }     
  }

  
?>