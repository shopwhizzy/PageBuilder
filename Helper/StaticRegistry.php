<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Helper;

use Goomento\Core\Traits\TraitStaticCaller;
use Goomento\Core\Traits\TraitStaticInstances;
use Goomento\Core\Model\Registry;

/**
 * Class Registry
 * @package Goomento\PageBuilder
 * @method static registry($key);
 * @method static register($key, $value, $graceful = false);
 */
class StaticRegistry
{
    use TraitStaticCaller;
    use TraitStaticInstances;

    /**
     * @inheritDoc
     */
    private static function getStaticInstance()
    {
        return self::getInstance(Registry::class);
    }
}
