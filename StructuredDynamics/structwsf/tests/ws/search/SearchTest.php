<?php

  namespace StructuredDynamics\structwsf\tests\ws\search;
  
  use StructuredDynamics\structwsf\framework\WebServiceQuerier;
  use StructuredDynamics\structwsf\php\api\ws\search\SearchQuery;
  use StructuredDynamics\structwsf\php\api\ws\search\ExtendedFiltersBuilder;
  use StructuredDynamics\structwsf\tests\Config;
  use StructuredDynamics\structwsf\tests\content\validation\CrudReadContentValidation;
  use StructuredDynamics\structwsf\framework\Resultset;
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
  
  class SearchTest extends \PHPUnit_Framework_TestCase {
    
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
    
    public function testWrongEndpointUrl() {

      $settings = new Config();   
      
      $wsq = new WebServiceQuerier($settings->endpointUrl . "search/" . "wrong", 
                                   "post", 
                                   "text/xml",
                                   "query=".
                                   "&types=".
                                   "&datasets=".
                                   "&attributes=".
                                   "&attributes_boolean_operator=".
                                   "&include_attributes_list=".
                                   "&items=".
                                   "&page=".
                                   "&include_aggregates=".
                                   "&aggregate_attributes=".
                                   "&aggregate_attributes_object_type=".
                                   "&aggregate_attributes_object_nb=".
                                   "&distance_filter=".
                                   "&range_filter=".
                                   "&lang=".
                                   "&sort=".
                                   "&results_location_aggregator=".
                                   "&extended_filters=".
                                   "&types_boost=".
                                   "&datasets_boost=".
                                   "&attributes_boost=".
                                   "&spellcheck=".
                                   "&interface=". urlencode($settings->searchInterface) .
                                   "&version=". urlencode($settings->searchInterfaceVersion) .
                                   "&registered_ip=" . urlencode("Self"));        
                         
      $this->assertEquals($wsq->getStatus(), "404", "Debugging information: ".var_export($wsq, TRUE));                                       
      $this->assertEquals($wsq->getStatusMessage(), "Not Found", "Debugging information: ".var_export($wsq, TRUE));
      
      unset($wsq);
      unset($settings);
    }
    
    
    public function testWrongEndpointMethodGet(){
      
      $settings = new Config();  
      
      $wsq = new WebServiceQuerier($settings->endpointUrl . "/search/",
                                   "get", 
                                   "text/xml",
                                   "query=".
                                   "&types=".
                                   "&datasets=".
                                   "&attributes=".
                                   "&attributes_boolean_operator=".
                                   "&include_attributes_list=".
                                   "&items=".
                                   "&page=".
                                   "&include_aggregates=".
                                   "&aggregate_attributes=".
                                   "&aggregate_attributes_object_type=".
                                   "&aggregate_attributes_object_nb=".
                                   "&distance_filter=".
                                   "&range_filter=".
                                   "&lang=".
                                   "&sort=".
                                   "&results_location_aggregator=".
                                   "&extended_filters=".
                                   "&types_boost=".
                                   "&datasets_boost=".
                                   "&attributes_boost=".
                                   "&spellcheck=".
                                   "&interface=". urlencode($settings->crudReadInterface) .
                                   "&version=". urlencode($settings->crudReadInterfaceVersion) .
                                   "&registered_ip=" . urlencode("Self"));
                                   
      $this->assertEquals($wsq->getStatus(), "405", "Debugging information: ".var_export($wsq, TRUE));                                       
      $this->assertEquals($wsq->getStatusMessage(), "Method Not Allowed", "Debugging information: ".var_export($wsq, TRUE));          

      unset($wsq);
      unset($settings);
    } 
            
    public function testValidInterfaceVersion() {
      
      $settings = new Config();  
      
      $search = new SearchQuery($settings->endpointUrl);
      
      $search->sourceInterface($settings->searchInterface)
             ->sourceInterfaceVersion($settings->searchInterfaceVersion)
             ->send();
                           
      $this->assertEquals($search->getStatus(), "200", "Debugging information: ".var_export($search, TRUE));                                       

      unset($search);
      unset($settings);    
    }     
    
    public function testInvalidInterfaceVersion() {
      
      $settings = new Config();  
      
      $search = new SearchQuery($settings->endpointUrl);
      
      $search->sourceInterface($settings->searchInterface)
             ->sourceInterfaceVersion('667.7')
             ->send();
                           
      $this->assertEquals($search->getStatus(), "400", "Debugging information: ".var_export($search, TRUE));                                       
      $this->assertEquals($search->getStatusMessage(), "Bad Request", "Debugging information: ".var_export($search, TRUE));
      $this->assertEquals($search->error->id, "WS-SEARCH-303", "Debugging information: ".var_export($search, TRUE));                                       

      unset($search);
      unset($settings);            
    } 
    
    public function testInterfaceExists() {
      
      $settings = new Config();  
      
      $search = new SearchQuery($settings->endpointUrl);
      
      $search->datasetFilter($settings->testDataset)
             ->sourceInterface($settings->searchInterface)
             ->sourceInterfaceVersion($settings->searchInterfaceVersion)
             ->send();

      $this->assertEquals($search->getStatus(), "200", "Debugging information: ".var_export($search, TRUE));                                       

      unset($search);
      unset($settings);     
    }  
    
    
    public function testInterfaceNotExisting() {
      
      $settings = new Config();  
      
      $search = new SearchQuery($settings->endpointUrl);
      
      $search->sourceInterface('interface-not-existing')
             ->sourceInterfaceVersion($settings->searchInterfaceVersion)
             ->send();
                           
      $this->assertEquals($search->getStatus(), "400", "Debugging information: ".var_export($search, TRUE));                                       
      $this->assertEquals($search->getStatusMessage(), "Bad Request", "Debugging information: ".var_export($search, TRUE));
      $this->assertEquals($search->error->id, "WS-SEARCH-302", "Debugging information: ".var_export($search, TRUE));                                       

      unset($search);
      unset($settings);       
    }     

    public function testSearch_RDFXML() {
      
      $settings = new Config();  
      
      $search = new SearchQuery($settings->endpointUrl);
      
      $search->datasetFilter($settings->testDataset)    
             ->items(2)
             ->mime('application/rdf+xml')
             ->excludeAggregates()
             ->sourceInterface($settings->searchInterface)
             ->sourceInterfaceVersion($settings->searchInterfaceVersion)
             ->send();
             
      $this->assertEquals($search->getStatus(), "200", "Debugging information: ".var_export($search, TRUE));                                       

      utilities\validateParameterApplicationRdfXml($this, $search);

      $this->assertTrue(utilities\compareRdf($search->getResultset(), file_get_contents($settings->contentDir.'validation/search.xml'), TRUE));

      unset($search);
      unset($settings);     
    }      
        
    public function testSearch_RDFN3() {
      
      $settings = new Config();  
      
      $search = new SearchQuery($settings->endpointUrl);
      
      $search->datasetFilter($settings->testDataset)    
             ->items(2)
             ->mime('application/rdf+n3')
             ->excludeAggregates()
             ->sourceInterface($settings->searchInterface)
             ->sourceInterfaceVersion($settings->searchInterfaceVersion)
             ->send();
             
      $this->assertEquals($search->getStatus(), "200", "Debugging information: ".var_export($search, TRUE));                                       

      utilities\validateParameterApplicationRdfN3($this, $search);
      
      $this->assertTrue(utilities\compareRdf($search->getResultset(), file_get_contents($settings->contentDir.'validation/search.xml'), TRUE));
      
      unset($search);
      unset($settings);     
    }          
        
    public function testSearch_StructJSON() {
      
      $settings = new Config();  
      
      $search = new SearchQuery($settings->endpointUrl);
      
      $search->datasetFilter($settings->testDataset)    
             ->items(2)
             ->mime('application/json')
             ->excludeAggregates()
             ->sourceInterface($settings->searchInterface)
             ->sourceInterfaceVersion($settings->searchInterfaceVersion)
             ->send();
             
      $this->assertEquals($search->getStatus(), "200", "Debugging information: ".var_export($search, TRUE));                                       

      utilities\validateParameterApplicationJson($this, $search);
      
      $this->assertTrue(utilities\compareStructJSON($search->getResultset(), file_get_contents($settings->contentDir.'validation/search.json')));
      
      unset($search);
      unset($settings);     
    }       
        
    public function testSearch_StructXML() {
      
      $settings = new Config();  
      
      $search = new SearchQuery($settings->endpointUrl);
      
      $search->datasetFilter($settings->testDataset)    
             ->items(2)
             ->mime('text/xml')
             ->excludeAggregates()
             ->sourceInterface($settings->searchInterface)
             ->sourceInterfaceVersion($settings->searchInterfaceVersion)
             ->send();
             
      $this->assertEquals($search->getStatus(), "200", "Debugging information: ".var_export($search, TRUE));                                       

      utilities\validateParameterTextXml($this, $search);

      $resultset = new Resultset($settings->endpointUrl);
      
      // To test the structXML serialization, we import it into a Resultset() object and we export as RDF+XML
      // then we compare it to the expected RDF+XML resultset.
      $resultset->importStructXMLResultset($search->getResultset());
            
      $this->assertTrue(utilities\compareRdf($resultset->getResultsetRDFXML(), file_get_contents($settings->contentDir.'validation/search.xml'), TRUE));
      
      unset($search);
      unset($settings);     
    }       
        
    public function testSearch_Resultset() {
      
      $settings = new Config();  
      
      $search = new SearchQuery($settings->endpointUrl);
      
      $search->datasetFilter($settings->testDataset)    
             ->items(2)
             ->mime('resultset')
             ->excludeAggregates()
             ->sourceInterface($settings->searchInterface)
             ->sourceInterfaceVersion($settings->searchInterfaceVersion)
             ->send();
             
      $this->assertEquals($search->getStatus(), "200", "Debugging information: ".var_export($search, TRUE));                                       

      // To test the Resultset we export it as RDF+XML
      // then we compare it to the expected RDF+XML resultset.
      $this->assertTrue(utilities\compareRdf($search->getResultset()->getResultsetRDFXML(), file_get_contents($settings->contentDir.'validation/search.xml'), TRUE));
      
      unset($search);
      unset($settings);     
    }    

    public function testSearch_IncludeAggregates_RDFXML() {
      
      $settings = new Config();  
      
      $search = new SearchQuery($settings->endpointUrl);
      
      $search->datasetFilter($settings->testDataset)    
             ->items(2)
             ->mime('application/rdf+xml')
             ->includeAggregates()             
             ->sourceInterface($settings->searchInterface)
             ->sourceInterfaceVersion($settings->searchInterfaceVersion)
             ->send();
             
      $this->assertEquals($search->getStatus(), "200", "Debugging information: ".var_export($search, TRUE));                                       

      utilities\validateParameterApplicationRdfXml($this, $search);

      $this->assertTrue(utilities\compareRdf($search->getResultset(), str_replace('--ENDPOINT-URI--', $settings->endpointUri, file_get_contents($settings->contentDir.'validation/search_aggregates.xml')), TRUE));

      unset($search);
      unset($settings);     
    }      
        
    public function testSearch_IncludeAggregates_RDFN3() {
      
      $settings = new Config();  
      
      $search = new SearchQuery($settings->endpointUrl);
      
      $search->datasetFilter($settings->testDataset)    
             ->items(2)
             ->mime('application/rdf+n3')
             ->includeAggregates()
             ->sourceInterface($settings->searchInterface)
             ->sourceInterfaceVersion($settings->searchInterfaceVersion)
             ->send();
             
      $this->assertEquals($search->getStatus(), "200", "Debugging information: ".var_export($search, TRUE));                                       

      utilities\validateParameterApplicationRdfN3($this, $search);
      
      $this->assertTrue(utilities\compareRdf($search->getResultset(), str_replace('--ENDPOINT-URI--', $settings->endpointUri, file_get_contents($settings->contentDir.'validation/search_aggregates.xml')), TRUE));
      
      unset($search);
      unset($settings);     
    }          
        
    public function testSearch_IncludeAggregates_StructJSON() {
      
      $settings = new Config();  
      
      $search = new SearchQuery($settings->endpointUrl);
      
      $search->datasetFilter($settings->testDataset)    
             ->items(2)
             ->mime('application/json')
             ->includeAggregates()
             ->sourceInterface($settings->searchInterface)
             ->sourceInterfaceVersion($settings->searchInterfaceVersion)
             ->send();
             
      $this->assertEquals($search->getStatus(), "200", "Debugging information: ".var_export($search, TRUE));                                       

      utilities\validateParameterApplicationJson($this, $search);

      $this->assertTrue(utilities\compareStructJSON($search->getResultset(), str_replace('--ENDPOINT-URI--', $settings->endpointUri, file_get_contents($settings->contentDir.'validation/search_aggregates.json'))));
      
      unset($search);
      unset($settings);     
    }       
        
    public function testSearch_IncludeAggregates_StructXML() {
      
      $settings = new Config();  
      
      $search = new SearchQuery($settings->endpointUrl);
      
      $search->datasetFilter($settings->testDataset)    
             ->items(2)
             ->mime('text/xml')
             ->includeAggregates()
             ->sourceInterface($settings->searchInterface)
             ->sourceInterfaceVersion($settings->searchInterfaceVersion)
             ->send();
             
      $this->assertEquals($search->getStatus(), "200", "Debugging information: ".var_export($search, TRUE));                                       

      utilities\validateParameterTextXml($this, $search);

      $resultset = new Resultset($settings->endpointUrl);
      
      // To test the structXML serialization, we import it into a Resultset() object and we export as RDF+XML
      // then we compare it to the expected RDF+XML resultset.
      $resultset->importStructXMLResultset($search->getResultset());
            
      $this->assertTrue(utilities\compareRdf($resultset->getResultsetRDFXML(), str_replace('--ENDPOINT-URI--', $settings->endpointUri, file_get_contents($settings->contentDir.'validation/search_aggregates.xml')), TRUE));
      
      unset($search);
      unset($settings);     
    }       
        
    public function testSearch_IncludeAggregates_Resultset() {
      
      $settings = new Config();  
      
      $search = new SearchQuery($settings->endpointUrl);
      
      $search->datasetFilter($settings->testDataset)    
             ->items(2)
             ->mime('resultset')
             ->includeAggregates()
             ->sourceInterface($settings->searchInterface)
             ->sourceInterfaceVersion($settings->searchInterfaceVersion)
             ->send();
             
      $this->assertEquals($search->getStatus(), "200", "Debugging information: ".var_export($search, TRUE));                                       

      // To test the Resultset we export it as RDF+XML
      // then we compare it to the expected RDF+XML resultset.
      $this->assertTrue(utilities\compareRdf($search->getResultset()->getResultsetRDFXML(), str_replace('--ENDPOINT-URI--', $settings->endpointUri, file_get_contents($settings->contentDir.'validation/search_aggregates.xml')), TRUE));
      
      unset($search);
      unset($settings);     
    }    
            
    public function testSearch_QueryExisting_SingleWord_Resultset() {
      
      $settings = new Config();  
      
      $search = new SearchQuery($settings->endpointUrl);
      
      $search->query('Crescent')
             ->datasetFilter($settings->testDataset)    
             ->items(1)
             ->mime('resultset')
             ->includeAggregates()
             ->sourceInterface($settings->searchInterface)
             ->sourceInterfaceVersion($settings->searchInterfaceVersion)
             ->send();
             
      $this->assertEquals($search->getStatus(), "200", "Debugging information: ".var_export($search, TRUE));                                       
      
      $resultset = $search->getResultset()->getResultset();
      
      // There are 4 neighbourhoods that match this query string
      $this->assertTrue(isset($resultset['unspecified'][''.$settings->endpointUri.'search/aggregate/b06bcd7e555a2d4412416d0f617153f3']));
      
      // There are 3 schools that belong to a neigbourhood that match tihs query
      $this->assertTrue(isset($resultset['unspecified'][''.$settings->endpointUri.'search/aggregate/0839ed9a7080e5f3a70ed6ad6a2e3aec']));
      
      unset($search);
      unset($settings);     
    }   
    
    public function testSearch_QueryExisting_SingleWordWithWildcard_Resultset() {
      
      $settings = new Config();  
      
      $search = new SearchQuery($settings->endpointUrl);
      
      $search->query('Crescent*')
             ->datasetFilter($settings->testDataset)    
             ->items(1)
             ->mime('resultset')
             ->includeAggregates()
             ->sourceInterface($settings->searchInterface)
             ->sourceInterfaceVersion($settings->searchInterfaceVersion)
             ->send();
             
      $this->assertEquals($search->getStatus(), "200", "Debugging information: ".var_export($search, TRUE));                                       
      
      $resultset = $search->getResultset()->getResultset();

      // There are 5 neighbourhoods that match this query string
      $this->assertTrue(isset($resultset['unspecified'][''.$settings->endpointUri.'search/aggregate/55ec5688960ba10060289ac052802a1c']));
      
      // There are 6 schools that belong to a neigbourhood that match tihs query
      $this->assertTrue(isset($resultset['unspecified'][''.$settings->endpointUri.'search/aggregate/3466580911e0cfeabe4e6cd274d3e3d5']));
      
      // There is 11 results
      $this->assertTrue(isset($resultset['unspecified'][''.$settings->endpointUri.'search/aggregate/c68d31a4da184baa6b099abd3f71e019']));
      
      unset($search);
      unset($settings);     
    }     
    
    public function testSearch_QueryExisting_MultipleWords_Resultset() {
      
      $settings = new Config();  
      
      $search = new SearchQuery($settings->endpointUrl);
      
      $search->query('Crescent Park')
             ->datasetFilter($settings->testDataset)    
             ->items(1)
             ->mime('resultset')
             ->includeAggregates()
             ->sourceInterface($settings->searchInterface)
             ->sourceInterfaceVersion($settings->searchInterfaceVersion)
             ->send();
             
      $this->assertEquals($search->getStatus(), "200", "Debugging information: ".var_export($search, TRUE));                                       
      
      $resultset = $search->getResultset()->getResultset();
   
      // There are 1 neighbourhoods that match this query string
      $this->assertTrue(isset($resultset['unspecified'][''.$settings->endpointUri.'search/aggregate/b6f1ff9042e72ce25028fbb17bdaedef']));
      
      // There are 3 schools that belong to a neigbourhood that match tihs query
      $this->assertTrue(isset($resultset['unspecified'][''.$settings->endpointUri.'search/aggregate/ee9e0fe8c19d7b828ec4953ee4c16389']));
      
      // There is 4 results
      $this->assertTrue(isset($resultset['unspecified'][''.$settings->endpointUri.'search/aggregate/88f43bf4b1018779daad3f9fc2126e87']));
      
      unset($search);
      unset($settings);     
    }  
    
    public function testSearch_QueryExisting_NotBooleanOperator_Resultset() {
      
      $settings = new Config();  
      
      $search = new SearchQuery($settings->endpointUrl);
      
      $search->query('Crescent NOT Park')
             ->datasetFilter($settings->testDataset)    
             ->items(1)
             ->mime('resultset')
             ->includeAggregates()
             ->sourceInterface($settings->searchInterface)
             ->sourceInterfaceVersion($settings->searchInterfaceVersion)
             ->send();
             
      $this->assertEquals($search->getStatus(), "200", "Debugging information: ".var_export($search, TRUE));                                       
      
      $resultset = $search->getResultset()->getResultset();

      // There are 3 neighbourhoods that match this query string
      $this->assertTrue(isset($resultset['unspecified'][''.$settings->endpointUri.'search/aggregate/80e97fde5b7055c78ab26511bcb98a1a']));
      
      // There is 3 results
      $this->assertTrue(isset($resultset['unspecified'][''.$settings->endpointUri.'search/aggregate/6c3e77e45eaa86ffe19dc6a239b23a95']));
      
      unset($search);
      unset($settings);     
    } 
    
    public function testSearch_ItemsParam30Results_Resultset() {
      
      $settings = new Config();  
      
      $search = new SearchQuery($settings->endpointUrl);
      
      $search->datasetFilter($settings->testDataset)    
             ->items(30)
             ->mime('resultset')
             ->excludeAggregates()
             ->sourceInterface($settings->searchInterface)
             ->sourceInterfaceVersion($settings->searchInterfaceVersion)
             ->send();
             
      $this->assertEquals($search->getStatus(), "200", "Debugging information: ".var_export($search, TRUE));                                       
      
      $resultset = $search->getResultset()->getResultset();

      $this->assertTrue(count($resultset[$settings->testDataset]) == 30);
      
      unset($search);
      unset($settings);     
    }    

    public function testSearch_ItemsParam0Results_Resultset() {
      
      $settings = new Config();  
      
      $search = new SearchQuery($settings->endpointUrl);
      
      $search->datasetFilter($settings->testDataset)    
             ->items(0)
             ->mime('resultset')
             ->excludeAggregates()
             ->sourceInterface($settings->searchInterface)
             ->sourceInterfaceVersion($settings->searchInterfaceVersion)
             ->send();
             
      $this->assertEquals($search->getStatus(), "200", "Debugging information: ".var_export($search, TRUE));                                       
      
      $resultset = $search->getResultset()->getResultset();

      $this->assertTrue(!isset($resultset[$settings->testDataset]));
      
      unset($search);
      unset($settings);     
    } 

    public function testSearch_LanguageEnglish_Resultset() {
      
      $settings = new Config();  
      
      $search = new SearchQuery($settings->endpointUrl);
      
      $search->query('Crescent')
             ->datasetFilter($settings->testDataset)    
             ->items(1)
             ->lang('en')
             ->mime('resultset')
             ->includeAggregates()
             ->sourceInterface($settings->searchInterface)
             ->sourceInterfaceVersion($settings->searchInterfaceVersion)
             ->send();
             
      $this->assertEquals($search->getStatus(), "200", "Debugging information: ".var_export($search, TRUE));                                       
      
      $resultset = $search->getResultset()->getResultset();

      // There are 4 neighbourhoods that match this query string
      $this->assertTrue(isset($resultset['unspecified'][''.$settings->endpointUri.'search/aggregate/b06bcd7e555a2d4412416d0f617153f3']));
      
      // There are 3 schools that belong to a neigbourhood that match tihs query
      $this->assertTrue(isset($resultset['unspecified'][''.$settings->endpointUri.'search/aggregate/0839ed9a7080e5f3a70ed6ad6a2e3aec']));
            
      unset($search);
      unset($settings);     
    } 

    public function testSearch_LanguageFrench_Supported_Unexisting_Resultset() {
      
      $settings = new Config();  
      
      $search = new SearchQuery($settings->endpointUrl);
      
      $search->query('Crescent')
             ->datasetFilter($settings->testDataset)    
             ->items(1)
             ->lang('fr')
             ->mime('resultset')
             ->includeAggregates()
             ->sourceInterface($settings->searchInterface)
             ->sourceInterfaceVersion($settings->searchInterfaceVersion)
             ->send();
             
      if($search->getStatus() == '200')
      {
        $this->assertEquals($search->getStatus(), "200", "Debugging information: ".var_export($search, TRUE));                                       
        
        $resultset = $search->getResultset()->getResultset();

        $this->assertTrue(!isset($resultset[$settings->testDataset]));
      }
      else
      {
        // If we endup here it means that the French language is not supported which may
        // be the case depending on the configuration of structWSF
        $this->assertEquals($search->getStatus(), "400", "Debugging information: ".var_export($search, TRUE));                                       
        $this->assertEquals($search->getStatusMessage(), "Bad Request", "Debugging information: ".var_export($search, TRUE));
        $this->assertEquals($search->error->id, "WS-SEARCH-307", "Debugging information: ".var_export($search, TRUE));                                       
      }
                  
      unset($search);
      unset($settings);     
    }

    public function testSearch_Language_Unsupported_Unexisting_Resultset() {
      
      $settings = new Config();  
      
      $search = new SearchQuery($settings->endpointUrl);
      
      $search->query('Crescent')
             ->datasetFilter($settings->testDataset)    
             ->items(1)
             ->lang('unexisting-language')
             ->mime('resultset')
             ->includeAggregates()
             ->sourceInterface($settings->searchInterface)
             ->sourceInterfaceVersion($settings->searchInterfaceVersion)
             ->send();
             
      $this->assertEquals($search->getStatus(), "400", "Debugging information: ".var_export($search, TRUE));                                       
      $this->assertEquals($search->getStatusMessage(), "Bad Request", "Debugging information: ".var_export($search, TRUE));
      $this->assertEquals($search->error->id, "WS-SEARCH-307", "Debugging information: ".var_export($search, TRUE));                                       
      
      unset($search);
      unset($settings);     
    }
    
    public function testSearch_Sort_PrefLabel_Asc_Resultset() {
      
      $settings = new Config();  
      
      $search = new SearchQuery($settings->endpointUrl);
      
      $search->datasetFilter($settings->testDataset)    
             ->items(1)
             ->sort('prefLabel', 'asc')
             ->mime('resultset')
             ->excludeAggregates()
             ->sourceInterface($settings->searchInterface)
             ->sourceInterfaceVersion($settings->searchInterfaceVersion)
             ->send();
             
      $this->assertEquals($search->getStatus(), "200", "Debugging information: ".var_export($search, TRUE));                                       
      
      $resultset = $search->getResultset()->getResultset();

      $this->assertTrue(isset($resultset[$settings->testDataset]['http://now.winnipeg.ca/datasets/Schools/0']));
                  
      unset($search);
      unset($settings);     
    }    
    
    public function testSearch_Sort_PrefLabelURI_Asc_Resultset() {
      
      $settings = new Config();  
      
      $search = new SearchQuery($settings->endpointUrl);
      
      $search->datasetFilter($settings->testDataset)    
             ->items(1)
             ->sort('http://purl.org/ontology/iron#prefLabel', 'asc')
             ->mime('resultset')
             ->excludeAggregates()
             ->sourceInterface($settings->searchInterface)
             ->sourceInterfaceVersion($settings->searchInterfaceVersion)
             ->send();
             
      $this->assertEquals($search->getStatus(), "200", "Debugging information: ".var_export($search, TRUE));                                       
      
      $resultset = $search->getResultset()->getResultset();

      $this->assertTrue(isset($resultset[$settings->testDataset]['http://now.winnipeg.ca/datasets/Schools/0']));
                  
      unset($search);
      unset($settings);     
    }    
    
    
    public function testSearch_Sort_PrefLabel_Desc_Resultset() {
      
      $settings = new Config();  
      
      $search = new SearchQuery($settings->endpointUrl);
      
      $search->datasetFilter($settings->testDataset)    
             ->items(1)
             ->sort('prefLabel', 'desc')
             ->mime('resultset')
             ->excludeAggregates()
             ->sourceInterface($settings->searchInterface)
             ->sourceInterfaceVersion($settings->searchInterfaceVersion)
             ->send();
             
      $this->assertEquals($search->getStatus(), "200", "Debugging information: ".var_export($search, TRUE));                                       
      
      $resultset = $search->getResultset()->getResultset();
      
      $this->assertTrue(isset($resultset[$settings->testDataset]['http://now.winnipeg.ca/datasets/neighbourhoods/5.517']));
                  
      unset($search);
      unset($settings);     
    }    
    
    public function testSearch_Sort_PrefLabelURI_Desc_Resultset() {
      
      $settings = new Config();  
      
      $search = new SearchQuery($settings->endpointUrl);
      
      $search->datasetFilter($settings->testDataset)    
             ->items(1)
             ->sort('http://purl.org/ontology/iron#prefLabel', 'desc')
             ->mime('resultset')
             ->excludeAggregates()
             ->sourceInterface($settings->searchInterface)
             ->sourceInterfaceVersion($settings->searchInterfaceVersion)
             ->send();
             
      $this->assertEquals($search->getStatus(), "200", "Debugging information: ".var_export($search, TRUE));                                       
      
      $resultset = $search->getResultset()->getResultset();

      $this->assertTrue(isset($resultset[$settings->testDataset]['http://now.winnipeg.ca/datasets/neighbourhoods/5.517']));
                  
      unset($search);
      unset($settings);     
    }  
              
    public function testSearch_Sort_Multivalued_Resultset() {
      
      $settings = new Config();  
      
      $search = new SearchQuery($settings->endpointUrl);
      
      $search->datasetFilter($settings->testDataset)    
             ->items(1)
             ->sort('http://purl.org/ontology/iron#altLabel', 'asc')
             ->mime('resultset')
             ->excludeAggregates()
             ->sourceInterface($settings->searchInterface)
             ->sourceInterfaceVersion($settings->searchInterfaceVersion)
             ->send();
             
      $this->assertEquals($search->getStatus(), "400", "Debugging information: ".var_export($search, TRUE));                                       
      $this->assertEquals($search->getStatusMessage(), "Bad Request", "Debugging information: ".var_export($search, TRUE));
      $this->assertEquals($search->error->id, "WS-SEARCH-308", "Debugging information: ".var_export($search, TRUE));                                       
                  
      unset($search);
      unset($settings);     
    } 

    public function testSearch_Sort_PrefLabel_Unexisting_Ordering_Resultset() {
      
      $settings = new Config();  
      
      $search = new SearchQuery($settings->endpointUrl);
      
      $search->datasetFilter($settings->testDataset)    
             ->items(1)
             ->sort('prefLabel', 'unexisting-ordering')
             ->mime('resultset')
             ->excludeAggregates()
             ->sourceInterface($settings->searchInterface)
             ->sourceInterfaceVersion($settings->searchInterfaceVersion)
             ->send();
             
      $this->assertEquals($search->getStatus(), "400", "Debugging information: ".var_export($search, TRUE));                                       
      $this->assertEquals($search->getStatusMessage(), "Bad Request", "Debugging information: ".var_export($search, TRUE));
      $this->assertEquals($search->error->id, "WS-SEARCH-319", "Debugging information: ".var_export($search, TRUE));                                       
          
      unset($search);
      unset($settings);     
    }      

    public function testSearch_SecondPage_Resultset() {
      
      $settings = new Config();  
      
      $search = new SearchQuery($settings->endpointUrl);
      
      $search->datasetFilter($settings->testDataset)    
             ->items(10)
             ->page(10)
             ->sort('prefLabel', 'asc')
             ->mime('resultset')
             ->excludeAggregates()
             ->sourceInterface($settings->searchInterface)
             ->sourceInterfaceVersion($settings->searchInterfaceVersion)
             ->send();
             
      $this->assertEquals($search->getStatus(), "200", "Debugging information: ".var_export($search, TRUE));                                       
      
      $resultset = $search->getResultset()->getResultset();

      $this->assertTrue(isset($resultset[$settings->testDataset]['http://now.winnipeg.ca/datasets/Schools/5']));
                      
      unset($search);
      unset($settings);     
    }
    
    public function testSearch_NoPageAnymore_Resultset() {
      
      $settings = new Config();  
      
      $search = new SearchQuery($settings->endpointUrl);
      
      $search->datasetFilter($settings->testDataset)    
             ->items(10)
             ->page(1000)
             ->sort('prefLabel', 'asc')
             ->mime('resultset')
             ->excludeAggregates()
             ->sourceInterface($settings->searchInterface)
             ->sourceInterfaceVersion($settings->searchInterfaceVersion)
             ->send();
             
      $this->assertEquals($search->getStatus(), "200", "Debugging information: ".var_export($search, TRUE));                                       
      
      $resultset = $search->getResultset()->getResultset();

      $this->assertTrue(count($resultset) == 0);
                      
      unset($search);
      unset($settings);     
    }    
    
    public function testSearch_Attribute_Boost_Resultset() {
      
      $settings = new Config();  
      
      $search = new SearchQuery($settings->endpointUrl);
      
      $search->datasetFilter($settings->testDataset)    
             ->items(4)
             ->attributeValueBoost('http://purl.org/ontology/now#gradeLevel', 30)
             ->mime('resultset')
             ->excludeAggregates()
             ->sourceInterface($settings->searchInterface)
             ->sourceInterfaceVersion($settings->searchInterfaceVersion)
             ->send();
             
      $this->assertEquals($search->getStatus(), "200", "Debugging information: ".var_export($search, TRUE));                                       
      
      $resultset = $search->getResultset()->getResultset();

      $this->assertTrue(isset($resultset[$settings->testDataset]['http://now.winnipeg.ca/datasets/Schools/295']));
      $this->assertTrue(isset($resultset[$settings->testDataset]['http://now.winnipeg.ca/datasets/Schools/69']));
      $this->assertTrue(isset($resultset[$settings->testDataset]['http://now.winnipeg.ca/datasets/Schools/71']));
      $this->assertTrue(isset($resultset[$settings->testDataset]['http://now.winnipeg.ca/datasets/Schools/75']));
                      
      unset($search);
      unset($settings);     
    }    

    public function testSearch_Attribute_Value_Literal_Boost_Resultset() {
      
      $settings = new Config();  
      
      $search = new SearchQuery($settings->endpointUrl);
      
      $search->datasetFilter($settings->testDataset)    
             ->items(4)
             ->attributeValueBoost('http://purl.org/ontology/now#gradeLevel', 30, 'K-8')
             ->mime('resultset')
             ->excludeAggregates()
             ->sourceInterface($settings->searchInterface)
             ->sourceInterfaceVersion($settings->searchInterfaceVersion)
             ->send();
             
      $this->assertEquals($search->getStatus(), "200", "Debugging information: ".var_export($search, TRUE));                                       
      
      $resultset = $search->getResultset()->getResultset();

      $this->assertTrue(isset($resultset[$settings->testDataset]['http://now.winnipeg.ca/datasets/Schools/71']));
      $this->assertTrue(isset($resultset[$settings->testDataset]['http://now.winnipeg.ca/datasets/Schools/75']));
      $this->assertTrue(isset($resultset[$settings->testDataset]['http://now.winnipeg.ca/datasets/Schools/47']));
      $this->assertTrue(isset($resultset[$settings->testDataset]['http://now.winnipeg.ca/datasets/Schools/51']));
                      
      unset($search);
      unset($settings);     
    }   
    
    public function testSearch_Attribute_Value_URI_Boost_Resultset() {
      
      $settings = new Config();  
      
      $search = new SearchQuery($settings->endpointUrl);
      
      $search->datasetFilter($settings->testDataset)    
             ->items(4)
             ->attributeValueBoost('http://www.geonames.org/ontology#locatedIn', 30, 'http://now.winnipeg.ca/datasets/neighbourhoods/1.601', TRUE)
             ->mime('resultset')
             ->excludeAggregates()
             ->sourceInterface($settings->searchInterface)
             ->sourceInterfaceVersion($settings->searchInterfaceVersion)
             ->send();
             
      $this->assertEquals($search->getStatus(), "200", "Debugging information: ".var_export($search, TRUE));                                       
      
      $resultset = $search->getResultset()->getResultset();

      $this->assertTrue(isset($resultset[$settings->testDataset]['http://now.winnipeg.ca/datasets/Schools/149']));
      $this->assertTrue(isset($resultset[$settings->testDataset]['http://now.winnipeg.ca/datasets/Schools/255']));
      $this->assertTrue(isset($resultset[$settings->testDataset]['http://now.winnipeg.ca/datasets/Schools/116']));
                      
      unset($search);
      unset($settings);     
    }     

    public function testSearch_Core_Attribute_Value_Literal_Boost_Resultset() {
      
      $settings = new Config();  
      
      $search = new SearchQuery($settings->endpointUrl);
      
      $search->datasetFilter($settings->testDataset)    
             ->items(4)
             ->attributeValueBoost('preflabel', 30, 'Avery')
             ->mime('resultset')
             ->excludeAggregates()
             ->sourceInterface($settings->searchInterface)
             ->sourceInterfaceVersion($settings->searchInterfaceVersion)
             ->send();
             
      $this->assertEquals($search->getStatus(), "200", "Debugging information: ".var_export($search, TRUE));                                       
      
      $resultset = $search->getResultset()->getResultset();

      $this->assertTrue(isset($resultset[$settings->testDataset]['http://now.winnipeg.ca/datasets/Schools/117']));
                      
      unset($search);
      unset($settings);     
    }       
    
    public function testSearch_Attribute_URI_And_Literal_Boost_Resultset() {
      
      $settings = new Config();  
      
      $search = new SearchQuery($settings->endpointUrl);
      
      $search->datasetFilter($settings->testDataset)    
             ->items(4)
             ->includeScores()
             ->attributeValueBoost('http://www.geonames.org/ontology#locatedIn', 10, 'http://now.winnipeg.ca/datasets/neighbourhoods/4.409', TRUE)
             ->attributeValueBoost('http://purl.org/ontology/now#gradeLevel', 10, 'K-6')
             ->mime('resultset')
             ->excludeAggregates()
             ->sourceInterface($settings->searchInterface)
             ->sourceInterfaceVersion($settings->searchInterfaceVersion)
             ->send();
             
      $this->assertEquals($search->getStatus(), "200", "Debugging information: ".var_export($search, TRUE));                                       
      
      $resultset = $search->getResultset()->getResultset();

      $this->assertTrue(isset($resultset[$settings->testDataset]['http://now.winnipeg.ca/datasets/Schools/107']));
      $this->assertTrue(isset($resultset[$settings->testDataset]['http://now.winnipeg.ca/datasets/Schools/295']));
      $this->assertTrue(isset($resultset[$settings->testDataset]['http://now.winnipeg.ca/datasets/Schools/69']));
      $this->assertTrue(isset($resultset[$settings->testDataset]['http://now.winnipeg.ca/datasets/Schools/81']));
                      
      unset($search);
      unset($settings);     
    } 
    
    public function testSearch_Include_Scores_Test_1_Resultset() {
      
      $settings = new Config();  
      
      $search = new SearchQuery($settings->endpointUrl);
      
      $search->datasetFilter($settings->testDataset)    
             ->items(4)
             ->includeScores()
             ->attributeValueBoost('http://www.geonames.org/ontology#locatedIn', 10, 'http://now.winnipeg.ca/datasets/neighbourhoods/4.409', TRUE)
             ->attributeValueBoost('http://purl.org/ontology/now#gradeLevel', 10, 'K-6')
             ->mime('resultset')
             ->excludeAggregates()
             ->sourceInterface($settings->searchInterface)
             ->sourceInterfaceVersion($settings->searchInterfaceVersion)
             ->send();
             
      $this->assertEquals($search->getStatus(), "200", "Debugging information: ".var_export($search, TRUE));                                       
      
      $resultset = $search->getResultset()->getResultset();

      foreach($resultset[$settings->testDataset] as $uri => $description)
      {
        $this->assertTrue(isset($description['http://purl.org/ontology/wsf#score']));
      }      
                      
      unset($search);
      unset($settings);     
    }     
    
    public function testSearch_Include_Scores_Test_2_Resultset() {
      
      $settings = new Config();  
      
      $search = new SearchQuery($settings->endpointUrl);
      
      $search->datasetFilter($settings->testDataset)    
             ->query('private winnipeg')
             ->phraseBoostDistance(2)
             ->searchRestriction('http://purl.org/ontology/now#schoolDivision', 1)
             ->attributePhraseBoost('http://purl.org/ontology/now#schoolDivision', 30)
             ->includeScores()
             ->items(10)
             ->mime('resultset')
             ->excludeAggregates()
             ->sourceInterface($settings->searchInterface)
             ->sourceInterfaceVersion($settings->searchInterfaceVersion)
             ->send();
                          
      $this->assertEquals($search->getStatus(), "200", "Debugging information: ".var_export($search, TRUE));                                       
      
      $resultset = $search->getResultset()->getResultset();

      foreach($resultset[$settings->testDataset] as $uri => $description)
      {
        $this->assertTrue(isset($description['http://purl.org/ontology/wsf#score']));
      }      
                      
      unset($search);
      unset($settings);     
    }     
        
    public function testSearch_Exclude_Scores_Resultset() {
      
      $settings = new Config();  
      
      $search = new SearchQuery($settings->endpointUrl);
      
      $search->datasetFilter($settings->testDataset)    
             ->items(4)
             ->excludeScores()
             ->attributeValueBoost('http://www.geonames.org/ontology#locatedIn', 10, 'http://now.winnipeg.ca/datasets/neighbourhoods/4.409', TRUE)
             ->attributeValueBoost('http://purl.org/ontology/now#gradeLevel', 10, 'K-6')
             ->mime('resultset')
             ->excludeAggregates()
             ->sourceInterface($settings->searchInterface)
             ->sourceInterfaceVersion($settings->searchInterfaceVersion)
             ->send();
             
      $this->assertEquals($search->getStatus(), "200", "Debugging information: ".var_export($search, TRUE));                                       
      
      $resultset = $search->getResultset()->getResultset();

      foreach($resultset[$settings->testDataset] as $uri => $description)
      {
        $this->assertTrue(!isset($description['http://purl.org/ontology/wsf#score']));
      }      
                      
      unset($search);
      unset($settings);     
    }     
    
    public function testSearch_One_Type_Filter_Resultset() {
      
      $settings = new Config();  
      
      $search = new SearchQuery($settings->endpointUrl);
      
      $search->datasetFilter($settings->testDataset)    
             ->typeFilter('http://purl.org/ontology/now#Schools')
             ->items(10)
             ->mime('resultset')
             ->excludeAggregates()
             ->sourceInterface($settings->searchInterface)
             ->sourceInterfaceVersion($settings->searchInterfaceVersion)
             ->send();
             
      $this->assertEquals($search->getStatus(), "200", "Debugging information: ".var_export($search, TRUE));                                       
      
      $resultset = $search->getResultset()->getResultset();

      foreach($resultset[$settings->testDataset] as $uri => $description)
      {
        $this->assertTrue($description['type'][0] == 'http://purl.org/ontology/now#Schools');
      }
                      
      unset($search);
      unset($settings);     
    }   
    
    public function testSearch_Two_Type_Filter_Resultset() {
      
      $settings = new Config();  
      
      $search = new SearchQuery($settings->endpointUrl);
      
      $search->datasetFilter($settings->testDataset)    
             ->typeBoost('http://purl.org/ontology/now#Arenas', 30)
             ->typesFilters(array('http://purl.org/ontology/now#Arenas', 'http://purl.org/ontology/now#Schools'))
             ->items(50)
             ->mime('resultset')
             ->excludeAggregates()
             ->sourceInterface($settings->searchInterface)
             ->sourceInterfaceVersion($settings->searchInterfaceVersion)
             ->send();
             
      $this->assertEquals($search->getStatus(), "200", "Debugging information: ".var_export($search, TRUE));                                       
      
      $resultset = $search->getResultset()->getResultset();

      foreach($resultset[$settings->testDataset] as $uri => $description)
      {
        $this->assertTrue($description['type'][0] == 'http://purl.org/ontology/now#Schools' || $description['type'][0] == 'http://purl.org/ontology/now#Arenas');
      }
                      
      unset($search);
      unset($settings);     
    }   
    
    public function testSearch_Unexisting_Attribute_Value_Literal_Boost_Resultset() {
      
      $settings = new Config();  
      
      $search = new SearchQuery($settings->endpointUrl);
      
      $search->datasetFilter($settings->testDataset)    
             ->items(4)
             ->attributeValueBoost('http://purl.org/ontology/now#Unexisting', 30, 'K-8')
             ->mime('resultset')
             ->excludeAggregates()
             ->sourceInterface($settings->searchInterface)
             ->sourceInterfaceVersion($settings->searchInterfaceVersion)
             ->send();
             
      $this->assertEquals($search->getStatus(), "400", "Debugging information: ".var_export($search, TRUE));                                       
      $this->assertEquals($search->getStatusMessage(), "Bad Request", "Debugging information: ".var_export($search, TRUE));
      $this->assertEquals($search->error->id, "WS-SEARCH-314", "Debugging information: ".var_export($search, TRUE));                                       
                      
      unset($search);
      unset($settings);     
    }   
    
    public function testSearch_Unexisting_Type_Filter_Resultset() {
      
      $settings = new Config();  
      
      $search = new SearchQuery($settings->endpointUrl);
      
      $search->datasetFilter($settings->testDataset)    
             ->typeFilter('http://purl.org/ontology/now#Unexisting')
             ->items(50)
             ->mime('resultset')
             ->excludeAggregates()
             ->sourceInterface($settings->searchInterface)
             ->sourceInterfaceVersion($settings->searchInterfaceVersion)
             ->send();
             
      $this->assertEquals($search->getStatus(), "200", "Debugging information: ".var_export($search, TRUE));                                       
      
      $resultset = $search->getResultset()->getResultset();

      $this->assertTrue(count($resultset) == 0);
                      
      unset($search);
      unset($settings);     
    }
        
    public function testSearch_Type_Boost_Resultset() {
      
      $settings = new Config();  
      
      $search = new SearchQuery($settings->endpointUrl);
      
      $search->datasetFilter($settings->testDataset)    
             ->typeBoost('http://purl.org/ontology/now#Arenas', 30)
             ->includeScores()
             ->items(10)
             ->mime('resultset')
             ->excludeAggregates()
             ->sourceInterface($settings->searchInterface)
             ->sourceInterfaceVersion($settings->searchInterfaceVersion)
             ->send();
             
      $this->assertEquals($search->getStatus(), "200", "Debugging information: ".var_export($search, TRUE));                                       
      
      $resultset = $search->getResultset()->getResultset();

      foreach($resultset[$settings->testDataset] as $uri => $description)
      {
        $this->assertTrue($description['type'][0] == 'http://purl.org/ontology/now#Arenas');
      }
                      
      unset($search);
      unset($settings);     
    } 
    
    public function testSearch_Two_Types_Boost_Resultset() {
      
      $settings = new Config();  
      
      $search = new SearchQuery($settings->endpointUrl);
      
      $search->datasetFilter($settings->testDataset)    
             ->typeBoost('http://purl.org/ontology/now#Schools', 60)
             ->typeBoost('http://purl.org/ontology/now#Arenas', 30)
             ->includeScores()
             ->items(10)
             ->mime('resultset')
             ->excludeAggregates()
             ->sourceInterface($settings->searchInterface)
             ->sourceInterfaceVersion($settings->searchInterfaceVersion)
             ->send();
             
      $this->assertEquals($search->getStatus(), "200", "Debugging information: ".var_export($search, TRUE));                                       
      
      $resultset = $search->getResultset()->getResultset();
      
      foreach($resultset[$settings->testDataset] as $uri => $description)
      {
        $this->assertTrue($description['type'][0] == 'http://purl.org/ontology/now#Schools' ||
                          $description['type'][0] == 'http://purl.org/ontology/now#Arenas' );
      }
                      
      unset($search);
      unset($settings);     
    }     
    
    public function testSearch_Type_Boost_Unexisting_Type_Resultset() {
      
      $settings = new Config();  
      
      $search = new SearchQuery($settings->endpointUrl);
      
      $search->datasetFilter($settings->testDataset)    
             ->typeBoost('http://purl.org/ontology/now#Unexisting', 60)
             ->includeScores()
             ->items(10)
             ->mime('resultset')
             ->excludeAggregates()
             ->sourceInterface($settings->searchInterface)
             ->sourceInterfaceVersion($settings->searchInterfaceVersion)
             ->send();
             
      $this->assertEquals($search->getStatus(), "200", "Debugging information: ".var_export($search, TRUE));                                       
      
      $resultset = $search->getResultset()->getResultset();

      foreach($resultset[$settings->testDataset] as $uri => $description)
      {
        $this->assertTrue(floatval($description['http://purl.org/ontology/wsf#score'][0]['value']) < 0.5);
      } 
                      
      unset($search);
      unset($settings);     
    }     
        
    public function testSearch_Dataset_Boost_Resultset() {
      
      $settings = new Config();  
      
      $search = new SearchQuery($settings->endpointUrl);
      
      $search->datasetBoost($settings->testDataset, 30)    
             ->includeScores()
             ->items(40)
             ->mime('resultset')
             ->excludeAggregates()
             ->sourceInterface($settings->searchInterface)
             ->sourceInterfaceVersion($settings->searchInterfaceVersion)
             ->send();
             
      $this->assertEquals($search->getStatus(), "200", "Debugging information: ".var_export($search, TRUE));                                       
      
      $resultset = $search->getResultset()->getResultset();

      foreach($resultset as $dataset => $record)
      {
        $this->assertTrue($dataset == $settings->testDataset);
      }
                      
      unset($search);
      unset($settings);     
    } 
    
    public function testSearch_Dataset_Boost_Unexisting_Dataset_Resultset() {
      
      $settings = new Config();  
      
      $search = new SearchQuery($settings->endpointUrl);
      
      $search->datasetBoost($settings->testDataset.'Unexisting', 30)    
             ->includeScores()
             ->items(40)
             ->mime('resultset')
             ->excludeAggregates()
             ->sourceInterface($settings->searchInterface)
             ->sourceInterfaceVersion($settings->searchInterfaceVersion)
             ->send();
             
      $this->assertEquals($search->getStatus(), "200", "Debugging information: ".var_export($search, TRUE));                                       
      
      $resultset = $search->getResultset()->getResultset();

      foreach($resultset as $dataset => $record)      
      {
        foreach($record as $uri => $description)
        {
          $this->assertTrue(floatval($record[$uri]['http://purl.org/ontology/wsf#score'][0]['value']) < 0.5);
        }
      } 
                      
      unset($search);
      unset($settings);     
    }    
    
    public function testSearch_Attribute_Filter_Resultset() {
      
      $settings = new Config();  
      
      $search = new SearchQuery($settings->endpointUrl);
      
      $search->datasetFilter($settings->testDataset)    
             ->attributeValuesFilters('http://purl.org/ontology/now#phoneNumber')
             ->items(10)
             ->mime('resultset')
             ->excludeAggregates()
             ->sourceInterface($settings->searchInterface)
             ->sourceInterfaceVersion($settings->searchInterfaceVersion)
             ->send();
             
      $this->assertEquals($search->getStatus(), "200", "Debugging information: ".var_export($search, TRUE));                                       
      
      $resultset = $search->getResultset()->getResultset();

      foreach($resultset[$settings->testDataset] as $uri => $description)
      {
        $this->assertTrue(isset($description['http://purl.org/ontology/now#phoneNumber']));
      }
                      
      unset($search);
      unset($settings);     
    }     

    public function testSearch_Attribute_Value_Filter_Resultset() {
      
      $settings = new Config();  
      
      $search = new SearchQuery($settings->endpointUrl);
      
      $search->datasetFilter($settings->testDataset)    
             ->attributeValuesFilters('http://purl.org/ontology/now#phoneNumber', '204-786-5631')
             ->items(10)
             ->mime('resultset')
             ->excludeAggregates()
             ->sourceInterface($settings->searchInterface)
             ->sourceInterfaceVersion($settings->searchInterfaceVersion)
             ->send();
             
      $this->assertEquals($search->getStatus(), "200", "Debugging information: ".var_export($search, TRUE));                                       
      
      $resultset = $search->getResultset()->getResultset();

      foreach($resultset[$settings->testDataset] as $uri => $description)
      {
        $this->assertTrue($description['http://purl.org/ontology/now#phoneNumber'][0]['value'] == '204-786-5631');
      }
                      
      unset($search);
      unset($settings);     
    }     
    
    public function testSearch_Attribute_Unexisting_Filter_Resultset() {
      
      $settings = new Config();  
      
      $search = new SearchQuery($settings->endpointUrl);
      
      $search->datasetFilter($settings->testDataset)    
             ->attributeValuesFilters('http://purl.org/ontology/now#Unexisting')
             ->items(10)
             ->mime('resultset')
             ->excludeAggregates()
             ->sourceInterface($settings->searchInterface)
             ->sourceInterfaceVersion($settings->searchInterfaceVersion)
             ->send();
             
      $this->assertEquals($search->getStatus(), "400", "Debugging information: ".var_export($search, TRUE));                                       
      $this->assertEquals($search->getStatusMessage(), "Bad Request", "Debugging information: ".var_export($search, TRUE));
      $this->assertEquals($search->error->id, "WS-SEARCH-313", "Debugging information: ".var_export($search, TRUE));                                       
                      
      unset($search);
      unset($settings);     
    }      
    
    public function testSearch_Attribute_Unexisting_Value_Filter_Resultset() {
      
      $settings = new Config();  
      
      $search = new SearchQuery($settings->endpointUrl);
      
      $search->datasetFilter($settings->testDataset)    
             ->attributeValuesFilters('http://purl.org/ontology/now#Unexisting', '204-786-5631')
             ->items(10)
             ->mime('resultset')
             ->excludeAggregates()
             ->sourceInterface($settings->searchInterface)
             ->sourceInterfaceVersion($settings->searchInterfaceVersion)
             ->send();
             
      $this->assertEquals($search->getStatus(), "400", "Debugging information: ".var_export($search, TRUE));                                       
      $this->assertEquals($search->getStatusMessage(), "Bad Request", "Debugging information: ".var_export($search, TRUE));
      $this->assertEquals($search->error->id, "WS-SEARCH-313", "Debugging information: ".var_export($search, TRUE));                                       
                      
      unset($search);
      unset($settings);     
    }    
    
    public function testSearch_Attribute_Value_Unexisting_Filter_Resultset() {
      
      $settings = new Config();  
      
      $search = new SearchQuery($settings->endpointUrl);
      
      $search->datasetFilter($settings->testDataset)    
             ->attributeValuesFilters('http://purl.org/ontology/now#phoneNumber', 'unexisting')
             ->items(10)
             ->mime('resultset')
             ->excludeAggregates()
             ->sourceInterface($settings->searchInterface)
             ->sourceInterfaceVersion($settings->searchInterfaceVersion)
             ->send();
             
      $this->assertEquals($search->getStatus(), "200", "Debugging information: ".var_export($search, TRUE));                                       
      
      $resultset = $search->getResultset()->getResultset();

      $this->assertTrue(count($resultset) == 0);

      unset($search);
      unset($settings);     
    }    
    
    public function testSearch_Multiple_Attributes_Filters_Resultset() {
      
      $settings = new Config();  
      
      $search = new SearchQuery($settings->endpointUrl);
      
      $search->datasetFilter($settings->testDataset)    
             ->attributeValuesFilters('http://purl.org/ontology/sco#namedEntity')
             ->attributeValuesFilters('http://purl.org/ontology/now#gradeLevel')
             ->items(10)
             ->mime('resultset')
             ->excludeAggregates()
             ->sourceInterface($settings->searchInterface)
             ->sourceInterfaceVersion($settings->searchInterfaceVersion)
             ->send();

      $this->assertEquals($search->getStatus(), "200", "Debugging information: ".var_export($search, TRUE));                                       
      
      $resultset = $search->getResultset()->getResultset();
             
      foreach($resultset[$settings->testDataset] as $uri => $description)
      {
        $this->assertTrue(isset($description['http://purl.org/ontology/sco#namedEntity']));
        $this->assertTrue(isset($description['http://purl.org/ontology/now#gradeLevel']));
      }
                      
      unset($search);
      unset($settings);     
    }    

    public function testSearch_Multiple_Attributes_Filters_Values_Resultset() {
      
      $settings = new Config();  
      
      $search = new SearchQuery($settings->endpointUrl);
      
      $search->datasetFilter($settings->testDataset)    
             ->attributeValuesFilters('http://purl.org/ontology/sco#namedEntity', 'true')
             ->attributeValuesFilters('http://purl.org/ontology/now#gradeLevel', 'K-6')
             ->items(10)
             ->mime('resultset')
             ->excludeAggregates()
             ->sourceInterface($settings->searchInterface)
             ->sourceInterfaceVersion($settings->searchInterfaceVersion)
             ->send();

      $this->assertEquals($search->getStatus(), "200", "Debugging information: ".var_export($search, TRUE));                                       
      
      $resultset = $search->getResultset()->getResultset();
             
      foreach($resultset[$settings->testDataset] as $uri => $description)
      {
        $this->assertTrue($description['http://purl.org/ontology/sco#namedEntity'][0]['value'] == 'true');
        $this->assertTrue($description['http://purl.org/ontology/now#gradeLevel'][0]['value'] == 'K-6');
      }
                      
      unset($search);
      unset($settings);     
    }   
    
    public function testSearch_Multiple_Attributes_Filters_One_Unexisting_Resultset() {
      
      $settings = new Config();  
      
      $search = new SearchQuery($settings->endpointUrl);
      
      $search->datasetFilter($settings->testDataset)    
             ->attributeValuesFilters('http://purl.org/ontology/sco#namedEntity')
             ->attributeValuesFilters('http://purl.org/ontology/now#unexisting')
             ->items(10)
             ->mime('resultset')
             ->excludeAggregates()
             ->sourceInterface($settings->searchInterface)
             ->sourceInterfaceVersion($settings->searchInterfaceVersion)
             ->send();

      $this->assertEquals($search->getStatus(), "400", "Debugging information: ".var_export($search, TRUE));                                       
      $this->assertEquals($search->getStatusMessage(), "Bad Request", "Debugging information: ".var_export($search, TRUE));
      $this->assertEquals($search->error->id, "WS-SEARCH-313", "Debugging information: ".var_export($search, TRUE));                                       
                      
      unset($search);
      unset($settings);     
    }    
    
    public function testSearch_Multiple_Attributes_Filters_Values_AND_Operator_Resultset() {
      
      $settings = new Config();  
      
      $search = new SearchQuery($settings->endpointUrl);
      
      $search->datasetFilter($settings->testDataset)    
             ->attributeValuesFilters('http://purl.org/ontology/sco#namedEntity', 'true')
             ->attributeValuesFilters('http://purl.org/ontology/now#gradeLevel', 'K-6')
             ->items(10)
             ->setAttributesBooleanOperatorToAnd()
             ->mime('resultset')
             ->excludeAggregates()
             ->sourceInterface($settings->searchInterface)
             ->sourceInterfaceVersion($settings->searchInterfaceVersion)
             ->send();

      $this->assertEquals($search->getStatus(), "200", "Debugging information: ".var_export($search, TRUE));                                       
      
      $resultset = $search->getResultset()->getResultset();
             
      foreach($resultset[$settings->testDataset] as $uri => $description)
      {
        $this->assertTrue($description['http://purl.org/ontology/sco#namedEntity'][0]['value'] == 'true');
        $this->assertTrue($description['http://purl.org/ontology/now#gradeLevel'][0]['value'] == 'K-6');
      }
                      
      unset($search);
      unset($settings);     
    }       
    
    public function testSearch_Multiple_Attributes_Filters_Values_OR_Operator_Resultset() {
      
      $settings = new Config();  
      
      $search = new SearchQuery($settings->endpointUrl);
      
      $search->datasetFilter($settings->testDataset)    
             ->attributeValuesFilters('http://purl.org/ontology/now#neighbourhoodNumber', '3.17')
             ->attributeValuesFilters('http://purl.org/ontology/now#hasConcession', 'Yes')
             ->items(50)
             ->setAttributesBooleanOperatorToOr()
             ->mime('resultset')
             ->excludeAggregates()
             ->sourceInterface($settings->searchInterface)
             ->sourceInterfaceVersion($settings->searchInterfaceVersion)
             ->send();

      $this->assertEquals($search->getStatus(), "200", "Debugging information: ".var_export($search, TRUE));                                       
      
      $resultset = $search->getResultset()->getResultset();
             
      foreach($resultset[$settings->testDataset] as $uri => $description)
      {
        if(isset($description['http://purl.org/ontology/now#hasConcession']))
        {
          $this->assertTrue($description['http://purl.org/ontology/now#hasConcession'][0]['value'] == 'Yes');
        } 
        elseif(isset($description['http://purl.org/ontology/now#neighbourhoodNumber']))
        {
          $this->assertTrue($description['http://purl.org/ontology/now#neighbourhoodNumber'][0]['value'] == '3.17');
        }
        else
        {
          $this->assertTrue(FALSE);
        } 
      }
                      
      unset($search);
      unset($settings);     
    }       
    
   public function testSearch_Include_Attributes_List_None_Resultset() {
      
      $settings = new Config();  
      
      $search = new SearchQuery($settings->endpointUrl);
      
      $search->datasetFilter($settings->testDataset)    
             ->includeAttribute('uri')
             ->items(10)
             ->mime('resultset')
             ->excludeAggregates()
             ->sourceInterface($settings->searchInterface)
             ->sourceInterfaceVersion($settings->searchInterfaceVersion)
             ->send();

      $this->assertEquals($search->getStatus(), "200", "Debugging information: ".var_export($search, TRUE));                                       
      
      $resultset = $search->getResultset()->getResultset();

      foreach($resultset[$settings->testDataset] as $uri => $description)
      {
        $this->assertTrue(count($description) == 2);
        $this->assertTrue(isset($description['type']));
        $this->assertTrue(isset($description['http://purl.org/dc/terms/isPartOf']));
      }                      
      
      unset($search);
      unset($settings);     
    }  
    
    public function testSearch_Include_Attributes_List_One_Resultset() {
      
      $settings = new Config();  
      
      $search = new SearchQuery($settings->endpointUrl);
      
      $search->datasetFilter($settings->testDataset)    
             ->includeAttribute('prefLabel')
             ->items(10)
             ->mime('resultset')
             ->excludeAggregates()
             ->sourceInterface($settings->searchInterface)
             ->sourceInterfaceVersion($settings->searchInterfaceVersion)
             ->send();

      $this->assertEquals($search->getStatus(), "200", "Debugging information: ".var_export($search, TRUE));                                       
      
      $resultset = $search->getResultset()->getResultset();

      foreach($resultset[$settings->testDataset] as $uri => $description)
      {
        $this->assertTrue(count($description) == 4);
        $this->assertTrue(isset($description['type']));
        $this->assertTrue(isset($description['http://purl.org/ontology/iron#prefLabel']));
        $this->assertTrue(isset($description['prefLabel']));
        $this->assertTrue(isset($description['http://purl.org/dc/terms/isPartOf']));
      }                      
      
      unset($search);
      unset($settings);     
    }    
    
    public function testSearch_Include_Attributes_List_Two_Resultset() {
      
      $settings = new Config();  
      
      $search = new SearchQuery($settings->endpointUrl);
      
      $search->datasetFilter($settings->testDataset)
             ->typeFilter('http://purl.org/ontology/now#Arenas')    
             ->includeAttributes(array('prefLabel', 'http://purl.org/ontology/now#ownedBy'))
             ->items(10)
             ->mime('resultset')
             ->excludeAggregates()
             ->sourceInterface($settings->searchInterface)
             ->sourceInterfaceVersion($settings->searchInterfaceVersion)
             ->send();

      $this->assertEquals($search->getStatus(), "200", "Debugging information: ".var_export($search, TRUE));                                       
      
      $resultset = $search->getResultset()->getResultset();

      foreach($resultset[$settings->testDataset] as $uri => $description)
      {
        $this->assertTrue(count($description) == 5);
        $this->assertTrue(isset($description['type']));
        $this->assertTrue(isset($description['http://purl.org/ontology/iron#prefLabel']));
        $this->assertTrue(isset($description['prefLabel']));
        $this->assertTrue(isset($description['http://purl.org/dc/terms/isPartOf']));
        $this->assertTrue(isset($description['http://purl.org/ontology/now#ownedBy']));
      }                      
      
      unset($search);
      unset($settings);     
    }    

    public function testSearch_Include_Attributes_List_Unexisting_Resultset() {
      
      $settings = new Config();  
      
      $search = new SearchQuery($settings->endpointUrl);
      
      $search->datasetFilter($settings->testDataset)    
             ->includeAttribute('unexisting')
             ->items(10)
             ->mime('resultset')
             ->excludeAggregates()
             ->sourceInterface($settings->searchInterface)
             ->sourceInterfaceVersion($settings->searchInterfaceVersion)
             ->send();

      $this->assertEquals($search->getStatus(), "200", "Debugging information: ".var_export($search, TRUE));                                       
      
      $resultset = $search->getResultset()->getResultset();

      foreach($resultset[$settings->testDataset] as $uri => $description)
      {
        $this->assertTrue(count($description) == 2);
        $this->assertTrue(isset($description['type']));
        $this->assertTrue(isset($description['http://purl.org/dc/terms/isPartOf']));
      }                      
      
      unset($search);
      unset($settings);     
    }

    public function testSearch_Aggregate_Attribute_Resultset() {
      
      $settings = new Config();  
      
      $search = new SearchQuery($settings->endpointUrl);
      
      $search->datasetFilter($settings->testDataset)    
             ->typeFilter('http://purl.org/ontology/now#Arenas')
             ->aggregateAttribute('http://purl.org/ontology/now#concessionDesc')
             ->items(0)
             ->mime('resultset')
             ->includeAggregates()
             ->sourceInterface($settings->searchInterface)
             ->sourceInterfaceVersion($settings->searchInterfaceVersion)
             ->send();

      $this->assertEquals($search->getStatus(), "200", "Debugging information: ".var_export($search, TRUE));                                       
      
      $resultset = $search->getResultset()->getResultset();

      $this->assertTrue(count($resultset['unspecified']) == 32);
      $this->assertTrue(isset($resultset['unspecified'][$settings->endpointUri."search/aggregate/2f01bcb652dd0e8d8a72116c7879aab2"]));
      $this->assertTrue(isset($resultset['unspecified'][$settings->endpointUri."search/aggregate/5961eaad9d450de4cfeded6356d02da2"]));
      $this->assertTrue(isset($resultset['unspecified'][$settings->endpointUri."search/aggregate/f2dc2c2f54fd5c632b59d0df7fee9db9"]));
      
      unset($search);
      unset($settings);     
    }

    public function testSearch_Aggregate_Two_Attributes_Resultset() {
      
      $settings = new Config();  
      
      $search = new SearchQuery($settings->endpointUrl);
      
      $search->datasetFilter($settings->testDataset)    
             ->typeFilter('http://purl.org/ontology/now#Arenas')
             ->aggregateAttribute('http://purl.org/ontology/now#concessionDesc')
             ->aggregateAttribute('http://purl.org/ontology/now#hasVendingMachines')
             ->items(0)
             ->mime('resultset')
             ->includeAggregates()
             ->sourceInterface($settings->searchInterface)
             ->sourceInterfaceVersion($settings->searchInterfaceVersion)
             ->send();

      $this->assertEquals($search->getStatus(), "200", "Debugging information: ".var_export($search, TRUE));                                       
      
      $resultset = $search->getResultset()->getResultset();

      $this->assertTrue(count($resultset['unspecified']) == 34);
      $this->assertTrue(isset($resultset['unspecified'][$settings->endpointUri."search/aggregate/2f01bcb652dd0e8d8a72116c7879aab2"]));
      $this->assertTrue(isset($resultset['unspecified'][$settings->endpointUri."search/aggregate/5961eaad9d450de4cfeded6356d02da2"]));
      $this->assertTrue(isset($resultset['unspecified'][$settings->endpointUri."search/aggregate/f2dc2c2f54fd5c632b59d0df7fee9db9"]));
      $this->assertTrue(isset($resultset['unspecified'][$settings->endpointUri."search/aggregate/ed810c80fa66e365864754f0f328aa11"]));
      $this->assertTrue(isset($resultset['unspecified'][$settings->endpointUri."search/aggregate/752f112a09acd21dc28492e66a567583"]));
      
      unset($search);
      unset($settings);     
    }    

    public function testSearch_Aggregate_Object_Attribute_Type_Literal_Resultset() {
      
      $settings = new Config();  
      
      $search = new SearchQuery($settings->endpointUrl);
      
      $search->datasetFilter($settings->testDataset)    
             ->typeFilter('http://purl.org/ontology/now#Arenas')
             ->aggregateAttribute('http://www.geonames.org/ontology#locatedIn')
             ->setAggregateAttributesObjectTypeToLiteral()             
             ->items(0)
             ->mime('resultset')
             ->includeAggregates()
             ->sourceInterface($settings->searchInterface)
             ->sourceInterfaceVersion($settings->searchInterfaceVersion)
             ->send();

      $this->assertEquals($search->getStatus(), "200", "Debugging information: ".var_export($search, TRUE));                                       
      
      $resultset = $search->getResultset()->getResultset();
                                                   
      $this->assertTrue($resultset['unspecified'][$settings->endpointUri."search/aggregate/09f1d6b60190aaddbf7157ade7ad9abf"]["http://purl.org/ontology/aggregate#object"][0]['value'] == 'Eric Coy');
      $this->assertTrue(!isset($resultset['unspecified'][$settings->endpointUri."search/aggregate/09f1d6b60190aaddbf7157ade7ad9abf"]["http://purl.org/ontology/aggregate#object"][1]['uri']));
      
      unset($search);
      unset($settings);     
    }    

    public function testSearch_Aggregate_Object_Attribute_Type_Uri_Literal_Resultset() {
      
      $settings = new Config();  
      
      $search = new SearchQuery($settings->endpointUrl);
      
      $search->datasetFilter($settings->testDataset)    
             ->typeFilter('http://purl.org/ontology/now#Arenas')
             ->aggregateAttribute('http://www.geonames.org/ontology#locatedIn')
             ->setAggregateAttributesObjectTypeToUriLiteral()             
             ->items(0)
             ->mime('resultset')
             ->includeAggregates()
             ->sourceInterface($settings->searchInterface)
             ->sourceInterfaceVersion($settings->searchInterfaceVersion)
             ->send();

      $this->assertEquals($search->getStatus(), "200", "Debugging information: ".var_export($search, TRUE));                                       
      
      $resultset = $search->getResultset()->getResultset();
                                                   
      $this->assertTrue($resultset['unspecified'][$settings->endpointUri."search/aggregate/ad998416671f5e3fd2bc9ee512477fa9"]["http://purl.org/ontology/aggregate#object"][0]['uri'] == 'http://now.winnipeg.ca/datasets/neighbourhoods/2.614');
      $this->assertTrue($resultset['unspecified'][$settings->endpointUri."search/aggregate/ad998416671f5e3fd2bc9ee512477fa9"]["http://purl.org/ontology/aggregate#object"][1]['value'] == 'Eric Coy');
      
      unset($search);
      unset($settings);     
    }    

    public function testSearch_Aggregate_Object_Attribute_Type_Uri_Resultset() {
      
      $settings = new Config();  
      
      $search = new SearchQuery($settings->endpointUrl);
      
      $search->datasetFilter($settings->testDataset)    
             ->typeFilter('http://purl.org/ontology/now#Arenas')
             ->aggregateAttribute('http://www.geonames.org/ontology#locatedIn')
             ->setAggregateAttributeObjectTypeToUri()
             ->items(0)
             ->mime('resultset')
             ->includeAggregates()
             ->sourceInterface($settings->searchInterface)
             ->sourceInterfaceVersion($settings->searchInterfaceVersion)
             ->send();

      $this->assertEquals($search->getStatus(), "200", "Debugging information: ".var_export($search, TRUE));                                       
      
      $resultset = $search->getResultset()->getResultset();
                                                   
      $this->assertTrue($resultset['unspecified'][$settings->endpointUri."search/aggregate/7ed59d16654385b191460290027868a5"]["http://purl.org/ontology/aggregate#object"][0]['uri'] == 'http://now.winnipeg.ca/datasets/neighbourhoods/2.614');
      $this->assertTrue(!isset($resultset['unspecified'][$settings->endpointUri."search/aggregate/7ed59d16654385b191460290027868a5"]["http://purl.org/ontology/aggregate#object"][1]['value']));
      
      unset($search);
      unset($settings);     
    }

    public function testSearch_Aggregate_Object_Attribute_Type__LiteralUri_Nb_1_Resultset() {
      
      $settings = new Config();  
      
      $search = new SearchQuery($settings->endpointUrl);
      
      $search->datasetFilter($settings->testDataset)    
             ->typeFilter('http://purl.org/ontology/now#Arenas')
             ->aggregateAttribute('http://www.geonames.org/ontology#locatedIn')
             ->setAggregateAttributesObjectTypeToUriLiteral()
             ->numberOfAggregateAttributesObject(1)
             ->items(0)
             ->mime('resultset')
             ->includeAggregates()
             ->sourceInterface($settings->searchInterface)
             ->sourceInterfaceVersion($settings->searchInterfaceVersion)
             ->send();

      $this->assertEquals($search->getStatus(), "200", "Debugging information: ".var_export($search, TRUE));                                       

      $resultset = $search->getResultset()->getResultset();
      
      $nbFound = 0;
      
      foreach($resultset['unspecified'] as $uri => $description)
      {
        if($description["http://purl.org/ontology/aggregate#property"][0]['uri'] == 'http://www.geonames.org/ontology#locatedIn')
        {
          $nbFound++;
        }
      }
      
      $this->assertTrue($nbFound == 1);
      
      unset($search);
      unset($settings);     
    }
    
    public function testSearch_Aggregate_Object_Attribute_Type__LiteralUri_Nb_0_Resultset() {
      
      $settings = new Config();  
      
      $search = new SearchQuery($settings->endpointUrl);
      
      $search->datasetFilter($settings->testDataset)    
             ->typeFilter('http://purl.org/ontology/now#Arenas')
             ->aggregateAttribute('http://www.geonames.org/ontology#locatedIn')
             ->setAggregateAttributesObjectTypeToUriLiteral()
             ->numberOfAggregateAttributesObject(0)
             ->items(0)
             ->mime('resultset')
             ->includeAggregates()
             ->sourceInterface($settings->searchInterface)
             ->sourceInterfaceVersion($settings->searchInterfaceVersion)
             ->send();

      $this->assertEquals($search->getStatus(), "200", "Debugging information: ".var_export($search, TRUE));                                       
      
      $resultset = $search->getResultset()->getResultset();

      $nbFound = 0;
      
      foreach($resultset['unspecified'] as $uri => $description)
      {
        if($description["http://purl.org/ontology/aggregate#property"][0]['uri'] == 'http://www.geonames.org/ontology#locatedIn')
        {
          $nbFound++;
        }
      }
      
      $this->assertTrue($nbFound == 0);
      
      unset($search);
      unset($settings);     
    }
    
    public function testSearch_Aggregate_Object_Attribute_Type__LiteralUri_Nb_5_Resultset() {
      
      $settings = new Config();  
      
      $search = new SearchQuery($settings->endpointUrl);
      
      $search->datasetFilter($settings->testDataset)    
             ->typeFilter('http://purl.org/ontology/now#Arenas')
             ->aggregateAttribute('http://www.geonames.org/ontology#locatedIn')
             ->setAggregateAttributesObjectTypeToUriLiteral()
             ->numberOfAggregateAttributesObject(5)
             ->items(0)
             ->mime('resultset')
             ->includeAggregates()
             ->sourceInterface($settings->searchInterface)
             ->sourceInterfaceVersion($settings->searchInterfaceVersion)
             ->send();

      $this->assertEquals($search->getStatus(), "200", "Debugging information: ".var_export($search, TRUE));                                       
      
      $resultset = $search->getResultset()->getResultset();

      $nbFound = 0;
      
      foreach($resultset['unspecified'] as $uri => $description)
      {
        if($description["http://purl.org/ontology/aggregate#property"][0]['uri'] == 'http://www.geonames.org/ontology#locatedIn')
        {
          $nbFound++;
        }
      }
      
      $this->assertTrue($nbFound == 5);
      
      unset($search);
      unset($settings);     
    }        
   
    public function testSearch_Aggregate_Object_Attribute_Type_LiteralUri_Nb_All_Resultset() {
      
      $settings = new Config();  
      
      $search = new SearchQuery($settings->endpointUrl);
      
      $search->datasetFilter($settings->testDataset)    
             ->typeFilter('http://purl.org/ontology/now#Arenas')
             ->aggregateAttribute('http://www.geonames.org/ontology#locatedIn')
             ->setAggregateAttributesObjectTypeToUriLiteral()
             ->numberOfAggregateAttributesObject(-1)
             ->items(0)
             ->mime('resultset')
             ->includeAggregates()
             ->sourceInterface($settings->searchInterface)
             ->sourceInterfaceVersion($settings->searchInterfaceVersion)
             ->send();

      $this->assertEquals($search->getStatus(), "200", "Debugging information: ".var_export($search, TRUE));                                       
      
      $resultset = $search->getResultset()->getResultset();

      $nbFound = 0;
      
      foreach($resultset['unspecified'] as $uri => $description)
      {
        if($description["http://purl.org/ontology/aggregate#property"][0]['uri'] == 'http://www.geonames.org/ontology#locatedIn')
        {
          $nbFound++;
        }
      }
      
      $this->assertTrue($nbFound == 28);
      
      unset($search);
      unset($settings);     
    }    
                
    public function testSearch_Geo_Range_Filter_2_Records_Resultset() {
      
      $settings = new Config();  
      
      $search = new SearchQuery($settings->endpointUrl);
      
      $search->datasetFilter($settings->testDataset)    
             ->typeFilter('http://purl.org/ontology/now#Arenas')
             ->rangeFilter('49.82111681237976', '-97.08151086730959', '49.798961376801145', '-97.14536889953615')
             ->items(10)
             ->mime('resultset')
             ->excludeAggregates()
             ->sourceInterface($settings->searchInterface)
             ->sourceInterfaceVersion($settings->searchInterfaceVersion)
             ->send();

      $this->assertEquals($search->getStatus(), "200", "Debugging information: ".var_export($search, TRUE));                                       
      
      $resultset = $search->getResultset()->getResultset();

      $this->assertTrue(count($resultset[$settings->testDataset]) == 2);
      $this->assertTrue(isset($resultset[$settings->testDataset]['http://now.winnipeg.ca/datasets/Arenas/3']));
      $this->assertTrue(isset($resultset[$settings->testDataset]['http://now.winnipeg.ca/datasets/Arenas/24']));
      
      unset($search);
      unset($settings);     
    } 
    
    public function testSearch_Geo_Distance_Filter_3km_Resultset() {
      
      $settings = new Config();  
      
      $search = new SearchQuery($settings->endpointUrl);
      
      $search->datasetFilter($settings->testDataset)    
             ->typeFilter('http://purl.org/ontology/now#Arenas')
             ->distanceFilter('49.7990119661213', '-97.1366309566203', 3, 'km')
             ->items(10)
             ->mime('resultset')
             ->excludeAggregates()
             ->sourceInterface($settings->searchInterface)
             ->sourceInterfaceVersion($settings->searchInterfaceVersion)
             ->send();

      $this->assertEquals($search->getStatus(), "200", "Debugging information: ".var_export($search, TRUE));                                       
      
      $resultset = $search->getResultset()->getResultset();

      $this->assertTrue(count($resultset[$settings->testDataset]) == 1);
      $this->assertTrue(isset($resultset[$settings->testDataset]['http://now.winnipeg.ca/datasets/Arenas/24']));

      unset($search);
      unset($settings);     
    }    
        
    public function testSearch_Geo_Distance_Filter_3Miles_Resultset() {
      
      $settings = new Config();  
      
      $search = new SearchQuery($settings->endpointUrl);
      
      $search->datasetFilter($settings->testDataset)    
             ->typeFilter('http://purl.org/ontology/now#Arenas')
             ->distanceFilter('49.7990119661213', '-97.1366309566203', 3, 'mile')
             ->items(10)
             ->mime('resultset')
             ->excludeAggregates()
             ->sourceInterface($settings->searchInterface)
             ->sourceInterfaceVersion($settings->searchInterfaceVersion)
             ->send();

      $this->assertEquals($search->getStatus(), "200", "Debugging information: ".var_export($search, TRUE));                                       
      
      $resultset = $search->getResultset()->getResultset();

      $this->assertTrue(count($resultset[$settings->testDataset]) == 4);
      $this->assertTrue(isset($resultset[$settings->testDataset]['http://now.winnipeg.ca/datasets/Arenas/5']));
      $this->assertTrue(isset($resultset[$settings->testDataset]['http://now.winnipeg.ca/datasets/Arenas/15']));
      $this->assertTrue(isset($resultset[$settings->testDataset]['http://now.winnipeg.ca/datasets/Arenas/24']));
      $this->assertTrue(isset($resultset[$settings->testDataset]['http://now.winnipeg.ca/datasets/Arenas/3']));

      unset($search);
      unset($settings);     
    }      

    public function testSearch_Geo_Location_Aggregator_Resultset() {
      
      $settings = new Config();  
      
      $search = new SearchQuery($settings->endpointUrl);
      
      $search->datasetFilter($settings->testDataset)    
             ->typeFilter('http://purl.org/ontology/now#Schools')
             ->rangeFilter('50.00566380535165', '-96.8430732147217', '49.8288134536122', '-97.3539374725342')
             ->recordsLocationAggregator('49.91731970931195', '-97.09850534362796')
             ->items(20)
             ->mime('resultset')
             ->excludeAggregates()
             ->sourceInterface($settings->searchInterface)
             ->sourceInterfaceVersion($settings->searchInterfaceVersion)
             ->send();

      $this->assertEquals($search->getStatus(), "200", "Debugging information: ".var_export($search, TRUE));                                       
      
      $resultset = $search->getResultset()->getResultset();

      $this->assertTrue(count($resultset[$settings->testDataset]) == 20);
      $this->assertTrue(isset($resultset[$settings->testDataset]['http://now.winnipeg.ca/datasets/Schools/201']));
      $this->assertTrue(isset($resultset[$settings->testDataset]['http://now.winnipeg.ca/datasets/Schools/184']));
      $this->assertTrue(isset($resultset[$settings->testDataset]['http://now.winnipeg.ca/datasets/Schools/242']));
      $this->assertTrue(isset($resultset[$settings->testDataset]['http://now.winnipeg.ca/datasets/Schools/203']));
      $this->assertTrue(isset($resultset[$settings->testDataset]['http://now.winnipeg.ca/datasets/Schools/86']));
      $this->assertTrue(isset($resultset[$settings->testDataset]['http://now.winnipeg.ca/datasets/Schools/179']));
      $this->assertTrue(isset($resultset[$settings->testDataset]['http://now.winnipeg.ca/datasets/Schools/216']));
      $this->assertTrue(isset($resultset[$settings->testDataset]['http://now.winnipeg.ca/datasets/Schools/104']));
      $this->assertTrue(isset($resultset[$settings->testDataset]['http://now.winnipeg.ca/datasets/Schools/214']));
      $this->assertTrue(isset($resultset[$settings->testDataset]['http://now.winnipeg.ca/datasets/Schools/234']));
      $this->assertTrue(isset($resultset[$settings->testDataset]['http://now.winnipeg.ca/datasets/Schools/248']));
      $this->assertTrue(isset($resultset[$settings->testDataset]['http://now.winnipeg.ca/datasets/Schools/143']));
      $this->assertTrue(isset($resultset[$settings->testDataset]['http://now.winnipeg.ca/datasets/Schools/2']));
      $this->assertTrue(isset($resultset[$settings->testDataset]['http://now.winnipeg.ca/datasets/Schools/168']));
      $this->assertTrue(isset($resultset[$settings->testDataset]['http://now.winnipeg.ca/datasets/Schools/69']));
      $this->assertTrue(isset($resultset[$settings->testDataset]['http://now.winnipeg.ca/datasets/Schools/165']));
      $this->assertTrue(isset($resultset[$settings->testDataset]['http://now.winnipeg.ca/datasets/Schools/178']));
      $this->assertTrue(isset($resultset[$settings->testDataset]['http://now.winnipeg.ca/datasets/Schools/107']));
      $this->assertTrue(isset($resultset[$settings->testDataset]['http://now.winnipeg.ca/datasets/Schools/178']));
      $this->assertTrue(isset($resultset[$settings->testDataset]['http://now.winnipeg.ca/datasets/Schools/151']));
      
      unset($search);
      unset($settings);     
    } 
    
    public function testSearch_Enable_Inference_Search_Things_Resultset() {
      
      $settings = new Config();  
      
      $search = new SearchQuery($settings->endpointUrl);
      
      $search->datasetFilter($settings->testDataset)    
             ->typeFilter('http://www.w3.org/2002/07/owl#Thing')
             ->items(10)
             ->enableInference()
             ->mime('resultset')
             ->excludeAggregates()
             ->sourceInterface($settings->searchInterface)
             ->sourceInterfaceVersion($settings->searchInterfaceVersion)
             ->send();

      $this->assertEquals($search->getStatus(), "200", "Debugging information: ".var_export($search, TRUE));                                       
      
      $resultset = $search->getResultset()->getResultset();

      $this->assertTrue(count($resultset[$settings->testDataset]) == 10);
      
      unset($search);
      unset($settings);     
    } 
    
    public function testSearch_Disable_Inference_Search_Things_Resultset() {
      
      $settings = new Config();  
      
      $search = new SearchQuery($settings->endpointUrl);
      
      $search->datasetFilter($settings->testDataset)    
             ->typeFilter('http://www.w3.org/2002/07/owl#Thing')
             ->items(10)
             ->disableInference()
             ->mime('resultset')
             ->excludeAggregates()
             ->sourceInterface($settings->searchInterface)
             ->sourceInterfaceVersion($settings->searchInterfaceVersion)
             ->send();

      $this->assertEquals($search->getStatus(), "200", "Debugging information: ".var_export($search, TRUE));                                       
      
      $resultset = $search->getResultset()->getResultset();

      $this->assertTrue(count($resultset) == 0);
      
      unset($search);
      unset($settings);     
    } 

    public function testSearch_Attribute_Phrases_Boost_Without_Search_Restriction_Distance_2_Resultset() {
      
      $settings = new Config();  
      
      $search = new SearchQuery($settings->endpointUrl);
      
      $search->datasetFilter($settings->testDataset)    
             ->query('school winnipeg')
             ->phraseBoostDistance(2)
             ->attributePhraseBoost('http://purl.org/ontology/now#schoolDivision', 2)
             ->includeScores()
             ->items(100)
             ->mime('resultset')
             ->excludeAggregates()
             ->sourceInterface($settings->searchInterface)
             ->sourceInterfaceVersion($settings->searchInterfaceVersion)
             ->send();

      $this->assertEquals($search->getStatus(), "200", "Debugging information: ".var_export($search, TRUE));                                       
      
      $resultset = $search->getResultset()->getResultset();

      $this->assertTrue(count($resultset[$settings->testDataset]) == 92);
      
      unset($search);
      unset($settings);     
    }
    
    public function testSearch_Attribute_Phrases_Boost_Without_Search_Restriction_Distance_1_Resultset() {
      
      $settings = new Config();  
      
      $search = new SearchQuery($settings->endpointUrl);
      
      $search->datasetFilter($settings->testDataset)    
             ->query('school winnipeg')
             ->phraseBoostDistance(1)
             ->attributePhraseBoost('http://purl.org/ontology/now#schoolDivision', 2)
             ->includeScores()
             ->items(100)
             ->mime('resultset')
             ->excludeAggregates()
             ->sourceInterface($settings->searchInterface)
             ->sourceInterfaceVersion($settings->searchInterfaceVersion)
             ->send();

      $this->assertEquals($search->getStatus(), "200", "Debugging information: ".var_export($search, TRUE));                                       
      
      $resultset = $search->getResultset()->getResultset();

      $this->assertTrue(count($resultset[$settings->testDataset]) == 92);
      
      unset($search);
      unset($settings);     
    }

    public function testSearch_Attribute_Phrases_Boost_With_Search_Restriction_Distance_2_Resultset() {
      
      $settings = new Config();  
      
      $search = new SearchQuery($settings->endpointUrl);
      
      $search->datasetFilter($settings->testDataset)    
             ->query('school winnipeg')
             ->phraseBoostDistance(2)
             ->searchRestriction('http://purl.org/ontology/now#schoolDivision', 1)
             ->attributePhraseBoost('http://purl.org/ontology/now#schoolDivision', 2)
             ->includeScores()
             ->items(100)
             ->mime('resultset')
             ->excludeAggregates()
             ->sourceInterface($settings->searchInterface)
             ->sourceInterfaceVersion($settings->searchInterfaceVersion)
             ->send();

      $this->assertEquals($search->getStatus(), "200", "Debugging information: ".var_export($search, TRUE));                                       
      
      $resultset = $search->getResultset()->getResultset();

      $this->assertTrue(count($resultset[$settings->testDataset]) == 88);
      
      
      unset($search);
      unset($settings);     
    }    
    
    public function testSearch_Attribute_Phrases_Boost_With_Search_Restriction_Distance_1_Resultset() {
      
      $settings = new Config();  
      
      $search = new SearchQuery($settings->endpointUrl);
      
      $search->datasetFilter($settings->testDataset)    
             ->query('school winnipeg')
             ->phraseBoostDistance(1)
             ->searchRestriction('http://purl.org/ontology/now#schoolDivision', 1)
             ->attributePhraseBoost('http://purl.org/ontology/now#schoolDivision', 2)
             ->includeScores()
             ->items(100)
             ->mime('resultset')
             ->excludeAggregates()
             ->sourceInterface($settings->searchInterface)
             ->sourceInterfaceVersion($settings->searchInterfaceVersion)
             ->send();

      $this->assertEquals($search->getStatus(), "200", "Debugging information: ".var_export($search, TRUE));                                       
      
      $resultset = $search->getResultset()->getResultset();

      $this->assertTrue(count($resultset[$settings->testDataset]) == 88);
      
      
      unset($search);
      unset($settings);     
    }    

    public function testSearch_One_Attribute_restriction_Resultset() {
      
      $settings = new Config();  
      
      $search = new SearchQuery($settings->endpointUrl);
      
      $search->datasetFilter($settings->testDataset)    
             ->query('winnipeg')
             ->searchRestriction('http://purl.org/ontology/now#schoolDivision', 2)
             ->includeScores()
             ->items(100)
             ->mime('resultset')
             ->excludeAggregates()
             ->sourceInterface($settings->searchInterface)
             ->sourceInterfaceVersion($settings->searchInterfaceVersion)
             ->send();

      $this->assertEquals($search->getStatus(), "200", "Debugging information: ".var_export($search, TRUE));                                       
      
      $resultset = $search->getResultset()->getResultset();

      $this->assertTrue(count($resultset[$settings->testDataset]) == 88);
      
      
      unset($search);
      unset($settings);     
    }

    public function testSearch_Two_Attribute_restriction_Resultset() {
      
      $settings = new Config();  
      
      $search = new SearchQuery($settings->endpointUrl);
      
      $search->datasetFilter($settings->testDataset)    
             ->query('winnipeg')
             ->searchRestriction('http://purl.org/ontology/now#schoolDivision', 2)
             ->searchRestriction('prefLabel', 1)
             ->includeScores()
             ->items(100)
             ->mime('resultset')
             ->excludeAggregates()
             ->sourceInterface($settings->searchInterface)
             ->sourceInterfaceVersion($settings->searchInterfaceVersion)
             ->send();

      $this->assertEquals($search->getStatus(), "200", "Debugging information: ".var_export($search, TRUE));                                       
      
      $resultset = $search->getResultset()->getResultset();

      $this->assertTrue(count($resultset[$settings->testDataset]) == 92);
      
      
      unset($search);
      unset($settings);     
    }     

    public function testSearch_Extended_Filters_Missing_Ending_Parenthesis_Resultset() {
      
      $settings = new Config();  
      
      $extendedFiltersBuilder = new ExtendedFiltersBuilder();

      $extendedFiltersBuilder->startGrouping()
                             ->attributeValueFilter("http://purl.org/ontology/now#schoolDivision", "winnipeg");
      
      $search = new SearchQuery($settings->endpointUrl);
      
      $search->datasetFilter($settings->testDataset)    
              ->extendedFilters($extendedFiltersBuilder->getExtendedFilters())
              ->items(10)
              ->mime('resultset')
              ->excludeAggregates()
              ->sourceInterface($settings->searchInterface)
              ->sourceInterfaceVersion($settings->searchInterfaceVersion)
              ->send();

      $this->assertEquals($search->getStatus(), "400", "Debugging information: ".var_export($search, TRUE));                                       
      $this->assertEquals($search->getStatusMessage(), "Bad Request", "Debugging information: ".var_export($search, TRUE));
      $this->assertEquals($search->error->id, "WS-SEARCH-311", "Debugging information: ".var_export($search, TRUE));                                       
      
      unset($search);
      unset($settings);     
    }       

    public function testSearch_Extended_Filters_Missing_Starting_Parenthesis_Resultset() {
      
      $settings = new Config();  
      
      $extendedFiltersBuilder = new ExtendedFiltersBuilder();

      $extendedFiltersBuilder->attributeValueFilter("http://purl.org/ontology/now#schoolDivision", "winnipeg")
                             ->endGrouping();
      
      $search = new SearchQuery($settings->endpointUrl);
      
      $search->datasetFilter($settings->testDataset)    
              ->extendedFilters($extendedFiltersBuilder->getExtendedFilters())
              ->items(10)
              ->mime('resultset')
              ->excludeAggregates()
              ->sourceInterface($settings->searchInterface)
              ->sourceInterfaceVersion($settings->searchInterfaceVersion)
              ->send();

      $this->assertEquals($search->getStatus(), "400", "Debugging information: ".var_export($search, TRUE));                                       
      $this->assertEquals($search->getStatusMessage(), "Bad Request", "Debugging information: ".var_export($search, TRUE));
      $this->assertEquals($search->error->id, "WS-SEARCH-311", "Debugging information: ".var_export($search, TRUE));                                       
      
      unset($search);
      unset($settings);     
    }  

    public function testSearch_Extended_Filters_No_Grouping_Resultset() {
      
      $settings = new Config();  
      
      $extendedFiltersBuilder = new ExtendedFiltersBuilder();

      $extendedFiltersBuilder->attributeValueFilter("http://purl.org/ontology/now#schoolDivision", "winnipeg");
      
      $search = new SearchQuery($settings->endpointUrl);
      
      $search->datasetFilter($settings->testDataset)    
              ->extendedFilters($extendedFiltersBuilder->getExtendedFilters())
              ->items(100)
              ->mime('resultset')
              ->excludeAggregates()
              ->sourceInterface($settings->searchInterface)
              ->sourceInterfaceVersion($settings->searchInterfaceVersion)
              ->send();

      $this->assertEquals($search->getStatus(), "200", "Debugging information: ".var_export($search, TRUE));                                       
      
      $resultset = $search->getResultset()->getResultset();

      $this->assertTrue(count($resultset[$settings->testDataset]) == 88);      
      
      unset($search);
      unset($settings);     
    }

    public function testSearch_Extended_Filters_Unexisting_Datatype_Attribute_Resultset() {
      
      $settings = new Config();  
      
      $extendedFiltersBuilder = new ExtendedFiltersBuilder();

      $extendedFiltersBuilder->attributeValueFilter("http://purl.org/ontology/now#unexisting", "winnipeg");
      
      $search = new SearchQuery($settings->endpointUrl);
      
      $search->datasetFilter($settings->testDataset)    
              ->extendedFilters($extendedFiltersBuilder->getExtendedFilters())
              ->items(100)
              ->mime('resultset')
              ->excludeAggregates()
              ->sourceInterface($settings->searchInterface)
              ->sourceInterfaceVersion($settings->searchInterfaceVersion)
              ->send();

      $this->assertEquals($search->getStatus(), "400", "Debugging information: ".var_export($search, TRUE));                                       
      $this->assertEquals($search->getStatusMessage(), "Bad Request", "Debugging information: ".var_export($search, TRUE));
      $this->assertEquals($search->error->id, "WS-SEARCH-317", "Debugging information: ".var_export($search, TRUE));   
      
      unset($search);
      unset($settings);     
    }
    
    public function testSearch_Extended_Filters_Unexisting_Object_Attribute_Resultset() {
      
      $settings = new Config();  
      
      $extendedFiltersBuilder = new ExtendedFiltersBuilder();

      $extendedFiltersBuilder->attributeValueFilter("http://www.geonames.org/ontology#unexisting", "http://now.winnipeg.ca/datasets/neighbourhoods/1.666", TRUE);
      
      $search = new SearchQuery($settings->endpointUrl);
      
      $search->datasetFilter($settings->testDataset)    
              ->extendedFilters($extendedFiltersBuilder->getExtendedFilters())
              ->items(100)
              ->mime('resultset')
              ->excludeAggregates()
              ->sourceInterface($settings->searchInterface)
              ->sourceInterfaceVersion($settings->searchInterfaceVersion)
              ->send();

      $this->assertEquals($search->getStatus(), "400", "Debugging information: ".var_export($search, TRUE));                                       
      $this->assertEquals($search->getStatusMessage(), "Bad Request", "Debugging information: ".var_export($search, TRUE));
      $this->assertEquals($search->error->id, "WS-SEARCH-317", "Debugging information: ".var_export($search, TRUE));   
      
      unset($search);
      unset($settings);     
    }    
    
    public function testSearch_Extended_Filters_Datatype_Attribute_Filter_Resultset() {
      
      $settings = new Config();  
      
      $extendedFiltersBuilder = new ExtendedFiltersBuilder();

      $extendedFiltersBuilder->attributeValueFilter("http://purl.org/ontology/now#schoolDivision", "winnipeg");
      
      $search = new SearchQuery($settings->endpointUrl);
      
      $search->datasetFilter($settings->testDataset)    
              ->extendedFilters($extendedFiltersBuilder->getExtendedFilters())
              ->items(100)
              ->mime('resultset')
              ->excludeAggregates()
              ->sourceInterface($settings->searchInterface)
              ->sourceInterfaceVersion($settings->searchInterfaceVersion)
              ->send();

      $this->assertEquals($search->getStatus(), "200", "Debugging information: ".var_export($search, TRUE));                                       
      
      $resultset = $search->getResultset()->getResultset();

      $this->assertTrue(count($resultset[$settings->testDataset]) == 88);      
      
      foreach($resultset[$settings->testDataset] as $uri => $description)
      {
        $this->assertTrue(stripos($description['http://purl.org/ontology/now#schoolDivision'][0]['value'], 'winnipeg') !== FALSE);
      }      
                      
      unset($search);
      unset($settings);     
    }  
          
    public function testSearch_Extended_Filters_Object_Attribute_Filter_Resultset() {
      
      $settings = new Config();  
      
      $extendedFiltersBuilder = new ExtendedFiltersBuilder();

      $extendedFiltersBuilder->attributeValueFilter("http://www.geonames.org/ontology#locatedIn", "http://now.winnipeg.ca/datasets/neighbourhoods/1.666", TRUE);
      
      $search = new SearchQuery($settings->endpointUrl);
      
      $search->datasetFilter($settings->testDataset)    
              ->extendedFilters($extendedFiltersBuilder->getExtendedFilters())
              ->items(100)
              ->mime('resultset')
              ->excludeAggregates()
              ->sourceInterface($settings->searchInterface)
              ->sourceInterfaceVersion($settings->searchInterfaceVersion)
              ->send();

      $this->assertEquals($search->getStatus(), "200", "Debugging information: ".var_export($search, TRUE));                                       
      
      $resultset = $search->getResultset()->getResultset();

      $this->assertTrue(count($resultset[$settings->testDataset]) == 4);      
      
      foreach($resultset[$settings->testDataset] as $uri => $description)
      {
        $this->assertTrue(stripos($description['http://www.geonames.org/ontology#locatedIn'][0]['uri'], 'http://now.winnipeg.ca/datasets/neighbourhoods/1.666') !== FALSE);
      }      
                      
      unset($search);
      unset($settings);     
    } 
    
    public function testSearch_Extended_Filters_Dataset_Filter_No_Inference_Resultset() {
      
      $settings = new Config();  
      
      $extendedFiltersBuilder = new ExtendedFiltersBuilder();

      $extendedFiltersBuilder->datasetFilter($settings->testDataset)
                             ->and_()
                             ->typeFilter('http://purl.org/ontology/now#Schools', FALSE);
      
      $search = new SearchQuery($settings->endpointUrl);
      
      $search->extendedFilters($extendedFiltersBuilder->getExtendedFilters())
             ->items(0)
             ->mime('resultset')
             ->includeAggregates()
             ->sourceInterface($settings->searchInterface)
             ->sourceInterfaceVersion($settings->searchInterfaceVersion)
             ->send();

      $this->assertEquals($search->getStatus(), "200", "Debugging information: ".var_export($search, TRUE));                                       
      
      $resultset = $search->getResultset()->getResultset();

      $this->assertTrue(isset($resultset['unspecified'][''.$settings->endpointUri.'search/aggregate/d2be46742892e216032fea49fac5cbc0']));
                      
      unset($search);
      unset($settings);     
    }    
    
    public function testSearch_Extended_Filters_Dataset_Filter_With_Inference_Resultset() {
      
      $settings = new Config();  
      
      $extendedFiltersBuilder = new ExtendedFiltersBuilder();

      $extendedFiltersBuilder->datasetFilter($settings->testDataset)
                             ->and_()
                             ->typeFilter('http://www.w3.org/2002/07/owl#Thing', TRUE);
      
      $search = new SearchQuery($settings->endpointUrl);
      
      $search->extendedFilters($extendedFiltersBuilder->getExtendedFilters())
             ->items(0)
             ->mime('resultset')
             ->includeAggregates()
             ->sourceInterface($settings->searchInterface)
             ->sourceInterfaceVersion($settings->searchInterfaceVersion)
             ->send();

      $this->assertEquals($search->getStatus(), "200", "Debugging information: ".var_export($search, TRUE));                                       
      
      $resultset = $search->getResultset()->getResultset();

      $this->assertTrue(isset($resultset['unspecified'][''.$settings->endpointUri.'search/aggregate/1efe853385bd7e60ae1be622bcd93e65']));
                      
      unset($search);
      unset($settings);     
    }    
   
    public function testSearch_Extended_Filters_Dataset_And_Operator_Resultset() {
      
      $settings = new Config();  
      
      $extendedFiltersBuilder = new ExtendedFiltersBuilder();

      $extendedFiltersBuilder->datasetFilter($settings->testDataset)
                             ->and_()
                             ->typeFilter('http://www.w3.org/2002/07/owl#Thing', TRUE)
                             ->and_()
                             ->attributeValueFilter("http://purl.org/ontology/now#schoolDivision", "winnipeg");
      
      $search = new SearchQuery($settings->endpointUrl);
      
      $search->extendedFilters($extendedFiltersBuilder->getExtendedFilters())
             ->items(0)
             ->mime('resultset')
             ->includeAggregates()
             ->sourceInterface($settings->searchInterface)
             ->sourceInterfaceVersion($settings->searchInterfaceVersion)
             ->send();

      $this->assertEquals($search->getStatus(), "200", "Debugging information: ".var_export($search, TRUE));                                       
      
      $resultset = $search->getResultset()->getResultset();

      $this->assertTrue(isset($resultset['unspecified'][''.$settings->endpointUri.'search/aggregate/5b943677d7dfa9a9a064843134b800e4']));
                      
      unset($search);
      unset($settings);     
    }    
   
    public function testSearch_Extended_Filters_Dataset_Not_Operator_Resultset() {
      
      $settings = new Config();  
      
      $extendedFiltersBuilder = new ExtendedFiltersBuilder();

      $extendedFiltersBuilder->startGrouping()
                               ->datasetFilter($settings->testDataset)
                               ->and_()
                               ->typeFilter('http://www.w3.org/2002/07/owl#Thing', TRUE)
                             ->endGrouping()
                             ->and_()
                             ->not_()
                             ->attributeValueFilter("http://purl.org/ontology/now#schoolDivision", "winnipeg");
      
      $search = new SearchQuery($settings->endpointUrl);
      
      $search->extendedFilters($extendedFiltersBuilder->getExtendedFilters())
             ->items(0)
             ->mime('resultset')
             ->includeAggregates()
             ->sourceInterface($settings->searchInterface)
             ->sourceInterfaceVersion($settings->searchInterfaceVersion)
             ->send();

      $this->assertEquals($search->getStatus(), "200", "Debugging information: ".var_export($search, TRUE));                                       
      
      $resultset = $search->getResultset()->getResultset();

      $this->assertTrue(isset($resultset['unspecified'][''.$settings->endpointUri.'search/aggregate/a9306e6c8f0e837def104522e74eb53c']));
                      
      unset($search);
      unset($settings);     
    }
        
  }

  
?>