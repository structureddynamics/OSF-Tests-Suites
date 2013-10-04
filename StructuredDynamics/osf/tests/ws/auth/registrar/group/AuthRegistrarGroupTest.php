<?php

    namespace StructuredDynamics\osf\tests\ws\auth\registrar\group;
    
    use StructuredDynamics\osf\framework\WebServiceQuerier;
    use StructuredDynamics\osf\php\api\ws\auth\registrar\group\AuthRegistrarGroupQuery;
    use StructuredDynamics\osf\php\api\framework\CRUDPermission;
    use StructuredDynamics\osf\tests\Config;
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

    class AuthRegistrarGroupTest extends \PHPUnit_Framework_TestCase {
      
        static private $outputs = array();
        
        public function testWrongEndpointUrl() {
          
          $settings = new Config();          
          
          $wsq = new WebServiceQuerier($settings->endpointUrl . "auth/registrar/group/" . "wrong", 
                                       "get", 
                                       "text/xml",
                                       "action=" . urlencode("create") .
                                       "&group_uri=" . urlencode($settings->testGroup) .
                                       "&app_id=" . urlencode($settings->applicationID),
                                       $settings->applicationID,
                                       $settings->apiKey,
                                       $settings->userID);
                                       
          $this->assertEquals($wsq->getStatus(), "404", "Debugging information: ".var_export($wsq, TRUE));                                       
          $this->assertEquals($wsq->getStatusMessage(), "Not Found", "Debugging information: ".var_export($wsq, TRUE));
          
          unset($wsq);
          unset($settings);
        }
        
        public function testWrongEndpointMethodPost() {
          
          $settings = new Config();  
          
          $wsq = new WebServiceQuerier($settings->endpointUrl . "auth/registrar/group/", 
                                       "post", 
                                       "text/xml",
                                       "action=" . urlencode("create") .
                                       "&group_uri=" . urlencode($settings->testGroup) .
                                       "&app_id=" . urlencode($settings->applicationID),
                                       $settings->applicationID,
                                       $settings->apiKey,
                                       $settings->userID);

          $this->assertEquals($wsq->getStatus(), "405", "Debugging information: ".var_export($wsq, TRUE));                                       
          $this->assertEquals($wsq->getStatusMessage(), "Method Not Allowed", "Debugging information: ".var_export($wsq, TRUE));          
          
          unset($wsq);
          unset($settings);
        }   
        
        
        public function testUnknownAction() {
          
          $settings = new Config();  
          
          $wsq = new WebServiceQuerier($settings->endpointUrl . "auth/registrar/group/", 
                                       "get", 
                                       "text/xml",
                                       "action=" . urlencode("unknown") .
                                       "&group_uri=" . urlencode($settings->testGroup) .
                                       "&app_id=" . urlencode($settings->applicationID),
                                       $settings->applicationID,
                                       $settings->apiKey,
                                       $settings->userID);

          $this->assertEquals($wsq->getStatus(), "400", "Debugging information: ".var_export($wsq, TRUE));                                       
          $this->assertEquals($wsq->getStatusMessage(), "Bad Request", "Debugging information: ".var_export($wsq, TRUE));
          $this->assertEquals($wsq->error->id, "WS-AUTH-REGISTRAR-GROUP-204", "Debugging information: ".var_export($wsq, TRUE));                                       
          
          unset($wsq);
          unset($settings);
        }          
        
        //
        // Test existing interface
        //
        
        public function testInterfaceExists() {
          $settings = new Config();  

          utilities\deleteGroup();
          
          $authRegistrarGroup = new AuthRegistrarGroupQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
          
          $authRegistrarGroup->createGroup()
                             ->application($settings->applicationID)
                             ->group($settings->testGroup)
                             ->sourceInterface($settings->authRegistrarGroupInterface)
                             ->sourceInterfaceVersion($settings->authRegistrarGroupInterfaceVersion)                     
                             ->send();          
          
                               
          $this->assertEquals($authRegistrarGroup->getStatus(), "200", "Debugging information: ".var_export($authRegistrarGroup, TRUE));                                       

          utilities\deleteGroup();

          unset($authRegistrarGroup);
          unset($settings);                 
        }  
        
        //
        // Test unexisting interface
        //
        
        public function testInterfaceNotExisting() {
          $settings = new Config();  

          utilities\deleteGroup();
          
          $authRegistrarGroup = new AuthRegistrarGroupQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
          
          $authRegistrarGroup->createGroup()
                             ->application($settings->applicationID)
                             ->group($settings->testGroup)
                             ->sourceInterface($settings->authRegistrarGroupInterface.'unexisting')
                             ->sourceInterfaceVersion($settings->authRegistrarGroupInterfaceVersion)                     
                             ->send();          
          
                               
          $this->assertEquals($authRegistrarGroup->getStatus(), "400", "Debugging information: ".var_export($authRegistrarGroup, TRUE));                                       
          $this->assertEquals($authRegistrarGroup->getStatusMessage(), "Bad Request", "Debugging information: ".var_export($authRegistrarGroup, TRUE));
          $this->assertEquals($authRegistrarGroup->error->id, "WS-AUTH-REGISTRAR-GROUP-301", "Debugging information: ".var_export($authRegistrarGroup, TRUE));                                       

          utilities\deleteGroup();

          unset($authRegistrarGroup);
          unset($settings);          
        }         
        
        public function testCreateGroup() {
          $settings = new Config();  

          utilities\deleteGroup();
          
          $authRegistrarGroup = new AuthRegistrarGroupQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
          
          $authRegistrarGroup->createGroup()
                             ->application($settings->applicationID)
                             ->group($settings->testGroup)
                             ->sourceInterface($settings->authRegistrarGroupInterface)
                             ->sourceInterfaceVersion($settings->authRegistrarGroupInterfaceVersion)                     
                             ->send();                    
                               
          $this->assertEquals($authRegistrarGroup->getStatus(), "200", "Debugging information: ".var_export($authRegistrarGroup, TRUE));                                       

          utilities\deleteGroup();

          unset($authRegistrarGroup);
          unset($settings);                 
        }     
                    
        public function testCreateGroup_NoGroupParameter() {
          $settings = new Config();  

          utilities\deleteGroup();
          
          $authRegistrarGroup = new AuthRegistrarGroupQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
          
          $authRegistrarGroup->createGroup()
                             ->application($settings->applicationID)
                             ->group("")
                             ->sourceInterface($settings->authRegistrarGroupInterface)
                             ->sourceInterfaceVersion($settings->authRegistrarGroupInterfaceVersion)                     
                             ->send();                    
                               
          $this->assertEquals($authRegistrarGroup->getStatus(), "400", "Debugging information: ".var_export($authRegistrarGroup, TRUE));                                       
          $this->assertEquals($authRegistrarGroup->getStatusMessage(), "Bad Request", "Debugging information: ".var_export($authRegistrarGroup, TRUE));
          $this->assertEquals($authRegistrarGroup->error->id, "WS-AUTH-REGISTRAR-GROUP-200", "Debugging information: ".var_export($authRegistrarGroup, TRUE));                                       

          utilities\deleteGroup();

          unset($authRegistrarGroup);
          unset($settings);                 
        }  
                            
        public function testCreateGroup_NoApplicationIDParameter() {
          $settings = new Config();  

          utilities\deleteGroup();
          
          $authRegistrarGroup = new AuthRegistrarGroupQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
          
          $authRegistrarGroup->createGroup()
                             ->application("")
                             ->group($settings->testGroup)
                             ->sourceInterface($settings->authRegistrarGroupInterface)
                             ->sourceInterfaceVersion($settings->authRegistrarGroupInterfaceVersion)                     
                             ->send();                    
                               
          $this->assertEquals($authRegistrarGroup->getStatus(), "400", "Debugging information: ".var_export($authRegistrarGroup, TRUE));                                       
          $this->assertEquals($authRegistrarGroup->getStatusMessage(), "Bad Request", "Debugging information: ".var_export($authRegistrarGroup, TRUE));
          $this->assertEquals($authRegistrarGroup->error->id, "WS-AUTH-REGISTRAR-GROUP-201", "Debugging information: ".var_export($authRegistrarGroup, TRUE));                                       

          utilities\deleteGroup();

          unset($authRegistrarGroup);
          unset($settings);                 
        }    
        
        public function testCreateGroup_GroupExisting() {
          $settings = new Config();  

          utilities\deleteGroup();
          
          $authRegistrarGroup = new AuthRegistrarGroupQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
          
          $authRegistrarGroup->createGroup()
                             ->application($settings->applicationID)
                             ->group($settings->testGroup)
                             ->sourceInterface($settings->authRegistrarGroupInterface)
                             ->sourceInterfaceVersion($settings->authRegistrarGroupInterfaceVersion)                     
                             ->send();                    
                               
          $this->assertEquals($authRegistrarGroup->getStatus(), "200", "Debugging information: ".var_export($authRegistrarGroup, TRUE));                                       

          $authRegistrarGroup = new AuthRegistrarGroupQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
          
          $authRegistrarGroup->createGroup()
                             ->application($settings->applicationID)
                             ->group($settings->testGroup)
                             ->sourceInterface($settings->authRegistrarGroupInterface)
                             ->sourceInterfaceVersion($settings->authRegistrarGroupInterfaceVersion)                     
                             ->send();                    
                               
          $this->assertEquals($authRegistrarGroup->getStatus(), "400", "Debugging information: ".var_export($authRegistrarGroup, TRUE));                                       
          $this->assertEquals($authRegistrarGroup->getStatusMessage(), "Bad Request", "Debugging information: ".var_export($authRegistrarGroup, TRUE));
          $this->assertEquals($authRegistrarGroup->error->id, "WS-AUTH-REGISTRAR-GROUP-203", "Debugging information: ".var_export($authRegistrarGroup, TRUE));                                       
          
          
          utilities\deleteGroup();

          unset($authRegistrarGroup);
          unset($settings);                 
        }   
        
        public function testDeleteGroup() {
          $settings = new Config();  

          $authRegistrarGroup = new AuthRegistrarGroupQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
          
          $authRegistrarGroup->createGroup()
                             ->application($settings->applicationID)
                             ->group($settings->testGroup)
                             ->sourceInterface($settings->authRegistrarGroupInterface)
                             ->sourceInterfaceVersion($settings->authRegistrarGroupInterfaceVersion)                     
                             ->send();                    
                               
          $this->assertEquals($authRegistrarGroup->getStatus(), "200", "Debugging information: ".var_export($authRegistrarGroup, TRUE));                                       

          $authRegistrarGroup = new AuthRegistrarGroupQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);

          
          $authRegistrarGroup = new AuthRegistrarGroupQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
          
          $authRegistrarGroup->deleteGroup()
                             ->application($settings->applicationID)
                             ->group($settings->testGroup)
                             ->sourceInterface($settings->authRegistrarGroupInterface)
                             ->sourceInterfaceVersion($settings->authRegistrarGroupInterfaceVersion)                     
                             ->send();                    
                               
          $this->assertEquals($authRegistrarGroup->getStatus(), "200", "Debugging information: ".var_export($authRegistrarGroup, TRUE));                                       

          $authRegistrarGroup = new AuthRegistrarGroupQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
          
          $authRegistrarGroup->createGroup()
                             ->application($settings->applicationID)
                             ->group($settings->testGroup)
                             ->sourceInterface($settings->authRegistrarGroupInterface)
                             ->sourceInterfaceVersion($settings->authRegistrarGroupInterfaceVersion)                     
                             ->send();                    
                               
          $this->assertEquals($authRegistrarGroup->getStatus(), "200", "Debugging information: ".var_export($authRegistrarGroup, TRUE));                                       

          $authRegistrarGroup = new AuthRegistrarGroupQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
             
          
          unset($authRegistrarGroup);
          unset($settings);                 
        }     
                    
        public function testDeleteGroup_NoGroupParameter() {
          $settings = new Config();  

          utilities\createGroup();
          
          $authRegistrarGroup = new AuthRegistrarGroupQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
          
          $authRegistrarGroup->deleteGroup()
                             ->application($settings->applicationID)
                             ->group("")
                             ->sourceInterface($settings->authRegistrarGroupInterface)
                             ->sourceInterfaceVersion($settings->authRegistrarGroupInterfaceVersion)                     
                             ->send();                    
                               
          $this->assertEquals($authRegistrarGroup->getStatus(), "400", "Debugging information: ".var_export($authRegistrarGroup, TRUE));                                       
          $this->assertEquals($authRegistrarGroup->getStatusMessage(), "Bad Request", "Debugging information: ".var_export($authRegistrarGroup, TRUE));
          $this->assertEquals($authRegistrarGroup->error->id, "WS-AUTH-REGISTRAR-GROUP-200", "Debugging information: ".var_export($authRegistrarGroup, TRUE));                                       

          unset($authRegistrarGroup);
          unset($settings);                 
        }  
                            
        public function testDeleteGroup_NoApplicationIDParameter() {
          $settings = new Config();  

          utilities\createGroup();
          
          $authRegistrarGroup = new AuthRegistrarGroupQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
          
          $authRegistrarGroup->deleteGroup()
                             ->application("")
                             ->group($settings->testGroup)
                             ->sourceInterface($settings->authRegistrarGroupInterface)
                             ->sourceInterfaceVersion($settings->authRegistrarGroupInterfaceVersion)                     
                             ->send();                    
                               
          $this->assertEquals($authRegistrarGroup->getStatus(), "400", "Debugging information: ".var_export($authRegistrarGroup, TRUE));                                       
          $this->assertEquals($authRegistrarGroup->getStatusMessage(), "Bad Request", "Debugging information: ".var_export($authRegistrarGroup, TRUE));
          $this->assertEquals($authRegistrarGroup->error->id, "WS-AUTH-REGISTRAR-GROUP-201", "Debugging information: ".var_export($authRegistrarGroup, TRUE));                                       

          unset($authRegistrarGroup);
          unset($settings);                 
        }  
        
        public function testDeleteGroup_NoGroupToDelete() {
          $settings = new Config();  

          utilities\deleteGroup();
          
          $authRegistrarGroup = new AuthRegistrarGroupQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
          
          $authRegistrarGroup->deleteGroup()
                             ->application("")
                             ->group($settings->testGroup)
                             ->sourceInterface($settings->authRegistrarGroupInterface)
                             ->sourceInterfaceVersion($settings->authRegistrarGroupInterfaceVersion)                     
                             ->send();                    
                               
          $this->assertEquals($authRegistrarGroup->getStatus(), "400", "Debugging information: ".var_export($authRegistrarGroup, TRUE));                                       
          $this->assertEquals($authRegistrarGroup->getStatusMessage(), "Bad Request", "Debugging information: ".var_export($authRegistrarGroup, TRUE));
          $this->assertEquals($authRegistrarGroup->error->id, "WS-AUTH-REGISTRAR-GROUP-201", "Debugging information: ".var_export($authRegistrarGroup, TRUE));                                       

          unset($authRegistrarGroup);
          unset($settings);                 
        }                               
             
    }
 
?>