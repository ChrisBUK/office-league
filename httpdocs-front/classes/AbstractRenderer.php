<?php
  
  class AbstractRenderer 
  {
      
      public function render($objData, $strTemplate = null)
      {
          $strTemplateFull = $_SERVER['DOCUMENT_ROOT'] . '/templates/' . $strTemplate . '.html.php';
          
          if (!file_exists($strTemplateFull))
          {
              throw new Exception("Template not found: ".$strTemplate);
          }
          
          ob_start();          
          include($strTemplateFull);
          return ob_get_clean();
          
      }
      
  }
?>
