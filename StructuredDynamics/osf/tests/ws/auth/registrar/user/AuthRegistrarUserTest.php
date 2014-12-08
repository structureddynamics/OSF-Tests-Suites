<?php

    namespace StructuredDynamics\osf\tests\ws\auth\registrar\user;
    
    use StructuredDynamics\osf\framework\WebServiceQuerier;
    use StructuredDynamics\osf\php\api\ws\auth\registrar\user\AuthRegistrarUserQuery;
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

    class AuthRegistrarUserTest extends \PHPUnit_Framework_TestCase {
      
        static private $outputs = array();
        
        public function testWrongEndpointUrl() {
          
          $settings = new Config();          
          
          $wsq = new WebServiceQuerier($settings->endpointUrl . "auth/registrar/user/" . "wrong", 
                                       "get", 
                                       "text/xml",
                                       "action=" . urlencode("create") .
                                       "&group_uri=" . urlencode($settings->testGroup) .
                                       "&user_uri=" . urlencode($settings->testUser),
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
          
          $wsq = new WebServiceQuerier($settings->endpointUrl . "auth/registrar/user/", 
                                       "post", 
                                       "text/xml",
                                       "action=" . urlencode("create") .
                                       "&group_uri=" . urlencode($settings->testGroup) .
                                       "&user_uri=" . urlencode($settings->testUser),
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
          
          $wsq = new WebServiceQuerier($settings->endpointUrl . "auth/registrar/user/", 
                                       "get", 
                                       "text/xml",
                                       "action=" . urlencode("unknown") .
                                       "&group_uri=" . urlencode($settings->testGroup) .
                                       "&user_uri=" . urlencode($settings->testUser),
                                       $settings->applicationID,
                                       $settings->apiKey,
                                       $settings->userID);

          $this->assertEquals($wsq->getStatus(), "400", "Debugging information: ".var_export($wsq, TRUE));                                       
          $this->assertEquals($wsq->getStatusMessage(), "Bad Request", "Debugging information: ".var_export($wsq, TRUE));
          $this->assertEquals($wsq->error->id, "WS-AUTH-REGISTRAR-USER-205", "Debugging information: ".var_export($wsq, TRUE));                                       
          
          unset($wsq);
          unset($settings);
        }          
        
        //
        // Test existing interface
        //
        
        public function testInterfaceExists() {
          $settings = new Config();  

          utilities\createGroup();
          utilities\leaveGroup();
          
          $authRegistrarUser = new AuthRegistrarUserQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
          
          $authRegistrarUser->joinGroup()
                            ->user($settings->testUser)
                            ->group($settings->testGroup)
                            ->sourceInterface($settings->authRegistrarUserInterface)
                            ->sourceInterfaceVersion($settings->authRegistrarUserInterfaceVersion)                     
                            ->send();          
          
                               
          $this->assertEquals($authRegistrarUser->getStatus(), "200", "Debugging information: ".var_export($authRegistrarUser, TRUE));                                       

          utilities\leaveGroup();
          utilities\deleteGroup();

          unset($authRegistrarUser);
          unset($settings);                 
        }  
        
        //
        // Test unexisting interface
        //
        
        public function testInterfaceNotExisting() {
          $settings = new Config();  

          utilities\createGroup();
          utilities\leaveGroup();
          
          $authRegistrarUser = new AuthRegistrarUserQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
          
          $authRegistrarUser->joinGroup()
                            ->user($settings->testUser)
                            ->group($settings->testGroup)
                            ->sourceInterface($settings->authRegistrarUserInterface.'unexisting')
                            ->sourceInterfaceVersion($settings->authRegistrarUserInterfaceVersion)                     
                            ->send();          
          
                               
          $this->assertEquals($authRegistrarUser->getStatus(), "400", "Debugging information: ".var_export($authRegistrarUser, TRUE));                                       
          $this->assertEquals($authRegistrarUser->getStatusMessage(), "Bad Request", "Debugging information: ".var_export($authRegistrarUser, TRUE));
          $this->assertEquals($authRegistrarUser->error->id, "WS-AUTH-REGISTRAR-USER-301", "Debugging information: ".var_export($authRegistrarUser, TRUE));                                       

          utilities\leaveGroup();
          utilities\deleteGroup();

          unset($authRegistrarUser);
          unset($settings);         
        }        
        
        public function testJoinGroup() {
          $settings = new Config();  

          utilities\createGroup();
          utilities\leaveGroup();
          
          $authRegistrarUser = new AuthRegistrarUserQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
          
          $authRegistrarUser->joinGroup()
                            ->user($settings->testUser)
                            ->group($settings->testGroup)
                            ->sourceInterface($settings->authRegistrarUserInterface)
                            ->sourceInterfaceVersion($settings->authRegistrarUserInterfaceVersion)                     
                            ->send();          
          
                               
          $this->assertEquals($authRegistrarUser->getStatus(), "200", "Debugging information: ".var_export($authRegistrarUser, TRUE));                                       

          utilities\leaveGroup();
          utilities\deleteGroup();

          unset($authRegistrarUser);
          unset($settings);                 
        }                
        
        public function testJoinGroup_MissingGroupURI() {
          $settings = new Config();  

          utilities\createGroup();
          utilities\leaveGroup();
          
          $authRegistrarUser = new AuthRegistrarUserQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
          
          $authRegistrarUser->joinGroup()
                            ->user($settings->testUser)
                            ->group("")
                            ->sourceInterface($settings->authRegistrarUserInterface)
                            ->sourceInterfaceVersion($settings->authRegistrarUserInterfaceVersion)                     
                            ->send();          
          
                               
          $this->assertEquals($authRegistrarUser->getStatus(), "400", "Debugging information: ".var_export($authRegistrarUser, TRUE));                                       
          $this->assertEquals($authRegistrarUser->getStatusMessage(), "Bad Request", "Debugging information: ".var_export($authRegistrarUser, TRUE));
          $this->assertEquals($authRegistrarUser->error->id, "WS-AUTH-REGISTRAR-USER-201", "Debugging information: ".var_export($authRegistrarUser, TRUE));                                       

          utilities\leaveGroup();
          utilities\deleteGroup();

          unset($authRegistrarUser);
          unset($settings);                 
        }    
        
       
        public function testJoinGroup_MissingUserURI() {
          $settings = new Config();  

          utilities\createGroup();
          utilities\leaveGroup();
          
          $authRegistrarUser = new AuthRegistrarUserQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
          
          $authRegistrarUser->joinGroup()
                            ->user("")
                            ->group($settings->testGroup)
                            ->sourceInterface($settings->authRegistrarUserInterface)
                            ->sourceInterfaceVersion($settings->authRegistrarUserInterfaceVersion)                     
                            ->send();          
          
                               
          $this->assertEquals($authRegistrarUser->getStatus(), "400", "Debugging information: ".var_export($authRegistrarUser, TRUE));                                       
          $this->assertEquals($authRegistrarUser->getStatusMessage(), "Bad Request", "Debugging information: ".var_export($authRegistrarUser, TRUE));
          $this->assertEquals($authRegistrarUser->error->id, "WS-AUTH-REGISTRAR-USER-200", "Debugging information: ".var_export($authRegistrarUser, TRUE));                                       

          utilities\leaveGroup();
          utilities\deleteGroup();

          unset($authRegistrarUser);
          unset($settings);                 
        }    
        
        public function testJoinGroup_UserAlreadyRegistered() {
          $settings = new Config();  

          utilities\createGroup();
          utilities\joinGroup();
          
          $authRegistrarUser = new AuthRegistrarUserQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
          
          $authRegistrarUser->joinGroup()
                            ->user($settings->testUser)
                            ->group($settings->testGroup)
                            ->sourceInterface($settings->authRegistrarUserInterface)
                            ->sourceInterfaceVersion($settings->authRegistrarUserInterfaceVersion)                     
                            ->send();                    
                               
          $this->assertEquals($authRegistrarUser->getStatus(), "400", "Debugging information: ".var_export($authRegistrarUser, TRUE));                                       
          $this->assertEquals($authRegistrarUser->getStatusMessage(), "Bad Request", "Debugging information: ".var_export($authRegistrarUser, TRUE));
          $this->assertEquals($authRegistrarUser->error->id, "WS-AUTH-REGISTRAR-USER-203", "Debugging information: ".var_export($authRegistrarUser, TRUE));                                       

          utilities\leaveGroup();
          utilities\deleteGroup();

          unset($authRegistrarUser);
          unset($settings);                 
        }   
        
        public function testLeaveGroup() {
          $settings = new Config();  

          utilities\createGroup();

          $authRegistrarUser = new AuthRegistrarUserQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
          
          $authRegistrarUser->joinGroup()
                            ->user($settings->testUser)
                            ->group($settings->testGroup)
                            ->sourceInterface($settings->authRegistrarUserInterface)
                            ->sourceInterfaceVersion($settings->authRegistrarUserInterfaceVersion)                     
                            ->send();           

          $this->assertEquals($authRegistrarUser->getStatus(), "200", "Debugging information: ".var_export($authRegistrarUser, TRUE));                                       
          
          $authRegistrarUser = new AuthRegistrarUserQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
          
          $authRegistrarUser->leaveGroup()
                            ->user($settings->testUser)
                            ->group($settings->testGroup)
                            ->sourceInterface($settings->authRegistrarUserInterface)
                            ->sourceInterfaceVersion($settings->authRegistrarUserInterfaceVersion)                     
                            ->send();          
                               
          $this->assertEquals($authRegistrarUser->getStatus(), "200", "Debugging information: ".var_export($authRegistrarUser, TRUE));                                       

          $authRegistrarUser->joinGroup()
                            ->user($settings->testUser)
                            ->group($settings->testGroup)
                            ->sourceInterface($settings->authRegistrarUserInterface)
                            ->sourceInterfaceVersion($settings->authRegistrarUserInterfaceVersion)                     
                            ->send();           

          $this->assertEquals($authRegistrarUser->getStatus(), "200", "Debugging information: ".var_export($authRegistrarUser, TRUE));                                       
          
          utilities\deleteGroup();

          unset($authRegistrarUser);
          unset($settings);                 
        }                               
         
        public function testLeaveGroup_MissingGroupURI() {
          $settings = new Config();  

          utilities\createGroup();
          utilities\joinGroup();
          
          $authRegistrarUser = new AuthRegistrarUserQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
          
          $authRegistrarUser->leaveGroup()
                            ->user($settings->testUser)
                            ->group("")
                            ->sourceInterface($settings->authRegistrarUserInterface)
                            ->sourceInterfaceVersion($settings->authRegistrarUserInterfaceVersion)                     
                            ->send();          
                               
          $this->assertEquals($authRegistrarUser->getStatus(), "400", "Debugging information: ".var_export($authRegistrarUser, TRUE));                                       
          $this->assertEquals($authRegistrarUser->getStatusMessage(), "Bad Request", "Debugging information: ".var_export($authRegistrarUser, TRUE));
          $this->assertEquals($authRegistrarUser->error->id, "WS-AUTH-REGISTRAR-USER-201", "Debugging information: ".var_export($authRegistrarUser, TRUE));                                       

          utilities\deleteGroup();

          unset($authRegistrarUser);
          unset($settings);                 
        }         
         
        public function testLeaveGroup_MissingUserURI() {
          $settings = new Config();  

          utilities\createGroup();
          utilities\joinGroup();
          
          $authRegistrarUser = new AuthRegistrarUserQuery($settings->endpointUrl, $settings->applicationID, $settings->apiKey, $settings->userID);
          
          $authRegistrarUser->leaveGroup()
                            ->user("")
                            ->group($settings->testGroup)
                            ->sourceInterface($settings->authRegistrarUserInterface)
                            ->sourceInterfaceVersion($settings->authRegistrarUserInterfaceVersion)                     
                            ->send();          
                               
          $this->assertEquals($authRegistrarUser->getStatus(), "400", "Debugging information: ".var_export($authRegistrarUser, TRUE));                                       
          $this->assertEquals($authRegistrarUser->getStatusMessage(), "Bad Request", "Debugging information: ".var_export($authRegistrarUser, TRUE));
          $this->assertEquals($authRegistrarUser->error->id, "WS-AUTH-REGISTRAR-USER-200", "Debugging information: ".var_export($authRegistrarUser, TRUE));                                       

          utilities\deleteGroup();

          unset($authRegistrarUser);
          unset($settings);                 
        }         
    }
 
?>