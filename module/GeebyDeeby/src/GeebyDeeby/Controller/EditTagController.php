<?php
/**
 * Edit tag controller
 *
 * PHP version 5
 *
 * Copyright (C) Demian Katz 2012.
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License version 2,
 * as published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301  USA
 *
 * @category GeebyDeeby
 * @package  Controller
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://github.com/demiankatz/Geeby-Deeby Main Site
 */
namespace GeebyDeeby\Controller;

/**
 * Edit tag controller
 *
 * @category GeebyDeeby
 * @package  Controller
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://github.com/demiankatz/Geeby-Deeby Main Site
 */
class EditTagController extends AbstractBase
{
    /**
     * Display a list of tags
     *
     * @return mixed
     */
    public function listAction()
    {
        $view = $this->getGenericList(
            'tag', 'tags', 'geeby-deeby/edit-tag/render-tags'
        );
        // If this is not an AJAX request, we also want to display types:
        if (!$this->getRequest()->isXmlHttpRequest()) {
            $view->tagTypes = $this->typelistAction()->tagTypes;
        }
        return $view;
    }

    /**
     * Save attributes for the current tag.
     *
     * @param int   $tagId   Tag ID
     * @param array $attribs Attribute values
     *
     * @return void
     */
    protected function saveAttributes($tagId, $attribs)
    {
        $table = $this->getDbTable('tagsattributesvalues');
        // Delete old values:
        $table->delete(['Tag_ID' => $tagId]);
        // Save new values:
        foreach ($attribs as $id => $val) {
            if (!empty($val)) {
                $table->insert(
                    [
                        'Tag_ID' => $tagId,
                        'Tags_Attribute_ID' => $id,
                        'Tags_Attribute_Value' => $val
                    ]
                );
            }
        }
    }

    /**
     * Operate on a single tag
     *
     * @return mixed
     */
    public function indexAction()
    {
        $assignMap = [
            'tag' => 'Tag',
            'type_id' => 'Tag_Type_ID'
        ];
        $view = $this->handleGenericItem('tag', $assignMap, 'tag');
        $view->tagTypes = $this->typelistAction()->tagTypes;

        // Get tag ID
        $tagId = $view->tag['Tag_ID'] ?? $view->affectedRow->Tag_ID ?? null;

        // Special handling for saving attributes:
        if ($this->getRequest()->isPost()
            && ($attribs = $this->params()->fromPost('attribs'))
        ) {
            $this->saveAttributes($tagId, $attribs);
        }

        // Add attribute details if we have a Tag_ID.
        if ($tagId) {
            $view->attributes = $this->getDbTable('tagsattribute')->getList();
            $attributeValues = [];
            $values = $this->getDbTable('tagsattributesvalues')
                ->getAttributesForTag($tagId);
            foreach ($values as $current) {
                $attributeValues[$current->Tags_Attribute_ID]
                    = $current->Tags_Attribute_Value;
            }
            $view->attributeValues = $attributeValues;
        }

        // Add extra fields/controls if outside of a lightbox:
        if (!$this->getRequest()->isXmlHttpRequest()) {
            $view->uris = $this->getDbTable('tagsuris')
                ->getURIsForTag($view->tagObj->Tag_ID);
            $view->setTemplate('geeby-deeby/edit-tag/edit-full');
            $view->items = $this->getDbTable('itemstags')
                ->getItemsForTag($view->tagObj->Tag_ID);
            $view->predicates = $this->getDbTable('predicate')->getList();
            $view->relationships = $this->getDbTable('tagsrelationship')
                ->getOptionList();
            $view->relationshipsValues = $this->getDbTable('tagsrelationshipsvalues')
                ->getRelationshipsForTag($tagId);
        }
        return $view;
    }

    /**
     * Display a list of types
     *
     * @return mixed
     */
    public function typelistAction()
    {
        return $this->getGenericList(
            'tagType', 'tagTypes', 'geeby-deeby/edit-tag/render-types'
        );
    }

    /**
     * Deal with items
     *
     * @return mixed
     */
    public function itemAction()
    {
        return $this->handleGenericLink(
            'itemstags', 'Tag_ID', 'Item_ID', 'items', 'getItemsForTag',
            'geeby-deeby/edit-tag/item-list.phtml'
        );
    }

    /**
     * Deal with arbitrary relationships.
     *
     * @return mixed
     */
    public function relationshipAction()
    {
        // The relationship ID may have a leading 'i' indicating an inverse
        // relationship; if we find this, we should handle it here to keep
        // the standard behavior consistent.
        $rid = $this->params()->fromRoute('relationship_id');
        if (substr($rid, 0, 1) === 'i') {
            $linkFrom = 'Object_Tag_ID';
            $linkTo = 'Subject_Tag_ID';
            $rid = substr($rid, 1);
        } else {
            $linkFrom = 'Subject_Tag_ID';
            $linkTo = 'Object_Tag_ID';
        }
        $extras = ['Tags_Relationship_ID' => $rid];
        return $this->handleGenericLink(
            'tagsrelationshipsvalues', $linkFrom, $linkTo,
            'relationshipsValues', 'getRelationshipsForTag',
            'geeby-deeby/edit-tag/relationship-list.phtml',
            $extras
        );
    }

    /**
     * Operate on a single type
     *
     * @return mixed
     */
    public function typeAction()
    {
        $assignMap = ['tagType' => 'Tag_Type'];
        return $this->handleGenericItem('tagType', $assignMap, 'tagType');
    }

    /**
     * Deal with URIs
     *
     * @return mixed
     */
    public function uriAction()
    {
        $extras = ($pid = $this->params()->fromPost('predicate_id'))
            ? ['Predicate_ID' => $pid] : [];
        return $this->handleGenericLink(
            'tagsuris', 'Tag_ID', 'URI',
            'uris', 'getURIsForTag',
            'geeby-deeby/edit-tag/uri-list.phtml',
            $extras
        );
    }
}
