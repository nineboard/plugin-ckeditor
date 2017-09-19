<?php
namespace Xpressengine\Plugins\CkEditor\Components\EditorTools\XeiconTool;

use App\Facades\XeFrontend;
use Illuminate\Contracts\Auth\Access\Gate;
use Xpressengine\Editor\AbstractTool;
use Xpressengine\Permission\Instance;
use Route;
use XePresenter;
use Xpressengine\Plugins\CkEditor\Plugin;
use Xpressengine\Http\Request;

class XeiconTool extends AbstractTool
{
    public static function boot()
    {
        static::route();
    }


    public static function route()
    {
        Route::fixed(
            Plugin::getId(),
            function () {
                Route::get(
                    'xeicon_tool/popup/create',
                    [
                        'as' => 'ckeditor::xeicon_tool.popup',
                        'uses' => function (Request $request) {

                            $title = 'XEIcon Tool';

                            // set browser title
                            XeFrontend::title($title);

                            XeFrontend::css([
                                asset(Plugin::asset('/components/EditorTools/XeiconTool/assets/style.css')),
                                asset('/assets/vendor/XEIcon/xeicon.min.css')
                            ])->load();

                            //header, footer 제거
                            \XeTheme::selectBlankTheme();

                            // output
                            return XePresenter::make('ckeditor::components.EditorTools.XeiconTool.views.popup');

                        }
                    ]
                );

                Route::get(
                    '/popup/edit',
                    [
                        'as' => 'ckeditor::xeicon_tool.popup-edit',
                        'uses' => function (Request $request) {

                            $title = 'XEIcon Tool';

                            // set browser title
                            XeFrontend::title($title);

                            // load css file
                            XeFrontend::css(Plugin::asset('/components/EditorTools/XeiconTool/assets/style.css'))->load();

                            //header, footer 제거
                            \XeTheme::selectBlankTheme();

                            // output
                            return XePresenter::make('ckeditor::components.EditorTools.XeiconTool.views.popup-edit');

                        }
                    ]
                );
            }
        );
    }

    /**
     * Initialize assets for the tool
     *
     * @return void
     */
    public function initAssets()
    {
        XeFrontend::html('ckeditor.xeicon_tool.load_url')->content("
        <script>
            (function() {
            
                var _url = {
                    popup: '".route('ckeditor::xeicon_tool.popup')."',      
                    edit_popup: '".route('ckeditor::xeicon_tool.popup-edit')."',
                    css: [
                        '".asset('/assets/vendor/XEIcon/xeicon.min.css')."'                    
                    ]
                };
            
                var URL = {
                    get: function (type) {
                        return _url[type];                 
                    }
                };
                
                window.xeiconToolURL = URL;
            })();
        </script>
        ")->load();

        XeFrontend::js([
            asset($this->getAssetsPath() . '/xeicon.js')
        ])->load();
        
        XeFrontend::css([
            asset($this->getAssetsPath() . '/style.css'),
            asset('/assets/vendor/XEIcon/xeicon.min.css')
        ])->load();
    }

    /**
     * Get the tool's symbol
     *
     * @return array ['normal' => '...', 'large' => '...']
     */
    public function getIcon()
    {
        return asset($this->getAssetsPath() . '/icon.png');
    }

    /**
     * Compile the raw content to be useful
     *
     * @param string $content content
     * @return string
     */
    public function compile($content)
    {
        return $content;
    }

    private function getAssetsPath()
    {
        return str_replace(base_path(), '', realpath(__DIR__ . '/assets'));
    }
}