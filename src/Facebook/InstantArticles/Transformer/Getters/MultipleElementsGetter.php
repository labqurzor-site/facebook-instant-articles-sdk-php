<?hh // strict
/**
 * Copyright (c) 2016-present, Facebook, Inc.
 * All rights reserved.
 *
 * This source code is licensed under the license found in the
 * LICENSE file in the root directory of this source tree.
 */
namespace Facebook\InstantArticles\Transformer\Getters;

use Facebook\InstantArticles\Validators\Type;
use Facebook\InstantArticles\Transformer\Transformer;
use Symfony\Component\CssSelector\CssSelectorConverter;

class MultipleElementsGetter extends AbstractGetter
{
    /**
     * @var Getters
     */
    protected vec<AbstractGetter> $children = vec[];

    public function createFrom(dict<string, mixed> $properties): this
    {
        $v = $properties['children'];
        invariant(is_array($v), "Not array");
        foreach ($v as $childName => $getter_configuration) {
            $this->children[] = GetterFactory::create(dict($getter_configuration));
        }

        return $this;
    }

    public function get(\DOMNode $node): mixed
    {
        $fragment = $node->ownerDocument->createDocumentFragment();
        foreach ($this->children as $child) {
            $cloned_node = $child->get($node);
            if ($cloned_node instanceof \DOMNode) {
                $fragment->appendChild(Transformer::cloneNode($cloned_node));
            }
        }
        if ($fragment->hasChildNodes()) {
            return $fragment;
        }
        return null;
    }
}
