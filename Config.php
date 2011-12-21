<?php

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
    public $datasetWebservices = "";
    
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
        
      /** Folder where PHPUnit is installed on the server */
      $this->phpUnitInstallFolder = "/usr/share/php/PHPUnit/";
      
      /** Folder where the structWSF instance is located on the server */
      $this->structwsfInstanceFolder = "/usr/share/structwsf/";
      
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
      
      /** String to use to update (change) values of the triples of a dataset description */
      $this->datasetUpdateString = "-update";
            
      $this->datasetWebservices = $this->endpointUri."auth/validator/;".
                                  $this->endpointUri."auth/lister/;".
                                  $this->endpointUri."sparql/;".
                                  $this->endpointUri."converter/bibtex/;".
                                  $this->endpointUri."converter/tsv/;".
                                  $this->endpointUri."converter/irjson/;".
                                  $this->endpointUri."search/;".
                                  $this->endpointUri."browse/;".
                                  $this->endpointUri."auth/registrar/ws/;".
                                  $this->endpointUri."auth/registrar/access/;".
                                  $this->endpointUri."dataset/create/;".
                                  $this->endpointUri."dataset/read/;".
                                  $this->endpointUri."dataset/update/;".
                                  $this->endpointUri."dataset/delete/;".
                                  $this->endpointUri."crud/create/;".
                                  $this->endpointUri."crud/read/;".
                                  $this->endpointUri."crud/update/;".
                                  $this->endpointUri."crud/delete/;".
                                  $this->endpointUri."ontology/create/;".
                                  $this->endpointUri."ontology/delete/;".
                                  $this->endpointUri."ontology/read/;".
                                  $this->endpointUri."ontology/update/";
                                  
      $this->datasetReadStructXMLResultset = '<resultset>
          <prefix uri="http://www.w3.org/2002/07/owl#" entity="owl"></prefix>
          <prefix uri="http://www.w3.org/1999/02/22-rdf-syntax-ns#" entity="rdf"></prefix>
          <prefix uri="http://www.w3.org/2000/01/rdf-schema#" entity="rdfs"></prefix>
          <prefix uri="http://purl.org/ontology/wsf#" entity="wsf"></prefix>
          <prefix uri="http://purl.org/ontology/aggregate#" entity="aggr"></prefix>
          <prefix uri="http://rdfs.org/ns/void#" entity="void"></prefix>
          <prefix uri="http://rdfs.org/sioc/ns#" entity="sioc"></prefix>
          <prefix uri="http://purl.org/dc/terms/" entity="dcterms"></prefix>
          <subject uri="http://test.com/unittests/" type="void:Dataset">
            <predicate type="dcterms:title">
              <object type="rdfs:Literal">This is a testing dataset</object>
            </predicate>
            <predicate type="dcterms:description">
              <object type="rdfs:Literal">This is a testing dataset</object>
            </predicate>
            <predicate type="dcterms:created">
              <object type="rdfs:Literal">'.date("Y-n-j").'</object>
            </predicate>
            <predicate type="dcterms:creator">
              <object uri="http://test.com/user/bob/" type="sioc:User"></object>
            </predicate>
          </subject>
        </resultset>      
      ';
      
      $this->datasetUpdatedReadStructXMLResultset = '<resultset>
              <prefix entity="owl" uri="http://www.w3.org/2002/07/owl#" />
              <prefix entity="rdf" uri="http://www.w3.org/1999/02/22-rdf-syntax-ns#" />
              <prefix entity="rdfs" uri="http://www.w3.org/2000/01/rdf-schema#" />
              <prefix entity="wsf" uri="http://purl.org/ontology/wsf#" />
              <prefix entity="aggr" uri="http://purl.org/ontology/aggregate#" />
              <prefix entity="void" uri="http://rdfs.org/ns/void#" />
              <prefix entity="sioc" uri="http://rdfs.org/sioc/ns#" />
              <prefix entity="dcterms" uri="http://purl.org/dc/terms/" />
              <subject type="void:Dataset" uri="http://test.com/unittests/">
                  <predicate type="dcterms:title">
                      <object type="rdfs:Literal">This is a testing dataset'.$this->datasetUpdateString.'</object>
                  </predicate>
                  <predicate type="dcterms:description">
                      <object type="rdfs:Literal">This is a testing dataset'.$this->datasetUpdateString.'</object>
                  </predicate>
                  <predicate type="dcterms:created">
                      <object type="rdfs:Literal">'.date("Y-n-j").'</object>
                  </predicate>
                  <predicate type="dcterms:modified">
                      <object type="rdfs:Literal">'.date("Y-n-j").'</object>
                  </predicate>
                  <predicate type="dcterms:creator">
                      <object type="sioc:User" uri="http://test.com/user/bob/" />
                  </predicate>
                  <predicate type="dcterms:contributor">
                      <object type="sioc:User" uri="http://test.com/user/bob'.$this->datasetUpdateString.'/" />
                  </predicate>
              </subject>
          </resultset>     
      ';      
      
      $this->datasetReadStructJSONResultset = '
        {
            "prefixes": [{
                "rdf": "http:\/\/www.w3.org\/1999\/02\/22-rdf-syntax-ns#",
                "rdfs": "http://www.w3.org/2000/01/rdf-schema#",
                "void": "http://rdfs.org/ns/void#",
                "dcterms": "http://purl.org/dc/terms/"
            }],
            "resultset": {
                "subject": [{
                    "uri": "http://test.com/unittests/",
                    "type": "void:Dataset",
                    "predicate": [{
                        "dcterms:title": "This is a testing dataset"
                    }, {
                        "dcterms:description": "This is a testing dataset"
                    }, {
                        "dcterms:created": "'.date("Y-n-j").'"
                    }, {
                        "dcterms:creator": {
                            "uri": "http://test.com/user/bob/"
                        }
                    }]
                }]
            }
        }      
      ';
      
      $this->datasetReadStructRDFXMLResultset = '<?xml version="1.0"?>
        <rdf:RDF xmlns:wsf="http://purl.org/ontology/wsf#" xmlns:void="http://rdfs.org/ns/void#" xmlns:dcterms="http://purl.org/dc/terms/" xmlns:rdfs="http://www.w3.org/2000/01/rdf-schema#" xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#">
            <void:Dataset rdf:about="http://test.com/unittests/">
                <dcterms:title>This is a testing dataset</dcterms:title>
                <dcterms:description>This is a testing dataset</dcterms:description>
                <dcterms:created>'.date("Y-n-j").'</dcterms:created>
                <dcterms:creator rdf:resource="http://test.com/user/bob/" />
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
