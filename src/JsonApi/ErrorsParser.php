<?php

namespace Swis\JsonApi\Client\JsonApi;

use Art4\JsonApiClient\Error as JsonApiError;
use Art4\JsonApiClient\ErrorCollection as JsonApiErrorCollection;
use Art4\JsonApiClient\ErrorLink as JsonApiErrorLink;
use Art4\JsonApiClient\ErrorSource as JsonApiErrorSource;
use Art4\JsonApiClient\Meta as JsonApiMeta;
use Swis\JsonApi\Client\Error;
use Swis\JsonApi\Client\ErrorCollection;
use Swis\JsonApi\Client\ErrorSource;
use Swis\JsonApi\Client\Links;
use Swis\JsonApi\Client\Meta;

class ErrorsParser
{
    /**
     * @var \Swis\JsonApi\Client\JsonApi\LinksParser
     */
    private $linksParser;

    /**
     * @param \Swis\JsonApi\Client\JsonApi\LinksParser $linksParser
     */
    public function __construct(LinksParser $linksParser)
    {
        $this->linksParser = $linksParser;
    }

    /**
     * @param \Art4\JsonApiClient\ErrorCollection $errorCollection
     *
     * @return \Swis\JsonApi\Client\ErrorCollection
     */
    public function parse(JsonApiErrorCollection $errorCollection)
    {
        $errors = new ErrorCollection();

        foreach ($errorCollection->asArray(false) as $error) {
            $errors->push($this->buildError($error));
        }

        return $errors;
    }

    /**
     * @param \Art4\JsonApiClient\Error $error
     *
     * @return \Swis\JsonApi\Client\Error
     */
    private function buildError(JsonApiError $error): Error
    {
        return new Error(
            $error->has('id') ? $error->get('id') : null,
            $error->has('links') ? $this->buildLinks($error->get('links')) : null,
            $error->has('status') ? $error->get('status') : null,
            $error->has('code') ? $error->get('code') : null,
            $error->has('title') ? $error->get('title') : null,
            $error->has('detail') ? $error->get('detail') : null,
            $error->has('source') ? $this->buildErrorSource($error->get('source')) : null,
            $error->has('meta') ? $this->buildMeta($error->get('meta')) : null
        );
    }

    /**
     * @param \Art4\JsonApiClient\ErrorLink $errorLink
     *
     * @return \Swis\JsonApi\Client\Links
     */
    private function buildLinks(JsonApiErrorLink $errorLink): Links
    {
        return $this->linksParser->parse($errorLink->asArray(false));
    }

    /**
     * @param \Art4\JsonApiClient\ErrorSource $errorSource
     *
     * @return \Swis\JsonApi\Client\ErrorSource
     */
    private function buildErrorSource(JsonApiErrorSource $errorSource): ErrorSource
    {
        return new ErrorSource(
            $errorSource->has('pointer') ? $errorSource->get('pointer') : null,
            $errorSource->has('parameter') ? $errorSource->get('parameter') : null
        );
    }

    /**
     * @param \Art4\JsonApiClient\Meta $meta
     *
     * @return \Swis\JsonApi\Client\Meta
     */
    private function buildMeta(JsonApiMeta $meta): Meta
    {
        return new Meta($meta->asArray(true));
    }
}
