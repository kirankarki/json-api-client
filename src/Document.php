<?php

namespace Swis\JsonApi\Client;

use Swis\JsonApi\Client\Errors\ErrorCollection;
use Swis\JsonApi\Client\Interfaces\DataInterface;
use Swis\JsonApi\Client\Interfaces\DocumentInterface;
use Swis\JsonApi\Client\Traits\HasMeta;

class Document implements DocumentInterface
{
    use HasMeta;

    /**
     * @var \Swis\JsonApi\Client\Interfaces\DataInterface
     */
    protected $data;

    /**
     * @var array
     */
    protected $links = [];

    /**
     * @var \Swis\JsonApi\Client\Errors\ErrorCollection
     */
    protected $errors;

    /**
     * @var \Swis\JsonApi\Client\Collection
     */
    protected $included;

    /**
     * @var array
     */
    protected $jsonapi = [];

    public function __construct()
    {
        $this->errors = new ErrorCollection();
        $this->included = new Collection();
    }

    /**
     * @return array
     */
    public function getLinks(): array
    {
        return $this->links;
    }

    /**
     * @param array $links
     */
    public function setLinks(array $links)
    {
        $this->links = $links;
    }

    /**
     * @return \Swis\JsonApi\Client\Errors\ErrorCollection
     */
    public function getErrors(): ErrorCollection
    {
        return $this->errors;
    }

    /**
     * @param \Swis\JsonApi\Client\Errors\ErrorCollection $errors
     */
    public function setErrors(ErrorCollection $errors)
    {
        $this->errors = $errors;
    }

    /**
     * @return bool
     */
    public function hasErrors(): bool
    {
        return !$this->errors->isEmpty();
    }

    /**
     * @return bool
     */
    public function isSuccess(): bool
    {
        return $this->errors->isEmpty();
    }

    /**
     * @return \Swis\JsonApi\Client\Collection
     */
    public function getIncluded(): Collection
    {
        return $this->included;
    }

    /**
     * @param \Swis\JsonApi\Client\Collection $included
     *
     * @return static
     */
    public function setIncluded(Collection $included)
    {
        $this->included = $included;

        return $this;
    }

    /**
     * @return array
     */
    public function getJsonapi(): array
    {
        return $this->jsonapi;
    }

    /**
     * @param array $jsonapi
     */
    public function setJsonapi(array $jsonapi)
    {
        $this->jsonapi = $jsonapi;
    }

    /**
     * @return \Swis\JsonApi\Client\Interfaces\DataInterface
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @param \Swis\JsonApi\Client\Interfaces\DataInterface $data
     *
     * @return static
     */
    public function setData(DataInterface $data)
    {
        $this->data = $data;

        return $this;
    }

    /**
     * Specify data which should be serialized to JSON.
     *
     * @see  http://php.net/manual/en/jsonserializable.jsonserialize.php
     *
     * @return mixed data which can be serialized by <b>json_encode</b>,
     *               which is a value of any type other than a resource
     *
     * @since 5.4.0
     */
    public function jsonSerialize()
    {
        return $this->toArray();
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        $document = [];

        if (!empty($this->getLinks())) {
            $document['links'] = $this->links;
        }

        if (!empty($this->getData())) {
            $document['data'] = $this->data->toJsonApiArray();
        }

        if ($this->getIncluded()->isNotEmpty()) {
            $document['included'] = $this->getIncluded()->toJsonApiArray();
        }

        if ($this->getMeta() !== null) {
            $document['meta'] = $this->getMeta()->toArray();
        }

        if ($this->hasErrors()) {
            $document['errors'] = $this->errors->toArray();
        }

        if (!empty($this->getJsonapi())) {
            $document['jsonapi'] = $this->jsonapi;
        }

        return $document;
    }
}
