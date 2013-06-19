<?php

  namespace StructuredDynamics\structwsf\tests;

  class Config
  {
    // Core configs
    
    /** Folder where PHPUnit is installed on the server */
    public $phpUnitInstallFolder = "";
    
    /** Folder where the structWSF instance is located on the server */
    public $structwsfInstanceFolder = "";
    
    /** Base URL of the endpoint to test */
    public $endpointUrl = "";
    
    /** Base URI of the web services in the structWSF network */
    public $endpointUri = "";
    
    /** URI of the test dataset to use for the test suite */
    public $testDataset = "";
    
    /** List of web services endpoint URI that are used on all testing datasets */
    public $datasetWebservices = array();
    
    /** The IP of the server that runs the tests. */
    public $requesterIP = "";
    
    /** Random IP for a dummy requester */
    public $randomRequester = "";


    // Additional configs used to validate resultsets
    
    /** structXML resultset that is supposed to be returned by the Dataset Read endpoint */
    public $datasetReadStructXMLResultset = "";
    
    /** structXML resultset that is supposed to be returned by the Dataset Read endpoint after the testing
        dataset got updated */
    public $datasetUpdatedReadStructXMLResultset = "";
    
    /** structXML resultsetin  JSON that is supposed to be returned by the Dataset Read endpoint */
    public $datasetReadStructJSONResultset = "";
    
    /** structXML resultset in rdf+xml that is supposed to be returned by the Dataset Read endpoint */
    public $datasetReadStructRDFXMLResultset = "";
    
    /** structXML resultset in rdf+n3 that is supposed to be returned by the Dataset Read endpoint */
    public $datasetReadStructRDFN3Resultset = "";
    
    /** String to use to update (change) values of the triples of a dataset description */
    public $datasetUpdateString = "";
    
    /** URI of the ontology to use for the ontologies related endpoints */
    public $testOntologyUri = "";
    
    /** URI of an invalid ontology to use for the ontologies related endpoints */
    public $testInvalidOntologyUri = "";
    
    /** URI of a datatype property of the test ontology */
    public $targetDatatypePropertyUri = "";

    /** URI of an object property of the test ontology */
    public $targetObjectPropertyUri = "";
    
    /** URI of an object property of the test ontology */
    public $targetAnnotationPropertyUri = "";
    
    /** URI of a class of the test ontology */
    public $targetClassUri = "";      
    
    /** URI of a named individual of the test ontology */
    public $targetNamedIndividualUri = "";  
    
    /** Title of a new web service endpoint to register in the network */
    public $newWebServiceTitle = "";        
    
    /** Endpoint URL of a new web service endpoint to register in the network */
    public $newWebServiceEndpointUrl = "";        
    
    /** CRUD usage of a new web service endpoint to register in the network */
    public $newWebServiceCrudUsage = "";        
    
    /** URI of the resource representing a new web service endpoint to register in the network */
    public $newWebServiceUri = "";     
    
    /** Auth Validator web service endpoint's interface */
    public $authValidatorInterface = "DefaultSourceInterface";   
    
    /** Auth Validator web service endpoint's interface version */
    public $authValidatorInterfaceVersion = ""; 
      
    /** Auth Registrar Access web service endpoint's interface */
    public $authRegistrarAccessInterface = "DefaultSourceInterface";   
    
    /** Auth Validator web service endpoint's interface version */
    public $authRegistrarAccessInterfaceVersion = "";   
    
    /** Auth Lister web service endpoint's interface version */
    public $authListerInterfaceVersion = "";   
    
    /** Auth Lister web service endpoint's interface */
    public $authListerInterface = "DefaultSourceInterface";   
    
    /** Dataset Create web service endpoint's interface version */
    public $datasetCreateInterfaceVersion = "";   
    
    /** Dataset Create web service endpoint's interface */
    public $datasetCreateInterface = "DefaultSourceInterface";   
    
    /** Dataset Read web service endpoint's interface version */
    public $datasetReadInterfaceVersion = "";   
    
    /** Dataset Read web service endpoint's interface */
    public $datasetReadInterface = "DefaultSourceInterface";   
    
    /** Dataset Update web service endpoint's interface version */
    public $datasetUpdateInterfaceVersion = "";   
    
    /** Dataset Update web service endpoint's interface */
    public $datasetUpdateInterface = "DefaultSourceInterface";   
    
    /** Dataset Delete web service endpoint's interface version */
    public $datasetDeleteInterfaceVersion = "";   
    
    /** Dataset Delete web service endpoint's interface */
    public $datasetDeleteInterface = "DefaultSourceInterface";   
    
    /** Ontology Create web service endpoint's interface version */
    public $ontologyCreateInterfaceVersion = "";   
    
    /** Ontology Create web service endpoint's interface */
    public $ontologyCreateInterface = "DefaultSourceInterface";   
    
    /** Ontology Delete web service endpoint's interface version */
    public $ontologyDeleteInterfaceVersion = "";   
    
    /** Ontology Delete web service endpoint's interface */
    public $ontologyDeleteInterface = "DefaultSourceInterface";   
    
    /** Ontology Read web service endpoint's interface version */
    public $ontologyReadInterfaceVersion = "";   
    
    /** Ontology Read web service endpoint's interface */
    public $ontologyReadInterface = "DefaultSourceInterface";   
    
    /** CRUD Create web service endpoint's interface version */
    public $crudCreateInterfaceVersion = "";   
    
    /** CRUD Create web service endpoint's interface */
    public $crudCreateInterface = "DefaultSourceInterface";  
    
    /** CRUD Update web service endpoint's interface version */
    public $crudUpdateInterfaceVersion = "";   
    
    /** CRUD Update web service endpoint's interface */
    public $crudUpdateInterface = "DefaultSourceInterface";  
    
    /** CRUD Read web service endpoint's interface version */
    public $crudReadInterfaceVersion = "";   
    
    /** CRUD Read web service endpoint's interface */
    public $crudReadInterface = "DefaultSourceInterface";  
    
    /** Directory where content files used by the tests are located */
    public $contentDir = ''; 
    
    function __construct()
    {
      /** 
      * If the REMOTE_ADDR is not defined, it probably means that the test is ran from the command
      * line so we simply set it using the localhost. 
      */
      if(!isset($_SERVER['REMOTE_ADDR']))
      {
        $_SERVER['REMOTE_ADDR'] = "127.0.0.1";
      }
      
      /** Directory where content files used by the tests are located */
      $this->contentDir = __DIR__ . '/content/';
      
      /** 
        structWSF web service interface to use for all endpoints 
        Note: if you specify "default", then the default interfaces defined in the
              network.ini file will be used for the calls
      */
      $this->webServiceInterface = "DefaultSourceInterface";
        
      /** Folder where PHPUnit is installed on the server */
      $this->phpUnitInstallFolder = "/usr/share/php/PHPUnit/";
      
      /** Folder where the structWSF instance is located on the server */
      $this->structwsfInstanceFolder = "/usr/share/structwsf/StructuredDynamics/structwsf/ws/";
      
      /** Base URL of the endpoint to test */
      $this->endpointUrl = "http://localhost/ws/";
      
      /** Base URI of the web services in the structWSF network */
      $this->endpointUri = "http://localhost/wsf/ws/";
      
      /** URI of the test dataset to use for the test suite */
      $this->testDataset = "http://test.com/unittests/";
      
      /** The IP of the server that runs the tests. */
      $this->requesterIP = "184.73.189.112";

      /** Random IP for a dummy requester */
      $this->randomRequester = "192.168.0.1";
      
      /** URI of the ontology to use for the ontologies related endpoints */
      $this->testOntologyUri = "file://localhost" . __DIR__ . "/content/foo.owl";

      /** URI of an invalid ontology to use for the ontologies related endpoints */
      $this->testInvalidOntologyUri = "file://localhost" . __DIR__ . "/content/fooInvalid.owl";
      
      /** URI of a datatype property of the test ontology */
      $this->targetDatatypePropertyUri = "http://foo.org/test#dpD";

      /** URI of an object property of the test ontology */
      $this->targetObjectPropertyUri = "http://foo.org/test#opD";
      
      /** URI of an object property of the test ontology */
      $this->targetAnnotationPropertyUri = "http://foo.org/test#aA";      
      
      /** URI of a class of the test ontology */
      $this->targetClassUri = "http://foo.org/test#A";      
      
      /** URI of a named individual of the test ontology */
      $this->targetNamedIndividualUri = "http://foo.org/test#niA";      
      
      /** Title of a new web service endpoint to register in the network */
      $this->newWebServiceTitle = "New Web Service Endpoint";        
      
      /** Endpoint URL of a new web service endpoint to register in the network */
      $this->newWebServiceEndpointUrl = $this->endpointUrl."new/";        
      
      /** CRUD usage of a new web service endpoint to register in the network */
      $this->newWebServiceCrudUsage = "True;True;True;True";
      
      /** URI of the resource representing a new web service endpoint to register in the network */
      $this->newWebServiceUri = $this->endpointUri."new/";
      
      /** String to use to update (change) values of the triples of a dataset description */
      $this->datasetUpdateString = "-update";
      
      /** Auth Validator web service endpoint's interface version */
      $this->authValidatorInterfaceVersion = "1.0";   
      
      /** Auth Registrar Access web service endpoint's interface version */
      $this->authRegistrarInterfaceVersion = "1.0";         
      
      /** Auth Lister web service endpoint's interface version */
      $this->authListerInterfaceVersion = "1.0";   
      
      /** Dataset Create web service endpoint's interface version */
      $this->datasetCreateInterfaceVersion = "1.0";   
      
      /** Dataset Read web service endpoint's interface version */
      $this->datasetReadInterfaceVersion = "1.0";   
      
      /** Dataset Update web service endpoint's interface version */
      $this->datasetUpdateInterfaceVersion = "1.0";   
      
      /** Dataset Delete web service endpoint's interface version */
      $this->datasetDeleteInterfaceVersion = "1.0";   
      
      /** Ontology Create web service endpoint's interface version */
      $this->ontologyCreateInterfaceVersion = "1.0";   
      
      /** Ontology Delete web service endpoint's interface version */
      $this->ontologyDeleteInterfaceVersion = "1.0";   
      
      /** Ontology Read web service endpoint's interface version */
      $this->ontologyReadInterfaceVersion = "1.0";        
      
      /** CRUD Create web service endpoint's interface version */
      $this->crudCreateInterfaceVersion = "1.0";        
      
      /** CRUD Update web service endpoint's interface version */
      $this->crudUpdateInterfaceVersion = "1.0";        
      
      /** CRUD Read web service endpoint's interface version */
      $this->crudReadInterfaceVersion = "1.0";        
      
      /** CRUD Delete web service endpoint's interface version */
      $this->crudDeleteInterfaceVersion = "1.0";        
      
      /** Revision Delete web service endpoint's interface version */
      $this->revisionDeleteInterfaceVersion = "1.0";        
      
      /** Revision Read web service endpoint's interface version */
      $this->revisionReadInterfaceVersion = "1.0";        

      /** Auth Validator web service endpoint's interface */
      $this->authValidatorInterface = "DefaultSourceInterface";   
      
      /** Auth Registrar Access web service endpoint's interface */
      $this->authRegistrarInterface = "DefaultSourceInterface";   
      
      /** Auth Lister web service endpoint's interface version */
      $this->authListerInterface = "DefaultSourceInterface";   
      
      /** Dataset Create web service endpoint's interface */
      $this->datasetCreateInterface = "DefaultSourceInterface";   
      
      /** Dataset Read web service endpoint's interface */
      $this->datasetReadInterface = "DefaultSourceInterface";   
      
      /** Dataset Update web service endpoint's interface */
      $this->datasetUpdateInterface = "DefaultSourceInterface";   
      
      /** Dataset Delete web service endpoint's interface */
      $this->datasetDeleteInterface = "DefaultSourceInterface";   
      
      /** Ontology Create web service endpoint's interface */
      $this->ontologyCreateInterface = "DefaultSourceInterface";   
      
      /** Ontology Delete web service endpoint's interface */
      $this->ontologyDeleteInterface = "DefaultSourceInterface";   
      
      /** Ontology Read web service endpoint's interface */
      $this->ontologyReadInterface = "DefaultSourceInterface";        
      
      /** CRUD Create web service endpoint's interface */
      $this->crudCreateInterface = "DefaultSourceInterface";        
      
      /** CRUD Update web service endpoint's interface */
      $this->crudUpdateInterface = "DefaultSourceInterface";        
      
      /** CRUD Read web service endpoint's interface */
      $this->crudReadInterface = "DefaultSourceInterface";        
      
      /** CRUD Delete web service endpoint's interface */
      $this->crudDeleteInterface = "DefaultSourceInterface";        
      
      /** Revision Delete web service endpoint's interface */
      $this->revisionDeleteInterface = "DefaultSourceInterface";        
      
      /** Revision Read web service endpoint's interface */
      $this->revisionReadInterface = "DefaultSourceInterface";        

            
      $this->datasetWebservices = array($this->endpointUri."auth/validator/",
                                        $this->endpointUri."auth/lister/",
                                        $this->endpointUri."sparql/",
                                        $this->endpointUri."converter/bibtex/",
                                        $this->endpointUri."converter/tsv/",
                                        $this->endpointUri."converter/irjson/",
                                        $this->endpointUri."search/",
                                        $this->endpointUri."browse/",
                                        $this->endpointUri."auth/registrar/ws/",
                                        $this->endpointUri."auth/registrar/access/",
                                        $this->endpointUri."dataset/create/",
                                        $this->endpointUri."dataset/read/",
                                        $this->endpointUri."dataset/update/",
                                        $this->endpointUri."dataset/delete/",
                                        $this->endpointUri."crud/create/",
                                        $this->endpointUri."crud/read/",
                                        $this->endpointUri."crud/update/",
                                        $this->endpointUri."crud/delete/",
                                        $this->endpointUri."revision/update/",
                                        $this->endpointUri."revision/read/",
                                        $this->endpointUri."revision/delete/",
                                        $this->endpointUri."revision/lister/",
                                        $this->endpointUri."revision/diff/",
                                        $this->endpointUri."ontology/create/",
                                        $this->endpointUri."ontology/delete/",
                                        $this->endpointUri."ontology/read/",
                                        $this->endpointUri."ontology/update/");
                                  
      $this->datasetReadStructXMLResultset = '<resultset>
          <prefix entity="owl" uri="http://www.w3.org/2002/07/owl#"/>
          <prefix entity="rdf" uri="http://www.w3.org/1999/02/22-rdf-syntax-ns#"/>
          <prefix entity="rdfs" uri="http://www.w3.org/2000/01/rdf-schema#"/>
          <prefix entity="iron" uri="http://purl.org/ontology/iron#"/>
          <prefix entity="xsd" uri="http://www.w3.org/2001/XMLSchema#"/>
          <prefix entity="wsf" uri="http://purl.org/ontology/wsf#"/>
          <prefix entity="void" uri="http://rdfs.org/ns/void#"/>
          <prefix entity="dcterms" uri="http://purl.org/dc/terms/"/>
          <subject uri="http://test.com/unittests/" type="void:Dataset">
            <predicate type="dcterms:title">
              <object type="rdfs:Literal">This is a testing dataset</object>
            </predicate>
            <predicate type="dcterms:description">
              <object type="rdfs:Literal">This is a testing dataset</object>
            </predicate>
            <predicate type="dcterms:creator">
              <object uri="http://test.com/user/bob/" type="sioc:User"></object>
            </predicate>
            <predicate type="dcterms:created">
              <object type="rdfs:Literal">'.date("Y-n-j").'</object>
            </predicate>
          </subject>
        </resultset>      
      ';
      
      $this->datasetUpdatedReadStructXMLResultset = '<resultset>
              <prefix entity="owl" uri="http://www.w3.org/2002/07/owl#"/>
              <prefix entity="rdf" uri="http://www.w3.org/1999/02/22-rdf-syntax-ns#"/>
              <prefix entity="rdfs" uri="http://www.w3.org/2000/01/rdf-schema#"/>
              <prefix entity="iron" uri="http://purl.org/ontology/iron#"/>
              <prefix entity="xsd" uri="http://www.w3.org/2001/XMLSchema#"/>
              <prefix entity="wsf" uri="http://purl.org/ontology/wsf#"/>
              <prefix entity="void" uri="http://rdfs.org/ns/void#"/>
              <prefix entity="dcterms" uri="http://purl.org/dc/terms/"/>
              <subject type="void:Dataset" uri="http://test.com/unittests/">
                  <predicate type="dcterms:title">
                      <object type="rdfs:Literal">This is a testing dataset'.$this->datasetUpdateString.'</object>
                  </predicate>
                  <predicate type="dcterms:description">
                      <object type="rdfs:Literal">This is a testing dataset'.$this->datasetUpdateString.'</object>
                  </predicate>
                  <predicate type="dcterms:creator">
                      <object type="sioc:User" uri="http://test.com/user/bob/" />
                  </predicate>                  
                  <predicate type="dcterms:created">
                      <object type="rdfs:Literal">'.date("Y-n-j").'</object>
                  </predicate>
                  <predicate type="dcterms:modified">
                      <object type="rdfs:Literal">'.date("Y-n-j").'</object>
                  </predicate>
                  <predicate type="dcterms:contributor">
                      <object type="sioc:User" uri="http://test.com/user/bob'.$this->datasetUpdateString.'/" />
                  </predicate>
              </subject>
          </resultset>     
      ';      
      
      $this->datasetReadStructJSONResultset = '
        {
          "prefixes": {
            "owl": "http://www.w3.org/2002/07/owl#",
            "rdf": "http://www.w3.org/1999/02/22-rdf-syntax-ns#",
            "rdfs": "http://www.w3.org/2000/01/rdf-schema#",
            "iron": "http://purl.org/ontology/iron#",
            "xsd": "http://www.w3.org/2001/XMLSchema#",
            "wsf": "http://purl.org/ontology/wsf#",
            "void": "http://rdfs.org/ns/void#",
            "dcterms": "http://purl.org/dc/terms/"
          },
          "resultset": {
            "subject": [
              {
                "uri": "http://test.com/unittests/",
                "type": "void:Dataset",
                "predicate": [
                  {
                    "dcterms:title": "This is a testing dataset"
                  },
                  {
                    "dcterms:description": "This is a testing dataset"
                  },
                  {
                    "dcterms:creator": {
                      "uri": "http://test.com/user/bob/",
                      "type": "sioc:User"
                    }
                  },
                  {
                    "dcterms:created": "'.date("Y-n-j").'"
                  }
                ]
              }
            ]
          }
        }      
      ';
      
      $this->datasetReadStructRDFXMLResultset = '<?xml version="1.0"?>
        <rdf:RDF xmlns:owl="http://www.w3.org/2002/07/owl#" xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#" xmlns:rdfs="http://www.w3.org/2000/01/rdf-schema#" xmlns:iron="http://purl.org/ontology/iron#" xmlns:xsd="http://www.w3.org/2001/XMLSchema#" xmlns:wsf="http://purl.org/ontology/wsf#" xmlns:void="http://rdfs.org/ns/void#" xmlns:dcterms="http://purl.org/dc/terms/">
            <void:Dataset rdf:about="http://test.com/unittests/">
                <dcterms:title>This is a testing dataset</dcterms:title>
                <dcterms:description>This is a testing dataset</dcterms:description>
                <dcterms:creator rdf:resource="http://test.com/user/bob/" />
                <dcterms:created>'.date("Y-n-j").'</dcterms:created>
            </void:Dataset>
        </rdf:RDF>      
      ';
      
      $this->datasetReadStructRDFN3Resultset = '
        @prefix rdf: <http://www.w3.org/1999/02/22-rdf-syntax-ns#> .
        @prefix rdfs: <http://www.w3.org/2000/01/rdf-schema#> .
        @prefix void: <http://rdfs.org/ns/void#> .
        @prefix dcterms: <http://purl.org/dc/terms/> .
        @prefix wsf: <http://purl.org/ontology/wsf#> .

        <http://test.com/unittests/> a void:Dataset ;
          dcterms:title """This is a testing dataset""" ;
          dcterms:description """This is a testing dataset""" ;
          dcterms:created """'.date("Y-n-j").'""" ;
          dcterms:creator <http://test.com/user/bob/> .      
      ';
    }
  }
?>
