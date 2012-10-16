<?php

namespace Gphoto2;

class Gphoto2
{
    private $executable = null;
    
    public function getExecutable()
    {
        if ($this->executable === null) {
            $this->setExecutable(exec('which gphoto2'));
        }
        return $this->executable;
    }
    
    public function setExecutable($path)
    {
        if (is_readable($path))
          $this->executable = $path;
    }
    
    protected function _exec($params, $mode = 'exec')
    {
      $exec = "LANG=C ".$this->getExecutable();
      foreach ($params as $param => $value) {
        if (is_numeric($param))
          $exec .= ' '. escapeshellarg($value);
        elseif ($value === '' || $value === null)
          $exec .= ' '. escapeshellarg($param);
        else
          $exec .= ' '.  escapeshellarg($param). '="'. escapeshellarg($value). '"';
      }
      

      $exec .= ' 2>/dev/null';
      $output = $result = null;
      
      switch ($mode) {
          case 'shell_exec':
              $output = shell_exec($exec);
              $result = $output === NULL ? 1 : 0;
              break;
          case 'exec':
          default:
          exec($exec, $output, $result); break;
          

      }
      
      return $result === 0 ? $output : false;
    }
    
    
    /**
     * List supported camera models
     * @return array
     */
    public function listCameras()
    {
        $result = $this->_exec(array('--list-cameras', '--quiet'));
        array_shift($result);
        return $result;
    }
  
    /**
     * List supported port devices
     * @return array
     */
    public function listPorts()
    {
        $rawResult = $this->_exec(array('--list-ports', '--quiet'));
        array_shift($rawResult);
        
        $result = array();
        foreach($rawResult as $r) {
          list($port, $descr) = explode(" ", $r, 2);
          $result[] = array(
            "port" => $port,
            "description" => trim($descr),
          );
        }
        return $result;
    }
  
    /**
     * List auto-detected cameras
     * @return array
     */
    public function autoDetect()
    {
        $rawResult = $this->_exec(array('--auto-detect', '--quiet'));
        array_shift($rawResult);
        array_shift($rawResult);

        $result = array();
        foreach($rawResult as $r) {
          $pos = strrpos($r, " ");
          if ($pos !== FALSE) {
            $result[] = array(
              "port" => substr($r, $pos + 1),
              "model" => substr($r, 0, $pos),
            );
          }
        }
        return $result;
    }

    /**
     * Display camera/driver abilities
     * @return array
     */
    public function abilities()
    {
        $rawResult = $this->_exec(array('--abilities', '--quiet'));
        array_shift($rawResult);
        $result = array();
        $prevAbility = null;
        foreach($rawResult as $r) {
          list($ability, $value) = explode(":", $r, 2);
          $ability = trim($ability);
          if ($ability && $value) {
            $result[] = array(
              "ability" => $ability,
              "value" => $value,
            );
          }
          elseif ($ability && !$value) {
            $result[] = array(
              "ability" => $ability,
              "value" => array(),
            );
            $prevAbility = count($result) - 1;
          }
          else {
            $result[$prevAbility]["value"][] = $value;
          }
        }
        return $result;
    }

  
    /**
     * List configuration tree
     * @return array
     */
    public function listConfig()
    {
        $rawResult = $this->_exec(array('--list-config', '--quiet'));
        if ($rawResult !== false)
          array_shift($rawResult);
        return $rawResult;
    }

    /**
     * Dump full configuration tree
     * @return array
     */
    public function listAllConfig()
    {
        $rawResult = $this->_exec(array('--list-all-config', '--quiet'));
        
        if (!is_array($rawResult))
            return false;
            
        array_shift($rawResult);

#        var_dump($rawResult);
        array_shift($rawResult);
        
        $result = array();
        $config = null;
        while ($r = array_shift($rawResult)) {
            if (strpos($r, '/') === 0) {
              if ($config) {
                $result[$r] = $config;
              }
              $config = array("Name" => $r,);
              continue;
            }

            if (strpos($r, ':') === false) {
//              var_dump($r);
              continue;
            }
            list($key, $value) = explode(":", $r, 2);
            $key = trim($key);
            $value = trim($value);

            if (in_array($key, array('Label', 'Type', 'Current', 'Bottom', 'Top', 'Step', 'Choice'))) {
              if ($key === 'Choice') {
                  list($choiceNum, $choiceText) = explode(" ", $value, 2);
                  $config[$key][$choiceNum] = $choiceText;
              }
              else {
                  $config[$key] = $value;
              }
            }
            else {
//              var_dump($r);
            }
        }
  
        return $result;
    }

    public function setConfig($config, $value)
    {
        $configs = $this->listAllConfig();
        if (array_key_exists($config, $configs)) {
            $opts = array('--quiet');
            $optsp[] = sprintf('--set-config=%s=%s', $config, $value);
            $rawResult = $this->_exec($opts);
            return $rawResult !== false && empty($rawResult);
        }
        else {
            return false;
        }
    }
    
    public function setConfigIndex($config, $index = null)
    {
        $configs = $this->listAllConfig();
        if (array_key_exists($config, $configs)) {
            $opts = array('--quiet');
            $optsp[] = sprintf('--set-config-index=%s=%s', $config, $index);
            $rawResult = $this->_exec($opts);
            return $rawResult !== false && empty($rawResult);
        }
        else {
            return false;
        }
    }
    
    public function setConfigValue($config, $value = null)
    {
        $configs = $this->listAllConfig();
        if (array_key_exists($config, $configs)) {
            $opts = array('--quiet');
            $optsp[] = sprintf('--set-config-value=%s=%s', $config, $value);
            $rawResult = $this->_exec($opts);
            return $rawResult !== false && empty($rawResult);
        }
        else {
            return false;
        }
    }
    
    public function captureImage()
    {
        
    }
    
    public function captureImageAndDownload()
    {
        $rawResult = $this->_exec(array('--capture-image-and-download', '--quiet', '--stdout'), 'shell_exec');
        
        if ($rawResult)
            return $rawResult;
        else
            return false;
    }
    
    

}