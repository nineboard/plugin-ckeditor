<?php
/**
 * CkEditorDefault
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

namespace Xpressengine\Plugins\CkEditor\FieldSkin;

use View;
use Xpressengine\Config\ConfigEntity;
use Xpressengine\DynamicField\AbstractSkin;

/**
 * CkEditorDefault
 *
 * @category    CkEditor
 *
 * @author      XE Developers <developers@xpressengine.com>
 * @copyright   2019 Copyright XEHub Corp. <https://www.xehub.io>
 * @license     http://www.gnu.org/licenses/lgpl-3.0-standalone.html LGPL
 *
 * @link        https://xpressengine.io
 */
class CkEditorDefault extends AbstractSkin
{
    protected static $id = 'fieldType/ckeditor@CkEditor/fieldSkin/ckeditor@default';

    protected $name = 'defult';

    protected $description = 'CkEditor 기본 스킨';

    /**
     * 다이나믹필스 생성할 때 스킨 설정에 적용될 rule 정의
     *
     * @return void
     */
    public function setSettingsRules()
    {
        // TODO: Implement setSettingsRules() method.
    }

    public function settings(?ConfigEntity $config = null)
    {
        return View::make('ckeditor::views/FieldType/CkEditor/Default/settings', ['config' => $config])->render();
    }

    public function create(array $inputs)
    {
        $config = $this->config;

        return View::make('ckeditor::views/FieldType/CkEditor/Default/create', [
            'config' => $config,
        ])->render();
    }

    public function edit(array $args)
    {
        $config = $this->config;

        return View::make('ckeditor::views/FieldType/CkEditor/Default/edit', [
            'config' => $config,
            'content' => $args[$config->get('id').'Contents'],
            'args' => $args,
        ])->render();
    }

    public function show(array $args)
    {
        $config = $this->config;

        return View::make('ckeditor::views/FieldType/CkEditor/Default/show', [
            'config' => $config,
            'content' => $args[$config->get('id').'Contents'],
            'args' => $args,
        ])->render();
    }

    public function search(array $inputs)
    {
        $config = $this->config;
        if ($config->get('searchable') !== true) {
            return '';
        }

        return View::make('ckeditor::views/FieldType/CkEditor/Default/search', [
            'config' => $config,
        ])->render();
    }

    /**
     * @param  string  $name
     */
    public function output($name, array $args)
    {
        $key = $name.'ItemId';
        if (isset($args[$key]) === false || $args[$key] == '') {
            return null;
        }

        return Category::getItem($args[$key])->word;
    }

    public static function getSettingsURI()
    {
    }
}
