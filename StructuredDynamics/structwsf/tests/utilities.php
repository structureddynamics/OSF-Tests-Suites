<?php
  
  namespace StructuredDynamics\structwsf\tests;
  
  use StructuredDynamics\structwsf\framework\WebServiceQuerier;
  use \StructuredDynamics\structwsf\php\api\ws\crud\delete\CrudDeleteQuery;
  
  /*
  
    These are a series of utility functions that are used to perform specific actions
    in different tests. These functions generally perform other web services actions
    needed to properly test different workflows of different unique tests.
  
  */
  
  
  function createDataset()
  {
    $settings = new Config();     
    
    $wsq = new WebServiceQuerier($settings->endpointUrl . "dataset/create/", 
                                 "post", 
                                 "text/xml",
                                 "uri=" . urlencode($settings->testDataset) .
                                 "&title=" . urlencode("This is a testing dataset") .
                                 "&description=" . urlencode("This is a testing dataset") .
                                 "&creator=" . urlencode("http://test.com/user/bob/") .
                                 "&webservices=" . urlencode($settings->datasetWebservices) .
                                 "&globalPermissions=" . urlencode("True;True;True;True"));    
                         
    if($wsq->getStatus() != "200")
    {            
      return(FALSE);
    }
    
    return(TRUE);                                 
  }
  
  function createDatasetGlobalPermissionsNone()
  {
    $settings = new Config();     
    
    $wsq = new WebServiceQuerier($settings->endpointUrl . "dataset/create/", 
                                 "post", 
                                 "text/xml",
                                 "uri=" . urlencode($settings->testDataset) .
                                 "&title=" . urlencode("This is a testing dataset") .
                                 "&description=" . urlencode("This is a testing dataset") .
                                 "&creator=" . urlencode("http://test.com/user/bob/") .
                                 "&webservices=" . urlencode($settings->datasetWebservices) .
                                 "&globalPermissions=" . urlencode("False;False;False;False"));    
                         
    if($wsq->getStatus() != "200")
    {
      return(FALSE);
    }
    
    return(TRUE);                                 
  }  
  
  function createTwoDatasets()
  {
    $settings = new Config();     
    
    $wsq = new WebServiceQuerier($settings->endpointUrl . "dataset/create/", 
                                 "post", 
                                 "text/xml",
                                 "uri=" . urlencode($settings->testDataset) .
                                 "&title=" . urlencode("This is a testing dataset") .
                                 "&description=" . urlencode("This is a testing dataset") .
                                 "&creator=" . urlencode("http://test.com/user/bob/") .
                                 "&webservices=" . urlencode($settings->datasetWebservices) .
                                 "&globalPermissions=" . urlencode("True;True;True;True"));    
                               
    if($wsq->getStatus() != "200")
    {
      return(FALSE);
    }

    $wsq = new WebServiceQuerier($settings->endpointUrl . "dataset/create/", 
                                 "post", 
                                 "text/xml",
                                 "uri=" . urlencode($settings->testDataset."2/")  .
                                 "&title=" . urlencode("This is a testing dataset") .
                                 "&description=" . urlencode("This is a testing dataset") .
                                 "&creator=" . urlencode("http://test.com/user/bob/") .
                                 "&webservices=" . urlencode($settings->datasetWebservices) .
                                 "&globalPermissions=" . urlencode("True;True;True;True"));    
                               
    if($wsq->getStatus() != "200")
    {
      return(FALSE);
    }
    
    return(TRUE);                                 
  }  
  
  
  function createTwoDatasetsGlobalPermissionsNone()
  {
    $settings = new Config();     
    
    $wsq = new WebServiceQuerier($settings->endpointUrl . "dataset/create/", 
                                 "post", 
                                 "text/xml",
                                 "uri=" . urlencode($settings->testDataset) .
                                 "&title=" . urlencode("This is a testing dataset") .
                                 "&description=" . urlencode("This is a testing dataset") .
                                 "&creator=" . urlencode("http://test.com/user/bob/") .
                                 "&webservices=" . urlencode($settings->datasetWebservices) .
                                 "&globalPermissions=" . urlencode("False;False;False;False"));    
                               
    if($wsq->getStatus() != "200")
    {
      return(FALSE);
    }

    $wsq = new WebServiceQuerier($settings->endpointUrl . "dataset/create/", 
                                 "post", 
                                 "text/xml",
                                 "uri=" . urlencode($settings->testDataset."2/")  .
                                 "&title=" . urlencode("This is a testing dataset") .
                                 "&description=" . urlencode("This is a testing dataset") .
                                 "&creator=" . urlencode("http://test.com/user/bob/") .
                                 "&webservices=" . urlencode($settings->datasetWebservices) .
                                 "&globalPermissions=" . urlencode("False;False;False;False"));    
                               
    if($wsq->getStatus() != "200")
    {
      return(FALSE);
    }
    
    return(TRUE);                                 
  }    
  
  function deleteDataset()
  {
    $settings = new Config(); 
    
    $wsq = new WebServiceQuerier($settings->endpointUrl . "dataset/delete/", 
                                 "get", 
                                 "text/xml",
                                 "uri=" . urlencode($settings->testDataset));    
    
    if($wsq->getStatus() != "200")
    {
      return(FALSE);
    }
    
    return(TRUE);
  } 
  
  
   
  function deleteTwoDatasets()
  {
    $settings = new Config(); 
    
    $wsq = new WebServiceQuerier($settings->endpointUrl . "dataset/delete/", 
                                 "get", 
                                 "text/xml",
                                 "uri=" . urlencode($settings->testDataset));    
    
    if($wsq->getStatus() != "200")
    {
      return(FALSE);
    }
    
    $wsq = new WebServiceQuerier($settings->endpointUrl . "dataset/delete/", 
                                 "get", 
                                 "text/xml",
                                 "uri=" . urlencode($settings->testDataset) . "2/");    
    
    if($wsq->getStatus() != "200")
    {
      return(FALSE);
    }
    
    return(TRUE);
  }
  
  function readDataset()
  {
    $settings = new Config(); 
    
    $wsq = new WebServiceQuerier($settings->endpointUrl . "dataset/read/", 
                                 "get", 
                                 "text/xml",
                                 "uri=" . urlencode($settings->testDataset));    

    if($wsq->getStatus() != "200")
    {
      return(FALSE);
    }
    
    return($wsq->getResultset());    
  }
  
  function createRecord($rdf)
  {
    
  }
  
  function deleteRecord($rdf)
  {
    
  }
  
  function createNoAccess_AccessRecord()
  {
    $settings = new Config();     
    
    $wsq = new WebServiceQuerier($settings->endpointUrl . "auth/registrar/access/", 
                                 "post", 
                                 "text/xml",
                                 "crud=" . urlencode("False;False;False;False") .
                                 "&ws_uris=" . $settings->datasetWebservices .
                                 "&dataset=" . urlencode($settings->testDataset) .
                                 "&action=" . urlencode("create") .
                                 "&registered_ip=" . urlencode($settings->randomRequester));    
                         
    if($wsq->getStatus() != "200")
    {
      return(FALSE);
    }
    
    return(TRUE);       
  }
  
  function createOntology()
  {
    $settings = new Config();     
    
    $wsq = new WebServiceQuerier($settings->endpointUrl . "ontology/create/", 
                                 "post", 
                                 "text/xml",
                                 "uri=" . urlencode($settings->testOntologyUri) .
                                 "&globalPermissions=" . urlencode("True;True;True;True") .
                                 "&advancedIndexation=" . urlencode("True") .
                                 "&reasoner=" . urlencode("True") .
                                 "&registered_ip=" . urlencode("Self"));    
                         
    if($wsq->getStatus() != "200")
    {            
      return(FALSE);
    }
    
    return(TRUE);                                 
  }  
  
  function deleteOntology()
  {
    $settings = new Config();     
    
    $wsq = new WebServiceQuerier($settings->endpointUrl . "ontology/delete/", 
                                 "post", 
                                 "text/xml",
                                 "ontology=" . urlencode($settings->testOntologyUri) .
                                 "&function=" . urlencode("deleteOntology") .
                                 "&registered_ip=" . urlencode("Self"));    
    
    if($wsq->getStatus() != "200")
    {            
      return(FALSE);
    }
    
    return(TRUE);                                 
  }   
  
  function unregisterWebServiceEndpoint()
  {
    //
    //
    //
    //   NEED TO UPDATE THE ENDPOINT TO BE ABLE TO UNREGISTER ENDPOINTS!!!!!
    //
    //
    //
    //
    
    
    $settings = new Config(); 
    
    $crudDelete = new CrudDeleteQuery($settings->endpointUrl);
    
    $crudDelete->dataset("http://ccr.nhccn.com.au/wsf/");
                          
    $crudDelete->uri($settings->newWebServiceUri);
    
    $crudDelete->send();
    
    if($crudDelete->getStatus() != "200")
    {
      print_r(var_export($crudDelete, TRUE));
      return(FALSE);
    }
    
    return(TRUE);
  }  
  
?>
