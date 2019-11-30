<?php

/**
 * @name Yii Require JS
 * 
 * Require JS Yii Extension. 
 * 
 * This extension setups up require js for use in your Yii project.
 * Once set up you use a widget in a view or layout to setup require and then
 * you can use require js as you normally would starting at an entry script
 * (usually main.js) where you can load further modules and dependencies etc.
 *
 * @author Richard Walker <richie@mediasuite.co.nz>
 * @version 0.1
 * @license http://www.opensource.org/licenses/bsd-license.php
 * 
 * How to Use:
 * 
 * 1. Place the yii-require-js folder in your extensions directory
 * 2. Use the widget in any view that you want to use require js
 * 
 * eg:
 * <?php $this->widget('ext.yii-require-js.YiiRequireJsWidget'); ?>
 * 
 * 3. Create main.js in /protected/components/js/ and start coding js
 * 
 * Config:
 * 
 * If you would prefer a different js starting point instead of main.js define
 * this in your main.php file like so: 
 * 
 * eg:
 * 'params' => array(
 *      'yii-require-js'=>array(
 *          'initialModule' => 'myModule'
 *      ),
 * 
 * If you would prefer to load your scripts from another directory other than
 * /protected/components/js you can define this in your main.php config like so:
 * 
 * eg:
 * 'params' => array(
 *      'yii-require-js'=>array(
 *         'scriptsDirectory' => 'application.components.js.requireModules'
 *      ), ...
 * 
 */

class YiiRequireJsWidget extends CWidget {

  private $_extensionAssetsUrl = null;
  private $_scriptsAssetsUrl = null;
  private $_initialModule = null;
  
  /**
   * Publishes everything in the assets directory of the extension
   * @return String - the url of the published resources
   */
  private function getExtensionAssetsDirectoryUrl()
  {
    if( is_null($this->_extensionAssetsUrl) )
    {
      $this->_extensionAssetsUrl = Yii::app()->getAssetManager()->publish(
        Yii::getPathOfAlias('ext.yii-require-js.assets') 
      );
    }
    return $this->_extensionAssetsUrl;
  }
    
  private function getScriptsDirectoryUrl()
  {
    if (  is_null($this->_scriptsAssetsUrl) )
    {
      $alias = 'application.components.js';
      if( isset(Yii::app()->params['yii-require-js']['scriptsDirectory']) )
      {
        $alias = Yii::app()->params['yii-require-js']['scriptsDirectory'];
      }
      $this->_scriptsAssetsUrl = Yii::app()->getAssetManager()->publish(
        Yii::getPathOfAlias($alias) 
      );
    }
    return $this->_scriptsAssetsUrl;
  }
  
  
  private function getInitialModule()
  {
    if( is_null($this->_initialModule) )
    {
      $this->_initialModule = 'main';
      if( isset(Yii::app()->params['yii-require-js']['initialModule']) )
      {
        $this->_initialModule = Yii::app()->params['yii-require-js']['initialModule'];
      }
    }
    return $this->_initialModule;
  }
  
  public function run() {

    $extensionAssetsDirectory = $this->getExtensionAssetsDirectoryUrl();
    $scriptsDirectory = $this->getScriptsDirectoryUrl();
    $initialModule = $this->getInitialModule();
    
    //load require script
    Yii::app()->clientScript->registerScriptFile (
      $extensionAssetsDirectory . '/require.js',
      CClientScript::POS_HEAD
    );
    //add init code for require js
    Yii::app()->clientScript->registerScript(
      'requireInit',
      'require.config({
        baseUrl: "'.$scriptsDirectory.'",
        waitSeconds: 15
      });
      require(["'.$initialModule.'"],function(){});',
      CClientScript::POS_END
    );

    
  }

}