<?php
# Generated by the protocol buffer compiler.  DO NOT EDIT!
# source: google/api/auth.proto

namespace Google\Api;

use Google\Protobuf\Internal\GPBType;
use Google\Protobuf\Internal\RepeatedField;
use Google\Protobuf\Internal\GPBUtil;

/**
 * Specifies a location to extract JWT from an API request.
 *
 * Generated from protobuf message <code>google.api.JwtLocation</code>
 */
class JwtLocation extends \Google\Protobuf\Internal\Message
{
    /**
     * The value prefix. The value format is "value_prefix{token}"
     * Only applies to "in" header type. Must be empty for "in" query type.
     * If not empty, the header value has to match (case sensitive) this prefix.
     * If not matched, JWT will not be extracted. If matched, JWT will be
     * extracted after the prefix is removed.
     * For example, for "Authorization: Bearer {JWT}",
     * value_prefix="Bearer " with a space at the end.
     *
     * Generated from protobuf field <code>string value_prefix = 3;</code>
     */
    protected $value_prefix = '';
    protected $in;

    /**
     * Constructor.
     *
     * @param array $data {
     *     Optional. Data for populating the Message object.
     *
     *     @type string $header
     *           Specifies HTTP header name to extract JWT token.
     *     @type string $query
     *           Specifies URL query parameter name to extract JWT token.
     *     @type string $cookie
     *           Specifies cookie name to extract JWT token.
     *     @type string $value_prefix
     *           The value prefix. The value format is "value_prefix{token}"
     *           Only applies to "in" header type. Must be empty for "in" query type.
     *           If not empty, the header value has to match (case sensitive) this prefix.
     *           If not matched, JWT will not be extracted. If matched, JWT will be
     *           extracted after the prefix is removed.
     *           For example, for "Authorization: Bearer {JWT}",
     *           value_prefix="Bearer " with a space at the end.
     * }
     */
    public function __construct($data = NULL) {
        \GPBMetadata\Google\Api\Auth::initOnce();
        parent::__construct($data);
    }

    /**
     * Specifies HTTP header name to extract JWT token.
     *
     * Generated from protobuf field <code>string header = 1;</code>
     * @return string
     */
    public function getHeader()
    {
        return $this->readOneof(1);
    }

    public function hasHeader()
    {
        return $this->hasOneof(1);
    }

    /**
     * Specifies HTTP header name to extract JWT token.
     *
     * Generated from protobuf field <code>string header = 1;</code>
     * @param string $var
     * @return $this
     */
    public function setHeader($var)
    {
        GPBUtil::checkString($var, True);
        $this->writeOneof(1, $var);

        return $this;
    }

    /**
     * Specifies URL query parameter name to extract JWT token.
     *
     * Generated from protobuf field <code>string query = 2;</code>
     * @return string
     */
    public function getQuery()
    {
        return $this->readOneof(2);
    }

    public function hasQuery()
    {
        return $this->hasOneof(2);
    }

    /**
     * Specifies URL query parameter name to extract JWT token.
     *
     * Generated from protobuf field <code>string query = 2;</code>
     * @param string $var
     * @return $this
     */
    public function setQuery($var)
    {
        GPBUtil::checkString($var, True);
        $this->writeOneof(2, $var);

        return $this;
    }

    /**
     * Specifies cookie name to extract JWT token.
     *
     * Generated from protobuf field <code>string cookie = 4;</code>
     * @return string
     */
    public function getCookie()
    {
        return $this->readOneof(4);
    }

    public function hasCookie()
    {
        return $this->hasOneof(4);
    }

    /**
     * Specifies cookie name to extract JWT token.
     *
     * Generated from protobuf field <code>string cookie = 4;</code>
     * @param string $var
     * @return $this
     */
    public function setCookie($var)
    {
        GPBUtil::checkString($var, True);
        $this->writeOneof(4, $var);

        return $this;
    }

    /**
     * The value prefix. The value format is "value_prefix{token}"
     * Only applies to "in" header type. Must be empty for "in" query type.
     * If not empty, the header value has to match (case sensitive) this prefix.
     * If not matched, JWT will not be extracted. If matched, JWT will be
     * extracted after the prefix is removed.
     * For example, for "Authorization: Bearer {JWT}",
     * value_prefix="Bearer " with a space at the end.
     *
     * Generated from protobuf field <code>string value_prefix = 3;</code>
     * @return string
     */
    public function getValuePrefix()
    {
        return $this->value_prefix;
    }

    /**
     * The value prefix. The value format is "value_prefix{token}"
     * Only applies to "in" header type. Must be empty for "in" query type.
     * If not empty, the header value has to match (case sensitive) this prefix.
     * If not matched, JWT will not be extracted. If matched, JWT will be
     * extracted after the prefix is removed.
     * For example, for "Authorization: Bearer {JWT}",
     * value_prefix="Bearer " with a space at the end.
     *
     * Generated from protobuf field <code>string value_prefix = 3;</code>
     * @param string $var
     * @return $this
     */
    public function setValuePrefix($var)
    {
        GPBUtil::checkString($var, True);
        $this->value_prefix = $var;

        return $this;
    }

    /**
     * @return string
     */
    public function getIn()
    {
        return $this->whichOneof("in");
    }

}

