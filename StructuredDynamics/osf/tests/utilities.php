<?php
  
  namespace StructuredDynamics\osf\tests;
  
  use \StructuredDynamics\osf\php\api\ws\auth\registrar\access\AuthRegistrarAccessQuery;
  use \StructuredDynamics\osf\php\api\ws\auth\registrar\user\AuthRegistrarUserQuery;
  use \StructuredDynamics\osf\php\api\ws\crud\delete\CrudDeleteQuery;
  use \StructuredDynamics\osf\php\api\ws\crud\create\CrudCreateQuery;
  use \StructuredDynamics\osf\php\api\ws\crud\update\CrudUpdateQuery;
  use \StructuredDynamics\osf\php\api\ws\dataset\create\DatasetCreateQuery;
  use \StructuredDynamics\osf\php\api\ws\dataset\delete\DatasetDeleteQuery;
  use \StructuredDynamics\osf\php\api\ws\dataset\read\DatasetReadQuery;
  use \StructuredDynamics\osf\php\api\framework\CRUDPermission;
  use \StructuredDynamics\osf\php\api\ws\revision\lister\RevisionListerQuery;
  use \StructuredDynamics\osf\php\api\ws\ontology\create\OntologyCreateQuery;
  use \StructuredDynamics\osf\php\api\ws\ontology\delete\OntologyDeleteQuery;
  use \StructuredDynamics\osf\php\api\ws\auth\registrar\group\AuthRegistrarGroupQuery;
  
  /*
  
    These are a series of utility functions that are used to perform specific actions
    in different tests. These functions generally perform other web services actions
    needed to properly test different workflows of different unique tests.
  
  */
  
  function createRevisionedRecord($published = TRUE)
  {
    $settings = new Config();  
    
    createDataset();
               
    $crudCreate = new CrudCreateQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
    
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

    $crudUpdate = new CrudUpdateQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
    
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
               
    $crudCreate = new CrudCreateQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);

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
  
  function deleteUnrevisionedRecord()
  {
    deleteDataset();
  }
  
  function deleteRevisionedRecord()
  {
    deleteDataset();
  }
  
  function createDatasetPermissions()
  {
    $settings = new Config(); 
    
    // Create the permissions for the "administrators" group    
    $crudPermissions = new CRUDPermission(TRUE, TRUE, TRUE, TRUE);
    
    $authRegistrarAccess = new AuthRegistrarAccessQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
    
    $authRegistrarAccess->create($settings->adminGroup, $settings->testDataset, $crudPermissions, $settings->datasetWebservices)
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
  
  function createDataset()
  {
    $settings = new Config();     

    $datasetCreate = new DatasetCreateQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
    
    $datasetCreate->uri($settings->testDataset)
                  ->title("This is a testing dataset")
                  ->description("This is a testing dataset")
                  ->creator("http://test.com/user/bob/")
                  ->targetWebservices($settings->datasetWebservices)
                  ->mime('text/xml')
                  ->sourceInterface($settings->datasetCreateInterface)
                  ->sourceInterfaceVersion($settings->datasetCreateInterfaceVersion)
                  ->send();
    
    if(!$datasetCreate->isSuccessful())
    {            
      return(FALSE);
    }

    // Create the permissions for the "administrators" group    
    $crudPermissions = new CRUDPermission(TRUE, TRUE, TRUE, TRUE);
    
    $authRegistrarAccess = new AuthRegistrarAccessQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
    
    $authRegistrarAccess->create($settings->adminGroup, $settings->testDataset, $crudPermissions, $settings->datasetWebservices)
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
  
  function createTwoDatasets()
  {
    $settings = new Config();     
    
    $datasetCreate = new DatasetCreateQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
    
    $datasetCreate->uri($settings->testDataset)
                  ->title("This is a testing dataset")
                  ->description("This is a testing dataset")
                  ->creator("http://test.com/user/bob/")
                  ->targetWebservices($settings->datasetWebservices)
                  ->mime('text/xml')
                  ->sourceInterface($settings->datasetCreateInterface)
                  ->sourceInterfaceVersion($settings->datasetCreateInterfaceVersion)
                  ->send();
                         
    if(!$datasetCreate->isSuccessful())    
    {
      return(FALSE);
    }

    // Create the permissions for the "administrators" group    
    $crudPermissions = new CRUDPermission(TRUE, TRUE, TRUE, TRUE);
    
    $authRegistrarAccess = new AuthRegistrarAccessQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
    
    $authRegistrarAccess->create($settings->adminGroup, $settings->testDataset, $crudPermissions, $settings->datasetWebservices)
                        ->mime('text/xml')
                        ->sourceInterface($settings->authRegistrarAccessInterface)
                        ->sourceInterfaceVersion($settings->authRegistrarAccessInterfaceVersion)
                        ->send();
                         
    if(!$authRegistrarAccess->isSuccessful())
    {
      return(FALSE);
    }  
                                 
    $datasetCreate = new DatasetCreateQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
    
    $datasetCreate->uri($settings->testDataset.'2/')
                  ->title("This is a testing dataset")
                  ->description("This is a testing dataset")
                  ->creator("http://test.com/user/bob/")
                  ->targetWebservices($settings->datasetWebservices)
                  ->mime('text/xml')
                  ->sourceInterface($settings->datasetCreateInterface)
                  ->sourceInterfaceVersion($settings->datasetCreateInterfaceVersion)
                  ->send();
                         
    if(!$datasetCreate->isSuccessful())    
    {
      return(FALSE);
    }
    
    // Create the permissions for the "administrators" group    
    $crudPermissions = new CRUDPermission(TRUE, TRUE, TRUE, TRUE);
    
    $authRegistrarAccess = new AuthRegistrarAccessQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
    
    $authRegistrarAccess->create($settings->adminGroup, $settings->testDataset.'2/', $crudPermissions, $settings->datasetWebservices)
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
  
  function deleteDataset()
  {
    $settings = new Config(); 

    $datasetDelete = new DatasetDeleteQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
    
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
    
    $datasetDelete = new DatasetDeleteQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
    
    $datasetDelete->uri($settings->testDataset)
                  ->mime('text/xml')
                  ->sourceInterface($settings->datasetDeleteInterface)
                  ->sourceInterfaceVersion($settings->datasetDeleteInterfaceVersion)
                  ->send();
    
    if(!$datasetDelete->isSuccessful())    
    {
      return(FALSE);
    }
    
    $datasetDelete = new DatasetDeleteQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
    
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
                                 
    $datasetRead = new DatasetReadQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
    
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
               
    $crudCreate = new CrudCreateQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
    
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
  
  function createSearchRecords()
  {
    $settings = new Config();  
    
    createDataset();
               
    $crudCreate = new CrudCreateQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
    
    $crudCreate->dataset($settings->testDataset)
               ->document(file_get_contents($settings->contentDir.'search_dataset.n3'))
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
                                 
    $authRegistrarAccess = new AuthRegistrarAccessQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
    
    $authRegistrarAccess->create('', $settings->testDataset, $crudPermissions, $settings->datasetWebservices)
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
  
  function createOntology($enableAdvanvedIndexation = TRUE)
  {
    $settings = new Config();     
        
    $crudPermissions = new CRUDPermission(TRUE, TRUE, TRUE, TRUE);         
    
    // Create the permissions for the "administrators" group    
    $crudPermissions = new CRUDPermission(TRUE, TRUE, TRUE, TRUE);
    
    $authRegistrarAccess = new AuthRegistrarAccessQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
    
    $authRegistrarAccess->create($settings->adminGroup, $settings->testOntologyUri, $crudPermissions, $settings->datasetWebservices)
                        ->mime('text/xml')
                        ->sourceInterface($settings->authRegistrarAccessInterface)
                        ->sourceInterfaceVersion($settings->authRegistrarAccessInterfaceVersion)
                        ->send();
                         
    if(!$authRegistrarAccess->isSuccessful())
    {
      return(FALSE);
    }      
                                 
    $ontologyCreate = new OntologyCreateQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
    
    if($enableAdvanvedIndexation)
    {
      $ontologyCreate->enableAdvancedIndexation();
    }
    else
    {
      $ontologyCreate->disableAdvancedIndexation();
    }
    
    $ontologyCreate->enableReasoner()
                   ->uri($settings->testOntologyUri)
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
    
    $ontologyDelete = new OntologyDeleteQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
    
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
    
    $crudDelete = new CrudDeleteQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
    
    $crudDelete->dataset("http://localhost/wsf/")
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
    
    $revisionLister = new RevisionListerQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
    
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
  
  function getInitialRevisionUri($uri)
  {
    $settings = new Config();
    
    $revisionLister = new RevisionListerQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
    
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
    
    return(key(array_slice($resultset['unspecified'], -1, 1, TRUE)));
  }
  
  function deleteGroup()
  {
    $settings = new Config();
    
    $authRegistrarGroup = new AuthRegistrarGroupQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
    
    $authRegistrarGroup->deleteGroup()
                       ->application($settings->applicationID)
                       ->group($settings->testGroup)
                       ->send();
                   
    if(!$authRegistrarGroup->isSuccessful())
    {
      return(FALSE);
    } 
    else
    {
      return(TRUE);
    }                       
  }  
  
  function createGroup()
  {
    $settings = new Config();
    
    $authRegistrarGroup = new AuthRegistrarGroupQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
    
    $authRegistrarGroup->createGroup()
                       ->application($settings->applicationID)
                       ->group($settings->testGroup)
                       ->sourceInterface($settings->authRegistrarGroupInterface)
                       ->sourceInterfaceVersion($settings->authRegistrarGroupInterfaceVersion)                     
                       ->send();   
                   
    if(!$authRegistrarGroup->isSuccessful())
    {
      return(FALSE);
    } 
    else
    {
      return(TRUE);
    }                       
  }
  
  function joinGroup()
  {
    $settings = new Config();
    
    $authRegistrarUser = new AuthRegistrarUserQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
    
    $authRegistrarUser->joinGroup()
                      ->group($settings->testGroup)
                      ->user($settings->testUser)
                      ->send();
                   
    if(!$authRegistrarUser->isSuccessful())
    {
      return(FALSE);
    } 
    else
    {
      return(TRUE);
    }                       
  }    

  function leaveGroup()
  {
    $settings = new Config();
    
    $authRegistrarUser = new AuthRegistrarUserQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
    
    $authRegistrarUser->leaveGroup()
                      ->group($settings->testGroup)
                      ->user($settings->testUser)
                      ->send();
               
    if(!$authRegistrarUser->isSuccessful())
    {
      return(FALSE);
    } 
    else
    {
      return(TRUE);
    }                       
  }    

  
?>
