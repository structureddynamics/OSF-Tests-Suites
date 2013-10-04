<?php

  namespace StructuredDynamics\osf\tests\ws\sparql;
  
  use StructuredDynamics\osf\framework\WebServiceQuerier;
  use StructuredDynamics\osf\php\api\ws\sparql\SparqlQuery;
  use StructuredDynamics\osf\tests\Config;
  use StructuredDynamics\osf\framework\Resultset;
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
  
  class SparqlTest extends \PHPUnit_Framework_TestCase {
    
    static private $outputs = array();
    
    public static function setUpBeforeClass()
    {      
      utilities\deleteDataset();

      utilities\createSearchRecords();
    }
    
    public static function tearDownAfterClass()
    {
      utilities\deleteDataset();
    }
    /*
    public function testWrongEndpointUrl() {

      $settings = new Config();   
      
      $wsq = new WebServiceQuerier($settings->endpointUrl . "sparql/" . "wrong", 
                                   "post", 
                                   "text/xml",
                                   "query=".
                                   "&dataset=".
                                   "&limit=".
                                   "&offset=".
                                   "&default-graph-uri=".
                                   "&named-graph-uri=".
                                   "&interface=". urlencode($settings->sparqlInterface) .
                                   "&version=". urlencode($settings->sparqlInterfaceVersion),
                                   $settings->applicationID,
                                   $settings->apiKey,
                                   $settings->userID);
                         
      $this->assertEquals($wsq->getStatus(), "404", "Debugging information: ".var_export($wsq, TRUE));                                       
      $this->assertEquals($wsq->getStatusMessage(), "Not Found", "Debugging information: ".var_export($wsq, TRUE));
      
      unset($wsq);
      unset($settings);
    }
    
    
    public function testWrongEndpointMethodGet(){
      
      $settings = new Config();  
      
      $wsq = new WebServiceQuerier($settings->endpointUrl . "/sparql/",
                                   "get", 
                                   "text/xml",
                                   "query=".
                                   "&dataset=".
                                   "&limit=".
                                   "&offset=".
                                   "&default-graph-uri=".
                                   "&named-graph-uri=".
                                   "&interface=". urlencode($settings->sparqlInterface) .
                                   "&version=". urlencode($settings->sparqlInterfaceVersion) .
                                   "&interface=". urlencode($settings->crudReadInterface) .
                                   "&version=". urlencode($settings->crudReadInterfaceVersion),
                                   $settings->applicationID,
                                   $settings->apiKey,
                                   $settings->userID);
                                   
      $this->assertEquals($wsq->getStatus(), "405", "Debugging information: ".var_export($wsq, TRUE));                                       
      $this->assertEquals($wsq->getStatusMessage(), "Method Not Allowed", "Debugging information: ".var_export($wsq, TRUE));          

      unset($wsq);
      unset($settings);
    } 
    */        
    public function testValidInterfaceVersion() {
      
      $settings = new Config();  

      $sparql = new SparqlQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
      
      $query = 'sparql
                select *
                where
                {
                  ?s ?p ?o.
                }';
      
      $sparql->query($query)
             ->dataset($settings->testDataset)
             ->sourceInterface($settings->sparqlInterface)
             ->sourceInterfaceVersion($settings->sparqlInterfaceVersion)
             ->send();
                           
      $this->assertEquals($sparql->getStatus(), "200", "Debugging information: ".var_export($sparql, TRUE));                                       

      unset($sparql);
      unset($settings);    
    }     
    
    public function testInvalidInterfaceVersion() {
      
      $settings = new Config();  
      
      $sparql = new SparqlQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
      
      $query = 'sparql
                select *
                where
                {
                  ?s ?p ?o.
                }';
      
      $sparql->query($query)
             ->dataset($settings->testDataset)
             ->sourceInterface($settings->sparqlInterface)
             ->sourceInterfaceVersion('663.7')
             ->send();
                 
      $this->assertEquals($sparql->getStatus(), "400", "Debugging information: ".var_export($sparql, TRUE));                                       
      $this->assertEquals($sparql->getStatusMessage(), "Bad Request", "Debugging information: ".var_export($sparql, TRUE));
      $this->assertEquals($sparql->error->id, "WS-SPARQL-303", "Debugging information: ".var_export($sparql, TRUE));                                       

      unset($sparql);
      unset($settings);            
    } 
    
    public function testInterfaceExists() {
      
      $settings = new Config();  

      $sparql = new SparqlQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
      
      $query = 'sparql
                select *
                where
                {
                  ?s ?p ?o.
                }';
      
      $sparql->query($query)
             ->dataset($settings->testDataset)
             ->sourceInterface($settings->sparqlInterface)
             ->sourceInterfaceVersion($settings->sparqlInterfaceVersion)
             ->send();
                           
      $this->assertEquals($sparql->getStatus(), "200", "Debugging information: ".var_export($sparql, TRUE));                                       

      unset($sparql);
      unset($settings);  
    }  
    
    
    public function testInterfaceNotExisting() {
      $settings = new Config();  

      $sparql = new SparqlQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
      
      $query = 'sparql
                select *
                where
                {
                  ?s ?p ?o.
                }';
      
      $sparql->query($query)
             ->dataset($settings->testDataset)
             ->sourceInterface('unexisting-interface')
             ->sourceInterfaceVersion($settings->sparqlInterfaceVersion)
             ->send();    
                           
      $this->assertEquals($sparql->getStatus(), "400", "Debugging information: ".var_export($sparql, TRUE));                                       
      $this->assertEquals($sparql->getStatusMessage(), "Bad Request", "Debugging information: ".var_export($sparql, TRUE));
      $this->assertEquals($sparql->error->id, "WS-SPARQL-302", "Debugging information: ".var_export($sparql, TRUE));                                       

      unset($sparql);
      unset($settings);       
    }   
    
    public function test_Records_Description_RDFXML() {
      
      $settings = new Config();  

      $sparql = new SparqlQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
      
      $query = 'sparql
                select *
                where
                {
                  ?s ?p ?o.
                  filter(?s in(<http://now.winnipeg.ca/datasets/Schools/267>))
                }';
      
      $sparql->query($query)
             ->dataset($settings->testDataset)
             ->mime('application/rdf+xml')
             ->sourceInterface($settings->sparqlInterface)
             ->sourceInterfaceVersion($settings->sparqlInterfaceVersion)
             ->send();
                           
      $this->assertEquals($sparql->getStatus(), "200", "Debugging information: ".var_export($sparql, TRUE));                                       

      utilities\validateParameterApplicationRdfXml($this, $sparql);
      
      $this->assertTrue(utilities\compareRdf($sparql->getResultset(), file_get_contents($settings->contentDir.'validation/sparql_single_record.xml')));
      
      unset($sparql);
      unset($settings);  
    } 
    
    public function test_Records_Description_RDFN3() {
      
      $settings = new Config();  

      $sparql = new SparqlQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
      
      $query = 'sparql
                select *
                where
                {
                  ?s ?p ?o.
                  filter(?s in(<http://now.winnipeg.ca/datasets/Schools/267>))
                }';
      
      $sparql->query($query)
             ->dataset($settings->testDataset)
             ->mime('application/rdf+n3')
             ->sourceInterface($settings->sparqlInterface)
             ->sourceInterfaceVersion($settings->sparqlInterfaceVersion)
             ->send();
                           
      $this->assertEquals($sparql->getStatus(), "200", "Debugging information: ".var_export($sparql, TRUE));                                       

      utilities\validateParameterApplicationRdfN3($this, $sparql);
      
      $this->assertTrue(utilities\compareRdf($sparql->getResultset(), file_get_contents($settings->contentDir.'validation/sparql_single_record.xml')));
      
      unset($sparql);
      unset($settings);  
    } 
    
    public function test_Records_Description_StructJSON() {
      
      $settings = new Config();  

      $sparql = new SparqlQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
      
      $query = 'sparql
                select *
                where
                {
                  ?s ?p ?o.
                  filter(?s in(<http://now.winnipeg.ca/datasets/Schools/267>))
                }';
      
      $sparql->query($query)
             ->dataset($settings->testDataset)
             ->mime('application/json')
             ->sourceInterface($settings->sparqlInterface)
             ->sourceInterfaceVersion($settings->sparqlInterfaceVersion)
             ->send();
                           
      $this->assertEquals($sparql->getStatus(), "200", "Debugging information: ".var_export($sparql, TRUE));                                       
      
      utilities\validateParameterApplicationJson($this, $sparql);
    
      $this->assertTrue(utilities\compareStructJSON($sparql->getResultset(), file_get_contents($settings->contentDir.'validation/sparql_single_record.json')));
      
      unset($sparql);
      unset($settings);  
    }       
    
    public function test_Records_Description_StructXML() {
      
      $settings = new Config();  

      $sparql = new SparqlQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
      
      $query = 'sparql
                select *
                where
                {
                  ?s ?p ?o.
                  filter(?s in(<http://now.winnipeg.ca/datasets/Schools/267>))
                }';
      
      $sparql->query($query)
             ->dataset($settings->testDataset)
             ->mime('text/xml')
             ->sourceInterface($settings->sparqlInterface)
             ->sourceInterfaceVersion($settings->sparqlInterfaceVersion)
             ->send();
                           
      $this->assertEquals($sparql->getStatus(), "200", "Debugging information: ".var_export($sparql, TRUE));                                       
            
      $resultset = new Resultset();
      
      // To test the structXML serialization, we import it into a Resultset() object and we export as RDF+XML
      // then we compare it to the expected RDF+XML resultset.
      $resultset->importStructXMLResultset($sparql->getResultset());
            
      $this->assertTrue(utilities\compareRdf($resultset->getResultsetRDFXML(), file_get_contents($settings->contentDir.'validation/sparql_single_record.xml')));
      
      unset($sparql);
      unset($settings);  
    }       
    
    public function test_Records_Description_Resultset() {
      
      $settings = new Config();  

      $sparql = new SparqlQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
      
      $query = 'sparql
                select *
                where
                {
                  ?s ?p ?o.
                  filter(?s in(<http://now.winnipeg.ca/datasets/Schools/267>))
                }';
      
      $sparql->query($query)
             ->dataset($settings->testDataset)
             ->mime('resultset')
             ->sourceInterface($settings->sparqlInterface)
             ->sourceInterfaceVersion($settings->sparqlInterfaceVersion)
             ->send();
                           
      $this->assertEquals($sparql->getStatus(), "200", "Debugging information: ".var_export($sparql, TRUE));                                       

      $resultset = $sparql->getResultset()->getResultset();
      
      $this->assertTrue(count($resultset['unspecified']['http://now.winnipeg.ca/datasets/Schools/267']) == 15);
      $this->assertTrue(isset($resultset['unspecified']['http://now.winnipeg.ca/datasets/Schools/267']));
      
      unset($sparql);
      unset($settings);  
    } 
    
    public function test_Record_Description_With_Graph_Resultset() {
      
      $settings = new Config();  

      $sparql = new SparqlQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
      
      $query = 'sparql
                select *
                from named <'.$settings->testDataset.'>
                where
                {
                  graph ?g {
                    ?s ?p ?o.
                    filter(?s in(<http://now.winnipeg.ca/datasets/Schools/267>))
                  }
                }';
      
      $sparql->query($query)
             ->dataset($settings->testDataset)
             ->mime('resultset')
             ->sourceInterface($settings->sparqlInterface)
             ->sourceInterfaceVersion($settings->sparqlInterfaceVersion)
             ->send();
                           
      $this->assertEquals($sparql->getStatus(), "200", "Debugging information: ".var_export($sparql, TRUE));                                       

      $resultset = $sparql->getResultset()->getResultset();

      $this->assertTrue(count($resultset['http://test.com/unittests/']['http://now.winnipeg.ca/datasets/Schools/267']) == 16);
      $this->assertTrue(isset($resultset['http://test.com/unittests/']['http://now.winnipeg.ca/datasets/Schools/267']));
      
      unset($sparql);
      unset($settings);  
    }   
    
    public function test_Record_Description_Default_Graph_Uri_Param() {
      
      $settings = new Config();  

      $sparql = new SparqlQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
      
      $query = 'sparql
                select *
                where
                {
                  ?s ?p ?o.
                  filter(?s in(<http://now.winnipeg.ca/datasets/Schools/267>))
                }';
      
      $sparql->query($query)
             ->defaultGraphUri($settings->testDataset)
             ->mime('resultset')
             ->sourceInterface($settings->sparqlInterface)
             ->sourceInterfaceVersion($settings->sparqlInterfaceVersion)
             ->send();
                           
      $this->assertEquals($sparql->getStatus(), "200", "Debugging information: ".var_export($sparql, TRUE));                                       

      $resultset = $sparql->getResultset()->getResultset();

      $this->assertTrue(count($resultset['unspecified']['http://now.winnipeg.ca/datasets/Schools/267']) == 15);
      $this->assertTrue(isset($resultset['unspecified']['http://now.winnipeg.ca/datasets/Schools/267']));
      
      unset($sparql);
      unset($settings);  
    }   
    
    public function test_Record_Description_Named_Graph_Uri_Param() {
      
      $settings = new Config();  

      $sparql = new SparqlQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
      
      $query = 'sparql
                select *
                where
                {
                  ?s ?p ?o.
                  filter(?s in(<http://now.winnipeg.ca/datasets/Schools/267>))
                }';
      
      $sparql->query($query)
             ->namedGraphUri($settings->testDataset)
             ->mime('resultset')
             ->sourceInterface($settings->sparqlInterface)
             ->sourceInterfaceVersion($settings->sparqlInterfaceVersion)
             ->send();
                           
      $this->assertEquals($sparql->getStatus(), "200", "Debugging information: ".var_export($sparql, TRUE));                                       

      $resultset = $sparql->getResultset()->getResultset();

      $this->assertTrue(count($resultset['unspecified']['http://now.winnipeg.ca/datasets/Schools/267']) == 15);
      $this->assertTrue(isset($resultset['unspecified']['http://now.winnipeg.ca/datasets/Schools/267']));
      
      unset($sparql);
      unset($settings);  
    }        
    
    public function test_Sparql_Update_Modify() {
      
      $settings = new Config();  

      $sparql = new SparqlQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
      
      $query = 'sparql
                modify <http://now.winnipeg.ca/datasets/Schools/267>
                delete {?s ?p ?o}';
      
      $sparql->query($query)
             ->namedGraphUri($settings->testDataset)
             ->mime('resultset')
             ->sourceInterface($settings->sparqlInterface)
             ->sourceInterfaceVersion($settings->sparqlInterfaceVersion)
             ->send();
                           
      $this->assertEquals($sparql->getStatus(), "400", "Debugging information: ".var_export($sparql, TRUE));                                       
      $this->assertEquals($sparql->getStatusMessage(), "Bad Request", "Debugging information: ".var_export($sparql, TRUE));
      $this->assertEquals($sparql->error->id, "WS-SPARQL-203", "Debugging information: ".var_export($sparql, TRUE));                                       
      
      unset($sparql);
      unset($settings);  
    }
    
    public function test_Sparql_Update_Delete() {
      
      $settings = new Config();  

      $sparql = new SparqlQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
      
      $query = 'sparql
                delete {?s ?p ?o}';
      
      $sparql->query($query)
             ->namedGraphUri($settings->testDataset)
             ->mime('resultset')
             ->sourceInterface($settings->sparqlInterface)
             ->sourceInterfaceVersion($settings->sparqlInterfaceVersion)
             ->send();
                           
      $this->assertEquals($sparql->getStatus(), "400", "Debugging information: ".var_export($sparql, TRUE));                                       
      $this->assertEquals($sparql->getStatusMessage(), "Bad Request", "Debugging information: ".var_export($sparql, TRUE));
      $this->assertEquals($sparql->error->id, "WS-SPARQL-203", "Debugging information: ".var_export($sparql, TRUE));                                       
      
      unset($sparql);
      unset($settings);  
    }
    
    public function test_Sparql_Update_Insert() {
      
      $settings = new Config();  

      $sparql = new SparqlQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
      
      $query = 'sparql
                insert {<a> <b> <c>}';
      
      $sparql->query($query)
             ->namedGraphUri($settings->testDataset)
             ->mime('resultset')
             ->sourceInterface($settings->sparqlInterface)
             ->sourceInterfaceVersion($settings->sparqlInterfaceVersion)
             ->send();
                           
      $this->assertEquals($sparql->getStatus(), "400", "Debugging information: ".var_export($sparql, TRUE));                                       
      $this->assertEquals($sparql->getStatusMessage(), "Bad Request", "Debugging information: ".var_export($sparql, TRUE));
      $this->assertEquals($sparql->error->id, "WS-SPARQL-203", "Debugging information: ".var_export($sparql, TRUE));                                       
      
      unset($sparql);
      unset($settings);  
    }
    
    public function test_Sparql_Update_Load() {
      
      $settings = new Config();  

      $sparql = new SparqlQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
      
      $query = 'sparql
                load <a> into <b>';
      
      $sparql->query($query)
             ->namedGraphUri($settings->testDataset)
             ->mime('resultset')
             ->sourceInterface($settings->sparqlInterface)
             ->sourceInterfaceVersion($settings->sparqlInterfaceVersion)
             ->send();
                           
      $this->assertEquals($sparql->getStatus(), "400", "Debugging information: ".var_export($sparql, TRUE));                                       
      $this->assertEquals($sparql->getStatusMessage(), "Bad Request", "Debugging information: ".var_export($sparql, TRUE));
      $this->assertEquals($sparql->error->id, "WS-SPARQL-203", "Debugging information: ".var_export($sparql, TRUE));                                       
      
      unset($sparql);
      unset($settings);  
    }    

    public function test_Sparql_Update_Clear() {
      
      $settings = new Config();  

      $sparql = new SparqlQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
      
      $query = 'sparql
                clear graph <'.$settings->testDataset.'>';
      
      $sparql->query($query)
             ->namedGraphUri($settings->testDataset)
             ->mime('resultset')
             ->sourceInterface($settings->sparqlInterface)
             ->sourceInterfaceVersion($settings->sparqlInterfaceVersion)
             ->send();
                           
      $this->assertEquals($sparql->getStatus(), "400", "Debugging information: ".var_export($sparql, TRUE));                                       
      $this->assertEquals($sparql->getStatusMessage(), "Bad Request", "Debugging information: ".var_export($sparql, TRUE));
      $this->assertEquals($sparql->error->id, "WS-SPARQL-203", "Debugging information: ".var_export($sparql, TRUE));                                       
      
      unset($sparql);
      unset($settings);  
    }      

    public function test_Sparql_Update_Create() {
      
      $settings = new Config();  

      $sparql = new SparqlQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
      
      $query = 'sparql
                create graph <'.$settings->testDataset.'>';
      
      $sparql->query($query)
             ->namedGraphUri($settings->testDataset)
             ->mime('resultset')
             ->sourceInterface($settings->sparqlInterface)
             ->sourceInterfaceVersion($settings->sparqlInterfaceVersion)
             ->send();
                           
      $this->assertEquals($sparql->getStatus(), "400", "Debugging information: ".var_export($sparql, TRUE));                                       
      $this->assertEquals($sparql->getStatusMessage(), "Bad Request", "Debugging information: ".var_export($sparql, TRUE));
      $this->assertEquals($sparql->error->id, "WS-SPARQL-203", "Debugging information: ".var_export($sparql, TRUE));                                       
      
      unset($sparql);
      unset($settings);  
    }      

    public function test_Sparql_Update_Drop() {
      
      $settings = new Config();  

      $sparql = new SparqlQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
      
      $query = 'sparql
                drop graph <'.$settings->testDataset.'>';
      
      $sparql->query($query)
             ->namedGraphUri($settings->testDataset)
             ->mime('resultset')
             ->sourceInterface($settings->sparqlInterface)
             ->sourceInterfaceVersion($settings->sparqlInterfaceVersion)
             ->send();
                           
      $this->assertEquals($sparql->getStatus(), "400", "Debugging information: ".var_export($sparql, TRUE));                                       
      $this->assertEquals($sparql->getStatusMessage(), "Bad Request", "Debugging information: ".var_export($sparql, TRUE));
      $this->assertEquals($sparql->error->id, "WS-SPARQL-203", "Debugging information: ".var_export($sparql, TRUE));                                       
      
      unset($sparql);
      unset($settings);  
    }        
         
    public function test_Record_Description_Graph_Not_Permit() {
      
      $settings = new Config();  

      $sparql = new SparqlQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
      
      $query = 'sparql
                select *
                where
                {
                  graph ?g {
                    ?s ?p ?o.
                    filter(?s in(<http://now.winnipeg.ca/datasets/Schools/267>))
                  }
                }';
      
      $sparql->query($query)
             ->dataset($settings->testDataset)
             ->mime('resultset')
             ->sourceInterface($settings->sparqlInterface)
             ->sourceInterfaceVersion($settings->sparqlInterfaceVersion)
             ->send();
                           
      $this->assertEquals($sparql->getStatus(), "400", "Debugging information: ".var_export($sparql, TRUE));                                       
      $this->assertEquals($sparql->getStatusMessage(), "Bad Request", "Debugging information: ".var_export($sparql, TRUE));
      $this->assertEquals($sparql->error->id, "WS-SPARQL-205", "Debugging information: ".var_export($sparql, TRUE));                                       
      
      unset($sparql);
      unset($settings);  
    } 
   
    public function test_Record_Description_No_Records() {
      
      $settings = new Config();  

      $sparql = new SparqlQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
      
      $query = 'sparql
                select *
                from named <'.$settings->testDataset.'test>
                where
                {
                  graph ?g {
                    ?s ?p ?o.
                    filter(?s in(<http://now.winnipeg.ca/datasets/Schools/267>))
                  }
                }';
      
      $sparql->query($query)
             ->dataset($settings->testDataset)
             ->mime('resultset')
             ->sourceInterface($settings->sparqlInterface)
             ->sourceInterfaceVersion($settings->sparqlInterfaceVersion)
             ->send();

      $this->assertTrue($sparql->getStatus() == ('400' || '403'), "Debugging information: ".var_export($sparql, TRUE));
      $this->assertTrue($sparql->getStatusMessage() == ('Bad Request' || 'Forbidden'), "Debugging information: ".var_export($sparql, TRUE));
      $this->assertTrue($sparql->error->id == ('WS-SPARQL-301' || 'WS-AUTH-VALIDATOR-303'), "Debugging information: ".var_export($sparql, TRUE)); 
      
      unset($sparql);
      unset($settings);  
    }     
    
    public function test_Construct_RDFN3() {
      
      $settings = new Config();  

      $sparql = new SparqlQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
      
      $query = 'sparql
                construct
                {
                  ?s <http://purl.org/ontology/iron#prefLabel> ?o.
                }
                where
                {
                  ?s a <http://purl.org/ontology/now#Schools> ;
                     <http://purl.org/ontology/iron#prefLabel> ?o.
                }';
      
      $sparql->query($query)
             ->dataset($settings->testDataset)
             ->mime('text/rdf+n3')
             ->sourceInterface($settings->sparqlInterface)
             ->sourceInterfaceVersion($settings->sparqlInterfaceVersion)
             ->send();

      $this->assertTrue(strpos($sparql->getResultset(), '<http://now.winnipeg.ca/datasets/Schools/247>') !== FALSE);
      
      unset($sparql);
      unset($settings);  
    }     
       
    public function test_Construct_RDFXML() {
      
      $settings = new Config();  

      $sparql = new SparqlQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
      
      $query = 'sparql
                construct
                {
                  ?s <http://purl.org/ontology/iron#prefLabel> ?o.
                }
                where
                {
                  ?s a <http://purl.org/ontology/now#Schools> ;
                     <http://purl.org/ontology/iron#prefLabel> ?o.
                }';
      
      $sparql->query($query)
             ->dataset($settings->testDataset)
             ->mime('application/rdf+xml')
             ->sourceInterface($settings->sparqlInterface)
             ->sourceInterfaceVersion($settings->sparqlInterfaceVersion)
             ->send();

      $this->assertTrue(strpos($sparql->getResultset(), '<rdf:Description rdf:about="http://now.winnipeg.ca/datasets/Schools/165">') !== FALSE);
      
      unset($sparql);
      unset($settings);  
    }   
    
    public function test_Construct_RDFJSON() {
      
      $settings = new Config();  

      $sparql = new SparqlQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
      
      $query = 'sparql
                construct
                {
                  ?s <http://purl.org/ontology/iron#prefLabel> ?o.
                }
                where
                {
                  ?s a <http://purl.org/ontology/now#Schools> ;
                     <http://purl.org/ontology/iron#prefLabel> ?o.
                }';
      
      $sparql->query($query)
             ->dataset($settings->testDataset)
             ->mime('application/rdf+json')
             ->sourceInterface($settings->sparqlInterface)
             ->sourceInterfaceVersion($settings->sparqlInterfaceVersion)
             ->send();

      $this->assertTrue(strpos($sparql->getResultset(), '"http://now.winnipeg.ca/datasets/Schools/69"') !== FALSE);
      
      unset($sparql);
      unset($settings);  
    }  

    public function test_Construct_NTRIPLES() {
      
      $settings = new Config();  

      $sparql = new SparqlQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
      
      $query = 'sparql
                construct
                {
                  ?s <http://purl.org/ontology/iron#prefLabel> ?o.
                }
                where
                {
                  ?s a <http://purl.org/ontology/now#Schools> ;
                     <http://purl.org/ontology/iron#prefLabel> ?o.
                }';
      
      $sparql->query($query)
             ->dataset($settings->testDataset)
             ->mime('text/plain')
             ->sourceInterface($settings->sparqlInterface)
             ->sourceInterfaceVersion($settings->sparqlInterfaceVersion)
             ->send();

      $this->assertTrue(strpos($sparql->getResultset(), '<http://now.winnipeg.ca/datasets/Schools/71>') !== FALSE);
      
      unset($sparql);
      unset($settings);  
    }
    
    public function test_Describe_RDFN3() {
      
      $settings = new Config();  

      $sparql = new SparqlQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
      
      $query = 'describe  ?s
                where
                {
                  ?s a <http://purl.org/ontology/now#Schools> .
                }';
                      
      $sparql->query($query)
             ->dataset($settings->testDataset)
             ->mime('text/rdf+n3')
             ->sourceInterface($settings->sparqlInterface)
             ->sourceInterfaceVersion($settings->sparqlInterfaceVersion)
             ->send();

      $this->assertTrue(substr_count($sparql->getResultset(), 'rdf:type') == 296);
      
      unset($sparql);
      unset($settings);  
    }     
       
    public function test_Describe_RDFXML() {
      
      $settings = new Config();  

      $sparql = new SparqlQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
      
      $query = 'describe  ?s
                where
                {
                  ?s a <http://purl.org/ontology/now#Schools> .
                }';
      
      $sparql->query($query)
             ->dataset($settings->testDataset)
             ->mime('application/rdf+xml')
             ->sourceInterface($settings->sparqlInterface)
             ->sourceInterfaceVersion($settings->sparqlInterfaceVersion)
             ->send();

      $this->assertTrue((substr_count($sparql->getResultset(), 'prefLabel') / 2) == 296);
      
      unset($sparql);
      unset($settings);  
    }   
    
    public function test_Describe_RDFJSON() {
      
      $settings = new Config();  

      $sparql = new SparqlQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
      
      $query = 'describe  ?s
                where
                {
                  ?s a <http://purl.org/ontology/now#Schools> .
                }';
      
      $sparql->query($query)
             ->dataset($settings->testDataset)
             ->mime('application/rdf+json')
             ->sourceInterface($settings->sparqlInterface)
             ->sourceInterfaceVersion($settings->sparqlInterfaceVersion)
             ->send();

      $this->assertTrue(substr_count($sparql->getResultset(), 'http://www.w3.org/1999/02/22-rdf-syntax-ns#type') == 296);
      
      unset($sparql);
      unset($settings);  
    }  

    public function test_Describe_NTRIPLES() {
      
      $settings = new Config();  

      $sparql = new SparqlQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
      
      $query = 'describe  ?s
                where
                {
                  ?s a <http://purl.org/ontology/now#Schools> .
                }';
      
      $sparql->query($query)
             ->dataset($settings->testDataset)
             ->mime('text/plain')
             ->sourceInterface($settings->sparqlInterface)
             ->sourceInterfaceVersion($settings->sparqlInterfaceVersion)
             ->send();

      $this->assertTrue(substr_count($sparql->getResultset(), 'rdf:type') == 296);
      
      unset($sparql);
      unset($settings);  
    }    
  }

  
?>