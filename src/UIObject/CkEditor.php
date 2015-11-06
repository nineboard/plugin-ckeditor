<?php
/**
 * CkEditor module class
 *
 * PHP version 5
 *
 * @category    CkEditor
 * @package     CkEditor
 * @author      XE Team (akasima) <osh@xpressengine.com>
 * @copyright   2014 Copyright (C) NAVER <http://www.navercorp.com>
 * @license     http://www.gnu.org/licenses/lgpl-3.0-standalone.html LGPL
 * @link        http://www.xpressengine.com
 */
namespace Xpressengine\Plugins\CkEditor\UIObject;

use Frontend;
use Xpressengine\UIObject\AbstractUIObject;
use Route;
use Xpressengine\Plugin\PluginRegister;

/**
 * CkEditor class
 *
 * @category    CkEditor
 * @package     CkEditor
 * @author      XE Team (akasima) <osh@xpressengine.com>
 * @copyright   2014 Copyright (C) NAVER <http://www.navercorp.com>
 * @license     http://www.gnu.org/licenses/lgpl-3.0-standalone.html LGPL
 * @link        http://www.xpressengine.com
 */
class CkEditor extends AbstractUIObject
{
    protected static $loaded = false;

    protected static $plugins = [];

    const FILE_UPLOAD_PATH = 'attached/ckeditor';
    const THUMBNAIL_TYPE = 'spill';

    protected function getDefaultSetting()
    {
        return [
            'contentDomName' => 'content',
            'contentDomId' => 'xeContentEditor',
            'contentDomOptions' => [
                'class' => 'form-control',
                'rows' => '20',
                'cols' => '80'
            ],
            'onlyJavaScript' => false,
        ];
    }

    public static function boot()
    {
        self::registerFixedRoute();

        // TODO: Implement boot() method.
    }

    /**
     * Register fixed route for slug
     *
     * @return void
     */
    protected static function registerFixedRoute()
    {
        Route::fixed('ckEditor', function () {
            $c = 'FixedController';
            Route::post('/file/upload', ['as' => 'fixed.ckEditor.file.upload', 'uses' => $c.'@fileUpload']);
            Route::get('/file/source/{id}', ['as' => 'fixed.ckEditor.file.source', 'uses' => $c.'@fileSource']);
            Route::get('/file/download/{id}', ['as' => 'fixed.ckEditor.file.download', 'uses' => $c.'@fileDownload']);
            Route::get('/hashTag/{id?}', ['as' => 'fixed.ckEditor.hashTag', 'uses' => $c.'@hashTag']);
            Route::get('/mention/{id?}', ['as' => 'fixed.ckEditor.mention', 'uses' => $c.'@mention']);
        }, ['namespace' => 'Xpressengine\Plugins\CkEditor']);
    }

    /**
     * getEditorConfig
     *
     * @return array
     */
    protected function getEditorConfig()
    {
        $args = $this->arguments;
        $editorConfig = array_merge($this->getDefaultSetting(), $args);
        return $editorConfig;
    }

    public function render()
    {
        /** @var \Xpressengine\Plugin\PluginRegister $register */
        $register = app('xe.pluginRegister');
        self::$plugins = $register->get(self::getId() . PluginRegister::KEY_DELIMITER . 'plugin');

        $editorConfig = $this->getEditorConfig();

        $this->initAssets();

        $htmlString = [];

        if(!$editorConfig['onlyJavaScript']){

            $content = $editorConfig['content'];

            $htmlString[] = $this->getContentHtml($content, $editorConfig);
            $htmlString[] = $this->getEditorScript($editorConfig);
        }

        $this->template = implode('', $htmlString);

        $this->template = $this->renderPlugins($this->template);

        return parent::render();
    }

    protected function renderPlugins($content)
    {
        /** @var CkEditorPluginInterface $plugin */
        foreach (self::$plugins as $plugin) {
            $content = $plugin::render($content);
        }

        return $content;
    }

    protected function getContentHtml($content, $editorConfig)
    {
        $contentDomHtmlOptionString = $this->getContentDomHtmlOption($editorConfig);

        $contentHtml = [];
        $contentHtml[] = "<textarea name='{$editorConfig['contentDomName']}' id='{$editorConfig['contentDomId']}' {$contentDomHtmlOptionString}>";
        $contentHtml[] = $content;
        $contentHtml[] = '</textarea>';

        return implode('', $contentHtml);
    }

    protected function getEditorScript($editorConfig)
    {
        $editorScript = [];
        $editorScript[] = "
        <script>
            $(function() {
                xe3CkEditor('{$editorConfig['contentDomId']}', ".json_encode($editorConfig['editorConfig']).");
            });
        </script>";

        return implode('', $editorScript);
    }

    protected function getPluginsScript()
    {
        $htmlString = [];
        /** @var CkEditorPluginInterface $plugin */
        foreach (self::$plugins as $plugin) {
            $htmlString[] = $plugin::render();
        }
    }

    public static function getManageUri()
    {
        // TODO: Implement getManageUri() method.
    }

    /**
     * initAssets
     *
     * @param $editorConfig
     *
     * @return void
     */
    protected function initAssets()
    {
        if (self::$loaded === false) {
            self::$loaded = true;



            $path = '/plugins/ckeditor/assets/ckeditor';
            Frontend::js([
                asset(str_replace(base_path(), '', $path . '/ckeditor.js')),
                asset(str_replace(base_path(), '', $path . '/styles.js')),
                asset(str_replace(base_path(), '', $path . '/xe3.js')),
            ])->load();

            Frontend::css([
                asset(str_replace(base_path(), '', $path . '/xe3.css')),
            ])->load();

            // ckeditor load 후 plugin 을 불러옴
            $this->initAssetPlugins();

        }

    }

    protected function initAssetPlugins()
    {
        /** @var CkEditorPluginInterface $plugin */
        foreach (self::$plugins as $plugin) {
            $plugin::initAssets();
        }
    }

    /**
     * getContentDomHtmlOption
     *
     * @param $editorConfig
     *
     * @return string
     */
    protected function getContentDomHtmlOption($editorConfig)
    {
        $optionsString = '';
        foreach($editorConfig['contentDomOptions'] as $key => $val)
        {
            $optionsString.= "$key='{$val}' ";
        }

        return $optionsString;
    }
}
