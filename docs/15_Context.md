# Context

The Toolbox context allows you to use different configuration namespaces.
For example, if you have multiple pimcore sites, it's possible to use different elements and configuration.
You're also able to merge different context configuration with the main one.

## Real Life Example
You have one main toolbox configuration. Now you need to implement a second page for your client, with a different layout and areas.
Just create a toolbox context (see below) and you're able to provide dedicated elements and a specific configuration for each area element.
If your client opens the document in the backend in a specific site tree for example, he will only see those defined elements in the toolbar.

## Context Configuration

| Name | Type | Description
|------|------|------------|
| `merge_with_root` | bool | Use the main toolbox configuration as base configuration and reconfigure them in the new context. **Note**: If you disable this note, you need to provide every single configuration by your own. |
| `enabled_areas` | array | Enable specific Areas |
| `disabled_areas` | array | Disable specific Areas. **Note:** If you have configured `enabled_areas` this option will be ignored. |

> **Note**: If you have enabled `merge_with_root` you cannot use a different theme in sub context since there would be a layout mismatch.

### Merge Hierarchy
Merge from root means:
1. get all config from toolbox core bundle
2. merge with all `toolbox` configs defined in app
3. merge with all `toolbox.context.context_name.xy` configs defined in app

## Configuration Example

```yml

toolbox:
    context:
        portal: # context identifier
            settings:
                merge_with_root: true
                enabled_areas:
                    - 'headline'
                    - 'accordion'
            areas:
                headline:
                    config_elements:
                        headline_type:
                            type: select
                            title: 'New Type'
                            config:
                                name: headlineType
                                store:
                                    h1: Headline 1
                                    h2: Headline 2
                                default: h2
                        anchor_name: ~

        app: # context identifier
            settings:
                merge_with_root: true
                disabled_areas:
                    - 'accordion'
                    - 'columns'
```

## Context Resolver
You need a Context Resolver to inform Toolbox which context is the current active one.
If the resolver returns `null` the main configuration will be used.

### Add Context Resolver to the Toolbox configuration.

> **Note:** A Context Resolver is a global setting and cannot be added to a sub context.

```yml
toolbox:
    context_resolver: 'AppBundle\Services\ToolboxBundle\ContextResolver'
```

### Create Context Resolver Class

```php
<?php

namespace AppBundle\Services\ToolboxBundle;

use Pimcore\Model\Document;
use Symfony\Component\HttpFoundation\RequestStack;
use ToolboxBundle\Resolver\ContextResolverInterface;
use Pimcore\Http\Request\Resolver\DocumentResolver;
use Pimcore\Http\Request\Resolver\EditmodeResolver;
use Pimcore\Http\Request\Resolver\SiteResolver;
use Pimcore\Model\Site;
use Pimcore\Tool;

class ToolboxContextResolver implements ContextResolverInterface
{
    /**
     * @var SiteResolver
     */
    protected $siteResolver;

    /**
     * @var RequestStack
     */
    protected $requestStack;

    /**
     * @var EditmodeResolver
     */
    private $editModeResolver;

    /**
     * @var DocumentResolver
     */
    private $documentResolver;

    /**
     * ToolboxContextResolver constructor.
     *
     * @param RequestStack     $requestStack
     * @param SiteResolver     $siteResolver
     * @param EditmodeResolver $editModeResolver
     * @param DocumentResolver $documentResolver
     */
    public function __construct(
        RequestStack $requestStack,
        SiteResolver $siteResolver,
        EditmodeResolver $editModeResolver,
        DocumentResolver $documentResolver
    ) {
        $this->requestStack = $requestStack;
        $this->siteResolver = $siteResolver;
        $this->editModeResolver = $editModeResolver;
        $this->documentResolver = $documentResolver;
    }

    public function getCurrentContextIdentifier()
    {
        $request = $this->requestStack->getMasterRequest();

        $site = null;

        //if request is coming from a ckeditor configuration request (/admin/*.js)
        if ($request->query->has('tb_document_request_id')) {
            try {
                $currentDocument = Document::getById($request->query->get('tb_document_request_id'));
            } catch (\Exception $e) {
                $currentDocument = null;
            }

            if ($currentDocument instanceof Document) {
                $site = Tool\Frontend::getSiteForDocument($currentDocument);
            }
        } else {
            //if request is not in editmode we can determinate site by site resolver
            if (!$this->editModeResolver->isEditmode($request)) {
                if ($this->siteResolver->isSiteRequest($request)) {
                    $site = $this->siteResolver->getSite($request);
                }
                // in backend we don't have any site request, we need to fetch it via document
            } else {
                $currentDocument = $this->documentResolver->getDocument();
                $site = Tool\Frontend::getSiteForDocument($currentDocument);
            }
        }

        if (!$site instanceof Site) {
            return null;
        }

        if ($site->getRootId() === 1) {
            return 'portal';
        } elseif ($site->getRootId() === 2) {
            return 'app';
        }

        return null;
    }
}
```
