<?php

  namespace StructuredDynamics\structwsf\tests\ws\ontology\read;
  
  use StructuredDynamics\structwsf\framework\WebServiceQuerier;
  use StructuredDynamics\structwsf\tests\Config;
  use StructuredDynamics\structwsf\tests as utilities;
   
  include_once("SplClassLoader.php");
  include_once("validators.php");
  include_once("utilities.php");   
  
  // Load the \tests namespace where all the test code is located 
  $loader_tests = new \SplClassLoader('StructuredDynamics\structwsf\tests', realpath("../../../"));
  $loader_tests->register();
 
  // Load the \framework namespace where all the supporting (utility) code is located
  $loader_framework = new \SplClassLoader('StructuredDynamics\structwsf\framework', realpath("../../../"));
  $loader_framework->register(); 
  
  ini_set("memory_limit","256M");
  set_time_limit(3600);

  $settings = new Config(); 
  
  // Database connectivity procedures
  include_once($settings->structwsfInstanceFolder . "framework/ProcessorXML.php");
  include_once($settings->structwsfInstanceFolder . "framework/arc2/ARC2.php");
  
  class OntologyReadTest extends \PHPUnit_Framework_TestCase {
    
    static private $outputs = array();

    static public function setUpBeforeClass() 
    {    
      utilities\createOntology();      
    }
    
    public function testWrongEndpointUrl() {
      
      $settings = new Config();          
      
      $wsq = new WebServiceQuerier($settings->endpointUrl . "ontology/read/" . "wrong", 
                                   "post", 
                                   "text/xml",
                                   "ontology=" . urlencode($settings->testOntologyUri) .
                                   "&function=" .
                                   "&parameters=" .
                                   "&reasoner=" .
                                   "&registered_ip=self");
                   
      $this->assertEquals($wsq->getStatus(), "404", "Debugging information: ".var_export($wsq, TRUE));                                       
      $this->assertEquals($wsq->getStatusMessage(), "Not Found", "Debugging information: ".var_export($wsq, TRUE));
      
      unset($wsq);
      unset($settings);
    }
    
    public function testWrongEndpointMethodGet() {
      
      $settings = new Config();  
      
      $wsq = new WebServiceQuerier($settings->endpointUrl . "ontology/read/", 
                                   "get", 
                                   "text/xml",
                                   "ontology=" . urlencode($settings->testOntologyUri) .
                                   "&function=" .
                                   "&parameters=" .
                                   "&reasoner=" .
                                   "&registered_ip=self");
                                   
      $this->assertEquals($wsq->getStatus(), "405", "Debugging information: ".var_export($wsq, TRUE));                                       
      $this->assertEquals($wsq->getStatusMessage(), "Method Not Allowed", "Debugging information: ".var_export($wsq, TRUE));          
      
      unset($wsq);
      unset($settings);
    }   
       
    public function testOntologyRead_function_getSerialized() {
      
      $settings = new Config();  
      
      $wsq = new WebServiceQuerier($settings->endpointUrl . "ontology/read/", 
                                   "post", 
                                   "text/xml",
                                   "ontology=" . urlencode($settings->testOntologyUri) .
                                   "&function=" . urlencode("getSerialized") .
                                   "&parameters=" .
                                   "&reasoner=" . urlencode("True") .
                                   "&registered_ip=self");
      
      $this->assertEquals($wsq->getStatus(), "200", "Debugging information: ".var_export($wsq, TRUE));                                       
      
      utilities\validateParameterApplicationRdfXml($this, $wsq);
                                   
      unset($wsq);
      unset($settings);
    } 
    
    // @ TODO add the remaining tests below.
    
    
    static public function tearDownAfterClass() 
    {
      utilities\deleteOntology();      
    }  
  }

  
?>