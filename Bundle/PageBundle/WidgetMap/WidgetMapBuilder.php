<?php
namespace Victoire\Bundle\PageBundle\WidgetMap;

use Victoire\Bundle\PageBundle\Entity\BasePage as Page;

/**
 * Page WidgetMap builder
 *
 * ref: page.widgetMap.builder
 */
class WidgetMapBuilder
{
    /**
     * Build page widget map builder
     * @param  Page   $page The page we want to build the widget map
     * @return array       The widget map as an array
     */
    public function build(Page $page)
    {
        $widgetMap = array();
        //Get all page/Template widgets
        foreach ($page->getWidgets() as $key => $widget) {
            if (!isset($widgetMap[$widget->getSlot()])) {
                $widgetMap[$widget->getSlot()] = array();
            }
            $widgetMap[$widget->getSlot()][] = $widget->getId();
        }

        //Then use old widgetMap to order them
        $oldWidgetMap = $page->getWidgetMap();

        $sortedWidgetMap = array();
        foreach ($oldWidgetMap as $slot => $widgets) {
            foreach ($widgets as $position => $widgetId) {
                if (isset($widgetMap[$slot]) && in_array($widgetId, $widgetMap[$slot])) {
                    if (!isset($sortedWidgetMap[$slot])) {
                        $sortedWidgetMap[$slot] = array();
                    }
                    $sortedWidgetMap[$slot][] = $widgetId;
                    unset($widgetMap[$slot][array_search($widgetId, $widgetMap[$slot])]);
                }
            }
        }
        foreach ($widgetMap as $slot => $widgets) {
            foreach ($widgets as $id) {
                if (!isset($sortedWidgetMap[$slot])) {
                    $sortedWidgetMap[$slot] = array();
                }
                $sortedWidgetMap[$slot][] = $id;
            }
        }

        if ($template = $page->getTemplate()) {
            $sortedWidgetMap = array_merge($sortedWidgetMap, $this->build($template));
        }

        return $sortedWidgetMap;
    }

    /**
     * Compute the complete widget map for a page by its parents
     *
     * @param Page   $page The page
     * @param string $slot The slot to get
     *
     * @return array The computed widgetMap
     */
    public function computeCompleteWidgetMap(Page $page, $slot)
    {
        $widgetMap = array();
        $parentWidgetMap = null;

        //get the parent widget map
        $parent = $page->getParent();

        if ($parent !== null) {
            $parentWidgetMap = $this->computeCompleteWidgetMap($parent, $slot);
        }

        $pageWidgetMap = $page->getWidgetMapForSlot($slot);

        if ($parentWidgetMap !== null) {

        }

        if ($pageWidgetMap !== null) {

        }

        //$parentWidgetMap


        return $widgetMap;
    }
}
