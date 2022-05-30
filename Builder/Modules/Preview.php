<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Builder\Modules;

use Goomento\PageBuilder\Configuration;
use Goomento\PageBuilder\Builder\Managers\Documents;
use Goomento\PageBuilder\Helper\HooksHelper;
use Goomento\PageBuilder\Helper\DataHelper;
use Goomento\PageBuilder\Helper\ObjectManagerHelper;
use Goomento\PageBuilder\Helper\RegistryHelper;
use Goomento\PageBuilder\Helper\StateHelper;
use Goomento\PageBuilder\Helper\ThemeHelper;

class Preview
{
    /**
     * @var int
     */
    private $contentId;

    /**
     * Init the preview
     */
    public function init()
    {
        if (StateHelper::isAdminhtml()) {
            return;
        }

        $model = RegistryHelper::registry('current_preview_content');
        $this->contentId = $model ? $model->getId() : 0;

        HooksHelper::addAction('pagebuilder/frontend/enqueue_scripts', [$this, 'enqueueScripts']);
        HooksHelper::addAction('pagebuilder/frontend/enqueue_scripts', [$this, 'enqueueStyles']);
        HooksHelper::addFilter('pagebuilder/content/html', [ $this,'builderWrapper' ], 2022);

        HooksHelper::addAction('pagebuilder/frontend/footer', [ $this, 'footer']);

        /**
         * Do action `pagebuilder/preview/init`
         */
        HooksHelper::doAction('pagebuilder/preview/init', $this);
    }

    /**
     * @return int
     */
    public function getContentId()
    {
        return $this->contentId;
    }

    /**
     * Builder wrapper.
     *
     * Used to add an empty HTML wrapper for the builder, the javascript will add
     * the content later.
     *
     * @param string $content The content of the builder.
     *
     * @return string HTML wrapper for the builder.
     */
    public function builderWrapper($content)
    {
        if ($this->getContentId()) {
            /** @var Documents $documentManager */
            $documentManager = ObjectManagerHelper::get(Documents::class);
            $document = $documentManager->get($this->getContentId());

            $attributes = $document->getContainerAttributes();

            $attributes['id'] = 'gmt';

            $attributes['class'] .= ' gmt-edit-mode';

            $content = '<div ' . DataHelper::renderHtmlAttributes($attributes) . '></div>';
        }

        return $content;
    }

    /**
     * Enqueue Styles
     */
    public function enqueueStyles()
    {
        ObjectManagerHelper::getFrontend()
            ->enqueueStyles();

        ObjectManagerHelper::getWidgetsManager()
            ->enqueueWidgetsStyles();

        $suffix = Configuration::debug() ? '' : '.min';

        $direction_suffix = DataHelper::isRtl() ? '-rtl' : '';

        ThemeHelper::registerStyle(
            'editor-preview',
            'Goomento_PageBuilder/build/editor-preview' . $direction_suffix . $suffix . '.css',
            [
                'jquery-select2',
                'inline-editor'
            ],
            Configuration::version()
        );

        ThemeHelper::registerStyle(
            'inline-editor',
            'Goomento_PageBuilder/lib/sofish/pen.css'
        );

        ThemeHelper::enqueueStyle('editor-preview');

        /**
         * Preview enqueue styles.
         *
         * Fires after SagoTheme preview styles are enqueued.
         *
         */
        HooksHelper::doAction('pagebuilder/preview/enqueue_styles');
    }

    /**
     *
     */
    public function enqueueScripts()
    {
        ObjectManagerHelper::getFrontend()
            ->registerScripts();

        ObjectManagerHelper::getWidgetsManager()
            ->enqueueWidgetsScripts();

        // For inline editor
        ThemeHelper::enqueueScript('sofish-pen');

        /**
         * Preview enqueue scripts.
         *
         * Fires after SagoTheme preview scripts are enqueued.
         *
         */
        HooksHelper::doAction('pagebuilder/preview/enqueue_scripts');
    }

    /**
     * Footer
     */
    public function footer()
    {
        $frontend = ObjectManagerHelper::getFrontend();
        $frontend->footer();
        $frontend->printFontsLinks();
    }

    /**
     * Preview constructor.
     */
    public function __construct()
    {
        HooksHelper::addAction('pagebuilder/content/preview', [ $this, 'init' ], 0);
    }
}