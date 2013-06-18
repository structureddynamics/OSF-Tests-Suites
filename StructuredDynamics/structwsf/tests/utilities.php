<?php
  
  namespace StructuredDynamics\structwsf\tests;
  
  use \StructuredDynamics\structwsf\php\api\ws\auth\registrar\access\AuthRegistrarAccessQuery;
  use \StructuredDynamics\structwsf\php\api\ws\crud\delete\CrudDeleteQuery;
  use \StructuredDynamics\structwsf\php\api\ws\crud\create\CrudCreateQuery;
  use \StructuredDynamics\structwsf\php\api\ws\crud\update\CrudUpdateQuery;
  use \StructuredDynamics\structwsf\php\api\ws\dataset\create\DatasetCreateQuery;
  use \StructuredDynamics\structwsf\php\api\ws\dataset\delete\DatasetDeleteQuery;
  use \StructuredDynamics\structwsf\php\api\ws\dataset\read\DatasetReadQuery;
  use \StructuredDynamics\structwsf\php\api\framework\CRUDPermission;
  use \StructuredDynamics\structwsf\php\api\ws\revision\lister\RevisionListerQuery;
  use \StructuredDynamics\structwsf\php\api\ws\ontology\create\OntologyCreateQuery;
  use \StructuredDynamics\structwsf\php\api\ws\ontology\delete\OntologyDeleteQuery;
  
  /*
  
    These are a series of utility functions that are used to perform specific actions
    in different tests. These functions generally perform other web services actions
    needed to properly test different workflows of different unique tests.
  
  */
  
  function createRevisionedRecord($published = TRUE)
  {
    $settings = new Config();  
    
    createDataset();
               
    $crudCreate = new CrudCreateQuery($settings->endpointUrl);
    
    $crudCreate->dataset($settings->testDataset)
               ->document(file_get_contents($settings->contentDir.'crud_create.n3'))
               ->documentMimeIsRdfN3()
               ->enableFullIndexationMode()
               ->sourceInterface($settings->crudCreateInterface)
               ->sourceInterfaceVersion($settings->crudCreateInterfaceVersion)
               ->send();
           
    if(!$crudCreate->isSuccessful())
    {            
      return(FALSE);
    }   

    $crudUpdate = new CrudUpdateQuery($settings->endpointUrl);
    
    $crudUpdate->dataset($settings->testDataset)
               ->document(file_get_contents($settings->contentDir.'crud_update.n3'))
               ->documentMimeIsRdfN3()
               ->createRevision()
               ->sourceInterface($settings->crudUpdateInterface)
               ->sourceInterfaceVersion($settings->crudUpdateInterfaceVersion);
               
    if($published)
    {
      $crudUpdate->isPublished()
                 ->send();
    }
    else
    {
      $crudUpdate->isUnspecified()
                 ->send();
    }
           
    if(!$crudUpdate->isSuccessful())
    {          
      return(FALSE);
    }       
                  
    return(TRUE);     
  }
  
  function createUnrevisionedRecord()
  {
    $settings = new Config();  
    
    createDataset();
               
    $crudCreate = new CrudCreateQuery($settings->endpointUrl);
    
    $crudCreate->dataset($settings->testDataset)
               ->document(file_get_contents($settings->contentDir.'crud_create.n3'))
               ->documentMimeIsRdfN3()
               ->enableFullIndexationMode()
               ->sourceInterface($settings->crudCreateInterface)
               ->sourceInterfaceVersion($settings->crudCreateInterfaceVersion)
               ->send();
           
    if(!$crudCreate->isSuccessful())
    {   die(var_export($crudCreate, TRUE));
      return(FALSE);
    }   
                     
    return(TRUE);     
  }  
  
  function deleteUnrevisionedRecord()
  {
    deleteDataset();
  }
  
  function deleteRevisionedRecord()
  {
    deleteDataset();
  }
  
  function createDataset()
  {
    $settings = new Config();     

    $crudPermissions = new CRUDPermission(TRUE, TRUE, TRUE, TRUE);
                                 
    $datasetCreate = new DatasetCreateQuery($settings->endpointUrl);
    
    $datasetCreate->uri($settings->testDataset)
                  ->title("This is a testing dataset")
                  ->description("This is a testing dataset")
                  ->creator("http://test.com/user/bob/")
                  ->targetWebservices($settings->datasetWebservices)
                  ->globalPermissions($crudPermissions)
                  ->mime('text/xml')
                  ->sourceInterface($settings->datasetCreateInterface)
                  ->sourceInterfaceVersion($settings->datasetCreateInterfaceVersion)
                  ->send();
    
    
    
    if(!$datasetCreate->isSuccessful())
    {            
      return(FALSE);
    }
    
    return(TRUE);                                 
  }
  
  function createDatasetGlobalPermissionsNone()
  {
    $settings = new Config();     
    
    $crudPermissions = new CRUDPermission(FALSE, FALSE, FALSE, FALSE);
                                 
    $datasetCreate = new DatasetCreateQuery($settings->endpointUrl);
    
    $datasetCreate->uri($settings->testDataset)
                  ->title("This is a testing dataset")
                  ->description("This is a testing dataset")
                  ->creator("http://test.com/user/bob/")
                  ->targetWebservices($settings->datasetWebservices)
                  ->globalPermissions($crudPermissions)
                  ->mime('text/xml')
                  ->sourceInterface($settings->datasetCreateInterface)
                  ->sourceInterfaceVersion($settings->datasetCreateInterfaceVersion)
                  ->send();
                         
    if(!$datasetCreate->isSuccessful())
    {
      return(FALSE);
    }
    
    return(TRUE);                                 
  }  
  
  function createTwoDatasets()
  {
    $settings = new Config();     
    
    $crudPermissions = new CRUDPermission(TRUE, TRUE, TRUE, TRUE);
                                 
    $datasetCreate = new DatasetCreateQuery($settings->endpointUrl);
    
    $datasetCreate->uri($settings->testDataset)
                  ->title("This is a testing dataset")
                  ->description("This is a testing dataset")
                  ->creator("http://test.com/user/bob/")
                  ->targetWebservices($settings->datasetWebservices)
                  ->globalPermissions($crudPermissions)
                  ->mime('text/xml')
                  ->sourceInterface($settings->datasetCreateInterface)
                  ->sourceInterfaceVersion($settings->datasetCreateInterfaceVersion)
                  ->send();
                         
    if(!$datasetCreate->isSuccessful())    
    {
      return(FALSE);
    }
                                 
    $datasetCreate = new DatasetCreateQuery($settings->endpointUrl);
    
    $datasetCreate->uri($settings->testDataset.'2/')
                  ->title("This is a testing dataset")
                  ->description("This is a testing dataset")
                  ->creator("http://test.com/user/bob/")
                  ->targetWebservices($settings->datasetWebservices)
                  ->globalPermissions($crudPermissions)
                  ->mime('text/xml')
                  ->sourceInterface($settings->datasetCreateInterface)
                  ->sourceInterfaceVersion($settings->datasetCreateInterfaceVersion)
                  ->send();
                         
    if(!$datasetCreate->isSuccessful())    
    {
      return(FALSE);
    }
    
    return(TRUE);                                 
  }  
  
  
  function createTwoDatasetsGlobalPermissionsNone()
  {
    $settings = new Config();     
    
    $crudPermissions = new CRUDPermission(FALSE, FALSE, FALSE, FALSE);
                                 
    $datasetCreate = new DatasetCreateQuery($settings->endpointUrl);
    
    $datasetCreate->uri($settings->testDataset)
                  ->title("This is a testing dataset")
                  ->description("This is a testing dataset")
                  ->creator("http://test.com/user/bob/")
                  ->targetWebservices($settings->datasetWebservices)
                  ->globalPermissions($crudPermissions)
                  ->mime('text/xml')
                  ->sourceInterface($settings->datasetCreateInterface)
                  ->sourceInterfaceVersion($settings->datasetCreateInterfaceVersion)
                  ->send();
                         
    if(!$datasetCreate->isSuccessful())    
    {
      return(FALSE);
    }

    $datasetCreate->uri($settings->testDataset.'2/')
                  ->title("This is a testing dataset")
                  ->description("This is a testing dataset")
                  ->creator("http://test.com/user/bob/")
                  ->targetWebservices($settings->datasetWebservices)
                  ->globalPermissions($crudPermissions)
                  ->mime('text/xml')
                  ->sourceInterface($settings->datasetCreateInterface)
                  ->sourceInterfaceVersion($settings->datasetCreateInterfaceVersion)
                  ->send();
                         
    if(!$datasetCreate->isSuccessful())    
    {
      return(FALSE);
    }
    
    return(TRUE);                                 
  }    
  
  function deleteDataset()
  {
    $settings = new Config(); 

    $datasetDelete = new DatasetDeleteQuery($settings->endpointUrl);
    
    $datasetDelete->uri($settings->testDataset)
                  ->mime('text/xml')
                  ->sourceInterface($settings->datasetDeleteInterface)
                  ->sourceInterfaceVersion($settings->datasetDeleteInterfaceVersion)
                  ->send();
                  
    if(!$datasetDelete->isSuccessful())
    {
      return(FALSE);
    }
    
    return(TRUE);
  } 
  
  
   
  function deleteTwoDatasets()
  {
    $settings = new Config(); 
    
    $datasetDelete = new DatasetDeleteQuery($settings->endpointUrl);
    
    $datasetDelete->uri($settings->testDataset)
                  ->mime('text/xml')
                  ->sourceInterface($settings->datasetDeleteInterface)
                  ->sourceInterfaceVersion($settings->datasetDeleteInterfaceVersion)
                  ->send();
    
    if(!$datasetDelete->isSuccessful())    
    {
      return(FALSE);
    }
    
    $datasetDelete = new DatasetDeleteQuery($settings->endpointUrl);
    
    $datasetDelete->uri($settings->testDataset.'2/')
                  ->mime('text/xml')
                  ->sourceInterface($settings->datasetDeleteInterface)
                  ->sourceInterfaceVersion($settings->datasetDeleteInterfaceVersion)
                  ->send();
    
    if(!$datasetDelete->isSuccessful())    
    {
      return(FALSE);
    }
    
    return(TRUE);
  }
  
  function readDataset()
  {
    $settings = new Config(); 
                                 
    $datasetRead = new DatasetReadQuery($settings->endpointUrl);
    
    $datasetRead->uri($settings->testDataset)
                ->mime('text/xml')
                ->sourceInterface($settings->datasetReadInterface)
                ->sourceInterfaceVersion($settings->datasetReadInterfaceVersion)
                ->send();
                                      
    if(!$datasetRead->isSuccessful())
    {
      return(FALSE);
    }
    
    return($datasetRead->getResultset());    
  }
  
  function createRecord()
  {
    $settings = new Config();  
    
    createDataset();
               
    $crudCreate = new CrudCreateQuery($settings->endpointUrl);
    
    $crudCreate->dataset($settings->testDataset)
               ->document(file_get_contents($settings->contentDir.'crud_create.n3'))
               ->documentMimeIsRdfN3()
               ->enableFullIndexationMode()
               ->sourceInterface($settings->crudCreateInterface)
               ->sourceInterfaceVersion($settings->crudCreateInterfaceVersion)
               ->send();
           
    if(!$crudCreate->isSuccessful())
    {            
      return(FALSE);
    }      
                     
    return(TRUE);      
  }
  
  function createNoAccess_AccessRecord()
  {
    $settings = new Config();     
    
    $crudPermissions = new CRUDPermission(FALSE, FALSE, FALSE, FALSE);                                 
                                 
    $authRegistrarAccess = new AuthRegistrarAccessQuery($settings->endpointUrl);
    
    $authRegistrarAccess->create($settings->randomRequester, $settings->testDataset, $crudPermissions, $settings->datasetWebservices)
                        ->mime('text/xml')
                        ->sourceInterface($settings->authRegistrarAccessInterface)
                        ->sourceInterfaceVersion($settings->authRegistrarAccessInterfaceVersion)
                        ->send();
                         
    if(!$authRegistrarAccess->isSuccessful())
    {
      return(FALSE);
    }
    
    return(TRUE);       
  }
  
  function createOntology()
  {
    $settings = new Config();     
        
    $crudPermissions = new CRUDPermission(TRUE, TRUE, TRUE, TRUE);         
                                 
    $ontologyCreate = new OntologyCreateQuery($settings->endpointUrl);
    
    $ontologyCreate->enableAdvancedIndexation()
                   ->enableReasoner()
                   ->uri($settings->testOntologyUri)
                   ->globalPermissions($crudPermissions)
                   ->mime('text/xml')
                   ->sourceInterface($settings->ontologyCreateInterface)
                   ->sourceInterfaceVersion($settings->ontologyCreateInterfaceVersion)
                   ->send();
                         
    if(!$ontologyCreate->isSuccessful())
    {            
      return(FALSE);
    }
    
    return(TRUE);                                 
  }  
  
  function deleteOntology()
  {
    $settings = new Config();     
    
    $ontologyDelete = new OntologyDeleteQuery($settings->endpointUrl);
    
    $ontologyDelete->ontology($settings->testOntologyUri)
                   ->deleteOntology()
                   ->mime('text/xml')
                   ->sourceInterface($settings->ontologyDeleteInterface)
                   ->sourceInterfaceVersion($settings->ontologyDeleteInterfaceVersion)
                   ->send();
    
    if(!$ontologyDelete->isSuccessful())
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
    
    $settings = new Config(); 
    
    $crudDelete = new CrudDeleteQuery($settings->endpointUrl);
    
    $crudDelete->dataset("http://ccr.nhccn.com.au/wsf/")
               ->uri($settings->newWebServiceUri)
               ->sourceInterface(/* TO SET */)
               ->sourceInterfaceVersion(/* TO SET */)               
               ->send();
    
    if(!$crudDelete->isSuccessful())
    {
      return(FALSE);
    }
    
    return(TRUE);
  }  
  
  function getLastRevisionUri($uri)
  {
    $settings = new Config();
    
    $revisionLister = new RevisionListerQuery($settings->endpointUrl);
    
    $revisionLister->dataset($settings->testDataset)
                   ->mime('resultset')
                   ->uri($uri)
                   ->shortResults()
                   ->send();
                   
    if(!$revisionLister->isSuccessful())
    {
      return(FALSE);
    } 
    
    $resultset = $revisionLister->getResultset()->getResultset();                  
                   
    if(!isset($resultset['unspecified']))                   
    {
      return(FALSE);
    }
    
    return(key($resultset['unspecified']));
  }
  
?>
