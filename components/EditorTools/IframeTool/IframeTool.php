<?php
/**
 * IframeTool
 *
 * PHP version 7
 *
 * @category    CkEditor
 *
 * @author      XE Developers <developers@xpressengine.com>
 * @copyright   2019 Copyright XEHub Corp. <https://www.xehub.io>
 * @license     http://www.gnu.org/licenses/lgpl-3.0-standalone.html LGPL
 *
 * @link        https://xpressengine.io
 */

namespace Xpressengine\Plugins\CkEditor\Components\EditorTools\IframeTool;

use Route;
use Symfony\Component\DomCrawler\Crawler;
use XeConfig;
use XeFrontend;
use Xpressengine\Editor\AbstractTool;
use Xpressengine\Plugins\CkEditor\Plugin;

/**
 * IframeTool
 *
 * @category    CkEditor
 *
 * @author      XE Developers <developers@xpressengine.com>
 * @copyright   2019 Copyright XEHub Corp. <https://www.xehub.io>
 * @license     http://www.gnu.org/licenses/lgpl-3.0-standalone.html LGPL
 *
 * @link        https://xpressengine.io
 */
class IframeTool extends AbstractTool
{
    public static function boot()
    {
        static::route();
    }

    protected static function route()
    {
        // implement code

        Route::fixed(Plugin::getId(), function () {
            Route::get('iframe_tool/popup/create', [
                'as' => 'ckeditor::iframe_tool.popup',
                'uses' => 'ComponentController@popup',
            ]);
        }, ['namespace' => 'Xpressengine\\Plugins\\CkEditor\\Components\\EditorTools\\IframeTool']);

        Route::settings(Plugin::getId(), function () {
            Route::get('setting', [
                'as' => 'xe.plugin.ckeditor.iframe_tool.settings.get',
                'uses' => 'SettingController@getSetting',
            ]);
            Route::post('setting', [
                'as' => 'xe.plugin.ckeditor.iframe_tool.settings.post',
                'uses' => 'SettingController@postSetting',
            ]);

        }, ['namespace' => 'Xpressengine\\Plugins\\CkEditor\\Components\\EditorTools\\IframeTool']);

    }

    public static function getInstanceSettingURI($instanceId)
    {
        return route('xe.plugin.ckeditor.iframe_tool.settings.get', $instanceId);
    }

    /**
     * Initialize assets for the tool
     *
     * @return void
     */
    public function initAssets()
    {
        $wls = function () {
            return implode(',', array_map(function ($item) {
                return "'".$item."'";
            }, $this->getWhiteList()));
        };
        XeFrontend::html('ckeditor.iframe_tool.load_url')->content("
        <script>
            (function() {

                var _url = {
                    popup: '".route('ckeditor::iframe_tool.popup')."',
                    whiteList: [".call_user_func($wls).']
                };

                var URL = {
                    get: function (type) {
                        return _url[type];
                    }
                };

                window.iframeToolURL = URL;
            })();
        </script>
        ')->load();

        XeFrontend::js([
            asset($this->getAssetsPath().'/iframe.js'),
        ])->load();

        XeFrontend::css([
        ])->load();
    }

    /**
     * Get the tool's symbol
     *
     * @return array ['normal' => '...', 'large' => '...']
     */
    public function getIcon()
    {
        return asset($this->getAssetsPath().'/icon.png');
    }

    /**
     * Compile the raw content to be useful
     *
     * @param  string  $content  content
     * @return string
     */
    public function compile($content)
    {
        $crawler = new Crawler($content);
        $data = $crawler->filter('*[xe-tool-data]')->eq(0)->attr('xe-tool-data');
        $data = str_replace("'", '"', $data);
        $data = json_decode($data, true);
        $source = array_get($data, 'src');
        $host = parse_url($source, PHP_URL_HOST);

        if (! $source || ! in_array($host, $this->getWhiteList())) {
            return '';
        }

        XeFrontend::css([
            asset($this->getAssetsPath().'/iframe.css'),
        ])->load();

        $attr = [];
        $result = [];
        $embedVideo = false;
        $attr[] = 'src="'.$data['src'].'"';

        if (array_get($data, 'width')) {
            $attr[] = 'width="'.$data['width'].'"';
        }

        if (array_get($data, 'height')) {
            $attr[] = 'height="'.$data['height'].'"';
        }

        if (array_get($data, 'scrolling')) {
            $attr[] = 'scrolling="'.$data['scrolling'].'"';
        }

        if (in_array($host, ['youtube.com', 'www.youtube.com', 'youtu.be'])) {
            $embedVideo = true;
        }

        if ($embedVideo) {
            $result[] = '<div class="xe-embed xe-embed-video">';
        }

        $result[] = '<iframe '.implode(' ', $attr).'></iframe>';

        if ($embedVideo) {
            $result[] = '</div>';
        }

        return implode($result);
    }

    private function getAssetsPath()
    {
        return str_replace(base_path(), '', plugins_path().'/ckeditor/components/EditorTools/IframeTool/assets');
    }

    private function getWhiteList()
    {
        $config = XeConfig::get(static::getId());

        return $config ? $config->get('whitelist') : [];
    }
}
