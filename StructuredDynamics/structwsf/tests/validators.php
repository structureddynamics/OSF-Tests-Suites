<?php
  
  namespace StructuredDynamics\structwsf\tests;
         
  function isValidXML($xml, &$errors = array())
  {
    libxml_use_internal_errors(true);
    
    $sxe = simplexml_load_string($xml);
    
    if(!$sxe) 
    {
      foreach(libxml_get_errors() as $error) 
      {
        array_push($errors, $error);
      }      
      
      return(FALSE);
    }
    else
    {
      return(TRUE);
    }
  }
  
  function isResultsetNonEmptyXML($xml)
  {
    libxml_use_internal_errors(true);
    
    $sxe = simplexml_load_string($xml);
    
    if(!$sxe) 
    {
      return(FALSE);
    }
    else
    {
      // Check if there are subjects into the resultset.
      $annotatedNeXML = new \SimpleXMLElement($xml);

      if(count($annotatedNeXML->xpath('//subject')) > 0)
      {
        return(TRUE);
      }
      else
      {
        return(FALSE);
      }
    }    
  }
  
  function isValidJSON($json, &$errors = array())
  {
    json_decode($json);

    switch (json_last_error()) {
        case JSON_ERROR_NONE:
            return(TRUE);
        break;
        case JSON_ERROR_DEPTH:
            array_push($errors, "Maximum stack depth exceeded");
        
            return(FALSE);
        break;
        case JSON_ERROR_STATE_MISMATCH:
            array_push($errors, "Underflow or the modes mismatch");
        
            return(FALSE);
        break;
        case JSON_ERROR_CTRL_CHAR:
            array_push($errors, "Unexpected control character found");
        
            return(FALSE);
        break;
        case JSON_ERROR_SYNTAX:
            array_push($errors, "Syntax error, malformed JSON");
        
            return(FALSE);
        break;
        case JSON_ERROR_UTF8:
            array_push($errors, "Malformed UTF-8 characters, possibly incorrectly encoded");
        
            return(FALSE);
        break;
        default:
            array_push($errors, "Unknown error");
        
            return(FALSE);
        break;
    }
  }
  
  function isResultsetNonEmptyJSON($json)
  {
    json_decode($json);

    if(json_last_error() == JSON_ERROR_NONE)
    {
      if(count(@json_decode($json)->{resultset}->subject) > 0)
      {
       return(TRUE);
      }
      else
      {
        return(FALSE);
      }
    }
    else
    {
      return(FALSE);
    }
  }  
  
  function isValidRDFXML($rdfxml, &$errors = array())
  { 
    $settings = new Config(); 
    
    include_once($settings->structwsfInstanceFolder."framework/arc2/ARC2.php");
    
    $parser = \ARC2::getRDFXMLParser();
    $parser->parse($settings->testDataset, $rdfxml);

    if(count($parser->getErrors()) > 0)
    {
      $errors = $parser->getErrors();
      
      return(FALSE);
    } 
    else
    {
      return(TRUE);
    }
  }
  
  function isValidRDFN3($rdfn3, &$errors = array())
  {    
    $settings = new Config(); 
    
    include_once($settings->structwsfInstanceFolder."framework/arc2/ARC2.php");
    
    $parser = \ARC2::getTurtleParser();
    $parser->parse($settings->testDataset, $rdfn3);

    if(count($parser->getErrors()) > 0)
    {
      $errors = $parser->getErrors();
      
      return(FALSE);
    } 
    else
    {
      return(TRUE);
    }
  } 
  
  function validateParameterTextXml(&$t, &$wsq)
  {
    $errors = array();  
    
    $t->assertEquals(isValidXML($wsq->getResultset(), $errors), TRUE, "[Test is valid XML] Debugging information: ".var_export($errors, TRUE));                                       
    $t->assertEquals(isValidXML($wsq->getResultset() . "this is invalid XML", $errors), FALSE, "[Test is invalid XML] Debugging information: ".var_export($errors, TRUE));                                       
    $t->assertEquals(isResultsetNonEmptyXML($wsq->getResultset()), TRUE, "[Test resultset non-empty] Debugging information: ".var_export($wsq->getResultset(), TRUE));                                       
  }
  
  function validateParameterApplicationJson(&$t, &$wsq)
  {
    $errors = array();  
    
    $t->assertEquals(isValidJSON($wsq->getResultset(), $errors), TRUE, "[Test is valid JSON] Debugging information: ".var_export($errors, TRUE));                                       
    $t->assertEquals(isValidJSON($wsq->getResultset() . "this is invalid JSON", $errors), FALSE, "[Test is invalid JSON] Debugging information: ".var_export($errors, TRUE));                                       
    $t->assertEquals(isResultsetNonEmptyJSON($wsq->getResultset()), TRUE, "[Test resultset non-empty] Debugging information: ".var_export($wsq->getResultset(), TRUE));                                           
  }  
  
  function validateParameterApplicationRdfXml(&$t, &$wsq)
  {
    $errors = array();  
    
    $t->assertEquals(isValidRDFXML($wsq->getResultset(), $errors), TRUE, "[Test is valid RDF+XML] Debugging information: ".var_export($errors, TRUE)." [Returned Resultset] ".$wsq->getResultset());                                       
    $t->assertEquals(isValidRDFXML($wsq->getResultset() . "this is invalid RDFXML", $errors), FALSE, "[Test is invalid RDF+XML] Debugging information: ".var_export($errors, TRUE)." [Returned Resultset] ".$wsq->getResultset());                                       
  }  
  
  function compareRdfXml($actual, $expected)
  {
    $settings = new Config(); 
    
    include_once($settings->structwsfInstanceFolder."framework/arc2/ARC2.php");
    
    $parserActual = \ARC2::getRDFParser();
    $parserActual->parse($settings->testDataset, $actual);

    if(count($parserActual->getErrors()) > 0)
    {
      return(FALSE);
    }                                                       
    
    $parserExpected = \ARC2::getRDFParser();
    $parserExpected->parse($settings->testDataset, $expected);

    if(count($parserExpected->getErrors()) > 0)
    {
      return(FALSE);
    } 
    
    $parserActual = $parserActual->getSimpleIndex(0);
    $parserExpected = $parserExpected->getSimpleIndex(0);    
    
    // Remove possible is-part-of that may be included by the endpoint
    unset($parserActual[key($parserActual)]['http://purl.org/dc/terms/isPartOf']);
    unset($parserExpected[key($parserExpected)]['http://purl.org/dc/terms/isPartOf']);
    
    if(count(rdfdiff($parserActual, $parserExpected)) > 0)
    {
      return(FALSE);
    }
    
    if(count(rdfdiff($parserExpected, $parserActual)) > 0)
    {
      return(FALSE);
    }    
    
    return(TRUE);
  }
  
  function compareStructJSON($actual, $expected)
  {
    $actual = json_decode($actual, TRUE);
    $expected = json_decode($expected, TRUE);

    if(count(arrayRecursiveDiff($actual, $expected)) > 0)
    {
      return(FALSE);
    }      
    
    return(TRUE);
  }
  
  function validateParameterApplicationRdfN3(&$t, &$wsq)
  { 
    $errors = array();  
    
    $t->assertEquals(isValidRDFN3($wsq->getResultset(), $errors), TRUE, "[Test is valid RDF+N3] Debugging information: ".var_export($errors, TRUE));                                       
    $t->assertEquals(isValidRDFN3($wsq->getResultset() . "this is invalid RDFN3", $errors), FALSE, "[Test is invalid RDF+N3] Debugging information: ".var_export($errors, TRUE));                                       
  }  
  
  /**
  * Source: http://web.archive.org/web/20100611171012/http://n2.talis.com/svn/playground/kwijibo/PHP/arc/plugins/trunk/ARC2_IndexUtilsPlugin.php
  */
  function rdfdiff()
  {
    $indices = func_get_args();
    $base = array_shift($indices);
    $diff = array();
    foreach($base as $base_uri => $base_ps){
      foreach($indices as $index){
        if(!isset($index[$base_uri])){
          $diff[$base_uri] = $base_ps;
        } else {
          foreach($base_ps as $base_p => $base_obs){
            if(!isset($index[$base_uri][$base_p])){
              $diff[$base_uri][$base_p] = $base_obs;
            } else {
              foreach($base_obs as $base_o){
                if(!in_array($base_o, $index[$base_uri][$base_p])){
                  $diff[$base_uri][$base_p][]=$base_o;
                }
              }
            }
          }
        }
      }
    }
    
    return $diff;    
  } 
  
  /**
  * Source: http://php.net/manual/en/function.array-diff.php
  */
  function arrayRecursiveDiff($aArray1, $aArray2) 
  {
    $aReturn = array();

    foreach ($aArray1 as $mKey => $mValue) {
      if (array_key_exists($mKey, $aArray2)) {
        if (is_array($mValue)) {
          $aRecursiveDiff = arrayRecursiveDiff($mValue, $aArray2[$mKey]);
          if (count($aRecursiveDiff)) { $aReturn[$mKey] = $aRecursiveDiff; }
        } else {
          if ($mValue != $aArray2[$mKey]) {
            $aReturn[$mKey] = $mValue;
          }
        }
      } else {
        $aReturn[$mKey] = $mValue;
      }
    }
    return $aReturn;
  }  
  
?>
